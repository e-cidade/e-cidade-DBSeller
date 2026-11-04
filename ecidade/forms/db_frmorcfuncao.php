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

//MODULO: orcamento
$clorcfuncao->rotulo->label();

$legenda = "Inclusão de Função";
if ($db_opcao == 2 || $db_opcao == 22) {
    $legenda = "Alteração de Função";
}
if ($db_opcao == 3 || $db_opcao == 33) {
    $legenda = "Exclusão de Função";
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
<body>
<form class="container" name="form1" method="post" action="">
    <fieldset>
        <legend><?= $legenda ?></legend>
        <table class="form-container">
            <tr>
                <td nowrap title="<?= @$To52_funcao ?>">
                    <?= @$Lo52_funcao ?>
                </td>
                <td>
                    <?php
                    if ($db_opcao == 1) {
                        $db_opcao02 = 1;
                    } else {
                        $db_opcao02 = 3;
                    }
                    db_input('o52_funcao', 2, $Io52_funcao, true, 'text', $db_opcao02);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To52_descr ?>">
                    <?= @$Lo52_descr ?>
                </td>
                <td>
                    <?php
                    db_input('o52_descr', 40, $Io52_descr, true, 'text', $db_opcao, "")
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To52_siconfi ?>">
                    <?= @$Lo52_siconfi ?>
                </td>
                <td>
                    <?php
                    db_input('o52_siconfi', 10, $Io52_siconfi, true, 'text', $db_opcao, "", '', '', '', 2);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To52_codtri ?>">
                    <?= @$Lo52_codtri ?>
                </td>
                <td>
                    <?php
                    db_input('o52_codtri', 10, $Io52_codtri, true, 'text', $db_opcao, "")
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To52_finali ?>">
                    <?= @$Lo52_finali ?>
                </td>
                <td>
                    <?php
                    db_textarea('o52_finali', 0, 40, $Io52_finali, true, 'text', $db_opcao, "")
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="db_opcao" type="submit" id="db_opcao"
           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
    <?php if (empty($novo)) { ?>
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
    <?php } else { ?>
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_orcfuncao.hide();">
    <?php } ?>
</form>
<script>
    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_orcfuncao', 'func_orcfuncao.php?funcao_js=parent.js_preenchepesquisa|o52_funcao', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_orcfuncao.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave;";
        }
        ?>
    }
</script>
</body>
