<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\MapeamentoEmpenhoRPManualService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapeamentoEmpenhoRPManualController extends Controller
{
    public function index(Request $request)
    {
        $service = new MapeamentoEmpenhoRPManualService();

        return new DBJsonResponse($service->get($request->get('exercicio')), 'Valores');
    }

    public function save(Request $request)
    {
        $service = new MapeamentoEmpenhoRPManualService();
        $codigo = $service->salvar($request->all());
        return new DBJsonResponse($codigo, 'Registro salvo com sucesso.');
    }

    public function delete($codigo)
    {
        $service = new MapeamentoEmpenhoRPManualService();
        $service->deletar($codigo);
        return new DBJsonResponse(null, 'Registro excluído com sucesso.');
    }
}
