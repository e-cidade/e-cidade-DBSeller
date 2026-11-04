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

use ECidade\Pdf\Pdf;

include(modification("libs/db_stdlib.php"));
include(modification("libs/db_conecta.php"));

$dataini = $_GET['dataini'];
$datafim = $_GET['datafim'];
$dataini = inverteData(str_replace('/','-',$dataini));
$datafim = inverteData(str_replace('/','-',$datafim));

$anousu = db_getsession("DB_anousu");
$instit = db_getsession("DB_instit");

$resultinst = db_query("select munic from db_config where codigo = {$instit}");
$munic = db_utils::fieldsMemory($resultinst, 0)->munic;

$pdf = new Pdf();

$pdf->addTitulo("MUNICÍPIO DE ".strtoupper($munic));
$pdf->addTitulo('Relatório de Empenhos');
$pdf->addTitulo('');
$pdf->addTitulo('Todos os empenhos');
$pdf->addTitulo("De ".inverteData($dataini)." até ".inverteData($datafim).".");

$pdf->init(false);
$pdf->addPage('L');
$pdf->aliasNbPages();
$pdf->setfillcolor(223);
$pdf->setfont('arial', '', 11);


$sqlNaoLiquidados = "SELECT DISTINCT ON (c80_codord)
              c80_codord
        FROM   empenho.empempenho
               JOIN conlancamemp
                 ON c75_numemp = e60_numemp
               JOIN contabilidade.conlancam
                 ON c70_codlan = c75_codlan
               JOIN contabilidade.conlancamdoc
                 ON c71_codlan = c70_codlan
               JOIN contabilidade.conhistdoc
                 ON c53_coddoc = c71_coddoc
               JOIN conlancamord
                 ON c80_codlan = c70_codlan
               JOIN empenho.pagordem
                 ON e50_codord = c80_codord
               JOIN empord
                 ON e82_codord = e50_codord
               JOIN empagemov
                 ON e81_codmov = e82_codmov
                    AND e81_cancelado IS NULL
               JOIN contabilidade.conlancamnota
                 ON c66_codlan = c70_codlan
               JOIN empenho.empnota
                 ON c66_codnota = e69_codnota
               JOIN empenho.pagordemnota
                 ON e71_codnota = c66_codnota
               JOIN empenho.empnotaele
                 ON e69_codnota = e70_codnota
               JOIN orcamento.orcdotacao
                 ON orcdotacao.o58_anousu = empempenho.e60_anousu
                    AND orcdotacao.o58_coddot = empempenho.e60_coddot
                    AND orcdotacao.o58_instit = empempenho.e60_instit
               JOIN orcamento.orcelemento
                 ON orcdotacao.o58_anousu = orcelemento.o56_anousu
                    AND orcdotacao.o58_codele = orcelemento.o56_codele
               JOIN orctiporec
                 ON o58_codigo = o15_codigo
               LEFT JOIN empenho.pagordemoutrosdados
                      ON e172_pagordem = e50_codord
        WHERE  e60_instit = {$instit}
               AND e60_anousu = {$anousu}
               AND c70_data BETWEEN '{$dataini}' AND '{$datafim}'
               AND c53_tipo != 20
               AND Substr(orcelemento.o56_elemento, 3, 1) = '1'
               AND Substr(orcelemento.o56_elemento, 6, 2) IN (
                   '01', '03', '04', '05',
                   '11', '16', '34' )";


$sql = " SELECT * FROM (
            SELECT DISTINCT ON (c80_codord)
              CASE
                WHEN  e172_dados ->> 'codigo_agrupamento' = ''
                  THEN 'Sem Agrupamento'
                ELSE
                    e172_dados ->> 'codigo_agrupamento' END                        AS
              codigo_agrupamento
              ,
                                        c80_codord                 AS
              numliquidacao,
                                        e60_numemp                 AS
              seq_empenho,
                                        e60_codemp                 AS
              numero_empenho,
              c53_descr,
                                        e60_anousu                 AS exercicio,
                                        e60_coddot                 AS
              reduzido_dotacao,
                                        e70_valor                  AS
              valor_liquidado,
                                        Lpad(o58_orgao, 2, 0)
                                        || '.'
                                        || Lpad(o58_unidade, 2, 0)
                                        || '.'
                                        || Lpad(o58_funcao, 2, 0)
                                        || '.'
                                        || Lpad(o58_subfuncao, 3, 0)
                                        || '.'
                                        || Lpad(o58_programa, 4, 0)
                                        || '.'
                                        || Lpad(o58_projativ, 4, 0)
                                        || '.'
                                        || o56_elemento
                                        || '.'
                                        || Lpad(o15_recurso, 4, 0) AS dotacao,
                                        c70_data                   AS data
        FROM   empenho.empempenho
               JOIN conlancamemp
                 ON c75_numemp = e60_numemp
               JOIN contabilidade.conlancam
                 ON c70_codlan = c75_codlan
               JOIN contabilidade.conlancamdoc
                 ON c71_codlan = c70_codlan
               JOIN contabilidade.conhistdoc
                 ON c53_coddoc = c71_coddoc
               JOIN conlancamord
                 ON c80_codlan = c70_codlan
               JOIN empenho.pagordem
                 ON e50_codord = c80_codord
               JOIN empord
                 ON e82_codord = e50_codord
               JOIN empagemov
                 ON e81_codmov = e82_codmov
                    AND e81_cancelado IS NULL
               JOIN contabilidade.conlancamnota
                 ON c66_codlan = c70_codlan
               JOIN empenho.empnota
                 ON c66_codnota = e69_codnota
               JOIN empenho.pagordemnota
                 ON e71_codnota = c66_codnota
               JOIN empenho.empnotaele
                 ON e69_codnota = e70_codnota
               JOIN orcamento.orcdotacao
                 ON orcdotacao.o58_anousu = empempenho.e60_anousu
                    AND orcdotacao.o58_coddot = empempenho.e60_coddot
                    AND orcdotacao.o58_instit = empempenho.e60_instit
               JOIN orcamento.orcelemento
                 ON orcdotacao.o58_anousu = orcelemento.o56_anousu
                    AND orcdotacao.o58_codele = orcelemento.o56_codele
               JOIN orctiporec
                 ON o58_codigo = o15_codigo
               LEFT JOIN empenho.pagordemoutrosdados
                      ON e172_pagordem = e50_codord
        WHERE  e60_instit = {$instit}
               AND e60_anousu = {$anousu}
               AND c70_data BETWEEN '{$dataini}' AND '{$datafim}'
               AND c53_tipo = 20
               AND c80_codord not in ({$sqlNaoLiquidados})
               AND Substr(orcelemento.o56_elemento, 3, 1) = '1'
               AND Substr(orcelemento.o56_elemento, 6, 2) IN (
                   '01', '03', '04', '05',
                   '11', '16', '34' )
        ORDER  BY 2 ASC) AS x
ORDER  BY codigo_agrupamento;  ";

$result = db_query($sql);
$total = pg_numrows($result);

$pdf->Ln();
$pdf->SetFont('Arial', '', 8);
$pdf->setfillcolor(212);
$pdf->Cell(38, 6, "COD AGRUPAMENTO", 1, 0, '', 1);
$pdf->Cell(24, 6, "Nº LIQUIDACAO", 1, 0, '', 1);
$pdf->Cell(25, 6, "SEQ. EMPENHO", 1, 0, '', 1);
$pdf->Cell(20, 6, "Nº EMPENHO", 1, 0, '', 1);
$pdf->Cell(12, 6, "EXERC.", 1, 0, '', 1);
$pdf->Cell(20, 6, "REDUZIDO D.", 1, 0, '', 1);
$pdf->Cell(90, 6, "DOTACAO", 1, 0, '', 1);
$pdf->Cell(24, 6, "DATA", 1, 0, '', 1);
$pdf->Cell(32, 6, "VALOR", 1, 0, '', 1);

$totalvalor = 0;
$arrayAgrup = [];
$agrupamentoAtual = '';

for ($i = 0; $i < $total; $i++) {
    $oDados = db_utils::fieldsMemory($result, $i);

    if (!array_key_exists($oDados->codigo_agrupamento, $arrayAgrup)) {
        if ($i != 0) {
            $pdf->SetFont('Arial', '', 12);
            $pdf->ln();
            $pdf->ln();
            $pdf->setX(195);
            $pdf->cell(90, 4, "Total agrupamento (".$agrupamentoAtual."): R$" . number_format($arrayAgrup[$agrupamentoAtual], 2, ',', '.'), 0, 1, "R", 0);
        }
        $arrayAgrup[$oDados->codigo_agrupamento] += 0;
    }

    if ($pdf->getY() > $pdf->getH() - 50) {
        $pdf->addpage('L');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(38, 6, "COD AGRUPAMENTO", 1, 0, '', 1);
        $pdf->Cell(24, 6, "Nº LIQUIDACAO", 1, 0, '', 1);
        $pdf->Cell(25, 6, "SEQ. EMPENHO", 1, 0, '', 1);
        $pdf->Cell(20, 6, "Nº EMPENHO", 1, 0, '', 1);
        $pdf->Cell(12, 6, "EXERC.", 1, 0, '', 1);
        $pdf->Cell(20, 6, "REDUZIDO D.", 1, 0, '', 1);
        $pdf->Cell(90, 6, "DOTACAO", 1, 0, '', 1);
        $pdf->Cell(24, 6, "DATA", 1, 0, '', 1);
        $pdf->Cell(32, 6, "VALOR", 1, 0, '', 1);
    }

    if (array_key_exists($oDados->codigo_agrupamento, $arrayAgrup)) {
        $arrayAgrup[$oDados->codigo_agrupamento] += $oDados->valor_liquidado;
    }

    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln();
    $pdf->Cell(38, 6, $oDados->codigo_agrupamento, 1);
    $pdf->Cell(24, 6, $oDados->numliquidacao, 1);
    $pdf->Cell(25, 6, $oDados->seq_empenho, 1);
    $pdf->Cell(20, 6, $oDados->numero_empenho, 1);
    $pdf->Cell(12, 6, $oDados->exercicio, 1);
    $pdf->Cell(20, 6, $oDados->reduzido_dotacao, 1);
    $pdf->Cell(90, 6, $oDados->dotacao, 1);
    $pdf->Cell(24, 6, $oDados->data, 1);
    $pdf->Cell(32, 6, number_format($oDados->valor_liquidado, 2, ',', '.'), 1, 0, "R", 0);

    if ($i+1 == $total) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->ln();
        $pdf->ln();
        $pdf->setX(195);
        $pdf->cell(90, 4, "Total agrupamento (".$oDados->codigo_agrupamento."): R$" . number_format($arrayAgrup[$oDados->codigo_agrupamento], 2, ',', '.'), 0, 1, "R", 0);
    }
    $agrupamentoAtual = $oDados->codigo_agrupamento;
    $totalvalor += floatval($oDados->valor_liquidado);

}
$pdf->SetFont('Arial', 'B', 13);
$pdf->ln();
$pdf->ln();
$pdf->setX(190);
$pdf->cell(90, 4, "Total: R$" . number_format($totalvalor, 2, ',', '.'), 0, 1, "R", 0);
$pdf->output();

function inverteData($data)
{
    $datanova = '';
    $lista = explode('-', $data);
    $datanova.=$lista[2].'-'.$lista[1].'-'.$lista[0];
    return $datanova;
}
