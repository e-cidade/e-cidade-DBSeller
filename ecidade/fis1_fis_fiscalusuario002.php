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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("classes/db_fis_fiscalusuario_classe.php");
include modification("classes/db_fis_fandamusu_classe.php");
include modification("dbforms/db_funcoes.php");
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
// Ticket 108335
$getIntimacao = ((isset($intimacao) && $intimacao == 1) ? '&intimacao=1' : '');
$getLabel     = ((isset($intimacao) && $intimacao == 1) ? 'Intimação' : 'Notificação');
// -------------
db_postmemory($HTTP_POST_VARS);
$clfiscalusuario = new cl_fis_fiscalusuario;
$clfandamusu     = new cl_fis_fandamusu;
$db_opcao = 22;
$db_botao = false;
global $y39_codandam;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $clfandamusu->excluir($y39_codandam,$y38_id_usuario_old);
  $clfandamusu->y40_obs="0";
  $clfandamusu->incluir($y39_codandam,$y38_id_usuario);
  $clfiscalusuario->excluir($y38_codnoti,$y38_id_usuario_old);
  $clfiscalusuario->incluir($y38_codnoti,$y38_id_usuario);
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clfiscalusuario->sql_record($clfiscalusuario->sql_query($chavepesquisa,$chavepesquisa1)); 
   db_fieldsmemory($result,0);
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
	include modification("forms/db_frm_fis_fiscalusuario.php");
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
  if($clfiscalusuario->erro_status=="0"){
    $clfiscalusuario->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clfiscalusuario->erro_campo!=""){
      echo "<script> document.form1.".$clfiscalusuario->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfiscalusuario->erro_campo.".focus();</script>";
    };
  }else{
    $clfiscalusuario->erro(true,false);
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_fiscalusuario001.php?y38_codnoti=$y38_codnoti&abas=1&y39_codandam=$y39_codandam".$getIntimacao."'</script>";
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>