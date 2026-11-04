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

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse\Service;

use App\Domain\Educacao\Escola\Models\Aluno;
use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\DisciplinaEnsino;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\Turno;
use App\Domain\Educacao\Escola\Requests\EmissaoDiarioClasseEscolarizacaoRequest;
use App\Domain\Educacao\Escola\Services\RegistroAulaService;
use AvaliacaoPeriodica;
use AvaliacaoPeriodicaRepository;
use Carbon\Carbon;
use DisciplinaRepository;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\AlunoDiarioClasse;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\DadosDiarioClasse;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\TurmaDiarioClasse;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use Etapa;
use EtapaRepository;
use Exception;
use Matricula;
use ProcedimentoAvaliacao;
use Turma;
use TurmaRepository;

/**
 * Class TurmaEscolarizacao
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse\Service
 */
class TurmaEscolarizacaoService
{
    /**
     * @var Turma
     */
    private $turma;
    /**
     * @var Etapa[]
     */
    private $etapas;
    /**
     * @var AvaliacaoPeriodica
     */
    private $avaliacaoPeriodica;

    /**
     * @var DisciplinaEnsino[]
     */
    private $disciplinas;

    /**
     * @var DadosDiarioClasse[]
     */
    private $dadosDiarioClasses = [];
    /**
     * @var bool
     */
    private $exibirAlunosAtivos;
    /**
     * @var bool
     */
    private $exibirTrocaTurma;
    /**
     * @var bool
     */
    private $filtrarFrequencia = false;
    /**
     * @var bool
     */
    private $periodoDeRecuperacao = false;
    /**
     * @var bool
     */
    private $apenasAlunosAtivos = false;
    /**
     * Se informado regente
     * @var string
     */
    private $regente;
    /**
     * @var bool
     */
    private $exibirAlunosRetorno;
    /**
     * @var string
     */
    private $dataCorte;

    /**
     * TurmaEscolarizacao constructor.
     * @param EmissaoDiarioClasseEscolarizacaoRequest $request
     */
    public function __construct(EmissaoDiarioClasseEscolarizacaoRequest $request)
    {
        $this->turma = TurmaRepository::getTurmaByCodigo($request->get('turma'));

        $data_corte = $request->get('dataCorte');
        $this->dataCorte = $data_corte ? \DBDate::create($data_corte)->convertTo('Y-m-d') : null;

        $periodoDiario = $this->getPeriodoDoDiario($request);
        $this->periodoDiario = [];
        $this->periodoDiario['inicio'] = $periodoDiario[0];
        $this->periodoDiario['fim'] = $periodoDiario[1];

        $this->etapas = collect($request->get('etapa'))->map(function ($etapa) {
            return EtapaRepository::getEtapaByCodigo($etapa);
        })->all();

        $this->avaliacaoPeriodica = AvaliacaoPeriodicaRepository::getAvaliacaoPeriodicaByCodigo(
            $request->get('periodo')
        );
        if ($request->modelo == 4) {
            $this->disciplinas = collect($request->get('disciplinas')[0])->map(function ($codigo) {
                return DisciplinaEnsino::find($codigo);
            })->all();
        } else {
            $this->disciplinas = collect($request->get('disciplinas'))->map(function ($codigo) {
                return DisciplinaEnsino::find($codigo);
            })->all();
        }


        $this->regente = $request->get('regente');
        $this->exibirTrocaTurma = $request->get('exibirTrocaTurma') == 1;
        $this->filtrarFrequencia = $request->get('registroManual') == 0;

        $this->apenasAlunosAtivos = $request->get('apenasAlunosAtivos') == 1;
        $this->exibirTrocaTurma = $request->get('exibirTrocaTurma') == 1;
        $this->periodoDeRecuperacao = $this->avaliacaoPeriodica->isRecuperacao();
        $this->exibirAlunosRetorno = $request->get('exibirAlunosRetorno') == 1;
    }

    public function getTurma()
    {
        return $this->turma;
    }

    public function getDataCorte()
    {
        return $this->dataCorte;
    }

    /**
     * @return DadosDiarioClasse[]
     * @throws Exception
     */
    public function processarDados()
    {
        $dados = [];
        foreach ($this->disciplinas as $disciplina) {
            $dadosDiarioClasse = $this->buildDiario($disciplina);
            if (!empty($this->regente)) {
                $dadosDiarioClasse->setNomeRegente($this->regente);
            }
            $dados[] = $this->buildMatriculas($dadosDiarioClasse);
        }

        return $dados;
    }

    /**
     * @param EmissaoDiarioClasseEscolarizacaoRequest $request
     * @return array
     * @throws Exception
     */
    public static function getPeriodoDoDiario($request)
    {
        if (empty($request)) {
            throw new Exception("Requisição vazia!", 1);
        }

        $oDaoTurma = new \cl_turma();
        $turma_str = $request->get('turma');
        $periodo_str = $request->get('periodo');

        $rsPeriodoCalendario = $oDaoTurma->sql_record("SELECT ed53_i_codigo, ed53_d_inicio, ed53_d_fim FROM escola.turma
            LEFT JOIN escola.periodocalendario on periodocalendario.ed53_i_calendario = turma.ed57_i_calendario
            LEFT JOIN escola.procavaliacao on procavaliacao.ed41_i_codigo = {$periodo_str}
            WHERE
                turma.ed57_i_codigo = {$turma_str} AND
                periodocalendario.ed53_i_periodoavaliacao = procavaliacao.ed41_i_periodoavaliacao
        ");

        if (!$rsPeriodoCalendario) {
            throw new Exception("Prezado usuário, os períodos de avaliação não foram informados no calendário.
            Deverá acessar o  Cadastro > Calendário > Alteração , na aba períodos de avaliação e informar.");
        }

        $periodoCalendario = pg_fetch_assoc($rsPeriodoCalendario);

        $calendarioInicio = \Carbon\Carbon::createFromFormat(
            'Y-m-d',
            $periodoCalendario['ed53_d_inicio']
        );
        $calendarioFim = \Carbon\Carbon::createFromFormat(
            'Y-m-d',
            $periodoCalendario['ed53_d_fim']
        );

        return array($calendarioInicio, $calendarioFim);
    }

    /**
     * @param DisciplinaEnsino $disciplina
     * @return DadosDiarioClasse
     * @throws Exception
     */
    private function buildDiario(DisciplinaEnsino $disciplina)
    {
        $turma = new TurmaDiarioClasse();
        $turma->setCodigo($this->turma->getCodigo())
            ->setNome($this->turma->getDescricao())
            ->setCurso($this->turma->getBaseCurricular()->getCurso())
            ->setEtapas($this->etapas);

        $dadosDiarioClasse = new DadosDiarioClasse();
        $regente = $this->buscarRegente($disciplina);

        $dadosDiarioClasse->setTurma($turma)
            ->setEscola(Escola::find($this->turma->getEscola()->getCodigo()))
            ->setCalendario(Calendario::find($this->turma->getCalendario()->getCodigo()))
            ->setAvaliacaoPeriodica($this->avaliacaoPeriodica)
            ->setAulasDadas($this->buscaAulasDadas($disciplina, $this->etapas))
            ->setDisciplina($disciplina)
            ->setNomeRegente($regente) // professor
            ->setTurno(Turno::find($this->turma->getTurno()->getCodigoTurno()))
            ->setProcedimentoAvaliacaoRegencia($this->buscaProcedimentoAvaliacaoRegencia($disciplina))
            ->setProcedimentoAvaliacaoTurma($this->buscaProcedimentoAvaliacaoTurma())
            ->setPeriodoDoDiario($this->periodoDiario)
            ->setDataCorte($this->dataCorte);

        return $dadosDiarioClasse;
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @return DadosDiarioClasse
     * @throws Exception
     */
    private function buildMatriculas(DadosDiarioClasse $dadosDiarioClasse)
    {
        $matriculas = $this->getMatriculas();
        foreach ($matriculas as $matricula) {
            $situacaoAluno = $matricula->getSituacao();
            if ($this->apenasAlunosAtivos) {
                $situacoes = [
                    SituacaoMatriculaEnum::AVANCADO,
                    SituacaoMatriculaEnum::CANCELADO,
                    SituacaoMatriculaEnum::TRANSFERIDO_REDE,
                    SituacaoMatriculaEnum::TRANSFERIDO_FORA,
                    SituacaoMatriculaEnum::EVADIDO,
                    SituacaoMatriculaEnum::FALECIDO,
                    SituacaoMatriculaEnum::TROCA_MODALIDADE,
                    SituacaoMatriculaEnum::MATRICULA_INDEVIDA,
                    SituacaoMatriculaEnum::RECLASSIFICADO,
                    SituacaoMatriculaEnum::DESISTENTE
                ];
                if (in_array($situacaoAluno, $situacoes)) {
                    continue;
                }
            }

            if (!$this->exibirTrocaTurma && $situacaoAluno === SituacaoMatriculaEnum::TROCA_TURMA) {
                continue;
            }

            $alunoDiario = new AlunoDiarioClasse();
            $dataEncerramento = $matricula->getDataEncerramento();
            $aluno = $matricula->getAluno();
            $matriculaAnteriorAluno = Aluno::find($aluno->getCodigoAluno())->matriculas()
                ->where('ed60_i_codigo', '<', $matricula->getCodigo())
                ->orderBy('ed60_i_codigo', 'desc')->get()->first();

            $matriculaAnterior = is_null($matriculaAnteriorAluno) ?
            null : \MatriculaRepository::getMatriculaByCodigo($matriculaAnteriorAluno->ed60_i_codigo);

            $alunoDiario->setCodigo($aluno->getCodigoAluno())
                ->setNome($aluno->getNome())
                ->setNomeSocial($aluno->getNomeSocial())
                ->setDataNascimento(new Carbon($aluno->getDataNascimento()))
                ->setNumero($matricula->getNumeroOrdemAluno())
                ->setSituacao(new SituacaoMatriculaEnum($matricula->getSituacao()))
                ->setSexo($aluno->getSexo())
                ->setMatricula($matricula)
                ->setMatriculaAnterior($matriculaAnterior);
            if (!is_null($dataEncerramento)) {
                $alunoDiario->setDataEncerramento($dataEncerramento);
            }
            $codigoDisciplina = $dadosDiarioClasse->getDisciplina()->getCodigo();
            $disciplina = DisciplinaRepository::getDisciplinaByCodigo($codigoDisciplina);
            $diarioDisciplina = is_null($matricula->getDiarioDeClasse()->getDisciplinasPorDisciplina($disciplina)) ?
                new \DiarioAvaliacaoDisciplina(null) :
                $matricula->getDiarioDeClasse()->getDisciplinasPorDisciplina($disciplina);


            $periodoAvaliacao = is_null($diarioDisciplina->getAvaliacoes()) ?
                null : $diarioDisciplina->getAvaliacoesPorOrdem(
                    $this->avaliacaoPeriodica->getOrdemSequencia()
                );
            $amparado = is_null($periodoAvaliacao) ? false : $periodoAvaliacao->isAmparado();
            $alunoDiario->setAmparado($amparado);
            $alunoDiario->setDiarioAvaliacaoDisciplina($diarioDisciplina);
            if ($this->filtrarFrequencia) {
                $alunoDiario->setFaltas(
                    $diarioDisciplina->getFaltasPorPeriodoDeAvaliacao($this->avaliacaoPeriodica->getPeriodoAvaliacao())
                );
                $alunoDiario->setFaltasAbonadasNoPeriodo(
                    $diarioDisciplina->getTotalFaltasAbonadasPorPeriodo(
                        $this->avaliacaoPeriodica->getPeriodoAvaliacao()
                    )
                );
            }

            if ($this->periodoDeRecuperacao) {
                if (!$diarioDisciplina->emRecuperacao()) {
                    continue;
                }
            }

            $dadosDiarioClasse->addAluno($alunoDiario);
        }

        if (!$this->apenasAlunosAtivos && !$this->exibirAlunosRetorno) {
            $alunos = $dadosDiarioClasse->getAlunos();
            $novosAlunos = [];

            foreach ($alunos as $aluno) {
                if (array_key_exists($aluno->getCodigo(), $novosAlunos)) {
                    $ultimoAluno = $novosAlunos[$aluno->getCodigo()];
                    if ($aluno->getMatricula()->getCodigo() < $ultimoAluno->getMatricula()->getCodigo()) {
                        continue;
                    }
                }

                $novosAlunos[$aluno->getCodigo()] = $aluno;
            }

            $dadosDiarioClasse->setAlunos(array_values($novosAlunos));
        }

        $alunos = $dadosDiarioClasse->getAlunos();

        if (count($dadosDiarioClasse->getAlunos()) === 0) {
            throw new Exception("Não existem alunos para os filtros selecionados", 406);
        }

        return $dadosDiarioClasse;
    }

    /**
     * @return Matricula[]
     */
    private function getMatriculas()
    {
        if (count($this->etapas) === 1) {
            $etapa = $this->etapas[0];
            return $this->turma->getAlunosMatriculadosNaTurmaPorSerie($etapa);
        }

        return $this->turma->getAlunosMatriculados();
    }

    /**
     * Retorna as aulas dadas da primeira regência da turma onde a disciplina é a mesma informada
     * @param DisciplinaEnsino $disciplina
     * @param Etapa[] $etapas
     * @return int|null
     */
    private function buscaAulasDadas(DisciplinaEnsino $disciplina, $etapas)
    {
        $aulasDadas = [];
        foreach ($etapas as $etapa) {
            $regencias = $this->turma->getDisciplinasPorEtapa($etapa);
            foreach ($regencias as $regencia) {
                if ($regencia->getDisciplina()->getCodigoDisciplina() == $disciplina->getCodigo()) {
                    $total = 0;
                    if (!empty($regencia->getTotalDeAulasNoPeriodo($this->avaliacaoPeriodica->getPeriodoAvaliacao()))) {
                        $total = $regencia->getTotalDeAulasNoPeriodo($this->avaliacaoPeriodica->getPeriodoAvaliacao());
                    }
                    $aulasDadas[] = $total;
                }
            }
        }

        return implode(' / ', $aulasDadas);
    }

    /**
     * @param DisciplinaEnsino $disciplina
     * @return string
     * @throws Exception
     */
    private function buscarRegente(DisciplinaEnsino $disciplina)
    {
        $regencias = $this->turma->getDisciplinas();
        foreach ($regencias as $regencia) {
            if ($regencia->getDisciplina()->getCodigoDisciplina() == $disciplina->getCodigo()) {
                $docentes = $regencia->getDocentes();
                if (!empty($docentes)) {
                    $docente = array_shift($docentes);

                    if ($docente->getNomeSocial() !== null && $docente->getNomeSocial() !== '') {
                        return $docente->getNome() . ' / Nome Social: ' . $docente->getNomeSocial();
                    }
                    return $docente->getNome();
                }
            }
        }
        return null;
    }

    /**
     * @param DisciplinaEnsino $disciplina
     * @return ProcedimentoAvaliacao
     * @throws Exception
     */
    private function buscaProcedimentoAvaliacaoRegencia(DisciplinaEnsino $disciplina)
    {
        $regencias = $this->turma->getDisciplinas();
        foreach ($regencias as $regencia) {
            if ($regencia->getDisciplina()->getCodigoDisciplina() == $disciplina->getCodigo()) {
                return $regencia->getProcedimentoAvaliacao();
            }
        }
    }

    /**
     * @return ProcedimentoAvaliacao
     */
    private function buscaProcedimentoAvaliacaoTurma()
    {
        return $this->turma->getProcedimentoDeAvaliacaoDaEtapa($this->etapas[0]);
    }

    private function buscarAuldasDadasTurma($codigo)
    {
        $service = new RegistroAulaService();
        return $service->getAulasDadasByTurma($codigo);
    }
}
