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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/DBDate.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/exceptions/DBException.php"));

$oGet = db_utils::postMemory($_GET);

$oGet->obs1 = base64_decode($oGet->obs1);
$oFiltros   = new stdClass();

/**
 * Forma de apresentacao dos pareceres padronizados
 *    true => 'C' - Concatenar pareceres lado a lado
 *   false => 'L' - Listar pareceres um abaixo do outro
 * @var boolean
 */
$oFiltros->lConcatenaParecerPadrao = $oGet->padraotipo == 'C' ? true : false;

/**
 * Informa se eh um parecer unico 'PU'
 *   'yes' => true
 *   'no'  => false
 * @var boolean
 */
$oFiltros->lParecerUnico = $oGet->punico == 'yes' ? true : false;

/**
 * Tipo de avaliacao concatenado com o codigo do periodo
 * Ex.: A|219
 *      A => Avaliacao
 *      R => Resultado
 * @var string
 */
$sPeriodo                 = explode("|", $oGet->periodo);
$oFiltros->sTipoAvaliacao = $sPeriodo[0];
$oFiltros->iPeriodo       = $sPeriodo[1];

/**
 * Informa se deve ser impresso o nome do professor conselheiro
 * 'S' | 'N'
 * @var string
 */
$oFiltros->lAssinatura = $oGet->assinaturaregente == 'S' ? true : false;

/**
 * Array com o codigo da regencia da disciplina
 * @var array
 */
$aDisciplinas = explode(",", $oGet->disciplinas);

/**
 * Array com o código dos alunos selecionados para impressao
 * Ex.: 49740,49741,49742,49743
 * @var string
 */
$aAlunos = explode(",", $oGet->alunos);

/**
 * Observacao a ser impressa no boletim
 * @var text
 */
$oFiltros->sObservacao = $oGet->obs1;

/**
 * Instancia de turma, pelo codigo passado como parametro (turmaserieregimemat)
 */
$oFiltros->oTurma = TurmaRepository::getTurmaByCodigoTurmaSerieRegimeMat($oGet->turma);

/**
 * Instancia de etapa, pelo codigo passado como parametro (turmaserieregimemat)
 */
$oFiltros->oEtapa = EtapaRepository::getEtapaByCodigoTurmaSerieRegimeMat($oGet->turma);

if ( !$oFiltros->oTurma instanceof Turma || !$oFiltros->oEtapa instanceof Etapa ) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Selecione a turma.');
}

/**
 * Variavel que recebe o elemento de uma avaliacao
 */
$oFiltros->oElementoAvaliacao = null;

$oFiltros->iLinha = 4;
$oFiltros->iLarguraRetangulo = 200;

/**
 * Diretor Escola
 */
$oFiltros->aDiretor = $oFiltros->oTurma->getEscola()->getDiretor();

/**
 * Professor conselheiro da Turma
 */
$oFiltros->sProfessorConselheiro = "";
if ($oFiltros->oTurma->getProfessorConselheiro() && $oFiltros->oTurma->getProfessorConselheiro()->getNome() != '') {
  $oFiltros->sProfessorConselheiro = $oFiltros->oTurma->getProfessorConselheiro()->getNome();
}

/**
 * Monta a data por extenso
 */
$oDiaAtual  = new DBDate(date("Y-m-d"));
$sMunicipio = $oFiltros->oTurma->getCalendario()->getEscola()->getDepartamento()->getInstituicao()->getMunicipio();
$sData      = $oDiaAtual->getDia() . " de " . DBDate::getMesExtenso((int)$oDiaAtual->getMes()) . " de " .$oDiaAtual->getAno();

$oFiltros->sDataExtenso = "{$sMunicipio}, {$sData}.";

/**
 * Ano de execução do calendário
 */
$iAno = $oFiltros->oTurma->getCalendario()->getAnoExecucao();

/**
 * Array com dos dados dos alunos que serão impressos no relatório
 */
$aAlunosImpressao = array();


foreach ($aAlunos as $iMatricula) {

  $oMatricula = MatriculaRepository::getMatriculaByCodigo($iMatricula);

  $oDadosAluno                  = new stdClass();
  $oDadosAluno->iCodigo         = $oMatricula->getAluno()->getCodigoAluno();
  $oDadosAluno->sNome = empty($oMatricula->getAluno()->getNomeSocial()) ||
    is_null($oMatricula->getAluno()->getNomeSocial()) ?
    $oMatricula->getAluno()->getNome() : $oMatricula->getAluno()->getNomeSocial();
  $oDadosAluno->iMatricula      = $oMatricula->getCodigo();

  $oDadosAluno->sResultadoFinal = ResultadoFinal($oMatricula->getCodigo(),
                                                 $oMatricula->getAluno()->getCodigoAluno(),
                                                 $oMatricula->getTurma()->getCodigo(),
                                                 $oMatricula->getSituacao(),
                                                 $oMatricula->isConcluida() ? 'S' : 'N');

  $oDadosAluno->aDisciplinas = array();

  foreach ($aDisciplinas as $iDisciplina) {

    $oRegencia        = RegenciaRepository::getRegenciaByCodigo($iDisciplina);
    $oDadosDisciplina = new stdClass();

    $oDadosDisciplina->iRegencia    = $oRegencia->getCodigo();
    $oDadosDisciplina->sAbreviatura = $oRegencia->getDisciplina()->getAbreviatura();
    $oDadosDisciplina->sDisciplina  = $oRegencia->getDisciplina()->getNomeDisciplina();
    $oDadosDisciplina->aAvaliacoes  = array();
    $oDadosDisciplina->iTotalFaltas = 0;

    db_inicio_transacao();
    $oDiarioDeClasse   = $oMatricula->getDiarioDeClasse();
    $oDiarioDisciplina = $oMatricula->getDiarioDeClasse()->getDisciplinasPorRegencia($oRegencia);
    db_fim_transacao();

    /**
     * Percorremos as avaliações da diciplina buscando os dados da avaliação
     */
    foreach ($oDiarioDisciplina->getAvaliacoes() as $oAvaliacao) {
      $oDadosAvaliacao = new stdClass();
      $iFaltasAbonadas = 0;
      $iFaltasPeriodo  = 0;
      $iOrdem          = $oAvaliacao->getElementoAvaliacao()->getOrdemSequencia();

      /**
       * Verificamos se trata-se de parecer unico, calculando o total de dias letivos e faltas. Caso contrario, o calculo
       * sera de acordo com a regencia
      */
      $oDadosAvaliacao->iAulasDadas = "";
      $sPeriodoAvaliacao            = "Parecer Final";

      if ( !$oAvaliacao->getElementoAvaliacao()->isResultado() ) {

        $sPeriodoAvaliacao  = "Período de Avaliação: ";
        $iFaltasAbonadas    = $oAvaliacao->getFaltasAbonadas();
        $sPeriodoAvaliacao .= $oAvaliacao->getElementoAvaliacao()->getPeriodoAvaliacao()->getDescricao();

        if ($oFiltros->lParecerUnico) {

          foreach ($oDiarioDeClasse->getDisciplinas() as $oDisciplinaDiario) {

            $iFaltasPeriodo                 += $oDisciplinaDiario->getTotalFaltasPorPeriodo($oAvaliacao->getElementoAvaliacao() ->getPeriodoAvaliacao());
            $oDadosDisciplina->iTotalFaltas += $oDisciplinaDiario->getTotalFaltasPorPeriodo($oAvaliacao->getElementoAvaliacao()->getPeriodoAvaliacao());
            $oDadosAvaliacao->iAulasDadas   += $oDisciplinaDiario->getRegencia()->getTotalDeAulasNoPeriodo($oAvaliacao->getElementoAvaliacao()->getPeriodoAvaliacao());
          }
        } else {

          $iFaltasPeriodo               = $oDiarioDisciplina->getTotalFaltasPorPeriodo($oAvaliacao->getElementoAvaliacao()
                                                                                                  ->getPeriodoAvaliacao());
          $oDadosDisciplina->iTotalFaltas += $oDiarioDisciplina->getTotalFaltasPorPeriodo($oAvaliacao->getElementoAvaliacao()
                                                                                                     ->getPeriodoAvaliacao());
          $oDadosAvaliacao->iAulasDadas = $oRegencia->getTotalDeAulasNoPeriodo($oAvaliacao->getElementoAvaliacao()->getPeriodoAvaliacao());
        }
      }

      if ($oAvaliacao->getElementoAvaliacao()->isResultado()) {
        $iFaltasPeriodo = $oDadosDisciplina->iTotalFaltas;
      }

      $oDadosAvaliacao->iFaltas           = $iFaltasPeriodo - $iFaltasAbonadas;
      $oDadosAvaliacao->sPeriodo          = $sPeriodoAvaliacao;
      $oDadosAvaliacao->lResultado        = $oAvaliacao->getElementoAvaliacao()->isResultado();
      $oDadosAvaliacao->lAvaliacaoExterna = $oAvaliacao->isAvaliacaoExterna();
      $oDadosAvaliacao->lAmparado         = $oAvaliacao->isAmparado();
      $oDadosAvaliacao->lConvertido       = $oAvaliacao->isConvertido();
      $oDadosAvaliacao->sObservacao       = $oAvaliacao->getObservacao();
      $oDadosAvaliacao->sFormaAvaliacao   = $oAvaliacao->getElementoAvaliacao()->getFormaDeAvaliacao()->getDescricao();

      $oDadosAvaliacao->oParecer = LancamentoAvaliacaoAluno::getParecer($oMatricula, $oRegencia, $iOrdem);

      if ($oFiltros->lParecerUnico) {
          $oDadosAvaliacao->aParecer = [];
          foreach ($oDiarioDeClasse->getDisciplinas() as $oDisciplinaDiario) {
              $oDadosAvaliacao->aParecer[] = LancamentoAvaliacaoAluno::getParecer($oMatricula, $oDisciplinaDiario->getRegencia(), $iOrdem);
          }
      }

      $oDadosDisciplina->aAvaliacoes[$iOrdem] = $oDadosAvaliacao;
    }

    $oDadosAluno->aDisciplinas[] = $oDadosDisciplina;

  }

  $aAlunosImpressao[] = $oDadosAluno;
}

$oPdf = new Pdf();
$oPdf->init(false);
$oPdf->AliasNbPages();
$oPdf->exibeHeader(true, \Fpdf\Pdf::HEADER_ESCOLA);
$oPdf->setExibeBrasao(true);
$oPdf->setFillColor(220);
$oPdf->SetAutoPageBreak(true, 10);

/** ***************************************************************************************************************** *
 ** ************************************** IMPRESSÃO DOS DADOS ****************************************************** *
 ** ***************************************************************************************************************** */

$sCurso  = $oFiltros->oTurma->getBaseCurricular()->getCurso()->getCodigo();
$sCurso .= " - " . $oFiltros->oTurma->getBaseCurricular()->getCurso()->getNome();

$oPdf->addTitulo("BOLETIM POR PARECER DESCRITIVO");
$oPdf->addTitulo("Aluno: ", "aluno");
$oPdf->addTitulo("Curso: {$sCurso}");
$oPdf->addTitulo("Calendário: " . $oFiltros->oTurma->getCalendario()->getDescricao());
$oPdf->addTitulo("Etapa: " . $oFiltros->oEtapa->getNome());
$oPdf->addTitulo("Turma: " . $oFiltros->oTurma->getDescricao());
$oPdf->addTitulo("Matricula: ", "matricula");

foreach ($aAlunosImpressao as $oAluno) {

  $oPdf->editTitleByKey("aluno", "Aluno: $oAluno->sNome");
  $oPdf->editTitleByKey("matricula", "Matricula: $oAluno->iMatricula");

  $oPdf->AddPage();
  foreach ($oAluno->aDisciplinas as $oDisciplina) {

    foreach ($oDisciplina->aAvaliacoes as $oPeriodoAvaliacao) {
      $oPdf->SetFont("Arial", "B", 8);
      $oPdf->Cell(192, $oFiltros->iLinha, $oPeriodoAvaliacao->sPeriodo, 1, 1, "C", 1);
      if ($oFiltros->lParecerUnico) {
        $oPdf->Cell(192, $oFiltros->iLinha, "PARECER ÚNICO", 1, 1, "L");
      }

      $oPeriodoAvaliacao->iFaltas = empty($oPeriodoAvaliacao->iFaltas) ? "" : $oPeriodoAvaliacao->iFaltas;

      $sAulasDadas = "Aulas Dadas: {$oPeriodoAvaliacao->iAulasDadas}";
      if ($oPeriodoAvaliacao->lResultado) {
        $sAulasDadas = "";
      }

      $oPdf->Cell(96, $oFiltros->iLinha, "Faltas: {$oPeriodoAvaliacao->iFaltas}", "BLT", 0, "L");
      $oPdf->Cell(96, $oFiltros->iLinha, $sAulasDadas,                            "BRT", 1, "R");

      $sDisciplina = '';

      if ( !$oFiltros->lParecerUnico ) {

        $sDisciplina = str_repeat(" ", 10) . "DISCIPLINA: {$oDisciplina->sDisciplina}";
        $oPdf->Cell(192, $oFiltros->iLinha,  $sDisciplina, 1, 1, "L");
      }

      // Imprime o parecer padronizado da disciplina
      imprimeParecerPadronizado($oPdf, $oFiltros, $oPeriodoAvaliacao);
      // Imprime o parecer da disciplina
    	imprimeParecer($oPdf, $oFiltros, $oPeriodoAvaliacao);
      // imprime observação lançada para o aluno
    	imprimeObservacaoAluno($oPdf, $oFiltros, $oPeriodoAvaliacao);
      $oPdf->ln(2);
    }
  }
  // Imprime o resultado final da disciplina
  $oPdf->SetFont("Arial", "B", 8);
  $oPdf->Cell(192, 4, "Resultado Final: {$oAluno->sResultadoFinal}", 1, 1, "L", 1);
  $oPdf->ln(2);

  //Imprime a Observação informada na tela de filtros
  imprimeObservacaoGeral($oPdf, $oFiltros);
  //Imprime assinatura
  imprimeAssinatura($oPdf, $oFiltros);

}

/**
 * Imprime os dados do parecer padronizado
 * @param Pdf $oPdf
 * @param stdClass $oFiltros
 * @param stdClass $oPeriodoAvaliacao
 */
function imprimeParecerPadronizado(Pdf $oPdf, $oFiltros, $oPeriodoAvaliacao) {

    foreach ($oPeriodoAvaliacao->aParecer as $oParecer) {
        if (!empty($oParecer->sParecerPadronizado)) {
            $iLinhasParecerPadronizado = 0;
            if ($oFiltros->lConcatenaParecerPadrao) {
                $iLinhasParecerPadronizado = $oPdf->NbLines(192, $oParecer->sParecerPadronizado);
            } else {
                $iLinhasParecerPadronizado = count(explode("**", $oParecer->sParecerPadronizado));
            }
            $iLinhasParecerPadronizado += 2;  //linhas de header do parecer padronizado

            validaQuebraPagina($oPdf, $oFiltros->iLinha, "", $iLinhasParecerPadronizado);

            $oPdf->SetFont('arial', 'B', 8);
            $oPdf->cell(192, 4, "Parecer Padronizado:", 1, 1, "C");
            $oPdf->SetFont('arial', '', 8);
            if ($oFiltros->lConcatenaParecerPadrao) {
                $oPdf->MultiCell(192, 4, $oParecer->sParecerPadronizado, 1, "L");
            } else {

                $oPdf->SetFont('arial', 'B', 8);
                $oPdf->Cell(192, 4, "Seq - Parecer => Legenda", 1, 1, "L");
                $oPdf->SetFont('arial', '', 8);
                $aPareceres = explode("**", $oParecer->sParecerPadronizado);

                foreach ($aPareceres as $sParecer) {
                    $oPdf->cell(192, 4, trim($sParecer), 1, 1, "L");
                }
            }
        }
    }
}


/**
 * Imprime os dados do parecer
 * @param Pdf $oPdf
 * @param stdClass $oFiltros
 * @param stdClass $oPeriodoAvaliacao
 */
function imprimeParecer(Pdf $oPdf, $oFiltros, $oPeriodoAvaliacao) {

    $iLinhasParecer = 5;
    $sParecerIsEmpty = true;
    $oPdf->SetFont("Arial", "B", 8);
    $oPdf->Cell(192, $oFiltros->iLinha, "Parecer", 1, 1, "C");
    $oPdf->SetFont("Arial", "", 8);

    foreach ($oPeriodoAvaliacao->aParecer as $oParecer) {
        validaQuebraPagina($oPdf, $oFiltros->iLinha, $oParecer->sParecer, $iLinhasParecer);
        if (!empty($oParecer->sParecer)) {
            $sParecerIsEmpty = false;
            $oPdf->MultiCell(192, 4, $oParecer->sParecer, 1, "J");
        }
    }

    /**
     * Quando é selecionado apenas uma disciplina, o objeto volta diferente
     * e entra nessa segunda verificação
     */
    if ($sParecerIsEmpty && !empty($oPeriodoAvaliacao->oParecer->sParecer)) {
      validaQuebraPagina($oPdf, $oFiltros->iLinha, $oPeriodoAvaliacao->oParecer->sParecer, $iLinhasParecer);
      $oPdf->MultiCell(192, 4, $oPeriodoAvaliacao->oParecer->sParecer , 1, "J");
      $sParecerIsEmpty = false;
    }

    if ($sParecerIsEmpty) {
        //Imprime as linhas em branco do parecer
        for($i = 1; $i <= 4; $i++) {
            $oPdf->Cell(192, $oFiltros->iLinha, "", 1, 1);
        }
    }
}

/**
 * Imprime as observações lançadas para o aluno no período
 * @param Pdf $oPdf
 * @param stdClass $oFiltros
 * @param stdClass $oPeriodoAvaliacao
 */
function imprimeObservacaoAluno(Pdf $oPdf, $oFiltros, $oPeriodoAvaliacao) {

  if (!empty($oPeriodoAvaliacao->sObservacao)) {

    validaQuebraPagina($oPdf, $oFiltros->iLinha, $oPeriodoAvaliacao->sObservacao);
    $oPdf->SetFont("Arial", "B", 8);
    $oPdf->Cell(192, $oFiltros->iLinha, "Observações", 1, 1, "L", 1);
    $oPdf->SetFont("Arial", "", 8);
    $oPdf->MultiCell(192, $oFiltros->iLinha, $oPeriodoAvaliacao->sObservacao, 1);
  }
}

/**
 *
 * @param Pdf $oPdf
 * @param stdClass $oFiltros
 */
function imprimeObservacaoGeral(Pdf $oPdf, $oFiltros) {

  if (!empty($oFiltros->sObservacao)) {

    validaQuebraPagina($oPdf, $oFiltros->iLinha, $oFiltros->sObservacao);

    $oPdf->SetFont("Arial", "B", 8);
    $oPdf->Cell(192, $oFiltros->iLinha, "Observações", 1, 1, "L", 1);
    $oPdf->SetFont("Arial", "", 8);
    $oPdf->MultiCell(192, $oFiltros->iLinha, $oFiltros->sObservacao, 1);
  }
}

/**
 *
 * @param Pdf $oPdf
 * @param integer $iAlturaLinha
 * @param string  $sString        se <> '' calcula quantas linhas
 * @param integer $iLinhasString  informado quando string vazia e queremos quebrar página
 */
function validaQuebraPagina(Pdf $oPdf, $iAlturaLinha, $sString, $iLinhasString = 0) {

  if (!empty($sString)) {
    $iLinhasString = 1 + $oPdf->NbLines(192, $sString);
  }
  if (($iLinhasString * $iAlturaLinha) + $oPdf->GetY() >= $oPdf->getPageHeight() - 10 && $oPdf->GetY() > 230) {
    $oPdf->AddPage();
  }
}

/**
 * Imprime os campos para assinatura
 * @param Pdf $oPdf
 * @param stdClass            $oFiltros
 */
function imprimeAssinatura(Pdf $oPdf, $oFiltros) {

  if ($oFiltros->lAssinatura) {

    validaQuebraPagina($oPdf, $oFiltros->iLinha, '', 7);

    $oPdf->SetY($oPdf->GetY() +5);
    $oPdf->SetFont("Arial", "", 8);
    $oPdf->Cell(192, $oFiltros->iLinha, $oFiltros->sDataExtenso, 0, 1, "L");
    $oPdf->SetY($oPdf->GetY() + 8);

    $oPdf->Cell(80, 4, "", "B", 0);
    $oPdf->SetX($oPdf->GetX() + 32);
    $oPdf->Cell(80, 4, "", "B", 1);

    $sProfessor = "{$oFiltros->sProfessorConselheiro} \nProfessor";
    $sDiretor   = !empty($oFiltros->aDiretor[0]->sNome) ? $oFiltros->aDiretor[0]->sNome : "";
    $sDiretor   = "{$sDiretor} \nDiretor";

    $iYAntes = $oPdf->GetY();
    $oPdf->MultiCell(80, 4, $sProfessor, 0, "C");
    $oPdf->SetY($iYAntes);
    $oPdf->SetX(122);
    $oPdf->MultiCell(80, 4, $sDiretor, 0, "C");

  }
}

$oPdf->Output();
?>
