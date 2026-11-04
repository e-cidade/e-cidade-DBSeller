<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\DisciplinaBNCC;
use App\Domain\Educacao\Escola\Models\HabilidadesBNCCFundamental;

class HabilidadesBNCCFundamentalRepository extends BaseRepository
{
    protected $modelClass = HabilidadesBNCCFundamental::class;

    public function getHabilidadesByEtapaDisciplina($ano, DisciplinaBNCC $disciplinaBNCC, $etapas)
    {
        $query = $this->newQuery()
            ->where('ed148_ano', $ano)
            ->whereRaw("trim(ed148_disciplina) = trim('{$disciplinaBNCC->ed149_nome}')");

        $whereEtapas = [];
        foreach ($etapas as $etapa) {
            $whereEtapas[] = "trim(ed148_etapa) ilike '%{$etapa->ed152_etapa}%'";
        }

        $whereEtapas = implode(' or ', $whereEtapas);
        $query->whereRaw("({$whereEtapas})");

        return $query->get();
    }
}
