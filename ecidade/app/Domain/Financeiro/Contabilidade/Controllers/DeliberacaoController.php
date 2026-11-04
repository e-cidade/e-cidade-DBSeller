<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\Deliberacao\Modelos\ModeloCincoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliberacaoController extends Controller
{
    public function modelo5(Request $request)
    {
        $sercice = new ModeloCincoService();
        $sercice->setFiltros($request->all());
        $files = $sercice->emitir();

        return new DBJsonResponse($files, 'Deliberação 285 - Modelo 5');
    }
}
