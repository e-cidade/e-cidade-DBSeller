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
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body>
<div class="alert alert-primary text-left" role="alert">
    Essa rotina foi desenvolvida pensando em agilizar o processo de inativar ou excluir os Recursos que não mais
    serão utilizados.<br>

    - Se informado campo <b>Inativar a partir da Data</b>, o recurso será inativado a partir da data informada.<br>
    - Para remover a data de Inativação, basta selecionar os recursos, deixar o campo data vazio e clicar em
    <kbd><i class="fas fa-save"></i> Inativar Selecionados</kbd> <br>
    - Para <b>Excluir</b> os recursos para o exercício, selecione os recursos e clique em
    <kbd><i class="fas fa-trash"></i> Excluir selecionados</kbd>. Também é possível excluir recurso por recurso clicando
    em <kbd><i class="fas fa-trash"></i></kbd> na linha da tabela.<br>
</div>
<div class="container">
    <form id="formulario">
        <div class="subcontainer">
            <fieldset style="width: 450px">
                <legend>Exclusão / Inativar os recursos em <b id="exercicio"></b>.</legend>
                <table class="form-container">
                    <tr>
                        <td><label for="dataLimite">Inativar a partir da Data:</label></td>
                        <td><input name="dataLimite" id="dataLimite"></td>
                        <td>
                            <button type="button" id="btnInativar" class="btn btn-light">
                                <i class="fas fa-save"></i>
                                Inativar Selecionados
                            </button>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <fieldset style="width: 1100px">
            <legend>Recursos</legend>
            <table id="data-table"
                   class="table table-sm"
                   data-height="250"
                   data-virtual-scroll="true"
                   style="width: 100%;">
            </table>
        </fieldset>
        <button type="button" id="btnExcluir" class="btn btn-light">
            <i class="fas fa-trash"></i>
            Excluir selecionados
        </button>
    </form>
</div>
</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

<script>

    $.noConflict();
    jQuery(document).ready(function ($) {
        const formulario = document.getElementById('formulario');
        const dataLimite = new DBInputDate(document.getElementById('dataLimite'));
        const btnInativar = document.getElementById('btnInativar');
        const btnExcluir = document.getElementById('btnExcluir');

        var exercicio = 2022;

        const routs = {
            buscarRecursosInativar: 'financeiro/orcamento/cadastro/recursos/inativar',
            inativar: 'financeiro/orcamento/cadastro/recurso/inativar',
            excluir: 'financeiro/orcamento/cadastro/recurso/excluir'
        };

        const formatterActions = (value, row, index) => {
            return [
                '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                '  <i class="fas fa-trash-alt"></i>',
                '</a>'
            ].join('')
        };

        window.operateEvents = {
            'click .excluir': function (e, value, row, index) {
                let exercicio = PHPSession.getValueSession('DB_anousu');

                let recurso = `${row.codigo_siconfi} - ${row.descricao}`;
                let message = `Tem certeza que deseja excluir o recurso ${recurso} para o exercício ${exercicio}?`;
                alertify.confirm(message, (e) => {
                    if (e) {
                        excluirRecursos([row]);
                    }
                });
            }
        }

        const colunas = [
            {
                field: 'check',
                checkbox: true,
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Subrecurso',
                field: 'recurso.o15_recurso',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Siconfi',
                field: 'codigo_siconfi',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Gestão',
                field: 'gestao',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Descrição',
                field: 'descricao',
                align: 'left',
                valign: 'center',
                sortable: true
            },{
                title: 'Complemento',
                field: 'complemento',
                align: 'left',
                valign: 'center',
                sortable: true
            }, {
                title: 'Inativo em',
                field: 'data_limite',
                align: 'center',
                valign: 'center',
                sortable: true,
                formatter: (value) => {
                    return  value === null ? ' - ' : js_formatar(value, 'd')
                }
            }, {
                title: 'Ações',
                field: 'acoes',
                align: 'center',
                valign: 'center',
                events: window.operateEvents,
                formatter: formatterActions
            }
        ];

        const recursosSelecionados = [];

        var table = $('#data-table');
        table.bootstrapTable({
            columns: colunas,
            uniqueId: "id",
            locale: 'pt-BR',
            cache: false,
            height: 500,
            pagination: true,
            pageSize: 10,
            pageList: [10, 25, 50, 100, 200, 'All'],
            search: true,
            showButtonText: true,
            class: "table table-sm",
            onPostBody: (data) => {
                data.map((fonte, index) => {
                    if (fonte.selecionado) {
                        table.bootstrapTable('check', index);
                        adicionaRecurso(fonte);
                    } else {
                        removeRecurso(fonte)
                    }
                });
            },
            onCheckAll: (rowsAfter) => {
                rowsAfter.map(recurso => {
                    recurso.selecionado = true;
                    adicionaRecurso(recurso);
                });
            },
            onUncheckAll: (rowsAfter, rowsBefore) => {
                rowsBefore.map(recurso => {
                    recurso.selecionado = false;

                    removeRecurso(recurso);
                });
            },
            onCheck: (row) => {
                row.selecionado = true;
                adicionaRecurso(row);
            },
            onUncheck: (row) => {
                row.selecionado = false;
                removeRecurso(row);
            }
        });

        const adicionaRecurso = (fonte) => {
            let index = recursosSelecionados.findIndex(obj => obj.id == fonte.id);
            if (index < 0) {
                recursosSelecionados.push(fonte);
            }
        };

        const removeRecurso = (fonte) => {
            let index = recursosSelecionados.findIndex(obj => obj.id == fonte.id);
            if (index >= 0) {
                recursosSelecionados.splice(index, 1)
            }
        };

        const buscarRecursosInativar = () => {
            HttpClient.get(`${PHPSession.requestApi}/${routs.buscarRecursosInativar}/${exercicio}`).then(response => {

                table.bootstrapTable('load', response.data);
            });
        };

        PHPSession.loadData().then(() => {
            exercicio = PHPSession.getValueSession('DB_anousu');
            if (exercicio < 2022) {
                alert('Você no pode acessar essa rotina em um exercicio anterior a 2022.');
                return;
            }

            document.getElementById('exercicio').innerText = exercicio;
            buscarRecursosInativar();
        });

        btnInativar.addEventListener('click', () => {
            if (recursosSelecionados.length === 0) {
                alert('Selecione os Recursos que deseja inativar.');
                return;
            }

            const formData = new FormData;
            recursosSelecionados.each((fonte) => {
                formData.append('codigos[]', fonte.id);
            });
            formData.append('dataLimite', '');
            if (dataLimite.getValue() != null) {
                formData.append('dataLimite', js_formatar(dataLimite.__toLocaleDateString(), 'd'));
            }


            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: 'Aguarde, inativando fontes de recurso.'
            }

            let rota = `${PHPSession.requestApi}/${routs.inativar}`;
            executar(rota, parametros);
        });

        btnExcluir.addEventListener('click', () => {
            if (recursosSelecionados.length === 0) {
                alert('Selecione os Recursos que deseja excluir.');
                return;
            }
            alertify.confirm(`Tem certeza que deseja excluir os recursos selecionados?`, (e) => {
                if (e) {
                    excluirRecursos(recursosSelecionados);
                }
            });
        });

        const excluirRecursos = (fontes) => {

            let rota = `${PHPSession.requestApi}/${routs.excluir}`;
            const formData = new FormData;
            fontes.each((fonte) => {
                formData.append('codigos[]', fonte.id);
            });

            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: 'Aguarde, removendo fontes de recurso.'
            }

            executar(rota, parametros);
        };

        const executar = (rota, parametros) => {
            HttpClient.post(`${rota}`, parametros).then(response => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                formulario.reset();
                buscarRecursosInativar();
            });
        };

    });
</script>
