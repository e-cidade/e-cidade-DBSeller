<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Contracts\AnexosFactoryInterface;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoUm\AnexoUm2023Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoUm\AnexoUmService;

class AnexoUmFactory extends AnexosFactory implements AnexosFactoryInterface
{

    /**
     * @inheritDoc
     */
    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = 'financeiro/contabilidade/relatorio/rreo/anexo-1';
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    public static function getCodigoRelatorio($exercicio)
    {
        switch ($exercicio) {
            case 2022:
                return 268;
            case 2023:
            default:
                return 269;
        }
    }

    /**
     * @param $exercicio
     * @param $filtros
     * @return AnexoUmService
     */
    public static function getService($exercicio, $filtros)
    {
        switch ($exercicio) {
            case 2022:
                return new AnexoUmService($filtros);
            case 2023:
            case 2024:
            default:
                return new AnexoUm2023Service($filtros);
        }
    }
}
