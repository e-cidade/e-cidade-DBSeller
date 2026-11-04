<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Resources\NaturezaDespesaResource;
use App\Domain\Financeiro\Orcamento\Resources\NaturezaReceitaResource;
use App\Domain\Financeiro\Orcamento\Services\NaturezaReceitaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NaturezaReceitaController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/orcamento/receitas/natureza-receita 01 - Consulta Natureza Receita
     * @apiDescription Consulta das Naturezas de Receita
     * @apiName NaturezaReceitaIndex
     * @apiGroup orcamento-natureza-receita
     *
     * @apiParam {Integer} exercicio Filtra as Naturezas de Receita do Exercício
     * @apiParam {Integer} page Página em que se encontra
     * @apiParam {Integer} rows Número de linhas para retornar
     * @apiParam {String} [estrutural] filtra pelo estrutural da natureza
     * @apiParam {String} [autocomplete] filtra pelo conjunto da natureza + descrição com ilike %%
     * @apiParam {String} [apenasComReceita] filtra as naturezas que possuem receita no exercício
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *         HTTP/1.1 200 OK
     *         {
     *           "error":false,
     *           "message": "",
     *           "data":
     * {
     *              "totalRegistros": 19,
     *              "naturezas": [
     *                {
     *                  "codigo": 1238,
     *                  "exercicio": 2023,
     *                  "elemento": "411125001000000",
     *                  "estrutural": "IPTU PREDIAL E TERIITORIAL URBANO PRINCIPAL"
     *                  "apresentar": "411125001000000 - IPTU PREDIAL E TERIITORIAL URBANO PRINCIPAL"
     *                },
     *               ]
     *            }
     * /
     */
    public function index(Request $request)
    {
        $elementos = (new NaturezaReceitaService())->get($request->all());
        return new DBJsonResponse((new NaturezaReceitaResource())->toArray($elementos));
    }
}
