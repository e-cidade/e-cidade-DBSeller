<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm\AnexoUmInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm\AnexoUmMdf2024Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm\AnexoUmMdfService;
use Exception;

class AnexoUmRgfFactory extends AnexosFactory
{
    const OPCAO_MODELO = 'modelo_anexo_1_rgf';

    /**
     * @param $exercicio
     * @return array
     * @throws Exception
     */
    public static function getDadosView($exercicio)
    {
        $programa = self::getProgramaRelatorio($exercicio);
        $relatorio = self::getCodigoRelatorio($exercicio);
        $rota = self::getRota($exercicio);
        return ['relatorio' => $relatorio, 'programa' => $programa, 'rota' => $rota];
    }

    /**
     * @param $exercicio
     * @return int
     * @throws Exception
     */
    public static function getCodigoRelatorio($exercicio)
    {
        $opcao = static::getOpcao($exercicio, static::OPCAO_MODELO);

        switch ($opcao->getValor()) {
            case 'in13':
                return static::getCodigoRelatorioInRS($exercicio);
            case 'mdf':
                return static::getCodigoRelatorioMDF($exercicio);
            default:
                throw new Exception("Não foi implementado o modelo para configuração atual.");
        }
    }

    /**
     * @param $exercicio
     * @return int
     */
    private static function getCodigoRelatorioMDF($exercicio)
    {
        switch ($exercicio) {
            case 2021:
            case 2022:
                return 260;
            case 2023:
                return 274;
            case 2024:
            default:
                return 276;
        }
    }

    /**
     * @param $exercicio
     * @return int
     */
    private static function getCodigoRelatorioInRS($exercicio)
    {
        switch ($exercicio) {
            case 2021:
            default:
                return 261;
        }
    }

    private static function getRota($exercicio)
    {
        $opcao = static::getOpcao($exercicio, static::OPCAO_MODELO);

        switch ($opcao->getValor()) {
            case 'in13':
                return 'financeiro/contabilidade/relatorio/rgf/anexo-1-in-rs';
            case 'mdf':
                return 'financeiro/contabilidade/relatorio/rgf/anexo-1-mdf';
            default:
                throw new Exception("Não foi implementado o modelo para configuração atual.");
        }
    }

    /**
     * @param $exercicio
     * @param $filtros
     * @return AnexoUmMdfService
     * @throws Exception
     */
    public static function getServiceMdf($exercicio, $filtros)
    {
        switch ($exercicio) {
            case 2021:
            case 2022:
            case 2023:
                return new AnexoUmMdfService($filtros);
            case 2024:
            default:
                return new AnexoUmMdf2024Service($filtros);
        }
    }

    /**
     * @param $exercicio
     * @param $filtros
     * @return AnexoUmInRsService
     * @throws Exception
     */
    public static function getServiceIn($exercicio, $filtros)
    {
        switch ($exercicio) {
            case 2021:
            default:
                return new AnexoUmInRsService($filtros);
        }
    }

    /**
     * @param $exercicio
     * @param $filtros
     * @return AnexoUmInRsService|AnexoUmMdfService
     * @throws Exception
     */
    public static function getService($exercicio, $filtros)
    {
        $modelo = static::getModelo($exercicio);

        switch ($modelo) {
            case 'in13':
                return static::getServiceIn($exercicio, $filtros);
            case 'mdf':
                return static::getServiceMdf($exercicio, $filtros);
            default:
                throw new Exception("Não foi implementado o modelo para configuração atual.");
        }
    }
}
