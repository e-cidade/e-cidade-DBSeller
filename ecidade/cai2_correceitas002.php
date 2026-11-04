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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("classes/db_orctiporec_classe.php"));

db_postmemory($_GET);

// quando for emissao sintetica coloca modelo retrato
if ($sinana == 'S1' || $sinana == 'S2') {
    $pdf = new ECidade\Pdf\Pdf();
} else {
    $pdf = new ECidade\Pdf\Pdf('L');
}
$pdf->init(false);
$pdf->AliasNbPages();

$pdf->addTitulo("RELATÓRIO DE RECEITAS ARRECADADAS");
if ($tipo == 'O') {
    $pdf->addTitulo('RECEITAS ORÇAMENTÁRIAS');
} elseif ($tipo == 'E') {
    $pdf->addTitulo('RECEITAS EXTRA-ORÇAMENTÁRIAS');
} else {
    $pdf->addTitulo('TODAS AS RECEITAS');
}
$pdf->addTitulo("Período : " . db_formatar($datai, 'd') . " a " . db_formatar($dataf, 'd'));
if ($ordem == 'r') {
    $orderby = ' order by k02_tipo, k02_codigo ';
} elseif ($ordem == 'e') {
    $orderby = ' order by k02_tipo, estrutural ';
} elseif ($ordem == 'd') {
    $orderby = ' order by k02_tipo, codrec ';
} elseif ($ordem == 'a') {
    $orderby = ' order by k02_tipo, k02_drecei ';
} elseif ($ordem == 'c') {
    if ($sinana == 'S1') {
        $orderby = ' order by k02_tipo, codrec';
    } else {
        $orderby = ' order by k02_tipo, c61_reduz ';
    }
}

$iInstit = db_getsession("DB_instit");
$iAnousu = db_getsession("DB_anousu");
$sSubQueryDesconto = "  -  coalesce ( ( select case
                                           when r.k12_estorn is true
                                             then sum(d.k12_valor)
                                             else sum(d.k12_valor)
                                           end
                	                   from cornumpdesconto d
                  	                  where d.k12_id               = f.k12_id
                                            and d.k12_data             = f.k12_data
                                            and d.k12_autent           = f.k12_autent
                                            and d.k12_numpre           = f.k12_numpre
                                            and d.k12_numpar           = f.k12_numpar
                                            and d.k12_receitaprincipal = f.k12_receit
                                            and d.k12_numnov           = f.k12_numnov ),0 )  ";
$where2 = ' where 1=1 and valor <> 0';

if ($estrut != '') {
    $where2 .= " and estrutural like '$estrut%' ";
}
$inner_sql = "";
$where = '';

if ($codrec != '') {
    $where = ' g.k02_codigo in (' . $codrec . ') and ';
}

$inner_sql = "";

if ($recurso != "") {
    $clorctiporec = new cl_orctiporec;

    $codigo_siconfi_valores = explode(',', $codigo_siconfi);
    $codigo_siconfi_unico = array_unique($codigo_siconfi_valores);
    $codigo_siconfi_unico_str = implode(', ', $codigo_siconfi_unico);

    $subrecurso_valores = explode(',', $subrecurso);
    $subrecurso_unico = array_unique($subrecurso_valores);
    $subrecurso_unico_str = implode(', ', $subrecurso_unico);






    $pdf->addTitulo("Recurso: $codigo_siconfi_unico_str");
    $pdf->addTitulo("Subrecurso: $subrecurso_unico_str");



    $inner_sql = "
        left outer join orcreceita  on o70_codrec = o.k02_codrec
                                   and o70_anousu = o.k02_anousu
        left outer join conplanoreduz c1 on c1.c61_codcon = o70_codfon
                                        and c1.c61_anousu = o70_anousu
        left outer join conplanoreduz c2 on c2.c61_anousu = p.k02_anousu
                                        and c2.c61_reduz  = p.k02_reduz
        left outer join orctiporec on o15_codigo = o70_codigo
    ";

    $inner_sql = analiseQueryPlanoOrcamento($inner_sql);

    $subrecursoValores = explode(',', $subrecurso);
    $subrecursoLista = implode("', '", $subrecursoValores);


    $where .= " c1.c61_codigo in (" . $recurso . ") and o15_recurso in('" .  $subrecursoLista . "') and ";
}

$ordem = ' order by g.k02_codigo, f.k00_dtpaga, f.k00_numpre ';

if ($sinana == 'S1') {
    // sintetico receita
    $sql = "select k02_codigo,k02_tipo,k02_drecei,codrec,estrutural,valor
		        from ( ";

    $sSqlInterno = "
        select g.k02_codigo,
			   g.k02_tipo,
			   g.k02_drecei,
			   case when o.k02_codrec is not null 	then o.k02_codrec else p.k02_reduz end as codrec,
               case when p.k02_codigo is null 	then o.k02_estorc else p.k02_estpla end as estrutural,
               round(sum( f.k12_valor #subquery_desconto#) ,2) as valor
        from cornump f
      inner join corrente r     on r.k12_id     = f.k12_id   and
                                   r.k12_data   = f.k12_data and
                       r.k12_autent = f.k12_autent
      inner join tabrec g       on g.k02_codigo = f.k12_receit
      left outer join taborc o  on o.k02_codigo = g.k02_codigo and
                                   o.k02_anousu = extract (year from r.k12_data)
      left outer join tabplan p on p.k02_codigo = g.k02_codigo and
                                   p.k02_anousu = extract (year from r.k12_data)
                      $inner_sql
     where $where f.k12_data between '$datai' and '$dataf' and r.k12_instit = " . db_getsession("DB_instit") . "
     group by g.k02_tipo,
          g.k02_codigo,
          g.k02_drecei,
          codrec,
          estrutural ";

    $sql .= str_replace("#subquery_desconto#", "$sSubQueryDesconto", $sSqlInterno) .
        " union all " .
        str_replace("#subquery_desconto#", "", str_replace("cornump ", "cornumpdesconto ", $sSqlInterno));
    $sql .= " ) as xxx $where2 $orderby ";

} elseif ($sinana == 'S2') {
    // sintetico estrutural
    $sql = "select estrutural,k02_tipo,descr,sum(valor) as valor from
			    ( ";
    $sSqlInterno = "
        select k02_tipo,
			   case when c60_descr is not null 	then c60_descr else o57_descr end as descr,
			   case when p.k02_codigo is null 	then o.k02_estorc else p.k02_estpla end as estrutural,
			   round(f.k12_valor #subquery_desconto# ,2) as valor
          from cornump f
		 inner join corrente r 		  on r.k12_id = f.k12_id and
		 				     r.k12_data = f.k12_data and
						     r.k12_autent = f.k12_autent
		 inner join tabrec g 		  on g.k02_codigo  = f.k12_receit
		 left outer join taborc o 	  on o.k02_codigo = g.k02_codigo and
		 				     o.k02_anousu = extract (year from r.k12_data)
		 left outer join tabplan p 	  on p.k02_codigo = g.k02_codigo and
		 				     p.k02_anousu = extract (year from r.k12_data)
		 left outer join conplanoreduz c1 on p.k02_reduz   = c1.c61_reduz and
		 				     c1.c61_anousu = extract (year from r.k12_data)
		 left outer join conplano    on c1.c61_codcon = c60_codcon
		                            and c1.c61_anousu = c60_anousu
		 left outer join orcreceita       on o70_codrec = o.k02_codrec and
		                                     o70_anousu = extract (year from r.k12_data)
		 left outer join orcfontes        on o57_codfon = o70_codfon and o70_anousu = o57_anousu

		where $where f.k12_data between '$datai'
		  and '$dataf'
		  and r.k12_instit = " . db_getsession("DB_instit");

    $sql .= str_replace("#subquery_desconto#", "$sSubQueryDesconto", $sSqlInterno) .
        " union all " .
        str_replace("#subquery_desconto#", "", str_replace("cornump ", "cornumpdesconto ", $sSqlInterno));

    $sql .= " ) as xxx
			    $where2
			    group by estrutural,descr,k02_tipo";

} elseif ($sinana == 'A') {
    /**
     *  analitico
     *  baixas de banco não tem numpre, porque gera um total no caixa
     */
    $sql = "select *
            from ( ";
    $sSqlInterno = "
        select g.k02_codigo,
			   g.k02_tipo,
			   g.k02_drecei,
			   case when o.k02_codrec is not null 	then o.k02_codrec else p.k02_reduz end as codrec,
			   case when p.k02_codigo is null 	then o.k02_estorc else p.k02_estpla end as estrutural,
			   k12_histcor as k00_histtxt,
			   f.k12_data,
			   f.k12_numpre,
			   f.k12_numpar,
			   c61_reduz,
			   c60_descr,
			   round( f.k12_valor #subquery_desconto# ,2) as valor
         from cornump f
        inner join corrente r 		 on r.k12_id        = f.k12_id
             and r.k12_data      = f.k12_data
             and r.k12_autent    = f.k12_autent
        inner join conplanoreduz c1	on r.k12_conta   = c1.c61_reduz
                and c1.c61_anousu = extract (year from r.k12_data)
        inner join conplano        	on c1.c61_codcon = c60_codcon
                and c60_anousu    = extract (year from r.k12_data)
        inner join tabrec g      		on g.k02_codigo  = f.k12_receit
        left outer join taborc o   	on o.k02_codigo  = g.k02_codigo
                and o.k02_anousu  = extract (year from r.k12_data)
        left outer join tabplan p  	on p.k02_codigo  = g.k02_codigo
                and p.k02_anousu  = extract (year from r.k12_data)
        left join corhist hist       on hist.k12_id     = f.k12_id
                and hist.k12_data   = f.k12_data
                and hist.k12_autent = f.k12_autent
     where $where f.k12_data between '$datai'
 and '$dataf'
 and r.k12_instit = " . db_getsession("DB_instit");

    $sql .= str_replace("#subquery_desconto#", "$sSubQueryDesconto", $sSqlInterno) .
        " union all " .
        str_replace("#subquery_desconto#", "", str_replace("cornump ", "cornumpdesconto ", $sSqlInterno));

    $sql .= " ) as xxx
			    $where2
			    $orderby,
          k12_data ";

} elseif ($sinana == 'S3') {
    $sql = "select k02_codigo, k02_tipo, k02_drecei, codrec, estrutural, c61_reduz, c60_descr, sum(valor) as valor
          from (
            select * from
			    ( ";

    $sSqlInterno = "
        select g.k02_codigo,
			   g.k02_tipo,
			   g.k02_drecei,
			   case when o.k02_codrec is not null 	then o.k02_codrec else p.k02_reduz end as codrec,
			   case when p.k02_codigo is null 	then o.k02_estorc else p.k02_estpla end as estrutural,
			   k12_histcor as k00_histtxt,
			   f.k12_data,
			   f.k12_numpre,
			   f.k12_numpar,
			   c61_reduz,
			   c60_descr,
			   round( f.k12_valor #subquery_desconto#, 2) as valor
        from cornump f
                inner join corrente r 		on r.k12_id        = f.k12_id    and
                                                   r.k12_data      = f.k12_data  and
                                   r.k12_autent    = f.k12_autent
                inner join conplanoreduz c1	on r.k12_conta     = c1.c61_reduz and
                                                   c1.c61_anousu   = extract (year from r.k12_data)
                inner join orctiporec on o15_codigo = c1.c61_codigo
                inner join conplano      	on c1.c61_codcon   = c60_codcon and
                                                   c60_anousu      = extract (year from r.k12_data)
                inner join tabrec g 		on g.k02_codigo    = f.k12_receit
                left outer join taborc o 	on o.k02_codigo    = g.k02_codigo and
                                                   o.k02_anousu    = extract (year from r.k12_data)
                left outer join tabplan p 	on p.k02_codigo    = g.k02_codigo and
                                                   p.k02_anousu    = extract (year from r.k12_data)
                left join corhist hist          on hist.k12_id     = f.k12_id   and
                                           hist.k12_data   = f.k12_data and
                                   hist.k12_autent = f.k12_autent
        where $where f.k12_data between '$datai'
                    and '$dataf'
                    and r.k12_instit = " . db_getsession("DB_instit");

    $sql .= str_replace("#subquery_desconto#", "$sSubQueryDesconto", $sSqlInterno) .
        " union all" .
        str_replace("#subquery_desconto#", "", str_replace("cornump ", "cornumpdesconto ", $sSqlInterno));

    $sql .= " ) as xxx
        			    $where2
			            $orderby,k12_data
        ) as zzz
        group by k02_codigo, k02_tipo, k02_drecei, codrec, estrutural, c61_reduz, c60_descr
        $orderby ";
}

$result = db_query($sql) or die("Erro realizando consulta : " . $sql);
$xxnum = pg_num_rows($result);
if ($xxnum == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem lançamentos para a receita ' . $codrec . ' no período de ' . db_formatar($datai, 'd') . ' a ' . db_formatar($dataf, 'd'));
}
$linha = 0;
$pre = 0;
$total_reco = 0;
$total_rece = 0;
$pagina = 0;
$valatu = array(); /// array que guarda o recursos
if ($sinana == 'S1' or $sinana == 'S3') {
    // relatório sintético ( sem histórico )

    if ($tipo == 'T' || $tipo == 'O') {
        $pdf->ln(2);
        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(220);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
        $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
        $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
        $pdf->Cell(100, 6, "RECEITA ORÇAMENTÁRIA", 1, 0, "C", 1);
        if ($sinana == 'S3') {
            $pdf->Cell(15, 6, "CONTA", 1, 0, "C", 1);
            $pdf->Cell(60, 6, "DESCRIÇÃO CONTA", 1, 0, "C", 1);
        }
        $pdf->Cell(25, 6, "VALOR", 1, 1, "C", 1);
        $pdf->SetFont('Arial', 'B', 9);
        for ($i = 0; $i < $xxnum; $i++) {
            db_fieldsmemory($result, $i);
            if ($k02_tipo == 'E')
                continue;

            // verifica se receita tem desdobramento

            $tem_desdobramento = false;

            if ($desdobrar == 'S') {
                if ($k02_tipo == 'O') {
                    if ($codrec == '')
                        continue;

                    $sql = "select o57_fonte, o70_codigo
                             from orcreceita
                                  inner join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                                  inner join orcfontesdes on o60_anousu = o70_anousu and o60_codfon = o70_codfon
                             where o70_anousu = " . db_getsession("DB_anousu") . " and o70_codrec = $codrec";
                    $result1 = db_query($sql) or die($sql);
                    if ($result1 != false && pg_num_rows($result1) > 0) {
                        $fonte = pg_fetch_result($result1, 0, 0);
                        $o70_codigo = pg_fetch_result($result1, 0, 1);
                        $contamae = db_le_mae_rec_sin($fonte, false);

                        if ($o70_codigo == 1) {

                            $sql = "select o70_codrec,o57_fonte,o57_descr,o60_perc,o15_codigo,o15_descr
                                     from orcreceita
                                    inner join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                                    inner join orcfontesdes on o60_anousu = o70_anousu and o60_codfon = o70_codfon
                                    left join orctiporec on o70_codigo = o15_codigo
                                     where o57_fonte like '$contamae%'
                                                 and orcreceita.o70_anousu =" . db_getsession("DB_anousu") . "
                                     order by o57_fonte";
                            $result1 = db_query($sql);
                            if ($result1 != false && pg_num_rows($result1) > 0) {
                                $tem_desdobramento = true;
                            }
                        }
                    }
                }
            }
            if ($pdf->gety() > $pdf->getH() - 30) {
                $pdf->addpage();
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
                $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
                $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
                $pdf->Cell(100, 6, "RECEITA", 1, 0, "C", 1);
                if ($sinana == 'S3') {
                    $pdf->Cell(15, 6, "CONTA", 1, 0, "C", 1);
                    $pdf->Cell(60, 6, "DESCRIÇÃO CONTA", 1, 0, "C", 1);
                }
                $pdf->Cell(25, 6, "VALOR", 1, 1, "C", 1);
            }
            $pdf->setfont('arial', '', 7);
            $pdf->cell(10, 4, $k02_codigo, 1, 0, "C", $pre);
            $pdf->cell(10, 4, $codrec, 1, 0, "C", $pre);
            $pdf->cell(40, 4, $estrutural, 1, 0, "C", $pre);
            $pdf->cell(100, 4, strtoupper($k02_drecei), 1, 0, "L", $pre);
            if ($sinana == 'S3') {
                $pdf->cell(15, 4, $c61_reduz, 1, 0, "C", $pre);
                $pdf->cell(60, 4, $c60_descr, 1, 0, "L", $pre);
            }
            $pdf->cell(25, 4, db_formatar($valor, 'f'), 1, 1, "R", $pre);
            $total_reco += $valor;

            if ($tem_desdobramento) {

                unset ($dbperc);
                unset ($dbrec);
                unset ($dbrecde);
                unset ($dbreces);
                unset ($dbcodigo);
                unset ($dbdescr);
                $vlrsoma = 0;
                $multiplica = false;
                if ($valor < 0) {
                    $multiplica = true;
                    $valor = $valor * -1;
                }
                for ($recc = 0; $recc < pg_num_rows($result1); $recc++) {
                    db_fieldsmemory($result1, $recc);
                    // aplica o percentual sobre o valor
                    if ($o60_perc == 0)
                        continue;
                    $vlrperc = round($valor * ($o60_perc / 100), 2);
                    $vlrsoma = $vlrsoma + $vlrperc;
                    if ($vlrsoma > $valor) {
                        // arredonda no ultimo desdobramento
                        $vlrperc = $vlrperc - ($vlrsoma - $valor);
                    }
                    $dbperc[$o70_codrec] = $o60_perc;
                    $dbrec[$o70_codrec] = $vlrperc;
                    $dbrecde[$o70_codrec] = $o57_descr;
                    $dbreces[$o70_codrec] = $o57_fonte;
                    $dbcodigo[$o70_codrec] = $o15_codigo;
                    $dbdescr[$o70_codrec] = $o15_descr;
                }
                if ($vlrsoma < $valor) {
                    $vlrperc = $vlrperc + ($valor - $vlrsoma);
                    $dbrec[$o70_codrec] = $vlrperc;
                }
                if ($multiplica) {
                    reset($dbrec);
                    for ($arrr = 0; $arrr < sizeof($dbrec); $arrr++) {
                        $dbrec[key($dbrec)] = $dbrec[key($dbrec)] * -1;
                        next($dbrec);
                    }
                }
                reset($dbperc);
                reset($dbrec);
                reset($dbrecde);
                reset($dbreces);
                reset($dbcodigo);
                reset($dbdescr);
                for ($d = 0; $d < sizeof($dbrec); $d++) {
                    $pdf->cell(20, 4, '', 1, 0, "C", $pre);
                    $pdf->cell(40, 4, $dbreces[key($dbrec)], 1, 0, "C", $pre);
                    $pdf->cell(90, 4, substr(strtoupper($dbrecde[key($dbrec)]) . '-' . $dbcodigo[key($dbrec)] . '-' . $dbdescr[key($dbrec)], 0, 50), 1, 0, "L", $pre);
                    $aa = $dbrec[key($dbrec)];
                    if ($aa < 0)
                        $aa = $aa * -1;

                    $pdf->cell(10, 4, db_formatar($dbperc[key($dbrec)], 'p') . "%", 1, 0, "R", $pre);
                    $pdf->cell(25, 4, db_formatar($aa, 'f'), 1, 1, "R", $pre);

                    $xrecurso = $dbcodigo[key($dbrec)] . '-' . $dbdescr[key($dbrec)];
                    $xvalor = $aa;
                    if (array_key_exists($xrecurso, $valatu)) {
                        $valatu[$xrecurso] += $xvalor;
                    } else {
                        $valatu[$xrecurso] = $xvalor;
                    }
                    next($dbrec);
                    next($dbrecde);
                    next($dbreces);
                    next($dbcodigo);
                    next($dbdescr);
                }

            }

        }
        $pdf->setfont('arial', 'B', 7);
        if ($sinana == 'S1') {
            $pdf->cell(160, 4, "TOTAL ...", 1, 0, "L", 0);
        } elseif ($sinana == 'S3') {
            $pdf->cell(235, 4, "TOTAL ...", 1, 0, "L", 0);
        }
        $pdf->cell(25, 4, db_formatar($total_reco, 'f'), 1, 1, "R", 0);
    }

    if ($tipo == 'T' || $tipo == 'E') {
        $pdf->ln(2);
        if ($tipo == 'E') {
            $pdf->AddPage();
        } else {
            if ($pdf->gety() > $pdf->getH() - 30) {
                $pdf->AddPage();
            }
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(220);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
        $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
        $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
        $pdf->Cell(100, 6, "RECEITA EXTRA-ORÇAMENTÁRIA", 1, 0, "C", 1);
        if ($sinana == 'S3') {
            $pdf->Cell(15, 6, "CONTA", 1, 0, "C", 1);
            $pdf->Cell(60, 6, "DESCRIÇÃO CONTA", 1, 0, "L", 1);
        }
        $pdf->Cell(25, 6, "VALOR", 1, 1, "C", 1);
        $pdf->SetFont('Arial', 'B', 9);
        for ($i = 0; $i < $xxnum; $i++) {
            db_fieldsmemory($result, $i);
            if ($k02_tipo == 'O')
                continue;
            if ($pdf->gety() > $pdf->getH() - 30) {
                $pdf->addpage();
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
                $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
                $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
                $pdf->Cell(100, 6, "RECEITA", 1, 0, "C", 1);
                if ($sinana == 'S3') {
                    $pdf->Cell(15, 6, "CONTA", 1, 0, "C", 1);
                    $pdf->Cell(60, 6, "DESCRIÇÃO CONTA", 1, 0, "L", 1);
                }
                $pdf->Cell(25, 6, "VALOR", 1, 1, "C", 1);
            }
            $pdf->setfont('arial', '', 7);
            $pdf->cell(10, 4, $k02_codigo, 1, 0, "C", $pre);
            $pdf->cell(10, 4, $codrec, 1, 0, "C", $pre);
            $pdf->cell(40, 4, $estrutural, 1, 0, "C", $pre);
            $pdf->cell(100, 4, strtoupper($k02_drecei), 1, 0, "L", $pre);
            if ($sinana == 'S3') {
                $pdf->cell(15, 4, $c61_reduz, 1, 0, "C", $pre);
                $pdf->cell(60, 4, $c60_descr, 1, 0, "L", $pre);
            }
            $pdf->cell(25, 4, db_formatar($valor, 'f'), 1, 1, "R", $pre);
            $total_rece += $valor;
        }
        $pdf->setfont('arial', 'B', 7);
        if ($sinana == 'S1') {
            $pdf->cell(160, 4, "TOTAL ...", 1, 0, "L", 0);
        } elseif ($sinana == 'S3') {
            $pdf->cell(235, 4, "TOTAL ...", 1, 0, "L", 0);
        }
        $pdf->cell(25, 4, db_formatar($total_rece, 'f'), 1, 1, "R", 0);

    }

    if ($sinana == 'S1') {
        $pdf->cell(160, 4, "TOTAL GERAL", 1, 0, "L", 0);
    } elseif ($sinana == 'S3') {
        $pdf->cell(235, 4, "TOTAL GERAL", 1, 0, "L", 0);
    }
    $pdf->cell(25, 4, db_formatar($total_rece + $total_reco, 'f'), 1, 1, "R", 0);
    $pdf->ln(5);

    $pdf->cell(110, 4, "DEMONSTRATIVO DO DESDOBRAMENTO DA RECEITA LIVRE", 1, 1, "L", 0);

    $totalrecursos = 0;
    while (list ($key, $valor) = each($valatu)) {
        $totalrecursos += $valor;
    }

    reset($valatu);

    while (list ($key, $valor) = each($valatu)) {
        $pdf->cell(70, 5, $key, 0, 0, "L", 0, 0, ".");
        $pdf->cell(20, 5, db_formatar($valor, 'f'), 0, 0, "R", 0);
        $pdf->cell(20, 5, db_formatar($valor / $totalrecursos * 100, 'p') . "%", 0, 1, "R", 0);
    }
    $pdf->setfont('arial', 'B', 7);
    $pdf->cell(110, 5, db_formatar($totalrecursos, 'f'), 1, 1, "R", 0);

} elseif ($sinana == 'S2') {
    ////// sintetico por estrutural
    $troca = 1;
    $pdf->ln(2);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(220);
    for ($i = 0; $i < $xxnum; $i++) {
        db_fieldsmemory($result, $i);
        if ($tipo == "O" && $k02_tipo == "E") {
            continue;
        } elseif ($tipo == "E" && $k02_tipo == "O") {
            continue;
        }
        if ($pdf->gety() > $pdf->getH() - 30 || $troca == 1) {
            $pdf->addpage();
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
            $pdf->Cell(100, 6, "DESCRIÇÃO", 1, 0, "C", 1);
            $pdf->Cell(25, 6, "VALOR", 1, 1, "C", 1);
            $troca = 0;
        }
        $pdf->setfont('arial', '', 7);
        $pdf->cell(40, 4, $estrutural, 1, 0, "C", $pre);
        $pdf->cell(100, 4, $descr, 1, 0, "L", $pre);
        $pdf->cell(25, 4, db_formatar($valor, 'f'), 1, 1, "R", $pre);
        $total_reco += $valor;
    }
    $pdf->setfont('arial', '', 7);
    $pdf->cell(140, 4, 'TOTAL GERAL', 1, 0, "C", $pre);
    $pdf->cell(25, 4, db_formatar($total_reco, 'f'), 1, 1, "R", $pre);

} else {

    // relatorio analitico ( com histórico )
    if ($tipo == 'T' || $tipo == 'O') {
        $pdf->ln(2);
        $pdf->AddPage();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(220);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
        $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
        $pdf->Cell(15, 6, "DATA", 1, 0, "C", 1);
        $pdf->Cell(15, 6, "NUMPRE", 1, 0, "C", 1);
        $pdf->Cell(25, 6, "ESTRUTURAL", 1, 0, "C", 1);
        $pdf->Cell(80, 6, "RECEITA ORÇAMENTÁRIA", 1, 0, "C", 1);
        $pdf->Cell(25, 6, "VALOR", 1, 0, "C", 1);
        $pdf->Cell(15, 6, "CONTA", 1, 0, "L", 1);
        $pdf->Cell(65, 6, "DESCRIÇÃO", 1, 1, "L", 1);
        $pdf->SetFont('Arial', 'B', 9);
        $pre = 1;
        for ($i = 0; $i < $xxnum; $i++) {
            db_fieldsmemory($result, $i);
            if ($k02_tipo == 'E')
                continue;

            // verifica se receita tem desdobramento

            $tem_desdobramento = false;

            if ($pdf->gety() > $pdf->getH() - 30) {
                $pdf->addpage("L");
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
                $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
                $pdf->Cell(15, 6, "DATA", 1, 0, "C", 1);
                $pdf->Cell(15, 6, "NUMPRE", 1, 0, "C", 1);
                $pdf->Cell(25, 6, "ESTRUTURAL", 1, 0, "C", 1);
                $pdf->Cell(80, 6, "RECEITA", 1, 0, "C", 1);
                $pdf->Cell(25, 6, "VALOR", 1, 0, "C", 1);
                $pdf->Cell(15, 6, "CONTA", 1, 0, "C", 1);
                $pdf->Cell(65, 6, "DESCRIÇÃO", 1, 1, "C", 1);
                $pre = 1;
            }
            if ($pre == 1)
                $pre = 0;
            else
                $pre = 1;


            $oData = new DBDate($k12_data);
            $sData = $oData->getDate(DBDate::DATA_PTBR);
            $pdf->setfont('arial', '', 7);
            $pdf->cell(10, 4, $k02_codigo, 1, 0, "C", $pre);
            $pdf->cell(10, 4, $codrec, 1, 0, "C", $pre);
            $pdf->Cell(15, 4, $sData, 1, 0, "C", $pre);
            $pdf->Cell(15, 4, $k12_numpre, 1, 0, "C", $pre);
            $pdf->cell(25, 4, $estrutural, 1, 0, "C", $pre);
            $pdf->cell(80, 4, strtoupper($k02_drecei), 1, 0, "L", $pre);
            $pdf->cell(25, 4, db_formatar($valor, 'f'), 1, 0, "R", $pre);
            $pdf->cell(15, 4, $c61_reduz, 1, 0, "C", $pre);
            $pdf->cell(65, 4, $c60_descr, 1, 1, "L", $pre);
            if (trim($k00_histtxt) != '') {
                $pdf->multicell(245, 4, 'HISTÓRICO :  ' . $k00_histtxt, 1, "L", $pre);
            }
            $total_reco += $valor;

        }
        $pdf->setfont('arial', 'B', 7);
        $pdf->cell(140, 4, "TOTAL ...", 1, 0, "L", 0);
        $pdf->cell(25, 4, db_formatar($total_reco, 'f'), 1, 1, "R", 0);
    }

    if ($tipo == 'T' || $tipo == 'E') {
        $pdf->ln(2);
        if ($pdf->gety() > $pdf->getH() - 30) {
            $pdf->AddPage("L");
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(220);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
        $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
        $pdf->Cell(15, 6, "DATA", 1, 0, "C", 1);
        $pdf->Cell(25, 6, "ESTRUTURAL", 1, 0, "C", 1);
        $pdf->Cell(80, 6, "RECEITA EXTRA-ORÇAMENTÁRIA", 1, 0, "C", 1);
        $pdf->Cell(25, 6, "VALOR", 1, 0, "C", 1);
        $pdf->Cell(0, 6, "HISTÓRICO", 1, 1, "C", 1);
        $pdf->SetFont('Arial', 'B', 9);
        for ($i = 0; $i < $xxnum; $i++) {
            db_fieldsmemory($result, $i);
            if ($k02_tipo == 'O')
                continue;
            if ($pdf->gety() > $pdf->getH() - 30) {
                $pdf->addpage("L");
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(10, 6, "COD", 1, 0, "C", 1);
                $pdf->Cell(10, 6, "RED", 1, 0, "C", 1);
                $pdf->Cell(15, 6, "DATA", 1, 0, "C", 1);
                $pdf->Cell(40, 6, "ESTRUTURAL", 1, 0, "C", 1);
                $pdf->Cell(100, 6, "RECEITA", 1, 0, "C", 1);
                $pdf->Cell(25, 6, "VALOR", 1, 0, "C", 1);
                $pdf->Cell(0, 6, "HISTÓRICO", 1, 1, "C", 1);
            }

            $oData = new DBDate($k12_data);
            $sData = $oData->getDate(DBDate::DATA_PTBR);

            $pdf->setfont('arial', '', 7);
            $pdf->cell(10, 4, $k02_codigo, 1, 0, "C", $pre);
            $pdf->cell(10, 4, $codrec, 1, 0, "C", $pre);
            $pdf->Cell(15, 4, $sData, 1, 0, "C", $pre);
            $pdf->cell(40, 4, $estrutural, 1, 0, "C", $pre);
            $pdf->cell(100, 4, strtoupper($k02_drecei), 1, 0, "L", $pre);
            $pdf->cell(25, 4, db_formatar($valor, 'f'), 1, 0, "R", $pre);
            $pdf->multicell(0, 4, $k00_histtxt, 1, "L", $pre);
            $total_rece += $valor;
        }
        $pdf->setfont('arial', 'B', 7);
        $pdf->cell(140, 4, "TOTAL ...", 1, 0, "L", 0);
        $pdf->cell(25, 4, db_formatar($total_rece, 'f'), 1, 1, "R", 0);
    }
    $pdf->cell(140, 4, "TOTAL GERAL", 1, 0, "L", 0);
    $pdf->cell(25, 4, db_formatar($total_rece + $total_reco, 'f'), 1, 1, "R", 0);
    $pdf->ln(5);

}

$pdf->Output();
