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

 require_once(modification("libs/db_stdlib.php"));
 require_once(modification("std/db_stdClass.php"));
 require_once(modification("libs/db_conecta.php"));
 require_once(modification("libs/db_sessoes.php"));
 require_once(modification("dbforms/db_funcoes.php"));
 require_once(modification("libs/db_utils.php"));

$oCalendario = CalendarioRepository::getCalendarioByCodigo($iCalendario);

if (!isset($iEscola)) {
    $iEscola = 0;
}

$oEscola = EscolaRepository::getEscolaByCodigo($iEscola);
$aEtapas = array(0);
$aEscola = array($oEscola);
$aCalendario = array($oCalendario);

$calendarios = explode(",", $iCalendario);

if (!isset($iCalendario)) {
    $iCalendario = "";
}

$calendarios = str_replace("999999','", "", $iCalendario);
$bairros = str_replace("999999,", "", $iBairros);
$sCampos = " Distinct ed18_i_codigo,ed52_i_codigo,ed18_codigoreferencia,ed18_c_nome";
$sWhere = "  ed57_i_escola IN (SELECT ed18_i_codigo FROM escola WHERE ed18_i_bairro IN ($bairros))";
$sWhere .= " AND ed52_i_codigo  IN ($calendarios) ";
$sWhere .= " AND ed11_i_codigo IN ($iEtapas) ";
$sOrder = " ed18_codigoreferencia";

$oDaoTurma = new cl_turma();
$sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, $sOrder, $sWhere);
$rs = db_query($sSql);

if ($rs && pg_num_rows($rs) > 0) {
    $iLinhas = pg_num_rows($rs);
    for ($i = 0; $i < $iLinhas; $i++) {
        $codigosEscolasId[$i] = db_utils::fieldsMemory($rs, $i)->ed18_i_codigo;
        $codigosCalendariosId[$i] = db_utils::fieldsMemory($rs, $i)->ed52_i_codigo;
    }

    for ($i = 0; $i < sizeof($codigosCalendariosId); $i++) {
        $oEscola = EscolaRepository::getEscolaByCodigo($codigosEscolasId[$i]);
        $aEscola[$i] = $oEscola;
        $oCalendario = CalendarioRepository::getCalendarioByCodigo($codigosCalendariosId[$i]);
        $aCalendario[$i] = $oCalendario;
    }
}

$aEtapas = array();
$sCampos = " distinct ed11_i_codigo";
$sWhere = "    ed57_i_escola IN (SELECT ed18_i_codigo FROM escola WHERE ed18_i_bairro IN ($bairros))";
$sWhere .= " AND ed52_i_codigo IN ($calendarios) ";
$sWhere .= " AND ed11_i_codigo IN ($iEtapas) ";

$oDaoTurma = new cl_turma();
$sSql = $oDaoTurma->sql_query_turmaMatriculasAtivas(null, $sCampos, null, $sWhere);

$rs = db_query($sSql);

if ($rs && pg_num_rows($rs) > 0) {
    $iLinhas = pg_num_rows($rs);
    for ($i = 0; $i < $iLinhas; $i++) {
        $aEtapas[] = db_utils::fieldsMemory($rs, $i)->ed11_i_codigo;
    }
}

$bBairros = "SELECT DISTINCT array_to_string(array_accum(trim(j13_descr)),',') FROM bairro WHERE j13_codi IN ($iBairros) ORDER BY 1";
$rsBairros = db_query($bBairros);
$nBairros = "";
if ($rsBairros && pg_num_rows($rsBairros) > 0) {
    $nBairros .= pg_result($rsBairros, 0);
}

$bEtapas = "SELECT DISTINCT array_to_string(array_accum(trim(ed11_c_descr)),',') FROM serie WHERE ed11_i_codigo IN ($iEtapas) ORDER BY 1";
$rsEtapas = db_query($bEtapas);
$nEtapas = "";
if ($rsEtapas && pg_num_rows($rsEtapas) > 0) {
    $nEtapas .= pg_result($rsEtapas, 0);
}

$cBairros = explode(",", $bairros);
$numBairrosSel = count($cBairros);

$sqlBairrosTotal = "SELECT count(distinct(ed18_i_bairro))
			               FROM escola
                     INNER JOIN bairro ON ed18_i_bairro = j13_codi
                     INNER JOIN turma ON ed57_i_escola = ed18_i_codigo
                     INNER JOIN calendario ON  ed57_i_calendario = ed52_i_codigo
                          WHERE ed52_i_ano = " . db_getsession("DB_anousu") . "
                            AND j13_codi NOT IN (0)";

$numBairrosTotal = pg_result(db_query($sqlBairrosTotal), 0);
if (($numBairrosTotal) == $numBairrosSel) {
    $nBairros = "TODOS";
}
$oRelatorio = new RelatorioQuadroDeVagasBairro($aCalendario, $aEtapas, $aEscola, $nBairros, $nEtapas, $iCalendario, false);
$oRelatorio->gerarRelatorio();
