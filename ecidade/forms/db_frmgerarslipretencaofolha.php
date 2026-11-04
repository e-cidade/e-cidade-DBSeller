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
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("k02_codigo");
$clrotulo->label("k02_drecei");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e48_cgm");

require_once(modification("libs/db_utils.php"));
$clsaltes = new cl_saltes;
require_once(modification("libs/JSON.php"));
// seleciona conta a creditar
$db_opcao = 1;

$dao = new cl_caiparametro();
$sql = $dao->sql_query_file(db_getsession("DB_instit"), 'k29_gerarslipautomaticoreceitaretencao');
$rs = db_query($sql);

$msg = "O Sistema esta configurado para gerar slips automaticamente. ";
$style = "display: none";
$desabilitaPesquisa = '';
if (!$rs) {
    $msg = "Acesse: DB:FINANCEIRO > Tesouraria > Procedimentos > Parâmetros > Financeiro e configure os parâmetros ";
}

if (db_utils::fieldsMemory($rs, 0)->k29_gerarslipautomaticoreceitaretencao != 0) {
    $style = "";
    $db_opcao = 3;
    $desabilitaPesquisa = 'disabled';
}

/* [Extensão] - Filtro da Despesa */

echo "<script>\n";

if ($folha == 1) {
    echo "isFolha=true\n";
} else {
    echo "isFolha=false\n";
}
echo "</script>\n";
?>
<style type="text/css">
    .primeiraColuna {
        width: 110px;
    }
</style>
<div style="<?=$style?>" class="alert alert-danger text-left" role="alert">
    <?=$msg?>
</div>

<form class="container" name="form1" method="post" action="">

    <fieldset>
        <legend>Filtros</legend>
        <table id='folha' style='display: none' class="form-container">
            <tr>
                <td align="left" nowrap class="primeiraColuna">
                    <b>Ano / Mês :</b>
                </td>
                <td>
                    <?php
                    $anofolha = db_anofolha();
                    db_input('anofolha', 4, $IDBtxt23, true, 'text', 2, "onChange='js_validaTipoPonto()'");
                    ?>
                    &nbsp;/&nbsp;
                    <?php
                    $mesfolha = db_mesfolha();
                    db_input('mesfolha', 2, $IDBtxt25, true, 'text', 2, "onChange='js_validaTipoPonto()'");
                    ?>
                </td>
            </tr>
            <tr>
                <td class="primeiraColuna">
                    <b>Ponto:</b>
                </td>
                <td>
                    <?php
                    $aSigla = array("r14" => "Salário",
                        "r48" => "Complementar",
                        "r35" => "13o. Salário",
                        "r20" => "Rescisão",
                        "r22" => "Adiantamento");

                    db_select('ponto', $aSigla, true, 4, "onChange='js_validaTipoPonto()'");
                    ?>
                </td>
            </tr>
            <tr>
                <td class="primeiraColuna">
                    <b>Tipo:</b>
                </td>
                <td>
                    <?php
                    $aTipos = [
                        "1" => "Salário",
                        "2" => "Previdência",
                        "3" => "FGTS",
                    ];
                    db_select('tipo', $aTipos, true, 4);
                    ?>
                </td>
            </tr>
            <tr id='linhaComplementar' style='display:none'>
            </tr>

            <tr>
                <td align="left" nowrap class="primeiraColuna">
                    <?php
                    db_ancora(@$Lk02_codigo, "js_pesquisatabrec(true);", 4);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('k02_codigo', 10, $Ik02_codigo, true, 'text', 2, "onchange='js_pesquisatabrec(false);'");
                    db_input('k02_drecei', 40, $Ik02_drecei, true, 'text', 3);
                    ?>
                </td>
            </tr>
        </table>

        <table style='display: none' id='fornecedores' class="form-container">
            <tr>
                <td class="primeiraColuna">
                    <b>Data Inicial:</b>
                </td>
                <td>
                    <?php
                    db_inputdata("datainicial", null, null, null, true, "text", $db_opcao);
                    ?>
                </td>
                <td class="primeiraColuna">
                    <b>Data Final:</b>
                </td>
                <td>
                    <?php
                    db_inputdata("datafinal", null, null, null, true, "text", $db_opcao);
                    ?>
                </td>
            </tr>
        </table>
        <table class="form-container">
            <td>
                <?php
                db_ancora("<b>Fonte de Recurso:</b>", "js_pesquisaRecurso(true)", $db_opcao);
                ?>
            </td>
            <td>
                <?php
                db_input("codigosRecursos", 8, null, false, "hidden", 3);
                db_input("gestao", 10, null, true, "text", $db_opcao, "onchange='js_pesquisaRecurso(false);'");
                db_input("o15_recurso", 8, null, true, "text", 3);
                db_input("descricao", 40, null, true, "text", 3);
                ?>
            </td>
            <tr>
                <td nowrap title="<?= @$Tz01_numcgm ?>" class="primeiraColuna">
                    <?php
                    db_ancora("<b>Credor:</b>", "js_pesquisaz01_numcgm(true);", $db_opcao);
                    ?>
                </td>
                <td colspan='4' nowrap>
                    <?php
                    db_input('z01_numcgm', 10, $Iz01_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisaz01_numcgm(false);'");
                    db_input('z01_nome', 48, $Iz01_nome, true, 'text', 3, '')
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="Número da OP" class="primeiraColuna">
                    <b>Ordem Pag:</b>
                </td>
                <td colspan='4' nowrap>
                    <?php
                    $Ie50_codord = empty($Ie50_codord) ? 1 : $Ie50_codord;
                    db_input('e50_codord', 10, $Ie50_codord, true, 'text', $db_opcao, "");
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input type='button' value='Pesquisar' <?= $desabilitaPesquisa ?> onclick="js_getMovimentos()">
</form>

<fieldset class="subcontainer" style='width: 80%'>
    <legend>Valores a serem transferidos</legend>
    <div id='gridSlip'></div>
    <input type='button' value='Processar' onclick="js_gerarsLip();">
</fieldset>
<div style='position:absolute;top: 200px; left:15px;
            border:1px solid black;
            width:300px;
            text-align: left;
            padding:3px;
            background-color: #FFFFCC;
            display:none;' id='ajudaItem'>

</div>

<script type="text/javascript">
    const sUrlRPC = "emp4_gerarslipRPC.php";
    $('k02_codigo').className = 'field-size2';
    $('k02_drecei').className = 'field-size8';
    $('z01_numcgm').className = 'field-size2';
    $('z01_nome').className = 'field-size8';

    $('gestao').classList.add('field-size2')
    $('descricao').classList.add('field-size6')


    let contasCreditar = [];
    function buscaContas() {
        js_divCarregando("Aguarde, consultando contas.", "msgBox");
        new Ajax.Request(
            sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON({exec: "getContas"}),
                onComplete: js_retornoContas
            }
        );
    }

    function js_retornoContas(oAjax) {

        js_removeObj("msgBox");
        const retorno = JSON.parse(oAjax.responseText);

        retorno.contas.forEach(function (conta) {
            conta.nome = conta.nome.urlDecode();
            contasCreditar.push(conta);
        });
    }

    buscaContas();

    function js_pesquisatabrec(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_tabrec',
                'func_tabrec.php?tiporec=E&funcao_js=parent.js_mostratabrec1|k02_codigo|k02_drecei|recurso|arretipo|k00_descr'
                , 'Pesquisa', true, '15');
        } else {
            if (document.form1.k02_codigo.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_tabrec',
                    'func_tabrec.php?tiporec=E&pesquisa_chave=' +
                    document.form1.k02_codigo.value + '&funcao_js=parent.js_mostratabrec',
                    'Pesquisa',
                    false);
            } else {
                document.form1.k02_drecei.value = '';
            }
        }
    }

    function js_pesquisaz01_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('', 'func_nome',
                'func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
        } else {
            if (document.form1.z01_numcgm.value != '') {
                js_OpenJanelaIframe('', 'func_nome', 'func_nome.php?pesquisa_chave=' + document.form1.z01_numcgm.value +
                    '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostracgm(erro, chave) {
        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.z01_numcgm.focus();
            document.form1.z01_numcgm.value = '';
        }
    }

    function js_mostracgm1(chave1, chave2) {
        document.form1.z01_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        func_nome.hide();

    }

    function js_mostratabrec(chave2, erro, chave3, chave4, chave5) {
        document.form1.k02_drecei.value = chave2;

        if (erro == true) {
            document.form1.k02_codigo.focus();
            document.form1.k02_codigo.value = '';
        }

    }

    function js_mostratabrec1(chave1, chave2, chave3, chave4, chave5) {

        document.form1.k02_codigo.value = chave1;
        document.form1.k02_drecei.value = chave2;
        db_iframe_tabrec.hide();
    }

    /**
     * Iniciamos a construcao do aplicativo.
     */
    function init() {

        iTipoGerarSlip = 1;
        oGridSlip = new DBGrid("gridSlip");
        oGridSlip.nameInstance = "oGridSlip";

        /**
         * sobreescrevemos o metodo selectSingle do DBGrid
         */
        oGridSlip.selectSingle = function (oCheckbox, sRow, oRow, lVerificaSaldo) {

            if (lVerificaSaldo == null) {
                var lVerificaSaldo = true;
            }

            if (oCheckbox.checked) {

                oRow.isSelected = true;
                $(sRow).className = 'marcado';
                if (lVerificaSaldo) {
                    $('TotalForCol8').innerHTML = js_formatar(oGridSlip.sum(8).toFixed(2), 'f');
                }
                $('total_selecionados').innerHTML = new Number($('total_selecionados').innerHTML) + 1;
            } else {

                $(sRow).className = oRow.getClassName();
                oRow.isSelected = false;
                if (lVerificaSaldo) {
                    $('TotalForCol8').innerHTML = js_formatar(oGridSlip.sum(13).toFixed(2), 'f');
                }
                $('total_selecionados').innerHTML = new Number($('total_selecionados').innerHTML) - 1;
            }
        }

        /**
         * sobreescrevemos o metodo selectall do DBGrid
         */
        oGridSlip.selectAll = function (idObjeto, sClasse, sLinha) {

            var obj = document.getElementById(idObjeto);
            if (obj.checked) {
                obj.checked = false;
            } else {
                obj.checked = true;
            }

            itens = this.getElementsByClass(sClasse);
            for (var i = 0; i < itens.length; i++) {

                if (itens[i].disabled == false) {
                    if (obj.checked == true) {

                        if ($(this.aRows[i].sId).style.display != 'none') {
                            if (!itens[i].checked) {

                                itens[i].checked = true;
                                this.selectSingle($(itens[i].id), (sLinha + i), this.aRows[i], false);

                            }

                        }
                    } else {

                        if (itens[i].checked) {

                            itens[i].checked = false;
                            this.selectSingle($(itens[i].id), (sLinha + i), this.aRows[i], false);
                        }
                    }
                }
            }
            $('TotalForCol8').innerHTML = js_formatar(oGridSlip.sum(8).toFixed(2), 'f');
        }
        oGridSlip.setCheckbox(0);
        oGridSlip.setCellAlign(["right", "Right", "right", "right", "left", "right", "left", "right", "right"]);
        var aHeaders = [
            "Arrecadacao",
            "OP",
            "Cta Credito",
            "Cta Debito",
            "Receita",
            "Recurso",
            "Credor",
            "Valor",
            "Nº Favorecido",
            "Retenção",
            "id recurso",
            "id retencaoreceitas"
        ];
        oGridSlip.setHeader(aHeaders);
        oGridSlip.aHeaders[9].lDisplayed = false;
        oGridSlip.aHeaders[10].lDisplayed = false;
        oGridSlip.aHeaders[11].lDisplayed = false;
        oGridSlip.aHeaders[12].lDisplayed = false;
        oGridSlip.hasTotalizador = true;
        oGridSlip.show($('gridSlip'));
        $('ponto').style.width = "150px";
        $('tipo').style.width = "150px";
        $('gridSlipstatus').innerHTML = "&nbsp;<span style='color:blue' id ='total_selecionados'>0</span> Selecionados";
        if (isFolha) {
            $('folha').style.display = '';
        } else {
            $('fornecedores').style.display = '';
        }
    }

    function js_getMovimentos() {

        js_divCarregando("Aguarde, consultando informações.", "msgBox");
        js_controleBotoes(true);
        var oRequisicao = {
            exec: "getArrecExtra",
            isFolha: isFolha,
            paramFolha: js_getQueryDadosFolha(),
            idsRecursos: $F('codigosRecursos'),
            iNumCgm: $F('z01_numcgm'),
            ordemPag: $F('e50_codord')
        };

        if (!isFolha) {
            oRequisicao.dtIni = $F('datainicial');
            oRequisicao.dtFim = $F('datafinal');
        }

        oRequisicao.iReceita = $F('k02_codigo');

        new Ajax.Request(
            sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oRequisicao),
                onComplete: js_retornoGetMovimentos
            }
        );
    }

    function js_retornoGetMovimentos(oAjax) {

        js_removeObj("msgBox");
        js_controleBotoes(false);
        var oRetorno = JSON.parse(oAjax.responseText);
        oGridSlip.clearAll(true);

        if (oRetorno.status == 1) {
            for (var i = 0; i < oRetorno.itens.length; i++) {
                with (oRetorno.itens[i]) {
                    if (k13_conta != "") {
                        conta = k13_conta;
                    }

                    var aLinha = new Array();
                    aLinha[0] = k12_numpre;
                    aLinha[1] = "<a onclick='js_JanelaAutomatica(\"empempenho\"," + e60_numemp + ");return false;' href='#'>";
                    aLinha[1] += e50_codord + "</a>";
                    aLinha[2] = js_createComboContas(k12_numpre, contasCreditar, k13_descr, credito, false);
                    aLinha[3] = debito;
                    aLinha[4] = k02_drecei.urlDecode().substring(0, 20);
                    aLinha[5] = `${gestao} - ${o15_recurso} - ${o15_complemento}`;
                    aLinha[6] = z01_nome.urlDecode().substring(0, 30);
                    aLinha[7] = js_formatar(k12_valor, "f");
                    aLinha[8] = z01_numcgm;
                    aLinha[9] = k107_sequencial;
                    aLinha[10] = k00_recurso;
                    aLinha[11] = e23_sequencial;

                    oGridSlip.addRow(aLinha);

                    oGridSlip.aRows[i].aCells[7].sEvents = "onmouseover='js_setAjuda(\"" + z01_nome.urlDecode() + "\",true)'";
                    oGridSlip.aRows[i].aCells[7].sEvents += "onmouseOut='js_setAjuda(null,false)'";
                    oGridSlip.aRows[i].aCells[4].sEvents = "onmouseover='js_setAjuda(\"" + descrdebito.urlDecode() + "\",true)'";
                    oGridSlip.aRows[i].aCells[4].sEvents += "onmouseOut='js_setAjuda(null,false)'";
                }
            }

            oGridSlip.renderRows();
            oGridSlip.setNumRows(oRetorno.itens.length);
            $('gridSlipstatus').innerHTML = "&nbsp;<span style='color:blue' id ='total_selecionados'>0</span> Selecionados";
        } else {
            alert(oRetorno.message.urlDecode());
        }
    }

    function js_objectToJson(oObject) {
        return JSON.stringify(oObject);
    }

    function js_gerarsLip() {

        var aItens = oGridSlip.getSelection("object");
        if (aItens.length == 0) {

            alert("Nenhum Registro Selecionado.");
            return false;
        }

        var oRequisicao = new Object();
        oRequisicao.exec = "gerarSlipsExtra";
        oRequisicao.aSlips = new Array();
        oRequisicao.isFolha = isFolha;
        oRequisicao.paramFolha = js_getQueryDadosFolha();
        oRequisicao.lCreditarExtra = true;
        for (var i = 0; i < aItens.length; i++) {

            var oSlip = new Object();
            oSlip.iCtaCredito = aItens[i].aCells[3].getValue();
            if (aItens[i].aCells[3].getValue() == "") {

                alert('Arrecadação ' + aItens[i].aCells[1].getValue() + " sem conta crédito informada.");
                delete oSlip;
                delete oRequisicao;
                return false;
            }

            oSlip.iCtaDebito = aItens[i].aCells[4].getValue();
            oSlip.iRecurso = aItens[i].aCells[11].getValue();
            oSlip.iArrecadacao = aItens[i].aCells[1].getValue();
            oSlip.iCGM = aItens[i].aCells[9].getValue();
            oSlip.iRetencao = aItens[i].aCells[10].getValue().trim();
            oSlip.iOrdem = aItens[i].aCells[2].getValue();
            oSlip.nValor = js_strToFloat(aItens[i].aCells[8].getValue()).valueOf();
            oSlip.iRetencaoReceitas = aItens[i].aCells[12].getValue().trim();

            oRequisicao.aSlips.push(oSlip);
        }
        if (!confirm('Confirma a emissão dos slips?')) {
            return false;
        }
        js_divCarregando("Aguarde, Gerando slips.", "msgBox");
        var oAjax = new Ajax.Request(
            sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oRequisicao),
                onComplete: js_retornoGerarSlips
            }
        );
    }

    function js_retornoGerarSlips(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
            return;
        }

        if (oRetorno.status == 1) {

            var sSlis = oRetorno.aSlipsRetorno.implode(',');
            if (confirm(('Slip(s) (' + sSlis + ') gerado(s) com sucesso.\n\nDeseja Emiti-lo(s)?'))) {
                js_emiteSlips(sSlis);
            }
            js_getMovimentos();
        }
    }

    function js_emiteSlips(sSlips) {
        window.open('cai3_emiteslips002.php?slips=' + sSlips, '', 'location=0');
    }

    function js_controleBotoes(lDisabled) {

        var aItens = $$('input[type=submit], input[type=button], button');
        aItens.each(function (input, id) {

            input.disabled = lDisabled;

        });
    }

    var sUrl = 'pes1_rhempenhofolhaRPC.php';

    function js_consultaPontoComplementar() {

        js_divCarregando('Consultando ponto complementar...', 'msgBox');
        js_bloqueiaTela(true);

        var sQuery = 'sMethod=consultaPontoComplementar';
        sQuery += '&iAnoFolha=' + $F('anofolha');
        sQuery += '&iMesFolha=' + $F('mesfolha');
        sQuery += '&sSigla=' + $F('ponto');

        new Ajax.Request(sUrl, {
                method: 'post',
                parameters: sQuery,
                onComplete: js_retornoPontoComplementar
            }
        );

    }

    function js_retornoPontoComplementar(oAjax) {

        js_removeObj("msgBox");
        js_bloqueiaTela(false);

        var aRetorno = JSON.parse(oAjax.responseText);
        var sExpReg = new RegExp('\\\\n', 'g');


        if (aRetorno.lErro) {
            alert(aRetorno.sMsg.urlDecode().replace(sExpReg, '\n'));
            return false;
        }

        var sLinha = "";
        var iLinhasSemestre = aRetorno.aSemestre.length;

        if (iLinhasSemestre > 0) {


            sLinha += " <td align='left' title='Nro. Complementar'> ";
            sLinha += "   <strong>Nro. Complementar:</strong>       ";
            sLinha += " </td>                                       ";
            sLinha += " <td>                                        ";
            sLinha += "   <select id='semestre' name='semestre' style='width:150px'>    ";
            sLinha += "     <option value = '0'>Todos</option>      ";

            for (var iInd = 0; iInd < iLinhasSemestre; iInd++) {
                with (aRetorno.aSemestre[iInd]) {
                    sLinha += " <option value = '" + semestre + "'>" + semestre + "</option>";
                }
            }

            sLinha += " </td>                                       ";

        } else {

            sLinha += " <td colspan='2' align='center'>                                ";
            sLinha += "   <font color='red'>Sem complementar para este período.</font> ";
            sLinha += " </td>                                                          ";

        }

        $('linhaComplementar').innerHTML = sLinha;
        $('linhaComplementar').style.display = '';

    }

    function js_validaTipoPonto() {

        if ($F('ponto') == 'r48') {
            js_consultaPontoComplementar();
        } else {
            $('linhaComplementar').style.display = 'none';
        }

    }

    function js_bloqueiaTela(lBloq) {

        if (lBloq) {
            $('anofolha').disabled = true;
            $('mesfolha').disabled = true;
            $('ponto').disabled = true;

            if ($F('ponto') == 'r48') {
                if ($('semestre')) {
                    $('semestre').disabled = true;
                }
            }

        } else {

            $('anofolha').disabled = false;
            $('mesfolha').disabled = false;
            $('ponto').disabled = false;

            if ($F('ponto') == 'r48') {
                if ($('semestre')) {
                    $('semestre').disabled = false;
                }
            }

        }
    }


    function js_getQueryDadosFolha() {

        var oParam = new Object();
        oParam.iAnoFolha = $F('anofolha');
        oParam.iMesFolha = $F('mesfolha');
        oParam.sSigla = $F('ponto');
        oParam.iTipo = $F('tipo');
        oParam.sSemestre = "0";

        if ($F('ponto') == 'r48') {
            if ($('semestre')) {
                oParam.sSemestre = $F('semestre');
            }
        }

        return oParam;

    }


    function js_setAjuda(sTexto, lShow) {

        if (lShow) {

            el = $('gridSlip');
            var x = 0;
            var y = el.offsetHeight;
            while (el.offsetParent && el.tagName.toUpperCase() != 'BODY') {

                x += el.offsetLeft;
                y += el.offsetTop;
                el = el.offsetParent;

            }
            x += el.offsetLeft;
            y += el.offsetTop;
            $('ajudaItem').innerHTML = sTexto;
            $('ajudaItem').style.display = '';
            $('ajudaItem').style.top = y + 10;
            $('ajudaItem').style.left = x;

        } else {
            $('ajudaItem').style.display = 'none';
        }
    }

    function js_createComboContas(iCodMov, aContas, k13_descr, iContaConfig, lDisabled) {
        var sDisabled = "";
        if (lDisabled == null) {
            lDisabled = false;
        }
        if (lDisabled) {
            sDisabled = " disabled ";
        }
        var sCombo = "<select id='ctapag" + iCodMov + "' class='ctapag' style='width:100%'";
        sCombo += "<option value=''>Selecione</option>";

        if (aContas != null) {

            if (Array.isArray(aContas)) {
                for (var i = 0; i < aContas.length; i++) {

                    var sSelected = "";
                    if (iContaConfig == aContas[i].conta) {
                        sSelected = " selected ";
                    }
                    var sDescrConta = aContas[i].conta + " - " + aContas[i].nome.urlDecode();
                    sCombo += "<option " + sSelected + " value = " + aContas[i].conta + ">" + sDescrConta + "</option>";

                }
            } else {
                var sSelected = "";
                if (iContaConfig == aContas) {
                    sSelected = " selected ";
                }

                var sDescrConta = k13_descr.urlDecode();
                sCombo += "<option " + sSelected + " value = " + aContas + ">" + sDescrConta + "</option>";
            }
        }
        sCombo += "</select>";
        return sCombo;

    }

    init();

    function js_pesquisaRecurso(lMostraWindow) {
        if (!lMostraWindow && $('gestao').value == '') {
            $("codigosRecursos").value = '';
            $("o15_recurso").value = '';
            $('descricao').value = '';
            return
        }

        let param = 'gestao=' + $('gestao').value;
        pesquisaRecurso(param);
    }

    const pesquisaRecurso = (parametroAdicional) => {
        let sUrl = 'func_fontesubrecurso.php?funcao_js=parent.js_preencheRecurso|gestao|o15_recurso|descricao|db_ids_recursos';
        if (parametroAdicional) {
            sUrl += `&${parametroAdicional}`;
        }
        js_OpenJanelaIframe('', 'db_iframe_recurso', sUrl, 'Pesquisa Fonte de Recurso', true);
    };

    function js_preencheRecurso(gestao, recurso, descricao, db_ids_recursos) {
        $('gestao').value = gestao;
        $('o15_recurso').value = recurso;
        $('descricao').value = descricao;
        $('codigosRecursos').value = db_ids_recursos;
        db_iframe_recurso.hide();
    }
</script>
