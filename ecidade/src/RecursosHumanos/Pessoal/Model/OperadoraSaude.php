<?php

namespace ECidade\RecursosHumanos\Pessoal\Model;

use CgmJuridico;
use CgmRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\OperadoraSaudeRepository;
use Exception;

/**
 * Class OperadoraSaude
 * @package ECidade\RecursosHumanos\Pessoal\Model
 */
class OperadoraSaude
{
    /**
     * @var int
     */
    private $sequencial;
    /**
     * @var CgmJuridico
     */
    private $cgm;
    /**
     * @var int
     */
    private $ans;
    /**
     * @var boolean
     */
    private $ativo;
    /**
     * @var int
     */
    private $codigoInstituicao;

    /**
     * OperadoraSaude constructor.
     * @param null $sequencial
     * @throws Exception
     */
    public function __construct($sequencial = null)
    {
        if ($sequencial) {
            $operadoraSaude = OperadoraSaudeRepository::find($sequencial);

            $this->sequencial = $operadoraSaude->getSequencial();
            $this->cgm = $operadoraSaude->getCgm();
            $this->ans = $operadoraSaude->getAns();
            $this->ativo = $operadoraSaude->isAtivo();
            $this->codigoInstituicao = $operadoraSaude->getCodigoInstituicao();
        }
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param int $sequencial
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return CgmJuridico
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param CgmJuridico $cgm
     */
    public function setCgm(CgmJuridico $cgm)
    {
        $this->cgm = $cgm;
    }

    /**
     * @return int
     */
    public function getAns()
    {
        if ($this->ans == 0) {
            $this->ans = '';
        }
        return $this->ans;
    }

    /**
     * @param int $ans
     */
    public function setAns($ans)
    {
        $this->ans = $ans;
    }

    /**
     * @return bool
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param bool $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @param array $state
     * @return OperadoraSaude
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $operadoraSaude = new self();

        if (array_key_exists('rh221_sequencial', $state)) {
            $operadoraSaude->setSequencial((int)$state['rh221_sequencial']);
        }

        if (array_key_exists('rh221_cgm', $state)) {
            $operadoraSaude->setCgm(CgmRepository::getByCodigo($state['rh221_cgm']));
        }

        if (array_key_exists('rh221_ans', $state)) {
            $operadoraSaude->setAns((int)$state['rh221_ans']);
        }

        if (array_key_exists('rh221_ativo', $state)) {
            $operadoraSaude->setAtivo($state['rh221_ativo'] === 't');
        }

        if (array_key_exists('rh221_instituicao', $state)) {
            $operadoraSaude->setCodigoInstituicao((int)$state['rh221_instituicao']);
        }

        return $operadoraSaude;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'sequencial' => $this->getSequencial(),
            'cgm' => $this->getCgm()->toArray(),
            'ans' => $this->getAns(),
            'ativo' => $this->isAtivo(),
            'codigoInstituicao' => $this->getCodigoInstituicao()
        );
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->codigoInstituicao;
    }

    /**
     * @param int $codigoInstituicao
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }
}
