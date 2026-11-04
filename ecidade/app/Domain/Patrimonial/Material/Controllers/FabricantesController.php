<?php

namespace App\Domain\Patrimonial\Material\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Material\Models\Fabricante;
use App\Domain\Patrimonial\Material\Resources\FabricantesResource;
use App\Http\Controllers\Controller;

class FabricantesController extends Controller
{
    public function get($id)
    {
        $fabricante = Fabricante::find($id);

        return new DBJsonResponse(FabricantesResource::toResponse($fabricante));
    }
}
