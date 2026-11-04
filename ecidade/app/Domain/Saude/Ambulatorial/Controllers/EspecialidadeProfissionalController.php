<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Resources\EspecialidadeProfissionalResource;
use App\Domain\Saude\Ambulatorial\Services\EspecialidadeProfissionalService;

class EspecialidadeProfissionalController
{
    /**
     * @var EspecialidadeProfissionalService
     */
    private $service;

    public function __construct(EspecialidadeProfissionalService $service)
    {
        $this->service = $service;
    }

    public function getAtivas()
    {
        $dados = $this->service->recuperaProfissionaisAtivos();
        $especialidadeProfissionais = EspecialidadeProfissionalResource::toArray($dados);
        return new DBJsonResponse($especialidadeProfissionais);
    }
}
