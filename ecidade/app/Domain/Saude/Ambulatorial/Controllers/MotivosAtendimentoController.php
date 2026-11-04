<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Resources\MotivosAtendimentosResource;
use App\Domain\Saude\Ambulatorial\Services\MotivosAtendimentosService;
use App\Domain\Saude\Ambulatorial\Services\SetoresAmbulatoriaisService;

class MotivosAtendimentoController
{
    /**
     * @var SetoresAmbulatoriaisService
     */
    private $service;

    public function __construct(MotivosAtendimentosService $service)
    {
        $this->service = $service;
    }

    public function getMotivosAtendimento()
    {
        $dados = $this->service->recuperaMotivosAtendimentos();
        $motivos = MotivosAtendimentosResource::toArray($dados);
        return new DBJsonResponse($motivos);
    }
}
