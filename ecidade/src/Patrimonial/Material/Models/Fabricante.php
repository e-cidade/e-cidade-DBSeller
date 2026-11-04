<?php

namespace ECidade\Patrimonial\Material\Models;

use CgmBase;
use CgmFactory;

class Fabricante
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var string
     */
    private $nome;

    /**
     * @var CgmBase
     */
    private $cgm;

        /**
     * @param integer $codigo
     * @return Fabricante
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $nome
     * @return Fabricante
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param CgmBase $cgm
     * @return Fabricante
     */
    public function setCgm(CgmBase $cgm)
    {
        $this->cgm = $cgm;
        return $this;
    }

    /**
     * @return CgmBase
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param array $state
     * @return Fabricante
     */
    public static function fromState(array $state)
    {
        $self = new self;

        if (array_key_exists('m76_sequencial', $state)) {
            $self->setCodigo($state['m76_sequencial']);
        }
        if (array_key_exists('m76_nome', $state)) {
            $self->setNome($state['m76_nome']);
        }
        if (array_key_exists('m76_numcgm', $state)) {
            $self->setCgm(CgmFactory::getInstanceByCgm($state['m76_numcgm']));
        }

        return $self;
    }
}
