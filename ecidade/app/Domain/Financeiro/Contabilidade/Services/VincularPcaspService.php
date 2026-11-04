<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoAtributos;
use App\Domain\Financeiro\Contabilidade\Models\InformacaoComplementar;
use App\Domain\Financeiro\Contabilidade\Models\Pcasp;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Esse service por padrão vai tratar
 */
class VincularPcaspService
{
    const UNIAO = 'uniao';

    private $mapaInformacoesComplementares = [];

    /**
     * Retorna as contas do plano padrão
     * @param array $filtros
     * @return Collection
     */
    public function getContasPadrao(array $filtros)
    {
        return Pcasp::orderBy('conta')
            ->select('*')
            ->when(!empty($filtros['conta']), function ($query) use ($filtros) {
                $query->where('conta', 'like', "{$filtros['conta']}%");
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('exercicio', '=', $filtros['exercicio']);
            })
            ->when(!empty($filtros['tipoPlano']), function ($query) use ($filtros) {
                $query->where('uniao', $filtros['tipoPlano'] === self::UNIAO);
            })
            ->when(!empty($filtros['apenasAnaliticas']), function ($query) use ($filtros) {
                $query->where('sintetica', false);
            })
            ->when(!empty($filtros['contasCaixa']), function ($query) use ($filtros) {
                $query->contasCaixa();
            })
            ->when(!empty($filtros['contasBancarias']), function ($query) use ($filtros) {
                $query->contasBancarias();
            })
            ->when(!empty($filtros['contasExtrasOrcamentarias']), function ($query) use ($filtros) {
                $query->contasExtrasOrcamentarias();
            })
            ->when(!empty($filtros['outrasContas']), function ($query) use ($filtros) {
                $query->outrasContas();
            })
            ->when(!empty($filtros['existeVinculo']), function ($query) {
                $column = 'exists(select 1 from contabilidade.pcaspconplano where pcasp_id = pcasp.id ) as tem_vinculo';
                $query->addSelect(DB::raw($column));
            })
            ->get();
    }

    /**
     * Retorna as contas do e-Cidade (conplano)
     * @param array $filtros
     * @return Collection
     */
    public function getContasEcidade(array $filtros)
    {
        $service = new PcaspService();
        return $service->getContasEcidade($filtros);
    }

    /**
     * @param string $filtros
     */
    public function procesarVinculoGeral($filtros)
    {
        $this->getContasPadrao($filtros)
            ->each(function (Pcasp $pcasp) use ($filtros) {
                $estrutural = new Estrutural($pcasp->conta);
                $ateNivel = $estrutural->getEstruturalAteNivel();

                $filtros['estrutural'] = $ateNivel;
                $contas = $this->getContasEcidade($filtros)->map(function (Conplano $conplano) {
                    return $conplano->c60_codigo;
                });

                $this->vincularContas($pcasp, $contas->toArray());
            });
    }

    /**
     * @param integer $idPcasp
     * @param array $idsContasEcidade
     * @return bool
     */
    public function vincular($idPcasp, array $idsContasEcidade)
    {
        $pcasp = Pcasp::find($idPcasp);
        $this->vincularContas($pcasp, $idsContasEcidade);
        return true;
    }

    /**
     * Sincroniza as contas do e-cidade com uma conta do pcasp.
     * Mas refaz os vínculos com as contas informadas
     * @param Pcasp $pcasp
     * @param array $idsContasEcidade
     */
    private function vincularContas(Pcasp $pcasp, array $idsContasEcidade)
    {
        $pcasp->contasEcidade()->sync($idsContasEcidade);
        if ($pcasp->uniao) {
            $this->vincularInformacoesComplementares($pcasp, $idsContasEcidade);
        }
    }

    /**
     * Atualiza as contas mapeadas sem remover as demais
     * @param Pcasp $pcasp
     * @param array $idsContasEcidade
     */
    public function atualizarVinculoContas(Pcasp $pcasp, array $idsContasEcidade)
    {
        $pcasp->contasEcidade()->syncWithoutDetaching($idsContasEcidade);
        if ($pcasp->uniao) {
            $this->vincularInformacoesComplementares($pcasp, $idsContasEcidade);
        }
    }

    /**
     * @param Pcasp $pcasp
     * @param array $idsContasEcidade
     * @return void
     */
    private function vincularInformacoesComplementares(Pcasp $pcasp, array $idsContasEcidade)
    {
        $this->carregarInformacoesComplementares($pcasp->exercicio);

        $atributosContaPcasp = explode(',', $pcasp->informacoescomplementares);

        $contasEcidade = Conplano::query()->whereIn('c60_codigo', $idsContasEcidade)->get();
        $this->removeAtributosConplano($pcasp, $contasEcidade);

        $novosAtributos = [];
        foreach ($atributosContaPcasp as $atributo) {
            $codigoAtributo = $this->mapaInformacoesComplementares[trim($atributo)];
            /**
             * @var Conplano $conplano
             */
            foreach ($contasEcidade as $conplano) {
                $novosAtributos[] = [
                    "c120_anousu" => $conplano->c60_anousu,
                    "c120_conplano" => $conplano->c60_codcon,
                    "c120_infocomplementar" => $codigoAtributo,
                    "c120_conplanosistema" => ConplanoAtributos::SISTEMA_SICONFI
                ];

                if (count($novosAtributos) >= 100) {
                    $this->inserirAtributos($novosAtributos);
                    $novosAtributos = [];
                }
            }
        }

        if (!empty($novosAtributos)) {
            $this->inserirAtributos($novosAtributos);
        }
    }

    /**
     * Vincula as informações complementares com as contas do e-cidade
     * @param $dados
     * @return void
     */
    private function inserirAtributos($dados)
    {
        $model = new ConplanoAtributos();
        $model->insert($dados);
    }

    /**
     * Busca os códigos das informações complementares usadas no siconfi
     * @return array
     */
    private function carregarInformacoesComplementares($exercicio)
    {
        if (empty($this->mapaInformacoesComplementares)) {
            $filtrar = MatrizSaldoContabil::getAtributos($exercicio);
            InformacaoComplementar::query()
                ->whereIn('c121_sequencial', $filtrar)
                ->get()
                ->map(function (InformacaoComplementar $info) {
                    $this->mapaInformacoesComplementares[$info->c121_sigla] = $info->c121_sequencial;
                });
        }

        return $this->mapaInformacoesComplementares;
    }

    /**
     * @param Pcasp $pcasp
     * @param Collection $contasEcidade
     */
    private function removeAtributosConplano(Pcasp $pcasp, Collection $contasEcidade)
    {
        $codCons = $contasEcidade->map(function (Conplano $conplano) {
            return $conplano->c60_codcon;
        });

        DB::table('contabilidade.conplanoatributos')
            ->where('c120_conplanosistema', ConplanoAtributos::SISTEMA_SICONFI)
            ->where('c120_anousu', $pcasp->exercicio)
            ->whereIn('c120_conplano', $codCons)
            ->delete();
    }

    public function importarVinculo(array $filtros)
    {
        $copy = $filtros;
        $copy['exercicio'] = $filtros['exercicio'] - 1;

        $this->getContasPadrao($filtros)
            ->each(function (Pcasp $pcasp) use ($copy) {
                $copy['conta'] = $pcasp->conta;
                $pcaspAnterior = $this->getContasPadrao($copy)->shift();
                // quando a conta do pcasp é nova não vai ter no ano anterior
                if (is_null($pcaspAnterior)) {
                    return;
                }

                // não tem conta mapeada no exercicio anterior
                $contasAnoAnterior = $pcaspAnterior->contasEcidade;
                if ($contasAnoAnterior->isEmpty()) {
                    return;
                }

                $codCons = $contasAnoAnterior->map(function ($contaOrcamentaria) {
                    return $contaOrcamentaria->c60_codcon;
                })->toArray();

                $contasEcidade = Conplano::select('c60_codigo')
                    ->where('c60_anousu', $pcasp->exercicio)
                    ->whereIn('c60_codcon', $codCons)
                    ->get();

                if (!$contasEcidade->isEmpty()) {
                    $idContasEcidade = $contasEcidade->map(function ($conta) {
                        return $conta->c60_codigo;
                    })->toArray();

                    $this->vincularContas($pcasp, $idContasEcidade);
                }
            });
    }
}
