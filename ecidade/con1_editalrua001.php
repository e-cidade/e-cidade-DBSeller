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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

include(modification("classes/db_editalrua_classe.php"));
include(modification("classes/db_edital_classe.php"));
include(modification("classes/db_editalruaproj_classe.php"));
include(modification("classes/db_editalproj_classe.php"));
include(modification("classes/db_editalserv_classe.php"));
include(modification("classes/db_projmelhoriasmatric_classe.php"));
include(modification("classes/db_contlot_classe.php"));
include(modification("classes/db_contlotv_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$cleditalrua           = new cl_editalrua;
$cledital              = new cl_edital;
$cleditalruaproj       = new cl_editalruaproj;
$clcontlot             = new cl_contlot;
$clcontlotv            = new cl_contlotv;
$cleditalproj          = new cl_editalproj;
$clprojmelhoriasmatric = new cl_projmelhoriasmatric;
$cleditalserv          = new cl_editalserv;
$db_opcao = 1;
$db_botao = true;

$aDadosInc = array();
$nValorCalculoReal = 0;


if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){

  db_inicio_transacao();

  $oPost = db_utils::postMemory($_POST);


  $sqlerro=false;

  $cleditalrua->d02_idlog = db_getsession("DB_id_usuario");
  $cleditalrua->d02_data  = date("Y-m-d",db_getsession("DB_datausu"));
  $cleditalrua->d02_valorizacao  = $d02_valorizacao;
  $cleditalrua->incluir($d02_contri);
  if ($cleditalrua->erro_status=="0"){
    //$cleditalrua->erro(true,false);
    $sqlerro=true;
    $erro_msg = $cleditalrua->erro_msg;
  }


  if ($sqlerro == false) {
    $d02_contri = $cleditalrua->d02_contri;
    $matriz=explode("XX",$dados);
    $tam=sizeof($matriz);
    //
    // For inserindo os servicos
	//

	$aDados = explode("-", $oPost->dados);
	$d04_forma = $aDados[6];


    for($i=0; $i<$tam; $i++){

      if($matriz[$i]!=""){

	$dad =explode("-",$matriz[$i]);

	$cleditalserv->d04_contri  = $d02_contri;
	$cleditalserv->d04_tipos   = $dad[0];
	$cleditalserv->d04_quant   = $dad[1];
	$cleditalserv->d04_vlrcal  = $dad[2];
	$cleditalserv->d04_vlrval  = $dad[4];
	$cleditalserv->d04_mult    = $dad[5];
	$cleditalserv->d04_forma   = $dad[6];
	$cleditalserv->d04_vlrobra = $dad[7];
	$cleditalserv->incluir($d02_contri,$dad[0]);

	if($cleditalserv->erro_status=="0"){
	  //$cleditalserv->erro(true,false);
	  $sqlerro  = true;
	  $erro_msg = $cleditalserv->erro_msg;

	  db_msgbox($cleditalserv->erro_msg);
	}

      }

	}



	//echo "<br><br><br>FORMA: $d04_forma ";


    if(isset($d40_codigo)){





		$sSqlEdital = $cleditalproj->sql_query($numedital,"","d40_codigo,
		                                                      d01_perc,
															  d40_trecho,
															  d40_codlog,
															  j14_nome,
															  d40_profun",
															  "",
															  "d40_codigo not in (select d11_codproj from editalruaproj)
															  and d10_codedi = {$oPost->d02_codedi} ");
		$rsEdital   = $cleditalproj->sql_record( $sSqlEdital );

		//db_criatabela($rsEdital);

		$sD40_codigo = "";
		for (  $iLista = 0; $iLista < $cleditalproj->numrows;  $iLista++ )   {

		   $oDados = db_utils::fieldsMemory($rsEdital  , $iLista);
           $a40Codigo[] = $oDados->d40_codigo;
		}
		$sD40_codigo = implode(", " , $a40Codigo);


		$d40_codigo = $sD40_codigo;

		//echo $d40_codigo;

		//die();



		if (  $cleditalproj->numrows > 0 ) {



		//	for (  $iLista = 0; $iLista < $cleditalproj->numrows;  $iLista++ )   {


				//$oDados = db_utils::fieldsMemory($rsEdital  , $iLista);
				//db_fieldsmemory($rsEdital, $iLista);

				//$d40_codigo = $oDados->d40_codigo;





	  $sSqlprojmelhoriasmatric = $clprojmelhoriasmatric->sql_query_file($d40_codigo,"","sum(d41_testada + d41_eixo) as total_testada");
	  $sSqlprojmelhoriasmatric = $clprojmelhoriasmatric->sql_query_file("", "", "sum(d41_testada + d41_eixo) as total_testada", null, " d41_codigo in ($d40_codigo) "  );


	  echo $sSqlprojmelhoriasmatric;
	  //sql_query_file ( $d41_codigo=null,$d41_matric=null,$campos="*",$ordem=null,$dbwhere=""){

	  //echo $sSqlprojmelhoriasmatric; die();


	  $result_total=$clprojmelhoriasmatric->sql_record($sSqlprojmelhoriasmatric);

//db_criatabela($result_total);


      if ($clprojmelhoriasmatric->numrows > 0) {
	    db_fieldsmemory($result_total, 0);
      } else {
	    $total_testada = 0;
      }


      $sSQlMatricula = $clprojmelhoriasmatric->sql_query(null,null," j01_idbql,
                                                               sum(d41_testada) as d41_testada,
                                                               sum(d41_eixo) as d41_eixo,
                                                               d41_pgtopref ", "",
                                                               " d41_codigo in( $d40_codigo ) group by j01_idbql, d41_pgtopref " );





      $resulte = $clprojmelhoriasmatric->sql_record( $sSQlMatricula );
      $numer   = $clprojmelhoriasmatric->numrows;

      if ( $numer > 0 ) {

	$quantpgtopref=0;
	//
	// For inserindo as matriculas ()
	//

	// db_criatabela($resulte);exit;

	for( $ii = 0; $ii < $numer; $ii++ ) {
	  db_fieldsmemory($resulte,$ii);

	  //
	  // Se nao for pagamento na prefeitura ignora e passa para proxima
	  //

	  if ( $d41_pgtopref != "t" ) {
	    continue;
	  }

	  $quantpgtopref++;
	  $clcontlot->d05_contri = $d02_contri;
	  $clcontlot->d05_idbql  = $j01_idbql;
	  $clcontlot->d05_testad = $d41_testada+$d41_eixo;
	  $clcontlot->incluir($d02_contri,$j01_idbql);

	  if($clcontlot->erro_status=='0'){
	    //$clcontlot->erro(true,false);
	    $sqlerro = true;
	    $erro_msg = $clcontlot->erro_msg;
	    break;
	  }
	  $redital = $cleditalserv->sql_record($cleditalserv->sql_query_file($d02_contri));
	  $numrows = $cleditalserv->numrows;
	  if($numrows==0){
	    db_msgbox("Não foi cadastrado editaserv");
	    $sqlerro=true;
	    $erro_msg = $cleditalserv->erro_msg;
	    break;
	  }
	  $result06 = $cleditalrua->sql_record($cleditalrua->sql_query_file($d02_contri,"d02_profun"));
	  if($clcontlot->erro_status=='0'){
	    $sqlerro  = true;
	    $erro_msg = $clcontlot->erro_msg;
	    break;
	  }
	  db_fieldsmemory($result06,0);

	  //rotina que pega o desconto do edital
	  $result09 = $cledital->sql_record($cledital->sql_query_file($d02_codedi,"d01_perc"));
	  if($cledital->erro_status=='0'){
	    $sqlerro = true;
	    $erro_msg = $cledital->erro_msg;
	    break;
	  }
	  db_fieldsmemory($result09,0);

	  for($j=0; $j<$numrows; $j++){


		db_fieldsmemory($redital,$j);




	    if ($d04_forma == 1) {





	      $valor_normal = $d04_vlrcal - (($d04_vlrcal * $d01_perc) / 100) ;
		  $valor_contri = ($valor_normal * ($d41_testada + $d41_eixo) * $d02_profun);





	    } elseif ($d04_forma == 2) {

	      $valor_normal = $d04_vlrval - (($d04_vlrval*$d01_perc)/100) ;
	      $valor_contri = ($valor_normal * ($d41_testada+$d41_eixo)*$d02_profun)*$d04_mult;

	    } elseif ($d04_forma == 3) {


	      $ano = db_getsession('DB_anousu');

	      $sql  = " select sum( case  ";
	      $sql .= "               when j22_valor is null  ";
	      $sql .= "                 then 0  ";
	      $sql .= "               else j22_valor  ";
	      $sql .= "             end + j23_vlrter ) as j23_vlrter ";
	      $sql .= "   from ( select ( select sum(j22_valor) ";
	      $sql .= "                     from iptucale  ";
	      $sql .= "                    where j22_anousu = $ano  ";
	      $sql .= "                      and j22_matric in ( select j01_matric from iptubase where j01_idbql = $j01_idbql ) ) as j22_valor,  ";
	      $sql .= "                 ( select sum(j23_vlrter)  ";
	      $sql .= "                     from iptucalc  ";
	      $sql .= "                    where j23_anousu = $ano ";
	      $sql .= "                      and j23_matric in ( select j01_matric from iptubase where j01_idbql = $j01_idbql ) ) as j23_vlrter ";
	      $sql .= "        ) as j23_vlrter ";
	      //die( $sql );
	      $rsValorVenalTer = db_query($sql) or die($sql);
	      db_fieldsmemory($rsValorVenalTer,0);

	      (float)$nValorVenal    = $j23_vlrter;

	      // Área Real Total
	      (float)$nAreaRealTotal = $d04_quant * $d02_profun;
	      //die("<br>quant: $d04_quant - prof: $d02_profun - nAreaRealTotal: $nAreaRealTotal");

	      // area total
	      (float)$nAreaTotal     = ( $total_testada * $d02_profun );
	      //die("<br>total_testada: $total_testada - nAreaTotal: $nAreaTotal");

	      // valor do m2
	      (float)$nValorM2       = ( $d04_vlrobra / $nAreaRealTotal );
	      //die("<br>nValorM2: $nValorM2 - d04_vlrobra: $d04_vlrobra - nAreaRealTotal: $nAreaRealTotal");
//	      die("d04_vlrobra: $d04_vlrobra");

	      // valor valorizacao
	      (float)$nValorizacao   = ( $nValorVenal * $d02_valorizacao / 100 );
//	      die("d02_valorizacao: $d02_valorizacao");
//              die("nValorizacao: $nValorizacao");

	      // area parcial
	      (float)$nAreaParcial   = ( ($d41_testada + $d41_eixo ) * $d02_profun );
	      //die("<br>Testada: $d41_testada - eixo: $d41_eixo - nAreaParcial: $nAreaParcial");

	      // area corrigida
	      (float)$nAreaCorrigida = ( $nAreaParcial / $nAreaTotal * $nAreaRealTotal );
	      //die("<br>nAreaCorrigida: $nAreaCorrigida");

	      // valor venal
	      (float)$nValorFinal    = ( $nValorVenal + $nValorizacao );
//	      die("nValorFinal: $nValorFinal");

	      // Custo
	      (float)$nCusto         = ( ( $nAreaCorrigida * $nValorM2 ) / 100 ) * (100 - $d01_perc);
//	      die("d01_perc: $d01_perc - nCusto: $nCusto");

	      //
	      // Se Custo maior que a valorizacao entao custo fica a valorizacao
		  //

          $nValorCalculoReal = $nCusto;

	      if ( $nCusto > $nValorizacao ) {

		       (float)$nCusto = $nValorizacao;
	      }

	      $valor_contri         = $nCusto;

	    }

	    $valor_contri = number_format($valor_contri,"2",".","");

	    $clcontlotv->d06_contri = $d02_contri;
	    $clcontlotv->d06_idbql  = $j01_idbql;
	    $clcontlotv->d06_tipos  = $d04_tipos;
	    $clcontlotv->d06_fracao = $d41_testada + $d41_eixo;
		$clcontlotv->d06_valor  = $valor_contri;




		$oDadosIncluir = new stdClass();
		$oDadosIncluir->d06_contri         = $clcontlotv->d06_contri;
        $oDadosIncluir->d06_idbql          = $clcontlotv->d06_idbql;
        $oDadosIncluir->d06_tipos          = $clcontlotv->d06_tipos;
        $oDadosIncluir->d06_fracao         = $clcontlotv->d06_fracao;
		$oDadosIncluir->d06_valor          = $clcontlotv->d06_valor;
		$oDadosIncluir->nValorRealCalculo  = $nValorCalculoReal;
        $oDadosIncluir->valorizacao        = $nValorizacao;
		$aDadosInc[] = $oDadosIncluir;

		//echo "<br>IDBQL: $j01_idbql  d06_valor: $valor_contri fracao: $clcontlotv->d06_fracao ";


       // echo "<pre>";
       // print_r( $clcontlotv);
       // echo "</pre>";


	    $clcontlotv->incluir($d02_contri,$j01_idbql,$d04_tipos);
	    if($clcontlotv->erro_status=='0'){
	      //$clcontlotv->erro(true,false);
	      $sqlerro = true;
	      $erro_msg = $clcontlotv->erro_msg;
	      break;
	    }

	  }

	}

	if ($quantpgtopref == 0){

	  db_msgbox("Nenhum registro da lista foi configurado para pagamento na prefeitura!");
	  $sqlerro  = true;
	  $erro_msg = "Nenhum registro da lista foi configurado para pagamento na prefeitura!";

	}

      }

      if ($sqlerro == false) {

		   $results=$cleditalruaproj->sql_record($cleditalruaproj->sql_query($d02_contri,"","d11_codproj"));

		   /*
	       if($cleditalruaproj->numrows>0){
	         $cleditalruaproj->d11_contri=$d02_contri;
	         $cleditalruaproj->excluir($d02_contri);
	         if($cleditalruaproj->erro_status=='0'){
	           //$cleditalruaproj->erro(true,false);
	           $sqlerro = true;
	           $erro_msg = $cleditalruaproj->erro_msg;
	         }
	       }
	   */

	           if(isset($d40_codigo)){




		         foreach($a40Codigo as $d40_codigo) {


					 $cleditalruaproj->d11_contri = $d02_contri;
					 $cleditalruaproj->d11_codproj= $d40_codigo;
					 $cleditalruaproj->incluir($d02_contri,$d40_codigo);
					 if($cleditalruaproj->erro_status=='0'){
						 $sqlerro = true;
						 $erro_msg = $cleditalruaproj->erro_msg;
						}

				}

	           }
	  }









 //	}
 }











	}

  }

/*
echo "<pre>";
print_r($aDadosInc);
echo "</pre>";
*/

   // echo "<br>select * from contlotv where d06_contri = $d02_contri;<br>";
  // db_criatabela(  db_query( "select sum(d06_valor) from contlotv where d06_contri = $d02_contri;" ) );


   // db_criatabela(  db_query( "select * from contlotv where d06_contri = $d02_contri;" ) );


  //	$sqlerro = true;

 db_fim_transacao($sqlerro);

  //  die("final -- apos fim transacao <br>  -- ".@$erro_msg);

}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
<tr>
<td width="360" height="18">&nbsp;</td>
<td width="263">&nbsp;</td>
<td width="25">&nbsp;</td>
<td width="140">&nbsp;</td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td height="430" align="left" valign="top" bgcolor="#CCCCCC">
<center>
<?php
include(modification("forms/db_frmeditalruaalt.php"));
?>
</center>
</td>
</tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($HTTP_POST_VARS["db_opcao"])){
  if($sqlerro == true){
    $cleditalrua->erro_msg = "Ocorreu algum problema durante processamento da inclusao! Contate suporte! Mensagem: $erro_msg";
    $cleditalrua->erro(true,false);
    db_redireciona("con1_editalrua001.php");
  }else{
    $cleditalrua->erro(true,true);
    $result=$cleditalproj->sql_record($cleditalproj->sql_query($d02_codedi,"","d40_codigo,d40_trecho,d40_codlog,j14_nome","","d40_codigo not in (select d11_codproj from editalruaproj)  and d10_codedi=$d02_codedi"));
    if($cleditalproj->numrows>0){
      db_redireciona("con1_editalrua001.php?numedital=$d02_codedi&d02_codedi=$d02_codedi");
    }else{
      db_redireciona("con1_editalrua001.php");
    }
  };
}
?>
