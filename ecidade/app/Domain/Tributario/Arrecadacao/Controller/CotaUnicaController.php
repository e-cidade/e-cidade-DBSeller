<?php

namespace App\Domain\Tributario\Arrecadacao\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Repositories\CotaUnicaRepository;
use App\Http\Controllers\Controller;
use Exception;

class CotaUnicaController extends Controller
{
    public function search($k00_tipo = null)
    {
        try {
            return new DBJsonResponse(
                CotaUnicaRepository::getCotaUnica($k00_tipo),
                'Success'
            );
        } catch (Exception $e) {
            return new DBJsonResponse(
                [],
                $e->getMessage(),
                422
            );
        }
    }
}
