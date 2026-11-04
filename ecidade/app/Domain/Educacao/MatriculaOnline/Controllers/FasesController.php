<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\MatriculaOnline\Resources\FaseResource;
use App\Domain\Educacao\MatriculaOnline\Services\FasesService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FasesController extends Controller
{

    private $service;

    public function __construct(FasesService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $fases = $this->service->buscar()->map(function ($fase) {
            return FaseResource::toResponse($fase);
        });
        return new DBJsonResponse($fases);
    }

    public function salvar(Request $request)
    {
        return new DBJsonResponse($this->service->salvar(FaseResource::toArrayModel($request->all())));
    }

    public function excluir($codigo)
    {
        try {
            return new DBJsonResponse($this->service->excluir($codigo));
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23503) {
                throw new \Exception(
                    "Fase já possui inscritos ou vagas registradas, exclua-los primeiro!"
                );
            }
        }
    }
}
