<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Requests\BuscaPacienteProntuarioRequest;
use App\Domain\Saude\Ambulatorial\Requests\StatusPacienteRequest;
use App\Domain\Saude\Ambulatorial\Resources\BuscaPacienteProntuarioResource;
use App\Domain\Saude\Ambulatorial\Resources\StatusPacienteResource;
use App\Domain\Saude\Ambulatorial\Services\BuscaPacienteProntuarioService;
use App\Domain\Saude\Ambulatorial\Services\StatusPacienteService;
use App\Http\Controllers\Controller;

class StatusPacienteController extends Controller
{
    /**
     * @var StatusPacienteService
     */
    private $service;

    public function __construct(StatusPacienteService $service)
    {
        $this->service = $service;
    }

    public function buscaPacienteProntuario(BuscaPacienteProntuarioRequest $requisicao)
    {
        $cgs = $this->service->buscaPacienteProntuario($requisicao->all());
        $pacientes = BuscaPacienteProntuarioResource::toArray($cgs);
        return new DBJsonResponse($pacientes);
    }

    public function buscaDados(StatusPacienteRequest $requisicao)
    {
        $dados = $this->service->buscaDados($requisicao->all());
        $atendimentos = StatusPacienteResource::toArray($dados);
        return new DBJsonResponse($atendimentos);
    }
}
