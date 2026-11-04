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
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_cidadao_classe.php"));
include(modification("classes/db_cidadaoemail_classe.php"));
include(modification("classes/db_cidadaotelefone_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("libs/db_app.utils.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
//$clcidadao = new cl_cidadao;
$db_opcao = 22;
$db_botao = false;

/* Procedimento todo realizado via ajax
*/
if (isset($chavepesquisa)) {

	$db_opcao = 2;
	$db_botao = true;
}

$oGet                    = db_utils::postMemory($_GET);
$lHabilitaMenu           = true;
$sStyleProcessado        = "";
$sStylelTelaSocial       = "display:none;";
$iLocalatendimentosocial = '0';

if (isset($oGet->lOrigemLeitor) && $oGet->lOrigemLeitor) {

  $lHabilitaMenu    = false;
  $sStyleProcessado = "display:none;";
}

if (isset($oGet->lTelaSocial) && $oGet->lTelaSocial) {

  $sStylelTelaSocial = "display:block;";
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
	db_app::load('prototype.js, strings.js, scripts.js, DBAbas.widget.js, DBAbasItem.widget.js, datagrid.widget.js,
                widgets/windowAux.widget.js, widgets/dbtextField.widget.js, dbViewAvaliacoes.classe.js,
                dbmessageBoard.widget.js,dbautocomplete.widget.js,dbcomboBox.widget.js');
	db_app::load('estilos.css, grid.style.css, DBtab.style.css');
?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="" >
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr align="center">
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
	<?php
	include(modification("forms/db_frmcidadao.php"));
	?>
    </center>
	</td>
  </tr>
</table>
<?php
  if ($lHabilitaMenu) {
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  }
?>
</body>
</html>
<?php
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
if($db_opcao==2){
  echo "<script>js_pesquisaCidadao($chavepesquisa);</script>";
}
?>