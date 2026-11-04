<?php

namespace App\Domain\Educacao\Escola\Strategies\ResultadoParcial;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;

class MaiorNotaStrategy implements ResultadoParcialStrategy
{
    /**
     * @param array $aproveitamentosAvaliacaoParcial
     * @return float|string|null
     */
    public function calcular(array $aproveitamentosAvaliacaoParcial)
    {
        $maiorNota = null;
        foreach ($aproveitamentosAvaliacaoParcial as $aproveitamento) {
            if (is_null($maiorNota)) {
                $maiorNota = $aproveitamento->ed341_valornota;
            }
            if ($aproveitamento->ed341_valornota > $maiorNota) {
                $maiorNota = $aproveitamento->ed341_valornota;
            }
        }
        return $maiorNota;
    }

    /**
     * @param ResultadoParcialInterface $resultadoParcial
     * @return float|null
     */
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial)
    {
        if (empty($resultadoParcial->ed343_valornota) && empty($resultadoParcial->ed343_recuperacaonota)) {
            return null;
        }
        if ($resultadoParcial->ed343_valornota > $resultadoParcial->ed343_recuperacaonota) {
            return $resultadoParcial->ed343_valornota;
        }
        return $resultadoParcial->ed343_recuperacaonota;
    }
}
