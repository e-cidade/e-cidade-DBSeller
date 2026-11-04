<?php

namespace App\Domain\Tributario\ISSQN\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ISSQN\Services\AlvaraService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlvaraController extends Controller
{
    private $alvaraService;

    public function __construct(AlvaraService $alvaraService)
    {
        $this->alvaraService = $alvaraService;
    }

    public function getPortes(Request $request)
    {
        $portes = null;

        $fisica = $request->get('fisica', false);

        try {
            if ($fisica) {
                $portes = $this->alvaraService->getPortes($fisica);
            } else {
                $portes = $this->alvaraService->getPortes();
            }

            return new DBJsonResponse($portes);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar portes!", 404);
        }
    }
}
