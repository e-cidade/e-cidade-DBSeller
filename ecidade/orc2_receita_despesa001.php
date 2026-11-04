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
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
</head>
<body>
<div id='ctnAbas'></div>

<div id='ctnAbaEmissao' class='subcontainer'>
    <fieldset>
        <legend>Acanhamento das Metas de Arrecadação da Receita</legend>
        <form id="formulario">
            <fieldset class="separator">
                <legend>Filtros para impressão</legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size3"><label for="agruparPor">Agrupar:</label></td>
                        <td>
                            <select id="agruparPor" name="agruparPor">
                                <option value="recurso">Por Recurso e Complemento</option>
                                <option value="fonte_recurso">Por Recurso</option>
                                <option value="geral">Totalização Geral</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size3"><label for="periodicidade">Periodicidade:</label></td>
                        <td>
                            <select id="periodicidade" name="periodicidade">
                                <option value="mensal" selected>Mensal</option>
                                <option value="bimestral">Bimestral</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td id="ctnInstituicao" colspan="2" style="font-weight: normal">
                            <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </fieldset>

</div>
<div id="cntAbaRecursos" style="display: none">
    <fieldset class="subcontainer" style="width: 900px">
        <legend>Selecione os recursos que deseja filtrar</legend>
        <table id="data-table"
               class="table table-sm"
               data-locale="pt-BR"
               data-cache="false"
               data-height="600"
               data-search="true"
               style="width: 100%;">
        </table>
    </fieldset>
</div>

<div id="cntAbaNotasExplicativas" style="display: none">
    <iframe name="iframe_processapad" src="con2_conrelnotas.php?c83_codrel=77" width="100%" height="750px"  >
    </iframe>
</div>
<div class="subcontainer">
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</div>
</body>
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/planejamento.js"></script>
<script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript">
    var viewInstituicao = {};

    $.noConflict();
    jQuery(document).ready(function (jQuery) {
        // Objetos para controle das Abas
        let ctnAbaRecursos = document.getElementById('cntAbaRecursos');
        let cntAbaNotasExplicativas = document.getElementById('cntAbaNotasExplicativas');
        const dBAba = new DBAbas(document.getElementById('ctnAbas'));
        dBAba.adicionarAba("Relatório", document.getElementById('ctnAbaEmissao'));
        dBAba.adicionarAba("Recursos", ctnAbaRecursos);
        dBAba.adicionarAba("Notas Explicativas", cntAbaNotasExplicativas);
        ctnAbaRecursos.style.display = 'block';
        cntAbaNotasExplicativas.style.display = 'block';

        const recursosSelecionados = [];
        const btnEmitir = document.getElementById('emitir');

        viewInstituicao = new DBViewInstituicao('viewInstituicao', document.getElementById('ctnInstituicao'));
        viewInstituicao.show();

        const routs = {
            recursos: 'financeiro/orcamento/recursos',
            relatorio: 'financeiro/orcamento/relatorios/meta-x-cotas'
        };

        const montaColunas = () => {
            return [{
                field: 'check',
                checkbox: true,
                align: 'center',
                valign: 'middle',
            },
                {
                    title: 'Recurso',
                    field: 'o15_recurso',
                    halign: 'center',
                    valign: 'middle',
                    align: 'left',
                    width: '100',
                    sortable: true
                },
                {
                    title: 'Siconfi',
                    field: 'codigo_siconfi',
                    halign: 'center',
                    valign: 'middle',
                    align: 'left',
                    width: '100',
                    sortable: true
                },
                {
                    title: 'Gestão',
                    field: 'gestao',
                    halign: 'center',
                    valign: 'middle',
                    align: 'left',
                    width: '100',
                    sortable: true
                },
                {
                    title: 'Recurso',
                    field: 'descricao_recurso',
                    halign: 'center',
                    valign: 'middle',
                    align: 'left',
                    sortable: true
                },
                {
                    title: 'Complemento',
                    field: 'descricao_complemento',
                    halign: 'center',
                    valign: 'middle',
                    align: 'left',
                    sortable: true
                }
            ];
        };

        const adicionaRecurso = (recurso) => {
            let index = recursosSelecionados.findIndex(obj => obj.id == recurso.id);
            if (index < 0) {
                recursosSelecionados.push(recurso);
            }
        };

        const removeRecurso = (recurso) => {
            let index = recursosSelecionados.findIndex(obj => obj.id == recurso.id);
            if (index >= 0) {
                recursosSelecionados.splice(index, 1)
            }
        };

        var table = jQuery('#data-table');
        table.bootstrapTable({
            columns: montaColunas(),
            data: [],
            onPostBody: (data) => {
                data.map((recurso, index) => {
                    if (recurso.selecionado) {
                        table.bootstrapTable('check', index);
                        adicionaRecurso(recurso);
                    } else {
                        removeRecurso(recurso)
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
        })

        const buscarRecursos = (exercicio, dataFinal) => {

            HttpClient.get(`${PHPSession.requestApi}/${routs.recursos}/${exercicio}/${dataFinal}`).then(response => {

                let dados = response.data.map((recurso) => {
                    recurso.descricao_recurso = recurso.descricao;
                    recurso.descricao_complemento = `${recurso.o15_complemento} - ${recurso.o200_descricao}`;
                    recurso.check = false
                    return recurso;
                });

                table.bootstrapTable('load', dados);
            });
        };

        PHPSession.loadData().then(() => {
            buscarRecursos(PHPSession.getValueSession('DB_anousu'), PHPSession.getValueSession('data_usuario'));
        });

        const valida = () => {
            try {
                if (viewInstituicao.getInstituicoesSelecionadas(true).length === 0) {
                    throw 'Selecione ao menos uma instituição';
                }
            } catch (e) {
                alert(e);
                return false
            }
            return true;
        };

        btnEmitir.addEventListener('click', () => {
            if (!valida()) {
                return;
            }

            const formData = new FormData(document.getElementById('formulario'));
            for (let codigo of viewInstituicao.getInstituicoesSelecionadas(true)) {
                formData.append('instituicoes[]', codigo);
            }

            for (let recurso of recursosSelecionados) {
                formData.append('recursos[]', recurso.o15_codigo);
            }

            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.relatorio}`, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                const download = new DBDownload();
                download.addFile(response.data.pdf, "Metas de Arrecadação x Cotas da Despesa - PDF");
                download.addFile(response.data.csv, "Metas de Arrecadação x Cotas da Despesa - CSV");
                download.show();
            });
        });
    });
</script>
