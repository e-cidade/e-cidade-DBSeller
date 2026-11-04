<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Contracts\AnexosFactoryInterface;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoDois\AnexoDois2023Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoDois\AnexoDoisService;
use Exception;

class AnexoDoisRgfFactory extends AnexosFactory implements AnexosFactoryInterface
{
    /**
     * @param $exercicio
     * @return array
     */
    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = self::getRota($exercicio);
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    /**
     * Código do relatório para o exercício
     * @param $exercicio
     * @return int
     */
    public static function getCodigoRelatorio($exercicio)
    {
        switch ($exercicio) {
            case 2022:
            default:
                return 265;
        }
    }

    /**
     * Retora a view para renderizar a tela
     * @param $exercicio
     * @return string
     */
    public static function getProgramaRelatorio($exercicio)
    {
        return 'pla2_anexos_rreo001.php';
    }

    /**
     * Service para processamento e impressão do relatório
     * @param $exercicio
     * @param $filtros
     * @return AnexoDoisService
     * @throws Exception
     */
    public static function getService($exercicio, $filtros)
    {
        switch ($exercicio) {
            case 2022:
                return new AnexoDoisService($filtros);
            case 2023:
            default:
                return new AnexoDois2023Service($filtros);
        }
    }

    /**
     * Rota usada para emissão
     * @param $exercicio
     * @return string
     */
    private static function getRota($exercicio)
    {
        return 'financeiro/contabilidade/relatorio/rgf/anexo-2';
    }
}
