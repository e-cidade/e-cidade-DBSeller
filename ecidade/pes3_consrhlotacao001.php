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
include(modification("libs/db_sql.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$clrotulo = new rotulocampo;
$clrotulo->label("r70_estrut");
$clrotulo->label("r70_descr");
$clrotulo->label("DBtxt23");
$clrotulo->label("DBtxt25");
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="if(document.form1.r70_estrut)document.form1.r70_estrut.focus();">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<center>
<BR>
<table border="0" cellspacing="0" cellpadding="0">
  <form name="form1" method="post">
  <tr> 
    <td nowrap title="<?=@$Tr70_estrut?>"><strong>
        <?php
        db_ancora("Estrutural da Lotação:", "js_pesquisarlotacao(true);", 2);
        ?>
    </strong>
    </td>
    <td colspan="2">
        <?php
        db_input('r70_estrut', 10, $Ir70_estrut, true, 'text', 2, " onchange='js_pesquisarlotacao(false);'");
        db_input('r70_descr', 43, $Ir70_descr, true, 'text', 3, '');
        ?>
    <td>
  </tr>
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
  <tr> 
    <td colspan="2" align="center">
      <input type="button" value="Consultar" name="pesquisar" onclick="js_abrejan();">
    </td>
  </tr>
  </form>
</table>
</center>
<?php
 db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_abrejan(){
  qry = "";
  rog = "?";
  if(document.form1.r70_estrut.value!=""){
    qry = rog+"lotacao="+document.form1.r70_estrut.value;
  }
  location.href = 'pes3_consrhlotacao002.php'+qry;
}
function js_pesquisarlotacao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhlota','func_rhlota.php?funcao_js=parent.js_mostralotacao1|r70_estrut|r70_descr','Pesquisa',true);
  }else{
     if(document.form1.r70_estrut.value != ''){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhlota','func_rhlota.php?chave_r70_estrut='+document.form1.r70_estrut.value+'&funcao_js=parent.js_mostralotacao|r70_descr','Pesquisa',false);
     }else{
       document.form1.r70_descr.value = ''; 
     }
  }
}
function js_mostralotacao(descricao,erro){
  document.form1.r70_descr.value = descricao;
  if(erro==true){
    document.form1.r70_estrut.value = '';
    document.form1.r70_estrut.focus();
  }
}

function js_mostralotacao1(estrutural,descricao){
  document.form1.r70_estrut.value = estrutural;
  document.form1.r70_descr.value  = descricao;
  db_iframe_rhlota.hide();
}
</script>