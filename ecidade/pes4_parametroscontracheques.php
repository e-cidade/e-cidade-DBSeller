<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$mesFolha = DBPessoal::getMesFolha() - 1;
$anoFolha = DBPessoal::getMesFolha() == '01' ? DBPessoal::getAnoFolha() - 1
    : DBPessoal::getAnoFolha();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css"
          rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css"
          rel="stylesheet">
    <style>
        #progressbar {
            width: 100%;
            height: 24px;
            border: 1px solid #2c5676;
            background-color: #edf5ff;
        }

        #progressbar #progressbar_status {
            box-sizing: border-box;
            height: 23px;
            background-color: #2c5676;
            text-align: center;
            color: #fff;
            transition: width 1s;
        }
    </style>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Emissão Contra cheques</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td>
                    <label for="">Período: </label>
                </td>
                <td>
                    <input type="text" id="mes" value="<?= $mesFolha ?>"
                           size="3"/> /
                    <input type="text" id="ano" value="<?= $anoFolha ?>"
                           size="6"/>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="button_emitir">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</div>
<div class="container">
    <fieldset>
        <legend>Processamento</legend>
        <div style="width: 1000px">
            <table id="data-table"
                   class="table table-sm"
                   data-height="300"
                   data-virtual-scroll="true"
                   style="width: 100%;">
            </table>
        </div>
    </fieldset>
    <div class="alert alert-primary text-left" role="alert">
        Antes de emitir os <strong>contra-cheques</strong> de uma competência
        verifique se já não está em processamento!
    </div>
    <button type="button" class="btn btn-light" id="btnAtualizar">
        <i class="fas fa-sync"></i>
        Atualizar
    </button>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript"
        src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript"
        src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript"
        src="assets/jquery-tablednd/jquery.tablednd.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
            buscarEmitidos();
        });
    });
    const inputMes = document.getElementById("mes");
    const inputAno = document.getElementById("ano");
    const btnEmitir = document.getElementById("button_emitir");
    const btnAtualizar = document.getElementById("btnAtualizar");

    const buscarEmitidos = (showLoading = true) => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/recursos-humanos/pessoal/contra-cheques/emitidos`, {body: formData},{
            reportProgress:showLoading
        })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                }
                table.bootstrapTable('load', response.data);
            });
    }

    btnAtualizar.addEventListener('click', () => {
        buscarEmitidos();
    });

    btnEmitir.addEventListener('click', () => {
        if (empty(inputMes.value)) {
            alert("Informe o Mês.");
            return;
        }
        if (empty(inputAno.value)) {
            alert("Informe o Ano.");
            return;
        }

        const formData = new FormData();
        formData.append('ano', inputAno.value);
        formData.append('mes', inputMes.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/recursos-humanos/pessoal/contra-cheques/processar-competencia`, {body: formData})
            .then((response) => {
                alert(response.message);
                buscarEmitidos();
            });
    });


    const abrirFalhas = (batch_id) => {
        var url = CurrentWindow.ECIDADE_REQUEST_PATH;
        url = url.replace(/\/w/g, 'w');
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_falhas',
            `${url}web/recursos-humanos/pessoal/contra-cheque-app-falhas/${batch_id}`,
            'Falhas',
            true
        );
    }


    const statusFormatter = (value) => {
        return `<div id="progressbar">
            <div id="progressbar_status" style="width: ${value}%">${value}%</div>
        </div>`;
    }

    const falhaFormatter = (value, row, index) => {
        if (value > 0) {
            return `<a class="falhas" onclick="abrirFalhas(${row.batch_id})" href="#">${value}</a>`;
        }
        return 0;
    }

    const callbackAcoes = (value, row) => {
        if (row.status == 100) {
            return `<i class="fas fa-check"></i> Concluído`;
        }
        if (row.cancelado) {
            return `<i class="fas fa-ban"></i> Cancelado`;
        }
        return `<a href="#" class="cancelar"><i class="fas fa-ban"></i> Cancelar </a>`;
    }

    window.operateEvents = {
        'click .cancelar': function (e, value, row, index) {
            if (!confirm('Deseja cancelar a emissão de contra-cheques?')) {
                return;
            }

            const formData = new FormData();
            formData.append('batch_id', row.batch_id);
            PHPSession.appendFormData(formData);
            HttpClient.post(`${urlApi}/recursos-humanos/pessoal/contra-cheques/cancelar-emissao`, {body: formData})
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                    }

                    buscarEmitidos();
                });
        }
    }

    var table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        showButtonText: true,
        class: "table table-sm",
        reorderableRows: true,
        useRowAttrFunc: true,
        columns: [
            {
                title: 'Cód.',
                field: 'batch_id',
                align: 'left',
                width: '70px'
            }, {
                title: 'Competência',
                field: 'competencia',
                align: 'left',
                width: '150px'
            }, {
                title: 'Quantidade',
                field: 'quantidade',
                align: 'left',
                width: '150px'
            }, {
                title: 'Falhas',
                field: 'falhas',
                align: 'left',
                width: '80px',
                formatter: falhaFormatter
            }, {
                title: 'Status',
                field: 'status',
                align: 'left',
                formatter: statusFormatter,
            }, {
                field: 'acoes',
                title: 'Ações',
                align: 'center',
                events: window.operateEvents,
                formatter: callbackAcoes,
                width: '100px',
            }
        ],
        rowStyle: (row, index) => {
            if (row.status == 100) {
                return {
                    classes: 'alert-success'
                }
            }
            if (row.cancelado) {
                return {
                    classes: 'alert-danger'
                }
            }
            return {}
        }
    });

    setInterval(()=>{
        buscarEmitidos(false)
    },30000)

</script>
</body>
</html>
