<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Contracts\AnexosFactoryInterface;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoOito\AnexoOito2023Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoOito\AnexoOitoService;

class AnexoOitoFactory extends AnexosFactory implements AnexosFactoryInterface
{
    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = 'financeiro/contabilidade/relatorio/rreo/anexo-8';
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    public static function getCodigoRelatorio($exercicio)
    {
        switch ($exercicio) {
            case 2021:
            case 2022:
                return 245;
            case 2023:
            default:
                return 272;
        }
    }

    public static function getProgramaRelatorio($exercicio)
    {
        return 'pla2_anexos_rreo_consolida001.php';
    }

    /**
     * @param $exercicio
     * @param array $filtros
     * @return AnexoOitoService
     */
    public static function getService($exercicio, array $filtros)
    {
        switch ($exercicio) {
            case 2021:
                return new AnexoOitoService($filtros);
            case 2023:
            case 2024:
            default:
                return new AnexoOito2023Service($filtros);
        }
    }
}
