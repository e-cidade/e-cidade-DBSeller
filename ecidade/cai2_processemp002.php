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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$clconlancamdoc = new cl_conlancamdoc;

$clrotulo = new rotulocampo;
$clrotulo->label("e60_codemp");
$clrotulo->label("c80_codord");
$clrotulo->label("c71_data");
$clrotulo->label("c71_coddoc");
$clrotulo->label("c53_descr");
$clrotulo->label("z01_nome");
$clrotulo->label("c70_valor");

db_postmemory($_GET);

$where = "";
$and   = "";
$msg_1 = "";
$msg_2 = "TODOS PROCESSOS SELECIONADOS";

if ($db_processop == '10,11') {
    $xdata = 'e60_emiss';
} else {
    $xdata = 'c70_data';
}

$where = 'where ';

if ($orgao != 0) {
    $xorgao = ' and o58_orgao = '.$orgao.' and o58_unidade = '.$unidade;
} else {
    $xorgao = '';
}

if ((isset($datai) && trim($datai)!="") && (isset($dataf) && trim($dataf)!="")) {
    $where.= $and." $xdata between '".$datai."' and '".$dataf."'";
    $and   = " and ";
    $msg_1 = "Período entre ".db_formatar($datai, "d")." e ".db_formatar($dataf, "d");
} elseif (isset($datai) && trim($datai)!="") {
    $where.= $and." $xdata >= '".$datai."'";
    $and   = " and ";
    $msg_1 = "Período posterior a ".db_formatar($datai, "d");
} elseif (isset($dataf) && trim($dataf)!="") {
    $where.= $and." $xdata <= '".$dataf."'";
    $and   = " and ";
    $msg_1 = "Período anterior a ".db_formatar($dataf, "d");
}

if ($db_processop != 0) {
    $where.= $and." c53_tipo in (".$db_processop.")";
    $msg_2 = "Processos selecionados: ".$db_processdescr;
    $msg_3 = "Processos selecionados: ".$db_processdescr;
}

if ($tipo == 'e') {
    $xtipo = ' = ';
} else {
    $xtipo = ' < ';
}

 $sql = "
 select conlancam.*,
        c75_numemp,
        c80_codord,
        e60_codemp,
        e60_emiss,
        c53_tipo,
        c53_descr,
        e60_numcgm,
        z01_nome,
        o58_orgao,
        o58_unidade,
        o41_descr,
        o56_elemento,
        o56_descr,
        o58_projativ,
        o55_descr,
        o58_codigo,
        o15_recurso,
        o15_descr
   from conlancam 
        inner join conlancamemp on c70_codlan = c75_codlan 
        left  join conlancamord on c70_codlan = c80_codlan
        inner join conlancamdoc on c70_codlan = c71_codlan 
        inner join conhistdoc on c71_coddoc = c53_coddoc 
        inner join empempenho on e60_numemp = c75_numemp 
        inner join cgm on e60_numcgm = z01_numcgm
        inner join orcdotacao on o58_coddot = e60_coddot 
                             and o58_anousu = ".db_getsession("DB_anousu")."
                             and o58_instit = e60_instit
                             and o58_instit = ".db_getsession('DB_instit')."
        inner join orcorgao   on o58_orgao = o40_orgao 
                             and o40_anousu = o58_anousu
        inner join orcunidade on o58_unidade = o41_unidade
                             and o58_orgao = o41_orgao
                             and o41_anousu = o58_anousu
        inner join orcelemento on o56_codele = o58_codele
                              and o56_anousu = o58_anousu
        inner join orcprojativ on o55_projativ = o58_projativ
                              and o55_anousu = o58_anousu
                              and o55_instit = o58_instit
        inner join orctiporec  on o15_codigo = o58_codigo
        {$where} 
        and e60_anousu {$xtipo} ".db_getsession("DB_anousu")." {$xorgao} 
  order by o58_orgao,o58_unidade,o56_elemento,o58_projativ,o58_codigo,{$xdata} ";
    
$result = db_query($sql);
if (pg_num_rows($result) == 0) {
    $url  = "db_erros.php?fechar=true";
    $url .= "&db_erro=Nenhum processo encontrado com as seguintes informações passadas: <br> $msg_1 <br> $msg_3";
    db_redireciona($url);
}

$totalr1 = 0;
$totalv1 = 0;
$totalrg = 0;
$totalvg = 0;
$total  = 0;
$valor = 0;
$alt = 4;
$valor = 0;

$head3 = "PROCESSOS DO EMPENHO";
$head6 = "$msg_1";
$head7 = "$msg_2";

$pdf = new PDF();
$pdf->addTitulo($head3);
$pdf->addTitulo($head6);
$pdf->addTitulo($head7);
$pdf->AliasNbPages();
$pdf->setfillcolor(235);
$pdf->init(false);

$quebra = '';

for ($i=0; $i<pg_num_rows($result); $i++) {
    db_fieldsmemory($result, $i, true);
    if ($pdf->gety() > $pdf->geth() - 40 || $i==0) {
        $pdf->addpage();
        $pdf->setfont('arial', 'b', 8);
        $pdf->cell(18, $alt, "Empenho", 1, 0, "C", 1);
        $pdf->cell(13, $alt, $RLc80_codord, 1, 0, "C", 1);
        $pdf->cell(15, $alt, $RLc71_data, 1, 0, "C", 1);
        $pdf->cell(80, $alt, $RLz01_nome, 1, 0, "C", 1);
        $pdf->cell(45, $alt, $RLc53_descr, 1, 0, "C", 1);
        $pdf->cell(20, $alt, "Valor lanc", 1, 1, "C", 1);
    }
    if ($quebra != $o58_orgao.$o58_unidade.$o56_elemento.$o58_projativ.$o58_codigo) {
        $quebra = $o58_orgao.$o58_unidade.$o56_elemento.$o58_projativ.$o58_codigo;
        if ($i != 0) {
            $pdf->cell(121, $alt, 'TOTAL DE REGISTROS :  '.$totalr1, "LTB", 0, "L", 0);
            $pdf->cell(50, $alt, 'VALOR TOTAL  :  ', "TB", 0, "R", 0);
            $pdf->cell(20, $alt, db_formatar($totalv1, 'f'), "RTB", 1, "R", 0);
            $totalr1 = 0;
            $totalv1 = 0;
        }
        $pdf->setfont('arial', 'b', 8);
        $pdf->ln(3);
        $pdf->cell(
            0,
            $alt,
            db_formatar($o58_orgao, 'orgao').db_formatar($o58_unidade, 'orgao').' - '.$o41_descr,
            0,
            1,
            "L",
            0
        );
        $pdf->cell(0, $alt, db_formatar($o56_elemento, 'elemento').' - '.$o56_descr, 0, 1, "L", 0);
        $pdf->cell(0, $alt, db_formatar($o58_projativ, 'projativ').' - '.$o55_descr, 0, 1, "L", 0);
        $pdf->cell(0, $alt, db_formatar($o15_recurso, 'recurso').' - '.$o15_descr, 0, 1, "L", 0);
    }
    if (($c53_tipo % 2 ) != 0) {
        $valor = $c70_valor * (-1);
    } else {
        $valor = $c70_valor;
    }
    $pdf->setfont('arial', '', 6);
    $pdf->cell(18, $alt, $e60_codemp, 1, 0, "C", 0);
    $pdf->cell(13, $alt, $c80_codord, 1, 0, "C", 0);
    $pdf->cell(15, $alt, $$xdata, 1, 0, "C", 0);
    $pdf->cell(80, $alt, $z01_nome, 1, 0, "L", 0);
    $pdf->cell(45, $alt, $c53_descr, 1, 0, "L", 0);
    $pdf->cell(20, $alt, db_formatar($valor, 'f'), 1, 1, "R", 0);
  
    $totalv1 += $valor;
    $totalvg += $valor;
    $totalr1 ++;
    $totalrg ++;
}

$pdf->cell(121, $alt, 'TOTAL DE REGISTROS :  '.$totalr1, "LTB", 0, "L", 0);
$pdf->cell(50, $alt, 'VALOR TOTAL  :  ', "TB", 0, "R", 0);
$pdf->cell(20, $alt, db_formatar($totalv1, 'f'), "RTB", 1, "R", 0);
$pdf->ln(3);

$pdf->cell(121, $alt, 'TOTAL DE REGISTROS :  '.$totalrg, "LTB", 0, "L", 0);
$pdf->cell(50, $alt, 'VALOR TOTAL  :  ', "TB", 0, "R", 0);
$pdf->cell(20, $alt, db_formatar($totalvg, 'f'), "RTB", 1, "R", 0);

$pdf->Output();
