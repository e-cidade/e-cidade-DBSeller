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
use App\Domain\Educacao\Escola\Factories\FormaObtencaoFactory;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AproveitamentoAvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ProcedimentoResultadoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ResultadoParcial;
use App\Domain\Educacao\Escola\Models\Diario;
use App\Domain\Educacao\Escola\Models\ExcecaoBloqueioNota;
use App\Domain\Educacao\Escola\Models\ParametrosBloqueioNota;
use App\Domain\Educacao\Escola\Services\AvaliacoesParciaisService;
use App\Domain\Educacao\Escola\Services\BloqueioNotasService;
use App\Domain\Educacao\Escola\Services\ResultadoParcialService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use JSON;

class AvaliacaoParcialController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarNotasPeriodo(Request $request)
    {
        $regencia = new \Regencia($request->get('regencia'));
        $periodoavaliacao = new \PeriodoAvaliacao($request->get('periodoavaliacao'));

        $etapa = $regencia->getEtapa();
        $turma = $regencia->getTurma();
        $matriculas = $turma->getAlunosMatriculadosNaTurmaPorSerie($etapa);

        $avaliacoesParciaisService = new AvaliacoesParciaisService();
        $avaliacoesParciais = $avaliacoesParciaisService->getAvaliacoesRegenciaPeriodo($regencia, $periodoavaliacao);

        $procavaliacao = $avaliacoesParciais[0]->ed340_procavaliacao;

        $resultadoParcialService = new ResultadoParcialService();
        $procedimentoResultadoParcial = ProcedimentoResultadoParcial::query()
            ->regencia($regencia)
            ->avaliacaoPeriodica(new \AvaliacaoPeriodica($procavaliacao))
            ->first();

        $resultadoPeriodoStrategy = FormaObtencaoFactory::criarEstrategiaResultadoParcial(
            $procedimentoResultadoParcial->ed342_formaobtencaofinal
        );

        $resultadoParcialService->setResultadoPeriodoStrategy($resultadoPeriodoStrategy);

        $alunosNotas = [];

        foreach ($matriculas as $matricula) {
            require_once(modification("dbforms/db_funcoes.php"));
            db_inicio_transacao();
            $codigoDiario = $matricula->getDiarioDeClasse()->getDisciplinasPorRegencia($regencia)->getCodigoDiario();
            db_fim_transacao();
            $alunoNota = (object)[
                "matricula" => $matricula->getCodigo(),
                "aluno" => $matricula->getAluno()->getNome(),
                "situacao" => $matricula->getSituacao(),
                "concluida" => $matricula->isConcluida(),
                "data_modificacao" => !is_null($matricula->getDataModificacao())
                    ? $matricula->getDataModificacao()->convertTo('d/m/Y')
                    : '',
                "isAvaliadoPorParecer" => $matricula->isAvaliadoPorParecer(),
                "atingiuMinimo" => false,
                "valorAproveitamentoDiario" => null,
                "avaliacoes" => [],
                "resultado" => null,
                "resultado_periodo" => null,
            ];

            foreach ($avaliacoesParciais as $avalicaoParcial) {
                $alunoNota->avaliacoes[$avalicaoParcial->ed340_codigo] = $avalicaoParcial->toArray();

                $aproveitamento = $avaliacoesParciaisService->getAproveitamentoAluno(
                    $codigoDiario,
                    $avalicaoParcial
                );

                $alunoNota->avaliacoes[$avalicaoParcial->ed340_codigo]['aproveitamento'] = $aproveitamento->toArray();

                $diario = Diario::find($codigoDiario);
                $resultadoParcialAluno = $resultadoParcialService->getResultadoParcialAluno(
                    $diario,
                    $procedimentoResultadoParcial
                );
                $alunoNota->resultado = $resultadoParcialAluno;

                $aproveitamentoDiario = $resultadoParcialService->getAproveitamentoDiario(
                    $resultadoParcialAluno,
                    $matricula
                );
                if (!is_null($aproveitamentoDiario)) {
                    $valorAproveitamentoDiario = $aproveitamentoDiario->getValorAproveitamento()
                        ->getAproveitamentoReal();
                    $alunoNota->valorAproveitamentoDiario = \ArredondamentoNota::formatar(
                        $valorAproveitamentoDiario,
                        $diario->calendario->getAno()
                    );
                } else {
                    throw new Exception("Não foi possível encontrar a Nota de Aproveitamento da turma");
                }

                if ($matricula->isAvaliadoPorParecer()) {
                    $alunoNota->valorAproveitamentoDiario = "PD";
                }
            }

            $alunoNota->avaliacoes = array_values($alunoNota->avaliacoes);
            $alunoNota->atingiuMinimo = $resultadoParcialService->alunoAtingiuMinimo(
                $alunoNota->resultado,
                $alunoNota->resultado->ed343_valornota
            );

            $alunoNota->resultado_periodo = $resultadoParcialService->calcularResultadoPeriodo($alunoNota->resultado);
            $alunosNotas[] = $alunoNota;
        }

        return new DBJsonResponse($alunosNotas);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarAvaliacoesRegenciaPeriodo(Request $request)
    {
        $regencia = new \Regencia($request->get('regencia'));
        $periodoavaliacao = new \PeriodoAvaliacao($request->get('periodoavaliacao'));

        $avaliacoesParciaisService = new AvaliacoesParciaisService();
        $avaliacoesParciais = $avaliacoesParciaisService->getAvaliacoesRegenciaPeriodo($regencia, $periodoavaliacao);
        $resultado = $avaliacoesParciaisService->getResultadoRegenciaPeriodo($regencia, $periodoavaliacao);
        $formaAvaliacao = $resultado->procedimentoAvaliacao->formaAvaliacao;

        // Parametros bloqueio de notas
        $parametrosBloqueio = ParametrosBloqueioNota::first();
        $bloqueioNotasService = new BloqueioNotasService(
            $parametrosBloqueio->ed362_tipobloqueio,
            $parametrosBloqueio->ed362_prazodias,
            BloqueioNotasService::ACESSO_PARCIAIS
        );
        $turma = $regencia->getTurma();
        $excecoesBloqueio = ExcecaoBloqueioNota::query()->where('ed363_turma', $turma->getCodigo())->get();
        $bloqueioNotasService->adicionarExcecao($excecoesBloqueio);

        $calendario = $turma->getCalendario();
        $periodoCalendario = $calendario->getPeriodoCalendarioPorPeriodoAvaliacao($periodoavaliacao);

        $response = (object)[
            'avaliacoes' => $avaliacoesParciais,
            'resultado' => $resultado,
            'formaAvaliacao' => $formaAvaliacao,
            'permiteLancarNotas' => $bloqueioNotasService->podeLancarNota($regencia, $periodoCalendario)
        ];
        return new DBJsonResponse($response);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function adicionarAvaliacoesRegenciaPeriodo(Request $request)
    {
        $regencia = new \Regencia($request->get('regencia'));
        $periodoavaliacao = new \PeriodoAvaliacao($request->get('periodoavaliacao'));

        $avaliacoesParciaisService = new AvaliacoesParciaisService();
        $avaliacoesParciaisService->adicionarAvaliacoesRegenciaPeriodo($regencia, $periodoavaliacao);
        return new DBJsonResponse([], '', 201);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function excluirAvaliacaoParcial(Request $request)
    {
        $avaliacaoParcial = AvaliacaoParcial::find($request->get('avaliacaoParcial'));
        $codigoRegencia = $avaliacaoParcial->ed340_regencia;
        $codigoProcavaliacao = $avaliacaoParcial->ed340_procavaliacao;
        $avaliacaoParcial->delete();

        $regencia = new \Regencia($codigoRegencia);
        $avaliacaoPeriodica = new \AvaliacaoPeriodica($codigoProcavaliacao);
        $avaliacoesParciais = AvaliacaoParcial::query()->orderBy('ed340_ordem')
            ->regencia($regencia)
            ->avaliacaoPeriodica($avaliacaoPeriodica)
            ->get();

        $ordem = 1;

        foreach ($avaliacoesParciais as $avaliacaoParcial) {
            if ($avaliacaoParcial->ed340_ordem !== $ordem) {
                $avaliacaoParcial->ed340_ordem = $ordem;
                $avaliacaoParcial->save();
            }

            $ordem++;
        }

        return new DBJsonResponse([], 'Avaliação deletada com sucesso');
    }

    /**
     * @throws Exception
     */
    public function salvarAproveitamentos(Request $request)
    {
        $dados = $request->get('dados');
        $dadosAvaliacoes = JSON::create()->parse(str_replace("\\", "", $dados));

        $regencia = $dadosAvaliacoes[0]->avaliacoes[0]->ed340_regencia;
        $procavaliacao = $dadosAvaliacoes[0]->avaliacoes[0]->ed340_procavaliacao;

        $procedimentoResultadoParcial = ProcedimentoResultadoParcial::query()
            ->regencia(new \Regencia($regencia))
            ->avaliacaoPeriodica(new \AvaliacaoPeriodica($procavaliacao))
            ->first();

        $resultadoParcialService = new ResultadoParcialService();
        $resultadoParcialStrategy = FormaObtencaoFactory::criarEstrategiaResultadoParcial(
            $procedimentoResultadoParcial->ed342_formaobtencao
        );

        $resultadoParcialService->setResultadoParcialStrategy($resultadoParcialStrategy);

        foreach ($dadosAvaliacoes as $aluno) {
            $aproveitamentosAluno = [];

            foreach ($aluno->avaliacoes as $avaliacao) {
                if ($aluno->situacao !== "MATRICULADO") {
                    continue;
                }

                $aproveitamento = $avaliacao->aproveitamento;
                $aproveitamentoAvaliacaoParcial = AproveitamentoAvaliacaoParcial::find($aproveitamento->ed341_codigo);
                $nota = $aproveitamento->ed341_valornota == '' ? null : $aproveitamento->ed341_valornota;
                $aproveitamentoAvaliacaoParcial->ed341_valornota = $nota;
                $aproveitamentoAvaliacaoParcial->ed341_valornivel = $aproveitamento->ed341_valornivel;
                $aproveitamentoAvaliacaoParcial->save();

                $aproveitamentosAluno[] = $aproveitamentoAvaliacaoParcial;
            }

            if (count($aproveitamentosAluno) > 0) {
                $resultadoParcialAluno = $resultadoParcialService->getResultadoParcialAluno(
                    $aproveitamentosAluno[0]->diario,
                    $procedimentoResultadoParcial
                );

                $resultadoParcial = $resultadoParcialService->calcularResultadoParcial(
                    $aproveitamentosAluno,
                    $resultadoParcialAluno
                );

                $resultadoParcial->ed343_observacao = $aluno->resultado->ed343_observacao;
                $resultadoParcial->ed343_recuperacaonota = $aluno->resultado->ed343_recuperacaonota;

                if (empty($aluno->resultado->ed343_recuperacaonota)) {
                    $resultadoParcial->ed343_recuperacaonota = null;
                }

                $resultadoParcial->ed343_recuperacaonivel = $aluno->resultado->ed343_recuperacaonivel;

                if (empty($aluno->resultado->ed343_recuperacaonota)) {
                    $resultadoParcial->ed343_recuperacaonivel = '';
                }

                $resultadoParcial->save();
            }
        }

        return new DBJsonResponse([], 'Dados das avaliações salvos com sucesso!');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function encerrarPeriodoAluno(Request $request)
    {
        $this->validate($request, [
            'matricula' => 'required',
            'resultadoParcial' => 'required',
        ]);

        $resultadoParcial = ResultadoParcial::find($request->get('resultadoParcial'));
        $matricula = \MatriculaRepository::getMatriculaByCodigo($request->get('matricula'));

        $resultadoParcialStrategy = FormaObtencaoFactory::criarEstrategiaResultadoParcial(
            $resultadoParcial->procedimentoResultadoParcial->ed342_formaobtencaofinal
        );

        $resultadoParcialService = new ResultadoParcialService();
        $resultadoParcialService->setResultadoPeriodoStrategy($resultadoParcialStrategy);
        $notaFinalPeriodo = $resultadoParcialService->calcularResultadoPeriodo($resultadoParcial);
        $resultadoParcialService->encerrarPeriodoAluno($resultadoParcial, $matricula, $notaFinalPeriodo);

        return new DBJsonResponse([], 'Período do Aluno encerrado com sucesso!');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function reabrirPeriodoAluno(Request $request)
    {
        $this->validate($request, [
            'matricula' => 'required',
            'resultadoParcial' => 'required',
        ]);

        $resultadoParcial = ResultadoParcial::find($request->get('resultadoParcial'));
        $matricula = \MatriculaRepository::getMatriculaByCodigo($request->get('matricula'));

        $resultadoParcialService = new ResultadoParcialService();
        $resultadoParcialService->reabrirPeriodoAluno($resultadoParcial, $matricula);

        return new DBJsonResponse([], 'Período do Aluno reaberto com sucesso!');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function encerrarPeriodos(Request $request)
    {
        $dados = $request->get('dados');
        $dadosAvaliacoes = JSON::create()->parse(str_replace("\\", "", $dados));

        foreach ($dadosAvaliacoes as $aluno) {
            $resultadoParcial = ResultadoParcial::find($aluno->resultado->ed343_codigo);
            $matricula = \MatriculaRepository::getMatriculaByCodigo($aluno->matricula);

            if ($matricula->getSituacao() !== 'MATRICULADO' ||
                $matricula->isConcluida() ||
                $resultadoParcial->ed343_encerrado ||
                $matricula->isAvaliadoPorParecer() == true
            ) {
                continue;
            }

            $resultadoParcialStrategy = FormaObtencaoFactory::criarEstrategiaResultadoParcial(
                $resultadoParcial->procedimentoResultadoParcial->ed342_formaobtencaofinal
            );

            $resultadoParcialService = new ResultadoParcialService();
            $resultadoParcialService->setResultadoPeriodoStrategy($resultadoParcialStrategy);
            $notaFinalPeriodo = $resultadoParcialService->calcularResultadoPeriodo($resultadoParcial);
            $resultadoParcialService->encerrarPeriodoAluno($resultadoParcial, $matricula, $notaFinalPeriodo);
        }

        return new DBJsonResponse([], 'Período dos Alunos encerrado com sucesso!');
    }

    public function reabrirPeriodos(Request $request)
    {
        $dados = $request->get('dados');
        $dadosAvaliacoes = JSON::create()->parse(str_replace("\\", "", $dados));

        foreach ($dadosAvaliacoes as $aluno) {
            $resultadoParcial = ResultadoParcial::find($aluno->resultado->ed343_codigo);
            $matricula = \MatriculaRepository::getMatriculaByCodigo($aluno->matricula);

            if ($matricula->getSituacao() !== 'MATRICULADO' ||
                $matricula->isConcluida() ||
                !$resultadoParcial->ed343_encerrado ||
                $matricula->isAvaliadoPorParecer() == true
            ) {
                continue;
            }

            $resultadoParcialService = new ResultadoParcialService();
            $resultadoParcialService->reabrirPeriodoAluno($resultadoParcial, $matricula);
        }

        return new DBJsonResponse([], 'Período dos Alunos reaberto com sucesso!');
    }
}
