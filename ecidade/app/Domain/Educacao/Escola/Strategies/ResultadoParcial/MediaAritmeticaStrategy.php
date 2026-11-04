<?php

namespace App\Domain\Educacao\Escola\Strategies\ResultadoParcial;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;

class MediaAritmeticaStrategy implements ResultadoParcialStrategy
{
    /**
     * @param AproveitamentoAvaliacaoParcial[] $aproveitamentosAvaliacaoParcial
     * @return float|string|null
     */
    public function calcular(array $aproveitamentosAvaliacaoParcial)
    {
        $soma = 0;
        $quantidade = 0;

        foreach ($aproveitamentosAvaliacaoParcial as $aproveitamento) {
            if (!is_null($aproveitamento->ed341_valornota)) {
                $soma += $aproveitamento->ed341_valornota;
                $quantidade++;
            }
        }

        if ($soma == 0 && $quantidade == 0) {
            return null;
        }

        return $soma / $quantidade;
    }

    /**
     * @param ResultadoParcialInterface $resultadoParcial
     * @return float|null
     */
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial)
    {
        if (empty($resultadoParcial->ed343_valornota)) {
            return $resultadoParcial->ed343_recuperacaonota;
        }
        if (empty($resultadoParcial->ed343_recuperacaonota)) {
            return $resultadoParcial->ed343_valornota;
        }
        return ($resultadoParcial->ed343_valornota + $resultadoParcial->ed343_recuperacaonota) / 2;
    }
}
