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

//MODULO: protocolo
$cltipoproc->rotulo->label();
?>
<form name="form1" method="post" action="">
    <center>
        <fieldset>
            <legend><b>Tipo de Processo</b></legend>
            <table style="border:none">
                <tr>
                    <td nowrap title="<?php echo @$Tp51_codigo ?>">
                        <?php
                        echo @$Lp51_codigo
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('p51_codigo', 10, $Ip51_codigo, true, 'text', 3, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo @$Tp51_prottipodocumentoprocesso ?>">
                        <?php
                        db_ancora(
                            @$Lp51_prottipodocumentoprocesso,
                            "js_pesquisa_tipo_documento_processo(true);",
                            $db_opcao
                        );
                        ?>
                    </td>
                    <td nowrap>
                        <?php
                        db_input(
                            'p51_prottipodocumentoprocesso',
                            10,
                            $Ip51_prottipodocumentoprocesso,
                            false,
                            'text',
                            3,
                            " onchange='js_pesquisa_tipo_documento_processo(false);'"
                        );
                        db_input('p91_descricao', 40, $Iz01_nome, true, 'text', 3, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo @$Tp51_descr ?>">
                        <?php
                        echo @$Lp51_descr
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('p51_descr', 60, $Ip51_descr, true, 'text', $db_opcao, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Tp51_dtlimite ?>">
                        <?php
                        echo @$Lp51_dtlimite;
                        ?>
                    </td>
                    <td>
                        <?php
                        $matriz = array('t' => "Sim", 'f' => "Nao");
                        db_inputdata(
                            'p51_dtlimite',
                            @$p51_dtlimite_dia,
                            @$p51_dtlimite_mes,
                            @$p51_dtlimite_ano,
                            true,
                            'text',
                            $db_opcao,
                            ""
                        );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo @$Tp51_identificado ?>">
                        <?php
                        echo @$Lp51_identificado;
                        ?>
                    </td>
                    <td>
                        <?php
                        $x = array('t' => 'Sim', 'f' => 'Não');
                        db_select('p51_identificado', $x, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </center>
    <br/>
    <?php
    if (isset($db_opcao) && $db_opcao == 1) {
        $sBtn = "Incluir";
    } elseif (isset($db_opcao) && $db_opcao == 2 || $db_opcao == 22) {
        $sBtn = "Alterar";
    } elseif (isset($db_opcao) && $db_opcao == 3 || $db_opcao == 33) {
        $sBtn = "Excluir";
    }
    ?>
    <input name="db_opcao" type="submit" id="db_opcao"
           value="<?= $sBtn ?>" <?= ($db_botao == false ? "disabled" : "") ?> />

    <?php
    if (isset($db_opcao) && $db_opcao == 2) {
        ?>
        <input name='novo' type='button' id='novo' value='Novo Registro' onclick='js_novo();'>&nbsp;
        <?php
    }
    if (isset($db_opcao) && $db_opcao == 2 || $db_opcao == 22 || $db_opcao == 3 || $db_opcao == 33) {
        ?>
        <input name='pesquisar' type='button' id='pesquisar' value='Pesquisar' onclick='js_pesquisa();'>
        <?php
    }
    ?>
</form>

<script>
    function js_pesquisa() {
        db_iframe.jan.location.href = 'func_tipoproc_todos.php?funcao_js=parent.js_preenchepesquisa|0&grupo=';
        db_iframe.mostraMsg();
        db_iframe.show();
        db_iframe.focus();
    }

    function js_preenchepesquisa(chave) {
        db_iframe.hide();
        location.href = '<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>' + "?chavepesquisa=" + chave;
    }

    function js_novo() {
        parent.location = "ouv1_tipoproc001.php";
    }


    function js_pesquisa_tipo_documento_processo(mostra) {
        var url = "func_prottipodocumentoprocesso.php";
        var parametros = "?funcao_js=parent.js_mostra_tipo_documento_processo";

        parametros += !mostra ? `&pesquisa_chave=${document.form1.p51_prottipodocumentoprocesso.value}` : '|0|1';

        js_OpenJanelaIframe("", "iframe_tipodocumento", url + parametros, "Pesquisa Tipo de Documento", mostra);
    }

    function js_mostra_tipo_documento_processo(chave1, chave2) {
        document.form1.p51_prottipodocumentoprocesso.value = chave1;
        document.form1.p91_descricao.value = chave2;
        iframe_tipodocumento.hide();
    }
</script>

<?php
$func_iframe = new janela('db_iframe', '');
$func_iframe->posX = 1;
$func_iframe->posY = 20;
$func_iframe->largura = 780;
$func_iframe->altura = 430;
$func_iframe->titulo = 'Pesquisa';
$func_iframe->iniciarVisivel = false;
$func_iframe->mostrar();
if ($db_opcao == 22 || $db_opcao == 33) {
    ?>

    <script>
        onLoad = js_pesquisa();
    </script>
    <?php
}
?>
