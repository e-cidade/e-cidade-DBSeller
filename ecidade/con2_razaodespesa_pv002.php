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
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orcreserva_classe.php")); // classe da reserva

$clorcreserva = new cl_orcreserva;
$clorcreserva->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o58_coddot");
$clrotulo->label("o83_autori");
$clrotulo->label("DBtxtmes");
$clrotulo->label("DBtxtmesacumulado");
$clrotulo->label("DBtxtperiodoini");
$clrotulo->label("DBtxtperiodofim");

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$anousu = db_getsession("DB_anousu");

 $data1="";
 $data2="";
 @$data1="$data1_ano-$data1_mes-$data1_dia"; 
 @$data2="$data2_ano-$data2_mes-$data2_dia"; 
 if (strlen($data1) < 7){
    $data1= db_getsession("DB_anousu")."-01-31";
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
 $txt_where=" o58_instit =".db_getsession("DB_instit");
 if (isset($lista)){
     $txt_where= $txt_where." and c73_coddot in  $w";
 }  
 // data
 $txt_where = $txt_where." and c73_data between '$data1' and '$data2' "; 

 if ($listarec!=""){
    if (isset($verrec) and $verrec=="com"){
        $txt_where= $txt_where." and orcdotacao.o58_codigo in  ($listarec)";
    } else {
        $txt_where= $txt_where." and orcdotacao.o58_codigo not in  ($listarec)";
     }	 
 }  

$nivel = 3;

$result = db_dotacaosaldo(8, 2, 2, true, " o58_coddot in {$w} and o58_anousu = {$anousu} ", $anousu, $data1, $data2);

if (pg_num_rows($result) == 0) {
  db_redireciona("db_erros.php?fechar=true&db_erro=Dotação não cadastrada.");
}

$x = array("01" => "Janeiro",
           "02" => "Fevereiro",
           "03" => "Março",
           "04" => "Abril",
           "05" => "Maio",
           "06" => "Junho",
           "07" => "Julho",
           "08" => "Agosto",
           "09" => "Setembro",
           "10" => "Outubro",
           "11" => "Novembro",
           "12" => "Dezembro");

$head4 = "RAZÃO DESPESA";
$head7 = "PERÍODO: " .db_formatar($data1,'d')." à ".db_formatar($data2,'d');

$pdf = new PDF();
$pdf->Open();
$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$troca = 1;
$alt   = 4;


$numrows = pg_num_rows($result);

for( $dotacao = 0; $dotacao < $numrows ; $dotacao ++ ){
	
	db_fieldsmemory($result, $dotacao);

        $head6 = "REDUZIDO DA DOTAÇÃO: {$o58_coddot}";

	$pdf->addpage("L");
	$pdf->setfont('arial','b',8);

	$pdf->cell(149,$alt,"Descrição",1,0,"C",1);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->cell( 63,$alt,"Financeiro / ".$x[$mesusu],1,0,"C",1);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->cell( 63,$alt,"Acumulado ",1,1,"C",1);

	$pdf->cell( 21,$alt,"Orgão:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_orgao,1,0,"L",0);
	$pdf->cell( 98,$alt,$o40_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo Inicial:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($dot_ini,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo Inicial:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($dot_ini,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Unidade:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_unidade,1,0,"L",0);
	$pdf->cell( 98,$alt,$o41_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo Anterior:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($saldo_anterior,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo Anterior:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,0,1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Função:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_funcao,1,0,"L",0);
	$pdf->cell( 98,$alt,$o52_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Suplementação:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($suplementado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Suplementação:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($suplementado_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Sub-Função:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_subfuncao,1,0,"L",0);
	$pdf->cell( 98,$alt,$o53_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Redução:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($reduzido,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Redução:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($reduzido_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Programa:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_programa,1,0,"L",0);
	$pdf->cell( 98,$alt,$o54_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Empenhado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($empenhado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Empenhado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($empenhado_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Proj/Atividade:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_projativ,1,0,"L",0);
	$pdf->cell( 98,$alt,$o55_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Anulado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($anulado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Anulado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($anulado_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Elemento:",1,0,"L",0);
	$pdf->setfont('arial','',8);

	$pdf->cell( 30,$alt,db_formatar($o58_elemento,"elemento_int"),1,0,"L",0);

	$pdf->cell( 98,$alt,$o56_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Liquidado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($liquidado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Liquidado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,(db_formatar($liquidado_acumulado,'f')),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell( 21,$alt,"Recurso:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 30,$alt,$o58_codigo,1,0,"L",0);
	$pdf->cell( 98,$alt,$o15_descr,1,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Pago:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($pago,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Pago:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,(db_formatar($pago_acumulado,'f')),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell(149,$alt,"",0,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"A pagar liquidado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($atual_a_pagar_liquidado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"A pagar liquidado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,(db_formatar($liquidado_acumulado-$pago_acumulado,'f')),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell(149,$alt,"",0,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"A pagar empenhado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($atual_a_pagar,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"A pagar empenhado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($empenhado_acumulado-$anulado_acumulado-$liquidado_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell(149,$alt,"",0,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo dotação:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($atual,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo dotação:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($dot_ini+$suplementado_acumulado-$reduzido_acumulado-$empenhado_acumulado+$anulado_acumulado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell(149,$alt,"",0,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Reservado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($reservado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Reservado:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($reservado,'f'),1,1,"R",0);

	$pdf->setfont('arial','b',8);
	$pdf->cell(149,$alt,"",0,0,"L",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo disponível:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($atual_menos_reservado,'f'),1,0,"R",0);
	$pdf->cell(1.5,$alt,"",0,0,"C",0);
	$pdf->setfont('arial','b',8);
	$pdf->cell( 31,$alt,"Saldo disponível:",1,0,"L",0);
	$pdf->setfont('arial','',8);
	$pdf->cell( 32,$alt,db_formatar($dot_ini+$suplementado_acumulado-$reduzido_acumulado-$empenhado_acumulado+$anulado_acumulado-$reservado,'f'),1,1,"R",0);


	 ///////////////////////////////////////////////////

	$sql_analitico= "select c73_codlan,
		                c73_data, 
				c53_coddoc,
				c53_descr, 
				c82_reduz as credito,
				dd.c60_descr as credito_descr,
				c70_valor ,
				c79_codsup,
				e60_codemp||'/'||e60_anousu as c75_numemp,
				c72_complem,
				z01_nome,
                                c71_coddoc
			from conlancamdot 
			     inner join conlancam on c70_codlan = c73_codlan 
			     inner join orcdotacao      on o58_coddot=c73_coddot and o58_anousu=c73_anousu
			     inner join orcelemento     on o56_codele = orcdotacao.o58_codele and o56_anousu = orcdotacao.o58_anousu

			     left  join conlancampag    on c73_codlan = c82_codlan                        

			     left  join conplanoreduz cc on cc.c61_reduz= c82_reduz and cc.c61_anousu=".db_getsession("DB_anousu")."
			     left  join conplano      dd on dd.c60_codcon = cc.c61_codcon  and dd.c60_anousu = cc.c61_anousu


			     inner join conlancamdoc 	 on c71_codlan  = c73_codlan 
			     left  join conlancamcompl on c72_codlan = c71_codlan
			     inner join conhistdoc 	 on c53_coddoc  = conlancamdoc.c71_coddoc 

					     left outer join conlancamrec on c74_codlan = c73_codlan   and c74_anousu=c73_anousu 
					     left outer join conlancamsup on c79_codlan = c73_codlan
					     left outer join conlancamemp on c75_codlan = c73_codlan
					     left join empempenho on c75_numemp = e60_numemp
					     left outer join conlancamcgm on c76_codlan = c73_codlan
                                             left join cgm on z01_numcgm = c76_numcgm
					     left outer join conlancamdig on c78_codlan = c73_codlan

		 where c73_coddot = $o58_coddot
                   and c73_data between '$data1' and '$data2'
		 order by c73_data, c73_codlan ";

     $result_dados = pg_query($sql_analitico);
	if (pg_num_rows($result_dados) > 0) {
	  $pdf->ln();
  	  $pdf->cell( 20,$alt,"Lancamento",1,0,"C",0);
	  $pdf->cell( 15,$alt,"Data",1,0,"C",0);
	  $pdf->cell( 10,$alt,"Doc.",1,0,"R",0);
	  //$pdf->cell( 10,$alt,"Histórico",1,0,"R",0);
	  $pdf->cell( 60,$alt,"Descrição",1,0,"L",0);
	  $pdf->cell( 20,$alt,"Empenho",1,0,"L",0);
	  $pdf->cell( 123,$alt,"Identificação",1,0,"L",0);
	  $pdf->cell( 30,$alt,"Valor",1,1,"R",0);

	  $dt = "";
        for( $i = 0 ; $i < pg_num_rows($result_dados); $i ++ ){

	  db_fieldsmemory($result_dados,$i);

	  $pdf->cell( 20,$alt,$c73_codlan,0,0,"C",0);
	  if( $dt == "" || $dt != $c73_data ){
		  $pdf->cell( 15,$alt,db_formatar($c73_data,'d'),0,0,"C",0);
	  }else{
		  $pdf->cell( 15,$alt,'',0,0,"C",0);
	  }
	  $dt = $c73_data;
	  $pdf->cell( 10,$alt,$c71_coddoc,0,0,"R",0);
	  //$pdf->cell( 10,$alt,$c53_coddoc,0,0,"R",0);
	  $pdf->cell( 60,$alt,substr($c53_descr,0,30),0,0,"L",0);
	  $pdf->cell( 20,$alt,$c75_numemp,0,0,"L",0);
	  $pdf->cell( 123,$alt,($credito==''?$z01_nome:$credito."-".$credito_descr),0,0,"L",0);
	  $pdf->cell( 30,$alt,db_formatar($c70_valor,'f'),0,1,"R",0);
	  if( $c72_complem != "" ){
             $pdf->cell( 45,$alt,'',0,0,"C",0);
	     $pdf->Multicell(200,$alt,$c72_complem,0,"L",0);

          }

        }

     }







} 


$pdf->output();

?>

