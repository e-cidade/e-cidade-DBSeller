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

ini_set('memory_limit', '2048M');
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

$oGet = db_utils::postMemory($_GET);

/**
 *
 * Variáveis utilizadas no header do relatório
 */
$sPeriodo      = null;
$sClasse       = null;
$sDepartamento = null;
$iInstituicaoSessao = db_getsession("DB_instit");

$sTipoImpressao = "Analitica";
if ($oGet->sImpressao == "S") {
    $sTipoImpressao = "Sintética ";
} else if ($oGet->sImpressao == "T") {
    $sTipoImpressao = "Acumulado";
}

$sTipoTotal = $oGet->sTipoTotal;
$sTotalGrupo = "";

if ($oGet->sTipoTotal == "O"){
    $lTotOrgao = true;
    $lTotDepto = false;
    $sTotalGrupo = "Total do Órgão";
} else if ($oGet->sTipoTotal == "D") {
    $lTotOrgao = false;
    $lTotDepto = true;
    $sTotalGrupo = "Total do Departamento";
} else {
    $lTotOrgao = false;
    $lTotDepto = false;
}

$sWhere  = "     t57_ativo is true ";
$sWhere .= " and t57_processado is true";

if (!empty($oGet->sContasContabeis)) {
    $sWhere .= " and t86_conplano in ({$oGet->sContasContabeis})";
}

/** ***********************************************************
 *  *****************  FILTRO POR DATA  ***********************
 *  *********************************************************** */
/**
 * Se a data Final estiver vazia e a inicial preenchida
 * Busca todos registros apartir da data Inicial
 */
if (!empty($oGet->sDataInicio) && empty($oGet->sDataFinal)) {

    $oDataInicio = new DBDate($oGet->sDataInicio);
    $sDataInicio = formataData($oGet->sDataInicio);
    $sPeriodo    = $sDataInicio;
    $sWhere     .= " and t57_datacalculo >= '{$sDataInicio}' and t86_anousu = " . $oDataInicio->getAno() . " ";
}

/**
 * Se a data Incial estiver vazia e a final preenchida
 * Busca todos registros até a data final
 */
if (empty($oGet->sDataInicio) &&  !empty($oGet->sDataFinal)) {

    $oDataFim = new DBDate($oGet->sDataFinal);
    $sDataFinal = formataData($oGet->sDataFinal);
    $sPeriodo   = $sDataFinal;
    $sWhere    .= " and t57_datacalculo <= '{$sDataFinal}' and t86_anousu = " . $oDataFim->getAno() . " ";
}

/**
 * Se ambas as Datas estivérem válidas, faz um between entre elas
 */
if (!empty($oGet->sDataInicio) && !empty($oGet->sDataFinal)) {

    $sDataFinal  = formataData($oGet->sDataFinal);
    list($iDiaInicio, $iMesInicio, $iAnoInicio) = explode("/", $oGet->sDataInicio);
    list($iDiaFinal, $iMesFinal, $iAnoFinal)    = explode("/", $oGet->sDataFinal);


    $sPeriodo    = "{$oGet->sDataInicio} até {$oGet->sDataFinal}";
    $sWhere     .= " and cast( t57_ano||lpad(t57_mes,2,'0') as integer) ";
    $sWhere     .= " 		     between '{$iAnoInicio}{$iMesInicio}' and '{$iAnoFinal}{$iMesFinal}' ";
    $sWhere     .= " and t86_anousu in ({$iAnoInicio}, {$iAnoFinal}) ";

//   cast( t57_ano||lpad(t57_mes,2,'0') as integer) between '201201' and '201204'
}

/** ***********************************************************
 *  *****************  FILTRO POR CLASSE **********************
 *  *********************************************************** */

/**
 * Se a Classe Final estiver vazia e a inicial preenchida
 * Busca todos registros apartir da Classe Inicial
 */

if (!empty($oGet->sClasseInicio) && empty($oGet->sClasseFim)) {

    $sClasse = $oGet->sClasseInicio;
    $sWhere .= " and t64_class >= '{$oGet->sClasseInicio}'";
}

/**
 * Se a data Incial estiver vazia e a final preenchida
 * Busca todos registros até a data final
 */
if (empty($oGet->sClasseInicio) && !empty($oGet->sClasseFim)) {

    $sClasse = $oGet->sClasseFim;
    $sWhere .= " and t64_class <= '{$oGet->sClasseFim}'";
}

/**
 * Se ambas as Datas estivérem válidas, faz um between entre elas
 */
if (!empty($oGet->sClasseInicio) && !empty($oGet->sClasseFim)) {
    $sClasse = getclassificacao($oGet->sClasseInicio) . ' até ' . getclassificacao($oGet->sClasseFim);
    $sWhere .= " and t64_class between '{$oGet->sClasseInicio}' and '{$oGet->sClasseFim}'";
}

/** ***********************************************************
 *  **************  FILTRO POR ORGAOS         *****************
 *  *********************************************************** */

if (!empty($oGet->sOrgaos)) {

    $oDaoOrgao          = db_utils::getDao("orcorgao");
    $sWhereOrgao        = " o40_orgao in ($oGet->sOrgaos)";
    $sSqlOrgao          = $oDaoOrgao->sql_query_file(null,"orgao_descricao",null,"$sWhereOrgao");
    $rsOrgao            = $oDaoOrgao->sql_record($sSqlOrgao);
    $sOrgao             = "";
    $sVirgula           = "";

    if ($oDaoOrgao->numrows > 0) {

        for ($i = 0; $i < $oDaoOrgao->numrows; $i++) {

            $sOrgao .= "{$sVirgula}". db_utils::fieldsMemory($rsOrgao, $i)->o40_descr;
            $sVirgula			  = ", ";
        }
    }
    $sWhere       .= " and db01_orgao in ($oGet->sOrgaos)";
}


/** ***********************************************************
 *  **************  FILTRO POR DEPARTAMENTOS  *****************
 *  *********************************************************** */

if (!empty($oGet->sDepartamentos)) {

    $oDaoDepartamento   = db_utils::getDao("db_depart");
    $sWhereDepartamento = " coddepto in ($oGet->sDepartamentos)";
    $sSqlDepartamento   = $oDaoDepartamento->sql_query_file(null,"descrdepto",null,"$sWhereDepartamento");
    $rsDepartamento     = $oDaoDepartamento->sql_record($sSqlDepartamento);
    $sDepartamento      = "";
    $sVirgula           = "";

    if ($oDaoDepartamento->numrows > 0) {

        for ($i = 0; $i < $oDaoDepartamento->numrows; $i++) {

            $sDepartamento .= "{$sVirgula}". db_utils::fieldsMemory($rsDepartamento, $i)->descrdepto;
            $sVirgula			  = ", ";
        }
    }
    $sWhere       .= " and coddepto in ($oGet->sDepartamentos)";
}

/**
 * Campos utilizado na query
 */
$sCampos  = "distinct";
$sCampos .= " t52_bem            as codigo_bem, 			";
$sCampos .= " t52_ident          as placa, 		";
$sCampos .= " t52_descr          as bem_descricao,    ";
$sCampos .= " o40_orgao          as orgao,            ";
$sCampos .= " o40_descr          as orgao_descricao,  ";
$sCampos .= " coddepto,                               ";
$sCampos .= " descrdepto,                             ";
$sCampos .= " t64_class          as classe, 				  ";
$sCampos .= " t64_descr          as classe_descr, 	  ";
$sCampos .= " t44_vidautil       as vida_util,        ";
$sCampos .= " t46_descricao      as tipo_depreciacao, ";
$sCampos .= " t58_valorresidual  as valor_residual,   ";
$sCampos .= " t52_valaqu         as valor_aquisicao,  ";
$sCampos .= " t58_valorcalculado as valor_depreciado, ";
$sCampos .= "(t44_valoratual + t44_valorresidual) as valor_atual,";
$sCampos .= " t58_valoratual     as saldo, ";
$sCampos .= " t57_mes            as mes,   ";
$sCampos .= " t57_ano            as ano,   ";
$sCampos .= " t57_processado ";
/**
 * Ordenação da Query
 */
if ($lTotOrgao){
    $sOrdem 		     = "o40_orgao, coddepto, t57_ano, t57_mes, t52_bem ";
} else if ($lTotDepto) {
    $sOrdem 		     = "coddepto, t57_ano, t57_mes, t52_bem ";
} else {
    $sOrdem 		     = "t57_ano, t57_mes, t52_bem ";
}


$oDaoHistorico		 = new cl_benshistoricocalculo();
$sWhere             .= " and bens.t52_instit = {$iInstituicaoSessao} ";

$sSqlBensDepreciacao = $oDaoHistorico->sql_query_historico_depreciacao(null, $sCampos, $sOrdem, $sWhere);
$rsBensDepreciacao   = $oDaoHistorico->sql_record($sSqlBensDepreciacao);

$iNumrows            = $oDaoHistorico->numrows;

if ($iNumrows == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Não foi encontrado resultados para o filtro selecionado.");
    exit;
}

$aHistoricoDepreciacao       = array();
$aValoresDepreciado 		 = array();
$aValoresDepreciadoAcumulado = array();

for ($i = 0; $i < $iNumrows; $i++) {

    $oBenHistoricoDepreciacao = db_utils::fieldsMemory($rsBensDepreciacao, $i);

    if ($lTotOrgao){
        $iIndexRel = $oBenHistoricoDepreciacao->orgao.$oBenHistoricoDepreciacao->ano.$oBenHistoricoDepreciacao->mes;
    } else if ($lTotDepto) {
        $iIndexRel = $oBenHistoricoDepreciacao->coddepto.$oBenHistoricoDepreciacao->ano.$oBenHistoricoDepreciacao->mes;
    } else {
        $iIndexRel = $oBenHistoricoDepreciacao->ano.$oBenHistoricoDepreciacao->mes;
    }

    $oBemHistorico = new stdClass();
    $oBemHistorico->codigo_bem       = $oBenHistoricoDepreciacao->codigo_bem;
    $oBemHistorico->placa    		   = $oBenHistoricoDepreciacao->placa;
    $oBemHistorico->bem_descricao    = $oBenHistoricoDepreciacao->bem_descricao;
    $oBemHistorico->orgao            = $oBenHistoricoDepreciacao->orgao;
    $oBemHistorico->orgao_descricao  = $oBenHistoricoDepreciacao->orgao_descricao;
    $oBemHistorico->coddepto         = $oBenHistoricoDepreciacao->coddepto;
    $oBemHistorico->descrdepto       = $oBenHistoricoDepreciacao->descrdepto;
    $oBemHistorico->classe           = $oBenHistoricoDepreciacao->classe;
    $oBemHistorico->vida_util        = $oBenHistoricoDepreciacao->vida_util;
    $oBemHistorico->tipo_depreciacao = $oBenHistoricoDepreciacao->tipo_depreciacao;
    $oBemHistorico->valor_residual   = $oBenHistoricoDepreciacao->valor_residual;
    $oBemHistorico->valor_aquisicao  = $oBenHistoricoDepreciacao->valor_aquisicao;
    $oBemHistorico->valor_depreciado = $oBenHistoricoDepreciacao->valor_depreciado;
    $oBemHistorico->valor_atual      = $oBenHistoricoDepreciacao->valor_atual;
    $oBemHistorico->saldo            = $oBenHistoricoDepreciacao->saldo;
    $oBemHistorico->ano              = $oBenHistoricoDepreciacao->ano;
    $oBemHistorico->mes_descricao    = db_mes($oBenHistoricoDepreciacao->mes, 2);
    $oBemHistorico->mes              = $oBenHistoricoDepreciacao->mes;

    $aHistoricoDepreciacao[$iIndexRel][] = $oBemHistorico;

    /**
     * Só executa este calculo se a impressão for do tipo acumulada.
     */
    if ($oGet->sImpressao == "T") {

        if (array_key_exists($oBemHistorico->codigo_bem, $aValoresDepreciadoAcumulado)) {

            $nSaldo = $aValoresDepreciadoAcumulado[$oBemHistorico->codigo_bem]->saldo;
            $nSaldo = $nSaldo - $oBemHistorico->valor_depreciado;
            $aValoresDepreciadoAcumulado[$oBemHistorico->codigo_bem]->valor_depreciado += $oBemHistorico->valor_depreciado ;
            $aValoresDepreciadoAcumulado[$oBemHistorico->codigo_bem]->saldo             = $nSaldo;

        } else {
            $aValoresDepreciadoAcumulado[$oBemHistorico->codigo_bem] = $oBemHistorico;
        }
    }
}

$nValorDepreciado   = 0;

if ($oGet->sImpressao == "S") {


    foreach ($aHistoricoDepreciacao as $iIndice => $aBemHistorico) {

        if (!array_key_exists($iIndice, $aValoresDepreciado)) {
            $nValorDepreciado   = 0;
        }

        for ($i = 0; $i < count($aBemHistorico); $i++) {
            $nValorDepreciado += $aBemHistorico[$i]->valor_depreciado;
        }
        $oSomaValorDepreciado = new stdClass();
        /**
         * Peguei os dados do indice [0] pois este é igual em todos os index
         */
        $oSomaValorDepreciado->orgao            = $aBemHistorico[0]->orgao;
        $oSomaValorDepreciado->orgao_descricao  = $aBemHistorico[0]->orgao_descricao;
        $oSomaValorDepreciado->coddepto         = $aBemHistorico[0]->coddepto;
        $oSomaValorDepreciado->descrdepto       = $aBemHistorico[0]->descrdepto;
        $oSomaValorDepreciado->ano              = $aBemHistorico[0]->ano;
        $oSomaValorDepreciado->mes_descricao    = db_mes($aBemHistorico[0]->mes, 2);
        $oSomaValorDepreciado->mes              = $aBemHistorico[0]->mes;
        $oSomaValorDepreciado->total            = $nValorDepreciado;

        $aValoresDepreciado[$iIndice] = $oSomaValorDepreciado;
    }
}
$head1 = "Histórico de Bens Depreciados";
$head2 = "Impressão: {$sTipoImpressao}";
if (!empty($sPeriodo)) {
    $head3 = "Periodo de: {$sPeriodo}";
}
if (!empty($sClasse)) {
    $head4 = "Classificação de: {$sClasse}";
}
if (!empty($sDepartamento)) {
    $head5 = "Departamento: {$sDepartamento}";
}
if (!empty($oGet->sContasContabeis)) {
    $head6 = "Contas Contábeis: {$oGet->sContasContabeis}";
}

$oPdf  = new PDFDocument();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false, 15);
$oPdf->addHeaderDescription($head1);
$oPdf->addHeaderDescription($head2);


if (!empty($head3)) {
    $oPdf->addHeaderDescription($head3);
}

if (!empty($head4)) {
    $oPdf->addHeaderDescription($head4);
}

if (!empty($head5)) {
    $oPdf->addHeaderDescription($head5);
}

if (!empty($head6)) {
    $oPdf->addHeaderDescription(str_replace(',', ', ', $head6));
}

$iHeigth             = 4;
$iWidth              = 100;
$lPrimeiroLaco       = true;

/**
 * Imprime o relatório de acordo com o Tipo de Impressão selecionado:
 * S = Sintético
 * A = Analítico
 * T = Acumulado
 */

if ($oGet->sImpressao == 'S') {
    $oPdf->addPage();
} else {
    $oPdf->AddPage('L');
}

switch ($oGet->sImpressao) {

    case "S":

        $nTotalValorDepreciado = 0;
        $nTotalValorDepreciadoDepto = 0;
        $nTotalValorDepreciadoOrgao = 0;

        $codOrgaoAnt = 0;
        $codDeptoAnt = 0;

        foreach ($aValoresDepreciado as $oPeriodoDepreciado) {

            $codOrgaoAtual = $oPeriodoDepreciado->orgao;
            $codDeptoAtual = $oPeriodoDepreciado->coddepto;

            if ($sTipoTotal == 'D'){
                $sDescricaoH = "{$oPeriodoDepreciado->coddepto} - {$oPeriodoDepreciado->descrdepto} - Órgão: {$oPeriodoDepreciado->orgao} - {$oPeriodoDepreciado->orgao_descricao}";
            } else if ($sTipoTotal == 'O'){
                $sDescricaoH = "{$oPeriodoDepreciado->orgao} - {$oPeriodoDepreciado->orgao_descricao}";
            }

            if (($codOrgaoAnt <> 0) && ($codOrgaoAnt <> $codOrgaoAtual) && ($lTotOrgao)) {
                $lQuebraOrgao = true;
            } else {
                $lQuebraOrgao = false;
            }

            if (($codDeptoAnt <> 0) && ($codDeptoAnt <> $codDeptoAtual) && ($lTotDepto)) {
                $lQuebraDepto = true;
            } else {
                $lQuebraDepto = false;
            }
            if ($lQuebraDepto && $lTotDepto) {
                $oPdf->SetFont("arial", "B", 7);
                $oPdf->Cell(100,  $iHeigth, "Total do Departamento:",						"TBR", 0, "R");
                $oPdf->Cell(90, $iHeigth, db_formatar($nTotalValorDepreciadoDepto, 'f'), "TB", 1,"R");
                $nTotalValorDepreciadoDepto = 0;
            }

            if ($lQuebraOrgao && $lTotOrgao) {
                $oPdf->SetFont("arial", "B", 7);
                $oPdf->Cell(100,  $iHeigth, "Total do Órgão:", "TBR", 0, "R");
                $oPdf->Cell(90, $iHeigth, db_formatar($nTotalValorDepreciadoOrgao, 'f'), "TB", 1,"R");
                $nTotalValorDepreciadoOrgao = 0;
            }

            if ($oPdf->gety() > $oPdf->h - 30){
                $lQuebraPagina = true;
            } else {
                $lQuebraPagina = false;
            }

            if ($lQuebraPagina || $lPrimeiroLaco || $lQuebraDepto || $lQuebraOrgao) {
                setHeader($oPdf, $iHeigth, $oGet->sImpressao, $lQuebraPagina, $oPeriodoDepreciado, $sDescricaoH, $sTipoTotal);
                $lPrimeiroLaco = false;
            }

            $oPdf->SetFont("arial", "", 7);

            $oPdf->Cell(50,  $iHeigth, "{$oPeriodoDepreciado->mes_descricao}", "TB", 0);
            $oPdf->Cell(50,  $iHeigth, "{$oPeriodoDepreciado->ano}", "TB", 0, "C");
            $oPdf->Cell(90, $iHeigth, db_formatar($oPeriodoDepreciado->total, 'f'), "TB", 1,"R");
            $nTotalValorDepreciado += $oPeriodoDepreciado->total;
            $nTotalValorDepreciadoDepto += $oPeriodoDepreciado->total;
            $nTotalValorDepreciadoOrgao += $oPeriodoDepreciado->total;

            $codDeptoAnt = $oPeriodoDepreciado->coddepto;
            $codOrgaoAnt = $oPeriodoDepreciado->orgao;
        }
        if ($lTotDepto) {
            //total do ultimo departamento
            $oPdf->SetFont("arial", "B", 7);
            $oPdf->Cell(100,  $iHeigth, "Total do Departamento:",			         "TBR", 0, "R");
            $oPdf->Cell(90, $iHeigth, db_formatar($nTotalValorDepreciadoDepto, 'f'), "TB", 1,"R");
            $nTotalValorDepreciadoDepto = 0;
        }

        if ($lTotOrgao) {
            //total do ultimo orgao
            $oPdf->SetFont("arial", "B", 7);
            $oPdf->Cell(100,  $iHeigth, "Total do Órgão:",			         "TBR", 0, "R");
            $oPdf->Cell(90, $iHeigth, db_formatar($nTotalValorDepreciadoOrgao, 'f'), "TB", 1,"R");
            $nTotalValorDepreciadoOrgao = 0;
        }
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell(100,  $iHeigth, "Total:",     											"TBR", 0, "R");
        $oPdf->Cell(90, $iHeigth, db_formatar($nTotalValorDepreciado, 'f'), "TB", 1,"R");

        break;
    case "A":

        $nValorDepreciado = 0;
        $sValorAquisicao  = 0;
        $sValorResidual   = 0;
        $sSaldo           = 0;

        $nValorDepreciadoGrupo = 0;
        $sValorAquisicaoGrupo  = 0;
        $sValorResidualGrupo   = 0;
        $sSaldoGrupo           = 0;

        $codOrgaoAnt = 0;
        $codDeptoAnt = 0;

        foreach ($aHistoricoDepreciacao as $iIndiceDepreciacao => $aHistorico) {

            foreach ($aHistorico as $iIndiceHistorico => $oHistorico) {

                $codOrgaoAtual = $oHistorico->orgao;
                $codDeptoAtual = $oHistorico->coddepto;

                if (($codOrgaoAnt <> 0) && ($codOrgaoAnt <> $codOrgaoAtual) && ($lTotOrgao)) {
                    $lQuebraOrgao = true;
                } else {
                    $lQuebraOrgao = false;
                }

                if (($codDeptoAnt <> 0) && ($codDeptoAnt <> $codDeptoAtual) && ($lTotDepto)) {
                    $lQuebraDepto = true;
                } else {
                    $lQuebraDepto = false;
                }

                if ($sTipoTotal == 'D'){
                    $sDescricaoH = "Departamento: {$oHistorico->coddepto} - {$oHistorico->descrdepto} - Órgão: {$oHistorico->orgao} - {$oHistorico->orgao_descricao}";
                } else if ($sTipoTotal == 'O'){
                    $sDescricaoH = "Órgão: {$oHistorico->orgao} - {$oHistorico->orgao_descricao}";
                }

                /*
			    if ($lQuebraDepto && $lTotDepto) {
			    	$oPdf->SetFont("arial", "B", 7);
                    $oPdf->Cell(157,  $iHeigth, "Total do Departamento:",	             "TBR", 0, "R");
			        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorAquisicaoDepto, 'f'),      1, 0, "R");
			        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorResidualDepto, 'f'),       1, 0, "R");
			        $oPdf->Cell(30,  $iHeigth, db_formatar($nValorDepreciadoDepto, 'f'),     1, 0, "R");
			        $oPdf->Cell(30,  $iHeigth, db_formatar($sSaldoDepto, 'f'), 		     "TBL", 1, "C");

			    }
                */
                $oPdf->setBold(false);
                $oPdf->setFontSize(7);

                $iMultiCellHeight = $oPdf->getMultiCellHeight(45, $iHeigth, $oHistorico->bem_descricao);
                $lNovaPagina      = ($oPdf->getAvailHeight() < ($iHeigth*2)+$iMultiCellHeight);

                if ($lNovaPagina || $lPrimeiroLaco || $lQuebraDepto || $lQuebraOrgao) {
                    setHeader($oPdf, $iHeigth, $oGet->sImpressao, $lNovaPagina, $aHistorico[0], $sDescricaoH, $sTipoTotal);
                    $lPrimeiroLaco = false;
                }

                $oPdf->setBold(false);
                $oPdf->setFontSize(7);

                $oPdf->setAutoNewLineMulticell(false);
                $oPdf->Cell(15,  $iMultiCellHeight, $oHistorico->placa,                         "TBR", 0, "C");
                $oPdf->MultiCell(45,  $iHeigth, $oHistorico->bem_descricao,           1, "L");
                $oPdf->Cell(45,  $iMultiCellHeight, substr($oHistorico->tipo_depreciacao, 0, 30),        1, 0, "L");
                $oPdf->Cell(25,  $iMultiCellHeight, $oHistorico->classe,                                 1, 0, "C");
                $oPdf->Cell(27,  $iMultiCellHeight, $oHistorico->vida_util,                              1, 0, "C");
                $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_aquisicao, 'f'),      1, 0, "R");
                $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_residual, 'f'),       1, 0, "R");
                $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_depreciado, 'f'),     1, 0, "R");
                $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_atual - $oHistorico->valor_residual, 'f'), 					  "TBL", 1, "C");

                $sValorAquisicaoGrupo  += $oHistorico->valor_aquisicao;
                $sValorResidualGrupo   += $oHistorico->valor_residual;
                $nValorDepreciadoGrupo += $oHistorico->valor_depreciado;
                $sSaldoGrupo           += $oHistorico->valor_atual - $oHistorico->valor_residual;

                $sValorAquisicao  += $oHistorico->valor_aquisicao;
                $sValorResidual   += $oHistorico->valor_residual;
                $nValorDepreciado += $oHistorico->valor_depreciado;
                $sSaldo           += $oHistorico->valor_atual - $oHistorico->valor_residual;

                $codOrgaoAnt = $oHistorico->orgao;
                $codDeptoAnt = $oHistorico->coddepto;

            }
            //ultimo departamento
            /*
		    if ($lTotDepto) {
		    	$oPdf->SetFont("arial", "B", 7);
                $oPdf->Cell(157,  $iHeigth, "Total do Departamento:",	             "TBR", 0, "R");
		        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorAquisicaoDepto, 'f'),      1, 0, "R");
		        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorResidualDepto, 'f'),       1, 0, "R");
		        $oPdf->Cell(30,  $iHeigth, db_formatar($nValorDepreciadoDepto, 'f'),     1, 0, "R");
		        $oPdf->Cell(30,  $iHeigth, db_formatar($sSaldoDepto, 'f'), 		     "TBL", 1, "C");
            }
            */
            if ($sTipoTotal =='O' || $sTipoTotal == 'D'){
                $oPdf->SetFont("arial", "B", 7);
                $oPdf->Cell(157, $iHeigth, "$sTotalGrupo:",     	                 "TBR", 0, "R");
                $oPdf->Cell(30,  $iHeigth, db_formatar($sValorAquisicaoGrupo, 'f'),      1, 0, "R");
                $oPdf->Cell(30,  $iHeigth, db_formatar($sValorResidualGrupo, 'f'),       1, 0, "R");
                $oPdf->Cell(30,  $iHeigth, db_formatar($nValorDepreciadoGrupo, 'f'),     1, 0, "R");
                $oPdf->Cell(30,  $iHeigth, db_formatar($sSaldoGrupo, 'f'), 				  "TBL", 1, "C");
                //$oPdf->Cell(30,  $iHeigth, db_formatar($oHistorico->coddepto, 'f'), 				  "TBL", 1, "C");

                $nValorDepreciadoGrupo = 0;
                $sValorAquisicaoGrupo  = 0;
                $sValorResidualGrupo   = 0;
                $sSaldoGrupo           = 0;

            }

        }

        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell(157,  $iHeigth, "Total Geral:",     											"TBR", 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorAquisicao, 'f'),      1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorResidual, 'f'),       1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($nValorDepreciado, 'f'),     1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sSaldo, 'f'), 				  "TBL", 1, "C");
        //$oPdf->Cell(30,  $iHeigth, db_formatar($oHistorico->coddepto, 'f'), 				  "TBL", 1, "C");

        break;
    case "T":

        $nValorDepreciado = 0;
        $sValorAquisicao  = 0;
        $sValorResidual   = 0;
        $sSaldo           = 0;

        $nValorDepreciadoGupo = 0;
        $sValorAquisicaoGrupo = 0;
        $sValorResidualGrupo  = 0;
        $sSaldoGrupo          = 0;

        $codDeptoAnt   = 0;
        $codOrgaoAnt   = 0;

        foreach ($aValoresDepreciadoAcumulado as $iIndiceHistorico => $oHistorico) {

            if ($sTipoTotal == 'O'){
                $sDescricaoH = "{$oHistorico->orgao} - {$oHistorico->orgao_descricao}";
            } else if ($sTipoTotal == 'D'){
                $sDescricaoH = "{$oHistorico->coddepto} - {$oHistorico->descrdepto} - Órgão: {$oHistorico->orgao} - {$oHistorico->orgao_descricao}";
            }

            $codOrgaoAtual = $oHistorico->orgao;
            $codDeptoAtual = $oHistorico->coddepto;

            if (($codOrgaoAnt <> 0) && ($codOrgaoAnt <> $codOrgaoAtual) && ($lTotOrgao)) {
                $lQuebraOrgao = true;
            } else {
                $lQuebraOrgao = false;
            }

            if (($codDeptoAnt <> 0) && ($codDeptoAnt <> $codDeptoAtual) && ($lTotDepto)) {
                $lQuebraDepto = true;
            } else {
                $lQuebraDepto = false;
            }
            if (($lQuebraDepto && $lTotDepto) || ($lQuebraOrgao && $lTotOrgao)) {
                $oPdf->SetFont("arial", "B", 7);
                $oPdf->Cell(157,  $iHeigth, "$sTotalGrupo:",                "TBR", 0, "R");
                $oPdf->Cell(30,   $iHeigth, db_formatar($sValorAquisicaoGrupo, 'f'),      1, 0, "R");
                $oPdf->Cell(30,   $iHeigth, db_formatar($sValorResidualGrupo, 'f'),       1, 0, "R");
                $oPdf->Cell(30,   $iHeigth, db_formatar($nValorDepreciadoGrupo, 'f'),     1, 0, "R");
                $oPdf->Cell(30,   $iHeigth, db_formatar($sSaldoGrupo, 'f'), 		  "TBL", 1, "C");

                $nValorDepreciadoGrupo = 0;
                $sValorAquisicaoGrupo  = 0;
                $sValorResidualGrupo   = 0;
                $sSaldoGrupo           = 0;
            }

            $iMultiCellHeight = $oPdf->getMultiCellHeight(45, $iHeigth, $oHistorico->bem_descricao);

            if ($oPdf->GetY() > $oPdf->h - 25 ){
                $lQuebraPagina = true;
            } else {
                $lQuebraPagina = false;
            }

            if ($lQuebraPagina || $lPrimeiroLaco || $lQuebraDepto || $lQuebraOrgao) {
                setHeader($oPdf, $iHeigth, $oGet->sImpressao, $lQuebraPagina, null, $sDescricaoH, $sTipoTotal);
                $lPrimeiroLaco = false;
            }

//			echo "<pre>";
//			print_r($oHistorico);
//			echo "</pre>";exit;
//
            $oPdf->SetFont("arial", "", 7);
            $iMultiCellHeight = $oPdf->getMultiCellHeight(45, $iHeigth, $oHistorico->bem_descricao);
            $oPdf->setAutoNewLineMulticell(false);
            $oPdf->Cell(15,  $iMultiCellHeight, $oHistorico->placa,                         "TBR", 0, "C");
            $oPdf->MultiCell(45,  $iHeigth, $oHistorico->bem_descricao,           1, "L");
            $oPdf->Cell(45,  $iMultiCellHeight, substr($oHistorico->tipo_depreciacao, 0, 30),        1, 0, "L");
            $oPdf->Cell(25,  $iMultiCellHeight, $oHistorico->classe,                                 1, 0, "C");
            $oPdf->Cell(27,  $iMultiCellHeight, $oHistorico->vida_util,                              1, 0, "C");
            $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_aquisicao, 'f'),      1, 0, "R");
            $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_residual, 'f'),       1, 0, "R");
            $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_depreciado, 'f'),     1, 0, "R");
            $oPdf->Cell(30,  $iMultiCellHeight, db_formatar($oHistorico->valor_atual - $oHistorico->valor_residual, 'f'), 					  "TBL", 1, "C");

            $sValorAquisicaoGrupo  += $oHistorico->valor_aquisicao;
            $sValorResidualGrupo   += $oHistorico->valor_residual;
            $nValorDepreciadoGrupo += $oHistorico->valor_depreciado;
            $sSaldoGrupo           += $oHistorico->valor_atual - $oHistorico->valor_residual;

            $sValorAquisicao  += $oHistorico->valor_aquisicao;
            $sValorResidual   += $oHistorico->valor_residual;
            $nValorDepreciado += $oHistorico->valor_depreciado;
            $sSaldo           += $oHistorico->valor_atual - $oHistorico->valor_residual;

            $codOrgaoAnt = $oHistorico->orgao;
            $codDeptoAnt = $oHistorico->coddepto;
        }
        if ($lTotDepto || $lTotOrgao) {
            //total do ultimo departamento
            $oPdf->SetFont("arial", "B", 7);
            $oPdf->Cell(157,  $iHeigth, "$sTotalGrupo:",                          "TBR", 0, "R");
            $oPdf->Cell(30,   $iHeigth, db_formatar($sValorAquisicaoGrupo, 'f'),      1, 0, "R");
            $oPdf->Cell(30,   $iHeigth, db_formatar($sValorResidualGrupo, 'f'),       1, 0, "R");
            $oPdf->Cell(30,   $iHeigth, db_formatar($nValorDepreciadoGrupo, 'f'),     1, 0, "R");
            $oPdf->Cell(30,   $iHeigth, db_formatar($sSaldoGrupo, 'f'), 		  "TBL", 1, "C");

            $nValorDepreciadoGrupo = 0;
            $sValorAquisicaoGrupo  = 0;
            $sValorResidualGrupo   = 0;
            $sSaldoGrupo           = 0;
        }
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell(157,  $iHeigth, "Total Geral:",     				"TBR", 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorAquisicao, 'f'),      1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sValorResidual, 'f'),       1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($nValorDepreciado, 'f'),     1, 0, "R");
        $oPdf->Cell(30,  $iHeigth, db_formatar($sSaldo, 'f'), 				  "TBL", 1, "C");
        break;
}

/**
 * Insere o cabeçalho do relatório de acordo com o tipo: (Sintético, Analítico ou Acumulado)
 * @param object $oPdf
 * @param integer $iHeigth Altura da linha
 * @param strinf $sImpressao Tipo do relatório
 * @param boolean $lQuebraPagina Se é para quebra a página
 * @param object $oBenHistorico
 */
function setHeader($oPdf, $iHeigth, $sImpressao, $lQuebraPagina = true, $oBenHistorico = null, $sDescricaoH = '', $sTipoTotal = 'N') {

    $oPdf->setfont('arial', 'b', 7);
    $oPdf->setfillcolor(235);
    switch ($sImpressao) {

        case "S":
            if ($lQuebraPagina) {
                $oPdf->AddPage();
            }

            if ($sTipoTotal == 'D'){
                $oPdf->Cell(190,  $iHeigth, "Departamento: {$sDescricaoH}", "TB", 1);
            } else if ($sTipoTotal == 'O') {
                $oPdf->Cell(190,  $iHeigth, "Órgão: {$sDescricaoH}", "TB", 1);
            }

            $oPdf->Cell(50,  $iHeigth, "Mês", "TBR", 0, "C", 1);
            $oPdf->Cell(50,  $iHeigth, "Ano", "LTB", 0, "C", 1);
            $oPdf->Cell(90,  $iHeigth, "Valor", "TBL", 1, "C", 1);
            break;
        case "A":

            if ($lQuebraPagina) {
                $oPdf->AddPage("L");
            } else {
                $oPdf->ln(5);
            }
            if (!empty($oBenHistorico)) {

                //if ($sTipoTotal == 'D') {
                //    $sDepart  = "{$oBenHistorico->coddepto} - {$oBenHistorico->descrdepto}";
                //    $oPdf->Cell(277, $iHeigth, "Departamento: {$sDepart}", "TB", 1, "L", 1);
                //}
                $sPeriodo = "{$oBenHistorico->mes_descricao} / {$oBenHistorico->ano}";

                if (!empty($sDescricaoH)){
                    $oPdf->Cell(277, $iHeigth, "Periodo: {$sPeriodo} - {$sDescricaoH}", "TB", 1, "L", 1);
                } else {
                    $oPdf->Cell(277, $iHeigth, "Periodo: {$sPeriodo}", "TB", 1, "L", 1);
                }

            }
            $oPdf->Cell(15,  $iHeigth, "Placa",            "TBR", 0, "C", 1);
            $oPdf->Cell(45,  $iHeigth, "Bem",                  1, 0, "C", 1);
            $oPdf->Cell(45,  $iHeigth, "Tipo Depreciação",     1, 0, "C", 1);
            $oPdf->Cell(25,  $iHeigth, "Classificação",        1, 0, "C", 1);
            $oPdf->Cell(27,  $iHeigth, "Vida Útil",            1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Aquisição",       1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Residual",        1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Depreciado",      1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Depreciável",    "TBL", 1, "C", 1);
            break;
        case "T":
            if ($lQuebraPagina) {
                $oPdf->AddPage('L');
            }

            if ($sTipoTotal == 'D'){
                $oPdf->Cell(277,  $iHeigth, "Departamento: {$sDescricaoH}", "TB", 1, "L", 1);
            } else if ($sTipoTotal == 'O') {
                $oPdf->Cell(277,  $iHeigth, "Órgão: {$sDescricaoH}", "TB", 1, "L", 1);
            }

            $oPdf->Cell(15,  $iHeigth, "Placa",            "TBR", 0, "C", 1);
            $oPdf->Cell(45,  $iHeigth, "Bem",                  1, 0, "C", 1);
            $oPdf->Cell(45,  $iHeigth, "Tipo Depreciação",     1, 0, "C", 1);
            $oPdf->Cell(25,  $iHeigth, "Classificação",        1, 0, "C", 1);
            $oPdf->Cell(27,  $iHeigth, "Vida Útil",            1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Aquisição",       1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Residual",        1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Depreciado",      1, 0, "C", 1);
            $oPdf->Cell(30,  $iHeigth, "Vlr. Depreciável",		 "TBL", 1, "C", 1);

            break;
    }

}

/**
 *
 * Converte uma data do formato banco para formato de leitura brasileiro
 * @param unknown_type $sData
 */
function formataData($sData) {
    return implode("-", array_reverse(explode("/", $sData)));
}

function getClassificacao($classificacao) {
    $clabens = new cl_clabens;
    $instit = db_getsession("DB_instit");
    $where = "t64_class = '{$classificacao}' and t64_instit = {$instit}";
    $sql = $clabens->sql_query_file(null, "*", null, $where);
    $rs = $clabens->sql_record($sql);

    return db_utils::fieldsMemory($rs, 0)->t64_descr;

}

$oPdf->showPDF("bens_depreciados" . time());
