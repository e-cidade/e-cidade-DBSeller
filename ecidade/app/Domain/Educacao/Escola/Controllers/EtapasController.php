<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Repositories\EtapaRepository;
use App\Domain\Educacao\Escola\Resources\EtapaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EtapasController extends Controller
{
    public function getByRegimeMatricula($escola, $regime, EtapaRepository $repository)
    {
        $etapas = $repository->getByRegimeMatricula($regime)->map(function ($etapa) {
            return EtapaResource::toResponse($etapa);
        });
        return new DBJsonResponse($etapas);
    }

    public function getByCodigo($escola, $codigo, EtapaRepository $repository)
    {
        $etapa = EtapaResource::toResponse($repository->get($codigo));
        return new DBJsonResponse($etapa);
    }

    public function getEtapasNoIntervalo(Request $request, EtapaRepository $repository)
    {
        $etapaI = $request->get('inicial');
        $etapaF = $request->get('final');

        $etapas = $repository->getEtapasNoIntervalo($etapaI, $etapaF)->map(function ($etapa) {
            return EtapaResource::toResponse($etapa);
        });
        return new DBJsonResponse($etapas);
    }

    public function getEtapasFaseAberta(EtapaRepository $repository)
    {
        $etapas = $repository->getEtapasFaseAberta()->map(function ($etapa) {
            return EtapaResource::toResponse($etapa);
        });

        return new DBJsonResponse($etapas);
    }
}
