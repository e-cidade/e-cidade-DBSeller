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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis1_fis_vistorias005.php?db_opcao=3&inscr=1'</script>";
  exit;
}
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_vistorias_classe.php"));
include(modification("classes/db_fis_vistorianumpre_classe.php"));
include(modification("classes/db_fis_vistoriarec_classe.php"));
include(modification("classes/db_fis_vistoriaandam_classe.php"));
include(modification("classes/db_fis_vistinscr_classe.php"));
include(modification("classes/db_fis_vistmatric_classe.php"));
include(modification("classes/db_fis_vistsanitario_classe.php"));
include(modification("classes/db_fis_vistcgm_classe.php"));
include(modification("classes/db_fis_vistusuario_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_vistlocal_classe.php"));
include(modification("classes/db_fis_vistexec_classe.php"));
include(modification("classes/db_fis_vistestem_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clvistorias      = new cl_fis_vistorias;
$clvistorianumpre = new cl_fis_vistorianumpre;
$clvistoriarec    = new cl_fis_vistoriarec;
$clvistoriaandam  = new cl_fis_vistoriaandam;
$clvistinscr      = new cl_fis_vistinscr;
$clvistmatric     = new cl_fis_vistmatric;
$clvistcgm        = new cl_fis_vistcgm;
$clvistsanitario  = new cl_fis_vistsanitario;
$clvistusuario    = new cl_fis_vistusuario;
$clvistlocal      = new cl_fis_vistlocal;
$clvistexec       = new cl_fis_vistexec;
$clvistestem      = new cl_fis_vistestem;
$clfandam         = new cl_fis_fandam;
$clfandamusu      = new cl_fis_fandamusu;

$erromsg  = "";
$sqlerro  = false;
$db_botao = false;
$db_opcao = 33;


if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){

  $rsCalculo = $clvistorianumpre->sql_record($clvistorianumpre->sql_query_file($y70_codvist));
  if ($clvistorianumpre->numrows > 0) {
		db_msgbox("Vistoria ja calculada, use a rotina de anulação !");		
    $sqlerro  = true;
	}	
  if (!$sqlerro){
		db_inicio_transacao();
		$db_opcao = 3;
		
		$clvistcgm->excluir($y70_codvist);
		if ( $clvistcgm->erro_status == "0" ){
			$erromsg =  "VISTCGM - ".$clvistcgm->erro_msg;		
			$sqlerro = false;
			db_msgbox($erromsg);
		}

		$clenderecopecas = db_utils::getDao("fis_enderecopecas");
    	$clenderecopecas->end01_codpeca  = $y70_codvist;
   	 	$clenderecopecas->end01_tipopeca = 'V';
    	$rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$y70_codvist} and end01_tipopeca = 'V'");
	    if( pg_num_rows($rsEndPecas) > 0 ){
	      $clenderecopecas->excluir();
	    }

		$clvistmatric->excluir($y70_codvist); 
		if ( $clvistmatric->erro_status == "0" ){
			$erromsg =  "VISTMATRIC - ".$clvistmatric->erro_msg;		
			$sqlerro = false;
			db_msgbox($erromsg);
		}

		$clvistinscr->excluir($y70_codvist); 
		if ( $clvistinscr->erro_status == "0" ){
			$erromsg =  "VISTINSCR - ".$clvistinscr->erro_msg;		
			$sqlerro = false;
			db_msgbox($erromsg);
		}

		$clvistsanitario->excluir($y70_codvist); 
		if ( $clvistsanitario->erro_status == "0" ){
			$erromsg =  "VISTSANITARIO - ".$clvistsanitario->erro_msg;		
			$sqlerro = false;
			db_msgbox($erromsg);
		}

		$clvistlocal->excluir($y70_codvist); 
		if ( $clvistlocal->erro_status == "0" ){
			$erromsg =  "VISTLOCAL - ".$clvistlocal->erro_msg;		
			$sqlerro = false;
			db_msgbox($erromsg);
		}

		$clvistexec->excluir($y70_codvist); 
		if ( $clvistexec->erro_status == "0" ){
			$erromsg =  "VISTEXEC - ".$clvistexec->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
		}

		$clvistestem->excluir($y70_codvist); 
		if ( $clvistestem->erro_status == "0" ){
			$erromsg =  "VISTESTEM - ".$clvistestem->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
		}

		$result = $clfandamusu->sql_record($clfandamusu->sql_query($y70_ultandam));
		if($clfandamusu->numrows > 0){
			$numrows =  $clfandamusu->numrows;
			for($i=0;$i<$numrows;$i++){
				db_fieldsmemory($result,$i);
				$clfandamusu->excluir($y40_codandam,$y40_id_usuario);
				if ( $clfandamusu->erro_status == "0" ){
					$erromsg =  "FANDAMUSU - ".$clfandamusu->erro_msg;		
					$sqlerro = true;
					db_msgbox($erromsg);
					break;
				}
			}
		}
		$result = $clvistoriarec->sql_record($clvistoriarec->sql_query($y70_codvist));
		if($clvistoriarec->numrows > 0){
			$numrows =  $clvistoriarec->numrows;
			for($i=0;$i<$numrows;$i++){
				db_fieldsmemory($result,$i);

				$clvistoriarec->excluir($y76_codvist,$y76_receita);
				if ( $clvistoriarec->erro_status == "0" ){
					$erromsg = "FANDAMUSU - ".$clvistoriarec->erro_msg;		
					$sqlerro = true;
					db_msgbox($erromsg);
					break;
				}

			}
		}
		$clvistusuario->excluir($y70_codvist);
		if ( $clvistusuario->erro_status == "0" ){
			$erromsg =  "VISTUSUARIO - ".$clvistusuario->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
			break;
		}

		$clvistoriaandam->excluir($y70_codvist,$y70_ultandam);
		if ( $clvistoriaandam->erro_status == "0" ){
			$erromsg =  "VISTORIAANDAM - ".$clvistoriaandam->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
			break;
		}

		$clvistorias->excluir($y70_codvist);
		if ( $clvistorias->erro_status == "0" ){
			$erromsg =  "VISTORIAS - ".$clvistorias->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
		}

		$clfandam->excluir($y70_ultandam);
		if ( $clfandam->erro_status == "0" ){
			$erromsg =  "FANDAM - ".$clfandam->erro_msg;		
			$sqlerro = true;
			db_msgbox($erromsg);
		}
		
		db_fim_transacao($sqlerro);

	}

}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $result = $clvistorias->sql_record($clvistorias->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $result = $clvistexec->sql_record($clvistexec->sql_query($chavepesquisa)); 
   if($clvistexec->numrows > 0){
     db_fieldsmemory($result,0);
   }
   $result = $clvistlocal->sql_record($clvistlocal->sql_query($chavepesquisa)); 
   if($clvistlocal->numrows > 0){
     db_fieldsmemory($result,0);
   }
   if($y70_coddepto != db_getsession("DB_coddepto")){
     $db_opcao = 22;
     echo "<script>alert('Departamento da Vistoria não é o mesmo do usuário, verifique!');</script>";
     echo "<script>location.href='fis1_fis_vistorias002.php?abas=1';</script>";
     exit;
     $db_botao = false;
   }
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y10_numero,end01_compl as  y10_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = 'V' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
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
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php 
	include(modification("forms/db_frm_fis_vistorias.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<script>
js_tabulacaoforms("form1","db_opcao",true,1,"db_opcao",true);
</script>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){
  if($clvistorias->erro_status=="0"){
    $clvistorias->erro(true,false);
  }else{
    $clvistorias->erro(true,false);
    echo "<script>parent.document.formaba.fiscais.disabled=true;</script>";
    echo "<script>parent.document.formaba.testem.disabled=true;</script>";
    echo "<script>parent.iframe_vistorias.location.href='fis1_fis_vistorias003.php?abas=1';</script>";
  };
};
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>