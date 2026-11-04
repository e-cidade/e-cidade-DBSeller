<?php

namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Orcamento\Resources\DotacaoResource;
use App\Domain\Financeiro\Orcamento\Services\DotacaoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DotacaoController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/orcamento/despesa/dotacoes 01 - Consulta Dotações
     * @apiDescription Consulta Dotações com paginação
     * @apiName DotacoesByFilters
     * @apiGroup orcamento-dotacoes
     *
     * @apiParam {Integer} exercicio Filtra as dotações do Exercício
     * @apiParam {Integer} instituicao Filtra as dotações da instituição
     * @apiParam {Integer} page Página em que se encontra
     * @apiParam {Integer} rows Número de linhas para retornar
     * @apiParam {String} [elemento] filtra pelo código elemento
     * @apiParam {String} [reduzido] filtra pelo redizido (coddot)
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *       HTTP/1.1 200 OK
     *       {
     *         "error":false,
     *         "message": "",
     *         "data":
     *         {
     *              "totalRegistros": 48,
     *              "dotacoes": [
     *                {
     *                  "exercicio": 2023,
     *                  "reduzido": 4659,
     *                  "saldoInicial": "167164.75",
     *                  "criacao": "2023-01-01",
     *                  "esferaOrcamentaria": 10,
     *                  "funcionalProgramatica": "002.012.004.062.0110.2364.8760000000000.1500.0001.0000",
     *                  "orgao": {
     *                    "codigo": 2,
     *                    "descricao": "GABINETE DO PREFEITO"
     *                  },
     *                  "unidade": {
     *                    "codigo": 12,
     *                    "descricao": "CHEFIA DO GABINETE"
     *                  },
     *                  "funcao": {
     *                    "codigo": 4,
     *                    "descricao": "ADMINISTRACAO"
     *                  },
     *                  "subfuncao": {
     *                    "codigo": 62,
     *                    "descricao": "DEFESA INTERESSE PUB PROCESSO JUDICIARIO"
     *                  },
     *                  "programa": {
     *                    "codigo": 110,
     *                    "descricao": "PROGRAMA DE GESTÃO E MANUT. DE SERVIÇOS"
     *                  },
     *                  "projeto": {
     *                    "codigo": 2364,
     *                    "descricao": "MANUTENÇÃO DA PROCURADORIA JURIDICA"
     *                  },
     *                  "elemento": {
     *                    "codigo": 876,
     *                    "elemento": "3319011000000",
     *                    "descricao": "VENCIMENTOS E VANTAGENS FIXAS - PESSOAL CIVIL"
     *                  },
     *                  "recurso": {
     *                    "orctiporec_id": 1,
     *                    "siconfi": "1500",
     *                    "gestao": "1500",
     *                    "subrecurso": "0001",
     *                    "complemento": {
     *                      "codigo": 0,
     *                      "descricao": "NÃO SE APLICA"
     *                    },
     *                    "descricao_fr": "Recursos não Vinculados de Impostos",
     *                    "descricao": "RECURSO LIVRE",
     *                    "apresentacao": "1500 - 0001 - 0 - RECURSO LIVRE"
     *                  },
     *                  "cp": {
     *                    "codigo": "000",
     *                    "descricao": "NÃO SE APLICA"
     *                  },
     *                  "instituicao": {
     *                    "codigo": 1,
     *                    "descricao": "PREFEITURA MUNICIPAL DE CAPIVARI DO SUL"
     *                  }
     *                },
     *              ]
     *         }
     */
    public function index(Request $request)
    {
        $dotacoes = (new DotacaoService())->get($request->all());
        return new DBJsonResponse(DotacaoResource::toArray($dotacoes));
    }
}
