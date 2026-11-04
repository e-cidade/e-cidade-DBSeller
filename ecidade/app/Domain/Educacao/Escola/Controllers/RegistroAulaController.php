<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Requests\RegistroAulaRequest;
use App\Domain\Educacao\Escola\Resources\ConteudoDesenvolvidoResource;
use App\Domain\Educacao\Escola\Services\RegistroAulaService;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RegistroAulaController extends Controller
{
    protected $service;

    /**
     * @param $service
     */
    public function __construct(RegistroAulaService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     *
     * @api {get} educacao/diario-classe/registro-aula/get-dados-regente/{escola}/{user} 01 - Buscar dados do Regente
     * @apiName BuscarDadosRegente
     * @apiGroup Educacao-Escola
     * @apiPermission usuario
     * @apiVersion 1.0.0
     *
     * @apiDescription Retorna um array de objetos com suas respectivas propriedades
     *
     * @apiSuccess {Object[]} data Array com dados do regente
     * @apiSuccess {String} data.nome Nome do Regente
     * @apiSuccess {Number} data.cgm CGM do Regente
     * @apiSuccess {Number} data.usuario Codigo de Usuario do Regente
     * @apiSuccess {Array} data.turmas Array com as Turmas Que o Regente Leciona
     * @apiSuccess {false} error Indica que não ocorreu erro
     *
     * @apiSuccessExample {json} Exemplo:
     *     HTTP/1.1 200 OK
     *     {
     *       "data": [
     *         {
     *            "nome": "Nome Teste 1",
     *            "cgm": 21,
     *            "cgm": 22,
     *            "turmas": []
     *         },
     *         {
     *            "nome": "Nome Teste 1",
     *            "cgm": 21,
     *            "cgm": 22,
     *            "turmas": []
     *         },
     *       ],
     *       "error": false,
     *       "message": ""
     *     }
     */
    public function getDadosRegente(Request $request, $escola, $user)
    {
        $dataSistema = Carbon::createFromTimestamp($request->get('DB_datausu'));
        $periodos = !is_null($request->get('periodos'));
        return new DBJsonResponse($this->service->getDadosRegente($user, $escola, $dataSistema, $periodos));
    }
    public function getConteudosRegenteEscola(Request $request)
    {
        $usuario = $request->get('usuario');
        $escola = $request->get('DB_coddepto');
        $linhas = $request->query('items') ? $request->query('items', 5) : null;
        $filtros = $request->query();
        $filtros['dataSistema'] = Carbon::createFromTimestamp($request->get('DB_datausu'));

        return new DBJsonResponse($this->service->getConteudosRegenteEscola($usuario, $escola, $linhas, $filtros));
    }

    public function salvarConteudo(RegistroAulaRequest $request)
    {
        return new DBJsonResponse(
            ConteudoDesenvolvidoResource::toResponse($this->service->salvarConteudo($request->all()))
        );
    }

    public function excluirConteudo($codigo)
    {
        return new DBJsonResponse($this->service->excluirConteudo($codigo));
    }

    public function getDisciplinasBnccByRegencia($regencia)
    {
        return new DBJsonResponse($this->service->getDisciplinasBncc($regencia));
    }
    public function getHabilidadesBnccByRegenciaDisciplinaBncc(Request $request)
    {
        $regencia = $request->get('regencia');
        $disciplinaBncc = $request->get('disciplinaBncc');
        return new DBJsonResponse($this->service->getHabilidadesBncc($regencia, $disciplinaBncc));
    }

    public function getHabilidadesDesenvolvidas(Request $request)
    {
        return new DBJsonResponse($this->service->getHabilidadesDesenvolvidas($request->all()));
    }

    public function salvarHabilidadeDesenvolvida(Request $request)
    {
        return new DBJsonResponse($this->service->salvarHabilidadeDesenvolvida($request->all()));
    }

    public function updateConteudosLote(Request $request)
    {
        $resposta = $this->service->updateConteudosLote($request->conteudos);
        if (count($resposta) > 0) {
            $ids = implode(', ', $resposta);
            return new DBJsonResponse(null, "Conteudos não salvos: {$ids}");
        }

        return new DBJsonResponse(null, "Conteudos salvos com sucesso");
    }
}
