<?php

namespace App\Domain\Patrimonial\Licitacoes\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Licitacoes\Exceptions\BncException;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use App\Domain\Patrimonial\Licitacoes\Services\BncService;
use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class BncController extends Controller
{
    /**
     * @param Licitacao $licitacao
     * @param BncService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function exportar(Licitacao $licitacao, BncService $service)
    {
        try {
            $response = $service->exportar($licitacao);

            $procedimento = !empty($response['IdIntegProcess']) ? 'atualizada' : 'cadastrada';
            $mensagem = "Licitação {$procedimento} com sucesso no BNC!";
        } catch (ClientException $e) {
            throw new BncException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse([], $mensagem);
    }

    /**
     * @param Licitacao $licitacao
     * @param BncService $service
     * @return DBJsonResponse
     * @throws Exception
     */
    public function importar(Licitacao $licitacao, BncService $service)
    {
        try {
            $service->importar($licitacao);
        } catch (ClientException $e) {
            throw new BncException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        } catch (Exception $e) {
            throw new Exception("Nenhum dado importado: {$e->getMessage()}");
        }

        return new DBJsonResponse(
            [],
            'Ação realizada com sucesso! Verifique os dados importados no e-cidade.'
        );
    }

    /**
     * @param Licitacao $licitacao
     * @param Request $request
     * @param BncService $service
     * @return DBJsonResponse
     */
    public function buscar(Licitacao $licitacao, Request $request, BncService $service)
    {
        try {
            $response = $service->buscar($licitacao, $request);
        } catch (ClientException $e) {
            throw new BncException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse($response, '');
    }

    /**
     * @param Licitacao $licitacao
     * @param BncService $service
     * @return DBJsonResponse
     */
    public function excluir(Licitacao $licitacao, BncService $service)
    {
        try {
            $service->excluir($licitacao);
        } catch (ClientException $e) {
            throw new BncException(
                $e->getMessage(),
                $e->getRequest(),
                $e->getResponse()
            );
        }

        return new DBJsonResponse([], 'Licitação excluída do portal BNC com sucesso!');
    }
}
