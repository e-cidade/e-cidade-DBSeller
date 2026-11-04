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

//MODULO: prefeitura
$clconfigdbpref->rotulo->label();
?>

<form name="form1" method="post" action="">
    <fieldset>
        <legend>
            <b>Configuração DBPref</b>
        </legend>
        <table>
            <tr>
                <td nowrap title="<?= @$Tw13_instit ?>">
                    <?= @$Lw13_instit ?>
                </td>
                <td>
                    <?php
                    db_input('w13_instit', 10, $Iw13_instit, true, 'text', 3, "");
                    ?>
                </td>
            </tr>
            <tr style='display:none;'>
                <td nowrap title="<?= @$Tw13_liberaatucgm ?>">
                    <?= @$Lw13_liberaatucgm ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_liberaatucgm");
                    db_select('w13_liberaatucgm', $x, true, $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_liberapedsenha ?>">
                    <?= @$Lw13_liberapedsenha ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_liberapedsenha");
                    db_select('w13_liberapedsenha', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_permfornsemlog ?>">
                    <?= @$Lw13_permfornsemlog ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_permfornsemlog");
                    db_select('w13_permfornsemlog', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_permvarsemlog ?>">
                    <?= @$Lw13_permvarsemlog ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_permvarsemlog");
                    db_select('w13_permvarsemlog', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_permconsservdemit ?>">
                    <?= @$Lw13_permconsservdemit ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_permconsservdemit");
                    db_select('w13_permconsservdemit', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_liberaimobiliaria ?>">
                    <?= @$Lw13_liberaimobiliaria ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_liberaimobiliaria");
                    db_select('w13_liberaimobiliaria', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_permconscgm ?>">
                    <?= @$Lw13_permconscgm ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_permconscgm");
                    db_select('w13_permconscgm', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_aliqissretido ?>">
                    <?= @$Lw13_aliqissretido ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_aliqissretido");
                    db_select('w13_aliqissretido', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_liberaissretido ?>">
                    <?= @$Lw13_liberaissretido ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_liberaissretido");
                    db_select('w13_liberaissretido', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_utilizafolha ?>">
                    <?= @$Lw13_utilizafolha ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_utilizafolha");
                    db_select('w13_utilizafolha', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_libcertpos ?>">
                    <?= @$Lw13_libcertpos ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_libcertpos");
                    db_select('w13_libcertpos', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_libcarnevariavel ?>">
                    <?= @$Lw13_libcarnevariavel ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_libcarnevariavel");
                    db_select('w13_libcarnevariavel', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_libsociosdai ?>">
                    <?= @$Lw13_libsociosdai ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_libsociosdai");
                    db_select('w13_libsociosdai', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_libissprestado ?>">
                    <?= @$Lw13_libissprestado ?>
                </td>
                <td>
                    <?php
                    $x = array("f" => "Não", "t" => "Sim");
                    $x = getValoresPadroesCampo("w13_libissprestado");
                    db_select('w13_libissprestado', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>

            <tr>
                <td nowrap title="<?= @$Tw13_liberalancisssemmov ?>">
                    <?= @$Lw13_liberalancisssemmov ?>
                </td>
                <td>
                    <?php
                    $x = array("f" => "Não", "t" => "Sim");
                    $x = getValoresPadroesCampo("w13_liberalancisssemmov");
                    db_select('w13_liberalancisssemmov', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_exigecpfcnpjmatricula ?>">
                    <?= @$Lw13_exigecpfcnpjmatricula ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_exigecpfcnpjmatricula");
                    db_select('w13_exigecpfcnpjmatricula', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_exigecpfcnpjinscricao ?>">
                    <?= @$Lw13_exigecpfcnpjinscricao ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_exigecpfcnpjinscricao");
                    db_select('w13_exigecpfcnpjinscricao', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_tipocertidao ?>">
                    <?= @$Lw13_tipocertidao ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_tipocertidao");
                    db_select('w13_tipocertidao', $x, true, $db_opcao, "style='width:110px;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_agrupadebrecibos ?>">
                    <?= @$Lw13_agrupadebrecibos ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_agrupadebrecibos");
                    db_select('w13_agrupadebrecibos', $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            <tr>
                <td nowrap title="<?= @$Tw13_msgaviso ?>">
                    <?= @$Lw13_msgaviso ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_msgaviso");
                    db_select("w13_msgaviso", $x, true, $db_opcao, "style='width:110px;;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_comprovanterendimentosanoanterior ?>">
                    <?= @$Lw13_comprovanterendimentosanoanterior ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_comprovanterendimentosanoanterior");
                    db_select("w13_comprovanterendimentosanoanterior", $x, true, $db_opcao, "style='width:110px;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_liberaescritorios ?>">
                    <?= @$Lw13_liberaescritorios ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_liberaescritorios");
                    db_select('w13_liberaescritorios', $x, true, $db_opcao, "style='width:360px;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_regracnd ?>">
                    <?= @$Lw13_regracnd ?>
                </td>
                <td>
                    <?php
                    $aRegraCND = getValoresPadroesCampo("w13_regracnd");
                    db_select('w13_regracnd', $aRegraCND, true, $db_opcao, "style='width:360px;'");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_tipocodigocertidao ?>">
                    <?= @$Lw13_tipocodigocertidao ?>
                </td>
                <td>
                    <?php
                    $x = getValoresPadroesCampo("w13_tipocodigocertidao");
                    db_select("w13_tipocodigocertidao", $x, true, $db_opcao, "style='width:360px;'");
                    ?>
                </td>
            </tr>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_emailadmin ?>">
                    <?= @$Lw13_emailadmin ?>
                </td>
                <td>
                    <?php
                    db_input('w13_emailadmin', 50, $Iw13_emailadmin, true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?= @$Tw13_uploadarquivos ?>">
                    <?= @$Lw13_uploadarquivos ?>
                </td>
                <td>
                    <?php
                    db_input('w13_uploadarquivos', 50, $Iw13_uploadarquivos, true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>

            <tr>
                <td nowrap title="Recaptcha Chave Pública">

                    <strong> Recaptcha Chave Pública: </strong>
                </td>

                <td>
                    <?php
                    db_input('w13_recaptcha_sitekey', 50, "Recaptcha Chave Pública", true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>

            <tr>
                <td nowrap title="Recaptcha Chave Privada">
                    <strong> Recaptcha Chave Privada: </strong>
                </td>
                <td>
                    <?php
                    db_input('w13_recaptcha_privatekey', 50, "Recaptcha Chave Privada", true, 'text', $db_opcao);
                    ?>
                </td>
            </tr>

        </table>
    </fieldset>
    <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
           type="submit" id="db_opcao"
           value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> >
</form>
<script>
    function js_pesquisa() {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_configdbpref', 'func_configdbpref.php?funcao_js=parent.js_preenchepesquisa|w13_instit', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_configdbpref.hide();
        <?php
        if ($db_opcao != 1) {
            echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        }
        ?>
    }
</script>
