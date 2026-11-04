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
include(modification("classes/db_alunopassagem_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clalunopassagem = new cl_alunopassagem;
$db_botao = false;
$db_opcao = 33;
if(isset($excluir)){
 db_inicio_transacao();
 $db_opcao = 3;
 $clalunopassagem->excluir($ed215_i_codigo);
 db_fim_transacao();
}else if(isset($chavepesquisa)){
 $db_opcao = 3;
 $campos= "alunopassagem.*,
           ed47_v_nome,
           ed217_c_origem,
           bairro.j13_descr as bairroaluno,
           case when ed56_i_codigo is null then ed82_c_nome else ed18_c_nome end as ed18_c_nome,
           case when ed56_i_codigo is null then ed82_i_codigo else ed18_i_codigo end as ed18_i_codigo,
           case when ed56_i_codigo is null then ed82_c_bairro else bairro.j13_descr end as bairroescola
          ";
 $result = $clalunopassagem->sql_record($clalunopassagem->sql_query("",$campos,"ed215_i_ano","ed215_i_codigo= $chavepesquisa"));
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
   <center>
   <fieldset style="width:95%"><legend><b>Exclusão de alunos que utilizam passagens</b></legend>
    <?include(modification("forms/db_frmalunopassagem.php"));?>
   </fieldset>
   </center>
  </td>
 </tr>
</table>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<?php
if(isset($excluir)){
 if($clalunopassagem->erro_status=="0"){
  $clalunopassagem->erro(true,false);
 }else{
  $clalunopassagem->erro(true,true);
 }
}
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","excluir",true,1,"excluir",true);
</script>
