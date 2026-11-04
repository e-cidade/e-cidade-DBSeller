<?php

namespace App\Domain\Educacao\CentralMatriculas\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Helpers\ConfiguracaoCentralHelper;
use App\Domain\Educacao\CentralMatriculas\Services\ConfiguracoesService;
use App\Domain\Educacao\MatriculaOnline\Models\ConfiguracaoGeral;
use App\Domain\Educacao\MatriculaOnline\Models\CorPersonalizada;
use App\Domain\Educacao\MatriculaOnline\Resources\ConfiguracaoGeralResource;
use App\Domain\Educacao\MatriculaOnline\Resources\CorPersonalizadaResource;
use App\Http\Controllers\Controller;

class ConfiguracoesController extends Controller
{
    protected $service;
    public function __construct(ConfiguracoesService $service)
    {
        $this->service = $service;
    }


    public function getImagens()
    {
        $retorno = $this->service->getImagens();
        return new DBJsonResponse($retorno);
    }

    public function getDocumentos()
    {
        $retorno = $this->service->getDocumentos();
        return new DBJsonResponse($retorno);
    }

    public function getDuvidas()
    {
        $retorno = $this->service->getDuvidas();
        return new DBJsonResponse($retorno);
    }


    public function getConfiguracaoGeral()
    {
        $retorno = ConfiguracaoGeral::all()->map(function ($config) {
            return ConfiguracaoGeralResource::toResponse($config);
        });
        return new DBJsonResponse($retorno->first());
    }
}
