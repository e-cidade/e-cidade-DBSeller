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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('dbforms/db_funcoes.php');

$instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit'));
$getRequest = db_utils::postMemory($_GET);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Documento Termo Contrato</legend>

        <input
            type="hidden"
            id="anoContrato"
            value="<?= !empty($getRequest->anoContrato) ? $getRequest->anoContrato : ''; ?>"
        >
        <input
            type="hidden"
            id="numeroContrato"
            value="<?= !empty($getRequest->numeroContrato) ? $getRequest->numeroContrato : ''; ?>"
        >
        <input
            type="hidden"
            id="sequencialTermoContrato"
            value="<?= !empty($getRequest->termo) ? $getRequest->termo : ''; ?>"
        >
        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">

        <form enctype="multipart/form-data" id="form" onsubmit="return false;">
            <table class="form-container">
                <tr>
                    <td>
                        <label>Termo:</label>
                    </td>
                    <td>
                        <input
                            type="text"
                            name="tipoTermo"
                            value="<?= !empty($getRequest->tipoTermo) ? $getRequest->tipoTermo : ''; ?>"
                            class="field-size5"
                            disabled
                        >
                        <input
                            type="text"
                            name="numeroTermo"
                            value="<?= !empty($getRequest->numeroTermo)
                                ? base64_decode($getRequest->numeroTermo)
                                : ''; ?>"
                            class="field-size4"
                            disabled
                        >
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="tituloDocumento">Título do Documento:</label>
                    </td>
                    <td>
                        <input contrato type="text" id="tituloDocumento" class="field-size9" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="tipoDocumentoId">Tipo:</label>
                    </td>
                    <td>
                        <select
                            contrato
                            id="tipoDocumentoId"
                            name="tipoDocumentoId"
                            rel="ignore-css"
                            class="field-size9"
                        >
                            <option value="0" disabled selected>Selecione</option>
                            <option value="13">Termo de Recisão</option>
                            <option value="14">Termo Aditivo</option>
                            <option value="15">Termo de Apostilamento</option>
                            <option value="16">Outros</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="documento">Documento:</label>
                    </td>
                    <td>
                        <input contrato type="file" id="documento" class="field-size9" style="height: auto;">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>

    <button class="btn" id="btnSalvar">
        <i class="fa fa-save" aria-hidden="true"></i>
        Salvar
    </button>
    <button type="button" id="fechar" class="btn" onClick="parent.db_iframe_acordo.hide();">
        Fechar
    </button>
</div>

<div class="subcontainer" style="width: 70%">
    <fieldset>
        <legend>Termos</legend>
        <table id="data-table" class="table table-sm"></table>
    </fieldset>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<script>
    $.noConflict();

    let integracaoAtiva = false;
    let rotasApi;

    const botaoSalvar = jQuery('#btnSalvar');
    const inputAnoContrato = jQuery('#anoContrato');
    const tabelaDocumentos = jQuery('#data-table');
    const inputNumeroContrato = jQuery('#numeroContrato');
    const cnpj = jQuery('#cnpj').val();

    function getRotasApi(baseUrl) {
        return {
            buscarEntidade:
                baseUrl + '/patrimonial/pncp/unidades/buscarEntidade/',
            verificaIntegracao:
                baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
            incluirDocumento:
                baseUrl + '/patrimonial/pncp/termos/documentos/incluir',
            buscarDocumentos:
                baseUrl + '/patrimonial/pncp/termos/documentos/buscar',
            excluirDocumento:
                baseUrl + '/patrimonial/pncp/termos/documentos/excluir',
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

        if (inputEstaVazio('sequencialTermoContrato')) {
            alert('Selecione o termo.');
            return false;
        }

        if (inputEstaVazio('numeroContrato')) {
            alert('Preencha o número do contrato.');
            return false;
        }

        if (inputEstaVazio('anoContrato')) {
            alert('Preencha o ano do contrato.');
            return false;
        }

        if (inputEstaVazio('tituloDocumento')) {
            alert('Preencha o titulo do documento.');
            return false;
        }

        if (inputEstaVazio('tipoDocumentoId')) {
            alert('Preencha o tipo do documento.');
            return false;
        }

        if (inputEstaVazio('documento')) {
            alert('Selecione o documento.');
            return false;
        }

        return true;
    }

    function montarCamposEnvio() {
        const formData = new FormData;
        const documento = document.getElementById('documento').files[0];

        formData.append('cnpj', cnpj);
        formData.append('anoContrato', inputAnoContrato.val());
        formData.append('numeroContrato', inputNumeroContrato.val());
        formData.append('sequencialTermoContrato', jQuery('#sequencialTermoContrato').val());
        formData.append('tituloDocumento', jQuery('#tituloDocumento').val());
        formData.append('tipoDocumentoId', jQuery('#tipoDocumentoId').val());
        formData.append('documento', documento);

        return formData;
    }

    function limparFormulario() {
        jQuery('input[contrato],textarea[contrato]').val('');

        const contratoSelect = jQuery('select[contrato]');
        contratoSelect.each((index, select) => {
            jQuery(select)
                .prop('selectedIndex', 0)
                .prop('disabled', false)
                .trigger('change');
        });
    }

    function buscarDocumentos() {
        const formData = montarCamposEnvio();
        PHPSession.appendFormData(formData);

        HttpClient.post(rotasApi.buscarDocumentos, {body: formData})
            .then(response => {
                if (response.erro) {
                    return alert(response.mensagem);
                }

                tabelaDocumentos.bootstrapTable('load', Object.values(response.data))
            });
    }

    async function verificaIntegracaoAtiva() {
        const formData = new FormData();

        formData.append('documento', cnpj);
        PHPSession.appendFormData(formData);

        let integracaoAtiva = false;

        await HttpClient
            .post(rotasApi.verificaIntegracao, {body: formData})
            .then(response => {
                if (response.error || response.erro) {
                    alert(response.message);
                    return integracaoAtiva;
                }

                if (response.data !== null) {
                    integracaoAtiva = true;
                } else {
                    alert('Por favor, verifique a configuração de integração com o PNCP!');
                }
            });

        return integracaoAtiva;
    }

    PHPSession.loadData().then(async () => {
        rotasApi = getRotasApi(PHPSession.requestApi);

        integracaoAtiva = await verificaIntegracaoAtiva();
        if (!integracaoAtiva) {
            botaoSalvar.prop('disabled', true);
            return;
        }

        buscarDocumentos();
    });

    botaoSalvar.on('click', function () {
        if (!verificaCamposObrigatorios()) {
            return;
        }

        const formData = montarCamposEnvio();

        HttpClient
            .post(rotasApi.incluirDocumento, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                alert('Documento incluído com sucesso!');

                limparFormulario();
                buscarDocumentos();
            })
            .catch(e => {
                return alert(e.message);
            });
    });

    window.operateEvents = {
        'click #excluir': (e, d, data) => {
            const exclusaoConfirmada = confirm('Confirma a exclusão do Documento?');

            if (!exclusaoConfirmada) {
                return;
            }

            const formData = montarCamposEnvio();
            formData.append('sequencialDocumento', data.sequencialDocumento)

            PHPSession.appendFormData(formData);
            HttpClient.post(rotasApi.excluirDocumento, {body: formData})
                .then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    alert('Documento excluído com sucesso!');

                    limparFormulario();
                    buscarDocumentos();
                })
                .catch(response => alert(response.message));
        }
    }

    const colunas = [
        {
            field: 'titulo',
            title: 'Título',
            halign: 'center',
        },
        {
            field: 'tipoDocumentoNome',
            title: 'Tipo Documento',
            halign: 'center',
        },
        {
            field: 'dataPublicacaoPncp',
            title: 'Data Publicação',
            halign: 'center',
            formatter: (value) => {
                const dataPublicacao = new Date(value);
                return dataPublicacao.toLocaleDateString();
            }
        },
        {
            field: 'acao',
            title: 'Ações',
            halign: 'center',
            width: '80px',
            formatter: (value, row) => {
                let acoes = '';
                acoes = `<a id="download" title="Baixar" style="margin-right:5px;" target="blank" href="${row.url}">`;
                acoes += '<i class="fas fa-download"></i></a>';
                acoes += `<a id="excluir" title="Excluir">`;
                acoes += '<i class="fas fa-trash-alt"></i></a>';

                return acoes;
            },
            events: window.operateEvents
        }
    ]

    tabelaDocumentos.bootstrapTable({
        locale: 'pt-BR',
        height: 400,
        search: false,
        class: "table table-sm",
        columns: colunas,
        showButtonText: true,
        cache: false,
        useRowAttrFunc: true,
        reorderableRows: true,
    });
</script>
<script type="text/javascript">
    (function () {
        let query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('#fechar');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
</body>
</html>
