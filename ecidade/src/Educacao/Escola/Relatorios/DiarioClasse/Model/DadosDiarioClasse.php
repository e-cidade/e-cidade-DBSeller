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

use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\DisciplinaEnsino;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\Turno;
use AvaliacaoPeriodica;
use ECidade\Educacao\Escola\Model\AtividadeComplementar;

/**
 * Class TurmaDiarioClasse
 * Essa classe é um a interface que representa uma Turma a ser impressa no Diário de Classe.
 * Essa turma pode ser uma Turma Regular (Escolarização) ou uma Turma AC (Especial)
 *
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model
 */
class DadosDiarioClasse
{
    /**
     * @var AtividadeComplementar
     */
    protected $atividadeComplementar;

    /**
     * @var AlunoDiarioClasse[]
     */
    protected $alunos = [];

    /**
     * @var Escola
     */
    private $escola;

    /**
     * @var Calendario
     */
    private $calendario;

    /**
     * @var Turno
     */
    private $turno;
    /**
     * @var TurmaDiarioClasse
     */
    private $turma;

    /**
     * @var AvaliacaoPeriodica
     */
    private $avaliacaoPeriodica;

    /**
     * Em caso de turma multietapa, retorna as aulas dadas da primeira etapa que contém a disciplina
     * @var string
     */
    private $aulasDadas;

    /**
     * @var DisciplinaEnsino
     */
    private $disciplina;
    /**
     * @var string
     */
    private $nomeRegente;
    /**
     * @var string
     */
    private $nomeSocialRegente;
    /**
     * @var \ProcedimentoAvaliacao
     */
    private $procedimentoAvaliacaoTurma;
    /**
     * @var \ProcedimentoAvaliacao
     */
    private $procedimentoAvaliacaoRegencia;
    /**
     * @var string
     */
    private $dataCorte;
    /**
     * @return AtividadeComplementar
     */
    public function getAtividadeComplementar()
    {
        return $this->atividadeComplementar;
    }

    /**
     * @param AtividadeComplementar $atividadeComplementar
     * @return DadosDiarioClasse
     */
    public function setAtividadeComplementar(AtividadeComplementar $atividadeComplementar)
    {
        $this->atividadeComplementar = $atividadeComplementar;
        return $this;
    }

    /**
     * @return AlunoDiarioClasse[]
     */
    public function getAlunos()
    {
        return $this->alunos;
    }

    /**
     * @param AlunoDiarioClasse[] $alunos
     * @return DadosDiarioClasse
     */
    public function setAlunos(array $alunos)
    {
        $this->alunos = $alunos;
        return $this;
    }

    /**
     * @param AlunoDiarioClasse $aluno
     */
    public function addAluno(AlunoDiarioClasse $aluno)
    {
        $this->alunos[] = $aluno;
    }

    /**
     * @return Escola
     */
    public function getEscola()
    {
        return $this->escola;
    }

    /**
     * @param Escola $escola
     * @return DadosDiarioClasse
     */
    public function setEscola(Escola $escola)
    {
        $this->escola = $escola;
        return $this;
    }

    /**
     * @return Calendario
     */
    public function getCalendario()
    {
        return $this->calendario;
    }

    /**
     * @param Calendario $calendario
     * @return DadosDiarioClasse
     */
    public function setCalendario(Calendario $calendario)
    {
        $this->calendario = $calendario;
        return $this;
    }

    /**
     * @return Turno
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * @param Turno $turno
     * @return DadosDiarioClasse
     */
    public function setTurno($turno)
    {
        $this->turno = $turno;
        return $this;
    }

    /**
     * @param TurmaDiarioClasse $turma
     * @return $this
     */
    public function setTurma(TurmaDiarioClasse $turma)
    {
        $this->turma = $turma;
        return $this;
    }

    /**
     * @return TurmaDiarioClasse
     */
    public function getTurma()
    {
        return $this->turma;
    }

    /**
     * @param AvaliacaoPeriodica $avaliacaoPeriodica
     * @return $this
     */
    public function setAvaliacaoPeriodica(AvaliacaoPeriodica $avaliacaoPeriodica)
    {
        $this->avaliacaoPeriodica = $avaliacaoPeriodica;
        return $this;
    }

    /**
     * @return AvaliacaoPeriodica
     */
    public function getAvaliacaoPeriodica()
    {
        return $this->avaliacaoPeriodica;
    }

    /**
     * @return string
     */
    public function getAulasDadas()
    {
        return $this->aulasDadas;
    }

    /**
     * Em caso de turma multietapa, retorna as aulas dadas da primeira etapa que contém a disciplina
     * @param string $aulasDadas
     * @return DadosDiarioClasse
     */
    public function setAulasDadas($aulasDadas)
    {
        $this->aulasDadas = $aulasDadas;
        return $this;
    }

    /**
     * @return DisciplinaEnsino
     */
    public function getDisciplina()
    {
        return $this->disciplina;
    }

    /**
     * @param DisciplinaEnsino $disciplina
     * @return DadosDiarioClasse
     */
    public function setDisciplina(DisciplinaEnsino $disciplina)
    {
        $this->disciplina = $disciplina;
        return $this;
    }

    /**
     * @param string $regente
     * @return $this
     */
    public function setNomeRegente($regente)
    {
        $this->nomeRegente = $regente;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeRegente()
    {
        return $this->nomeRegente;
    }

     /**
     * @param string $nome_social
     * @return $this
     */
    public function setNomeSocialRegente($nome_social)
    {
        $this->nomeSocialRegente = $nome_social;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeSocialRegente()
    {
        return $this->nomeSocialRegente;
    }

    /**
     * @return mixed
     */
    public function getProcedimentoAvaliacaoTurma()
    {
        return $this->procedimentoAvaliacaoTurma;
    }

    /**
     * @param mixed $procedimentoAvaliacaoTurma
     * @return DadosDiarioClasse
     */
    public function setProcedimentoAvaliacaoTurma($procedimentoAvaliacaoTurma)
    {
        $this->procedimentoAvaliacaoTurma = $procedimentoAvaliacaoTurma;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProcedimentoAvaliacaoRegencia()
    {
        return $this->procedimentoAvaliacaoRegencia;
    }

    /**
     * @param mixed $procedimentoAvaliacaoRegencia
     * @return DadosDiarioClasse
     */
    public function setProcedimentoAvaliacaoRegencia($procedimentoAvaliacaoRegencia)
    {
        $this->procedimentoAvaliacaoRegencia = $procedimentoAvaliacaoRegencia;
        return $this;
    }

    /**
     * @return AvaliacaoPeriodica[]
     */
    public function getAvaliacoesControlaFrequencia()
    {
        $avaliacoes = [];
        foreach ($this->procedimentoAvaliacaoTurma->getAvaliacoes() as $avaliacao) {
            if ($avaliacao->getPeriodoAvaliacao()->hasControlaFrequencia()) {
                $avaliacoes[] = $avaliacao;
            }
        }

        return $avaliacoes;
    }

    /**
     * @return AvaliacaoPeriodica|null
     */
    public function getUltimoPeriodoControleFrequencia()
    {
        $avaliacoes = $this->getAvaliacoesControlaFrequencia();

        $ultimo = null;
        foreach ($avaliacoes as $avaliacao) {
            if ($avaliacao->getOrdemSequencia() >= $this->avaliacaoPeriodica->getOrdemSequencia()) {
                $ultimo = $avaliacao;
            }
        }

        return $ultimo;
    }

    /**
     * @return array
     */
    public function getPeriodoDoDiario()
    {
        return $this->periodoDiario;
    }

    /**
     * @return DadosDiarioClasse
     */
    public function setPeriodoDoDiario($periodoDoDiario)
    {
        $this->periodoDiario = $periodoDoDiario;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataCorte()
    {
        return $this->dataCorte;
    }

    /**
     * @param string $dataCorte
     * @return DadosDiarioClasse
     */
    public function setDataCorte($dataCorte)
    {
        $this->dataCorte = $dataCorte;
        return $this;
    }
}
