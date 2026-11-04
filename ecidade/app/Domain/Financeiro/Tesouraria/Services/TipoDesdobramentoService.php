<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Financeiro\Orcamento\Models\Recurso;
use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use App\Domain\Financeiro\Tesouraria\Repositories\TipoDesdobramentoRepository;

class TipoDesdobramentoService
{

    public function get(array $filtros)
    {
        return (new TipoDesdobramentoRepository())
            ->getByFilters($filtros, $this->getSort())
            ->each(function (TipoDesdobramento $tipoDesdobramento) {
                $tipoDesdobramento->recursos->each(function (Recurso $recurso) {
                    $recurso->complemento;
                    $recurso->fonteRecurso = $recurso->fonteRecurso(session('DB_anousu'));
                });
            });
    }

    public function salvar(array $dados)
    {
        foreach ($dados['desdobramentos'] as $desdobramento => $dado) {
            $tipo = TipoDesdobramento::where('k222_origem', $dados['origem'])
                ->where('k222_descricao', mb_strtoupper($desdobramento))
                ->first();

            $tipo->k222_percentual = $dado['percentual'];
            $tipo->save();

            $tipo->recursos()->sync($dado['recursos']);
        }
    }

    private function getSort()
    {
        return ['k222_origem', 'k222_codigo'];
    }
}
