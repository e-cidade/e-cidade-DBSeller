<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Requests\AdicionarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\BuscarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\DeletarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Requests\EditarGrupoTaxasRequest;
use App\Domain\Tributario\Arrecadacao\Services\GrupoTaxasService;
use App\Http\Controllers\Controller;

class GrupoTaxasController extends Controller
{
    /**
     * @var GrupoTaxasService
     */
    private $grupoTaxasService;

    /**
     * @param GrupoTaxasService $grupoTaxasService
     * @return void
     */
    public function __construct(GrupoTaxasService $grupoTaxasService)
    {
        $this->grupoTaxasService = $grupoTaxasService;
    }

    /**
     * @param AdicionarGrupoTaxasRequest $request
     * @return void
     */
    public function adicionar(AdicionarGrupoTaxasRequest $request)
    {
        $this->grupoTaxasService->adicionar($request);

        return;
    }

    /**
     * @param EditarGrupoTaxasRequest $request
     * @return void
     */
    public function editar(EditarGrupoTaxasRequest $request)
    {
        $this->grupoTaxasService->editar($request);

        return;
    }

    /**
     * @param DeletarGrupoTaxasRequest $request
     * @return void
     */
    public function deletar(DeletarGrupoTaxasRequest $request)
    {
        $this->grupoTaxasService->deletar($request);

        return;
    }

    /**
     * @param BuscarGrupoTaxasRequest $request
     * @return Array
     */
    public function buscar(BuscarGrupoTaxasRequest $request)
    {
        $resultado = $this->grupoTaxasService->buscar($request);

        return new DBJsonResponse($resultado);
    }

    /**
     * @return Array
     */
    public function getRotulos()
    {
        $resultado = $this->grupoTaxasService->getRotulos();

        return new DBJsonResponse($resultado);
    }
}
