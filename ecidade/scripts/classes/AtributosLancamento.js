require_once('scripts/widgets/windowAux.widget.js');
require_once('scripts/widgets/dbmessageBoard.widget.js');

/**
 *
 * @param instance
 * @constructor
 */
AtributosLancamento = function (instance) {

    this._instance = instance;

    /**
     * RPC
     * @type {string}
     * @private
     */
    this._rpc = 'con4_manutencaoatributoslancamento.RPC.php';

    /**
     * Contas Debito
     * @type {Array}
     * @private
     */
    this._contasDebito = [];

    /**
     * Conta Credito
     * @type {Array}
     * @private
     */
    this._contasCredito = [];

    /**
     *
     * @type {undefined}
     * @private
     */
    this._grid = undefined;

    /**
     * Dados configurados
     * @type {Array}
     * @private
     */
    this._retorno = [];

    /**
     *
     * @type {boolean}
     * @private
     */
    this._possuiAtributos = false;

    this.atributosContas = [];
    this.atributosIgnorar = ['FR', 'DDR', 'CO', 'RV'];
    /**
     * function
     * @private
     */
    this._callback = function () {
    };

    /**
     *
     * @type {string}
     * @private
     */
    this._html = "<div id='messageBoardWindow'></div> ";
    this._html += "<fieldset>";
    this._html += "  <legend class='bold'>Atributos para lançamento contábil</legend>";
    this._html += "  <p style='text-align: center; padding: 10px; background-color: #fff984; border: 1px solid black '>";
    this._html += "Atenção: O recurso do lançamento deve ser selecionado. Ele será aplicado ao lançamento conforme atributos.</p> ";
    this._html += "<table>";
    this._html += '<tr>';
    this._html += '    <td>';
    this._html += '        <a id="ancoraFonteRecursoCredito" href="#">Crédito - Fonte de Recursos:</a>';
    this._html += '    </td>';
    this._html += '    <td>';
    this._html += '        <input type="hidden" id="iCodigoRecursoCredito" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 60px;" id="siconfiCredito" lang="codigo_siconfi" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 60px;" id="o15_recursoCredito" lang="o15_recurso" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 360px;" id="descricaoRecursoCredito" lang="descricao" readonly class="readonly">';
    this._html += '    </td>';
    this._html += '</tr>';
    this._html += '<tr>';
    this._html += '    <td><label for="complementoRecurso" class="bold">Crédito - Complemento:</label></td>';
    this._html += '    <td>';
    this._html += '        <input type="text" style="width: 122px;" id="o15_complementoCredito" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 360px;" id="o200_descricaoCredito" readonly class="readonly">';
    this._html += '    </td>';
    this._html += '</tr> ';
    this._html += '<tr>';
    this._html += '    <td>';
    this._html += '        <a id="ancoraFonteRecursoDebito" href="#">Débito - Fonte de Recursos:</a>';
    this._html += '    </td>';
    this._html += '    <td>';
    this._html += '        <input type="hidden" id="iCodigoRecursoDebito" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 60px;" id="siconfiDebito" lang="codigo_siconfi" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 60px;" id="o15_recursoDebito" lang="o15_recurso" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 360px;" id="descricaoRecursoDebito" lang="descricao" readonly class="readonly">';
    this._html += '    </td>';
    this._html += '</tr>';
    this._html += '<tr>';
    this._html += '    <td><label for="complementoRecurso" class="bold">Débito - Complemento:</label></td>';
    this._html += '    <td>';
    this._html += '        <input type="text" style="width: 122px;" id="o15_complementoDebito" readonly class="readonly">';
    this._html += '        <input type="text" style="width: 360px;" id="o200_descricaoDebito" readonly class="readonly">';
    this._html += '    </td>';
    this._html += '</tr> ';
    this._html += "</table>";
    this._html += "  <div id='ctnContas'></div>";
    this._html += "</fieldset>";
    this._html += "<p style='text-align:center;'>";
    this._html += "  <input type='button' id='btnSalvar' value='Salvar' onclick='" + this._instance + ".salvarInformacoes()' />";
    this._html += "  <input type='button' id='btnLimpar' value='Limpar Campos' onclick='" + this._instance + ".limpar()' />";
    this._html += "</p>";

};

AtributosLancamento.prototype.montaEstruturaContaCorrente = function (sinal) {
    let contaCorrente = []
    for (const cc of this.atributosContas) {
        if (cc.sinal_conta !== sinal) {
            continue;
        }

        if (contaCorrente.find((value) => value.codigo == cc.codigo_sistema) === undefined) {
            contaCorrente.push({
                "codigo": cc.codigo_sistema,
                "descricao": cc.descricao_sistema,
                "atributos": []
            });
        }
    }

    return contaCorrente;
}


AtributosLancamento.prototype.montaAtributos = function (sinal, contaCorrente) {
    for (const cc of this.atributosContas) {
        if (cc.sinal_conta !== sinal) {
            continue;
        }

        if (contaCorrente.codigo == cc.codigo_sistema) {
            let valor = '';
            let idElemento = '';
            switch (cc.sigla_atributo) {
                case 'FR':
                case 'RV':
                    idElemento = cc.sinal_conta === 'D' ? 'o15_recursoDebito' : 'o15_recursoCredito';
                    valor = document.getElementById(idElemento).value;
                    break;
                case 'DDR':
                    idElemento = cc.sinal_conta === 'D' ? 'iCodigoRecursoDebito' : 'iCodigoRecursoCredito';
                    valor = document.getElementById(idElemento).value;
                    break;
                case 'CO':
                    idElemento = cc.sinal_conta === 'D' ? 'o15_complementoDebito' : 'o15_complementoCredito';
                    valor = document.getElementById(idElemento).value;
                    break;
            }

            contaCorrente.atributos.push({
                "codigo": cc.codigo_atributo,
                "descricao": cc.descricao_atributo,
                "sigla": cc.sigla_atributo,
                "valor": valor,
            });
        }
    }

    return contaCorrente;
}

AtributosLancamento.prototype.pegaAtributosDaGrig = function (contasCorrente, row) {

    let dadosContaCorrente = row.aCells[2].getValue().split(' - ');
    let codigo = dadosContaCorrente[0];

    for (let cc of contasCorrente) {
        if (cc.codigo == codigo) {
            for (let atributo of cc.atributos) {
                if (atributo.sigla == row.aCells[3].getValue()) {
                    atributo.valor = row.aCells[5].getValue();
                }
            }
        }
    }
}


/**
 * Armazena os dados informados pelo usuário em uma propriedade
 * @returns {boolean}
 */
AtributosLancamento.prototype.salvarInformacoes = function () {

    let permiteSalvar = true;
    let erro = false;

    this._retorno = [];
    let self = this;

    if (document.getElementById('iCodigoRecursoDebito').value === '' ||
        document.getElementById('iCodigoRecursoCredito').value === '') {
        alert("O recurso a Crédito e a Débito devem ser selecionados.");
        return false;
    }

    let contasCorrenteDebito = this.montaEstruturaContaCorrente('D').map(function (contaCorrente) {
        self.montaAtributos('D', contaCorrente);
        return contaCorrente;
    });

    let contasCorrenteCredito = this.montaEstruturaContaCorrente('C').map(function (contaCorrente) {
        self.montaAtributos('C', contaCorrente);
        return contaCorrente;
    });


    this._grid.aRows.forEach(
        function (row, indice) {
            row.aCells[1].getValue() === 'D' ? self.pegaAtributosDaGrig(contasCorrenteDebito, row) : self.pegaAtributosDaGrig(contasCorrenteCredito, row);
            if (row.aCells[5].getValue().trim() === '') {
                permiteSalvar = false;
            }
        }
    );

    let contaDebito = {
        "sinal": 'D',
        "conta_corrente": {
            'reduzido': document.getElementById('c69_debito').value,
            'estrutural': document.getElementById('c69_debito_estrut').value,
            'descricao': document.getElementById('debito_descr').value
        },
        "conta_corrente": contasCorrenteDebito
    };

    let contaCredito = {
        "sinal": 'C',
        "conta_corrente": {
            'reduzido': document.getElementById('c69_credito').value,
            'estrutural': document.getElementById('c69_credito_estrut').value,
            'descricao': document.getElementById('credito_descr').value
        },
        "conta_corrente": contasCorrenteCredito
    };

    self._retorno.push(contaDebito);
    self._retorno.push(contaCredito);


    if (!permiteSalvar) {
        alert("Todos os atributos devem ser preenchidos.");
        return false;
    }

    if (erro) {
        return false;
    }
    this._callback();
};

/**
 * Retorna as linhas configuradas
 * @returns {Array}
 */
AtributosLancamento.prototype.getAtributosPorSinal = function (sinal) {

    let registros = [];
    for (let linha in this._retorno) {
        if (this._retorno[linha].sinal === sinal) {
            registros.push(this._retorno[linha]);
        }
    }
    return registros;
};


AtributosLancamento.prototype.show = function () {
    this._carregarAtributos();
};

/**
 * Limpa todos os campos da grid.
 */
AtributosLancamento.prototype.limpar = function () {
    this._grid.aRows.forEach(
        function (row, indice) {
            document.querySelector('#input_' + indice).value = '';
        }
    );
};
/**
 *
 * @param resposta
 * @private
 */
AtributosLancamento.prototype._construirTela = function (resposta) {

    let window = new windowAux('window', 'Atributos do Lançamento', 1200, 500);
    window.setShutDownFunction(function () {
        window.destroy();
    });
    window.setContent(this._html);

    let tituloMessageBoard = "Atributos do lançamento";
    let ajudaMessageBoard = "As contas selecionadas possuem atributos vinculados. Todos os atributos devem ser preenchidos.";
    window.show();
    let messageBoard = new DBMessageBoard('messageBoard', tituloMessageBoard, ajudaMessageBoard, window.getContentContainer());
    messageBoard.show();

    this._grid = new DBGrid('gridAtributos');
    this._grid.nameInstance = this._instance + '._grid';
    this._grid.setHeader(['Conta Contábil', 'Natureza', 'Conta Corrente', 'Sigla', 'Atributo', 'Valor']);
    this._grid.setCellWidth(['40%', '5%', '20%', '5%', '20%', '10%']);
    this._grid.setCellAlign(['left', 'center', 'left', 'center', 'left', 'left']);
    this._grid.setHeight(280);
    this._grid.show($('ctnContas'));

    this.atributosContas = resposta.atributos;

    let rowGrid = 0;
    for (let dadosSistema of resposta.atributos) {
        if (this.atributosIgnorar.includes(dadosSistema.sigla_atributo)) {
            continue;
        }

        this._grid.addRow([
            dadosSistema.codigo_conta + " - " + dadosSistema.codigo_reduzido + " - " + dadosSistema.estrutural_conta + " - " + dadosSistema.descricao_conta,
            dadosSistema.sinal_conta,
            Number(dadosSistema.codigo_sistema) === 1 ? '1 - MSC' : dadosSistema.codigo_sistema + ' - ' + dadosSistema.descricao_sistema,
            dadosSistema.sigla_atributo,
            dadosSistema.codigo_atributo + " - " + dadosSistema.descricao_atributo,
            "<input type='text' id='input_" + rowGrid + "' style='width:100%; border:1 solid grey;' onchange='" + this._instance + ".atualizarValores(" + rowGrid + ")' value='" + dadosSistema.valor + "'/>"
        ]);
        rowGrid++;
    }

    this._grid.renderRows();
    cria()
};

/**
 * Atualiza os valores.
 * @param linhaGrid
 */
AtributosLancamento.prototype.atualizarValores = function (linhaGrid) {

    let valor = this._grid.aRows[linhaGrid].aCells[5].getValue();
    let sigla = this._grid.aRows[linhaGrid].aCells[3].getValue();
    let sinal = this._grid.aRows[linhaGrid].aCells[1].getValue();
    this._grid.aRows.forEach(
        function (row, indice) {
            let valorDestino = document.querySelector('#input_' + indice).value;
            if (row.aCells[3].getValue() === sigla && valorDestino === '' && row.aCells[1].getValue() === sinal) {
                document.querySelector('#input_' + indice).value = valor;
            }
        }
    );
};

/**
 * Carrega os atributos das contas.
 * @private
 */
AtributosLancamento.prototype._carregarAtributos = function () {

    let parametros = {
        'exec': 'getInformacoes',
        'contas_debito': this._contasDebito,
        'contas_credito': this._contasCredito
    };

    let self = this;
    AjaxRequest.create(
        this._rpc,
        parametros,
        function (resposta, erro) {

            if (erro) {
                alert(resposta.mensagem);
                return false;
            }
            self._possuiAtributos = (resposta.atributos.length > 0);
            if (!self._possuiAtributos) {
                return;
            }
            self._construirTela(resposta);
        }
    ).setMessage('Aguarde, carregando informações de atributos...').execute();
};

/**
 * @param {array} contas
 */
AtributosLancamento.prototype.setContasCredito = function (contas) {
    this._contasCredito = contas;
};

/**
 * @param {array} contas
 */
AtributosLancamento.prototype.setContasDebito = function (contas) {
    this._contasDebito = contas;
};

/**
 * Função a ser chamada quando o usuário clicar em salvar.
 * @param callback
 */
AtributosLancamento.prototype.setCallback = function (callback) {
    this._callback = callback
};


function cria() {
    const lookUpRecursoCredito = new DBLookUp($('ancoraFonteRecursoCredito'), $('siconfiCredito'), $('descricaoRecursoCredito'), {
        'sArquivo': 'func_novosRecursos.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'zIndex': 1000,
        'aCamposAdicionais': ['o15_codigo', 'o15_complemento', 'o200_descricao', 'o15_recurso']
    });

    lookUpRecursoCredito.setCallBack('onClick', (retorno) => {
        preencheFormCredito(retorno[0], retorno[1], retorno[2], retorno[3], retorno[4], retorno[5]);
    });

    const preencheFormCredito = (recurso, descricao, id, codComplemento, descrComplemento, subrecurso) => {
        $('iCodigoRecursoCredito').value = id;
        $('siconfiCredito').value = recurso;
        $('descricaoRecursoCredito').value = descricao;
        $('o15_recursoCredito').value = subrecurso;
        $('o15_complementoCredito').value = codComplemento;
        $('o200_descricaoCredito').value = descrComplemento;
    };

    // lockup conta debito
    const lookUpRecursoDebito = new DBLookUp($('ancoraFonteRecursoDebito'), $('siconfiDebito'), $('descricaoRecursoDebito'), {
        'sArquivo': 'func_novosRecursos.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'zIndex': 1000,
        'aCamposAdicionais': ['o15_codigo', 'o15_complemento', 'o200_descricao', 'o15_recurso']
    });

    lookUpRecursoDebito.setCallBack('onClick', (retorno) => {
        preencheFormDebito(retorno[0], retorno[1], retorno[2], retorno[3], retorno[4], retorno[5]);
    });

    const preencheFormDebito = (recurso, descricao, id, codComplemento, descrComplemento, subrecurso) => {
        $('iCodigoRecursoDebito').value = id;
        $('siconfiDebito').value = recurso;
        $('descricaoRecursoDebito').value = descricao;
        $('o15_recursoDebito').value = subrecurso;
        $('o15_complementoDebito').value = codComplemento;
        $('o200_descricaoDebito').value = descrComplemento;
    };

}
