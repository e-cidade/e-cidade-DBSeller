<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Secretaria\Repositories\TiposIntrumentosAvaliativosRepository;
use App\Domain\Educacao\Secretaria\Resources\TiposInstrumentosAvaliativosResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TiposInstrumentosAvaliativosController extends Controller
{
    private $repository;

    public function __construct(TiposIntrumentosAvaliativosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return new DBJsonResponse($this->repository->index()->map(function ($registro) {
            return TiposInstrumentosAvaliativosResource::toResponse($registro);
        }));
    }

    public function getByEnsino($ensino)
    {
        return new DBJsonResponse($this->repository->getByEnsino($ensino)->map(function ($registro) {
            return TiposInstrumentosAvaliativosResource::toResponse($registro);
        }));
    }

    public function salvar(Request $request)
    {
        $retorno = TiposInstrumentosAvaliativosResource::toResponse($this->repository->salvar($request->all()));
        return new DBJsonResponse($retorno);
    }

    public function excluir($codigo)
    {
        return new DBJsonResponse($this->repository->excluir($codigo));
    }
}
