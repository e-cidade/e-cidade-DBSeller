<?php

namespace App\Domain\Financeiro\Contabilidade\Factories;

use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\AnexosService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RGF\AnexoUm\Versao279Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoQuatro\Versao281Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoTres\Versao278Service;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\RREO\AnexoUm\Versao280Service;
use Exception;

class VersoesRelatoriosLegaisMscFactory
{
    /**
     * Retorna as versões implementadas para o relatório
     * @param string $tipo se é rreo/rgf
     * @param integer $anexo código do anexo 1,2,3...
     * @return int[]
     * @throws Exception
     */
    public static function getVersoes($tipo, $anexo)
    {
        switch (strtoupper($tipo)) {
            case 'RREO':
                return self::getVersoesRREO($anexo);
            case 'RGF':
                return self::getVersoesRGF($anexo);
            default:
                throw new Exception(sprintf(
                    'Não foi possível buscar as versões do relatório com do tipo %s do anexo %s.',
                    $tipo,
                    $anexo
                ), 403);
        }
    }

    /**
     * Retorna os códigos das versões do anexo informado.
     * @param integer $anexo código do anexo 1,2,3...
     * @return int[]
     * @throws Exception
     */
    private static function getVersoesRREO($anexo)
    {
        switch ($anexo) {
            case 1:
                return [280];
            case 3:
                return [278];
            case 4:
                return [281];
            default:
                throw new Exception(sprintf('Não foi implementado o anexo %s da RREO', $anexo), 403);
        }
    }

    /**
     * Retorna os códigos das versões do anexo informado.
     * @param integer $anexo código do anexo 1,2,3...
     * @return int[]
     * @throws Exception
     */
    private static function getVersoesRGF($anexo)
    {
        switch ($anexo) {
            case 1:
                return [279];
            default:
                throw new Exception(sprintf('Não foi implementado o anexo %s da RGF', $anexo), 403);
        }
    }

    /**
     * Retorna o service
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService essa é a classe abstrata para não documentar todas
     * @throws Exception
     */
    public static function getService($filtros)
    {
        switch (strtoupper($filtros['tipo'])) {
            case 'RREO':
                return self::getServiceRREO($filtros);
            case 'RGF':
                return self::getServiceRGF($filtros);
            default:
                throw new Exception(sprintf(
                    'Não foi possível buscar as versões do relatório com do tipo %s do anexo %s.',
                    $filtros['tipo'],
                    $filtros['anexo']
                ));
        }
    }

    /**
     * Retorna os services da RGF
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService
     * @throws Exception
     */
    private static function getServiceRGF($filtros)
    {
        switch ($filtros['anexo']) {
            case 1:
                return self::getServiceAnexo1RGF($filtros);
            default:
                throw new Exception(sprintf('Não foi implementado o anexo %s da RGF', $filtros['anexo']));
        }
    }

    /**
     * Retorna os services da RREO
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService
     * @throws Exception
     */
    private static function getServiceRREO($filtros)
    {
        switch ($filtros['anexo']) {
            case 1:
                return self::getServiceAnexo1RREO($filtros);
            case 3:
                return self::getServiceAnexo3RREO($filtros);
            case 4:
                return self::getServiceAnexo4RREO($filtros);
            default:
                throw new Exception(sprintf(
                    'Não foi possível buscar o service do relatório com do anexo %s.',
                    $filtros['anexo']
                ));
        }
    }

    /**
     * Retorna os services responsáveis pela emissão do anexo 1 da RREO
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService
     * @throws Exception
     */
    private static function getServiceAnexo1RREO($filtros)
    {
        switch ($filtros['relatorio']) {
            case 280:
            default:
                return new Versao280Service($filtros);
        }
    }

    /**
     * Retorna os services responsáveis pela emissão do anexo 3 da RREO
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService
     * @throws Exception
     */
    private static function getServiceAnexo3RREO($filtros)
    {
        switch ($filtros['relatorio']) {
            case 278:
            default:
                return new Versao278Service($filtros);
        }
    }

    /**
     * Retorna os services responsáveis pela emissão do anexo 1 da RGF
     * @param array $filtros todos filtros usados na impressão mais todos dados da Sessão
     * @return AnexosService
     * @throws Exception
     */
    private static function getServiceAnexo1RGF($filtros)
    {
        switch ($filtros['relatorio']) {
            case 279:
            default:
                return new Versao279Service($filtros);
        }
    }

    private static function getServiceAnexo4RREO($filtros)
    {
        switch ($filtros['relatorio']) {
            case 281:
            default:
                return new Versao281Service($filtros);
        }
    }
}
