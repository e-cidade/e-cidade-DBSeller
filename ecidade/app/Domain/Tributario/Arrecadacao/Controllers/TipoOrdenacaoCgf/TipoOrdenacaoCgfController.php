<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers\TipoOrdenacaoCgf;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Services\TipoOrdenacaoCgfService;
use App\Http\Controllers\Controller;

class TipoOrdenacaoCgfController extends Controller
{
    /** @var TipoOrdenacaoCgfService */
    public $tipoOrdenacaoCgfService;

    public function __construct(TipoOrdenacaoCgfService $tipoOrdenacaoCgfService)
    {
        $this->tipoOrdenacaoCgfService = $tipoOrdenacaoCgfService;
    }

    public function getTiposOrdenacao()
    {
        return new DBJsonResponse(
            $this->tipoOrdenacaoCgfService->getTiposOrdenacao()
        );
    }

    public function getRotulos()
    {
        return new DBJsonResponse(
            $this->tipoOrdenacaoCgfService->getRotulos()
        );
    }
}
