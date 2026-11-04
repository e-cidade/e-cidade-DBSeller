<?php

namespace App\Domain\Saude\Ambulatorial\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Ambulatorial\Models\Unidade;
use App\Domain\Saude\Ambulatorial\Resources\UnidadeResource;
use App\Http\Controllers\Controller;

class UnidadesController extends Controller
{
    public function get($id)
    {
        $unidade = Unidade::find($id);

        return new DBJsonResponse(UnidadeResource::toResponse($unidade));
    }
}
