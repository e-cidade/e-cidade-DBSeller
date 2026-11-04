<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Controllers;

use App\Domain\Configuracao\GeradorRelatorio\Requests\ExecutaSqlRequest;
use App\Domain\Configuracao\GeradorRelatorio\Services\ExecutaSqlService;
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
class ExecutaSqlController extends Controller
{
    /**
     * @param ExecutaSqlRequest $request
     * @param ExecutaSqlService $service
     * @return DBJsonResponse
     * @throws \Exception
     *
     * @api {post} configuracao/gerador/relatorios/executa-sql 11 - Executar um SQL
     * @apiName ExecutaSql
     * @apiGroup Configuracao-GeradorRelatorio
     * @apiPermission usuario
     *
     * @apiBody {String} sql SQL a ser executado
     * @apiBody {[]} bindings Valores a substituir no sql.
     *
     * @apiSuccess {Object[]} data dados retornados
     * @apiSuccess {false} error indica que nao ocorreu erro
     *
     * @apiUse Error
     */
    public function handle(ExecutaSqlRequest $request, ExecutaSqlService $service)
    {
        return new DBJsonResponse($service->execute($request->sql, $request->bindings));
    }
}
