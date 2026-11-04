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

include(modification("fpdf151/pdf.php"));
include(modification("classes/db_empempenho_classe.php"));
include(modification("classes/db_cgm_classe.php"));
include(modification("classes/db_orctiporec_classe.php"));
include(modification("classes/db_orcdotacao_classe.php"));
include(modification("classes/db_orcorgao_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_conlancamcgm_classe.php"));
include(modification("classes/db_conlancamval_classe.php"));
include(modification("classes/db_conlancam_classe.php"));
include(modification("classes/db_orcsuplem_classe.php"));
include(modification("classes/db_conlancamrec_classe.php"));
include(modification("classes/db_conlancamemp_classe.php"));
include(modification("classes/db_conlancamdot_classe.php"));
include(modification("classes/db_conlancamdig_classe.php"));
include(modification("libs/db_liborcamento.php"));

db_postmemory($HTTP_POST_VARS);

$clrotulo = new rotulocampo;
$clconlancamval = new cl_conlancamval;
$clconlancamcgm = new cl_conlancamcgm;
$clconlancam  = new cl_conlancam;
$auxiliar     = new cl_conlancam;
$clorcsuplem = new cl_orcsuplem;
$clconlancamrec = new cl_conlancamrec;
$clconlancamemp  = new cl_conlancamemp;
$clconlancamdot  = new cl_conlancamdot;
$clconlancamdig  = new cl_conlancamdig;

$clconlancamcgm->rotulo->label();
$clconlancamval->rotulo->label();
$clconlancam->rotulo->label();
$clorcsuplem->rotulo->label();

$clrotulo->label("c60_descr");
$clrotulo->label("c53_descr");
$clrotulo->label("c53_coddoc");


///////////////////////////////////////////////////////////////////////
 $data1="";
 $data2="";
 @$data1="$data1_ano-$data1_mes-$data1_dia";
 @$data2="$data2_ano-$data2_mes-$data2_dia";
 if (strlen($data1) < 7){
    $data1= db_getsession("DB_anousu")."-01-01";
 }
 if (strlen($data2) < 7){
    $data2= db_getsession("DB_anousu")."-12-31";
 }

 //---------
 if (isset($lista)){
    $w="(";
    $tamanho= sizeof($lista);
    for ($x=0;$x < sizeof($lista);$x++){
       $w = $w."$lista[$x]";
       if ($x < $tamanho-1) {
         $w= $w.",";
       }
    }
    $w = $w.")";
 }
  //--  monta sql
 $txt_where = "o70_instit =".db_getsession("DB_instit");
 $txt_where = $txt_where." and c74_codrec in  $w";
 $txt_where = $txt_where." and c74_data between '$data1' and '$data2' ";

////////////////////////////////////////////////////

//saldo anterior

$dt_ini = db_getsession("DB_anousu")."-01-01";
$dt_fim = $data1;
if( substr($dt_fim,8,2) == '01' ){

  if ( $dt_ini != $dt_fim ){
      $ultdia = ( substr($dt_fim,5,2) == '01' ||
	          substr($dt_fim,5,2) == '03' ||
		  substr($dt_fim,5,2) == '05' ||
		  substr($dt_fim,5,2) == '07' ||
		  substr($dt_fim,5,2) == '08' ||
		  substr($dt_fim,5,2) == '10' ||
		  substr($dt_fim,5,2) == '12' ? '30' : '31' );

      $dt_fim = substr($dt_fim,0,5).str_pad((substr($dt_fim,5,2)-1),2,'0',STR_PAD_LEFT)."-".str_pad($ultdia,2,'0',STR_PAD_LEFT);
   }

}else{
   $dt_fim = substr($dt_fim,0,8).str_pad((substr($dt_fim,8,2)-1),2,'0',STR_PAD_LEFT);
}

//echo " - $dt_ini,$dt_fim";exit;

//saldo atual
if( $dt_fim != db_getsession('DB_anousu')."-01-01" ){

  $result1 = db_receitasaldo(11,2,3,true," o70_codrec in $w ",$anousu,$dt_ini,$dt_fim);

  $rows = pg_num_rows($result1);
  if( $rows == 0 ) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Não existem dados para gerar a consulta ! ok  in $w $anousu,$dt_ini,$dt_fim");
  }

  $saldo_arrecadado_anterior = pg_result($result1,0,'saldo_arrecadado');

  //db_criatabela($result1);

}else{
  $saldo_arrecadado_anterior = 0;
}


$result2 = db_receitasaldo(11,2,3,true," o70_codrec in $w ",$anousu,$data1,$data2);


$rows = pg_num_rows($result2);
if( $rows == 0 ) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Não existem dados para gerar a consulta ! ');
}


//db_criatabela($result2);exit;

//exit;


//////////////////////////////////////////////////////////////////////
$head2 = "RAZÃO POR RECEITA";
$head5 = "PERÍODO : ".db_formatar($data1,'d')." à ".db_formatar($data2,'d');

$pdf = new PDF(); // abre a classe
$pdf->Open(); // abre o relatorio
$pdf->AliasNbPages(); // gera alias para as paginas
$pdf->AddPage('P'); // adiciona uma pagina
$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(235);
$tam = '04';
$imprime_header=true;
$contador=0;
$repete = "";
$__total=0;


$pdf->SetFont('Arial','',7);

for ($x=0; $x < $rows;$x++){

	db_fieldsmemory($result2,$x);

	$pdf->Cell(20,$tam, "Receita:",0,0,"L");
	$pdf->Cell(30,$tam, $o57_fonte ,0,0,"L");
	$pdf->Cell(140,$tam,$o57_descr ,0,1,"L");

	$pdf->Cell(30,$tam, "Previsão inicial:",0,0,"L");
	$pdf->Cell(40,$tam, db_formatar($saldo_inicial,'f'),0,1,"R");

        $pdf->Cell(30,$tam, "Previsão Adicional:",0,0,"L");
        $pdf->Cell(40,$tam, db_formatar($saldo_prevadic_acum,'f'),0,1,"R");

	$pdf->Cell(30,$tam, "Total Ajustado:",0,0,"L");
        $pdf->Cell(40,$tam, db_formatar($saldo_inicial+$saldo_prevadic_acum,'f'),0,1,"R");

	$pdf->Cell(30,$tam, "Arrecadado Anterior:",0,0,"L");
	$pdf->Cell(40,$tam, db_formatar($saldo_arrecadado_anterior,'f'),0,1,"R");

	$pdf->Cell(30,$tam, "Arrecadado Período:",0,0,"L");
        $pdf->Cell(40,$tam, db_formatar($saldo_arrecadado,'f'),0,1,"R");

	$pdf->Cell(30,$tam, "Diferença:",0,0,"L");
        $pdf->Cell(40,$tam, db_formatar(($saldo_inicial+$saldo_prevadic_acum)-($saldo_arrecadado_anterior+$saldo_arrecadado),'f'),0,1,"R");



        $sql_analitico= "select
                	     c74_codlan,
                         c74_data,
                         c53_coddoc,
                         c53_descr,
                         c82_reduz as credito,
                         bb.c60_descr as credito_descr,
                         case when c53_coddoc = 100 then c70_valor else c70_valor * -1 end as c70_valor,
			             c74_codrec,
			             c79_codsup,
			             z01_nome,
			             c72_complem,
                         c71_coddoc,
			             c58_descr || case
                                       when c53_coddoc = 100 and substr(o57_fonte,1,1) = '9' then ' - ESTORNO'
                                        else ''
                                     end as c58_descr
                   from conlancamrec
             inner join conlancam on c70_codlan = c74_codlan
			 inner join orcreceita      on o70_codrec=c74_codrec  and o70_anousu=c74_anousu
             inner join orcfontes on o57_anousu = o70_anousu
                                 and o57_codfon = o70_codfon

            inner join conlancampag on c82_codlan = c74_codlan
			inner join conplanoreduz aa on aa.c61_reduz = c82_reduz and aa.c61_anousu=c74_anousu



		    inner join conplano bb on bb.c60_codcon  = aa.c61_codcon and bb.c60_anousu  = aa.c61_anousu
             left join conlancamcompl on c72_codlan = c74_codlan

		    inner join conlancamdoc	 on c71_codlan  = c74_codlan
		    inner join conhistdoc 	 on c53_coddoc  = conlancamdoc.c71_coddoc
			 left join conlancamsup on c79_codlan = c74_codlan
			 left join conlancamcgm on c76_codlan = c74_codlan
             left join cgm on z01_numcgm = c76_numcgm
			 left join conlancamdig on c78_codlan = c74_codlan
             left join conlancamconcarpeculiar on c08_codlan = c74_codlan
             left join concarpeculiar on c58_sequencial = c08_concarpeculiar
	 where c74_codrec = $o70_codrec
           and c74_data between '$data1' and '$data2'
         order by c74_data,c74_codlan
";


$sql_analitico = <<<SQL

                   select
                        c74_codlan,
                        c74_data,
                        c53_tipo,
                        c53_coddoc,
                        c53_descr,
                        c61_reduz as credito,
                        bb.c60_descr as credito_descr,
                        case
                            when c53_tipo in (101, 111) then c70_valor * -1
                            else c70_valor
                        end as c70_valor,
                        c74_codrec,
                        c79_codsup,
                        z01_nome,
                        c72_complem,
                        c71_coddoc,
                        c58_descr || case
                                      when c53_coddoc = 100 and substr(o57_fonte,1,1) = '9' then ' - ESTORNO'
                                       else ''
                                    end as c58_descr
                        from conlancamrec
                        inner join conlancam on c70_codlan = c74_codlan
                        inner join orcreceita      on o70_codrec=c74_codrec  and o70_anousu=c74_anousu
                        inner join orcfontes on o57_anousu = o70_anousu
                                and o57_codfon = o70_codfon


                        join conlancamval on c74_codlan = c69_codlan
                                         and c69_ordem = 1
                        INNER JOIN conplanoreduz aa ON aa.c61_reduz = c69_credito
                                                   AND aa.c61_anousu = c74_anousu



                        inner join conplano bb on bb.c60_codcon  = aa.c61_codcon and bb.c60_anousu  = aa.c61_anousu
                        left join conlancamcompl on c72_codlan = c74_codlan

                        inner join conlancamdoc	 on c71_codlan  = c74_codlan
                        inner join conhistdoc 	 on c53_coddoc  = conlancamdoc.c71_coddoc
                        left join conlancamsup on c79_codlan = c74_codlan
                        left join conlancamcgm on c76_codlan = c74_codlan
                        left join cgm on z01_numcgm = c76_numcgm
                        left join conlancamdig on c78_codlan = c74_codlan
                        left join conlancamconcarpeculiar on c08_codlan = c74_codlan
                        left join concarpeculiar on c58_sequencial = c08_concarpeculiar
                        where c74_codrec = $o70_codrec
                          and c74_data between '$data1' and '$data2'
                        order by c74_data,c74_codlan
SQL;


//echo $sql_analitico;exit;

        $res=$clconlancam->sql_record($sql_analitico);
       //echo $sql_analitico;
        //db_criatabela($res);    exit;
       if ($clconlancam->numrows > 0 ){

          $pdf->ln();
          $pdf->cell( 15,$tam,"Lancamento",1,0,"C",0);
          $pdf->cell( 15,$tam,"Data",1,0,"C",0);
          $pdf->cell( 11,$tam,"Doc.",1,0,"R",0);
          //$pdf->cell( 10,$alt,"Histórico",1,0,"R",0);
          $pdf->cell( 40,$tam,"Descrição",1,0,"L",0);
          $pdf->cell( 85,$tam,"Identificação",1,0,"L",0);
          $pdf->cell( 30,$tam,"Valor",1,1,"R",0);

          $dt = "";

          for( $i = 0 ; $i < pg_num_rows($res); $i ++ ){

            db_fieldsmemory($res,$i);

	    $pdf->cell( 15,$tam,$c74_codlan,0,0,"C",0);
	    if( $dt == "" || $dt != $c74_data ) {
              $pdf->cell( 15,$tam,db_formatar($c74_data,'d'),0,0,"C",0);
	    }else{
              $pdf->cell( 15,$tam,'',0,0,"C",0);
	    }
	    $dt = $c74_data;
            $pdf->cell( 11,$tam,$c71_coddoc,0,0,"R",0);
            //$pdf->cell( 10,$alt,$c53_coddoc,0,0,"R",0);
            $pdf->cell( 40,$tam,substr($c53_descr,0,30),0,0,"L",0);
            $pdf->cell( 85,$tam,($credito==''?$z01_nome:$credito."-".$credito_descr),0,0,"L",0);
            $pdf->cell( 30,$tam,db_formatar($c70_valor,'f'),0,1,"R",0);
            if( $c72_complem != "" ){
               $pdf->cell( 41,$tam,'',0,0,"C",0);
               $pdf->Multicell(125,$tam,$c72_complem,0,"L",0);

            }

         }

      }



 }

$pdf->output();

?>
