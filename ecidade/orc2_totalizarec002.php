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

require_once(modification("libs/db_liborcamento.php"));


// pesquisa a conta mae da receita

$tipo_mesini = 1;
$tipo_mesfim = 1;

require_once(modification("fpdf151/pdf.php"));
require_once(modification("libs/db_sql.php"));

//parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($_POST);

$xinstit =explode("-", $db_selinstit);
$resultinst = db_query("select codigo,nomeinstabrev from db_config where codigo in (" . str_replace('-', ', ', $db_selinstit) . ") ");
$descr_inst = '';
$xvirg = '';
for ($xins = 0; $xins < pg_num_rows($resultinst); $xins++) {
    db_fieldsmemory($resultinst, $xins);
    $descr_inst .= $xvirg . $nomeinstabrev;
    $xvirg = ', ';
}
$head2 = "TOTAL DO ORCAMENTO - RECEITA";
$head3 = "POR RECURSO";
$head4 = "EXERCICIO: " . db_getsession("DB_anousu");
$head5 = "INSTITUIÇÕES : " . $descr_inst;

$sele_work = ' o70_instit in (' . str_replace('-', ', ', $db_selinstit) . ') ';

$sql = "
select x.o15_recurso,
       aa.o15_descr,
       sum(saldo_inicial) as saldo_inicial,
       sum(saldo_prevadic_acum) as saldo_prevadic_acum
  from (" . db_receitasaldo(11, 1, 1, true, $sele_work, null, null, null, true) . ") as aa
  join orctiporec x on x.o15_codigo = o70_codigo
 group by x.o15_recurso, aa.o15_descr
 order by x.o15_recurso
";

$result = db_query($sql);

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$troca = 1;
$alt = 4;
$total1 = 0;
$total2 = 0;

$pagina = 1;
for ($i = 0; $i < pg_num_rows($result); $i++) {
    db_fieldsmemory($result, $i);

    if ($pdf->gety() > $pdf->h - 30 || $pagina == 1) {
        $pagina = 0;
        $pdf->addpage();
        $pdf->setfont('arial', 'b', 7);

        $pdf->cell(20, $alt, "Recurso", 0, 0, "L", 0);
        $pdf->cell(80, $alt, "Descrição", 0, 0, "L", 0);
        if (isParaiba()) {
            $pdf->setx(175);
        }
        $pdf->cell(25, $alt, "PREVISÃO", 0, 0, "R", 0);
        if (!isParaiba()) {
            $pdf->cell(25, $alt, "PREV.ADICIONAL", 0, 0, "R", 0);
            $pdf->cell(25, $alt, "TOTAL", 0, 1, "R", 0);
        }
        $pdf->cell(0, $alt, '', "T", 1, "C", 0);
        $pdf->setfont('arial', '', 7);
    }

    if ($o15_recurso != 0) {
        $pdf->cell(20, $alt, $o15_recurso, 0, 0, "L", 0);
        if (strlen($o15_descr) >= 90) {
            $descricaowrap = explode("<br>", wordwrap($o15_descr, 90, "<br>", false));
            $pdf->cell(80, $alt, $descricaowrap[0], 0, 0, "L", 0);
            $pdf->ln();
            $pdf->setx(30);
            $pdf->cell(80, $alt, $descricaowrap[1], 0, 0, "L", 0);
        } else {
            $pdf->cell(80, $alt, $o15_descr, 0, 0, "L", 0);
        }
        if (!isParaiba()) {
            $pdf->cell(25, $alt, db_formatar($saldo_inicial, 'f'), 0, 0, "R", 0);
            $pdf->cell(25, $alt, db_formatar($saldo_prevadic_acum, 'f'), 0, 0, "R", 0);
        } else {
            $pdf->setx(175);
        }
        $pdf->cell(25, $alt, db_formatar($saldo_inicial + $saldo_prevadic_acum, 'f'), 0, 1, "R", 0);
        $total1 += $saldo_inicial;
        $total2 += $saldo_prevadic_acum;
    }
}
$pdf->setfont('arial', 'b', 7);
$pdf->ln(3);
$pdf->cell(100, $alt, 'T O T A L', 0, 0, "L", 0);
if (isParaiba()) {
    $pdf->setx(175);
}
$pdf->cell(25, $alt, db_formatar($total1, 'f'), 0, 0, "R", 0);
if (!isParaiba()) {
    $pdf->cell(25, $alt, db_formatar($total2, 'f'), 0, 0, "R", 0);
    $pdf->cell(25, $alt, db_formatar($total1 + $total2, 'f'), 0, 1, "R", 0);
}

$pdf->Output();

db_query("commit");
