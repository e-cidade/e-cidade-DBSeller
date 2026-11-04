<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Repositories\EtapaRepository;
use App\Domain\Educacao\Escola\Resources\EtapaResource;
use App\Domain\Educacao\MatriculaOnline\Resources\FaseResource;
use App\Domain\Educacao\MatriculaOnline\Services\VagasParciaisService;
use App\Http\Controllers\Controller;
use ECidade\Educacao\MatriculaOnline\Resource\ConfiguracaoEscolaEtapasConfiguradasResource;
use ECidade\Educacao\MatriculaOnline\Service\ConfiguracaoEscolaService;
use EscolaRepository;
use Exception;
use Illuminate\Http\Request;
use stdClass;

class VagasController extends Controller
{

    private $service;

    public function __construct(VagasParciaisService $service)
    {
        $this->service = $service;
    }
    public function getFasesAbertas()
    {
        $fasesAbertas = $this->service->getFasesAbertas()->map(function ($fase) {
            return FaseResource::toResponse($fase);
        });

        return new DBJsonResponse($fasesAbertas);
    }

    /**
     * @throws Exception
     */
    public function getEtapasConfiguradasPorEscola(Request $request, ConfiguracaoEscolaService $service)
    {
        $parameters = new stdClass();
        $parameters->ensino = reset($request->input('fase')['ciclo']['ensinos'])['codigo'];
        $etapas = new stdClass();
        $etapas->etapasConfiguradas = [];

        foreach ($request->input('escolas') as $escola) {
            $parameters->escola = $escola['code'];
            $config = $service->setParametrosFromRequest($parameters)->getConfiguracao();

            $etapasConfiguradas = ConfiguracaoEscolaEtapasConfiguradasResource::toArray($config);
            foreach ($etapasConfiguradas as $valor) {
                $codEtapa = $valor->codigo;

                if (!array_key_exists($codEtapa, $etapas->etapasConfiguradas)) {
                    $etapas->etapasConfiguradas[$codEtapa] = $valor;
                }
            }
        }

        usort($etapas->etapasConfiguradas, function ($a, $b) {
            return strcmp($a->descricao, $b->descricao);
        });

        return new DBJsonResponse($etapas);
    }

    public function getEscolasFaseAberta(Request $request)
    {
        $parameters = $request->all();
        $escolas = $this->service->getEscolasFase($parameters['fase']['code']);

        return new DBJsonResponse($escolas);
    }

    public function getTurnosFaseAberta(Request $request)
    {
        $parameters = $request->all();
        $turnos = $this->service->getTurnosFase($parameters);

        return new DBJsonResponse($turnos);
    }
}
