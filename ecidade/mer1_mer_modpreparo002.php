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
include(modification("classes/db_mer_modpreparo_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clmer_modpreparo = new cl_mer_modpreparo;
$db_opcao         = 22;
$db_botao         = false;
if (isset($alterar)) {

  $aux      = $me05_i_cardapio;
  db_inicio_transacao();
  $db_opcao = 2;
  $clmer_modpreparo->alterar($me05_i_codigo);
  db_fim_transacao();

} elseif (isset($me05_i_cardapio)) {

  $db_opcao = 2;
  $result   = $clmer_modpreparo->sql_record($clmer_modpreparo->sql_query("","*",""," me05_i_cardapio=$me05_i_cardapio "));

  if ($clmer_modpreparo->numrows>0){
    db_fieldsmemory($result,0);
  } else{

    $aux      = $me05_i_cardapio;
    $db_opcao = 22;

  }
  $db_botao   = true;
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
<center>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top" bgcolor="#CCCCCC">
    <br><br>
    <fieldset style="width:95%"><legend><b>Alteração do Modo Preparo</b></legend>
	<?php include(modification("forms/db_frmmer_modpreparo.php"));?>
	</fieldset>
	</td>
  </tr>
</table>
</center>
</body>
</html>
<?php
if (isset($alterar)) {

  if ($clmer_modpreparo->erro_status=="0") {

    $clmer_modpreparo->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";

    if ($clmer_modpreparo->erro_campo!="") {

      echo "<script> document.form1.".$clmer_modpreparo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clmer_modpreparo->erro_campo.".focus();</script>";

    }
  } else {

    $clmer_modpreparo->erro(true,false);
    ?><script>
       (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_a3.location.href = 'mer1_mer_modpreparo002.php?me05_i_cardapio=<?=$aux?>'+
                                           '&me01_c_nome=<?=$me01_c_nome?>';
    </script><?php
  }
}
if ($db_opcao==22) {

  ?>
  <script>
    (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_a3.location.href = 'mer1_mer_modpreparo001.php?me05_i_cardapio=<?=$aux?>'+
                                        '&me01_c_nome=<?=$me01_c_nome?>';
  </script>
  <?php
}
?>
<script>
js_tabulacaoforms("form1","me05_f_porcao",true,1,"me05_f_porcao",true);
</script>
