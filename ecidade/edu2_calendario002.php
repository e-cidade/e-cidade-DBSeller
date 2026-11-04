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

require_once modification("libs/db_utils.php");
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");

$oGet = db_utils::postMemory($_GET);

$oParametros = new stdClass();

$oParametros->iEscola = $oGet->iEscola;
$oParametros->iCalendario = $oGet->iCalendario;
$oParametros->sNomeCalendario = $oGet->sNomeCalendario;
$oParametros->iAno = $oGet->iAno;

/**
 * @todo talvez não precise
 */
$oEscola = EscolaRepository::getEscolaByCodigo($oParametros->iEscola);

$oCalendarioTurma = CalendarioRepository::getCalendarioByCodigo($oParametros->iCalendario);

$oCalendario = new stdClass;
$oCalendario->periodos_letivos = array();
$oCalendario->data_inicio = $oCalendarioTurma->getDataInicio()->convertTo(DBDate::DATA_EN);
$oCalendario->data_fim = $oCalendarioTurma->getDataFinal()->convertTo(DBDate::DATA_EN);
$oCalendario->observacoes = $oCalendarioTurma->getObs();

$aIntervalosMeses = DBDate::getDatasNoIntervalo(
    $oCalendarioTurma->getDataInicio(),
    $oCalendarioTurma->getDataFinal()
);

$aMeses = [];

foreach ($aIntervalosMeses as $key => $data) {
    $index = $data->getMes() . $data->getAno();
    $aMeses[$index] = (object)[
        "mes" => (int)$data->getMes(),
        "ano" => $data->getAno()
    ];
    unset($aIntervalosMeses[$key]);
}

$aMeses = array_values($aMeses);

$oCalendario->meses = array();
$aPeriodosAula = $oCalendarioTurma->getPeriodos();

$aRecesso = array();

$oInicioPeriodo = null;
$oFimPeriodo = null;

foreach ($aPeriodosAula as $oPeriodosLetivos) {
    $oPeriodo = new stdClass();
    $oPeriodo->nome = $oPeriodosLetivos->getPeriodoAvaliacao()->getDescricaoAbreviada();
    $oPeriodo->data_inicio = $oPeriodosLetivos->getDataInicio()->convertTo(DBDate::DATA_EN);
    $oPeriodo->data_termino = $oPeriodosLetivos->getDataTermino()->convertTo(DBDate::DATA_EN);
    $oCalendario->periodos_letivos[] = $oPeriodo;

    $oInicioPeriodo = $oPeriodo->data_inicio;
    $oFimPeriodo = $oPeriodo->data_termino;
}

foreach ($aMeses as $mes) {
    $sMes = $mes->mes;
    $sAno = $mes->ano;
    $oMes = new stdClass();
    $sDataFinal = "{$sAno}-$sMes-" . cal_days_in_month(CAL_GREGORIAN, $sMes, $sAno);
    $oMes->nome = ucfirst(db_mes($sMes));
    $oMes->dias = array();
    $aDiasNoMes = DBDate::getDatasNoIntervalo(new DBDate("{$sAno}-$sMes-01"), new DBDate($sDataFinal));

    foreach ($aDiasNoMes as $oDiaNoMes) {
        $oDia = new stdClass();
        $oDia->data = $oDiaNoMes->convertTo(DBDate::DATA_EN);
        $oDia->eventos = getEventosDia($oCalendarioTurma, $oDiaNoMes);
        $oDia->dia_letivo = getDiaLetivo($oCalendarioTurma, $oDiaNoMes, $oDia->eventos);
        $oMes->dias[] = $oDia;
    }
    $oCalendario->meses[] = $oMes;
}

function getEventosDia(Calendario $oCalendario, DBDate $oDia)
{
    $aEventos = array();
    foreach ($oCalendario->getEventos() as $oEvento) {
        if ($oEvento->getDataEvento() == $oDia) {
            $oEventoRetorno = new stdClass();
            $oEventoRetorno->nome = $oEvento->getDescricao();
            $oEventoRetorno->letivo = $oEvento->isDiaLetivo();
            $aEventos[] = $oEventoRetorno;
        }
    }
    return $aEventos;
}

/**
 * Retorna se a data é letiva
 * @return stdClass
 */
function getDiaLetivo(Calendario $oCalendarioEscolar, DBDate $oData, $aEventos)
{

    $oDiaLetivo = new stdClass();
    $oDiaLetivo->dia_letivo = false;
    $oDiaLetivo->periodo = '';
    $aPeriodosCalendario = $oCalendarioEscolar->getPeriodos();

    foreach ($aPeriodosCalendario as $oPeriodoCalendario) {
        $oDataInicio = $oPeriodoCalendario->getDataInicio();
        $oDataFinal = $oPeriodoCalendario->getDataTermino();
        if (DBDate::dataEstaNoIntervalo($oData, $oDataInicio, $oDataFinal)) {
            $oDiaLetivo->dia_letivo = true;
            $oDiaLetivo->periodo = $oPeriodoCalendario->getPeriodoAvaliacao()->getDescricaoAbreviada();
            break;
        }
    }

    if (in_array($oData->getDiaSemana(), array(0, 6))) {
        foreach ($aEventos as $oEvento) {
            if ($oEvento->letivo) {
                $oDiaLetivo->dia_letivo = true;
            }
        }

        if ($oDiaLetivo->dia_letivo) {
            return $oDiaLetivo;
        }
    }

    return $oDiaLetivo;
}

/**
 * Array com as cores para cada período
 */
$aCoresPeriodo = array();
$iCor = 255;
foreach ($oCalendario->periodos_letivos as $iIndice => $oPeriodo) {
    $iCor = $iCor - 30;
    $aCoresPeriodo[$oPeriodo->nome] = $iCor;
}

$aEventos = array();

/**
 * Agrupar eventos
 */
$oUltimoEvento = null;
foreach ($oCalendario->meses as $oMes) {
    foreach ($oMes->dias as $oDia) {
        if (count($oDia->eventos) == 0) {
            continue;
        }

        foreach ($oDia->eventos as $oEvento) {
            $oEventoAgrupado = new stdClass();
            $oEventoAgrupado->data_inicial = $oDia->data;
            $oEventoAgrupado->data_final = $oDia->data;
            $oEventoAgrupado->nome = $oEvento->nome;

            if (empty($oUltimoEvento)) {
                $oUltimoEvento = $oEventoAgrupado;
            }

            $dataEventoAnterior = new DateTime($oUltimoEvento->data_final);
            $dataEventoPercorrido = new DateTime($oEventoAgrupado->data_inicial);
            $diasDiferencaEventos = date_diff($dataEventoAnterior, $dataEventoPercorrido);

            if ($oUltimoEvento->nome == $oEventoAgrupado->nome && $diasDiferencaEventos->days <= 1) {
                $oUltimoEvento->data_final = $oDia->data;
            } else {
                $aEventos[] = $oUltimoEvento;
                $oUltimoEvento = $oEventoAgrupado;
            }
        }
    }
}
$aEventos[] = $oUltimoEvento;

$oPdf = new ECidade\Pdf\Pdf();
$oPdf->init(false);
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(true, 15);
$oPdf->setfillcolor(255);

$oPdf->addTitulo('CALENDÁRIO LETIVO', 1);
$title2 = "Calendário: " . $oCalendarioTurma->getDescricao();
$title3 = "Período: " . $oCalendarioTurma->getDataInicio()->convertTo(DBDate::DATA_PTBR);
$title3 .= " até " . $oCalendarioTurma->getDataFinal()->convertTo(DBDate::DATA_PTBR);

$oPdf->addTitulo($title2, 2);
$oPdf->addTitulo($title3, 3);

$oPdf->addpage('P');
$oPdf->setfont('arial', 'B', 9);

$iY = $oPdf->GetY();         // Eixo Y inicial, corrigido a cada quebra de linha de miniaturas
$iX = $oPdf->GetX();         // Eixo X inicial, corrigido a cada coluna impressa
$iXOriginal = $oPdf->GetX(); // Usado para reiniciar o eixo X quando quebra o limite de colunas

$iColunaMiniatura = 3; // Usado para controlar o número de colunas de miniaturas

/**
 * Percorremos os meses do calendário para imprimir a miniaturas
 */
foreach ($oCalendario->meses as $iInd => $oMes) {
    $iColunaMiniatura--;
    montaMiniatura($oPdf, $oMes, $iY, $iX, $aCoresPeriodo);

    $iX += 64;

    if ($iColunaMiniatura == 0) {
        $iColunaMiniatura = 3;
        $iY += 36;
        $iX = $iXOriginal;
    }
}

/**
 * Função que monta as miniaturas de cada mês.
 * @param ECidade\Pdf\Pdf $oPdf
 * @param stdClass $oMes Objeto com os dados do mês
 * @param integer $iPosicaoEixoY Posição que devemos escrever no eixo Y
 * @param integer $iPosicaoEixoX Posição que devemos escrever no eixo X
 * @param array $aCoresPeriodo Array com as cores de preenchimento do período
 */
function montaMiniatura(ECidade\Pdf\Pdf $oPdf, $oMes, $iPosicaoEixoY, $iPosicaoEixoX, $aCoresPeriodo)
{
    $oPdf->SetY($iPosicaoEixoY);
    $oPdf->SetX($iPosicaoEixoX);
    $oPdf->SetFont("arial", "b", 8);
    $iTamanhoMiniatura = 60;
    $iTamanhoDia = $iTamanhoMiniatura / 7;

    $oPdf->Cell($iTamanhoMiniatura, 4, $oMes->nome, 1, 1, "C");
    $oPdf->SetX($iPosicaoEixoX);
    $oPdf->Cell($iTamanhoDia, 4, "Dom", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Seg", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Ter", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Qua", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Qui", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Sex", 1, 0, "C");
    $oPdf->Cell($iTamanhoDia, 4, "Sab", 1, 1, "C");
    $oPdf->SetX($iPosicaoEixoX);

    // Pega o dia da semana correspondente ao primeiro dia do mês e soma 1
    $iPrimeiroDiaDaSemana = (date('N', strtotime($oMes->dias[0]->data)) + 1);

    $iNumeroCelulasVazias = ($iPrimeiroDiaDaSemana % 8);

    // Completa com celulas vazias antes do primeiro dia do mes
    for ($x = 1; $x < $iNumeroCelulasVazias; $x++) {
        $oPdf->Cell($iTamanhoDia, 4, "", 1, 0, "C");
    }

    $oPdf->SetFont("arial", "", 8);
    $sPeriodoAvaliacao = "";

    foreach ($oMes->dias as $iIndexDia => $oDia) {
        if ($sPeriodoAvaliacao != $oDia->dia_letivo->periodo) {
            $sPeriodoAvaliacao = $oDia->dia_letivo->periodo;
        }
        $oPdf->SetFillColor(getCorPeriodo($oPdf, $sPeriodoAvaliacao, !empty($oDia->eventos), $aCoresPeriodo));

        // Pega o dia da semada do dia do mês correspondente
        $iDiaSemana = ($iPrimeiroDiaDaSemana + $iIndexDia) % 7;

        // Verifica se é dia letivo ou se possui algum evento letivo
        if ($oDia->dia_letivo->dia_letivo || hasDiaLetivo($oDia->eventos)) {
            // Caso tenha um período de avaliação pega a cor correspondente
            if ($sPeriodoAvaliacao) {
                $iCorPeriodo = getCorPeriodo($oPdf, $sPeriodoAvaliacao, !empty($oDia->eventos), $aCoresPeriodo);
                $oPdf->SetFillColor($iCorPeriodo);
            }
        }

        $oPdf->SetFont("arial", "", 8);
        if (!empty($oDia->eventos)) {
            $oPdf->SetFont("arial", "B", 8);
        }


        $iQuebraPagina = 0;
        if ($iDiaSemana == 0) {
            $iQuebraPagina = 1;
        }

        $oPdf->Cell($iTamanhoDia, 4, date('d', strtotime($oDia->data)), 1, $iQuebraPagina, "C", 1);

        if ($iQuebraPagina == 1) {
            $oPdf->SetX($iPosicaoEixoX);
        }
    }

    // Pega o dia da semana correspondente ao último dia do mês
    $iUltimoDiaMes = (date('N', strtotime($oMes->dias[$iIndexDia]->data)));

    // Preenche com células vazias ao final do mês
    $iIteracoes = 7 - ($iUltimoDiaMes % 7);
    for ($x = 1; $x < $iIteracoes; $x++) {
        $oPdf->Cell($iTamanhoDia, 4, "", 1, 0, "C");
    }
}

$oPdf->Ln(8);

/**
 * Imprime as legendas dos períodos letivos
 */
$iLegendasImpressas = 0;
foreach ($oCalendario->periodos_letivos as $oPeriodoLetivo) {
    $iLegendasImpressas++;

    $oDtInicio = new DBDate($oPeriodoLetivo->data_inicio);
    $dtInicio = $oDtInicio->convertTo(DBDate::DATA_PTBR);
    $oDtFim = new DBDate($oPeriodoLetivo->data_termino);
    $dtFim = $oDtFim->convertTo(DBDate::DATA_PTBR);

    $sPeriodo = "{$oPeriodoLetivo->nome} - {$dtInicio} até {$dtFim}";

    $oPdf->SetFillColor(getCorPeriodo($oPdf, $oPeriodoLetivo->nome, false, $aCoresPeriodo));

    $oPdf->Cell(10, 4, "", 1, 0, "", 1);
    $oPdf->Cell(50, 4, $sPeriodo, 0, 0, 'L');
    $oPdf->SetX($oPdf->GetX() + 4);

    if ($iLegendasImpressas == 3) {
        $oPdf->Ln(6);
        $iLegendasImpressas = 0;
    }
}

function cabecalhoEventos($oPdf)
{
    $oPdf->Ln(6);
    $oPdf->SetFont("arial", "b", 8);
    $oPdf->Cell(190, 4, "Eventos", "B", 1, "C");
    $oPdf->SetY($oPdf->getY() + 2);
}

cabecalhoEventos($oPdf);

$iYEvento = $oPdf->GetY();
$iXEvento = $oPdf->GetX();
$iYOriginalEventos = $oPdf->GetY();

$oPdf->SetFont("arial", "", 6.5);

/**
 * Imprime os eventos
 */

$oPdfHeight = 0;

foreach ($aEventos as $oEvento) {
    $oDtInicio = new DBDate($oEvento->data_inicial);
    $dtInicio = $oDtInicio->convertTo(DBDate::DATA_PTBR);

    $oDtFim = new DBDate($oEvento->data_final);
    $dtFim = $oDtFim->convertTo(DBDate::DATA_PTBR);

    $sEvento = "{$dtInicio}";
    if ($oEvento->data_inicial != $oEvento->data_final) {
        $sEvento .= " até {$dtFim}";
    }
    $sEvento .= " {$oEvento->nome}";

    $oPdf->SetX($iXEvento);
    $oPdf->Cell(190, 4, $sEvento, 0, 1);
    if ($oPdf->GetY() >= $oPdf->getH() - 20) {
        $oPdf->SetY($iYOriginalEventos);
        $iXEvento += 64; // Largura de cada miniatura + espaço entre elas
        if ($iXEvento >= 200) {
            $iXEvento = 10;
            $oPdf->AddPage();
            cabecalhoEventos($oPdf);
            $iYOriginalEventos = $oPdf->GetY();
            $oPdf->SetFont("arial", "", 6.5);
        }
    }

    if ($oPdf->GetY() > $oPdfHeight) {
        $oPdfHeight = $oPdf->GetY();
    }
}

if ($oPdfHeight > $oPdf->GetPageHeight() - 50) {
    $oPdf->AddPage();
}

$contadorObservacao = strlen($oCalendario->observacoes);

if ($contadorObservacao <= 1100) {
    $oPdf->Ln(6);
    $oPdf->Cell(190,1,"","B", 1, "C");
    $oPdf->SetFont("arial", "B", 6.5);
    $oPdf->Multicell(190, 4, substr('OBSERVAÇÕES: ', 0, 12), "LR", "", 0, 0);
    $oPdf->SetFont("arial", "", 6.5);
    $oPdf->Multicell(190, 4, substr($oCalendario->observacoes, 0, 1100), "LRB", "J", 0, 0);
} else {
    $oPdf->Cell(276,0,"",'B',0,0);
}

/**
 * Retorna a cor correspondente do período letivo
 * @param string $sPeriodo Nome do período letivo
 * @param boolean $temEvento indicador se possui evento
 * @return string Hexadecimal corresponde a cor
 */
function getCorPeriodo($oPdf, $sPeriodo, $temEvento, $aCores)
{

    $iValorCor = 255;

    $oPdf->SetFont("arial", "", 8);
    if (isset($aCores[$sPeriodo])) {
        $iValorCor = $aCores[$sPeriodo];

        // Caso tenha algum evento coloca a cor em um tom mais escuro
        if ($temEvento) {
            $iValorCor -= 50;
        }
    }
    return $iValorCor;
}

/**
 * Verifica se o parâmetro possui algum dia letivo
 * @param array $aEventos coleção de eventos
 * @return boolean possui ou nao dia letivo
 */
function hasDiaLetivo($aEventos)
{
    foreach ($aEventos as $oEvento) {
        if ($oEvento->letivo) {
            return true;
        }
    }
    return false;
}

$oPdf->Output('I');
