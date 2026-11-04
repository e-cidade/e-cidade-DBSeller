<?php

namespace ECidade\Tributario\Arrecadacao\Model;

use ECidade\Tributario\Library\Model;

final class TipoOrdenacaoCgf extends Model
{
    /** @var integer */
    private $sequencial;

    /** @var string */
    private $descricao;

    /**
     * @param integer $sequencial
     * @return void
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @param string $descricao
     * @return void
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return integer
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param array $state
     * @return TipoOrdenacaoCgf
     * @throws \Exception
     */
    public static function fromState($state)
    {
        $self = new self();
        if (array_key_exists('k240_sequencial', $state)) {
            $self->setSequencial($state['k240_sequencial']);
        }

        if (array_key_exists('k240_descricao', $state)) {
            $self->setDescricao($state['k240_descricao']);
        }

        return $self;
    }
}
