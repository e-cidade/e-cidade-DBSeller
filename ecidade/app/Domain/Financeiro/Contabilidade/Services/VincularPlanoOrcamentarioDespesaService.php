<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Models\PlanoDespesa;
use App\Domain\Financeiro\Contabilidade\Resources\MapaPlanoContasOrcamentarioResource;
use Illuminate\Support\Facades\DB;

class VincularPlanoOrcamentarioDespesaService extends VincularPlanoOrcamentario
{
    /**
     * @param array $filtros
     * @return array
     */
    public function getContasEcidade(array $filtros)
    {
        $contas = $this->getContasConplanoOrcamento($filtros);
        $tipoPlano = $filtros['tipoPlano'];
        $retornar = $contas->map(function (ConplanoOrcamento $conta) use ($tipoPlano) {
            if ($tipoPlano === PlanoContas::PLANO_UNIAO) {
                $contasVinculadas = $conta->planoUniaoDespesa;
                $temVinculo = $contasVinculadas->count() > 0;
            } else {
                $contasVinculadas = $conta->planoEstadualDespesa;
                $temVinculo = $contasVinculadas->count() > 0;
            }

            return MapaPlanoContasOrcamentarioResource::toData($conta, $temVinculo, $contasVinculadas);
        });
        return $retornar->toArray();
    }

    public function getContasPadrao(array $filtros)
    {
        return PlanoDespesa::orderBy('conta')
            ->select('*')
            ->when(!empty($filtros['tipoPlano']), function ($query) use ($filtros) {
                $query->where('uniao', $filtros['tipoPlano'] === PlanoContas::PLANO_UNIAO);
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('exercicio', '=', $filtros['exercicio']);
            })
            ->when(!empty($filtros['conta']), function ($query) use ($filtros) {
                $query->where('conta', 'like', "{$filtros['conta']}%");
            })
            ->when(isset($filtros['apenasAnaliticas']), function ($query) {
                $query->where('sintetica', false);
            })
            ->whereRaw("substring(conta, 3, 2) != '00'")
            ->get()
            ->map(function (PlanoDespesa $planoOrcamentario) use ($filtros) {
                if (isset($filtros['comVinculos'])) {
                    $planoOrcamentario->contas_ecidade = $planoOrcamentario
                        ->contasEcidade()
                        ->get()
                        ->map(function (ConplanoOrcamento $conplanoOrcamento) {
                            return MapaPlanoContasOrcamentarioResource::toData(
                                $conplanoOrcamento,
                                true
                            );
                        });
                    $planoOrcamentario->tem_vinculo = $planoOrcamentario->contas_ecidade->count();
                }

                return $planoOrcamentario;
            });
    }

    public function getContasConplanoOrcamento(array $filtros)
    {
        $filtrar = [];
        if (!empty($filtros['idContaVinculada'])) {
            $filtrar[] = "exists(
                select 1 from contabilidade.planodespesaconplanoorcamento
             where conplanoorcamento_codigo = c60_codigo
            and planodespesa_id = {$filtros['idContaVinculada']})
            ";
        }

        if (!empty($filtros['estrutural'])) {
            $filtrar[] = " c60_estrut = '{$filtros['estrutural']}'";
        }

        return ConplanoOrcamento::contasDespesaAPartirElemento($filtros['exercicio'], $filtrar);
    }

    public function getPlanoOrcamentario($id)
    {
        return PlanoDespesa::find($id);
    }

    public function vincular($planoOrcamentario, array $idsContasEcidade)
    {
        $existe = DB::table('contabilidade.planodespesaconplanoorcamento')
            ->join('contabilidade.planodespesa', 'planodespesa.id', '=', 'planodespesa_id')
            ->whereIn('conplanoorcamento_codigo', $idsContasEcidade)
            ->where('planodespesa_id', '!=', $planoOrcamentario->id)
            ->where('uniao', $planoOrcamentario->uniao)
            ->first();

        if (!is_null($existe)) {
            $str = "Uma ou mais conta(s) do e-cidade selecionada(s), ";
            $str .= "já estão vínculada(s) a uma conta do plano do Governo.";
            throw new \Exception($str);
        }

        parent::vincular($planoOrcamentario, $idsContasEcidade);
        return true;
    }

    public function vinculoGeral(array $filtros)
    {
        $contasPadrao = $this->getContasPadrao($filtros);

        foreach ($contasPadrao as $planoOrcamentario) {
            $filtrar = ["c60_estrut like '3{$planoOrcamentario->conta}%'"];
            $idsContasEcidade = ConplanoOrcamento::contasDespesaAPartirElemento($filtros['exercicio'], $filtrar)
                ->map(function (ConplanoOrcamento $conplanoOrcamento) {
                    return $conplanoOrcamento->c60_codigo;
                })->toArray();

            if (!empty($idsContasEcidade)) {
                $this->vincular($planoOrcamentario, $idsContasEcidade);
            }
        }
    }

    public function importarVinculo(array $filtros)
    {
        $copy = $filtros;
        $copy['exercicio'] = $filtros['exercicio'] - 1;
        $this->removerVinculos($filtros);
        $contasPadrao = $this->getContasPadrao($filtros);

        foreach ($contasPadrao as $pcasp) {
            $copy['conta'] = $pcasp->conta;
            $pcaspAnterior = $this->getContasPadrao($copy)->shift();

            // quando a conta do pcasp é nova não vai ter no ano anterior
            if (is_null($pcaspAnterior)) {
                continue;
            }
            // não tem conta mapeada no exercicio anterior
            $contasAnoAnterior = $pcaspAnterior->contasEcidade;
            if ($contasAnoAnterior->isEmpty()) {
                continue;
            }

            $codCons = $contasAnoAnterior->map(function ($contaOrcamentaria) {
                return $contaOrcamentaria->c60_codcon;
            })->toArray();

            $idsContasEcidade = ConplanoOrcamento::query()
                ->select('c60_codigo')
                ->where('c60_anousu', $filtros['exercicio'])
                ->whereIn('c60_codcon', $codCons)
                ->get()
                ->map(function (ConplanoOrcamento $conta) {
                    return $conta->c60_codigo;
                })
                ->toArray();

            if (!empty($idsContasEcidade)) {
                $this->vincular($pcasp, $idsContasEcidade);
            }
        }
    }

    protected function removerVinculos($filtros)
    {
        $where = sprintf(
            "exercicio = %s and uniao = '%s'",
            $filtros['exercicio'],
            $filtros['tipoPlano'] === PlanoContas::PLANO_UNIAO ? 't' : 'f'
        );

        DB::statement(<<<SQL
            delete from contabilidade.planodespesaconplanoorcamento
            using contabilidade.planodespesa
            where planodespesa_id = planodespesa.id
            and $where;
SQL
        );
    }
}
