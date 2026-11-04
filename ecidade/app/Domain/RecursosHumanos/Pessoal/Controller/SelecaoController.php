<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Services\SelecaoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use BusinessException;

class SelecaoController extends Controller
{
    public function getList(Request $request)
    {
        try {
            $selecaoService = new SelecaoService($request->DB_instit);
            $selecoes = $selecaoService->getSelecoes();
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
        return new DBJsonResponse($selecoes);
    }

    public function find(Request $request)
    {
        try {
            $selecaoService = new SelecaoService($request->DB_instit);
            $selecoes = $selecaoService->getSelecao($request->codigo);
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
        return new DBJsonResponse($selecoes);
    }
}
