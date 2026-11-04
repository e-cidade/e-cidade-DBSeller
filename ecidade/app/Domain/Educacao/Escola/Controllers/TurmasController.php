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

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\Etapa;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Models\TurmaEspecial;
use App\Domain\Educacao\Escola\Repositories\ProfissionalEscolaRepository;
use App\Domain\Educacao\Escola\Repositories\TurmaRepository;
use App\Domain\Educacao\Escola\Models\Views\TurmasRegenteView;
use App\Domain\Educacao\Escola\Resources\TurmasRegentesViewResource;
use App\Domain\Educacao\Escola\Requests\TrocaAlunosTurmaRequest;
use App\Domain\Educacao\Escola\Resources\RegenciaResource;
use App\Domain\Educacao\Escola\Services\TrocaAlunosTurmaService;
use App\Http\Controllers\Controller;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use App\Domain\Educacao\Escola\Enums\AtendimentosEspecialEnum;
use App\Domain\Educacao\Escola\Resources\TurmaResource;
use Exception;
use Illuminate\Http\Request;
use Regencia;
use stdClass;
use UsuarioSistema;
use App\Domain\Educacao\Escola\Services\TurmasProfissionaisService;

/**
 * Class TurmasController
 * @package App\Domain\Educacao\Escola\Controllers
 */
class TurmasController extends Controller
{
    protected $repository;
    public function __construct()
    {
        $this->repository = new TurmaRepository();
    }

    public function index(Request $request)
    {
        $turmas = Turma::query();
        if ($request->has('calendario')) {
            $calendario = $request->get('calendario');
            $turmas->where('ed57_i_calendario', $calendario);
        }

        if ($request->has('tipoTurma')) {
            $turmas->where('ed57_i_tipoturma', $request->get('tipoTurma'));
        }

        $turmasEtapas = [];
        $turmas->get()->map(function (Turma $turma) use (&$turmasEtapas) {
            foreach ($turma->etapasTurma as $turmaEtapa) {
                $turmasEtapas[] = (object)[
                    "turmaCodigo" => $turma->getCodigo(),
                    "turmaDescricao" => trim($turma->getDescricao()),
                    "turmaEtapaCodigo" => $turmaEtapa->ed220_i_codigo,
                    "etapaCodigo" => $turmaEtapa->etapaRegimeMatricula->etapa->ed11_i_codigo,
                    "etapaOrdem" => $turmaEtapa->etapaRegimeMatricula->etapa->ed11_i_sequencia,
                    "etapaDescricao" => trim($turmaEtapa->etapaRegimeMatricula->etapa->ed11_c_descr),
                ];
            }
        });

        usort($turmasEtapas, function ($a, $b) {
            if ($a->etapaOrdem == $b->etapaOrdem) {
                return strcmp($a->turmaDescricao, $b->turmaDescricao);
            }
            return ($a->etapaOrdem < $b->etapaOrdem) ? -1 : 1;
        });

        return new DBJsonResponse($turmasEtapas, '');
    }
    public function buscar($codigo)
    {
        $turma = Turma::with('calendario')->find($codigo);
        $turma->etapas = $turma->getEtapas();
        return new DBJsonResponse($turma);
    }

    public function buscarTurmasMultiEtapas($calendario)
    {
        $turmas = Turma::whereIn('ed57_i_tipoturma', [3, 7])
            ->where('ed57_i_calendario', $calendario)->get();

        return new DBJsonResponse($turmas);
    }

    public function buscaTurmasPorEscolaCalendarios($codigoEscola, $calendarios)
    {
        $aCalendarios = explode('&', $calendarios);
        $turmas = Turma::whereIn('ed57_i_calendario', $aCalendarios)
            ->where('ed57_i_escola', $codigoEscola)
            ->orderBy('ed57_c_descr')
            ->get()
            ->map(function ($turma) {
                return [
                    'ed57_i_codigo' => $turma->ed57_i_codigo,
                    'ed57_c_descr' => trim($turma->ed57_c_descr),
                ];
            });
        return new DBJsonResponse($turmas);
    }

    public function buscarTurmasPorCalendario($calendario, Request $request)
    {
        $validaUsuario = $request->has('validaUsuario');
        $hasFullPermission = false;
        $isProfessor = false;
        $escola = $request->DB_coddepto;
        $profissionalRepository = new ProfissionalEscolaRepository();
        $filtros = (object) [
            'escola' => $escola,
            'cgm' => $request->user()->usuarioCgm->cgm->z01_numcgm
        ];

        $profissionalEscola = $profissionalRepository->getProfissionaisEscola($filtros);

        if ($profissionalEscola->count() > 0) {
            $permissoes = [];
            foreach ($profissionalEscola as $profissional) {
                $permissoes[] = $profissional->permissaoDiario;
            }
            $hasFullPermission = in_array('TOTAL', $permissoes);
            $isProfessor = in_array('PROFESSOR', $permissoes);
        }

        if ($validaUsuario && !$hasFullPermission && $isProfessor) {
            $turmasQuery = new TurmasRegenteView();
            $turmasRegentesResource = new TurmasRegentesViewResource();

            $turmasRegentes = $turmasQuery->where('escola_professor', $escola)
                ->where('calendario', $calendario)
                ->where('cgm', $filtros->cgm);

            $turmas = $turmasRegentesResource->toResponse($turmasRegentes->get());

            usort($turmas, function ($a, $b) {
                return strcmp($a->nome, $b->nome);
            });

            return new DBJsonResponse($turmas);
        }

        $turmas = Turma::where('ed57_i_calendario', $calendario)->orderBy('ed57_c_descr')->get()
            ->filter(function ($turma) {
                return ($turma->getTipoTurma() == 6 || count($turma->matriculas) > 0);
            })
            ->map(function ($turma) {
                foreach ($turma->etapaRegimeMatricula->procedimento->procedimentosAvaliacao as $prcoAvalicao) {
                    $proc[] = trim($prcoAvalicao->formaAvaliacao->ed37_c_tipo) == 'PARECER';
                }
                $turma->temFormaAvaliacaoParecer = in_array(true, $proc);
                return $turma;
            });

        if ($validaUsuario) {
            $turmasResource = new TurmaResource();
            $turmasReturn = [];

            foreach ($turmas as $turma) {
                $turmasReturn[] = $turmasResource->toResponse($turma, ['turnoReferente', 'etapa']);
            }

            return new DBJsonResponse($turmasReturn);
        }

        return new DBJsonResponse($turmas->values());
    }

    public function matriculasEtapa(Turma $turma, Etapa $etapa)
    {
        $matriculas = $turma->getMatriculas();
        foreach ($matriculas as $key => $matricula) {
            if ($matricula['ed60_c_concluida'] == "S") {
                unset($matriculas[$key]);
                continue;
            }
            if ($matricula['ed60_c_ativa'] != "S") {
                unset($matriculas[$key]);
                continue;
            }
            if ($matricula['ed60_c_situacao'] != SituacaoMatriculaEnum::MATRICULADO) {
                unset($matriculas[$key]);
                continue;
            }
            $etapaMatricula = $matricula->getEtapaMatricula()->shift();
            if ($etapaMatricula->ed11_i_codigo !== $etapa->ed11_i_codigo) {
                unset($matriculas[$key]);
                continue;
            }
            $matricula->aluno;
        }
        $matriculas = array_values($matriculas->toArray());

        return new DBJsonResponse($matriculas);
    }

    public function vagas(Turma $turma)
    {
        foreach ($turma->turnosReferentes as $turnoReferente) {
            $turnoReferente->turno = (object)[
                "nome" => ''
            ];
            switch ($turnoReferente->ed336_turnoreferente) {
                case 1:
                    $turnoReferente->turno->nome = 'MANHÃ';
                    break;
                case 2:
                    $turnoReferente->turno->nome = 'TARDE';
                    break;
                case 3:
                    $turnoReferente->turno->nome = 'NOITE';
                    break;
            }
        }
        return new DBJsonResponse($turma);
    }

    public function regenciasTurmas(Turma $turmaOrigem, Turma $turmaDestino, Etapa $etapa)
    {
        $turmaOrigem->etapaRegimeMatricula;
        $turmaDestino->etapaRegimeMatricula;

        foreach ($turmaOrigem->etapaRegimeMatricula->procedimento->procedimentosAvaliacao as $procedimentoAvaliacao) {
            $procedimentosAvaliacaoDestino = $turmaDestino->etapaRegimeMatricula->procedimento->procedimentosAvaliacao;
            foreach ($procedimentosAvaliacaoDestino as $key => $procedimentoAvaliacaoDestino) {
                $periodoOrigem = $procedimentoAvaliacao['ed41_i_periodoavaliacao'];
                $periodoDestino = $procedimentoAvaliacaoDestino['ed41_i_periodoavaliacao'];
                if ($periodoOrigem === $periodoDestino) {
                    $procedimentoAvaliacao->equivalente = $procedimentoAvaliacaoDestino;
                    unset($procedimentosAvaliacaoDestino[$key]);
                }
            }
        }

        foreach ($turmaOrigem->regencias as $key => $regenciaOrigem) {
            if ($regenciaOrigem['ed59_i_serie'] !== $etapa['ed11_i_codigo']) {
                unset($turmaOrigem->regencias[$key]);
                continue;
            }
            foreach ($turmaDestino->regencias as $key => $regenciaDestino) {
                if ($regenciaOrigem['ed59_i_disciplina'] === $regenciaDestino['ed59_i_disciplina']) {
                    $regenciaOrigem->equivalente = $regenciaDestino;
                    unset($turmaDestino->regencias[$key]);
                }
            }
        }

        $turmaOrigem->regenciasOrigem = array_values($turmaOrigem->regencias->toArray());
        $turmaDestino->regenciasSemVinculo = array_values($turmaDestino->regencias->toArray());
        return new DBJsonResponse([$turmaOrigem, $turmaDestino]);
    }

    /**
     * @param TrocaAlunosTurmaRequest $request
     * @return DBJsonResponse
     * @throws \BusinessException
     * @throws \DBException
     * @throws \ParameterException
     * @throws Exception
     */
    public function trocarAlunosTurma(TrocaAlunosTurmaRequest $request)
    {
        $turmaDestino = $request->get('turmaDestino');
        $turmaOrigem =  $request->get('turmaOrigem');
        $matriculas = $request->get('matriculas');
        $regencias = $request->get('regencias');
        $dataAlteracao = $request->get('dataAlteracao');
        $etapaDestino = $request->get('etapaDestino');
        $turnos = $request->get('turnosReferentes');
        $importarAvaliacoes = $request->get('importarAvaliacoes');

        $regenciasVinculadas = [];
        if (is_null($regencias)) {
            throw new Exception("Erro ao buscar disciplinas na turma de Origem.");
        }
        foreach ($regencias as $key => $regencia) {
            $regencia = json_decode(str_replace('\\"', '"', $regencia));
            if (!empty($regencia->equivalente)) {
                $oRegencia              = new stdClass();
                $oRegencia->origem      = new Regencia($regencia->regenciaOrigem);
                $oRegencia->destino     = new Regencia($regencia->equivalente->regenciaDestino);
                $regenciasVinculadas[] = $oRegencia;
            }
        }
        $procedimentos = $request->get('procedimentosAvaliacao');
        foreach ($procedimentos as $key => $procedimento) {
            $string = str_replace('\\"', '"', $procedimento);
            $procedimentos[$key] = json_decode($string);
        }

        $trocaAlunosService = new TrocaAlunosTurmaService(
            $turmaDestino,
            $matriculas,
            $regenciasVinculadas,
            $procedimentos,
            $dataAlteracao,
            $etapaDestino,
            $turnos,
            $importarAvaliacoes,
            $turmaOrigem
        );

        $retorno = $trocaAlunosService->processar();
        return new DBJsonResponse('', $retorno);
    }


    public function buscarTurmasEspeciaisPorCalendarioEscola(Request $request)
    {
        $turmas = TurmaEspecial::select(
            'ed268_i_codigo as id',
            'ed268_c_descr as descricao',
            'ed268_c_aee as atendimentos'
        )
            ->getPorCalendarioEscola($request->get('calendario'), $request->get('escola'))
            ->get()->all();

        return new DBJsonResponse($turmas, '');
    }

    public function buscarTurmasEspeciaisPorCalendarioEscolaAEE(Request $request)
    {
        $turmas = TurmaEspecial::select(
            'ed268_i_codigo as id',
            'ed268_c_descr as descricao',
            'ed268_c_aee as atendimentos'
        )
            ->where('ed268_i_tipoatend', 5)
            ->getPorCalendarioEscola($request->get('calendario'), $request->get('escola'))
            ->get()->all();

        return new DBJsonResponse($turmas, '');
    }

    public function getTiposAtendimentos()
    {
        return new DBJsonResponse(AtendimentosEspecialEnum::all());
    }

    public function buscarDisciplinas($turma)
    {
        $etapasDistintasDaTurma = array_unique(Turma::find($turma)->regencias->map(function ($regenciaLista) {
            return $regenciaLista->ed59_i_serie;
        })->toArray());
        $isTipoTurmaMultiEtapa = count($etapasDistintasDaTurma) > 1;

        $disciplinas = [];
        $regencias = [];
        if ($isTipoTurmaMultiEtapa) {
            $regencias = Turma::find($turma)->regencias->map(function ($regencia) use ($regencias) {
                if (!in_array($regencia->ed59_i_codigo, $regencias)) {
                    return $regencia->ed59_i_codigo;
                }
            });
            foreach ($regencias as $regenciaId) {
                $dadosDisciplina = new StdClass();
                $regencia = new Regencia($regenciaId);
                $disciplinaAtual = $regencia->getDisciplina();
                $dadosDisciplina->ed232_i_codigo = $disciplinaAtual->getCodigoDisciplina();
                $dadosDisciplina->ed232_c_abrev = $disciplinaAtual->getAbreviatura();
                $dadosDisciplina->codigoRegenciaNaTurma = $regenciaId;
                $dadosDisciplina->ed232_c_descr = $disciplinaAtual
                        ->getNomeDisciplina()." - ".$regencia->getEtapa()->getNome();
                $disciplinas[] = $dadosDisciplina;
            }
            return new DBJsonResponse($disciplinas);
        }

        $disciplinas = Turma::find($turma)->regencias->map(function ($regencia) {
            $regencia->disciplinaEnsino->disciplina->codigoRegenciaNaTurma = $regencia->ed59_i_codigo;
            return $regencia->disciplinaEnsino->disciplina;
        });
        return new DBJsonResponse($disciplinas);
    }

    public function getRegenciasPorEtapa(Request $request, $turma, $etapa)
    {
        $rechumano = null;
        $hasFullPermission = false;

        if (!is_null($request->get('validaUsuario')) && $request->user()->id_usuario !== 1) {
            $profissionalRepository = new ProfissionalEscolaRepository();
            $filtros = (object) [];
            $filtros->escola = $request->get('DB_coddepto');
            $filtros->cgm = $request->user()->usuarioCgm->cgm->z01_numcgm;
            $profissionalEscola = $profissionalRepository->getProfissionaisEscola($filtros);

            if ($profissionalEscola->count() === 0) {
                $hasFullPermission = true;
            }

            if ($profissionalEscola->count() > 0) {
                $hasFullPermission = $profissionalEscola->first()->permissaoDiario === 'TOTAL';
                if ($profissionalEscola->first()->permissaoDiario === 'PROFESSOR') {
                    $rechumano = $profissionalEscola->first()->cod_rechumano;
                    if ($profissionalEscola->count() > 1) {
                        $rechumano = $profissionalEscola->map(function ($rechumano) use (&$hasFullPermission) {
                            if ($rechumano->permissaoDiario === 'TOTAL') {
                                $hasFullPermission = true;
                            }
                            return $rechumano->cod_rechumano;
                        });
                        $rechumano = $rechumano->toArray();
                    }
                }
            }
        }

        if ($hasFullPermission) {
            $regencias = $this->repository->getRegenciasByEtapa($turma, $etapa, null)->map(function ($regencia) {
                return RegenciaResource::toResponse($regencia);
            });

            return new DBJsonResponse($regencias);
        }

        $regencias = $this->repository->getRegenciasByEtapa($turma, $etapa, $rechumano)->map(function ($regencia) {
            return RegenciaResource::toResponse($regencia);
        });

        return new DBJsonResponse($regencias);
    }

    /**
     * @return DBJsonResponse
     *
     * @throws Exception
     */
    public function turmasProfissional()
    {
        $codigoUsuario = db_getsession('DB_id_usuario');

        $usuarioSistema = new UsuarioSistema($codigoUsuario);

        $cgm = $usuarioSistema->getCGM();
        $codigoEscola = db_getsession('DB_coddepto');

        // Verifica admin
        $turmasProfissionaisService = new TurmasProfissionaisService();
        if ($usuarioSistema->isAdministrador()) {
            $dados = $turmasProfissionaisService->buscarTurmasEscola($codigoEscola);

            return new DBJsonResponse($dados);
        }

        // Verifica professor
        $profissionalEscolaRepository = new ProfissionalEscolaRepository;
        $profissionalEscola = $profissionalEscolaRepository->scopeEscola($codigoEscola)
            ->scopeCgm($cgm->getCodigo())
            ->scopeAtivo()
            ->first();

        if (is_null($profissionalEscola)) {
            throw new Exception("Profissional não tem permissão nessa escola!");
        }

        $dados = $turmasProfissionaisService->buscarTurmasPorProfissional(
            $profissionalEscola,
            $codigoEscola
        );

        return new DBJsonResponse($dados);
    }

    public function getPeriodosAvaliacao(Request $request, Turma $turma)
    {
        $sql = " select ed09_i_codigo,
                   ed09_c_descr,
                   ed09_c_abrev,
                   case
                       when current_date between ed53_d_inicio and ed53_d_fim
                           then true
                       else false end ativo,
                   ed53_d_inicio,
                   ed53_d_fim,
                   current_date
            from turma
                     join turmaserieregimemat ON turmaserieregimemat.ed220_i_turma = turma.ed57_i_codigo
                     join serieregimemat ON serieregimemat.ed223_i_codigo = turmaserieregimemat.ed220_i_serieregimemat
                     join procedimento ON procedimento.ed40_i_codigo = turmaserieregimemat.ed220_i_procedimento
                     join procavaliacao ON procavaliacao.ed41_i_procedimento = procedimento.ed40_i_codigo
                     join periodoavaliacao ON periodoavaliacao.ed09_i_codigo = procavaliacao.ed41_i_periodoavaliacao
                     join periodocalendario
                         ON periodocalendario.ed53_i_periodoavaliacao = periodoavaliacao.ed09_i_codigo
                and periodocalendario.ed53_i_calendario = turma.ed57_i_calendario
            where ed57_i_codigo = {$turma->getCodigo()}
                and serieregimemat.ed223_i_serie = {$request->get('etapa')}
            order by ed09_i_sequencia; ";

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar periodos de avaliação");
        }

        $dados = \db_utils::getCollectionByRecord($rs);

        $periodosAvaliacaos = [];
        $temAbaAtiva = false;
        foreach ($dados as $dado) {
            if ($dado->ativo == 't') {
                $temAbaAtiva = true;
            }
            $periodosAvaliacaos[] = (object)[
                'titulo' => $dado->ed09_c_descr,
                'periodoAvaliacao' => $dado->ed09_i_codigo,
                'containerId' => "periodo-{$dado->ed09_i_codigo}",
                'ativo' => $dado->ativo == 't'
            ];
        }

        if (!$temAbaAtiva) {
            $periodosAvaliacaos[0]->ativo = true;
        }
    
        return new DBJsonResponse($periodosAvaliacaos);
    }
}
