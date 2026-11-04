<?php

namespace App\Domain\Tributario\Arrecadacao\Controllers\CadTipo;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Services\CadTipoService;
use App\Http\Controllers\Controller;

class CadTipoController extends Controller
{
    /** @var CadTipoService */
    public $cadTipoService;

    public function __construct(CadTipoService $cadTipoService)
    {
        $this->cadTipoService = $cadTipoService;
    }

    public function getGruposDebito()
    {
        return new DBJsonResponse(
            $this->cadTipoService->getGruposDebito()
        );
    }

    public function getRotulos()
    {
        return new DBJsonResponse(
            $this->cadTipoService->getRotulos()
        );
    }
}
