<?php

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Controllers\Relatorios\InscritosTransposicaoCSV;
use App\Domain\Educacao\Escola\Models\CandidatoTransposicao;
use App\Domain\Educacao\Escola\Models\DesignadoTransposicao;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\Transposicao;
use App\Domain\Educacao\Escola\Models\VagasTransposicao;
use App\Domain\Educacao\Escola\Relatorios\TransposicaoPDF;
use App\Domain\Educacao\Escola\Requests\RelatorioTransposicaoRequest;
use App\Domain\Educacao\Escola\Requests\TransposicaoCandidatosRequest;
use App\Domain\Educacao\Escola\Services\ProcessarTransposicaoService;
use App\Domain\Educacao\Escola\Services\TransposicaoService;
use App\Http\Controllers\Controller;
use db_utils;
use EtapaRepository;
use Exception;
use Illuminate\Http\Request;
use JSON;
use MatriculaRepository;
use Turma;

class TransposicaoController extends Controller
{
    /**
     * @param TransposicaoCandidatosRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscarCandidatos(TransposicaoCandidatosRequest $request)
    {
        $codigoTurma = $request->get('turma');
        $etapaDestino = $request->get('etapaDestino');
        $anoDestino = $request->get('anoDestino');

        $candidatos = [];
        $turma = new Turma($codigoTurma);
        $matriculas = $turma->getAlunosMatriculados();

        $transposicao = Transposicao::ano($anoDestino)->first();
        if (is_null($transposicao)) {
            if ($anoDestino != date('Y') && $anoDestino != date('Y') + 1) {
                throw new Exception("Não é possível realizar transposição para o ano informado.");
            }
            $transposicao = new Transposicao();
            $transposicao->ed353_ano = $anoDestino;
            $transposicao->ed353_ativa = true;
            $transposicao->ed353_processada = false;
            $transposicao->save();
        }

        foreach ($matriculas as $matricula) {
            $codigoAluno = $matricula->getAluno()->getCodigoAluno();
            $candidatoVO = (object)[
                'codigo' => '',
                'codigo_matricula' => $matricula->getCodigo(),
                'codigo_aluno' => $codigoAluno,
                'nome_aluno' => $matricula->getAluno()->getNome(),
                'escola1' => '',
                'escola2' => '',
                'escola3' => '',
                'irmao1' => '',
                'irmao2' => '',
                'irmao3' => '',
                'servidor1' => '',
                'servidor2' => '',
                'servidor3' => '',
                'situacao' => $matricula->getSituacao(),
            ];

            $candidatoTransposicao = CandidatoTransposicao::where('ed349_matricula', $matricula->getCodigo())
                ->where('ed349_etapa', $etapaDestino)
                ->where('ed349_ano', $anoDestino)
                ->first();

            $designado = DesignadoTransposicao::where('ed354_transposicao', $transposicao->ed353_codigo)
                ->where('ed354_aluno', $codigoAluno)->first();
            if (!is_null($designado)) {
                $candidatoVO->situacao = "DESIGNADO";
            }

            if (!is_null($candidatoTransposicao)) {
                if ($matricula->getSituacao() != 'MATRICULADO' && $candidatoVO->situacao != "DESIGNADO") {
                    $candidatoTransposicao->delete();
                    $candidatos[] = $candidatoVO;
                    continue;
                }
                $candidatoVO->codigo = $candidatoTransposicao->ed349_codigo;
                $candidatoVO->escola1 = $candidatoTransposicao->ed349_escola1;
                $candidatoVO->escola2 = $candidatoTransposicao->ed349_escola2;
                $candidatoVO->escola3 = $candidatoTransposicao->ed349_escola3;
                $candidatoVO->irmao1 = $candidatoTransposicao->ed349_irmao1;
                $candidatoVO->irmao2 = $candidatoTransposicao->ed349_irmao2;
                $candidatoVO->irmao3 = $candidatoTransposicao->ed349_irmao3;
                $candidatoVO->servidor1 = $candidatoTransposicao->ed349_servidor1;
                $candidatoVO->servidor2 = $candidatoTransposicao->ed349_servidor2;
                $candidatoVO->servidor3 = $candidatoTransposicao->ed349_servidor3;
            }

            $candidatos[] = $candidatoVO;
        }

        $escolaLogada = db_getsession('DB_coddepto');
        $sqlEscolas = "select ed18_i_codigo, ed18_c_nome from escola
                            join cursoescola ON cursoescola.ed71_i_escola = escola.ed18_i_codigo
                            join cursoedu ON cursoedu.ed29_i_codigo = cursoescola.ed71_i_curso
                            join ensino ON ensino.ed10_i_codigo = cursoedu.ed29_i_ensino
                            join serie ON serie.ed11_i_ensino = ensino.ed10_i_codigo
                         where serie.ed11_i_codigo = {$etapaDestino} and ed18_i_codigo <> {$escolaLogada}";
        $rsEscolas = db_query($sqlEscolas);
        if (!$rsEscolas) {
            throw new Exception("Erro ao buscar escolas com a etapa de destino");
        }
        $escolasDestino = db_utils::getCollectionByRecord($rsEscolas);
        return new DBJsonResponse(['candidatos' => $candidatos, 'escolasDisponiveis' => $escolasDestino]);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function salvarCandidatos(Request $request)
    {
        $anoDestino = (int)$request->get('anoDestino');
        $etapaDestino = (int)$request->get('etapaDestino');
        $etapaOrigem = (int)$request->get('etapaOrigem');
        $escolaOrigem = (int)db_getsession('DB_coddepto');
        $candidatos = JSON::create()->parse(str_replace("\\", "", $request->get('candidatos')));

        foreach ($candidatos as $candidato) {
            if ((!empty($candidato->escola1) && empty($candidato->escola2) && empty($candidato->escola3)) ||
                (empty($candidato->escola1) && !empty($candidato->escola2) && empty($candidato->escola3)) ||
                (empty($candidato->escola1) && empty($candidato->escola2) && !empty($candidato->escola3))
            ) {
                continue;
            }

            if (empty($candidato->escola1) && empty($candidato->escola2) && empty($candidato->escola3)) {
                if (!empty($candidato->codigo)) {
                    $candidatoSalvar = CandidatoTransposicao::findOrFail($candidato->codigo);
                    $candidatoSalvar->delete();
                }
                continue;
            }

            if (!empty($candidato->codigo)) {
                $candidatoSalvar = CandidatoTransposicao::findOrFail($candidato->codigo);
            } else {
                $candidatoSalvar = new CandidatoTransposicao();
                $candidatoSalvar->ed349_aluno = $candidato->codigo_aluno;
                $candidatoSalvar->ed349_ano = $anoDestino;
                $candidatoSalvar->ed349_etapa = $etapaDestino;
                $candidatoSalvar->ed349_etapaorigem = $etapaOrigem;
                $candidatoSalvar->ed349_escolaorigem = $escolaOrigem;
                $matricula = MatriculaRepository::getMatriculaByCodigo($candidato->codigo_matricula);
                if (empty($matricula)) {
                    continue;
                }
                $candidatoSalvar->ed349_matricula = $matricula->getCodigo();
            }

            $candidatoSalvar->ed349_escola1 = null;
            if (!empty($candidato->escola1)) {
                $candidatoSalvar->ed349_escola1 = $candidato->escola1;
            }
            $candidatoSalvar->ed349_escola2 = null;
            if (!empty($candidato->escola2)) {
                $candidatoSalvar->ed349_escola2 = $candidato->escola2;
            }
            $candidatoSalvar->ed349_escola3 = null;
            if (!empty($candidato->escola3)) {
                $candidatoSalvar->ed349_escola3 = $candidato->escola3;
            }
            $candidatoSalvar->ed349_irmao1 = null;
            if (!empty($candidato->irmao1)) {
                $candidatoSalvar->ed349_irmao1 = $candidato->irmao1;
            }
            $candidatoSalvar->ed349_irmao2 = null;
            if (!empty($candidato->irmao2)) {
                $candidatoSalvar->ed349_irmao2 = $candidato->irmao2;
            }
            $candidatoSalvar->ed349_irmao3 = null;
            if (!empty($candidato->irmao3)) {
                $candidatoSalvar->ed349_irmao3 = $candidato->irmao3;
            }
            $candidatoSalvar->ed349_servidor1 = null;
            if (!empty($candidato->servidor1)) {
                $candidatoSalvar->ed349_servidor1 = $candidato->servidor1;
            }
            $candidatoSalvar->ed349_servidor2 = null;
            if (!empty($candidato->servidor1)) {
                $candidatoSalvar->ed349_servidor2 = $candidato->servidor1;
            }
            $candidatoSalvar->ed349_servidor3 = null;
            if (!empty($candidato->servidor1)) {
                $candidatoSalvar->ed349_servidor3 = $candidato->servidor1;
            }
            $candidatoSalvar->save();
        }

        return new DBJsonResponse([], 'Candidatos salvos com sucesso!');
    }

    /**
     * @param RelatorioTransposicaoRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function relatorio(RelatorioTransposicaoRequest $request)
    {
        $anoDestino = (int)$request->get('anoDestino');
        $etapaDestino = (int)$request->get('etapaDestino');
        $escolaOrigem = (int)$request->get('escolaOrigem');

        $candidatos = CandidatoTransposicao::where('ed349_etapa', $etapaDestino)
            ->where('ed349_ano', $anoDestino)
            ->where('ed349_escolaorigem', $escolaOrigem)
            ->get();

        $pdf = new TransposicaoPDF($candidatos);
        return new DBJsonResponse($pdf->emitirPdf());
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function vagasPorEtapa(Request $request)
    {
        $ano = $request->get('ano');
        $etapa = EtapaRepository::getEtapaByCodigo($request->get('etapa'));

        $sqlEscolas = "select distinct ed18_i_codigo
            from escola
              join escolabase ON escolabase.ed77_i_escola = escola.ed18_i_codigo
              join base ON base.ed31_i_codigo = escolabase.ed77_i_base
              join baseserie ON baseserie.ed87_i_codigo = base.ed31_i_codigo
              join serie serieinicial on serieinicial.ed11_i_codigo = ed87_i_serieinicial
              join serie seriefinal on seriefinal.ed11_i_codigo = ed87_i_seriefinal
            where ed18_i_funcionamento = 1 and seriefinal.ed11_i_ensino = {$etapa->getEnsino()->getCodigo()}
              and serieinicial.ed11_i_ensino = {$etapa->getEnsino()->getCodigo()}
              and {$etapa->getOrdem()} between serieinicial.ed11_i_sequencia and seriefinal.ed11_i_sequencia";

        $rsEscolas = db_query($sqlEscolas);
        if (!$rsEscolas) {
            throw new Exception("Erro ao buscar escolas");
        }

        $escolas = db_utils::getCollectionByRecord($rsEscolas);

        $aVagasTransposicao = [];
        foreach ($escolas as $escola) {
            $vagas = VagasTransposicao::with(['escola' => function ($query) {
                $query->select('ed18_i_codigo', 'ed18_c_nome');
            }])
                ->where('ed350_ano', $ano)
                ->where('ed350_escola', $escola->ed18_i_codigo)
                ->where('ed350_etapa', $etapa->getCodigo())
                ->first();

            if (is_null($vagas)) {
                $vagas = new VagasTransposicao();
                $vagas->ed350_ano = $ano;
                $vagas->ed350_escola = $escola->ed18_i_codigo;
                $vagas->ed350_etapa = $etapa->getCodigo();
                $vagas->save();
                $vagas->refresh();
                $vagas->escola;
            }

            $aVagasTransposicao[] = $vagas->toArray();
        }

        return new DBJsonResponse($aVagasTransposicao);
    }

    public function salvarVagas(Request $request)
    {
        $vagasSalvar = JSON::create()->parse(str_replace("\\", "", $request->get('vagas')));

        foreach ($vagasSalvar as $vagaSalvar) {
            $vaga = VagasTransposicao::firstOrNew(['ed350_codigo' => $vagaSalvar->ed350_codigo]);

            $vaga->ed350_vagas = $vagaSalvar->ed350_vagas;
            $vaga->ed350_saldo = $vagaSalvar->ed350_saldo;
            $vaga->ed350_vagaspcd = $vagaSalvar->ed350_vagaspcd;
            $vaga->ed350_saldopcd = $vagaSalvar->ed350_saldopcd;
            $vaga->save();
        }
        return new DBJsonResponse([], 'Vagas alteradas com sucesso.');
    }

    public function toggle(Request $request)
    {
        $transposicao = Transposicao::find($request->get('codigo'));
        $transposicao->ed353_ativa = !$transposicao->ed353_ativa;
        $transposicao->save();

        if ($transposicao->ed353_ativa) {
            return new DBJsonResponse($transposicao, 'Inscrições reabertas para transposição');
        }
        return new DBJsonResponse($transposicao, 'Inscrições encerradas para transposição');
    }

    public function relatorioGeralInscritos(Request $request)
    {
        $anoTransposicao = $request->get('anoDestino');
        $transposicaoService = new TransposicaoService();
        $inscritos = $transposicaoService->buscarInscritos($anoTransposicao);
        $relatorioCSV = new InscritosTransposicaoCSV($inscritos);
        $relatorio = $relatorioCSV->emitir();
        return new DBJsonResponse($relatorio);
    }

    public function processar(Request $request)
    {
        $transposicao = Transposicao::where('ed353_ano', $request->get('anoTransposicao'))->first();
        $processarTransposicaoService = new ProcessarTransposicaoService();
        $processarTransposicaoService->processarDesignacao($transposicao);

        return new DBJsonResponse([], "Transposição processada com sucesso!");
    }

    public function matriculasDesignados(Request $request)
    {
        $transposicao = Transposicao::findOrFail($request->get('transposicao'));
        $escola = Escola::findOrFail($request->get('DB_coddepto'));

        $designadosEscola = DesignadoTransposicao::with('aluno', 'escolaDestino', 'etapaDestino')
            ->where('ed354_transposicao', $transposicao->ed353_codigo)
            ->where('ed354_escoladestino', $escola->ed18_i_codigo)
            ->get();

        $designadosMatrculas = [];
        foreach ($designadosEscola as $designadoEscola) {
            $situacao = "Em andamento";
            $permiteMatricular = false;

            $aluno = $designadoEscola->aluno;
            $candidatoTransposicao = CandidatoTransposicao::where('ed349_aluno', $aluno->ed47_i_codigo)
                ->where('ed349_ano', $transposicao->ed353_ano)
                ->first();

            $matricula = MatriculaRepository::getMatriculaByCodigo($candidatoTransposicao->ed349_matricula);
            if ($matricula->isConcluida()) {
                $situacao = $matricula->getDiarioDeClasse()->getResultadoFinal() == 'A' ? 'Aprovado' : 'Reprovado';
                $permiteMatricular = $matricula->getDiarioDeClasse()->getResultadoFinal() == 'A';
            }

            $designadosMatrculas[] = (object)[
                'escola_origem' => $candidatoTransposicao->escolaOrigem->ed18_c_nome,
                'etapa_destino' => $designadoEscola->etapaDestino->ed11_c_descr,
                'codigo_aluno' => $designadoEscola->aluno->ed47_i_codigo,
                'aluno' => $designadoEscola->aluno->ed47_v_nome,
                'situacao' => $situacao,
                'permite_matricular' => $permiteMatricular
            ];
        }
        return new DBJsonResponse($designadosMatrculas);
    }

    public function alunoDesignado(Request $request)
    {
        if (empty($request->get('codigoAluno')) || !is_numeric($request->get('codigoAluno'))) {
            throw new Exception("Código do aluno não informado");
        }
        $transposicao = Transposicao::getTransposicaoAtual();
        $designado = DesignadoTransposicao::query()->where('ed354_transposicao', $transposicao->ed353_codigo)
            ->where('ed354_aluno', $request->get('codigoAluno'))
            ->first();
        if (is_null($designado)) {
            throw new Exception("Não encontrado designação para o aluno selecionado");
        }

        $candidatoTransposicao = CandidatoTransposicao::where('ed349_aluno', $designado->ed354_aluno)
            ->where('ed349_ano', $transposicao->ed353_ano)
            ->first();
        return new DBJsonResponse((object)[
            'anoTransposicao' => $designado->transposicao->ed353_ano,
            'escolaOrigem' => $candidatoTransposicao->escolaOrigem->ed18_c_nome,
            'etapaOrigem' => $candidatoTransposicao->etapaOrigem->ed11_c_descr,
            'escolaDesignacao' => $designado->escolaDestino->ed18_c_nome,
            'etapaDesignada' => $designado->etapaDestino->ed11_c_descr,
        ]);
    }

    public function cancelarDesignacao(Request $request)
    {
        if (empty($request->get('codigoAluno')) || !is_numeric($request->get('codigoAluno'))) {
            throw new Exception("Código do aluno não informado");
        }
        $transposicao = Transposicao::getTransposicaoAtual();
        $designado = DesignadoTransposicao::query()->where('ed354_transposicao', $transposicao->ed353_codigo)
            ->where('ed354_aluno', $request->get('codigoAluno'))
            ->first();
        if (is_null($designado)) {
            throw new Exception("Não encontrado designação para o aluno selecionado");
        }

        $designado->delete();
        return new DBJsonResponse([], "Designação cancelada com sucesso!");
    }
}
