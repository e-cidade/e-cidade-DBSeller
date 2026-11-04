<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\RH\Relatorios\Services\CertidaoTempoContribuicaoService;
use App\Http\Controllers\Controller;
use InstituicaoRepository;
use stdClass;
use App\Domain\RecursosHumanos\RH\Relatorios\Requests\GerarCertidaoTempoContribuicaoRequest;

class CertidaoTempoContribuicaoController extends Controller
{
    private $oService;

    public function __construct(CertidaoTempoContribuicaoService $oService)
    {
        $this->oService = $oService;
    }

    public function gerar(GerarCertidaoTempoContribuicaoRequest $request)
    {
        $this->oService->setMatricula($request->get('matricula'));
        $this->oService->setCodigoInstituicao($request->get('DB_instit'));
        $this->oService->setAno($request->get('ano'));
        $this->oService->setNumero($request->get('numero'));

        if (!empty($request->get('periodoInicial'))) {
            $this->oService->setPeriodoInicial($request->get('periodoInicial'));
        }
        if (!empty($request->get('periodoFinal'))) {
            $this->oService->setPeriodoFinal($request->get('periodoFinal'));
        }
        $oResponse = $this->oService->gerar();
        return new DBJsonResponse($oResponse, "Processamento realizado com sucesso.");
    }
}
