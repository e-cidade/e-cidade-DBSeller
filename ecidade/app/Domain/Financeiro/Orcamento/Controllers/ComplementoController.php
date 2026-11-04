<?php


namespace App\Domain\Financeiro\Orcamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Resources\ConsultaComplementoResource;
use App\Domain\Financeiro\Orcamento\Requests\Cadastro\ComplementoExcluirRequest;
use App\Domain\Financeiro\Orcamento\Requests\Cadastro\ComplementoSalvarRequest;
use App\Domain\Financeiro\Orcamento\Services\ComplementoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

/**
 * Class ComplementoController
 * @package App\Domain\Financeiro\Orcamento\Controllers
 */
class ComplementoController extends Controller
{
    /**
     * @var ComplementoService
     */
    private $service;

    public function __construct(ComplementoService $service)
    {
        $this->service = $service;
    }

    /**
     * @api {get} v4/api/financeiro/orcamento/cadastro/complemento 01 Retorna todos os complementos
     * @apiDescription Retorna todos os complementos.
     * @apiName Complementos
     * @apiGroup orcamento-recursos-complementos
     */
    public function get()
    {
        return new DBJsonResponse($this->service->getAll()->toArray());
    }

    /**
     * @param ComplementoSalvarRequest $request
     * @return DBJsonResponse
     * @throws Exception
     *
     * @api {post} v4/api/financeiro/orcamento/cadastro/complemento/salvar 02 Salva o complemento
     * @apiDescription Cria ou altera um complemento
     * @apiName ComplementosSalvar
     * @apiGroup orcamento-recursos-complementos
     *
     * @apiBody {integer} codigo do complemento, se null, cria
     * @apiBody {String} descricao Nome do complemento
     * @apiBody {Boolean} msc Se vai para matriz de saldo contábeis
     * @apiBody {Boolean} tribunal Se vai para o PAD
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *     HTTP/1.1 200 OK
     *     {
     *       "error":false,
     *       "message": "Complemento salvo com sucesso.",
     *       "data":
     *       {
     *          "codigo" : 1,
     *          "descricao" : 'bla bla ',
     *          "msc" : false,
     *          "tribunal" : true
     *       }
     *     }
     */
    public function salvar(ComplementoSalvarRequest $request)
    {
        $complemento = $this->service->salvar($request);
        return new DBJsonResponse($complemento, "Complemento salvo com sucesso.");
    }

    /**
     * @param ComplementoExcluirRequest $request
     * @return DBJsonResponse
     * @throws Exception
     *
     * @api {post} v4/api/financeiro/orcamento/cadastro/complemento/excluir 03 Exclui o complemento
     * @apiDescription Exclui o complemento
     * @apiName ComplementosExcluir
     * @apiGroup orcamento-recursos-complementos
     *
     * @apiBody {integer} codigo do complemento a ser excluído
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     */
    public function excluir(ComplementoExcluirRequest $request)
    {
        $this->service->excluir($request->get('codigo'));
        return new DBJsonResponse([], "Complemento excluído com sucesso.");
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {post} v4/api/financeiro/orcamento/recursos/complementos 04 - Consulta de complementos
     * @apiDescription Consulta de complementos por filtros
     * @apiName ComplementosByFilters
     * @apiGroup orcamento-recursos-complementos
     *
     * @apiParam {String} [autocomplete] String de filtro dinâmico. Filtra o código e o nome.
     * @apiParam {Integer} [codigo] filtra por código.
     * @apiParam {String} [descricao] filtra pelo nome ou parte dele.
     *
     * @apiSuccess {String} message Mensagem de sucesso
     * @apiSuccess {false} error Indica que nao ocorreu erro
     * @apiSuccessExample {json} Resposta
     *      HTTP/1.1 200 OK
     *      {
     *        "error":false,
     *        "message": "Complemento salvo com sucesso.",
     *        "data":
     *        {
     *           "codigo" : 1,
     *           "descricao" : 'bla bla ',
     *           "msc" : false,
     *           "tribunal" : true
     *        }
     *      }
     */
    public function byFilters(Request $request)
    {
        return new DBJsonResponse(
            array_values(ConsultaComplementoResource::toArray($this->service->getByFilters($request))),
            "Complementos encontrados."
        );
    }
}
