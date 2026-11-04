<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Mappers\PlanoContas\PlanoContas;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamento;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Contabilidade\Resources\MapaPlanoContasOrcamentarioResource;
use Illuminate\Support\Facades\DB;

class VincularPlanoOrcamentarioReceitaService extends VincularPlanoOrcamentario
{
    /**
     * @param $id
     * @return PlanoReceita
     */
    public function getPlanoOrcamentario($id)
    {
        return PlanoReceita::find($id);
    }

    public function getContasEcidade(array $filtros)
    {
        $contas = $this->getContasConplanoOrcamento($filtros);
        $tipoPlano = $filtros['tipoPlano'];
        $retornar = $contas->map(function (ConplanoOrcamento $conta) use ($tipoPlano) {
            if ($tipoPlano === PlanoContas::PLANO_UNIAO) {
                $contasVinculadas = $conta->planoUniaoReceita;
                $temVinculo = $contasVinculadas->count() > 0;
            } else {
                $contasVinculadas = $conta->planoEstadualReceita;
                $temVinculo = $contasVinculadas->count() > 0;
            }

            return MapaPlanoContasOrcamentarioResource::toData($conta, $temVinculo, $contasVinculadas);
        });

        return $retornar->toArray();
    }

    public function getContasPadrao(array $filtros)
    {
        return PlanoReceita::orderBy('conta')
            ->select('*')
            ->when(!empty($filtros['conta']), function ($query) use ($filtros) {
                $query->where('conta', 'like', "{$filtros['conta']}%");
            })
            ->when(!empty($filtros['tipoPlano']), function ($query) use ($filtros) {
                $query->where('uniao', $filtros['tipoPlano'] === PlanoContas::PLANO_UNIAO);
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('exercicio', '=', $filtros['exercicio']);
            })
            ->when(isset($filtros['apenasAnaliticas']), function ($query) {
                $query->where('sintetica', false);
            })
            ->get()
            ->map(function (PlanoReceita $planoOrcamentario) use ($filtros) {
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
            })->toArray();
    }

    /**
     * Realiza o vínculo das contas onde da um match com o estrutural das contas.
     * Para isso, pensamos na seguinte lógica.
     *  - Removemos o número 4 extra na frente das contas do e-cidade;
     *  - pegamos o tamanho da conta do governo... exemplo 8 digitos;
     *  - aplicamos um corte no estrutural do e-cidade realizamos a comparação das contas (e-Cidade = Gov)
     * Se der match criamos o vínculo
     * @param array $filtros
     */
    public function vinculoGeral(array $filtros)
    {
        $contasGoverno = $this->getContasGoverno($filtros['tipoPlano'], $filtros['exercicio']);

        $contasGoverno->each(function (PlanoReceita $planoOrcamentario) {
            $tamanhoConta = strlen($planoOrcamentario->conta);

            // contas de dedução (classe 9) deve pegar o estrutural inteiro, incluindo o 9.
            $corte = $planoOrcamentario->classe === 9 ? 1 : 2;

            $contasEcidade = DB::table('contabilidade.conplanoorcamento')
                ->select('c60_codigo')
                ->where('c60_anousu', $planoOrcamentario->exercicio)
                // procura contas com o padrão do estrutural da conta do governo
                ->whereRaw("substring(c60_estrut, $corte, $tamanhoConta) = '$planoOrcamentario->conta'")
                // garante contas da receita contas 4 e 9
                ->whereRaw("substring(c60_estrut, 1, 1)::int = {$planoOrcamentario->classe}")
                ->get();

            if (!$contasEcidade->isEmpty()) {
                $idContasEcidade = $contasEcidade->map(function ($conta) {
                    return $conta->c60_codigo;
                })->toArray();

                $this->vincularContas($planoOrcamentario, $idContasEcidade);
            }
        });
    }

    public function vincular($planoOrcamentario, array $idsContasEcidade)
    {
        $existe = DB::table('contabilidade.planoreceitaconplanoorcamento')
            ->join('contabilidade.planoreceita', 'planoreceita.id', '=', 'planoreceita_id')
            ->whereIn('conplanoorcamento_codigo', $idsContasEcidade)
            ->where('planoreceita_id', '!=', $planoOrcamentario->id)
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

    public function importarVinculo(array $filtros)
    {
        $contasGoverno = $this->getContasGoverno($filtros['tipoPlano'], $filtros['exercicio']);

        $contasGoverno->each(function (PlanoReceita $planoOrcamentario) use ($filtros) {
            $planoOrcamentario->conta;
            $exercicioAnterior = $planoOrcamentario->exercicio - 1;
            $tipoPlano = $planoOrcamentario->uniao ? 'uniao' : 'UF';

            $pcaspAnterior = $this->getContasGoverno($tipoPlano, $exercicioAnterior, $planoOrcamentario->conta)
                ->shift();

            // quando a conta do pcasp é nova não vai ter no ano anterior
            if (is_null($pcaspAnterior)) {
                return;
            }

            // não tem conta mapeada no exercicio anterior
            $contasAnoAnterior = $pcaspAnterior->contasEcidade;
            if ($contasAnoAnterior->isEmpty()) {
                return;
            }

            $codCons = [];
            foreach ($contasAnoAnterior as $contaOrcamentaria) {
                $codCons[] = $contaOrcamentaria->c60_codcon;
            }

            $contasEcidade = DB::table('contabilidade.conplanoorcamento')
                ->select('c60_codigo')
                ->where('c60_anousu', $planoOrcamentario->exercicio)
                ->whereIn('c60_codcon', $codCons)
                ->get();

            if (!$contasEcidade->isEmpty()) {
                $idContasEcidade = $contasEcidade->map(function ($conta) {
                    return $conta->c60_codigo;
                })->toArray();

                $this->vincularContas($planoOrcamentario, $idContasEcidade);
            }
        });
        return true;
    }

    /**
     * @param string $tipoPlano
     * @param integer $exercicio
     * @param string $conta
     * @return mixed
     */
    public function getContasGoverno($tipoPlano, $exercicio, $conta = null)
    {
        return PlanoReceita::orderBy('conta')
            ->select('*')
            ->when(!empty($conta), function ($query) use ($conta) {
                $query->where('conta', '=', $conta);
            })
            ->when(!empty($tipoPlano), function ($query) use ($tipoPlano) {
                $query->where('uniao', $tipoPlano === PlanoContas::PLANO_UNIAO);
            })
            ->when(!empty($exercicio), function ($query) use ($exercicio) {
                $query->where('exercicio', '=', $exercicio);
            })
            ->where('sintetica', false)
            ->get();
    }
}
