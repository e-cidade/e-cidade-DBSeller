<?php

namespace ECidade\Educacao\Secretaria\BNCC\Model;

use ECidade\Enum\Educacao\BNCC\EnsinoEnum;
use Exception;

class HabilidadeReferencialCurricularEstadual
{
    /**
     * @var integer
     */
    private $codigo;
    /**
     * @var EnsinoEnum
     */
    private $ensino;
    /**
     * @var string
     */
    private $etapa;
    /**
     * @var string
     */
    private $codigoHabilidade;
    /**
     * @var string
     */
    private $codigoReferencial;
    /**
     * @var string
     */
    private $habilidade;
    /**
     * @var integer
     */
    private $ano;

    private $objetoConhecimento;

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return EnsinoEnum
     */
    public function getEnsino()
    {
        return $this->ensino;
    }

    /**
     * @param EnsinoEnum $ensino
     */
    public function setEnsino($ensino)
    {
        $this->ensino = $ensino;
    }

    /**
     * @return string
     */
    public function getEtapa()
    {
        return $this->etapa;
    }

    /**
     * @param string $etapa
     */
    public function setEtapa($etapa)
    {
        $this->etapa = $etapa;
    }

    /**
     * @return string
     */
    public function getCodigoHabilidade()
    {
        return $this->codigoHabilidade;
    }

    /**
     * @param string $codigoHabilidade
     */
    public function setCodigoHabilidade($codigoHabilidade)
    {
        $this->codigoHabilidade = $codigoHabilidade;
    }

    /**
     * @return string
     */
    public function getCodigoReferencial()
    {
        return $this->codigoReferencial;
    }

    /**
     * @param string $codigoReferencial
     */
    public function setCodigoReferencial($codigoReferencial)
    {
        $this->codigoReferencial = $codigoReferencial;
    }

    /**
     * @return string
     */
    public function getHabilidade()
    {
        return $this->habilidade;
    }

    /**
     * @param string $habilidade
     */
    public function setHabilidade($habilidade)
    {
        $this->habilidade = $habilidade;
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
    }


    public function getObjetoConhecimento()
    {
        return $this->objetoConhecimento;
    }


    public function setObjetoConhecimento($objConhecimento)
    {
        $this->objetoConhecimento = $objConhecimento;
    }

    /**
     * @param int $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @param array $state
     * @return HabilidadeReferencialCurricularEstadual
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $self = new self();
        if (array_key_exists('ed168_codigo', $state)) {
            $self->setCodigo($state['ed168_codigo']);
        }
        if (array_key_exists('ed168_ensino', $state)) {
            $self->setEnsino(new EnsinoEnum($state['ed168_ensino']));
        }
        if (array_key_exists('ed168_etapa', $state)) {
            $self->setEtapa($state['ed168_etapa']);
        }
        if (array_key_exists('ed168_codigohabilidade', $state)) {
            $self->setCodigoHabilidade($state['ed168_codigohabilidade']);
        }
        if (array_key_exists('ed168_codigoreferencial', $state)) {
            $self->setCodigoReferencial($state['ed168_codigoreferencial']);
        }
        if (array_key_exists('ed168_habilidade', $state)) {
            $self->setHabilidade($state['ed168_habilidade']);
        }
        if (array_key_exists('ed168_objeto_conhecimento', $state)) {
            $self->setObjetoConhecimento($state['ed168_objeto_conhecimento']);
        }
        if (array_key_exists('ed168_ano', $state)) {
            $self->setAno((int) $state['ed168_ano']);
        }

        return $self;
    }
}
