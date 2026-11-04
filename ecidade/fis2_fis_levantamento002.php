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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('classes/db_fis_levanta_estendida_classe.php'));

$cllevanta      = new cl_fis_levanta_estendida;
$clparfiscal    = new cl_fis_parfiscal;
$cllevvalor     = new cl_fis_levvalor;
$cllevantanotas = new cl_fis_levantanotas;
$cllevinscr     = new cl_fis_levinscr;
$clativprinc    = new cl_ativprinc;
$cllevusu       = new cl_fis_levusu;
$cllevcgm       = new cl_fis_levcgm;
$clissbase      = new cl_issbase;
$clrotulo       = new rotulocampo;

$clrotulo->label("q02_inscr");
$clrotulo->label("y63_aliquota");
$cllevantanotas->rotulo->label();

db_postmemory($_POST);

class NPDF extends PDF
{
    function Header()
    {

        global $conn;
        global $result;
        global $url;
        global $db21_compl;

        //Dados da instituição
        $dados = db_query($conn, "select nomeinst,
                                   db21_compl,
                                   trim(ender)||', '||trim(cast(numero as text)) as ender,
                                   trim(ender) as rua,
                                   munic,
                                   numero,
                                   uf,
                                   cgc,
                                   telef,
                                   email,
                                   url,
                                   cep,
                                   logo
                            from db_config where codigo = " . db_getsession("DB_instit"));
        $url = @pg_result($dados, 0, "url");
        $this->SetXY(1, 1);
        $this->Image('imagens/files/' . pg_result($dados, 0, "logo"), 7, 3, 20);

        $nome = pg_result($dados, 0, "nomeinst");
        global $nomeinst;
        $nomeinst = pg_result($dados, 0, "nomeinst");

        if (strlen($nome) > 42) {
            $TamFonteNome = 8;
        } else {
            $TamFonteNome = 9;
        }

        $this->SetFont('Arial', 'BI', $TamFonteNome);
        $this->Text(33, 9, pg_result($dados, 0, 'nomeinst'));
        $this->SetFont('Arial', 'I', 8);
        $this->Text(33, 14, 'RUA ' . trim(pg_result($dados, 0, "rua")) . ", Nº " . trim(pg_result($dados, 0, "numero") . ', ' . trim(pg_result($dados, 0, "bairro"))));
        $this->Text(33, 18, trim(pg_result($dados, 0, "munic")) . " - " . pg_result($dados, 0, "uf") . ' - CEP: ' . pg_result($dados, 0, "cep"));
        $this->Text(33, 22, trim(pg_result($dados, 0, "telef")));

        $comprim = ($this->w - $this->rMargin - $this->lMargin);
        $Espaco = $this->w - 80;
        $this->SetFont('Arial', '', 7);
        $margemesquerda = $this->lMargin;
        $this->setleftmargin($Espaco);
        $this->sety(6);
        $this->setfillcolor(235);
        $this->roundedrect($Espaco - 3, 5, 75, 28, 2, 'DF', '123');
        $this->line(10, 33, $comprim, 33);
        $this->setfillcolor(255);
        $this->multicell(0, 3, @$GLOBALS["head1"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head2"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head3"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head5"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head6"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head7"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head8"], 0, "J", 0);
        $this->multicell(0, 3, @$GLOBALS["head9"], 0, "J", 0);
        $this->setleftmargin($margemesquerda);
        $this->SetY(35);
    }
}

$pdf = new NPDF();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$total = 0;
$alt = 6;

$vtot_bruto = 0;
$vtot_imposto = 0;
$vtot_pago = 0;
$vtot_saldo = 0;
$total_consi = 0;

//valor a pagar
$vtot_correcao = 0;
$vtot_multa = 0;
$vtot_juro = 0;
$vtot_total = 0;

$result_nomeusu = db_query($conn, "select nome as nomeusu from db_usuarios where id_usuario =" . db_getsession("DB_id_usuario"));
if (pg_numrows($result_nomeusu) > 0) {
    $nomeusu = pg_result($result_nomeusu, 0, 0);
}

if (isset($nomeusu) && $nomeusu != "") {
    $emissor = $nomeusu;
} else {
    $emissor = $GLOBALS["DB_login"];
}

$joins = '';
$where = '';

if ((isset($codlev) and $codlev != '') and (isset($codprocfiscal) and $codprocfiscal != '')) {
    $joins .= " INNER JOIN fiscalizacao.fis_procfiscallevanta ON y112_levanta = y60_codlev INNER JOIN fiscalizacao.fis_procfiscal ON y112_procfiscal = y100_sequencial ";
    $where .= " fis_levanta.y60_codlev = $codlev and fis_procfiscal.y100_sequencial $codprocfiscal ";
} else if (isset($codlev) and $codlev != '') {
    $where .= " fis_levanta.y60_codlev = $codlev ";
} else if (isset($codprocfiscal) and $codprocfiscal != '') {
    $joins .= " INNER JOIN fiscalizacao.fis_procfiscallevanta ON y112_levanta = y60_codlev INNER JOIN fiscalizacao.fis_procfiscal ON y112_procfiscal = y100_sequencial ";
    $where .= " fis_procfiscal.y100_sequencial = $codprocfiscal ";
}

$result = $cllevanta->sql_record($cllevanta->getLevantamentos('*', $joins, $where));
$numrows = $cllevanta->numrows;

if ($numrows > 0) {
    db_fieldsmemory($result, 0, true);
}

if ($numrows == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.");
}

$sql     = $cllevinscr->sql_query($codlev, "q02_inscr,z01_nome,z01_ender,z01_numero,z01_compl,z01_bairro");
$result  = $cllevinscr->sql_record($sql);
$numrows = $cllevinscr->numrows;

if ($numrows > 0) {

    db_fieldsmemory($result, 0);

    $result = $clativprinc->sql_record($clativprinc->sql_query_compl($q02_inscr, "q03_descr"));
    $numrows = $clativprinc->numrows;

    if ($numrows > 0) {
        db_fieldsmemory($result, 0);
    }
    $sQueryEndereco  = $clissbase->empresa_query($q02_inscr,"z01_nome,z01_ender,z01_numero,z01_compl,z01_bairro");
    $rsQueryEndereco = $clissbase->sql_record($sQueryEndereco);
    if( $clissbase->numrows > 0) {
        db_fieldsmemory( $rsQueryEndereco ,0 );
    }
    $head1 = "Inscrição: $q02_inscr";
    $head2 = "Nome: $z01_nome";
    $head3 = "Endereço: $z01_ender" . "," . $z01_numero .",". $z01_compl . " -" . $z01_bairro;
} else {

    $result = $cllevcgm->sql_record($cllevcgm->sql_query($codlev, "z01_nome,z01_ender"));
    db_fieldsmemory($result, 0);

    $head2 = "Nome: $z01_nome";
    $head3 = "Endereço: $z01_ender";
}

$head5 = "Levantamento fiscal: $codlev Data:$y60_data";
$head6 = "Período: $y60_dtini a $y60_dtfim";

$result_auto = db_query($conn, "select nl15_lancamento from fiscalizacao.fis_lanclevanta where nl15_levanta = $codlev");

$dtCiencia = null;
$sqlAndamento = "select data_ciencia from fiscalizacao.fis_fandam inner join fiscalizacao.fis_datacienciaandamento on fis_datacienciaandamento.fandam = y39_codandam";

if (pg_numrows($result_auto) > 0) {
    $auto = pg_result($result_auto, 0, 0);
    $head7 = "Notificação: $auto";
    $sqlAndamento .= " inner join fiscalizacao.fis_lancandam on nl19_codandam  =  y39_codandam where  nl19_codlanc = {$auto}";
    $rsAndamento = db_query($sqlAndamento);
    if( pg_num_rows($rsAndamento) > 0 ) {
        db_fieldsmemory($rsAndamento , 0);
        $dtCiencia = $data_ciencia;
    }
}

$result_auto = db_query($conn, "select y117_auto from fiscalizacao.fis_autolevanta where y117_levanta = $codlev");
if (pg_numrows($result_auto) > 0) {
    $auto = pg_result($result_auto, 0, 0);
    $head7 = "Auto: $auto";
    $sqlAndamento .= " inner join fiscalizacao.fis_autoandam on y58_codandam  =  y39_codandam where   y58_codauto = {$auto}";
    $rsAndamento = db_query($sqlAndamento);
    if( pg_num_rows($rsAndamento) > 0 ) {
        db_fieldsmemory($rsAndamento , 0);
        $dtCiencia = $data_ciencia;
    }
}

$pdf->addpage("L");

$sqlLevValor = $cllevvalor->sql_query_file(null, "distinct y63_sequencia,y63_mes,y63_ano","y63_ano asc, y63_mes asc", "y63_codlev=$codlev");
$result01 = $cllevvalor->sql_record($sqlLevValor);
$numrows01 = $cllevvalor->numrows;

$sPrenche1 = "B";
$sPrenche2 = "BR";
$sPrenche3 = "RB";
$sPrenche = "TRLB";

for ($x = 0; $x < $numrows01; $x++) {

    db_fieldsmemory($result01, $x);
    if ($pdf->gety() < $pdf->h - 50) {
        $competencia = $y63_mes . "/" . $y63_ano;
    }

    $campos  = "y63_sequencia, y63_mes, y63_ano, y63_codlev, y63_sequencia, y63_bruto, y63_aliquota";
    $campos .= ", (select sum(yl63_pagoriginal) ";
    $campos .= "     from fiscalizacao.fis_valordefla ";
    $campos .= "    where yl63_sequencia = y63_sequencia ) as y63_pago, y63_pago as y68_valor, y63_saldo, y63_dtvenc";
    $campos .= ", y63_histor, (y63_pago + y63_saldo) as y63_apagar";
    $campos .= ", (select case when count(y68_pgto)> 1";
    $campos .= "          then null ";
    $campos .= "          else (select y68_pgto";
    $campos .= "                  from fiscalizacao.fis_levvalorpgtos";
    $campos .= "                 where y68_sequencia = y63_sequencia limit 1) end as teste";
    $campos .= "     from fiscalizacao.fis_levvalorpgtos";
    $campos .= "    where y68_sequencia = y63_sequencia) as y68_pgto";

    $where = "y63_sequencia = {$y63_sequencia}  and y63_codlev = {$codlev}";
    $sql = $cllevvalor->sql_query_file("", $campos, " y63_ano,y63_mes", $where);
    $result = $cllevvalor->sql_record($sql);
    $numrows = $cllevvalor->numrows;

    if ($numrows < 1) {
        continue;
    }

    $tot_valor = 0;
    $tot_imposto = 0;

    for ($i = 0; $i < $numrows; $i++) {

        db_fieldsmemory($result, $i);

        if (empty($y79_codigo)) {
            $y79_valor = $y63_bruto;
        }

        $imposto = ($y63_aliquota * $y79_valor) / 100;
        $pdf->setfont('arial', 'b', 8);

        if ($pdf->gety() > $pdf->h - 50 || $i == 0) {
            if ($pdf->gety() > $pdf->h - 50) {
                $pdf->addpage("L");
                $pdf->setfont('arial', '', 7);
                $competencia = $y63_mes . "/" . $y63_ano;
                $pdf->setfont('arial', 'b', 8);

                $pdf->cell(25, $alt, "Competência", "BTL", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor bruto", "BT", 0, "C", 1);
                $pdf->cell(5,  $alt, "Alí", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Imposto", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Vencimento", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Valor pago", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Dt. do Pagamento", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Vlr. Pgt Desc.", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor a pagar", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor corrigido", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Multa", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Juros", "BT", 0, "C", 1);
                $pdf->cell(26, $alt, "Valor total", "BTR", 1, "C", 1);
            }

            // Imprimir cabeçalho na primeira folha
            if ($x == 0) {
                $pdf->cell(25, $alt, "Competência", "BTL", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor bruto", "BT", 0, "C", 1);
                $pdf->cell(5,  $alt, "Alí", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Imposto", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Vencimento", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Valor pago", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Dt. do Pagamento", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Vlr. Pgt Desc.", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor a pagar", "BT", 0, "C", 1);
                $pdf->cell(25, $alt, "Valor corrigido", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Multa", "BT", 0, "C", 1);
                $pdf->cell(20, $alt, "Juros", "BT", 0, "C", 1);
                $pdf->cell(26, $alt, "Valor total", "BTR", 1, "C", 1);
            }
        }

        $tot_imposto += $imposto;
        $tot_valor += $y79_valor;
    }

    //correções
    if ($numrows > 0) {
        $result66 = $clparfiscal->sql_record($clparfiscal->sql_query_file(db_getsession('DB_instit'), "*"));
        db_fieldsmemory($result66, 0);

        $dtoper = date("Y-m-d", db_getsession("DB_datausu"));
        if (!empty($dtCiencia)) {
            $dtoper = $dtCiencia;
        }
        $aData = explode("-", $y63_dtvenc);
        $dDtOperData = $aData[0] . "-" . $aData[1] . "-01";

        $result = db_query("select round(fc_corre(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "'," . $y63_saldo . ",'" . $dtoper . "'," . db_getsession("DB_anousu") . ",'$y63_dtvenc'),2) as correcao");
        db_fieldsmemory($result, 0);


        $result = db_query("select round(fc_juros(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "','" . $dtoper . "','" . $dDtOperData . "','f'," . db_getsession("DB_anousu") . " , 0),2) as juro");
        db_fieldsmemory($result, 0);
        $juro = round($correcao * $juro, 4);

        $result = db_query("select round(fc_multa(" . ($y60_espontaneo == 't' ? $y32_receitexp : $y32_receit) . ",'" . $y63_dtvenc . "','" . $dtoper . "','" . $dDtOperData . "'," . db_getsession("DB_anousu") . "),2) as multa");
        db_fieldsmemory($result, 0);

        $multa = round($correcao * $multa, 4);

        $total = round($correcao + $juro + $multa, 4);
    } else {
        $multa = '0.00';
        $correcao = '0.00';
        $juro = '0.00';
        $total = '0.00';
        $y63_saldo = '0.00';
    }

    $pdf->setfont('arial', '', 7);
    $pdf->cell(25, $alt, $competencia, "$sPrenche", 0, "C", 1);
    $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($y79_valor, "f")), "$sPrenche1", 0, "C", 0);
    $pdf->cell(5,  $alt, "$y63_aliquota", "$sPrenche1", 0, "R", 0);
    $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($imposto, "f")), "$sPrenche1", 0, "C", 0);
    $pdf->cell(20, $alt, trim(db_formatar($y63_dtvenc, "d")), "$sPrenche1", 0, "C", 0);
    $pdf->cell(20, $alt, 'R$ ' . trim(db_formatar($y63_pago, "f")), "$sPrenche1", 0, "C", 0);
    $pdf->cell(22, $alt, trim(db_formatar($y68_pgto, "d")), "$sPrenche1", 0, "R", 0);
    $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($y68_valor, "f")), "$sPrenche1", 0, "C", 0);
    $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($y63_saldo, "f")), "$sPrenche1", 0, "R", 0);
    $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($correcao, "f")), "$sPrenche1", 0, "R", 0);
    $pdf->cell(23, $alt, 'R$ ' . trim(db_formatar($multa, "f")), "$sPrenche1", 0, "R", 0);
    $pdf->cell(18, $alt, 'R$ ' . trim(db_formatar($juro, "f")), "$sPrenche1", 0, "R", 0);
    $pdf->cell(28, $alt, 'R$ ' . trim(db_formatar($total, "f")), "$sPrenche3", 1, "R", 0);

    if (!empty($troca) && $troca == 1) {
        $troca = 2;
        $sPrenche1 = "B";
        $sPrenche2 = "BR";
        $sPrenche3 = "RB";
        $sPrenche = "TRLB";
    }

    $sql  = "select y68_seq as seq ,y68_valor as vlrpag, yl63_pagoriginal as vlrpagor, y68_pgto as dtpaga";
    $sql .= "  from fiscalizacao.fis_levvalor";
    $sql .= "       inner join fiscalizacao.fis_levvalorpgtos       on y63_sequencia = y68_sequencia";
    $sql .= "       inner join fiscalizacao.fis_valordefla  on yl63_sequencia = y68_sequencia";
    $sql .= "                                     and y68_seq = yl63_seq";
    $sql .= " where y63_sequencia = {$y63_sequencia}";
    $sql .= "   and y63_codlev    = {$codlev}";

    $result2 = db_query($sql);
    $numrows2 = pg_num_rows($result2);

    if ($numrows2 > 1) {
        $pdf->setfont('arial', '', 7);

        for ($y = 0; $y < $numrows2; $y++) {
            $pdf->SetX(90);

            db_fieldsmemory($result2, $y);

            $pdf->cell(20, $alt, "Pagamentos", "TRLB", 0, "C", 1);
            $pdf->cell(20, $alt, 'R$ ' . trim(db_formatar($vlrpagor, "f")), "B", 0, "R", 0);
            $pdf->cell(22, $alt, trim(db_formatar($dtpaga, "d")), "B", 0, "R", 0);
            $pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($vlrpag, "f")), "BR", 1, "R", 0);
        }

        $sPrenche1 = "BT";
        $sPrenche2 = "RLB";
        $sPrenche3 = "BRT";
        $troca = 1;
    }

    if ($y63_histor != "") {
        $pdf->setfont('arial', 'b', 7);
        $pdf->SetX(10);
        $pdf->cell(25, $alt-1, "Descrição: ", "TRLB", 0, "C", 1);
        $pdf->setfont('arial', '', 7);
        $pdf->MultiCell(260, $alt-1, $y63_histor, "TRLB", "L");

        $sPrenche1 = "BT";
        $sPrenche2 = "RLB";
        $sPrenche3 = "BRT";
        $troca = 1;
    }

    $pdf->setfont('arial', '', 7);

    $vtot_bruto += $tot_valor;
    $vtot_imposto += $tot_imposto;
    $vtot_pago += $y63_pago;
    $vtot_saldo += $y63_saldo;
    $total_consi += $y68_valor;

    //valor a pagar
    $vtot_correcao += $correcao;
    $vtot_multa += $multa;
    $vtot_juro += $juro;
    $vtot_total += $total;
}

// imprime o total geral
$pdf->setfont('arial', 'b', 7);
$pdf->cell(24, $alt, "TOTAL GERAL", "T", 0, "C", 0);
$pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($vtot_bruto, "f")), "T", 0, "R", 0);
$pdf->cell(5,  $alt, "", "T", 0, "C", 0);
$pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($vtot_imposto, "f")), "T", 0, "R", 0);
$pdf->cell(19, $alt, "", "T", 0, "C", 0);
$pdf->cell(22, $alt, 'R$ ' . trim(db_formatar($vtot_pago, "f")), "T", 0, "C", 0);
$pdf->cell(22, $alt, "", "T", 0, "C", 0);
$pdf->cell(25, $alt, 'R$ ' . trim(db_formatar($total_consi, "f")), "T", 0, "C", 0);
$pdf->cell(28, $alt, 'R$ ' . trim(db_formatar($vtot_saldo, "f")), "T", 0, "C", 0);
$pdf->cell(23, $alt, 'R$ ' . trim(db_formatar($vtot_correcao, "f")), "T", 0, "C", 0);
$pdf->cell(23, $alt, 'R$ ' . trim(db_formatar($vtot_multa, "f")), "T", 0, "R", 0);
$pdf->cell(23, $alt, 'R$ ' . trim(db_formatar($vtot_juro, "f")), "T", 0, "C", 0);
$pdf->cell(28, $alt, 'R$ ' . trim(db_formatar($vtot_total, "f")), "T", 1, "L", 0);

$pdf->ln(7);

$pdf->cell(139, 5, "Recebido em: ____/____/_______", 0, 0, "C", 0);
$pdf->cell(139, 5, "", 0, 1, "C", 0);
$pdf->ln(2);
$pdf->cell(139, 4, "______________________________________________________________", 0, 0, "C", 0);
$pdf->cell(139, 4, "______________________________________________________________", 0, 1, "C", 0);
$pdf->cell(139, 2, "Assinatura", 0, 0, "C", 0);
$pdf->cell(139, 2, $emissor, 0, 1, "C", 0);

$pdf->Output();
