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
require_once(modification("libs/db_libdicionario.php"));
require_once(modification("classes/db_numpref_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_libdicionario.php"));


parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clnumpref = new cl_numpref;
$db_opcao = 22;
$db_botao = false;
$instit = db_getsession("DB_instit");


if (isset($alterar)) {
    db_inicio_transacao();
    $db_opcao = 2;
    $clnumpref->k03_instit = $instit;
    $clnumpref->k03_separajurmulparc = 0;
    $clnumpref->alterar($k03_anousu, $instit);
    db_fim_transacao();
} elseif (isset($chavepesquisa)) {
    $db_opcao = 2;
    $campos = "*,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidao_cgm) as templatecertidao_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidao_matric) as templatecertidao_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidao_inscr) as templatecertidao_inscr,

            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaoweb_cgm) as templatecertidaoweb_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaoweb_matric) as templatecertidaoweb_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaoweb_inscr) as templatecertidaoweb_inscr,

            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositiva_cgm) as templatecertidaopositiva_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositiva_matric) as templatecertidaopositiva_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositiva_inscr) as templatecertidaopositiva_inscr,

            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositivaweb_cgm) as templatecertidaopositivaweb_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositivaweb_matric) as templatecertidaopositivaweb_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaopositivaweb_inscr) as templatecertidaopositivaweb_inscr,

            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativa_cgm) as templatecertidaonegativa_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativa_matric) as templatecertidaonegativa_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativa_inscr) as templatecertidaonegativa_inscr,

            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativaweb_cgm) as templatecertidaonegativaweb_cgm,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativaweb_matric) as templatecertidaonegativaweb_matric,
            (SELECT db82_descricao
               FROM db_documentotemplate
              WHERE db82_sequencial = k03_templatecertidaonegativaweb_inscr) as templatecertidaonegativaweb_inscr";

    $result = $clnumpref->sql_record($clnumpref->sql_query($chavepesquisa, $instit, $campos));
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
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
        <td width="360" height="18">&nbsp;</td>
        <td width="263">&nbsp;</td>
        <td width="25">&nbsp;</td>
        <td width="140">&nbsp;</td>
    </tr>
</table>
<table width="800" border="0" align="center" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center" align="top" bgcolor="#CCCCCC">
            <center>
                <?php include(modification("forms/db_frmnumpref.php")); ?>
            </center>
        </td>
    </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if (isset($alterar)) {
    if ($clnumpref->erro_status == "0") {
        $clnumpref->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clnumpref->erro_campo != "") {
            echo "<script> document.form1." . $clnumpref->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clnumpref->erro_campo . ".focus();</script>";
        };
    } else {
        $clnumpref->erro(true, true);
    };
};
if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
