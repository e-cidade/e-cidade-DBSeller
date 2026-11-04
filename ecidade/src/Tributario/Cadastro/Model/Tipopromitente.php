<?php

namespace ECidade\Tributario\Cadastro\Model;

class Tipopromitente
{
    private $j164_tipopromitente;
    private $j164_descricao;
    private $j164_promitipo;
    private $j164_abreviatura;

    /**
     * @return integer
     */
    public function getTipopromitente()
    {
        return $this->j164_tipopromitente;
    }

    /**
     * @param integer
     * @return void
     */
    public function setTipopromitente($j164_tipopromitente)
    {
        $this->j164_tipopromitente = $j164_tipopromitente;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->j164_descricao;
    }

    /**
     * @param string
     * @return void
     */
    public function setDescricao($j164_descricao)
    {
        $this->j164_descricao = $j164_descricao;
    }
    
    /**
     * @return string
     */
    public function getPromitipo()
    {
        return $this->j164_promitipo;
    }

    /**
     * @param string
     * @return void
     */
    public function setPromitipo($j164_promitipo)
    {
        $this->j164_promitipo = $j164_promitipo;
    }

    /**
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->j164_abreviatura;
    }

    /**
     * @param string
     * @return void
     */
    public function setAbreviatura($j164_abreviatura)
    {
        $this->j164_abreviatura = $j164_abreviatura;
    }
}
