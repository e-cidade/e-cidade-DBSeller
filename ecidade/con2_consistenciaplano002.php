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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_conplanoreduz_classe.php"));

$oGet  = db_utils::postMemory($_GET);
$head3 = "Consistência do Plano de Contas";
$head4 = "";
$head5 = "EXERCICIO: ".db_getsession("DB_anousu");

$sInstit    = str_replace("-", ",", $oGet->instit);
$sSqlPlano  = "select c60_codcon, c60_estrut, c60_descr, c61_reduz";
$sSqlPlano .= "  from conplano ";
$sSqlPlano .= "       inner join conplanoreduz on c61_codcon = c60_codcon";
$sSqlPlano .= "                               and c61_anousu = c60_anousu ";

$sWhereDespesas  = " where (c61_contrapartida = 0 or c61_contrapartida is null) ";
$sWhereDespesas .= "   and c60_estrut ilike '3%' ";
$sWhereDespesas .= "   and c60_anousu    = ".db_getsession("DB_anousu");
$sWhereDespesas .= "   and c61_instit    in($sInstit) ";

$sSqlDespesa  = $sSqlPlano.$sWhereDespesas." order by c60_estrut";
$rsDespesas   = db_query($sSqlDespesa);
$aDespesas    = db_utils::getCollectionByRecord($rsDespesas);

$sWhereReceitas  = " where (c61_codigo = 1) ";
$sWhereReceitas .= "   and c60_estrut ilike '4%' ";
$sWhereReceitas .= "   and c60_anousu    = ".db_getsession("DB_anousu");
$sWhereReceitas .= "   and c61_instit    in($sInstit) ";

$sSqlReceitas = $sSqlPlano.$sWhereReceitas." order by c60_estrut";
$rsReceitas   = db_query($sSqlReceitas);
$aReceitas    = db_utils::getCollectionByRecord($rsReceitas);
   
$pdf = new PDF();
$pdf->Open();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->setfillcolor(245);
$pdf->SetAutoPageBreak(0);
$sFonte = "arial";
$iAlt   = 3; 
$pdf->SetFont($sFonte, "", 8);
$sSStringDespesas  = "As contas de despesa devem ter indicada a informação da conta de contrapartida. ";
$sSStringDespesas .= "esta conta será utilizada no momento em que a Instituição estiver liquidando empenhos. ";
$sSStringDespesas .= "Se esta conta não for informada os lançamentos contábeis ficarão incompletos. ";
$sSStringDespesas .= "Para proceder este cadastro, acesse o menu CONTABILIDADE > CADASTROS > PLANO DE CONTAS > ";
$sSStringDespesas .= "ALTERAÇÃO , selecione as contas listadas abaixo e informe a contrapartida na aba 'reduzidos'.";

$pdf->MultiCell(190, 3, $sSStringDespesas, 0, "J", 0,10);
$pdf->SetFont($sFonte, "b", 7);
$pdf->Cell(5,$iAlt, '',0,1);
$pdf->Cell(15, $iAlt, "Código", "TBR", 0, "C", 1);
$pdf->Cell(40, $iAlt, "Estrutural", 1, 0, "C", 1);
$pdf->Cell(110, $iAlt, "Descrição", 1, 0, "C", 1);
$pdf->Cell(25, $iAlt, "Reduzido", "TBL", 1, "C", 1);
$pdf->SetFont($sFonte, "", 6);
foreach ($aDespesas as $oDespesa) {
  
  if ($pdf->getY() > $pdf->h - 15) {
    
    $pdf->AddPage();
    $pdf->SetFont($sFonte, "b", 7);
    $pdf->Cell(15, $iAlt, "Código", "TBR", 0, "C", 1);
    $pdf->Cell(40, $iAlt, "Estrutural", 1, 0, "C", 1);
    $pdf->Cell(110, $iAlt, "Descrição", 1, 0, "C", 1);
    $pdf->Cell(25, $iAlt, "Reduzido", "TBL", 1, "C", 1);
    $pdf->SetFont($sFonte, "", 6);    
  }
  
  $pdf->Cell(15, $iAlt, $oDespesa->c60_codcon, "TBR", 0, "R");
  $pdf->Cell(40, $iAlt, $oDespesa->c60_estrut, 1, 0, "C");
  $pdf->Cell(110, $iAlt,$oDespesa->c60_descr, 1, 0, "L");
  $pdf->Cell(25, $iAlt, $oDespesa->c61_reduz, "TBL", 1, "R");
  
}
$sStringReceita  = "As contas contábeis de Receita devem ter relação direta com Recursos Vinculados. ";
$sStringReceita .= "Na importação foi indicado um recurso padrão e você deve revisar as contas abaixo listadas, ";
$sStringReceita .= "indicando através do menu CONTABILIDADE > CADASTROS > PLANO DE CONTAS > ALTERAÇÃO o código do ";
$sStringReceita .= "recurso correto para cada registro, na aba 'reduzidos'. ";
$sStringReceita .= "Caso ainda não tenha cadastrado todos os recursos, acesse o menu: ";
$sStringReceita .= "ORÇAMENTO > CADASTROS > TIPOS DE RECURSO > INCLUSÃO.";

$pdf->AddPage();
$pdf->SetFont($sFonte, "", 8);
$pdf->MultiCell(190, 3, $sStringReceita, 0, "J", 0,10);
$pdf->Cell(5,$iAlt, '',0,1);
$pdf->SetFont($sFonte, "b", 7);
$pdf->Cell(15, $iAlt, "Código", "TBR", 0, "C", 1);
$pdf->Cell(40, $iAlt, "Estrutural", 1, 0, "C", 1);
$pdf->Cell(110, $iAlt, "Descrição", 1, 0, "C", 1);
$pdf->Cell(25, $iAlt, "Reduzido", "TBL", 1, "C", 1);
$pdf->SetFont($sFonte, "", 6);
foreach ($aReceitas as $oReceita) {
  
  if ($pdf->getY() > $pdf->h - 15) {
    
    $pdf->AddPage();
    $pdf->SetFont($sFonte, "b", 7);
    $pdf->Cell(15, $iAlt, "Código", "TBR", 0, "C", 1);
    $pdf->Cell(40, $iAlt, "Estrutural", 1, 0, "C", 1);
    $pdf->Cell(110, $iAlt, "Descrição", 1, 0, "C", 1);
    $pdf->Cell(25, $iAlt, "Reduzido", "TBL", 1, "C", 1);
    $pdf->SetFont($sFonte, "", 6);    
  }
  
  $pdf->Cell(15, $iAlt, $oReceita->c60_codcon, "TBR", 0, "R");
  $pdf->Cell(40, $iAlt, $oReceita->c60_estrut, 1, 0, "C");
  $pdf->Cell(110, $iAlt,$oReceita->c60_descr, 1, 0, "L");
  $pdf->Cell(25, $iAlt, $oReceita->c61_reduz, "TBL", 1, "R");
  
}
$pdf->Output();
?>