<?php

namespace ECidade\Tributario\Arrecadacao\Model;

class TaxasLancadas
{
    /**
     * @var integer
     */
    private $sequencial;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var float
     */
    private $valorinflator;

    /**
     * @var string
     */
    private $inflator;

    /**
     * @var integer
     */
    private $diasvencimento;

    /**
     * @var integer
     */
    private $tipo;

    /**
     * @var integer
     */
    private $receitaxaexpediente;

    /**
     * @var float
     */
    private $valortaxaexpediente;

    /**
     * @var string
     */
    private $datavigencia;

    /**
     * @var integer
     */
    private $procedencia;

    /**
     * @var integer
     */
    private $receita;

    /**
     * @var boolean
     */
    private $emissaoweb;

    /**
     * @var integer
     */
    private $valor;

    /**
     * @var boolean
     */
    private $recursoadm;

    /**
     * @var string
     */
    private $origem;

    /**
     * @var boolean
     */
    private $permitesubtaxas;

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
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     *
     * @return self
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return float
     */
    public function getValorinflator()
    {
        return $this->valorinflator;
    }

    /**
     * @param float $valorinflator
     *
     * @return self
     */
    public function setValorinflator($valorinflator)
    {
        $this->valorinflator = $valorinflator;

        return $this;
    }

    /**
     * @return string
     */
    public function getInflator()
    {
        return $this->inflator;
    }

    /**
     * @param string $inflator
     *
     * @return self
     */
    public function setInflator($inflator)
    {
        $this->inflator = $inflator;

        return $this;
    }

    /**
     * @return integer
     */
    public function getDiasvencimento()
    {
        return $this->diasvencimento;
    }

    /**
     * @param integer $diasvencimento
     *
     * @return self
     */
    public function setDiasvencimento($diasvencimento)
    {
        $this->diasvencimento = $diasvencimento;

        return $this;
    }

        /**
     * @return integer
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param integer $tipo
     *
     * @return self
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * @return integer
     */
    public function getReceitaxaexpediente()
    {
        return $this->receitaxaexpediente;
    }

    /**
     * @param integer $receitaxaexpediente
     *
     * @return self
     */
    public function setReceitaxaexpediente($receitaxaexpediente)
    {
        $this->receitaxaexpediente = $receitaxaexpediente;

        return $this;
    }

    /**
     * @return float
     */
    public function getValortaxaexpediente()
    {
        return $this->valortaxaexpediente;
    }

    /**
     * @param float $valortaxaexpediente
     *
     * @return self
     */
    public function setValortaxaexpediente($valortaxaexpediente)
    {
        $this->valortaxaexpediente = $valortaxaexpediente;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatavigencia()
    {
        return $this->datavigencia;
    }

    /**
     * @param string $datavigencia
     *
     * @return self
     */
    public function setDatavigencia($datavigencia)
    {
        $this->datavigencia = $datavigencia;

        return $this;
    }

    /**
     * @return integer
     */
    public function getProcedencia()
    {
        return $this->procedencia;
    }

    /**
     * @param integer $procedencia
     *
     * @return self
     */
    public function setProcedencia($procedencia)
    {
        $this->procedencia = $procedencia;

        return $this;
    }

    /**
     * @return integer
     */
    public function getReceita()
    {
        return $this->receita;
    }

    /**
     * @param integer $receita
     *
     * @return self
     */
    public function setReceita($receita)
    {
        $this->receita = $receita;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmissaoweb()
    {
        return $this->emissaoweb;
    }

    /**
     * @param boolean $emissaoweb
     *
     * @return self
     */
    public function setEmissaoweb($emissaoweb)
    {
        $this->emissaoweb = $emissaoweb;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isGeraDebito()
    {
        return (!empty($this->procedencia) ? true : false);
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
     * @return boolean
     */
    public function isRecursoadm()
    {
        return $this->recursoadm;
    }

    /**
     * @param boolean $recursoadm
     *
     * @return self
     */
    public function setRecursoadm($recursoadm)
    {
        $this->recursoadm = $recursoadm;

        return $this;
    }

    /**
     * Get the value of origem
     *
     * @return  string
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * Set the value of origem
     *
     * @param  string  $origem
     *
     * @return  self
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPermiteSubtaxas()
    {
        return $this->permitesubtaxas;
    }

    /**
     * @param boolean $recursoadm
     *
     * @return self
     */
    public function setPermiteSubtaxas($permitesubtaxas)
    {
        $this->permitesubtaxas = $permitesubtaxas;

        return $this;
    }
}
