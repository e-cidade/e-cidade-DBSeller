<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Contracts\AnexosFactoryInterface;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoDoze\AnexoDozeService;
use Exception;

class AnexoDozeFactory extends AnexosFactory implements AnexosFactoryInterface
{

    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = 'financeiro/contabilidade/relatorio/rreo/anexo-12';
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    public static function getCodigoRelatorio($exercicio)
    {
        return 273;
    }

    public static function getProgramaRelatorio($exercicio)
    {
        return 'pla2_anexos_rreo_consolida001.php';
    }

    /**
     * @param $exercicio
     * @param array $filtros
     * @return AnexoDozeService
     * @throws Exception
     */
    public static function getService($exercicio, array $filtros)
    {
        switch ($exercicio) {
            case 2023:
            default:
                return new AnexoDozeService($filtros);
        }
    }
}
