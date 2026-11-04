<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao;

use App\Domain\Financeiro\Empenho\Models\AquisicaoProducaoRuralProcessos;
use App\Domain\Financeiro\Empenho\Models\RetencaoReceitasProdutorRural;
use App\Domain\Financeiro\Empenho\Services\TipoAquisicaoProducaoRuralService;
use BusinessException;
use DBDate;
use Exception;
use Illuminate\Support\Facades\DB;

class RetencaoR2055 extends Retencao
{
    public function getRetencoes($filters)
    {
        $query = DB::table('empenho.empnota')
            ->select(
                'e69_codnota as nota',
                'e69_dtnota as data_nota',
                'e69_numero as nfnumero',
                'e60_numemp as empenho',
                'z01_numcgm as cgm',
                'z01_nome as prestador',
                'z01_cgccpf as cgccpf',
                'retencaoreceitas.e23_dtcalculo as data_calculo',
                'retencaoreceitasprodutorrural.*',
                'e70_vlrliq as vlrBruto',
                'emptipoaquisicaoproducaorural.e159_tipo as indAqProd'
            )
            ->distinct()
            ->selectRaw("concat(empempenho.e60_codemp, '/', empempenho.e60_anousu) as empenho_numero")
            ->join('empnotaele', 'empnotaele.e70_codnota', '=', 'empnota.e69_codnota')
            ->join('empempenho', 'e69_numemp', '=', 'e60_numemp')
            ->join('pcforne', 'pc60_numcgm', '=', 'e60_numcgm')
            ->join('cgm', 'z01_numcgm', '=', 'e60_numcgm')
            ->join('cgmtipoempresa', 'z01_numcgm', '=', 'z03_numcgm')
            ->join(
                'pagordemnota',
                'empnota.e69_codnota',
                '=',
                DB::Raw('pagordemnota.e71_codnota and pagordemnota.e71_anulado is false')
            )
            ->leftJoin('retencaoreceitasprodutorrural', 'e158_empnota', '=', 'e69_codnota')
            ->leftJoin('emptipoaquisicaoproducaorural', 'e159_empempenho', '=', 'e60_numemp')
            ->leftJoin('retencaoreceitas', 'e23_sequencial', '=', 'e158_retencaoreceitas')
            ->leftJoin('retencaoempagemov', 'e27_retencaoreceitas', '=', 'e23_sequencial')
            ->leftJoin('empagemov', 'e81_codmov', '=', 'e27_empagemov');

        if ($filters) {
            if ($filters->nota) {
                $query->where('empnota.e69_numero', '=', $filters->nota);
            }

            if ($filters->cgm) {
                $query->where('cgm.z01_numcgm', '=', $filters->cgm);
            }

            if ($filters->periodo) {
                $dataIni = empty($filters->periodo[0]) ? false : DBDate::converter($filters->periodo[0]);
                $dataFim = empty($filters->periodo[1]) ? false : DBDate::converter($filters->periodo[1]);

                if ($dataIni && $dataFim) {
                    $query->whereBetween('empnota.e69_dtnota', [$dataIni, $dataFim]);
                } elseif ($dataIni && $dataFim === false) {
                    $query->where('empnota.e69_dtnota', '>=', $dataIni);
                } elseif ($dataFim && $dataIni === false) {
                    $query->where('empnota.e69_dtnota', '<=', $dataFim);
                }
            }
        }

        // caso possua filtro de orgaoUnidade
        $query->when(isset($filters->orgaoUnidade), function ($q) use ($filters) {
            $orgao   = $filters->orgaoUnidade->orgao;
            $unidade = $filters->orgaoUnidade->unidade;

            $autorizado = $this->checkOrgaoUnidadeUsuario($orgao);
            if (!$autorizado) {
                throw new BusinessException('Orgão ou Unidade não autorizado para pesquisa.');
            }

            $q->selectRaw(
                "concat(o40_orgao, ' - ', o40_descr, ' / ', o41_unidade, ' - ', o41_descr) as orgao_unidade"
            )
                ->join(
                    'orcdotacao',
                    'empempenho.e60_coddot',
                    '=',
                    DB::Raw('orcdotacao.o58_coddot and empempenho.e60_anousu = orcdotacao.o58_anousu')
                )
                ->join(
                    'orcunidade',
                    'o58_unidade',
                    '=',
                    DB::Raw('o41_unidade and o58_anousu = o41_anousu and o58_orgao = o41_orgao')
                )
                ->join(
                    'orcorgao',
                    'o41_orgao',
                    '=',
                    DB::Raw('o40_orgao and o41_anousu = o40_anousu')
                );

            if ($orgao) {
                $q->where('o58_orgao', '=', $orgao);
            }

            if ($unidade) {
                $q->where('o58_unidade', '=', $unidade);
            }
        });

        $query->where('e60_instit', '=', $_SESSION['DB_instit']);
        $query->whereIn('z03_tipoempresa', [35, 4120]);
        $query->whereNull('e81_cancelado');
        $query->where(function ($q) {
            $q->whereNull('e23_ativo')->orWhere('e23_ativo', true);
        });

        return $query->get();
    }

    public function saveRetencao($data)
    {
        $this->saveDadosTipoAquisicaoProdutorRural($data);
        $this->saveDadosRetencaoProducaoRural($data);
        $this->saveDadosProcessoProducaoRural($data);
    }

     /**
     * Tipo de aquisição de produção rural
     *
     * @param object $dados
     * @return boolean
     */
    private function saveDadosTipoAquisicaoProdutorRural($dados)
    {
        // validation
        if (!isset($dados->indAqProd) && !isset($dados->empenho) && empty($dados->empenho)) {
            throw new Exception("Campos obrigatórios não informados.");
            return false;
        }

        try {
            $tipoAquisicaoProducaoRural = new TipoAquisicaoProducaoRuralService;
            $tipoAquisicaoProducaoRural->setNumemp($dados->empenho);
            $tipoAquisicaoProducaoRural->setTipo($dados->indAqProd);
            $tipoAquisicaoProducaoRural->save();
        } catch (Exception $e) {
            $msg  = "Erro - Não foi possível incluir indicativo de Aquisição";
            throw new Exception($msg . "\n{$e->getMessage()}");
            return false;
        }
    }

    /**
     * Dados da retencao de producao rural
     *
     * @param object $dados
     * @return boolean
     */
    private function saveDadosRetencaoProducaoRural($dados)
    {
        // validation
        if (empty($dados->nota)) {
            throw new Exception("Nota de liquidação deve ser informada");
            return false;
        }

        $retencaoReceitasProdutorRural = RetencaoReceitasProdutorRural::where('e158_empnota', $dados->nota)->first();
        if (empty($retencaoReceitasProdutorRural)) {
            $retencaoReceitasProdutorRural = new RetencaoReceitasProdutorRural;
            $retencaoReceitasProdutorRural->e158_empnota  = $dados->nota;
        }

        $retencaoReceitasProdutorRural->e158_vlrrat   = floatval($dados->e158_vlrrat);
        $retencaoReceitasProdutorRural->e158_vlrsenar = floatval($dados->e158_vlrsenar);
        $retencaoReceitasProdutorRural->e158_vlrcp    = floatval($dados->e158_vlrcp);

        try {
            $retencaoReceitasProdutorRural->save();
        } catch (Exception $e) {
            $msg  = "Erro - Não foi possível incluir indicativo de Aquisição";
            throw new BusinessException($msg . "\n{$e->getMessage()}");
            return false;
        }
    }

    /**
     * Dados do processo
     *
     * @param object $dados
     * @return boolean
     */
    private function saveDadosProcessoProducaoRural($dados)
    {
        if ($dados->processos) {
            $retencao = RetencaoReceitasProdutorRural::where('e158_empnota', $dados->nota)->first(['e158_sequencial']);
            if (empty($retencao)) {
                throw new BusinessException("
                    Não foi encontrado retenção de produtor rural para salvar os dados dos processos
                ");
                return false;
            }

            foreach ($dados->processos as $item) {
                $processo = new AquisicaoProducaoRuralProcessos;

                if ($item->id) {
                    $processo = AquisicaoProducaoRuralProcessos::find($item->id);
                    if (!$processo) {
                        continue;
                    }
                }

                $processo->e157_retencaoreceitasprodutorrural = $retencao->e158_sequencial;
                $processo->e157_nrprocjud    = $item->numero;
                $processo->e157_vlrcpnret    = floatval($item->cp);
                $processo->e157_vlrratnret   = floatval($item->rat);
                $processo->e157_vlrsenarnret = floatval($item->senar);

                try {
                    $processo->save();
                } catch (Exception $e) {
                    $msg  = "Erro - Não foi possível incluir o processo {$processo->e157_vlrcpnret}";
                    throw new BusinessException($msg . "\n{$e->getMessage()}");
                }
            }
        }

        if ($dados->processosToRemove) {
            if (is_array($dados->processosToRemove)) {
                AquisicaoProducaoRuralProcessos::destroy($dados->processosToRemove);
            }
        }
    }
}
