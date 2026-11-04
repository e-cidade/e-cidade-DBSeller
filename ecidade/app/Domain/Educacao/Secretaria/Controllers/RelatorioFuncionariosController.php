<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Secretaria\Relatorios\FuncionariosAtivosPDF;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class RelatorioFuncionariosController extends Controller
{

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function funcionariosAtivosEscola(Request $request)
    {
        $dataInicial = $request->get('dataInicial');
        $dataFinal = $request->get('dataFinal');

        $escola = Escola::find(db_getsession('DB_coddepto'));
        $dadosCabecalho = (object)[
            "escola" => $escola,
            "dataInicial" => $dataInicial,
            "dataFinal" => $dataFinal
        ];

        $filtros = (object)$request->only(['funcoes', 'cargos']);
        if (db_getsession('DB_modulo') != 7159) {
            $filtros->escolas = db_getsession('DB_coddepto');
        } else {
            $filtros->escolas = $request->get('escolas');
        }

        $relatorioPdf = new FuncionariosAtivosPDF($dadosCabecalho, $filtros);

        return new DBJsonResponse($relatorioPdf->emitirPdf());
    }
}
