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
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    O campo <strong>Programas Estratégicos</strong> lista apenas os programas onde o usuário tem permissão
</div>

<div class="container">
    <div style="width: 1200px">
        <h2 style="text-align: left">Manutenção das Iniciativas do Programa Estratégico</h2>
        <table >
            <tr>
                <td style="text-align: left"><label class="bold" for="planejamento">Planejamento:&nbsp;</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
            <tr title="">
                <td style="text-align: left" ><label class="bold" for="programas">Programas Estratégicos:&nbsp;</label></td>
                <td>
                    <select id="programas" class="field-size8">
                        <option value="">Selecione um programa</option>
                    </select>
                </td>
            </tr>
        </table>

        <fieldset id="ctnTable" style="margin-top: 20px;">
            <legend>Iniciativas Cadastradas no programa</legend>
            <table id="data-table"
                   class="table table-sm"
                   data-height="250"
                   data-virtual-scroll="true"
                   style="width: 100%;">
            </table>
        </fieldset>
    </div>
</div>

<?php db_menu() ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>

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
<script type="text/javascript">
    const get = js_urlToObject();
    const routs = {
        filtrarProgramas : 'financeiro/planejamento/programas-estrategico/filtros',
        filtrarIniciativas : 'financeiro/planejamento/iniciativa/filtros',
        remover : 'financeiro/planejamento/iniciativa/remover'
    }

    const planejamento = new Planejamento(document.getElementById('planejamento'));
    const cboPrograma = document.getElementById('programas')

    $.noConflict();
    jQuery(document).ready(function ($) {
        const formatterActions = (value, row, index) => {
            return [
                '<a class="alterar" href="javascript:void(0)" title="Alterar">',
                '  <i class="fa fa-edit"></i>',
                '</a>',
                '&nbsp;&nbsp;',
                '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                '  <i class="fas fa-trash-alt"></i>',
                '</a>'
            ].join('')
        };

        window.operateEvents = {
            'click .alterar': function (e, value, row, index) {
                let parametros = [
                    `planejamento=${planejamento.getValue()}`,
                    `programa=${cboPrograma.value}`,
                    `codigo=${row.pl12_codigo}`,
                ];
                location.href = `pla4_iniciativasmanutencao.php?${parametros.join('&')}`;
            },
            'click .excluir': function (e, value, row, index) {

                let iniciativa = '';
                alertify.confirm(`Tem certeza que deseja excluir a iniciativa ${iniciativa}`, (e) => {
                    if (e) {
                        const formData = new FormData;
                        formData.append('pl12_codigo', row.pl12_codigo);
                        PHPSession.appendFormData(formData);

                        const parametros = {
                            body: formData,
                            reportMessage: `Aguarde, removendo iniciativa ${iniciativa}.`
                        }

                        HttpClient.post(`${PHPSession.requestApi}/${routs.remover}`, parametros).then(response => {
                            alert(response.message);
                            if (response.error) {
                                return;
                            }
                            table.bootstrapTable('remove', {
                                field: 'pl12_codigo',
                                values: [row.pl12_codigo]
                            });
                        });
                    }
                });
            }
        };

        function buttons() {
            return {
                btnAdd: {
                    text: 'Adicionar Iniciativa',
                    icon: 'fa-plus',
                    event: function () {
                        let codigo = planejamento.getValue()
                        if (codigo == '') {
                            alert("Selecione um planejamento.");
                            return;
                        }

                        if (cboPrograma.value == '') {
                            alert("Selecione um programa.");
                            return;
                        }

                        location.href = `pla4_iniciativasmanutencao.php?planejamento=${codigo}&programa=${cboPrograma.value}`;
                    },
                    attributes: {
                        title: 'Clique para adicionar uma nova iniciativa'
                    }
                }
            }
        };

        const colunas = [
            {
                title: 'Iniciativa',
                field: 'acao',
                align: 'center',
                valign: 'middle',
                sortable: true
            }, {
                title: 'Descrição',
                field: 'descricao_acao',
                align: 'left',
                valign: 'center',
                sortable: true
            }, {
                title: 'Ações',
                field: 'acoes',
                align: 'center',
                valign: 'center',
                events: window.operateEvents,
                formatter: formatterActions
            }
        ];

        var table = $('#data-table');
        table.bootstrapTable({
            columns: colunas,
            buttons: buttons,
            uniqueId: "pl12_codigo",
            locale: 'pt-BR',
            cache: false,
            height: 500,
            pagination: true,
            pageSize: 10,
            pageList: [10, 25, 50, 100, 200, 'All'],
            search: true,
            showButtonText: true,
            class: "table table-sm"
        });

        PHPSession.loadData().then(() => {
            planejamento.load();
        });

        planejamento.getElement().addEventListener('change', () => {

            cboPrograma.options.length = 0;
            cboPrograma.add(new Option('Selecione um programa', ''));
            table.bootstrapTable('load', []);
            if (planejamento.getValue() == '') {
                return;
            }

            const formData = new FormData();
            PHPSession.appendFormData(formData);
            formData.append('planejamento', planejamento.getValue());
            formData.append('filtrarPermissao', true);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando programas cadastrados no planejamento.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.filtrarProgramas}`, parametros).then(response => {

                response.data.map(programa => {
                    cboPrograma.add(new Option(`${programa.programa} - ${programa.descricao}`, programa.pl9_codigo));
                });

                if (get.programa) {
                    cboPrograma.value = get.programa;
                    cboPrograma.dispatchEvent(new Event('change'));
                    get.programa = '';
                }
            });
        });

        cboPrograma.addEventListener('change', () => {
            table.bootstrapTable('load', []);
            if (cboPrograma.value == '') {
                return;
            }
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            formData.append('pl12_programaestrategico', cboPrograma.value);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando as iniciativas do programa selecionado.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.filtrarIniciativas}`, parametros).then(response => {
                table.bootstrapTable('load', response.data);
            });

        });
    });
</script>
