<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>

    <style>
        #fade {
            position: absolute;
            top: 0;
            display: none;
            height: 100vh;
            align-items: center;
            justify-content: center;
            width: 100%;
            z-index: 1;
            background: rgba(0, 0, 0, 0.7);
        }

        #ctnModal {
            width: 90%;
            top: 0;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 2px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        }

        #close {
            position: absolute;
            float: right;
            right: 5px;
            cursor: pointer;
        }

        .form-container input[type="text"] {
            min-height: 22px;
            margin-left: 2px;
        }

        .form-container select {
            min-height: 22px;
            width: 100%;
        }

    </style>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Essa rotina realiza o vínculo das contas de Receita do Governo com as contas do e-cidade.<br>
    - Você pode mapear manualmente outras contas, selecionado a "Conta de Receita do Governo" e clicando em
    <kbd>Adicionar <i class="fa fa-plus"></i></kbd>
</div>
<div class="container">
    <table class="form-container" >
        <tr>
            <td><label for="exercicio">Exercício:</label></td>
            <td><select id="exercicio" name="exercicio" rel="ignore-css"></select></td>
        </tr>
        <tr>
            <td><label for="plano">Plano de Contas:</label></td>
            <td>
                <select id="plano" name="plano" rel="ignore-css">
                    <option value="uniao">União / Federação</option>
                    <option value="UF">Estadual / Regional</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><a href="#" id="ancoraConta">Plano Governo:</a></td>
            <td>
                <input type="text" id="conta" name="conta" class="field-size4" rel="ignore-css">
                <input type="text" id="descricaoConta" name="descricaoConta" class="field-size7 readonly" readonly
                       rel="ignore-css">
            </td>
        </tr>
    </table>
</div>


<div id="fade">
    <div id="ctnModal">
        <div class="alert text-left" role="alert">
            <i id="close" class="fas fa-window-close"></i>
            Clique sobre a linha para selecionar a conta.<br>
            <div class="alert-success"> - Contas que já possuem vínculos, mas podem ser selecionadas para adicionar/remover novas contas.</div>
        </div>
        <fieldset id="ctnPesquisaPcasp">
            <legend>Plano de Contas - <span id="labelTipoPlano"></span></legend>
            <div>
                <table id="tablePlanoGoverno"
                       class="table table-sm"
                       data-height="400"
                       data-virtual-scroll="true"

                       style="width: 100%;">
                </table>
            </div>
        </fieldset>
    </div>
</div>

<div class="subcontainer" style="width: 1200px">
    <fieldset id="ctnPesquisaPcasp">
        <legend>Plano Orçamentário da Receita e-Cidade</legend>
        <table id="tablePlanoEcidade"
               class="table table-sm"
               data-height="250"
               data-virtual-scroll="true"
               style="width: 100%;">
        </table>
    </fieldset>

    <button type="button" id="btnSalvar" name="btnSalvar" class="btn btn-light">
        <i class="fas fa-save"></i>
        Salvar
    </button>
    <button type="button" id="btnClean" name="btnClean" class="btn btn-light">
        <i class="fas fa-eraser"></i>
    </button>
    <button type="button" id="btnImprimir" name="btnImprimir" class="btn btn-light">
        <i class="fas fa-print"></i>
    </button>
</div>

<?php db_menu() ?>

<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src='scripts/widgets/Collection.widget.js'></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>

<script type="text/javascript">

    $.noConflict();
    var contaSelecionada = null;

    const collectionContasEcidade = new Collection().setId("codigo");

    const cboTipoPlano = document.getElementById('plano');
    const ancoraConta = document.getElementById('ancoraConta');
    const inputConta = document.getElementById('conta');
    const inputDescricaoConta = document.getElementById('descricaoConta');
    const cboExercicio = document.getElementById('exercicio');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnClean = document.getElementById('btnClean');
    const btnImprimir = document.getElementById('btnImprimir');
    const labelTipoPlano = document.getElementById('labelTipoPlano');

    const fade = document.getElementById('fade');
    const close = document.getElementById('close');
    close.onclick = () => {
        fade.style.display = "none"
    }

    const routs = {
        planoGoverso: 'financeiro/contabilidade/plano-contas/consulta/orcamentario/receita/padrao',
        ecidade: 'financeiro/contabilidade/plano-contas/consulta/orcamentario/receita/ecidade',
        vincular: 'financeiro/contabilidade/plano-contas/orcamentario/receita/vincular',
        mapeamento: 'financeiro/contabilidade/plano-contas/emitir/orcamentario/receita/mapeamento'
    }

    PHPSession.loadData().then(() => {
        let exercicio = Number(PHPSession.getValueSession('DB_anousu'));
        cboExercicio.add(new Option(exercicio, exercicio));
        cboExercicio.add(new Option(exercicio + 1, exercicio + 1));
    });

    const abrirPesquisaContaPcasp = () => {
        contaSelecionada = null;
        tablePlanoGoverno.bootstrapTable('removeAll');
        labelTipoPlano.innerHTML = cboTipoPlano.options[cboTipoPlano.selectedIndex].innerHTML;
        fade.style.display = "flex"
        buscaPlanoPadrao();
    };

    ancoraConta.addEventListener('click', abrirPesquisaContaPcasp);
    inputConta.addEventListener('change', abrirPesquisaContaPcasp);

    btnClean.addEventListener('click', () => {
        contaSelecionada = null;
        inputConta.value = '';
        inputDescricaoConta.value = '';
        tablePlanoEcidade.bootstrapTable('removeAll');
    });

    const buscaPlanoPadrao = () => {
        const formData = new FormData();
        formData.append('tipoPlano', cboTipoPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('apenasAnaliticas', 1);
        formData.append('comVinculos', 1);
        if (inputConta.value != '') {
            formData.append('conta', inputConta.value);
        }
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.planoGoverso}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            tablePlanoGoverno.bootstrapTable('load', response.data);
        });
    };

    const linhaComVinculo = (row, index) => {
        if (row.vinculada_outra_selecionada) {
            return {classes: 'alert-danger', css: {'font-weight': 'bold'}};
        }

        if (row.tem_vinculo) {
            return {classes: 'alert-success', css: { 'font-weight': 'bold'}};
        }
        return {classes: ''}
    }

    const tablePlanoGoverno = jQuery('#tablePlanoGoverno');
    tablePlanoGoverno.bootstrapTable({
        locale: 'pt-BR',
        search: true,
        onClickRow: function (row, $element, field) {
            fade.style.display = "none";
            contaSelecionada = row;
            inputConta.value = row.conta;
            inputDescricaoConta.value = row.nome;

            buscarContasEcidade();
        },
        rowStyle: linhaComVinculo,
        columns: [
            {
                "title": "Conta",
                "field": 'conta',
                "align": 'center',
                "valign": 'middle',
                "width" : "150"
            },
            {
                "title": "Nome",
                "field": 'nome',
                "align": 'left',
                "valign": 'middle'
            }
        ]
    });


    /** **************************************************************************************************************
     *  ************** Funções da tabela das contas orçamentaria do e-cidade
     *  **************************************************************************************************************
     */
    const operateFormatterActions = (value, row, index) => {
        return [
            '<a class="excluir" href="javascript:void(0)" title="Excluir">',
            '  <i class="fas fa-trash-alt"></i>',
            '</a>'
        ].join('')
    }

    window.operateEvents = {
        'click .excluir': function (e, value, row, index) {
            collectionContasEcidade.remove(row.codigo);
            console.log(collectionContasEcidade.build());
            tablePlanoEcidade.bootstrapTable('load', collectionContasEcidade.build());
        }
    };

    const tablePlanoEcidade = jQuery('#tablePlanoEcidade');
    tablePlanoEcidade.bootstrapTable({
        locale: 'pt-BR',
        rowStyle: linhaComVinculo,
        buttons: function () {
            return {
                btnAdd: {
                    html:
                        '<div style="text-align: right; margin-right: 5px;">' +
                        '  <button class="adicionar btn btn-light"> Adicionar <i class="fa fa-plus"></i></button>' +
                        '</div>',
                }
            }
        },
        columns: [
            {
                "title": "Conta e-Cidade",
                "field": 'estrutural',
                "align": 'center',
                "valign": 'middle',
                "width" : "150"
            },
            {
                "title": "Nome",
                "field": 'descricao',
                "align": 'left',
                "valign": 'middle'
            },
            {
                "title": "Ações",
                "field": 'acoes',
                "align": 'center',
                "valign": 'middle',
                "width" : "50",
                events: window.operateEvents,
                formatter: operateFormatterActions
            }
        ]
    });

    document.querySelector('.adicionar').addEventListener('click', () => {
        if (contaSelecionada === null) {
            alert('Antes de buscar as contas do e-Cidade, você deve selecionar a conta do Plano de contas do Governo.');
            return;
        }

        let url = `func_conplanoorcamento.php?funcao_js=parent.buscarCodcon|c60_estrut&exercicio=${cboExercicio.value}`;
        url +=`&apenasReceita=1`;
        js_OpenJanelaIframe('', 'db_iframe_conta', url, 'Pesquisa', true, '0');
    });

    function buscarCodcon(estrutural) {
        db_iframe_conta.hide();
        const formData = new FormData();
        formData.append('exercicio', cboExercicio.value);
        formData.append('estrutural', estrutural);
        formData.append('tipoPlano', cboTipoPlano.value);
        formData.append('apenasAnaliticas', 1);
        formData.append('temVinculoTipoPlano', cboTipoPlano.value);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.ecidade}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            console.log(response.data)
            loadContasEcidade(response.data)
        });
    }

    /**
     * - Adiciona as contas do e-cidade (conplanoorcamento) na collection
     * - Trata se a conta esta vinculada, e se esta vinculada a conta (Plano Governo) selecionada
     * - Carrega os dados na tabela
     * @param contas
     */
    const loadContasEcidade = contas => {
        contas.each(conta => {
            if (conta.contas_vinculadas.length > 0) {
                conta.tem_vinculo = true;
                conta.vinculada_outra_selecionada = conta.contas_vinculadas[0].id !== contaSelecionada.id;
            }
        });

        collectionContasEcidade.add(contas);
        tablePlanoEcidade.bootstrapTable('load', collectionContasEcidade.build());
    };

    /**
     * busca as contas do e-cidade vinculadas a conta do governo
     */
    const buscarContasEcidade = () => {
        const formData = new FormData();
        formData.append('exercicio', cboExercicio.value);
        formData.append('idContaVinculada', contaSelecionada.id);
        formData.append('apenasAnaliticas', 1);
        formData.append('temVinculoTipoPlano', cboTipoPlano.value);
        formData.append('tipoPlano', cboTipoPlano.value);

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.ecidade}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            collectionContasEcidade.clear();
            loadContasEcidade(response.data)
        });
    };


    btnSalvar.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('tipoPlano', cboTipoPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('planoorcamentario_id', contaSelecionada.id);
        let contasVinculadasOutraConta = [];
        let contasVinculadas = [];
        collectionContasEcidade.build().each(conta => {
            if (conta.vinculada_outra_selecionada) {
                contasVinculadasOutraConta.push(conta.estrutural);
                return
            }

            contasVinculadas.push(conta.codigo);
            formData.append('contas_ecidade[]', conta.codigo)
        });

        if (contasVinculadasOutraConta.length > 0) {
            let msg = `As contas do e-cidade grifadas em vermelho na tabela "Planos de Contas e-Cidade" nao seram `
                +`vinculadas a conta: ${contaSelecionada.conta}.\nContas do e-cidade que não serão vinculadas: `
                + contasVinculadasOutraConta.join(', ');
            alert(msg);
        }

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.vincular}`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            btnClean.dispatchEvent(new Event('click'));
        });
    });

    btnImprimir.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('exercicio', cboExercicio.value);
        formData.append('tipoPlano', cboTipoPlano.value);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.mapeamento}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.csv, "Relatório do mapeamento");
            download.show();
        });
    })
</script>
