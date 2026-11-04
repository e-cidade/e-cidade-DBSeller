<?php

namespace App\Domain\Financeiro\Tesouraria\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Tesouraria\Resources\ContaResource;
use App\Domain\Financeiro\Tesouraria\Services\ContasService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Trata as contas da tesouraria
 */
class ContasController extends Controller
{

    public function index(Request $request)
    {
        $service = new ContasService();
        return new DBJsonResponse(ContaResource::toArray($service->get($request->all())));
    }
}
