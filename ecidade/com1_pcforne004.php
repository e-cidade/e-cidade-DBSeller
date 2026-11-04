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
require(modification("libs/db_app.utils.php"));
include(modification("classes/db_pcforne_classe.php"));
include(modification("classes/db_pcfornecon_classe.php"));
include(modification("classes/db_pcfornemov_classe.php"));
include(modification("classes/db_pcfornecert_classe.php"));
require_once(modification("classes/db_tipoempresa_classe.php"));
require_once(modification("classes/db_cgmtipoempresa_classe.php"));

$clpcforne = new cl_pcforne;
$cltipoempresa = new cl_tipoempresa;
$cl_cgmtipoempresa = new cl_cgmtipoempresa;
  /*
$clpcfornecon = new cl_pcfornecon;
$clpcfornemov = new cl_pcfornemov;
$clpcfornecert = new cl_pcfornecert;
  */

db_postmemory($HTTP_POST_VARS);
$db_opcao = 1;
$db_botao = true;

if (isset($incluir)){
    $sqlerro=false;
    db_inicio_transacao();

    $clpcforne->pc60_usuario=db_getsession("DB_id_usuario");
    $clpcforne->pc60_hora=db_hora();
    $clpcforne->incluir($pc60_numcgm);

    if($clpcforne->erro_status==0){
        $sqlerro=true;
    }

    $erro_msg = $clpcforne->erro_msg;
    db_fim_transacao($sqlerro);
    $pc60_numcgm= $clpcforne->pc60_numcgm;
    $db_opcao = 1;
    $db_botao = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">

<?php
db_app::load("scripts.js, strings.js, prototype.js,datagrid.widget.js, widgets/dbautocomplete.widget.js");
db_app::load("widgets/windowAux.widget.js");
?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<br />
    <center>
	   <?php
	     include(modification("forms/db_frmpcforne.php"));
	   ?>
    </center>

<script>
oAutoComplete = new dbAutoComplete($('z01_nome'),'com4_pesquisafornecedor.RPC.php');
oAutoComplete.setTxtFieldId(document.getElementById('pc60_numcgm'));
oAutoComplete.show();
</script>
</body>
</html>
<?php
if(isset($incluir)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clpcforne->erro_campo!=""){
      echo "<script> document.form1.".$clpcforne->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clpcforne->erro_campo.".focus();</script>";
    };
  }else{
   db_msgbox($erro_msg);
   db_redireciona("com1_pcforne005.php?liberaaba=true&chavepesquisa=$pc60_numcgm");
  }
}
?>
