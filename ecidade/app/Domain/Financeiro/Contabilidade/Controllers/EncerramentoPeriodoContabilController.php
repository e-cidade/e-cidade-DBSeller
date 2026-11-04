<?php


namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\EncerramentoPeriodoContabilService;
use Illuminate\Support\Facades\DB;

class EncerramentoPeriodoContabilController
{
    public function ultimaData($instituicao)
    {
        $data = EncerramentoPeriodoContabilService::ultimaData($instituicao);
        return new DBJsonResponse($data, "Data Encerramento.");
    }
}
