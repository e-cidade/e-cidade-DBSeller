<?php

namespace Ecidade\Tributario\Cadastro\Model;

use phpDocumentor\Reflection\Types\Integer;

class Tipoproprietariopromitente
{
    private $j165_tipoproprietariopromitente;
    private $j165_tipoproprietario;
    private $j165_tipopromitente;

    /**
     * @return integer
     */
    public function getTipoproprietariopromitente()
    {
        return $this->j165_tipoproprietariopromitente;
    }

    /**
     * @param integer
     * @return void
     */
    public function setTipoproprietariopromitente($j165_tipoproprietariopromitente)
    {
        $this->j165_tipoproprietariopromitente = $j165_tipoproprietariopromitente;
    }

    /**
     * @return integer
     */
    public function getTipoproprietario()
    {
        return $this->j165_tipoproprietario;
    }

    /**
     * @param integer
     * @return void
     */
    public function setTipoproprietario($j165_tipoproprietario)
    {
        $this->j165_tipoproprietario = $j165_tipoproprietario;
    }

    /**
     * @return integer
     */
    public function getTipopromitente()
    {
        return $this->j165_tipopromitente;
    }

    /**
     * @param integer
     * @return void
     */
    public function setTipopromitente($j165_tipopromitente)
    {
        $this->j165_tipopromitente = $j165_tipopromitente;
    }
}
