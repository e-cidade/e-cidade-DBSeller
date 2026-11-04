/**
 *
 * Componente para o formulário de cálculo do ISSQN
 *
 * @author Alberto Ferri Neto alberto@dbseller.com.br
 * @package ISSQN
 * @revision $Author: dbpietro.carrara $
 * @version $Revision: 1.13 $
 *
 */
DBViewCalculoIssqn = function (sInstancia, sNomeComponente) {

    /**
     * Instância do Componente
     */
    var me = this;
    var oElementoDestino = null;
    var iInscricao = null;
    var lUsaJanela = false;
    var inscricaoVeiculo = false;
    this.sIntancia = sInstancia;
    this.sNomeComponente = sNomeComponente;
    this.oForm = new Object();

    this.setInscricao = function (iCodigo) {
        iInscricao = iCodigo;
    };

    this.setInscricaoVeiculo = function (isIncricaoVeiculo) {
        inscricaoVeiculo = isIncricaoVeiculo;
    };
    /**
     * Cria HTML BáSICO do Componente
     */
    this.renderizarHTML = function () {

        //fieldset do formulário
        me.oForm.oFieldset = document.createElement('fieldset');
        me.oForm.oFieldset.style.margin = '10px auto 0 auto ';
        me.oForm.oFieldset.style.width = '750px';

        oElementoDestino.appendChild(me.oForm.oFieldset);

        me.oForm.oLegend = document.createElement('legend');
        me.oForm.oLegend.innerHTML = '<strong>Cálculo de Alvará</strong>';
        me.oForm.oFieldset.appendChild(me.oForm.oLegend);


        me.oForm.oTable = document.createElement('table');
        me.oForm.oTable.style.margin = '0 auto';
        me.oForm.oFieldset.appendChild(me.oForm.oTable);

        /**
         * Criando linha para o tipo de Calculo
         */
        me.oForm.oLinhaTipo = document.createElement('tr');
        me.oForm.oTable.appendChild(me.oForm.oLinhaTipo);

        me.oForm.oCelulaLabelTipo = document.createElement('td');
        me.oForm.oCelulaLabelTipo.style.width = "130px";
        me.oForm.oCelulaLabelTipo.innerHTML = '<strong>Tipo de Cálculo:</strong>';
        me.oForm.oCelulaCboTipo = document.createElement('td');
        me.oForm.oCelulaCboTipo.innerHTML = '<div id="' + me.sNomeComponente + '_cboTipo"></div>';

        me.oForm.oLinhaTipo.appendChild(me.oForm.oCelulaLabelTipo);
        me.oForm.oLinhaTipo.appendChild(me.oForm.oCelulaCboTipo);

        /**
         * Linha para a Quantidade de Parcelas Alvará
         */
        me.oForm.oLinhaParcelasAlvara = document.createElement('tr');
        me.oForm.oLinhaParcelasAlvara.id = sNomeComponente + "_linhaParcelasAlvara";
        me.oForm.oTable.appendChild(me.oForm.oLinhaParcelasAlvara);

        me.oForm.oCelulaLabelParcelasAlvara = document.createElement('td');
        me.oForm.oCelulaLabelParcelasAlvara.innerHTML = '<strong>Número de Parcelas Alvará:</strong>';
        me.oForm.oCelulaNumeroParcelasAlvara = document.createElement('td');
        me.oForm.oCelulaNumeroParcelasAlvara.innerHTML = '<div id="' + me.sNomeComponente + '_cboParcelasAlvara"></div>';

        me.oForm.oLinhaParcelasAlvara.appendChild(me.oForm.oCelulaLabelParcelasAlvara);
        me.oForm.oLinhaParcelasAlvara.appendChild(me.oForm.oCelulaNumeroParcelasAlvara);

        /**
         * Linha para a Quantidade de Parcelas Issqn
         */
        me.oForm.oLinhaParcelasIssqn = document.createElement('tr');
        me.oForm.oLinhaParcelasIssqn.id = sNomeComponente + "_linhaParcelasIssqn";
        me.oForm.oTable.appendChild(me.oForm.oLinhaParcelasIssqn);

        me.oForm.oCelulaLabelParcelasIssqn = document.createElement('td');
        me.oForm.oCelulaLabelParcelasIssqn.innerHTML = '<strong>Número de Parcelas do ISSQN Fixo:</strong>';
        me.oForm.oCelulaNumeroParcelasIssqn = document.createElement('td');
        me.oForm.oCelulaNumeroParcelasIssqn.innerHTML = '<div id="' + me.sNomeComponente + '_cboParcelasIssqn"></div>';

        me.oForm.oLinhaParcelasIssqn.appendChild(me.oForm.oCelulaLabelParcelasIssqn);
        me.oForm.oLinhaParcelasIssqn.appendChild(me.oForm.oCelulaNumeroParcelasIssqn);

        /**
         * Linha para a Quantidade de Parcelas Publicidade
         */
        me.oForm.oLinhaParcelasPublicidade = document.createElement('tr');
        me.oForm.oLinhaParcelasPublicidade.id = sNomeComponente + "_linhaParcelasPublicidade";
        me.oForm.oTable.appendChild(me.oForm.oLinhaParcelasPublicidade);

        me.oForm.oCelulaLabelParcelasPublicidade = document.createElement('td');
        me.oForm.oCelulaLabelParcelasPublicidade.innerHTML = '<strong>Número de Parcelas Publicidade:</strong>';
        me.oForm.oCelulaNumeroParcelasPublicidade = document.createElement('td');
        me.oForm.oCelulaNumeroParcelasPublicidade.innerHTML = '<div id="' + me.sNomeComponente + '_cboParcelasPublicidade"></div>';

        me.oForm.oLinhaParcelasPublicidade.appendChild(me.oForm.oCelulaLabelParcelasPublicidade);
        me.oForm.oLinhaParcelasPublicidade.appendChild(me.oForm.oCelulaNumeroParcelasPublicidade);

        /**
         * Linha para a Quantidade de Parcelas Uso de Solo
         */
        me.oForm.oLinhaParcelasUsoSolo = document.createElement('tr');
        me.oForm.oLinhaParcelasUsoSolo.id = sNomeComponente + "_linhaParcelasUsoSolo";
        me.oForm.oTable.appendChild(me.oForm.oLinhaParcelasUsoSolo);

        me.oForm.oCelulaLabelParcelasUsoSolo = document.createElement('td');
        me.oForm.oCelulaLabelParcelasUsoSolo.innerHTML = '<strong>Número de Parcelas Uso de Solo:</strong>';
        me.oForm.oCelulaNumeroParcelasUsoSolo = document.createElement('td');
        me.oForm.oCelulaNumeroParcelasUsoSolo.innerHTML = '<div id="' + me.sNomeComponente + '_cboParcelasUsoSolo"></div>';

        me.oForm.oLinhaParcelasUsoSolo.appendChild(me.oForm.oCelulaLabelParcelasUsoSolo);
        me.oForm.oLinhaParcelasUsoSolo.appendChild(me.oForm.oCelulaNumeroParcelasUsoSolo);


        /**
         * Linha de exercicios para calculo exibida conforme parâmetro
         */
        me.oForm.oLinhaExercicios = document.createElement('tr');
        me.oForm.oLinhaExercicios.id = me.sNomeComponente + "_linhaExercicios";
        me.oForm.oLinhaExercicios.style.display = "none";
        me.oForm.oTable.appendChild(me.oForm.oLinhaExercicios);

        me.oForm.oCelulaLabelAnoCalculo = document.createElement('td');
        me.oForm.oCelulaLabelAnoCalculo.innerHTML = '<strong>Ano Cálculo</strong>';
        me.oForm.oCelulaCboAno = document.createElement('td');
        var sConteudo = "<span id='" + me.sNomeComponente + "_anoInicial'></span>";
        sConteudo += "<strong> até </strong>";
        sConteudo += "<span id='" + me.sNomeComponente + "_anoFinal'></span>";
        me.oForm.oCelulaCboAno.innerHTML = sConteudo;

        me.oForm.oLinhaExercicios.appendChild(me.oForm.oCelulaLabelAnoCalculo);
        me.oForm.oLinhaExercicios.appendChild(me.oForm.oCelulaCboAno);

        /**
         * Grid atividades
         */
        me.oForm.oFieldsetGrid = document.createElement('fieldset');
        me.oForm.oFieldsetGrid.style.margin = '10px auto 0 auto ';
        me.oForm.oFieldsetGrid.style.width = '750px';
        me.oForm.oLegendGrid = document.createElement('legend');
        me.oForm.oLabelGrid = document.createElement('strong');
        me.oForm.oLabelGrid.innerHTML = 'Atividades';
        me.oForm.oSpanGrid = document.createElement('div');
        me.oForm.oSpanGrid.id = me.sNomeComponente + '_gridAtividades';

        /**
         * Elementos grid atividades
         */
        me.oForm.oLegendGrid.appendChild(me.oForm.oLabelGrid);
        me.oForm.oFieldsetGrid.appendChild(me.oForm.oSpanGrid);
        me.oForm.oFieldsetGrid.appendChild(me.oForm.oLegendGrid);

        /**
         * Adicionando o Componente ao Destino
         */
        oElementoDestino.appendChild(me.oForm.oFieldsetGrid);
        if (!lUsaJanela) {

            var sConteudo = " <input type='button' value='Processar' onClick='" + sInstancia + ".processar();'> ";
            oElementoDestino.innerHTML = oElementoDestino.innerHTML + sConteudo;
        }
    };

    /**
     * Cria componentes existentes na biblioteca
     */
    this.criarComponentes = function () {

        me.oCboTipo = new DBComboBox(me.sNomeComponente + '_oComboTipo', sInstancia + '.oCboTipo', new Array(), '92');

        if (inscricaoVeiculo == false) {
            me.oCboTipo.addItem(0, 'Todos');
            me.oCboTipo.addItem(2, 'Alvará');
        }
        me.oCboTipo.addItem(1, 'ISSQN');
        me.oCboTipo.addItem(3, 'Publicidade');
        me.oCboTipo.addItem(4, 'Uso de Solo');

        me.oCboTipo.addEvent('onChange', sInstancia + '.validarTipoSelecao();');
        me.oCboTipo.show($(me.sNomeComponente + '_cboTipo'));

        me.oCboParcelasAlvara = new DBComboBox(me.sNomeComponente + '_oCboParcelasAlvara', sInstancia + '.oCboParcelasAlvara', [], '92');
        me.oCboParcelasAlvara.show($(me.sNomeComponente + '_cboParcelasAlvara'));

        me.oCboParcelasIssqn = new DBComboBox(me.sNomeComponente + '_oCboParcelasIssqn', sInstancia + '.oCboParcelasIssqn', [], '92');
        me.oCboParcelasIssqn.show($(me.sNomeComponente + '_cboParcelasIssqn'));

        me.oCboParcelasPublicidade = new DBComboBox(me.sNomeComponente + '_oCboParcelasPublicidade', sInstancia + '.oCboParcelasPublicidade', [], '92');
        me.oCboParcelasPublicidade.show($(me.sNomeComponente + '_cboParcelasPublicidade'));

        me.oCboParcelasUsoSolo = new DBComboBox(me.sNomeComponente + '_oCboParcelasUsoSolo', sInstancia + '.oCboParcelasUsoSolo', [], '92');
        me.oCboParcelasUsoSolo.show($(me.sNomeComponente + '_cboParcelasUsoSolo'));

        var oParametrosgetParametros = getParametros();

        $R(0, oParametrosgetParametros.iNumeroParcelasAlvara, true).each(function (iParcela) {
            me.oCboParcelasAlvara.addItem(iParcela + 1, iParcela + 1);
        });

        for(var i = 0; i < oParametrosgetParametros.iNumeroParcelasIssqn; i++) {
            me.oCboParcelasIssqn.addItem(i + 1, i + 1);
        }

        for(var i = 0; i < oParametrosgetParametros.iNumeroParcelasPublicidade; i++) {
            me.oCboParcelasPublicidade.addItem(i + 1, i + 1);
        }

        for(var i = 0; i < oParametrosgetParametros.iNumeroParcelasUsoSolo; i++) {
            me.oCboParcelasUsoSolo.addItem(i + 1, i + 1);
        }

        // Se o usuário não configurou as parcelas do issqn,
        // não as use
        if (oParametrosgetParametros.iNumeroParcelasIssqn == null) {
            me.oCboParcelasIssqn.addItem(0, 0);
            me.oCboParcelasIssqn.invisible = true;
        }

        // Se o usuário não configurou as parcelas de publicidade,
        // não as use
        if (oParametrosgetParametros.iNumeroParcelasPublicidade == null) {
            me.oCboParcelasPublicidade.addItem(0, 0);
            me.oCboParcelasPublicidade.invisible = true;
        }

        // Se o usuário não configurou as parcelas de uso de solo,
        // não as use
        if (oParametrosgetParametros.iNumeroParcelasUsoSolo == null) {
            me.oCboParcelasUsoSolo.addItem(0, 0);
            me.oCboParcelasUsoSolo.invisible = true;
        }

        if (getAnoCalculo().length > 0) {
            me.cboAnoCalculoInicial = new DBComboBox(me.sNomeComponente + '_cboAnoInicial', sInstancia + '.cboAnoCalculoInicial', [], '92');
            me.cboAnoCalculoFinal = new DBComboBox(me.sNomeComponente + '_cboAnoFinal', sInstancia + '.cboAnoCalculoFinal', [], '92');

            var iAnoAux;

            getAnoCalculo().each(function (iAno) {

                me.cboAnoCalculoInicial.addItem(iAno, iAno);
                me.cboAnoCalculoFinal.addItem(iAno, iAno);

                iAnoAux = iAno;
            });

            me.cboAnoCalculoInicial.setValue(iAnoAux);
            me.cboAnoCalculoFinal.setValue(iAnoAux);

            me.cboAnoCalculoInicial.show($(me.sNomeComponente + '_anoInicial'));
            me.cboAnoCalculoFinal.show($(me.sNomeComponente + '_anoFinal'));

            $(me.sNomeComponente + "_linhaExercicios").style.display = "";
        }
        me.montaGridAtividades();

        // Inicializa a visibilidade dos combos
        this.validarTipoSelecao();
    };

    this.montaGridAtividades = function () {

        oGridAtividades = new DBGrid('gridAtividades');

        oGridAtividades.nameInstance = 'oGridAtividades';

        aAlinhamentoColunas = new Array('center', 'left', 'center', 'center', 'center');
        aTamanhoColunas = new Array('10%', '60%', '10%', '10%', '10%');
        aCabecalhoColunas = new Array('Sequencial', 'Descrição', 'Data Início', 'Permanente', 'Quantidade');

        oGridAtividades.setCheckbox(0);
        oGridAtividades.setCellWidth(aTamanhoColunas);
        oGridAtividades.setCellAlign(aAlinhamentoColunas);
        oGridAtividades.setHeader(aCabecalhoColunas);
        oGridAtividades.show($(me.sNomeComponente + '_gridAtividades'));
        oGridAtividades.clearAll(true);
        oGridAtividades.setStatus('<span style="border:1px solid #000; margin-left:450px;padding-right: 15px; background-color: #d1f07c;"></span> Atividade Principal');

        if (this.getAtividades().length > 0) {

            this.getAtividades().each(function (oAtividade, iIndice) {

                var aRow = new Array();

                aRow[0] = oAtividade.iSequencial;
                aRow[1] = oAtividade.sDescricao.urlDecode();
                aRow[2] = js_formatar(oAtividade.dDataInicial, 'd');
                aRow[3] = oAtividade.lPermanente == 't' ? 'Sim' : 'Não';
                aRow[4] = oAtividade.iQuantidade;

                oGridAtividades.addRow(aRow, false, false, true);

                if (oAtividade.lPrincipal == 't') {
                    oGridAtividades.aRows[iIndice].setClassName('destacado');
                }

            });
            oGridAtividades.renderRows();
        }
    };

    var getParametros = function () {

        var oParam = new Object();
        oParam.sExec = 'getParametros';

        oParametrosIssqn = new Object();
        var oAjax = new Ajax.Request('iss4_isscalc.RPC.php',
            {
                method: 'POST',
                parameters: 'json=' + Object.toJSON(oParam),
                asynchronous: false,
                onComplete: function (oAjax) {

                    var oRetorno = JSON.parse(oAjax.responseText);
                    if (oRetorno.status == 1) {
                        oParametrosIssqn.iNumeroParcelasAlvara = oRetorno.iNumeroParcelasAlvara > 0 ? oRetorno.iNumeroParcelasAlvara : 1;
                        oParametrosIssqn.iNumeroParcelasIssqn = oRetorno.iNumeroParcelasIssqn;
                        oParametrosIssqn.iNumeroParcelasPublicidade = oRetorno.iNumeroParcelasPublicidade;
                        oParametrosIssqn.iNumeroParcelasUsoSolo = oRetorno.iNumeroParcelasUsoSolo;
                    }
                }
            });

        return oParametrosIssqn;
    };

    this.getAtividades = function () {

        var oParam = new Object();
        oParam.sExec = 'getAtividades';
        oParam.iInscricao = iInscricao;

        aAtividades = new Array();

        var oAjax = new Ajax.Request('iss4_isscalc.RPC.php',
            {
                method: 'POST',
                parameters: 'json=' + Object.toJSON(oParam),
                asynchronous: false,
                onComplete: function (oAjax) {

                    var oRetorno = JSON.parse(oAjax.responseText);

                    if (oRetorno.status == 1) {

                        if (oRetorno.aAtividades.length > 0) {

                            oRetorno.aAtividades.each(function (oAtividade) {
                                aAtividades.push(oAtividade);
                            });
                        }
                    }
                }
            });

        return aAtividades;

    };

    var getAnoCalculo = function () {

        var oParam = new Object();
        oParam.sExec = 'getParametrosFiscais';
        aAnoCalculo = new Array();

        var oAjax = new Ajax.Request('iss4_isscalc.RPC.php',
            {
                method: 'POST',
                parameters: 'json=' + Object.toJSON(oParam),
                asynchronous: false,
                onComplete: function (oAjax) {

                    var oRetorno = JSON.parse(oAjax.responseText);

                    if (oRetorno.status == 1) {

                        if (oRetorno.aAnosCalculo.length > 0) {

                            oRetorno.aAnosCalculo.each(function (oAnoCalculo) {
                                aAnoCalculo.push(oAnoCalculo['anocalculo']);
                            });
                        }
                        iNumeroParcelas = oAnoCalculo['iNumeroParcelas'];
                    }
                }
            });

        return aAnoCalculo;

    };

    /**
     * Valida o tipo de cálculo e libera o parcelamento
     */
    this.validarTipoSelecao = function () {

        // Esconde/mostra alvará
        if (new Number(me.oCboTipo.getValue()) == 1) {
            $(sNomeComponente + "_linhaParcelasAlvara").style.display = 'none';
            $(sNomeComponente + "_oCboParcelasAlvara").setValue("1");
        } else {
            $(sNomeComponente + "_linhaParcelasAlvara").style.display = '';
        }

        // Esconde/mostra issqn
        if (new Number(me.oCboTipo.getValue()) == 2 || me.oCboParcelasIssqn.invisible) {
            $(sNomeComponente + "_linhaParcelasIssqn").style.display = 'none';
            $(sNomeComponente + "_oCboParcelasIssqn").setValue("1");
        } else {
            $(sNomeComponente + "_linhaParcelasIssqn").style.display = '';
        }

        // Esconde/mostra publicidade
        if (new Number(me.oCboTipo.getValue()) == 3 || me.oCboParcelasPublicidade.invisible) {
            $(sNomeComponente + "_linhaParcelasPublicidade").style.display = 'none';
            $(sNomeComponente + "_oCboParcelasPublicidade").setValue("1");
        } else {
            $(sNomeComponente + "_linhaParcelasPublicidade").style.display = '';
        }

        // Esconde/mostra uso de solo
        if (new Number(me.oCboTipo.getValue()) == 4 || me.oCboParcelasUsoSolo.invisible) {
            $(sNomeComponente + "_linhaParcelasUsoSolo").style.display = 'none';
            $(sNomeComponente + "_oCboParcelasUsoSolo").setValue("1");
        } else {
            $(sNomeComponente + "_linhaParcelasUsoSolo").style.display = '';
        }
    };

    this.criarJanela = function () {

        me.oForm.oInput = document.createElement('input');
        me.oForm.oInput.type = 'button';
        me.oForm.oInput.id = 'processar';
        me.oForm.oInput.name = 'processar';
        me.oForm.oInput.value = 'Processar';
        me.oForm.oInput.style.margin = '0 auto';
        me.oForm.oInput.style.clear = 'both';
        me.oForm.oInput.onClick = 'js_processar()';

        var sConteudo = "<div id='" + me.sNomeComponente + "_divCalculoConteudo'></div>                                 ";
        sConteudo += "<div id='" + me.sNomeComponente + "_divCalculoRodape' style=\"text-align:center\">                ";
        sConteudo += "  <input type='button' value='Fechar' onClick='" + sInstancia + ".oWindowCalculoIssqn.destroy();'>";
        sConteudo += "  <input type='button' value='Processar' onClick='" + sInstancia + ".processar();'>               ";
        sConteudo += "</div>                                                                                            ";
        me.oWindowCalculoIssqn = new windowAux(me.sNomeComponente + 'oWindowCalculoIssqn', 'Cálculo ISSQN', 800, 500);
        me.oWindowCalculoIssqn.setContent(sConteudo);
        me.oWindowCalculoIssqn.show();
        return $(me.sNomeComponente + '_divCalculoConteudo');
    };

    /**
     * Processa o Cálculo
     */
    this.processar = function () {

        js_divCarregando("Aguarde...", 'msgBox' + me.id);

        // Se for issqn ou todos, veja se é válido
        if (me.oCboTipo.getValue() != 2) {

            var oParametros = new Object();
            oParametros.sExec = "validarExigibilidade";
            oParametros.iInscricao = iInscricao;

            var oDadosValidacao = new Object();
            oDadosValidacao.method = "post";
            oDadosValidacao.parameters = 'json=' + Object.toJSON(oParametros);
            oDadosValidacao.asynchronous = false;
            oDadosValidacao.onComplete = function (oAjax) {

                var oRetorno = JSON.parse(oAjax.responseText);

                if (oRetorno.status == 2 || oRetorno.lExigibilidadeInvalida) {

                    alert(oRetorno.message.urlDecode().replace(/\\n/, "\n"));
                    window.location.reload();
                }
            };

            var oAjax = new Ajax.Request('iss4_isscalc.RPC.php', oDadosValidacao);
        }


        if (oGridAtividades.getSelection().length == 0) {
            alert('Nenhuma atividade selecionada para o cálculo.');
            js_removeObj('msgBox' + me.id);
            return false;
        }

        var oParametros = new Object();
        oParametros.sExec = 'processarCalculo';
        oParametros.iInscricao = iInscricao;
        oParametros.iTipoCalculo = me.oCboTipo.getValue();
        oParametros.iParcelasAlvara = me.oCboParcelasAlvara.getValue();
        oParametros.iParcelasIssqn = me.oCboParcelasIssqn.getValue();
        oParametros.aSelecionados = new Array();

        oGridAtividades.getSelection().each(function (aDadosGrid) {
            oParametros.aSelecionados.push(aDadosGrid[0]);
        });

        oParametros.iAnoInicial = "";
        oParametros.iAnoFinal = "";

        if (getAnoCalculo().length > 0) {

            oParametros.iAnoInicial = me.cboAnoCalculoInicial.getValue();
            oParametros.iAnoFinal = me.cboAnoCalculoFinal.getValue();
        }

        var oDadosRequisicao = new Object();
        oDadosRequisicao.method = "post";
        oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametros);
        oDadosRequisicao.asynchronous = false;
        oDadosRequisicao.onComplete = function (oAjax) {

            var oRetorno = JSON.parse(oAjax.responseText);
            alert(oRetorno.message.urlDecode().replace(/\\n/, "\n"));

            if (oRetorno.status == 1) {
                window.location.reload();
            }

            js_removeObj('msgBox' + me.id);
        };

        oDadosRequisicao.method = "post";

        var oAjax = new Ajax.Request('iss4_isscalc.RPC.php', oDadosRequisicao);
    };

    /**
     * Cria HTML no Local especificado
     */
    this.show = function (oContainer) {

        oElementoDestino = oContainer;
        if (oContainer == null) {
            oElementoDestino = me.criarJanela();
            lUsaJanela = true;
        }
        me.renderizarHTML();
        me.criarComponentes();
    };

};
