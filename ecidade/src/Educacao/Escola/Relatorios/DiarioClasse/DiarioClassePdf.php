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

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Educacao\Escola\Requests\EmissaoDiarioClasseEscolarizacaoRequest;
use App\Domain\Educacao\Escola\Requests\EmissaoDiarioClasseEspecialRequest;
use ArredondamentoNota;
use AvaliacaoAproveitamento;
use CalendarioRepository;
use Carbon\Carbon;
use DBDate;
use DisciplinaRepository;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\AlunoDiarioClasse;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\DadosDiarioClasse;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model\TurmaDiarioClasse;
use ECidade\Enum\Educacao\Escola\Relatorios\ModeloDiarioClasseEnum;
use ECidade\Enum\Educacao\Escola\SituacaoMatriculaEnum;
use Exception;
use FpdfMultiCellBorder;
use GradeHorario;
use Illuminate\Http\Request;
use TurmaRepository;

abstract class DiarioClassePdf extends FpdfMultiCellBorder
{
    /**
     * @var ModeloDiarioClasseEnum
     */
    protected $modelo;

    /**
     * @var integer
     */
    protected $colunas;
    /**
     * @var DadosDiarioClasse[]
     */
    protected $dadosDiarioClasse = [];

    protected $periodoInformado;

    /* ---------------------------------------------------------------------- *
     *                                                                        *
     *              CONFIGURAÇÃO DA PRIMEIRA PÁGINA                           *
     *                                                                        *
     * ---------------------------------------------------------------------- */
    protected $tituloColunaNome = "Nome do Aluno";
    protected $larguraColunaNumero = 5;
    protected $larguraColunaPadrao = 5;
    protected $larguraMinimoFalta = 3.79;
    protected $larguraMaximaFalta = 5;

    protected $larguraColunaTotalFaltas = 10;
    protected $larguraColunaNome = 55;

    protected $numeroColunasAvaliacao = 7; // avaliações manual

    protected $tamanhoFonteGrade = 6;
    protected $larguraPagina = 265;
    protected $larguraPaginaReduzida = 265; // valor reduzido para caber mais avaliações
    protected $larguraPaginaCompleta = 280; // valor original de larguraPagina

    protected $larguraColunaOutros = 0;

    protected $numeroAlunosPagina = 38;

    protected $numeroMinimoColunaFalta = 30;
    protected $numeroMaximoColunaFalta = 70;


    /**
     * Se false não exibe a descrição do período de avaliação no subcabeçalho
     * @var bool
     */
    protected $exibirPeriodo = false;
    /**
     * Controla se devemos exibir a coluna de avaliação
     * @var bool
     */
    protected $exibirColunasAvaliacaoManual = false;

    /**
     * Se Relatório vai ser emitido pela data corte
     */
    protected $dataCorte = "";

    /**
     * Se exibe uma coluna de falta ($larguraColunaPadrao)
     * @var bool
     */
    protected $exibirColunaFalta = false;
    /**
     * @var bool
     */
    protected $exibirColunaTotalFalta = false;
    protected $turmaGlobalizada = false;
    /**
     * Exibe o número dos alunos após coluna de faltas
     * @var bool
     */
    protected $exibirNumeroAlunoAposGrade = false;

    /**t
     * Exibe idade do aluno após o nome do aluno
     * @var bool
     */
    protected $exibirIdadeAposNome = false;

    /**
     * @var bool
     */
    protected $exibirPontos = true;
    /**
     *
     * @var bool
     */
    protected $exibirDatasPeriodo = true;
    /**
     * @var bool
     */
    protected $registroManual = false;
    /**
     * @var bool
     */
    protected $exibirDiasLetivos = false;
    /**
     * @var bool
     */
    protected $exibirSituacaoAlunoDiario = false;
    /**
     * @var bool
     */
    protected $exibirTodasDisciplinas = false;
    /**
     * @var bool
     */
    protected $pautaUnica = false;

    /* ------------------------------------------------------------------ *
     *                                                                    *
     *                  CONFIGURAÇÃO DA SEGUNDA PÁGINA                    *
     *                                                                    *
     * ------------------------------------------------------------------ */
    private $larguraColunaNota = 15;
    private $larguraColunaDataNascimento = 20;
    /**
     * @var bool
     */
    private $exibirTotalFaltas = false;
    /**
     * @var bool
     */
    private $exibirSexo = false;
    /**
     * @var bool
     */
    private $exibirIdade = false;
    /**
     * @var bool
     */
    private $exibirFaltasAbonadas = false;
    /**
     * @var bool
     */
    protected $exibirAulasDadas = false;
    /**
     * @var bool
     */
    private $exibirCodigo = false;
    /**
     * @var bool
     */
    private $exibirNascimento = false;
    /**
     * @var bool
     */
    private $exibirResultadoAnterior = false;
    /**
     * @var bool
     */
    private $exibirParecer = false;
    /**
     * @var int
     */
    private $quantidadeColunasExtras;
    /**
     * @var float
     */
    private $tamanhoColunasExtras;
    /**
     * @var int
     */
    private $quantidadeDisciplinasTurmaGlobalizada = 0;
    /**
     * @var bool
     */
    private $exibirAlunos = true;

    /**
     * DiarioClassePdf constructor.
     * @param EmissaoDiarioClasseEspecialRequest|EmissaoDiarioClasseEscolarizacaoRequest $request
     * @param DadosDiarioClasse[] $dadosDiarioClasse
     * @throws Exception
     */
    public function __construct(Request $request, array $dadosDiarioClasse)
    {
        parent::__construct('L');
        $this->colunas = $request->get('colunas');
        $this->dadosDiarioClasse = $dadosDiarioClasse;

        $this->modelo = new ModeloDiarioClasseEnum((int)$request->get('modelo'));
        $this->configurarModelo($request);
    }

    public function setLarguraPagina($largura)
    {
        $this->larguraPagina = $largura;
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @throws Exception
     */
    protected function cabecalhoDadosInstituicao(DadosDiarioClasse $dadosDiarioClasse)
    {
        $this->AddPage();
        $escola = $dadosDiarioClasse->getEscola();

        /**
         * @var DBConfig $instituicao
         */
        $instituicao = $escola->getDepartamento()->getInstituicao();
        $imagem = $instituicao->getLogo();
        $this->Image("imagens/files/{$imagem}", 11, 9, 13);
        $posicaoX = 25;
        $tamanhoLinha = 80;

        $this->SetFont('Arial', 'B', '7');
        $this->SetXY($posicaoX, 8);

        $nomeEscola = $escola->getNome();
        $referencia = $escola->getCodigoReferencia();
        if (!empty($referencia)) {
            $nomeEscola = "{$referencia} - {$nomeEscola}";
        }

        $this->Cell($tamanhoLinha, 4, $instituicao->getNome(), 0, 1, 'L');
        $this->SetX($posicaoX);
        $this->MultiCell($tamanhoLinha, 4, $nomeEscola, 0, 1, 'L');

        $this->SetFont('Arial', '', '7');
        $this->SetXY($posicaoX, 24);
        $this->Cell($tamanhoLinha, 4, "Cidade: {$escola->getMunicipio()->getNome()}", 0, 0, 'L');
    }

    abstract protected function cabecalho(DadosDiarioClasse $dadosDiarioClasse, $request = null);

    /**
     * @return string
     */
    public function emitir($request = null)
    {
        $this->opem();

        foreach ($this->dadosDiarioClasse as $dadosDiarioClasse) {
            if ($this->turmaGlobalizada) {
                $this->recalculaColunaOutros($dadosDiarioClasse);
            }
            $estruturaMeses = $this->calculaEstruturaSubCabecalho($dadosDiarioClasse);
            $numeroPaginas = count($estruturaMeses);
            $imprimirEtapaAluno = count($dadosDiarioClasse->getTurma()->getEtapas()) > 1;
            foreach ($estruturaMeses as $pagina => $meses) {
                $ultimaPagina = $pagina === $numeroPaginas;
                $this->cabecalho($dadosDiarioClasse, $request);
                $this->emitirCorpo($dadosDiarioClasse, $meses, $ultimaPagina, $imprimirEtapaAluno, $request);
                $this->assinaturas();
            }

            if ($this->modelo->value() === ModeloDiarioClasseEnum::MODELO_DUAS_PAGINAS) {
                $this->cabecalho($dadosDiarioClasse, $request);
                $this->emitirSegundaPagina($dadosDiarioClasse, $imprimirEtapaAluno, $request);
            }
        }

        return $this->imprimir();
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @param $meses
     * @param $ultimaPagina
     * @param $imprimirEtapaAluno
     * @throws Exception
     */
    protected function emitirCorpo(
        DadosDiarioClasse $dadosDiarioClasse,
        $meses,
        $ultimaPagina,
        $imprimirEtapaAluno,
        $request = null
    ) {
        $this->imprimeSubcabecalho($dadosDiarioClasse, $meses, $ultimaPagina);
        $alunosDiarioClasses = $dadosDiarioClasse->getAlunos();
        $alunosImpressos = 1;

        /*  Pega a data do periodo do calendário se NÃO for Turma AEE */
        if ($this->modelo->getValue() !== 1) {
            $periodo = $dadosDiarioClasse->getAvaliacaoPeriodica()->getPeriodoAvaliacao();
            $calendario = CalendarioRepository::getCalendarioByCodigo($dadosDiarioClasse->getCalendario()->getCodigo());
            $periodoCalendario = $calendario->getPeriodoCalendarioPorPeriodoAvaliacao($periodo);
            $dataFimPeriodoCalendario = $periodoCalendario->getDataTermino();
        }
        foreach ($alunosDiarioClasses as $alunoDiarioClasse) {
            /*  Pega a data do periodo do calendário se NÃO for Turma AEE */
            if ($this->modelo->getValue() !== 1) {
                $dataMatriculaAluno = $alunoDiarioClasse->getMatricula()->getDataMatricula();
                $dataFimComparacao = $dataFimPeriodoCalendario;

                if (!empty($this->dataCorte)) {
                    $dataCorteFormatada = DBDate::create($this->dataCorte);
                    $dataFimComparacao = $dataCorteFormatada;
                }

                if ($dataMatriculaAluno > $dataFimComparacao) {
                    continue;
                }

                // verifica se trocou de turma para esta posteriormente
                $matricula = $alunoDiarioClasse->getMatricula();
                if ($matricula->getDataModificacaoAnterior() !== null) {
                    $matriculasDoAluno = \Matricula::getMatriculas($matricula->getMatricula());
                    if (count($matriculasDoAluno) > 1) {
                        for ($i=1; $i < count($matriculasDoAluno); $i++) {
                            if ($matricula->getCodigo() === $matriculasDoAluno[$i]->getCodigo() &&
                                $matriculasDoAluno[$i - 1]->getSituacao() === 'TROCA DE TURMA') {
                                $saidaAlunoUltimaTurma = \Carbon\Carbon::createFromTimestamp(
                                    $matriculasDoAluno[$i - 1]->getDataEncerramento()->getTimestamp()
                                );

                                if (
                                    //$dadosDiarioClasse->getPeriodoDoDiario()['inicio']->gte($saidaAlunoUltimaTurma) &&
                                    $dadosDiarioClasse->getPeriodoDoDiario()['fim']->lt($saidaAlunoUltimaTurma)
                                    ) {
                                    continue 2;
                                }
                            }
                        }
                    }
                }
            }

            if ($alunosImpressos == $this->numeroAlunosPagina) {
                $alunosImpressos = 1;
                $this->assinaturas();
                $this->cabecalho($dadosDiarioClasse, $request);
                $this->imprimeSubcabecalho($dadosDiarioClasse, $meses, $ultimaPagina);
            }
            $alunosImpressos++;

            if ($this->exibirAlunos) {
                $this->imprimeAluno($alunoDiarioClasse, $ultimaPagina, $meses, $imprimirEtapaAluno);
            } else {
                $this->imprimeGradeSemAlunos($alunoDiarioClasse, $ultimaPagina, $meses, $imprimirEtapaAluno);
            }
        }

        if ($alunosImpressos < $this->numeroAlunosPagina) {
            $linhas = $this->numeroAlunosPagina - $alunosImpressos;
            $colunasEmBranco = $this->colunas;
            $tamanhoColuna = 0;
            if ($this->exibirDiasLetivos) {
                $colunasEmBranco = $meses->colunas + $meses->colunasEmBranco;
                $tamanhoColuna = $meses->tamanhoColuna;
            }
            $this->emitirLinhasEmBranco($linhas, $tamanhoColuna, $colunasEmBranco, $ultimaPagina);
        }
    }

    public function assinaturas()
    {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(140, 4, "Entregue em ___/___/_______ Por ________________________________", 1, 0);
        $this->Cell(140, 4, "Revisado em ___/___/_______ Por ________________________________", 1, 1);

        $this->Cell(140, 4, "Processado em ___/___/_______ Por ________________________________", 1, 0);
        $this->Cell(140, 4, "Assinatura do Professor ___/___/_______ Por ________________________________", 1, 1);
    }

    protected function opem()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->SetMargins(8, 8, 8);
        $this->Open();
        $this->SetAutoPageBreak(false, 10);
        $this->AliasNbPages();
        $this->SetFillColor(235);
        $this->SetFont('Arial', 'B', 10);
    }

    protected function imprimir()
    {
        $fileName = 'tmp/diario_classe' . time() . '.pdf';
        $this->Output($fileName, false, true);
        return ECIDADE_REQUEST_PATH . $fileName;
    }

    private function imprimeSubcabecalho(DadosDiarioClasse $dadosDiarioClasse, $meses, $ultimaPagina)
    {
        if ($this->exibirPeriodo) {
            $this->imprimeLinhaDescricaoPeriodo($dadosDiarioClasse);
        }

        $this->SetFont("arial", 'B', 7);
        $turma = $dadosDiarioClasse->getTurma();
        $this->imprimeLinhaMeses($meses, $ultimaPagina, $turma);

        $this->Cell($this->larguraColunaNumero, 4, "Nº", 1, 0, "C");
        $this->Cell($this->larguraColunaNome - 10, 4, "Nome do Aluno", 1, 0, "C");
        $this->Cell(10, 4, "Dia >", 1, 0, "C");

        if (empty($meses)) {
            $this->imprimeCelulasManualGrade($ultimaPagina, true);
        } else {
            foreach ($meses->meses as $mes) {
                foreach ($mes->dias as $dia) {
                    $this->cellAdapt($this->tamanhoFonteGrade, $meses->tamanhoColuna, 4, $dia->dia, 1, 0, 'C');
                }
            }

            $this->imprimeColunasEmBranco($meses->colunasEmBranco, $meses->tamanhoColuna, true);
        }
        $this->escreverColunaNumeroAluno($ultimaPagina);
        $this->escreverColunasAvaliacao($ultimaPagina);
        $this->escreverColunasTurmaGlobalizada($ultimaPagina);
        $this->escreverColunaFalta($ultimaPagina);
        $this->escreverColunaTotalFaltas($ultimaPagina);
        $this->Ln();
        $this->SetFont('Arial', '', $this->tamanhoFonteGrade);
    }

    /**
     * @param $request
     */
    private function configurarModelo($request)
    {
        if ($this->modelo->value() !== ModeloDiarioClasseEnum::ESPECIAL) {
            // subcabeçalho
            $this->exibirPeriodo = true;
            $this->exibirPontos = $request->get('exibirPontos') == 1;
            $this->exibirDatasPeriodo = $request->get('exibirDataPeriodo') == 1;
            $this->exibirDiasLetivos = $request->get('exibirDiasLetivos') == 1;
            $this->registroManual = $request->get('registroManual') == 1;
            $this->exibirAlunosRetorno = $request->get('exibirAlunosRetorno') == 1;
            $this->numeroAlunosPagina--;
        }

        switch ($this->modelo->value()) {
            case ModeloDiarioClasseEnum::ESPECIAL:
                // parâmetros da grade
                $this->exibirColunaTotalFalta = true;
                $this->exibirPontos = $request->get('exibirPontos') == 1;
                $this->exibirNumeroAlunoAposGrade = true;
                $this->exibirIdadeAposNome = true;

                // subcabeçalho
                $this->exibirDiasLetivos = false;
                break;

            case ModeloDiarioClasseEnum::MODELO_UMA_DISCIPLINA_PAGINA:
                // parâmetros da grade
                $this->exibirDiasLetivos = $request->get('exibirDiasLetivos') == 1;
                $this->pautaUnica = $request->get('pautaUnica') == 1;
                $this->exibirColunasAvaliacaoManual = $request->get('exibirAvaliacoes') == 1;
                $this->exibirColunaFalta = $request->get('exibirTotalFaltas') == 1;
                $this->exibirAlunos = $request->get('exibirAlunos') == 1;
                $this->dataCorte = $request->get('dataCorte');
                $this->exibirNumeroAlunoAposGrade = $this->exibirColunasAvaliacaoManual || $this->exibirColunaFalta;
                break;

            case ModeloDiarioClasseEnum::MODELO_DUAS_PAGINAS:
                // altera largura para compensar a falta da grade de avaliações
                $this->setLarguraPagina($this->larguraPaginaCompleta);

                $this->exibirIdadeAposNome = true;
                // segunda pagina
                $this->exibirTotalFaltas = $request->get('exibirTotalFaltas') == 1;
                $this->exibirSexo = $request->get('exibirSexo') == 1;
                $this->exibirIdade = $request->get('exibirIdade') == 1;
                $this->exibirFaltasAbonadas = $request->get('exibirFaltasAbonadas') == 1;
                $this->exibirCodigo = $request->get('exibirCodigo') == 1;
                $this->exibirAlunos = $request->get('exibirAlunos') == 1;
                $this->exibirNascimento = $request->get('exibirNascimento') == 1;
                $this->exibirResultadoAnterior = $request->get('exibirResultadoAnterior') == 1;
                $this->exibirParecer = $request->get('exibirParecer') == 1;
                break;

            case ModeloDiarioClasseEnum::MODELO_CURRICULO:
                $this->exibirDatasPeriodo = $request->get('exibirDataPeriodo') == 1;
                $this->exibirColunasAvaliacaoManual = $request->get('exibirAvaliacoes') == 1;
                $this->exibirColunaFalta = true;
                $this->turmaGlobalizada = true;
                $this->exibirTodasDisciplinas = $request->get('exibirTodasDisciplinas') == 1;
                $this->exibirNumeroAlunoAposGrade = true;
                break;
        }

        $this->exibirAulasDadas = $request->get('exibirAulasDadas') == 1;

        if (!$this->exibirDiasLetivos) {
            $this->exibirSituacaoAlunoDiario = $request->get('exibirSituacaoAlunoDiario') == 1;
        }

        // se não precisar das colunas de avaliação
        if (!$this->exibirColunasAvaliacaoManual) {
            $this->setLarguraPagina($this->larguraPaginaCompleta);
        }

        // se for registro de Frequência / Conteúdo
        if (!$this->registroManual) {
            $this->setLarguraPagina($this->larguraPaginaCompleta);
        }

        // $this->setLarguraPagina($this->larguraPaginaCompleta);
        $this->calcularColunaOutros();
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @throws Exception
     */
    private function imprimeLinhaDescricaoPeriodo(DadosDiarioClasse $dadosDiarioClasse)
    {
        $dataInicio = "___/___/_____";
        $dataFim = "___/___/_____";

        $periodoAvaliacao = $dadosDiarioClasse->getAvaliacaoPeriodica()->getPeriodoAvaliacao();
        if ($this->exibirDatasPeriodo) {
            $calendario = CalendarioRepository::getCalendarioByCodigo($dadosDiarioClasse->getCalendario()->getCodigo());
            $oPeriodoCalendario = $calendario->getPeriodoCalendarioPorPeriodoAvaliacao($periodoAvaliacao);
            $dataInicio = $oPeriodoCalendario->getDataInicio()->convertTo(DBDate::DATA_PTBR);
            $dataFim = $oPeriodoCalendario->getDataTermino()->convertTo(DBDate::DATA_PTBR);
        }

        $periodo = $periodoAvaliacao->getDescricao();
        $periodo .= " - {$dataInicio} à {$dataFim}";

        $this->SetFont("arial", 'B', 8);
        $this->ln(0.5);
        $this->Cell($this->larguraPagina, 4, $periodo, '', 1, "C");
        $this->SetFont("arial", '', $this->tamanhoFonteGrade);
    }

    /**
     * @param object $meses
     * @param boolean $ultimaPagina
     * @param TurmaDiarioClasse $turma
     */
    private function imprimeLinhaMeses($meses, $ultimaPagina, TurmaDiarioClasse $turma)
    {
        $primeiraColuna = $this->larguraColunaNumero + $this->larguraColunaNome - 10;

        $this->Cell($primeiraColuna, 4, "", 1);
        $this->Cell(10, 4, "Mês >", 1);

        $w = $this->getWidthGrade($ultimaPagina);
        if (empty($meses)) {
            $this->Cell($w, 4, "", 1);
        } else {
            foreach ($meses->meses as $key => $mes) {
                $wMes = count($mes->dias) * $meses->tamanhoColuna;
                $this->cellAdapt($this->tamanhoFonteGrade, $wMes, 4, $key, 1, 0, 'C');
            }
            if (!empty($meses->colunasEmBranco)) {
                $wEmBranco = $meses->colunasEmBranco * $meses->tamanhoColuna;
                $this->Cell($wEmBranco, 4, '', 1);
            }
        }

        $this->escreverColunaNumeroAluno($ultimaPagina, 'Nº');
        $this->escreverColunasAvaliacao($ultimaPagina, true);
        $this->escreverColunasTurmaGlobalizada($ultimaPagina, $turma, true);
        $this->escreverColunaFalta($ultimaPagina, "F");
        $this->escreverColunaTotalFaltas($ultimaPagina, "Ft");
        $this->Ln();
    }


    /**
     * Calcula o disponível para o calculo da grade
     * @param boolean $ultimaPagina
     * @return int
     */
    private function getWidthGrade($ultimaPagina)
    {
        $tamanhoDisponivelGrade = $this->larguraPagina - $this->larguraColunaNome - $this->larguraColunaNumero;

        if (!$ultimaPagina) {
            if (!$this->registroManual) {
                return $tamanhoDisponivelGrade;
            } else {
                if ($this->modelo->getValue() !== 3) {
                    if (!$this->exibirColunasAvaliacaoManual) {
                        return $tamanhoDisponivelGrade;
                    }
                     return $tamanhoDisponivelGrade + 15;
                }
                return $tamanhoDisponivelGrade;
            }
        }

        if (!$this->registroManual) {
            $tamanhoDisponivelGrade = $this->larguraPaginaReduzida - $this->larguraColunaNome;
            $tamanhoDisponivelGrade = $tamanhoDisponivelGrade - $this->larguraColunaNumero - $this->larguraColunaOutros;
            return $tamanhoDisponivelGrade;
        }

        return $tamanhoDisponivelGrade - $this->larguraColunaOutros;
    }

    private function calculaEstruturaSubCabecalho(DadosDiarioClasse $dadosDiarioClasse)
    {
        $avaliacaoPeriodica = $dadosDiarioClasse->getAvaliacaoPeriodica();
        if (!$this->exibirDiasLetivos) {
            $paginas[1] = [];
            return $paginas;
        }

        if ($this->registroManual) {
            $periodoAvaliacao = $avaliacaoPeriodica->getPeriodoAvaliacao();
            $calendario = CalendarioRepository::getCalendarioByCodigo($dadosDiarioClasse->getCalendario()->getCodigo());
            $datasCalendario = $calendario->getDatasLetivoNoPeriodo($periodoAvaliacao);
            if (empty($datasCalendario)) {
                throw new Exception("Não existe dias letivos configurado no calendário para o período selecionado.");
            }

            $datas = [];
            foreach ($datasCalendario as $data) {
                $datas[] = (object)[
                    'data' => $data,
                    'periodo' => null
                ];
            }
            $paginas = $this->organizaDatasSubCabecalho($datas);
        }

        if (!$this->registroManual) {
            $periodoAvaliacao = $dadosDiarioClasse->getAvaliacaoPeriodica()->getPeriodoAvaliacao();
            $turma = TurmaRepository::getTurmaByCodigo($dadosDiarioClasse->getTurma()->getCodigo());
            $etapas = $dadosDiarioClasse->getTurma()->getEtapas();
            $etapa = array_shift($etapas);
            $gradeHorario = new GradeHorario($turma, $etapa);
            $disciplina = DisciplinaRepository::getDisciplinaByCodigo($dadosDiarioClasse->getDisciplina()->getCodigo());
            $datasLetivas = $gradeHorario->getDiasDeAulaDaDisciplinaNoPeriodoDeAvaliacao(
                $disciplina,
                $periodoAvaliacao
            );

            $datas = [];
            foreach ($datasLetivas as $dataLetiva) {
                $periodosOrdenados = [];
                foreach ($dataLetiva->aPeriodoAula as $oPeriodoAula) {
                    $turno = trim(strval($oPeriodoAula->getPeriodoEscola()->getTurno()->getCodigoTurno()));
                    $periodo = trim(strval($oPeriodoAula->getPeriodoEscola()->getPeriodoAula()));
                    $periodosOrdenados[intval("{$turno}{$periodo}")] = $oPeriodoAula;
                }
                ksort($periodosOrdenados);
                foreach ($periodosOrdenados as $oPeriodoAula) {
                    $datas[] = (object)[
                        'data' => $dataLetiva->oData,
                        'periodo' => $oPeriodoAula->getPeriodoEscola()->getCodigo()
                    ];
                }
            }
            $paginas = $this->organizaDatasSubCabecalho($datas);
        }
        return $paginas;
    }

    private function escreverColunaNumeroAluno($ultimaPagina = true, $valor = '')
    {
        if (!$ultimaPagina) {
            return;
        }
        if ($this->exibirNumeroAlunoAposGrade) {
            $this->Cell($this->larguraColunaNumero, 4, $valor, 1, 0, 'C');
        }
    }

    private function escreverColunasAvaliacao($ultimaPagina = true, $titulo = false)
    {
        if (!$ultimaPagina || !$this->exibirColunasAvaliacaoManual) {
            return;
        }

        if ($titulo) {
            $larguraColunaAvaliacao = $this->numeroColunasAvaliacao * $this->larguraColunaPadrao;
            $this->Cell($larguraColunaAvaliacao, 4, "Avaliações", 1, 0, "C");
        } else {
            for ($i = 0; $i < $this->numeroColunasAvaliacao; $i++) {
                $this->Cell($this->larguraColunaPadrao, 4, "", 1, 0, "C");
            }
        }
    }

    private function escreverColunaFalta($ultimaPagina = true, $valor = '')
    {
        if (!$ultimaPagina) {
            return;
        }
        if ($this->exibirColunaFalta) {
            $this->Cell($this->larguraColunaPadrao, 4, $valor, 1, 0, "C");
        }
    }

    /**
     * @param bool $ultimaPagina
     * @param $valor
     */
    private function escreverColunaTotalFaltas($ultimaPagina = true, $valor = '')
    {
        if (!$ultimaPagina) {
            return;
        }
        if ($this->exibirColunaTotalFalta) {
            $this->Cell($this->larguraColunaTotalFaltas, 4, $valor, 1, 0, "C");
        }
    }

    private function calculaLarguraCelulaGrade($ultimaPagina)
    {
        $w = $this->getWidthGrade($ultimaPagina);
        return $w / $this->colunas;
    }

    /**
     * @param Model\AlunoDiarioClasse $alunoDiarioClasse
     * @param boolean $ultimaPagina
     * @param array $meses
     * @param boolean $imprimirEtapaAluno
     * @throws Exception
     */
    private function imprimeAluno(
        Model\AlunoDiarioClasse $alunoDiarioClasse,
        $ultimaPagina,
        $meses,
        $imprimirEtapaAluno
    ) {
        if ($ultimaPagina && !$this->registroManual) {
            $this->setLarguraPagina($this->larguraPaginaReduzida);
        }
        $this->Cell($this->larguraColunaNumero, 4, $alunoDiarioClasse->getNumero(), 1);

        $larguraColunaNome = $this->larguraColunaNome;
        if ($this->exibirIdadeAposNome) {
            $larguraColunaNome -= $this->larguraColunaPadrao;
        }

        if ($imprimirEtapaAluno) {
            $larguraColunaNome -= 7;
        }

        $fonte = $this->tamanhoFonteGrade;
        $nome = is_null($alunoDiarioClasse->getNomeSocial()) || empty($alunoDiarioClasse->getNomeSocial()) ?
            $alunoDiarioClasse->getNome() :
            $alunoDiarioClasse->getNomeSocial();
        $this->cellAdapt($fonte, $larguraColunaNome, 4, $nome, 1);

        if ($this->exibirIdadeAposNome) {
            $this->Cell($this->larguraColunaPadrao, 4, $alunoDiarioClasse->getIdadeEmAnos(), 1);
        }

        if ($imprimirEtapaAluno) {
            $sigla = $alunoDiarioClasse->getMatricula()->getEtapaDeOrigem()->getNomeAbreviado();
            $this->cellAdapt($fonte, 7, 4, $sigla, 1);
        }
        if ($alunoDiarioClasse->isAmparado()) {
            $this->imprimeAlunoAmparado($ultimaPagina);
        } elseif (!$this->exibirDiasLetivos) {
            if ($this->exibirSituacaoAlunoDiario) {
                if ($alunoDiarioClasse->getSituacao()->value() != "MATRICULADO") {
                    if ($this->dataCorte) {
                        $encerramento = $alunoDiarioClasse->getDataEncerramento();
                        $dataCorte = new \DBDate($this->dataCorte);
                        if ($encerramento->getTimestamp() >= $dataCorte->getTimeStamp()) {
                            $this->imprimeCelulasManualGrade($ultimaPagina);
                        } else {
                            $tamanho = $this->colunas * $this->calculaLarguraCelulaGrade($ultimaPagina);
                            $this->imprimeSituacaoAluno($alunoDiarioClasse, $tamanho);
                        }
                    } else {
                        $tamanho = $this->colunas * $this->calculaLarguraCelulaGrade($ultimaPagina);
                        $this->imprimeSituacaoAluno($alunoDiarioClasse, $tamanho);
                    }
                } else {
                    $this->imprimeCelulasManualGrade($ultimaPagina);
                }
            } else {
                $this->imprimeCelulasManualGrade($ultimaPagina);
            }
        } else {
            $this->imprimeCelulasFrequencia($meses, $alunoDiarioClasse);
        }

        $this->escreverColunaNumeroAluno($ultimaPagina, $alunoDiarioClasse->getNumero());
        $this->escreverColunasAvaliacao($ultimaPagina);
        $this->escreverColunasTurmaGlobalizada($ultimaPagina);
        $this->escreverColunaFalta($ultimaPagina, count($alunoDiarioClasse->getFaltas()));
        $this->escreverColunaTotalFaltas($ultimaPagina);
        $this->Ln();
    }

    /**
     * @param Model\AlunoDiarioClasse $alunoDiarioClasse
     * @param boolean $ultimaPagina
     * @param array $meses
     * @param boolean $imprimirEtapaAluno
     * @throws Exception
     */
    private function imprimeGradeSemAlunos(
        Model\AlunoDiarioClasse $alunoDiarioClasse,
        $ultimaPagina,
        $meses,
        $imprimirEtapaAluno
    ) {
        $this->Cell($this->larguraColunaNumero, 4, '', 1);

        $larguraColunaNome = $this->larguraColunaNome;
        if ($this->exibirIdadeAposNome) {
            $larguraColunaNome -= $this->larguraColunaPadrao;
        }

        if ($imprimirEtapaAluno) {
            $larguraColunaNome -= 7;
        }

        $fonte = $this->tamanhoFonteGrade;

        $this->cellAdapt($fonte, $larguraColunaNome, 4, '', 1);

        if ($this->exibirIdadeAposNome) {
            $this->Cell($this->larguraColunaPadrao, 4, '', 1);
        }

        if ($imprimirEtapaAluno) {
            $sigla = $alunoDiarioClasse->getMatricula()->getEtapaDeOrigem()->getNomeAbreviado();
            $this->cellAdapt($fonte, 7, 4, $sigla, 1);
        }
        if ($alunoDiarioClasse->isAmparado()) {
            $this->imprimeAlunoAmparado($ultimaPagina);
        } elseif (!$this->exibirDiasLetivos) {
            $this->imprimeCelulasManualGrade($ultimaPagina);
        } else {
            $this->imprimeCelulasFrequencia($meses, $alunoDiarioClasse);
        }
        $this->escreverColunaNumeroAluno($ultimaPagina, $alunoDiarioClasse->getNumero());
        $this->escreverColunasAvaliacao($ultimaPagina);
        $this->escreverColunasTurmaGlobalizada($ultimaPagina);
        $this->escreverColunaFalta($ultimaPagina);
        $this->escreverColunaTotalFaltas($ultimaPagina);
        $this->Ln();
    }

    private function imprimePontoGrade($largura, $cabecalho, $valor = '')
    {
        if ($cabecalho) {
            $valor = '';
        }
        if (!$cabecalho && $this->exibirPontos && empty($valor)) {
            $y = $this->GetY();
            $x = $this->GetX();

            $this->Setfont('arial', 'B', 12);
            $this->Text($x + ($largura * 30 / 95), $y + 2.5, ".");
            $this->SetFont("Arial", '', $this->tamanhoFonteGrade);
        }

        $this->Cell($largura, 4, $valor, 1);
    }

    /**
     * @param $ultimaPagina
     * @param bool $cabecalho
     */
    private function imprimeCelulasManualGrade($ultimaPagina, $cabecalho = false)
    {
        $largura = $this->calculaLarguraCelulaGrade($ultimaPagina);
        for ($i = 0; $i < $this->colunas; $i++) {
            $this->imprimePontoGrade($largura, $cabecalho);
        }
    }

    private function emitirLinhasEmBranco($linhas, $tamanhoColuna, $colunasEmBranco, $ultimaPagina)
    {
        for ($i = 0; $i < $linhas; $i++) {
            $this->Cell($this->larguraColunaNumero, 4, '', 1);
            $this->Cell($this->larguraColunaNome, 4, '', 1);

            if (!$this->exibirDiasLetivos) {
                $this->imprimeCelulasManualGrade($ultimaPagina);
            } else {
                $this->imprimeColunasEmBranco($colunasEmBranco, $tamanhoColuna);
            }

            $this->escreverColunaNumeroAluno($ultimaPagina);
            $this->escreverColunasAvaliacao($ultimaPagina);
            $this->escreverColunasTurmaGlobalizada($ultimaPagina);
            $this->escreverColunaFalta($ultimaPagina);
            $this->escreverColunaTotalFaltas($ultimaPagina);
            $this->Ln();
        }
    }

    /**
     * @param array $datasCalendario
     * @return array
     */
    private function organizaDatasSubCabecalho(array $datasCalendario)
    {
        $wMaximoGradeUltimaPagina = $this->getWidthGrade(true);
        $wMaximoGradePaginas = $this->getWidthGrade(false);

        $maximoUltimaPagina = [
            'maximo' => floor($wMaximoGradeUltimaPagina / $this->larguraMinimoFalta),
            'minimo' => floor($wMaximoGradeUltimaPagina / $this->larguraMaximaFalta)
        ];

        $maximoPaginas = [
            'maximo' => floor($wMaximoGradePaginas / $this->larguraMinimoFalta),
            'minimo' => floor($wMaximoGradePaginas / $this->larguraMaximaFalta)
        ];

        $totalCelulaGrade = count($datasCalendario);
        $numeroMaximoColunaFalta = $maximoPaginas['maximo'];
        $maximos = $maximoPaginas;
        if ($totalCelulaGrade <= $maximoUltimaPagina['maximo']) {
            $numeroMaximoColunaFalta = $maximoUltimaPagina['maximo'];
            $maximos = $maximoUltimaPagina;
        }

        $pagina = 1;
        $paginas = [];

        $contador = 1;
        foreach ($datasCalendario as $data) {
            if ($contador > $numeroMaximoColunaFalta) {
                $totalCelulaGrade -= $contador;
                if ($totalCelulaGrade <= $maximoUltimaPagina['maximo']) {
                    $numeroMaximoColunaFalta = $maximoUltimaPagina['maximo'];
                    $maximos = $maximoUltimaPagina;
                }
                $contador = 1;
                $pagina += 1;
            }

            if (!array_key_exists($pagina, $paginas)) {
                $paginas[$pagina] = (object)[
                    'meses' => [],
                    'colunas' => 0,
                    'maximos' => $maximos,
                ];
            }

            $dia = (object)[
                'dia' => $data->data->getDia(),
                'data' => $data->data,
                'periodo' => $data->periodo,
            ];

            if (!array_key_exists($data->data->getMes(), $paginas[$pagina]->meses)) {
                $mes = new \stdClass();
                $mes->nome = $data->data->getMesExtenso($data->data->getMes());
                $mes->dias = [];
                $paginas[$pagina]->meses[$data->data->getMes()] = $mes;
            }

            $paginas[$pagina]->meses[$data->data->getMes()]->dias[] = $dia;
            $paginas[$pagina]->colunas++;
            $contador++;
        }

        $totalPaginas = count($paginas);

        foreach ($paginas as $iPagina => $pagina) {
            $wGrade = $this->getWidthGrade(false);
            if ($iPagina === $totalPaginas) {
                $wGrade = $this->getWidthGrade(true);
            }

            $pagina->colunasEmBranco = 0;
            if ($pagina->colunas < $pagina->maximos['minimo']) {
                $pagina->colunasEmBranco = abs($pagina->colunas - $pagina->maximos['minimo']);
            }

            $pagina->tamanhoColuna = $wGrade / ($pagina->colunas + $pagina->colunasEmBranco);
        }

        return $paginas;
    }

    /**
     * Verifica se o aluno tem falta na data que esta sendo impressa
     * @param Model\AlunoDiarioClasse $alunoDiarioClasse
     * @param $dia
     * @return bool
     */
    protected function hasFalta(Model\AlunoDiarioClasse $alunoDiarioClasse, $dia)
    {
        if (!$this->registroManual) {
            $faltas = $alunoDiarioClasse->getFaltas();
            foreach ($faltas as $falta) {
                if ($falta->getData()->getTimeStamp() === $dia->data->getTimeStamp() &&
                    $dia->periodo == $falta->getPeriodo()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $meses
     * @param Model\AlunoDiarioClasse $alunoDiarioClasse
     * @throws Exception
     */
    private function imprimeCelulasFrequencia($meses, Model\AlunoDiarioClasse $alunoDiarioClasse)
    {
        $colunas = 0;
        $aMeses = array_keys($meses->meses);
        $anoCorte = $meses->meses[$aMeses[0]]->dias[0]->data->getAno();
        $diaMatricula = null;
        $mesMatricula = null;
        $anoMatricula = null;
        $situacao = "";

        $inicioDiario = $this->dadosDiarioClasse[0]->getPeriodoDoDiario()['inicio'];
        $fimDiario = $this->dadosDiarioClasse[0]->getPeriodoDoDiario()['fim'];
        $matriculaAnterior = $alunoDiarioClasse->getMatriculaAnterior();
        if (!is_null($matriculaAnterior)) {
            if ($matriculaAnterior->getSituacao() == 'TROCA DE TURMA') {
                if ($matriculaAnterior->getDataModificacao()->getTimeStamp()
                    >
                    $inicioDiario->getTimeStamp()
                    &&
                    $matriculaAnterior->getDataModificacao()->getTimeStamp()
                    <
                    $fimDiario->getTimeStamp()
                ) {
                    $dataMatricula = new Carbon($matriculaAnterior->getDataModificacao()->getDate());
                    $mesMatricula = $dataMatricula->month;
                    $diaMatricula = $dataMatricula->day;
                    $anoMatricula = $dataMatricula->year;
                    $situacao = new SituacaoMatriculaEnum($matriculaAnterior->getSituacao());
                }
            }
        }

        if ($alunoDiarioClasse->getMatricula()->getDataMatricula()->getMes() >= $aMeses[0]) {
            if (!(  $alunoDiarioClasse->getMatricula()->getDataMatricula()->getMes() == $aMeses[0]
                        &&
                    $alunoDiarioClasse->getMatricula()
                        ->getDataMatricula()
                        ->getDia() <= $meses->meses[$aMeses[0]]->dias[0]->dia
                )
            ) {
                $diaMatricula = $alunoDiarioClasse->getMatricula()->getDataMatricula()->getDia();
                $mesMatricula = $alunoDiarioClasse->getMatricula()->getDataMatricula()->getMes();
                $anoMatricula = $alunoDiarioClasse->getMatricula()->getDataMatricula()->getAno();
            }
        }

        // verifica se aluno trocou de turma
        $alunoMatricula = $alunoDiarioClasse->getMatricula();
        if ($alunoMatricula->getDataModificacaoAnterior() !== null) {
            $matriculasDoAluno = \Matricula::getMatriculas($alunoMatricula->getMatricula());
            if (count($matriculasDoAluno) > 1) {
                $achouTrocaDeTurma = false;
                for ($i=1; $i < count($matriculasDoAluno); $i++) {
                    if ($alunoMatricula->getCodigo() === $matriculasDoAluno[$i]->getCodigo() &&
                        $matriculasDoAluno[$i-1]->getSituacao() === 'TROCA DE TURMA') {
                        $achouTrocaDeTurma = true;

                        $saidaAlunoUltimaTurma = \Carbon\Carbon::createFromTimestamp(
                            $matriculasDoAluno[$i-1]->getDataEncerramento()->getTimestamp()
                        );
                        $saidaAlunoUltimaTurmaMes = $saidaAlunoUltimaTurma->month;
                        $saidaAlunoUltimaTurmaDia = $saidaAlunoUltimaTurma->day;

                        $inicioDoDiario = \Carbon\Carbon::createFromTimestamp(
                            $meses->meses[array_keys($meses->meses)[0]]->dias[0]->data->getTimestamp()
                        );
                        $inicioDoDiarioMes = $inicioDoDiario->month;
                        $inicioDoDiarioDia = $inicioDoDiario->day;

                        $mesMatricula = $saidaAlunoUltimaTurmaMes < $inicioDoDiarioMes ?
                            null : $saidaAlunoUltimaTurmaMes;
                        $diaMatricula = null;

                        if ($mesMatricula != $saidaAlunoUltimaTurmaMes) {
                            $diaMatricula = null;
                        } else {
                            $diaMatricula = $saidaAlunoUltimaTurmaDia;
                            if ($inicioDoDiarioMes === $saidaAlunoUltimaTurmaMes &&
                            $saidaAlunoUltimaTurmaDia < $inicioDoDiarioDia) {
                                $mesMatricula = null;
                                $diaMatricula = null;
                            }
                        }

                        if ($inicioDoDiarioMes === $saidaAlunoUltimaTurmaMes &&
                        $inicioDoDiarioDia === $saidaAlunoUltimaTurmaDia) {
                            $mesMatricula = null;
                            $diaMatricula = null;
                        }
                    }
                }
            }
        }

        $tamanhoColunaDeDiasEmbranco = 0;
        foreach ($meses->meses as $key => $mes) {
            if (isset($mesMatricula)) {
                if ($key < $mesMatricula && $anoCorte == $anoMatricula) {
                    $tamanhoColunaDeDiasEmbranco += count($mes->dias);
                } elseif ($key == $mesMatricula && $anoCorte == $anoMatricula) {
                    foreach ($mes->dias as $dia) {
                        if ($dia->dia < $diaMatricula) {
                            $tamanhoColunaDeDiasEmbranco++;
                        }
                    }
                }
            }
        }
        if (isset($mesMatricula)) {
            $tamanhoColuna = ($tamanhoColunaDeDiasEmbranco) * $meses->tamanhoColuna;
            if ($tamanhoColuna != 0) {
                $this->imprimeSituacaoAluno($alunoDiarioClasse, $tamanhoColuna, $situacao);
            }
        }

        foreach ($meses->meses as $key => $mes) {
            if (isset($mesMatricula)) {
                if ($aMeses[count($aMeses)-1] != $mesMatricula) {
                    if ($key < $mesMatricula && $anoCorte == $anoMatricula) {
                        continue;
                    }
                }
            }
            foreach ($mes->dias as $dia) {
                if (isset($mesMatricula)) {
                    if ($key < $mesMatricula && $anoCorte == $anoMatricula) {
                        continue;
                    } else {
                        if ($key == $mesMatricula && $anoCorte == $anoMatricula) {
                            if ($dia->dia < $diaMatricula) {
                                if ($key == $mesMatricula && $key == $aMeses[0]) {
                                    continue;
                                } elseif ($key == $mesMatricula) {
                                    continue;
                                }
                                if ($aMeses[count($aMeses)-1] != $mesMatricula) {
                                    if ($dia->dia != $diaMatricula) {
                                        continue;
                                    }
                                }
                            }
                        } else {
                            if ($aMeses[count($aMeses)-1] == $mesMatricula) {
                                continue;
                            }
                        }
                    }
                }
                $falta = $this->hasFalta($alunoDiarioClasse, $dia) ? 'F' : '';
                if ($alunoDiarioClasse->getSituacao()->value() === SituacaoMatriculaEnum::MATRICULADO) {
                    $this->imprimePontoGrade($meses->tamanhoColuna, false, $falta);
                } elseif ($alunoDiarioClasse->getSituacao()->value() !== SituacaoMatriculaEnum::MATRICULADO) {
                    $dataEncerramentoAluno = $alunoDiarioClasse->getDataEncerramento()->getTimeStamp();
                    if ($this->dataCorte != '') {
                        $dataCorteFormatada = strtotime(str_replace("/", "-", $this->dataCorte));
                        if ($dataCorteFormatada <= $dataEncerramentoAluno) {
                            $this->imprimePontoGrade($meses->tamanhoColuna, false, $falta);
                        } else {
                            if ($alunoDiarioClasse->getDataEncerramento()
                                ->getTimeStamp() <= $dia->data->getTimeStamp()) {
                                $colunas++;
                            } else {
                                $this->imprimePontoGrade($meses->tamanhoColuna, false, $falta);
                            }
                        }
                    } else {
                        if ($alunoDiarioClasse->getDataEncerramento()->getTimeStamp() <= $dia->data->getTimeStamp()) {
                            $colunas++;
                        } else {
                            $this->imprimePontoGrade($meses->tamanhoColuna, false, $falta);
                        }
                    }
                }
            }
        }
        if ($alunoDiarioClasse->getSituacao()->value() !== SituacaoMatriculaEnum::MATRICULADO && $colunas > 0) {
            $tamanhoColuna = ($colunas + $meses->colunasEmBranco) * $meses->tamanhoColuna;
            $this->imprimeSituacaoAluno($alunoDiarioClasse, $tamanhoColuna);
        }

        if ($colunas === 0) {
            $this->imprimeColunasEmBranco($meses->colunasEmBranco, $meses->tamanhoColuna);
        }
    }

    private function imprimeColunasEmBranco($colunasEmBranco, $tamanhoColuna, $cabecalho = false)
    {
        for ($i = 0; $i < $colunasEmBranco; $i++) {
            $this->imprimePontoGrade($tamanhoColuna, $cabecalho);
        }
    }

    /**
     * Calcula largura das colunas após a grade de frequência
     */
    private function calcularColunaOutros()
    {
        $this->larguraColunaOutros = 0;
        if ($this->exibirColunasAvaliacaoManual) {
            $this->larguraColunaOutros += ($this->larguraColunaPadrao * 4);
        }
        if ($this->exibirColunaFalta) {
            $this->larguraColunaOutros += $this->larguraColunaPadrao;
        }
        if ($this->exibirNumeroAlunoAposGrade) {
            $this->larguraColunaOutros += $this->larguraColunaNumero;
        }
        if ($this->exibirColunaTotalFalta) {
            $this->larguraColunaOutros += $this->larguraColunaTotalFaltas;
        }
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @param $imprimirEtapaAluno
     */
    private function emitirSegundaPagina(DadosDiarioClasse $dadosDiarioClasse, $imprimirEtapaAluno, $request = null)
    {
        $this->imprimeSubcabecalhoSegundaPagina($dadosDiarioClasse);
        $alunosDiarioClasses = $dadosDiarioClasse->getAlunos();
        $alunosImpressos = 1;
        if ($this->modelo->getValue() !== 1) {
            $periodo = $dadosDiarioClasse->getAvaliacaoPeriodica()->getPeriodoAvaliacao();
            $calendario = CalendarioRepository::getCalendarioByCodigo($dadosDiarioClasse->getCalendario()->getCodigo());
            $periodoCalendario = $calendario->getPeriodoCalendarioPorPeriodoAvaliacao($periodo);
            $dataFimPeriodoCalendario = $periodoCalendario->getDataTermino();
        }

        foreach ($alunosDiarioClasses as $alunoDiarioClasse) {
            if ($this->modelo->getValue() !== 1) {
                $dataMatriculaAluno = $alunoDiarioClasse->getMatricula()->getDataMatricula();
                $dataFimComparacao = $dataFimPeriodoCalendario;

                if (!empty($this->dataCorte)) {
                    $dataCorteFormatada = DBDate::create($this->dataCorte);
                    $dataFimComparacao = $dataCorteFormatada;
                }

                if ($dataMatriculaAluno > $dataFimComparacao) {
                    continue;
                }
            }

            if ($alunosImpressos == $this->numeroAlunosPagina) {
                $alunosImpressos = 1;
                $this->assinaturaSegundaPagina();
                $this->cabecalho($dadosDiarioClasse, $request);
                $this->imprimeSubcabecalhoSegundaPagina($dadosDiarioClasse);
            }
            $alunosImpressos++;
            $this->imprimeAlunoSegundaPagina($dadosDiarioClasse, $alunoDiarioClasse, $imprimirEtapaAluno);
        }
        while ($alunosImpressos < $this->numeroAlunosPagina) {
            $this->imprimeLinhaEmBrancoSegundaPagina($dadosDiarioClasse);
            $alunosImpressos++;
        }

        $this->assinaturaSegundaPagina();
    }

    private function imprimeSubcabecalhoSegundaPagina(DadosDiarioClasse $dadosDiarioClasse)
    {
        $this->SetFont("arial", 'B', 7);
        $this->imprimeNumero("Nº");
        $this->imprimeColunaNome("Nome do Aluno");
        $this->imprimeSexo("S");
        $this->imprimeNascimento("Nascimento");
        $this->imprimeIdade("I");
        $this->imprimeResultadoAnterior("RA");
        $this->imprimeCodigo("Código");

        $avaliacoes = $dadosDiarioClasse->getProcedimentoAvaliacaoTurma()->getElementosAvaliacoesAnteriores(
            $dadosDiarioClasse->getAvaliacaoPeriodica()
        );

        foreach ($avaliacoes as $avaliacao) {
            $descricaoAbreviada = $avaliacao->getPeriodoAvaliacao()->getDescricaoAbreviada();
            $this->Cell($this->larguraColunaNota, 4, $descricaoAbreviada, 1, 0, "C");
        }

        if ($this->exibirNotaParcial($dadosDiarioClasse)) {
            $this->Cell($this->larguraColunaNota, 4, 'NP', 1, 0, "C");
        }

        if ($this->exibirNotaProjetada($dadosDiarioClasse)) {
            $this->Cell($this->larguraColunaNota, 4, 'P', 1, 0, "C");
        }

        // avaliação do período
        $label = $dadosDiarioClasse->getProcedimentoAvaliacaoRegencia()->getFormaAvaliacao()->getTipo();
        $this->Cell($this->larguraColunaNota, 4, $label, 1, 0, "C");
        $this->imprimeFaltas('F');
        $this->imprimeFaltasAbonadas('FA');
        $this->imprimeTotalFaltas('TF');

        if ($this->exibirParecer) {
            $this->Cell($this->larguraColunaNota, 4, 'Parecer', 1, 0, "C");
        }

        $wExtra = $this->larguraPagina - $this->getX() + 13;
        $this->quantidadeColunasExtras = floor($wExtra / $this->larguraColunaPadrao);
        $this->tamanhoColunasExtras = ($wExtra / $this->quantidadeColunasExtras);

        $this->imprimeColunasExtras();

        $this->Ln();
        $this->SetFont('Arial', '', $this->tamanhoFonteGrade);
    }

    /**
     * Verifica se devemos exibir a Nota Parcial
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @return bool
     */
    private function exibirNotaParcial(DadosDiarioClasse $dadosDiarioClasse)
    {
        if ($dadosDiarioClasse->getAvaliacaoPeriodica()->getFormaDeAvaliacao()->getTipo() == 'NOTA' &&
            $dadosDiarioClasse->getAvaliacaoPeriodica()->getOrdemSequencia() > 2) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se devemos exibir a Nota Projetada
     * @param DadosDiarioClasse $dadosDiarioClasse
     * @return bool
     */
    private function exibirNotaProjetada(DadosDiarioClasse $dadosDiarioClasse)
    {
        if ($dadosDiarioClasse->getAvaliacaoPeriodica()->getFormaDeAvaliacao()->getTipo() == 'NOTA' &&
            $dadosDiarioClasse->getAvaliacaoPeriodica()->getOrdemSequencia() > 2 &&
            $dadosDiarioClasse->getAvaliacaoPeriodica() === $dadosDiarioClasse->getUltimoPeriodoControleFrequencia()
        ) {
            return true;
        }
        return false;
    }

    private function assinaturaSegundaPagina()
    {
        $this->SetFont('arial', 'b', 7);
        $texto = implode(" | ", $this->legendasParaImpressao());
        $this->Cell($this->larguraPagina, 5, $texto, 1, 1, "L");
        $str = "Assinatura do Professor ___/___/_______ Por " . str_repeat('_', 100);
        $this->Cell($this->larguraPagina, 4, $str, 1, 1);
    }

    /**
     * Verifica quais legendas devem aparecer, conforme preenchido na tela
     * @return array contendo as legendas que serão impressa no relatório
     */
    private function legendasParaImpressao()
    {
        $legendas['RA'] = $this->exibirResultadoAnterior;
        $legendas['TF'] = $this->exibirTotalFaltas;
        $legendas['I'] = $this->exibirIdade;
        $legendas['S'] = $this->exibirSexo;
        $legendas['FA'] = $this->exibirFaltasAbonadas;

        $itensLegendaRA = "(A-Aprovado R-Reprovado T-Transferido C-Cancelado E-Evadido)";
        $descricaoLegenda['RA'] = "RA - Resultado Anterior {$itensLegendaRA}";
        $descricaoLegenda['TF'] = "TF - Total de Faltas";
        $descricaoLegenda['I'] = "I - Idade";
        $descricaoLegenda['S'] = "S - Sexo";
        $descricaoLegenda['FA'] = "FA - Faltas Abonadas";

        $legendasImprimir = array();

        foreach ($legendas as $sigla => $legenda) {
            if ($legenda) {
                $legendasImprimir[$sigla] = $descricaoLegenda[$sigla];
            }
        }

        $legendasImprimir['F'] = "F - Faltas";
        $legendasImprimir['AMP'] = "AMP - Amparado";
        $legendasImprimir['NP'] = "NP - Nota Parcial";
        $legendasImprimir['*'] = "* - Nota Externa";

        return $legendasImprimir;
    }

    private function imprimeAlunoSegundaPagina(
        DadosDiarioClasse $dadosDiarioClasse,
        Model\AlunoDiarioClasse $alunoDiarioClasse,
        $imprimirEtapaAluno
    ) {
        $this->imprimeNumero($alunoDiarioClasse->getNumero());
        $this->imprimeValoreColunaNome($alunoDiarioClasse, $imprimirEtapaAluno);

        $matricula = $alunoDiarioClasse->getMatricula();
        $this->imprimeSexo($alunoDiarioClasse->getSexo());
        $this->imprimeNascimento($alunoDiarioClasse->getDataNascimento()->format('d/m/Y'));
        $this->imprimeIdade($alunoDiarioClasse->getIdadeEmAnos());
        $this->imprimeResultadoAnterior($matricula->getResultadoFinalAnterior());
        $this->imprimeCodigo($alunoDiarioClasse->getCodigo());

        if ($alunoDiarioClasse->getSituacao()->value() === SituacaoMatriculaEnum::MATRICULADO) {
            $ano = $dadosDiarioClasse->getCalendario()->getAno();
            $avaliacoes = $dadosDiarioClasse->getProcedimentoAvaliacaoTurma()->getElementosAvaliacoesAnteriores(
                $dadosDiarioClasse->getAvaliacaoPeriodica()
            );

            $avaliadoParecer = $matricula->isAvaliadoPorParecer();

            foreach ($avaliacoes as $avaliacaoPeriodica) {
                $disciplina = DisciplinaRepository::getDisciplinaByCodigo(
                    $dadosDiarioClasse->getDisciplina()->getCodigo()
                );

                $aproveitamento = $matricula
                    ->getDiarioDeClasse()
                    ->getAvaliacoesPorDisciplina($disciplina, $avaliacaoPeriodica);

                $this->imprimeAvaliacaoAlunoPeriodo($aproveitamento, $ano, $avaliadoParecer);
            }

            if ($this->exibirNotaParcial($dadosDiarioClasse)) {
                $this->imprimeNotaParcial($dadosDiarioClasse, $alunoDiarioClasse);
            }

            if ($this->exibirNotaProjetada($dadosDiarioClasse)) {
                $this->Cell($this->larguraColunaNota, 4, 'P', 1, 0, "C");
            }

            // avaliação do período
            $avaliacaoPeriodo = $alunoDiarioClasse->getDiarioAvaliacaoDisciplina()->getAvaliacoesPorOrdem(
                $dadosDiarioClasse->getAvaliacaoPeriodica()->getOrdemSequencia()
            );

            $this->imprimeAvaliacaoAlunoPeriodo($avaliacaoPeriodo, $ano, $avaliadoParecer);

            $faltas = count($alunoDiarioClasse->getFaltas());
            $faltasAbonadas = $alunoDiarioClasse->getFaltasAbonadasNoPeriodo();
            $this->imprimeFaltas($faltas);
            $this->imprimeFaltasAbonadas($faltasAbonadas);

            $total = $faltas - $faltasAbonadas;
            $this->imprimeTotalFaltas($total);

            $this->imprimeColunaParecer();
            $this->imprimeColunasExtras();
        } else {
            $tamanho = $this->GetRightMargin() + ($this->larguraPagina - $this->GetX());
            $this->imprimeSituacaoAluno($alunoDiarioClasse, $tamanho);
        }

        $this->Ln();
    }

    private function imprimeColunasExtras()
    {
        for ($i = 1; $i < $this->quantidadeColunasExtras; $i++) {
            $this->Cell($this->tamanhoColunasExtras, 4, '', 1, 0, "C");
        }
    }

    /**
     * @param AlunoDiarioClasse $alunoDiarioClasse
     * @param int $tamanhoColuna
     * @throws Exception
     */
    private function imprimeSituacaoAluno(AlunoDiarioClasse $alunoDiarioClasse, $tamanhoColuna, $situacao = null)
    {
        $this->SetFont('arial', 'B', $this->tamanhoFonteGrade);
        $stringSituacao = "";
        if (!is_null($situacao)) {
            if (!($situacao instanceof SituacaoMatriculaEnum)) {
                $stringSituacao = "";
            } else {
                $stringSituacao = $tamanhoColuna < 20 ?
                    (new SituacaoMatriculaEnum($situacao))->sigla() : (new SituacaoMatriculaEnum($situacao))->value();
            }
        }
        $string = is_null($situacao) ?
            $alunoDiarioClasse->getSituacao()->value() : $stringSituacao;

        if ($tamanhoColuna < 20) {
            $string = is_null($situacao) ?
                $alunoDiarioClasse->getSituacao()->sigla() : $stringSituacao;
        }
        $this->cellAdapt(
            $this->tamanhoFonteGrade,
            $tamanhoColuna,
            4,
            $string,
            1,
            0,
            'C'
        );
        $this->SetFont('arial', '', $this->tamanhoFonteGrade);
    }

    /**
     * @param AvaliacaoAproveitamento $avaliacaoAproveitamento
     * @param bool $avaliadoParecer
     * @param $ano
     */
    private function imprimeAvaliacaoAlunoPeriodo(
        AvaliacaoAproveitamento $avaliacaoAproveitamento,
        $ano,
        $avaliadoParecer = false
    ) {
        $nota = $avaliacaoAproveitamento->getValorAproveitamento()->getAproveitamento();
        $nota = ArredondamentoNota::arredondar($nota, $ano);

        if (!$avaliacaoAproveitamento->temAproveitamentoMinimo()) {
            $this->SetFont("arial", 'B', $this->tamanhoFonteGrade);
        }

        if ($avaliacaoAproveitamento->getElementoAvaliacao()->getFormaDeAvaliacao()->getTipo() == 'PARECER') {
            if ($nota != '') {
                $this->SetFont("arial", '', 5.5);
                $nota = 'PARECER';
            }
        }


        if ($avaliacaoAproveitamento->isAvaliacaoExterna() && $nota != '') {
            $nota = "*{$nota}";
        }

        if ($avaliadoParecer && !empty($nota)) {
            $nota = "PD";
        }

        if ($avaliacaoAproveitamento->isAmparado()) {
            $nota = 'AMP';
        }

        $this->Cell($this->larguraColunaNota, 4, $nota, 1, 0, 'C');
        $this->SetFont("arial", '', $this->tamanhoFonteGrade);
    }

    private function imprimeNotaParcial(
        DadosDiarioClasse $dadosDiarioClasse,
        Model\AlunoDiarioClasse $alunoDiarioClasse
    ) {
        $avaliacaoPeriodica = $dadosDiarioClasse->getAvaliacaoPeriodica();
        $aproveitamentoMinimo = $avaliacaoPeriodica->getFormaDeAvaliacao()->getAproveitamentoMinino();
        $elementoAvaliacaoAnterior = $dadosDiarioClasse->getProcedimentoAvaliacaoTurma()
            ->getElementoAvaliacaoAnterior($avaliacaoPeriodica);

        if (!empty($elementoAvaliacaoAnterior)) {
            $avaliacaoDisciplina = $alunoDiarioClasse->getDiarioAvaliacaoDisciplina();
            $avaliacaoParcial = $avaliacaoDisciplina->getNotaParcial($elementoAvaliacaoAnterior);

            if ($avaliacaoParcial < $aproveitamentoMinimo) {
                $this->SetFont("arial", 'B', 7);
            }

            $this->Cell($this->larguraColunaNota, 4, $avaliacaoParcial, 1, 0, 'C');
            $this->SetFont("arial", '', $this->tamanhoFonteGrade);
        } else {
            $this->cell($this->larguraColunaNota, 4, '', 1, 0);
        }
    }


    /**
     * Escreve a coluna parecer
     */
    private function imprimeColunaParecer()
    {
        if ($this->exibirParecer) {
            for ($i = 0; $i < 3; $i++) {
                $this->Cell($this->larguraColunaPadrao, 4, '', 1, 0, 'C');
            }
        }
    }

    private function imprimeSexo($valor = '')
    {
        if ($this->exibirSexo) {
            $this->Cell($this->larguraColunaPadrao, 4, $valor, 1, 0, "C");
        }
    }

    private function imprimeNascimento($valor = '')
    {
        if ($this->exibirNascimento) {
            $this->Cell($this->larguraColunaDataNascimento, 4, $valor, 1, 0, "C");
        }
    }

    private function imprimeIdade($valor = '')
    {
        if ($this->exibirIdade) {
            $this->Cell($this->larguraColunaPadrao, 4, $valor, 1, 0, "C");
        }
    }

    private function imprimeResultadoAnterior($valor = '')
    {
        if ($this->exibirResultadoAnterior) {
            $this->Cell($this->larguraColunaPadrao, 4, $valor, 1, 0, "C");
        }
    }

    private function imprimeCodigo($valor = '')
    {
        if ($this->exibirCodigo) {
            $this->Cell($this->larguraColunaNota, 4, $valor, 1, 0, "C");
        }
    }

    private function imprimeLinhaEmBrancoSegundaPagina(DadosDiarioClasse $dadosDiarioClasse)
    {
        $this->imprimeNumero();
        $this->imprimeColunaNome();
        $this->imprimeSexo();
        $this->imprimeNascimento();
        $this->imprimeIdade();
        $this->imprimeResultadoAnterior();
        $this->imprimeCodigo();

        $avaliacoes = $dadosDiarioClasse->getProcedimentoAvaliacaoTurma()->getElementosAvaliacoesAnteriores(
            $dadosDiarioClasse->getAvaliacaoPeriodica()
        );

        foreach ($avaliacoes as $avaliacao) {
            $this->Cell($this->larguraColunaNota, 4, '', 1, 0, "C");
        }

        if ($this->exibirNotaParcial($dadosDiarioClasse)) {
            $this->Cell($this->larguraColunaNota, 4, '', 1, 0, "C");
        }

        if ($this->exibirNotaProjetada($dadosDiarioClasse)) {
            $this->Cell($this->larguraColunaNota, 4, '', 1, 0, "C");
        }
        $this->Cell($this->larguraColunaNota, 4, '', 1, 0, "C");
        $this->imprimeFaltas();
        $this->imprimeFaltasAbonadas();
        $this->imprimeTotalFaltas();
        $this->imprimeColunaParecer();
        $this->imprimeColunasExtras();
        $this->Ln();
    }

    private function imprimeNumero($valor = '')
    {
        $this->Cell($this->larguraColunaNumero, 4, $valor, 1, 0, "C");
    }

    private function imprimeColunaNome($valor = '')
    {
        $this->cellAdapt($this->tamanhoFonteGrade, $this->larguraColunaNome, 4, $valor, 1);
    }

    public function imprimeValoreColunaNome($alunoDiarioClasse, $imprimirEtapaAluno = false)
    {
        $larguraColunaNome = $this->larguraColunaNome;
        if ($imprimirEtapaAluno) {
            $larguraColunaNome -= 7;
        }

        $fonte = $this->tamanhoFonteGrade;
        $this->cellAdapt($fonte, $larguraColunaNome, 4, $alunoDiarioClasse->getNome(), 1);

        if ($imprimirEtapaAluno) {
            $sigla = $alunoDiarioClasse->getMatricula()->getEtapaDeOrigem()->getNomeAbreviado();
            $this->cellAdapt($fonte, 7, 4, $sigla, 1);
        }
    }

    private function imprimeFaltas($faltas = '')
    {
        if ($this->registroManual) {
            $faltas = str_replace("0", " ", $faltas);
        }
        $this->Cell($this->larguraColunaPadrao, 4, "{$faltas}", 1, 0, "C");
    }

    private function imprimeFaltasAbonadas($faltasAbonadas = '')
    {
        if ($this->exibirFaltasAbonadas) {
            $this->Cell($this->larguraColunaPadrao, 4, "{$faltasAbonadas}", 1, 0, "C");
        }
    }

    private function imprimeTotalFaltas($total = '')
    {
        if ($this->exibirTotalFaltas) {
            if ($this->registroManual) {
                $total = str_replace("0", " ", $total);
            }
            $this->Cell($this->larguraColunaPadrao, 4, "{$total}", 1, 0, "C");
        }
    }

    /**
     * @param DadosDiarioClasse $dadosDiarioClasse
     */
    private function recalculaColunaOutros(DadosDiarioClasse $dadosDiarioClasse)
    {
        $this->calcularColunaOutros();

        $turma = TurmaRepository::getTurmaByCodigo($dadosDiarioClasse->getTurma()->getCodigo());
        $etapas = $dadosDiarioClasse->getTurma()->getEtapas();
        $this->quantidadeDisciplinasTurmaGlobalizada = count($turma->getDisciplinasPorEtapa($etapas[0]));
        $this->larguraColunaOutros += $this->quantidadeDisciplinasTurmaGlobalizada * $this->larguraColunaPadrao;
    }

    /**
     * @param $ultimaPagina
     * @param bool $titulo
     * @param TurmaDiarioClasse $turma
     * @throws Exception
     */
    private function escreverColunasTurmaGlobalizada($ultimaPagina, TurmaDiarioClasse $turma = null, $titulo = false)
    {
        if (!$ultimaPagina || !$this->turmaGlobalizada) {
            return;
        }

        if ($titulo) {
            $turmaModel = TurmaRepository::getTurmaByCodigo($turma->getCodigo());
            $regencias = $turmaModel->getDisciplinasPorEtapa($turma->getEtapas()[0]);
            foreach ($regencias as $regencia) {
                $abreviatura = $regencia->getDisciplina()->getAbreviatura();
                $this->cellAdapt($this->tamanhoFonteGrade, $this->larguraColunaPadrao, 4, $abreviatura, 1, 0, "C");
            }
        } else {
            for ($i = 0; $i < $this->quantidadeDisciplinasTurmaGlobalizada; $i++) {
                $this->Cell($this->larguraColunaPadrao, 4, "", 1, 0, "C");
            }
        }
    }

    /**
     * @param $ultimaPagina
     */
    private function imprimeAlunoAmparado($ultimaPagina)
    {
        $largura = $this->getWidthGrade($ultimaPagina);
        $this->Cell($largura, 4, "AMPARADO", 1, 0, "C");
    }
}
