<?php

namespace App\Domain\Tributario\Arrecadacao\Controller\TipoDebito;

use App\Domain\Tributario\Arrecadacao\Repositories\TipoDebitoRepository;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Exception;

class TipoDebitoController extends Controller
{
    /**
     * Retorna uma lista em json como a rotina legada "alterar tipo débito"
     */
    public function listTipoDebito()
    {
        try {
            return new DBJsonResponse(
                TipoDebitoRepository::getTiposDebito(),
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

    /**
     * Retorna um object com o resultado da pesquisa
     */
    public function searchTipoDebito($k00_tipo)
    {
        try {
            return new DBJsonResponse(
                TipoDebitoRepository::getTipoDebito($k00_tipo),
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
