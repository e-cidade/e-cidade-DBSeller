<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Resources\ReceitaResource;
use App\Domain\Financeiro\Orcamento\Services\ReceitaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/orcamento/receita/receitas 01 - Consulta Receitas
     * @apiDescription Consulta Receitas com paginação
     * @apiName ReceutasByFilters
     * @apiGroup orcamento-receitas
     *
     * @apiParam {Integer} exercicio Filtra as dotações do Exercício
     * @apiParam {Integer} instituicao Filtra as dotações da instituição
     * @apiParam {Integer} page Página em que se encontra
     * @apiParam {Integer} rows Número de linhas para retornar
     * @apiParam {String} [reduzido] filtra pelo redizido (codrec)
     * @apiParam {String} [codigoNaturezaReceita] Filtra pelo código da Natureza de Receita (codfon)
     * @apiParam {String} [estrutural] Filtra pelo estrutural da Natureza de Receita
     * @apiParam {String} [codigoRecurso] Filtra pelo código do recurso
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *     {
     *       "totalRegistros": 1,
     *       "receitas": [
     *         {
     *           "exercicio": 2023,
     *           "reduzido": 8607,
     *           "saldoInicial": "1038956.81",
     *           "naturezaReceita": {
     *             "codigo": 509888,
     *             "estrutural": "411125001000000",
     *             "descricao": "IPTU PREDIAL E TERIITORIAL URBANO PRINCIPAL"
     *           },
     *           "recurso": {
     *             "orctiporec_id": 1104,
     *             "siconfi": "1501",
     *             "gestao": "1501",
     *             "subrecurso": "1104",
     *             "complemento": {
     *               "codigo": 0,
     *               "descricao": null
     *             },
     *             "descricao_fr": "Outros Recursos não Vinculados",
     *             "descricao": "ALIENAÇÃO DE BENS",
     *             "apresentacao": "1501 - 1104 - 0 - ALIENAÇÃO DE BENS"
     *           },
     *           "instituicao": {
     *             "codigo": 1,
     *             "descricao": "PREFEITURA MUNICIPAL DE CAPIVARI DO SUL"
     *           },
     *           "cp": {
     *             "codigo": "000",
     *             "descricao": "NÃO SE APLICA"
     *           },
     *           "criacao": "2023-01-01",
     *           "unidade": {
     *             "orgao": 4,
     *             "unidade": 43,
     *             "descricao": "DEP. PLANEJAMENTO E GESTÃO",
     *             "orgaoUnidade": "0400"
     *           },
     *           "esferaOrcamentaria": {
     *             "codigo": 10,
     *             "descricao": "F - Orçamento Fiscal"
     *           }
     *         }
     *       ]
     *     }
     */
    public function index(Request $request)
    {
        $receitas = (new ReceitaService())->get($request->all());
        return new DBJsonResponse(ReceitaResource::toArray($receitas));
    }
}
