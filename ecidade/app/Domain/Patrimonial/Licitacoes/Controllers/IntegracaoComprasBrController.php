<?php

namespace App\Domain\Patrimonial\Licitacoes\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Licitacoes\Requests\ReprocessamentoComprasBrRequest;
use App\Domain\Patrimonial\Licitacoes\Services\IntegracaoComprasBrExportService;
use App\Domain\Patrimonial\Licitacoes\Services\IntegracaoComprasBrImportService;
use App\Domain\Patrimonial\Licitacoes\Requests\ImportacaoComprasBrRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

class IntegracaoComprasBrController extends Controller
{
    /**
     * Exportar arquivo do compras BR
     *
     * @param ImportacaoComprasBrRequest $request
     * @return \Illuminate\Http\JsonResponse|DBJsonResponse
     */
    public function import(ImportacaoComprasBrRequest $request, IntegracaoComprasBrImportService $service)
    {
        try {
            return $service->importFilePregao($request);
        } catch (Exception $e) {
            return new DBJsonResponse(null, $e->getMessage(), 500);
        }
    }

    public function reprocessar(ReprocessamentoComprasBrRequest $request, IntegracaoComprasBrImportService $service)
    {
        try {
            return $service->reprocessar($request);
        } catch (Exception $e) {
            $mensagem = $e->getMessage();
            if ($e instanceof \PDOException) {
                $mensagem = 'Falha ao remover informações da importação.';
            }

            return new DBJsonResponse(null, $mensagem, 500);
        }
    }

    /**
     * Exportar arquivo para compras BR
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $this->validate($request, [
            'licitacao' => 'required|numeric',
            'casas_decimais' => 'required|in:2,3,4'
        ]);

        $licitacao = $request->licitacao;
        $service = new IntegracaoComprasBrExportService;
        $service->setCasasDecimais($request->casas_decimais);

        try {
            $file = $service->exportFilePregao($licitacao);
            return response()->json($file);
        } catch (Exception $e) {
            return new DBJsonResponse('', $e->getMessage(), 500);
        }
    }
}
