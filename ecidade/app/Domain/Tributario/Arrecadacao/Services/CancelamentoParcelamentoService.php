<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\CancelamentoParcelamentoRepository;
use Exception;

class CancelamentoParcelamentoService
{

    private $CancelamentoRepository;

    public function __construct()
    {
        $this->CancelamentoRepository = new CancelamentoParcelamentoRepository;
    }
    /**
     * @throws Exception
     */
    public function processar(
        $pacel,
        $motivo,
        $processo,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario
    ) {
        return $this->CancelamentoRepository->cancela(
            $pacel,
            $motivo,
            $processo,
            $DB_anousu,
            $DB_instit,
            $DB_datausu,
            $DB_id_usuario
        );
    }
}
