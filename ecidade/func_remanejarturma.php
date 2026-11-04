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

//MODULO: educação
include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_turmalog_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clturmalog = new cl_turmalog;
if (isset($turma)) {
  $result  = $clturmalog->sql_record(
                                   $clturmalog->sql_query("",
                                                       "turma.ed57_c_descr,turma.ed57_i_codigo",
                                                       "turma.ed57_c_descr",
                                                       " ed287_i_turma in ($turma)"
                                                       )
                                 );

}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
.cabec{
 text-align: left;
 font-size: 10;
 font-weight: bold;
 color: #DEB887;
 background-color:#444444;
 border:1px solid #CCCCCC;
}
.aluno{
 font-size: 10;
}
</style>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form2" method="post" action="">
<table border="0" cellspacing="2px" width="100%" height="100%" cellpadding="1px" bgcolor="#cccccc">
<tr>
 <td align="center" valign="top">
  <table border='1px' width="100%" bgcolor="#cccccc" style="" cellspacing="0px">
   <tr class='cabec'>
    <td align='center' colspan='8'>
     Turmas Remanejadas
    </td>
   </tr>
   <tr><td height='2' colspan='8' bgcolor='#444444'></td></tr>
   <tr bgcolor="#DBDBDB" align="center">
    <td><b>Código</b></td>
    <td><b>Turma</b></td>
    <td colspan="2"><b>Opções</b></td>
   </tr>
   <?php
   for ($c = 0;$c < $clturmalog->numrows; $c++) {

     db_fieldsmemory($result,$c);
    ?>
    <tr>
     <td class="aluno" width="5%" align="center"><?=$ed57_i_codigo?></td>
     <td class="aluno"><?=$ed57_c_descr?></td>
     <td class="aluno"  align="center"><a href="javascript:js_turma002(<?=$ed57_i_codigo?>)">Alterar</a></td>
     <td class="aluno"  align="center"><a href="javascript:js_turma003(<?=$ed57_i_codigo?>)">Excluir</a></td>
    </tr>
    <?php
   }
   ?>
   <tr>
    <td align="center" colspan="4">
      <input type="button" name="novo" value="Nova Turma" Onclick = "js_novo();">
      <input type="button" name="fechar" value="Fechar" Onclick="js_fechar();">
      <input type="submit" name="teste" value="Teste" style = visibility:hidden;>
    </td>
   </tr>
  </table>
 </td>
</tr>
</table>
</body>
</html>
<script>

function js_turma002(ed57_i_codigo) {

	var iTop  = ( document.body.clientHeight - 600) / 2;
	var iLeft = ( document.body.clientWidth - 900 ) / 2;

  <?php if (isset($ed57_i_codigo)) {?>
      js_OpenJanelaIframe("",
                          "db_iframe_remanejarturma",
                          "edu1_remanejarturma002.php?chavepesquisa="+ed57_i_codigo+"&abre=true",
                          "Alunos Matriculados na Turma",true,iTop, iLeft, 900,400);

    <?php } ?>
}

function js_turma003(ed57_i_codigo) {

	var iTop  = ( document.body.clientHeight - 600) / 2;
	var iLeft = ( document.body.clientWidth - 900 ) / 2;

   <?php if (isset($ed57_i_codigo)) {?>
	   js_OpenJanelaIframe("",
	                       "db_iframe_remanejarturma",
	                       "edu1_remanejarturma003.php?chavepesquisa="+ed57_i_codigo+"&abre=true",
	                       "Alunos Matriculados na Turma",true, iTop, iLeft, 900, 400);

   <?php } ?>
}

function js_novo() {
  parent.location="edu1_turmaabas001.php";
}

function js_fechar(){
  parent.location="edu1_remanejarturma001.php";
}
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
