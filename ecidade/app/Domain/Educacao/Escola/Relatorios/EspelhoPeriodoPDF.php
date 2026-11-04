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

namespace App\Domain\Educacao\Escola\Relatorios;

use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\AvaliacaoParcial;
use App\Domain\Educacao\Escola\Models\AvaliacoesParciais\ProcedimentoResultadoParcial;
use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Models\PeriodoCalendario;
use ArredondamentoNota;
use Disciplina;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use ECidade\Pdf\Pdf;
use Etapa;
use Exception;
use IElementoAvaliacao;
use Matricula;
use ResultadoAvaliacao;
use Turma;

class EspelhoPeriodoPDF extends Pdf
{
    /**
     * @var object
     */
    private $dados;

    /**
     * @var Turma
     */
    private $turma;

    /**
     * @var Etapa
     */
    private $etapa;

    /**
     * @var PeriodoCalendario
     */
    private $periodoCalendario;

    private $paginas = [];

    private $exibirNotas;

    private $umaPorPagina;

    private $tamanhos;

    private $recuperacaoNotasParciais;

    public function __construct($dados, $exibirNotas = true, $umaPorPagina = false, $recuperacaoNotasParciais = true)
    {
        parent::__construct('L');
        $this->tamanhos = (object)[
            'nomeAluno' => 65,
            'colunaAvaliacao' => 7,
            'colunaRecuperacao' => 7,
            'colunaObservacao' => 280,
            'mediaFinal' => 10,
            'alturaAvaliacao' => 20,
        ];
        $this->exibirNotas = $exibirNotas;
        $this->umaPorPagina = $umaPorPagina;
        $this->recuperacaoNotasParciais = $recuperacaoNotasParciais;
        if ($umaPorPagina) {
            $this->tamanhos->nomeAluno = 70;
            $this->tamanhos->colunaAvaliacao = 20;
            $this->tamanhos->colunaRecuperacao = 25;
            $this->tamanhos->mediaFinal = 22;
            $this->tamanhos->alturaAvaliacao = 4;
        }
        $this->dados = $dados;
        $this->turma = $dados->turma;
        $this->etapa = $dados->etapa;
        $this->periodoCalendario = PeriodoCalendario::query()
            ->where('ed53_i_calendario', $this->turma->getCalendario()->getCodigo())
            ->where('ed53_i_periodoavaliacao', $dados->periodo->ed09_i_codigo)
            ->first();
        $this->paginas = $this->separarDisciplinasPorPaginas($this->dados->disciplinas);
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(8, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFillColor(235);
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader(false);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/espelho_periodo_' . time() . '.pdf';
        $this->output('F', $fileName);
        return [
            "name" => "Relatório Espelho do Período",
            "path" => $fileName,
            "file" => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function emitirPdf()
    {
        $this->initPdf();

        foreach ($this->paginas as $disciplinasPagina) {
            $this->cabecalho();

            $this->setFont('arial', 'B', 8);
            $this->savePoint();
            $this->cell(280, 5, 'RELATÓRIO ESPELHO DO PERÍODO', 0, 0, 'L', 0);
            $this->rollbackToSavePoint();
            $dataInicio = date('d/m/Y', strtotime($this->periodoCalendario->ed53_d_inicio));
            $dataFim = date('d/m/Y', strtotime($this->periodoCalendario->ed53_d_fim));
            $this->cell(280, 5, "{$this->dados->periodo->ed09_c_descr} - {$dataInicio} à {$dataFim}", 0, 1, 'C', 0);

            $this->imprimirCabecalhoTabela($disciplinasPagina);
            foreach ($this->dados->alunos as $aluno) {
                $this->imprimirAluno($aluno, $disciplinasPagina);
            }

            while ($this->getAvailableHeight() > 15) {
                $this->imprimirLinhaEmBranco($disciplinasPagina);
            }
        }

        return $this->imprimir();
    }

    protected function cabecalhoDadosInstituicao()
    {
        $this->addPage();
        $escola = Escola::find($this->turma->getEscola()->getCodigo());

        $instituicao = $escola->getDepartamento()->getInstituicao();
        $imagem = $instituicao->getLogo();
        if (file_exists("imagens/files/{$imagem}")) {
            $this->Image("imagens/files/{$imagem}", 11, 9, 13);
        }
        $posicaoX = 25;
        $tamanhoLinha = 120;

        $this->SetFont('Arial', 'B', '7');
        $this->SetXY($posicaoX, 8);

        $nomeEscola = $escola->getNome();
        $referencia = $escola->getCodigoReferencia();
        if (!empty($referencia)) {
            $nomeEscola = "{$referencia} - {$nomeEscola}";
        }

        $this->Cell($tamanhoLinha, 4, $instituicao->getNome(), 0, 1, 'L');
        $this->SetX($posicaoX);
        $this->Cell($tamanhoLinha, 4, $nomeEscola, 0, 1, 'L');

        $this->SetFont('Arial', '', '7');
        $this->SetXY($posicaoX, 24);
        $this->Cell($tamanhoLinha, 4, "Cidade: {$escola->getMunicipio()->getNome()}", 0, 0, 'L');
    }

    protected function cabecalho()
    {
        $this->cabecalhoDadosInstituicao();
        $turma = $this->turma;
        $calendario = $turma->getCalendario();

        $posicaoX = 140;
        $tamanhoLinha = 90;

        $this->SetXY($posicaoX, 8);
        $this->Cell($tamanhoLinha, 4, "Curso: {$turma->getBaseCurricular()->getCurso()->getNome()}", 0, 0, "L");
        $this->Cell($tamanhoLinha, 4, "Turma: {$turma->getDescricao()}", 0, 1, "L");
        $this->setX($posicaoX);
        $this->Cell($tamanhoLinha, 4, "Calendário: {$calendario->getDescricao()}", 0, 0, "L");
        $this->Cell($tamanhoLinha, 4, "Etapa: {$this->etapa->getNome()}", 0, 1, "L");
        $this->setX($posicaoX);
        $this->Cell($tamanhoLinha, 4, "Período: {$this->dados->periodo->ed09_c_descr}", 0, 0, "L");
        $this->Cell($tamanhoLinha, 4, "Turno: {$turma->getTurno()->getDescricao()}", 0, 1, "L");
        $this->roundedrect(8, 8, 280, 20, 2, '', '1234');
        $this->SetXY(8, 30);
    }

    private function imprimirCabecalhoTabela($disciplinas)
    {
        $this->setFont('arial', 'B', 8);

        $this->tamanhos->colunaObservacao = 280 - 5 - $this->tamanhos->nomeAluno;
        $this->Cell($this->tamanhos->nomeAluno, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C'); // retangulo vazio
        $this->Cell(5, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C'); // retangulo vazio numero
        foreach ($disciplinas as $disciplina) {
            $this->imprimirCabecalhoDisciplina($disciplina);
        }

        // FIM Cabeçalho Disciplinas - Quebra de Linha
        $labelObservacoes = '';
        if ($this->umaPorPagina) {
            $this->setXY($this->getX() - 10, $this->getY() + $this->tamanhos->alturaAvaliacao);
            $labelObservacoes = 'OBSERVAÇÕES';
        }
        $this->Cell($this->tamanhos->colunaObservacao, ($this->tamanhos->alturaAvaliacao + 4), $labelObservacoes, 1);

        $this->setXY(8, $this->getY() + $this->tamanhos->alturaAvaliacao);
        $this->Cell($this->tamanhos->nomeAluno, 4, 'Nome do Aluno', 1, 0);

        $this->Cell(5, 4, 'Nº', 1, 1);
    }

    private function imprimirCabecalhoDisciplina($disciplina)
    {
        // Titulo Disciplina
        $this->setFont('arial', 'B', 8);
        $this->savePoint();
        $largura = $this->tamanhos->colunaAvaliacao * (count($disciplina->avaliacoes) + 2)
            + $this->tamanhos->colunaRecuperacao;
        $this->tamanhos->colunaObservacao -= $largura;
        $this->tamanhos->colunaObservacao -= $this->tamanhos->mediaFinal;

        $regencia = $disciplina->regencia;
        $descricaoDisciplina = $regencia->getDisciplina()->getNomeDisciplina();
        if (strlen($descricaoDisciplina) > 25 && !$this->umaPorPagina) {
            $descricaoDisciplina = $regencia->getDisciplina()->getAbreviatura();
        }

        $this->cellAdapt(8, $largura, 4, $descricaoDisciplina, 1, 0, 'C');

        if (!$this->umaPorPagina) {
            $this->Cell(10, 24, '', 1, 1, 'C', 1);
        }

        $this->rollbackToSavePoint();
        $this->setXY($this->getX(), $this->getY() + 4);

        // Avaliações
        $this->setFont('arial', 'B', 7);
        foreach ($disciplina->avaliacoes as $avaliacao) {
            if ($this->umaPorPagina) {
                $this->Cell(
                    $this->tamanhos->colunaAvaliacao,
                    $this->tamanhos->alturaAvaliacao,
                    "AVALIAÇÃO {$avaliacao->ed340_ordem}",
                    1
                );
                continue;
            }
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, '', 1);
            $this->savePoint();
            $this->setXY($this->getX() - 2, $this->getY() + 19);
            $this->setFillColor(0);
            $this->TextWithRotation($this->getX(), $this->getY(), "AVALIAÇÃO {$avaliacao->ed340_ordem}", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        // Média Bimestral
        if ($this->umaPorPagina) {
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, 'RESULTADO', 1, 0, 'C', 1);
        } else {
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C', 1);
            $this->savePoint();
            $this->setXY($this->getX() - 2, $this->getY() + 19);
            $this->setFillColor(0);
            $this->TextWithRotation($this->getX(), $this->getY(), "RESULTADO", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        // Recuperação
        if ($this->umaPorPagina) {
            $this->Cell($this->tamanhos->colunaRecuperacao, $this->tamanhos->alturaAvaliacao, 'RECUPERAÇÃO', 1);
        } else {
            $this->setFont('arial', 'B', 6);
            $this->Cell($this->tamanhos->colunaRecuperacao, $this->tamanhos->alturaAvaliacao, '', 1);
            $this->savePoint();
            $this->setXY($this->getX() - 2, $this->getY() + 19);
            $this->setFillColor(0);
            $this->TextWithRotation($this->getX(), $this->getY(), "RECUPERAÇÃO", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        // Faltas
        if ($this->umaPorPagina) {
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, 'Faltas', 1);
        } else {
            $this->setFont('arial', 'B', 7);
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, '', 1);
            $this->savePoint();
            $this->setXY($this->getX() - 2, $this->getY() + 19);
            $this->setFillColor(0);
            $this->TextWithRotation($this->getX(), $this->getY(), "FALTAS", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        if ($this->umaPorPagina) {
            $this->setXY($this->getX(), $this->getY() - 4);
            $this->Cell(
                $this->tamanhos->mediaFinal,
                ($this->tamanhos->alturaAvaliacao * 2),
                "RESULT. FINAL",
                1,
                0,
                '',
                1
            );
        } else {
            $this->savePoint();
            $this->setXY($this->getX() + 6, $this->getY() + 19);
            $this->setFillColor(0);
            $this->TextWithRotation($this->getX(), $this->getY(), "RESULT. FINAL", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        // FIM Avaliações - Quebra de Linha
        $this->setXY($this->getX() + 10, $this->getY() - 4);
    }

    /**
     * @param $aluno
     * @param $disciplinas
     * @return void
     * @throws Exception
     */
    private function imprimirAluno($aluno, $disciplinas)
    {
        $matricula = $aluno->matricula;
        $this->setFont('arial', '', 8);
        $situacaoAluno = new SituacaoMatriculaEnum($matricula->getSituacao());
        $situacaoImprimir = '';
        if ($situacaoAluno->getValue() != SituacaoMatriculaEnum::MATRICULADO) {
            $situacaoImprimir = " ({$situacaoAluno->sigla()})";
        }
        $this->cellAdapt(8, $this->tamanhos->nomeAluno, 4, $matricula->getAluno()->getNome() . $situacaoImprimir, 1);
        $this->Cell(5, 4, $matricula->getNumeroOrdemAluno(), 1, 0, 'C');

        foreach ($disciplinas as $disciplina) {
            foreach ($disciplina->avaliacoes as $avaliacao) {
                $this->imprimirAproveitamento($avaliacao, $aluno);
            }
            $this->imprimirResultado($disciplina->resultadoParcial, $aluno);

            if ($this->recuperacaoNotasParciais) {
                $this->imprimirRecuperacaoNotasParciais($disciplina->resultadoParcial, $aluno);
            } else {
                $this->Cell($this->tamanhos->colunaRecuperacao, 4, ' - ', 1, 0, 'C', 0);
            }

            $this->imprimirFaltas($disciplina->resultadoParcial, $aluno);
            $this->imprimirResultadoFinal($disciplina->resultadoParcial, $aluno);

            if ($this->umaPorPagina) {
                $this->imprimirObservacao($disciplina->resultadoParcial, $aluno);
            }
        }

        if (!$this->umaPorPagina) {
            $this->Cell($this->tamanhos->colunaObservacao, 4, '', 1);
        }

        $this->setXY(8, $this->getY() + 4);
    }

    private function imprimirAproveitamento(AvaliacaoParcial $avaliacaoParcial, $aluno)
    {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C');
            return;
        }

        foreach ($aluno->aproveitamentos as $aproveitamento) {
            if ($avaliacaoParcial->ed340_codigo == $aproveitamento->ed341_procavaliacaoparcial) {
                $this->Cell($this->tamanhos->colunaAvaliacao, 4, $aproveitamento->ed341_valornota, 1, 0, 'C');
                break;
            }
        }
    }

    /**
     * @param $disciplinas
     * @return array
     */
    private function separarDisciplinasPorPaginas($disciplinas)
    {
        $paginas = [];
        $pagina = 0;
        $paginas[$pagina] = [];
        $largura = 78;
        foreach ($disciplinas as $disciplina) {
            if ($this->umaPorPagina) {
                if (count($paginas[$pagina]) > 0) {
                    $pagina++;
                    $paginas[$pagina] = [];
                }
                $paginas[$pagina][] = $disciplina;
                continue;
            }
            $larguraDisciplina = ($this->tamanhos->colunaAvaliacao * (count($disciplina->avaliacoes) + 3))
                + $this->tamanhos->mediaFinal;
            $largura += $larguraDisciplina;
            if ($largura > 288) {
                $pagina++;
                $paginas[$pagina] = [];
                $largura = 78 + $larguraDisciplina;
            }
            $paginas[$pagina][] = $disciplina;
        }

        return $paginas;
    }

    private function imprimirResultado(ProcedimentoResultadoParcial $procedimentoResultadoParcial, $aluno)
    {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C', 1);
            return;
        }

        foreach ($aluno->resultados as $resultado) {
            if ($procedimentoResultadoParcial->ed342_codigo == $resultado->ed343_procresultadoparcial) {
                $this->Cell($this->tamanhos->colunaAvaliacao, 4, $resultado->ed343_valornota, 1, 0, 'C', 1);
            }
        }
    }

    private function imprimirRecuperacaoNotasParciais(
        ProcedimentoResultadoParcial $procedimentoResultadoParcial,
        $aluno
    ) {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->colunaRecuperacao, 4, '', 1, 0, 'C', 0);
            return;
        }

        foreach ($aluno->resultados as $resultado) {
            if ($procedimentoResultadoParcial->ed342_codigo == $resultado->ed343_procresultadoparcial) {
                $this->Cell($this->tamanhos->colunaRecuperacao, 4, $resultado->ed343_recuperacaonota, 1, 0, 'C', 0);
            }
        }
    }

    private function imprimirObservacao(ProcedimentoResultadoParcial $procedimentoResultadoParcial, $aluno)
    {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->colunaObservacao, 4, '', 1, 0, 'L', 0);
            return;
        }

        foreach ($aluno->resultados as $resultado) {
            if ($procedimentoResultadoParcial->ed342_codigo == $resultado->ed343_procresultadoparcial) {
                $this->Cell($this->tamanhos->colunaObservacao, 4, $resultado->ed343_observacao, 1, 0, 'L', 0);
            }
        }
    }

    /**
     * @param ProcedimentoResultadoParcial $procedimentoResultadoParcial
     * @param $aluno
     * @return void
     * @throws Exception
     */
    private function imprimirResultadoFinal(ProcedimentoResultadoParcial $procedimentoResultadoParcial, $aluno)
    {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->mediaFinal, 4, '', 1, 0, 'C', 1);
            return;
        }

        /** @var \Matricula $matricula */
        $matricula = $aluno->matricula;

        if ($matricula->isAvaliadoPorParecer()) {
            $this->Cell($this->tamanhos->mediaFinal, 4, 'PD', 1, 0, 'C', 1);
            return;
        }

        $regencia = \RegenciaRepository::getRegenciaByCodigo($procedimentoResultadoParcial->ed342_regencia);
        $periodoAvaliacao = $procedimentoResultadoParcial->procedimentoAvaliacao->periodoAvaliacao;
        $diarioClasse = $matricula->getDiarioDeClasse();
        $disciplina = $diarioClasse->getDisciplinasPorRegencia($regencia);
        $aproveitamentoDiario = $disciplina->getAvaliacoesPorOrdemSequencial($periodoAvaliacao->ed09_i_sequencia);

        $anoCalendario = $diarioClasse->getTurma()->getCalendario()->getAnoExecucao();
        $valor = ArredondamentoNota::formatar(
            $aproveitamentoDiario->getValorAproveitamento()->getAproveitamento(),
            $anoCalendario
        );
        $isParecer = $aproveitamentoDiario->getElementoAvaliacao()->getFormaDeAvaliacao()->getTipo() === 'PARECER';
        $this->Cell($this->tamanhos->mediaFinal, 4, $isParecer ? 'PD' : $valor, 1, 0, 'C', 1);
    }

    /**
     * @throws Exception
     */
    private function imprimirFaltas(ProcedimentoResultadoParcial $procedimentoResultadoParcial, $aluno)
    {
        if (!$this->exibirNotas) {
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C');
            return;
        }

        /** @var \Matricula $matricula */
        $matricula = $aluno->matricula;

        $regencia = \RegenciaRepository::getRegenciaByCodigo($procedimentoResultadoParcial->ed342_regencia);
        $periodoAvaliacao = $procedimentoResultadoParcial->procedimentoAvaliacao->periodoAvaliacao;
        $diarioClasse = $matricula->getDiarioDeClasse();
        $disciplina = $diarioClasse->getDisciplinasPorRegencia($regencia);
        $aproveitamentoDiario = $disciplina->getAvaliacoesPorOrdem($periodoAvaliacao->ed09_i_codigo);
        $totalFaltas  = $aproveitamentoDiario == null ? 0 : $aproveitamentoDiario->getTotalFaltas();
        $this->Cell($this->tamanhos->colunaAvaliacao, 4, $totalFaltas, 1, 0, 'C');
    }

    private function imprimirLinhaEmBranco($disciplinas)
    {
        $this->setFont('arial', '', 8);
        $this->cellAdapt(8, $this->tamanhos->nomeAluno, 4, '', 1);
        $this->Cell(5, 4, '', 1, 0, 'C');

        foreach ($disciplinas as $disciplina) {
            foreach ($disciplina->avaliacoes as $avaliacao) {
                $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C');
            }
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C', 1); // Resultado
            $this->Cell($this->tamanhos->colunaRecuperacao, 4, '', 1, 0, 'C', 0); // Recuperação

            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C'); //Faltas
            $this->Cell($this->tamanhos->mediaFinal, 4, '', 1, 0, 'C', 1); // Media Final
        }

        $this->Cell($this->tamanhos->colunaObservacao, 4, '', 1);
        $this->setXY(8, $this->getY() + 4);
    }
}
