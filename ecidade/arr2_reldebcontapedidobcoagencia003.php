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


$pdf = new PDF('L');
$pdf->Open();
$pdf->AliasNbPages();

$sqlArquivo = "SELECT k15_codbco,
                      k15_codage,
                      arqret,
                      login
                 FROM disarq 
           INNER JOIN db_usuarios on disarq.id_usuario = db_usuarios.id_usuario
                WHERE codret = $codret";
$resultArquivo = db_query($sqlArquivo) or die("Erro realizando consulta : ".$sqlArquivo);  
 

$sql = "SELECT *, (SELECT arqret from disarq where codret = codretatual) as nomearqatual, (SELECT login from disarq inner join db_usuarios ON disarq.id_usuario = db_usuarios.id_usuario where codret = codretatual) as usuarioatual, (SELECT d83_datalanc||'||'||d83_status||'||'||d83_acao||'||'||d83_banco||'||'||d83_agencia||'||'||d83_conta::integer||'||'||d83_codret||'||'||arqret||'||'||d83_sequencial FROM debcontapedidohistorico 
    LEFT JOIN disarq ON d83_codret = codret
        WHERE d83_idempresa::integer = d63_empresaatual
          AND d83_sequencial <> sequencialatual order by 1 desc limit 1) as anterior
  FROM (SELECT d63_codigo AS d63_codigoatual,
       d63_banco::integer AS d63_bancoatual,
       d63_agencia AS d63_agenciaatual,
       d63_conta::integer AS d63_contaatual,
       d63_datalanc AS d63_datalancatual,
       d63_status AS d63_statusatual,
       d63_instit AS d63_institatual,
      (SELECT d83_acao
         FROM debcontapedidohistorico
        WHERE d83_debcontapedido = d63_codigo
     ORDER BY d83_sequencial DESC LIMIT 1) AS d63_acaoatual,
       d63_idempresa::integer as d63_empresaatual,
      (SELECT d83_sequencial
         FROM debcontapedidohistorico
        WHERE d83_debcontapedido = d63_codigo
     ORDER BY d83_sequencial DESC LIMIT 1) AS sequencialatual,
      (SELECT d83_codret
         FROM debcontapedidohistorico
        WHERE d83_debcontapedido = d63_codigo
     ORDER BY d83_sequencial DESC LIMIT 1) AS codretatual
  FROM debcontapedido
 WHERE d63_idempresa::integer IN
       (SELECT DISTINCT d83_idempresa::integer
          FROM debcontapedidohistorico
             WHERE d83_codret = $codret AND d63_instit = ".db_getsession("DB_instit")."
       ) ) as x ORDER BY d63_datalancatual";

//echo $sql; exit;
$result = db_query($sql) or die("Erro realizando consulta : ".$sql);
$xxnum = pg_numrows($result);
if ($xxnum == 0) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem lançamentos o período de '.db_formatar($iDatai, 'd').' a '.db_formatar($iDataf, 'd'));
}

$sqlPag = "SELECT arrematric.k00_matric,
               disbanco.codret,
               disbanco.vlrtot,
               disbanco.dtpago,
               disbanco.dtcredito,
               disbanco.k00_numpre,
               disbanco.k00_numpar,
               d63_agencia::integer,
               d63_conta::integer,
               arqret
FROM disbanco
INNER JOIN arrematric ON disbanco.k00_numpre = arrematric.k00_numpre
INNER JOIN debcontapedido ON k00_matric = d63_idempresa::integer
INNER JOIN disarq ON disbanco.codret = disarq.codret
WHERE disbanco.codret = $codret";

$resultPag = db_query($sqlPag) or die("Erro realizando consulta : ".$sqlPag);
$xxnumPag = pg_numrows($resultPag);

$head2 = "RELATÓRIO DE CADASTRO EM CONTA";
$head3 = "POR ARQUIVO PROCESSADO";
$head4 = "Banco: ".pg_result($resultArquivo,0,0)." - Agência: ".pg_result($resultArquivo,0,1);
$head5 = "Código do Arquivo: ".$codret;
$head6 = "Nome do Arquivo: ".pg_result($resultArquivo,0,2);
$head7 = "Usuário: ".pg_result($resultArquivo,0,3);

$pdf->ln(2);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(279, 6, "CADASTRO", 1, 1, "C", 0);
$pdf->MultiCell(19, 12, "MATRÍCULA", 'TBRL', 'C', 0, 0);
$iPosX = $pdf->GetX();
$iPosY = $pdf->GetY();
$pdf->SetXY($iPosX+19,$iPosY-12);
$pdf->Cell(66, 6, "ATUAL",     1, 0, "C", 0);
$pdf->Cell(194, 6, "ANTERIOR", 1, 1, "C", 0);
$iPosX = $pdf->GetX();
$pdf->SetX($iPosX+19);

/* ATUAL */
$pdf->Cell(16, 6, "DATA",    1, 0, "C", 0);
$pdf->Cell(10, 6, "SIT.",    1, 0, "C", 0);
$pdf->Cell(10, 6, "AÇÃO",    1, 0, "C", 0);
$pdf->Cell(14, 6, "AGÊNCIA", 1, 0, "C", 0);
$pdf->Cell(16, 6, "CONTA",   1, 0, "C", 0);

/* ANTERIOR */
$pdf->Cell(16, 6, "DATA",     1, 0, "C", 0);
$pdf->Cell(10, 6, "SIT.",     1, 0, "C", 0);
$pdf->Cell(10, 6, "AÇÃO",     1, 0, "C", 0);
$pdf->Cell(12, 6, "BANCO",    1, 0, "C", 0);
$pdf->Cell(14, 6, "AGÊNCIA",  1, 0, "C", 0);
$pdf->Cell(16, 6, "CONTA",    1, 0, "C", 0);
$pdf->Cell(13, 6, "CODRET",   1, 0, "C", 0);
$pdf->Cell(103, 6, "ARQUIVO", 1, 1, "C", 0);

$pdf->SetFont('Arial', '', 8);

$qtdinc = 0;
$qtdalt = 0;
$qtdexc = 0;

for ($i = 0; $i < $xxnum; $i ++) {
  $statusatual = "";
  $acaoatual   = "";
  $statusant   = "";
  $acaoant     = "";
  db_fieldsmemory($result, $i);

    if($d63_statusatual == 1) {
      $statusatual = "PEND";
    } else if($d63_statusatual == 2) {
      $statusatual = "ATIV";
    } else if($d63_statusatual == 3) {
      $statusatual = "INAT";
    }

    if($d63_acaoatual == 1) {
      $acaoatual = "INC";
      $qtdinc++;
    } else if($d63_acaoatual == 2) {
      $acaoatual = "ALT";
      $qtdalt++;
    } else if($d63_acaoatual == 3) {
      $acaoatual = "EXC";
      $qtdexc++;
    } 

    /* ATUAL */
  $pdf->Cell(19, 6, $d63_empresaatual                   , 1, 0, "R", 0);
  $pdf->Cell(16, 6, db_formatar($d63_datalancatual, 'd'), 1, 0, "C", 0);
  $pdf->Cell(10, 6, $statusatual                        , 1, 0, "L", 0);
  $pdf->Cell(10, 6, $acaoatual                          , 1, 0, "L", 0);
  $pdf->Cell(14, 6, trim($d63_agenciaatual)             , 1, 0, "R", 0);
  $pdf->Cell(16, 6, trim($d63_contaatual)               , 1, 0, "R", 0);

  $arr_ant = explode("||", $anterior);

    if($arr_ant[1] == 1) {
      $statusant = "PEND";
    } else if($arr_ant[1] == 2) {
      $statusant = "ATIV";
    } else if($arr_ant[1] == 3) {
      $statusant = "INAT";
    }

    if($arr_ant[2] == 1) {
      $acaoant = "INC";
    } else if($arr_ant[2] == 2) {
      $acaoant = "ALT";
    } else if($arr_ant[2] == 3) {
      $acaoant = "EXC";
    } 
    /* ANTERIOR */
  $pdf->Cell(16, 6, db_formatar($arr_ant[0], 'd'), 1, 0, "C", 0);
  $pdf->Cell(10, 6, $statusant                   , 1, 0, "L", 0);
  $pdf->Cell(10, 6, $acaoant                     , 1, 0, "L", 0);
  $pdf->Cell(12, 6, $arr_ant[3]                  , 1, 0, "R", 0);
  $pdf->Cell(14, 6, $arr_ant[4]                  , 1, 0, "R", 0);
  $pdf->Cell(16, 6, $arr_ant[5]                  , 1, 0, "R", 0);
  $pdf->Cell(13, 6, $arr_ant[6]                  , 1, 0, "R", 0);
  $pdf->Cell(103,6, $arr_ant[7]                  , 1, 1, "L", 0);

}

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(279, 6, "TOTAL DE ATIVOS (QTD. INCLUSÃO: ".$qtdinc." + QTD. ALTERAÇÃO: ".$qtdalt."): ".($qtdinc+$qtdalt), 1, 1, "L", 0);
$pdf->Cell(279, 6, "TOTAL DE INATIVOS (QTD. EXCLUSÃO): ".($qtdexc), 1, 1, "L", 0);
$pdf->Cell(279, 6, "TOTAL DE REGISTROS: ".$xxnum, 1, 1, "L", 0);

//Pagamentos do Arquivo
$vlrtotal = 0;
for ($iPag = 0; $iPag < $xxnumPag; $iPag ++) {
  db_fieldsmemory($resultPag, $iPag);
  $vlrtotal += $vlrtot;
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(279, 6, "", 0, 1, "C", 0);
$pdf->Cell(279, 6, "PAGAMENTO", 1, 1, "C", 0);
$pdf->Cell(279, 6, "VALOR TOTAL PAGO(R$):".db_formatar($vlrtotal,"f"), 1, 1, "L", 0);
$pdf->Cell(279, 6, "TOTAL DE LANÇAMENTOS: ".$xxnumPag, 1, 1, "L", 0);

$pdf->Output();