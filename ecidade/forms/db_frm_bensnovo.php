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

$clrotulo = new rotulocampo;

$clrotulo->label("t30_descr");
$clrotulo->label("t33_divisao");
$clrotulo->label("o40_descr");

$clrotulo->label("o41_descr");
$clrotulo->label("t41_placa");

$clrotulo->label("t42_descr");

$clrotulo->label("t45_sequencial");
$clrotulo->label("t45_descricao");

$clrotulo->label("t52_depart");

$clrotulo->label("t52_bem");
$clrotulo->label("t52_descr");
$clrotulo->label("t52_dtaqu");
$clrotulo->label("t52_dtinclusao");
$clrotulo->label("t52_numcgm");
$clrotulo->label("t52_processo");

$clrotulo->label("t64_descr");
$clrotulo->label("t64_class");
$clrotulo->label("t64_codcla");

$clrotulo->label("t04_sequencial");
$clrotulo->label("z01_nome_convenio");

$clrotulo->label("t53_codbem");
$clrotulo->label("t53_ntfisc");
$clrotulo->label("t53_empen");
$clrotulo->label("t53_ordem");
$clrotulo->label("t53_garant");

$clrotulo->label("t44_vidautil");

$clrotulo->label("t44_valoratual");

$clrotulo->label("t54_codbem");
$clrotulo->label("t54_idbql");
$clrotulo->label("t54_obs");

$clrotulo->label("t56_situac");
$clrotulo->label("t70_descr");

$clrotulo->label("pc01_descrmater");
$clrotulo->label("z01_nome");
$clrotulo->label("descrdepto");

$oDataAtual = new DBDate(date("d/m/Y", db_getsession("DB_datausu")));
$oInstituicao = new Instituicao(db_getsession("DB_instit"));

$lPossuiIntegracaoPatrimonial = ParametroIntegracaoPatrimonial::possuiIntegracaoPatrimonio($oDataAtual, $oInstituicao);

$iLimitePlaca = 10;
$oDaoCfPatriPlaca = new cl_cfpatriplaca();
$sSqlCfPatriPlaca = $oDaoCfPatriPlaca->sql_query_file(db_getsession("DB_instit"), 't07_digseqplaca');
$rsCfPatriPlaca = db_query($sSqlCfPatriPlaca);

if ($rsCfPatriPlaca && pg_num_rows($rsCfPatriPlaca) > 0) {
    $iLimitePlaca = db_utils::fieldsMemory($rsCfPatriPlaca, 0)->t07_digseqplaca;
}
?>

<div class="container">
    <fieldset id="fieldsetBensNovo">
        <legend class="bold">Inclusão de Bens</legend>
        <form id='form1' name="form1" method="post" action="">
            <fieldset class="separator">
                <legend class="bold">Informações do Bem</legend>

                <table class="form-container">
                    <tr>
                        <td>
                            <div class="ctnFoto">
                                <fieldset>
                                    <legend>Foto</legend>
                                    <input id="fotoBem" name="fotoBem" />
                                </fieldset>

                                <input id="removerFoto" name="removerFoto" type="button" value="Remover Foto" />
                            </div>
                        </td>

                        <td>
                            <table>
                                <tr>
                                    <td title="<?php echo $Tt52_bem; ?>">
                                        <label for="t52_bem">
                                            <?php echo $Lt52_bem; ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        db_input('t52_bem', 10, $It52_bem, true, "text", 3);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td id='placa' title="<?php echo $Tt41_placa; ?>">
                                        <label for="t41_placa">
                                            <?php echo $Lt41_placa; ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        db_input(
                                            'sPlaca',
                                            10,
                                            "",
                                            true,
                                            "text",
                                            1,
                                            "onblue='js_ValidaMaiusculo(this,'t',event);'",
                                            "",
                                            "",
                                            "text-transform: uppercase;"
                                        );
                                        db_input('t41_placa', 10, "", true, "text", 1, "", '', '', '', $iLimitePlaca);
                                        ?>
                                        <label>Placa Impressa:</label>
                                        <span id='impressa' class="bold">Não</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="<?php echo $Tt52_dtaqu ?>">
                                        <label for="t52_dtaqu">
                                            <?php echo $Lt52_dtaqu; ?>
                                        </label>
                                    </td>
                                    <td colspan="6">
                                        <?php
                                        $t52_dtaqu_dia = !empty($t52_dtaqu_dia) ? $t52_dtaqu_dia : '';
                                        $t52_dtaqu_mes = !empty($t52_dtaqu_mes) ? $t52_dtaqu_mes : '';
                                        $t52_dtaqu_ano = !empty($t52_dtaqu_ano) ? $t52_dtaqu_ano : '';

                                        db_inputdata(
                                            't52_dtaqu',
                                            $t52_dtaqu_dia,
                                            $t52_dtaqu_mes,
                                            $t52_dtaqu_ano,
                                            true,
                                            'text',
                                            $db_opcao
                                        );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="<?php echo $Tt52_dtaqu ?>">
                                        <label for="t52_dtinclusao">
                                            <?php echo $Lt52_dtinclusao; ?>
                                        </label>
                                    </td>
                                    <td colspan="6">
                                        <?php

                                        $data = date('d/m/Y', db_getsession('DB_datausu'));

                                        echo "<input type='text' id='t52_dtinclusao' name='t52_dtinclusao'
                                               value='{$data}' size='10' style='background-color: #DEB887' readonly />";
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td title="<?php echo $Tt52_descr; ?>">
                                        <label for="t52_descr">
                                            <?php echo $Lt52_descr; ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        db_input('t52_descr', 81, $It52_descr, true, 'text', $db_opcao);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td nowrap="nowrap" title="<?php echo $Tt64_class; ?>">
                            <?php
                            db_ancora(
                                $Lt64_class,
                                "js_pesquisaClasse(true);",
                                (($db_opcao == 2 && $lPossuiIntegracaoPatrimonial) ? 3 : $db_opcao)
                            );
                            ?>
                        </td>
                        <td class="field-size2">
                            <?php
                            db_input('t64_codcla', 10, "", true, 'hidden', $db_opcao);
                            db_input(
                                't64_class',
                                10,
                                $It64_class,
                                true,
                                'text',
                                (($db_opcao == 2 && $lPossuiIntegracaoPatrimonial) ? 3 : $db_opcao),
                                "onchange='js_pesquisaClasse(false);'"
                            );
                            db_input('t64_descr', 100, $It64_descr, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" title="<?php echo $Tt52_numcgm; ?>" id="tdFornecedor">
                            <?php
                            db_ancora($Lt52_numcgm, "js_pesquisaFornecedor(true);", $db_opcao);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                't52_numcgm',
                                10,
                                $It52_numcgm,
                                true,
                                'text',
                                $db_opcao,
                                "onchange='js_pesquisaFornecedor(false);'"
                            );
                            db_input('z01_nome', 100, $Iz01_nome, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" title="<?php echo $Tt45_sequencial; ?>">
                            <?php
                            db_ancora("Tipo de Aquisição:", "js_pesquisaTipoAquisicao(true);", $db_opcao);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                't45_sequencial',
                                10,
                                $It45_sequencial,
                                true,
                                'text',
                                $db_opcao,
                                "onchange='js_pesquisaTipoAquisicao(false);'"
                            );
                            db_input('t45_descricao', 100, $It45_descricao, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr id='orgao' style="display: none;">
                        <td>
                            <label for="o40_descr">Orgão:</label>
                        </td>
                        <td colspan="5">
                            <?php
                            db_input('o40_descr', 81, "", true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr id='unidade' style="display: none;">
                        <td>
                            <label for="o41_descr">Unidade:</label>
                        </td>
                        <td colspan="5">
                            <?php
                            db_input('o41_descr', 81, "", true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" title="<?php echo $Tt52_depart; ?>">
                            <?php
                            db_ancora($Lt52_depart, "js_pesquisaDepartamento(true);", $db_opcao);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                't52_depart',
                                10,
                                $It52_depart,
                                true,
                                'text',
                                $db_opcao,
                                "onchange='js_pesquisaDepartamento(false);'"
                            );
                            db_input('descrdepto', 100, $Idescrdepto, true, 'text', 3);
                            ?>
                        </td>
                        <td id="l-divisao" style="display: none;" title="<?php echo $Tt52_dtaqu; ?>">
                            <label for="divisao">Divisão</label>
                        </td>
                        <td id="c-divisao" style="display: none;">
                            <?php
                            $x = array("0" => "Selecione");
                            db_select('divisao', $x, true, $db_opcao);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" title="Convênio">
                            <?php
                            db_ancora("Convênio:", "js_pesquisaConvenio(true);", $db_opcao);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                't04_sequencial',
                                10,
                                $It04_sequencial,
                                true,
                                'text',
                                $db_opcao,
                                "onchange='js_pesquisaConvenio(false);'"
                            );
                            db_input('z01_nome_convenio', 100, '', true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap="nowrap" title="<?php echo $Tt56_situac; ?>">
                            <?php
                            db_ancora($Lt56_situac, "js_pesquisaSituacaoBem(true);", 1);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('t56_situac', 10, $It56_situac, true, 'text', 1, " onchange='js_pesquisaSituacaoBem(false);'");
                            db_input('t70_descr', 100, $It70_descr, true, 'text', 3);
                            db_input("tipo_inclui", 40, "0", true, "hidden", 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td> <b>Processo:</b></td>
                        <td colspan="5">
                            <?php
                            db_input('t52_processo', 10, $It52_processo, true, 'text', $db_opcao, '');
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="separator">
                <legend class="bold">Dados Financeiros</legend>
                <table>
                    <tr>
                        <td class="field-size1">
                            <label for="vlAquisicao" class="bold">Valor de Aquisição:</label>
                        </td>
                        <td class="field-size2">
                            <?php
                            db_input(
                                'vlAquisicao',
                                10,
                                $It64_descr,
                                true,
                                'text',
                                $db_opcao,
                                'onchange = "js_calculaValorTotal();"'
                            );
                            ?>
                        </td>
                        <td style="text-align: left;">
              <label for="vlResidual" class="bold">Valor Residual (%):</label>
                            <?php
                            db_input('perc_residual', 5, $It64_descr, true, 'text', 1, '', '', '', "text-align: right");
                            db_input(
                                'vlResidual',
                                10,
                                $It64_descr,
                                true,
                                'text',
                                3,
                                ' onchange = "js_calculaValorTotal()"'
                            );
                            ?>
                        </td>
                        <td  class="field-size2" style="text-align: left;">
                            <label for="vlTotalDepreciavel" class="bold">Valor Depreciável:</label>
                            <?php
                            db_input('vlTotalDepreciavel', 10, $It64_descr, true, 'text', 3);
                            ?>
                        </td>
                        <td style="text-align: left;">
                            <label for="vlTotal" class="bold">Valor Atual:</label>
                            <?php
                            db_input('vlTotal', 10, $It64_descr, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="" id='tdLookupTipoDepreciacao' class="field-size2">
                            <?php
                            db_ancora(
                                "Tipo Depreciação:",
                                "js_pesquisaTipoDepreciacao(true);",
                                $db_opcao,
                                "",
                                "linkLookupTipoDepreciacao"
                            );
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input(
                                'cod_depreciacao',
                                10,
                                $It64_class,
                                true,
                                'text',
                                $db_opcao,
                                "onchange='js_pesquisaTipoDepreciacao(false);'"
                            );
                            ?>
                        </td>
                        <td colspan="2" style="text-align: left;">
                            <?php
                            db_input('descr', 40, $It64_descr, true, 'text', 3);
                            ?>
                        </td>
                        <td nowrap="nowrap" title="Vida util do bem em anos.">
                            <label for="vidaUtil" class="bold">Vida Útil:</label>
                            <?php
                            db_input('vidaUtil', 10, $It44_vidautil, true, 'text', $db_opcao, '');
                            ?>
                            <span class="bold">Ano(s)</span>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset id='outros-dados'>
                <legend>Outros Dados</legend>
                <table class="form-container">
                    <tr>
                        <td>
                            <label for="t67_sequencial">Medida:</label>
                        </td>
                        <td colspan="5">
                            <?php
                            $rsBensMedida = $clBensMedida->sql_record($clBensMedida->sql_query());
                            db_selectrecord('t67_sequencial', $rsBensMedida, 'true', $db_opcao);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="t66_sequencial">Modelo:</label>
                        </td>
                        <td colspan="5">
                            <?php
                            $rsBensModelo = $clBensModelo->sql_record($clBensModelo->sql_query());
                            db_selectrecord('t66_sequencial', $rsBensModelo, 'true', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="t65_sequencial">Marca:</label>
                        </td>
                        <td colspan="5">
                            <?php
                            $rsBensMarca = $clBensMarca->sql_record($clBensMarca->sql_query());
                            db_selectrecord('t65_sequencial', $rsBensMarca, 'true', $db_opcao);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset id='observacoes'>
                <legend class='bold'>Observações</legend>
                <?php
                db_textarea('obser', 5, 98, "", true, "text", 2);
                ?>
            </fieldset>
            <?php
            db_input("iCodigoItemNota", 10, false, true, 'hidden', 3);
            ?>
        </form>
    </fieldset>

    <input id="limitePlaca" type="hidden" value="<?= $iLimitePlaca ?>"/>

    <input name="<?php echo($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
           type="button"
           id="db_opcao"
           value="<?php echo($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>"
           onclick="salvarDados();"/>

    <?php if ($db_opcao != 1) : ?>
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();"/>
    <?php endif; ?>

    <?php if ($db_opcao == 2) { ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="parent.location.href='pat1_bens001.php';"/>
        <?php
    } else {
        ?>
        <input name="importar" type="button" id="importar" value="Importação" onClick="js_pesquisa();">
        <input name="novo" type="button" id="novo" value="Novo" onclick="parent.location.href='pat1_bens001.php';"
               style="display: none;"/>
        <?php
    }
    ?>
</div>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript">
    // Essa variável é = ao parametro cfpatriplaca.t07_confplaca
    $("form1").reset();
    var iParametro;
    var dbOpcao = <?php echo $db_opcao;?>;
    var iParametroPlaca = null;
    var bemComCalculo = false;
    var lPossuiIntegracaoPatrimonial = false;
    var fotoBem = new DBInputFoto($('fotoBem'));

    /**
     * Função chamada ao iniciar
     */
    function js_carregaDadosForm(iDbOpcao) {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "carregaInclusao";
        oObject.dbOpcao = iDbOpcao;

        if(iDbOpcao != 1) {

            $("sPlaca").setAttribute('readonly', 'readonly');
            $("sPlaca").setAttribute('disabled', 'disabled');
            js_pesquisa();
            return false;
        }

        new AjaxRequest(
            url, oObject, js_retornoBusca
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando')).execute();
    }

    /**
     * Retorno do js_carregaDadosForm
     */
    function js_retornoBusca(oRetorno) {

        if(oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
        } else {

            iParametroPlaca = oRetorno.dados.parametro;
            dbOpcao = oRetorno.dbOpcao;

            if(oRetorno.dbOpcao == 1 && iParametroPlaca != 3) {
                js_Inclusao(oRetorno.dados);
            }

            if(iParametroPlaca == 3) {
                js_buscaPlacaString();
            }
        }
    }

    /**
     * Formularo em modo Inclusão
     */
    function js_Inclusao(oDados) {

        if(oDados.bloqueia) {
            $("sPlaca").style.display = "none";
        }

        switch(oDados.parametro) {

            case '1':

                $("t41_placa").setAttribute("readonly", "readonly");
                $("t41_placa").value = oDados.t41_placa;
                break;
        }

        $("impressa").innerHTML = "Não";
        if(oDados.lImpressa) {
            $("impressa").innerHTML = "Sim";
        }
    }

    function js_buscaPlacaString() {

        var oPlaca = $("placa");
        var a = new Element('a', {'class': 'ancora', href: '#', onclick: "js_pesquisaPlacaString(true)"}).update("Placa:");
        oPlaca.innerHTML = "";
        oPlaca.appendChild(a);
    }

    $("sPlaca").observe('change', js_buscaPlacaStringDigitda);

    function js_buscaPlacaStringDigitda() {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "buscaPlacaString";
        oObject.sPlaca = $F("sPlaca");
        oObject.iParametro = iParametroPlaca;

        new AjaxRequest(
            url, oObject, js_retornoPlacaStringDigitda
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando')).execute();
    }

    function js_retornoPlacaStringDigitda(oRetorno) {

        if(oRetorno.status == 1) {
            $("t41_placa").value = oRetorno.dados.t41_placa;
        }
    }

    /** ***********************************************************************************************************
     *
     */
    function js_pesquisaPlacaString() {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_bensplaca',
            'func_bensplacatext.php?funcao_js=parent.js_mostratext|t41_placa', 'Pesquisa', true);
    }

    function js_mostratext(placa) {

        db_iframe_bensplaca.hide();
        js_buscplaca(placa);
    }

    function js_buscplaca(classif) {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_bp', 'pat1_retseqplaca.php?classif=' + classif, '', false);
    }

    function js_retplaca(placa, seq) {

        $("sPlaca").value = placa;
        $("t41_placa").value = seq;
        $("t41_placa").setAttribute("readonly", "readonly");
    }


    /** ***********************************************************************************************************
     * Função de Pesquisa da classe
     */
    function js_pesquisaClasse(mostra) {

        if(mostra) {


                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_clabens',
                'func_clabens.php?funcao_js=parent.js_mostraclabens1|t64_class|t64_descr|' +
        't64_codcla|t64_benstipodepreciacao|t46_descricao|t64_vidautil|t64_valorresidual&analitica=true&classincluir=true',
                'Pesquisa', true);
        } else {

            testa = new String($F("t64_class"));

            if(testa != '' && testa != 0) {

                i = 0;
                for(i = 0; i < $("t64_class").value.length; i++) {
                    testa = testa.replace('.', '');
                }
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_clabens',
                    'func_clabens.php?pesquisa_chave=' + testa + '&funcao_js=parent.js_mostraclabens&analitica=true&classincluir=true',
                    'Pesquisa', false);
            } else {

                if(iParametro == 2 && dbOpcao == 1) {
                    $("t64_class").value = "";
                }
                $("t64_descr").value = '';
            }
        }
    }

  function js_mostraclabens(chave, erro, chave2, iCodigoDepreciacao, sDescricaoDepreciacao, iVidaUtil, iValorResidual) {

        $("t64_descr").value = chave;
        $("t64_codcla").value = chave2;
        if(erro) {

            $("t64_class").value = "";
            $("t64_class").focus();
            $("t64_codcla").value = "";
        } else {

            if(!bemComCalculo) {

                $("vidaUtil").value = iVidaUtil;
                $("perc_residual").value = iValorResidual;
                $("descr").value = sDescricaoDepreciacao;
                $("cod_depreciacao").value = iCodigoDepreciacao;
            }
            if(iParametroPlaca == 2 && dbOpcao == 1) {
                js_buscaPlaca($F("t64_class"));
            }
        }
    }

  function js_mostraclabens1(chave1, chave2, chave3, iCodigoDepreciacao, sDescricaoDepreciacao, iVidaUtil, iValorResidual) {
        $("t64_class").value = chave1;
        $("t64_descr").value = chave2;
        $("t64_codcla").value = chave3;
        if(!bemComCalculo) {

            $("vidaUtil").value = iVidaUtil;
            $("perc_residual").value = iValorResidual;
            $("perc_residual").dispatchEvent(new Event('change'));
            $("descr").value = sDescricaoDepreciacao;
            $("cod_depreciacao").value = iCodigoDepreciacao;
        }
        db_iframe_clabens.hide();
        if(iParametroPlaca == 2 && dbOpcao == 1) {
            js_buscaPlaca($F("t64_class"));
        }
    }

    function js_buscaPlaca(iClasse) {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "carregaPlacaClasse";
        oObject.iClasse = iClasse;
        oObject.iParametro = iParametroPlaca;

        new AjaxRequest(
            url, oObject, js_retornoPlaca
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando')).execute();
    }

    /**
     * Retorno do js_carregaDadosForm
     */
    function js_retornoPlaca(oRetorno) {

        if(oRetorno.status == 2) {
            alert(oRetorno.message);
        } else {

            if(oRetorno.dados.bloqueia) {
                $("sPlaca").style.display = "none";
            }

            switch(oRetorno.dados.parametro) {

                case '2':

                    var sPlacaClasse = new String($F("t64_class"));
                    $("t41_placa").setAttribute("readonly", "readonly");
                    $("t41_placa").value = sPlacaClasse + "" + oRetorno.dados.t41_placa;
                    $("sPlaca").value = sPlacaClasse
                    break;
            }

            $("impressa").innerHTML = "Não";
            if(oDados.lImpressa) {
                $("impressa").innerHTML = "Sim";
            }
        }
    }


    /** ***********************************************************************************************************
     * Função de Pesquisa do Fornecedor
     */
    function js_pesquisaFornecedor(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_forne',
                'func_nome.php?funcao_js=parent.js_mostraforne1|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {

            if(document.form1.t52_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_forne',
                    'func_nome.php?pesquisa_chave=' + document.form1.t52_numcgm.value + '&funcao_js=parent.js_mostraforne',
                    'Pesquisa', false);
            } else {
                $("z01_nome").value = '';
            }
        }
    }

    function js_mostraforne(erro, chave) {

        $("z01_nome").value = chave;
        if(erro == true) {
            $("t52_numcgm").focus();
            $("t52_numcgm").value = '';
        }
    }

    function js_mostraforne1(chave1, chave2) {

        $("t52_numcgm").value = chave1;
        $("z01_nome").value = chave2;
        db_iframe_forne.hide();
    }

    /** ***********************************************************************************************************
     * Função de Pesquisa da Aquisição
     */
    function js_pesquisaTipoAquisicao(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_aquisicao',
                'func_benstipoaquisicao.php?funcao_js=parent.js_mostraAquisicao1|t45_sequencial|t45_descricao', 'Pesquisa', true);
        } else {

            if($F("t45_sequencial") != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_aquisicao',
                    'func_benstipoaquisicao.php?pesquisa_chave=' + $F("t45_sequencial") + '&funcao_js=parent.js_mostraAquisicao',
                    'Pesquisa', false);
            } else {
                $("t45_descricao").value = '';
            }
        }
    }

    function js_mostraAquisicao(chave, erro) {

        $("t45_descricao").value = chave;
        if(erro == true) {
            $("t45_sequencial").focus();
            $("t45_sequencial").value = '';
        }
    }

    function js_mostraAquisicao1(chave1, chave2) {

        $("t45_sequencial").value = chave1;
        $("t45_descricao").value = chave2;
        db_iframe_aquisicao.hide();
    }

    /** ***********************************************************************************************************
     * Função de Pesquisa do Departamento
     */
    function js_pesquisaDepartamento(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens',
                'db_iframe_db_depart',
                'func_db_depart.php?funcao_js=parent.js_mostradb_depart1|coddepto|descrdepto', 'Pesquisa', true);
        } else {

            if(document.form1.t52_depart.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens',
                    'db_iframe_db_depart',
                    'func_db_depart.php?pesquisa_chave=' + $F("t52_depart") + '&funcao_js=parent.js_mostradb_depart',
                    'Pesquisa', false);
            } else {
                $("descrdepto").value = '';
            }
        }
    }

    function js_mostradb_depart(chave, erro) {

        $("descrdepto").value = chave;
        if(erro == true) {

            $("t52_depart").focus();
            $("t52_depart").value = '';
        } else {

            js_setOrgaoUnidade($F("t52_depart"));
            js_liberaDivisao($F("t52_depart"));
        }
    }

    function js_mostradb_depart1(chave1, chave2) {

        $("t52_depart").value = chave1;
        $("descrdepto").value = chave2;
        db_iframe_db_depart.hide();
        js_liberaDivisao(chave1);
        js_setOrgaoUnidade(chave1);
    }

    /** ***********************************************************************************************************
     * Busca Orgao/Unidade, se o Departamento tiver divisão
     */
    function js_setOrgaoUnidade(iDepartamento) {
        js_carregaOrgaoUnidade(iDepartamento);
    }

    function js_carregaOrgaoUnidade(iDepartamento) {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "buscaOrgaoUnidade";
        oObject.departamento = iDepartamento;

        new AjaxRequest(
            url, oObject, js_retornoOrgaoUnidade
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando_divisao')).execute();
    }

    function js_retornoOrgaoUnidade(oRetorno) {

        $("orgao").style.display = "none";
        $("unidade").style.display = "none";

        if(oRetorno.status == 2) {

            alert(oRetorno.message);
        } else {

            if(oRetorno.dados.libera == "t") {

                $("orgao").style.display = "table-row";
                $("unidade").style.display = "table-row";
                $("o40_descr").value = oRetorno.dados.o40_descr.urlDecode();
                $("o41_descr").value = oRetorno.dados.o41_descr.urlDecode();
            }
        }
    }

    /** ***********************************************************************************************************
     * Busca Divisão, se o Departamento tiver divisão
     */
    function js_liberaDivisao(iDepartamento, iDivisao = 0) {
        js_carregaDadosDivisao(iDepartamento, iDivisao);
    }

    function js_carregaDadosDivisao(iDepartamento, iDivisao = 0) {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "buscaDivisao";
        oObject.departamento       = iDepartamento;
        oObject.divisaoselecionada = iDivisao;

        new AjaxRequest(
            url, oObject, js_retornoBuscaDivisao
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando_divisao')).execute();
    }

    function js_retornoBuscaDivisao(oRetorno) {

        $("l-divisao").style.display = "none";
        $("c-divisao").style.display = "none";
        $("divisao").options.length = 1;

        if(oRetorno.departamento.length > 0) {

            for(var i = 0; i < oRetorno.departamento.length; i++) {

                var oOption = new Element('option', {'value': '' + oRetorno.departamento[i].t30_codigo + ''}).update(oRetorno.departamento[i].t30_descr.urlDecode());
                if(oOption.value == oRetorno.divisaoselecionada) {

                    oOption.setAttribute("selected", "selected") ;
                }

                $("divisao").appendChild(oOption);
                $("divisao").style.width = "100px";
            }
            $("l-divisao").style.display = "table-cell";
            $("c-divisao").style.display = "table-cell";
        }
    }

    /** ***********************************************************************************************************
     * Função de Pesquisa do Convenio
     */
    function js_pesquisaConvenio(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens',
                'db_iframe_benscadcedente',
                'func_benscadcedente.php?funcao_js=parent.js_mostraconvenio1|t04_sequencial|z01_nome',
                'Pesquisa', true);
        } else {

            if($F("t04_sequencial") != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_benscadcedente',
                    'func_benscadcedente.php?pesquisa_chave=' + $F("t04_sequencial") + '&funcao_js=parent.js_mostraconvenio',
                    'Pesquisa', false);
            } else {
                $("z01_nome_convenio").value = '';
            }
        }
    }

    function js_mostraconvenio(chave, erro) {

        $("z01_nome_convenio").value = chave;
        if(erro == true) {

            $("t04_sequencial").focus();
            $("t04_sequencial").value = '';
        }
    }

    function js_mostraconvenio1(chave1, chave2) {

        $("t04_sequencial").value = chave1;
        $("z01_nome_convenio").value = chave2;
        db_iframe_benscadcedente.hide();
    }

    /** ***********************************************************************************************************
     * Função de Pesquisa do SituacaoBem
     */
    function js_pesquisaSituacaoBem(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_situabens',
                'func_situabens.php?funcao_js=parent.js_mostrasituabens1|t70_situac|t70_descr', 'Pesquisa', true);
        } else {

            if($F("t56_situac") != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_situabens',
                    'func_situabens.php?pesquisa_chave=' + $F("t56_situac") + '&funcao_js=parent.js_mostrasituabens',
                    'Pesquisa', false);
            } else {
                $("t70_descr").value = '';
            }
        }
    }

    function js_mostrasituabens(chave, erro) {

        $("t70_descr").value = chave;
        if(erro == true) {

            $("t56_situac").focus();
            $("t56_situac").value = '';
        }
    }

    function js_mostrasituabens1(chave1, chave2) {

        $("t56_situac").value = chave1;
        $("t70_descr").value = chave2;
        db_iframe_situabens.hide();
    }

    /** ***********************************************************************************************************
     * Função de Pesquisa do TipoDepreciacao
     */
    function js_pesquisaTipoDepreciacao(mostra) {

        if(mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_deprecBem',
                'func_benstipodepreciacao.php?funcao_js=parent.js_mostraDepreciacao1|t46_sequencial|t46_descricao&limita=true', 'Pesquisa', true);
        } else {

            if($F("cod_depreciacao") != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_deprecBem',
                    'func_benstipodepreciacao.php?pesquisa_chave=' + $F("cod_depreciacao") + '&limita=true&funcao_js=parent.js_mostraDepreciacao',
                    'Pesquisa', false);
            } else {
                $("descr").value = '';
            }
        }
    }

    function js_mostraDepreciacao(chave, erro) {
        $("descr").value = chave;
        if(erro == true) {

            $("cod_depreciacao").focus();
            $("cod_depreciacao").value = '';
        }
    }

    function js_mostraDepreciacao1(chave1, chave2) {

        $("cod_depreciacao").value = chave1;
        $("descr").value = chave2;
        db_iframe_deprecBem.hide();
    }


    var oCampos = {
        valor_aquisicao : $("vlAquisicao"),
        perc_residual : $("perc_residual"),
        valor_residual : $("vlResidual"),
        valor_depreciavel : $("vlTotalDepreciavel"),
        valor_atual : $("vlTotal"),
        vida_util : $("vidaUtil")
    }

    oCampos.valor_aquisicao.onchange = function() {
        calculaDadosFinanceiros();
    }

    oCampos.perc_residual.onfocus = function() {
        this.value = this.value.getNumber();
    }

    oCampos.valor_aquisicao.addEventListener('change', () => {
        let valor = oCampos.valor_aquisicao.value.getNumber();
        oCampos.valor_aquisicao.value = js_formatar(valor, 'f', 2);
    })

    new DBInputValor(oCampos.valor_aquisicao);

    oCampos.perc_residual.onchange = function() {
        calculaDadosFinanceiros();
    }

    function calculaDadosFinanceiros() {

        oCampos.valor_residual.value = '';
        oCampos.valor_depreciavel.value = '';
        oCampos.valor_atual.value = '';

        var nValor = oCampos.valor_aquisicao.value.getNumber(),
            nPerc = oCampos.perc_residual.value.getNumber();

        if (isNaN(nValor) || isNaN(nPerc)) {
            return false;
        }

        var nValorResidual = (nPerc/100)*nValor;

        oCampos.valor_depreciavel.value = js_formatar(nValor-nValorResidual, 'f', 2);
        oCampos.valor_atual.value = js_formatar(nValor, 'f', 2);
        oCampos.valor_residual.value = js_formatar(nValorResidual, 'f', 2);
    }


    /** ***********************************************************************************************************
     * Salva os Dados do Formulário
     */
    function salvarDados() {

        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        var t41_placa = $F("t41_placa");
        const QUOTAS_CONSTATES = 2;

        oObject.exec = "salvar";

        if(iParametroPlaca == 2) {
            if(t41_placa.startsWith($F("sPlaca"))) {
                t41_placa = t41_placa.replace($F("sPlaca"), "");
            }
        }

        if(t41_placa.trim() == '') {

            alert(_M("patrimonial.patrimonio.db_frm_bensnovo.informe_placa_bem"));
            return false;
        }

        if(t41_placa.length > $F('limitePlaca')) {

            alert(_M("patrimonial.patrimonio.db_frm_bensnovo.limite_placa", {'limitePlaca': $F('limitePlaca')}));
            return false;
        }

        oObject.t52_bem = $F("t52_bem");
        oObject.pathImage = fotoBem.getValue();
        oObject.sPlaca = $F("sPlaca");
        oObject.t41_placa = t41_placa;
        oObject.t52_dtaqu = $F("t52_dtaqu");
        oObject.t52_descr = encodeURIComponent(tagString($F("t52_descr")));
        oObject.t64_codcla = $F("t64_codcla");
        oObject.t52_numcgm = $F("t52_numcgm");
        oObject.t45_sequencial = $F("t45_sequencial");
        oObject.t52_depart = $F("t52_depart");
        oObject.divisao = $F("divisao");
        oObject.t04_sequencial = $F("t04_sequencial");
        oObject.t56_situac = $F("t56_situac");
        oObject.vlAquisicao = $F("vlAquisicao").getNumber();
        oObject.vlResidual = $F("vlResidual").getNumber();
        oObject.vlTotal = $F("vlTotal").getNumber();//valor depreciavel
        oObject.cod_depreciacao = $F("cod_depreciacao");
        oObject.vidaUtil = $F("vidaUtil").getNumber();
        oObject.t67_sequencial = $F("t67_sequencial");
        oObject.t66_sequencial = $F("t66_sequencial");
        oObject.t65_sequencial = $F("t65_sequencial");
        oObject.iCodigoItemNota = $F("iCodigoItemNota");
        oObject.t52_processo = encodeURIComponent(tagString($F("t52_processo")));
        oObject.obser = encodeURIComponent(tagString($F("obser")));
        oObject.acao = encodeURIComponent(tagString("Incluir"));
        oObject.nameImage = '';
        if (oObject.pathImage) {
            if (fotoBem.inputFileElement.files && fotoBem.inputFileElement.files.length > 0) {
                oObject.nameImage = fotoBem.inputFileElement.files[0].name;
            } else {
                oObject.nameImage = null; // Mantém vazio se não há arquivo novo
            }
        }

        if($F("db_opcao") == "Alterar") {
            oObject.acao = encodeURIComponent(tagString("Alterar"));
        }

        let inclusao = $F("t52_dtinclusao").split('/');
        let dataInclusao = inclusao[2] + '-' + inclusao[1] + '-' + inclusao[0];

        var aquisicao = $F("t52_dtaqu").split('/');
        var dataAquisicao = aquisicao[2] + '-' + aquisicao[1] + '-' + aquisicao[0];

        if (new Date(dataAquisicao) > new Date(dataInclusao)) {
            alert("Data de aquisição maior que a data de inclusão");
            return;
        }

        if (oObject.vlResidual == 0 && oObject.cod_depreciacao == QUOTAS_CONSTATES) {
            alert("Valor residual (%) ? obrigatório para Tipo de depreciação QUOTAS CONSTANTES");
            return;
        }

        if($F("obser").trim() == "") {
            alert("Campo observações obrigatório.");
            return false;
        }

        $('db_opcao').disabled = true;
        js_divCarregando(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando'),'msgBox');
        var objAjax   = new Ajax.Request (url,{
                method:'post',
                parameters:'json='+Object.toJSON(oObject),
                onComplete:js_retornoSalvar
            }
        );
    }

    /**
     * Retorno do js_carregaDadosForm
     */

    function js_retornoSalvar(oJson) {

        $('db_opcao').disabled = false;
        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oJson.responseText);

        if(oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode().replace(/\\n/g, "\n"));

        } else {

            $('t41_placa').disabled = true;
            alert(_M('patrimonial.patrimonio.db_frm_bensnovo.bem_salvo'));
            $('t52_bem').value = oRetorno.dados.t52_bem;
            $('db_opcao').value = 'Alterar';
            this.importar.style.display = 'none';
            $("novo").style.display = "";
            js_liberarAbas();
        }
    }

    function js_pesquisa() {

        var url = "func_bens.php?funcao_js=parent.js_preenchepesquisa|t52_bem&lObrigaConta=false";
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_bens', 'db_iframe_bens', url, 'Pesquisa', true);
    }

    function js_preenchepesquisa(t52_bem) {

        db_iframe_bens.hide();
        js_pesquisaBem(t52_bem);
    }

    /**
     * Função chamada ao iniciar
     */
    function js_pesquisaBem(iCodigoBem) {
        js_limpaCamposAlteracao();
        var url = 'pat1_bensnovo.RPC.php';
        var oObject = new Object();
        oObject.exec = "buscaBem";
        oObject.dbOpcao = dbOpcao;
        oObject.iCodigoBem = iCodigoBem;

        new AjaxRequest(
            url, oObject, js_retornoBuscaBem
        ).setMessage(_M('patrimonial.patrimonio.db_frm_bensnovo.buscando')).execute();
    }

    function js_limpaCamposAlteracao() {

        $("perc_residual").removeAttribute("hidden");
        $("cod_depreciacao").removeAttribute("readonly");
        $("cod_depreciacao").removeAttribute("disabled");
        $("vidaUtil").removeAttribute("readonly");
        $("vidaUtil").removeAttribute("disabled");

        $("cod_depreciacao").style.backgroundColor = "#FFFFFF";
        $("vidaUtil").style.backgroundColor = "#FFFFFF";

    }

    var sLinkLookupTipoDepreciacao = $('tdLookupTipoDepreciacao').innerHTML;

    /**
     * Retorno do js_carregaDadosForm
     */
    function js_retornoBuscaBem(oRetorno) {

        if(oRetorno.status == 2) {

            alert(oRetorno.message);
            return false;
        }

        lPossuiIntegracaoPatrimonial = oRetorno.lPossuiIntegracaoPatrimonial;

        /**
         * Se não tivermos no modo inclusão, ele traz todos os valores.
         */
        if(dbOpcao != 1) {

            var iVidaUtil = "0";

            $("t41_placa").setAttribute('readonly', 'readonly');
            $("sPlaca").setAttribute('readonly', 'readonly');
            $("vlResidual").setAttribute('readonly', 'readonly');
            $("sPlaca").style.display = "none";
            $("t41_placa").value = oRetorno.dados.t52_ident;
            $("vlAquisicao").value = oRetorno.dados.vlAquisicao;
            $("vlResidual").value = oRetorno.dados.vlResidual;
            $("vlTotal").value = oRetorno.dados.vlTotal;
            $("vlTotalDepreciavel").value = oRetorno.dados.vlTotalDepreciavel;
            $("t52_bem").value = oRetorno.dados.t52_bem;
            $("t52_dtaqu").value = oRetorno.dados.t52_dtaqu;
            $("t52_dtinclusao").value = oRetorno.dados.t52_dtinclusao;

            // Valida a Regra do Tipo de configuracao = 2 classificacao + sequencial
            if(oRetorno.dados.iParametroPlaca == 2) {
                $("sPlaca").value = oRetorno.dados.sPlaca;
            }

            if(oRetorno.dados.vidaUtil != '') {
                iVidaUtil = oRetorno.dados.vidaUtil;
            }

            $("vidaUtil").value = iVidaUtil;

            if(oRetorno.dados.valorDepreciado > 0) {
                js_hasDepreciacaoBloqueiaCampos();
            }

        }

        $("t52_descr").value = oRetorno.dados.t52_descr.urlDecode();
        $("t64_codcla").value = oRetorno.dados.t64_codcla;
        $("t64_class").value = oRetorno.dados.t64_class.urlDecode();
        $("t64_descr").value = oRetorno.dados.t64_descr.urlDecode();
        $("t52_numcgm").value = oRetorno.dados.t52_numcgm;
        $("z01_nome").value = oRetorno.dados.z01_nome.urlDecode();
        $("t45_sequencial").value = oRetorno.dados.t45_sequencial;
        $("t45_descricao").value = oRetorno.dados.t45_descricao.urlDecode();
        $("t52_depart").value = oRetorno.dados.t52_depart;
        $("descrdepto").value = oRetorno.dados.descrdepto.urlDecode();
        $("t04_sequencial").value = oRetorno.dados.t04_sequencial;
        $("z01_nome_convenio").value = oRetorno.dados.z01_nome_convenio.urlDecode();
        $("t56_situac").value = oRetorno.dados.t56_situac;
        $("t70_descr").value = oRetorno.dados.t70_descr.urlDecode();
        $("cod_depreciacao").value = oRetorno.dados.cod_depreciacao;
        $("descr").value = oRetorno.dados.descr.urlDecode();
        $("t67_sequencial").value = oRetorno.dados.t67_sequencial;
        $("t66_sequencial").value = oRetorno.dados.t66_sequencial;
        $("t65_sequencial").value = oRetorno.dados.t65_sequencial;
        $("obser").value = oRetorno.dados.obser.urlDecode();
        $("t52_processo").value = oRetorno.dados.t52_processo.urlDecode();
        fotoBem.setValue(oRetorno.imagePath);

        js_ProcCod_t66_sequencial('t66_sequencial', 't66_sequencialdescr');
        js_ProcCod_t67_sequencial('t67_sequencial', 't67_sequencialdescr');
        js_ProcCod_t65_sequencial('t65_sequencial', 't65_sequencialdescr');

        $("l-divisao").style.display = "none";
        $("c-divisao").style.display = "none";
        $("divisao").setValue(0);
        $("divisao").options.length = 0;

        if(oRetorno.dados.divisao != "") {

            js_liberaDivisao(oRetorno.dados.t52_depart, oRetorno.dados.divisao);
            js_setOrgaoUnidade(oRetorno.dados.t52_depart);
            $("l-divisao").style.display = "table-cell";
            $("c-divisao").style.display = "table-cell";
            $("divisao").setValue(oRetorno.dados.divisao);
        }

        js_liberarAbas();
        bemComCalculo = oRetorno.dados.bemComCalculo;
        js_controlaDadosFinanceiros(oRetorno.dados.bemComCalculo);

    }

    function js_calculaValorTotal() {

        var vlAquisicao = new Number($F("vlAquisicao"));
        var vlResidual = new Number($F("vlResidual"));

        if(vlResidual > vlAquisicao) {

            alert(_M('patrimonial.patrimonio.db_frm_bensnovo.residual_maior_que_aquisicao'));
            $("vlResidual").value = "";
            $("vlAquisicao").focus();
            return false;
        } else {

            $("vlTotalDepreciavel").value = (vlAquisicao - vlResidual).toFixed(2);
            $("vlTotal").value = (vlAquisicao);
        }
    }


    function js_liberarAbas() {

        var iCodigoBem = $F('t52_bem');
        parent.document.formaba.bensimoveis.disabled = false;
        parent.document.formaba.bensmater.disabled = false;
        (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_bensimoveis.location.href = 'pat1_bensimoveis001.php?db_opcaoal=22&t54_codbem=' + iCodigoBem;
        (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_bensmater.location.href = 'pat1_bensmater001.php?db_opcaoal=22&t53_codbem=' + iCodigoBem;

        /**
         * Seleciona a ultima aba quando for uma inclusão
         */
        if(+dbOpcao == 1) {

            setTimeout(function() {
                parent.document.formaba.bensmater.click();
            }, 1500);
        }
    }

    function js_controlaDadosFinanceiros(lBloquear) {

        //var sCor = 'white';
        $('tdLookupTipoDepreciacao').innerHTML = sLinkLookupTipoDepreciacao;
        if(lBloquear) {
            $('tdLookupTipoDepreciacao').innerHTML = "<b>" + $('linkLookupTipoDepreciacao').innerHTML + "<b>";
            sCor = '#DEB887';
        }

        $("vlAquisicao").classList.remove('readonly');
        $("vlResidual").classList.remove('readonly');
        $("perc_residual").classList.remove('hidden');

        /**
         * Caso seja alteração bloqueia os campos vlr aquisição e vlr residual
         */
        if(dbOpcao != 1 && lPossuiIntegracaoPatrimonial) {

            $("vlAquisicao").disabled = true;
            $("vlResidual").disabled = true;

            $("vlAquisicao").classList.add('readonly');
            $("vlResidual").classList.add('readonly');

        } else {

            $("vlAquisicao").disabled = lBloquear;
            $("vlAquisicao").style.backgroundColor = sCor;
            $("vlResidual").disabled = lBloquear;
            $("vlResidual").style.backgroundColor = sCor;
            $("perc_residual").disabled = lBloquear;
            $("perc_residual").style.backgroundColor = sCor;
        }

        $("vlTotal").disabled = lBloquear;
        $("vlTotal").style.backgroundColor = '#DEB887';
        $("cod_depreciacao").disabled = lBloquear;
        $("descr").disabled = lBloquear;
        $("vidaUtil").disabled = lBloquear;
        $('t52_dtaqu').style.backgroundColor = sCor;
        $('t52_dtaqu').disabled = lBloquear;
    }

    /**
     * Bloqueia campos se a depreciação já foi inicializada
     */
    function js_hasDepreciacaoBloqueiaCampos() {

        $("t52_dtaqu").setAttribute("readonly", "readonly");
        $("vlAquisicao").setAttribute("readonly", "readonly");
        $("vlResidual").setAttribute("readonly", "readonly");
        $("perc_residual").setAttribute("hidden", "hidden");
        $("cod_depreciacao").setAttribute("readonly", "readonly");
        $("vidaUtil").setAttribute("readonly", "readonly");

        form1.dtjs_t52_dtaqu.removeAttribute("onclick");
        form1.dtjs_t52_dtaqu.setAttribute("disabled", "disabled");

        $("t52_dtaqu").style.backgroundColor = "#DEB887";
        $("vlAquisicao").style.backgroundColor = "#DEB887";
        $("cod_depreciacao").style.backgroundColor = "#DEB887";
        $("vidaUtil").style.backgroundColor = "#DEB887";
        $("vlResidual").style.backgroundColor = "#DEB887";

        if($("linkLookupTipoDepreciacao") != null) {
            $("linkLookupTipoDepreciacao").removeAttribute("class");
            $("linkLookupTipoDepreciacao").style.color = "#000";
            $("linkLookupTipoDepreciacao").style.textDecoration = "none";
            $("linkLookupTipoDepreciacao").style.cursor = "default";
            $("linkLookupTipoDepreciacao").setAttribute("onClick", "return false;");
        }
    }

    // Configura Form
    var oOutrosDados = new DBToogle($('outros-dados'), false);
    var oObservacoes = new DBToogle($('observacoes'), false);
    $("t67_sequencial").style.width = "50px";
    $("t66_sequencial").style.width = "50px";
    $("t65_sequencial").style.width = "50px";
    $("t67_sequencialdescr").style.width = "150px";
    $("t66_sequencialdescr").style.width = "150px";
    $("t65_sequencialdescr").style.width = "150px";

    $("t52_descr").addClassName('field-size-max');
    $("o40_descr").addClassName('field-size-max');
    $("o41_descr").addClassName('field-size-max');
    $("descr").addClassName('field-size-max');

    $('removerFoto').observe('click', function() {
        fotoBem.setValue('');
    });
</script>
