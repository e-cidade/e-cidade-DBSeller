<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits;

use App\Domain\Configuracao\GeradorRelatorio\Enums\MascaraEnum;

trait PodeMascarar
{
    /**
     * @param object $dados
     * @param array $campo
     * @return string
     * @throws \Exception
     */
    private function getValorMascarado($dados, array $campo)
    {
        $valor = $dados->{$campo['nome']};
        if ($campo['mascara'] === MascaraEnum::DATA) {
            $valor = (new \DateTime($valor))->format('d/m/Y');
        }
        if ($campo['mascara'] === MascaraEnum::MOEDA) {
            $valor = number_format($valor, 2, ',', '.');
        }

        return $valor;
    }
}
