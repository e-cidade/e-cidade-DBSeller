<?php

namespace App\Domain\Educacao\Secretaria\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Ensino;
use App\Domain\Educacao\Secretaria\Resources\EnsinoResource;
use App\Http\Controllers\Controller;

class EnsinosController extends Controller
{
    public function index()
    {
        $ensinos = Ensino::all()->map(function ($ensino) {
            return EnsinoResource::toResponse($ensino);
        });
        return new DBJsonResponse($ensinos);
    }

    public function getByCodigo($codigo)
    {
        $ensinos = EnsinoResource::toResponse(Ensino::find($codigo));
        return new DBJsonResponse($ensinos);
    }
}
