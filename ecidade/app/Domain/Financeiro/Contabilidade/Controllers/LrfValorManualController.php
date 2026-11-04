<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\GetValoresLinhaLrf;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\SalvarValoresLinhaLrfRequest;
use App\Domain\Financeiro\Contabilidade\Resources\Lrf\ValoresManuaisResource;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\ValorManualService;
use App\Http\Controllers\Controller;

class LrfValorManualController extends Controller
{
    public function get(GetValoresLinhaLrf $request)
    {
        $service = new ValorManualService();
        $dados = $service->getByFilters($request->all());
        return new DBJsonResponse(ValoresManuaisResource::toArray($dados), 'Linhas Manuais');
    }

    public function salvar(SalvarValoresLinhaLrfRequest $request)
    {
        $service = new ValorManualService();
        $codigo = $service->salvar($request->all());
        return new DBJsonResponse($codigo, 'Valor manual adicionado com sucesso.');
    }

    public function delete($codigo)
    {
        $service = new ValorManualService();
        $service->delete($codigo);

        return new DBJsonResponse([], 'Valor deletado com sucesso.');
    }
}
