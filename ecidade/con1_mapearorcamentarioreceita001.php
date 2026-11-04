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
        .form-container input[type="text"] {
            min-height: 22px;
            margin-left: 2px;
        }

        .flex-container {
            padding: 0;
            margin: 0;
            /*list-style: none;*/
            -ms-box-orient: horizontal;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -moz-flex;
            display: -webkit-flex;
            display: flex;
            justify-content: space-between;
        }

        .row {
            -webkit-flex-direction: row;
            flex-direction: row;
        }

        .flex-item {
            padding: 5px;
            width: 50%;
            height: 600px;
            margin: 5px;
            font-size: 0.8em;
            text-align: left;
        }

        .filtros {
            display: flex;
            flex-flow: row wrap;
            padding-left: 10px;
            justify-content: space-around;
            width: 500px;
            font-size: 0.8em;
            align-items: center;
        }

        .filtros label {
            font-weight: bold;
        }

        .filtros button {
            height: 22px;
        }
    </style>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    - <kbd><i class="fas fa-print"></i></kbd> imprime as contas: não mapeadas com movimentação,
    não mapeadas sem movimentação e as contas mapeadas.
</div>

<div class="filtros">
    <div class="flex-item-filtros">
        <label for="exercicio">Exercício:</label>
        <select id="exercicio" name="exercicio" rel="ignore-css"></select>
    </div>
    <div class="flex-item-filtros">
        <label for="plano">Plano de Contas:</label>
        <select id="plano" name="plano" rel="ignore-css">
            <option value="uniao">União / Federação</option>
            <option value="UF">Estadual / Regional</option>
        </select>
    </div>
    <div class="flex-item-filtros">
        <button type="button" id="btnBuscar" name="btnBuscar" class="btn btn-light" disabled>
            <i class="fas fa-search"></i>
        </button>
        <button type="button" id="btnImprimir" name="btnImprimir" class="btn btn-light">
            <i class="fas fa-print"></i>
        </button>
    </div>
</div>
<div class="flex-container row">
    <div class="flex-item">
        <fieldset id="">
            <legend>Plano de Contas - e-Cidade</legend>
            <div>
                <table id="tableEcidade"
                       class="table table-sm"
                       data-height="550"
                       data-virtual-scroll="false"
                       data-maintain-meta-data="true"
                       style="width: 100%;">
                </table>
            </div>
        </fieldset>
    </div>

    <div class="flex-item">
        <fieldset id="">
            <legend>Plano de Contas - Governo</legend>
            <table id="tablePlanoOrcamentario"
                   class="table table-sm"
                   data-height="550"
                   data-detail-view="true"
                   data-virtual-scroll="false"
                   data-maintain-meta-data="true"
                   style="width: 100%;">
            </table>
        </fieldset>
    </div>
</div>

<?php db_menu() ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript">
    $.noConflict();
    const cboExercicio = document.getElementById('exercicio');
    const cboPlano = document.getElementById('plano');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnImprimir = document.getElementById('btnImprimir');

    const routs = {
        planoGoverso: 'financeiro/contabilidade/plano-contas/consulta/orcamentario/receita/padrao',
        planoEcidade: 'financeiro/contabilidade/plano-contas/consulta/orcamentario/receita/ecidade',
        vincular: 'financeiro/contabilidade/plano-contas/orcamentario/receita/vincular',
        desvincular: 'financeiro/contabilidade/plano-contas/orcamentario/receita/desvincular',
        mapeamento: 'financeiro/contabilidade/plano-contas/emitir/orcamentario/receita/mapeamento'
    };

    PHPSession.loadData().then(() => {
        btnBuscar.disabled = false;
        let exercicio = Number(PHPSession.getValueSession('DB_anousu'));
        cboExercicio.add(new Option(exercicio, exercicio));
        cboExercicio.add(new Option(exercicio + 1, exercicio + 1));
    });

    const detailFormatter = (index, row) => {
        let detalhes = [], dados = [];
        row.contas_ecidade.map((conta) => {
            dados.push(conta.estrutural);
        });

        detalhes.push([{label: "Contas e-Cidade:", valor: dados.join(', ')}]);

        return detailFormaterTable.createDetail(detalhes, `Contas Vinculadas`);
    };

    const linhaComVinculo = (row, index) => {
        if (row?.contas_ecidade !== undefined && row.contas_ecidade.length > 0) {
            return {classes: 'alert-success', css: {'font-weight': 'bold'}};
        }

        if (row?.vinculada !== undefined && row.vinculada) {
            return {classes: 'alert-success', css: {'font-weight': 'bold'}};
        }

        return {classes: ''}
    }

    const formatterActions = (value, row, index) => {
        return [
            '<a class="excluir" href="javascript:void(0)" title="Excluir">',
            '  <i class="fas fa-eraser"></i>',
            '</a>'
        ].join('')
    };

    window.operateEvents = {
        'click .excluir': function (e, value, row, index) {
            let contasVinculadas = row.contas_vinculadas.map(conta => {
                return conta.conta
            }).join(', ');

            let msg = `Tem certeza que quer remover o vínculo da conta ${row.estrutural} com a(s) conta(s) `;
            msg += contasVinculadas;
            alertify.confirm(msg, (e) => {
                if (e) {
                    desvincular(row);
                }
            });
        }
    };

    const tableEcidade = jQuery('#tableEcidade');
    tableEcidade.bootstrapTable({
        locale: 'pt-BR',
        search: true,
        uniqueId: "estrutural",
        rowStyle: linhaComVinculo,
        columns: [
            {
                field: 'check',
                checkbox: true,
                align: 'center',
                valign: 'middle'
            },
            {
                "title": "Conta",
                "field": 'estrutural',
                "align": 'center',
                "valign": 'middle',
                "width": "150"
            },
            {
                "title": "Nome",
                "field": 'descricao',
                "align": 'left',
                "valign": 'middle'
            },
            {
                "title": "Vinculada a",
                "field": 'vinculada',
                "align": 'left',
                "valign": 'middle',
                "width": "150",
                formatter: function (value, row) {
                    let contasVinculadas = row.contas_vinculadas.map(conta => {
                        return conta.conta
                    }).join(', ');
                    return `<label title="${contasVinculadas}">${contasVinculadas}</label>`
                }
            },
            {
                "title": "Ações",
                "field": 'acoes',
                "align": 'center',
                "valign": 'middle',
                "width": "50",
                events: window.operateEvents,
                formatter: formatterActions
            }
        ]
    });

    const tablePlano = jQuery('#tablePlanoOrcamentario');
    tablePlano.bootstrapTable({
        locale: 'pt-BR',
        uniqueId: "conta",
        cache: false,
        search: true,
        class: "table table-sm",
        detailFormatter: detailFormatter,
        rowStyle: linhaComVinculo,
        onClickCell: (field, value, row, $element) => {
            if (field !== 'nome') {
                return;
            }

            if (!validaContasSelecionadas()) {
                return;
            }
            let plano = cboPlano.options[cboPlano.selectedIndex].innerHTML;
            let msg = `Confirma o vinculo das contas do e-Cidade com a conta: ${row.conta} da(o) plano: ${plano}`
            alertify.confirm(msg, (e) => {
                if (e) {
                    vincular(row);
                }
            });
        },
        columns: [
            {
                "title": "Conta",
                "field": 'conta',
                "align": 'center',
                "valign": 'middle',
                "width": "150"
            },
            {
                "title": "Nome",
                "field": 'nome',
                "align": 'left',
                "valign": 'middle'
            },
            {
                "title": "Sintética",
                "field": 'sintetica',
                "align": 'center',
                "valign": 'middle',
                "width": "100",
                formatter: function (value, row) {
                    return value ? 'Sim' : 'Não';
                }
            }
        ]
    });

    btnBuscar.addEventListener('click', function () {
        tableEcidade.bootstrapTable('showLoading');
        tablePlano.bootstrapTable('showLoading');


        buscarContasEcidade();
        buscarContasPlanoGoverno();
    });

    const buscarContasEcidade = () => {
        const formData = new FormData();
        formData.append('tipoPlano', cboPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('comVinculos', 1);
        formData.append('receita', 1);
        formData.append('apenasAnaliticas', 1);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.planoEcidade}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            tableEcidade.bootstrapTable('hideLoading');
            tableEcidade.bootstrapTable('load', response.data);
        });
    };


    const buscarContasPlanoGoverno = () => {
        const formData = new FormData();
        formData.append('tipoPlano', cboPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('comVinculos', 1);
        formData.append('apenasAnaliticas', 1);

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.planoGoverso}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            tablePlano.bootstrapTable('hideLoading');
            tablePlano.bootstrapTable('load', response.data);
        });
    };

    const validaContasSelecionadas = () => {
        if (tableEcidade.bootstrapTable('getSelections').length === 0) {
            alert('Você deve selecionar ao menos uma conta na tabela "Plano de Contas - e-Cidade" antes de clicar para víncular.');
            return false;
        }
        return true;
    };

    const vincular = linha => {
        const formData = new FormData();
        formData.append('tipoPlano', cboPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('planoorcamentario_id', linha.id);

        tableEcidade.bootstrapTable('getSelections').map(conta => {
            formData.append('contas_ecidade[]', conta.codigo);
        });

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.vincular}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            btnBuscar.dispatchEvent(new Event('click'));
        });
    };

    const desvincular = linha => {
        const formData = new FormData();
        formData.append('tipoPlano', cboPlano.value);
        formData.append('exercicio', cboExercicio.value);
        formData.append('conplanoorcamento_codigo', linha.codigo);
        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${routs.desvincular}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            btnBuscar.dispatchEvent(new Event('click'));
        });
    };

    btnImprimir.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('exercicio', cboExercicio.value);
        formData.append('tipoPlano', cboPlano.value);
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
</body>
</html>
