<?php

namespace App\Domain\Educacao\MatriculaOnline\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\MatriculaOnline\Models\CamposOpcionais;

class CamposOpcionaisRepository extends BaseRepository
{
    protected $modelClass = CamposOpcionais::class;

    public function index()
    {
        return $this->newQuery()->get();
    }
}
