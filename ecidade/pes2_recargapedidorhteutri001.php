<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidadedbseller.com.br
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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_rhteutri_classe.php"));

$clrhteutri = new cl_rhteutri;
$clrotulo   = new rotulocampo;

$clrotulo->label("rh68_descr");
$clrotulo->label("rh67_rhtipovale");

$rh67_rhtipovale = '';

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

    <form class="container" name="form1" method="post" action="" onsubmit="return js_verifica();">
        <fieldset>
            <Legend align="left">Arquivo de Pedido de Vale Transporte Rio Card</Legend>
            <table class="form-container">
                <tr>
                    <td align="right" nowrap title="<?= $Trh67_rhtipovale ?>">
                        <?php
                        if (isset($rh67_rhtipovale)) {
                            $rh67_rhtipovale = '';
                            $rh68_descr = '';
                        }
                        db_ancora($Lrh67_rhtipovale, "js_pesquisarh67_rhtipovale(true);", 2);
                        ?>
                    </td>
                    <td>
                        <?php
                            db_input('rh67_rhtipovale', 4, $Irh67_rhtipovale, true, 'text', 2, " onchange='js_pesquisarh67_rhtipovale(false);'")
                        ?>
                        <?php
                            db_input('rh68_descr', 40, $Irh68_descr, true, 'text', 3, '')
                        ?>
                    </td>
                </tr>
                <?php
                if ($rh67_rhtipovale == '2') {
                    echo "<tr id='camposdiversos' style='display'>";
                } else {
                    echo "<tr id='camposdiversos' style='display:none'>";
                }
                ?>
                </tr>
                <tr>
                    <td align="right" nowrap title="Tipo de emissão">
                        <strong>Tipo de emissão:&nbsp;</strong>
                    </td>
                    <td>
                        <?php
                            $aTipoEmissao = array("valor" => "Valor Recarga");
                            db_select('tipo_emissao', $aTipoEmissao, true, 1, "");
                        ?>
                    </td>
                </tr>

                <tr>
                    <td align="right" nowrap title="Ordem de emissão do relatório">
                        <strong>Ordem :&nbsp;&nbsp;</strong>
                    </td>
                    <td>
                        <?php
                            $x = array("n" => "Numérica", "a" => "Alfabética");
                            db_select('ordem', $x, true, 1, "");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="gera" id="gera" type="button" value="Processar" onclick="js_gerarArquivo();">
    </form>
    <?php
        db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));

        if (isset($_GET['mensagem'])) {
            echo "<script>alert('" . $_GET['mensagem'] . "');</script>";
        }
    ?>
</body>
</html>

<script>

    function js_recarregar(iTipo) {
        if (iTipo == 2) {
            var display = '';
        } else {
            var display = 'none';
        }
    }

    function js_ajaxRequest(obj) {
        js_divCarregando("Aguarde, buscando grupos", "processando");
        var url = 'pes4_dadosGrupoRPC.php';
        var parametro = 'tipovale=' + obj;
        var objAjax = new Ajax.Request(url, {
            method: 'post',
            parameters: parametro,
            onComplete: carregaDadosSelect
        });
        document.form1.rh67_rhtipovale.disabled = true;

    }

    function js_gerarArquivo() {

        js_divCarregando("Aguarde, gerando arquivo...", "processando");

        var rh67_rhtipovale = document.form1.rh67_rhtipovale.value;
        var rh68_descr      = document.form1.rh68_descr.value;
        var tipo_emissao    = document.form1.tipo_emissao.value;
        var ordem           = document.form1.ordem.value;
        var gera            = document.form1.gera.value;

        var url = 'pes2_recargapedidorhteutri002.php';

        var parametros = 'rh67_rhtipovale=' + rh67_rhtipovale +
                         '&rh68_descr='     + rh68_descr +
                         '&tipo_emissao='   + tipo_emissao +
                         '&ordem='          + ordem +
                         '&gera='           + gera;

        var objAjax = new Ajax.Request(url, {
            method: 'post',
            parameters: parametros,
            onComplete: function(response) {

                var data = JSON.parse(response.responseText);
                var oDownload = new DBDownload();
                oDownload.addFile(data.arquivo, 'PEDIDO');
                oDownload.show();

                js_removeObj("processando");

                document.form1.rh67_rhtipovale.disabled = false;
            }
        });
    }

    function carregaDadosSelect(resposta) {
        js_removeObj('processando');
        document.form1.rh67_rhtipovale.disabled = false;
        js_limpaSelect(document.form1.grupo);
        js_addSelectFromStr(resposta.responseText, document.form1.grupo);

    }

    function js_limpaSelect(obj) {
        obj.length = 0;
    }

    function js_addSelectFromStr(str, obj) {
        var linhas = str.split("|");
        obj.options[0] = new Option();
        obj.options[0].value = "todos";
        obj.options[0].text = "Todos";
        for (i = 0; i < linhas.length + 1; i++) {
            if (linhas[i] != '') {
                colunas = linhas[i].split("-");
                obj.options[i + 1] = new Option();
                obj.options[i + 1].value = colunas[0];
                obj.options[i + 1].text = colunas[1];
            }
        }
    }

    function js_pesquisarh67_rhtipovale(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhtipovale', 'func_rhtipovale.php?funcao_js=parent.js_mostrarhtipovale1|rh68_sequencial|rh68_descr', 'Pesquisa', true);
        } else {
            if (document.form1.rh67_rhtipovale.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_rhtipovale', 'func_rhtipovale.php?pesquisa_chave=' + document.form1.rh67_rhtipovale.value + '&funcao_js=parent.js_mostrarhtipovale', 'Pesquisa', false);
            } else {
                document.form1.rh68_descr.value = '';
            }
        }
    }

    function js_mostrarhtipovale(chave, erro) {
        document.form1.rh68_descr.value = chave;
        if (erro == true) {
            document.form1.rh67_rhtipovale.focus();
            document.form1.rh67_rhtipovale.value = '';
        } else {
            js_recarregar(document.form1.rh67_rhtipovale.value);
        }
    }

    function js_mostrarhtipovale1(chave1, chave2) {
        document.form1.rh67_rhtipovale.value = chave1;
        document.form1.rh68_descr.value = chave2;
        js_recarregar(chave1);
        db_iframe_rhtipovale.hide();
    }

    function js_verifica() {
        if (document.form1.rh67_rhtipovale.value == '') {
            alert('Escolha um Tipo de Vale');
            return false;
        }
    }

</script>
