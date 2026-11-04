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
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_libtxt.php"));
require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_con"."ecta.php"));
require_once(modification("con4_padbal_ver.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));

$oGet         = db_utils::postMemory($_GET);


$iBanco = $oGet->iBanco;
$iAnoUsu      = db_getsession("DB_anousu");
$iInstituicao = db_getsession("DB_instit");


$head2 = "Relatório Gerencial Conciliação";
$head4 = "EXERCÍCIO: {$iAnoUsu}";

$pdf = new pdf();
$pdf->SetFillColor( 235 );
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage("L");

$iAlturalinha = 4;
$iFonte       = 6;


$sWhereConta = "";
if ( $iBanco != "" ) {
    $sWhereConta = " and db90_codban = '{$iBanco}'";
}

if( $oGet->iData == '' ){
   $iData = date("d/m/Y");
}


$oDataFiltro = new DBDate($iData);
$iData    = $oDataFiltro->getDate();


$sSql = "


SELECT *,
       round(saldo - vlr_pendencias_caixa + vlr_pendencias_extrato - vlr_saldocontacaixa, 2) AS vlr_diferenca,
       CASE
           WHEN round(saldo - vlr_pendencias_caixa + vlr_pendencias_extrato - vlr_saldocontacaixa, 2) = 0 THEN 'NAO'
           ELSE 'SIM'
       END AS expr_diferenca
FROM
  (  SELECT db83_sequencial,
            db83_descricao,
            db90_codban,
            db90_descr AS nomebanco,
            db83_conta||'-'||db83_dvconta AS db83_conta,
            array_to_string(
               (SELECT array_accum(DISTINCT c61_instit)
                FROM contabilidade.conplanocontabancaria
                INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                      AND c61_codcon = c56_codcon
                                                      and c61_reduz = c56_reduz  
                                                      and c61_instit = {$iInstituicao}         
                INNER JOIN configuracoes.db_config ON c61_instit = codigo
                WHERE db83_sequencial = c56_contabancaria
                  AND c56_anousu = {$iAnoUsu} ),', ') AS instit,

            array_to_string(
                (SELECT array_accum(c61_reduz)
                 FROM contabilidade.conplanocontabancaria
                 INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                       AND c61_codcon = c56_codcon
                                                       and c61_reduz = c56_reduz  
                                                       and c61_instit = {$iInstituicao} 
                 INNER JOIN configuracoes.db_config ON c61_instit = codigo
                 WHERE db83_sequencial = c56_contabancaria
                   AND c56_anousu =  {$iAnoUsu} ),', ') AS reduzido_contabil,

            (SELECT max(k68_data)
             FROM caixa.concilia
             inner join conciliastatus on k95_sequencial = k68_conciliastatus
             WHERE k68_contabancaria = db83_sequencial
               and k95_fechada is true
               and k68_data <= '{$iData}') AS conciliada,

            (SELECT max(k86_data)
             FROM caixa.extratolinha
             WHERE k86_contabancaria = db83_sequencial
               and k86_data <= '{$iData}' ) AS movimento_extrato,

             (SELECT max(k68_data)
             FROM caixa.concilia
             WHERE k68_contabancaria = db83_sequencial
               and k68_data <= '{$iData}' ) AS movimento_n,


            (SELECT max(k12_data)
               FROM
               ( SELECT max(k12_data) AS k12_data
                FROM contabilidade.conplanocontabancaria
                INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                      AND c61_codcon = c56_codcon
                INNER JOIN caixa.corrente ON k12_conta = c61_reduz
                                         AND EXTRACT (YEAR FROM k12_data) = {$iAnoUsu}
                                         and k12_data <= '{$iData}'
                WHERE db83_sequencial = c56_contabancaria
                   AND c56_anousu =  {$iAnoUsu}
                   and k12_data <= '{$iData}'
                UNION
                 SELECT max(k12_data) AS k12_data
                FROM contabilidade.conplanocontabancaria
                INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                AND c61_codcon = c56_codcon
                INNER JOIN caixa.corlanc ON k12_conta = c61_reduz
                AND EXTRACT (YEAR FROM k12_data) =  {$iAnoUsu}
                and k12_data <= '{$iData}'
                WHERE db83_sequencial = c56_contabancaria
                  AND c56_anousu =  {$iAnoUsu} ) AS x) AS movimento_autenticacao,

            COALESCE (
                      (SELECT count(*)
                       FROM caixa.conciliapendextrato
                       INNER JOIN caixa.concilia ON k68_sequencial = k88_concilia
                       WHERE k68_contabancaria = db83_sequencial
                         AND k68_data =
                           (SELECT max(k68_data)
                            FROM caixa.concilia a
                            WHERE a.k68_contabancaria = db83_sequencial
                            and k68_data <= '{$iData}' ) ),0) AS pendencias_extrato,

            COALESCE (
                        (SELECT count(*)
                         FROM caixa.conciliapendcorrente
                         INNER JOIN caixa.concilia ON k68_sequencial = k89_concilia
                         WHERE k68_contabancaria = db83_sequencial
                           AND k68_data =
                             (SELECT max(k68_data)
                              FROM caixa.concilia a
                              WHERE a.k68_contabancaria = db83_sequencial
                               and k68_data <= '{$iData}' )
                           AND NOT EXISTS
                             (SELECT 1
                              FROM caixa.corgrupocorrente
                              WHERE k105_autent = k89_autent
                                AND k105_id = k89_id
                                AND k105_data = k89_data
                                AND k105_corgrupotipo in (2,
                                                          3,
                                                          5,
                                                          6)
                                AND extract(YEAR FROM k105_data) <= 2012 ) ),0) AS pendencias_autenticacao,


            COALESCE (
                        (SELECT k97_saldofinal
                         FROM extratosaldo a
                         WHERE a.k97_contabancaria = db83_sequencial
                           AND a.k97_dtsaldofinal <=
                             (SELECT max(k68_data)
                              FROM caixa.concilia
                              WHERE k68_contabancaria = db83_sequencial
                               and k68_data <= '{$iData}' )
                         ORDER BY a.k97_dtsaldofinal DESC, a.k97_extrato DESC
                         LIMIT 1),0) AS saldo,

            COALESCE (
                        (SELECT sum(CASE (CASE
                                              WHEN richeque IS NOT NULL
                                                   AND richeque <> 0
                                                   AND rivalorcredito <> 0 THEN 'cheque'
                                              WHEN rnvalordebito IS NOT NULL
                                                   AND rnvalordebito <> 0
                                                   OR richeque IS NOT NULL
                                                   AND richeque <> 0
                                                   AND rnvalordebito <> 0 THEN 'debito'
                                              WHEN rivalorcredito IS NOT NULL
                                                   AND rivalorcredito <> 0 THEN 'credito'
                                          END)
                                        WHEN 'debito' THEN (CASE
                                                                WHEN rnvalordebito IS NOT NULL
                                                                     AND rnvalordebito <> 0 THEN rnvalordebito
                                                                ELSE rivalorcredito
                                                            END) *-1
                                        ELSE (CASE
                                                  WHEN rnvalordebito IS NOT NULL
                                                       AND rnvalordebito <> 0 THEN rnvalordebito
                                                  ELSE rivalorcredito
                                              END)
                                    END) AS valor
                         FROM conciliapendcorrente
                         INNER JOIN fc_extratocaixa(
                                                      (SELECT DISTINCT c61_instit
                                                       FROM contabilidade.conplanocontabancaria
                                                       INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                       AND c61_codcon = c56_codcon
                                                       INNER JOIN configuracoes.db_config ON c61_instit = codigo
                                                       WHERE db83_sequencial = c56_contabancaria
                                                         and c61_instit = {$iInstituicao}
                                                         AND c56_anousu = {$iAnoUsu} ),db83_sequencial, NULL, NULL, FALSE) ON ricaixa = k89_id
                         AND riautent = k89_autent
                         AND ridata = k89_data
                         WHERE k89_concilia =
                             (SELECT k68_sequencial
                              FROM concilia
                              WHERE k68_contabancaria = db83_sequencial
                                AND k68_data =
                                  (SELECT max(k68_data)
                                   FROM caixa.concilia
                                   WHERE k68_contabancaria = db83_sequencial
                                     and k68_data <= '{$iData}' ) )
                           AND NOT EXISTS
                             (SELECT 1
                              FROM corgrupocorrente
                              WHERE k105_autent = k89_autent
                                AND k105_id = k89_id
                                AND k105_data = k89_data
                                AND k105_corgrupotipo in (2,
                                                          3,
                                                          5,
                                                          6)
                                and k89_data <= '{$iData}'
                                AND extract(YEAR
                                            FROM k105_data) <= 2012 ) ) ,0) AS vlr_pendencias_caixa,




            COALESCE (
                        (SELECT round(sum(CASE
                                              WHEN k86_tipo = 'C' THEN k86_valor * -1
                                              ELSE k86_valor
                                          END), 2)
                         FROM conciliapendextrato
                         INNER JOIN extratolinha ON k86_sequencial = k88_extratolinha
                         INNER JOIN extrato ON k85_sequencial = k86_extrato
                         WHERE k88_concilia =
                             (SELECT k68_sequencial
                              FROM concilia
                              WHERE k68_contabancaria = db83_sequencial
                                AND k68_data =
                                  (SELECT max(k68_data)
                                   FROM caixa.concilia
                                   WHERE k68_contabancaria = db83_sequencial
                                    and k68_data <= '{$iData}' ) ) ),0) AS vlr_pendencias_extrato,


            COALESCE (round (
                               (SELECT sum(substr(fc_saltessaldo(c61_reduz,
                                                                   (SELECT max(k68_data)
                                                                    FROM caixa.concilia
                                                                    WHERE k68_contabancaria = db83_sequencial
                                                                      and  k68_data <= '{$iData}'),

                                                                   (SELECT max(k68_data)
                                                                    FROM caixa.concilia
                                                                    WHERE k68_contabancaria = db83_sequencial
                                                                      and k68_data <= '{$iData}' ),NULL,
                                                                   (SELECT DISTINCT c61_instit
                                                                    FROM contabilidade.conplanocontabancaria
                                                                    INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                                                                          AND c61_codcon = c56_codcon
                                                                                                          and c61_instit = ({$iInstituicao})
                                                                    INNER JOIN configuracoes.db_config ON c61_instit = codigo
                                                                    WHERE db83_sequencial = c56_contabancaria
                                                                      AND c56_anousu = {$iAnoUsu} ) ), 41, 13)::float)
                                FROM
                                  (SELECT DISTINCT c61_reduz
                                     FROM contabancaria a
                                   INNER JOIN conplanocontabancaria ON conplanocontabancaria.c56_contabancaria = a.db83_sequencial
                                                                   AND conplanocontabancaria.c56_anousu = {$iAnoUsu}
                                   INNER JOIN conplanoreduz ON conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon
                                                           AND conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu
                                                           AND conplanoreduz.c61_anousu = {$iAnoUsu}
                                   WHERE a.db83_sequencial = contabancaria.db83_sequencial ) AS x),2),
                      0) AS vlr_saldocontacaixa


   FROM configuracoes.contabancaria
   INNER JOIN bancoagencia ON db83_bancoagencia = db89_sequencial
   INNER JOIN db_bancos ON db89_db_bancos = db90_codban
   WHERE
       (
           SELECT DISTINCT c61_instit
                     FROM contabilidade.conplanocontabancaria
               INNER JOIN contabilidade.conplanoreduz ON c61_anousu = c56_anousu
                                                     AND c61_codcon = c56_codcon
                                                     and c61_reduz = c56_reduz
                                                     and c61_instit = ({$iInstituicao})
               INNER JOIN configuracoes.db_config ON c61_instit = codigo
                    WHERE db83_sequencial = c56_contabancaria
                      AND c56_anousu =  {$iAnoUsu} ) in ({$iInstituicao})
        {$sWhereConta}
        ) AS x
   ORDER BY reduzido_contabil


";

//$sSql = "select * from ($sSql ) as dd where reduzido_contabil ilike '%25865%' ";
//echo $sSql; die();

$rs = db_query($sSql);

if ( pg_numrows($rs) <= 0 and !isParaiba()) {

  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum Registro Encontrado.");
  exit;
}

$aDados = array();

for (  $i = 0; $i <  pg_numrows($rs); $i++ ) {

  $oDados = db_utils::fieldsMemory($rs, $i);
  $oValores = new stdClass();

  $dtMovimento = $oDados->movimento_autenticacao;
  if ($dtMovimento == "") {
    $dtMovimento = $oDados->movimento_n;
  }


  $oValores->vlr_pendencias_extrato = $oDados->vlr_pendencias_extrato;
  $oValores->db83_sequencial = $oDados->db83_sequencial;
  $oValores->db83_descricao = $oDados->db83_descricao;
  $oValores->db83_conta = $oDados->db83_conta;
  $oValores->reduzido_contabil = $oDados->reduzido_contabil;
  $oValores->conciliada = $oDados->conciliada;
  $oValores->movimento_autenticacao = $dtMovimento;
  $oValores->saldo = $oDados->saldo;
  $oValores->vlr_diferenca = $oDados->vlr_diferenca;
  $aDados[$oDados->db90_codban  . " - " . $oDados->nomebanco][] = $oValores;
}


foreach ( $aDados as $iIndice => $aValores  ) {

    $pdf->setfont('arial','B', 8);
    $pdf->cell(25,  $iAlturalinha, "$iIndice",  "", 1, "L", 0);
    imprimirCabecalho($pdf, $iAlturalinha, true);

    foreach($aValores as $oDados){

        $saldo = db_formatar( $oDados->saldo, "f");
        $diferenca = db_formatar( $oDados->vlr_diferenca, "f");
        $conciliada = db_formatar( $oDados->conciliada, "d");
        $movimento = db_formatar( $oDados->movimento_autenticacao, "d");
        $vlr_pendencias_extrato = db_formatar( $oDados->vlr_pendencias_extrato, "f");

		$pdf->setfont('arial','',6);
		$pdf->cell(15,  $iAlturalinha, $oDados->db83_sequencial ,  "", 0, "R", 0);
		$pdf->cell(80,  $iAlturalinha, $oDados->db83_descricao ,  "", 0, "L", 0);
		$pdf->cell(25,  $iAlturalinha, $oDados->db83_conta ,  "", 0, "R", 0);
		$pdf->cell(25,  $iAlturalinha, $movimento ,  "", 0, "C", 0);
		$pdf->cell(25,  $iAlturalinha, $conciliada ,  "", 0, "C", 0);
        $pdf->cell(35,  $iAlturalinha, $saldo ,  "", 0, "R", 0);
        $pdf->cell(35,  $iAlturalinha, $vlr_pendencias_extrato  ,  "", 0, "R", 0);
		$pdf->cell(35,  $iAlturalinha, $diferenca ,  "", 1, "R", 0);

        $pdf->setfont('arial','B',6);
        $pdf->cell(28,  $iAlturalinha, "Reduzidos" ,  "", 0, "R", 0);
        $pdf->setfont('arial','',6);
        $pdf->MultiCell(150, $iAlturalinha, "$oDados->reduzido_contabil", "", "L", 0);

        $pdf->cell(275,  1, "",  "T", 1, "C", 0);
        imprimirCabecalho($pdf, $iAlturalinha, false);
    }

   $pdf->Ln();
  }

if(isParaiba()) {
    if(pg_numrows($rs) == 0)
    {
        $pdf->setfont('arial','',10);
        $pdf->cell($pdf->w-15,$pdf->h/2,"NADA A REGISTRAR",0,0,"C",0);
        $pdf->setfont('arial','',7);
        $pdf->sety($pdf->h-50);

    }
    $sec = "______________________________" . "\n" . "Secretaria da Fazenda";
    $cont = "______________________________" . "\n" . "Contador";
    $pref = "______________________________" . "\n" . "Prefeito";
    $classinatura = new cl_assinatura;
    $ass_pref = $classinatura->assinatura(1000, $pref);
    $ass_sec = $classinatura->assinatura(1002, $sec);
    $ass_cont = $classinatura->assinatura(1005, $cont);

    if ($pdf->gety() > ($pdf->h - 35)) {
        $pdf->addpage('L');
    }
    $largura = ($pdf->w) / 2;
    $pdf->ln(5);
    $pos = $pdf->gety();
    $pdf->multicell($largura, 4, $ass_pref, 0, "C", 0, 0);
    $pdf->setxy(($pdf->w) / 4, $pos);
    $pdf->multicell($largura, 4, $ass_sec, 0, "C", 0, 0);
    $pdf->setxy($largura, $pos);
    $pdf->multicell($largura, 4, $ass_cont, 0, "C", 0, 0);
}

$pdf->Output();

function imprimirCabecalho($oPdf, $iAlturalinha, $lImprime) {

	if ( $oPdf->GetY() > $oPdf->h - 30 || $lImprime ) {

		$oPdf->SetFont('arial', 'b', 8);

		if ( !$lImprime ) {

			$oPdf->AddPage("L");
		}
		$oPdf->setfont('arial','b',6);
		$oPdf->cell(15,  $iAlturalinha, "SEQ"       ,  "", 0, "C", 1);
		$oPdf->cell(80,  $iAlturalinha, "DESCRIÇÃO"     ,  "", 0, "C", 1);
		$oPdf->cell(25,  $iAlturalinha, "CONTA" ,  "", 0, "C", 1);
		$oPdf->cell(25,  $iAlturalinha, "MOVIMENTO"    ,  "", 0, "C", 1);
		$oPdf->cell(25,  $iAlturalinha, "CONCILIAÇÃO"   ,  "", 0, "C", 1);
        $oPdf->cell(35,  $iAlturalinha, "SALDO"   ,  "", 0, "C", 1);
        $oPdf->cell(35,  $iAlturalinha, "PENDENCIA"   ,  "", 0, "C", 1);
		$oPdf->cell(35,  $iAlturalinha, "DIFERENÇA"   ,  "", 1, "C", 1);

	}
}


