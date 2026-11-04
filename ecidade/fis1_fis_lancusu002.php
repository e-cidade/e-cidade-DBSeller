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
require_once(modification("classes/db_fis_lancusu_classe.php"));
require_once(modification("classes/db_fis_fandamusu_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$cllancusu   = new cl_fis_lancusu;
$clfandamusu = new cl_fis_fandamusu;
$db_opcao    = 22;
$db_botao    = false;
global $nl18_codlanc;
global $y39_codandam;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  //$clfandamusu->excluir($y39_codandam,$nl14_id_usuario_old);
  //$clfandamusu->y40_obs="0";
  //$clfandamusu->incluir($y39_codandam,$nl14_id_usuario);
   //$clfandamusu->y39_codandam = $y39_codandam;
   //$clfandamusu->nl14_id_usuario = $nl14_id_usuario;
   //$clfandamusu->alterar();
   $cllancusu->excluir($nl14_codlanc,$nl14_id_usuario_old);
   $cllancusu->incluir($nl14_codlanc,$nl14_id_usuario);
   //$cllancusu->nl14_obs = $nl14_obs;
   //$cllancusu->alterar($chavepesquisa, $nl14_id_usuario);
  db_fim_transacao();
  
}else if(isset($chavepesquisa)){

   $db_opcao = 2;
   $result   = $cllancusu->sql_record($cllancusu->sql_query($chavepesquisa,$chavepesquisa1));
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
<body class="abas">
  <div class="container">
  	<?php
  	  include(modification("forms/db_frm_fis_lancusu.php"));
  	?>
  </div>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($cllancusu->erro_status=="0"){
    $cllancusu->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cllancusu->erro_campo!=""){
      echo "<script> document.form1.".$cllancusu->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cllancusu->erro_campo.".focus();</script>";
    };
  }else{
    $cllancusu->erro(true,false);
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl14_codlanc=$nl14_codlanc&y39_codandam=$y39_codandam';</script>";
  };
};
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
