<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
include(modification("classes/db_fis_vistorias_classe.php"));
include(modification("classes/db_fis_tipovistorias_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("classes/db_fis_vistlocal_classe.php"));
include(modification("classes/db_fis_vistexec_classe.php"));
include(modification("classes/db_fis_vistinscr_classe.php"));
include(modification("classes/db_fis_vistsanitario_classe.php"));
include(modification("classes/db_fis_procfiscalvistorias_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis1_fis_vistorias005.php?db_opcao=2&inscr=1'</script>";
  exit;
}

//parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clvistorias = new cl_fis_vistorias;
$cltipovistorias = new cl_fis_tipovistorias;
$clfandamusu = new cl_fis_fandamusu;
$clfandam    = new cl_fis_fandam;
$clvistlocal     = new cl_fis_vistlocal;
$clvistexec      = new cl_fis_vistexec;
$clvistinscr      = new cl_fis_vistinscr;
$clvistsanitario      = new cl_fis_vistsanitario;
$clprocfiscalvistorias = new cl_fis_procfiscalvistorias;
$db_opcao = 22;
$db_botao = false;

if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $sqlerro=false;
  $result = $cltipovistorias->sql_record($cltipovistorias->sql_query("","*",""," y77_codtipo = $y70_tipovist"));
  if($cltipovistorias->numrows > 0){
    db_fieldsmemory($result,0);
  }
  $clfandam->y39_codtipo=$y77_tipoandam;
  $clfandam->alterar($y77_tipoandam);
  $erro=$clfandam->erro_msg;
  if($clfandam->erro_status==0){
    $sqlerro = true;
  }
  $clfandamusu->y40_codandam=$y39_codandam;
  $result = $clfandamusu->sql_record($clfandamusu->sql_query($y39_codandam));
  if($clfandamusu->numrows > 0){
    for($i=0;$i<$clfandamusu->numrows;$i++){
     db_fieldsmemory($result,$i);
     $clfandamusu->y40_codandam=$y39_codandam;
     $clfandamusu->y40_id_usuario=$y40_id_usuario;
     $clfandamusu->alterar($y39_codandam,$y40_id_usuario);
      $erro=$clfandamusu->erro_msg;
      if($clfandamusu->erro_status==0){
        $sqlerro = true;
      }
    }
  }
  $result = $clvistexec->sql_record($clvistexec->sql_query($y70_codvist)); 
  if($clvistexec->numrows > 0){
    $clvistexec->y11_codvist=$y70_codvist;
    $clvistexec->y11_codigo=$y11_codigo;
    $clvistexec->y11_codi=$y11_codi;
    $clvistexec->y11_numero=$y11_numero;
    $clvistexec->y11_compl=$y11_compl;
    $clvistexec->alterar($y70_codvist);
  }else{
    $clvistexec->incluir($y70_codvist);
  }
  if( $sqlerro == false && $y10_codigo != "" ){
    $result = $clvistlocal->sql_record($clvistlocal->sql_query($y70_codvist)); 
    if($clvistlocal->numrows > 0){
      $clvistlocal->y10_codvist=$y70_codvist;
      $clvistlocal->y10_codigo=$y10_codigo;
      $clvistlocal->y10_codi=$y10_codi;
      $clvistlocal->y10_numero=$y10_numero;
      $clvistlocal->y10_compl=$y10_compl;
      $clvistlocal->alterar($y70_codvist);
    }else{
      $clvistlocal->incluir($y70_codvist);
    }
  }else if( $sqlerro == false ){
    $clenderecopecas = db_utils::getDao("fis_enderecopecas");
    $clenderecopecas->end01_codpeca  = $y70_codvist;
    $clenderecopecas->end01_tipopeca = 'V';
    $clenderecopecas->end01_rua      = $j14_nome;
    $clenderecopecas->end01_numero   = $y10_numero;
    $clenderecopecas->end01_compl    = $y10_compl;
    $clenderecopecas->end01_bairro   = $j13_descr;

    $rsEndPecas = db_query("select * from fiscalizacao.fis_enderecopecas where end01_codpeca = {$y70_codvist} and end01_tipopeca = 'V'");
    
    if( pg_num_rows($rsEndPecas) > 0 ){
      $clenderecopecas->alterar();
    }else{
      $clenderecopecas->incluir();
    }

    if( $clenderecopecas->erro_status == "0" ){
      $sqlerro = true;
      $erro    = $clenderecopecas->erro_msg;
    }
  }

// exclui e inclui no procfiscalvistorias
	 $sqlprocfiscalv = "select y109_sequencial from fiscalizacao.fis_procfiscalvistorias where y109_codvist = $y70_codvist ";
	 $resultprocfiscalv = pg_query($sqlprocfiscalv);
	 $linhasprocfiscalv = pg_num_rows($resultprocfiscalv);
	 if($linhasprocfiscalv>0){
	 	 db_fieldsmemory($resultprocfiscalv,0);
	   $clprocfiscalvistorias->y109_sequencial =$y109_sequencial;
		 $clprocfiscalvistorias->excluir($y109_sequencial);
		 if($clprocfiscalvistorias->erro_status==0){
				$erro=$clprocfiscalvistorias->erro_msg;
	      $sqlerro = true;
				
	   }
	 }
	 if($procfiscal!=""){
		  $clprocfiscalvistorias->y109_codvist    = $clvistorias->y70_codvist;
		  $clprocfiscalvistorias->y109_procfiscal = $procfiscal;
		  $clprocfiscalvistorias->incluir(null);
			if($clprocfiscalvistorias->erro_status==0){
				$erro=$clprocfiscalvistorias->erro_msg;
	      $sqlerro = true;
	    }
		}
	
  $clvistorias->alterar($y70_codvist);
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
	
   $result = $clvistorias->sql_record($clvistorias->sql_query_info($chavepesquisa)); 
   
   db_fieldsmemory($result,0);
   $result = $clvistexec->sql_record($clvistexec->sql_query($chavepesquisa)); 
   if($clvistexec->numrows > 0){
     db_fieldsmemory($result,0);
   }
   $result = $clvistlocal->sql_record($clvistlocal->sql_query($chavepesquisa)); 
   if($clvistlocal->numrows > 0){
     db_fieldsmemory($result,0);
   }
   $result = $clvistinscr->sql_record($clvistinscr->sql_query($chavepesquisa)); 
   if($clvistinscr->numrows > 0){
     echo "<script>parent.document.formaba.calculo.disabled = false</script>";
     echo "<script>parent.iframe_calculo.location.href='fis1_fis_calculo001.php?y70_codvist=".$y70_codvist."';</script>";
   }
   $result = $clvistsanitario->sql_record($clvistsanitario->sql_query($chavepesquisa)); 
   if($clvistsanitario->numrows > 0){
     echo "<script>parent.document.formaba.calculo.disabled = false</script>";
     echo "<script>parent.iframe_calculo.location.href='fis1_fis_calculo001.php?y70_codvist=".$y70_codvist."';</script>";
   }
   if($y70_coddepto != db_getsession("DB_coddepto")){
     $db_opcao = 22;
     echo "<script>alert('Departamento da Vistoria não é o mesmo do usuário, verifique!');</script>";
     echo "<script>location.href='fis1_fis_vistorias002.php?abas=1';</script>";
     exit;
     $db_botao = false;
   }
   $db_botao = true;
  
   echo "<script>parent.iframe_fiscais.location.href='fis1_fis_vistusuario001.php?y75_codvist=".$y70_codvist."&y39_codandam=$y70_ultandam';</script>";
   echo "<script>parent.iframe_testem.location.href='fis1_fis_vistestem001.php?y25_codvist=".$y70_codvist."&y39_codandam=$y70_ultandam';</script>";
   echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
   echo "<script>parent.document.formaba.testem.disabled=false;</script>";

 $sqlprocfiscal = "select y109_procfiscal as procfiscal,z01_nome as nome 
										 from fiscalizacao.fis_procfiscalvistorias 
										 inner join fiscalizacao.fis_procfiscalcgm on y109_procfiscal = y101_procfiscal 
										 inner join cgm on y101_numcgm=z01_numcgm 
										 where y109_codvist = $chavepesquisa ";
	 $resultprocfiscal = pg_query($sqlprocfiscal);
	 $linhasprocfiscal = pg_num_rows($resultprocfiscal);
	 if($linhasprocfiscal>0){
	 	db_fieldsmemory($resultprocfiscal,0);
	 }else{
	 	$nome="";
	 }
    $rs2EndPecas = db_query( "select end01_rua as j14_nome, end01_numero as y10_numero,end01_compl as  y10_compl,end01_bairro as j13_descr
                                from fiscalizacao.fis_enderecopecas where end01_codpeca = {$chavepesquisa} and end01_tipopeca = 'V' " );
   if( pg_num_rows( $rs2EndPecas ) > 0 ){
      db_fieldsmemory( $rs2EndPecas,0 );
   }
	 
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
js_setatabulacao();
</script>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($clvistorias->erro_status=="0"){
    $clvistorias->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clvistorias->erro_campo!=""){
      echo "<script> document.form1.".$clvistorias->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clvistorias->erro_campo.".focus();</script>";
    };
  }else{
    if($sqlerro==true){
      db_msgbox($erro);
    }else{
      $clvistorias->erro(true,false);
      echo "<script>parent.iframe_fiscais.location.href='fis1_fis_vistusuario001.php?y75_codvist=".$y70_codvist."&y39_codandam=$y39_codandam';</script>";
      echo "<script>parent.mo_camada('fiscais');</script>";
      echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
      echo "<script>parent.iframe_vistorias.location.href='fis1_fis_vistorias002.php?abas=1&chavepesquisa=".$y70_codvist."';</script>";
      echo "<script>parent.iframe_testem.location.href='fis1_fis_vistestem001.php?y25_codvist=".$y70_codvist."&y39_codandam=$y39_codandam';</script>";
      echo "<script>parent.document.formaba.testem.disabled=false;</script>";
    }
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
  echo "<script>parent.document.formaba.calculo.disabled = true</script>";
}
?>