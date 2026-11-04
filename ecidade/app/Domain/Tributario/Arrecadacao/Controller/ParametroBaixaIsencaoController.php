<?php

namespace App\Domain\Tributario\Arrecadacao\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Services\ParametroBaixaIsencaoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParametroBaixaIsencaoController extends Controller
{
    private $service;

    public function __construct(ParametroBaixaIsencaoService $service)
    {
        $this->service = $service;
    }

    public function salvar(Request $request)
    {
        return new DBJsonResponse(
            $this->service->salvar($request),
            'Parâmetro da Baixa de Isenção cadastrado com sucesso.'
        );
    }
    public function excluir($id)
    {
        return new DBJsonResponse($this->service->excluir($id), 'Parâmetro da Baixa de Isenção excluido com sucesso.');
    }
    public function listar()
    {
        return new DBJsonResponse($this->service->listar(), 'Lista de Parâmetros da Baixa de Isenção.');
    }
}
