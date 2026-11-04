<?php

namespace App\Domain\Tributario\ITBI\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ITBI\Services\AutenticacaoService;
use App\Domain\Tributario\ITBI\Requests\AutenticacaoRequest;
use App\Http\Controllers\Controller;

class AutenticacaoController extends Controller
{
    /**
     * Faz a autenticação do recibo
     * @param AutenticacaoRequest $request
     * @return DBJsonResponse
     */
    public function autenticar(AutenticacaoRequest $request)
    {
        $autenticacaoService = new AutenticacaoService();
        $autenticacaoService->setNumeroGuia($request->numeroGuia);
        $retorno = $autenticacaoService->autenticar();
        if ($retorno === false) {
            return new DBJsonResponse([
                'mensagem' => "A guia {$request->numeroGuia} não está paga.",
                'erro' => false,
                'paga' => false
            ]);
        }

        return new DBJsonResponse([
            "arquivo" => $autenticacaoService->getArquivo()
        ]);
    }
}
