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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
  
if($iPeriodo == 1){
  $varPer = " dtcredito ";
  $chead6  = "Data do Crédito:";
} else {
  $varPer = " dtpago ";
  $chead6  = "Data do Pagamento";
}
$sqlbco    = "";
$sqlcodret = "";

if($iCodRet != ''){  
  $sqlcodret = " AND disbanco.codret = $iCodRet ";
  $head3 = "POR CODRET";
  $head4 = "CodRet: $iCodRet";
}
if($iBco != '' AND $iAgencia != ''){
  $sqlbco    = " AND disbanco.k15_codbco = $iBco AND disbanco.k15_codage = '$iAgencia' ";
  $head3 = "POR BANCO E AGÊNCIA";
  $head4 = "Banco: $iBco - Agência: $iAgencia";
} 
if($iBco != '' AND $iAgencia != '' AND $iCodRet != ''){
  $head3 = "POR BANCO, AGÊNCIA E CODRET";
  
}

$pdf = new PDF('L');
$pdf->Open();
$pdf->AliasNbPages();

$sql = "SELECT DISTINCT
               arrematric.k00_matric,
               disbanco.codret,
               disbanco.vlrtot,
               disbanco.dtpago,
               disbanco.dtcredito,
               disbanco.k00_numpre,
               recibopaga.k00_numpar,
               d63_agencia::integer,
               d63_conta::integer,
               arqret
FROM disbanco
INNER JOIN recibopaga ON disbanco.k00_numpre = recibopaga.k00_numnov                                                
INNER JOIN arrematric ON recibopaga.k00_numpre = arrematric.k00_numpre
INNER JOIN debcontapedidomatric ON arrematric.k00_matric = debcontapedidomatric.d68_matric
INNER JOIN debcontapedido ON debcontapedidomatric.d68_codigo = debcontapedido.d63_codigo
INNER JOIN disarq ON disbanco.codret = disarq.codret
WHERE 1=1 AND $varPer BETWEEN '$iDatai' AND '$iDataf' $sqlbco $sqlcodret
ORDER BY $varPer";

//echo $sql; exit;
$result = db_query($sql) or die("Erro realizando consulta : ".$sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem lançamentos o período de '.db_formatar($iDatai, 'd').' a '.db_formatar($iDataf, 'd'));
}

$head2 = "RELATÓRIO DE PAGAMENTOS POR DEBITO EM CONTA";
$head5 = "Período : ".db_formatar($iDatai, 'd')." a ".db_formatar($iDataf, 'd');
$head6 = "Por: $chead6";

$pdf->ln(2);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(18,  6, "MATRÍCULA",  1, 0, "C", 0);
$pdf->Cell(20,  6, "NUMPRE",     1, 0, "C", 0);
$pdf->Cell(15,  6, "NUMPAR",     1, 0, "C", 0);
$pdf->Cell(20,  6, "VLR PAGTO",  1, 0, "C", 0);
$pdf->Cell(20,  6, "DT PAGTO",   1, 0, "C", 0);
$pdf->Cell(16,  6, "AGÊNCIA",    1, 0, "C", 0);
$pdf->Cell(16,  6, "CONTA",      1, 0, "C", 0);
$pdf->Cell(21,  6, "DT CRÉDITO", 1, 0, "C", 0);
$pdf->Cell(15,  6, "CODRET",     1, 0, "C", 0);
$pdf->Cell(118, 6, "ARQUIVO",    1, 1, "C", 0);

$pdf->SetFont('Arial', '', 8);
$vlrtotal = 0;
for ($i = 0; $i < $xxnum; $i ++) {
  db_fieldsmemory($result, $i);

  $pdf->Cell(18,  6, $k00_matric,                  1, 0, "R", 0);
  $pdf->Cell(20,  6, $k00_numpre,                  1, 0, "R", 0);
  $pdf->Cell(15,  6, $k00_numpar,                  1, 0, "R", 0);
  $pdf->Cell(20,  6, db_formatar($vlrtot,"f"),     1, 0, "R", 0);
  $pdf->Cell(20,  6, db_formatar($dtpago, 'd'),    1, 0, "C", 0);
  $pdf->Cell(16,  6, $d63_agencia,                 1, 0, "R", 0);
  $pdf->Cell(16,  6, $d63_conta,                   1, 0, "R", 0);
  $pdf->Cell(21,  6, db_formatar($dtcredito, 'd'), 1, 0, "C", 0);
  $pdf->Cell(15,  6, $codret,                      1, 0, "R", 0);
  $pdf->Cell(118, 6, $arqret,                      1, 1, "L", 0);
  $vlrtotal += $vlrtot;
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(279, 6, "VALOR TOTAL (R$):".db_formatar($vlrtotal,"f"), 1, 1, "L", 0);
$pdf->Cell(279, 6, "TOTAL DE LANÇAMENTOS: ".$xxnum, 1, 1, "L", 0);
$pdf->Output();