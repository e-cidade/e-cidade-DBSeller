<?php

namespace ECidade\Tributario\Arrecadacao\Model;

class Subtaxas
{
    /**
     * @var integer
     */
    private $sequencial;

    /**
     * @var integer
     */
    private $sequencialTaxa;

    /**
     * @var integer
     */
    private $sequencialSubtaxa;

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
    public function getSequencialTaxa()
    {
        return $this->sequencialTaxa;
    }

    /**
     * @param string $sequencialTaxa
     *
     * @return self
     */
    public function setSequencialTaxa($sequencialTaxa)
    {
        $this->sequencialTaxa = $sequencialTaxa;

        return $this;
    }
   /**
     * @return string
     */
    public function getSequencialSubtaxa()
    {
        return $this->sequencialSubtaxa;
    }

    /**
     * @param string $sequencialSubtaxa
     *
     * @return self
     */
    public function setSequencialSubtaxa($sequencialSubtaxa)
    {
        $this->sequencialSubtaxa = $sequencialSubtaxa;

        return $this;
    }
}
