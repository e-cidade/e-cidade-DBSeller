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
        <legend>Termo de Contrato</legend>

        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
        <input contrato type="hidden" id="sequencialTermoContrato">
        <input contrato type="hidden" id="ac16_sequencial">

        <table class="form-container">
            <tr>
                <td>
                    <label for="orgao">Órgão:</label>
                </td>
                <td>
                    <input type="text" id="orgao" class="field-size9" disabled>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="contrato" id="ancoraContrato">Contrato:</label>
                </td>
                <td>
                    <input type="text" name="pn04_numero" id="pn04_numero" class="field-size3" disabled>
                    <input type="text" name="pn04_ano" id="pn04_ano" class="field-size2" disabled>
                    <button class="field-size2" id="btnConsultarContrato">
                        <i class="fas fa-search"></i>
                        Consultar
                    </button>
                    <a style="color:inherit" id="linkContrato" target="_blank">
                        <button class="field-size2" id="btnConsultarContratoPncp" type="button">
                            <i class="fas fa-search "></i>
                            PNCP
                        </button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tipoTermoContratoId">Tipo Termo de Contrato:</label>
                </td>
                <td>
                    <select contrato type="text" name="tipo_termo" id="tipoTermoContratoId" class="field-size9">
                        <option value="0" selected disabled>Selecione</option>
                        <option value="1">Termo de Rescisão</option>
                        <option value="2">Termo Aditivo</option>
                        <option value="3">Termo de Apostilamento</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="numeroTermoContrato">Nº Termo de Contrato:</label>
                </td>
                <td>
                    <input
                        contrato
                        maxlength="50"
                        type="text"
                        name="numeroTermoContrato"
                        id="numeroTermoContrato"
                        class="field-size9"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="objetoTermoContrato">Objeto do Termo de Contrato:</label>
                </td>
                <td>
                    <input
                        contrato
                        maxlength="5120"
                        type="text"
                        name="objetoTermoContrato"
                        id="objetoTermoContrato"
                        class="field-size9"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="dataAssinatura">Data Assinatura Termo:</label>
                </td>
                <td>
                    <input contrato name="dataAssinatura" id="dataAssinatura" class="field-size2">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="qualificacaoAcrescimoSupressao">Qualificação Acrescimo/Supressão:</label>
                </td>
                <td>
                    <select
                        contrato
                        name="qualificacaoAcrescimoSupressao"
                        id="qualificacaoAcrescimoSupressao"
                        class="field-size9"
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="qualificacaoVigencia">Qualificação Vigência:</label>
                </td>
                <td>
                    <select
                        contrato
                        name="qualificacaoVigencia"
                        id="qualificacaoVigencia"
                        class="field-size9"
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="qualificacaoFornecedor">Qualificação Fornecedor:</label>
                </td>
                <td>
                    <select
                        contrato
                        name="qualificacaoFornecedor"
                        id="qualificacaoFornecedor"
                        class="field-size9"
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="qualificacaoReajuste">Qualificação Reajuste:</label>
                </td>
                <td>
                    <select
                        contrato
                        name="qualificacaoReajuste"
                        id="qualificacaoReajuste"
                        class="field-size9"
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="qualificacaoInformativo">Qualificação Informativo:</label>
                </td>
                <td>
                    <select
                        contrato
                        name="qualificacaoInformativo"
                        id="qualificacaoInformativo"
                        class="field-size9"
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr id="rowInformativoObservacao" style="display: none;">
                <td>
                    <label for="informativoObservacao">Observação Informativo:</label>
                </td>
                <td>
                    <textarea
                        contrato
                        name="informativoObservacao"
                        id="informativoObservacao"
                        rows="2"
                        cols="54"
                        maxlength="5120"
                        rel="ignore-css"
                    ></textarea>
                </td>
            </tr>
            <tr id="rowExibeJustificativa" style="display: none">
                <td>
                    <label for="exibeJustificativa">Justificar:</label>
                </td>
                <td>
                    <select contrato name="exibeJustificativa" id="exibeJustificativa">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr id="rowJustificativa" style="display: none">
                <td>
                    <label for="justificativa">Justificativa:</label>
                </td>
                <td>
                    <textarea
                        contrato
                        name="justificativa"
                        id="justificativa"
                        rows="2"
                        cols="54"
                        maxlength="255"
                        rel="ignore-css"
                    ></textarea>
                </td>
            </tr>
        </table>
    </fieldset>

    <button type="button" id="btnSalvarDados">
        <i class="fas fa-save"></i>
        Enviar Dados
    </button>

    <button type="button" id="btnEditarDados" style="display:none;">
        <i class="fas fa-edit"></i>
        Salvar Alterações
    </button>

    <button type="button" id="btnLimpar" style="display:none;">
        <i class="fas fa-eraser"></i>
        Limpar
    </button>
</div>

<div class="subcontainer" style="max-width: 70%;width:70%">
    <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">

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

    let ancoraContrato = document.getElementById('ancoraContrato');
    let integracaoAtiva = false;
    let rotasApi;

    const inputDataAssinatura = new DBInputDate(document.getElementById("dataAssinatura"));
    const ancoraConsultarContratoPncp = jQuery('#btnConsultarContratoPncp');
    const inputSequencialContrato = jQuery('#ac16_sequencial');
    const ancoraConsultarContrato = jQuery('#btnConsultarContrato');
    const inputExibeJustificativa = jQuery('#exibeJustificativa');
    const inputQualificacaoInformativo = jQuery('#qualificacaoInformativo');
    const rowInformativoObservacao = jQuery('#rowInformativoObservacao');
    const inputInformativoObservacao = jQuery('#informativoObservacao');
    const inputContratoId = document.getElementById('pn04_numero');
    const inputContratoAno = document.getElementById('pn04_ano');
    const rowExibeJustificativa = jQuery('#rowExibeJustificativa')
    const rowJustificativa = jQuery('#rowJustificativa')
    const inputNumeroContrato = jQuery('#pn04_numero');
    const justificativa = jQuery('#justificativa');
    const botaoLimpar = jQuery('#btnLimpar');
    const botaoSalvar = jQuery('#btnSalvarDados');
    const botaoEditar = jQuery('#btnEditarDados');
    const inputAnoContrato = jQuery('#pn04_ano');
    const tabelaTermos = jQuery('#data-table');
    const cnpj = jQuery('#cnpj').val();

    function trocarMetodo(modo) {
        limparFormulario();

        if (modo === 'editar') {
            botaoSalvar.hide();
            botaoEditar.show();
            botaoLimpar.show();
            rowExibeJustificativa.show().trigger('change');
        } else {
            botaoSalvar.show();
            botaoEditar.hide();
            botaoLimpar.hide();
            rowExibeJustificativa.hide().trigger('change');
        }
    }

    function getRotasApi(baseUrl) {
        return {
            buscarEntidade:
                baseUrl + '/patrimonial/pncp/unidades/buscarEntidade/',
            verificaIntegracao:
                baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
            incluirTermo:
                baseUrl + '/patrimonial/pncp/termos/incluir',
            buscarTermos:
                baseUrl + '/patrimonial/pncp/termos/buscar',
            excluirTermo:
                baseUrl + '/patrimonial/pncp/termos/excluir',
            retificarTermo:
                baseUrl + '/patrimonial/pncp/termos/retificar',
        }
    }

    function limparFormulario() {
        ancoraConsultarContratoPncp.removeAttr('href');

        jQuery('input[contrato],textarea[contrato]').val('');

        const contratoSelect = jQuery('select[contrato]');
        contratoSelect.each((index, select) => {
            jQuery(select)
                .prop('selectedIndex', 0)
                .prop('disabled', false)
                .trigger('change');
        });
    }

    function buscarEntidade() {
        const formData = new FormData();
        const orgao = jQuery('#orgao');

        formData.append('documento', cnpj);
        PHPSession.appendFormData(formData);

        HttpClient
            .post(rotasApi.buscarEntidade, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                orgao.val(response.data.razaoSocial);
            });
    }

    function inputEstaVazio(id) {
        let valorInput = jQuery(`#${id}`).val().trim();

        return (
            valorInput === '' || valorInput === undefined ||
            valorInput === false || valorInput === 0 ||
            valorInput === null
        );
    }

    function montarCamposEnvio() {
        const formData = new FormData;

        formData.append('cnpj', cnpj);
        formData.append('anoContrato', inputAnoContrato.val());
        formData.append('numeroContrato', inputNumeroContrato.val());
        formData.append('tipoTermoContratoId', jQuery('#tipoTermoContratoId').val());
        formData.append('numeroTermoContrato', jQuery('#numeroTermoContrato').val());
        formData.append('objetoTermoContrato', jQuery('#objetoTermoContrato').val());
        formData.append('qualificacaoAcrescimoSupressao', jQuery('#qualificacaoAcrescimoSupressao').val());
        formData.append('qualificacaoVigencia', jQuery('#qualificacaoVigencia').val());
        formData.append('qualificacaoFornecedor', jQuery('#qualificacaoFornecedor').val());
        formData.append('qualificacaoInformativo', inputQualificacaoInformativo.val());
        formData.append('qualificacaoReajuste', jQuery('#qualificacaoReajuste').val());
        formData.append('dataAssinatura', js_formatar(inputDataAssinatura.__toLocaleDateString(), 'd'));
        formData.append('informativoObservacao', inputInformativoObservacao.val());

        if (justificativa.val() !== '' && justificativa.val() !== undefined) {
            formData.append('justificativa', justificativa.val());
        }

        return formData;
    }

    function verificaCamposObrigatorios() {

        if (inputEstaVazio('pn04_numero') || inputEstaVazio('pn04_ano')) {
            alert('Selecione o contrato.');
            return false;
        }

        if (inputEstaVazio('tipoTermoContratoId')) {
            alert('Preencha o tipo termo de contrato.');
            return false;
        }

        if (inputEstaVazio('numeroTermoContrato')) {
            alert('Preencha o número do termo de contrato.');
            return false;
        }

        if (inputEstaVazio('objetoTermoContrato')) {
            alert('Preencha o objeto do termo de contrato.');
            return false;
        }

        if (inputExibeJustificativa.val() === '1') {
            if (inputEstaVazio('justificativa')) {
                alert('Preencha a justificativa da retificação.');
                return false;
            }
        }

        if (inputQualificacaoInformativo.val() === '1') {
            if (inputEstaVazio('informativoObservacao')) {
                alert('Preencha a observação do informativo.');
                return false;
            }
        }

        if (inputDataAssinatura.value === null) {
            alert('Preencha a data de assinatura.');
            return false;
        }

        if (inputDataAssinatura.value) {
            const dataInicioPncp = new Date('2021-04-01');

            if (inputDataAssinatura.value < dataInicioPncp) {
                alert('Data da assinatura para o termo é anterior ao da Lei nº 14.133 (01/04/2021).');
                return false;
            }
        }

        return true;
    }

    function buscarTermos() {
        const formData = new FormData;

        formData.append('cnpj', cnpj)
        formData.append('numeroContrato', inputNumeroContrato.val());
        formData.append('anoContrato', inputAnoContrato.val());

        PHPSession.appendFormData(formData);
        HttpClient.post(rotasApi.buscarTermos, {body: formData})
            .then(response => {
                if (response.erro) {
                    return alert(response.mensagem);
                }

                tabelaTermos.bootstrapTable('load', Object.values(response.data))
            })
            .catch(e => alert(e.message));
    }

    function formataQualificacao(value) {
        if (value === true) {
            return 'Sim';
        }
        return 'Não';
    }

    function preencheCamposTermo(data) {

        const tiposTermoContrato = jQuery(`#tipoTermoContratoId > option`);
        const tipoTermo = tiposTermoContrato.filter(function () {
            return this.text.toLowerCase().trim() === data.tipoTermoContratoNome.toLowerCase().trim();
        });
        const informativoObservacao = data.informativoObservacao;

        if (tipoTermo.length === 1) {
            tiposTermoContrato.prop('selected', false);
            tipoTermo.prop('selected', true);
        }

        jQuery('#qualificacaoAcrescimoSupressao').val(data.qualificacaoAcrescimoSupressao | 0);
        jQuery('#qualificacaoFornecedor').val(data.qualificacaoFornecedor | 0);
        jQuery('#qualificacaoReajuste').val(data.qualificacaoReajuste | 0);
        jQuery('#qualificacaoVigencia').val(data.qualificacaoVigencia | 0);
        jQuery('#numeroTermoContrato').val(data.numeroTermoContrato);
        jQuery('#objetoTermoContrato').val(data.objetoTermoContrato);

        if (informativoObservacao) {
            inputQualificacaoInformativo.val(1).trigger('change');
            inputInformativoObservacao.val(informativoObservacao);
        }

        jQuery('#dataAssinatura').val(data.dataAssinatura.split('-').reverse().join('/'));
    }

    async function buscaDadosFormulario() {
        buscarEntidade();
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

        await buscaDadosFormulario();
    });

    botaoSalvar.on('click', function () {
        if (!verificaCamposObrigatorios()) {
            return;
        }

        const formData = montarCamposEnvio();

        HttpClient
            .post(rotasApi.incluirTermo, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                alert('Termo incluído com sucesso!');

                limparFormulario();
                buscarTermos();
            })
            .catch(e => {
                return alert(e.message);
            });
    });

    botaoEditar.on('click', function () {
        if (!verificaCamposObrigatorios()) {
            return;
        }

        const formData = montarCamposEnvio();
        formData.append('sequencialTermoContrato', jQuery('#sequencialTermoContrato').val());

        PHPSession.appendFormData(formData);
        HttpClient.post(rotasApi.retificarTermo, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                alert('Termo retificado com sucesso!');

                trocarMetodo();
                buscarTermos();
                tabelaTermos.bootstrapTable('refresh');
            })
            .catch(response => alert(response.message));
    });

    botaoLimpar.on('click', function () {
        trocarMetodo();
    })

    ancoraConsultarContratoPncp.parent().on('click', function () {
        if (this.href === '' || this.href === undefined) {
            return alert('Selecione o contrato para consultá-lo no PNCP.');
        }
    });

    ancoraConsultarContrato.on('click', function () {
        if (inputEstaVazio('ac16_sequencial')) {
            return alert('Selecione o contrato para consultá-lo.');
        } else {
            const contratoId = inputSequencialContrato.val();

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe',
                'con4_consacordos003.php?ac16_sequencial=' + contratoId,
                'Pesquisa',
                true
            );
        }
    });

    inputExibeJustificativa.on('change', function () {
        if (this.value === '0') {
            rowJustificativa.hide();
            justificativa.val('');
        } else {
            rowJustificativa.show();
        }
    });

    inputQualificacaoInformativo.on('change', function () {
        if (this.value === '0') {
            rowInformativoObservacao.hide();
            inputInformativoObservacao.val('');
        } else {
            rowInformativoObservacao.show();
        }
    });

    window.operateEvents = {
        'click #excluir': (e, d, data) => {
            const exclusaoConfirmada = confirm('Confirma a exclusão do Termo?');

            if (!exclusaoConfirmada) {
                return;
            }

            const formData = montarCamposEnvio();
            formData.append('sequencialTermoContrato', data.sequencialTermoContrato);

            PHPSession.appendFormData(formData);
            HttpClient.post(rotasApi.excluirTermo, {body: formData})
                .then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    alert('Termo excluído com sucesso!');

                    buscarTermos();
                    limparFormulario();
                    trocarMetodo();
                    tabelaTermos.bootstrapTable('refresh');
                })
                .catch(response => alert(response.message));
        },
        'click #editar': (e, d, data) => {
            trocarMetodo('editar');
            preencheCamposTermo(data);

            jQuery('#sequencialTermoContrato').val(data.sequencialTermoContrato);
        },
        'click #documento': (e, d, data) => {
            let anoContrato = inputAnoContrato.val();
            let numeroContrato = inputNumeroContrato.val();
            let sequencialTermo = data.sequencialTermoContrato;

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe',
                'pncp1_documentotermocontrato001.php?'
                + `termo=${sequencialTermo}&numeroContrato=${numeroContrato}&anoContrato=${anoContrato}&`
                + `numeroTermo=${btoa(data.numeroTermoContrato)}&tipoTermo=${data.tipoTermoContratoNome}`,
                'Pesquisa',
                true
            );
        }
    };

    const colunas = [
        {
            field: 'numeroTermoContrato',
            title: 'Número',
            halign: 'center',
            formatter: (value) => {
                if (value.length > 50) {
                    return value.substring(0, 50) + '...';
                }

                return value;
            }
        },
        {
            field: 'tipoTermoContratoNome',
            title: 'Tipo',
            halign: 'center',
        },
        {
            field: 'objetoTermoContrato',
            title: 'Objeto',
            halign: 'center',
            formatter: (value) => {
                if (value.length > 80) {
                    return value.substring(0, 50) + '...';
                }

                return value;
            }
        },
        {
            field: 'dataAssinatura',
            title: 'Assinatura',
            halign: 'center',
            formatter: (value) => {
                return value.split('-').reverse().join('/');
            }
        },
        {
            field: 'qualificacaoAcrescimoSupressao',
            title: 'Acréscimo/Supressão',
            halign: 'center',
            formatter: (value) => {
                return formataQualificacao(value);
            }
        },
        {
            field: 'qualificacaoVigencia',
            title: 'Vigência',
            halign: 'center',
            formatter: (value) => {
                return formataQualificacao(value);
            }
        },
        {
            field: 'qualificacaoFornecedor',
            title: 'Fornecedor',
            halign: 'center',
            formatter: (value) => {
                return formataQualificacao(value);
            }
        },
        {
            field: 'qualificacaoReajuste',
            title: 'Reajuste',
            halign: 'center',
            formatter: (value) => {
                return formataQualificacao(value);
            }
        },
        {
            field: 'informativoObservacao',
            title: 'Informativo',
            halign: 'center',
            formatter: (value) => {
                if (value && value.length > 50) {
                    return value.substring(0, 50) + '...';
                }

                return value;
            }

        },
        {
            field: 'acao',
            title: 'Ações',
            halign: 'center',
            width: '80px',
            formatter: () => {
                let acoes = '';
                acoes += '<div style="display: flex;justify-content: center;">';
                acoes += '<a id="documento" title="Documento" style="margin-right:5px;">';
                acoes += '<i class="fas fa-file-alt"></i></a>';
                acoes += '<a id="editar" title="Editar" style="margin-right:5px;">';
                acoes += '<i class="fas fa-edit"></i></a>';
                acoes += '<a id="excluir" title="Excluir">'
                acoes += '<i class="fas fa-trash-alt"></i></a></div>';
                return acoes;
            },
            events: window.operateEvents
        }
    ]

    tabelaTermos.bootstrapTable({
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

    const contratoLookup = new DBLookUp(ancoraContrato, inputContratoId, inputContratoId, {
        'arquivo': 'func_acordoinstitpncp.php',
        'label': 'Pesquisa de contratos',
        'objetoLookUp': 'db_iframe_acordoinstitpncp',
        'parametrosAdicionais': ['contratosPncp=1'],
        'camposAdicionais': ['pn04_ano', 'ac16_sequencial'],
    });
    contratoLookup.setCallBack('onClick', retorno => {
        limparFormulario();

        const numero = retorno[1];
        const ano = retorno[2];
        const sequencialContrato = retorno[3];

        inputContratoAno.value = ano;
        inputContratoAno.disabled = true;

        inputContratoId.value = numero;
        inputContratoId.disabled = true;

        inputSequencialContrato.val(sequencialContrato);

        if (integracaoAtiva) {
            buscarTermos();
        }

        const linkContrato = `https://pncp.gov.br/app/contratos/${cnpj}/${ano}/${numero}`
        jQuery('#linkContrato').attr('href', linkContrato);
    });
</script>
</body>
</html>
