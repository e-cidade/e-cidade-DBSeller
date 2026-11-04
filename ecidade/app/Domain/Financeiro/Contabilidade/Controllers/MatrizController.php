<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\MSC\ImportarMatrizRequest;
use App\Domain\Financeiro\Contabilidade\Services\ImportarSaldosMatrizService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\Conferencia\MatrizAtributoFpService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MatrizService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class MatrizController extends Controller
{
    public function importar(ImportarMatrizRequest $request)
    {
        $service = new ImportarSaldosMatrizService();
        $service->importar($request->all());

        return new DBJsonResponse([], 'Processamento concluído');
    }
    public function emitir(Request $request)
    {
        $service = new MatrizService();
        $service->setFiltrosArray($request->all());
        return new DBJsonResponse($service->processar(), 'Processamento concluído');
    }

    public function atributoFP($exercicio)
    {
        $service = new MatrizAtributoFpService();
        return new DBJsonResponse($service->emitir($exercicio), 'Consistência - Atributo FP');
    }
}
