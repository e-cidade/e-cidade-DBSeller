<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Services\TabelaPrevidenciaService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use BusinessException;

class TabelaPrevidenciaController extends Controller
{
    public function getList(Request $request)
    {
        $tabelasPrevidencia = [];
        $tabelasPrevidenciaService = new TabelaPrevidenciaService($request->DB_instit);
        $tabelasPrevidencia = $tabelasPrevidenciaService->getTabelasPrevidencia();
        return new DBJsonResponse($tabelasPrevidencia);
    }
}
