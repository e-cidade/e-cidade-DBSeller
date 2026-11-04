<?php

require_once(modification("interfaces/ILancamentoAuxiliar.interface.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));

class LancamentoAuxiliarApopriacaoDecimoFerias extends LancamentoAuxiliarBase implements ILancamentoAuxiliar
{

    /**
     * Retorna a observação do histórico da operação
     */
    public function getObservacaoHistorico()
    {
        return $this->sObservacaoHistorico;
    }

    /**
     * Seta a observação do histórico da operação
     * @param string $sObservacaoHistorico
     */
    public function setObservacaoHistorico($sObservacaoHistorico)
    {
        $this->sObservacaoHistorico = $sObservacaoHistorico;
    }

    /**
     * Seta a conta credito
     * @param integer $iContaCredito
     */
    public function setContaCredito($iContaCredito)
    {
        $this->iContaCredito = $iContaCredito;
    }

    /**
     * Retorna a conta credito
     * @return integer
     */
    public function getContaCredito()
    {
        return $this->iContaCredito;
    }

    /**
     * Seta a conta debito
     * @param integer $iContaDebito
     */
    public function setContaDebito($iContaDebito)
    {
        $this->iContaDebito = $iContaDebito;
    }

    /**
     * Retorna a conta debito
     * @return integer
     */
    public function getContaDebito()
    {
        return $this->iContaDebito;
    }

    public function executaLancamentoAuxiliar($iCodigoLancamento, $dtLancamento)
    {
        $this->iCodigoLancamento = $iCodigoLancamento;
        $this->dtLancamento = $dtLancamento;

        parent::salvarVinculoComplemento();

    }

    public function setValorTotal($nValorTotal)
    {
        $this->nValorTotal = $nValorTotal;
    }

    public function getValorTotal()
    {
        return $this->nValorTotal;
    }

    public function getHistorico()
    {
        return$this->iHistorico;
    }

    public function setHistorico($iHistorico)
    {
        $this->iHistorico = $iHistorico;
    }

    public function setRecurso($codigoRecurso)
    {
        $this->codigoRecurso = $codigoRecurso;
    }

    public function getRecurso()
    {
        return $this->codigoRecurso;
    }
}
