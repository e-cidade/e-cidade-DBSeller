<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\ApropriacaoDecimoFeriasService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ApropriacaoDecimoFeriasController extends Controller
{

    public function buscarCompetenciaApropriar(Request $request)
    {
        $data = ApropriacaoDecimoFeriasService::proximaCompetenciaApropriar($request->get('DB_instit'));
        return new DBJsonResponse($data, 'Processado com sucesso.');
    }

    public function buscarCompetenciaEstornar(Request $request)
    {
        $data = ApropriacaoDecimoFeriasService::proximaCompetenciaExtornar($request->get('DB_instit'));
        return new DBJsonResponse($data, 'Processado com sucesso.');
    }

    public function buscarValores(Request $request)
    {
        $service = new ApropriacaoDecimoFeriasService(
            $request->get('DB_instit'),
            $request->get('exercicio'),
            $request->get('mes'),
            date('Y-m-d', $request->get('DB_datausu'))
        );
        $calculo = array_values($service->buscarValoresApropriar()->toArray());

        return new DBJsonResponse($calculo, 'Valores calculados para apropriação.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function apropriar(Request $request)
    {
        $service = new ApropriacaoDecimoFeriasService(
            $request->get('DB_instit'),
            $request->get('exercicio'),
            $request->get('mes'),
            date('Y-m-d', $request->get('DB_datausu'))
        );

        $service->apropriar();
        return new DBJsonResponse([], 'Valores apropriados.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function estornar(Request $request)
    {
        $service = new ApropriacaoDecimoFeriasService(
            $request->get('DB_instit'),
            $request->get('exercicio'),
            $request->get('mes'),
            date('Y-m-d', $request->get('DB_datausu'))
        );

        $service->estornar();
        return new DBJsonResponse([], 'Lançamentos extornados.');
    }
}
