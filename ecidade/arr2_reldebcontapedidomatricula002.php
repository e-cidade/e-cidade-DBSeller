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

if($iSituacao > 1){
  $sqlSit = " AND d63_statusatual = $iSituacao ";
  if($iSituacao == 2){
    $nSit = "Ativos";
  } else{
    $nSit = "Inativos";
  }
} else { 
  $sqlSit = "";
  $nSit = "Todos";
}

$pdf = new PDF('P');
$pdf->Open();
$pdf->AliasNbPages();


$sql = "SELECT d83_sequencial,
       d83_debcontapedido,
       d83_instit,
       d83_banco::integer as d83_banco,
       d83_agencia::integer as d83_agencia,
       d83_conta::integer as d83_conta,
       d83_datalanc,
       d83_horalanc,
       d83_status,
       d83_acao,
       d83_idempresa,
       d83_codret,
       arqret,
       login
FROM debcontapedidohistorico
INNER JOIN disarq ON codret = d83_codret
INNER JOIN db_usuarios ON disarq.id_usuario = db_usuarios.id_usuario
WHERE d83_idempresa::integer = $iMatric
AND d83_datalanc BETWEEN '$iDatai' AND '$iDataf'
ORDER BY 1 DESC";

//echo $sql; exit;
$result = db_query($sql) or die("Erro realizando consulta : ".$sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem lançamentos no período de '.db_formatar($iDatai, 'd').' a '.db_formatar($iDataf, 'd'));
}

$head3 = "RELATÓRIO DE CADASTRO EM CONTA POR MATRÍCULA";
$head4 = "";
$head5 = "Matrícula: ".$iMatric;
$head6 = "Período : ".db_formatar($iDatai, 'd')." a ".db_formatar($iDataf, 'd');

$pdf->ln(2);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);

$pdf->Cell(16, 6, "DATA",    1, 0, "C", 0);
$pdf->Cell(12, 6, "SIT.",    1, 0, "C", 0);
$pdf->Cell(12, 6, "AÇÃO",    1, 0, "C", 0);
$pdf->Cell(16, 6, "BANCO",   1, 0, "C", 0);
$pdf->Cell(16, 6, "AGÊNCIA", 1, 0, "C", 0);
$pdf->Cell(20, 6, "CONTA",   1, 0, "C", 0);
$pdf->Cell(13, 6, "CODRET",  1, 0, "C", 0);
$pdf->Cell(52, 6, "ARQUIVO", 1, 0, "C", 0);
$pdf->Cell(35, 6, "LOGIN",   1, 1, "C", 0);


$pdf->SetFont('Arial', '', 8);

$qtdinc = 0;
$qtdalt = 0;
$qtdexc = 0;

for ($i = 0; $i < $xxnum; $i ++) {
  $status = "";
  $acao   = "";

  db_fieldsmemory($result, $i);

  if($d83_status == 1) {
    $status = "PEND";
  } else if($d83_status == 2) {
    $status = "ATIV";
  } else if($d83_status == 3) {
    $status = "INAT";
  }

  if($d83_acao == 1) {
    $acao = "INC";
    $qtdinc++;
  } else if($d83_acao == 2) {
    $acao = "ALT";
    $qtdalt++;
  } else if($d83_acao == 3) {
    $acao = "EXC";
    $qtdexc++;
  } 

  $pdf->Cell(16, 6, db_formatar($d83_datalanc, 'd'), 1, 0, "C", 0);
  $pdf->Cell(12, 6, $status,                         1, 0, "L", 0);
  $pdf->Cell(12, 6, $acao,                           1, 0, "L", 0);
  $pdf->Cell(16, 6, $d83_banco,                      1, 0, "R", 0);
  $pdf->Cell(16, 6, $d83_agencia,                    1, 0, "R", 0);
  $pdf->Cell(20, 6, $d83_conta,                      1, 0, "R", 0);
  $pdf->Cell(13, 6, $d83_codret,                     1, 0, "R", 0);
  $pdf->Cell(52, 6, substr($arqret,0,28),            1, 0, "L", 0);
  $pdf->Cell(35, 6, $login,                          1, 1, "L", 0);
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(192, 6, "TOTAL DE REGISTROS: ".$xxnum, 1, 1, "L", 0);
$pdf->Output();