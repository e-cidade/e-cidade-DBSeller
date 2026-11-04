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

include modification('fpdf151/pdf.php');
include modification('classes/db_orcsuplem_classe.php');
include modification('libs/db_liborcamento.php');

$auxiliar = new cl_orcsuplem();
$auxiliar2 = new cl_orcsuplem();
$anousu = db_getsession('DB_anousu');

$clrotulo = new rotulocampo();
$clrotulo->label('o45_descr');

parse_str($_SERVER['QUERY_STRING']);

$db_selinstit = str_replace('-', ', ', $db_selinstit);
$iAnoUsu = db_getsession('DB_anousu');
$dt_ini = $dt_ini;
$dt_fim = $dt_fim;
if ((strlen($dt_ini) < 7) || (strlen($dt_fim) < 7)) {
    $dt_ini = '';
    $dt_fim = '';
}
$txt_where = '';
$txt_where = "  o46_data between '$dt_ini' and '$dt_fim' ";

if ($processados == 1) {
    $head7 = 'SOMENTE PROCESSADOS';
    $txt_where .= '  and o49_codsup is not null ';
} elseif ($processados == 2) {
    $head7 = 'SOMENTE NÃO PROCESSADOS';
    $txt_where .= '  and o49_codsup is null ';
}
if ($tipoproj == 1) {
    $txt_where .= ' and o39_tipoproj=1 ';
} elseif ($tipoproj == 2) {
    $txt_where .= ' and o39_tipoproj=2 ';
}
$txt_where .= "and o39_anousu = {$iAnoUsu}";

$iTotalLinhas = 0;
for ($tiporel = 0; $tiporel <= 1; ++$tiporel) {
    $wheretipo = ($tiporel == 0 ? ' o46_tiposup <> 1014' : 'o46_tiposup = 1014').' and ';

    $sql = "
    select sum(o47_valor) as total,
           o15_recurso as recurso,
           o15_codigo as codigorecurso,
           o15_descr,
           anousu
    from (
        select  o47_valor,
                o15_codigo,
                o15_recurso,
                o15_descr,
                o49_codsup,
               o58_anousu as anousu
           from orcsuplem
                inner join orcprojeto    on o39_codproj = orcsuplem.o46_codlei
                left join orcsuplemretif on o48_retificado= orcprojeto.o39_codproj
                left join orcsuplemlan   on o49_codsup = o46_codsup
                inner join orcsuplemval  on o47_codsup = o46_codsup
                                        and orcsuplemval.o47_valor > 0
                inner join orcdotacao    on o58_coddot = orcsuplemval.o47_coddot
                                        and o58_anousu = orcsuplemval.o47_anousu
                                        and o58_instit in ({$db_selinstit})
                inner join orctiporec on o15_codigo = o58_codigo
                inner join fonterecurso on orctiporec_id  = o15_codigo and exercicio = o58_anousu
          where {$wheretipo} o48_retificado is null
            and {$txt_where}

          union all

        select  o136_valor,
                o15_codigo,
                o15_recurso,
                o15_descr,
                o49_codsup,
               o39_anousu as anousu
           from orcsuplem
                inner join orcprojeto           on o39_codproj = orcsuplem.o46_codlei
                left join orcsuplemretif        on o48_retificado= orcprojeto.o39_codproj
                left join orcsuplemlan          on o49_codsup = o46_codsup
                inner join orcsuplemdespesappa  on o136_orcsuplem = o46_codsup
                                               and o136_valor > 0
                inner join ppaestimativadespesa on o07_sequencial = o136_ppaestimativadespesa
                inner join ppadotacao           on o08_sequencial = o07_coddot
                inner join orctiporec on o15_codigo = o08_recurso
          where {$wheretipo} o48_retificado is null
            and {$txt_where}
            and o08_instit in ({$db_selinstit})
         ) as xx
      group by
      recurso,o15_descr,anousu, o15_codigo
      order by o15_recurso
    ";
    $res = db_query($sql);

    $linhas = pg_num_rows($res);
    $iTotalLinhas += $linhas;

    $head2 = 'RELATORIO DE SUPLEMENTAÇÔES POR RECURSO';
    $perini = explode('-', $dt_ini);
    $perfim = explode('-', $dt_fim);

    $head3 = "PERIODO : $perini[2]/$perini[1]/$perini[0]  à  $perfim[2]/$perfim[1]$perfim[0]";
    $xinstit = explode('-', $db_selinstit);
    $sqlInstit = 'select codigo,nomeinst from db_config where codigo in ('.str_replace('-', ', ', $db_selinstit).') ';
    $resultinst = db_query($sqlInstit);
    $descr_inst = '';
    $xvirg = '';
    for ($xins = 0; $xins < pg_num_rows($resultinst); ++$xins) {
        db_fieldsmemory($resultinst, $xins);
        $descr_inst .= $xvirg.$nomeinst;
        $xvirg = ', ';
    }
    $header4 = $descr_inst;

    if ($tiporel == 0) {
        $pdf = new PDF();
        $pdf->Open();
        $pdf->AliasNbPages();
        $pdf->SetFillColor(235);
        $pdf->SetFont('Arial', '', 9);
        $pdf->setY(40);
    }

    $pagina = 1;

    for ($x = 0; $x < $linhas; ++$x) { // loop nos projetos
        db_fieldsmemory($res, $x);

        $recursocompleto = descricaoCompletaRecurso($codigorecurso, $anousu, 4);
        if ($pdf->gety() > $pdf->h - 30 || $pagina == 1) {
            $pagina = 0;
            $pdf->addpage();

            if ($tiporel == 0) {
                $pdf->Cell(180, 4, 'CRÉDITOS ADICIONAIS', '1', 1, 'L', '1');
            } else {
                $pdf->Cell(180, 4, 'TRANSFERÊNCIA DE RECURSOS', '1', 1, 'L', '1');
            }

            $pdf->setfont('arial', '', 9);
            //$pdf->setX(30);
            //$pdf->Cell(100,4,"TOTAIS POR RECURSO",'B',1,"L",'0');
            $pdf->Ln();
            $pdf->setX(20);
            $pdf->Cell(20, 4, 'RECURSO', 'B', 0, 'L', '0');
            $pdf->setX(50);
            $pdf->Cell(110, 4, 'DESCR', 'B', 0, 'L', '0');
            $pdf->Cell(20, 4, 'TOTAL', 'B', 1, 'R', '0');  // br
            $pdf->Ln();
        }

        $pdf->setX(15);
        $pdf->Cell(20, 4, "$recursocompleto", 0, 0, 'L', '0');
        $pdf->setX(50);
        $pdf->Cell(110, 4, "$o15_descr", 0, 0, 'L', '0');
        $pdf->Cell(20, 4, db_formatar($total, 'f'), 0, 1, 'R', '0');  // br
        // trans as qtds por tipo de projeto
        $sql = "select o46_tiposup,
            o48_descr,
            sum(o47_valor) as vtotal
            from orcsuplem
            inner join orcprojeto on o39_codproj = orcsuplem.o46_codlei
            left join orcsuplemretif on o48_retificado= orcprojeto.o39_codproj
            inner join orcsuplemtipo on o48_tiposup = o46_tiposup
            left outer join orcsuplemlan on o49_codsup = o46_codsup
            inner join orcsuplemval on o47_codsup = o46_codsup
            and orcsuplemval.o47_valor >0
            inner join orcdotacao on o58_coddot = orcsuplemval.o47_coddot and
            o58_anousu = orcsuplemval.o47_anousu and
            o58_instit in ($db_selinstit)
            left outer join orctiporec on o15_codigo = o58_codigo
            where $wheretipo o48_retificado is null  and
            $txt_where
            and o15_codigo = $codigorecurso
            and o15_recurso = '$recurso'
            group by
            o46_tiposup,o48_descr
        ";
        $restipo = db_query($sql);
        if (pg_num_rows($restipo) > 0) {
            for ($u = 0; $u < pg_num_rows($restipo); ++$u) {
                db_fieldsmemory($restipo, $u);
                $pdf->setX(50);
                $pdf->Cell(20, 4, ' ', 0, 0, 'L', '0');
                $pdf->Cell(90, 4, "$o48_descr", 0, 0, 'L', '0');
                $pdf->Cell(20, 4, db_formatar($vtotal, 'f'), 0, 1, 'R', '0');  // br
            }
        }
        $pdf->setX(50);
        $pdf->Cell(130, 4, ' ', 'T', 0, 'L', '0');

        $pdf->Ln();
    }
}

if ($iTotalLinhas == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado ');
}

// TOTALIZACAO

$sql = "
    select o46_tiposup,
           o48_descr,
           sum(o47_valor) as o47_valor
    from orcsuplem
    join orcsuplemtipo on o46_tiposup = o48_tiposup
    join orcprojeto on o39_codproj = orcsuplem.o46_codlei
    left join orcsuplemretif on o48_retificado= orcprojeto.o39_codproj
    left join orcsuplemlan on o49_codsup = o46_codsup
    inner join orcsuplemval on o47_codsup = o46_codsup
    and orcsuplemval.o47_valor > 0
    inner join orcdotacao on o58_coddot = orcsuplemval.o47_coddot and
    o58_anousu = orcsuplemval.o47_anousu and
    o58_instit in ($db_selinstit)
    inner join orctiporec on o15_codigo = o58_codigo
    where o48_retificado is null  and
    $txt_where
    group by o46_tiposup,
    o48_descr
";
// echo $sql;exit;
$res = $auxiliar->sql_record($sql);
// db_criatabela($res);
if ($auxiliar->numrows == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado ');
}

$head4 = 'RELATORIO DE SUPLEMENTAÇÔES POR TIPO';
$perini = explode('-', $dt_ini);
$perfim = explode('-', $dt_fim);

$head5 = "PERIODO : $perini[2]/$perini[1]/$perini[0]  à  $perfim[2]/$perfim[1]$perfim[0]";

$pdf->AliasNbPages();
$pdf->SetFillColor(235);
$pdf->SetFont('Arial', '', 9);
$pdf->setY(40);

$pagina = 1;
$totalzao = 0;
for ($x = 0; $x < $auxiliar->numrows; ++$x) { // loop nos projetos
    db_fieldsmemory($res, $x);

    if ($pdf->gety() > $pdf->h - 30 || $pagina == 1) {
        $pagina = 0;
        $pdf->addpage();
        $pdf->setfont('arial', '', 9);
        $pdf->Ln();
    }

    $pdf->Cell(20, 4, "$o46_tiposup", 0, 0, 'L', '0');
    $pdf->Cell(90, 4, "$o48_descr", 0, 0, 'L', '0');
    $pdf->Cell(20, 4, db_formatar($o47_valor, 'f'), 0, 1, 'R', '0');  // br
    $totalzao += $o47_valor;
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(130, 4, db_formatar($totalzao, 'f'), 0, 1, 'R', '0');  // br

/// out
$pdf->Output();
