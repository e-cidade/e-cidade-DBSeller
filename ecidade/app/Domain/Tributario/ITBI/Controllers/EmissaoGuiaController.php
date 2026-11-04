<?php

namespace App\Domain\Tributario\ITBI\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ITBI\Services\EmissaoGuiaService;
use App\Http\Controllers\Controller;
use App\Domain\Tributario\ITBI\Requests\EmissaoGuiaRequest;

class EmissaoGuiaController extends Controller
{
    public function emitir(EmissaoGuiaRequest $request)
    {
        $emissaoGuiaService = new EmissaoGuiaService();
        $emissaoGuiaService->setNumeroGuia($request->numeroGuia);

        if ($emissaoGuiaService->verificaGuiaPaga()) {
            return new DBJsonResponse([
                'mensagem' => "A guia {$request->numeroGuia} já está paga.",
                'erro' => false,
                'paga' => true
            ]);
        }

        $emissaoGuiaService->setMostraArquivo(false);
        $emissaoGuiaService->emitir();

        return new DBJsonResponse([
            "arquivo" => $emissaoGuiaService->getArquivo()
        ]);
    }
}
