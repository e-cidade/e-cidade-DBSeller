<?php

namespace ECidade\Tributario\Issqn\Model;

use DateTime;
use Exception;

class ProcessoEletronicoGrauRisco
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var integer
     */
    private $processo;

    /**
     * @var integer
     */
    private $grauRisco;

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
     * @return int
     */
    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * @param int $processo
     */
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    /**
     * @return int
     */
    public function getGrauRisco()
    {
        return $this->grauRisco;
    }

    /**
     * @param int $alvaraMei
     */
    public function setGrauRisco($grauRisco)
    {
        $this->grauRisco = $grauRisco;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'codigo' => $this->getCodigo(),
            'processo' => $this->getProcesso(),
            'grauRisco' => $this->getGrauRisco()
        );
    }

    /**
     * @param array $state
     * @return ProcessoEletronicoGrauRisco
     * @throws Exception
     */
    public function fromState(array $state)
    {

        if (array_key_exists('q151_sequencial', $state)) {
            $this->setCodigo($state['q151_sequencial']);
        }

        if (array_key_exists('q151_processo', $state)) {
            $this->setProcesso($state['q151_processo']);
        }

        if (array_key_exists('q151_graurisco', $state)) {
            $this->setGrauRisco($state['q151_graurisco']);
        }

        return $this;
    }
}
