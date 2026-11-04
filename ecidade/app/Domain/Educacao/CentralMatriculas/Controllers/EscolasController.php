<?php

namespace App\Domain\Educacao\CentralMatriculas\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Requests\EscolasDisponiveisRequest;
use App\Domain\Educacao\CentralMatriculas\Services\EscolasService;
use App\Http\Controllers\Controller;
use Exception;

class EscolasController extends Controller
{
    protected $service;
    public function __construct(EscolasService $service)
    {
        $this->service = $service;
    }
    /**
     * @param EscolasDisponiveisRequest $escolasDisponiveisRequest
     * @return DBJsonResponse
     * @throws Exception
     */
    public function disponiveisPreMatricula($fase, $etapa)
    {
        $escolas = $this->service->buscarEscolasDisponiveis($etapa, $fase);

        return new DBJsonResponse($escolas->toArray());
    }

    public function escolasBairro()
    {
        return new DBJsonResponse($this->service->getEscolasPorBairro());
    }
}
