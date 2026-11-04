<?php

namespace App\Domain\Educacao\Escola\Strategies\ResultadoParcial;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;

class SomaStrategy implements ResultadoParcialStrategy
{
    /**
     * @param AproveitamentoAvaliacaoParcial[] $aproveitamentosAvaliacaoParcial
     */
    public function calcular(array $aproveitamentosAvaliacaoParcial)
    {
        $soma = 0;
        $temAvaliacao = false;
        foreach ($aproveitamentosAvaliacaoParcial as $aproveitamento) {
            if (!is_null($aproveitamento->ed341_valornota)) {
                $temAvaliacao = true;
                $soma += $aproveitamento->ed341_valornota;
            }
        }
        if (!$temAvaliacao) {
            $soma = null;
        }
        return $soma;
    }

    /**
     * @param ResultadoParcialInterface $resultadoParcial
     * @return float|null
     */
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial)
    {
        return $resultadoParcial->ed343_valornota + $resultadoParcial->ed343_recuperacaonota;
    }
}
