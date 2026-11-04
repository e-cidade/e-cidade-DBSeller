<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model;

use Carbon\Carbon;
use DBDate;
use DiarioAvaliacaoDisciplina;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use Exception;
use Falta;

/**
 * Class AlunoDiarioClasse
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse\Factory
 */
class AlunoDiarioClasse
{
    /**
     * @var integer
     */
    protected $codigo;
    /**
     * @var integer
     */
    protected $matriculaAnterior;
    /**
     * @var string
     */
    protected $nome;
     /**
     * @var string
     */
    protected $nomeSocial;
    /**
     * @var Carbon
     */
    protected $dataNascimento;
    /**
     * @var integer
     */
    private $numero;

    /**
     * @var \Matricula
     */
    private $matricula = [];
    /**
     * @var SituacaoMatriculaEnum
     */
    private $situacao;

    /**
     * @var DBDate
     */
    private $dataEncerramento;
    /**
     * @var array
     */
    private $faltas = [];
    /**
     * @var int
     */
    private $faltasAbonadasNoPeriodo;
    /**
     * @var DiarioAvaliacaoDisciplina
     */
    private $diarioAvaliacaoDisciplina;
    /**
     * @var string
     */
    private $sexo;
    /**
     * @var boolean
     */
    private $amparado = false;

    /**
     * @return int
     */
    public function getMatriculaAnterior()
    {
        return $this->matriculaAnterior;
    }

    /**
     * @param int $matriculaAnterior
     * @return AlunoDiarioClasse
     */
    public function setMatriculaAnterior($matriculaAnterior)
    {
        $this->matriculaAnterior = $matriculaAnterior;
        return $this;
    }


    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     * @return AlunoDiarioClasse
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return AlunoDiarioClasse
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeSocial()
    {
        return $this->nomeSocial;
    }

    /**
     * @param string $nomeSocial
     * * @return AlunoDiarioClasse
     */
    public function setNomeSocial($nomeSocial)
    {
        $this->nomeSocial = $nomeSocial;
        return $this;
    }
    /**
     * @return Carbon
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
    }

    /**
     * @param Carbon $dataNascimento
     * @return AlunoDiarioClasse
     */
    public function setDataNascimento(Carbon $dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
        return $this;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getIdadeEmAnos()
    {
        $interval = $this->dataNascimento->diff(new \Carbon\Carbon());
        return $interval->y;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     * @return AlunoDiarioClasse
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * @param SituacaoMatriculaEnum $situacao
     * @return $this
     */
    public function setSituacao(SituacaoMatriculaEnum $situacao)
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @return SituacaoMatriculaEnum
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * @return \Matricula
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @param \Matricula $matricula
     * @return AlunoDiarioClasse
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
        return $this;
    }

    /**
     * @return DBDate
     */
    public function getDataEncerramento()
    {
        return $this->dataEncerramento;
    }

    /**
     * @param DBDate $dataEncerramento
     * @return AlunoDiarioClasse
     */
    public function setDataEncerramento($dataEncerramento)
    {
        $this->dataEncerramento = $dataEncerramento;
        return $this;
    }

    public function setFaltas(array $faltas)
    {
        $this->faltas = $faltas;
    }

    /**
     * @return Falta[]
     */
    public function getFaltas()
    {
        return $this->faltas;
    }

    /**
     * @param $faltasAbonadas
     */
    public function setFaltasAbonadasNoPeriodo($faltasAbonadas)
    {
        $this->faltasAbonadasNoPeriodo = $faltasAbonadas;
    }

    /**
     * @return int
     */
    public function getFaltasAbonadasNoPeriodo()
    {
        return $this->faltasAbonadasNoPeriodo;
    }

    /**
     * @param DiarioAvaliacaoDisciplina $diarioDisciplina
     * @return AlunoDiarioClasse
     */
    public function setDiarioAvaliacaoDisciplina(DiarioAvaliacaoDisciplina $diarioDisciplina)
    {
        $this->diarioAvaliacaoDisciplina = $diarioDisciplina;
        return $this;
    }

    public function getDiarioAvaliacaoDisciplina()
    {
        return $this->diarioAvaliacaoDisciplina;
    }

    /**
     * @param $sexo
     * @return $this
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function isAmparado()
    {
        return $this->amparado;
    }

    /**
     * @param bool $amparado
     */
    public function setAmparado($amparado = false)
    {
        $this->amparado = $amparado;
    }
}
