<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\TipoGuiaPrevidenciaPdf;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use BusinessException;
use App\Domain\RecursosHumanos\Pessoal\Services\Relatorios\TipoGuiaPrevidenciaService;

class TipoGuiaPrevidenciaController extends Controller
{
    /**
     * @param TipoGuiaPrevidenciaRequest $request
     * @return DBJsonResponse
     * @throws Exception
     *
     * @api {post} recursos-humanos/pessoal/tipo_guia_previdencia/emitir
     * Tipos de Guia de Previdencia (GPS)
     * @apiDescription Essa rota emite a guia de previdencia por tipo
     * @apiName TipoGuiaPrevidencia
     * @apiGroup Pessoal-Relatorios
     *
     * @apiHeaderExample {json} Request Header
     *   { "Authorization": "Bearer token" ,
     *     "Content-Type": "application/json",
     *     "Accept": "application/json"
     *  }
     *
     * @apiBody  {int} mes. Valores válidos 1 até 12
     * @apiBody  {int} ano. Valores válidos 1XXX ate 2XXX
     * @apiBody  {string} tipo. Valores válidos selecao/lotacao.
     * @apiBody  {int} codigoSelecao Se tipo=selecao apresenta codigo da selecao para escolher
     *  Exemplo de envio: codigoSelecao
     * @apiBody  {int} codigoLotacao. Se tipo=lotacao apresenta codigo da lotacao para escolher
     *  Exemplo de envio: codigoLotacao
     * @apiBody  {int} codigoPagamento.
     * @apiBody  {string} dataVencimento. Valores validos d/m/Y.
     * @apiBody  {string} arquivo. Valores validos salario/decimo.
     * @apiBody  {string} tipoGuia. Valores validos patronal/laboral.
     * @apiBody  {json} filtros tabelasPrevidencia.
     * Exemplo:
     *  {"tabelasPrevidencia":{"codigo":[],"operador":"in"}}
     * @apiSuccessExample {json} Resposta
     *       HTTP/1.1 200 OK
     *       {
     *         "error":false,
     *         "message": "Geracao da Guia de Previdência.",
     *         "data": {
     *             pdf: "tmp/tipo-guia-previdencia-123456789.pdf",
     *             pdfLinkExterno: "http://localhost/e-cidade/tmp/tipo-guia-previdencia-123456789.pdf"
     *         }
     *       }
     */
    public function emitir(Request $request)
    {
        $filtro=[];
        $service = new TipoGuiaPrevidenciaService();
        $service->setFiltrosRequest($request->all());
        $filtro[] = $service->emitir();
        $filtro['descricao'] = $request->descricao;
        $filtro['ano'] = $request->ano;
        $filtro['mes'] = $request->mes;
        $filtro['codigoPagamento'] = $request->codigoPagamento;
        $filtro['tipoGuia'] = $request->tipoGuia;
        $filtro['dataVencimento'] = $request->dataVencimento;

        $pdf = (new TipoGuiaPrevidenciaPdf($filtro))->emitir();
        return new DBJsonResponse($pdf, 'Emissão de tipo de Guia de Previdência.');
    }
}
