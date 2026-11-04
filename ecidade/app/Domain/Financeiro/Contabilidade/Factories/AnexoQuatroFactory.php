<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Contracts\AnexosFactoryInterface;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoQuatro\AnexoQuatro2022Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoQuatro\AnexoQuatroService;

class AnexoQuatroFactory extends AnexosFactory implements AnexosFactoryInterface
{
    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = 'financeiro/contabilidade/relatorio/rreo/anexo-4';
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    public static function getCodigoRelatorio($exercicio)
    {
        switch ($exercicio) {
            case 2021:
                return 244;
            case 2022:
            default:
                return 263;
        }
    }

    public static function getProgramaRelatorio($exercicio)
    {
        return 'pla2_anexos_rreo_consolida001.php';
    }


    public static function getService($exercicio, $filtros)
    {
        switch ($exercicio) {
            case 2021:
                return new AnexoQuatroService($filtros);
            case 2022:
            default:
                return new AnexoQuatro2022Service($filtros);
        }
    }
}
