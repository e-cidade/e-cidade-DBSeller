<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Repositories;

use App\Domain\Configuracao\GeradorRelatorio\Enums\TipoVisualizacaoRelatorioEnum;
use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;
use App\Domain\Core\Base\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class RelatorioRepository extends BaseRepository
{
    protected $modelClass = Relatorio::class;

    /**
     * @param TipoVisualizacaoRelatorioEnum $enum
     * @return Collection|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Support\Collection
     */
    public function getByVisualizacao(TipoVisualizacaoRelatorioEnum $enum)
    {
        $query = $this->newQueryByVisualizacao($enum->getValue());

        return $query->orderByDesc('db63_sequencial')
            ->get(['db63_sequencial as codigo', 'db63_nomerelatorio as nome']);
    }

    /**
     * @param int $tipoVisualizacao
     * @return \Illuminate\Database\Eloquent\Builder|Builder
     */
    private function newQueryByVisualizacao($tipoVisualizacao)
    {
        $query = $this->newQuery();
        if ($tipoVisualizacao === TipoVisualizacaoRelatorioEnum::USUARIO) {
            return $query->join('db_relatoriousuario', 'db09_db_relatorio', 'db63_sequencial')
                ->where('db09_db_usuarios', session('DB_id_usuario'));
        }
        if ($tipoVisualizacao === TipoVisualizacaoRelatorioEnum::DEPARTAMENTO) {
            return $query->join('db_relatoriodepart', 'db07_db_relatorio', 'db63_sequencial')
                ->where('db07_db_depart', session('DB_coddepto'));
        }
        if ($tipoVisualizacao === TipoVisualizacaoRelatorioEnum::CUBOS_BI) {
            return $query->whereIn('db63_db_gruporelatorio', [4, 5]);
        }

        $query->whereNotExists(function (Builder $query) {
            $query->from('db_relatoriodepart')->whereColumn('db07_db_relatorio', 'db63_sequencial');
        });
        $query->whereNotExists(function (Builder $query) {
            $query->from('db_relatoriousuario')->whereColumn('db09_db_relatorio', 'db63_sequencial');
        });

        return $query;
    }
}
