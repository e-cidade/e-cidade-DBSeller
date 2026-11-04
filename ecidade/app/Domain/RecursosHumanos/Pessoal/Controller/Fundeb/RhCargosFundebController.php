<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\Fundeb;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\Fundeb\RhCargosFundeb;
use App\Domain\RecursosHumanos\Pessoal\Requests\Fundeb\CargosFundebRequest;
use App\Domain\RecursosHumanos\Pessoal\Services\Fundeb\FundebService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

/**
 * Class RhCargosFundebController
 * @package App\Domain\RecursosHumanos\Pessoal\Controller
 */

class RhCargosFundebController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     * @var FundebService
     */
    public function listCargosFundeb(Request $request)
    {
        try {
            $campos = ['rh283_codigo', 'rh283_descricao'];
            if ($request->rh283_codigo) {
                $rhcargosfundeb = RhCargosFundeb::where(
                    'rh283_codigo',
                    $request->rh283_codigo
                )->get($campos);
            } else {
                $rhcargosfundeb = RhCargosFundeb::all($campos);
            }
            return new DBJsonResponse($rhcargosfundeb);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }

    private $service;

    public function __construct(FundebService $service)
    {
        $this->service = $service;
    }

     /**
     * @param $id
     * @return DBJsonResponse
     */
    public function show(CargosFundebRequest $request)
    {
        return new DBJsonResponse($this->service->findCargos($request), 'Cargos Fundeb encontrado.');
    }

    /**
     * @param CargosFundebRequest $request
     * @return DBJsonResponse
     */

    public function save(CargosFundebRequest $request)
    {
        return new DBJsonResponse($this->service->salvarCargosFundeb($request), 'Cargo salvo com sucesso.');
    }

    /*public function delete(CargosFundebRequest $request)
    {
        $this->service->removerCargosFundeb($request->get('rh283_codigo'));
        return new DBJsonResponse([], 'Cargo removido com sucesso.');
    } */
}
