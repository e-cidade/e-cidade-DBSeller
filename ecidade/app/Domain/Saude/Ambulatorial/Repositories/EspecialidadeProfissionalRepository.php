<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Saude\Ambulatorial\Models\EspecialidadeProfissional;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @package App\Domain\Saude\Ambulatorial\Repositories
 */

class EspecialidadeProfissionalRepository extends BaseRepository
{
    protected $modelClass = EspecialidadeProfissional::class;

    /**
     * Recupera os profissionais ativos de acordo com a unidade.
     * @return Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function recuperaProfissionaisAtivos()
    {
        $unidade = $_SESSION['DB_coddepto'];
        return $this->newQuery()
            ->select(['especmedico.*'])
            ->join('unidademedicos', 'sd04_i_codigo', 'sd27_i_undmed')
            ->join('medicos', 'sd03_i_codigo', 'sd04_i_medico')
            ->join('cgm', 'z01_numcgm', 'sd03_i_cgm')
            ->where('sd27_c_situacao', 'A')
            ->where('sd04_i_unidade', $unidade)
            ->orderBy('z01_nome')
            ->get();
    }
}
