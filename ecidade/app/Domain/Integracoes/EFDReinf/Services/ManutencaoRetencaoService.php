<?php

namespace App\Domain\Integracoes\EFDReinf\Services;

use App\Domain\Integracoes\EFDReinf\Retencao\Factories\RetencaoFactory;
use Illuminate\Support\Facades\DB;

class ManutencaoRetencaoService
{
    public function getRetencoes($filters)
    {
        $retencao = RetencaoFactory::getInstance($filters->evento);
        return $retencao->getRetencoes($filters);
    }

    public function saveRetencao($dados)
    {
        $retencao = RetencaoFactory::getInstance($dados->evento);
        return $retencao->saveRetencao($dados);
    }

    /**
     * Consulta os tipos de serviços de nota fiscal
     */
    public static function getTipoServicoNota()
    {
        $query  = DB::table('empenho.tiposerviconotafiscal');
        $result = $query->get();

        return $result;
    }
}
