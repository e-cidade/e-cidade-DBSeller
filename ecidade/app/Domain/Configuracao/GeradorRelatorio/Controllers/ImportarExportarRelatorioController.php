<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Models\Relatorio;
use App\Domain\Configuracao\GeradorRelatorio\Requests\ImportarRelatorioRequest;
use App\Domain\Configuracao\GeradorRelatorio\Services\ImportarExportarRelatorioService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;

/**
 * @apiDefine usuario Usuario access only
 * Requisicao deve ser feita por um usuario logado
 */
/**
 * @apiDefine Error
 * @apiError {[]} data
 * @apiError {true} error Indica que ocorreu um erro
 * @apiError {String} message Mensagem de erro
 */
class ImportarExportarRelatorioController extends Controller
{
    /**
     * @var ImportarExportarRelatorioService
     */
    private $service;

    public function __construct(ImportarExportarRelatorioService $service)
    {
        $this->service = $service;
    }

    /**
     * @api {post} configuracao/gerador/relatorios/importar 09 - Importar Relatorio
     * @apiName ImportarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiError {[]} data
     * @apiError {true} error Indica que ocorreu um erro
     * @apiError {String} message Mensagem de erro
     */
    public function importar(ImportarRelatorioRequest $request)
    {
        $this->service->importar($request->all());

        return new DBJsonResponse([], 'Relatório importado com sucesso.');
    }

    /**
     * @api {get} configuracao/gerador/relatorios/:id/exportar 10 - Exportar Relatorio
     * @apiName ExportarRelatorio
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission auth:user
     *
     * @apiParam {Integer} id Código do relatorio
     *
     * @apiSuccess {Object} data Dados do relatorio
     * @apiSuccess {String} data.name Nome do relatorio
     * @apiSuccess {String} data.path Caminho do relatorio para download
     * @apiSuccess {false} error Indica que nao ocorreu erro
     *
     * @apiError {[]} data
     * @apiError {true} error Indica que ocorreu um erro
     * @apiError {String} message Mensagem de erro
     */
    public function exportar(Relatorio $relatorio)
    {
        return new DBJsonResponse($this->service->exportar($relatorio));
    }
}
