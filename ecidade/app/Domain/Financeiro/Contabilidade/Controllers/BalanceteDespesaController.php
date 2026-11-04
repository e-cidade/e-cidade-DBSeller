<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\EmissaoBalancetesRequest;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteDespesaPlanoPadraoService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\RelatorioBalanceteDespesaService;
use App\Http\Controllers\Controller;
use Exception;

class BalanceteDespesaController extends Controller
{
    /**
     * @param EmissaoBalancetesRequest $request
     * @return DBJsonResponse
     * @throws Exception
     *
     * @api {post} financeiro/contabilidade/relatorio/balancete-despena-por-complemento
     * Balancete da Despesa por complemento
     * @apiDescription Essa rota emite o balancete da despesa usando o plano orçamentário do e-cidade
     * @apiName BalanceteDespesaPorComplemento
     * @apiGroup Contabilidade-Balancetes
     *
     * @apiHeaderExample {json} Request Header
     *   { "Authorization": "Bearer token" ,
     *     "Content-Type": "application/json",
     *     "Accept": "application/json"
     *  }
     *
     * @apiBody  {string} modelo=analitico Modelo de impressão. Valores válidos analitico/sintetico
     * @apiBody  {array} nivel Se modelo=sintetico apresenta os níveis para escolher
     *  Opções válidas: orgao, unidade, funcao, subfuncao, programa, projeto, elemento, recurso
     *  Exemplo de envio: [funcao, elemento]
     *
     * @apiBody  {string} apresentarRecurso=fonteRecurso Fonte de recurso que será apresentada.
     *  Valores válidos: fonteRecurso/depara/siconfi
     * @apiBody  {string} tipoPlano=ecidade Plano Orçamentário que será usado para apresentar os dados.
     *  Valores válidos: ecidade
     * @apiBody  {string} dataInicio Data no formato BR. Exemplo: 01/01/2023
     * @apiBody  {string} dataFinal  Data no formato BR. Exemplo: 01/01/2023
     * @apiBody  {json} instituicoes Array com instituições selecionadas.
     *  Exemplo: [{"codigo":"1","nome":"PREFEITURA MUNICIPAL DE CAPIVARI DO SUL"}]
     * @apiBody  {json} filtros Outros filtros para emissão.
     * Exemplo:
     *  {"orgao":{"aOrgaos":[],"operador":"in"},"unidade":{"aUnidades":[],"operador":"in"},
     * "funcao":{"aFuncoes":[],"operador":"in"},"subfuncao":{"aSubFuncoes":[],"operador":"in"},
     * "programa":{"aProgramas":[],"operador":"in"},"projativ":{"aProjAtiv":[],"operador":"in"},
     * "elemento":{"aElementos":[],"operador":"in"},"recurso":{"aRecursos":[],"operador":"in"}
     * }
     *
     * @apiSuccessExample {json} Resposta modelo analitico
     *      HTTP/1.1 200 OK
     *      {
     *        "error":false,
     *        "message": "Emissão do balancete da despesa.",
     *        "data": {
     *            pdf: "tmp/balancete-despesa-1691677586.pdf",
     *            pdfLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.pdf",
     *            csv: "tmp/balancete-despesa-1691677586.csv",
     *            csvLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.csv"
     *        }
     *      }
     * @apiSuccessExample {json} Resposta modelo sintético
     *       HTTP/1.1 200 OK
     *       {
     *         "error":false,
     *         "message": "Emissão do balancete da despesa.",
     *         "data": {
     *             pdf: "tmp/balancete-despesa-1691677586.pdf",
     *             pdfLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.pdf"
     *         }
     *       }
     */
    public function emitirPorComplemento(EmissaoBalancetesRequest $request)
    {
        $service = new RelatorioBalanceteDespesaService();
        $service->setFiltrosRequest($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete da despesa.');
    }

    /**
     * @param EmissaoBalancetesRequest $request
     * @return DBJsonResponse
     * @throws Exception
     *
     *
     * @api {post} financeiro/contabilidade/relatorio/balancete-despena-plano-padrao
     *  Balancete da Despesa por complemento usando o plano padrão
     * @apiDescription Essa rota emite o balancete da despesa usando o plano padrão da União ou do Estado.
     *
     * @apiName BalanceteDespesaPlanoPadrao
     * @apiGroup Contabilidade-Balancetes
     *
     * @apiHeaderExample {json} Request Header
     *    { "Authorization": "Bearer token" ,
     *      "Content-Type": "application/json",
     *      "Accept": "application/json"
     *   }
     *
     * @apiBody  {string} modelo=analitico Modelo de impressão. Valores válidos analitico/sintetico
     * @apiBody  {array} nivel Se modelo=sintetico apresenta os níveis para escolher
     *   Opções válidas: orgao, unidade, funcao, subfuncao, programa, projeto, elemento, recurso
     *   Exemplo de envio: [funcao, elemento]
     *
     * @apiBody  {string} apresentarRecurso=fonteRecurso Fonte de recurso que será apresentada.
     *   Valores válidos: fonteRecurso/depara/siconfi
     * @apiBody  {string} tipoPlano=uniao Plano Orçamentário que será usado para apresentar os dados.
     *   Valores válidos: uniao/estadual
     * @apiBody  {string} dataInicio Data no formato BR. Exemplo: 01/01/2023
     * @apiBody  {string} dataFinal  Data no formato BR. Exemplo: 01/01/2023
     * @apiBody  {json} instituicoes Array com instituições selecionadas.
     *   Exemplo: [{"codigo":"1","nome":"PREFEITURA MUNICIPAL DE CAPIVARI DO SUL"}]
     * @apiBody  {json} filtros Outros filtros para emissão.
     *  Exemplo:
     *   {"orgao":{"aOrgaos":[],"operador":"in"},"unidade":{"aUnidades":[],"operador":"in"},
     *  "funcao":{"aFuncoes":[],"operador":"in"},"subfuncao":{"aSubFuncoes":[],"operador":"in"},
     *  "programa":{"aProgramas":[],"operador":"in"},"projativ":{"aProjAtiv":[],"operador":"in"},
     *  "elemento":{"aElementos":[],"operador":"in"},"recurso":{"aRecursos":[],"operador":"in"}
     *  }
     *
     * @apiSuccessExample {json} Resposta modelo analitico
     *       HTTP/1.1 200 OK
     *       {
     *         "error":false,
     *         "message": "Emissão do balancete da despesa.",
     *         "data": {
     *             pdf: "tmp/balancete-despesa-1691677586.pdf",
     *             pdfLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.pdf",
     *             csv: "tmp/balancete-despesa-1691677586.csv",
     *             csvLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.csv"
     *         }
     *       }
     * @apiSuccessExample {json} Resposta modelo sintético
     *        HTTP/1.1 200 OK
     *        {
     *          "error":false,
     *          "message": "Emissão do balancete da despesa.",
     *          "data": {
     *              pdf: "tmp/balancete-despesa-1691677586.pdf",
     *              pdfLinkExterno: "http://localhost/e-cidade/tmp/balancete-despesa-1691677586.pdf"
     *          }
     *        }
     */
    public function emitirPlanoPadrao(EmissaoBalancetesRequest $request)
    {
        $service = new BalanceteDespesaPlanoPadraoService();
        $service->setFiltrosRequest($request->all());
        $files = $service->emitir();
        return new DBJsonResponse($files, 'Emissão do balancete da despesa.');
    }
}
