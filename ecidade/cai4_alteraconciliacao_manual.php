<?php
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_saltes_classe.php"));
include(modification("classes/db_corrente_classe.php"));

$clsaltes   = new cl_saltes;
$clcorrente = new cl_corrente;
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);
$db_opcao = 1;
$db_botao = true;

?>
<html>
	<head>
		<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta http-equiv="Expires" CONTENT="0">
		<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
		<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
		<link href="estilos.css" rel="stylesheet" type="text/css">
	</head>
	<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
		<table width="790" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
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
				<center>
				<form name="form1" enctype="multipart/form-data" method="post" action="">
				</form>
				</center>
			</td>
			</tr>
		</table>
		<?php
		db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
		?>
	</body>
</html>

<script>
//alert('fahdhalf hlkasdhfkl hasdelk');

js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_concilia','func_concilia_manual.php?funcao_js=parent.js_continuar|dl_reduzido','Pesquisa',true);
function js_continuar(conta){
  document.location.href = 'cai4_concbanc001_manual.php?conta='+conta;
	db_iframe_concilia.hide();
}

</script>
