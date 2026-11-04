<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\Turma;

class TurmaRepository extends BaseRepository
{
    protected $modelClass = Turma::class;

    public function find($id)
    {
        return $this->newQuery()->find($id);
    }
    public function getRegenciasByEtapa($turma, $etapa, $rechumano = null)
    {
        $query = $this->find($turma)->regencias()->where('ed59_i_serie', $etapa);

        if (!is_null($rechumano)) {
            $query->whereHas('regenciasHorario', function ($query) use ($rechumano) {
                if (is_array($rechumano)) {
                    $query->whereIn('ed58_i_rechumano', $rechumano);
                } else {
                    $query->where('ed58_i_rechumano', $rechumano);
                }
            });
        }

        return $query->get();
    }
}
