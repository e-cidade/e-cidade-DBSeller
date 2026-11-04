<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Services\TransposicaoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use JSON;

class CriteriosTransposicaoController extends Controller
{
    /**
     * @var TransposicaoService
     */
    private $service;

    public function __construct()
    {
        $this->service = new TransposicaoService();
    }
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function criteriosOrdenacao(Request $request)
    {
        $ano = $request->get('ano');
        $etapa = $request->get('etapa');
        $criteriosOrdenacao = $this->service->getCriteriosOrdenacao($ano, $etapa);
        return new DBJsonResponse($criteriosOrdenacao);
    }

    public function salvarCriteriosOrdenacao(Request $request)
    {
        $criteriosSalvar = $request->get('criteriosOrdenacao');
        $criteriosSalvar = JSON::create()->parse(str_replace("\\", "", $criteriosSalvar));
        $criteriosOrdenacao = $this->service->salvarCriteriosOrdenacao($criteriosSalvar);
        return new DBJsonResponse($criteriosOrdenacao);
    }
}
