<?php

namespace App\Domain\Tributario\Diversos\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Diversos\Requests\ProcedenciaParamsRequest;
use App\Domain\Tributario\Diversos\Services\ProcedenciaService;
use App\Http\Controllers\Controller;

class ProcedenciaController extends Controller
{
    /**
     * @var ProcedenciaService
     */
    private $procedenciaService;

    public function __construct(ProcedenciaService $procedenciaService)
    {
        $this->procedenciaService = $procedenciaService;
    }

    /**
     * Metodo para buscar as labels para a tabela de pesquisa no frontend
     */
    public function getRotulos()
    {
        $rotulos = $this->procedenciaService->getRotulos();

        return new DBJsonResponse($rotulos);
    }

    public function getByParams(ProcedenciaParamsRequest $request)
    {
        $resultados = $this->procedenciaService->getByParams($request);

        return new DBJsonResponse($resultados);
    }
}
