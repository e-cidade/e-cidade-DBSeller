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
$clorcsubfuncao->rotulo->label();

$bloqueiaCodigo = 1;
$legenda = "Inclusão de Subfunção";
if ($db_opcao == 2 || $db_opcao == 22) {
    $legenda = "Alteração de Subfunção";
}
if ($db_opcao == 3 || $db_opcao == 33) {
    $legenda = "Exclusão de Subfunção";
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>

<form class="container" name="form1" method="post" action="">
    <fieldset style="margin-top: 10px; width: 700px;">
        <legend><?= $legenda ?></legend>
        <table class="form-container">
            <tr>
                <td nowrap title="<?= @$To53_subfuncao ?>">
                    <?= @$Lo53_subfuncao ?>
                </td>
                <td>
                    <?php
                    db_input('o53_subfuncao', 3, $Io53_subfuncao, true, 'text', $bloqueiaCodigo);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To53_descr ?>">
                    <?= @$Lo53_descr ?>
                </td>
                <td>
                    <?php
                    db_input('o53_descr', 80, $Io53_descr, true, 'text', $db_opcao, "")
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To53_siconfi ?>">
                    <?= @$Lo53_siconfi ?>
                </td>
                <td>
                    <?php
                    db_input('o53_siconfi', 10, $Io53_siconfi, true, 'text', $db_opcao, "", '', '', '', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$To53_codtri ?>">
                    <?= @$Lo53_codtri ?>
                </td>
                <td>
                    <?php
                    db_input('o53_codtri', 10, $Io53_codtri, true, 'text', $db_opcao, "")
                    ?>
                </td>
            </tr>
            <tr>

                <td colspan="2" title="<?= @$To53_finali ?>">
                    <fieldset>
                        <legend> <?= @$Lo53_finali ?></legend>

                        <?php
                        db_textarea('o53_finali', 0, 40, $Io53_finali, true, 'text', $db_opcao, "")
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
    </fieldset>

    <div style="margin-top: 5px;">
        <input name="db_opcao" type="submit" id="db_opcao"
               value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
        <?php if (empty($novo)) { ?>
            <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();">
        <?php } else { ?>
            <input name="Fechar" type="button" id="fechar" value="Fechar"
                   onClick="parent.db_iframe_orcsubfuncao.hide();">
        <?php } ?>

    </div>
</form>

<script>
    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_orcsubfuncao', 'func_orcsubfuncao.php?funcao_js=parent.js_preenchepesquisa|o53_subfuncao', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_orcsubfuncao.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave;";
        }
        ?>
    }
</script>
