<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Parametros;
use App\Http\Controllers\Controller;

class ParametrosController extends Controller
{
    public function index($escola)
    {
        return new DBJsonResponse(Parametros::where('ed233_i_escola', $escola)->get());
    }
}
