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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_matestoque_classe.php"));
require_once(modification("classes/db_matestoqueitem_classe.php"));
require_once(modification("classes/db_db_almox_classe.php"));
require_once(modification("classes/materialestoque.model.php"));
require_once(modification("classes/db_empparametro_classe.php"));
require_once(modification("libs/db_app.utils.php"));
db_app::import("contabilidade.contacorrente.ContaCorrenteFactory");
db_app::import("Acordo");
db_app::import("AcordoComissao");
db_app::import("CgmFactory");
db_app::import("financeiro.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("Dotacao");
db_app::import("contabilidade.planoconta.*");
db_app::import("contabilidade.contacorrente.*");
$oParametros      = db_utils::postMemory($_GET);
$clmatestoque     = new cl_matestoque;
$clmatestoqueitem = new cl_matestoqueitem;
$cldb_almox       = new cl_db_almox;



$clrotulo = new rotulocampo;
$clrotulo->label('m60_descr');
$clrotulo->label('descrdepto');

/**
 * busca o parametro de casas decimais para formatar o valor jogado na grid
 */

$oDaoParametros          = new cl_empparametro;
$iAnoSessao              = db_getsession("DB_anousu");
$sWherePeriodoParametro  = " 1=1 limit 1";
$sSqlPeriodoParametro    = $oDaoParametros->sql_query_file(null, "e30_numdec", null, $sWherePeriodoParametro);
$rsPeriodoParametro      = $oDaoParametros->sql_record($sSqlPeriodoParametro);
$iParametroNumeroDecimal = db_utils::fieldsMemory($rsPeriodoParametro, 0)->e30_numdec;

$iAlt = 4;
$sOrderBy = "m80_data";
if (isset($oParametros->quebra) && $oParametros->quebra == "S") {

  $sOrderBy = 'm80_coddepto, m60_descr, m80_data';
} else if ($oParametros->ordem == 'a') {
  $sOrderBy = 'm70_codmatmater, m80_data';
} else	if ($oParametros->ordem == 'b') {
	$sOrderBy = 'm80_coddepto, m60_descr, m80_data';
} else	if ($oParametros->ordem == 'c') {
	$sOrderBy = 'm60_descr';
} else  if ($oParametros->ordem == 'd') {
  $sOrderBy = " m70_codmatmater,m80_data desc";
}

if ($oParametros->listamatestoquetipo != "") {
  $sWhere  = " m80_codtipo in ({$oParametros->listamatestoquetipo}) ";
}else{
  //$sWhere  = " m80_codtipo in (5, 17, 19, 21) ";
  $sWhere  = " m81_tipo = 2 ";
}
$sWhere .= " and m71_servico is false";
$sWhere .= " and origem.instit=".db_getsession('DB_instit');
if ($oParametros->listaorgao!= "") {

 $sWhere .= " and o40_orgao in ({$oParametros->listaorgao}) ";
 $sWhere .= " and o40_anousu = ".db_getsession('DB_anousu');
 $sWhere .= " and o40_instit=".db_getsession('DB_instit');
}

if ($oParametros->listadepart != "") {
	if (isset ($oParametros->verdepart) && $oParametros->verdepart == "com") {
		$sWhere .= " and m80_coddepto in ({$oParametros->listadepart})";
	} else {
	  $sWhere .= " and m80_coddepto not in ({$oParametros->listadepart})";
	}
}

if ($oParametros->listamat != "") {
	if (isset ($oParametros->vermat) && $oParametros->vermat == "com") {
		$sWhere .= " and m70_codmatmater in ({$oParametros->listamat})";
	} else {
		$sWhere .= " and m70_codmatmater not in ({$oParametros->listamat})";
	}
}

if ($oParametros->listausu != "") {
	if (isset ($oParametros->verusu) && $oParametros->verusu == "com") {
		$sWhere .= " and m80_login in ({$oParametros->listausu})";
	} else {
		$sWhere .= " and m80_login not in ({$oParametros->listausu})";
	}
}


if ( isset($oParametros->listadepartDestino) && !empty($oParametros->listadepartDestino)) {

  $sWhere .= " and  m40_depto in ($oParametros->listadepartDestino) ";

}

$sDataIni = implode('-',array_reverse(explode('/',$oParametros->dataini)));
$sDataFin = implode('-',array_reverse(explode('/',$oParametros->datafin)));

if ((trim($oParametros->dataini) != "--") && ( trim($oParametros->datafin) != "--")) {

  $sWhere .= " and m80_data between '{$sDataIni}' and '{$sDataFin}' ";
  $info    = "De ".$oParametros->dataini." até ".$oParametros->datafin;
} else if (trim($oParametros->dataini) != "--") {

 	$sWhere .= " and m80_data >= '{$sDataIni}' ";
    $info  = "Apartir de ".$oParametros->dataini;
} else if (trim($oParametros->datafin) != "--") {

 	$sWhere .= " and m80_data <= '{$sDataFin}' ";
    $info   = "Até ".$oParametros->datafin;
}
$info_listar_serv = " LISTAR: TODOS";
$head3 = "Relatório de Saída de Material por Departamento";
$head5 = "$info";
$head7 = "$info_listar_serv";

$sInnerJoinGrupos = '';
$sFiltroGrupo = '';

if (isset($oParametros->grupos) && trim($oParametros->grupos) != "") {
    $sWhere .= " and materialestoquegrupo.m65_db_estruturavalor in ({$oParametros->grupos}) ";
    $sFiltroGrupo = 'Filtro por Grupos/Subgrupos';
    $head4 = $sFiltroGrupo;//"Relatório de Saída de Material por Departamento";
}

$sInnerJoinGrupos = "
    inner join matmatermaterialestoquegrupo on matmater.m60_codmater = matmatermaterialestoquegrupo.m68_matmater
    inner join materialestoquegrupo on matmatermaterialestoquegrupo.m68_materialestoquegrupo
                                    = materialestoquegrupo.m65_sequencial
	";


$sSqlSaidas = "SELECT m65_db_estruturavalor, ";
$sSqlSaidas .= "       db121_estrutural,";
$sSqlSaidas .= "       db121_descricao, ";
$sSqlSaidas .= "       m80_codigo, ";
$sSqlSaidas .= "       m61_abrev,";
$sSqlSaidas .= "       db121_sequencial,";
$sSqlSaidas .= "       db121_nivel,";
$sSqlSaidas .= "       db121_estruturavalorpai as contapai, ";
$sSqlSaidas .= "       m70_coddepto,  ";
$sSqlSaidas .= "       m70_codmatmater, ";
$sSqlSaidas .= "       m80_coddepto, ";
$sSqlSaidas .= "       m60_descr,  ";
$sSqlSaidas .= "       origem.descrdepto,  ";
$sSqlSaidas .= "       sum(m82_quant) as qtde, ";
$sSqlSaidas .= "       m80_data,  ";
$sSqlSaidas .= "       m80_codtipo,  ";
$sSqlSaidas .= "       m83_coddepto,  ";
$sSqlSaidas .= "       m81_descr,  ";
$sSqlSaidas .= "       m41_codmatrequi, ";
$sSqlSaidas .= "       m89_precomedio as precomedio, ";
$sSqlSaidas .= "       sum(m89_valorfinanceiro) as m89_valorfinanceiro, ";
$sSqlSaidas .= "       coalesce(m40_depto, ( ";
$sSqlSaidas .= "             select matestoqueinidestino.m80_coddepto  ";
$sSqlSaidas .= "               from  matestoqueini as matestoqueiniorigem  ";
$sSqlSaidas .= "         inner join matestoqueinill on matestoqueinill.m87_matestoqueini ";
$sSqlSaidas .= "                                     = matestoqueiniorigem.m80_codigo ";
$sSqlSaidas .= "         inner join matestoqueinil  on matestoqueinil.m86_matestoqueini ";
$sSqlSaidas .= "                                     = matestoqueinill.m87_matestoqueini ";
$sSqlSaidas .= "         inner join matestoqueinill as matestoqueinilldestino on ";
$sSqlSaidas .= "                              matestoqueinilldestino.m87_matestoqueinil = matestoqueinil.m86_codigo ";
$sSqlSaidas .= "         inner join matestoqueini   as matestoqueinidestino on matestoqueinidestino.m80_codigo ";
$sSqlSaidas .= "                                                           = matestoqueinilldestino.m87_matestoqueini ";
$sSqlSaidas .= "         inner join db_depart as destino on destino.coddepto = matestoqueinidestino.m80_coddepto ";
$sSqlSaidas .= "              where matestoqueiniorigem.m80_codigo = matestoqueini.m80_codigo ";
$sSqlSaidas .= "       )) as m40_depto,  ";
$sSqlSaidas .= "       destino.descrdepto as depto_destino,  ";
$sSqlSaidas .= "       m89_valorunitario ";
$sSqlSaidas .= "  from matestoqueini  ";
$sSqlSaidas .= "       inner join matestoqueinimei    on m80_codigo              = m82_matestoqueini ";
$sSqlSaidas .= "       inner join matestoqueinimeipm  on m82_codigo              = m89_matestoqueinimei ";
$sSqlSaidas .= "       inner join matestoqueitem      on m82_matestoqueitem      = m71_codlanc  ";
$sSqlSaidas .= "       inner join matestoque          on m70_codigo              = m71_codmatestoque ";
$sSqlSaidas .= "       inner join matmater            on m70_codmatmater         = m60_codmater  ";
$sSqlSaidas .= "       inner join matestoquetipo      on m80_codtipo             = m81_codtipo  ";
$sSqlSaidas .= "       left  join matestoquetransf    on m83_matestoqueini       = m80_codigo   ";
$sSqlSaidas .= "       left  join matestoqueinimeiari on m49_codmatestoqueinimei = m82_codigo  ";
$sSqlSaidas .= "       left  join atendrequiitem      on m49_codatendrequiitem   = m43_codigo  ";
$sSqlSaidas .= "       left  join matrequiitem        on m41_codigo              = m43_codmatrequiitem ";
$sSqlSaidas .= "       left  join matrequi            on m40_codigo              = m41_codmatrequi ";
$sSqlSaidas .= "       left  join db_depart           on db_depart.coddepto      = matrequi.m40_depto ";
$sSqlSaidas .= "       left  join db_departorg        on db01_coddepto           = db_depart.coddepto  ";
$sSqlSaidas .= "                                     and db01_anousu             = {$iAnoSessao}";
$sSqlSaidas .= "       left  join orcorgao            on o40_orgao               = db_departorg.db01_orgao ";
$sSqlSaidas .= "                                     and o40_anousu              = {$iAnoSessao}";
$sSqlSaidas .= "       left  join matunid             on matmater.m60_codmatunid = matunid.m61_codmatunid ";

$sSqlSaidas .= $sInnerJoinGrupos; // string de inner caso venha grupos selecionados

$sSqlSaidas .= "       inner join db_estruturavalor on  m65_db_estruturavalor = db121_sequencial ";
$sSqlSaidas .= "       inner join db_depart origem on origem.coddepto = m70_coddepto
                       left join db_depart destino on destino.coddepto = m40_depto";
$sSqlSaidas .= "      where {$sWhere} ";
$sSqlSaidas .= " group by m65_db_estruturavalor, destino.descrdepto,";
$sSqlSaidas .= "          db121_sequencial, ";
$sSqlSaidas .= "          db121_nivel, ";
$sSqlSaidas .= "          db121_estruturavalorpai,";
$sSqlSaidas .= "          db121_estrutural, ";
$sSqlSaidas .= "          db121_descricao, ";
$sSqlSaidas .= "          m80_codigo,  ";
$sSqlSaidas .= "          m61_abrev, ";
$sSqlSaidas .= "          m70_coddepto,  ";
$sSqlSaidas .= "          m70_codmatmater, ";
$sSqlSaidas .= "          m80_data,  ";
$sSqlSaidas .= "          m40_depto,  ";
$sSqlSaidas .= "          m81_descr,  ";
$sSqlSaidas .= "          m80_codtipo,  ";
$sSqlSaidas .= "          m80_coddepto,  ";
$sSqlSaidas .= "          m83_coddepto,  ";
$sSqlSaidas .= "         origem. descrdepto,  ";
$sSqlSaidas .= "          m89_precomedio,  ";
$sSqlSaidas .= "          m60_descr,  ";
$sSqlSaidas .= "          m41_codmatrequi, m89_valorunitario ";
$sSqlSaidas .= " order by m80_data, {$sOrderBy} , m80_data ";

if ($oParametros->ordem == 'b') {
    $sOrderBy = "m70_coddepto,  m80_data";
}

if ( $oParametros->iTipoRelatorio == 0 ) {

  $sSqlSaidas = "

       select m80_codigo,
              pai.db121_descricao as descr_pai,
              pai.db121_estrutural as estrut_pai,
              m40_depto as idepto_destino,
              destino.descrdepto as sdepto_destino,
              m70_coddepto as idepto_origem,
              origem.descrdepto as sdepto_origem,
              (precomedio * qtde) as total,
              *
         from (
                  {$sSqlSaidas}
              ) as xx
    inner join db_estruturavalor pai on contapai = pai.db121_sequencial
    inner join db_depart origem on  m70_coddepto = origem.coddepto
     left join db_depart destino on  m40_depto = destino.coddepto
    order by {$sOrderBy}
    ";

}

//echo $sSqlSaidas; die();

 $pdf = new PDF("L");
 $pdf->Open();
 $pdf->AliasNbPages();
 $pdf->setfillcolor(235);
 $pdf->SetAutoPageBreak(false);
 $lEscreveHeader = true;
 $nTotalItens    = 0;
 $nValorTotal    = 0;
 $iAlturalinha   = 4;

$rsSaidas = db_query($sSqlSaidas);
$iNumRows = pg_num_rows($rsSaidas);
$nValorTotalGeral = 0;

if ($iNumRows <= 0 || !$rsSaidas) {

  $sMensagem = "Nenhum registro encontrado para os filtros selecionados.";
  db_redireciona("db_erros.php?fechar=true&db_erro=" . $sMensagem);
  exit;
}

if ( $oParametros->iTipoRelatorio == 0 ) {

  $aDados = array();
  for ($i = 0; $i < $iNumRows; $i++) {

    $oDados = db_utils::fieldsMemory($rsSaidas, $i);
    $aDados[$oDados->idepto_destino . " - " . $oDados->sdepto_destino]
             [ $oDados->estrut_pai . " - " . $oDados->descr_pai ]
               [$oDados->db121_estrutural . " - " .  $oDados->db121_descricao] [] = $oDados;
  }

  $pdf->AddPage("L");

  foreach($aDados as $sDestino => $aDestinos){

    $pdf->SetFont('arial', 'b', $iTamFonte);
    $pdf->Cell(70, $iAlturalinha, "Departamento(s) de Destino:  {$sDestino}", "", 1, "L", 0);

    $nQuantTotalDepto = 0;
    $nValorTotalDepto = 0;


    foreach ($aDestinos as $sGrupo => $aGrupos) {

      $pdf->Cell(10, $iAlturalinha, "", "", 0, "L", 0);
      $pdf->Cell(70, $iAlturalinha, "Grupo: $sGrupo", "", 1, "L", 0);

      foreach ($aGrupos as $sSupgrupo => $aSupGrupo) {

        $pdf->SetFont('arial', 'b', $iTamFonte);
        $pdf->Cell(20, $iAlturalinha, "", "", 0, "L", 0);
        $pdf->Cell(50, $iAlturalinha, "Sub Grupo: $sSupgrupo", "", 1, "L", 0);

        $nTotalValorSubGrupo = 0;
        $nTotalQuantSubgrup = 0;

        imprimirCabecalho($pdf, $iAlturalinha, true, $oParametros);

        foreach ($aSupGrupo as $oDadosMaterial) {

          $sRequisicao = "$oDadosMaterial->m41_codmatrequi - $oDadosMaterial->m81_descr";
          $pdf->Cell(5, $iAlturalinha, "$oDadosMaterial->m70_codmatmater", "", 0, "R", 0);
          $pdf->Cell(90, $iAlturalinha, "$oDadosMaterial->m60_descr", "", 0, "L", 0);
          $pdf->Cell(10, $iAlturalinha, "$oDadosMaterial->m61_abrev", "", 0, "C", 0);
          $pdf->Cell(45, $iAlturalinha, "$oDadosMaterial->sdepto_origem", "", 0, "C", 0);
          $pdf->Cell(45, $iAlturalinha,  $sRequisicao, "", 0, "L", 0);
          $pdf->Cell(15, $iAlturalinha, db_formatar("$oDadosMaterial->m80_data", "d"), "", 0, "C", 0);
          $pdf->Cell(35, $iAlturalinha, "$oDadosMaterial->precomedio", "", 0, "R", 0);
          $pdf->Cell(15, $iAlturalinha, "$oDadosMaterial->qtde", "", 0, "R", 0);
          $pdf->Cell(20, $iAlturalinha, db_formatar("$oDadosMaterial->total", "f"), "", 1, "R", 0);

          $nQuantTotalDepto += $oDadosMaterial->qtde;
          $nValorTotalDepto += $oDadosMaterial->total;
          $nTotalValorSubGrupo += $oDadosMaterial->total;
          $nTotalQuantSubgrup  += $oDadosMaterial->qtde;
          $nValorTotalGeral    += $oDadosMaterial->total;

          imprimirCabecalho($pdf, $iAlturalinha, false, $oParametros);

        }

        $pdf->SetFont('arial', 'b', $iTamFonte);
        $pdf->Cell(20, $iAlturalinha, "", "", 0, "L", 0);
        $pdf->Cell(70, $iAlturalinha, "Total Subgrupo:  $sSupgrupo", "", 0, "L", 0);
        $pdf->Cell(40, $iAlturalinha, "Quant.:  $nTotalQuantSubgrup", "", 0, "L", 0);
        $pdf->Cell(40, $iAlturalinha, "Valor Total: ". db_formatar($nTotalValorSubGrupo, "f"), "", 1, "L", 0);
        $pdf->Cell(70, $iAlturalinha, "", "", 1, "L", 0);
        $pdf->SetFont('arial', '', $iTamFonte);
      }
    }
    $pdf->SetFont('arial', 'b', $iTamFonte);
    $pdf->Cell(70, $iAlturalinha, "Total Depto:  $sDestino", "", 0, "L", 0);
    $pdf->Cell(40, $iAlturalinha, "Quant.:  $nQuantTotalDepto", "", 0, "L", 0);
    $pdf->Cell(40, $iAlturalinha, "Valor Total Depto: ". db_formatar($nValorTotalDepto, "f"), "", 1, "L", 0);
    $pdf->Cell(70, $iAlturalinha, "", "", 1, "L", 0);
    $pdf->SetFont('arial', '', $iTamFonte);
  }


  $pdf->ln();
  $pdf->SetFont('arial', 'b', $iTamFonte);
  $pdf->Cell(70, $iAlturalinha, "Total Geral:", "", 0, "L", 0);
  //$pdf->Cell(40, $iAlturalinha, "Quant.:  $nQuantTotalDepto", "", 0, "L", 0);
  $pdf->Cell(40, $iAlturalinha, "R$: ". db_formatar($nValorTotalGeral, "f"), "", 1, "L", 0);
  $pdf->Cell(70, $iAlturalinha, "", "", 1, "L", 0);
  $pdf->SetFont('arial', '', $iTamFonte);
}


// RELATORIOS SINTETICO
if ($oParametros->iTipoRelatorio ==  1) {

$aLinhas  = array();
for ($i = 0; $i < $iNumRows; $i++) {

  $oItem = db_utils::fieldsMemory($rsSaidas, $i);

  array_push($aLinhas, $oItem);
  unset($oItem);
}

foreach ($aLinhas as $oLinha) {

  $sUnidade = $oLinha->m61_abrev;//$oItem->getUnidade()->getAbreviacao();

  if ($pdf->gety() > $pdf->h -30 || $lEscreveHeader) {

    $pdf->AddPage();
    $pdf->setfont('arial', 'b', 8);
    $pdf->Cell(15, $iAlt, "Material"             ,"RTB", 0, "C", 1);
    $pdf->Cell(70, $iAlt, "Descrição do Material", 1, 0, "C", 1);
    $pdf->Cell(13, $iAlt, "Unidade"              , 1, 0, "C", 1);
    $pdf->Cell(37, $iAlt, "Depto Origem"         , 1, 0, "C", 1);
    $pdf->Cell(32, $iAlt, "Depto Destino"        , 1, 0, "C", 1);
    $pdf->Cell(45, $iAlt, "Lançamento"           , 1, 0, "C", 1);
    $pdf->Cell(15, $iAlt, "Data"                 , 1, 0, "C", 1);
    $pdf->Cell(18, $iAlt, "Preço Médio"          , 1, 0, "C", 1);
    $pdf->Cell(15, $iAlt, "Quant."               , 1, 0, "C", 1);
    $pdf->Cell(20, $iAlt, "Valor Total"          , "LTB", 1, "C", 1);
    $lEscreveHeader = false;
    $pdf->setfont('arial', '', 6);
  }
  $pdf->Cell(15, $iAlt, substr($oLinha->m70_codmatmater, 0, 40), "RTB", 0, "R");
  $pdf->Cell(70, $iAlt, substr($oLinha->m60_descr, 0, 50), 1, 0, "L");
  $pdf->Cell(13, $iAlt, $sUnidade, 1, 0, "l", 0);
  $pdf->Cell(37, $iAlt, substr($oLinha->m70_coddepto." - ".$oLinha->descrdepto , 0, 27), 1, 0, "L");


  $iDeptoDestino = $oLinha->m40_depto;
  if ($oLinha->m83_coddepto != "") {
    $iDeptoDestino = $oLinha->m83_coddepto;
  }
  /**
   * consultamos a descricao do departamento de origem.
   */
  if ($iDeptoDestino !="") {

    $sSqlDeptoDestino = "select descrdepto from db_depart where coddepto = {$iDeptoDestino}";
    $rsDeptoDestino   = db_query($sSqlDeptoDestino);
    $iDeptoDestino    = "{$iDeptoDestino} - ".db_utils::fieldsMemory($rsDeptoDestino, 0)->descrdepto;
  }

  $pdf->Cell(32, $iAlt, substr($iDeptoDestino, 0, 24), 1, 0, "L");
  $iCodigoLancamento = $oLinha->m41_codmatrequi;
  if ($oLinha->m41_codmatrequi == "") {
    $iCodigoLancamento = $oLinha->m80_codigo;
  }

  $nValorPrecoMedio = $oLinha->precomedio;
  if (in_array($oLinha->m80_codtipo, array(4, 19))) {
    $nValorPrecoMedio = $oLinha->m89_valorunitario;
  }

  $pdf->Cell(45, $iAlt, substr($oLinha->m81_descr,0,30 )."(".$iCodigoLancamento.")", 1, 0, "L");
  $pdf->Cell(15, $iAlt, db_formatar($oLinha->m80_data, "d"), 1, 0, "C");
  $pdf->Cell(18, $iAlt, number_format($nValorPrecoMedio, $iParametroNumeroDecimal), 1, 0, "R");
  $pdf->Cell(15, $iAlt, $oLinha->qtde, 1, 0, "R");
  $pdf->Cell(20, $iAlt, db_formatar($oLinha->m89_valorfinanceiro, 'f'), "LTB", 1, "R");
  $nValorTotal += $oLinha->m89_valorfinanceiro;
  $nTotalItens += $oLinha->qtde;

  }

  $pdf->setfont('arial', 'b', 6);
  $pdf->Cell(240, $iAlt, "Total", "RTB", 0, "R", 1);
  $pdf->Cell(20, $iAlt, $nTotalItens, 1, 0, "R", 1);
  $pdf->Cell(20, $iAlt, db_formatar($nValorTotal, "f"), "LTB", 1, "R", 1);

}

$pdf->Output();

function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime, $oParametros)
{
    $iTamFonte = 6;
    if ($oPdf->GetY() > $oPdf->h - 45 || $lImprime) {
        $oPdf->SetFont('arial', 'b', $iTamFonte);

        if (!$lImprime) {
            $oPdf->AddPage("L");
        }

        $oPdf->setfont('arial', 'b', $iTamFonte);

        $oPdf->cell(5,  $iAlturalinha, "Material"             , "", 0, "C", 0);
        $oPdf->cell(90, $iAlturalinha, "Descrição do Material", "", 0, "C", 0);
        $oPdf->cell(10,  $iAlturalinha, "Unidade"              , "", 0, "C", 0);
        $oPdf->Cell(45,  $iAlturalinha, "Depto Origem"         , "", 0, "C", 0);
        $oPdf->cell(45,  $iAlturalinha, "Lançamento"           , "", 0, "C", 0);
        $oPdf->cell(15,  $iAlturalinha, "Data"                 , "", 0, "C", 0);
        $oPdf->cell(35,  $iAlturalinha, "Preço Médio"          , "", 0, "C", 0);
        $oPdf->cell(15,  $iAlturalinha, "Quant"                , "", 0, "C", 0);
        $oPdf->cell(20,  $iAlturalinha, "Valor Total"          , "", 1, "C", 0);
        $oPdf->SetFont('arial', '', $iTamFonte);
    }
}

?>
