<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Resources\ClassificacoesRiscoResource;
use App\Domain\Saude\Ambulatorial\Services\ClassificacoesRiscoService;

class ClassificacoesRiscoController
{
    /**
     * @var ClassificacoesRiscoService
     */
    private $service;

    public function __construct(ClassificacoesRiscoService $service)
    {
        $this->service = $service;
    }

    public function getClassificacoes()
    {
        $dados = $this->service->recuperaClassificacoes();
        $classificacoes = ClassificacoesRiscoResource::toArray($dados);
        return new DBJsonResponse($classificacoes);
    }
}
