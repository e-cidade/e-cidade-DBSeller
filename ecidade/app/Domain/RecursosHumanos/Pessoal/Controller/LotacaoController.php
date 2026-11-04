<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Services\LotacaoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use BusinessException;

class LotacaoController extends Controller
{
    public function getList(Request $request)
    {
        $lotacaoService = new LotacaoService($request->DB_instit);
        $lotacoes = $lotacaoService->getLotacoes();
        return  new DBJsonResponse($lotacoes);
    }

    public function find(Request $request)
    {
        try {
            $lotacaoService = new LotacaoService($request->DB_instit);
            $lotacao = $lotacaoService->getLotacao($request->codigo);
        } catch (Exception $e) {
            throw new BusinessException($e->getMessage());
        }
        return new DBJsonResponse($lotacao);
    }

    public function getListActive(Request $request)
    {
        $lotacaoService = new LotacaoService($request->DB_instit);
        $lotacoes = $lotacaoService->getLotacoesAtivas();
        return  new DBJsonResponse($lotacoes);
    }
}
