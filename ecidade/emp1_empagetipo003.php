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
require_once(modification("classes/db_empagetipo_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_conplanoconta_classe.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clempagetipo = new cl_empagetipo;
$clconplanoconta = new cl_conplanoconta;
$db_botao = false;
$db_opcao = 33;

if (isset($excluir)) {
    $lErro = false;

    db_inicio_transacao();

    $campos = "
    exists(select 1 from empageformacgm where e28_empagetipo = e83_codtipo)  as empageformacgm,
    exists(select 1 from empagepag where e85_codtipo = e83_codtipo )  as empagepag,
    exists(select 1 from empageparam where e99_tipo = e83_codtipo ) as empageparam
    ";

    $rs = db_query($clempagetipo->sql_query_file($e83_codtipo, $campos));
    $data = db_utils::fieldsMemory($rs, 0);


    if ($data->empageformacgm == 't' or $data->empagepag == 't' or $data->empageparam == 't') {
        $lErro = true;
        $sMsgErro = "Essa conta não pode ser excluída pois já esta sendo utilizada.";
    }

    if (!$lErro) {
        $db_opcao = 3;
        $clempagetipo->excluir($e83_codtipo);
        $sMsgErro = $clempagetipo->erro_msg;
        if ($clempagetipo->erro_status == "0") {
            $lErro = true;
        }
    }
    db_fim_transacao($lErro);
} else if (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $clempagetipo->sql_record($clempagetipo->sql_query($chavepesquisa));

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
    <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
    <table width="680" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td valign="top">
                <?php
                include(modification("forms/db_frmempagetipo.php"));
                ?>
            </td>
        </tr>
    </table>
    <?php
    db_menu();
    ?>
    </body>
    </html>
<?php
if (isset($excluir)) {
    db_msgbox($sMsgErro);
}

if ($db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
