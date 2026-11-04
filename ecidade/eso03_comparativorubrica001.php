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
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html;>
        <meta http-equiv="Expires" CONTENT="0">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">        <link href="estilos.css" rel="stylesheet" type="text/css">
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
        <script src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js" rel="script" type="text/javascript"></script>

    </head>
    <style type="text/css">
        #gridgridOcorrencias td, #gridgridStatus td {
            white-space: normal !important;
        }
        th {
            color: #fff;
        }
    </style>
    <body class='body-default'>
        <div class="container">
            <fieldset style="width: 900px">
                <legend>Situação de Eventos</legend>
                <table class="form-container">
                    <tr style="display: none" id="trEmpregador">
                        <td>
                            <label for="empregador">Responsável:</label>
                        </td>
                        <td>
                            <select id="empregador" name="empregador" class="field-size-max"></select>
                        </td>
                    </tr>
                    <tr id="competenciaTr">
                        <td id="labelCompetencia"></td>
                        <td id="formularioCompetencia"></td>
                    </tr>
                </table>
            </fieldset>
            <button type="button" onclick="imprimeRelatorio()" id="imprimir"><i class="fas fa-print"></i> Imprimir</button>
            <div id="containerEventos" style="display: none">
                <fieldset>
                    <legend>Eventos</legend>
                    <div id="gridEventos"></div>
                        <div id="container">
                            <table id="data-table"/>
                        </div>
                    </div>
                </fieldset>
            </div>

        </div>
        <?php db_menu(); ?>
        <script type='text/javascript'>

            const sUrlRPC = 'eso_preenchimentos.RPC.php';
            const selectEmpregador = document.getElementById('empregador');
            const urlParams = new URLSearchParams(window.location.search);
            const integracao = urlParams.has('integracao') ? urlParams.get('integracao') : '2';
            const competenciaTr = document.getElementById('competenciaTr');
            const labelCompetencia = document.getElementById('labelCompetencia');
            const formularioCompetencia = document.getElementById('formularioCompetencia');
            var table = $('#data-table');
            var colunas = [
                {
                    title: 'Código',
                    field: 'codigo',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle',
                    visible: false,
                    sortable: true
                },{
                    title: 'Rubrica',
                    field: 'rubrica',
                    checkbox: false,
                    align: 'center',
                    valign: 'middle',
                    visible: false,
                    sortable: true
                },{
                    title: 'Descrição',
                    field: 'data',
                    checkbox: false,
                    sortable: true,
                    align: 'center',
                    valign: 'middle',
                    width: `150px`
                }
            ];
            function buscarEmpregador()
            {
                const formData = new FormData();

                formData.append('acao', 'inicializar');
                formData.append('integracao', integracao);

                HttpClient.post('sped02_preenchimento.RPC.php', {
                    body: formData
                }).then(response => {
                    if (response.erro) {
                        throw response.mensagem;
                    }

                    response.empregadores.map(empregadorOption => {
                        selectEmpregador.add(new Option(empregadorOption.nome, empregadorOption.cgm));
                    });
                    document.getElementById('trEmpregador').style.display = '';
                }).catch(mensagem => alert(mensagem));
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
                    pageList : [10, 25, 50, 100, 200],
                    search : true,
                    class : "table table-sm"
                });

                buscarEmpregador();
            });

            function buscaRubricas()
            {
                const params = {
                    'exec': 'buscaRubricas',
                    'filtros': {
                        'inscricaoEmpregador' : selectEmpregador.value
                    }
                };


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
            }

            function imprimeRelatorio() {
                var ano = formularioCompetencia.querySelector("#ano").value;
                var mes = formularioCompetencia.querySelector("#mes").value;
                if (ano == '') {
                    alert ('É necessário preencher o ano da competência.');
                    return false;
                }
                if (mes == '') {
                    alert ('É necessário preencher o mês da competência.');
                    return false;
                }
                const params = {
                    'exec': 'imprimirRelatorio',
                    'filtros': {
                        'tipoFormulario' : 2,
                        'inscricaoEmpregador' : selectEmpregador.value,
                        'ano' : ano,
                        'mes' : mes
                    }
                };

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
            }

            var dbViewFormularioFolha = new DBViewFormularioFolha.CompetenciaFolha(false);
            dbViewFormularioFolha.renderizaLabel(labelCompetencia);
            dbViewFormularioFolha.renderizaFormulario(formularioCompetencia);
        </script>
    </body>
</html>
