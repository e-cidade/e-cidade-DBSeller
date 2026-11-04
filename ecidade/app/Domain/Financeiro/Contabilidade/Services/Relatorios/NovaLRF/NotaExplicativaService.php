<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Financeiro\Orcamento\Models\LrfNotaExplicativa;
use Illuminate\Database\Eloquent\Builder;

class NotaExplicativaService
{
    /**
     * @param $filters
     * @return LrfNotaExplicativa[]
     */
    public function getByFilters($filters = [])
    {
        return LrfNotaExplicativa::query()
            ->when(!empty($filters['relatorio']), function (Builder $builder) use ($filters) {
                $builder->where('o42_codparrel', $filters['relatorio']);
            })
            ->when(!empty($filters['instituicao']), function (Builder $builder) use ($filters) {
                $builder->where('o42_instit', $filters['instituicao']);
            })
            ->when(!empty($filters['exercicio']), function (Builder $builder) use ($filters) {
                $builder->where('o42_anousu', $filters['exercicio']);
            })
            ->when(!empty($filters['periodo']), function (Builder $builder) use ($filters) {
                $builder->where('o42_periodo', $filters['periodo']);
            })
            ->when(!empty($filters['scope']), function (Builder $builder) use ($filters) {
                foreach ($filters['scope'] as $relation) {
                    $builder->$relation();
                }
            })
            ->get();
    }

    public function delete($codigo)
    {
        return LrfNotaExplicativa::query()->where('o42_sequencial', $codigo)->delete();
    }

    public function salvar(array $dados)
    {
        $nota = new LrfNotaExplicativa();
        if (!empty($dados['codigo'])) {
            $nota = LrfNotaExplicativa::find($dados['codigo']);
        }

        $nota->o42_codparrel = $dados['relatorio'];
        $nota->o42_anousu = $dados['exercicio'];
        $nota->o42_instit = $dados['instituicao'];
        $nota->o42_nota = $dados['nota'];
        $nota->o42_fonte = $dados['fonte'];
        $nota->o42_periodo = $dados['periodo'];
        $nota->o42_tamanhofontenota = $dados['notaSize'];
        $nota->o42_tamanhofontedados = $dados['fonteSize'];
        $nota->save();
        return $nota->o42_sequencial;
    }
}
