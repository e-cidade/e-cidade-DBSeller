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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder;

use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;
use ECidade\Educacao\Escola\Censo\Helpers\Turma;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro50;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\TurmaCensoVo;
use ECidade\Educacao\Escola\Model\CensoDisciplina;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use ECidade\RecursosHumanos\Pessoal\Registry\VinculoRegistry;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Service\Registro00Service;

class Registro50Builder
{

    protected static $deParaVinculo = array(
        1 => 1,
        2 => 4,
        3 => 3,
    );

    /**
     * @var Registro50
     */
    protected $registro;

    /**
     * @var ProfissionalEscola
     */
    protected $profissional;

    /**
     * @var integer
     */
    protected $funcao;

    /**
     * @var TurmaCensoVo
     */
    protected $turma;

    /**
     * @var CensoDisciplina[]
     */
    private $disciplinas = array();

    /**
     * @param ProfissionalEscola $profissional
     * @return Registro50Builder
     */
    public function addProfissional(ProfissionalEscola $profissional)
    {
        $this->profissional = $profissional;
        return $this;
    }

    /**
     * @param integer $funcao
     * @return Registro50Builder
     */
    public function addFuncao($funcao)
    {
        $this->funcao = $funcao;
        return $this;
    }

    /**
     * @param TurmaCensoVo $turma
     * @return Registro50Builder
     */
    public function addTurma(TurmaCensoVo $turma)
    {
        $this->turma = $turma;
        return $this;
    }

    /**
     *
     */
    public function build()
    {
        $this->create();
        $this->buildTurma();
        return $this->registro;
    }

    protected function create()
    {
        $this->registro = new Registro50();
    }

    protected function buildTurma()
    {
        $this->registro->setCodigoInepEscola($this->profissional->getEscola()->getCodigoInep());
        $this->registro->setCodigoPessoa(Pessoa::buildCodigoProfissional($this->profissional->getCgm()->getCpf()));
        $this->registro->setCodigoInep($this->profissional->getCodigoInep());

        $this->registro->setCodigoTurma(Turma::buildCodigoTurmaRegular($this->turma->getCodigoTurma()));
        if (!$this->turma->isEscolarizacao()) {
            $this->registro->setCodigoTurma(Turma::buildCodigoTurmaAC($this->turma->getCodigoTurma()));
        }

        if ($this->turma->isTurmaUnificada()) {
            $this->registro->setCodigoTurma(Turma::buildCodigoTurmaUnificada($this->turma->getCodigoTurmaUnificada()));
        }

        $this->registro->setFuncaoExerce($this->funcao);

        $dependenciaAdministrativa = Registro00Service::getRegistroProcessado()->getDependenciaAdministrativa();
        if (in_array($this->funcao, array(1, 5, 6)) && in_array($dependenciaAdministrativa, array(1, 2, 3))) {
            $regime = $this->profissional->getRegimeContratacao();

            if (!empty($regime)) {
                $vinculo = VinculoRegistry::get($regime);
                $this->registro->setRegimeContratacao(self::$deParaVinculo[$vinculo->getCodigoRegime()]);

                if ($vinculo->getNaturezaRegime() == 3) {
                    $this->registro->setRegimeContratacao(2);
                }
            }
        }

        if ($this->turma->isEscolarizacao() &&
            in_array($this->funcao, array(1, 5)) &&
            !in_array($this->turma->getEtapaCenso(), array(1, 2, 3))) {
            $this->buidDiscplinas();
        }
    }

    protected function buidDiscplinas()
    {
        foreach ($this->disciplinas as $index => $disciplina) {
            switch ($index) {
                case 0:
                    $this->registro->setCodigo1($disciplina->getCodigo());
                    break;
                case 1:
                    $this->registro->setCodigo2($disciplina->getCodigo());
                    break;
                case 2:
                    $this->registro->setCodigo3($disciplina->getCodigo());
                    break;
                case 3:
                    $this->registro->setCodigo4($disciplina->getCodigo());
                    break;
                case 4:
                    $this->registro->setCodigo5($disciplina->getCodigo());
                    break;
                case 5:
                    $this->registro->setCodigo6($disciplina->getCodigo());
                    break;
                case 6:
                    $this->registro->setCodigo7($disciplina->getCodigo());
                    break;
                case 7:
                    $this->registro->setCodigo8($disciplina->getCodigo());
                    break;
                case 8:
                    $this->registro->setCodigo9($disciplina->getCodigo());
                    break;
                case 9:
                    $this->registro->setCodigo10($disciplina->getCodigo());
                    break;
                case 10:
                    $this->registro->setCodigo11($disciplina->getCodigo());
                    break;
                case 11:
                    $this->registro->setCodigo12($disciplina->getCodigo());
                    break;
                case 12:
                    $this->registro->setCodigo13($disciplina->getCodigo());
                    break;
                case 13:
                    $this->registro->setCodigo14($disciplina->getCodigo());
                    break;
                case 14:
                    $this->registro->setCodigo15($disciplina->getCodigo());
                    break;
                case 15:
                    $this->registro->setCodigo16($disciplina->getCodigo());
                    break;
                case 16:
                    $this->registro->setCodigo17($disciplina->getCodigo());
                    break;
                case 17:
                    $this->registro->setCodigo18($disciplina->getCodigo());
                    break;
                case 18:
                    $this->registro->setCodigo19($disciplina->getCodigo());
                    break;
                case 19:
                    $this->registro->setCodigo20($disciplina->getCodigo());
                    break;
                case 20:
                    $this->registro->setCodigo21($disciplina->getCodigo());
                    break;
                case 21:
                    $this->registro->setCodigo22($disciplina->getCodigo());
                    break;
                case 22:
                    $this->registro->setCodigo23($disciplina->getCodigo());
                    break;
                case 23:
                    $this->registro->setCodigo24($disciplina->getCodigo());
                    break;
                case 24:
                    $this->registro->setCodigo25($disciplina->getCodigo());
                    break;
            }
        }
    }

    /**
     * @param CensoDisciplina[] $disciplinas
     */
    public function addDisciplinas(array $disciplinas)
    {
        $this->disciplinas = $disciplinas;
    }
}
