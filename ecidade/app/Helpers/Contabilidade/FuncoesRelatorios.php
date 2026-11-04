<?php
/**
 * Funções comum para uso nos relatórios do financeiro
 */

use Carbon\Carbon;

if (!function_exists('headerTipoPlano')) {
    /**
     * Retorna um label para usar nos relatórios
     * @param string $tipoPlano
     * @return string
     */
    function headerTipoPlano($tipoPlano)
    {
        $plano = 'e-Cidade';
        if ($tipoPlano === 'uniao') {
            $plano = 'Plano União/Federação';
        }
        if ($tipoPlano === 'estadual') {
            $plano = 'Plano Estadual/Regional';
        }
        return $plano;
    }
}

if (!function_exists('headerNomeInstituicoes')) {
    /**
     * Retorna os nomes das instituições separado por vírgula
     * @param array $instituicoes
     * @return string
     */
    function headerNomeInstituicoes(array $instituicoes)
    {
        return implode(', ', $instituicoes);
    }
}

if (!function_exists('headerPeriodo')) {
    /**
     * Retorna o período como string
     * @param Carbon $dataInicio
     * @param Carbon $dataFim
     * @return string
     */
    function headerPeriodo(Carbon $dataInicio, Carbon $dataFim)
    {
        return sprintf('%s - %s', $dataInicio->format('d/m/Y'), $dataFim->format('d/m/Y'));
    }
}

if (!function_exists('sinalContaBalanceteVerificacao')) {
    /**
     * Retorna a natureza em que a conta se encontra com base na natureza padrão da conta e o valor informado
     * @param integer $classe
     * @param float $valor
     * @return string
     */
    function sinalContaBalanceteVerificacao($classe, $valor)
    {
        // a natureza das contas de classe 1,3,5,7 e D.
        if (in_array($classe, [1, 3, 5, 7])) {
            if ($valor < 0) {
                return 'C';
            }
            return 'D';
        }

        // a natureza das contas de classe 2,4,6,8 e C.
        if (in_array($classe, [2, 4, 6, 8])) {
            if ($valor > 0) {
                return 'D';
            }
            return 'C';
        }
    }
}
