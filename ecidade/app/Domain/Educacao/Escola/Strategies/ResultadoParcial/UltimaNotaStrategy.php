<?php

namespace App\Domain\Educacao\Escola\Strategies\ResultadoParcial;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;

class UltimaNotaStrategy implements ResultadoParcialStrategy
{
    /**
     * @param AproveitamentoAvaliacaoParcial[] $aproveitamentosAvaliacaoParcial
     * @return float|null
     */
    public function calcular(array $aproveitamentosAvaliacaoParcial)
    {
        usort($aproveitamentosAvaliacaoParcial, function ($a, $b) {
            return $a->procedimentoAvaliacaoParcial->ed340_ordem > $b->procedimentoAvaliacaoParcial->ed340_ordem;
        });

        $ultimaNotaLancada = null;
        foreach ($aproveitamentosAvaliacaoParcial as $aproveitamentoAvaliacaoParcial) {
            if (!is_null($aproveitamentoAvaliacaoParcial->ed341_valornota)) {
                $ultimaNotaLancada = $aproveitamentoAvaliacaoParcial->ed341_valornota;
            }
        }
        return $ultimaNotaLancada;
    }

    /**
     * @param ResultadoParcialInterface $resultadoParcial
     * @return float|null
     */
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial)
    {
        if (empty($resultadoParcial->ed343_recuperacaonota)) {
            return $resultadoParcial->ed343_valornota;
        }
        return $resultadoParcial->ed343_recuperacaonota;
    }
}
