<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script src="scripts/widgets/Input/DBInput.widget.js" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputInteger.widget.js" type="text/javascript"></script>
    <script src="scripts/widgets/Input/DBInputDate.widget.js" type="text/javascript"></script>
    <script src="scripts/classes/http/http.js" type="text/javascript"></script>
</head>
<style>
    body {
        overflow-x: hidden;
        overflow-y: scroll !important;
    }
    .griRegistros {
        display: table;
        margin: 100px auto 0 auto;
        text-align: center;
        width: 70%;
        overflow: hidden;
    }
</style>
<body >
    <form class="container" id="formLicitacao">
        <fieldset>
            <legend
                style="font-family: Arial, Helvetica, serif, sans-serif, verdana; font-size: 12px;">
                Relatório de Itens Bloqueados
            </legend>
            <table class="form-container" style="border-collapse: separate;">
                <tr>
                    <td class="field-size2">
                        <label for="contratos">
                            <a id="abreModal" style="color: blue; text-decoration: underline">Licitação: </a>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="codigoLicitacao" />
                    </td>
                </tr>
            </table>
        </fieldset>

        <button id="btnImprimir" name="imprimir" type="button" class="btn btn-sm">
            <i class="fas fa-print"></i>
            Imprimir
        </button>

    </form>

    <div class="griRegistros" id="modal">
        <fieldset>
            <legend>Licitações</legend>
            <table id="data-table"
                   class="table table-sm"
                   data-cache="false"
                   data-height="500"
                   data-show-columns="true">
            </table>
        </fieldset>
    </div>

</body>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>

<script>
    $.noConflict();
    var ancora = document.querySelector("#abreModal");
    var modal = document.querySelector("#modal");
    var inputLicitacao = new DBInputInteger($('codigoLicitacao'));

    modal.style.display = 'none';
    var formulario = document.querySelector("#formLicitacao");

    const routs = {
        registrosDePreco: "patrimonial/licitacoes/registrosDePreco",
        itensBloqueados: "patrimonial/licitacoes/itensBloqueados"
    };

    function buscaRegistros() {
        HttpClient.get(`${PHPSession.requestApi}/${routs.registrosDePreco}`).then(response => {
            table.bootstrapTable('load', response.data);
        });
    }

    PHPSession.loadData().then(() => {
        buscaRegistros();
    });

    const colunas = [
        {
            title: 'Cod. Sequencial',
            field: 'l21_codliclicita',
            halign: 'center',
            align: 'center',
            width: '1px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                                onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                                ${data.l21_codliclicita}
                            </a>`;
                return link;
            }
        },
        {
            title: 'Sigla',
            field: 'l44_sigla',
            halign: 'center',
            align: 'center',
            width: '50px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                               onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                               ${data.l44_sigla}
                           </a>`;
                return link;
            }
        },
        {
            title: 'Descrição do Tipo de Compra',
            field: 'pc50_descr',
            halign: 'center',
            align: 'center',
            width: '70px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                               onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                               ${data.pc50_descr}
                           </a>`;
                return link;
            }
        },
        {
            title: 'Numeração',
            field: 'l20_numero',
            halign: 'center',
            align: 'center',
            width: '20px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                                onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                                ${data.l20_numero}
                            </a>`;
                return link;
            }
        },
        {
            title: 'Exercício',
            field: 'l20_anousu',
            halign: 'center',
            align: 'center',
            width: '20px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                                onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                                ${data.l20_anousu}
                            </a>`;
                return link;
            }
        },
        {
            title: 'Descrição da Situação',
            field: 'l20_licsituacao',
            halign: 'center',
            align: 'center',
            width: '20px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                               onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                               ${data.l08_descr}
                           </a>`;
                return link;
            }
        },
        {
            title: 'Data Abertura',
            field: 'l20_dataaber',
            halign: 'center',
            align: 'center',
            width: '70px;',
            formatter: (a, data) => {
                date = new Date(data.l20_dataaber);
                dataFormatada = date.toLocaleDateString('pt-BR', {timeZone: 'UTC'});
                let link = `<a style="text-decoration: none; color: black"
                                onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                                ${dataFormatada}
                            </a>`;
                return link;
            }
        },
        {
            title: 'Objeto',
            field: 'l20_objeto',
            halign: 'center',
            align: 'center',
            width: '400px;',
            formatter: (a, data) => {
                let link = `<a style="text-decoration: none; color: black"
                                onclick="selecionaLicitacao(${data.l21_codliclicita})" >
                                ${data.l20_objeto.substr(0,60) + '...'}
                            </a>`;
                return link;
            }
        }
    ];

    const table = jQuery('#data-table');

    table.bootstrapTable({
        columns: colunas,
        uniqueId: "id",
        locale: 'pt-BR',
        cache: false,
        search: true,
        class: "table table-sm",
    });

    ancora.addEventListener("click", async () => {
        formulario.style.display = "none";
        modal.style.display = "table";
    })

    function selecionaLicitacao(codigoLicictacao) {
        inputLicitacao.value = codigoLicictacao;
        formulario.style.display = "table";
        modal.style.display = "none";
    }

    document.querySelector("#btnImprimir").addEventListener("click", () => {


            var formData = new FormData();
            formData.append("licitacao", inputLicitacao.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(`${PHPSession.requestApi}/${routs.itensBloqueados}`, {body: formData}).then(response => {
                window.open(response.data.pdf, '_blank');
                inputLicitacao.value = "";
            });
    })
</script>
<?php
db_menu();
?>
</html>
