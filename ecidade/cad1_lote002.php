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
include(modification("classes/db_lotedist_classe.php"));
include(modification("classes/db_testada_classe.php"));
include(modification("classes/db_testadanumero_classe.php"));
include(modification("classes/db_testpri_classe.php"));
include(modification("classes/db_lote_classe.php"));
include(modification("classes/db_loteam_classe.php"));
include(modification("classes/db_loteloc_classe.php"));
include(modification("classes/db_loteloteam_classe.php"));
include(modification("classes/db_carlote_classe.php"));
include(modification("classes/db_face_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_setor_classe.php"));
include(modification("classes/db_lotesetorfiscal_classe.php"));
include(modification("classes/db_cfiptu_classe.php"));
include(modification("classes/db_tesinter_classe.php"));
include(modification("classes/db_tesinterlote_classe.php"));
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$cllote = new cl_lote;
$clloteloc = new cl_loteloc;
$clloteam = new cl_loteam;
$clloteloteam = new cl_loteloteam;
$clcarlote = new cl_carlote;
$cllotedist = new cl_lotedist;
$cltestada = new cl_testada;
$cltestadanumero = new cl_testadanumero;
$cltestpri = new cl_testpri;
$clface = new cl_face;
$clsetor = new cl_setor;
$cllotesetorfiscal = new cl_lotesetorfiscal;
$clcfiptu = new cl_cfiptu;
$cltesinterlote = new cl_tesinterlote;
$cltesinter = new cl_tesinter;
$db_opcao = 22;
$db_botao = false;
$replote=false;
$sqlerro=false;
$rsResultmostra = ($clcfiptu->sql_record($clcfiptu->sql_query_file(db_getsession('DB_anousu'), '*', "", "")));
if ($clcfiptu->numrows > 0) {
	db_fieldsmemory($rsResultmostra, 0);
	$mostrasetfiscal = $j18_utilizasetfisc;
	$numerotestada = $j18_testadanumero;
}
if(isset($incluir) || isset($alterar)){
	$mesmo=false;
  	$result = @$cllote->sql_record($cllote->sql_query("","j34_idbql as tidbql","","j34_setor= '$j34_setor' and j34_quadra='$j34_quadra' and j34_lote=$j34_lote" ));
  	$numrows=$cllote->numrows;
  	if($result!=false && $numrows!=0){
    	if(isset($alterar)){
      		for($xi=0; $xi<$numrows; $xi++){
        		db_fieldsmemory($result,$xi);
        		if($j34_idbql==$tidbql){
          			$mesmo=true;
        	  		break;
    	    	}
	      	}
    	}
    	if($mesmo==false){
      		$replote=true;
      		if(isset($incluir)){
        		unset($incluir);
        		$repete="incluir";
        		$db_opcao = 1;
      		}else{
        		unset($alterar);
        		$repete="alterar";
        		$db_opcao = 2;
      		}
    	}
	}
}
if(isset($outrolote)&& $outrolote!=""){
 	$$outrolote="ok";
}
$sqlerro=false;
if($replote==true){
//=======================================================================================================================================================================
}else if(isset($alterar)){
	$db_opcao = 2;
  	db_inicio_transacao();
  	$j34_lote = str_pad($j34_lote, 4, "0", STR_PAD_LEFT);
  	$cllote->j34_lote = $j34_lote;
 	$cllote->alterar($j34_idbql);
	if($cllote->erro_status==1){
		$result = $clcarlote->sql_record($clcarlote->sql_query_file($j34_idbql));
  		$xx=$clcarlote->numrows;
//========================================================================================================================================================================
  		for($i=0; $i<$xx; $i++){
    		db_fieldsmemory($result,$i);
    		$clcarlote->j35_idbql = $j35_idbql;
    		$clcarlote->j35_caract = $j35_caract;
    		$clcarlote->excluir($j35_idbql,$j35_caract);
    		if ($clcarlote->erro_status==0){
    		//	db_msgbox("erro numero 7 ");
    			$sqlerro=true;
    			break;
  		    }
  		}
/*======================================*/
// exclusao da tesinter e tesinterlote
/*======================================*/
					if ($sqlerro == false) {
						$rsTesinterlote = $cltesinter->sql_record($cltesinter->sql_query_file(null,'j39_sequencial',null,"j39_idbql = $j34_idbql"));
						$intNumrows = $cltesinter->numrows;
						for($intTes = 0; $intTes < $intNumrows; $intTes++){
							db_fieldsmemory($rsTesinterlote,$intTes);
							$cltesinterlote->excluir($j39_sequencial);
							$cltesinter->excluir($j39_sequencial);
						}
					}
				 /*============ TESTADAS INTERNAS ============== */
  				//=============================================
   		if($j34_loteam!=""){
      		$result = $clloteloteam->sql_record($clloteloteam->sql_query_file("","","loteloteam.j34_loteam as loteam","","loteloteam.j34_idbql=$j34_idbql"));
      		$numrows=$clloteloteam->numrows;
	      	if($numrows>0){
    		    db_fieldsmemory($result,0);
	 			if($j34_loteam!=$loteam){
             		$result = $clloteam->sql_record($clloteam->sql_query($j34_loteam,"j34_loteam"));
             		$numrows=$clloteam->numrows;
             		if($numrows>0){
    	       			$clloteloteam->j34_idbql=$j34_idbql;
               			$clloteloteam->j34_loteam=$loteam;
	       				$clloteloteam->excluir($j34_idbql,$loteam);
    	       			$clloteloteam->j34_idbql=$j34_idbql;
               			$clloteloteam->j34_loteam=$j34_loteam;
               			$clloteloteam->incluir($j34_idbql,$j34_loteam);
               			if ($clloteloteam->erro_status==0){
			  				$sqlerro=true;
			  				$erro_msg=$clloteloteam->erro_msg;
			  				return false;
			  			}
	     			}
          		}
      		}else{
	 			$result = $clloteam->sql_record($clloteam->sql_query($j34_loteam));
         		$numrows= $clloteam->numrows;
         		if($numrows>0){
           			$clloteloteam->j34_idbql=$j34_idbql;
           			$clloteloteam->j34_loteam=$j34_loteam;
           			$clloteloteam->incluir($j34_idbql,$j34_loteam);
           			if ($clloteloteam->erro_status==0){
		  				$sqlerro=true;
		  				$erro_msg=$clloteloteam->erro_msg;
		  				return false;
		  			}
	       		}
      		}
   		}else{
      		$result = $clloteloteam->sql_record($clloteloteam->sql_query_file("","","loteloteam.j34_loteam as loteam","","loteloteam.j34_idbql=$j34_idbql"));
      		$numrows=$clloteloteam->numrows;
      		if($numrows>0){
	   			db_fieldsmemory($result,0);
         		$clloteloteam->j34_idbql=$j34_idbql;
         		$clloteloteam->j34_loteam=$loteam;
         		$clloteloteam->excluir($j34_idbql);
         		if ($clloteloteam->erro_status==0){
	  			//	db_msgbox("erro numero 12 ");
	  				$sqlerro=true;
	  				$erro_msg=$clloteloteam->erro_msg;
	  				return false;
	  			}
      		}
   		}

//============== EXCLUI A TESTADA PRINCIPAL =========================================================================================================================================================

 		if($sqlerro==false){
    		$cltestpri->j49_idbql=$j34_idbql;
    		$cltestpri->excluir($j34_idbql);
    		if ($cltestpri->erro_status==0){
				$sqlerro=true;
	  			$erro_msg=$cltestpri->erro_msg;
			}

//=============== EXCLUI DA TESTADA E DA TESTADANUMERO ========================================================================================================================================================
    		$result = $cltestada->sql_record($cltestada->sql_query($j34_idbql));

    		$xx = $cltestada->numrows;
    		for($i=0; $i<$xx; $i++){
    			db_fieldsmemory($result,$i);
                     $cltestadanumero->sql_record($cltestadanumero->sql_query (null,"*",null,"j15_idbql = $j36_idbql"));
					 $numrowstestadanumero = $cltestadanumero->numrows;
					 if ($numrowstestadanumero > 0){
						  $cltestadanumero->j15_idbql = $j36_idbql;
						  $cltestadanumero->j15_face = $j36_face;
						  $cltestadanumero->excluir("", " j15_idbql = $j36_idbql and j15_face = $j36_face ");
						  if ($cltestadanumero->erro_status==0){
							  $sqlerro=true;
							  $erro_msg=$cltestadanumero->erro_msg;
							  break;
						  }
					 }

				$cltestada->j36_idbql = $j36_idbql;
      			$cltestada->j36_face = $j36_face;
      			$cltestada->excluir($j36_idbql,$j36_face);
      			if ($cltestada->erro_status==0){
      				db_msgbox($cltestada->erro_msg);
	  				$sqlerro=true;
	  				$erro_msg=$cltestada->erro_msg;
	  				break;
	  			}
    		}
		}

//================= INCLUI NA TESTADA E NA TESTADA NUMERO ==================================================================================================================================

$matriztesta = explode("x", $cartestada);
  for ($i = 0; $i < sizeof($matriztesta); $i++) {
	$dados = $matriztesta[$i];
	$matrizdados = explode("||", $dados);

		    $j37_face=$matrizdados[0];
      		$j14_codigo=$matrizdados[1];
      		$j36_testad=$matrizdados[2];
      		$j36_testle=$matrizdados[3];
     	    $j15_numero = $matrizdados[4];
      		$j15_compl  = $matrizdados[5];

      		if($j36_testad!="0" ||  $j36_testle!="0"){
        		$cltestada->j36_idbql  =$j36_idbql;
        		$cltestada->j36_face   =$j37_face;
        		$cltestada->j36_codigo =$j14_codigo;
        		$cltestada->j36_testad =$j36_testad;
        		$cltestada->j36_testle =$j36_testle;
        		$cltestada->incluir($cllote->j34_idbql,$j37_face);
          		if($cltestada->erro_status=="0"){
          			db_msgbox($cltestada->erro_msg);
          			$trans_erro=true;
          			$sqlerro=true;
          			break;
       			}
      		}
      		if(isset($numerotestada) && $numerotestada=='t'){
	      		if((isset($j15_numero) && $j15_numero != "") || (isset($j15_compl) && $j15_compl != "")){
	       			$cltestadanumero->j15_idbql  = $cllote->j34_idbql;
	       			$cltestadanumero->j15_face   = $j37_face;
	       			if(isset($j15_compl) && $j15_compl != ""){
	       				$cltestadanumero->j15_compl  = $j15_compl;
	       			}
                    if(isset($j15_numero) && $j15_numero != ""){
	       				$cltestadanumero->j15_numero = $j15_numero;
                    }
	       			$cltestadanumero->incluir("");
	       			if ($cltestadanumero->erro_status==0){
		  				$sqlerro=true;
		  				$erro_msg=$cltestadanumero->erro_msg;
		  				break;
		  			}
				}
      		}
    	}

//==========================================================================================================================================================================================
        if (isset($numerotestada) && $numerotestada != ""){
	    	$result_testadanumero = $cltestadanumero->sql_record($cltestadanumero->sql_query(null,"*",null," j15_idbql = $j34_idbql "));
	    	$result_testada = $cltestada->sql_record($cltestada->sql_query($j34_idbql));
        }
//============================== INCLUI NA TESTPRI  ============================================================================================================================================================
      	$result=$clface->sql_record($clface->sql_query_file("","j37_codigo","","j37_face=$cartestpri"));
    	$num=$clface->numrows;
    	if($num!=0){
     		db_fieldsmemory($result,0);
		    $cltestpri->j49_face=$cartestpri;
     		$cltestpri->j49_codigo=$j37_codigo;
     		$cltestpri->incluir($cllote->j34_idbql,$cartestpri);
     		if ($cltestpri->erro_status==0){
				$sqlerro=true;
	  			$erro_msg=$cltestpri->erro_msg;
			}
	    }
//============================ INCLUI NA CARLOTE E NA LOTEDIST ==============================================================================================================================================================
	   	$j34_idbql=$cllote->j34_idbql;
    	$clcarlote->j35_idbql=$j34_idbql;
    	$matriz=explode("X",$caracteristica);
    	for($i=1;$i<sizeof($matriz);$i++){
     		$j35_caract = $matriz[$i];
     		if($j35_caract!=""){
       			$clcarlote->j35_caract=$j35_caract;
    			$clcarlote->incluir($j34_idbql,$j35_caract);
    			if($clcarlote->erro_status=="0"){
          			$sqlerro=true;
                }
	     	}
	    if($j54_codigo!=""&&$j54_distan!=""&&$j54_orientacao!=""){
    		$cllotedist->j54_idbql=$j34_idbql;
      		$cllotedist->excluir($j34_idbql);
      		$cllotedist->j54_idbql = $cllote->j34_idbql;
      		$cllotedist->j54_codigo = $j54_codigo;
      		$cllotedist->j54_distan = $j54_distan;
      		$cllotedist->j54_orientacao = $j54_orientacao;
      		$cllotedist->incluir($j34_idbql);
      		if($cllotedist->erro_status=="0"){
          		$sqlerro=true;
            }
    	}
  	}
//========================  ALTERA NA SETORFICAL  ==================================================================================================================================================================
}

	if(isset($mostrasetfiscal) && $mostrasetfiscal=='t'){
	   //  ALTERACAO  NA TABELA LOTESETORFISCAL
	   if(!isset($j91_codigo) || $j91_codigo==""){
	     $cllotesetorfiscal->excluir(""," j91_idbql = $cllote->j34_idbql ");
	     if($cllotesetorfiscal->erro_status=="0"){
	       $sqlerro=true;
	     }
	   }else if(isset($j91_codigo) && $j91_codigo!=""){
	   	 $cllotesetorfiscal->excluir(""," j91_idbql = $cllote->j34_idbql ");
	     $cllotesetorfiscal->j91_idbql = $cllote->j34_idbql;
	     $cllotesetorfiscal->j91_codigo = $j91_codigo;
	     $cllotesetorfiscal->incluir();
	     if($cllotesetorfiscal->erro_status=="0"){
	        $sqlerro=true;
	     }
	   }
	}
//==========================================================================================================================================================================================
db_fim_transacao($sqlerro);

//========================================================================//
 //LOTELOC
  if(isset($j06_setorloc) && $j06_setorloc != ""){
   $clloteloc->j06_idbql = $cllote->j34_idbql;
   $result = $clloteloc->sql_record( $clloteloc->sql_query($cllote->j34_idbql) );
   if($clloteloc->numrows > 0){
    $clloteloc->alterar($cllote->j34_idbql);
   }else{
    $clloteloc->incluir($cllote->j34_idbql);
   }
  }
//=======================================================================//

}else if(isset($chavepesquisa)&&!isset($incluquadra)){
   $result = $cllote->sql_record($cllote->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
   $rsResultsetfis = $cllotesetorfiscal->sql_record($cllotesetorfiscal->sql_query_file("","j91_codigo",""," j91_idbql = $chavepesquisa"));
   if($cllotesetorfiscal->numrows!=0){
	     db_fieldsmemory($rsResultsetfis,0);
   }
   $result = $cllotedist->sql_record($cllotedist->sql_query($chavepesquisa));
   if($cllotedist->numrows!=0){
     db_fieldsmemory($result,0);
   }else{
     $j54_codigo="";
     $j54_distan="";
     $j54_orientacao="";
     $j14_nome="";
   }
   $result = @$cltestpri->sql_record($cltestpri->sql_query_file($chavepesquisa));
   if($result!=false){
     db_fieldsmemory($result,0);
     $cartestpri=$j49_face;
   }

   $result = $clloteloteam->sql_record($clloteloteam->sql_query("","","loteloteam.j34_loteam,loteam.j34_descr","","loteloteam.j34_idbql=$chavepesquisa"));
   $numrows=$clloteloteam->numrows;
   if($result>=1){
     db_fieldsmemory($result,0);
   }
		//=============================================================================================================================
		$testadainter = null;
		$carX = "";
		$sqlTesinter  = " select coalesce(j69_idbql,0) as idbql, ";
		$sqlTesinter .= "				 j39_orientacao as orientacao, ";
		$sqlTesinter .= "				 j39_testad as testad, ";
		$sqlTesinter .= "				 j39_testle as testle ";
		$sqlTesinter .= "		from tesinter ";
		$sqlTesinter .= " 	     left join tesinterlote on j69_tesinter = j39_sequencial ";
		$sqlTesinter .= " where j39_idbql = $j34_idbql ";
		$rsTestadaInter = $cltesinter->sql_record($sqlTesinter);
		for ($i = 0; $i < $cltesinter->numrows; $i++) {
			db_fieldsmemory($rsTestadaInter,$i);
			$testadainter .= $carX.$idbql."-".$orientacao."-".$testad."-".$testle;
			$carX = "X";
		}

   $result = $cltestada->sql_record($cltestada->sql_query_file($chavepesquisa));
   $cartestada = null;
   $cart="";
   for($i=0; $i < $cltestada->numrows; $i++){
     db_fieldsmemory($result,$i);
     $cartestada .= $cart.$j36_face."-".$j36_codigo."-".$j36_testad."-".$j36_testle;
     if(isset($numerotestada) && $numerotestada=='t'){
	     $rsResult = $cltestadanumero->sql_record($cltestadanumero->sql_query_file(null,"*",null," j15_idbql = $chavepesquisa and j15_face = $j36_face "));
	     if ($cltestada->numrows>0){
	     	 db_fieldsmemory($result,$i);
	         $cartestada .= "-".@$j15_numero."-".@$j15_compl;
	     }
     }
     $cart="X	";
   }

   $result = $clcarlote->sql_record($clcarlote->sql_query($chavepesquisa));
   $caracteristica = null;
   $car="X";
   for($i=0; $i<$clcarlote->numrows; $i++){
     db_fieldsmemory($result,$i);
     $caracteristica .= $car.$j35_caract ;
     $car="X";

   }
   $caracteristica .= $car;
   $db_opcao = 2;
   $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_load_lote(){
  <?php
    if(!isset($chavepesquisa)){
       echo "js_pesquisa();";
   }
  ?>
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="js_load_lote();" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
	<?php
	include(modification("forms/db_frmlote.php"));
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
if($replote==true){
 echo "<script>";
 if($repete=="incluir"){
   echo "var confirma=confirm('Este Lote já foi cadastrado! Deseja cadastrar outro?');";
 }else{
   echo "var confirma=confirm('Este Lote já foi cadastrado! Deseja continuar a altera??o?');";

 }
 echo "if(confirma){\n
         document.form1.outrolote.value='$repete'; \n
         document.form1.submit(); \n
       }\n
      ";
  echo "</script>";
       exit;
}


if($cllote->erro_status=="0"){
  $cllote->erro(true,false);
  $db_botao=true;
  echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  if($cllote->erro_campo!=""){
    echo "<script> document.form1.".$cllote->erro_campo.".style.backgroundColor='#99A9AE';</script>";
    echo "<script> document.form1.".$cllote->erro_campo.".focus();</script>";
  };
}else{
  $cllote->erro(true,false);
};
