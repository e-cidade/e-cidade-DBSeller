<?php

namespace App\Domain\Patrimonial\PNCP\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\PNCP\Requests\IntegracaoRequest;
use App\Domain\Patrimonial\PNCP\Services\HabilitarIntegracaoService;
use App\Http\Controllers\Controller;
use function App\Domain\Patrimonial\Patrimonio\Controllers\env;
use Illuminate\Http\Request;

class IntegracaoController extends Controller
{
    /**
     * @param Request $request
     * @param HabilitarIntegracaoService $service
     * @return DBJsonResponse|void
     */
    public function habilitar(Request $request, HabilitarIntegracaoService $service)
    {
        $dado = \InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));
        if ($request->request->get('habilitar_pncp') === "habilitar") {
            $habilitarPNCP = $service->habilitarIntegracaoPNCP(
                $dado->getCNPJ(),
                $request->DB_instit,
                $request->DB_id_usuario
            );

            return new DBJsonResponse([], $habilitarPNCP);
        }
    }

    /**
     * @param Request $request
     * @param HabilitarIntegracaoService $service
     * @return DBJsonResponse
     */
    public function verificaIntegracao(Request $request, HabilitarIntegracaoService $service)
    {
        $verificaEnteAutorizado = $service->verificaIntegracao($request->get('DB_instit'));
        return new DBJsonResponse($verificaEnteAutorizado, '');
    }

    /**
     * @param Request $request
     * @param HabilitarIntegracaoService $service
     * @return DBJsonResponse
     * @throws \Exception
     */
    public function incluirOrgao(Request $request, HabilitarIntegracaoService $service)
    {
        $response = $service->incluirOrgao($request);
        return new DBJsonResponse($response, '');
    }

    public function bloquearUnidade(Request $request, HabilitarIntegracaoService $service)
    {
        $response = $service->bloquearUnidade($request);
        return new DBJsonResponse($response, '');
    }
}
