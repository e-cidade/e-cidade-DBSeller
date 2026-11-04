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
require_once(modification("classes/db_orcsubfuncao_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$clorcsubfuncao = new cl_orcsubfuncao;
$db_botao = false;
$db_opcao = 33;
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"])=="Excluir") {
    db_inicio_transacao();
    $db_opcao = 3;
    $clorcsubfuncao->excluir($o53_subfuncao);
    db_fim_transacao();
} elseif (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $clorcsubfuncao->sql_record($clorcsubfuncao->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0);
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

 <?php
    require_once(modification("forms/db_frmorcsubfuncao.php"));
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
</body>
</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"])=="Excluir") {
    if ($clorcsubfuncao->erro_status=="0") {
        $clorcsubfuncao->erro(true, false);
    } else {
        $clorcsubfuncao->erro(true, true);
    };
};
if ($db_opcao==33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
