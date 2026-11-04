<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\MatriculaOnline\Repositories\DuvidasFrequentesRepository;
use App\Domain\Educacao\MatriculaOnline\Resources\DuvidasFrequentesResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DuvidasFrequentesController extends Controller
{
    protected $repository;
    public function __construct(DuvidasFrequentesRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index()
    {
        $duvidas = $this->repository->index()->map(function ($duvida) {
            return DuvidasFrequentesResource::toResponse($duvida);
        });

        return new DBJsonResponse($duvidas);
    }
    public function salvar(Request $request)
    {
        return new DBJsonResponse($this->repository->salvar(DuvidasFrequentesResource::toArrayModel($request->all())));
    }

    public function excluir($codigo)
    {
        return new DBJsonResponse($this->repository->excluir($codigo));
    }
}
