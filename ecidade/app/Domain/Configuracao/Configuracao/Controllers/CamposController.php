<?php

namespace App\Domain\Configuracao\Configuracao\Controllers;

use App\Domain\Configuracao\Configuracao\Model\Campo;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;

class CamposController extends Controller
{
    public function getCampo($campo)
    {
        $label = Campo::where('nomecam', $campo)->get();
        return new DBJsonResponse($label);
    }
}
