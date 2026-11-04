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
require_once modification('dbforms/db_funcoes.php');
require_once(modification("libs/db_utils.php"));

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
    <style>
        select:disabled, input:disabled {
            color: dimgrey;
        }
    </style>
    <style>
        input:disabled {
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <fieldset>
        <legend><?= empty($getRequest->acordo) ? 'Documento' : "Contrato - $getRequest->acordo"; ?></legend>

        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">

        <form enctype="multipart/form-data" id="form">
            <table class="form-container">
                <tr>
                    <?php if (empty($getRequest->acordo)) : ?>
                        <td>
                            <label for="contrato" id="ancoraContrato">Contrato:</label>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if (!empty($getRequest->acordo)) : ?>
                            <input type="hidden" name="acordo" id="acordo" value="<?= $getRequest->acordo; ?>">
                            <input type="hidden" name="pn04_numero" id="pn04_numero" class="field-size4">
                            <input type="hidden" name="pn04_ano" id="pn04_ano" class="field-size3">
                        <?php else : ?>
                            <input contrato type="text" name="pn04_numero" id="pn04_numero" class="field-size4">
                            <input contrato type="text" name="pn04_ano" id="pn04_ano" class="field-size3" disabled>

                            <a style="color:inherit" id="linkContrato" target="_blank">
                                <button class="field-size2" id="btnConsultarContrato" type="button">
                                    <i class="fas fa-search "></i>
                                    PNCP
                                </button>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="titulo-documento">Título do Documento:</label>
                    </td>
                    <td>
                        <?php if (!empty($getRequest->acordo)) : ?>
                            <input contrato type="text" id="tituloDocumento" class="field-size7" maxlength="50">
                            <a style="color:inherit" id="linkContrato" target="_blank">
                                <button class="field-size2" id="btnConsultarContrato" type="button">
                                    <i class="fas fa-search "></i>
                                    PNCP
                                </button>
                            </a>
                        <?php else : ?>
                            <input contrato type="text" id="tituloDocumento" class="field-size9" maxlength="50">
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="tipo-documento">Tipo:</label>
                    </td>
                    <td>
                        <select
                            contrato
                            id="tipoDocumentoId"
                            name="tipo-documento"
                            rel="ignore-css"
                            class="field-size9"
                        >
                            <option value="0" disabled selected>Selecione</option>
                            <option value="12">Contrato</option>
                            <option value="17">Nota de empenho</option>
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
</div>
</body>

<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<script>
    $.noConflict();

    const botaoSalvar = jQuery('#btnSalvar');
    const ancoraConsultarContrato = jQuery('#linkContrato');
    const acordo = jQuery('#acordo');
    const cnpj = jQuery('#cnpj').val();
    let rotasApi;

    function getRotasApi(baseUrl) {
        return {
            verificaIntegracao:
                baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
            incluirDocumento:
                baseUrl + '/patrimonial/pncp/contratos/incluirDocumento',
            incluirContrato:
                baseUrl + '/patrimonial/pncp/contratos/incluirContrato',
            buscarContrato:
                baseUrl + '/patrimonial/contratos/consulta/acordos',
        }
    }

    function limparFormulario() {
        const codigoAcordo = acordo.val();

        if (!codigoAcordo) {
            ancoraConsultarContrato.removeAttr('href');
        }

        jQuery('input[contrato],textarea[contrato]').val('');

        const contratoSelect = jQuery('select[contrato]');
        contratoSelect.each((index, select) => {
            jQuery(select)
                .prop('selectedIndex', 0)
                .prop('disabled', false)
                .trigger('change');
        });
    }

    function desabilitarBotoes() {
        botaoSalvar.attr('disabled', true);
        jQuery('#btnBuscar').attr('disabled', true);
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

        if (inputEstaVazio('pn04_numero')) {
            alert('Selecione o contrato.');
            return false;
        }

        if (inputEstaVazio('tituloDocumento')) {
            alert('Preencha o título do documento.');
            return false;
        }

        if (inputEstaVazio('tipoDocumentoId')) {
            alert('Selecione o tipo de documento.');
            return false;
        }

        if (inputEstaVazio('documento')) {
            alert('Selecione o documento.');
            return false;
        }

        return true;
    }

    async function verificaIntegracaoAtiva() {
        const formData = new FormData();

        formData.append('documento', cnpj);
        PHPSession.appendFormData(formData);

        let integracaoAtiva = false;

        await HttpClient
            .post(rotasApi.verificaIntegracao, {body: formData})
            .then(response => {
                if (response.erro || response.error) {
                    return alert(response.message);
                }

                if (response.data !== null) {
                    integracaoAtiva = true;
                } else {
                    return alert('Por favor, verifique a configuração de integração com o PNCP!');
                }
            });

        return integracaoAtiva;
    }

    PHPSession.loadData().then(async () => {
        rotasApi = getRotasApi(PHPSession.requestApi);

        const integracaoAtiva = await verificaIntegracaoAtiva();
        if (!integracaoAtiva) {
            botaoSalvar.prop('disabled', true);
        }

        acordo.trigger('change');
    });

    acordo.on('change', () => {
        const formData = new FormData();
        let codigoLicitacao = jQuery('#acordo').val();

        formData.append('ac16_sequencial', codigoLicitacao);
        PHPSession.appendFormData(formData);

        HttpClient
            .post(rotasApi.buscarContrato, {body: formData})
            .then(response => {
                if (response.data.length === 0) {
                    return alert('Acordo não foi encontrado!');
                }

                const acordo = response.data[0];
                const contrato = acordo.contrato_pncp;

                if (!acordo.contrato_pncp) {
                    botaoSalvar.prop('disabled', true);
                    return alert('Acordo não possui contrato no PNCP!')
                }

                jQuery('#pn04_numero').val(contrato.pn04_numero);
                jQuery('#pn04_ano').val(contrato.pn04_ano);

                let linkContrato = 'https://pncp.gov.br/app/contratos/'
                linkContrato += `${cnpj}/${contrato.pn04_ano}/${contrato.pn04_numero}`

                jQuery('#linkContrato').attr('href', linkContrato);
            })
            .catch(e => {
                return alert(e.message);
            });
    })

    botaoSalvar.on('click', () => {
        if (!verificaCamposObrigatorios()) {
            return;
        }

        const documento = document.getElementById('documento').files[0];

        const formData = new FormData();
        formData.append('cnpj', cnpj);
        formData.append('ano', jQuery('#pn04_ano').val());
        formData.append('sequencial', jQuery('#pn04_numero').val());
        formData.append('tituloDocumento', jQuery('#tituloDocumento').val());
        formData.append('tipoDocumentoId', jQuery('#tipoDocumentoId').val());
        formData.append('documento', documento);

        HttpClient
            .post(rotasApi.incluirDocumento, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                limparFormulario();
                alert('Documento anexado com sucesso ao contrato!');
            })
            .catch(response => alert(response.message));
    });

    ancoraConsultarContrato.on('click', () => {
        const link = ancoraConsultarContrato.attr('href')

        if (link === undefined) {
            alert('Selecione o contrato para consultá-lo no PNCP.');
        }
    })
</script>
<script>
    PHPSession.loadData().then(async () => {
        const ancoraContrato = document.getElementById('ancoraContrato');
        const inputNumeroContrato = document.getElementById('pn04_numero');
        const inputAnoContrato = document.getElementById('pn04_ano');

        if (ancoraContrato === null) {
            return;
        }

        const contratoLookup = new DBLookUp(ancoraContrato, inputAnoContrato, inputNumeroContrato, {
            'arquivo': 'func_acordoinstitpncp.php',
            'label': 'Pesquisa de contratos',
            'parametrosAdicionais': ['contratosPncp=1'],
            'objetoLookUp': 'db_iframe_contratospncp',
            'camposAdicionais': ['pn04_ano'],
        });

        contratoLookup.setCallBack('onClick', retorno => {
            limparFormulario();

            const numero = retorno[1];
            const ano = retorno[2];

            inputAnoContrato.value = ano;
            inputAnoContrato.disabled = true;

            inputNumeroContrato.value = numero;
            inputNumeroContrato.disabled = true;

            const linkContrato = `https://pncp.gov.br/app/contratos/${cnpj}/${ano}/${numero}`
            jQuery('#linkContrato').attr('href', linkContrato);
        });
    });
</script>
