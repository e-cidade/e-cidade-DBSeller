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
        <legend>Contrato</legend>

        <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
        <input type="hidden" id="valorGlobal" name="valor-global">
        <input type="hidden" id="dadosBuscados" value="0">

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
                    <label for="unidade-compradora">Unidade Compradora: </label>
                </td>
                <td>
                    <select
                        name="unidade-compradora"
                        class="field-size9"
                        rel="ignore-css"
                        id="codigoUnidade"
                    >
                        <option value="" disabled>Selecione</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="compra" id="ancoraCompra">
                        Contratação/Edital/Aviso:
                    </label>
                </td>
                <td>
                    <input type="text" name="numero-compra" id="numeroCompra" class="field-size4" disabled>
                    <input type="text" name="ano-compra" id="anoCompra" class="field-size3" disabled>

                    <a style="color:inherit" id="linkCompra" target="_blank">
                        <button class="field-size2" id="btnConsultarCompra">
                            <i class="fas fa-search "></i>
                            PNCP
                        </button>
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="contrato" id="ancoraContrato">Contrato:</label>
                </td>
                <td>
                    <input type="text" name="ac16_sequencial" id="ac16_sequencial" class="field-size4" disabled>
                    <button class="field-size3" id="btnBuscarContrato">
                        <i class="fas fa-search"></i>
                        Buscar Dados
                    </button>
                    <button class="field-size2" id="btnConsultarContrato">
                        <i class="fas fa-search"></i>
                        Consultar
                    </button>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="sequencial-tipo-contrato">Tipo de Contrato: </label>
                </td>
                <td>
                    <select contrato id="tipoContratoId" class="field-size9" rel="ignore-css">
                        <option disabled selected>Selecione</option>
                        <option value="1">Contrato (termo inicial)</option>
                        <option value="2">Comodato</option>
                        <option value="3">Arrendamento</option>
                        <option value="4">Concessão</option>
                        <option value="5">Termo de Adesão</option>
                        <option value="7">Empenho</option>
                        <option value="8">Outros</option>
                        <option value="12">Carta de Contrato</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="numeroContratoEmpenho">Número do Contrato/Empenho:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        name="empenho-contrato"
                        id="numeroContratoEmpenho"
                        class="field-size9"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="ac16_anousu">Ano do Contrato:</label>
                </td>
                <td>
                    <input contrato type="text" name="ac16_anousu" id="anoContrato" class="field-size9">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="codigo-fornecedor">Fornecedor:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        name="codigo-fornecedor"
                        id="codigoFornecedor"
                        class="field-size2"
                    >
                    <input
                        contrato
                        type="text"
                        name="nome-fornecedor"
                        id="nomeRazaoSocialFornecedor"
                        class="field-size7"
                    >
                    <input contrato type="hidden" name="cpf-cnpj-fornecedor" id="niFornecedor">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="subcontratado">Fornecedor Subcontratado: </label>
                </td>
                <td>
                    <select contrato name="subcontratado" id="subcontratado">
                        <option value="0" selected>Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr style="display: none" name="informacoes-subcontratado">
                <td>
                    <label for="numero-subcontratado">
                        <a id="ancoraSubcontratado"> Subcontratado:</a>
                    </label>
                </td>
                <td>
                    <input contrato type="text" name="z01_numero" class="field-size2" id="z01_numcgm">
                    <input contrato type="text" name="z01_nome" class="field-size7" id="z01_nome">
                    <input contrato type="hidden" name="z01_cgccpf" class="field-size2" id="niFornecedorSubContratado">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="objeto">Objeto do Contrato:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        name="objeto"
                        style="text-overflow:ellipsis"
                        id="objetoContrato"
                        class="field-size9"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="processo-categoria">Categoria do Processo: </label>
                </td>
                <td>
                    <select
                        contrato
                        name="processo-categoria"
                        id="categoriaProcessoId"
                        class="field-size9"
                        rel="ignore-css"
                    >
                        <option value="0" selected disabled>Selecione</option>
                        <option value="1">Cessão</option>
                        <option value="2">Compras</option>
                        <option value="3">Informática (TIC)</option>
                        <option value="4">Internacional</option>
                        <option value="5">Locação Imóveis</option>
                        <option value="6">Mão de Obra</option>
                        <option value="7">Obras</option>
                        <option value="8">Serviços</option>
                        <option value="9">Serviços de Engenharia</option>
                        <option value="10">Serviços de Saúde</option>
                        <option value="11">Alienação de bens móveis/imóveis</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="processo">Processo:</label>
                </td>
                <td>
                    <input contrato type="text" name="processo" id="processo" class="field-size9">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="receita">Receita: </label>
                </td>
                <td>
                    <select contrato name="receita" id="receita" class="field-size9" rel="ignore-css">
                        <option selected disabled>Selecione</option>
                        <option value="1">Receita</option>
                        <option value="0">Despesa</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="valor-inicial">Valor Inicial:</label>
                </td>
                <td>
                    <input contrato type="text" name="valor-inicial" id="valorInicial" class="field-size9">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="quantidade-parcelas">Quantidade de Parcelas:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        min="1"
                        name="quantidade-parcelas"
                        id="numeroParcelas"
                        class="field-size9"
                    >
                </td>
            </tr>
            <tr>
                <td>
                    <label for="informacoes-complementares">Informação Complementar: </label>
                </td>
                <td>
                    <select
                        contrato
                        name="informacoes-complementares"
                        id="informacoesComplementares"
                        class="field-size9"
                        rel="ignore-css"
                    >
                        <option value="0" selected>Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr style="display: none" name="informacao-complementar">
                <td>
                    <label for="informacao-complementar">Informação Complementar:</label>
                </td>
                <td>
                    <textarea
                        contrato
                        name="informacao-complementar"
                        id="informacaoComplementar"
                        style="resize:none"
                    ></textarea>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="data-assinatura">Data da Assinatura: </label>
                </td>
                <td>
                    <input contrato id="dataAssinatura" name="data-assinatura">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="data-vigencia-inicio">Período de Vigência de: </label>
                </td>
                <td>
                    <input contrato id="dataVigenciaInicio" name="data-vigencia-inicio">
                    <label for="data-vigencia-inicio">Até: </label>
                    <input contrato id="dataVigenciaFim" name="data-vigencia-fim">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="cipi">CIPI:</label>
                </td>
                <td>
                    <select contrato name="cipi" id="cipi">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                </td>
            </tr>
            <tr style="display: none" name="informacoes-cipi">
                <td>
                    <label for="cnpj-compra">Identificador:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        name="identificador-cipi"
                        id="identificadorCipi"
                        class="field-size9"
                        maxlength="14"
                    >
                </td>
            </tr>
            <tr style="display: none" name="informacoes-cipi">
                <td>
                    <label for="cnpj-compra">URL:</label>
                </td>
                <td>
                    <input
                        contrato
                        type="text"
                        name="url-cipi"
                        id="urlCipi"
                        class="field-size9"
                        maxlength="512"
                    >
                </td>
            </tr>
        </table>
    </fieldset>
    <button class="btn" id="btnSalvar">
        <i class="fas fa-save"></i>
        Enviar Dados
    </button>
    <button class="btn" id="btnAnexarDocumento" disabled>
        <i class="fa fa-link" aria-hidden="true"></i>
        Anexar Documento
    </button>
</div>

<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>

<script>
    $.noConflict();

    function mostraCampos(selectId, identificadorTr, options = {}) {
        let selectMostraCampo = jQuery(`#${selectId}`);

        if (options.selectByName) {
            identificadorTr = `[name=${identificadorTr}]`;
        } else {
            identificadorTr = `#${identificadorTr}`;
        }

        selectMostraCampo.on('change', e => {
            let campoAtivo = jQuery(e.target).children(':selected').val();

            if (campoAtivo === '1') {
                jQuery(identificadorTr).css('display', 'table-row');
            } else {
                jQuery(identificadorTr).css('display', 'none');
                jQuery(identificadorTr).children().children('input,textarea').val('');
            }
        });
    }

    const inputDataAssinatura = new DBInputDate(document.getElementById("dataAssinatura"));
    const inputDataVigenciaInicio = new DBInputDate(document.getElementById("dataVigenciaInicio"));
    const inputDataVigenciaFim = new DBInputDate(document.getElementById("dataVigenciaFim"));

    mostraCampos('informacoesComplementares', 'informacao-complementar', {selectByName: true});
    mostraCampos('cipi', 'informacoes-cipi', {selectByName: true});
    mostraCampos('subcontratado', 'informacoes-subcontratado', {selectByName: true});
</script>
<script>
    let dadosCompra;

    const ancoraCompra = document.getElementById('ancoraCompra');
    const inputCompraId = document.getElementById('numeroCompra');
    const ancoraConsultarCompra = jQuery('#btnConsultarCompra');
    const botaoAnexarDocumento = jQuery('#btnAnexarDocumento');
    const botaoBuscarContrato = jQuery('#btnBuscarContrato');
    const inputNumeroParcelas = jQuery('#numeroParcelas');
    const ancoraBuscaContrato = jQuery('#ancoraContrato');
    const inputDadosBuscados = jQuery('#dadosBuscados');
    const cnpj = jQuery('#cnpj').val();
    const botaoSalvar = jQuery('#btnSalvar');

    let rotasApi;
    let unidadesCompradoras;

    function getRotasApi(baseUrl) {
        return {
            buscarEntidade:
                baseUrl + '/patrimonial/pncp/unidades/buscarEntidade/',
            buscarUnidadesAtivas:
                baseUrl + '/patrimonial/pncp/unidades/buscarUnidadesAtivas/',
            verificaIntegracao:
                baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
            buscarCompra:
                baseUrl + '/patrimonial/pncp/compraEditalAviso/buscarCompra/',
            buscarContrato:
                baseUrl + '/patrimonial/contratos/consulta/acordos',
            incluirContrato:
                baseUrl + '/patrimonial/pncp/contratos/incluirContrato',
            buscarAtas:
                baseUrl + '/patrimonial/pncp/ataRegistroPreco/buscar',
        }
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

    function desabilitarBotoes() {
        botaoSalvar.attr('disabled', true);
        botaoAnexarDocumento.attr('disabled', true);
        jQuery('#btnBuscar').attr('disabled', true);
    }

    function criarOpcoesUnidadesCompradoras(unidades) {
        let selectUnidadesCompradoras = jQuery('#codigoUnidade');

        unidades.forEach(item => {
            let option = document.createElement('option');
            jQuery(option).text(item.pn02_nome);
            jQuery(option).val(item.pn02_unidade);

            selectUnidadesCompradoras.append(option)
        });

        if (unidades.length === 1) {
            selectUnidadesCompradoras.attr('disabled', true);
            selectUnidadesCompradoras.children('option').eq(1).attr('selected', true);
        } else {
            selectUnidadesCompradoras.children('option').eq(0).attr('selected', true);
        }
    }

    function preencheCamposCompra(dados) {
        if (dados.pn03_ano) {
            jQuery('#anoCompra').val(dados.pn03_ano);
        }

        if (dados.pn03_numero) {
            jQuery('#numeroCompra').val(dados.pn03_numero);
        }
    }

    function preencheCamposContrato(dados) {
        if (dados.ac16_anousu) {
            jQuery('#anoContrato').val(dados.ac16_anousu).attr('disabled', true);
        }

        if (dados.ac16_numero) {
            jQuery('#numeroContratoEmpenho').val(dados.ac16_numero).attr('disabled', true);
        }

        if (dados.ac16_contratado) {
            jQuery('#codigoFornecedor').val(dados.ac16_contratado).attr('disabled', true);
        }

        if (dados.contratado.z01_nome) {
            jQuery('#nomeRazaoSocialFornecedor').val(dados.contratado.z01_nome).attr('disabled', true);
        }

        if (dados.contratado.z01_cgccpf) {
            jQuery('#niFornecedor').val(dados.contratado.z01_cgccpf).attr('disabled', true);
        }

        if (dados.ac16_objeto) {
            jQuery('#objetoContrato').val(dados.ac16_objeto).attr('disabled', true);
        }

        if (dados.ac16_numeroprocesso) {
            jQuery('#processo').val(dados.ac16_numeroprocesso).attr('disabled', true);
        }

        if (dados.ac16_dataassinatura) {
            const dataAssinatura = dados.ac16_dataassinatura.split('-').reverse().join('/');
            jQuery('#dataAssinatura').val(dataAssinatura);

            inputDataAssinatura.setReadOnly(true);
        }

        if (dados.ac16_datainicio) {
            const dataInicio = dados.ac16_datainicio.split('-').reverse().join('/');
            jQuery('#dataVigenciaInicio').val(dataInicio);

            inputDataVigenciaInicio.setReadOnly(true);
        }

        if (dados.ac16_datafim) {
            const dataFim = dados.ac16_datafim.split('-').reverse().join('/');
            jQuery('#dataVigenciaFim').val(dataFim);

            inputDataVigenciaFim.setReadOnly(true);
        }

        if (dados.ac16_valor) {
            jQuery('#valorInicial').val(dados.ac16_valor).attr('disabled', true);
            jQuery('#valorGlobal').val(dados.ac16_valor);
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

    function limparFormulario() {
        inputDadosBuscados.val('0').trigger('change');
        botaoAnexarDocumento.prop('disabled', true);
        jQuery('input[contrato],textarea[contrato]').val('').attr('disabled', false);

        inputDataAssinatura.setReadOnly(false);
        inputDataVigenciaInicio.setReadOnly(false);
        inputDataVigenciaFim.setReadOnly(false);

        const contratoSelect = jQuery('select[contrato]');
        contratoSelect.each((index, select) => {
            jQuery(select)
                .prop('selectedIndex', 0)
                .prop('disabled', false)
                .trigger('change');
        });

        if (dadosCompra) {
            configurarCampos(dadosCompra);
        }
    }

    function verificaCamposObrigatorios() {

        if (inputEstaVazio('ac16_sequencial')) {
            alert('Selecione o contrato.');
            return false;
        }

        if (inputEstaVazio('tipoContratoId')) {
            alert('Selecione o tipo de contrato.');
            return false;
        }

        if (jQuery('#subcontratado').val() === '1') {
            if (inputEstaVazio('z01_numcgm')) {
                alert('Selecione o subcontratado.');
                return false;
            }
        }

        if (inputEstaVazio('categoriaProcessoId')) {
            alert('Selecione a categoria do processo.');
            return false;
        }

        if (inputEstaVazio('receita')) {
            alert('Selecione se este contrato é uma receita.');
            return false;
        }

        if (jQuery('#informacoesComplementares').val() === '1') {
            if (inputEstaVazio('informacaoComplementar')) {
                alert('Preencha a informação complementar.');
                return false;
            }
        }

        if (jQuery('#cipi').val() === '1') {
            if (inputEstaVazio('urlCipi') || inputEstaVazio('identificadorCipi')) {
                alert('Preencha as informações do CIPI.');
                return false;
            }
        }

        if (inputDataAssinatura.value === null) {
            return alert('Preencha a data de assinatura.');
        }

        if (inputDataVigenciaInicio.value === null) {
            return alert('Preencha a data inicial da vigência.');
        }

        if (inputDataVigenciaFim.value === null) {
            return alert('Preencha a data final da vigência.');
        }

        return true;
    }

    function montarCamposEnvio() {
        const formData = new FormData;

        formData.append('acordo', jQuery('#ac16_sequencial').val());
        formData.append('codigoFornecedor', jQuery('#codigoFornecedor').val());
        formData.append('codigoSubContratado', jQuery('#z01_numcgm').val());
        formData.append('cnpjCompra', cnpj);
        formData.append('anoCompra', jQuery('#anoCompra').val());
        formData.append('sequencialCompra', jQuery('#numeroCompra').val());
        formData.append('tipoContratoId', jQuery('#tipoContratoId').val());
        formData.append('numeroContratoEmpenho', jQuery('#numeroContratoEmpenho').val());
        formData.append('anoContrato', jQuery('#anoContrato').val());
        formData.append('processo', jQuery('#processo').val());
        formData.append('categoriaProcessoId', jQuery('#categoriaProcessoId').val());
        formData.append('niFornecedor', jQuery('#niFornecedor').val());
        formData.append('nomeRazaoSocialFornecedor', jQuery('#nomeRazaoSocialFornecedor').val());
        formData.append('receita', jQuery('#receita').val());
        formData.append('codigoUnidade', jQuery('#codigoUnidade').val());
        formData.append('objetoContrato', jQuery('#objetoContrato').val());
        formData.append('numeroParcelas', inputNumeroParcelas.val());
        formData.append('valorGlobal', Number(jQuery('#valorGlobal').val()).toFixed(4));

        formData.append('dataAssinatura', js_formatar(inputDataAssinatura.__toLocaleDateString(), 'd'));
        formData.append('dataVigenciaInicio', js_formatar(inputDataVigenciaInicio.__toLocaleDateString(), 'd'));
        formData.append('dataVigenciaFim', js_formatar(inputDataVigenciaFim.__toLocaleDateString(), 'd'));
        formData.append('valorInicial', Number(jQuery('#valorInicial').val()).toFixed(4));

        if (jQuery('#subcontratado').val() === '1') {
            formData.append('niFornecedorSubContratado', jQuery('#niFornecedorSubContratado').val());
            formData.append('nomeRazaoSocialFornecedorSubContratado', jQuery('#z01_nome').val());
        }

        if (jQuery('#informacoesComplementares').val() === '1') {
            formData.append('informacaoComplementar', jQuery('#informacaoComplementar').val());
        }

        if (jQuery('#cipi').val() === '1') {
            formData.append('urlCipi', jQuery('#urlCipi').val());
            formData.append('identificadorCipi', jQuery('#identificadorCipi').val());
        }

        return formData;
    }

    function alternaCampo(seletor, condicao, options = {}) {
        const campo = jQuery(seletor);
        const hasOptions = Object.keys(options).length

        if (condicao) {
            campo.prop('selectedIndex', 0).prop('disabled', true).trigger('change');

            if (hasOptions) {
                campo.children('option').first().text(options.mensagem);
            }
        } else {
            campo.prop('disabled', false).trigger('change');

            if (hasOptions) {
                campo.children('option').first().text(options.mensagemOriginal);
            }
        }
    }

    function configurarCampos(compra) {
        const modalidadeLeilao = compra.modalidadeId === 1 || compra.modalidadeId === 13;

        const options = {
            mensagem: "Não se aplica",
            mensagemOriginal: "Não"
        }

        alternaCampo('#cipi', modalidadeLeilao, options);
        alternaCampo('#subcontratado', modalidadeLeilao, options);
    }

    async function verificarCompraPossuiAta(compra) {
        const formData = new FormData();
        let possuiAta = false;

        formData.append('cnpj', cnpj);
        formData.append('licitacao', compra.licitacao);

        PHPSession.appendFormData(formData);

        await HttpClient.post(rotasApi.buscarAtas, {body: formData})
            .then(response => {
                const dados = response.data.data;

                if (dados !== undefined && dados.length > 0) {
                    possuiAta = true;
                }
            })
            .catch(e => alert(e.message));

        return possuiAta;
    }

    async function validarCompraMedianteSrp(compra) {
        if (compra.srp === false) return true;

        if (compra.srp === true) {
            const possuiAta = await verificarCompraPossuiAta(compra);

            if (!possuiAta) {
                alert(
                    'Contratação/Edital/Aviso do tipo SRP não possui nenhuma '
                    + 'ata de registro de preços publicada no PNCP.'
                );
            }

            return possuiAta;
        }
    }

    async function buscarCompra(compra) {
        const formData = new FormData();
        let compraValida = false;

        formData.append('cnpj', cnpj);
        formData.append('numero', compra.numero);
        formData.append('ano', compra.ano);

        PHPSession.appendFormData(formData);

        await HttpClient
            .post(rotasApi.buscarCompra, {body: formData})
            .then(response => {
                if (response.error || response.erro) {
                    return alert(response.message);
                }

                dadosCompra = response.data;
                dadosCompra.licitacao = compra.licitacao;

                configurarCampos(dadosCompra);
                compraValida = validarCompraMedianteSrp(dadosCompra);
            })
            .catch(e => alert(e.message));

        return compraValida;
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

    async function buscarUnidades() {
        const formData = new FormData();

        formData.append('documento', cnpj);
        PHPSession.appendFormData(formData);

        let unidadesCompradoras;

        await HttpClient
            .post(rotasApi.buscarUnidadesAtivas, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message)
                }

                unidadesCompradoras = response.data;
            });

        return unidadesCompradoras;
    }

    async function buscaDadosFormulario() {
        buscarEntidade();
        const unidadesCompradoras = await buscarUnidades();

        if (unidadesCompradoras.length === 0) {
            alert(
                'Não foi possível identificar nenhuma unidade compradora,' +
                ' por favor, verifique o cadastro de unidade.'
            );

            botaoSalvar.attr('disabled', true);
            botaoBuscarContrato.attr('disabled', true);
        }

        criarOpcoesUnidadesCompradoras(unidadesCompradoras);
    }

    PHPSession.loadData().then(async () => {
        rotasApi = getRotasApi(PHPSession.requestApi);

        const integracaoAtiva = await verificaIntegracaoAtiva();
        if (!integracaoAtiva) {
            return desabilitarBotoes();
        }

        await buscaDadosFormulario();
    });

    ancoraConsultarCompra.on('click', () => {
        const linkCompra = jQuery('#linkCompra').attr('href');

        if (linkCompra === undefined) {
            return alert('Selecione a Contratação/Edital/Aviso para consultá-la no PNCP.');
        }
    });

    botaoBuscarContrato.on('click', () => {
        if (inputEstaVazio('codigoUnidade')) {
            return alert('Selecione a Unidade Compradora.');
        }

        if (inputEstaVazio('numeroCompra')) {
            return alert('Selecione a Contratação/Edital/Aviso.');
        }

        if (inputEstaVazio('ac16_sequencial')) {
            return alert('Selecione o contrato.');
        }

        const formData = new FormData();
        let codigoLicitacao = jQuery('#ac16_sequencial').val();

        formData.append('ac16_sequencial', codigoLicitacao);
        PHPSession.appendFormData(formData);

        HttpClient
            .post(rotasApi.buscarContrato, {body: formData})
            .then(response => {
                if (response.data[0].contrato_pncp !== null) {
                    const dadosAcordo = response.data[0];
                    const cnpjCompra = "<?= $instituicao->getCNPJ(); ?>"
                    const anoContrato = dadosAcordo.contrato_pncp.pn04_ano
                    const codigoCompra = dadosAcordo.contrato_pncp.pn04_numero

                    const linkContrato =
                        '<a href="https://pncp.gov.br/app/contratos/'
                        + `${cnpjCompra}/${anoContrato}/${codigoCompra}"`
                        + `target="_blank">Clique aqui para acessar</a>`;

                    return alert(`Contrato já encontra-se publicado no PNCP!\n${linkContrato}`);
                }

                inputDadosBuscados.val('1').trigger('change');
                preencheCamposContrato(response.data[0]);
            })
            .catch(e => {
                inputDadosBuscados.val('0').trigger('change');
                return alert(e.message);
            });
    })

    inputNumeroParcelas.on('input', (e) => {
        if (e.target.value < 1) {
            e.target.value = ''
        }

        e.target.value = e.target.value.replace(/\D/g, '');
    });

    inputDadosBuscados.on('change', (e) => {
        if (e.target.value === '0') {
            botaoSalvar.prop('disabled', true);
        } else {
            botaoSalvar.prop('disabled', false);
        }
    });

    botaoAnexarDocumento.on('click', () => {
        const acordo = jQuery('#ac16_sequencial').val();

        if (!acordo) {
            return alert('Selecione o acordo.');
        }

        js_OpenJanelaIframe(
            "",
            "iframe_anexar_documento",
            `pncp1_documentocontrato001.php?acordo=${acordo}`,
            "Anexar Documento",
            true
        );
    });

    botaoSalvar.on('click', function () {
        if (inputDadosBuscados.val() === '0') {
            inputDadosBuscados.trigger('change');
            return alert('Realize a busca de dados antes de enviar o Contrato.');
        }

        if (!verificaCamposObrigatorios()) {
            return;
        }

        const formData = montarCamposEnvio();

        HttpClient
            .post(rotasApi.incluirContrato, {body: formData})
            .then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                const sequencialCompra = response.data.sequencialContrato;
                const anoContrato = response.data.anoContrato;
                const cnpjCompra = response.data.orgaoEntidade.cnpj;

                const linkContrato =
                    '<a href="https://pncp.gov.br/app/contratos/'
                    + `${cnpjCompra}/${anoContrato}/${sequencialCompra}"`
                    + `target="_blank">Clique aqui para acessar</a>`;

                alert(
                    `Publicação de Contrato efetuada com sucesso no PNCP!\n${linkContrato}`
                    + '\n\nUm evento automático foi criado para esta ação.'
                );

                jQuery('#numeroCompra,#anoCompra').val('');
                jQuery('#linkCompra').removeAttr('href');

                const selectUnidadeCompradora = jQuery('#codigoUnidade');
                selectUnidadeCompradora.prop('selectedIndex', 0);

                if (jQuery('#codigoUnidade>option').length === 2) {
                    selectUnidadeCompradora.prop('selectedIndex', 1);
                    selectUnidadeCompradora.prop('disabled', true);
                }

                botaoSalvar.prop('disabled', true);
                botaoAnexarDocumento.prop('disabled', false);

            })
            .catch(e => {
                return alert(e.message);
            });
    });

    // Lookups
    const comprasLookup = new DBLookUp(ancoraCompra, inputCompraId, inputCompraId, {
        'arquivo': 'func_compraspncp.php',
        'label': 'Pesquisa de compras',
        'objetoLookUp': 'db_iframe_compraspncp',
        'camposAdicionais': ['pn03_numero', 'pn03_ano', 'pn03_liclicita']
    });

    comprasLookup.setCallBack('onClick', async retorno => {
        const anoCompra = jQuery('#anoCompra');
        const compra = {
            ano: retorno[3],
            numero: retorno[2],
            licitacao: retorno[4],
        }

        const compraValida = await buscarCompra(compra);
        if (!compraValida) {
            anoCompra.val('');
            return desabilitarBotoes();
        }

        botaoSalvar.prop('disabled', false);
        jQuery('#numeroCompra').val(compra.numero);
        anoCompra.val(compra.ano);

        let linkCompraSite = 'https://pncp.gov.br/app/editais/';
        linkCompraSite += `${cnpj}/${compra.ano}/${compra.numero}`

        jQuery('#linkCompra').attr('href', linkCompraSite);
    });

    let ancoraContrato = document.getElementById('ancoraContrato');
    const inputContratoId = document.getElementById('ac16_sequencial');

    new DBLookUp(ancoraContrato, inputContratoId, inputContratoId, {
        'arquivo': 'func_acordoinstitpncp.php',
        'label': 'Pesquisa de contratos',
        'objetoLookUp': 'db_iframe_acordoinstitpncp',
        'callBack': limparFormulario
    });

    const ancoraSubcontratado = document.getElementById('ancoraSubcontratado');
    const inputSubcontratadoId = document.getElementById('z01_numcgm');
    const inputSubcontratadoNome = document.getElementById('z01_nome');

    const contratoLookup = new DBLookUp(ancoraSubcontratado, inputSubcontratadoId, inputSubcontratadoNome, {
        'arquivo': 'func_nome.php',
        'label': 'Pesquisa de cgm',
        'objetoLookUp': 'db_iframe_cgm',
        'camposAdicionais': ['z01_cgccpf'],
    });

    contratoLookup.setCallBack('onClick', retorno => {
        jQuery('#niFornecedorSubContratado').val(retorno[2]);
    })

    const botaoConsultarContrato = jQuery('#btnConsultarContrato');

    botaoConsultarContrato.on('click', () => {
        if (inputEstaVazio('ac16_sequencial')) {
            return alert('Selecione o contrato para consultar mais detalhes.');
        }

        const contratoId = jQuery('#ac16_sequencial').val();

        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe',
            'con4_consacordos003.php?ac16_sequencial=' + contratoId,
            'Pesquisa',
            true
        );
    });
</script>
</body>
</html>
