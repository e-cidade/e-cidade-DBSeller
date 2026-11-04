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
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));

$total        = 0;
$troca        = 1;
$iAlturalinha = 4;
$iFonte       = 6;
$oGet         = db_utils::postMemory($_GET);

//$iSolicitacao = $oGet->iSolicitacao;
$sItens = db_getsession("sInconsistencia");//$oGet->sItens;

$pdf = new PDF("P");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false);
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 6);

$head2  = "RELATÓRIO DE INCONSISTÊNCIAS";
$head3  = "Itens da solicitção sem saldo disponível.";

$pdf->AddPage("P");

$pdf->setfont('arial','b',$iFonte);
$pdf->cell(117 ,  $iAlturalinha, "Ítens",     "LTBR",  1, "C", 1);

/**
 * se haver erro ou aviso criamos ele na seção para o relatorio
 * e a variavel $iInconsistencia passa para 1
 */
$aInconsistencia  = explode(",", $sItens);

foreach ($aInconsistencia as $oIndiceDados => $oValorDados) {
  
	$pdf->setfont('arial','',$iFonte);
	$pdf->cell(117 ,  $iAlturalinha, "{$oValorDados}"           ,  "LTBR",  1, "L", 0);
	imprimirCabecalho($pdf, $iAlturalinha, false);
}

$pdf->output();

function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime) {

	if ( $oPdf->GetY() > $oPdf->h - 25 || $lImprime ) {

		$oPdf->SetFont('arial', 'b', 6);

		if ( !$lImprime ) {
			 
			$oPdf->AddPage("P");
		}

		$oPdf->setfont('arial','b',6);
    $oPdf->cell(117 ,  $iAlturalinha, "Ítens",     "LTBR",  1, "C", 1);
		 
	}
}

?>