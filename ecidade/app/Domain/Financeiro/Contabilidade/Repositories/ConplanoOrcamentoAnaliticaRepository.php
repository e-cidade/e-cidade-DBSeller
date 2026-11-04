<?php

namespace App\Domain\Financeiro\Contabilidade\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoOrcamentoAnalitica;

class ConplanoOrcamentoAnaliticaRepository extends BaseRepository
{
    protected $modelClass = ConplanoOrcamentoAnalitica::class;
}
