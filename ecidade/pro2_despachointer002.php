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

$clprocandamint = new cl_procandamint;
$clprotprocesso = new cl_protprocesso;
$clrotulo = new rotulocampo;

$oGet = db_utils::postMemory($_GET);

if (isset($oGet->codprocandamint) && !empty($oGet->codprocandamint)) {
    $codprocandamint = $oGet->codprocandamint;
}

if (isset($oGet->codproc) && !empty($oGet->codproc)) {
    $codproc = $oGet->codproc;
}

parse_str($_SERVER['QUERY_STRING']);

$sTipoDespacho = "Despacho";
$result_procandamint = $clprocandamint->sql_record($clprocandamint->sql_query_sim($codprocandamint));

if ($clprocandamint->numrows > 0) {
    $oProcAndamInt = db_utils::fieldsMemory($result_procandamint, 0);
    $despacho = html_entity_decode($oProcAndamInt->p78_despacho);
    $sTipoDespacho = $oProcAndamInt->p100_descricao;
}


$public = "Não";
if ($oProcAndamInt->p78_publico == 't') {
    $public = "Sim";
}

$sNumeroProcesso = $codproc;

/**
 * Busca numero e ano do processo pelo codigo processo
 */
$sSqlNumeroProcesso = $clprotprocesso->sql_query_file($codproc, 'p58_numero, p58_ano');
$rsNumeroProcesso = $clprotprocesso->sql_record($sSqlNumeroProcesso);

if ($clprotprocesso->numrows > 0) {
    $oNumeroProcesso = db_utils::fieldsMemory($rsNumeroProcesso, 0);
    $sNumeroProcesso = $oNumeroProcesso->p58_numero . '/' . $oNumeroProcesso->p58_ano;
}

$head2 = "PROCESSO N° $sNumeroProcesso";
$head3 = "IMPRESSÃO DE " . mb_strtoupper($sTipoDespacho);
$head4 = "Data: " . db_formatar($oProcAndamInt->p78_data, 'd');
$head5 = "Hora: " . $oProcAndamInt->p78_hora;
$head6 = "Usuário: " . $oProcAndamInt->nome;
$head7 = "Público: " . $public;

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->addpage();
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$alt = 4;

$result_protprocesso = $clprotprocesso->sql_record($clprotprocesso->sql_query($codproc));

if ($clprotprocesso->numrows != 0) {

    $dados = db_utils::fieldsMemory($result_protprocesso, 0);

    $pdf->cell(25, $alt, 'Processo :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, $sNumeroProcesso, 0, 0, "L", 0);
    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Titular do Processo :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, $dados->z01_nome, 0, 1, "L", 0);

    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Data :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, db_formatar($dados->p58_dtproc, 'd'), 0, 0, "L", 0);
    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Hora :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, $dados->p58_hora, 0, 1, "L", 0);

    $posicaoY = $pdf->GetY();
    $totalLinhasTipo = $pdf->NbLines(75, $dados->p51_descr);

    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Tipo :', 0, 0, "R", 0);

    $pdf->setfont('arial', '', 8);
    $pdf->MultiCell(75, $alt, $dados->p51_descr, 0, 'L', 0);
    $pdf->SetXY(110, $posicaoY);

    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Atendente :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, $dados->nome, 0, 1, "L", 0);

    $pdf->Ln($totalLinhasTipo + 2);
    $pdf->setfont('arial', 'b', 8);
    $pdf->cell(25, $alt, 'Requerente :', 0, 0, "R", 0);
    $pdf->setfont('arial', '', 8);
    $pdf->cell(75, $alt, $dados->p58_requer, 0, 1, "L", 0);

    // Alteracao Plugin TaxonomiaDeProcessosDoMinisterioPublico - pro2_despachointer002.php #1
}

$pdf->Ln();
$pdf->cell(190, $alt, '', 'T', 1, "R", 0);
$pdf->setfont('arial', 'b', 10);
$pdf->cell(25, $alt, "{$sTipoDespacho} :", 0, 0, "R", 0);
$pdf->setfont('arial', '', 10);
$pdf->multicell(160, $alt, strip_tags($despacho), 0, "L", 0);

/**
 * CAMPOS DINAMICOS
 */
$result = db_query("
                SELECT
                *
                FROM
                protocolo.camposandpadraoresposta
                INNER JOIN db_syscampo ON db_syscampo.codcam = camposandpadraoresposta.p111_codcam
                WHERE
                   p111_codandam =  {$dados->p58_codandam}
                ORDER BY p111_sequencial desc
           ");

if ($result) {

    $pdf->Ln();
    $pdf->cell(190, $alt, '', 'T', 1, "R", 0);
    $pdf->setfont('arial', 'b', 10);
    $pdf->cell(50, $alt, "CAMPOS ADICIONAIS", 0, 0, "R", 0);
    $pdf->Ln();
    $pdf->Ln();

    $camposDinamicos = pg_fetch_all($result);
    if (!empty($camposDinamicos)) {
        foreach ($camposDinamicos as $campo) {
            $pdf->setfont('arial', 'b', 10);
            $pdf->cell(25, $alt, "{$campo['rotulo']} :", 0, 0, "R", 0);
            $pdf->setfont('arial', '', 10);
            $pdf->multicell(160, $alt, $campo['p111_resposta'], 0, "L", 0);
        }
    }
}
$dataHora = Date('dmYHis');
$caminho = "tmp/despacho_processo_{$codproc}_{$dataHora}.pdf";

if (!isset($mostrarPDF)) {
    $mostrarPDF = false;
}

$pdf->Output($caminho, false, $mostrarPDF);
