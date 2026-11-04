<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Requests\LRF\LrfNotaExplicativaRequest;
use App\Domain\Financeiro\Contabilidade\Resources\Lrf\NotaExplicativaResource;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\NotaExplicativaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LrfNotaExplicativaController extends Controller
{

    public function get(Request $request)
    {
        $dados = (new NotaExplicativaService())->getByFilters($request->all());
        return new DBJsonResponse(NotaExplicativaResource::toArray($dados), 'Notas Explicativas');
    }

    public function salvar(LrfNotaExplicativaRequest $request)
    {
        $service = new NotaExplicativaService();
        $codigo = $service->salvar($request->all());
        return new DBJsonResponse($codigo, 'Nota Explicativa salva com sucesso.');
    }

    public function delete($codigo)
    {
        (new NotaExplicativaService())->delete($codigo);
        return new DBJsonResponse([], 'Nota Explicativa deletada com sucesso.');
    }
}
