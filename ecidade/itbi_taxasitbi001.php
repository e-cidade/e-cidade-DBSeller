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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

db_postmemory($_GET);

$clrotulo = new rotulocampo;
$clrotulo->label("it36_sequencial");
$clrotulo->label("it36_descricao");
$clrotulo->label("it36_imovelurbano");
$clrotulo->label("it36_imovelrural");
$clrotulo->label("it36_imovelurbanopleno");

$clrotulo->label("ar44_sequencial");
$clrotulo->label("ar44_descricao");

?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <script language="javascript" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <style>
            span > label {
                position: relative;
                top: -5px;
            }

            #ctnGridTaxas {
                width: 1000px;
            }
        </style>
    </head>
    <body>
        <input type="hidden" name="db_opcao" id="db_opcao" value="<?= $db_opcao ?>">
        <div style="margin-top: 15px;" id = 'ctnAbas'></div>
        <div id="abaTipo" class="container">
            <form name="formTipo" method="post">
                <fieldset>
                    <legend><strong>Tipo</strong></legend>
                    <table class="form-container">
                        <tr>
                            <td title="<?= @$Tit36_sequencial ?>" style="width: 70px;">
                                <?= @$Sit36_sequencial ?>
                            </td>
                            <td>
                                <?php
                                db_input("it36_sequencial", 5, @$Iit36_sequencial, "it36_sequencial", "text", 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td title="<?= @$Tit36_descricao ?>" style="width: 70px;">
                                <?= @$Sit36_descricao ?>
                            </td>
                            <td>
                                <?php
                                db_input("it36_descricao", 50, @$Iit36_descricao, "it36_descricao", "text", 1);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Tipo de ITBI:
                            </td>
                            <td>
                                <span>
                                    <?php
                                    db_input("it36_imovelurbano", 50, @$Iit36_imovelurbano, "it36_imovelurbano", "radio", 1, "onclick='js_desmarcarRadio(this)'");
                                    ?>
                                    <label for="it36_imovelurbano"><?= @$Sit36_imovelurbano ?></label>
                                </span>
                                <span>
                                    <?php
                                    db_input("it36_imovelrural", 50, @$Iit36_imovelrural, "it36_imovelrural", "radio", 1, "onclick='js_desmarcarRadio(this)'");
                                    ?>
                                    <label for="it36_imovelrural"><?= @$Sit36_imovelrural ?></label>
                                </span>
                                <span>
                                    <?php
                                    db_input("it36_imovelurbanopleno", 50, @$Iit36_imovelurbanopleno, "it36_imovelurbanopleno", "radio", 1, "onclick='js_desmarcarRadio(this)'");
                                    ?>
                                    <label for="it36_imovelurbanopleno"><?= @$Sit36_imovelurbanopleno ?></label>
                                </span>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <input name="salvarTipo" id="salvarTipo" type="button" onclick="js_salvarTipo();" value="Salvar">
                <input name="pesquisarTipo" id="pesquisarTipo" type="button" onclick="js_pesquisaTipo(true);" value="Pesquisar" style="display: none;">
            </form>
        </div>
        <div id="abaTaxas" class="container" style="width: 700px;">
            <form name="form1" method="post">
                <input type="hidden" name="departamentos" value="<?= db_getsession("DB_coddepto") ?>">
                <fieldset>
                    <legend>Taxas</legend>
                    <table class="form-container">
                        <tr>
                            <td>
                                    <?php
                                    db_ancora("Taxa", "js_pesquisaTaxa(true);", 4);
                                    ?>
                            </td>
                            <td>
                                    <?php
                                    db_input("ar44_sequencial", 5, @$Iar44_sequencial, true, "text", 1, "onchange='js_pesquisaTaxa(false);'", "", "white");
                                    db_input("ar44_descricao", 70, false, true, "text", 5, "", "", "", "width: 260px;");
                                    ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br>
                                <div id="ctnGridTaxas"></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <input name="salvar" id="salvar" type="button" onclick="js_salvarTaxa();" value="Salvar">
            </form>
        </div>
        <?php db_menu(); ?>
    </body>
</html>
<script>
    var aTaxas = [];
    const db_opcao = document.getElementById("db_opcao").value;
    const oDBAba = new DBAbas($('ctnAbas'));
    const oAbaTipos = oDBAba.adicionarAba("Tipos", $('abaTipo'));
    const oAbaTaxas = oDBAba.adicionarAba("Taxas", $('abaTaxas'));
    oAbaTaxas.bloquear();

    if (db_opcao == 2) {
        document.getElementById("pesquisarTipo").show();
        js_pesquisaTipo(true);
    }

    function js_salvarTipo()
    {
        if (js_verificaCamposTipo()) {
            return false;
        }

        const obj = document.formTipo;

		var oParam = new Object();
        oParam.executa = "salvarTipo";
        oParam.it36_sequencial = obj.it36_sequencial.value;
        oParam.it36_descricao = obj.it36_descricao.value;
        oParam.it36_imovelurbano = obj.it36_imovelurbano.checked;
        oParam.it36_imovelrural = obj.it36_imovelrural.checked;
        oParam.it36_imovelurbanopleno = obj.it36_imovelurbanopleno.checked;

		new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function(oRetorno) {
            alert(oRetorno.mensagem);

            if (oRetorno.erro) {
                return;
            }

            document.formTipo.it36_sequencial.value = oRetorno.it36_sequencial;

            document.getElementById("it36_sequencial").setAttribute("isMandatory", "true");

            oAbaTaxas.desbloquear();

            const AbaTaxas = document.getElementById("Taxas");
            AbaTaxas.dispatchEvent(new Event("click"));
        }).execute();
    }

    function js_pesquisaTipo(mostra)
    {
        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxasitbi','func_taxasitbi.php?funcao_js=parent.js_mostraTipoItbi|it36_sequencial|it36_descricao','Pesquisa',true);
        }else{
            if (document.formTipo.it36_sequencial.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxasitbi','func_taxasitbi.php?pesquisa_chave='+document.formTipo.it36_sequencial.value+'&funcao_js=parent.js_mostraTipoItbi1','Pesquisa',false);
            } else {
                document.formTipo.it36_sequencial.value = '';
            }
        }
    }

    function js_mostraTipoItbi(chave1,chave2)
    {
        document.formTipo.it36_sequencial.value = chave1;
        document.formTipo.it36_descricao.value = chave2;
        db_iframe_taxasitbi.hide();

        js_buscarTipo();
    }

    function js_mostraTipoItbi1(chave,erro)
    {
        document.formTipo.it36_descricao.value = chave;

        if(erro==true){
            document.formTipo.it36_sequencial.focus();
            document.formTipo.it36_sequencial.value = '';
            document.formTipo.it36_descricao.value = '';
        } else {
            js_buscarTipo();
        }
    }

    function js_pesquisaTaxa(mostra)
    {
        const departamentos = document.form1.departamentos.value;

        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxaslancadas','func_taxaslancadas.php?funcao_js=parent.js_mostraTaxaLancada|ar44_sequencial|ar44_descricao|ar44_tipo&receita=true&departamentos='+departamentos,'Pesquisa',true);
        }else{
            if (document.form1.ar44_sequencial.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxaslancadas','func_taxaslancadas.php?pesquisa_chave='+document.form1.ar44_sequencial.value+'&funcao_js=parent.js_mostraTaxaLancada1&receita=true&departamentos='+departamentos,'Pesquisa',false);
            } else {
                document.form1.ar44_sequencial.value = '';
            }
        }
    }

    function js_mostraTaxaLancada(chave1,chave2, tipo)
    {
        document.form1.ar44_sequencial.value = chave1;
        document.form1.ar44_descricao.value = chave2;
        db_iframe_taxaslancadas.hide();

        if (js_verificaDadosArray(aTaxas, chave1)) {
            document.form1.ar44_sequencial.value = "";
            document.form1.ar44_descricao.value = "";
            document.form1.ar44_sequencial.focus();
            return false;
        }

        aTaxas.push({"codigo" : chave1, "descricao" : chave2, "calculaSobre" : 1, "tipo" : tipo});

        js_listaTaxas();
    }

    function js_mostraTaxaLancada1(chave, erro, tipo)
    {
        document.form1.ar44_descricao.value = chave;

        if(erro==true){
            document.form1.ar44_sequencial.focus();
            document.form1.ar44_sequencial.value = '';
            document.form1.ar44_descricao.value = '';
        } else {

            if (js_verificaDadosArray(aTaxas, document.form1.ar44_sequencial.value)) {
                document.form1.ar44_sequencial.value = "";
                document.form1.ar44_descricao.value = "";
                document.form1.ar44_sequencial.focus();
                return false;
            }

            aTaxas.push({"codigo" : document.form1.ar44_sequencial.value, "descricao" : chave, "calculaSobre" : 1, "tipo" : tipo});

            setTimeout(() => {
                js_listaTaxas();
            }, 500);
        }
    }

    var oGridTaxas = new DBGrid('gridTaxas');
    var aHeaders   = ["Código", "Descrição", "Calcula Sobre", "Faixa", "Ação"];
    var aCellWidth = ["10%", "50%", "22%", "32%", "6%"];
    var aCellAlign = ["center", "left", "center", "center", "center"];

    oGridTaxas.nameInstance = 'oGridTaxas';
    oGridTaxas.setCellWidth(aCellWidth);
    oGridTaxas.setCellAlign(aCellAlign);
    oGridTaxas.setHeader(aHeaders);
    oGridTaxas.show($('ctnGridTaxas'));

    function js_listaTaxas()
    {
        oGridTaxas.clearAll(true);

        aTaxas.forEach(function (oTaxa){
            var aLinha = [];
            aLinha.push(oTaxa.codigo);
            aLinha.push(oTaxa.descricao);

            if (oTaxa.tipo == "2" || oTaxa.tipo == "3") {
                var oSelect = document.createElement("select");
                oSelect.setAttribute("id", "idTaxa_"+oTaxa.codigo);
                oSelect.setAttribute("onchange", "js_alteraCalculaSobre("+oTaxa.codigo+", this.value);");

                const optionVenalTerreno = document.createElement("option");
                optionVenalTerreno.setAttribute("value", "1");
                optionVenalTerreno.innerHTML = "Valor do Terreno";
                if (oTaxa.calculaSobre == 1) {
                    optionVenalTerreno.setAttribute("selected", "selected");
                }

                oSelect.appendChild(optionVenalTerreno);

                const optionVenalConstrucao = document.createElement("option");
                optionVenalConstrucao.setAttribute("value", "2");
                optionVenalConstrucao.innerHTML = "Valor da Construção";
                if (oTaxa.calculaSobre == 2) {
                    optionVenalConstrucao.setAttribute("selected", "selected");
                }

                oSelect.appendChild(optionVenalConstrucao);

                const optionAmbos = document.createElement("option");
                optionAmbos.setAttribute("value", "3");
                optionAmbos.innerHTML = "Ambos";
                if (oTaxa.calculaSobre == 3) {
                    optionAmbos.setAttribute("selected", "selected");
                }

                oSelect.appendChild(optionAmbos);

                aLinha.push(oSelect.outerHTML);
            } else {
                aLinha.push("Valor Fixo");
            }

            if (oTaxa.tipo == "3") {
                const inputInicio = document.createElement("input");
                inputInicio.setAttribute("id", "idFaixaIni_"+oTaxa.codigo);
                inputInicio.setAttribute("size", "13");
                inputInicio.setAttribute("value", ((oTaxa.inicioFaixa != "" && oTaxa.inicioFaixa != undefined) ? oTaxa.inicioFaixa : "0"));
                inputInicio.setAttribute("onchange", "js_alteraFaixa("+oTaxa.codigo+", false);");
                inputInicio.setAttribute("onkeyup", "jsFormataMoeda(this);");

                const inputFim = document.createElement("input");
                inputFim.setAttribute("id", "idFaixaFim_"+oTaxa.codigo);
                inputFim.setAttribute("size", "13");
                inputFim.setAttribute("value", ((oTaxa.fimFaixa != "" && oTaxa.fimFaixa != undefined) ? oTaxa.fimFaixa : "0"));
                inputFim.setAttribute("onchange", "js_alteraFaixa("+oTaxa.codigo+", true);");
                inputFim.setAttribute("onkeyup", "jsFormataMoeda(this);");

                aLinha.push(inputInicio.outerHTML+" à "+inputFim.outerHTML);
            } else {
                aLinha.push("");
            }

            var oBtnAlterar = document.createElement('input');
            oBtnAlterar.setAttribute("value", "R");
            oBtnAlterar.setAttribute("type", "button");
            oBtnAlterar.setAttribute("id", "btnRemover_" + oTaxa.codigo);
            oBtnAlterar.setAttribute("onclick", "js_removerTaxa("+oTaxa.codigo+")");

            aLinha.push(oBtnAlterar.outerHTML);

            oGridTaxas.addRow(aLinha);
        });

        oGridTaxas.renderRows();

        document.form1.ar44_sequencial.value = '';
        document.form1.ar44_descricao.value = '';

        document.form1.ar44_sequencial.focus();
    }

    function js_alteraCalculaSobre(taxa, valor)
    {
        aTaxas.forEach(function (oTaxa, key){
            if (oTaxa.codigo == taxa) {
                aTaxas[key].calculaSobre = valor;
            }
        });
    }

    function js_alteraFaixa(iTaxa, isFaixaFim)
    {
        aTaxas.forEach(function (oTaxa, key){
            if (oTaxa.codigo == iTaxa) {

                var bAdicionaFaixa = true;
                const campoValorInicio = document.getElementById(`idFaixaIni_${iTaxa}`);
                const campoValorFim = document.getElementById(`idFaixaFim_${iTaxa}`);

                const valorInicio = parseFloat(js_removeMascaraMoeda(campoValorInicio.value));
                const valorFim = parseFloat(js_removeMascaraMoeda(campoValorFim.value));

                if (isFaixaFim) {
                    if (valorFim < valorInicio) {
                        alert("Valor final não pode ser menor que o inicial.");
                        campoValorInicio.value = 0;
                        campoValorFim.value = 0;
                        return false;
                    } else {
                        bAdicionaFaixa = true;
                    }
                } else {
                    bAdicionaFaixa = true;
                }

                if (bAdicionaFaixa) {
                    aTaxas[key].faixa = {valorInicio, valorFim};
                }
            }
        });
    }

    function js_removeMascaraMoeda(sValor)
    {
        return sValor.replaceAll(".", "").replace(",", ".");
    }

    function js_removerTaxa(codigo)
    {
        aTaxas.forEach(function (oTaxa, key){
            if (oTaxa.codigo == codigo) {
                aTaxas.splice(key, 1);
                return;
            }
        });

        js_listaTaxas();
    }

    function js_verificaDadosArray(objeto, codigo)
    {
        var erro = false;

        objeto.forEach(function (elemento) {
            if (elemento.codigo == codigo && !erro) {
                alert("Item: "+elemento.codigo+" - "+elemento.descricao+" já incluso na lista.");
                erro = true;
            }
        });

        return erro;
    }

    function js_verificaCamposTipo()
    {
        const obj = document.formTipo;

        if (db_opcao == 2) {
            if (obj.it36_imovelurbano.value = "") {
                alert("Selecione um tipo.");
                return true;
            }
        }

        if (obj.it36_descricao.value == "") {
            alert("Campo Descrição não informado.");
            return true;
        }

        if (obj.it36_imovelurbano.checked == false && obj.it36_imovelrural.checked == false && obj.it36_imovelurbanopleno.checked == false) {
            alert("Selecione um tipo de ITBI.");
            return true;
        }
    }

    function js_buscarTipo()
    {
        const obj = document.formTipo;

        var oParam = new Object();
        oParam.executa = "buscarTipo";
        oParam.it36_sequencial = obj.it36_sequencial.value;

        new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
            const oTipo = oRetorno.oTipo;

            obj.it36_imovelurbano.checked = (oTipo.it36_imovelurbano == "t" ? true :  false);
            obj.it36_imovelrural.checked = (oTipo.it36_imovelrural == "t" ? true :  false);
            obj.it36_imovelurbanopleno.checked = (oTipo.it36_imovelurbanopleno == "t" ? true :  false);

            aTaxas = oRetorno.aTaxas;

            js_listaTaxas();

            oAbaTaxas.desbloquear();
        }).execute();
    }

    function js_salvarTaxa()
    {
        const obj = document.formTipo;

        var oParam = new Object();
        oParam.executa = "salvarTaxas";
        oParam.it36_sequencial = obj.it36_sequencial.value;
        oParam.taxas = JSON.stringify(aTaxas);

        new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function (oRetorno) {
            alert(oRetorno.mensagem);

            if (oRetorno.erro) {
                return;
            }
        }).execute();
    }

    function js_desmarcarRadio(oCampo)
    {
        const aCampos = document.querySelectorAll("input[type='radio']");

        aCampos.forEach(function (oCampo1) {
            if (oCampo.name != oCampo1.name) {
                oCampo1.checked = false;
            }
        });
    }
</script>
