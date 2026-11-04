<?php

namespace App\Domain\Integracoes\EFDReinf\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Integracoes\EFDReinf\Services\ManutencaoRetencaoService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class ManutencaoRetencaoController extends Controller
{
    /**
     * Service da classe
     *
     * @var ManutencaoRetencaoService
     */
    private $service;

    /**
     * Construct Class
     */
    public function __construct()
    {
        $this->service = new ManutencaoRetencaoService;
    }

    /**
     * Lista retencoes pelo filtros informados
     *
     * @param Request $request
     * @return DBJsonResponse
     */
    public function getRetencoes(Request $request)
    {
        $filters = (object) $request->all();

        try {
            $retencoes = $this->service->getRetencoes($filters);
            return new DBJsonResponse($retencoes);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 500);
        } catch (Exception $e) {
            $errors = 'Manutencao Retencao: ' . $e->getMessage();
            Log::error($errors);

            return new DBJsonResponse(null, 'Erro Interno.', 500);
        }
    }

    /**
     * Salva os dados da retencao
     *
     * @param Request $request
     * @return DBJsonResponse
     */
    public function saveRetencao(Request $request)
    {
        $dados = (object) $request->all();

        try {
            $this->service->saveRetencao($dados);
            return new DBJsonResponse(null, 'Retencao salva com sucesso.');
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 500);
        } catch (Exception $e) {
            $errors = 'Manutencao Retencao: ' . $e->getMessage();
            Log::error($errors);

            return new DBJsonResponse(null, 'Erro Interno.', 500);
        }
    }

    /**
     * Tipo de Servico das Notas
     *
     * @param Request $request
     * @return DBJsonResponse
     */
    public function getTipoServicoNota(Request $request)
    {
        try {
            $response = new stdClass;
            $response->tipos = $this->service->getTipoServicoNota();
            return new DBJsonResponse($response);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 500);
        } catch (Exception $e) {
            $errors = 'Manutencao Retencao: ' . $e->getMessage();
            Log::error($errors);
            return new DBJsonResponse(null, 'Erro Interno.', 500);
        }
    }
}
