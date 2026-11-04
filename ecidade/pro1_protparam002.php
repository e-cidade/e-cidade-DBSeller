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
include(modification("libs/db_utils.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_protparam_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once('model/configuracao/DBDepartamentoRepository.model.php');

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$clprotparam = new cl_protparam;
$db_opcao    = 22;
$db_botao    = false;

$sqlerro = false;

$templateOculta = "none";

if (isset($alterar)) {
    db_inicio_transacao();
    $sSqlParam = $clprotparam->sql_query(
        null,
        "*",
        null,
        "p90_instit=".db_getsession("DB_instit")
    );
    $result    = $clprotparam->sql_record($sSqlParam);

    $clprotparam->p90_depandamentopadrao = $p90_depandamentopadrao;

    if ($clprotparam->numrows == 0) {
        $clprotparam->p90_instit = db_getsession("DB_instit");
        $clprotparam->incluir();

        if ($clprotparam->erro_status == "0") {
            $sqlerro = true;
        }
    } else {
        if (empty($p90_db_documentotemplate)) {
            $clprotparam->p90_db_documentotemplate = $p90_db_documentotemplate;
        } else {
            $clprotparam->p90_db_documentotemplate = null;
        }

        $clprotparam->p90_instit = db_getsession("DB_instit");
        $clprotparam->alterar_instit(db_getsession("DB_instit"));

        if ($clprotparam->erro_status == "0") {
            $sqlerro = true;
        }
    }
    db_fim_transacao($sqlerro);
}

$db_opcao = 2;
$db_botao = true;

$result = $clprotparam->sql_record(
    $clprotparam->sql_query(
        null,
        "*",
        null,
        "p90_instit=".db_getsession("DB_instit")
    )
);
if ($result != false && $clprotparam->numrows > 0) {
    db_fieldsmemory($result, 0);
    $oParam = db_utils::fieldsmemory($result, 0);

    if ( ! empty($oParam->p90_db_documentotemplate)) {
        $templateOculta = "table-row";
    }
}

$Ip90_depandamentopadrao = '';
if ( ! empty($oParam->p90_depandamentopadrao)) {
    $postgresObject = db_query(
        "SELECT descrdepto as descricao_departamento FROM db_depart where coddepto = {$oParam->p90_depandamentopadrao}"
    );

    if (pg_num_rows($postgresObject) > 0) {
        db_fieldsmemory($postgresObject, 0);
    }
}

?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript"
                src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript"
                src="scripts/prototype.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0"
          marginheight="0" onLoad="a=1">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
                <div style="text-align: center;">
                    <?php
                    include(modification("forms/db_frmprotparam.php"));
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <?php
    db_menu(
        db_getsession("DB_id_usuario"),
        db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),
        db_getsession("DB_instit")
    );
    ?>
    </body>
    </html>
<?php
if (isset($alterar)) {
    if ($clprotparam->erro_status == "0") {
        $clprotparam->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clprotparam->erro_campo != "") {
            echo "<script> document.form1.".$clprotparam->erro_campo
                 .".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.".$clprotparam->erro_campo
                 .".focus();</script>";
        }
    } else {
        $clprotparam->erro(true, true);
    }
}
if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
