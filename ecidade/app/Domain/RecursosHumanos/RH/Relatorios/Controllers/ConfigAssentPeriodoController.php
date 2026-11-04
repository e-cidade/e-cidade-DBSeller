<?php

namespace App\Domain\RecursosHumanos\RH\Relatorios\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\RH\Relatorios\Services\ConfigAssentPeriodoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use InstituicaoRepository;
use stdClass;

class ConfigAssentPeriodoController extends Controller
{
    private $oService;

    public function __construct(ConfigAssentPeriodoService $oService)
    {
        $this->oService = $oService;
    }

    public function getConfig()
    {
        $oResponse = new stdClass;
        $iInstit   = session('DB_instit');

        $oConfig = $this->oService->getByInstit($iInstit);
        $oResponse->config = $oConfig;

        $sNomeInstit = InstituicaoRepository::getInstituicaoByCodigo($iInstit)->getDescricao();
        $oResponse->institDescr = $iInstit . ' - ' . $sNomeInstit;

        return new DBJsonResponse($oResponse);
    }

    public function saveConfig(Request $request)
    {
        $iInstit = session('DB_instit');

        try {
            return $this->oService->save((object) $request->all(), $iInstit);
        } catch (\Exception $e) {
            return new DBJsonResponse('', $e->getMessage(), 500);
        }
    }

    public function getAssentamentos()
    {
        $oResponse = new stdClass;
        $oResponse->assentamentos = $this->oService->getAssentamentos();
        return new DBJsonResponse($oResponse);
    }
}
