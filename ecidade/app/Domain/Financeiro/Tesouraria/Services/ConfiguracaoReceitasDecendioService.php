<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Financeiro\Orcamento\Repositories\NaturezaReceitaRepositoty;
use App\Domain\Financeiro\Tesouraria\Models\ReceitaDecendio;
use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use App\Domain\Financeiro\Tesouraria\Resources\ReceitaDecendioResource;

class ConfiguracaoReceitasDecendioService
{
    public function getReceitas(array $filtros)
    {
        $repository = new NaturezaReceitaRepositoty();
        $receitas = $repository->receitasDecendio($filtros);
        return ReceitaDecendioResource::toArray($receitas);
    }

    /**
     * @param integer $exercicio
     * @param array $receitas
     * @param integer $receitas
     * @param array $desdobramentos
     * @return void
     */
    public function configurarReceitas($exercicio, array $receitas, $origem, array $desdobramentos)
    {
        $this->excluirConfiguracaoReceitas($exercicio, $receitas);
        $tipos = $this->buscaTiposDesdobramentoOrigem($origem);
        $persistir = [];
        foreach ($receitas as $reduzido) {
            foreach ($desdobramentos as $desdobramento) {
                $persistir[] = [
                    'k224_tipodesdobramento' => $tipos[mb_strtoupper($desdobramento)],
                    'k224_reduz' => $reduzido,
                    'k224_anousu' => $exercicio
                ];
            }
        }

        (new ReceitaDecendio)->insert($persistir);
    }

    public function excluirConfiguracaoReceitas($exercicio, array $receitas)
    {
        ReceitaDecendio::where('k224_anousu', $exercicio)->whereIn('k224_reduz', $receitas)->delete();
    }

    private function buscaTiposDesdobramentoOrigem($origem)
    {
        $retorno = [];
        TipoDesdobramento::query()
            ->where('k222_origem', $origem)
            ->get()
            ->each(function (TipoDesdobramento $tipoDesdobramento) use (&$retorno) {
                $retorno[$tipoDesdobramento->k222_descricao] = $tipoDesdobramento->k222_codigo;
            });
        return $retorno;
    }
}
