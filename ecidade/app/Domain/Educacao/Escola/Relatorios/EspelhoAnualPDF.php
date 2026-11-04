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

use App\Domain\Educacao\Escola\Models\Escola;
use App\Domain\Educacao\Escola\Services\EspelhoAnualService;
use ArredondamentoFrequencia;
use ArredondamentoNota;
use Disciplina;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use ECidade\Pdf\Pdf;
use Etapa;
use Exception;
use IElementoAvaliacao;
use Matricula;
use Regencia;
use Turma;

class EspelhoAnualPDF extends Pdf
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

    private $paginas = [];

    private $tamanhos;

    public function __construct($dados)
    {
        parent::__construct('L');
        $this->tamanhos = (object)[
            'nomeAluno' => 65,
            'numeroAluno' => 5,
            'colunaAvaliacao' => 5,
            'colunaFrequencia' => 8,
            'colunaResultadoFinal' => 8,
            'alturaAvaliacao' => 20,

        ];

        $this->dados = $dados;
        $this->turma = $dados->turma;
        $this->etapa = $dados->etapa;
        $this->paginas = $this->separarDisciplinasPorPaginas($this->dados->disciplinas);

        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
            $this->tamanhos->nomeAluno = 80;
            $this->tamanhos->colunaAvaliacao = 12;
            $this->tamanhos->colunaFrequencia = 23;
            $this->tamanhos->colunaResultadoFinal = 25;
            $this->tamanhos->alturaAvaliacao = 4;
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
            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                if (count((array)$paginas[$pagina]) > 0) {
                    $pagina++;
                    $paginas[$pagina] = [];
                }
                $paginas[$pagina][] = $disciplina;
                continue;
            }
            $larguraDisciplina = ($this->tamanhos->colunaAvaliacao
                * (count((array)$disciplina->elementosAvaliacoes) - 1))
                + $this->tamanhos->colunaAvaliacao;
            $largura += $larguraDisciplina;
            if ($largura > 288) {
                $pagina++;
                $paginas[$pagina] = [];
                $largura = 78 + $larguraDisciplina;
            }
            $paginas[$pagina][] = $disciplina;
        }
        if ($largura > 247) {
            $pagina++;
            $paginas[$pagina] = [];
        }

        return $paginas;
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
        $fileName = 'tmp/espelho_anual_' . time() . '.pdf';
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

        foreach ($this->paginas as $key => $disciplinasPagina) {
            $ultimaPagina = false;
            if ($key == count((array)$this->paginas) - 1) {
                $ultimaPagina = true;
            }

            $this->cabecalho();

            $this->setFont('arial', 'B', 8);
            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                $this->cell(280, 5, "ESPELHO ANUAL - POR DISCIPLINA", 0, 1, 'C', 0);
            } else {
                $this->cell(280, 5, "ESPELHO ANUAL - GERAL", 0, 1, 'C', 0);
            }

            $this->imprimirCabecalhoTabela($disciplinasPagina, $ultimaPagina);
            foreach ($this->dados->matriculas as $matricula) {
                $this->imprimirAluno($matricula, $disciplinasPagina, $ultimaPagina);
            }

            $margemInferior = $this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA ? 20 : 15;
            while ($this->getAvailableHeight() > $margemInferior) {
                $this->imprimirLinhaEmBranco($disciplinasPagina, $ultimaPagina);
            }

            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                $nomeProfessor = 'PROFESSOR(A) RESPONSÁVEL';
                /**
                 * @var $regencia Regencia
                 */
                $regencia = $disciplinasPagina[0]->regencia;
                if (count((array)$regencia->getDocentes()) > 0) {
                    $docentes = $regencia->getDocentes();
                    $docente = end($docentes);
                    $nomeProfessor = $docente->getNome();
                }
                $this->Cell(280, 5, "", 0, 1, 'C', 0);
                $this->Cell(280, 5, "________________________________________", 0, 1, 'C', 0);
                $this->Cell(280, 5, $nomeProfessor, 0, 1, 'C', 0);
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
        $this->Cell($tamanhoLinha, 4, "Turno: {$turma->getTurno()->getDescricao()}", 0, 1, "L");
        $this->roundedrect(8, 8, 280, 20, 2, '', '1234');
        $this->SetXY(8, 30);
    }

    private function imprimirCabecalhoTabela($disciplinas, $ultimaPagina = false)
    {
        $this->setFont('arial', 'B', 8);

        $this->Cell($this->tamanhos->nomeAluno, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C'); // retangulo vazio
        $this->Cell(5, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C'); // retangulo vazio numero

        if (count((array)$disciplinas) > 0) {
            foreach ($disciplinas as $disciplina) {
                $this->imprimirCabecalhoDisciplina($disciplina);
            }
        }

        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA || $ultimaPagina) {
            $this->imprimirCabecalhoQuadroFaltas($this->dados->elementos);
            $this->imprimirCabecalhoResultadoFinal();
        }

        // FIM Cabeçalho Disciplinas - Quebra de Linha
        $this->setXY(8, $this->getY() + $this->tamanhos->alturaAvaliacao);
        $this->Cell($this->tamanhos->nomeAluno, 4, 'Nome do Aluno', 1, 0);

        $this->Cell(5, 4, 'Nº', 1, 1);
    }

    private function imprimirCabecalhoDisciplina($disciplina)
    {
        // Titulo Disciplina
        $this->setFont('arial', 'B', 8);
        $this->savePoint();
        $largura = $this->tamanhos->colunaAvaliacao * (count((array)$disciplina->elementosAvaliacoes) - 1);

        $regencia = $disciplina->regencia;
        $descricaoDisciplina = $regencia->getDisciplina()->getNomeDisciplina();
        if (strlen($descricaoDisciplina) > 25) {
            $descricaoDisciplina = $regencia->getDisciplina()->getAbreviatura();
        }

        $this->cellAdapt(8, $largura, 4, $descricaoDisciplina, 1, 0, 'C');

        $this->rollbackToSavePoint();
        $this->setXY($this->getX(), $this->getY() + 4);

        // Avaliações
        $this->setFont('arial', 'B', 7);
        foreach ($disciplina->elementosAvaliacoes as $key => $elementoAvaliacao) {
            /**
             * @var \AvaliacaoPeriodica|\ResultadoAvaliacao $elementoAvaliacao
             */
            $fill = $elementoAvaliacao instanceof \ResultadoAvaliacao;
            $ultimoElemento = $key == count((array)$disciplina->elementosAvaliacoes);
            $test = 0;
            if ($ultimoElemento) {
                $test = 4;
                $this->setXY($this->getX(), $this->getY() - $test);
            }

            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                $this->Cell(
                    $this->tamanhos->colunaAvaliacao,
                    $this->tamanhos->alturaAvaliacao + $test,
                    $elementoAvaliacao->getDescricaoAbreviada(),
                    1,
                    0,
                    'C',
                    $fill
                );
                $this->setXY($this->getX(), $this->getY() + $test);
            } else {
                $this->Cell(
                    $this->tamanhos->colunaAvaliacao,
                    $this->tamanhos->alturaAvaliacao + $test,
                    '',
                    1,
                    0,
                    'C',
                    $fill
                );
                $this->setXY($this->getX(), $this->getY() + $test);

                $this->savePoint();
                $this->setXY($this->getX() - 2, $this->getY() + 19);

                $this->setFillColor(0);

                $this->setFont('arial', 'B', 7);
                $content = "{$elementoAvaliacao->getDescricao()}  ";
                $tamanhoString = $this->GetStringWidth($content);

                $content = trim($content);
                if ($tamanhoString > $this->tamanhos->alturaAvaliacao) {
                    // Deixa a fonte EXATAMENTE no tamanho para caber na célula
                    $tamanhoFonte = 7 * $this->tamanhos->alturaAvaliacao / $tamanhoString;
                    $this->SetFontSize($tamanhoFonte);
                }
                $this->TextWithRotation($this->getX(), $this->getY(), $content, 90);

                $this->setFillColor(235);
                $this->rollbackToSavePoint();
            }
        }
        // FIM Avaliações - Quebra de Linha
        $this->setXY($this->getX(), $this->getY() - 4);
    }

    /**
     * @param Matricula $matricula
     * @param $disciplinas
     * @param bool $ultimaPagina
     * @return void
     * @throws Exception
     */
    private function imprimirAluno(Matricula $matricula, $disciplinas, $ultimaPagina = false)
    {
        $this->setFont('arial', '', 7);
        $nee = '';
        if (count((array)$matricula->getAluno()->getNecessidadesEspeciais()) > 0) {
            $nee = ' (NEE)';
        }
        $this->cellAdapt(7, $this->tamanhos->nomeAluno, 4, $matricula->getAluno()->getNome() . $nee, 1);
        $this->Cell(5, 4, $matricula->getNumeroOrdemAluno(), 1, 0, 'C');

        $situacaoAluno = new SituacaoMatriculaEnum($matricula->getSituacao());
        $naoMatriculado = $situacaoAluno->getValue() != SituacaoMatriculaEnum::MATRICULADO;
        if ($naoMatriculado) {
            $elementoAvaliacaoDisciplina = isset($disciplinas[0]->elementosAvaliacoes) ?
                $disciplinas[0]->elementosAvaliacoes : 0;
            $larguraDisciplina = count((array)$elementoAvaliacaoDisciplina) * $this->tamanhos->colunaAvaliacao;
            $larguraFaltas = 0;

            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                $larguraFaltas = (4 * $this->tamanhos->colunaAvaliacao) + $this->tamanhos->colunaResultadoFinal;
            } elseif ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL && $ultimaPagina) {
                $larguraFaltas = 3 + (5 * $this->tamanhos->colunaAvaliacao) + $this->tamanhos->colunaResultadoFinal;
            }

            $larguraNotas = (count((array)$disciplinas) * $larguraDisciplina) + $larguraFaltas;
            $this->cell($larguraNotas, 4, $situacaoAluno->getValue(), 1, 0, 'C');
        } else {
            foreach ($disciplinas as $disciplina) {
                foreach ($disciplina->elementosAvaliacoes as $elementoAvaliacao) {
                    $fill = $elementoAvaliacao instanceof \ResultadoAvaliacao;
                    $regencia = $disciplina->regencia;
                    $this->imprimirNotas($regencia->getDisciplina(), $elementoAvaliacao, $matricula, $fill);
                }
            }
        }

        if ($situacaoAluno->getValue() == SituacaoMatriculaEnum::MATRICULADO
            && (EspelhoAnualService::MODELO_GERAL && $ultimaPagina
                || $this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA)
        ) {
            $this->imprimirQuadroFaltasAluno($matricula, $disciplinas);
            $this->imprimirResultadoFinalAluno($matricula);
        }

        $this->setXY(8, $this->getY() + 4);
    }

    private function imprimirNotas(
        Disciplina $disciplina,
        IElementoAvaliacao $elementoAvaliacao,
        Matricula $matricula,
        $fill
    ) {
        $this->setFont('arial', '', 7);
        $avaliacao = $matricula->getDiarioDeClasse()
            ->getAvaliacoesPorDisciplina($disciplina, $elementoAvaliacao);
        $isParecer = $matricula->isAvaliadoPorParecer()
            || $avaliacao->getElementoAvaliacao()->getFormaDeAvaliacao()->getTipo() == 'PARECER';
        $nota = $avaliacao->getValorAproveitamento()->getAproveitamento();
        if ($isParecer && !empty($nota)) {
            $this->setFont('arial', 'B', 7);
            $nota = 'PD';
        }
        if (!empty($nota) && $nota != 'PD') {
            $anoCalendario = $matricula->getTurma()->getCalendario()->getAnoExecucao();
            $nota = ArredondamentoNota::formatar($nota, $anoCalendario);
        }
        $this->Cell($this->tamanhos->colunaAvaliacao, 4, $nota, 1, 0, 'C', $fill);
    }

    private function imprimirLinhaEmBranco($disciplinas, $ultimaPagina)
    {
        $this->setFont('arial', '', 8);
        $this->cellAdapt(8, $this->tamanhos->nomeAluno, 4, '', 1);
        $this->Cell(5, 4, '', 1, 0, 'C');

        foreach ($disciplinas as $disciplina) {
            foreach ($disciplina->elementosAvaliacoes as $elementoAvaliacao) {
                $fill = $elementoAvaliacao instanceof \ResultadoAvaliacao;
                $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1, 0, 'C', $fill);
            }
        }

        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA || $ultimaPagina) {
            if (isset($disciplinas[0]->elementosAvaliacoes)) {
                foreach ($disciplinas[0]->elementosAvaliacoes as $elementoAvaliacao) {
                    if ($elementoAvaliacao->isResultado()) {
                        continue;
                    }
                    $isRecuperacao = !is_null($elementoAvaliacao->getElementoAvaliacaoVinculado());
                    if ($isRecuperacao) {
                        continue;
                    }
                    $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1);
                }
            }
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, '', 1);
            if ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL) {
                $this->Cell($this->tamanhos->colunaFrequencia, 4, '', 1, 0, 'C', 1);
            }
            $this->Cell($this->tamanhos->colunaResultadoFinal, 4, '', 1, 0, 'C', 1);
        }
        $this->setXY(8, $this->getY() + 4);
    }

    /**
     * @param Matricula $matricula
     * @param $disciplinas
     * @return void
     * @throws Exception
     */
    private function imprimirQuadroFaltasAluno(Matricula $matricula, $disciplinas)
    {
        $this->setFont('arial', '', 7);

        $diarioClasse = $matricula->getDiarioDeClasse();
        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
            $disciplinas = [$diarioClasse->getDisciplinasPorRegencia($disciplinas[0]->regencia)];
        } else {
            $disciplinas = $diarioClasse->getDisciplinas();
        }

        $totalFaltasPeriodos = [];
        foreach ($disciplinas as $disciplina) {
            foreach ($disciplina->getAvaliacoes() as $avaliacao) {
                if ($avaliacao->isResultado()) {
                    continue;
                }
                $isRecuperacao = !is_null($avaliacao->getElementoAvaliacao()->getElementoAvaliacaoVinculado());
                if ($isRecuperacao) {
                    continue;
                }

                if (!array_key_exists($avaliacao->getOrdemSequencia(), $totalFaltasPeriodos)) {
                    $totalFaltasPeriodos[$avaliacao->getOrdemSequencia()] = 0;
                }

                $totalFaltasPeriodos[$avaliacao->getOrdemSequencia()] += $avaliacao->getTotalFaltas();
            }
        }

        foreach ($totalFaltasPeriodos as $totalFaltas) {
            $quantidade = $totalFaltas > 0 ? $totalFaltas : '-';
            $this->Cell($this->tamanhos->colunaAvaliacao, 4, $quantidade, 1, 0, 'C', 0);
        }
        $totalGeral = array_reduce($totalFaltasPeriodos, function ($carry, $item) {
            return $carry + $item;
        }, 0);
        $this->Cell($this->tamanhos->colunaAvaliacao, 4, $totalGeral, 1, 0, 'C');

        if ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL) {
            $this->Cell(
                $this->tamanhos->colunaFrequencia,
                4,
                $this->calcularPercentualFrequenciaAluno($diarioClasse) . '%',
                1,
                0,
                'C',
                1
            );
        }
    }

    public function calcularPercentualFrequenciaAluno($diarioClasse)
    {
        $totalFaltas         = 0;
        $totalFaltasAbonadas = 0;
        $totalAulasDadas     = 0;
        foreach ($diarioClasse->getDisciplinas() as $disciplinaSeparada) {
            $totalFaltas         += (int)$disciplinaSeparada->getTotalFaltas();
            $totalFaltasAbonadas += (int)$disciplinaSeparada->getTotalFaltasAbonadas();
            $totalAulasDadas     += (int)$disciplinaSeparada->getTotalDeAulasParaCalculo();
        }

        $totalFaltasAluno = $totalFaltas - $totalFaltasAbonadas;

        if ($diarioClasse->getProcedimentoDeAvaliacao()->getFormaCalculoFrequencia() == 1) {
            $nPercentualFaltasNaoArredondado = ($totalFaltasAluno * 100) / $totalAulasDadas;
            $nPercentualFrequencia = ArredondamentoFrequencia::formatar(
                100 - $nPercentualFaltasNaoArredondado,
                $diarioClasse->getTurma()->getCalendario()->getAnoExecucao()
            );
        } else {
            $totalAulasPresentes = $totalAulasDadas - $totalFaltasAluno;
            if ($totalAulasDadas > 0) {
                $porcentualGlobal = ($totalAulasPresentes * 100) / $totalAulasDadas;
            }
            $nPercentualFrequencia = ArredondamentoFrequencia::arredondar(
                $porcentualGlobal,
                $diarioClasse->getTurma()->getCalendario()->getAnoExecucao()
            );
        }

        return $nPercentualFrequencia;
    }

    private function imprimirCabecalhoQuadroFaltas($elementos)
    {
        /**
         * @var $avaliacao \AvaliacaoPeriodica|\ResultadoAvaliacao
         */
        $totalElementos = 0;
        foreach ($elementos as $avaliacao) {
            if ($avaliacao->isResultado()) {
                continue;
            }
            $isRecuperacao = !is_null($avaliacao->getElementoAvaliacaoVinculado());
            if ($isRecuperacao) {
                continue;
            }
            $totalElementos++;
        }

        $largura = $this->tamanhos->colunaAvaliacao * ($totalElementos + 1);
        $this->savePoint();
        $this->cellAdapt(8, $largura, 4, 'FALTAS', 1, 0, 'C');
        $this->rollbackToSavePoint();
        $this->setXY($this->getX(), $this->getY() + 4);

        foreach ($elementos as $avaliacao) {
            if ($avaliacao->isResultado()) {
                continue;
            }
            $isRecuperacao = !is_null($avaliacao->getElementoAvaliacaoVinculado());
            if ($isRecuperacao) {
                continue;
            }

            if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
                $this->setFont('arial', 'B', 7);
                $this->Cell(
                    $this->tamanhos->colunaAvaliacao,
                    $this->tamanhos->alturaAvaliacao,
                    $avaliacao->getDescricaoAbreviada(),
                    1,
                    0,
                    'C'
                );
            }

            if ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL) {
                $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, '', 1);
                $this->savePoint();
                $this->setXY($this->getX() - 2, $this->getY() + 19);

                $this->setFillColor(0);
                $this->setFont('arial', 'B', 7);
                $content = "{$avaliacao->getDescricao()}  ";
                $tamanhoString = $this->GetStringWidth($content);

                $content = trim($content);
                if ($tamanhoString > $this->tamanhos->alturaAvaliacao) {
                    // Deixa a fonte EXATAMENTE no tamanho para caber na célula
                    $tamanhoFonte = 7 * $this->tamanhos->alturaAvaliacao / $tamanhoString;

                    $this->SetFontSize($tamanhoFonte);
                }

                $this->TextWithRotation($this->getX(), $this->getY(), $content, 90);
                $this->rollbackToSavePoint();
                $this->setFillColor(235);
            }
        }

        // Total de faltas
        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
            $this->savePoint();
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, 'TOTAL', 1, 0, 'C', 0);
            $this->rollbackToSavePoint();
        } else {
            $this->savePoint();
            $this->Cell($this->tamanhos->colunaAvaliacao, $this->tamanhos->alturaAvaliacao, '', 1, 0, 'C', 0);
            $this->rollbackToSavePoint();

            $this->savePoint();
            $this->setXY($this->getX() + 3, $this->getY() + 19);
            $this->setFillColor(0);
            $this->setFont('arial', 'B', 5.5);
            $this->TextWithRotation($this->getX(), $this->getY(), "TOTAL DE FALTAS", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }
        $this->setXY($this->getX() + $this->tamanhos->colunaAvaliacao, $this->getY() - 4);

        if ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL) {
            // Percentual de frequencia
            $this->savePoint();
            $this->Cell($this->tamanhos->colunaFrequencia, $this->tamanhos->alturaAvaliacao + 4, '', 1, 0, 'C', 1);
            $this->rollbackToSavePoint();

            $this->savePoint();
            $this->setXY($this->getX() + 4, $this->getY() + 23);
            $this->setFillColor(0);
            $this->setFont('arial', 'B', 6);
            $this->TextWithRotation($this->getX(), $this->getY(), "% DE FREQUENCIA", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();

            $this->setXY($this->getX() + $this->tamanhos->colunaFrequencia, $this->getY());
        }
    }

    private function imprimirCabecalhoResultadoFinal()
    {
        $this->savePoint();
        $this->Cell($this->tamanhos->colunaResultadoFinal, $this->tamanhos->alturaAvaliacao + 4, '', 1, 0, 'C', 1);
        $this->rollbackToSavePoint();

        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA) {
            $this->Cell(
                $this->tamanhos->colunaResultadoFinal,
                $this->tamanhos->alturaAvaliacao + 4,
                'RESULTADO',
                1,
                0,
                'C',
                1
            );
        } else {
            $this->savePoint();
            $this->setXY($this->getX() + 5, $this->getY() + 22);
            $this->setFillColor(0);
            $this->setFont('arial', 'B', 7);
            $this->TextWithRotation($this->getX(), $this->getY(), "RESULTADO", 90);
            $this->setFillColor(235);
            $this->rollbackToSavePoint();
        }

        $this->setXY($this->getX() + $this->tamanhos->colunaResultadoFinal, $this->getY());
    }

    /**
     * @param Matricula $matricula
     * @return void
     * @throws Exception
     */
    private function imprimirResultadoFinalAluno(Matricula $matricula)
    {
        $diarioClasse = $matricula->getDiarioDeClasse();
        $ensino = $matricula->getEtapaDeOrigem()->getEnsino();
        $resultadoFinal = '-';
        $disciplina = $diarioClasse->getDisciplinasPorRegencia($this->dados->disciplinas[0]->regencia);
        if ($this->dados->modelo == EspelhoAnualService::MODELO_POR_DISCIPLINA &&
            !empty($disciplina->getResultadoFinal()->getResultadoFinal())
        ) {
            $termoFinal = \DBEducacaoTermo::getTermoEncerramento(
                $ensino->getCodigo(),
                $disciplina->getResultadoFinal()->getResultadoFinal()
            );
            $resultadoFinal = $termoFinal[0]->sAbreviatura;
        } elseif ($this->dados->modelo == EspelhoAnualService::MODELO_GERAL
            && !empty($diarioClasse->getResultadoFinal())
        ) {
            $resultadoFinal = $diarioClasse->getResultadoFinal();
            if ($diarioClasse->aprovadoComProgressaoParcial()) {
                $resultadoFinal = 'A';
            }
            $termoFinal = \DBEducacaoTermo::getTermoEncerramento($ensino->getCodigo(), $resultadoFinal);
            $resultadoFinal = $termoFinal[0]->sAbreviatura;
        }
        $this->Cell($this->tamanhos->colunaResultadoFinal, 4, $resultadoFinal, 1, 0, 'C', 1);
    }
}
