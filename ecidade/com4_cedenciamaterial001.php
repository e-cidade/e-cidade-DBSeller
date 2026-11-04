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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <?php
    db_app::load("scripts.js, prototype.js, datagrid.widget.js, strings.js, grid.style.css, AjaxRequest.js");
    db_app::load("estilos.css, widgets/windowAux.widget.js, dbmessageBoard.widget.js, dbtextField.widget.js");
    ?>

</head>
<body onload="js_init();">
    <div class="container">
        <div style="width: 820px" id='ctnMessageCompilacao'>
        </div>
        <fieldset style="width: 800px">
            <legend><b>Compilação</b></legend>
            <div id="ctnGridCompilacao">
            </div>
        </fieldset>
        <span style='font-weight:bold'>
        </span>
    </div>
    <div style='
        position:absolute;
        top: 200px; left:15px;
        border:1px solid black;
        text-align: left;
        padding:3px;
        background-color: #FFFFCC;
        display:none;' id='ajudaItem'
    >
    </div>
<?php
db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
);
?>


<script>

    var sMsgTitle = "Registros de Preço que o Departamento Participa "
    var sUrlRPC = "com4_cedenciamateriais.RPC.php";
    var iCompilacao = 0;
    var iEstimativaRecebe = 0;
    var iEstimativaCedente = 0;
    var iDepartamentoRecebe = 0;
    var lWindowCompilacoes = false;

    /**
     *  Função JS executada no momento em que a página é aberta
     */
    function js_init() {

        oGridCompilacao = new DBGrid('ctnGridCompilacao');
        oGridCompilacao.nameInstance = "oGridCompilacao";
        oGridCompilacao.setHeight(250);
        oGridCompilacao.setCellAlign(["center", "center"]);
        oGridCompilacao.setCellWidth(["10%", "10%", "30%", "10%", "40%"]);
        oGridCompilacao.setHeader([
            "Compilação",
            "Licitação",
            "Modalidade",
            "Número/Ano",
            "Resumo"
        ]);
        oGridCompilacao.show($('ctnGridCompilacao'));
        oGridCompilacao.setStatus(
            '*Duplo clique sobre o registro de preço para escolher o ' +
            'departamento para o qual deseja ceder materiais.'
        );
        js_getCompilacao();
    }

    /**
     *  Busca os dados de compilações existentes no sistema
     */
    function js_getCompilacao() {

        js_divCarregando('Aguarde, pesquisando.', 'msgBox');
        var oParam = {};
        oParam.exec = "getCompilacao";
        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: js_preencheGridCompilacao
            });
    }

    /**
     * Função que preenche a grid com os dados da compilação
     */
    function js_preencheGridCompilacao(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oAjax.responseText);
        oGridCompilacao.clearAll(true);

        if (oRetorno.status == 2) {
            alert(oRetorno.message.urlDecode());
        } else {

            if (oRetorno.aCompilacao.length == 0) {
                alert("Nenhuma compilação encontrada.");
                return false;
            }

            oRetorno.aCompilacao.each(
                function (oDado, iIdLinha) {

                    var aLinha = [];
                    aLinha[0] = oDado.pc10_numero;
                    aLinha[1] = `
                    <a
                        onclick="js_buscaLicitacao(this)"
                        class="dbancora"
                        value="${oDado.l20_codigo}">
                        ${oDado.l20_codigo}
                    </a>`;
                    aLinha[2] = oDado.l03_descr.urlDecode().substring(0, 70);
                    aLinha[3] = oDado.numero_ano.urlDecode();
                    aLinha[4] = oDado.pc10_resumo.urlDecode().substring(0, 70);
                    oGridCompilacao.addRow(aLinha);
                    oGridCompilacao.aRows[iIdLinha].sEvents +=
                        "onDblClick='js_openCompilacao(" +
                            oDado.pc10_numero +
                        ")'";
                });
            oGridCompilacao.renderRows();
        }
    }

    /**
     *  Abre a WindowAux contendo os departamentos da compilação
     */
    function js_openCompilacao(iIdSolicita)
    {
        if (lWindowCompilacoes) {
            return false;
        }
        iCompilacao = iIdSolicita;
        var sHtmlDepart = "<fieldset>";
        sHtmlDepart += "  <legend><b>Departamentos da Compilação<b></legend>";
        sHtmlDepart += "  <div id='ctnDepartCompilacao'></div>";
        sHtmlDepart += "</fieldset>";

        sHtmlDepart += "<center><input type=\"button\" style='margin-top: 5px' id='criaEstimativa' ";
        sHtmlDepart += " onclick='js_openCriaEstimativa(" + iIdSolicita + ")' value=\"Gerar Estimativa\" \> <center>";

        lWindowCompilacoes = true;
        /**
         *  Window Aux
         */
        oWindowDepart = new windowAux("winId_" + iIdSolicita, "Departamentos Participantes", 900, 500);
        oWindowDepart.setContent(sHtmlDepart);
        oWindowDepart.allowCloseWithEsc(false);
        $("windowwinId_" + iIdSolicita + "_btnclose").onclick = function () {

            lWindowCompilacoes = false;
            oWindowDepart.destroy();

        }

        /**
         *  Message Board
         */
        var sMsgTitle = "Departamentos da Compilação " + iIdSolicita;
        var sMsgHelp = "Duplo clique sobre o departamento para escolher os itens que deseja ceder.";
        oMsgBoardDepart = new DBMessageBoard("msgBoard_" + iIdSolicita, sMsgTitle, sMsgHelp, oWindowDepart.getContentContainer());

        oMsgBoardDepart.show();
        oWindowDepart.show();

        /**
         *  Monta grid com os departamentos da compilação
         */
        oGridDepartamento = new DBGrid('ctnDepartCompilacao');
        oGridDepartamento.nameInstance = "oGridDepartamento";
        oGridDepartamento.setHeight(300);
        oGridDepartamento.setCellAlign(["center", "left", "center"]);
        oGridDepartamento.setCellWidth(["10%", "80%", "10%"]);
        oGridDepartamento.setHeader(["Departamento", "Descrição", "Estimativa"]);
        oGridDepartamento.show($('ctnDepartCompilacao'));
        js_getDepartamentos(iIdSolicita);
    }

    function js_openCriaEstimativa(iIdSolicita) {

        document.getElementById("criaEstimativa").setAttribute("disabled", true);
        sHtml = "<div class=\"container \" >";
        sHtml += " <form name=\"form1\" method=\"post\" action=\"\" >";
        sHtml += "        <fieldset>";
        sHtml += "          <legend>Gerar estimativa a partir da compilação: " + iIdSolicita + "</legend>";
        sHtml += "          <table>";
        sHtml += "           <tr>";
        sHtml += "            <td><a href='#' class='dbancora' style='text-decoration:underline;' onclick=\"js_pesquisat94_depart(true)\">Departamento:</a>";
        sHtml += "            </td>";
        sHtml += "            <td> <input title=\"Código do departamento Campo:t94_depart\" name=\"t94_depart\" type=\"text\" id=\"t94_depart\" value=\"\" size=\"8\" maxlength=\"5\" onchange=\"js_pesquisat94_depart(false);\" onkeyup=\"js_ValidaMaiusculo(this,'f',event);\" oninput=\"js_ValidaCampos(this,0,'Departamento destino','f','f',event);\" onkeydown=\"return js_controla_tecla_enter(this,event);\" autocomplete=\"off\" labelvalidacao=\"Departamento destino\" ismandatory=\"true\">";
        sHtml += "            </td>";

        sHtml += "<td> <input title=\"Descrição do departamento Campo:descrdepto\" name=\"depto_destino\" type=\"text\" id=\"depto_destino\" value=\"\" size=\"40\" maxlength=\"40\" readonly=\"\" style=\"background-color:#DEB887;text-transform:uppercase;\" autocomplete=\"off\" labelvalidacao=\"Descrição do Departamento\"></td>";
        sHtml += "          </tr>";
        sHtml += "          </table>";
        sHtml += "        </fieldset>";
        sHtml += "        <input name=\"salvarEstimativa\" id=\"salvarEstimativa\" type=\"button\" value=\"Processar\" onClick=\"js_Salvar(" + iIdSolicita + ");\" >";
        sHtml += "      </form>";
        sHtml += "</div>";
        oWindowCriaEstimativa = new windowAux("winsolicitaId_" + iIdSolicita, "Inclusão de Estimativa", 700, 300);

        oWindowCriaEstimativa.setContent(sHtml);
        oWindowCriaEstimativa.allowCloseWithEsc(false);
        $("windowwinsolicitaId_" + iIdSolicita + "_btnclose").onclick = function () {

            js_getDepartamentos(iIdSolicita);
            document.getElementById("criaEstimativa").removeAttribute("disabled");
            oWindowDepart.show();
            oWindowCriaEstimativa.destroy();
        }

        oWindowDepart.hide();
        oWindowCriaEstimativa.show();
    }

    function js_Salvar(iSolicita) {


        lTemcerteza = confirm("Esta ação não poderá ser desfeita. Deseja realmente processar?");

        if (lTemcerteza == false) {

            return;
        }

        var RPC = 'com4_solicitaregistro.RPC.php';

        iDepart = $('t94_depart').value
        if (iSolicita == '') {
            alert('Informe o registro de preço');
            return false;
        }

        if (iDepart == '') {
            alert('Informe o departamento');
            return false;
        }
        new AjaxRequest(
            RPC,
            {
                exec: 'lSalvar', iSolicita: iSolicita,
                iDepart: iDepart
            },
            function (oRetorno, lErro) {
                if (lErro) {

                    alert(oRetorno.mensagem.urlDecode());
                } else {
                    alert('Salvo com Sucesso\nCódigo Gerado : ' + oRetorno.iSolicita);
                }
            }
        ).setMessage('Aguarde, Processando...').asynchronous(false).execute();

    }

    function js_pesquisat94_depart(mostra) {


        if (mostra == true) {
            oWindowCriaEstimativa.hide();
            js_OpenJanelaIframe('', 'db_iframe_depart', 'func_db_depart.php?funcao_js=parent.js_mostradb_depart1|coddepto|descrdepto&todasinstit=1&chave_t93_depart=' + $F('t94_depart') + '&automatico=0', 'Pesquisa', true);
        } else {
            if ($F('t94_depart') != '') {
                js_OpenJanelaIframe('', 'db_iframe_depart', 'func_db_depart.php?pesquisa_chave=' + document.form1.t94_depart.value + '&todasinstit=1&funcao_js=parent.js_mostradb_depart&chave_t93_depart=' + document.form1.t94_depart.value, 'Pesquisa', false);
            } else {
                document.form1.t94_depart.value = '';
            }
        }
    }

    function js_mostradb_depart(chave, erro) {
        document.form1.depto_destino.value = chave;
        if (erro == true) {
            document.form1.t94_depart.focus();
            document.form1.t94_depart.value = '';
        }
    }

    function js_mostradb_depart1(chave1, chave2) {
        document.form1.t94_depart.value = chave1;
        document.form1.depto_destino.value = chave2;
        db_iframe_depart.hide();
        oWindowCriaEstimativa.show();
    }

    /**
     *  Executa um AJAX buscando os dados do departamento e o código da estimativa
     */
    function js_getDepartamentos(iCompilacao) {

        js_divCarregando('Aguarde, pesquisando.', 'msgBox');
        var oParam = {};
        oParam.exec = "getDepartamento";
        oParam.iCompilacao = iCompilacao;

        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: js_preencheGridDepartamento
            });
    }

    /**
     *  Preenche a grid criada na função 'js_openCompilacao' com os dados retornados pela função 'js_getDepartamentos'
     */
    function js_preencheGridDepartamento(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.aEstimativa.length == 0) {
            alert("Não existem outros departamentos participantes para inclusão de cedência.");
            return false;
        }

        oGridDepartamento.clearAll(true);
        oRetorno.aEstimativa.each((oEstimativa, iIdLinha) => {
            var aLinha = [];
            aLinha[0] = oEstimativa.iDepartamento;
            aLinha[1] = oEstimativa.sDescrDepartamento.urlDecode().substring(0, 70);
            aLinha[2] = oEstimativa.iEstimativa;
            oGridDepartamento.addRow(aLinha);
            let event = `onDblClick="js_openItensDepartamento(
                ${oEstimativa.iDepartamento},
                '${oEstimativa.sDescrDepartamento}',
                ${oRetorno.iEstimativaDptoAtual},
                ${oEstimativa.iEstimativa},
                ${oRetorno.compilacao}
            )"`;
            oGridDepartamento.aRows[iIdLinha].sEvents += event;
        });
        oGridDepartamento.renderRows();
    }

    /**
     * Abre uma window contendo os itens que podem ser cedidos para outros departamentos
     */
    function js_openItensDepartamento(iCodDepartamento, sDepartamento, iCodEstimativaCede, iEstimativaRecebe, compilacao) {

        iEstimativaCedente = iCodEstimativaCede;

        var sHtmlItens = "<fieldset>";
        sHtmlItens += "  <legend><b>Itens</b></legend>";
        sHtmlItens += "  <div id='ctnItensDepartamento'></div>";
        sHtmlItens += "</fieldset>";
        sHtmlItens += "<center>";
        sHtmlItens += "<br><input type='button' id='btnSalvarItens' name='btnSalvarItens' value='Salvar' >";
        sHtmlItens += "</center>";

        /**
         *  Window Aux
         */
        oWindowItensDepart = new windowAux("winId_" + iCodDepartamento, "Itens do Departamento", 800, 400);
        oWindowItensDepart.setContent(sHtmlItens);
        oWindowItensDepart.allowCloseWithEsc(false);
        oWindowItensDepart.allowDrag(false);
        $("windowwinId_" + iCodDepartamento + "_btnclose").onclick = function () {

            destroyDivModal(oWindowDepart.getContentContainer());
            oWindowItensDepart.destroy();
        }

        /**
         *  Message Board
         */
        var sMsgTitleItens = "Itens disponíveis para o departamento " + sDepartamento.urlDecode();
        var sMsgHelpItens = "Digite a quantidade de cada item que deseja ceder no campo <b>Qtde. ";
        sMsgHelpItens += "Ceder</b>. Após concluir clique em salvar";
        oMsgBoardItensDepart = new DBMessageBoard("msgBoard_" + iCodDepartamento,
            sMsgTitleItens,
            sMsgHelpItens,
            oWindowItensDepart.getContentContainer()
        );

        oMsgBoardItensDepart.show();
        oWindowItensDepart.setChildOf(oWindowDepart);
        oWindowItensDepart.show(30, 35);
        createDivModal(oWindowDepart.getContentContainer());

        $('btnSalvarItens').observe("click", js_confirmaCedencia);
        /**
         * Monta a grid que receberá os itens do departamento selecionado
         */
        oGridItensDepart = new DBGrid('ctnItensDepartamento');
        oGridItensDepart.nameInstance = "oGridItensDepart";
        oGridItensDepart.setHeight(200);
        oGridItensDepart.setCellAlign(["right", "center", "left", "right", "right", "right"]);
        oGridItensDepart.setCellWidth(["5%", "10%", "55%", "10%", "10%", "10%"]);
        oGridItensDepart.setHeader(["Ordem", "Material", "Descrição", "Qtd. Disp.",
            "Cedido",
            "Qtd. Ceder",
            "Item Doa",
            "Item Recebe"]
        );
        oGridItensDepart.aHeaders[6].lDisplayed = false;
        oGridItensDepart.aHeaders[7].lDisplayed = false;
        oGridItensDepart.show($('ctnItensDepartamento'));
        js_getItensDepartamento(iCodDepartamento, iCodEstimativaCede, iEstimativaRecebe, compilacao);
    }

    function js_getItensDepartamento(iCodDepartamento, iCodEstimativa, iCodigoEstimativaRecebe, iCodCompilacao) {

        iDepartamentoRecebe = iCodDepartamento;
        js_divCarregando('Aguarde, pesquisando.', 'msgBox');
        var oParam = {};
        oParam.exec = "getItens";
        oParam.iCodDepartamento = iCodDepartamento;
        oParam.iCodEstimativa = iCodEstimativa;
        oParam.iCodEstimativaRecebe = iCodigoEstimativaRecebe;
        oParam.iCodCompilacao = iCodCompilacao;

        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: "post",
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: js_preencheGridItensDepartamento
            });
    }

    function js_preencheGridItensDepartamento(oAjax) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.aItens.length == 0) {

            alert("Nenhum item encontrado.");
            return false;
        }


        oGridItensDepart.clearAll(true);
        oRetorno.aItens.each(
            function (oItem, iIdLinha) {

                var lDisabled = false;
                if (oItem.iQtdSaldo < 1 || oItem.iItemRecebe == '' || oItem.fornecedor == '') {
                    lDisabled = true;
                }
                var aLinha = [];
                aLinha[0] = iIdLinha + 1;
                aLinha[1] = oItem.iCodMaterial;
                aLinha[2] = oItem.sDescrMaterial.urlDecode().substring(0, 70);
                aLinha[3] = String(oItem.iQtdSaldo).valueOf();
                aLinha[4] = oItem.iQtdCedida;
                if (lDisabled) {
                    aLinha[5] = '0';
                } else {

                    aLinha[5] = new DBTextField("iValor_" + oItem.iCodigoItem, "iValor_" + oItem.iCodigoItem, 0, 10);
                    aLinha[5].addStyle("text-align", "right");
                    aLinha[5].addStyle("height", "100%");
                    aLinha[5].addStyle("width", "100%");
                    aLinha[5].addStyle("border", "1px solid transparent;");
                    aLinha[5].addEvent("onBlur", "js_bloqueiaDigitacao(this);");
                    aLinha[5].addEvent("onBlur", "iValor_" + oItem.iCodigoItem + ".sValue=this.value;");
                    aLinha[5].addEvent("onFocus", "js_liberaDigitacao(this);");
                    aLinha[5].addEvent("onKeyPress", "return js_mask(event,\"0-9|.|-\")");
                }
                aLinha[6] = oItem.iCodigoItem;
                aLinha[7] = oItem.iItemRecebe;
                //aLinha[5].addEvent("onKeyDown","return js_verifica(this,event,false)");
                oGridItensDepart.addRow(aLinha, false, lDisabled);
                oGridItensDepart.aRows[iIdLinha].aCells[0].sStyle += "background-color:#DED5CB;font-weight:bold;padding:1px";
                if (lDisabled) {
                    oGridItensDepart.aRows[iIdLinha].setClassName('disabled');
                }
                oGridItensDepart.aRows[iIdLinha].aCells[2].sEvents = "onmouseover='js_setAjuda(\"" + oItem.sResumo + "\",true)'";
                oGridItensDepart.aRows[iIdLinha].aCells[2].sEvents += "onmouseOut='js_setAjuda(null,false)'";
            });
        oGridItensDepart.renderRows();

    }

    /**
     * Libera  o input passado como parametro para a digitacao.
     * é Retirado a mascara do valor e liberado para Edição
     * é Colocado a Variavel nValorObjeto no escopo GLOBAL
     */
    function js_liberaDigitacao(object) {

        nValorObjeto = object.value;
        object.value = object.value;
        object.style.border = '1px solid black';
        object.readOnly = false;
        object.style.fontWeight = "bold";
        object.select();
    }

    /**
     * bloqueia  o input passado como parametro para a digitacao.
     * É colocado  a mascara do valor e bloqueado para Edição
     */
    function js_bloqueiaDigitacao(object, iBold) {


        object.readOnly = true;
        object.style.border = '0px';
        object.style.fontWeight = "normal";
        if (iBold) {
            object.style.fontWeight = "bold";
        }
        object.value = object.value;
    }

    function js_setAjuda(sTexto, lShow) {

        if (lShow) {

            el = $('ctnItensDepartamento');
            var x = 0;
            var y = el.offsetHeight;
            while (el.offsetParent && el.tagName.toUpperCase() != 'BODY') {

                x += el.offsetLeft;
                y += el.offsetTop;
                el = el.offsetParent;

            }
            x += el.offsetLeft;
            y += el.offsetTop;
            $('ajudaItem').innerHTML = sTexto.urlDecode();
            $('ajudaItem').style.display = '';
            $('ajudaItem').style.top = y + 10;
            $('ajudaItem').style.left = x;
            $('ajudaItem').style.zIndex = 100000;

        } else {
            $('ajudaItem').style.display = 'none';
        }
    }


    function js_confirmaCedencia() {

        if (!confirm('Confirma a cedência dos Itens?')) {
            return false;
        }
        var aItens = oGridItensDepart.aRows;
        var iItensComCedencia = 0;
        var aItensCedidos = [];
        var sErro = '';
        aItens.each(function (oItem, iIndice) {

            var nQuantidade = oItem.aCells[5].getValue();
            var lCorreto = true;
            if (Number(oItem.aCells[3].getValue()) > 0) {
                if (Number(oItem.aCells[5].getValue()) > Number(oItem.aCells[3].getValue())) {

                    sErro += " Item " + oItem.aCells[0].getValue() + " - " + oItem.aCells[2].getValue() + " sem saldo para Transferencias\n";
                    lCorreto = false;
                }
            }
            if (lCorreto && nQuantidade > 0) {

                oItemCedido = {};
                oItemCedido.itemrecebe = oItem.aCells[7].getValue().trim();
                if (oItemCedido.itemrecebe.trim() == "") {
                    oItemCedido.itemrecebe = '';
                }
                oItemCedido.itemcedente = oItem.aCells[6].getValue();
                oItemCedido.quantidade = nQuantidade;
                aItensCedidos.push(oItemCedido);
            }
        });

        if (sErro.trim() != "") {

            alert(sErro);
        } else {

            var oParam = {};
            oParam.exec = "cederItens";
            oParam.iEstimativa = iEstimativaCedente;
            oParam.iDepartRecebe = iDepartamentoRecebe;
            oParam.aItensCedidos = aItensCedidos

            js_divCarregando("Aguarde, processando...", "msgBox");
            var oAjax = new Ajax.Request(sUrlRPC,
                {
                    method: "post",
                    parameters: 'json=' + Object.toJSON(oParam),
                    onComplete: js_retornoCederItens
                });
        }
    }

    function js_retornoCederItens(oAjax) {

        js_removeObj("msgBox");

        var oRetorno = JSON.parse(oAjax.responseText);

        if (oRetorno.status == 2) {

            alert(oRetorno.message.urlDecode());
            return false;
        } else {
            alert('Cedência realizada com sucesso!');
            lWindowCompilacoes = false;

            oWindowItensDepart.destroy();
            oWindowDepart.destroy();
        }
    }

    function js_buscaLicitacao(ancoraLicitacao) {
        let codigoLicitacao = ancoraLicitacao.getAttribute('value');

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_infolic',
            `lic3_licitacao002.php?l20_codigo=${codigoLicitacao}`,
            'Consulta Licitação',
            true
        );
    }

    createDivModal = function (oNode) {

        var oDiv = document.createElement('div');
        oDiv.id = "modalfor" + oNode.id;
        oDiv.style.width = oNode.clientWidth;
        oDiv.style.height = oNode.clientHeight - 5;
        oDiv.style.height = oNode.clientHeight - 5;
        oDiv.style.backgroundImage = "url(imagens/transparencia.png)";
        oDiv.style.backgroundRepeat = "repeat";
        oDiv.style.position = 'absolute';
        oDiv.style.top = '25px';
        oDiv.style.left = '2px';
        oDiv.style.zIndex = oNode.style.zIndex + 1;
        oNode.appendChild(oDiv);
        oNode.setAttribute('modal', oDiv.id);


    }

    destroyDivModal = function (oNode) {

        if (oNode.getAttribute('modal')) {

            var oDiv = $(oNode.getAttribute('modal'));
            oDiv.parentNode.removeChild(oDiv);
            oNode.setAttribute('modal', '');
        }
    }
</script>
</body>
</html>
