/**
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

const REFERENCIA_FIXA = 1;
const REFERENCIA_NUMERICA = 2;
const REFERENCIA_SELECIONAVEL = 3;
const MENSAGENS_LANCAMENTO_EXAME_LABORATORIO = 'saude.laboratorio.LancamentoExameLaboratorio.';
const MSG_LANCAMENTOLABCONFERENCIA = 'saude.laboratorio.db_frmlab_conferencia.'

require_once("scripts/arrays.js");
LancamentoExameLaboratorio = function(sInstance, origenDigitacaoExame = false) {

    var oSelf = this;
    this.iCodigoExame = '';
    this.iCodigoRequisicao = '';
    this.aAtributos = [];
    this.sNameInstance = sInstance;
    this.lReadOnly = false;
    this.lAbrirComoJanela = false;
    this.aCIDs = [];
    this.iCodigoProcedimento = '';
    this.permitidoConferir = false;
    this.iCIDConferido = null;
    this.atributosFormula = null;
    this.habilitarAbsurdo = true;

    this.callbackAfterSalvar = function() { return true; }
    this.callbackAfterConferir = function() { return true; }
    this.callbackAfterNovaColeta = function() { return true; }

    this.oElementoDivContainer = document.createElement("div");

    this.oElementoDivObservacao = document.createElement("div");
    this.oElementoDivObservacao.style.paddingTop = '10px';
    this.oElementoDivObservacao.style.display = "none";

    this.oElementoDivMotivoNovaColeta = document.createElement("div");
    this.oElementoDivMotivoNovaColeta.style.paddingTop = '10px';

    this.oElementoFieldset = document.createElement("fieldset");

    this.oLegend = document.createElement("legend");
    this.oLegend.innerHTML = "<b>Atributos do Exame</b>";
    this.oElementoFieldset.appendChild(this.oLegend);

    this.oElementoDivGrid = document.createElement("div");
    this.oElementoDivGrid.style.textAlign = "center";

    this.oElementoDivBotao = document.createElement("div");
    this.oElementoDivBotao.style.textAlign = "center";

    this.oTextAreaObservacao = document.createElement("textarea");
    this.oTextAreaObservacao.rows = 5;
    this.oTextAreaObservacao.cols = 100;
    this.oTextAreaObservacao.id = 'textAreaObservacao';

    this.oElementoFieldsetObservacao = document.createElement("fieldset");

    this.oLegendObservacao = document.createElement("legend");
    this.oLegendObservacao.innerHTML = "<b>Observação</b>";

    this.oOptionNao = document.createElement("option");
    this.oOptionNao.value = 'nao';
    this.oOptionNao.innerHTML = 'Não';

    this.oOptionSim = document.createElement("option");
    this.oOptionSim.value = 'sim';
    this.oOptionSim.innerHTML = 'Sim';

    this.oSelectNecessitaNovaColeta = document.createElement("select");
    this.oSelectNecessitaNovaColeta.id = 'selectNecessitaNovaColeta';
    this.oSelectNecessitaNovaColeta.style.width = '100px';
    this.oSelectNecessitaNovaColeta.value = 'nao';

    this.oSelectNecessitaNovaColeta.appendChild(this.oOptionNao);
    this.oSelectNecessitaNovaColeta.appendChild(this.oOptionSim);
    this.oSelectNecessitaNovaColeta.value = 'nao';

    this.oContainerMotivoNovaColeta = document.createElement('div');
    this.oContainerMotivoNovaColeta.id = 'containerMotivoNovaColeta'
    this.oContainerMotivoNovaColeta.style.marginTop = "10px";

    this.otextTitleMotivoNovaColeta = document.createElement("p");
    this.otextTitleMotivoNovaColeta.innerHTML = 'Descrição do Motivo da Nova Coleta';
    this.otextTitleMotivoNovaColeta.style.fontWeight = 'bold';

    this.otextAreaMotivoNovaColeta = document.createElement("textarea");
    this.otextAreaMotivoNovaColeta.rows = 5;
    this.otextAreaMotivoNovaColeta.cols = 100;
    this.otextAreaMotivoNovaColeta.id = 'textAreaMotivoNovaColeta';

    this.oContainerMotivoNovaColeta.appendChild(this.otextTitleMotivoNovaColeta);
    this.oContainerMotivoNovaColeta.appendChild(this.otextAreaMotivoNovaColeta);
    this.oContainerMotivoNovaColeta.style.display = 'none';

    this.oSelectNecessitaNovaColeta.addEventListener('change', function(element){
        if(element.target.value == 'sim'){
            alert('Ao preencher o campo Descrição do Motivo da Nova Coleta e salvar irá alterar a situação da requisição para 40 - Nova Coleta');
            document.getElementById('containerMotivoNovaColeta').style.display = '';
            document.getElementById('fildset-exames').style.height = '716px';
            document.getElementById('btnSalvarConferir').setAttribute('type', 'hidden');
        }else{
            document.getElementById('containerMotivoNovaColeta').style.display = 'none';
            document.getElementById('fildset-exames').style.height = '581px';
            if(oSelf.permitidoConferir == 't'){
                document.getElementById('btnSalvarConferir').setAttribute('type', 'button');
            }
        }
    });

    this.oElementoFieldsetMotivoNovaColeta = document.createElement("fieldset");

    this.oLegendMotivoNovaColeta = document.createElement("legend");
    this.oLegendMotivoNovaColeta.innerHTML = "<b>Necessidade de Nova Coleta</b>";

    this.oElementoDivCID = document.createElement("div");
    this.oElementoDivCID.style.paddingTop = '10px';
    this.oElementoDivCID.style.display = 'none';
    this.oFieldsetCID = document.createElement('fieldset');
    this.oFieldsetCIDLegend = document.createElement('legend');
    this.oFieldsetCIDLegend = document.createElement('legend');
    this.oCIDLabel = document.createElement('label');
    this.oCIDLabel.setAttribute('for', 'iCodigoCID');
    this.oCIDSelect = document.createElement('select');
    this.oCIDSelect.id = 'iCodigoCID';
    this.oCIDSelect.addClassName('field-size-max');
    this.oFieldsetCIDLegend.innerHTML = 'CID';
    this.oFieldsetCID.appendChild(this.oFieldsetCIDLegend);
    this.oCIDLabel.appendChild(this.oCIDSelect);
    this.oFieldsetCID.appendChild(this.oCIDLabel);
    this.oElementoDivCID.appendChild(this.oFieldsetCID);

    this.oElementoFieldsetObservacao.appendChild(this.oLegendObservacao);
    this.oElementoFieldsetObservacao.appendChild(this.oTextAreaObservacao);
    this.oElementoDivObservacao.appendChild(this.oElementoFieldsetObservacao);

    this.oElementoFieldsetMotivoNovaColeta.appendChild(this.oSelectNecessitaNovaColeta);
    this.oElementoFieldsetMotivoNovaColeta.appendChild(this.oLegendMotivoNovaColeta);
    this.oElementoFieldsetMotivoNovaColeta.appendChild(this.oContainerMotivoNovaColeta);
    this.oElementoDivMotivoNovaColeta.appendChild(this.oElementoFieldsetMotivoNovaColeta);

    this.oBtnSalvar = document.createElement("input");
    this.oBtnSalvar.value = 'Salvar';
    this.oBtnSalvar.id = 'btnSalvar';
    this.oBtnSalvar.type = 'button';
    this.oBtnSalvar.observe('click', function() {
        oSelf.salvar(true, origenDigitacaoExame, false);
    });

    this.oBtnSalvarConferir = document.createElement("input");
    this.oBtnSalvarConferir.value = 'Salvar & Conferir';
    this.oBtnSalvarConferir.id = 'btnSalvarConferir';
    this.oBtnSalvarConferir.type = 'hidden';
    this.oBtnSalvarConferir.style.marginLeft = '5px';
    this.oBtnSalvarConferir.observe('click', function() {
        oSelf.salvar(true, origenDigitacaoExame, true);
    });

    document.body.observe('keydown', function(event) {

        if (event.ctrlKey && event.which == 13) {
            oSelf.oBtnSalvar.click();
        }
        if (event.shiftKey && event.which == 13 && oSelf.permitidoConferir == 't' && oSelf.otextAreaMotivoNovaColeta.value == "") {
            oSelf.oBtnSalvarConferir.click();
        }
    });

    this.oBtnFechar = document.createElement("input");
    this.oBtnFechar.id = 'btnFechar';
    this.oBtnFechar.value = 'Fechar';
    this.oBtnFechar.type = 'button';
    this.oBtnFechar.style.marginLeft = '5px';
    this.oBtnFechar.style.display = 'none';

    this.oBtnConfirmar = document.createElement("input");
    this.oBtnConfirmar.id = 'btnConfirmar';
    this.oBtnConfirmar.value = 'Confirmar Resultado';
    this.oBtnConfirmar.type = 'button';
    this.oBtnConfirmar.style.marginRight = '5px';
    this.oBtnConfirmar.style.display = 'none';

    this.oBtnConfirmar.observe('click', function() {

        oSelf.setCallbackSalvar(oSelf.confirmarExame);
        oSelf.salvar(false, origenDigitacaoExame);
    });

    this.oBtnLancarMedicamento = document.createElement("input");
    this.oBtnLancarMedicamento.id = 'btnMedicamento';
    this.oBtnLancarMedicamento.value = 'Medicamentos';
    this.oBtnLancarMedicamento.type = 'button';
    this.oBtnLancarMedicamento.style.marginLeft = '5px';
    this.oBtnLancarMedicamento.setAttribute('disabled', 'disabled');

    this.oBtnLancarMedicamento.observe('blur', function(){
        document.getElementsByClassName('selected')[0].lastChild.click();
    });

    this.oElementoDivBotao.appendChild(this.oBtnConfirmar);
    this.oElementoDivBotao.appendChild(this.oBtnSalvar);
    this.oElementoDivBotao.appendChild(this.oBtnFechar);
    this.oElementoDivBotao.appendChild(this.oBtnLancarMedicamento);

    this.oElementoFieldset.appendChild(this.oElementoDivGrid);
    this.oElementoFieldset.appendChild(this.oElementoDivCID);
    this.oElementoFieldset.appendChild(this.oElementoDivMotivoNovaColeta);
    this.oElementoFieldset.appendChild(this.oElementoDivObservacao);
    this.oElementoFieldset.appendChild(this.oBtnConfirmar);
    this.oElementoFieldset.appendChild(this.oBtnSalvar);
    this.oElementoFieldset.appendChild(this.oBtnSalvarConferir);
    this.oElementoFieldset.appendChild(this.oBtnFechar);
    this.oElementoFieldset.appendChild(this.oBtnLancarMedicamento);

    this.oElementoDivContainer.appendChild(this.oElementoFieldset);
    this.oElementoDivContainer.appendChild(this.oElementoDivBotao);

    this.sUrlRPC = 'lab4_digitacaoexame.RPC.php';
    this.sUrlRPCConferencia = 'lab4_conferencia.RPC.php';
    oGridAtributosExame = new DBGrid("gridAtributos");
    oGridAtributosExame.nameInstance = 'oGridAtributos';
    oGridAtributosExame.setHeader(['Codigo', 'Atributo', '%', 'VA', "Referência", "codigo_ref"]);
    oGridAtributosExame.setCellWidth(['5', '35', '10', '20', '30', '1']);
    oGridAtributosExame.setCellAlign(['right']);
    oGridAtributosExame.setHeight(300);
    oGridAtributosExame.aHeaders[5].lDisplayed = false;

};

/**
 * Renderiza   o componente
 * @param oElement
 */
LancamentoExameLaboratorio.prototype.show = function(oElement) {

    var oSelf = this;

    oElement.appendChild(this.oElementoDivContainer);
    oGridAtributosExame.show(this.oElementoDivGrid);
};

/**
 * Define o codigo da requisicao de Exame
 * @param iRequisicaoExame
 */
LancamentoExameLaboratorio.prototype.setRequisicao = function(iRequisicaoExame, bSelecionou = false) {

    this.iCodigoRequisicao = iRequisicaoExame;
    this.getAtributosDoExame(bSelecionou);
    this.oBtnLancarMedicamento.removeAttribute('disabled');

    if (!this.lAbrirComoJanela) {
        this.lancarMedicamentos();
    }
};

LancamentoExameLaboratorio.prototype.setHabilitarAbsurdo = function(valor) {
    this.habilitarAbsurdo = valor;
};

/**
 * Carrega todos os atributos do exame
 * @private
 */
LancamentoExameLaboratorio.prototype.getAtributosDoExame = function(bSelecionou = false) {

    var oParam = { 'exec': 'getAtributosDoExame', 'requisicao': this.iCodigoRequisicao, 'lConferencia': this.lReadOnly };
    var oSelf = this;

    js_divCarregando(_M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'buscando_atributos'), 'msgBox');
    new Ajax.Request(oSelf.sUrlRPC, {
        method: 'post',
        parameters: 'json=' + Object.toJSON(oParam),
        onComplete: function(oResponse) {

            js_removeObj('msgBox');
            var oRetorno = JSON.parse(oResponse.responseText);

            if (oRetorno.status == 2) {
                alert(oRetorno.message.urlDecode());
                return;
            }

            $('textAreaObservacao').value = oRetorno.sObservacao.urlDecode();
            $('textAreaMotivoNovaColeta').value = oRetorno.sMotivoNovaColeta.urlDecode();
            if($('textAreaMotivoNovaColeta').value == ""){
                $('selectNecessitaNovaColeta').value = 'nao';
                $('containerMotivoNovaColeta').style.display = 'none';
            }else{
                $('containerMotivoNovaColeta').style.display = '';
                $('selectNecessitaNovaColeta').value = 'sim';
            }
            oSelf.aAtributos = oRetorno.atributos;
            oSelf.atributosFormula = oRetorno.dadosAtributos;

            // decodifica string da titulação
            for (var oAtributo of oSelf.aAtributos) {
                oAtributo.titulacao = oAtributo.titulacao.urlDecode();
            }
            oSelf.preencherAtributos(bSelecionou);
        }
    });
};

/**
 * Preenche os dados dos atributos
 * @private
 */
LancamentoExameLaboratorio.prototype.preencherAtributos = function(bSelecionou = false) {

    oGridAtributosExame.clearAll(true);
    var oSelf = this;
    var linhasParaBloquear = [];

    this.aAtributos.each(function(oAtributo, iSeq) {


        var sDescricaoAtributo = oAtributo.descricao.urlDecode()
            // quando atributo recebe valor, transforma em um link
        if (oAtributo.tipo == 2) {
            sDescricaoAtributo = '<a href="#" tabindex="-1" id="atributo_obs_' + oAtributo.codigo + '" title="Clique para lançar titulação."> ' + sDescricaoAtributo + '</a>';
        }


        var aLinha = [];
        aLinha[0] = oAtributo.codigo;
        aLinha[1] = strRepeat("&nbsp;&nbsp;", oAtributo.nivel) + sDescricaoAtributo;

        aLinha[2] = oSelf.inputPercentual(oAtributo, iSeq);
        aLinha[3] = oSelf.inputValorAbsoluto(oAtributo, iSeq);
        aLinha[4] = '';
        if (oAtributo.referencia != '') {

            var sStringReferencia = '';
            switch (oAtributo.referencia.tipo) {

                case REFERENCIA_NUMERICA:

                    sStringReferencia = "(" + oAtributo.referencia.faixanormalminimo + " Até ";
                    sStringReferencia += oAtributo.referencia.faixanormalmaximo + ") " + oAtributo.referencia.unidade.urlDecode();
                    break;

                case REFERENCIA_FIXA:

                    sStringReferencia = oAtributo.referencia.fixo.urlDecode();
                    break;
            }
            aLinha[4] = sStringReferencia;
        }

        aLinha[5] = oAtributo.codigoreferencia;

        if (oAtributo.temFormula === true) {
            let atributoBloquear = {
                linha: iSeq,
                formula: oAtributo.formula
            };

            linhasParaBloquear.push(atributoBloquear);
        }

        oGridAtributosExame.addRow(aLinha);
        if (oAtributo.tipo == 1) {
            oGridAtributosExame.aRows[iSeq].sStyle += ";font-weight:bold";
        }

    });

    // Bloqueia o campo
    linhasParaBloquear.each(function(atributo) {
        oGridAtributosExame.aRows[atributo.linha].setClassName('disabled');
    });

    oGridAtributosExame.renderRows();

    // Adiciona hint com a fórmula
    linhasParaBloquear.each(function(atributo) {
        oGridAtributosExame.setHint(atributo.linha, 0, atributo.formula.urlDecode());
        oGridAtributosExame.setHint(atributo.linha, 1, atributo.formula.urlDecode());
        oGridAtributosExame.setHint(atributo.linha, 2, atributo.formula.urlDecode());
        oGridAtributosExame.setHint(atributo.linha, 3, atributo.formula.urlDecode());
        oGridAtributosExame.setHint(atributo.linha, 4, atributo.formula.urlDecode());
    });

    var oPrimeiroAtributo = null;
    oSelf.aAtributos.each(function(oAtributo) {

        // oAtributo.setAttribute('tabindex','1000');
        // implementa ação ao link do atributo para lançar titulação
        if ($("atributo_obs_" + oAtributo.codigo)) {
            $("atributo_obs_" + oAtributo.codigo).addEventListener('click', oSelf.lancarTitulacao.bind(this, oAtributo, oSelf));
        }

        if (!$("atributo_" + oAtributo.codigo)) {
            return;
        }

        if ($("atributo_" + oAtributo.codigo)) {

            $("atributo_" + oAtributo.codigo).addEventListener('paste', oSelf.bloqueiaEventos.bind(this, $("atributo_" + oAtributo.codigo)));
            $("atributo_" + oAtributo.codigo).addEventListener('drop', oSelf.bloqueiaEventos.bind(this, $("atributo_" + oAtributo.codigo)));
            $("atributo_" + oAtributo.codigo).addEventListener('change', oSelf.validaValorInformado.bind(this, $("atributo_" + oAtributo.codigo), oAtributo, oSelf));
            $("atributo_" + oAtributo.codigo).addEventListener('keypress', oSelf.validaValorInformado.bind(this, $("atributo_" + oAtributo.codigo), oAtributo, oSelf));

            if (oPrimeiroAtributo == null) {
                oPrimeiroAtributo = $("atributo_" + oAtributo.codigo);
            }
        }

        if ($("atributo_" + oAtributo.codigo + "_percentual")) {

            $("atributo_" + oAtributo.codigo + "_percentual").observe('keypress', function(event) {

                if (!js_teclas(event)) {

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return false;
                }
            });

            $("atributo_" + oAtributo.codigo + "_percentual").addEventListener('paste', oSelf.bloqueiaEventos.bind(this, $("atributo_" + oAtributo.codigo + "_percentual")));
            $("atributo_" + oAtributo.codigo + "_percentual").addEventListener('drop', oSelf.bloqueiaEventos.bind(this, $("atributo_" + oAtributo.codigo + "_percentual")));
            if (oPrimeiroAtributo == null) {
                oPrimeiroAtributo = $("atributo_" + oAtributo.codigo + "_percentual");
            }
        }

        if (oAtributo.tiporeferencia != REFERENCIA_NUMERICA) {
            return;
        }
        if ($("atributo_" + oAtributo.codigo)) {
            oSelf.sinalizaInput(oAtributo.codigo, $("atributo_" + oAtributo.codigo));
        }
        oPrimeiroAtributo.setAttribute('class', 'primeiro-campo');
    });
    if(bSelecionou){
        oPrimeiroAtributo.focus();
    }

    oSelf.atualizarValoresAtributosFormula();
};

/**
 * Cria um input para informacao do valor percentual
 *
 * @private
 * @param oAtributo
 * @returns {string}
 */
LancamentoExameLaboratorio.prototype.inputPercentual = function(oAtributo, sequencial) {

    if (oAtributo.referencia == '') {
        return '';
    }
    var sCampo = '';
    var sBloqueioTela = '';
    var sReadOnly = '';
    //var sFuncaoCalculo = "onchange='"+this.sNameInstance+".calcularValorAbsoluto("+oAtributo.codigo+", this)';";
    var sFuncaoCalculo = "onchange='" + this.sNameInstance + ".calcularPorcentagem(" + oAtributo.codigo + ", this)';";


    if (this.lReadOnly || oAtributo.temFormula === true) {

        sBloqueioTela = ' border:0px;';
        sFuncaoCalculo = '';
        sReadOnly = 'readonly="readonly" tabindex="-1"';
    }


    if (oAtributo.referencia.tipo == REFERENCIA_NUMERICA &&
        (oAtributo.referencia.tipocalculo == 2)) { // (oAtributo.referencia.tipocalculo == 1 || oAtributo.referencia.baseparacalculo) ) {

        if (oAtributo.referencia.baseparacalculo) {

            sReadOnly = 'readonly="readonly" tabindex="-1"';
            sFuncaoCalculo = '';
        }

        sCampo = "<input class='campoAtributoExame' type='text'" + sReadOnly + " style='width:99%;text-align: right;" + sBloqueioTela + "'";
        sCampo += " id='atributo_" + oAtributo.codigo + "_percentual' " + sFuncaoCalculo;
        sCampo += " value='" + oAtributo.valorpercentual;

        sCampo += "'>";
    } else if (oAtributo.referencia.baseparacalculo) {
        sCampo = '100';
    }

    return sCampo;
};

/**
 * Cria um input de texto numerico, validando seus intervalos
 * @private
 * @param oAtributo
 * @returns {string}
 */
LancamentoExameLaboratorio.prototype.inputValorNumerico = function(oAtributo, sequencial) {

    if (oAtributo.referencia == '') {
        return '';
    }

    var oSelf = this;
    var oInput = new Element('input', { 'type': 'text', 'id': 'atributo_' + oAtributo.codigo, 'value': oAtributo.valorabsoluto.urlDecode(), 'onchange': this.sNameInstance + '.verificaFormulas(' + oAtributo.codigo + ', this)' });
    oInput.style = 'width:99%; text-align: right';

    if (oAtributo.referencia.tipocalculo == 1 || oAtributo.referencia.tipocalculo == 2) { // oAtributo.referencia.tipocalculo == 1
        oInput.setAttribute('readonly', 'readonly');
        oInput.setAttribute('tabindex', '-1');
        oInput.style.border = '0px';
    }

    if (this.lReadOnly || oAtributo.temFormula === true) {

        oInput.setAttribute('readonly', 'readonly');
        oInput.setAttribute('tabindex', '-1');
        oInput.style.border = '0px';
    }

    if (oAtributo.referencia.tipocalculo != 2 && oAtributo.temFormula === false) {
    }

    return oInput.outerHTML;
};

/**
 * Cria uminput de digitacao livre
 *
 * @private
 * @param oAtributo
 * @returns {string}
 */
LancamentoExameLaboratorio.prototype.inputValorFixo = function(oAtributo, sequencial) {

    var oInput = new Element('input', { type: 'text', value: oAtributo.valorabsoluto.urlDecode(), style: 'width:98%' });
    oInput.setAttribute("id", 'atributo_' + oAtributo.codigo);
    oInput.setAttribute("maxlength", 50);

    if (this.lReadOnly || oAtributo.temFormula) {

        oInput.style.border = "0px";
        oInput.setAttribute("readonly", "readonly");
        oInput.setAttribute("tabindex", "-1");
    }

    if (oAtributo.referencia.tipocalculo != 2) {
    }

    return oInput.outerHTML;
};

LancamentoExameLaboratorio.prototype.comboBoxAtributos = function(oAtributo) {

    var sValorTexto = '';
    var sSelect = "<select style='width:100%' id='atributo_" + oAtributo.codigo + "' tabindex='"+oAtributo.iTabIndex+"'>";
    oAtributo.referencia.selecoes.each(function(oSelecao, iSeq) {

        var sSelected = '';
        if (oSelecao.codigo == oAtributo.valorabsoluto) {

            sSelected = ' selected ';
            sValorTexto = oSelecao.nome.urlDecode();
        }
        sSelect += "<option value='" + oSelecao.codigo + "'" + sSelected + ">" + oSelecao.nome.urlDecode() + "</option>";
    });

    sSelect += "</selectd>";
    if (this.lReadOnly) {
        sSelect = sValorTexto;
    }

    return sSelect;
};

/**
 * Realiza a validação dos valores digitados
 *
 * @private
 * @param iCodigoAtributo
 * @param oInput
 */
LancamentoExameLaboratorio.prototype.validaValores = function(iCodigoAtributo, oInput) {

    var oAtributo = this.getAtributo(iCodigoAtributo);
    var oReferencia = oAtributo.referencia;
    var oSelf = this;

    if (oReferencia == '' || oInput.value == '') {
        return;
    }

    var nReferenciaMinimo = oReferencia.faixaabsurdoinicio.replace(',', '.');
    var nReferenciaMaximo = oReferencia.faixaasurdomaximo.replace(',', '.');

    var nValor = new Number(oInput.value).valueOf();
    var sValorMinimo = new Number(nReferenciaMinimo).valueOf();
    var sValorMaximo = new Number(nReferenciaMaximo).valueOf();

    if (this.habilitarAbsurdo) {
        if (nValor < sValorMinimo || nValor > sValorMaximo) {

            var sStringIntervalor = "(" + sValorMinimo + " até ";
            sStringIntervalor += sValorMaximo + ") " + oAtributo.referencia.unidade.urlDecode() + ' para ' + oAtributo.descricao.urlDecode();

            var oPropriedades = {};
            oPropriedades.sValor = sStringIntervalor;
            alert(_M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'fora_valores_absurdos', oPropriedades));
        }
    }

    /**
     * Verifica se o valor informado para o percentual do atributo ultrapassa o valor de percentual do atributo base
     */
    if (!this.calculaTotalPercentual(oAtributo.referencia.atributobase)) {

        alert(_M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'valor_acima_porcentagem'));
        $('atributo_' + oAtributo.codigo + '_percentual').value = 0;
        $('atributo_' + oAtributo.codigo).value = 0;
    }

    oSelf.sinalizaInput(iCodigoAtributo, oInput);
    if (oAtributo.referencia.baseparacalculo) {

        oSelf.aAtributos.each(function(oAtributoCalculo, iSeq) {

            if (oAtributoCalculo.referencia == '' || (oAtributoCalculo.referencia.tipocalculo != 2)) {
                return;
            }

            if (oAtributoCalculo.referencia.atributobase == iCodigoAtributo) {
                oSelf.calcularPorcentagem(oAtributoCalculo.codigo, $('atributo_' + oAtributoCalculo.codigo + '_percentual'));
            }
        });
    }
};

/**
 * Realiza a marcacas das cores dos textos conforme seu resultado
 *
 * @private
 * @param iCodigoAtributo
 * @param oInput
 */
LancamentoExameLaboratorio.prototype.sinalizaInput = function(iCodigoAtributo, oInput) {

    var oAtributo = this.getAtributo(iCodigoAtributo);
    var oReferencia = oAtributo.referencia;
    if (oReferencia == '' || oInput.value == '') {
        return;
    }

    var nNormaMinimo = oReferencia.faixanormalminimo.replace(',', '.');
    var nNormaMaximo = oReferencia.faixanormalmaximo.replace(',', '.');

    var nValor = new Number(oInput.value).valueOf();
    var sValorMinimo = new Number(nNormaMinimo).valueOf();
    var sValorMaximo = new Number(nNormaMaximo).valueOf();
    oInput.style.color = 'green';

    if (nValor < sValorMinimo || nValor > sValorMaximo) {
        oInput.style.color = 'red';
    }
};

/**
 * Pesquisa um atributro pelo codigo
 *
 * @private
 * @param iCodigoAtributo
 * @returns {string}
 */
LancamentoExameLaboratorio.prototype.getAtributo = function(iCodigoAtributo) {

    var oAtributoRetorno = '';
    this.aAtributos.each(function(oAtributo) {

        if (oAtributo.codigo == iCodigoAtributo) {

            oAtributoRetorno = oAtributo;
            return;
        }
    });

    return oAtributoRetorno;
};

/**
 * Checa se existem campos com fórmula fazendo referência ao campo atual
 *
 * @private
 * @param iAtributo
 * @param oInput
 */
LancamentoExameLaboratorio.prototype.verificaFormulas = function(iAtributo, oInput) {

    let self = this;
    let atributoFormulaAtual = null;

    if (self.atributosFormula.lenght === 0) {
        return;
    }

    self.atributosFormula.each(function(atributoFormula) {

        let calcularFormula = true;
        atributoFormulaAtual = atributoFormula.codigo_atributo;

        atributoFormula.atributosFormula.each(function(atributo) {

            let codigoAtributoFormula = new Number(atributo.codigo);
            let codigoAtributoAlterado = new Number(iAtributo);

            if (codigoAtributoFormula.valueOf() === codigoAtributoAlterado.valueOf()) {
                atributo.valor = oInput.value;
            }

            if (atributo.valor === '') {
                calcularFormula = false;
            }
        });

        if (calcularFormula === true) {
            self.executarCalculoFormula(atributoFormula);
        }

        if (calcularFormula === false) {
            $('atributo_' + atributoFormula.codigo_atributo).value = '';
        }
    });
};


/**
 * Realiza o calculo do valor absoluto do atributo (NÃO ESTÁ SENDO UTILIZADA. VER calcularPorcentagem())
 *
 * @private
 * @param iAtributo
 * @param oInput
 */
LancamentoExameLaboratorio.prototype.calcularValorAbsoluto = function(iAtributo, oInput) {

    var oAtributo = this.getAtributo(iAtributo);
    if (oAtributo.referencia.atributobase == '') {
        return;
    }

    var nValorBase = new Number($F('atributo_' + oAtributo.referencia.atributobase)).valueOf();
    var nPercentualBase = new Number($F('atributo_' + oAtributo.referencia.atributobase + "_percentual")).valueOf();
    var nPercentualDigitado = new Number(oInput.value).valueOf();
    var nValorAbsoluto = new Number((nPercentualDigitado * nValorBase) / nPercentualBase);

    $('atributo_' + iAtributo).value = nValorAbsoluto;
    this.validaValores(iAtributo, $('atributo_' + iAtributo));
};

/**
 * Realiza o calculo do valor absoluto do atributo
 *
 * @private
 * @param iAtributo
 * @param oInput
 */
LancamentoExameLaboratorio.prototype.calcularPorcentagem = function(iAtributo, oInput) {

    var oAtributo = this.getAtributo(iAtributo);
    if (oAtributo.referencia.atributobase == '') {
        return;
    }

    if (oInput == '') {
        return;
    }
    var nValorBase = new Number($F('atributo_' + oAtributo.referencia.atributobase)).valueOf();
    var nPercentualDigitado = new Number(oInput.value).valueOf();
    var nValorAbsoluto = new Number((nValorBase / 100) * nPercentualDigitado);

    $('atributo_' + iAtributo).value = nValorAbsoluto;
    this.validaValores(iAtributo, $('atributo_' + iAtributo));
};

/**
 * Cria um componente de entrada conforme sua referencia
 *
 * @private
 * @param oLinha
 * @returns {string}
 */
LancamentoExameLaboratorio.prototype.inputValorAbsoluto = function(oLinha, sequencial) {

    if (oLinha.tipo == 1) {
        return '';
    }

    var sCampo = '';
    switch (oLinha.referencia.tipo) {

        case REFERENCIA_NUMERICA:

            sCampo = this.inputValorNumerico(oLinha, sequencial);
            break;

        case REFERENCIA_FIXA:

            sCampo = this.inputValorFixo(oLinha, sequencial);
            break;

        case REFERENCIA_SELECIONAVEL:

            sCampo = this.comboBoxAtributos(oLinha);
            break;
    }
    return sCampo;
};

/**
 * Salva os dados do exame
 * @returns {boolean}
 */
LancamentoExameLaboratorio.prototype.salvar = function(lExibeMensagem, origenDigitacaoExame= false, salvarConferir = false) {
    if (lExibeMensagem && origenDigitacaoExame==false) {

        if (!confirm(_M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'confirma_valores'))) {
            return false;
        }
    }

    var oSelf = this;
    var aAtributos = [];

    aAtributosGrid = oGridAtributosExame.aRows.each(function(oLinha) {

        var oAtributo = oSelf.getAtributo(oLinha.aCells[0].getValue());
        if (oAtributo.tipo == 1) {
            return;
        }

        var oAtributoValor = {

            iCodigoAtributo: oLinha.aCells[0].getValue(),
            nValorPercentual: parseFloat(oLinha.aCells[2].getValue().trim()),
            iCodigoReferencia: oLinha.aCells[5].getValue().trim(),
            nValorAbsoluto: encodeURIComponent(tagString(oLinha.aCells[3].getValue().trim())),
            sTitulacao: encodeURIComponent(tagString(oAtributo.titulacao))
        };

        aAtributos.push(oAtributoValor);
    });

    var oParam = {
        exec: 'salvarResultadoExame',
        iCodigoExame: this.iCodigoRequisicao,
        sObservacao: encodeURIComponent(tagString(this.oTextAreaObservacao.value)),
        sMotivoNovaColeta: encodeURIComponent(tagString(this.otextAreaMotivoNovaColeta.value)),
        aAtributos: aAtributos
    };

    if (origenDigitacaoExame == false){
        js_divCarregando(_M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'salvando_exame'), 'msgBox');
    }
    new Ajax.Request(oSelf.sUrlRPC, {
        method: 'post',
        parameters: 'json=' + Object.toJSON(oParam),
        onComplete: function(oResponse) {

            js_removeObj('msgBox');
            var oRetorno = JSON.parse(oResponse.responseText);

            if(oSelf.otextAreaMotivoNovaColeta.value != ""){
                oSelf.callbackAfterNovaColeta(oRetorno);
            }else if (origenDigitacaoExame && oRetorno.status === 1){
                if(salvarConferir){
                    oSelf.confirmarExame(origenDigitacaoExame);
                }else{
                    oSelf.callbackAfterSalvar(oRetorno);
                }
            }else{
                oSelf.callbackAfterSalvar(oRetorno);
            }
        }
    });
};

LancamentoExameLaboratorio.prototype.setReadOnly = function(lReadOnly) {

    this.lReadOnly = lReadOnly;
    this.oBtnSalvar.style.display = '';
    this.oBtnSalvar.disabled = lReadOnly;

    if (lReadOnly) {
        this.oBtnSalvar.style.display = 'none';
    }
};

/**
 * Monta uma WindowAux e agrega a gridAtributos a ela, abrindo a grid em uma nova janela
 * @param  integer iLancamentoExame Código da RequisicaoExame
 */
LancamentoExameLaboratorio.prototype.abrirComoJanela = function(iLancamentoExame) {

    this.lAbrirComoJanela = true;

    var oSelf = this;

    if ($('wndLancamentoExame')) {
        return false;
    }

    this.oWindowLancamentoExame = new windowAux('wndLancamentoExame', 'Lançamento de Exames', 800, 650);

    oSelf.oWindowLancamentoExame.setShutDownFunction(function() {
        oSelf.oWindowLancamentoExame.destroy();
    });

    var sConteudo = '<div style="height:78%;width:97%;">';
    sConteudo += '    <div id="ctnGridResultado"></div>';
    sConteudo += '</div>';

    this.oWindowLancamentoExame.setContent(sConteudo);

    var sMensagemExame = _M(MENSAGENS_LANCAMENTO_EXAME_LABORATORIO + 'dados_exame');
    new DBMessageBoard(
        'msgLancamentoExame',
        'Dados do Exame',
        sMensagemExame,
        oSelf.oWindowLancamentoExame.getContentContainer()
    );

    if (this.aCIDs.length) {

        this.aCIDs.forEach(function(oCID) {

            var oCIDOption = document.createElement('option');
            oCIDOption.value = oCID.iCodigo;
            oCIDOption.innerHTML = oCID.sCID + " - " + oCID.sNome.urlDecode();

            if (oSelf.iCIDConferido == oCID.iCodigo) {
                oCIDOption.selected = true;
            }

            oSelf.oCIDSelect.appendChild(oCIDOption);
        });

        this.oElementoDivCID.style.display = '';
    }

    this.oBtnConfirmar.style.display = '';
    this.oBtnFechar.style.display = '';


    oSelf.setRequisicao(iLancamentoExame);
    this.show($('ctnGridResultado'));
    this.oWindowLancamentoExame.show();
    this.lancarMedicamentos();

    this.oBtnFechar.observe("click", function() {
        oSelf.oWindowLancamentoExame.destroy();
    });

};

/**
 * Seta se o campo observação deve ser mostrado ou não
 * @param  {boolean} lMostraCampoObservacao
 */
LancamentoExameLaboratorio.prototype.mostraCampoObservacao = function(lMostraCampoObservacao) {

    if (lMostraCampoObservacao) {
        this.oElementoDivObservacao.style.display = '';
    }
};

LancamentoExameLaboratorio.prototype.bloqueiaEventos = function(oElement, oEvent) {

    var aType = ['paste', 'drop'];

    if (aType.in_array(oEvent.type)) {

        oElement.value = '';
        oEvent.preventDefault();
        oEvent.stopImmediatePropagation();
    }
};

LancamentoExameLaboratorio.prototype.validaValorInformado = function(oElement, oAtributo, oSelf, oEvent) {

    switch (oAtributo.referencia.tipo) {

        case REFERENCIA_FIXA:
        case REFERENCIA_SELECIONAVEL:

            break
        case REFERENCIA_NUMERICA:

            if (oEvent.type == 'keypress' && !js_teclas(oEvent)) {

                oEvent.preventDefault();
                oEvent.stopImmediatePropagation();
                return false;
            }
            if (oEvent.type == 'change') {
                oSelf.validaValores(oAtributo.codigo, oElement);
            }
            break;
    }
};

/**
 * Soma a quantidade de porcentagem informada para todos os atributos que referenciam o atributo base informado e valida
 * se o total somado ultrapassa o valor percentual do atributo base
 * @param  {integer} iAtributoBase Código do Atributo Base
 * @return {boolean}
 */
LancamentoExameLaboratorio.prototype.calculaTotalPercentual = function(iAtributoBase) {

    var oAtributoBase = this.getAtributo(iAtributoBase);
    var iTotalPercentual = 0;

    this.aAtributos.each(function(oAtributo) {

        if (oAtributo.referencia.atributobase == oAtributoBase.codigo) {

            if ($('atributo_' + oAtributo.codigo + '_percentual') && $('atributo_' + oAtributo.codigo + '_percentual').value != '') {
                iTotalPercentual += new Number($('atributo_' + oAtributo.codigo + '_percentual').value);
            }
        }
    });

    if (iTotalPercentual > oAtributoBase.valorpercentual) {
        return false;
    }
    return true;
};

LancamentoExameLaboratorio.prototype.setCallbackSalvar = function(sFunction) {
    this.callbackAfterSalvar = sFunction;
};

LancamentoExameLaboratorio.prototype.setCallbackConferir = function(sFunction) {
    this.callbackAfterConferir = sFunction;
};

LancamentoExameLaboratorio.prototype.setCallBackNovaColeta = function(sFunction) {
    this.callbackAfterNovaColeta = sFunction;
};

LancamentoExameLaboratorio.prototype.clear = function(sFunction) {

    $('textAreaObservacao').value = '';
    $('textAreaMotivoNovaColeta').value = '';

    oGridAtributosExame.clearAll(true);

    this.aAtributos = [];
};

LancamentoExameLaboratorio.prototype.lancarMedicamentos = function() {

    var oSelf = this;

    this.oBtnLancarMedicamento.observe('click', function() {

        var oMedicamento = new LancarMedicamentoExame('oMedicamento', oSelf.iCodigoRequisicao);
        oMedicamento.show();
        if (oSelf.lAbrirComoJanela) {

            oMedicamento.setParentWindowAux(oSelf.oWindowLancamentoExame);
        }

    });
};

LancamentoExameLaboratorio.prototype.setCIDs = function(aCIDs) {
    this.aCIDs = aCIDs;
};

LancamentoExameLaboratorio.prototype.setProcedimento = function(iCodigoProcedimento) {
    this.iCodigoProcedimento = iCodigoProcedimento;
};

LancamentoExameLaboratorio.prototype.setPermitidoConferir = function(permitido) {
    this.permitidoConferir = permitido;
    if(this.permitidoConferir == 't'){
        this.oBtnSalvarConferir.setAttribute('type','button');
    }else{
        this.oBtnSalvarConferir.setAttribute('type','hidden');
    }
};

LancamentoExameLaboratorio.prototype.confirmarExame = function(origemDigitacaoExame = false) {
    var oSelf = this,
        oParametro = {};

    var procedimento = this.iCodigoProcedimento;

    if(origemDigitacaoExame){
        oParametro.exec = 'buscarProcedimento';
        oParametro.requisicaoItem = this.iCodigoRequisicao;

        var oRequest = {};
        oRequest.method = 'post';
        oRequest.parameters = 'json=' + Object.toJSON(oParametro);
        oRequest.asynchronous = false;

        oRequest.onComplete = function(oAjax) {

            js_removeObj("msgBoxB");

            var oRetorno = JSON.parse(oAjax.responseText);
            procedimento = oRetorno.procedimento;
            alert(oRetorno.sMensagem.urlDecode());

            if (oRetorno.iStatus == '2') {
                return false;
            }
        };

        new Ajax.Request(oSelf.sUrlRPC, oRequest);
    }

    var oSelf = this,
        oParametro = {};

    oParametro.exec = 'salvarConferencia';
    oParametro.iCodigo = $F('la22_i_codigo');
    oParametro.lConferido = true;
    oParametro.aExames = [];

    var oExame = {};
    oExame.iCodigoRequisicaoExame = this.iCodigoRequisicao;
    oExame.iCodigoCID = this.oCIDSelect.value;
    oExame.iProcedimento = procedimento;
    oParametro.aExames.push(oExame);


    var oRequest = {};
    oRequest.method = 'post';
    oRequest.parameters = 'json=' + Object.toJSON(oParametro);
    oRequest.asynchronous = false;
    var oCID = null;

    if (oExame.iCodigoCID != '') {

        var aDadosCID = this.oCIDSelect.options[this.oCIDSelect.selectedIndex].text.split(' - ');

        oCID = {
            'sEstruturalCidConferido': aDadosCID[0],
            'sNomeCidConferido': aDadosCID[1]
        };
    }

    oRequest.onComplete = function(oAjax) {

        js_removeObj("msgBoxB");

        var oRetorno = JSON.parse(oAjax.responseText);
        if(!origemDigitacaoExame){
            alert(oRetorno.sMensagem.urlDecode());
        }

        if (oRetorno.iStatus == '2') {
            return false;
        }
        if(origemDigitacaoExame){
            oSelf.callbackAfterConferir(oRetorno);
        }else{
            oSelf.callbackAfterConferir(oCID);
        }
    };
    js_divCarregando(_M(MSG_LANCAMENTOLABCONFERENCIA + "aguarde_salvando_conferencia"), "msgBoxB");
    new Ajax.Request(oSelf.sUrlRPCConferencia, oRequest);
    document.getElementById('la22_i_codigo').focus();
};

LancamentoExameLaboratorio.prototype.setCodigoCIDConferido = function(iCIDConferido) {
    this.iCIDConferido = iCIDConferido;
};

/**
 * Monta a window para inserção da titulação
 * @param  {Object}                     oAtributo
 * @param  {LancamentoExameLaboratorio} oSelf
 * @return {void}
 */
LancamentoExameLaboratorio.prototype.lancarTitulacao = function(oAtributo, oSelf) {

    if ($("wndLancaTitulacaoAtributo")) {
        return;
    }

    var oWindowTitulacao = new windowAux("wndLancaTitulacaoAtributo", "Titulação", 450, 240);
    oWindowTitulacao.setShutDownFunction(function() {
        oWindowTitulacao.destroy();
    });

    oWindowTitulacao.allowCloseWithEsc(true);

    var sConteudo = "<div class='subcontainer'>                                                  \n";
    sConteudo += "  <fieldset id='ctnTitulacao'>                                           \n";
    sConteudo += "    <legend><label for='titulacaoAtributo'>Titulação</label></legend>   \n";
    sConteudo += "    <textarea rows='4' cols='50' id='titulacaoAtributo' > </textarea>    \n";
    sConteudo += "  </fieldset>                                                            \n";
    sConteudo += "  <input type='button' value='Adicionar' id='salvarTitulacao' />  \n";
    sConteudo += "</div>                                                                   \n";

    oWindowTitulacao.setShutDownFunction(function() {
        oWindowTitulacao.destroy();
    });

    var sHelpMsgBox = ' Titular: <b>' + oAtributo.descricao.urlDecode() + '</b> ';

    oWindowTitulacao.setContent(sConteudo);
    var oMessageBoard = new DBMessageBoard('msgBoardTitulacao' + oAtributo.codigo,
        'Titular: <b>' + oAtributo.descricao.urlDecode() + '</b> ',
        'Adicione a titulação e clique em salvar.',
        oWindowTitulacao.getContentContainer()
    );
    oWindowTitulacao.show();

    if ($('wndLancaTitulacaoAtributo')) {

        setTimeout(function() {
            $('wndLancaTitulacaoAtributo').style.zIndex = 99999;
        }, 1);
    }

    $('titulacaoAtributo').value = undoTagString(oAtributo.titulacao);
    $('salvarTitulacao').addEventListener('click', oSelf.salvarTitulacao.bind(this, oAtributo.codigo, $('titulacaoAtributo'), oSelf, oWindowTitulacao));
};

/**
 * Salva na classe a titulação informada
 * @param  {integer}                    iAtributo  código do atributo
 * @param  {HTMLTextAreaElement}        oTitulacao
 * @param  {LancamentoExameLaboratorio} oSelf
 * @param  {windowAux}                  oWindow
 * @return {void}
 */
LancamentoExameLaboratorio.prototype.salvarTitulacao = function(iAtributo, oTitulacao, oSelf, oWindow) {

    for (var oAtributo of oSelf.aAtributos) {

        if (oAtributo.codigo == iAtributo) {
            oAtributo.titulacao = oTitulacao.value;
        }
    }

    oWindow.destroy();
};

LancamentoExameLaboratorio.prototype.executarCalculoFormula = function(atributoFormula) {

    let self = this;
    let parametros = {
        'exec': 'calcularFormula',
        'requisicaoExame': self.iCodigoRequisicao,
        'atributoExame': atributoFormula.codigo_atributo,
        'atributosFormula': atributoFormula.atributosFormula,
    };

    new Ajax.Request(
        self.sUrlRPC, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(parametros),
            onComplete: function(response) {

                let retorno = JSON.parse(response.responseText);

                if (retorno.status == 2) {
                    alert(retorno.mensagem.urlDecode());
                    return;
                }

                const valorPrevio = $('atributo_' + retorno.codigoAtributo).value;
                $('atributo_' + retorno.codigoAtributo).value = retorno.valor;

                if (valorPrevio !== retorno.valor) {
                    $('atributo_' + retorno.codigoAtributo).dispatchEvent(new Event('change'));
                }
            }
        }
    );
};

LancamentoExameLaboratorio.prototype.atualizarValoresAtributosFormula = function() {

    let self = this;

    if (self.atributosFormula.lenght === 0) {
        return;
    }

    self.atributosFormula.each(function(atributoFormula) {

        atributoFormula.atributosFormula.each(function(atributo) {

            if ($('atributo_' + atributo.codigo) !== null) {
                atributo.valor = $('atributo_' + atributo.codigo).value;
            }
        });
    });
};
