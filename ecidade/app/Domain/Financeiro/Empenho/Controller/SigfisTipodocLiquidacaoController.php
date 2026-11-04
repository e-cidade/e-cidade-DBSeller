<?php

namespace App\Domain\Financeiro\Empenho\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Empenho\Services\SigfisTipodocLiquidacaoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use stdClass;

class SigfisTipodocLiquidacaoController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new SigfisTipodocLiquidacaoService;
    }

    public function getTipos(Request $request)
    {
        try {
            $oRetorno = new stdClass;
            $oRetorno->tipos = $this->service->getTipos();
            return new DBJsonResponse($oRetorno);
        } catch (\Throwable $th) {
            return new DBJsonResponse(null, $th->getMessage(), 401);
        }
    }
}
