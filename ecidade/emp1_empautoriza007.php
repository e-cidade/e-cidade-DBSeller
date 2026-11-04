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
include(modification("classes/db_empautoriza_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clempautoriza = new cl_empautoriza;
if (empty($db_opcao)) {
    $db_opcao = 1;
    $db_botao = true;
} else {
    $db_opcao = 33;
    $db_botao = false;
}
if (isset($confirmar)) {
    db_inicio_transacao();
    $clempautoriza->e54_autori = $e54_autori;
    $clempautoriza->alterar($e54_autori);
    db_fim_transacao();

} elseif (isset($chavepesquisa) || isset($e54_autori)) {
    if (isset($e54_autori)) {
        $chavepesquisa = $e54_autori;
    }
    $result = $clempautoriza->sql_record($clempautoriza->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0);
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<div class="container">
    <fieldset>
        <legend>Prazos</legend>
        <?php
        include(modification("forms/db_frmempautoriza_prazos.php"));
        ?>
    </fieldset>
</div>
</body>
</html>
<?php
if (isset($confirmar)) {
    if ($clempautoriza->erro_status == "0") {
        $clempautoriza->erro(true, false);
        $db_botao = true;
        if ($clempautoriza->erro_campo != "") {
            echo "<script> document.form1." . $clempautoriza->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clempautoriza->erro_campo . ".focus();</script>";
        }
    } else {
        $clempautoriza->erro(true, false);
    }
}
?>
