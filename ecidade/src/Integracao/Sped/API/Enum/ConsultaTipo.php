<?php

namespace ECidade\Integracao\Sped\API\Enum;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

final class ConsultaTipo
{
    const ES_RETORNO_CONTRIBUICOES_SOCIAIS_TRABALHADOR = 'S5001';
    const ES_RETORNO_IMPOSTO_RENDA_FONTE = 'S5002';
    const ES_RETORNO_FGTS_TRABALHADOR = 'S5003';
    const ES_RETORNO_CONTRIBUICOES_SOCIAIS_CONTRIBUINTE = 'S5011';
    const ES_RETORNO_IRRF_CONTRIBUINTE = 'S5012';
    const ES_RETORNO_FGTS_CONSOLIDADAS = 'S5013';

    const EFD_RETORNO_CONTRI_PREV = 'R9001';
    const EFD_RETORNO_CONTRI_PREV_CONSOLIDADO = 'R9011';

    const EFD_RETORNO_FONTE  = 'R9005';
    const EFD_RETORNO_FONTE_CONSOLIDADO = 'R9015';

    public static function tipos($tipo = null, $integracao = null)
    {
        $tiposESocial = array(
            self::ES_RETORNO_CONTRIBUICOES_SOCIAIS_TRABALHADOR => 'S-5001 - Informações das contribuições
            sociais por trabalhador',

            self::ES_RETORNO_IMPOSTO_RENDA_FONTE => 'S-5002 - Imposto de Renda Retido na Fonte',
            self::ES_RETORNO_FGTS_TRABALHADOR => 'S-5003 - Informações do FGTS por Trabalhador',

            self::ES_RETORNO_CONTRIBUICOES_SOCIAIS_CONTRIBUINTE => 'S-5011 - Informações das contribuições
            sociais consolidadas por contribuinte',

            self::ES_RETORNO_IRRF_CONTRIBUINTE => 'S-5012 - Informações do IRRF consolidadas por contribuinte',
            self::ES_RETORNO_FGTS_CONSOLIDADAS => 'S-5013 - Informações do FGTS consolidadas por contribuinte'
        );

        $tiposEFD = array(
            self::EFD_RETORNO_CONTRI_PREV => 'R-9001 - Bases e tributos - contribuição previdenciária',
            self::EFD_RETORNO_CONTRI_PREV_CONSOLIDADO => 'R-9011 - Consolidação de bases
            e tributos - Contrib. previdenciária',

            self::EFD_RETORNO_FONTE => 'R-9005 - Bases e tributos - retenções na fonte',
            self::EFD_RETORNO_FONTE_CONSOLIDADO => 'R-9015 - Consolidação das retenções na fonte'
        );

        $tipos = array();

        if (!empty($integracao)) {
            if ($integracao == Tipo::EFD_REINF) {
                $tipos = $tiposEFD;
            } elseif ($integracao == Tipo::ESOCIAL) {
                $tipos = $tiposESocial;
            }
        }

        if (count($tipos) == 0) {
            $tipos = array_merge($tiposESocial, $tiposEFD);
        }

        if (!empty($tipo) && !empty($tipos[$tipo])) {
            $tipos = $tipos[$tipo];
        }

        return $tipos;
    }

    public static function getDeParaEventosRetorno($strRetorno)
    {
        switch ($strRetorno) {
            case 'S-5001':
            case 'S-5003':
                return 'S-1200, S-2299, S-2399';
            case 'S-5002':
                return 'S-1210';
            case 'S-5011':
            case 'S-5012':
            case 'S-5013':
                return 'S-1295, S-1299';
            case 'R-9011':
                return 'R-2099';
            case 'R-9001':
                return 'R-2010, R-2020, R-2030, R-2040, R-2050, R-2060, R-3010';
            case 'R-9005':
                return 'R-4010, R-4020, R-4040';
            case 'R-9015':
                return 'R-4099';
            default:
                return false;
        }
    }
}
