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

require_once(modification("fpdf151/pdf.php"));
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("libs/db_liborcamento.php"));

$oGet = db_utils::postMemory($_GET);
$iInstit = str_replace("-",",",$oGet->instit);

$sAgrupador1 = substr($oGet->sAgrupador1,0,1);
$sAgrupador2 = substr($oGet->sAgrupador2,0,1);

$iNivel = $sAgrupador2;
if ($sAgrupador1 > $sAgrupador2) {
    $iNivel = $sAgrupador1;
}

$aCodAgrupa1 = explode("-",str_replace("pai_","",$oGet->sOrgaos1));
$aCodAgrupa2 = explode("-",str_replace("pai_","",$oGet->sOrgaos2));

function montaWherePorNivel($nivel, $valores) {
    switch ($nivel) {
        case '1': //"1A","Órgão"
            return "o58_orgao in (" . implode(',', $valores) . ")";
        case '3': //"3B","Função"
            return "o58_funcao in (" . implode(',', $valores) . ")";
        case '4': //"4B","Subfunção"
            return "o58_subfuncao in (" . implode(',', $valores) . ")";
        case '5': //"5B","Programa"
            return "o58_programa in (" . implode(',', $valores) . ")";
        case '6': //"6B","Proj/Ativ"
            return "o58_projativ in (" . implode(',', $valores) . ")";
        case '2': //"2A","Unidade"
            $unidades = array_map(function ($valor) {
                $unidade = explode('_', $valor);
                return "(o58_orgao = {$unidade[0]} and o58_unidade = {$unidade[1]})";
            }, $valores);

            return "(". implode(' or ', $unidades) . ")";
        case '7': //"7B","Elemento"
            $elementos = array_map(function ($valor) {
                $elemento = substr(trim($valor), 0, 7);
                return "(e.o56_elemento ilike '{$elemento}%')";
            }, $valores);
            return "(". implode(' or ', $elementos) . ")";
        case '8': //"8B","Recurso"
            return "o58_codigo in (" . implode(',', $valores) . ")";

    }
}
$where = ["w.o58_instit in ({$iInstit})"];

$where[] = montaWherePorNivel($sAgrupador1, $aCodAgrupa1);
$where[] = montaWherePorNivel($sAgrupador2, $aCodAgrupa2);

switch ($sAgrupador1) {
    case "1":
        $sNivelAgrupa1 = "o58_orgao";
        $sDescrNivel1 = "o40_descr";
        $sCabNivel1 = "Orgão";
        break;
    case "2":
        $sNivelAgrupa1 = "o58_unidade";
        $sDescrNivel1 = "o41_descr";
        $sCabNivel1 = "Unidade";
        break;
    case "3":
        $sNivelAgrupa1 = "o58_funcao";
        $sDescrNivel1 = "o52_descr";
        $sCabNivel1 = "Função";
        break;
    case "4":
        $sNivelAgrupa1 = "o58_subfuncao";
        $sDescrNivel1 = "o53_descr";
        $sCabNivel1 = "Subfunção";
        break;
    case "5":
        $sNivelAgrupa1 = "o58_programa";
        $sDescrNivel1 = "o54_descr";
        $sCabNivel1 = "Programa";
        break;
    case "6":
        $sNivelAgrupa1 = "o58_projativ";
        $sDescrNivel1 = "o55_descr";
        $sCabNivel1 = "Proj/Ativ";
        break;
    case "7":
        $sNivelAgrupa1 = "o58_elemento";
        $sDescrNivel1 = "o56_descr";
        $sCabNivel1 = "Elemento";
        break;
    case "8":
        $sNivelAgrupa1 = "o15_recurso";
        $sDescrNivel1 = "o15_descr";
        $sCabNivel1 = "Recurso";
        break;
}

switch ($sAgrupador2) {
    case "1":
        $sNivelAgrupa2 = "o58_orgao";
        $sDescrNivel2 = "o40_descr";
        $sCabNivel2 = "Orgão";
        break;
    case "2":
        $sNivelAgrupa2 = "o58_unidade";
        $sDescrNivel2 = "o41_descr";
        $sCabNivel2 = "Unidade";
        break;
    case "3":
        $sNivelAgrupa2 = "o58_funcao";
        $sDescrNivel2 = "o52_descr";
        $sCabNivel2 = "Função";
        break;
    case "4":
        $sNivelAgrupa2 = "o58_subfuncao";
        $sDescrNivel2 = "o53_descr";
        $sCabNivel2 = "Subfunção";
        break;
    case "5":
        $sNivelAgrupa2 = "o58_programa";
        $sDescrNivel2 = "o54_descr";
        $sCabNivel2 = "Programa";
        break;
    case "6":
        $sNivelAgrupa2 = "o58_projativ";
        $sDescrNivel2 = "o55_descr";
        $sCabNivel2 = "Proj/Ativ";
        break;
    case "7":
        $sNivelAgrupa2 = "o58_elemento";
        $sDescrNivel2 = "o56_descr";
        $sCabNivel2 = "Elemento";
        break;
    case "8":
        $sNivelAgrupa2 = "o15_recurso";
        $sDescrNivel2 = "o15_descr";
        $sCabNivel2 = "Recurso";
        break;
}

// Caso o Nível1 seja tipo Unidade a string "$oGet->sOrgaos1" retorna junto o orgão da unidade, então haverá mais um explode
if ($sAgrupador1 == "2") {
    foreach ($aCodAgrupa1 as $ChaveUnidade) {
        $aUnidade1[] = explode("_", $ChaveUnidade);
    }
    $aCodAgrupa1 = $aUnidade1;
}

// Caso o Nível2 seja tipo Unidade a string "$oGet->sOrgaos2" retorna junto o orgão da unidade, então haverá mais um explode
if ($sAgrupador2 == "2") {
    foreach ($aCodAgrupa2 as $ChaveUnidade) {
        $aUnidade2[] = explode("_", $ChaveUnidade);
    }
    $aCodAgrupa2 = $aUnidade2;
}


$filtroRecurso = '';
/**
 * Quando nível 8, foi agrupado por recurso... Se agrupado por recurso, temos que levar em consideração o campo
 * o15_recuso
 */
if ($iNivel == 8) {
    $valores = $iNivel == $sAgrupador1 ? $aCodAgrupa1 : $aCodAgrupa2;
    $filtroRecurso = montaWherePorNivel($iNivel, $valores);
    $filtro = str_replace('o58_codigo', 'o15_codigo', $filtroRecurso);
    $dao = new cl_orctiporec();
    $rsRecurso = db_query($dao->sql_query_file(null, 'distinct o15_recurso', null, $filtro));

    $recursos = db_utils::makeCollectionFromRecord($rsRecurso, function ($dado) {
        return $dado->o15_recurso;
    });
    if ($iNivel == $sAgrupador1) {
        $aCodAgrupa1 = $recursos;
    } else {
        $aCodAgrupa2 = $recursos;
    }
    $filtroRecurso = " where " . $filtroRecurso;
}

$iAnoUsu = db_getsession('DB_anousu');
$dataini = db_getsession('DB_anousu')."-01-01";

$iDia = substr($oGet->dataf,0,2);
$iMes = substr($oGet->dataf,3,2);
$iAno = substr($oGet->dataf,6,4);

// Caso seja o último dia do mês a variável "$PrevPerVal" será multiplicada pelo mês da data selecionada, caso contrário multiplicará pelo mês anterior

$lUltimoDia = verifica_ultimo_dia_mes($oGet->dataf);

if ($lUltimoDia) {
    $iMesPrev = $iMes;
} else {
    $iMesPrev = ($iMes - 1);
}

$datafin = "{$iAno}-{$iMes}-{$iDia}";

$sWhere = implode(' and ', $where);
//con2_acomporc002.php
$campos = "
x.o58_orgao,
x.o40_descr,
x.o58_unidade,
x.o41_descr,
x.o58_funcao,
x.o52_descr,
x.o58_subfuncao,
x.o53_descr,
x.o58_programa,
x.o54_descr,
x.o58_projativ,
x.o55_descr,
x.o55_finali,
x.o58_elemento,
x.o56_descr,
x.o58_coddot,
oc.o15_recurso,
oc.o15_descr,
sum(x.dot_ini) as dot_ini,
sum(x.saldo_anterior) as saldo_anterior,
sum(x.empenhado) as empenhado,
sum(x.anulado) as anulado,
sum(x.liquidado) as liquidado,
sum(x.pago) as pago,
sum(x.suplementado) as suplementado,
sum(x.reduzido) as reduzido,
sum(x.atual) as atual,
sum(x.reservado) as reservado,
sum(x.atual_menos_reservado) as atual_menos_reservado,
sum(x.atual_a_pagar) as atual_a_pagar,
sum(x.atual_a_pagar_liquidado) as atual_a_pagar_liquidado,
sum(x.empenhado_acumulado) as empenhado_acumulado,
sum(x.anulado_acumulado) as anulado_acumulado,
sum(x.liquidado_acumulado) as liquidado_acumulado,
sum(x.pago_acumulado) as pago_acumulado,
sum(x.suplementado_acumulado) as suplementado_acumulado,
sum(x.reduzido_acumulado) as reduzido_acumulado,
sum(x.proj) as proj,
sum(x.ativ) as ativ,
sum(x.oper) as oper,
sum(x.ordinario) as ordinario,
sum(x.vinculado) as vinculado,
sum(x.suplemen) as suplemen,
sum(x.suplemen_acumulado) as suplemen_acumulado,
sum(x.especial) as especial,
sum(x.especial_acumulado) as especial_acumulado,
sum(x.reservado_manual_ate_data) as reservado_manual_ate_data,
sum(x.reservado_automatico_ate_data) as reservado_automatico_ate_data,
sum(x.reservado_ate_data) as reservado_ate_data
";
$group = "
x.o58_orgao,
x.o40_descr,
x.o58_unidade,
x.o41_descr,
x.o58_funcao,
x.o52_descr,
x.o58_subfuncao,
x.o53_descr,
x.o58_programa,
x.o54_descr,
x.o58_projativ,
x.o55_descr,
x.o55_finali,
x.o58_elemento,
x.o56_descr,
x.o58_coddot,
oc.o15_recurso,
oc.o15_descr
";


//dd("filtroRecurso $filtroRecurso");
$sqlDotacaoSaldo = db_dotacaosaldo($iNivel,1,4,true,$sWhere,$iAnoUsu,$dataini,$datafin,8,0,true);

$sql = "
  select {$campos}
    from ($sqlDotacaoSaldo) as x
    join orctiporec oc on oc.o15_codigo = x.o58_codigo
   {$filtroRecurso}
 group by {$group}
";

$rsDotacaoSaldo = db_query($sql);

$aGrupo = [];
$aTotGrupo = [];

$linhas = pg_num_rows($rsDotacaoSaldo);
for ($i = 0; $i < $linhas; $i++) {
    $oDotacaoSaldo = db_utils::fieldsMemory($rsDotacaoSaldo, $i);

    foreach ($aCodAgrupa1 as $iCodAgrupa1) {
        // Caso o Nível1 seja tipo Unidade será testado antes se o Orgão correspondente

        if ($sAgrupador1 == "2") {
            $lContinua = false;
            if ($oDotacaoSaldo->o58_orgao == $iCodAgrupa1[0]) {
                $iCodAgrupa1 = $iCodAgrupa1[1];
                $lContinua = true;
            }
            if (!$lContinua) {
                continue;
            }
        }

        if ($oDotacaoSaldo->$sNivelAgrupa1 == $iCodAgrupa1) {
            foreach ($aCodAgrupa2 as $iCodAgrupa2) {
                // Caso o Nível2 seja tipo Unidade será testado antes se o Orgão correspondente

                if ($sAgrupador2 == "2") {
                    $lContinua = false;
                    if ($oDotacaoSaldo->o58_orgao == $iCodAgrupa2[0]) {
                        $iCodAgrupa2 = $iCodAgrupa2[1];
                        $lContinua = true;
                    }
                    if (!$lContinua) {
                        continue;
                    }
                }

                if ($oDotacaoSaldo->$sNivelAgrupa2 == $iCodAgrupa2) {
                    switch ($oGet->iTipoDespesa) {
                        case "1":
                            $RealPeriodoVal = $oDotacaoSaldo->empenhado_acumulado - $oDotacaoSaldo->anulado_acumulado;
                            break;
                        case "2":
                            $RealPeriodoVal = $oDotacaoSaldo->liquidado_acumulado;
                            break;
                        case "3":
                            $RealPeriodoVal = $oDotacaoSaldo->pago_acumulado;
                            break;
                    }

                    $PrevAtuVal = $oDotacaoSaldo->dot_ini + $oDotacaoSaldo->suplementado_acumulado - $oDotacaoSaldo->reduzido_acumulado;
                    $PrevPerVal = ($PrevAtuVal / 12) * $iMesPrev;

                    if (!isset($aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2])) {
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevAtualizada'] = $PrevAtuVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['realPeriodo'] = $RealPeriodoVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevPeriodo'] = $PrevPerVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['desvio'] = $PrevPerVal - $RealPeriodoVal;
                    } else {
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevAtualizada'] += $PrevAtuVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['realPeriodo'] += $RealPeriodoVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevPeriodo'] += $PrevPerVal;
                        $aGrupo[$iCodAgrupa1][$oDotacaoSaldo->$sDescrNivel1][$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['desvio'] += $PrevPerVal - $RealPeriodoVal;
                    }

                    if (!isset($aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2])) {
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevAtualizada'] = $PrevAtuVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['realPeriodo'] = $RealPeriodoVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevPeriodo'] = $PrevPerVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['desvio'] = $PrevPerVal - $RealPeriodoVal;
                    } else {
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevAtualizada'] += $PrevAtuVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['realPeriodo'] += $RealPeriodoVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['prevPeriodo'] += $PrevPerVal;
                        $aTotGrupo[$iCodAgrupa2][$oDotacaoSaldo->$sDescrNivel2]['desvio'] += $PrevPerVal - $RealPeriodoVal;

                    }
                }
            }
        }
    }
}


if ($oGet->iTipoDespesa == "1") {
	$sCabDespesa = "Empenhada";
} else if ($oGet->iTipoDespesa == "2") {
	$sCabDespesa = "Liquidada";
} else {
	$sCabDespesa = "Paga";
}

$head2 = "ACOMPANHAMENTO ORÇAMENTÁRIO";
$head4 = "Posição até: {$oGet->dataf}";
$head6 = "Detalhamento da Despesa {$sCabDespesa} por:";
$head7 = "Nível 1 - ".$sCabNivel1;
$head8 = "Nível 2 - ".$sCabNivel2;

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->addpage();
$iAlt = 4;
$iFonte = 6;

ksort($aGrupo);
ksort($aTotGrupo);

foreach ($aGrupo as $CodGrupo1 => $aChaveDescr1) {
    foreach ($aChaveDescr1 as $DescrGrupo1 => $aChaveCod2) {
        $TotPrevAtualizada = 0;
        $TotRealPeriodo = 0;
        $TotPrevPeriodo = 0;
        $TotDesvio = 0;

        $pdf->setfont('arial', 'b', $iFonte);

        $pdf->ln(10);

        $pdf->cell(0, $iAlt, "AGRUPAMENTO DE NÍVEL 1 :  {$CodGrupo1} - {$DescrGrupo1}", 0, 1, "L", 0);
        $pdf->ln(2);

        $pdf->cell(80, $iAlt, "Agrupamento de Nível 2", "TB", 0, "L", 1);
        $pdf->cell(30, $iAlt, "Previsão Atualizada", "LTB", 0, "C", 1);
        $pdf->cell(30, $iAlt, "Realizado Até o Período", "LTB", 0, "C", 1);
        $pdf->cell(30, $iAlt, "Previsão Até o Período", "LTB", 0, "C", 1);
        $pdf->cell(25, $iAlt, "Desvio", "LTB", 1, "C", 1);
        $pdf->setfont('arial', '', $iFonte);

        ksort($aChaveCod2);

        foreach ($aChaveCod2 as $CodGrupo2 => $aChaveDescr2) {
            foreach ($aChaveDescr2 as $DescrGrupo2 => $aChaveVal2) {
                $pdf->cell(80, $iAlt, "{$CodGrupo2} - {$DescrGrupo2}", "TB", 0, "L", 0);
                $pdf->cell(30, $iAlt, db_formatar($aChaveVal2['prevAtualizada'], "f"), "LTB", 0, "R", 0);
                $pdf->cell(30, $iAlt, db_formatar($aChaveVal2['realPeriodo'], "f"), "LTB", 0, "R", 0);
                $pdf->cell(30, $iAlt, db_formatar($aChaveVal2['prevPeriodo'], "f"), "LTB", 0, "R", 0);
                $pdf->cell(25, $iAlt, db_formatar($aChaveVal2['desvio'], "f"), "LTB", 1, "R", 0);

                $TotPrevAtualizada += $aChaveVal2['prevAtualizada'];
                $TotRealPeriodo += $aChaveVal2['realPeriodo'];
                $TotPrevPeriodo += $aChaveVal2['prevPeriodo'];
                $TotDesvio += $aChaveVal2['desvio'];

            }
        }

        $pdf->setfont('arial', 'b', $iFonte);
        $pdf->cell(80, $iAlt, "Totalizador do Agrupamento de Nível 1", "TB", 0, "L", 1);
        $pdf->cell(30, $iAlt, db_formatar($TotPrevAtualizada, "f"), "LTB", 0, "R", 1);
        $pdf->cell(30, $iAlt, db_formatar($TotRealPeriodo, "f"), "LTB", 0, "R", 1);
        $pdf->cell(30, $iAlt, db_formatar($TotPrevPeriodo, "f"), "LTB", 0, "R", 1);
        $pdf->cell(25, $iAlt, db_formatar($TotDesvio, "f"), "LTB", 1, "R", 1);

    }
}


$TotPrevAtualizada = 0;
$TotRealPeriodo		 = 0;
$TotPrevPeriodo		 = 0;
$TotDesvio         = 0;

$pdf->ln(10);
$pdf->setfont('arial','b',$iFonte);
$pdf->cell(0, $iAlt,"TOTALIZADOR GERAL",0,1,"L",0);
$pdf->ln(2);

$pdf->cell(80,$iAlt,"Agrupamento de Nível 2"  ,"TB" ,0,"L",1);
$pdf->cell(30,$iAlt,"Previsão Atualizada"     ,"LTB",0,"C",1);
$pdf->cell(30,$iAlt,"Realizado Até o Período" ,"LTB",0,"C",1);
$pdf->cell(30,$iAlt,"Previsão Até o Período"  ,"LTB",0,"C",1);
$pdf->cell(25,$iAlt,"Desvio" 								  ,"LTB",1,"C",1);
$pdf->setfont('arial','',$iFonte);


foreach ($aTotGrupo as $CodTotGrupo => $aChaveTotDescr) {
    foreach ($aChaveTotDescr as $TotDescrGrupo => $aChaveValTot) {
        $pdf->cell(80, $iAlt, "{$CodTotGrupo} - {$TotDescrGrupo}", "TB", 0, "L", 0);
        $pdf->cell(30, $iAlt, db_formatar($aChaveValTot['prevAtualizada'], "f"), "LTB", 0, "R", 0);
        $pdf->cell(30, $iAlt, db_formatar($aChaveValTot['realPeriodo'], "f"), "LTB", 0, "R", 0);
        $pdf->cell(30, $iAlt, db_formatar($aChaveValTot['prevPeriodo'], "f"), "LTB", 0, "R", 0);
        $pdf->cell(25, $iAlt, db_formatar($aChaveValTot['desvio'], "f"), "LTB", 1, "R", 0);

        $TotPrevAtualizada += $aChaveValTot['prevAtualizada'];
        $TotRealPeriodo += $aChaveValTot['realPeriodo'];
        $TotPrevPeriodo += $aChaveValTot['prevPeriodo'];
        $TotDesvio += $aChaveValTot['desvio'];
    }
}

$pdf->setfont('arial','b',$iFonte);
$pdf->cell(80,$iAlt,"Totalizador Geral"									,"TB" ,0,"L",1);
$pdf->cell(30,$iAlt,db_formatar($TotPrevAtualizada,"f")	,"LTB",0,"R",1);
$pdf->cell(30,$iAlt,db_formatar($TotRealPeriodo,"f")		,"LTB",0,"R",1);
$pdf->cell(30,$iAlt,db_formatar($TotPrevPeriodo,"f")		,"LTB",0,"R",1);
$pdf->cell(25,$iAlt,db_formatar($TotDesvio,"f")					,"LTB",1,"R",1);

notasExplicativas($pdf,57,$iMes,190);

$pdf->Output();
