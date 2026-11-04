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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orctiporec_classe.php"));

db_postmemory($_POST);

$clorctiporec = new cl_orctiporec;
$cliframe_seleciona = new cl_iframe_seleciona;
$clrotulo = new rotulocampo;
$clrotulo->label('c60_codcon');
$clrotulo->label('c60_descr');
$clrotulo->label("k17_codigo");
$clrotulo->label("c50_descr");
$clrotulo->label("k17_hist");

$db_opcao = 1;
db_app::load("scripts.js,
              strings.js,
              prototype.js,
              estilos.css,
              datagrid.widget.js,
              grid.style.css,
              DBLancador.widget.js,
              DBAncora.widget.js,
              dbtextField.widget.js
         ");
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script>
        function js_emite() {

            var aCGMSelecionado = oDBLancadorCGM.getRegistros(false);
            var sCGM = "";
            var sVirgula = "";

            aCGMSelecionado.each(function (oDado, iIndice) {

                sCGM += sVirgula + oDado.sCodigo;
                sVirgula = ",";
            });

            qry = 'codigos=' + sCGM;
            qry += '&situacao=' + document.form1.situacao.value;
            qry += '&data=' + document.form1.data_ano.value + '-' + document.form1.data_mes.value + '-' + document.form1.data_dia.value;
            qry += '&data1=' + document.form1.data1_ano.value + '-' + document.form1.data1_mes.value + '-' + document.form1.data1_dia.value;
            qry += '&slip1=' + document.form1.k17_codigo.value;
            qry += '&recurso=' + document.form1.o15_recurso.value;
            qry += '&slip2=' + document.form1.k17_codigo02.value;
            qry += '&hist=' + document.form1.k17_hist.value;
            qry += '&k145_numeroprocesso=' + encodeURIComponent($F('k145_numeroprocesso'));
            js_consulta(qry);
        }
    </script>
</head>

<style>
    #o15_codigodescr {
        width: 300px !important;
    }

    #situacao {
        width: 400px !important;
    }

</style>

<body>
<div class="container">
    <form id="form1" name="form1">

            <fieldset>

                <legend><strong>Dados do Slip</strong></legend>
                <table align='left'>
                    <tr>
                        <td nowrap title="<?= @$Tk17_codigo ?>" align='left'>
                            <?php db_ancora(@$Lk17_codigo, "js_pesquisak17_codigo(true);", $db_opcao); ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                'k17_codigo',
                                10,
                                $Ik17_codigo,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisak17_codigo(false);'",
                                "",
                                "",
                                "",
                                7
                            )
                            ?>
                        </td>
                        <td>
                            <?php db_ancora("<b>Até:</b>", "js_pesquisak17_codigo02(true);", $db_opcao); ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                'k17_codigo',
                                10,
                                $Ik17_codigo,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisak17_codigo02(false);'",
                                "k17_codigo02",
                                "",
                                "",
                                7
                            )
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align=left>
                            <b>De:</b>
                        </td>
                        <td>
                            <?php
                            db_inputdata("data", "", "", "", "true", "text", 2);
                            ?>
                        </td>
                        <td>
                            <b>Até:</b>
                        </td>
                        <td>
                            <?php db_inputdata("data1", "", "", "", "true", "text", 2); ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <?php db_ancora(@$Lk17_hist, "js_pesquisac50_codhist(true);", $db_opcao); ?>
                        </td>
                        <td colspan="4">
                            <?php
                            db_input(
                                'k17_hist',
                                10,
                                $Ik17_hist,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisac50_codhist(false);'"
                            );
                            db_input('c50_descr', 40, $Ic50_descr, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Recurso:</strong>
                        </td>
                        <td nowrap colspan='3'>
                            <?php
                            $date = date('Y-m-d', db_getsession('DB_datausu'));
                            $dbwhere = " o15_datalimite is null or o15_datalimite > '{$date}'";
                            $sql = $clorctiporec->sql_query(null, "o15_recurso,o15_descr", "o15_recurso", $dbwhere);
                            $rs = $clorctiporec->sql_record($sql);
                            db_selectrecord("o15_recurso", $rs, false, 1, "", "", "", "0");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align=left>
                            <strong>Situacao:</strong>
                        </td>
                        <td colspan="3">
                            <?php
                            $tipo_ordem = [
                                "A" => "Todas",
                                "1" => "Não Autenticado",
                                "2" => "Autenticado",
                                "3" => "Estornado",
                                "4" => "Cancelado"
                            ];
                            db_select("situacao", $tipo_ordem, true, 2);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align=left>
                            <strong>Processo Administrativo:</strong>
                        </td>
                        <td colspan="3">
                            <?php
                            db_input('k145_numeroprocesso', 10, null, true, 'text', $db_opcao);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <div id="divLancadorCGM"></div>

            <input name="emite2" id="emite2" type="button" value="Consultar" onclick="js_emite();">

    </form>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>

    $('o15_recurso').style.width = '95px';
    $('situacao').style.width = '100%';

    var oDBLancadorCGM = new DBLancador('oDBLancadorCGM');
    oDBLancadorCGM.setNomeInstancia('oDBLancadorCGM');
    oDBLancadorCGM.setGridHeight(200);
    oDBLancadorCGM.setLabelAncora('CGM: ');
    oDBLancadorCGM.setParametrosPesquisa('func_nome.php', ['z01_numcgm', 'z01_nome']);
    oDBLancadorCGM.show($('divLancadorCGM'));
    var oFieldsetLancador = oDBLancadorCGM.getFieldset();

    //-----------------------------------------------------------
    //---slip 01
    function js_pesquisak17_codigo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', 'func_slip.php?funcao_js=parent.js_mostraslip1|k17_codigo', 'Pesquisa', true);
        } else {
            slip01 = new Number(document.form1.k17_codigo.value);
            if (slip01 != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', 'func_slip.php?pesquisa_chave=' + slip01 + '&funcao_js=parent.js_mostraslip', 'Pesquisa', false);
            } else {
                document.form1.k17_codigo.value = '';
            }
        }
    }

    function js_mostraslip(chave, erro) {
        if (erro == true) {
            document.form1.k17_codigo.focus();
            document.form1.k17_codigo.value = '';
        }
    }

    function js_mostraslip1(chave1, chave2) {
        document.form1.k17_codigo.value = chave1;
        db_iframe_slip.hide();
    }

    //-----------------------------------------------------------
    //---slip 02
    function js_pesquisak17_codigo02(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', 'func_slip.php?funcao_js=parent.js_mostraslip12|k17_codigo', 'Pesquisa', true);
        } else {
            slip01 = new Number(document.form1.k17_codigo02.value);
            if (slip01 != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', 'func_slip.php?pesquisa_chave=' + slip01 + '&funcao_js=parent.js_mostraslip2', 'Pesquisa', false);
            } else {
                document.form1.k17_codigo02.value = '';
            }
        }
    }

    function js_consulta(pars) {

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_slip', 'cai3_conslip002.php?' + pars, 'Pesquisa', true);

    }

    function js_mostraslip2(chave, erro) {
        if (erro == true) {
            document.form1.k17_codigo02.focus();
            document.form1.k17_codigo02.value = '';
        }
    }

    function js_mostraslip12(chave1, chave2) {
        document.form1.k17_codigo02.value = chave1;
        db_iframe_slip.hide();
    }

    function js_pesquisac50_codhist(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_conhist.php?funcao_js=parent.js_mostrahist1|c50_codhist|c50_descr', 'Pesquisa', true);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'func_conhist.php?pesquisa_chave=' + document.form1.k17_hist.value + '&funcao_js=parent.js_mostrahist', 'Pesquisa', false);
        }
    }

    function js_mostrahist(chave, erro) {
        document.form1.c50_descr.value = chave;
        if (erro == true) {
            document.form1.k17_hist.focus();
            document.form1.k17_hist.value = '';
        }
    }

    function js_mostrahist1(chave1, chave2) {
        document.form1.k17_hist.value = chave1;
        document.form1.c50_descr.value = chave2;
        db_iframe.hide();
    }
</script>
