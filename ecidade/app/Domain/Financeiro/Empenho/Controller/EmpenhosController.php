<?php

namespace App\Domain\Financeiro\Empenho\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Empenho\Repositories\EmpenhoRepository;
use App\Domain\Financeiro\Empenho\Resources\EmpenhoResource;
use App\Domain\Financeiro\Empenho\Services\DialogEmpenhoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpenhosController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/empenho/empenhos/dialog-pesquisa Consulta de empenhos resumida
     * @apiDescription Consulta de empenhos para apresentação no dialog de pesquisa
     * @apiName EmpnhoResumida
     * @apiGroup empenho
     *
     *
     * @apiParam {Number} instituicao Instituicao
     * @apiParam {Number} [exercicio] Exercício
     * @apiParam {Number} [numero] Número do empenho no exercício
     * @apiParam {String} [numemp] id do empenho
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *        HTTP/1.1 200 OK
     *        {
     *          "error":false,
     *          "message": "",
     *          "data": {
     *              "totalRegistros": 6492,
     *              "empenhos": [{
     *                 "numemp": 121577,
     *                 "numero": "6492",
     *                 "exercicio": 2023,
     *                 "dataEmissao": "2023-11-03",
     *                 "cgm": {
     *                    "codigo": 5959,
     *                    "nome": "SIMONE DA SILVEIRA FERREIRA"
     *                 },
     *                 "valorEmpenhado": "11143.95",
     *                 "valorAnulado": "0",
     *                 "valorLiquidado": "11143.95",
     *                 "valorPago": "0",
     *                 "saldoLiquido": null,
     *                 "saldo": "11143.95"
     *                 }
     *              }]
     */
    public function dialogPesquisa(Request $request)
    {
        $empenhos = (new DialogEmpenhoService())->get($request->all());
        return new DBJsonResponse(EmpenhoResource::toDialog($empenhos));
    }
}
