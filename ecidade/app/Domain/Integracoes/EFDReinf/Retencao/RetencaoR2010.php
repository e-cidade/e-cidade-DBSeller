<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao;

use App\Domain\Financeiro\Empenho\Services\TipoServicoObraService;
use BusinessException;
use cl_empnota;
use cl_retencaoreceitasadicionais;
use DBDate;
use Illuminate\Support\Facades\DB;

class RetencaoR2010 extends Retencao
{
    /**
     * Retencoes para o evento R2010
     */
    public function getRetencoes($filters)
    {
        $query = DB::table('empenho.empnota')
            ->select(
                'cgmprestador.z01_numcgm as identificador_prestador',
                'cgmprestador.z01_nome as nome_prestador',
                'cgmprestador.z01_cgccpf as cnpj_prestador',
                'retencaotiporec.e21_descricao as rentencao_tipo',
                'emptiposervicoobra.e154_tipo as indicativo_obra_tipo',
                'emptiposervicoobra.e154_label as indicativo_obra_descricao',
                'emptiposervicoobra.e154_cno as indicativo_obra_cno',
                'empnota.e69_codnota as codigo_nota',
                'empnota.e69_numero as numero_nota',
                'empnota.e69_serienota as serie_nota',
                'empnota.e69_dtnota as data_emissao',
                'tiposerviconotafiscal.e18_sequencial as referencia_tipo_servico',
                'tiposerviconotafiscal.e18_descricao as referencia_tipo_servico_desc',
                'retencaoreceitas.e23_sequencial as retencao_sequencial',
                'retencaoreceitas.e23_valorretencao as valor_retencao',
                'retencaoreceitas.e23_valorbase as valor_base_retido',
                'retencaoreceitas.e23_dtcalculo as data_calculo',
                'retencaoreceitasadicionais.e19_sequencial as receitasadicionais_sequencial',
                'retencaoreceitasadicionais.e19_valornaoretidoprincipal as valor_nao_retido_principal',
                'retencaoreceitasadicionais.e19_valorservico15 as valor_servicos_15',
                'retencaoreceitasadicionais.e19_valorservico20 as valor_servicos_20',
                'retencaoreceitasadicionais.e19_valorservico25 as valor_servicos_25',
                'retencaoreceitasadicionais.e19_valoradicional as valor_adicional',
                'retencaoreceitasadicionais.e19_valornaoretidoadicional as valor_nao_retido_adicional',
                'empnotaele.e70_vlrliq as valor_nota_liq',
                'empempenho.e60_numemp as empenho',
                'retencaoreceitasadicionais.e19_indvalorbase as indicativo_valor_base',
                'pc60_indicativocprb as indicativo_cprb'
            )
            ->distinct()
            ->selectRaw(
                "(select sum(b.e70_vlrliq)
		        from empnota a
		        inner join empnotaele b on b.e70_codnota = a.e69_codnota
		        left join pagordemnota c on a.e69_codnota = c.e71_codnota and c.e71_anulado is false
		        inner join empempenho d on d.e60_numemp = a.e69_numemp
		        where
		        d.e60_numcgm = cgmprestador.z01_numcgm and
		        a.e69_numero = empnota.e69_numero and
		        a.e69_codnota <> empnota.e69_codnota
	        ) as notas_nao_retidas"
            )
            ->selectRaw("concat(empempenho.e60_codemp, '/', empempenho.e60_anousu) as empenho_numero")
            ->selectRaw(
                "(case when pc60_indicativocprb is true
            then cast('3,5' as varchar) else retencaotiporec.e21_aliquota::varchar end) as aliquota"
            )
            ->join(
                'pagordemnota',
                'empnota.e69_codnota',
                '=',
                DB::Raw('pagordemnota.e71_codnota and pagordemnota.e71_anulado is false')
            )
            ->join('empnotaele', 'empnotaele.e70_codnota', '=', 'empnota.e69_codnota')
            ->join('pagordem', 'e71_codord', '=', 'pagordem.e50_codord')
            ->join('retencaopagordem', 'pagordem.e50_codord', '=', 'retencaopagordem.e20_pagordem')
            ->join('retencaoreceitas', 'retencaopagordem.e20_sequencial', '=', 'retencaoreceitas.e23_retencaopagordem')
            ->join('retencaotiporec', 'retencaotiporec.e21_sequencial', '=', 'retencaoreceitas.e23_retencaotiporec')
            ->join('empempenho', 'empempenho.e60_numemp', '=', 'pagordem.e50_numemp')
            ->join('db_config', 'db_config.codigo', '=', 'empempenho.e60_instit')
            ->join('cgm as cgmcontribuinte', 'cgmcontribuinte.z01_numcgm', '=', 'db_config.numcgm')
            ->join('retencaoempagemov', 'e27_retencaoreceitas', '=', 'e23_sequencial')
            ->join('empagemov', 'e81_codmov', '=', 'e27_empagemov')
            ->leftJoin('pagordemconta', 'pagordemconta.e49_codord', 'pagordem.e50_codord')
            ->leftJoin(
                'cgm as cgmprestador',
                'cgmprestador.z01_numcgm',
                '=',
                DB::Raw("coalesce(pagordemconta.e49_numcgm, empempenho.e60_numcgm)")
            )
            ->leftJoin('pcforne', 'pcforne.pc60_numcgm', '=', 'cgmprestador.z01_numcgm')
            ->leftJoin('emptiposervicoobra', 'emptiposervicoobra.e154_numemp', '=', 'empempenho.e60_numemp')
            ->leftJoin(
                'retencaoreceitasadicionais',
                'retencaoreceitas.e23_sequencial',
                '=',
                'retencaoreceitasadicionais.e19_retencaoreceitas'
            )
            ->leftJoin(
                'tiposerviconotafiscal',
                'tiposerviconotafiscal.e18_sequencial',
                '=',
                'retencaoreceitasadicionais.e19_tiposerviconotafiscal'
            );

        /**
         * Adiciona os filtros na consulta
         */
        if ($filters) {
            if ($filters->nota) {
                $nota = trim($filters->nota);
                $query->whereRaw('trim(empnota.e69_numero) = ?', [$nota]);
            }

            if ($filters->cgm) {
                $query->where('cgmprestador.z01_numcgm', '=', $filters->cgm);
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

            // caso possua filtro de orgaoUnidade
            if (isset($filters->orgaoUnidade)) {
                $orgao   = $filters->orgao;
                $unidade = $filters->unidade;

                $autorizado = $this->checkOrgaoUnidadeUsuario($orgao);
                if (!$autorizado) {
                    throw new BusinessException('Orgão ou Unidade não autorizado para pesquisa.');
                }

                $query->selectRaw(
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
                    DB::Raw('o41_unidade and o58_anousu = o41_anousu and o58_orgao = o41_orgao')
                )
                ->join(
                    'orcorgao',
                    'o41_orgao',
                    DB::Raw('o40_orgao and o41_anousu = o40_anousu')
                );

                if ($orgao) {
                    $query->where('o58_orgao', '=', $orgao);
                }

                if ($unidade) {
                    $query->where('o58_unidade', '=', $unidade);
                }
            }
        }

        $query->where('e60_instit', '=', session('DB_instit'));
        $query->where('retencaoreceitas.e23_ativo', '=', true);
        $query->whereNull('e81_cancelado');
        $query->where('retencaotiporec.e21_retencaotipocalc', '=', 4);
        $result = $query->orderBy('empnota.e69_dtnota', 'desc')->get();

        return $result;
    }

    /**
     * Salva retencoes
     *
     * @param object $dados
     * @return void
     */
    public function saveRetencao($dados)
    {
        $this->saveDadosTipoServicoObra($dados);
        $this->saveNotaFiscal($dados);
        $this->saveDadosAdcionais($dados);
    }

    /**
     * salva alteracoes da nota fiscal
     *
     * @param object $dados
     * @return bool
     */
    private function saveNotaFiscal($dados)
    {
        $empnota = new cl_empnota;

        $empnota->e69_codnota   = $dados->codigo_nota;
        $empnota->e69_numero    = $dados->numero_nota;
        $empnota->e69_serienota = $dados->serie_nota;

        $empnota->alterar($dados->codigo_nota);

        if ($empnota->erro_status == 0) {
            $msg  = "Erro ao alterar dados da nota fiscal.\n";
            $msg .= "Erro Técnico: {$empnota->erro_msg}";

            throw new BusinessException($msg);
        }
    }

    /**
     * Salva alterações das retencoes adicionais
     *
     * @param object $dados
     * @return bool
     */
    private function saveDadosAdcionais($dados)
    {
        // evita duplicidade
        $receitaAdicional = DB::table('retencaoreceitasadicionais')->where(
            'e19_retencaoreceitas',
            '=',
            $dados->retencao_sequencial
        )->first(['e19_sequencial']);

        $receitasAdicionais = new cl_retencaoreceitasadicionais;
        $receitasAdicionais->e19_retencaoreceitas      = $dados->retencao_sequencial;
        $receitasAdicionais->e19_tiposerviconotafiscal = $dados->referencia_tipo_servico;

        $receitasAdicionais->e19_valornaoretidoprincipal = floatval($dados->valor_nao_retido_principal);
        $receitasAdicionais->e19_valornaoretidoadicional = floatval($dados->valor_nao_retido_adicional);

        $receitasAdicionais->e19_valorservico15 = floatval($dados->valor_servicos_15);
        $receitasAdicionais->e19_valorservico20 = floatval($dados->valor_servicos_20);
        $receitasAdicionais->e19_valorservico25 = floatval($dados->valor_servicos_25);

        $receitasAdicionais->e19_indvalorbase = ($dados->indicativo_valor_base == "true") ? true : false;

        if ($receitaAdicional) {
            $receitasAdicionais->alterar($receitaAdicional->e19_sequencial);
        } else {
            $receitasAdicionais->incluir(null);
        }

        if ($receitasAdicionais->erro_status == 0) {
            $msg  = "Erro - Não foi possível incluir dados adicionais na Retencao {$dados->retencao_sequencial}.\n";
            $msg .= "Erro Técnico: {$receitasAdicionais->erro_msg}";

            throw new BusinessException($msg);
        }
    }

    /**
     * Salva alteracoes do tipo de serviço em obra
     *
     * @param object $dados
     * @return bool
     */
    private function saveDadosTipoServicoObra($dados)
    {
        $tiposervicoobra = new TipoServicoObraService;
        $tiposervicoobra->setNumemp($dados->empenho);
        $tiposervicoobra->setTipo($dados->indicativo_obra_tipo);
        $tiposervicoobra->setCNO($dados->indicativo_obra_cno);

        try {
            $tiposervicoobra->save();
        } catch (\Exception $e) {
            $msg  = "Erro - Não foi possível incluir indicativo de obra";

            throw new \Exception($msg . "\n{$e->getMessage()}");
        }
    }
}
