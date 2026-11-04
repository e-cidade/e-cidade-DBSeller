<?php

namespace App\Domain\Financeiro\Tesouraria\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Tesouraria\Requests\ConfiguracaoReceitasDecendioRequest;
use App\Domain\Financeiro\Tesouraria\Requests\GetDetalhamentoDecendioRequest;
use App\Domain\Financeiro\Tesouraria\Requests\GetLancamentosDecendioRequest;
use App\Domain\Financeiro\Tesouraria\Resources\TipoDesdobramentoResource;
use App\Domain\Financeiro\Tesouraria\Services\ConfiguracaoReceitasDecendioService;
use App\Domain\Financeiro\Tesouraria\Services\GerarSlipsDecendioService;
use App\Domain\Financeiro\Tesouraria\Services\Relatorios\DecendioRelatorioService;
use App\Domain\Financeiro\Tesouraria\Services\TipoDesdobramentoService;
use App\Domain\Financeiro\Tesouraria\Services\ValoresDecendioService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DecendioController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @api {get} v4/api/financeiro/tesouraria/decendio/receitas Retorna as receitas para configurar o decêndio
     * @apiName DecendioReceitas
     * @apiGroup decendio-receitas
     * @apiDescription Configura as receitas para configurar quais vão ser usadas no decêndio e os desdobramentos que
     *                 cada uma vai executar
     *
     * @apiParam {Number} exercicio Exercício da receita
     * @apiParam {Array} receitas Lista das receitas
     * @apiParam {Array} desdobramentos Lista dos desdobramentos para configurar nas receitas
     *
     * @apiParam {Number} exercicio exercício para buscar as receitas
     * @apiParam {String} estrutural parte do estrutural a ser buscado
     * @apiParam {Mixed} [configurada] Caso querira só as receitas que estão configuradas
     * @apiParam {Number} [rows] Caso vá usar paginação
     *
     * @apiSuccess {Object} response objeto de resposta com um array com as receitas
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     * "message": "Receitas configuradas com sucesso.",
     * "error": false,
     * "data": [{}]
     * }
     */
    public function getReceitas(Request $request)
    {
        $service = new ConfiguracaoReceitasDecendioService();
        $receitas = $service->getReceitas($request->all());
        return new DBJsonResponse(array_values($receitas), "Receitas encontradas.");
    }

    /**
     * @param ConfiguracaoReceitasDecendioRequest $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/tesouraria/decendio/receitas Configura as receitas do decêndio
     * @apiName DecendioReceitas
     * @apiGroup decendio-receitas
     * @apiDescription Configura as receitas do decêndio
     *
     * @apiParam {Number} exercicio Exercício da receita
     * @apiParam {Array} receitas Lista das receitas
     * @apiParam {Array} desdobramentos Lista dos desdobramentos para configurar nas receitas
     *
     * @apiSuccess {Object} response objeto de resposta
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *        "message": "Receitas configuradas com sucesso.",
     *        "error": false,
     *        "data": []
     *      }
     */
    public function configurarReceitas(ConfiguracaoReceitasDecendioRequest $request)
    {
        $service = new ConfiguracaoReceitasDecendioService();
        $service->configurarReceitas(
            $request->get('exercicio'),
            $request->get('receitas'),
            $request->get('origem'),
            $request->get('desdobramentos')
        );
        return new DBJsonResponse([], "Receitas configuradas com sucesso.");
    }

    /**
     * @api {post} v4/api/financeiro/tesouraria/decendio/receitas/excluir Excluir Receitas
     * @apiName DecendioReceitasExcluir
     * @apiGroup decendio-receitas
     * @apiDescription Este método exclui a configuração de receitas com base nos parâmetros de solicitação fornecidos.
     *
     * @apiParam {Number} exercicio Exercício da receita
     * @apiParam {Array} receitas Lista das receitas
     *
     * @apiSuccess {Object} response objeto de resposta
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "Removida configuração das receitas com sucesso.",
     *       "error": false,
     *       "data": []
     *     }
     */
    public function excluirReceitas(Request $request)
    {
        $service = new ConfiguracaoReceitasDecendioService();
        $service->excluirConfiguracaoReceitas(
            $request->get('exercicio'),
            $request->get('receitas')
        );
        return new DBJsonResponse([], "Removida configuração da receitas com sucesso.");
    }

    public function getTipoDesdobramento(Request $request)
    {
        $service = new TipoDesdobramentoService();
        $tipoDeDesdobramento = TipoDesdobramentoResource::toArray($service->get($request->all()));
        return new DBJsonResponse($tipoDeDesdobramento, "Tipos de desdobramento");
    }

    public function salvarTipoDesdobramento(Request $request)
    {
        $service = new TipoDesdobramentoService();
        $service->salvar($request->all());
        return new DBJsonResponse([], "Tipo de Desenvolvimento salvo com sucesso.");
    }

    /**
     * @api {get} v4/api/financeiro/tesouraria/decendio/lancamentos Retorna os reduzidos que tem que realizar o
     * decêndio no período informado
     * @apiName DecendioReceitasLancamentos
     * @apiGroup decendio-receitas
     * @apiDescription Este método retorna os reduzidos que tem que realizar o decêndio no período informado
     *
     * @apiParam {Number} exercicio Exercício da receita
     * @apiParam {Array} receitas Lista das receitas
     * @apiParam {Number} instituicao Instituição do sistema
     * @apiParam {String} dataInicial data para filtro inicial
     * @apiParam {String} dataFinal data para filtro final
     *
     * @apiSuccess {Object} response objeto de resposta
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "message": "Removida configuração das receitas com sucesso.",
     *       "error": false,
     *       "data": {
     *         "receita": {
     *            "reduzidos": "8607,8659",
     *            "estrutural": "11125001000000",
     *            "classes": "4,9",
     *            "descricao": "IPTU PREDIAL E TERIITORIAL URBANO PRINCIPAL"
     *         },
     *         "conta_pagadora": {
     *            "reduzido": 17256,
     *            "estrutural": "111111902010200",
     *            "descricao": "BB PM CAPIVARI DO SUL CAIXA",
     *            "recurso": 1
     *         },
     *         "valor": "217261.68",
     *         "instituicao": 1,
     *         "lancamentos": [1223,1224],
     *         "desdobramento": {
     *            "codigo": 1,
     *            "percentual": 25,
     *            "descricao": "MDE",
     *            "valor": 54315.42,
     *            "recursos": [20, 8753],
     *            "contasBancarias": [
     *               {
     *                 "conta": 17347,
     *                 "nome": "17347 - BANRI  MANUT DESENV. DO ENSINO  MDE"
     *               },
     *            ]
     *         },
     *         "id": "72fc339e24b1f",
     *         "contaSelecionada": {
     *           "conta": 17347,
     *           "nome": "17347 - BANRI  MANUT DESENV. DO ENSINO  MDE"
     *         },
     *         "apenasDeducao": false
     *         }
     *     }
     */
    public function getLancamentos(GetLancamentosDecendioRequest $request)
    {
        $service = new ValoresDecendioService();
        $valores = $service->processarValoresDescendio(
            $request->get('exercicio'),
            $request->get('instituicao'),
            $request->get('dataInicial'),
            $request->get('dataFinal')
        );

        return new DBJsonResponse($valores, "Valores decêndio período.");
    }

    public function getDetalhamento(GetDetalhamentoDecendioRequest $request)
    {
        $service = new ValoresDecendioService();
        $valores = $service->buscaDetalhamentoReceitaDecendio($request->all());
        return new DBJsonResponse($valores, "Valores da receita para o decêndio no período.");
    }

    public function gerarSlips(Request $request)
    {
        $service = new GerarSlipsDecendioService();
        $gerarSlips = $service->gerarSlips(
            $request->get('receitas'),
            $request->get('DB_anousu'),
            $request->get('dataInicial'),
            $request->get('dataFinal')
        );

        return new DBJsonResponse($gerarSlips, "Slips gerados com sucesso.");
    }

    public function relatorio(Request $request)
    {
        $service = new DecendioRelatorioService();
        $files = $service->emitir(
            $request->get('dataInicial'),
            $request->get('dataFinal'),
            $request->get('instituicao')
        );

        return new DBJsonResponse($files, "Relatório de decêndio gerado com sucesso.");
    }
}
