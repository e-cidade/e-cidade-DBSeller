<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\DisciplinaBNCC;
use App\Domain\Educacao\Escola\Models\HabilidadesBNCCInfantil;

class HabilidadesBNCCInfantilRepository extends BaseRepository
{
    protected $modelClass = HabilidadesBNCCInfantil::class;

    public function getHabilidadesByDiscilina($ano, DisciplinaBNCC $disciplina)
    {
        return $this->newQuery()
            ->where('ed147_ano', $ano)
            ->whereRaw("trim(ed147_disciplina) = trim('{$disciplina->ed149_nome}')")
            ->get();
    }
}
