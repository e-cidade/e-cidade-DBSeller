<?php

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\Regencia;

class RegenciaRepository extends BaseRepository
{
    protected $modelClass = Regencia::class;

    public function find($id)
    {
        return $this->newQuery()->find($id);
    }
}
