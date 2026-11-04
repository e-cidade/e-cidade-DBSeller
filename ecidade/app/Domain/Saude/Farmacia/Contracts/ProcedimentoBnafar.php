<?php

namespace App\Domain\Saude\Farmacia\Contracts;

use Generator;

interface ProcedimentoBnafar
{
    /**
     * @param \DateTime[] $periodo
     * @return object
     */
    public function getSituacaoEnvio(array $periodo);

    /**
     * @param integer $codigoMovimentacao
     * @return ProcedimentoBnafar
     */
    public function setCodigoMovimentacao($codigoMovimentacao);

    /**
     * @return string ex: entrada
     */
    public function getProcedimento();

    /**
     * @return string ex: entrada-lote
     */
    public function getProcedimentoLote();

    /**
     * @return integer
     */
    public function getTipo();

    /**
     * @param array $periodo
     * @return array
     */
    public function verificarInconsistencias(array $periodo);

    /**
     * @return object
     */
    public function processar();

    /**
     * @param array $periodo
     * @return Generator
     */
    public function processarLote(array $periodo);
}
