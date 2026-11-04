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
 
include(modification("fpdf151/pdf.php"));
include(modification("fpdf151/assinatura.php"));
include(modification("libs/db_sql.php"));
include(modification("libs/db_utils.php"));
include(modification("libs/db_app.utils.php"));
include(modification("libs/db_libcontabilidade.php"));
include(modification("libs/db_liborcamento.php"));
include(modification("libs/JSON.php"));
include(modification("classes/db_empresto_classe.php"));
include(modification("classes/db_orcparamrel_classe.php"));
include(modification("classes/db_conrelinfo_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_db_config_classe.php"));
include(modification("classes/db_orcparamelemento_classe.php"));  
require_once(modification("model/linhaRelatorioContabil.model.php"));
require_once(modification("model/relatorioContabil.model.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("classes/db_caiparametro_classe.php"));
  
$classinatura       = new cl_assinatura;
$clempresto         = new cl_empresto;
$orcparamrel        = new cl_orcparamrel;
$clconrelinfo       = new cl_conrelinfo;
$cldb_config        = new cl_db_config;
$clorcparamelemento = new cl_orcparamelemento();
$clcaiparametro     = new cl_caiparametro();


$iAnoUsu     = db_getsession("DB_anousu");
$oJson       = new Services_JSON();
$oParametros = $oJson->decode(str_replace("\\","",$_GET["sFiltros"]));
$sPeriodo    = $oParametros->periodo;
$aRecursos   = $oParametros->sRecursos;
$sPeriodoBanco = implode("-", array_reverse(explode("/", $oParametros->periodo)));

/*
 * Instancia a classe para retornar do objeto, a
 * propriedade munic, que tras o nome do municipio
 */

$oMunicipio = db_stdClass::getDadosInstit(db_getsession("DB_instit"));
$sMunicipio = $oMunicipio->munic;

/*
 * SQL para processar os dados
 * Baseado nas seleções do usuário
 * 
 */

// define a clausula where conforme os recursos na lista
if($aRecursos != "") {
	$sWhere = "where k111_recurso in({$aRecursos})";
}else {
	$sWhere = "";
}

$sSqlRecursos  = "SELECT * ,  k111_tabplan, "; 
$sSqlRecursos .= "       k02_descr, ";
$sSqlRecursos .= "       o15_codigo, ";
$sSqlRecursos .= "       c60_estrut, ";
//$sSqlRecursos .= "       k02_codigo, ";
$sSqlRecursos .= "       o15_descr,k111_debitoinicial,k111_creditoinicial "; 
$sSqlRecursos .= "from tabplansaldorecurso "; 
$sSqlRecursos .= "       inner join orctiporec on orctiporec.o15_codigo = tabplansaldorecurso.k111_recurso "; 
$sSqlRecursos .= "       inner join tabplan    on tabplan.k02_codigo    = tabplansaldorecurso.k111_tabplan "; 
$sSqlRecursos .= "                                                        and tabplan.k02_anousu = {$iAnoUsu}"; 
$sSqlRecursos .= "       inner join tabrec         on tabrec.k02_codigo     = tabplan.k02_codigo ";
$sSqlRecursos .= "       inner join conplanoreduz  on tabplan.k02_reduz     = c61_reduz ";
$sSqlRecursos .= "                                and c61_anousu = tabplan.k02_anousu ";
$sSqlRecursos .= "                                and c61_instit = ".db_getsession("DB_instit");
$sSqlRecursos .= "       inner join conplano       on c61_codcon = c60_codcon ";
$sSqlRecursos .= "                                and c61_anousu = c60_anousu "; 
$sSqlRecursos .= "{$sWhere}"; 
$sSqlRecursos .= "order by o15_codigo";
$rsRecursos      = db_query($sSqlRecursos);
$iTotalRecursos  = pg_num_rows($rsRecursos);
$oLinhas           = new stdClass();
$oLinhas->recursos = array();


for ($iLinhaRecurso = 0; $iLinhaRecurso < $iTotalRecursos; $iLinhaRecurso++) {

   $oRecursos = db_utils::fieldsMemory($rsRecursos, $iLinhaRecurso);
   if (!isset($oLinhas->recursos[$oRecursos->o15_codigo])) {
   	  	
     $oRecurso = new stdClass();
   	 $oRecurso->codigo    = $oRecursos->o15_codigo;
   	// $oRecurso->estrut    = $oRecursos->c60_estrut;
   	 $oRecurso->descricao = $oRecursos->o15_descr.$oRecursos->c60_estrut;
   	 $oRecurso->contas    = array();
   	 $oLinhas ->recursos[$oRecursos->o15_codigo] = $oRecurso;   	 
   }
   
   //excutar sql do saldo... preencher propriedades
   $sSqlConta    = "select * from fc_saltessaldoextra({$oRecursos->k111_tabplan} ,'{$sPeriodoBanco}',{$oRecurso->codigo})";
   $rsContas     = db_query($sSqlConta);      	
   $oValores     = db_utils::fieldsMemory($rsContas, 0);
   
   $oConta               = new stdClass();  
   $oConta->codigo       = $oRecursos->k111_tabplan;
   $oConta->descricao    = $oRecursos->k02_descr;
   $oConta->estrut       = $oRecursos->c60_estrut;
   $oConta->codDescr     = $oRecursos->k02_codigo;
   //$oConta->saldoinicial = $oValores->rnsaldoanteriordebito - $oValores->rnsaldoanteriorcredito;
   $oConta->saldoinicial = $oRecursos->k111_debitoinicial - $oRecursos->k111_creditoinicial;
   $oConta->recebimentos = $oValores->rnvalorcredito;
   $oConta->pagamentos   = $oValores->rnvalordebito;
   $oConta->saldoreceber = $oValores->rnsaldocredito;
   $oConta->saldopagar   = $oValores->rnsaldodebito;   
   
   array_push($oLinhas->recursos[$oRecursos->o15_codigo]->contas, $oConta);
   
} 

$head3 = "Movimentação de Contas Extras";
$head4 = "Posição Até : {$sPeriodo} ";

/*
 * Select na caiparametro, para pegar o campo k29_datasaldocontaextra , para
 * adicionar na nota padrão do relatorio.
 */
$sInstit        = db_getsession("DB_instit");
$sSqlNotaPadrao = $clcaiparametro->sql_query_file("","k29_datasaldocontasextra","","k29_instit = {$sInstit} ");
$rsNotaPadrao   = $clcaiparametro->sql_record($sSqlNotaPadrao);
if ($clcaiparametro->numrows > 0) {
	
	db_fieldsmemory($rsNotaPadrao,0);
	$sDataImplantacao = db_formatar($k29_datasaldocontasextra, "d");
	$sNotaPadrao      = " (*) Entende-se por saldo inicial o valor indicado na implantação do controle do saldo e ";
	$sNotaPadrao     .= "movimentações de natureza Extra-Orçamentária. Os valores representam a posição do saldo ";
	$sNotaPadrao     .= "das contas implantadas em {$sDataImplantacao}. Os saldos a pagar estão apresentados em ";
	$sNotaPadrao     .= "valores positivos e os a receber em valores negativos.";
}


$sFonte = "Arial";
$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);
$oPdf->AddPage();
$oPdf->SetFont($sFonte, "", 6);
$oPdf->SetFillColor(255);

$iAlturalinha = 4;
$iNumRows = 20;
$iRecursos = 5;

/*
 * laço para repetir total de recursos
 * selecionados
 */
foreach ($oLinhas->recursos as $oRecurso) {
 $oPdf->SetFont('arial', 'b', 6);
 $oPdf->Cell(100, 5, "{$oRecurso->codigo} - {$oRecurso->descricao}", "", 1, "L", 0);
 imprimirCabecalho($oPdf, $iAlturalinha, true);
  
/*
 * Laço que retorna o numero de linhas filtradas pelo 
 * recurso selecionado
 */
 $iTotal_Saldo_Inicial = 0;
 $iTotal_Recebimentos  = 0;
 $iTotal_Pagamentos    = 0;
 $iTotal_Saldo_Receber = 0;
 $iTotal_Pagar         = 0;
 
	foreach ($oRecurso->contas as $oConta) {
		////totais do recurso
		 $iTotal_Saldo_Inicial = $iTotal_Saldo_Inicial + $oConta->saldoinicial;
		 $iTotal_Recebimentos  = $iTotal_Recebimentos  + $oConta->recebimentos;
		 $iTotal_Pagamentos    = $iTotal_Pagamentos    + $oConta->pagamentos;
		 $iTotal_Saldo_Receber = $iTotal_Saldo_Receber + $oConta->saldoreceber;
		 $iTotal_Pagar         = $iTotal_Pagar         + $oConta->saldopagar;
		 $sContaRecurso        = $oConta->codDescr." - ".$oConta->estrut." - ".$oConta->descricao;
		
	   $oPdf->SetFont('arial', '', 6);
	   //$oPdf->Cell(25, $iAlturalinha, $oConta->codigo,                         "TRB",  0, "C", 0);
	   $oPdf->Cell(75, $iAlturalinha, $sContaRecurso,                          "TRB",  0, "L", 0);
	   $oPdf->Cell(20, $iAlturalinha, db_formatar($oConta->saldoinicial, "f"), "TRB",  0, "R", 0);
	   $oPdf->Cell(20, $iAlturalinha, db_formatar($oConta->recebimentos, "f"), "TLRB", 0, "R", 0);
	   $oPdf->Cell(25, $iAlturalinha, db_formatar($oConta->pagamentos,   "f"), "TLRB", 0, "R", 0);
	   $oPdf->Cell(25, $iAlturalinha, db_formatar($oConta->saldoreceber, "f"), "TLRB", 0, "R", 0);
	   $oPdf->Cell(25, $iAlturalinha, db_formatar($oConta->saldopagar,   "f"), "TLB",  1, "R", 0);   
	  
	 imprimirCabecalho($oPdf, $iAlturalinha, false);
	 //imprimeInfoProxPagina($oPdf, $iAlturalinha, false);
	} 
  $oPdf->ln();
  
  $oPdf->SetFont('arial', 'b', 6);
  $oPdf->Cell(75, $iAlturalinha, "Total do Recurso",                      "TRB",   0, "C", 1);
  $oPdf->SetFont('arial', '', 6);
  $oPdf->Cell(20, $iAlturalinha, db_formatar($iTotal_Saldo_Inicial, "f"), "TLRB",  0, "R", 1);
  $oPdf->Cell(20, $iAlturalinha, db_formatar($iTotal_Recebimentos,  "f"), "TLRB",  0, "R", 1);
  $oPdf->Cell(25, $iAlturalinha, db_formatar($iTotal_Pagamentos,    "f"), "TLRB",  0, "R", 1);
  $oPdf->Cell(25, $iAlturalinha, db_formatar($iTotal_Saldo_Receber, "f"), "TLRB",  0, "R", 1);
  $oPdf->Cell(25, $iAlturalinha, db_formatar($iTotal_Pagar,         "f"), "TLB",   1, "R", 1); 

  $oPdf->ln();
  $oPdf->ln();
}    
$oPdf->ln();  
$oPdf->SetFont('arial', '', 6);  
$oPdf->MultiCell(170, $iAlturalinha, $sNotaPadrao );

    
$oPdf->Output();

/**
 * Impime cabecalho do relatorio
 *
 * @param Object  type $oPdf
 * @param Integer type $iAlt
 * @param Boolean type $lImprime
 */
function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime) {
  
  if ( $oPdf->GetY() > $oPdf->h - 25 || $lImprime ) {
    
    $oPdf->SetFont('arial', 'b', 6);
    if ( !$lImprime ) {
      
      $oPdf->AddPage("P");
    } else {
      
    $oPdf->SetFillColor("240");
    }
/*
 * Cabeçalho a ser Repetido nas paginas
 */  
    //$oPdf->Cell(25, $iAlturalinha, "CONTA",               "TRB",  0, "C", 1);
    $oPdf->Cell(75, $iAlturalinha, "Conta / Recurso",     "TRB", 0, "C", 1);
    $oPdf->Cell(20, $iAlturalinha, "SALDO INICIAL (*)",   "TLRB", 0, "C", 1);
    $oPdf->Cell(20, $iAlturalinha, "RECEBIMENTOS",        "TLRB", 0, "C", 1);
    $oPdf->Cell(25, $iAlturalinha, "PAGAMENTOS",          "TLRB", 0, "C", 1);
    $oPdf->Cell(25, $iAlturalinha, "SALDO A RECEBER",     "TLRB", 0, "C", 1);
    $oPdf->Cell(25, $iAlturalinha, "SALDO A PAGAR",       "TLB",  1, "C", 1);    
       
  }
}

/**
 * Impime informacao da proxima pagina no relatorio
 *
 * @param Object type $oPdf
 * @param Integer type $iAlt
 * @param Boolean type $lInicio
 */
function imprimeInfoProxPagina($oPdf, $iAlturalinha, $lImprime) {
  
  if ( $oPdf->GetY() > $oPdf->h - 31 || $lImprime ) {
    
    $oPdf->SetFont('arial', '', 6);
    if ( $lImprime ) {
      $oPdf->Cell(190, ($iAlturalinha*2), 'Continuação '.($oPdf->PageNo())."/{nb}",          'T', 1, "R", 0);
    } else {
      //die('aqui');
      $oPdf->Cell(190, ($iAlturalinha*3), 'Continua na página '.($oPdf->PageNo()+1)."/{nb}", 'T', 1, "R", 0);
      imprimirCabecalho($oPdf, $iAlturalinha, false,'');
    }
  }
} 
?>