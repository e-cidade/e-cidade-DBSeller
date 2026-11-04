<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\AnexoXVIIService;
use ECidade\Core\Request\Request;
use Exception;
use ParameterException;

class AnexoXVIIController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return DBJsonResponse
     * @throws ParameterException
     */
    public function processar(\Illuminate\Http\Request $request)
    {
        $service = new AnexoXVIIService($request->all());
        $file = $service->emitirPdf();
        return new DBJsonResponse($file, 'Emissão do Relatório AnexoXVII');
    }
}
