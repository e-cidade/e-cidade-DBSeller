<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\MatriculaOnline\Models\Ciclo;
use App\Domain\Educacao\MatriculaOnline\Resources\CiclosResource;
use App\Domain\Educacao\MatriculaOnline\Services\CiclosService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CiclosController extends Controller
{
    private $service;

    public function __construct(CiclosService $service)
    {
        $this->service = $service;
    }
    public function todos()
    {
        $ciclos = $this->service->buscar()->map(function ($ciclo) {
            return CiclosResource::toResponse($ciclo);
        });
        return new DBJsonResponse($ciclos);
    }

    public function salvar(Request $request)
    {
        return new DBJsonResponse($this->service->salvar(CiclosResource::toArrayModel($request->all())));
    }

    public function excluir($codigo)
    {
        try {
            return new DBJsonResponse($this->service->excluir($codigo));
        } catch (\Exception $exception) {
            if ($exception->getCode() == 23503) {
                throw new \Exception("Ciclo possui fase cadastrada, exclua primeiro!");
            }
        }
    }
}
