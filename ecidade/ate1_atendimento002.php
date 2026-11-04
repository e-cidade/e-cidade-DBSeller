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
  echo "<script>location.href='ate1_atendimento005.php?db_opcao=2'</script>";
  exit;
}
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_atendimento_classe.php"));
include(modification("classes/db_tecnico_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$clatendimento = new cl_atendimento;
$cltecnico = new cl_tecnico;
$db_opcao = 22;
$db_botao = false;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $clatendimento->alterar($at02_codatend);
  $cltecnico->at03_codatend = $clatendimento->at02_codatend;
  
  $result = $cltecnico->sql_record($cltecnico->sql_query($clatendimento->at02_codatend,null,"at03_id_usuario as usuario_excluir"));
  for ($atend=0; $atend < $cltecnico->numrows; $atend++) {
  	db_fieldsmemory($result, $atend);
  	$cltecnico->at03_codatend = $clatendimento->at02_codatend;
  	$cltecnico->at03_id_usuario = $usuario_excluir;
  	$cltecnico->excluir($clatendimento->at02_codatend, $usuario_excluir);
  }
  
  for($i=0;$i<sizeof($at03_id_usuario);$i++){
    $cltecnico->at03_codatend = $clatendimento->at02_codatend;
    $cltecnico->at03_id_usuario = $at03_id_usuario[$i];
    $cltecnico->incluir($clatendimento->at02_codatend,$at03_id_usuario[$i]);
  }
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clatendimento->sql_record($clatendimento->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $db_botao = true;
   echo "<script>parent.iframe_atenditem.location.href='ate1_atenditem001.php?at05_codatend=$chavepesquisa'</script>";
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
	include(modification("forms/db_frmatendimento.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($clatendimento->erro_status=="0"){
    $clatendimento->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clatendimento->erro_campo!=""){
      echo "<script> document.form1.".$clatendimento->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clatendimento->erro_campo.".focus();</script>";
    };
  }else{
    $clatendimento->erro(true,false);
    echo "<script>parent.iframe_atenditem.location.href='ate1_atenditem001.php?at05_codatend=$at02_codatend'</script>";
    echo "<script>parent.mo_camada('atenditem');</script>";
    echo "<script>parent.iframe_atendimento.location.href='ate1_atendimento002.php?abas=1&chavepesquisa=$at02_codatend'</script>";
  };
}
if($db_opcao == 22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>