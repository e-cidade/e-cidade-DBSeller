<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits;

trait PodeTotalizar
{
    private $totalizador;

    private function count($campo, $valor)
    {
        if (!in_array($campo['totalizar'], ['q', 's'])) {
            return;
        }

        if (!array_key_exists($campo['nome'], $this->totalizador)) {
            $this->totalizador[$campo['nome']] = (object)[
                'campo' => $campo['alias'] ?: $campo['nome'],
                'tipo' => $campo['totalizar'] === 'q' ? 'Quantidade' : 'Soma',
                'valor' => 0
            ];
        }

        $this->totalizador[$campo['nome']]->valor += $campo['totalizar'] === 'q' ? 1 : $valor;
    }
}
