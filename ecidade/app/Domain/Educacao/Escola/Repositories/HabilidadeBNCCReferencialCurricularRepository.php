<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\HabilidadesBNCCReferencial;

class HabilidadeBNCCReferencialCurricularRepository extends BaseRepository
{
    protected $modelClass = HabilidadesBNCCReferencial::class;

    public function getByFilters(array $filters)
    {
        $query = $this->newQuery();
        foreach ($filters as $key => $filter) {
            switch ($key) {
                case 'ano':
                    $query->where('ed168_ano', $filter);
                    break;
                case 'habilidade':
                    if (is_array($filter)) {
                        $query->whereIn('ed168_codigohabilidade', $filter);
                    } elseif (in_array('referencial', $filters)) {
                        $query->where('ed168_codigoreferencial', $filter);
                    } else {
                        $query->where('ed168_codigohabilidade', $filter);
                    }

                    break;
                case 'objetoConhecimento':
                    $query->where('ed168_objeto_conhecimento', $filter);
                    break;
                case 'etapa':
                    $where = [];
                    foreach ($filter as $etapa) {
                        $where[] = "trim(ed168_etapa) ilike '%{$etapa}%'";
                    }
                    $where = implode(' or ', $where);
                    $query->whereRaw("({$where})");
                    break;
            }
        }

        return $query->distinct()->get();
    }
}
