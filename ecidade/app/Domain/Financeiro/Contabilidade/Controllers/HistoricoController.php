<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Models\Historico;
use App\Domain\Financeiro\Contabilidade\Repositories\HistoricoRepository;
use App\Domain\Financeiro\Contabilidade\Resources\HistoricoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoricoController
{
    public function index(Request $request)
    {
        $historicos = (new HistoricoRepository())->getByFilters($request->all(), ['c50_codhist']);
        return new DBJsonResponse(HistoricoResource::toArray($historicos), 'Históricos encontrados.');
    }
}
