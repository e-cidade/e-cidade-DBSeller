<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller\AjudaCusto;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\AjudaCusto\AjudaCustoCsv;
use App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto\AjudaCustoConfigRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\AjudaCusto\AjudaCustoRequest;
use App\Domain\RecursosHumanos\Pessoal\Services\AjudaCusto\AjudaCustoService;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\AjudaCusto\AjudaCustoPdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AjudaCustoController extends Controller
{

    public function salvarConfig(AjudaCustoConfigRequest $resquest, AjudaCustoService $service)
    {
        $service->saveConfigFromRequest($resquest);
        return new DBJsonResponse([], 'Salvo com sucesso.');
    }

    public function buscarConfig($instituicao, AjudaCustoService $service)
    {
        return new DBJsonResponse(
            [
                'configuracao' => $service->getConfigByInstit($instituicao)
            ]
        );
    }

    public function store(AjudaCustoRequest $resquest, AjudaCustoService $service)
    {
        $service->salvar($resquest);
        return new DBJsonResponse([], 'Salvo com sucesso.');
    }

    public function destroy($codigo, AjudaCustoService $service)
    {
        $service->excluir($codigo);
    }

    public function show($codigo, AjudaCustoService $service)
    {
        return new DBJsonResponse($service->getAjudaCusto($codigo));
    }

    public function update(AjudaCustoRequest $resquest, AjudaCustoService $service)
    {
        $service->salvar($resquest);
        return new DBJsonResponse([], 'Salvo com sucesso.');
    }

    public function index(Request $request, AjudaCustoService $service)
    {
        return new DBJsonResponse($service->getServidoresLancados($request));
    }

    public function lancamentos($id, Request $request, AjudaCustoService $service)
    {
        return new DBJsonResponse($service->lancamentos($id));
    }

    public function processar(Request $request, AjudaCustoService $service)
    {
        $service->processar($request);
        return new DBJsonResponse([], 'Processado com sucesso.');
    }

    public function relatorio(Request $request, AjudaCustoService $service)
    {
        $data = $service->getDadosRelatorio($request);
        if ($data->isEmpty()) {
            return new DBJsonResponse([], 'Não foram encontrados registros para o filtro informado', 401);
        }

        if ($request->tipo == 2) {
            $emissao = new AjudaCustoCsv($data->toArray());
        } else {
            $emissao = new AjudaCustoPdf($data->toArray());
        }

        return new DBJsonResponse($emissao->emitir());
    }
}
