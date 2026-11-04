<?php

namespace ECidade\Tributario\Arrecadacao\Model;

use ECidade\Tributario\Library\Model;

final class CadTipo extends Model
{
    /** @var integer */
    private $sequencial;

    /** @var string */
    private $descricao;

    /** @var bool */
    private $parelaSomanteAnoAtual;

    /** @var bool */
    private $isParcelamento;

    /** @var bool */
    private $permiteParcelar;

    /**
     *  @param integer $sequencial
     * @return void
     * */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return integer
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     *  @param string $descricao
     * @return void
     * */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     *  @param bool $parelaSomanteAnoAtual
     * @return void
     * */
    public function setParelaSomanteAnoAtual($parelaSomanteAnoAtual)
    {
        $this->parelaSomanteAnoAtual = $parelaSomanteAnoAtual;
    }

    /**
     * @return bool
     */
    public function getParelaSomanteAnoAtual()
    {
        return $this->parelaSomanteAnoAtual;
    }

    /**
     *  @param bool $isParcelamento
     * @return void
     * */
    public function setIsParcelamento($isParcelamento)
    {
        $this->isParcelamento = $isParcelamento;
    }

    /**
     * @return bool
     */
    public function getIsParcelamento()
    {
        return $this->isParcelamento;
    }

    /**
     *  @param bool $permiteParcelar
     * @return void
     * */
    public function setPermiteParcelar($permiteParcelar)
    {
        $this->permiteParcelar = $permiteParcelar;
    }

    /**
     * @return bool
     */
    public function getPermiteParcelar()
    {
        return $this->permiteParcelar;
    }

    /**
     * @param array $state
     * @return CadTipo
     * @throws \Exception
     */
    public static function fromState($state)
    {
        $self = new self();
        if (array_key_exists('k03_tipo', $state)) {
            $self->setSequencial($state['k03_tipo']);
        }

        if (array_key_exists('k03_descr', $state)) {
            $self->setDescricao($state['k03_descr']);
        }

        if (array_key_exists('k03_parcano', $state)) {
            $self->setParelaSomanteAnoAtual($state['k03_parcano']);
        }

        if (array_key_exists('k03_parcelamento', $state)) {
            $self->setIsParcelamento($state['k03_parcelamento']);
        }

        if (array_key_exists('k03_permparc', $state)) {
            $self->setPermiteParcelar($state['k03_permparc']);
        }

        return $self;
    }
}
