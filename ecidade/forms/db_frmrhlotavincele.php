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

//MODULO: pessoal
require_once(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clrhlotavinc->rotulo->label();
$clrhlotavincativ->rotulo->label();
$clrhlotavincele->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("o55_descr");
$clrotulo->label("o56_descr");
$clrotulo->label("r70_descr");
$clrotulo->label("rh28_codeledef");
$clrotulo->label("rh43_recurso");
$clrotulo->label("o15_descr");
$clrotulo->label("o54_descr");
$clrotulo->label("o53_descr");
$clrotulo->label("o52_descr");
$clrotulo->label("gestao");
?>
<form name="form1" method="post" action="">
    <div class="container">
        <fieldset>
            <legend>Elementos Secundários</legend>

            <table class="form-container">
                <tr>
                    <td nowrap title="<?= @$Trh25_codlotavinc ?>">
                        <?php
                        db_ancora(@$Lrh25_codlotavinc, "", 3);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh25_codlotavinc', 8, $Irh25_codlotavinc, true, 'text', 3)
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Trh25_codigo ?>">
                        <?php
                        db_ancora(@$Lrh25_codigo, "", 3);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh25_codigo', 8, $Irh25_codigo, true, 'text', 3)
                        ?>
                        <?php
                        db_input('rh25_descr', 50, $Ir70_descr, true, 'text', 3);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?= @$Trh39_projativ ?>">
                        <?php
                        db_ancora(@$Lrh39_projativ, "js_pesquisarh39_projativ(true)", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh39_projativ', 8, $Irh39_projativ, true, 'text', $db_opcao, "onchange='js_pesquisarh39_projativ(false)'");
                        db_input('rh39_anousu', 4, $Irh39_anousu, true, 'text', 3);
                        db_input('o55_descr', 44, $Io55_descr, true, 'text', 3);
                        ?>
                    </td>
                </tr>

                <tr title="Pesquise usando o código de gestão.">
                    <td>
                        <?php
                        db_ancora("<b>Fonte de Recurso:</b>", "js_pesquisaRecurso(true)", 1);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh43_recurso', 8, $Irh43_recurso, true, 'hidden');
                        db_input("gestao", 5, $Igestao, true, "text", $db_opcao, "onchange='js_pesquisaRecurso(false);'");
                        db_input('o15_recurso', 5, $Io15_descr, true, 'text', 3);
                        db_input("descricao", 50, null, true, "text", 3);
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>Complemento:</td>
                    <td>
                        <?php db_input('o15_complemento', 5, 0, true, 'text', 3); ?>
                        <?php db_input('o200_descricao', 60, 0, true, 'text', 3); ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?php echo $Trh39_programa; ?>">
                        <?php db_ancora($Lrh39_programa, 'js_pesquisa_programa(true)', $db_opcao); ?>
                    </td>
                    <td>
                        <?php db_input('rh39_programa', 8, $Irh39_programa, true, 'text', $db_opcao, "onchange='js_pesquisa_programa(false)'"); ?>
                        <?php db_input('o54_descr', 50, $Io54_descr, true, 'text', 3); ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?php echo $Trh39_funcao; ?>">
                        <?php db_ancora($Lrh39_funcao, 'js_pesquisa_funcao(true)', $db_opcao); ?>
                    </td>
                    <td>
                        <?php db_input('rh39_funcao', 8, $Irh39_funcao, true, 'text', $db_opcao, "onchange='js_pesquisa_funcao(false)'"); ?>
                        <?php db_input('o52_descr', 50, $Io52_descr, true, 'text', 3); ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap title="<?php echo $Trh39_subfuncao; ?>">
                        <?php db_ancora($Lrh39_subfuncao, 'js_pesquisa_subfuncao(true)', $db_opcao); ?>
                    </td>
                    <td>
                        <?php db_input('rh39_subfuncao', 8, $Irh39_subfuncao, true, 'text', $db_opcao, "onchange='js_pesquisa_subfuncao(false)'"); ?>
                        <?php db_input('o53_descr', 50, $Io53_descr, true, 'text', 3); ?>
                    </td>
                </tr>


                <tr>
                    <td nowrap title="<?= @$Trh28_codeledef ?>">
                        <?php
                        db_ancora(@$Lrh28_codeledef, "js_pesquisarh28_codeledef(true)", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh28_codeledef', 8, $Irh28_codeledef, true, 'text', $db_opcao, "onchange='js_pesquisarh28_codeledef(false)'");
                        db_input('o56_descr', 50, $Io56_descr, true, 'text', 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?= @$Trh28_codelenov ?>">
                        <?php
                        db_ancora(@$Lrh28_codelenov, "js_pesquisarh28_codelenov(true)", $db_opcao);
                        ?>
                    </td>
                    <td>
                        <?php
                        db_input('rh28_codelenov', 8, $Irh28_codelenov, true, 'text', $db_opcao, "onchange='js_pesquisarh28_codelenov(false)'");
                        db_input('o56_descr', 50, $Io56_descr, true, 'text', 3, "", "o56_descrnov");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input
            name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
            type="submit" id="db_opcao"
            value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?>>
        <?php
        if ($db_opcao != 1) {
            echo '<input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" >&nbsp;';
        }
        ?>

    </div>

    <div class="subcontainer" style="width: 90%">
        <?php
        $where = " orcelemento.o56_anousu = " . db_getsession("DB_anousu") . " and a.o56_anousu = " . db_getsession("DB_anousu");
        if (isset($rh25_codlotavinc) && trim($rh25_codlotavinc) != "") {
            $where .= " and rh28_codlotavinc = $rh25_codlotavinc ";
        } elseif (isset($rh28_codlotavinc) && trim($rh28_codlotavinc) != "") {
            $where .= " and rh28_codlotavinc = $rh28_codlotavinc ";
        }

        if (isset($default) && trim($default) != "") {
            $where .= " and rh28_codeledef <> $default ";
        }
        $chavepri = array("rh28_codlotavinc" => @$rh28_codlotavinc, "rh28_codeledef" => @$rh28_codeledef);
        $cliframe_alterar_excluir->chavepri = $chavepri;
        $sCampos = " DISTINCT rh28_codlotavinc, rh28_codeledef, orcelemento.o56_descr, rh28_codelenov, a.o56_descr";
        $sCampos .= " ,rh43_recurso, o15_codigo, gestao, o15_recurso, descricao, o55_projativ, o55_descr, o55_anousu";
        $sOrder = "rh28_codlotavinc, rh28_codeledef";
        $cliframe_alterar_excluir->sql = $clrhlotavincele->sql_query_ele(null, null, $sCampos, $sOrder, $where);
        $cliframe_alterar_excluir->campos = "rh28_codeledef,o56_descr,rh28_codelenov,o56_descr, gestao, o15_recurso, descricao,o55_projativ,o55_descr,o55_anousu";
        $cliframe_alterar_excluir->legenda = "ITENS LANÇADOS";
        $cliframe_alterar_excluir->iframe_height = "200";
        $cliframe_alterar_excluir->iframe_width = "100%";
        $cliframe_alterar_excluir->opcoes = $opcoesae;
        $cliframe_alterar_excluir->iframe_alterar_excluir(1);
        ?>
    </div>

</form>
<script>
    function js_pesquisarh39_projativ(mostra) {
        if (document.form1.rh28_codeledef.value == "") {
            parent.document.form1.chave2.value = '';
            parent.document.form1.chave3.value = '';
        }
        if (document.form1.rh28_codelenov.value == "") {
            parent.document.form1.chave4.value = '';
            parent.document.form1.chave5.value = '';
        }
        if (document.form1.rh43_recurso.value == "") {
            parent.document.form1.chave6.value = '';
            parent.document.form1.chave7.value = '';
        }
        <?php
        if (isset($opcao) && $opcao == "alterar") {
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave.value  = document.form1.rh39_anousu.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave1.value = document.form1.rh39_projativ.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave2.value = document.form1.rh28_codeledef.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave3.value = document.form1.o56_descr.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave4.value = document.form1.rh28_codelenov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave5.value = document.form1.o56_descrnov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave6.value = document.form1.rh43_recurso.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave7.value = document.form1.o15_descr.value;";
        }
        ?>
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcprojativ', 'func_orcprojativ.php?funcao_js=parent.js_mostraprojativ2|o55_anousu|o55_projativ&anousu=<?=(db_getsession("DB_anousu"))?>', 'Pesquisa', true, '0');
        } else {
            if (document.form1.rh39_projativ.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcprojativ', 'func_orcprojativ.php?pesquisa_chave=' + document.form1.rh39_projativ.value + '&funcao_js=parent.js_mostraprojativ2&mostraprojativ=true', 'Pesquisa', false, '0');
            } else {
                document.form1.o55_descr.value = "";
                document.form1.rh39_anousu.value = "";
                document.form1.rh39_projativ.focus();
                parent.document.form1.chave.value = '';
                parent.document.form1.chave1.value = '';
            }
        }
    }

    function js_pesquisarh28_codeledef(mostra) {
        if (document.form1.rh39_projativ.value == "") {
            parent.document.form1.chave.value = '';
            parent.document.form1.chave1.value = '';
        }
        if (document.form1.rh28_codelenov.value == "") {
            parent.document.form1.chave4.value = '';
            parent.document.form1.chave5.value = '';
        }
        if (document.form1.rh43_recurso.value == "") {
            parent.document.form1.chave6.value = '';
            parent.document.form1.chave7.value = '';
        }
        <?php
        if (isset($opcao) && $opcao == "alterar") {
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave.value  = document.form1.rh39_anousu.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave1.value = document.form1.rh39_projativ.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave2.value = document.form1.rh28_codeledef.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave3.value = document.form1.o56_descr.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave4.value = document.form1.rh28_codelenov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave5.value = document.form1.o56_descrnov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave6.value = document.form1.rh43_recurso.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave7.value = document.form1.o15_descr.value;";
        }
        ?>
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcelemento', 'func_orcelementodef.php?funcao_js=parent.js_mostraorcelemento|o56_codele|o56_descr', 'Pesquisa', true, '0');
        } else {
            if (document.form1.rh28_codeledef.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcelemento', 'func_orcelementodef.php?pesquisa_chave=' + document.form1.rh28_codeledef.value + '&funcao_js=parent.js_mostraorcelemento&mostradescr=true', 'Pesquisa', false, '0');
            } else {
                document.form1.rh28_codeledef.value = '';
                document.form1.o56_descr.value = '';
                parent.document.form1.chave2.value = '';
                parent.document.form1.chave3.value = '';
            }
        }
    }

    function js_pesquisarh28_codelenov(mostra) {
        if (document.form1.rh39_projativ.value == "") {
            parent.document.form1.chave.value = '';
            parent.document.form1.chave1.value = '';
        }
        if (document.form1.rh28_codeledef.value == "") {
            parent.document.form1.chave2.value = '';
            parent.document.form1.chave3.value = '';
        }
        if (document.form1.rh43_recurso.value == "") {
            parent.document.form1.chave6.value = '';
            parent.document.form1.chave7.value = '';
        }
        <?php
        if (isset($opcao) && $opcao == "alterar") {
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave.value  = document.form1.rh39_anousu.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave1.value = document.form1.rh39_projativ.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave2.value = document.form1.rh28_codeledef.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave3.value = document.form1.o56_descr.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave4.value = document.form1.rh28_codelenov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave5.value = document.form1.o56_descrnov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave6.value = document.form1.rh43_recurso.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave7.value = document.form1.o15_descr.value;";
        }
        ?>
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcelemento', 'func_orcelementonov.php?funcao_js=parent.js_mostraorcelemento1|o56_codele|o56_descr', 'Pesquisa', true, '0');
        } else {
            if (document.form1.rh28_codelenov.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhlotavinc', 'db_iframe_orcelemento', 'func_orcelementonov.php?pesquisa_chave=' + document.form1.rh28_codelenov.value + '&funcao_js=parent.js_mostraorcelemento1&mostradescr=true', 'Pesquisa', true, '0');
            } else {
                document.form1.rh28_codelenov.value = '';
                document.form1.o56_descrnov.value = '';
                parent.document.form1.chave4.value = '';
                parent.document.form1.chave5.value = '';
            }
        }
    }


    function js_pesquisaRecurso(lMostraWindow) {

        if (document.form1.rh39_projativ.value == "") {
            parent.document.form1.chave.value = '';
            parent.document.form1.chave1.value = '';
        }
        if (document.form1.rh28_codeledef.value == "") {
            parent.document.form1.chave2.value = '';
            parent.document.form1.chave3.value = '';
        }
        if (document.form1.rh28_codelenov.value == "") {
            parent.document.form1.chave4.value = '';
            parent.document.form1.chave5.value = '';
        }
        <?php
        if (isset($opcao) && $opcao == "alterar") {
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave.value   = document.form1.rh39_anousu.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave1.value  = document.form1.rh39_projativ.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave2.value  = document.form1.rh28_codeledef.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave3.value  = document.form1.o56_descr.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave4.value  = document.form1.rh28_codelenov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave5.value  = document.form1.o56_descrnov.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave6.value  = document.form1.rh43_recurso.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave7.value  = document.form1.o15_descr.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave8.value  = document.form1.rh39_programa.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave9.value  = document.form1.rh39_subfuncao.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.form1.chave10.value = document.form1.rh39_funcao.value;";
            echo "(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_rhlotavinc.document.getElementById('chave8').value  = document.form1.rh39_programa.value;";
        }
        ?>

        if (!lMostraWindow && $('gestao').value == '') {
            $('rh43_recurso').value = id;
            $('o15_recurso').value = recurso;
            $('descricao').value = descricao;
            $('o15_complemento').value = complemento;
            $('o200_descricao').value = descrComplemento
            return
        }

        let param = 'gestao=' + $('gestao').value;
        pesquisaRecurso(param);
    }

    const pesquisaRecurso = (parametroAdicional) => {
        let sUrl = 'func_novosRecursos.php?funcao_js=parent.js_preencheRecurso|o15_codigo|o15_recurso|gestao|descricao|o15_complemento|o200_descricao';
        if (parametroAdicional) {
            sUrl += `&${parametroAdicional}`;
        }
        js_OpenJanelaIframe('', 'db_iframe_recurso', sUrl, 'Pesquisa Fonte de Recurso', true);
    };

    function js_preencheRecurso(id, recurso, gestao, descricao, complemento, descrComplemento) {
        $('rh43_recurso').value = id;
        $('o15_recurso').value = recurso;
        $('gestao').value = gestao;
        $('descricao').value = descricao;
        $('o15_complemento').value = complemento;
        $('o200_descricao').value = descrComplemento

        db_iframe_recurso.hide();
    }

    function js_cancelar() {
        document.location.href = "pes1_rhlotavincele001.php?lotacao=" + document.form1.rh25_codigo.value + "&lotavinc=" + document.form1.rh25_codlotavinc.value;
    }

    /**
     * -------------------------------------------------
     * Pesquisa programa - rh39_programa
     * -------------------------------------------------
     */
    function js_pesquisa_programa(lMostra) {

        /**
         * Ancora
         */
        if (lMostra) {

            js_OpenJanelaIframe('',
                'db_iframe_orcprograma',
                'func_orcprograma.php?funcao_js=parent.js_preenchePesquisaProgramaAncora|o54_programa|o54_descr',
                'Pesquisa',
                true);
            return;
        }

        /**
         * Input change
         */
        var iPrograma = $('rh39_programa').value;

        js_OpenJanelaIframe('',
            'db_iframe_orcprograma',
            'func_orcprograma.php?pesquisa_chave=' + iPrograma + '&funcao_js=parent.js_preenchePesquisaProgramaInput',
            'Pesquisa',
            false);
    }

    /**
     * Programa
     * Preenhce campos pela pesquisa do ancora
     */
    function js_preenchePesquisaProgramaAncora(iPrograma, sDescricao, lErro) {

        if (sDescricao != '') {
            $('o54_descr').value = sDescricao;
        }

        if (iPrograma != '') {
            $('rh39_programa').value = iPrograma;
        }

        if (lErro) {
            $('o54_descr').value = '';
        }

        db_iframe_orcprograma.hide();
    }

    /**
     * Programa
     * Preenche campo pela pesquisa do input
     */
    function js_preenchePesquisaProgramaInput(sDescricao, lErro) {

        if (sDescricao != '') {
            $('o54_descr').value = sDescricao;
        }

        if (lErro) {
            $('rh39_programa').value = '';
        }
    }

    /**
     * -------------------------------------------------
     * Pesquisa subfunção - rh39_subfuncao
     * -------------------------------------------------
     */
    function js_pesquisa_subfuncao(lMostra) {

        /**
         * Ancora
         */
        if (lMostra) {

            js_OpenJanelaIframe('',
                'db_iframe_orcsubfuncao',
                'func_orcsubfuncao.php?funcao_js=parent.js_preenchePesquisaSubfuncaoAncora|o53_subfuncao|o53_descr',
                'Pesquisa',
                true);
            return;
        }

        /**
         * Input change
         */
        var iSubfuncao = $('rh39_subfuncao').value;

        js_OpenJanelaIframe('',
            'db_iframe_orcsubfuncao',
            'func_orcsubfuncao.php?pesquisa_chave=' + iSubfuncao + '&funcao_js=parent.js_preenchePesquisaSubfuncaoInput',
            'Pesquisa',
            false);
    }

    /**
     * Subfunção
     * Preenhce campos pela pesquisa do ancora
     */
    function js_preenchePesquisaSubfuncaoAncora(iSubfuncao, sDescricao, lErro) {

        if (sDescricao != '') {
            $('o53_descr').value = sDescricao;
        }

        if (iSubfuncao != '') {
            $('rh39_subfuncao').value = iSubfuncao;
        }

        if (lErro) {
            $('o53_descr').value = '';
        }

        db_iframe_orcsubfuncao.hide();
    }

    /**
     * Subfunção
     * Preenche campo pela pesquisa do input
     */
    function js_preenchePesquisaSubfuncaoInput(sDescricao, lErro) {

        if (sDescricao != '') {
            $('o53_descr').value = sDescricao;
        }

        if (lErro) {
            $('rh39_subfuncao').value = '';
        }
    }

    /**
     * -------------------------------------------------
     * Pesquisa função - rh39_funcao
     * -------------------------------------------------
     */
    function js_pesquisa_funcao(lMostra) {

        /**
         * Ancora
         */
        if (lMostra) {

            js_OpenJanelaIframe('',
                'db_iframe_orcfuncao',
                'func_orcfuncao.php?funcao_js=parent.js_preenchePesquisaFuncaoAncora|o52_funcao|o52_descr',
                'Pesquisa',
                true);
            return;
        }

        /**
         * Input change
         */
        var iFuncao = $('rh39_funcao').value;

        js_OpenJanelaIframe('',
            'db_iframe_orcfuncao',
            'func_orcfuncao.php?pesquisa_chave=' + iFuncao + '&funcao_js=parent.js_preenchePesquisaFuncaoInput',
            'Pesquisa',
            false);
    }

    /**
     * Função
     * Preenhce campos pela pesquisa do ancora
     */
    function js_preenchePesquisaFuncaoAncora(iFuncao, sDescricao, lErro) {

        if (sDescricao != '') {
            $('o52_descr').value = sDescricao;
        }

        if (iFuncao != '') {
            $('rh39_funcao').value = iFuncao;
        }

        if (lErro) {
            $('o52_descr').value = '';
        }

        db_iframe_orcfuncao.hide();
    }

    /**
     * Função
     * Preenche campo pela pesquisa do input
     */
    function js_preenchePesquisaFuncaoInput(sDescricao, lErro) {

        if (sDescricao != '') {
            $('o52_descr').value = sDescricao;
        }

        if (lErro) {
            $('rh39_funcao').value = '';
        }
    }
</script>
