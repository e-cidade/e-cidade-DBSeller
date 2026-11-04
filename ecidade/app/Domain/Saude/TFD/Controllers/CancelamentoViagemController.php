<?php

namespace App\Domain\Saude\TFD\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\TFD\Requests\CancelaVeiculoRequest;
use App\Domain\Saude\TFD\Resources\GetCancelamentoResource;
use App\Domain\Saude\TFD\Services\CancelamentoViagemService;

class CancelamentoViagemController
{
    /**
     * @var CancelamentoViagemService
     */
    private $service;

    public function __construct(CancelamentoViagemService $service)
    {
        $this->service = $service;
    }
    public function cancelaVeiculo(CancelaVeiculoRequest $request)
    {
        return new DBJsonResponse($this->service->cancelaVeiculo($request));
    }

    public function getCancelamento($cancelamento)
    {
        $dados = $this->service->getCancelamento($cancelamento);
        $dadosCancelamento = GetCancelamentoResource::toArray($dados);
        return new DBJsonResponse($dadosCancelamento);
    }
}
