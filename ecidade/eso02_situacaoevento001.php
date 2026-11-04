<?php
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
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('dbforms/db_funcoes.php');

$parametros = JSON::requestParameters();
?>
<!doctype html>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html">
        <meta http-equiv="Expires" CONTENT="0">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>

        <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
        <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>



        <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
        <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
        <link rel="stylesheet" type="text/css" href="estilos.css">
        <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
        <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
        <script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
        <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
        <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
        <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
       <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
    </head>

    <style type="text/css">
        #gridgridOcorrencias td, #gridgridStatus td {
            white-space: normal !important;
        }
        .form-control.search-input {
            font-size: 12px!important;
            margin-right: 15px!important;
        }
    </style>
    <body class='body-default'>
        <div class="container">
            <?php include_once modification("forms/db_frmsituacaoevento.php"); ?>
        </div>
        <?php db_menu(); ?>
        <script type='text/javascript'>
            var sUrlRPC = 'eso02_situacaoevento001.RPC.php';
            var dadosGrid;

            var iHeight = js_round((window.innerHeight / 2), 0);
            var iWidth = 750;
            windowAuxEsocial = new windowAux('wndApi',
                '',
                iWidth,
                iHeight
            );

            function imprimeRelatorio(tipo='pdf') {
                const params = {
                    'exec': 'imprimirRelatorioConsulta',
                    'filtros': {
                        'idEvento': $F('evento'),
                        'dataInicio': $F('dataInicio') + ' 00:00',
                        'dataFinal': $F('dataFim') + ' 23:59',
                        'statusErro': $F('statusErro'),
                        'statusRecibo': $F('statusRecibo'),
                        'statusOcorrencia': $F('statusOcorrencia'),
                        'tipoEvento': integracao,
                        'statusAdvertencia': $F('statusAdvertencia'),
                    },
                    'tipo': tipo
                };

                if (integracao === EFD_REINF) {
                    params.filtros.inscricaoEmpregador = selectContribuinte.value;
                }

                if (integracao === ESOCIAL) {
                    params.filtros.inscricaoEmpregador = selectEmpregador.value;
                }

                new AjaxRequest(
                    sUrlRPC,
                    params,
                    function (response, error) {
                        if (response.erro) {
                            alert(response.sMessage);
                            return;
                        }
                        window.open("db_download.php?arquivo=" + response.nomeArquivo);
                    }
                ).setMessage("Buscando os dados para imprimir o relatório..").execute();
            };

            $('#consultar').click(function () {
                try {
                    if (empty($F('dataInicio'))) {
                        throw 'O primeiro campo da Data Envio é de preenchimento obrigatório.';
                    }
                    if (empty($F('dataFim'))) {
                        throw 'O segundo campo da Data Envio é de preenchimento obrigatório.';
                    }

                    const oDataInicio = parseDataUTC($F('dataInicio'));
                    const oDataFim = parseDataUTC($F('dataFim'));

                    if (oDataInicio > oDataFim) {
                        throw 'A data inicial deve ser igual ou inferior a data final do envio.';
                    }
                    consultaDados();
                } catch (erro) {
                    alert(erro);
                    return false;
                }
            });

            function parseDataUTC (inputData) {
                const oData = new Date();
                const aDataDados = inputData.split('/');
                oData.setUTCFullYear(aDataDados[2]);
                oData.setUTCMonth(aDataDados[1] - 1);
                oData.setUTCDate(aDataDados[0]);
                return oData;
            }

            var oCollection = new Collection().setId('codigo');

            function consultaStatus(codigoEvento, responsavelPreenchimento) {
                var oParam = {};
                oParam.exec = 'consultaStatus';

                oParam.aFiltros = {};
                oParam.aFiltros.idEvento = codigoEvento;
                oParam.aFiltros.idReferencia = responsavelPreenchimento;

                if (integracao === EFD_REINF) {
                    oParam.aFiltros.inscricaoEmpregador = selectContribuinte.value;
                }

                if (integracao === ESOCIAL) {
                    oParam.aFiltros.inscricaoEmpregador = selectEmpregador.value;
                }

                js_divCarregando('Consultando os dados de recibo.', 'msgbox');

                var oAjax = new Ajax.Request(
                    sUrlRPC,
                    {
                        method: 'post',
                        parameters: 'json=' + js_objectToJson(oParam),
                        onComplete: mostraStatus
                    }
                );
            }

            function exibeDados(codigoEnvio) {
                var dadoJson = null;
                for (var dado of dadosGrid.dados) {
                    if (dado.codigoEnvio == codigoEnvio) {
                        dadoJson = dado;
                    }
                }

                var dado =  JSON.parse(dadoJson.dados);
                delete dado[`inscricao_empregador`];
                delete dado[`referencia`];
                var texto = syntaxHighlight(dado);
                js_divCarregando('Buscando os dados de enviados.', 'msgbox');
                windowAuxEsocial.setTitle('Dados Processados para envio.');
                windowAuxEsocial.setContent(texto);
                js_removeObj('msgbox');

                windowAuxEsocial.show();
            }

            function syntaxHighlight(json) {
                if (typeof json != 'string') {
                    json = JSON.stringify(json, undefined, 2);
                }
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return '<pre>' + json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = 'number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key';
                        } else {
                            cls = 'string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean';
                    } else if (/null/.test(match)) {
                        cls = 'null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                }) + '</pre>';
            }

            function consultaOcorrencias(evento, responsavelPreenchimento, codigo) {
                var oParam = {};
                oParam.exec = 'consultaOcorrencias';
                oParam.aFiltros = {};
                oParam.aFiltros.idEvento = evento;
                oParam.aFiltros.idReferencia = responsavelPreenchimento;
                oParam.idStatusEnvio = codigo;

                if (integracao === EFD_REINF) {
                    oParam.aFiltros.inscricaoEmpregador = selectContribuinte.value;
                }

                if (integracao === ESOCIAL) {
                    oParam.aFiltros.inscricaoEmpregador = selectEmpregador.value;
                }

                js_divCarregando('Consultando os dados de ocorrências.', 'msgbox');

                new Ajax.Request(
                    sUrlRPC,
                    {
                        method: 'post',
                        parameters: 'json=' + js_objectToJson(oParam),
                        onComplete: mostraOcorrencia
                    }
                );
            }

            function consultaRecibos(evento, responsavelPreenchimento) {
                var oParam = {};

                oParam.exec = 'consultaRecibos';
                oParam.aFiltros = {};
                oParam.aFiltros.idEvento = evento;
                oParam.aFiltros.tipoEvento = integracao;
                oParam.aFiltros.idReferencia = responsavelPreenchimento;

                if (integracao === EFD_REINF) {
                    oParam.aFiltros.inscricaoEmpregador = selectContribuinte.value;
                }

                if (integracao === ESOCIAL) {
                    oParam.aFiltros.inscricaoEmpregador = selectEmpregador.value;
                }

                js_divCarregando('Consultando os dados de recibo.', 'msgbox');

                new Ajax.Request(
                    sUrlRPC,
                    {
                        method: 'post',
                        parameters: 'json=' + js_objectToJson(oParam),
                        onComplete: mostraRecibo
                    }
                );
            }

            function consultaDados() {
                var oParam = {};

                oParam.exec = 'consultaDados';
                oParam.aFiltros = {};
                oParam.aFiltros.idEvento = $F('evento');
                oParam.aFiltros.dataInicio = $F('dataInicio') + ' 00:00';
                oParam.aFiltros.dataFinal = $F('dataFim') + ' 23:59';
                oParam.aFiltros.statusErro = $F('statusErro');
                oParam.aFiltros.statusRecibo = $F('statusRecibo');
                oParam.aFiltros.statusOcorrencia = $F('statusOcorrencia');
                oParam.aFiltros.statusAdvertencia = $F('statusAdvertencia');
                oParam.aFiltros.tipoEvento = integracao;

                if (integracao === EFD_REINF) {
                    oParam.aFiltros.inscricaoEmpregador = selectContribuinte.value;
                }

                if (integracao === ESOCIAL) {
                    oParam.aFiltros.inscricaoEmpregador = selectEmpregador.value;
                }

                js_divCarregando('Consultando os dados.', 'msgbox');

                new Ajax.Request(
                    sUrlRPC,
                    {
                        method: 'post',
                        parameters: 'json=' + js_objectToJson(oParam),
                        onComplete: preencheGrid
                    }
                );
            }

            function atualizarRegistro(evento, codigo) {
                var oParam = {
                    exec: 'atualizaRegistro',
                    evento: evento,
                    status_id: codigo,
                };

                var oAjax = new AjaxRequest(
                    sUrlRPC,
                    oParam,
                    function (oJson, lErro) {
                        if (oJson.iStatus != 2) {
                            var idBotaoAtualizar = "action_atualizar_" + oJson.idStatusEnvio;
                            $(idBotaoAtualizar).style.display = 'none';
                        }
                        alert(oJson.sMessage);
                    }
                ).setMessage("Atualizando registro.").execute();
            }

            function mostraRecibo(oJson) {
                js_removeObj('msgbox');

                var oRetorno = JSON.parse(oJson.responseText);

                if (oRetorno.iStatus == 1) {
                    var oGrid = new DBGrid('gridRecibos');
                    oGrid.setHeader(['Data', 'Protocolo', 'Número']);
                    oGrid.setHeight(iHeight - 100);
                    oGrid.setCellWidth(['33.33%', '33.33%', '33.33%']);
                    oGrid.setCellAlign(['center', 'center', 'center']);

                    $(oRetorno.recibos.forEach(function (recibo) {
                        oGrid.addRow([recibo.data, recibo.protocolo, recibo.numero]);
                    }));

                    windowAuxEsocial.setTitle('Recibo');
                    windowAuxEsocial.setContent('');
                    windowAuxEsocial.show();

                    oGrid.show($('windowwndApi_content'));
                } else {
                    alert(oRetorno.sMessage.urlDecode());
                }
            }

            function mostraStatus(oJson) {
                js_removeObj('msgbox');
                var oRetorno = JSON.parse(oJson.responseText);

                if (oRetorno.iStatus == 1) {
                    var oGrid = new DBGrid('gridStatus');
                    oGrid.setHeader(['Data', 'Descrição']);
                    oGrid.setHeight(iHeight - 100);
                    oGrid.setCellWidth(['20%', '80%']);
                    oGrid.setCellAlign(['center', 'left']);

                    $(oRetorno.dados.forEach(function (dado) {
                        oGrid.addRow([dado.data, dado.situacao.replace(/\n/g, '<br />')]);
                    }));
                    windowAuxEsocial.setTitle('Status');
                    windowAuxEsocial.setContent('');
                    windowAuxEsocial.show();

                    oGrid.show($('windowwndApi_content'));
                    oGrid.setNumRows(oRetorno.dados.length);
                } else {
                    alert(oRetorno.sMessage.urlDecode());
                }
            }

            function mostraOcorrencia(oJson) {
                js_removeObj('msgbox');
                var oRetorno = JSON.parse(oJson.responseText);

                if (oRetorno.iStatus == 1) {
                    var oGrid = new DBGrid('gridOcorrencias');
                    oGrid.setHeight(iHeight - 100);
                    oGrid.setHeader(['Código', 'Data', 'Descrição', 'Localização']);
                    oGrid.setCellWidth(['10%', '10%', '50%', '30%']);
                    oGrid.setCellAlign(['center', 'center', 'left', 'left']);
                    $(oRetorno.ocorrencias.forEach(function (ocorrencia) {
                        oGrid.addRow([
                            ocorrencia.codigo.toString(),
                            ocorrencia.data,
                            ocorrencia.descricao,
                            ocorrencia.localizacao
                        ]);
                    }));

                    windowAuxEsocial.setTitle('Ocorrências');
                    windowAuxEsocial.setContent('');
                    windowAuxEsocial.show();
                    oGrid.show($('windowwndApi_content'));
                    oGrid.setNumRows(oRetorno.ocorrencias.length);
                } else {
                    alert(oRetorno.sMessage.urlDecode());
                }
            }

            function preencheGrid(oJson) {
                js_removeObj('msgbox');
                var oRetorno = JSON.parse(oJson.responseText);

                if (oRetorno.iStatus == 1) {
                    dadosGrid = oRetorno;
                    table.bootstrapTable({
                        data: oRetorno.dados
                    });
                    table.bootstrapTable("load", oRetorno.dados);
                } else {
                    var limpa = [];
                    alert(oRetorno.sMessage.urlDecode());
                    table.bootstrapTable("load", limpa);
                }
            }

            var table = $('#data-table');
            var colunas = [
                {
                    title: 'Código',
                    field: 'codigo',
                    checkbox: false,
                    align: 'center',
                    valign: 'middle',
                    visible: false,
                    sortable: true
                },{
                    title: 'Data',
                    field: 'data',
                    checkbox: false,
                    sortable: true,
                    align: 'center',
                    valign: 'middle',
                    width: `150px`
                },{
                    title: 'Evento',
                    field: 'evento',
                    align: 'center',
                    valign: 'center',
                    width: '60px',
                    sortable: true
                },{
                    title: 'Descrição',
                    field: 'descricao',
                    sortable: true,
                    align: 'center',
                    valign: 'right',
                    width: '450px'
                },{
                    title: 'Ações',
                    field: 'acao',
                    align: 'center',
                    valign: 'right',
                    clickToSelect: false,
                    width: '100px',
                    formatter: adicionaAcao,
                    forceHide: true
                }
            ];
            function adicionaAcao(value, row, index) {
                return [
                    '<a href="#" onclick="consultaStatus(\'' + row.evento+'\',\'' + row.responsavelPreenchimento + '\')" title="Consultar" style="margin-right: 10px;"><i class="fas fa-eye"></i></a>',
                    '<a href="#" onclick="consultaOcorrencias(\'' + row.evento+'\',\'' + row.responsavelPreenchimento + '\',\'' + row.codigo + '\')" title="Ocorrências" style="margin-right: 10px; color: red"><i class="fas fa-exclamation-triangle"></i></a>',
                    '<a href="#" onclick="consultaRecibos(\'' + row.evento+'\',\'' + row.responsavelPreenchimento + '\')" title="Recibo" style="margin-right: 10px; color: green"><i class="fas fa-ticket-alt"></i></a>',
                    '<a href="#" onclick="exibeDados(' + row.codigoEnvio + ')" title="Dados Processados" ><i class="fas fa-file-alt" style="color: blue"></i></a>'
                ].join('');
            }
            window.operateEvents = {
                'click .like': function (e, value, row, index) {
                    alert('You click like action, row: ' + JSON.stringify(row))
                },
                'click .remove': function (e, value, row, index) {
                    $table.bootstrapTable('remove', {
                        field: 'id',
                        values: [row.id]
                    })
                }
            }

            $.noConflict();
            jQuery(document).ready(function($) {
                table.bootstrapTable({
                    columns : colunas,
                    uniqueId :"codigo",
                    locale : 'pt-BR',
                    cache : false,
                    height : 400,
                    pagination : true,
                    pageSize : 10,
                    pageList : [10, 25, 50, 100, 200, 'All'],
                    search : true,
                    class : "table table-sm",
                    showExport : true,
                    exportTypes : ['json', 'xml', 'csv', 'txt', 'excel']
                });

                (function () {
                    try {
                        buscarArquivos();
                    } catch (e) {
                        alert(e);
                    }
                })();
            });
        </script>
        <style>
            span.key{
                font-weight: bold;
            }

            span.string{
            }
        </style>
    </body>
</html>
