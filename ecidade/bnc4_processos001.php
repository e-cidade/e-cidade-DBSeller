<?php

require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$bncConfigurado = env('BNC_URL') && env('BNC_ACCESS_TOKEN') ? 1 : 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body style="margin:0;">
<div class="alert alert-primary text-left" role="alert">
    Esta rotina tem como finalidade comunicar-se com o
    serviço da Bolsa Nacional De Compras (BNC) para a modalidade pregão eletrônico.
</div>

<div class="container" style="width: 65%;">
    <fieldset>
        <legend>Processos</legend>

        <table class="form-container">
            <tr>
                <td style="width: 5%;">
                    <label for="licitacao-id" id="ancoraLicitacao">Licitação: </label>
                </td>
                <td>
                    <input type="text" name="licitacao-id" id="l20_codigo" class="field-size3" maxlength="10">

                    <button class="btn btn-light" id="botaoExportar" <?= !$bncConfigurado ? 'disabled' : '' ?>>
                        <i class="fas fa-save"></i>
                        Enviar Dados
                    </button>
                    <button class="btn btn-light" id="btnConsultarLicitacao" <?= !$bncConfigurado ? 'disabled' : '' ?>>
                        <i class="fas fa-search"></i>
                        Consultar
                    </button>
                    <button
                        class="btn btn-light"
                        id="btnConsultarLicitacaoBnc"
                        <?= !$bncConfigurado ? 'disabled' : '' ?>
                    >
                        <i class="fas fa-search"></i>
                        Consultar BNC
                    </button>
                </td>
            </tr>
        </table>
    </fieldset>
</div>

<div class="container" style="width: 65%;">
    <table id="data-table" class="table table-sm"></table>
</div>

<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>

<script>
    $.noConflict();

    if (<?= $bncConfigurado ?> === 0) {
        alert('Não foi possível estabelecer conexão com o BNC, verifique a configuração das variáveis de ambiente.');
    }

    const botaoExportar = jQuery('#botaoExportar');
    const tabelaLicitacao = jQuery('#data-table');
    const inputLicitacaoId = jQuery('#l20_codigo');
    const inputLicitacaoAno = jQuery('#licitacaoAno');
    const inputLicitacaoObjeto = jQuery('#licitacaoObjeto');
    const botaoConsultarLicitacao = jQuery('#btnConsultarLicitacao');
    const botaoConsultarLicitacaoBnc = jQuery('#btnConsultarLicitacaoBnc');

    let rotasApi;

    function getRotasApi(baseUrl) {
        return {
            importar:
                baseUrl + '/patrimonial/licitacoes/bnc/importar',
            buscar:
                baseUrl + '/patrimonial/licitacoes/bnc/buscar',
            exportar:
                baseUrl + '/patrimonial/licitacoes/bnc/exportar',
            excluir:
                baseUrl + '/patrimonial/licitacoes/bnc/excluir',
        }
    }

    function inputEstaVazio(id) {
        let valorInput = jQuery(`#${id}`).val();

        return (
            valorInput === '' || valorInput === undefined ||
            valorInput === false || valorInput === 0 ||
            valorInput === null
        );
    }

    function verificaCamposObrigatorios() {
        if (inputEstaVazio('l20_codigo')) {
            alert('Selecione a Licitação.');
            return false;
        }

        return true;
    }

    function buscarDadosLicitacao() {
        const formData = new FormData;
        PHPSession.appendFormData(formData);

        const codigoLicitacao = inputLicitacaoId.val();
        HttpClient.post(`${rotasApi.buscar}/${codigoLicitacao}`)
            .then(response => {
                if (response.error) {
                    if (response.message) {
                        return alert(response.message);
                    }

                    tabelaLicitacao.bootstrapTable('load', []);
                    return alert('Não foi possível buscar as informações da licitação.');
                }

                tabelaLicitacao.bootstrapTable('load', [response.data])
            });
    }

    function formatterData(valorCampo) {
        if (!valorCampo) return valorCampo;

        const data = new Date(valorCampo);
        return data.toLocaleDateString()
    }

    function verificarStatusValidoImportacao(processo) {
        const statusProcesso = processo.idStatus;
        if (!statusProcesso) {
            return false;
        }

        const statusPermitidos = [5, 24, 26, 28, 29];
        return statusPermitidos.includes(statusProcesso);
    }

    PHPSession.loadData().then(async () => {
        rotasApi = getRotasApi(PHPSession.requestApi);
    });

    jQuery(() => {
        window.operateEvents = {
            'click #excluir': () => {
                const licitacaoId = inputLicitacaoId.val();
                const exclusaoConfirmada = confirm(`Confirma a exclusão da licitação ${licitacaoId} do portal BNC?`);
                if (!exclusaoConfirmada) {
                    return;
                }

                HttpClient.post(`${rotasApi.excluir}/${licitacaoId}`)
                    .then(response => {
                        if (response.error) {
                            return alert(response.message)
                        }

                        alert(response.message);

                        tabelaLicitacao.bootstrapTable('load', []);
                        inputLicitacao.value = '';
                    })
                    .catch(response => alert(response.message));
            },
            'click #importar': (evento, val, dados) => {
                const licitacaoId = inputLicitacaoId.val();
                const statusProcesso = dados.processo.Status;

                if (!verificaCamposObrigatorios()) {
                    return;
                }

                if (!verificarStatusValidoImportacao(dados.processo)) {
                    return alert(
                        `Não é possível importar licitações com situação ${statusProcesso.toLowerCase()}.`
                    );
                }

                const importacaoConfirmada = confirm(
                    'Confirma importação dos dados '
                     + `da licitação ${licitacaoId} (${statusProcesso.toLowerCase()}) do portal BNC?`
                );

                if (!importacaoConfirmada) {
                    return;
                }

                HttpClient
                    .post(`${rotasApi.importar}/${licitacaoId}`)
                    .then(function (response) {
                        if (response.message) {
                            alert(response.message);
                        }
                    });
            }
        };

        let colunas = [
            {
                field: 'processo.Modality',
                title: 'Modalidade',
                halign: 'center',
                width: '20%',
            },
            {
                field: 'licitacao',
                title: 'Número/Ano',
                halign: 'center',
                width: '15%',
                formatter: function (licitacao) {
                    const anoLicitacao = licitacao.l20_anousu;
                    const numeroLicitacao = licitacao.l20_numero;

                    return `${numeroLicitacao}/${anoLicitacao}`;
                }
            },
            {
                field: 'processo.PublicationTime',
                title: 'Data da publicação',
                halign: 'center',
                width: '15%',
                formatter: formatterData
            },
            {
                field: 'processo.DisputeStart',
                title: 'Data da disputa',
                halign: 'center',
                width: '15%',
                formatter: formatterData
            },
            {
                field: 'processo.HomologationDate',
                title: 'Data de homologação',
                halign: 'center',
                width: '15%',
                formatter: formatterData
            },
            {
                field: 'processo.Status',
                title: 'Situação',
                halign: 'center',
                width: '15%',
            },
            {
                field: 'acao',
                title: 'Ações',
                halign: 'center',
                width: '15%',
                formatter: () => {
                    let acoes = '<a id="excluir" title="Excluir"><i class="fas fa-trash-alt"></i></a>';
                    acoes += `
                    <a id="importar" title="Importar" style="margin-left: 5px">
                        <i class="fas fa-download"></i>
                    </a>`;

                    return acoes;

                },
                events: window.operateEvents
            }
        ]

        tabelaLicitacao.bootstrapTable({
            locale: 'pt-BR',
            height: 300,
            search: false,
            class: "table table-sm",
            columns: colunas,
            showButtonText: true,
            cache: false,
            useRowAttrFunc: true,
            reorderableRows: true,
        });
    });

    botaoConsultarLicitacao.on('click', () => {
        if (inputEstaVazio('l20_codigo')) {
            return alert('Selecione a licitação para consultar mais detalhes.');
        }

        const codigoLicitacao = jQuery('#l20_codigo').val();

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe',
            'lic3_licitacao002.php?l20_codigo=' + codigoLicitacao,
            'Pesquisa',
            true
        );
    });

    botaoConsultarLicitacaoBnc.on('click', () => {
        if (inputEstaVazio('l20_codigo')) {
            return alert('Selecione a licitação para consultar mais detalhes.');
        }

        buscarDadosLicitacao();
    });

    botaoExportar.on('click', function () {
        const licitacaoId = inputLicitacaoId.val();

        if (!verificaCamposObrigatorios()) {
            return;
        }

        const exportacaoConfirmada = confirm(`Confirma o envio dos dados da licitação ${licitacaoId} ao portal BNC?`);
        if (!exportacaoConfirmada) {
            return;
        }

        HttpClient
            .post(`${rotasApi.exportar}/${licitacaoId}`)
            .then(function (response) {
                if (response.message) {
                    alert(response.message);
                }
            });
    });

    const ancoraLicitacao = document.getElementById('ancoraLicitacao');
    const inputLicitacao = document.getElementById('l20_codigo');

    const comprasLookup = new DBLookUp(ancoraLicitacao, inputLicitacao, inputLicitacao, {
        'arquivo': 'func_liclicitabnc.php',
        'label': 'Pesquisa de licitações',
        'objetoLookUp': 'db_iframe_licitacao',
        'parametrosAdicionais': ['situacao=0', 'sigla=PRE'],
        'callBack': function () {
            tabelaLicitacao.bootstrapTable('load', []);
        }
    });
</script>
</body>
</html>
