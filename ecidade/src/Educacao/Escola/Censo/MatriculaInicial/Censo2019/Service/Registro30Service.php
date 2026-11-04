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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Service;

use cl_rechumanonecessidade;
use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder\Registro30Builder;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Repository\Registro30Repository;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use ECidade\Educacao\Escola\Repository\AlunoNecessidadeEspecialRepository;
use ECidade\Educacao\Escola\Repository\AlunoRecursoNecessarioAvaliacaoInepRepository;
use ECidade\Educacao\Escola\Repository\AlunoRepository;
use ECidade\Educacao\Escola\Repository\ProfissionalEscolaRepository;
use ECidade\Educacao\Escola\Repository\ProfissionalFormacaoRepository;
use Escola;
use Exception;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Validators\Registro30Validator;

class Registro30Service
{
    /**
     * @var Escola
     */
    private $escola;

    /**
     * @var Registro30[]
     */
    private $registros = array();
    /**
     * @var Censo
     */
    private $censo;

    /**
     * @var Registro00Service
    */
    private $registro00Service;
    /**
     * @var Registro20Service
    */
    private $registro20Service;
    /**
     * @var Registro30Service
    */
    private $registro30Service;
    /**
     * @var Registro40Service
    */
    private $registro40Service;
    /**
     * @var Registro50Service
    */
    private $registro50Service;
    /**
     * @var Registro60Service
    */
    private $registro60Service;

    public function __construct()
    {
    }

    /**
     * @param Escola $escola
     */
    public function setEscola(Escola $escola)
    {
        $this->escola = $escola;
    }

    /**
     * @param Censo $censo
     */
    public function setCenso(Censo $censo)
    {
        $this->censo = $censo;
    }

    /**
     * @throws Exception
     */
    public function buscarDados()
    {
        $this->buscarProfissionais();
        $this->buscarAlunos();
    }

    public function setRegistro00Service(Registro00Service $registro00Service)
    {
        $this->registro00Service = $registro00Service;
    }

    public function setRegistro20Service(Registro20Service $registro20Service)
    {
        $this->registro20Service = $registro20Service;
    }

    public function setRegistro40Service(Registro40Service $registro40Service)
    {
        $this->registro40Service = $registro40Service;
    }

    public function setRegistro50Service(Registro50Service $registro50Service)
    {
        $this->registro50Service = $registro50Service;
    }

    public function setRegistro60Service(Registro60Service $registro60Service)
    {
        $this->registro60Service = $registro60Service;
    }

    private function buscarProfissionais()
    {
        $profissionalEscolaRepository = new ProfissionalEscolaRepository();
        $profissionalEscolaRepository->scopeDocentePresente($this->censo->getDataCenso());
        $profissionalEscolaRepository->scopeDiretorProfessorMonitor($this->censo->getDataCenso());
        $profissionaisEscola = $profissionalEscolaRepository->getProfissionaisAtivos(
            $this->escola,
            $this->censo->getDataCenso()
        );

        $profissionaisEscolaMatriculaUnificada = array();
        foreach ($profissionaisEscola as $profissionalEscola) {
            $profissionaisEscolaMatriculaUnificada[$profissionalEscola->getCgm()->getCpf()] = $profissionalEscola;
        }

        foreach ($this->buscaGestores() as $gestor) {
            if (!in_array($gestor, $profissionaisEscola)) {
                 $profissionaisEscolaMatriculaUnificada[$gestor->getCgm()->getCpf()] = $gestor;
            }
        }

        foreach ($profissionaisEscolaMatriculaUnificada as $profissionalEscola) {
            $repositoryFormacao = new ProfissionalFormacaoRepository();
            $formacoesProfissional = $repositoryFormacao
                ->scopeCodigoRecHumano($profissionalEscola->getCodigoRecursoHumano())
                ->scopeSituacao('CON')
                ->get();

            $posGraduacoes = $repositoryFormacao->getPosGraduacoes($profissionalEscola->getCgm()->getCodigo());
            $profissionalEscola->setPosgraduaceos($posGraduacoes);
            $profissionalEscola->setFormacoes($formacoesProfissional);

            $builder = new Registro30Builder();
            $builder->setInepEscola($this->escola->getCodigoInep());
            $builder->setDadosProfissional($profissionalEscola);
            $builder->setNecessidadesEspeciais($this->getNecessidadesEspecialProfissional($profissionalEscola));
            $builder->setOutrosDadosFormacao(
                Registro30Repository::buscaOutrosDadosAvaliacao($profissionalEscola->getCodigoRecursoHumano())
            );

            $this->addRegistro30($builder->build());
        }
    }

    private function addRegistro30(Registro30 $registro)
    {
        $this->registros[] = $registro;
    }

    private function getNecessidadesEspecialProfissional(ProfissionalEscola $profissional)
    {
        $dao = new cl_rechumanonecessidade();
        $where = "ed310_rechumano = {$profissional->getCodigoRecursoHumano()} ";
        $rs = db_query($dao->sql_query_file(null, "ed310_necessidade as codigo", null, $where));

        if (!$rs) {
            throw new Exception("Erro ao buscar necessidades especiais do profissional.");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }

        return pg_fetch_all($rs);
    }

    /**
     * Busca todos alunos matriculados em turmas regulares e em turmas ac
     * @throws Exception
     */
    private function buscarAlunos()
    {
        $alunoRepository = new AlunoRepository();
        $alunoNecessidade = new AlunoNecessidadeEspecialRepository();
        $alunoRecursos = new AlunoRecursoNecessarioAvaliacaoInepRepository();

        $alunosMatriculados = $alunoRepository->getAlunosCenso($this->escola, $this->censo->getDataCenso());

        $necessidadesExcluidasCenso = array(110, 111, 112);
        foreach ($alunosMatriculados as $aluno) {
            $necessidades = $alunoNecessidade
                ->scopeEscola($this->escola)
                ->scopeAluno($aluno)
                ->scopeNecessidade($necessidadesExcluidasCenso, 'not in')
                ->get();
            $aluno->setNecessidades($necessidades);

            $recursos = $alunoRecursos->scopeAluno($aluno)->get();
            $aluno->setRecursoNecessarioAvaliacaoInep($recursos);

            // remove as necessidades que podem existir duplicadas
            $necessidadesAuxiliar = array();
            foreach ($necessidades as $necessidade) {
                $necessidadesAuxiliar[$necessidade->getNecessidade()] = $necessidade->getNecessidade();
            }

            // cria um array na estrutua espera no builder
            $necessidadesAluno = array();
            foreach ($necessidadesAuxiliar as $necessidade) {
                $necessidadesAluno[]['codigo'] = $necessidade;
            }

            $builder = new Registro30Builder();
            $builder->setDadosAluno($aluno);
            $builder->setInepEscola($this->escola->getCodigoInep());
            $builder->setNecessidadesEspeciais($necessidadesAluno);
            $builder->setRecursoNecessarioAvaliacaoInep($recursos);
            $this->addRegistro30($builder->build());
        }
    }

    public function validar()
    {
        foreach ($this->registros as $registro) {
            $validator = new Registro30Validator();

            $validator->setCenso($this->censo);

            $registro00 = $this->registro00Service->getRegistro();
            $registro40 = $this->getRegistro40($registro);
            $registro60 = $this->getRegistro60($registro);
            $registros50 = $this->getRegistros50($registro);

            if ($registro60 == null) {
                $registros20 = $this->getRegistros20($registros50);
            } else {
                $registros20 = $this->getRegistros20(array($registro60));
            }


            $validator->setRegistro($registro);
            $validator->setRegistro00($registro00);
            $validator->setRegistros20($registros20);
            $validator->setRegistro40($registro40);
            $validator->setRegistros50($registros50);
            $validator->setRegistro60($registro60);

            $validator->validar();
        }
    }

    /**
     * @return Registro30[]
     */
    public function getRegistros()
    {
        return $this->registros;
    }

    private function getRegistros20(array $registros)
    {
        $res = array();

        foreach ($registros as $registro) {
            foreach ($this->registro20Service->getRegistros() as $registro20) {
                if ($registro->getCodigoTurma() == $registro20->getCodigoTurma()) {
                    $res[] = $registro20;
                    break;
                }
            }
        }

        return $res;
    }

    private function getRegistro40(Registro30 $pessoa)
    {
        foreach ($this->registro40Service->getRegistros() as $registro40) {
            if ($pessoa->getCodigoPessoa() == $registro40->getCodigoPessoa()) {
                return $registro40;
            }
        }
        return null;
    }

    private function getRegistros50(Registro30 $pessoa)
    {
        $res = array();

        foreach ($this->registro50Service->getRegistros() as $registro50) {
            if ($pessoa->getCodigoPessoa() == $registro50->getCodigoPessoa()) {
                $res[] = $registro50;
            }
        }

        return $res;
    }

    private function getRegistro60(Registro30 $pessoa)
    {
        foreach ($this->registro60Service->getRegistros() as $registro60) {
            if ($pessoa->getCodigoPessoa() == $registro60->getCodigoPessoa()) {
                return $registro60;
            }
        }

        return null;
    }

    public function buscaGestores()
    {
        $gestores = [];
        $sql = "select ed325_rechumano from escolagestorcenso where ed325_escola = {$this->escola->getCodigo()}";
        $rs = db_query($sql);
        while ($gestor = pg_fetch_assoc($rs)) {
            $dao = new \cl_rechumano();
            $where = ["ed75_i_rechumano = {$gestor['ed325_rechumano']}"];
            $sql = $dao->sqlProfissionaisEscola(null, $this->escola->getCodigo(), $where);
            $prof = pg_fetch_assoc(db_query($sql));
            $profissional = ProfissionalEscola::fromState($prof);
            $gestores[] = $profissional;
        }
        return $gestores;
    }
}
