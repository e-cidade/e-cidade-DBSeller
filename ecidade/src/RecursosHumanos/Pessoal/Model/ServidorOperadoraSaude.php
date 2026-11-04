<?php

namespace ECidade\RecursosHumanos\Pessoal\Model;

use ECidade\RecursosHumanos\Pessoal\Repository\OperadoraSaudeRepository;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOperadoraSaudeRepository;
use Exception;
use Instituicao;
use InstituicaoRepository;
use Rubrica;
use RubricaRepository;
use Servidor;
use ServidorRepository;
use ServidorOperadoraSaudeDependente as ServidorDependente;

/**
 * Class ServidorOperadoraSaude
 * @package ECidade\RecursosHumanos\Pessoal\Model
 */
class ServidorOperadoraSaude
{
    /**
     * @var int
     */
    private $sequencial;
    /**
     * @var float
     */
    private $valor;
    /**
     * @var int
     */
    private $ano;
    /**
     * @var int
     */
    private $mes;
    /**
     * @var Instituicao
     */
    private $instituicao;
    /**
     * @var OperadoraSaude;
     */
    private $operadoraSaude;
    /**
     * @var Servidor;
     */
    private $servidor;
    /**
     * @var Rubrica
     */
    private $rubrica;
    /**
     * @var ServidorDependente[]
     */
    private $servidorOperadoraSaudeDependente;

    /**
     * ServidorOperadoraSaude constructor.
     * @param null $id
     * @throws Exception
     */
    public function __construct($id = null)
    {
        if ($id) {
            $servidorOperadoraSaude = ServidorOperadoraSaudeRepository::find($id);
            $this->sequencial = $servidorOperadoraSaude->getSequencial();
            $this->valor = $servidorOperadoraSaude->getValor();
            $this->ano = $servidorOperadoraSaude->getAno();
            $this->mes = $servidorOperadoraSaude->getMes();
            $this->instituicao = $servidorOperadoraSaude->getInstituicao();
            $this->operadoraSaude = $servidorOperadoraSaude->getOperadoraSaude();
            $this->servidor = $servidorOperadoraSaude->getServidor();
            $this->rubrica = $servidorOperadoraSaude->getRubrica();
            $this->servidorOperadoraSaudeDependente = $servidorOperadoraSaude->getServidorOperadoraSaudeDependente();
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
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * @param int $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @return int
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param int $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @return Instituicao
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * @param Instituicao $instituicao
     */
    public function setInstituicao(Instituicao $instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * @return OperadoraSaude
     */
    public function getOperadoraSaude()
    {
        return $this->operadoraSaude;
    }

    /**
     * @param OperadoraSaude $operadoraSaude
     */
    public function setOperadoraSaude(OperadoraSaude $operadoraSaude)
    {
        $this->operadoraSaude = $operadoraSaude;
    }

    /**
     * @return Servidor
     */
    public function getServidor()
    {
        return $this->servidor;
    }

    /**
     * @param Servidor $servidor
     */
    public function setServidor(Servidor $servidor)
    {
        $this->servidor = $servidor;
    }

    /**
     * @return Rubrica
     */
    public function getRubrica()
    {
        return $this->rubrica;
    }

    /**
     * @param Rubrica $rubrica
     */
    public function setRubrica(Rubrica $rubrica)
    {
        $this->rubrica = $rubrica;
    }

    /**
     * @return ServidorDependente[]
     */
    public function getServidorOperadoraSaudeDependente()
    {
        return $this->servidorOperadoraSaudeDependente;
    }

    /**
     * @param ServidorDependente[] $servidorOperadoraSaudeDependente
     */
    public function setServidorOperadoraSaudeDependente($servidorOperadoraSaudeDependente)
    {
        $this->servidorOperadoraSaudeDependente = $servidorOperadoraSaudeDependente;
    }

    /**
     * @param array $state
     * @return ServidorOperadoraSaude
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $servidorOperadoraSaude = new self();

        if (array_key_exists('rh222_sequencial', $state)) {
            $servidorOperadoraSaude->setSequencial((int)$state['rh222_sequencial']);
        }

        if (array_key_exists('rh222_valor', $state)) {
            $servidorOperadoraSaude->setValor((float)$state['rh222_valor']);
        }

        if (array_key_exists('rh222_ano', $state)) {
            $servidorOperadoraSaude->setAno((int)$state['rh222_ano']);
        }

        if (array_key_exists('rh222_mes', $state)) {
            $servidorOperadoraSaude->setMes((int)$state['rh222_mes']);
        }

        if (array_key_exists('rh222_instituicao', $state)) {
            $servidorOperadoraSaude->setInstituicao(
                InstituicaoRepository::getInstituicaoByCodigo($state['rh222_instituicao'])
            );
        }

        if (array_key_exists('rh222_operadorasaude', $state)) {
            $servidorOperadoraSaude->setOperadoraSaude(OperadoraSaudeRepository::find($state['rh222_operadorasaude']));
        }

        if (array_key_exists('rh222_servidor', $state)) {
            $servidorOperadoraSaude->setServidor(ServidorRepository::getInstanciaByCodigo(
                $state['rh222_servidor'],
                $state['rh222_ano'],
                $state['rh222_mes'],
                $state['rh222_instituicao']
            ));
        }

        if (array_key_exists('rh222_rubrica', $state)) {
            $servidorOperadoraSaude->setRubrica(RubricaRepository::getInstanciaByCodigo($state['rh222_rubrica']));
        }

        return $servidorOperadoraSaude;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $retorno = array(
            'sequencial' => $this->getSequencial(),
            'valor' => $this->getValor(),
            'ano' => $this->getAno(),
            'mes' => $this->getMes(),
            'instituicao' => $this->getInstituicao()->toArray(),
            'operadoraSaude' => $this->getOperadoraSaude()->toArray(),
            'servidor' => $this->getServidor()->toArray(),
            'rubrica' => $this->getRubrica()->toArray(),
            'servidorOperadoraSaudeDependentes' => array()
        );
        $dependentes = $this->getServidorOperadoraSaudeDependente();
        if (count($dependentes) > 0) {
            foreach ($dependentes as $dependente) {
                $retorno['servidorOperadoraSaudeDependentes'][] = $dependente->toArray();
            }
        }
        return $retorno;
    }
}
