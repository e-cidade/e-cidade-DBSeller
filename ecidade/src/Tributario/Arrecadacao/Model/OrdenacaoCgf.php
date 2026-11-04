<?php

namespace ECidade\Tributario\Arrecadacao\Model;

use ECidade\Tributario\Library\Model;

final class OrdenacaoCgf extends Model
{
    /** @var integer */
    private $sequencial;

    /** @var integer */
    private $grupo;

    /** @var integer */
    private $tipo;

    /**
     * @param integer $sequencial
     * @return void
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @param integer $grupo
     * @return void
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;
    }

    /**
     * @param TipoOrdenacaoCgf $tipo
     * @return void
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return integer
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @return integer
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * @return integer
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param array $state
     * @return OrdenacaoCgf
     * @throws \Exception
     */
    public static function fromState($state)
    {
        $self = new self();
        if (array_key_exists('k241_sequencial', $state)) {
            $self->setSequencial($state['k241_sequencial']);
        }

        if (array_key_exists('k241_grupo', $state)) {
            $self->setGrupo($state['k241_grupo']);
        }

        if (array_key_exists('k241_tipo', $state)) {
            $self->setTipo($state['k241_tipo']);
        }

        return $self;
    }
}
