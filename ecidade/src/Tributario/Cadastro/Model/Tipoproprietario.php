<?php

namespace Ecidade\Tributario\Cadastro\Model;

use phpDocumentor\Reflection\Types\Integer;

class Tipoproprietario
{
    private $j163_tipoproprietario;
    private $j163_descricao;
    private $j163_abreviatura;
    private $j163_pesfisjur;

    /**
     * @return integer
     */
    public function getTipoproprietario()
    {
        return $this->j163_tipoproprietario;
    }

    /**
     * @param integer
     * @return void
     */
    public function setTipoproprietario($j163_tipoproprietario)
    {
        $this->j163_tipoproprietario = $j163_tipoproprietario;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->j163_descricao;
    }

    /**
     * @param string
     * @return void
     */
    public function setDescricao($j163_descricao)
    {
        $this->j163_descricao = $j163_descricao;
    }

    /**
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->j163_abreviatura;
    }

     /**
     * @return string
     */
    public function setAbreviatura($j163_abreviatura)
    {
        $this->j163_abreviatura = $j163_abreviatura;
    }

    /**
     * @return integer
     */
    public function getPesfisjur()
    {
        return $this->j163_pesfisjur;
    }

    /**
     * @param integer
     * @return void
     */
    public function setPesfisjur($j163_pesfisjur)
    {
        $this->j163_pesfisjur = $j163_pesfisjur;
    }
}
