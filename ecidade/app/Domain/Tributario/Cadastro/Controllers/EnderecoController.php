<?php

namespace App\Domain\Tributario\Cadastro\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Cadastro\Services\EnderecoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnderecoController extends Controller
{
    private $enderecoService;

    public function __construct(EnderecoService $enderecoService)
    {
        $this->enderecoService = $enderecoService;
    }

    public function getPaises(Request $request)
    {
        $paises = null;

        try {
            if (!empty($request->get("termo"))) {
                $paises = $this->enderecoService->getPaises($request->get("termo"));
            } else {
                $paises = $this->enderecoService->getPaises();
            }

            return new DBJsonResponse($paises);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar paises!", 404);
        }
    }

    public function getEstados(Request $request)
    {
        $estados = null;

        try {
            if (!empty($request->get("termo"))) {
                $estados = $this->enderecoService->getEstados($request->get("termo"));
            } else {
                $estados = $this->enderecoService->getEstados();
            }

            return new DBJsonResponse($estados);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar estados!", 404);
        }
    }

    public function getCidades(Request $request)
    {
        $cidades = null;

        try {
            if (!empty($request->get("uf"))) {
                $cidades = $this->enderecoService->getCidades($request->get("uf"));
            } else {
                $cidades = $this->enderecoService->getCidades();
            }

            return new DBJsonResponse($cidades);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar cidades!", 404);
        }
    }

    public function getEnderecoLocalidadeCep(Request $request)
    {
        $localidade = null;

        try {
            $localidade = $this->enderecoService->getEnderecoLocalidadeCep($request->get("cep"));
            return new DBJsonResponse($localidade);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return new DBJsonResponse([], "Erro ao buscar localidade!", 404);
        }
    }
}
