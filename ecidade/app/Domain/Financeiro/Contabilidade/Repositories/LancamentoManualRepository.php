<?php

namespace App\Domain\Financeiro\Contabilidade\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Contabilidade\Models\ResumoConsultaLancamentoManual;
use App\Domain\Financeiro\Contabilidade\Services\EncerramentoPeriodoContabilService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class LancamentoManualRepository extends BaseRepository
{

    protected $modelClass = ResumoConsultaLancamentoManual::class;

    /**
     * @param $filtros
     * @return Builder
     */
    public function queryBuilder($filtros)
    {
        return ResumoConsultaLancamentoManual::query()
            ->when(!empty($filtros['idLote']), function ($query) use ($filtros) {
                $query->where('c160_codigo', $filtros['idLote']);
            })
            ->when(!empty($filtros['instituicao']), function ($query) use ($filtros) {
                $query->where('c02_instit', $filtros['instituicao']);
            })
            ->when(!empty($filtros['exercicio']), function ($query) use ($filtros) {
                $query->where('c70_anousu', $filtros['exercicio']);
            })
            ->when(!empty($filtros['dataInicial']), function ($query) use ($filtros) {
                $query->where('c70_data', '>=', $filtros['dataInicial']);
            })
            ->when(!empty($filtros['dataFinal']), function ($query) use ($filtros) {
                $query->where('c70_data', '<=', $filtros['dataFinal']);
            })
            ->when(!empty($filtros['aposDataEncerramento']), function ($query) use ($filtros) {
                $dataEncerramento = EncerramentoPeriodoContabilService::ultimaData(
                    session('DB_instit'),
                    session('DB_anousu')
                );

                if (!empty($dataEncerramento)) {
                    $query->where('c70_data', '>', $dataEncerramento);
                }
            })
            ->when(!empty($filtros['documento']), function ($query) use ($filtros) {
                $query->where('c53_coddoc', '=', $filtros['documento']);
            })
            ->when(!empty($filtros['documentos']), function ($query) use ($filtros) {
                $query->wherein('c53_coddoc', $filtros['documentos']);
            })
            ->orderBy('c160_codigo');
    }

    /**
     * @param array $filtros
     * @return Collection
     */
    public function getResmo(array $filtros)
    {
        return $this->queryBuilder($filtros)->get();
    }
}
