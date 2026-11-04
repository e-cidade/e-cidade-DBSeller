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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_ruas_classe.php"));
require_once(modification("classes/db_ruascep_classe.php"));
require_once(modification("classes/db_cfiptu_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_ruastipo_classe.php"));
require_once(modification("libs/db_libdicionario.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clruas            = new cl_ruas;
$clruascep         = new cl_ruascep;
$clcfiptu          = new cl_cfiptu;
$clruastipo        = new cl_ruastipo;
$oDaoRuasanterior  = new cl_ruas;
$oDaoRuashistorico = new cl_ruashistorico();


if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){

	db_inicio_transacao();
  $db_opcao = 2;

  /**
   * Salva os dados anteriores antes da alteração
   */
  $sSqlRuasAnterior = $oDaoRuasanterior->sql_query_file($j14_codigo);
  $rsRuasAnterior   = $oDaoRuasanterior->sql_record($sSqlRuasAnterior);

  if ($oDaoRuasanterior->numrows > 0) {

  	$oRuaAnterior = db_utils::fieldsMemory($rsRuasAnterior, 0);

  	if (levenshtein($oRuaAnterior->j14_nome, $j14_nome) > 3) {
	  	$oDaoRuashistorico->j136_ruas          = $oRuaAnterior->j14_codigo;
	  	$oDaoRuashistorico->j136_ruastipo      = $oRuaAnterior->j14_tipo;
	  	$oDaoRuashistorico->j136_lei           = $oRuaAnterior->j14_lei;
	  	$oDaoRuashistorico->j136_datalei       = $oRuaAnterior->j14_dtlei;
	  	$oDaoRuashistorico->j136_nomeanterior  = $oRuaAnterior->j14_nome;
	  	$oDaoRuashistorico->j136_dataalteracao = date('Y-m-d', db_getsession('DB_datausu'));
	  	$oDaoRuashistorico->incluir(null);

	  	if ($oDaoRuashistorico->erro_status == '0') {
	  		$sqlerro  = true;
	  		$erro_msg = $oDaoRuashistorico->erro_msg;
	  	}
  	}



  }

  $clruas->alterar($j14_codigo);

  $result = $clruascep->sql_record($clruascep->sql_query($chavepesquisa));

  if($clruascep->numrows > 0){
    $clruascep->j29_cep = !empty($j29_cep) ? $j29_cep : '0';
    $clruascep->j29_lado = $j29_lado;
    $clruascep->j29_inicio= !empty($j29_inicio) ? $j29_inicio : '0';
    $clruascep->j29_final= !empty($j29_final) ? $j29_final : '0';
    $clruascep->j29_codigo = $j14_codigo;
    $clruascep->alterar($j14_codigo);
    if ($clruascep->erro_status==0){
      $erro_msg=$clruascep->erro_msg;
    }
  }else{
    if(isset($j29_cep) and $j29_cep <> ''){
      $clruascep->j29_cep=$j29_cep;
      $clruascep->j29_final=$j29_final;
      $clruascep->incluir($j14_codigo,$j29_inicio);
      if ($clruascep->erro_status==0){
        $sqlerro=true;
        $erro_msg=$clruascep->erro_msg;
      }
    }
   }
     $db_botao = true;
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clruas->sql_record($clruas->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
   $result2 = $clruascep->sql_record($clruascep->sql_query($chavepesquisa));
   if($clruascep->numrows >0 ){
     db_fieldsmemory($result2,0);
   }
   $db_botao = true;
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js, dbautocomplete.widget.js");
    db_app::load("dbmessageBoard.widget.js, prototype.js, prototype.maskedinput.js, dbtextField.widget.js");
    db_app::load("dbcomboBox.widget.js, estilos.css, grid.style.css");
    ?>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
	<?php
	include(modification("forms/db_frmruascep.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
