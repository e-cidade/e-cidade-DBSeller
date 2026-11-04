<?php

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
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
</head>
<body class="body-default">
<div class="container">

    <div style="width: 1000px">

        <h2 style="text-align: left">Manutenção dos Planos de Governo</h2>

        <fieldset id="ctnTable" style="margin-top: 20px;">
            <legend>Planos de Governo</legend>
            <div style="clear: both"></div>

            <table id="data-table"
                   class="table table-sm"
                   data-height="250"
                   data-virtual-scroll="true"

                   style="width: 100%;">
            </table>
        </fieldset>
    </div>
</div>
<div id="modalPlanoBase" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Escolha o plano que deseja usar como base para criar o PPA.
    </div>
    <div class="container">
        <table class="form-container">
            <tr>
                <td><label class="bold" for="planejamento">Plano Bae:</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label class="bold" for="anoInicial">Exercício Inicial do PPA: </label></td>
                <td>
                    <input type="text" id="anoInicial" name="pl2_ano_inicial" class="field-size2"
                           maxlength="4" oninput="js_ValidaCampos(this, 1, 'Ano', 'f', 'f', event)">
                </td>
            </tr>
        </table>

        <button type="button" id="criarPlano">
            <i class="far fa-save"></i>
            Criar PPA
        </button>
    </div>
</div>

<?php db_menu() ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript">

    const status = 1; // Só pode buscar os Planos de Governo EM DESENVOLVIMENTO

    const getURLParameters = url =>
        (url.match(/([^?=&]+)(=([^&]*))/g) || []).reduce(
            (a, v) => ((a[v.slice(0, v.indexOf('='))] = v.slice(v.indexOf('=') + 1)), a),
            {}
        );

    const get = getURLParameters(window.location.search);

    const routs = {
        index: 'financeiro/planejamento/consulta/planos',
        remove: 'financeiro/planejamento/remove',
        criarVinculo: 'financeiro/planejamento/criarVinculo',
        criarPPA: 'financeiro/planejamento/criarPPA',
    }

    const modalPlano = {
        container: document.getElementById('modalPlanoBase'),
        anoInicial: document.getElementById('anoInicial'),
        btnCriarPlano: document.getElementById('criarPlano')
    }

    const planejamento = new Planejamento(document.getElementById('planejamento'));


    const fechaModal = (janela) => {
        if (!!janela.oDBMask) {
            janela.oDBMask.destroy();
        }
        janela.hide();
    }

    var windowPlanoBase = new windowAux('windowPlanoBase', 'Escolha o plano base', 450, 300);
    windowPlanoBase.setContent(modalPlano.container);
    windowPlanoBase.setShutDownFunction(() => {
        fechaModal(windowPlanoBase)
    });

    $.noConflict();
    jQuery(document).ready(function ($) {

        function buttons() {
            return {
                btnAdd: {
                    html:
                        '<div style="text-align: right; margin-right: 5px;">' +
                        '  <button class="adicionar"> Adicionar <i class="fa fa-plus"></i></button>' +
                        '</div>',
                }
            }
        }

        const operateFormatterActions = (value, row, index) => {
            return [
                '<a class="alterar" href="javascript:void(0)" title="Alterar">',
                '  <i class="fa fa-edit"></i>',
                '</a>',
                '&nbsp;&nbsp;',
                '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                '  <i class="fas fa-trash-alt"></i>',
                '</a>'
            ].join('')
        }

        window.operateEvents = {
            'click .alterar': function (e, value, row, index) {
                location.href = `pla1_planejamento.php?tipo=${get.tipo}&codigo=${row.pl2_codigo}`;
            },
            'click .excluir': function (e, value, row, index) {

                let preposicao = get.tipo === 'PPA' ? 'o' : 'a';

                alertify.confirm(`Tem certeza que deseja excluir ${preposicao} ${get.tipo}`, (e) => {
                    if (e) {
                        const formData = new FormData;
                        formData.append('pl2_codigo', row.pl2_codigo);
                        PHPSession.appendFormData(formData);

                        HttpClient.post(`${PHPSession.requestApi}/${routs.remove}`, {body: formData}).then(response => {
                            alert(response.message);
                            if (response.error) {
                                return;
                            }
                            table.bootstrapTable('remove', {
                                field: 'pl2_codigo',
                                values: [row.pl2_codigo]
                            });
                        });
                    }
                });
            }
        }

        const table = jQuery('#data-table');
        table.bootstrapTable({
            buttons: buttons,
            locale: 'pt-BR',
            columns: [
                {
                    "title": "Período",
                    "field": 'periodo',
                    "align": 'center',
                    "valign": 'middle'
                },
                {
                    "title": "Plano",
                    "field": 'pl2_titulo',
                    "align": 'left',
                    "valign": 'middle'
                },
                {
                    "title": "Ações",
                    "field": 'acoes',
                    "align": 'center',
                    "valign": 'middle',
                    events: window.operateEvents,
                    formatter: operateFormatterActions
                }
            ]
        });

        PHPSession.loadData().then(() => {
            HttpClient.get(`${PHPSession.requestApi}/${routs.index}/${get.tipo}/${status}`).then(response => {
                let dados = response.data.map((plano) => {
                    plano.periodo = `${plano.pl2_ano_inicial} à ${plano.pl2_ano_final}`;
                    return plano;
                });

                table.bootstrapTable('load', dados);
            });
        });

        document.querySelector('.adicionar').addEventListener('click', () => {

            if (get.tipo !== 'PPA') {

                let tipoVincular = get.tipo === 'LDO' ? 'PPA' : 'LDO';
                const formData = new FormData();
                formData.append('tipo', get.tipo);
                formData.append('tipoVincular', tipoVincular);

                PHPSession.appendFormData(formData);

                const parametros = {
                    body: formData,
                    reportMessage: `Aguarde, buscando ${tipoVincular} aprovado para criar vínculo.`
                }

                HttpClient.post(`${PHPSession.requestApi}/${routs.criarVinculo}`, parametros).then(response => {

                    if (!response.error) {
                        table.bootstrapTable('load', response.data);
                        let codigo = response.data.pl2_codigo;
                        location.href = `pla1_planejamento.php?tipo=${get.tipo}&codigo=${codigo}`;
                    } else {
                        alert(response.message);
                    }
                });
            } else if (get.tipo === 'PPA') {
                let msg = 'Deseja escolher um plano anterior como base para o novo PPA?'
                alertify.confirm(msg, (e) => {
                    if (e) {
                        abrirModalPlano()
                    } else {
                        location.href = `pla1_planejamento.php?tipo=${get.tipo}`;
                    }
                });
            } else {
                location.href = `pla1_planejamento.php?tipo=${get.tipo}`;
            }
        });

        function abrirModalPlano() {

            planejamento._considerarGet = false;
            planejamento.load();
            windowPlanoBase.show(0, 0, true);
            modalPlano.container.style.display = '';
        }

        modalPlano.btnCriarPlano.addEventListener('click', () => {
            if (planejamento.getValue() === '') {
                alert('Selecione um plano.');
                return
            }
            if (modalPlano.anoInicial.value === '' || modalPlano.anoInicial.value.length < 4) {
                alert('Ano inicial deve ser informado e deve ser um exercício válido.')
                return
            }

            const formData = new FormData();
            formData.append('codigo', planejamento.getValue());
            formData.append('anoInicial', modalPlano.anoInicial.value);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportProgress: true,
                reportMessage: `Aguarde, criando plano.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.criarPPA}`, {body: formData}).then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                location.href = `pla1_planejamento.php?tipo=${get.tipo}&codigo=${response.data}`;
            });
        })
    });

</script>
</body>
</html>
