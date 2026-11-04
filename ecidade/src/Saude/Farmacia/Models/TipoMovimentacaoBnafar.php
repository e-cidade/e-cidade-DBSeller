<?php

namespace ECidade\Saude\Farmacia\Models;

class TipoMovimentacaoBnafar
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @param integer $codigo
     * @return TipoMovimentacaoBnafar
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
     * @param string $descricao
     * @return TipoMovimentacaoBnafar
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
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
     * @param string $tipo
     * @return TipoMovimentacaoBnafar
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param array $state
     * @return TipoMovimentacaoBnafar
     */
    public static function fromState(array $state)
    {
        $self = new self;

        if (array_key_exists('fa68_codigo', $state)) {
            $self->setCodigo($state['fa68_codigo']);
        }
        if (array_key_exists('fa68_descricao', $state)) {
            $self->setDescricao($state['fa68_descricao']);
        }
        if (array_key_exists('fa68_tipo', $state)) {
            $self->setTipo($state['fa68_tipo']);
        }

        return $self;
    }
}
