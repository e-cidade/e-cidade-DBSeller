<?php

namespace ECidade\RecursosHumanos\Pessoal\Model;

use Dependente;
use ECidade\RecursosHumanos\Pessoal\Repository\DependenteRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeDependenteRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use Exception;
use Rubrica;
use RubricaRepository;
use ParameterException;

/**
 * Class ServidorOperadoraSaudeDependente
 * @package ECidade\RecursosHumanos\Pessoal\Model
 */
class ServidorOperadoraSaudeDependente
{
    /**
     * @var int
     */
    private $sequencial;
    /**
     * @var string
     */
    private $tipo;
    /**
     * @var float
     */
    private $valor;
    /**
     * @var ServidorOperadoraSaude
     */
    private $servidorOperadoraSaude;
    /**
     * @var Dependente
     */
    private $dependente;
    /**
    * @var Rubrica
    */
    private $rubrica;

    /**
     * ServidorOperadoraSaudeDependente constructor.
     * @param null $id
     * @throws Exception
     */
    public function __construct($id = null)
    {
        if ($id) {
            $servidorOperadoraSaude = ServidorOperadoraSaudeDependenteRepository::find($id);

            $this->sequencial = $servidorOperadoraSaude->getSequencial();
            $this->tipo = $servidorOperadoraSaude->getTipo();
            $this->valor = $servidorOperadoraSaude->getValor();
            $this->servidorOperadoraSaude = $servidorOperadoraSaude->getServidorOperadoraSaude();
            $this->dependente = $servidorOperadoraSaude->getDependente();
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
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return float
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param float $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return ServidorOperadoraSaude
     */
    public function getServidorOperadoraSaude()
    {
        return $this->servidorOperadoraSaude;
    }

    /**
     * @param ServidorOperadoraSaude $servidorOperadoraSaude
     */
    public function setServidorOperadoraSaude(ServidorOperadoraSaude $servidorOperadoraSaude)
    {
        $this->servidorOperadoraSaude = $servidorOperadoraSaude;
    }

    /**
     * @return Dependente
     */
    public function getDependente()
    {
        return $this->dependente;
    }

    /**
     * @param Dependente $dependente
     */
    public function setDependente(Dependente $dependente)
    {
        $this->dependente = $dependente;
    }

    /**
     * @param array $state
     * @return ServidorOperadoraSaudeDependente
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $servidorOperadoraSaudeDependente = new self();

        if (array_key_exists('rh223_sequencial', $state)) {
            $servidorOperadoraSaudeDependente->setSequencial((int)$state['rh223_sequencial']);
        }

        if (array_key_exists('rh223_tipo', $state)) {
            $servidorOperadoraSaudeDependente->setTipo((string)$state['rh223_tipo']);
        }

        if (array_key_exists('rh223_valor', $state)) {
            $servidorOperadoraSaudeDependente->setValor((float)$state['rh223_valor']);
        }

        if (array_key_exists('rh223_servidoroperadorasaude', $state)) {
            $servidorOperadoraSaudeDependente
            ->setServidorOperadoraSaude(ServidorOperadoraSaudeRepository::find($state['rh223_servidoroperadorasaude']));
        }

        if (array_key_exists('rh223_dependente', $state)) {
            $servidorOperadoraSaudeDependente->setDependente(DependenteRepository::find($state['rh223_dependente']));
        }

        if (array_key_exists('rh223_rubrica', $state)) {
            $servidorOperadoraSaudeDependente
                ->setRubrica(RubricaRepository::getInstanciaByCodigo($state['rh223_rubrica']));
        }
        return $servidorOperadoraSaudeDependente;
    }

    /**
     * @return array
     * @throws ParameterException
     */
    public function toArray()
    {
        return array(
            'sequencial' => $this->getSequencial(),
            'tipo' => $this->getTipo(),
            'valor' => $this->getValor(),
            'servidorOperadoraSaude' => $this->getServidorOperadoraSaude()->toArray(),
            'dependente' => $this->getDependente()->toArray(),
            'rubrica' => $this->getRubrica()->toArray()
        );
    }

   /**
    * Get the value of rubrica
    *
    * @return  Rubrica
    */
    public function getRubrica()
    {
        return $this->rubrica;
    }

   /**
    * Set the value of rubrica
    *
    * @param  Rubrica  $rubrica
    *
    * @return  self
    */
    public function setRubrica(Rubrica $rubrica)
    {
        $this->rubrica = $rubrica;
    }
}
