<?php
namespace ECidade\RecursosHumanos\Pessoal\Model;

class DecimoModel
{

    private $matricula;
    private $instituicao;
    private $data;
    private $ano;

    /**
     * Get the value of matricula
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set the value of matricula
     *
     * @return  self
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get the value of instituicao
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * Set the value of instituicao
     *
     * @return  self
     */
    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;

        return $this;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of ano
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set the value of ano
     *
     * @return  self
     */
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }
}
