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
require_once(modification("classes/db_conparametro_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/dates.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>

    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <style>
        .flex {
            display: flex;
            flex-direction: row;
            flex-flow: row wrap;
            justify-content: space-around;
            align-items: center;
        }

        .flex-item {
            flex: 1;
        }

        .item {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container">
    <form id="frmFiltros" method="post" action="">
        <fieldset>
            <legend>Filtros de Movimentações</legend>
            <table class="form-container">
                <tr>
                    <td><label for="origem">Origem:</label></td>
                    <td>
                        <select id="origem" name="origem" style="width: 100px" onchange="trocarOrigem()">
                            <option value="despesa" selected>Despesa</option>
                            <option value="receita">Receita</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a id="ancoraFonteRecurso" href="#">Fonte de Recursos:</a>
                    </td>
                    <td>
                        <input type="text" style="width: 60px;" id="gestao" name="gestao" readonly class="readonly">
                        <input type="text" style="width: 60px;" id="o15_recurso" name="subrecurso" readonly
                               class="readonly">
                        <input type="text" style="width: 360px;" id="descricaoRecurso" lang="descricao" readonly
                               class="readonly">
                    </td>
                </tr>

                <tr id="trFiltroEmpenho" style="display: none">
                    <td nowrap title="Número do Empenho">
                        <?php db_ancora("Empenho:", "js_pesquisae60_codemp(true);", 1); ?>
                    </td>
                    <td nowrap="nowrap" title="Número do Empenho">
                        <input id='idEmpenho' name="idEmpenho" type='hidden'/>
                        <input id='e60_codemp' name="numeroEmpenho" type='text' class="readonly field-size2" disabled/>
                    </td>
                </tr>
                <tr id="trFiltroReceita" style="display: none">
                    <td nowrap title="Receita">
                        <a href="#" id='ancoraReceita'>Receita:</a>
                    </td>
                    <td nowrap="nowrap" title="Receita">
                        <input id='o70_codrec' name="codigoReceita" type='text'/>
                        <input id='o57_descr' name="o57_descr" type='text'/>
                    </td>
                </tr>
                <tr>
                    <td>Data Inicial:</td>
                    <td>
                        <input type="text" id="data_inicial" name="dataInicio">
                        &nbsp;<b>Data Final: </b>
                        <input type="text" id="data_final" name="dataFinal">
                    </td>
                </tr>
            </table>
        </fieldset>
        <button id="btnFiltrar" type="button">
            <i class="fas fa-filter"></i>
            Filtrar
        </button>
    </form>
</div>

<div id="modal" style="width: 1300px; display: none; margin-top: 10px;">
    <div class="flex">
        <div class="flex-item bold item" style="width: 30px;">Complementos:
        </div>
        <div class="flex-item item">
            <select id="aplicarComplementos" name="complementos">
                <option value="">Selecione</option>
            </select>
        </div>
        <div class="flex-item flex-" >
            <button id="btnAplicar" type="button">
                <i class="fas fa-sync-alt"></i>
                Aplicar
            </button>
        </div>
    </div>

    <div class="subcontainer">
        <fieldset class="container">
            <legend id="legendGrid"></legend>
            <div id="ctnGrid" style="width: 1200px"></div>
        </fieldset>

        <button id="btnSalvar" type="button">
            <i class="far fa-save"></i>
            Salvar
        </button>
    </div>
</div>
<?php
db_menu();
?>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
</body>
</html>

<script>
    const rota = '/financeiro/contabilidade/procedimento/manutencao/fonte-recurso';
    const rotaDataEncerramento = 'financeiro/contabilidade/procedimento/encerramento-periodo-contabil';

    const input = {
        'origem': $('origem'),
        'fonteGestao': $('gestao'),
        'subrecurso': $('o15_recurso'),
        'descricaoRecurso': $('descricaoRecurso'),
        'empenho': $('e60_codemp'),
        'idEmpenho': $('idEmpenho'),
        'receita': $('o70_codrec'),
        'data_inicial': $('data_inicial'),
        'data_final': $('data_final')
    };

    let dataLimite;
    const btnFiltrar = document.getElementById('btnFiltrar');
    const dataInicial = new DBInputDate(input.data_inicial);
    const dataFinal = new DBInputDate(input.data_final);

    new DBLookUp($('ancoraReceita'), $('o70_codrec'), $('o57_descr'), {
        "sArquivo": "func_orcreceita.php",
        "sObjetoLookUp": "db_iframe_orcreceita",
        "sLabel": "Pesquisa Receita",
    });

    const data = new Date();
    const anoAtual = data.getUTCFullYear();

    dataFinal.setValue(data.toLocaleDateString());

    const formulario = document.getElementById('frmFiltros');
    const modal = document.getElementById('modal');

    const cboAplicarComplementos = document.getElementById('aplicarComplementos');
    const btnAplicar = document.getElementById('btnAplicar');
    const btnSalvar = document.getElementById('btnSalvar');

    const collection = new Collection();
    collection.setId('codigo');

    var gridDados = new DatagridCollection(collection).configure({
        order: false,
        height: 300
    });

    const createSelect = (recursos, complementoSelecionar) => {

        const elemento = document.createElement('select');
        recursos.map(recurso => {
            let b = recurso.complemento.codigo == complementoSelecionar;
            elemento.add(new Option(recurso.complemento.descricao, recurso.complemento.codigo, b, b));
        });

        return elemento;
    };

    const validaDatas = () => {

        if (dataInicial.getValue() === null) {
            alert('Data Inicial é de preenchimento obrigatório.');
            return false;
        }

        if (dataFinal.getValue() === null) {
            alert('Data Final é de preenchimento obrigatório.');
            return false;
        }

        if (js_comparadata(dataInicial.__toLocaleDateString(), dataLimite, '<')) {
            alert(`A data inicial não pode ser menor que a data do encerramento contábil.\nEncerramento: ${dataLimite}`);
            return false;
        }

        if (dataInicial.getValue().getUTCFullYear() != dataFinal.getValue().getUTCFullYear()) {
            alert('As Datas de inicio e fim, devem estar dentro do mesmo Exercício.');
            return false;
        }

        if (dataInicial.getValue().compararData(dataFinal.getValue(), '>')) {
            alert('Data Inicial esta maior que data Final.');
            return false;
        }

        return true;
    }

    const validarFormulario = () => {

        if (input.origem.value === 'despesa') {
            if (input.empenho.value === '') {
                if (input.subrecurso.value === '') {
                    alert('Recurso é de preenchimento obrigatório.');
                    return false;
                }

                return validaDatas();
            }
        }

        if (input.origem.value === 'receita') {
            if (input.receita.value === '') {
                if (input.subrecurso.value === '') {
                    alert('Recurso é de preenchimento obrigatório.');
                    return false;
                }
                return validaDatas();
            }

            if (dataInicial.getValue() === null) {
                alert('Data Inicial é de preenchimento obrigatório.');
                return false;
            }
        }

        return true;
    };

    const windowManutencao = new windowAux('windowManutencao', 'Manutenção dos Complementos', 1300, 700);
    windowManutencao.setContent(modal);
    windowManutencao.setShutDownFunction(() => {
        fechaModal()
    });

    const fechaModal = () => {
        closeWindowAux(windowManutencao, 'msgBoardManutencao');
    }

    const closeWindowAux = (windowAux, idMsgBoard) => {
        if (idMsgBoard !== undefined) {
            let msgBoard = document.getElementById(idMsgBoard);
            if (msgBoard) {
                msgBoard.parentNode.removeChild(msgBoard);
            }
        }

        if (!!windowAux.oDBMask) {
            windowAux.oDBMask.destroy();
        }
        gridDados.clear();
        windowAux.hide();
    };

    const criaGrid = () => {
        gridDados = new DatagridCollection(collection).configure({
            order: false,
            height: 300
        });

        gridDados.getGrid().setCheckbox(0);
        if (input.origem.value === 'despesa') {
            gridDados.addColumn('numero', {label: "Número do Empenho", width: '15%', align: 'center'});
            gridDados.addColumn('dotacao', {label: "Dotação", width: '42%', align: 'left'});
            gridDados.addColumn('valor', {label: "Valor", width: '15%', align: 'right'}).transform('dinheiro');
        } else {
            gridDados.addColumn('codigo', {label: "Lançamento", width: '15%', align: 'center'});
            gridDados.addColumn('receita', {label: "Receita", width: '40%', align: 'left'});
            gridDados.addColumn('valor', {label: "Valor", width: '17%', align: 'right'}).transform('dinheiro');
        }

        gridDados.addColumn('complemento', {label: "Complemento", width: '28%', align: 'center'})
            .transform((complemento, linha) => {
                let elemento = createSelect(linha.recursos, complemento);
                elemento.id = `codigo_${linha.codigo}`;
                elemento.className = 'field-size-max complemento_item';
                return elemento.outerHTML;
            });

        gridDados.show($('ctnGrid'));

        gridDados.setEvent('onafterrenderrows', function (collection) {
            collection.get().map(function (linha) {
                let elemento = document.getElementById(`codigo_${linha.codigo}`);
                elemento.addEventListener('change', (event) => {
                    linha.complemento = event.target.value;
                });
            });
        });
    }

    const abreJanelaManutencao = (recursos, itens) => {

        modal.style.display = '';
        let msg = 'Empenho(s) Encontrados';
        let msgBoard = 'Para alterar o Complemento da Fonte, selecione os Empenhos,';
        if (input.origem.value === 'receita') {
            msgBoard = 'Para alterar o Complemento da Fonte, selecione os lançamentos de Receitas,';
        }
        msgBoard += ' o complemento desejado (individual ou em lote) e clique em: ';
        msgBoard += '<kbd style="padding: 5px 12px 4px 0">Salvar</kbd>'
        if (input.origem.value === 'receita') {
            msg = 'Lançamento(s) de Receita Encontrados';
        }
        new DBMessageBoard('msgBoardManutencao',
            'Manutenção dos complementos',
            msgBoard,
            windowManutencao.getContentContainer()
        );

        windowManutencao.show(0, 0, true);

        criaGrid();

        let legenda = document.getElementById('legendGrid');
        legenda.innerHTML = msg;

        cboAplicarComplementos.options.length = 0;
        cboAplicarComplementos.add(new Option('Selecione um complemento', ''));
        recursos.map(recurso => {
            cboAplicarComplementos.add(new Option(recurso.complemento.descricao, recurso.complemento.codigo));
        });

        itens.map(item => {
            item.recursos = recursos;
            collection.add(item);
        });

        gridDados.reload();
    }

    btnFiltrar.addEventListener('click', () => {
        if (!validarFormulario()) {
            return;
        }

        const formData = new FormData(formulario);
        PHPSession.appendFormData(formData);

        formData.append('dataInicio', '');
        if (dataInicial.getValue() !== null) {
            formData.append('dataInicio', js_formatar(dataInicial.__toLocaleDateString(), 'd'));
        }

        formData.append('dataFinal', '');
        if (dataFinal.getValue() !== null) {
            formData.append('dataFinal', js_formatar(dataFinal.__toLocaleDateString(), 'd'));
        }

        js_divCarregando('Aguarde um momento...', 'loading_janela');
        HttpClient.post(`${PHPSession.requestApi}${rota}/${input.origem.value}/lancamentos`, {body: formData})
            .then(response => {

                if (response.error) {
                    alert(response.message);
                    js_removeObj('loading_janela');
                    return;
                }

                abreJanelaManutencao(response.data.recursos, response.data.itens);
                js_removeObj('loading_janela');

            });
    });

    function js_pesquisae60_codemp(mostra) {
        if (mostra) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_empempenho',
                'func_empempenho.php?funcao_js=parent.js_mostraempempenho|e60_numemp|e60_codemp|e60_anousu',
                'Pesquisa',
                true
            );
        }
    }

    function js_mostraempempenho(id, codigo, ano) {
        $('idEmpenho').value = id;
        $('e60_codemp').value = codigo + '/' + ano;
        db_iframe_empempenho.hide();
    }


    const trocarOrigem = () => {
        var filtroEmpenho = $('trFiltroEmpenho');
        var filtroReceita = $('trFiltroReceita');

        input.empenho.value = '';
        input.idEmpenho.value = '';
        input.receita.value = '';

        filtroEmpenho.style.display = 'none';
        filtroReceita.style.display = 'none';
        if (input.origem.value === 'receita') {
            filtroReceita.style.display = '';
        }

        if (input.origem.value === 'despesa') {
            filtroEmpenho.style.display = '';
        }
    }

    trocarOrigem();

    const lookUpRecurso = new DBLookUp($('ancoraFonteRecurso'), input.fonteGestao, input.descricaoRecurso, {
        'sArquivo': 'func_novosRecursos.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'aCamposAdicionais': ['o15_recurso']
    });

    lookUpRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2]);
    });

    const preencheForm = (recurso, descricao, subrecurso ) => {
        input.fonteGestao.value = recurso;
        input.descricaoRecurso.value = descricao;
        input.subrecurso.value = subrecurso;
    };

    btnAplicar.addEventListener('click', () => {
        if (cboAplicarComplementos.value === '') {
            alert('Selecione um complemento para aplicar.');
            return;
        }

        const linhasGrid = gridDados.getGrid().aRows;
        for (let linha of linhasGrid) {
            if (linha.isSelected) {
                let elemento = document.getElementById(`codigo_${linha.itemCollection.codigo}`);
                elemento.value = cboAplicarComplementos.value;
                elemento.dispatchEvent(new Event('change'))
            }
        }
    });

    const filtraRecursos = (linha) => {
        return linha.recursos.filter(function (recurso) {
            return recurso.o15_complemento == linha.complemento
        }).map(function (recurso) {
            return {
                "o15_codigo": recurso.o15_codigo,
                "complemento": {"codigo": recurso.o15_complemento},
            }
        });
    };

    btnSalvar.addEventListener('click', () => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);

        const linhas = [];
        const linhasGrid = gridDados.getGrid().aRows;
        for (var linha of linhasGrid) {

            if (linha.isSelected) {
               linhas.push({
                    "codigo": linha.itemCollection.codigo,
                    "complemento": linha.itemCollection.complemento,
                    "recursos": filtraRecursos(linha.itemCollection)
                });
            }
        }

        if (linhas.length === 0) {
            alert("Você deve selecionar ao menos um item na grid antes de salvar.");
            return;
        }

        formData.append('origem', input.origem.value);

        linhas.forEach(linha => {
            formData.append('itens[]', JSON.stringify(linha));
        });

        HttpClient.post(`${PHPSession.requestApi}${rota}/atualizarComplemento`, {body: formData}).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }
        });
    });



    PHPSession.loadData().then(() => {
        console.log(PHPSession);
        let instituicao = PHPSession.getValueSession('DB_instit');

        HttpClient.get(`${PHPSession.requestApi}/${rotaDataEncerramento}/${instituicao}`).then(response => {
            dataLimite = js_formatar(response.data, 'd');
        });
    });
</script>
