<?php

namespace App\Domain\Financeiro\Empenho\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Empenho\Services\NaturezaRendimentoService;
use App\Http\Controllers\Controller;
use BusinessException;
use Illuminate\Http\Request;
use stdClass;

class NaturezaRendimentoController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new NaturezaRendimentoService;
    }

    public function getNaturezasPorGrupo(Request $request)
    {
        $this->validate($request, ['grupo' => 'required|numeric']);

        try {
            $response = new stdClass;
            $naturezas = $this->service->getNaturezasPorGrupo($request->grupo);
            $response->naturezas = $naturezas;

            return new DBJsonResponse($response);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        }
    }

    public function getGrupos(Request $request)
    {
        try {
            $response = new stdClass;
            $grupos = $this->service->getGrupos();
            $response->grupos = $grupos;

            return new DBJsonResponse($response);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        }
    }

    public function getNaturezas(Request $request)
    {
        try {
            $response = new stdClass;
            $response->naturezas = $this->service->getNaturezas($request);
            return new DBJsonResponse($response);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        }
    }
}
