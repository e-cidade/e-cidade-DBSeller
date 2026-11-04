<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Resources\NaturezaDespesaResource;
use App\Domain\Financeiro\Orcamento\Services\NaturezaDespesaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 *
 */
class NaturezaDespesaController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/orcamento/despesa/naturezas-despesas 01 - Consulta Natureza Despesa
     * @apiDescription Consulta das Naturezas de Despesas
     * @apiName NaturezaDespesaIndex
     * @apiGroup orcamento-natureza-despesa
     *
     * @apiParam {Integer} exercicio Filtra as Naturezas de Despesas do Exercício
     * @apiParam {Integer} page Página em que se encontra
     * @apiParam {Integer} rows Número de linhas para retornar
     * @apiParam {String} [elemento] filtra pelo estrutural elemento
     * @apiParam {String} [autocomplete] filtra pelo conjunto do elemento + descrição com ilike %%
     * @apiParam {String} [apenasComDotacao] filtra os elementos que possuem dotacao no exercício
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *        HTTP/1.1 200 OK
     *        {
     *          "error":false,
     *          "message": "",
     *          "data":
     *           {
     *             "totalRegistros": 19,
     *             "elementos": [
     *               {
     *                 "codigo": 1238,
     *                 "exercicio": 2023,
     *                 "elemento": "3339039000000",
     *                 "descricao": "OUTROS SERVICOS DE TERCEIROS-PESSOA JURIDICA",
     *                 "apresentar": "3339039000000 - OUTROS SERVICOS DE TERCEIROS-PESSOA JURIDICA"
     *               },
     *              ]
     *           }
     */
    public function index(Request $request)
    {
        $elementos = (new NaturezaDespesaService())->get($request->all());
        return new DBJsonResponse((new NaturezaDespesaResource())->toArray($elementos));
    }
}
