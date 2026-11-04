<?php

namespace App\Domain\Educacao\Secretaria\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\Disciplina;

class DIsciplinaRepository extends BaseRepository
{
    protected $modelClass = Disciplina::class;

    public function index()
    {
        return $this->newQuery()->get();
    }
}
