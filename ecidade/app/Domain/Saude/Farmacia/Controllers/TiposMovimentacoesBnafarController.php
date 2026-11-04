<?php

namespace App\Domain\Saude\Farmacia\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Farmacia\Models\TipoMovimentacaoBnafar;
use App\Http\Controllers\Controller;

class TiposMovimentacoesBnafarController extends Controller
{
    public function get($movimentacao)
    {
        return new DBJsonResponse(
            TipoMovimentacaoBnafar::byMovimentacao($movimentacao)->get(),
            'Tipos de movimentações encontradas!'
        );
    }
}
