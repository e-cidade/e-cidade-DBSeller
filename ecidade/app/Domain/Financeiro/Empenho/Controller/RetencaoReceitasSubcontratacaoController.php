<?php

namespace App\Domain\Financeiro\Empenho\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Empenho\Services\RetencaoReceitasSubcontratacaoService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RetencaoReceitasSubcontratacaoController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = new RetencaoReceitasSubcontratacaoService;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'e163_sequencial'       => 'required|numeric',
            'e163_numcgm'           => 'required|numeric',
            'e163_retencaotiporec'  => 'required|numeric',
            'e163_valor'            => 'required|numeric',
            'e163_valorbase'        => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return new DBJsonResponse(
                $validator->errors(),
                'Erro ao editar subcontratação (campos inválidos).',
                422
            );
        }

        try {
            $this->service->update($request);
            return new DBJsonResponse('Subcontratação editada com sucesso');
        } catch (Exception $e) {
            return new DBJsonResponse('Erro ao editar subcontratação.', $e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), ['e163_sequencial' => 'required|numeric']);

        if ($validator->fails()) {
            return new DBJsonResponse(
                $validator->errors(),
                'Erro ao excluir subcontratação (campos inválidos).',
                422
            );
        }

        try {
            $this->service->delete($request);
            return new DBJsonResponse('Subcontratação excluída com sucesso');
        } catch (Exception $e) {
            return new DBJsonResponse('Erro ao excluir subcontratação.', $e->getMessage(), 500);
        }
    }
}
