<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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

$pdf = new PDF('P');
$pdf->Open();
$pdf->AliasNbPages();

$sqlArquivo = "SELECT *, nome
FROM
  (SELECT *
   FROM fis_levantlotearq
   INNER JOIN fis_levantlotearqauto ON y122_codigo = y125_levantlotearq
   INNER JOIN fiscalizacao.fis_autoinscr ON y52_codauto = y125_codauto
   UNION ALL SELECT *
   FROM fis_levantlotearq
   INNER JOIN fis_levantlotearqlanc ON y122_codigo = y126_levantlotearq
   INNER JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = y126_codlanc) AS x
   INNER JOIN db_usuarios ON y122_usuario = id_usuario
   INNER JOIN fiscalizacao.fis_tipofiscaliza ON y122_tipofiscal = y27_codtipo
WHERE y122_codigo = $codlote
ORDER BY y125_codauto
";

// echo $sqlArquivo ; exit;
$resultArquivo = db_query($sqlArquivo) or die("Erro realizando consulta : ".$sqlArquivo);
$xxnum = pg_numrows($resultArquivo);
if ($xxnum == 0) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem registros para esse Levantamento!');
}
if(pg_result($resultArquivo,0,1) == 1){
  $tpArquivo = "AUTO DE INFRAÇÃO";
  $nmLanc    = "COD. AUTO";
} else {
  $tpArquivo = "NOTIFICAÇÃO DE LANÇAMENTO";
  $nmLanc    = "COD. NOTIF.";
}



$head2 = "RELATÓRIO DE LEVANTAMENTO";
$head3 = "POR ARQUIVO EM LOTE";
$head4 = "Código do Lote: ".$codlote." - ". $tpArquivo;
$head5 = "Data do Lote: ".db_formatar(pg_result($resultArquivo,0,2), 'd');
$head6 = "Arquivo do Lote: ".pg_result($resultArquivo,0,10);
$head7 = "Tipo da Fiscalização: ".pg_result($resultArquivo,0,31);

$pdf->ln(2);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(30, 6, "INSCRIÇÃO",       1, 0, "C", 0);
$pdf->Cell(30, 6, $nmLanc,           1, 0, "C", 0);
$pdf->Cell(40, 6, "PROCESSO FISCAL", 1, 0, "C", 0);
$pdf->Cell(40, 6, "LEVANTAMENTO",    1, 0, "C", 0);
$pdf->Cell(30, 6, "PRÉ CÁLCULO",     1, 1, "C", 0);

$pdf->SetFont('Arial', '', 8);

for ($i = 0; $i < $xxnum; $i ++) {

  db_fieldsmemory($resultArquivo, $i);

  $pdf->Cell(30, 6, $y52_inscr                          , 1, 0, "C", 0);
  $pdf->Cell(30, 6, $y52_codauto                        , 1, 0, "C", 0);
  $pdf->Cell(40, 6, $y125_procfiscal                    , 1, 0, "C", 0);
  $pdf->Cell(40, 6, $y125_levanta                       , 1, 0, "C", 0);
  $pdf->Cell(30, 6, "Ok"                                , 1, 1, "C", 0);

}

$pdf->Output();
