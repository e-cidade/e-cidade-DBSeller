<?php

namespace App\Domain\Patrimonial\Licitacoes\Controllers;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Patrimonial\Contratos\Models\Acordo;
use App\Domain\Patrimonial\Licitacoes\Exceptions\LicitaconException;
use App\Domain\Patrimonial\Licitacoes\Requests\IncluirObraRequest;
use App\Domain\Patrimonial\Licitacoes\Requests\SalvarUsuarioRequest;
use App\Domain\Patrimonial\Licitacoes\Services\LicitaconObrasService;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Licitacoes\Requests\BuscarSubFamiliasRequest;
use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Exception\ClientException;

class LicitaconObrasController extends Controller
{
    /**
     * @param DBConfig $instituicao
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function verificarOrgaoFiscalizado(DBConfig $instituicao, LicitaconObrasService $service)
    {
        $fiscalizado = false;

        try {
            $fiscalizado = $service->verificarOrgaoFiscalizado($instituicao);
        } catch (ClientException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse(['fiscalizado' => $fiscalizado], '');
    }

    /**
     * @param Acordo $acordo
     * @param IncluirObraRequest $request
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function incluir(Acordo $acordo, IncluirObraRequest $request, LicitaconObrasService $service)
    {
        $response = [];

        try {
            $response = $service->incluir($acordo, $request);
        } catch (ClientException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($response, '');
    }

    /**
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarFamilias(LicitaconObrasService $service)
    {
        $familias = [];

        try {
            $familias = $service->buscarFamilias();
        } catch (ClientException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($familias, '');
    }

    /**
     * @param BuscarSubFamiliasRequest $request
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarSubFamilias(BuscarSubFamiliasRequest $request, LicitaconObrasService $service)
    {
        $subFamilias = [];

        try {
            $subFamilias = $service->buscarSubFamilias($request->familias);
        } catch (ClientException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($subFamilias, '');
    }

    /**
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarDetalhamentoCaracteristicas(LicitaconObrasService $service)
    {
        $detalhamento = [];

        try {
            $detalhamento = $service->buscarDetalhamentoCarateristicas();
        } catch (ClientException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($detalhamento, '');
    }

    /**
     * @param DBConfig $instituicao
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     */
    public function buscarUsuario(DBConfig $instituicao, LicitaconObrasService $service)
    {
        try {
            $usuario = $service->buscarUsuario($instituicao->codigo);
        } catch (LicitaconException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($usuario, '');
    }

    /**
     * @param SalvarUsuarioRequest $request
     * @param DBConfig $instituicao
     * @param LicitaconObrasService $service
     * @return DBJsonResponse
     * @throws \Throwable
     */
    public function salvarUsuario(SalvarUsuarioRequest $request, DBConfig $instituicao, LicitaconObrasService $service)
    {
        $usuario = [];

        try {
            $usuario = $service->salvarUsuario($instituicao->codigo, $request);
        } catch (LicitaconException $e) {
            throw new LicitaconException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($usuario, '');
    }
}
