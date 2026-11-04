<?php

namespace ECidade\Tributario\Arrecadacao\Model;

class DiversosTaxa
{
    /**
     * @var integer
     */
    private $sequencial;

    /**
     * @var integer
     */
    private $codigoDiversos;

    /**
     * @var integer
     */
    private $codigoTaxa;

    /**
     * @var float
     */
    private $valor;

    /**
     * @var float
     */
    private $isTaxaPrincipal;

    /**
     * @return integer
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param integer $sequencial
     *
     * @return self
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigoDiversos()
    {
        return $this->codigoDiversos;
    }

    /**
     * @param integer $codigoDiversos
     *
     * @return self
     */
    public function setCodigoDiversos($codigoDiversos)
    {
        $this->codigoDiversos = $codigoDiversos;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigoTaxa()
    {
        return $this->codigoTaxa;
    }

    /**
     * @param integer $codigoTaxa
     *
     * @return self
     */
    public function setCodigoTaxa($codigoTaxa)
    {
        $this->codigoTaxa = $codigoTaxa;

        return $this;
    }

    /**
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param float $valor
     *
     * @return self
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * @param boolean $isTaxaPrincipal
     *
     * @return self
     */
    public function setIsPrincipal($isTaxaPrincipal)
    {
        $this->isTaxaPrincipal = $isTaxaPrincipal;

        return $this;
    }

    /**
     * @return boolean
     *
     * @return self
     */
    public function getIsPrincipal()
    {
        return $this->isTaxaPrincipal;
    }
}
