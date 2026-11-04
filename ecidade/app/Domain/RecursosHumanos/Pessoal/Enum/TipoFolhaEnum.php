<?php

namespace App\Domain\RecursosHumanos\Pessoal\Enum;

use ECidade\Enum\Enum;

class TipoFolhaEnum extends Enum
{
    const SALARIO      = 'salario';
    const DECIMO       = 'decimo';
    const FIXO         = 'fixo';
    const SUPLEMENTAR  = 'suplementar';
    const COMPLEMENTAR = 'complementar';
    const ADIANTAMENTO = 'adiantamento';
    const FERIAS       = 'ferias';
    const RESCISAO     = 'rescisao';

    /**
     * Busca o tipo de folha pelo ponto
     * @param string
     * @return enum
     */
    public static function getTipoFolhaByPonto($tipo)
    {
        switch ($tipo) {
            case 'fs':
                return self::SALARIO;
                break;
            case 'fx':
                return self::FIXO;
                break;
            case 'f13':
                return self::DECIMO;
                break;
            case 'com':
                return self::COMPLEMENTAR;
                break;
            case 'fa':
                return self::ADIANTAMENTO;
                break;
            case 'fe':
                return self::FERIAS;
                break;
            case 'fr':
                return self::RESCISAO;
                break;
            default:
                throw new \Exception("Tipo de folha não definido");
                break;
        }
    }

    /**
     * Busca o tipo de folha nome de tabela
     * @param string
     * @return enum
     */
    public static function getTipoFolhaByTabela($tabela)
    {
        switch ($tabela) {
            case 'gerfsal':
                return self::SALARIO;
                break;
            case 'gerffx':
                return self::FIXO;
                break;
            case 'gerfs13':
                return self::DECIMO;
                break;
            case 'gerfcom':
                return self::COMPLEMENTAR;
                break;
            case 'gerfadi':
                return self::ADIANTAMENTO;
                break;
            case 'gerffer':
                return self::FERIAS;
                break;
            case 'gerfres':
                return self::RESCISAO;
                break;
            default:
                throw new \Exception("Tipo de folha não definido");
                break;
        }
    }

    public static function getTipoFolha($tipo)
    {
        $tipos = [];
        for ($t=0; $t < sizeof($tipo); $t++) {
            switch ($tipo[$t]) {
                case 'sa':
                    $tipos[][$tipo[$t]] = ucfirst(self::SALARIO);
                    break;
                case 'co':
                    $tipos[][$tipo[$t]] = ucfirst(self::COMPLEMENTAR);
                    break;
                case 'su':
                    $tipos[][$tipo[$t]] = ucfirst(self::SUPLEMENTAR);
                    break;
                case 're':
                    $tipos[][$tipo[$t]] = ucfirst(self::RESCISAO);
                    break;
                case 'd13':
                    $tipos[][$tipo[$t]] = ucfirst(self::DECIMO);
                    break;
                case 'fe':
                    $tipos[][$tipo[$t]] = ucfirst(self::FERIAS);
                    break;
                default:
                    throw new \Exception("Tipo de folha não definido");
                    break;
            }
        }
        
        return $tipos;
    }
}
