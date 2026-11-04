<?php

namespace App\Domain\Tributario\Fiscalizacao\Controllers\Vistoria;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Fiscalizacao\Services\Vistoria\GetExerciciosCalculadosService;
use App\Domain\Tributario\Fiscalizacao\Services\Vistoria\GetCalculosVistoriaService;
use App\Domain\Tributario\Fiscalizacao\Services\Vistoria\GetTiposVistoriasCalculadosService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalculoVistoriaController extends Controller
{
    public function getCalculos(Request $request, GetCalculosVistoriaService $service)
    {
        $this->validate($request, [
            'exercicio' => 'required|integer',
            'numcgm' => 'required|integer',
            'tipoVistoria' => 'required|integer'
        ]);

        try {
            $calculos = $service->execute($request->only(['exercicio', 'numcgm', 'tipoVistoria']));
            return new DBJsonResponse($calculos);
        } catch (BusinessException $e) {
            return new DBJsonResponse('', $e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse('', 'Erro Interno ao buscar os calculos.', 500);
        }
    }

    public function getExerciciosCalculados(Request $request, GetExerciciosCalculadosService $service)
    {
        $this->validate($request, [
            'numcgm' => 'required|integer'
        ]);

        try {
            $exercicios = $service->execute($request->only(['numcgm']));
            return new DBJsonResponse($exercicios);
        } catch (BusinessException $e) {
            return new DBJsonResponse('', $e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse('', 'Erro Interno ao buscar os exercicios.', 500);
        }
    }

    public function getTiposVistoriasCalculados(Request $request, GetTiposVistoriasCalculadosService $service)
    {
        $this->validate($request, [
            'numcgm' => 'required|integer'
        ]);

        try {
            $exercicios = $service->execute($request->only(['numcgm']));
            return new DBJsonResponse($exercicios);
        } catch (BusinessException $e) {
            return new DBJsonResponse('', $e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse('', 'Erro Interno ao buscar os tipos de vistorias.', 500);
        }
    }
}
