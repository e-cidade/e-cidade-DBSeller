<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Financeiro\Contabilidade\Models\LrfValorManual;
use Illuminate\Database\Eloquent\Builder;

/**
 * Classe responsável pela nova manuteno nos valores manuais dos relatórios legais
 */
class ValorManualService
{
    /**
     * Retorna uma collection de LrfValorManual conforme os filtros informados
     * @param array $filters
     * @return LrfValorManual[]
     */
    public function getByFilters($filters = [])
    {
        return LrfValorManual::query()
            ->when(!empty($filters['relatorio']), function (Builder $builder) use ($filters) {
                $builder->where('c180_relatorio', $filters['relatorio']);
            })
            ->when(!empty($filters['instituicao']), function (Builder $builder) use ($filters) {
                $builder->where('c180_instituicao', $filters['instituicao']);
            })
            ->when(!empty($filters['coluna']), function (Builder $builder) use ($filters) {
                $builder->where('c180_coluna', $filters['coluna']);
            })
            ->when(!empty($filters['linha']), function (Builder $builder) use ($filters) {
                $builder->where('c180_linha', $filters['linha']);
            })
            ->when(!empty($filters['exercicio']), function (Builder $builder) use ($filters) {
                $builder->where('c180_exercicio', $filters['exercicio']);
            })
            ->when(!empty($filters['mes']), function (Builder $builder) use ($filters) {
                $builder->where('c180_mes', $filters['mes']);
            })
            ->orderBy('c180_linha')
            ->orderBy('c180_exercicio')
            ->orderBy('c180_mes')
            ->get();
    }

    /**
     * @param $codigo
     * @return mixed
     */
    public function delete($codigo)
    {
        return LrfValorManual::query()->where('c180_codigo', $codigo)->delete();
    }

    public function salvar(array $dados)
    {
        $vlr = new LrfValorManual();
        if (!empty($dados['codigo'])) {
            $vlr = LrfValorManual::find($dados['codigo']);
        }
        $vlr->c180_relatorio = $dados['relatorio'];
        $vlr->c180_instituicao = $dados['instituicao'];
        $vlr->c180_linha = $dados['linha'];
        $vlr->c180_coluna = $dados['coluna'];
        $vlr->c180_exercicio = $dados['exercicio'];
        $vlr->c180_mes = $dados['mes'];
        $vlr->c180_valor = $dados['valor'];

        $vlr->save();
        return $vlr->c180_codigo;
    }
}
