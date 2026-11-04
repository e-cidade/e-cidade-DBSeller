<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>

    <script rel="script" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
</head>
<body>
<div class="alert alert-primary text-left" role="alert">
    Rotina para a geração da transferência fnanceira de recurso vinculado para o livre, a respeito das retenções de receitas orçamentárias:. <br>

    - Quando o campo <b>Conta Crédito</b> não apresentar contas, não existe conta bancária com recurso compatível ao do
    empenho. Ao acrescentar a conta, acesse a rotina novamente para gerar o slip.<br/>
    - Quando o campo <b>Conta Débito</b> apresentar o valor "Conta selecionada esta sem contrapartida" você deve
    informar a contrapartida no cadastro: Tesouraria > Cadastros > Contas > Contas Tesouraria > Alteração de Conta, após acessar a rotina novamente para gerar o slip.
    <br/>
    - Antes de clicar em <b>Gerar Slips</b> confira o agrupamento.
    Se agrupar por "Débito/Crédito" o sistema vai totalizar os valores dos registros selecionados, onde as linhas
    conterem conta débito e crédito iguais e possuirem recurso compatíveis.
    Caso contrário, será gerado um slip por ordem
</div>
<div class="container">
    <form id="frmFiltros" name="frmFiltros">
        <fieldset>
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td><label for="dataInical">Data Inicial:</label></td>
                    <td><input type="text" name="dataInical" id="dataInical"></td>
                    <td><label for="dataFinal">até</label></td>
                    <td><input type="text" name="dataFinal" id="dataFinal"></td>
                </tr>
                <tr>
                    <td><label for="op">Ordem de Pagamento:</label></td>
                    <td colspan="3">
                        <input type="text" class="field-size2" name="op" id="op"
                               oninput="js_ValidaCampos(this,1,'OP','t','f',event);">
                    </td>
                </tr>
            </table>
        </fieldset>
        <button type="button" name="pesquisar" id="pesquisar" disabled>
            <i class="fas fa-search"></i>
            Pesquisar
        </button>
    </form>
</div>

<div class="subcontainer">
    <div id="gridRetencoes" style="width: 1200px;"></div>

    <div>
        <label for="agrupar" class="bold">Agrupar Por: &nbsp;</label>
        <select id="agrupar" name="agrupar">
            <option value="1">Débito/Crédito</option>
            <option value="2">Ordem Pagamento</option>
        </select>&nbsp;
        <button type="button" name="gerarSlips" id="gerarSlips">
            <i class="far fa-save"></i> Gerar Slips
        </button>

    </div>
</div>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>

<script type="text/javascript">

    const contas = [];
    const retencoes = new Collection().setId('id');

    const gridContainer = document.getElementById('gridRetencoes');

    const grid = new DatagridCollection(retencoes).configure({
        order: false,
        height: 200,
    });
    grid.grid.setCheckbox(0);
    grid.addColumn('ordem', {'label': 'OP', 'width': '7%'});
    grid.addColumn('descricao_retencao', {'label': 'Retenção', 'width': '10%'});
    grid.addColumn('conta_credito', {'label': 'Conta Crédito', 'width': '30%'}).transform((value, row) => {
        let contasCompativeis = contas.filter((conta) => {
            let recurso = conta.contaContabil.recurso
            return row.siconfi == recurso.siconfi && row.subrecurso == recurso.subrecurso
        });

        let htmlSelect = `<select class="valor-editavel field-size-max" id="cbo_conta_${row.id}" > `;
        htmlSelect += '<option value="">Selecione uma conta</option>';
        for (let conta of contasCompativeis) {
            let selected = row.creditar == conta.conta ? 'selected' : '';
            htmlSelect += `<option value='${conta.conta}' ${selected}>${conta.conta} - ${conta.nome}</option>`

            // se tem conta selecionada
            if (selected) {
                row.conta_debito = `Conta selecionada esta sem contrapartida.`;
                if (conta.contrapartida != null) {
                    row.conta_debito = `${conta.contrapartida.conta} - ${conta.contrapartida.nome}`
                    row.debitar = conta.contrapartida.conta;
                }
            }
        }
        htmlSelect += '</select>'
        return htmlSelect;
    });

    grid.addColumn('conta_debito', {'label': 'Conta Débito', 'width': '25%'}).transform((nome, row) => {
        return `<div style="padding-left: 5px;">${row.conta_debito}</div>`;
    });
    grid.addColumn('siconfi', {'label': 'Siconfi', 'width': '7%'});
    grid.addColumn('subrecurso', {'label': 'Subrecurso', 'width': '7%'});
    grid.addColumn('valor', {'label': 'Valor', 'width': '7%'}).transform('dinheiro');
    grid.show(gridContainer);

    grid.setEvent('onafterrenderrows', function (collection) {
        retencoes.get().map(function (row) {
            let elemento = document.getElementById(`cbo_conta_${row.id}`);
            elemento.addEventListener('change', (e) => {
                let contaSelecionada = contas.filter((conta) => {
                    return conta.conta == e.target.value
                }).shift();

                retencoes.get().filter(retencao => {
                    return retencao.id === row.id;
                }).map(retencao => {

                    console.log('change')
                    console.log(contaSelecionada)

                    if (contaSelecionada === undefined) {
                        retencao.creditar = '';
                        retencao.debitar = 0;
                        retencao.conta_debito = '';
                    } else if (contaSelecionada?.contrapartida == null) {
                        // seleciona a conta crédito mesmo sem contra partida
                        retencao.creditar = contaSelecionada.conta;
                        retencao.conta_debito = `Conta selecionada esta sem contrapartida.`;
                    } else {

                        let creditar = retencao.creditar;

                        retencao.conta_debito = `${contaSelecionada.contrapartida.conta} - ${contaSelecionada.contrapartida.nome}`
                        retencao.debitar = contaSelecionada.contrapartida.conta;
                        retencao.creditar = contaSelecionada.conta;
                        // implementado solicitação do Leandro para quando a conta creditar inicia vazia, o sistema
                        // pré-selecione a mesma conta em todas as linhas onde a OP seja igual.
                        if (empty(creditar)) {
                            preSelecionaContasOpsIguais(retencao.ordem, retencao.id, contaSelecionada);
                        }
                    }
                });

                grid.reload();
            });
        });
    });

    const preSelecionaContasOpsIguais = (ordem, idDiferente, contaSelecionada) => {
        retencoes.get().filter(retencao => {
            return retencao.ordem === ordem && retencao.id !== idDiferente;
        }).map(retencao => {
            retencao.conta_debito = `${contaSelecionada.contrapartida.conta} - ${contaSelecionada.contrapartida.nome}`
            retencao.debitar = contaSelecionada.contrapartida.conta;
            retencao.creditar = contaSelecionada.conta;
        })
    };

    // fields
    const dataInicial = new DBInputDate(document.getElementById('dataInical'))
    const dataFinal = new DBInputDate(document.getElementById('dataFinal'))
    const agrupar = document.getElementById('agrupar');
    const inputOP = document.getElementById('op');
    const frmFiltros = document.getElementById('frmFiltros');
    const btnPesquisa = document.getElementById('pesquisar');
    const btnGerarSlips = document.getElementById('gerarSlips');


    PHPSession.loadData().then(() => {
        btnPesquisa.disabled = false;
    });


    btnPesquisa.addEventListener('click', function () {
        if (dataInicial.__toLocaleDateString() === null && inputOP.value === '') {
            alert('Informe ao menos um dos campos: Data inicial ou Ordem de Pagamento.');
            return;
        }

        const formData = new FormData(frmFiltros);
        PHPSession.appendFormData(formData);
        formData.append('acao', 'buscarSlips');
        retencoes.clear();
        HttpClient.post('emp4_geraslipscomapropriacao.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            for (let dado of response.dados) {
                dado.id = dado.empagemovslips_id;
                dado.conta_debito = '';
                retencoes.add(dado);
            }

            grid.reload();
        });
    });

    const buscarContas = async () => {
        try {
            js_divCarregando('Carregando contas', 'loading_message_contas');

            const response = await CurrentWindow.axios.get(
                "v4/api/financeiro/tesouraria/contas?comContrapartida=1&comReduzidos=1"
            );

            for (const conta of response.data.data) {
                contas.push(conta);
            }

            js_removeObj('loading_message_contas');
        } catch (e) {
            alert(e.response.data.message);
        }
    };

    buscarContas();

    btnGerarSlips.addEventListener('click', function () {

        let linhasGrid = grid.getGrid().aRows;

        let selecionados = []
        for (let linha of linhasGrid) {
            if (linha.isSelected) {
                if (linha.itemCollection.creditar == '' || linha.itemCollection.creditar == 0) {
                    alert(`As linhas selecionadas devem ter Conta Crédito selecionada.`);
                    return;
                }

                if (linha.itemCollection.debitar == '' || linha.itemCollection.debitar == 0) {
                    alert(`As linhas selecionadas devem ter Conta Débito válida.`);
                    return;
                }

                selecionados.push(linha.itemCollection.build());
            }
        }

        if (selecionados.length === 0) {
            alert('Selecione ao menos um registro')
            return;
        }

        const formData = new FormData(frmFiltros);
        PHPSession.appendFormData(formData);
        formData.append('acao', 'gerarSlips');
        formData.append('agrupar', agrupar.value);
        formData.append('retencoes', JSON.stringify(selecionados));

        HttpClient.post('emp4_geraslipscomapropriacao.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }

            let lista = response.slipsGerados.join(',')
            window.open(`cai1_slip003.php?numslip=${lista}`,'', 'location=0');

            retencoes.clear();
            grid.reload();
        });
    })


</script>
</body>
</html>
