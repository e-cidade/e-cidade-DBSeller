<?php

namespace App\Domain\Saude\TFD\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\TFD\Resources\GetPassageirosRetornoResource;
use App\Domain\Saude\TFD\Services\IndiqueVeiculoService;
use App\Http\Controllers\Controller;

class IndiqueVeiculoController extends Controller
{
    /**
     * @var IndiqueVeiculoService
     */
    private $service;

    public function __construct(IndiqueVeiculoService $service)
    {
        $this->service = $service;
    }

    public function getPassageirosCancelados($viagem)
    {
        return new DBJsonResponse($this->service->getPassageirosCancelados($viagem));
    }

    public function getPassageirosRetorno($viagem)
    {
        $dados = $this->service->getPassageirosRetorno($viagem);
        $passageirosRetorno = GetPassageirosRetornoResource::toArray($dados);
        return new DBJsonResponse($passageirosRetorno);
    }

    public function getLotacaoCancelados($viagem)
    {
        return new DBJsonResponse($this->service->getLotacaoCancelados($viagem));
    }
}
