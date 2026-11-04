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
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body class="body-default">
<div class="container">
    <div style="width: 1200px">
        <h2 style="text-align: left">Manutenção do detalhamento da despesa.</h2>
        <table>
            <tr>
                <td style="text-align: left"><label class="bold" for="planejamento">Planejamento:&nbsp;</label></td>
                <td>
                    <select id="planejamento" class="field-size8">
                        <option value="">Selecione um plano</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align: left"><label class="bold" for="programa">Programas:&nbsp;</label>
                </td>
                <td>
                    <select id="programa" class="field-size8">
                        <option value="">Selecione um programa estratégico</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align: left">
                    <label class="bold" for="iniciativa">Iniciativa:&nbsp;</label></td>
                <td>
                    <select id="iniciativa" class="field-size8">
                        <option value="">Selecione uma iniciatva</option>
                    </select>
                </td>
            </tr>
        </table>
        <fieldset id="ctnTable" style="margin-top: 20px;">
            <legend>Detalhamentos da despesa cadastrados</legend>
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
        programas: 'financeiro/planejamento/programas-estrategico/filtros',
        iniciativas: 'financeiro/planejamento/iniciativa/filtros',
        buscarDetalhamento: 'financeiro/planejamento/despesa/detalhamento/buscar',
        remover: 'financeiro/planejamento/despesa/detalhamento/remover',
    };

    const planejamento = new Planejamento(document.getElementById('planejamento'));
    const cboPrograma = document.getElementById('programa');
    const cboIniciativa = document.getElementById('iniciativa');

    $.noConflict();
    jQuery(document).ready(function ($) {
        PHPSession.loadData().then(() => {
            planejamento.load();
        });

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

        const getParametros = () => {
            return  [
                `planejamento=${planejamento.getValue()}`,
                `programa=${cboPrograma.value}`,
                `iniciativa=${cboIniciativa.value}`,
                `exercicio=${planejamento.getPlano().pl2_ano_inicial}`
            ];
        }

        function buttons() {
            return {
                btnAdd: {
                    text: 'Adicionar Detalhamento',
                    icon: 'fa-plus',
                    event: function () {
                        if (cboIniciativa.value == '') {
                            alert("Selecione uma iniciativa.");
                            return;
                        }
                        let parameters = getParametros();
                        location.href = `pl4_detalhamento_despesa_manutencao.php?${parameters.join('&')}`;
                    },
                    attributes: {
                        title: 'Clique para adicionar um novo detalhamento da despesa'
                    }
                }
            }
        };
        window.operateEvents = {
            'click .alterar': function (e, value, row, index) {
                let parameters = getParametros();
                parameters.push(`codigo=${row.pl20_codigo}`);

                location.href = `pl4_detalhamento_despesa_manutencao.php?${parameters.join('&')}`;
            },
            'click .excluir': function (e, value, row, index) {
                alertify.confirm(`Tem certeza que deseja excluir o detalhamento ${row.estrutural}`, (e) => {
                    if (e) {
                        const formData = new FormData;
                        formData.append('pl20_codigo', row.pl20_codigo);
                        PHPSession.appendFormData(formData);

                        const parametros = {
                            body: formData,
                            reportMessage: `Aguarde, removendo detalhamento.`
                        }

                        HttpClient.post(`${PHPSession.requestApi}/${routs.remover}`, parametros).then(response => {
                            alert(response.message);
                            if (response.error) {
                                return;
                            }
                            table.bootstrapTable('remove', {
                                field: 'pl20_codigo',
                                values: [row.pl20_codigo]
                            });
                        });
                    }
                });
            }
        }

        const colunas = [
            {
                title: 'Estrutural',
                field: 'estrutural',
                align: 'center',
                valign: 'middle',
                width: '400',
                sortable: true
            }, {
                title: 'Esfera',
                field: 'esferaOrcamentaria',
                align: 'left',
                valign: 'center',
                sortable: true
            }, {
                title: 'C. Peculiar',
                field: 'cp',
                align: 'left',
                valign: 'center',
                sortable: true
            }, {
                title: 'Ações',
                field: 'acoes',
                align: 'center',
                valign: 'center',
                width: '100',
                events: window.operateEvents,
                formatter: formatterActions
            }
        ];

        var table = $('#data-table');
        table.bootstrapTable({
            columns: colunas,
            buttons: buttons,
            uniqueId: "pl20_codigo",
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

        planejamento.getElement().addEventListener('change', () => {

            resetSelects(true, true);
            if (planejamento.getValue() === '') {
                return;
            }

            const formData = new FormData();
            formData.append('planejamento', planejamento.getValue());
            formData.append('filtrarPermissao', true);
            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando os programas estratégicos cadastrados no planejamento.`
            };

            HttpClient.post(`${PHPSession.requestApi}/${routs.programas}`, parametros).then(response => {
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
            resetSelects(false, true);
            if (cboPrograma.value === '') {
                return;
            }

            const formData = new FormData();
            formData.append('pl12_programaestrategico', cboPrograma.value);

            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando as iniciativas do programas estratégicos.`
            };

            HttpClient.post(`${PHPSession.requestApi}/${routs.iniciativas}`, parametros).then(response => {
                response.data.map(iniciativa => {
                    cboIniciativa.add(new Option(`${iniciativa.acao} - ${iniciativa.descricao_acao}`, iniciativa.pl12_codigo));
                });

                if (get.iniciativa) {
                    cboIniciativa.value = get.iniciativa;
                    cboIniciativa.dispatchEvent(new Event('change'));
                    get.iniciativa = ''
                }
            });
        });

        cboIniciativa.addEventListener('change', () => {
            resetSelects(false, false);
            if (cboIniciativa.value === '') {
                return;
            }

            const formData = new FormData();
            formData.append('pl20_iniciativaprojativ', cboIniciativa.value);
            PHPSession.appendFormData(formData);
            const parametros = {
                body: formData,
                reportMessage: `Aguarde, buscando os detalhamentos da despesa.`
            };

            HttpClient.post(`${PHPSession.requestApi}/${routs.buscarDetalhamento}`, parametros).then(response => {
                response.data.map(detalhamento => {
                    let cp = detalhamento.caracteristica_peculiar;
                    detalhamento.cp = `${cp.c58_sequencial} - ${cp.c58_descr}`;
                    detalhamento.descricaoOrgao = detalhamento.orgao.o40_descr;
                });
                table.bootstrapTable('load', response.data);
            });
        });

        const resetSelects = (programa, iniciativa) => {
            if (programa) {
                cboPrograma.options.length = 0;
                cboPrograma.add(new Option('Selecione um programa estratégico', ''));
            }
            if (iniciativa) {
                cboIniciativa.options.length = 0;
                cboIniciativa.add(new Option('Selecione uma iniciativa', ''));
            }
            limparTabela();
        };

        const limparTabela = () => {
            table.bootstrapTable('load', []);
        }
    });
</script>
