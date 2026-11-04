<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Repositories\EspecialidadeProfissionalRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EspecialidadeProfissionalService
{
    /**
     * @return Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public function recuperaProfissionaisAtivos()
    {
        return (new EspecialidadeProfissionalRepository())->recuperaProfissionaisAtivos();
    }
}
