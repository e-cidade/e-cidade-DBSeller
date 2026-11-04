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
include(modification("classes/db_distancia_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$cldistancia = new cl_distancia;
$db_opcao = 22;
$db_botao = false;
if(isset($alterar)){
 $db_opcao = 2;
 if($ed223_i_bairroorigem!="" && $ed223_i_bairrodestino!=""){
  $result0 = $cldistancia->sql_record($cldistancia->sql_query("","ed223_i_codigo as confere",""," ed223_i_bairroorigem = $ed223_i_bairrodestino AND ed223_i_bairrodestino = $ed223_i_bairroorigem"));
  if($cldistancia->numrows>0){
   db_fieldsmemory($result0,0);
   $cldistancia->erro_status = "0";
   $cldistancia->erro_msg = "Distância entre estes dois bairros já foi cadastrada.\\n Redirecionando para o registro existente...";
  }else{
   db_inicio_transacao();
   $cldistancia->alterar($ed223_i_codigo);
   db_fim_transacao();
  }
 }else{
  db_inicio_transacao();
  $cldistancia->alterar($ed223_i_codigo);
  db_fim_transacao();
 }
}else if(isset($chavepesquisa)){
 $db_opcao = 2;
 $campos= "distancia.*, bairroorigem.j13_descr as j13_descrorigem,bairrodestino.j13_descr as j13_descrdestino";
 $result = $cldistancia->sql_record($cldistancia->sql_query("",$campos,""," ed223_i_codigo = $chavepesquisa"));
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
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
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
   <br>
   <fieldset style="width:95%"><legend><b>Alteração de Distâncias</b></legend>
    <center>
    <?include(modification("forms/db_frmdistancia.php"));?>
    </center>
   </fieldset>
  </td>
 </tr>
</table>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<?php
if(isset($alterar)){
 if($cldistancia->erro_status=="0"){
  $cldistancia->erro(true,false);
  $db_botao=true;
  echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
  if($cldistancia->erro_campo!=""){
   echo "<script> document.form1.".$cldistancia->erro_campo.".style.backgroundColor='#99A9AE';</script>";
   echo "<script> document.form1.".$cldistancia->erro_campo.".focus();</script>";
  }else{
  db_redireciona("edu1_distancia002.php?chavepesquisa=$confere");
  }
 }else{
  $cldistancia->erro(true,true);
 }
}
if($db_opcao==22){
 echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","ed223_i_bairroorigem",true,1,"ed223_i_bairroorigem",true);
</script>
