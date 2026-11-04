<?php

namespace App\Domain\Integracoes\EFDReinf\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Integracoes\EFDReinf\Retencao\Factories\ComposicaoBaseCalculoFactory;
use App\Domain\Integracoes\EFDReinf\Retencao\ValueObjects\Evento;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComposicaoBaseCalculoController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'retencao' => 'required|integer',
            'evento' => 'required|string'
        ]);

        try {
            $evento = new Evento($request->evento);
            $composicaoBaseCalculoFactory = new ComposicaoBaseCalculoFactory();
            $composicaoBaseCalculo = $composicaoBaseCalculoFactory->create($evento);
            $composicaoBaseCalculo->setRetencao($request->retencao);
            return new DBJsonResponse($composicaoBaseCalculo->getComposicao());
        } catch (BusinessException $e) {
            return new DBJsonResponse('', $e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse('', 'Erro Interno', 500);
        }
    }
}
