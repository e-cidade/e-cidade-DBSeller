<?php

namespace App\Domain\Educacao\Escola\Strategies\ResultadoParcial;

use App\Domain\Educacao\Escola\Contracts\AvaliacoesParciais\ResultadoParcialInterface;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;

interface ResultadoParcialStrategy
{
    /**
     * @param AproveitamentoAvaliacaoParcial[] $aproveitamentosAvaliacaoParcial
     * @return float|string|null
     */
    public function calcular(array $aproveitamentosAvaliacaoParcial);
    public function calcularResultadoPeriodo(ResultadoParcialInterface $resultadoParcial);
}
