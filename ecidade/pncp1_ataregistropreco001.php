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
    <script type="text/javascript" src="scripts/scripts.js"></script>
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
        <legend>Ata de Registro de Preço</legend>
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
                    <label for="l20_codigo" id="licitacao_ancora">Contratação/Edital/Aviso: </label>
                </td>
                <td>
                    <input type="text" id="pn03_numero" class="field-size4" disabled/>
                    <input type="text" id="pn03_liclicita" class="field-size4" hidden/>
                    <input type="text" id="pn03_ano" class="field-size3" disabled/>
                    <a style="color:inherit" id="linkCompra" target="_blank">
                        <button class="field-size2" id="btnConsultarCompra">
                            <i class="fas fa-search "></i>
                            PNCP
                        </button>
                    </a>
                </td>
            </tr>
            <tr id="trContratoSistema">
                <td>
                    <label for="contratoSistema">Contrato do sistema:</label>
                </td>
                <td>
                    <select name="contratoSistema" id="contratoSistema">
                        <option value="sim">Sim</option>
                        <option value="nao" selected>Não</option>
                    </select>
                </td>
            </tr>
            <tr id="trLinhaContrato" hidden>
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
            <tr id="trAta">
                <td>
                    <label for="numeroAtaRegistroPreco">Número Ata de Registro de Preço:</label>
                </td>
                <td>
                    <input type="text" id="numeroAtaRegistroPreco" class="field-size9" maxlength="50">
                </td>
            </tr>
            <tr id="trAnoAta">
                <td>
                    <label for="anoAta">Ano Ata:</label>
                </td>
                <td>
                    <input
                        type="text"
                        id="anoAta"
                        class="field-size9"
                        maxlength="4"
                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
                    ">
                </td>
            </tr>
            <tr id="trDataAssinatura">
                <td>
                    <label for="dataAssinatura">Data da Assinatura:</label>
                </td>
                <td>
                    <input id="dataAssinatura">
                </td>
            </tr>
            <tr id="trPeriodoVigencia">
                <td>
                    <label for="">Período Vigência de:</label>
                </td>
                <td>
                    <input id="dataVigenciaInicio"> <b>Até: </b> <input id="dataVigenciaFim">
                </td>
            </tr>
            <tr id="trDataCancelamento" hidden>
                <td>
                    <label for="dataCancelamento">Data Cancelamento:</label>
                </td>
                <td>
                    <input id="dataCancelamento">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="cnpj" value="<?= $instituicao->getCNPJ(); ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="dadosBuscados" value="0">
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btnSalvarDados">
        <i class="fas fa-save"></i>
        Enviar Dados
    </button>
    <button type="button" id="btnLimpar">
        <i class="fas fa-eraser"></i>
        Limpar Dados
    </button>
    <button type="button" id="btnAnexarDocumentos">
        <i class="fas fa-paperclip"></i>
        Anexar Documento
    </button>
</div>
<div id="modalAnexos" class="container">
    <fieldset>
        <legend>Configuração</legend>
        <form id="anexo" enctype="multipart/form-data">
            <table class="form-container">
                <tr>
                    <td>
                        <label for="tituloDocumento">Título:</label>
                    </td>
                    <td>
                        <input
                            type="text"
                            id="tituloDocumento"
                            name="tituloDocumento"
                            maxlength="50"
                            class="field-size8">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="anexoDocumento">Arquivo:</label>
                    </td>
                    <td>
                        <input type="file" id="anexoDocumento" name="anexoDocumento" style="padding-bottom: 20px">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <button class="btn btn-light" id="btnLancarAnexo">
        <i class="fa fa-plus-circle" aria-hidden="true"></i>
        Salvar
    </button>
</div>
<div id="modalAlteracao" class="container">
    <fieldset>
        <legend>Retificar Ata de Registro de Preço</legend>
        <table class="form-container">
            <tr id="trCancelar" hidden>
                <td>
                    <label for="cancelar">Cancelar:</label>
                </td>
                <td>
                    <select name="cancelar" id="cancelar" style="width: 240px">
                        <option value="sim">Sim</option>
                        <option value="nao" selected>Não</option>
                    </select>
                </td>
            </tr>
            <tr id="trDataCancelamentoRetificacao" hidden>
                <td>
                    <label for="dataCancelamentoRetificacao">Data Cancelamento:</label>
                </td>
                <td>
                    <input id="dataCancelamentoRetificacao">
                </td>
            </tr>
            <tr id="trJustificativa" hidden>
                <td>
                    <label for="justificativa">Justificativa:</label>
                </td>
                <td>
                    <textarea name="justificativa" id="justificativa" cols="25" rows="5" maxlength="255"></textarea>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" id="btnRetificar">
        <i class="fas fa-save"></i>
        Retificar
    </button>
</div>
<div class="subcontainer">
    <fieldset id="ctnTable" style="width: 1500px">
        <legend>Atas</legend>
        <table id="table-itens"
               class="table table-sm"
               data-height="300"
               data-virtual-scroll="true"
               style="width: 100%;">
        </table>
    </fieldset>
</div>
<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
    $.noConflict();
    window.addEventListener('load', async () => {
        var apiUrl;
        await PHPSession.loadData().then(() => {
            apiUrl = PHPSession.requestApi;
        });
        const btnAnexarDocumentos = document.getElementById("btnAnexarDocumentos");
        const btnLancarAnexo = document.getElementById("btnLancarAnexo");
        const anexoDocumento = document.getElementById("anexoDocumento");
        const tituloDocumento = document.getElementById("tituloDocumento");
        const frmAnexos = document.getElementById("anexo");
        const orgao = document.getElementById('orgao');
        const inputDataAssinatura = new DBInputDate(document.getElementById("dataAssinatura"));
        const inputDataVigenciaInicio = new DBInputDate(document.getElementById("dataVigenciaInicio"));
        const inputDataVigenciaFim = new DBInputDate(document.getElementById("dataVigenciaFim"));
        const inputDataCancelamento = new DBInputDate(document.getElementById("dataCancelamento"));
        const inputDataRetificacao = new DBInputDate(document.getElementById("dataCancelamentoRetificacao"));
        const trLinhaContrato = document.getElementById('trLinhaContrato');
        const trCancelar = document.getElementById('trCancelar');
        const trDataCancelamento = document.getElementById('trDataCancelamento');
        const trJustificativa = document.getElementById('trJustificativa');
        const trContratoSistema = document.getElementById('trContratoSistema');
        const trDataCancelamentoRetificacao = document.getElementById('trDataCancelamentoRetificacao');
        const dataCancelamentoRetificacao = document.getElementById('dataCancelamentoRetificacao');
        const inputCancelar = document.getElementById('cancelar');
        const inputJustificativa = document.getElementById('justificativa');
        const licitacaoAncora = document.getElementById('licitacao_ancora');
        const numeroAtaRegistroPreco = document.getElementById('numeroAtaRegistroPreco');
        const anoAta = document.getElementById('anoAta');
        const licitacaoCodigo = document.getElementById('pn03_liclicita');
        const numeroCompra = document.getElementById('pn03_numero');
        const licitacaoObjeto = document.getElementById('pn03_ano');
        const botaoBuscarContrato = document.getElementById('btnBuscarContrato');
        const btnConsultarCompra = document.getElementById('btnConsultarCompra');
        const btnConsultarContrato = document.getElementById('btnConsultarContrato');
        const btnRetificarAta = document.getElementById('btnRetificar');
        const btnLimpar = document.getElementById('btnLimpar');
        const inputContratoId = document.getElementById('ac16_sequencial');
        const inputDadosBuscados = jQuery('#dadosBuscados');
        const contratoSistema = document.getElementById('contratoSistema');
        const tabelaAtas = jQuery('#table-itens');
        const cnpj = document.getElementById('cnpj');
        let ancoraContrato = document.getElementById('ancoraContrato');
        let btnSalvarDados = document.getElementById('btnSalvarDados');
        let rotasApi;
        let linkCompraSite = '';
        let sequencialAta = '';
        btnRetificarAta.disabled = true;
        rotasApi = getRotasApi(apiUrl);

        const modalAlteracao = document.getElementById('modalAlteracao');
        var windowAta = new windowAux('windowAta', 'Alteração Ata Registro Preco', 490, 290);
        windowAta.setContent(modalAlteracao);
        windowAta.allowCloseWithEsc(true);
        windowAta.setShutDownFunction(function () {
            windowAta.oDBMask.destroy();
        });

        function getRotasApi(baseUrl) {
            return {
                buscarEntidade:
                    baseUrl + '/patrimonial/pncp/unidades/buscarEntidade/',
                verificaIntegracao:
                    baseUrl + '/patrimonial/pncp/integracao/verificaIntegracao/',
                buscarContrato:
                    baseUrl + '/patrimonial/contratos/consulta/acordos',
                incluirAtaRegistroPreco:
                    baseUrl + '/patrimonial/pncp/ataRegistroPreco/incluir',
                buscarAtas:
                    baseUrl + '/patrimonial/pncp/ataRegistroPreco/buscar',
                excluirAta:
                    baseUrl + '/patrimonial/pncp/ataRegistroPreco/excluir',
                retificarAtaRegistroPreco:
                    baseUrl + '/patrimonial/pncp/ataRegistroPreco/retificar',
            }
        }

        const modalAnexos = document.getElementById('modalAnexos');
        let windowItens = new windowAux('windowItens', 'Anexar Documento', 550, 250);
        windowItens.setContent(modalAnexos);
        windowItens.allowCloseWithEsc(true);
        windowItens.setShutDownFunction(function () {
            windowItens.oDBMask.destroy();
        });

        btnAnexarDocumentos.addEventListener('click', () => {
            windowItens.show(0, 0, true);
        });

        anexoDocumento.addEventListener('change', () => {
            let arquivo = anexoDocumento.files[0];
            let extencao = getExtension(arquivo.name);
            if (!verificaExtencao(extencao)) {
                return false;
            }
            if (arquivo.size > 30000000) {
                alert('O tamanho máximo aceito, por arquivo enviado ao PNCP, é de 30 MB (Megabytes).');
                anexoDocumento.value = '';
                return false;
            }
        })

        function verificaExtencao(anexo) {
            let extensoes = [
                'pdf', 'txt', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'odt', 'ods', 'sxw',
                'zip', '7z', 'rar', 'dwg', 'dwt', 'dxf', 'dwf', 'dwfx', 'svg', 'sldprt',
                'sldasm', 'dgn', 'ifc', 'skp', '3ds', 'dae', 'obj', 'rfa', 'rte'
            ];

            const extensao = extensoes.indexOf(anexo)
            if (extensao === -1) {
                alert('A extensão do arquivo anexado não é aceita pelo PNCP.')
                anexoDocumento.value = '';
                return false;
            }

            return true;
        }

        btnLancarAnexo.addEventListener('click', () => {
            windowItens.destroy();
        });

        btnLimpar.addEventListener('click', () => {
            limparFormulario();
            contratoSistema.value = 'nao';
            contratoSistema.dispatchEvent(new Event('change'));
        });

        btnConsultarContrato.addEventListener('click', () => {
            if (inputContratoId.value === '') {
                alert('Selecione o contrato para consultar mais detalhes.');
                return;
            }

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe',
                'con4_consacordos003.php?ac16_sequencial=' + inputContratoId.value,
                'Pesquisa',
                true
            );
        });

        btnConsultarCompra.addEventListener('click', () => {
            if (numeroCompra.value === '') {
                alert('Selecione a Contratação/Edital/Aviso para consultá-la no PNCP');
            }
        });

        inputCancelar.addEventListener('change', () => {
            btnRetificarAta.disabled = true;
            trDataCancelamentoRetificacao.hidden = true;
            inputDataRetificacao.setReadOnly(true);
            trJustificativa.hidden = true;
            if (inputCancelar.value === 'sim') {
                inputDataRetificacao.value = '';
                inputJustificativa.value = '';
                btnRetificarAta.disabled = false;
                trDataCancelamentoRetificacao.hidden = false;
                trCancelar.hidden = false;
                inputDataRetificacao.setReadOnly(false);
                trJustificativa.hidden = false;
            }
        })

        contratoSistema.addEventListener('change', () => {
            limparFormulario('contrato');
            trLinhaContrato.hidden = contratoSistema.value === 'nao';
            desabilitarCampos(false);
            if (contratoSistema.value === 'sim') {
                desabilitarCampos(true);
            }
        });

        function getExtension(path) {
            var r = /\.([^./]+)$/.exec(path);
            return r && r[1] || '';
        }

        btnSalvarDados.addEventListener('click', () => {
            if (!verificaCampos()) {
                return false;
            }
            const formData = new FormData(frmAnexos)
            formData.append('sequencialAta', sequencialAta);
            formData.append('pn03_codigo', licitacaoCodigo.value);
            formData.append('cnpj', cnpj.value);
            formData.append('numeroAtaRegistroPreco', numeroAtaRegistroPreco.value);
            formData.append('anoAta', anoAta.value);
            formData.append('dataAssinatura', js_formatar(inputDataAssinatura.__toLocaleDateString(), 'd'));
            formData.append('dataVigenciaInicio', js_formatar(inputDataVigenciaInicio.__toLocaleDateString(), 'd'));
            formData.append('dataVigenciaFim', js_formatar(inputDataVigenciaFim.__toLocaleDateString(), 'd'));
            PHPSession.appendFormData(formData);
            HttpClient.post(rotasApi.incluirAtaRegistroPreco, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                let msg = "Ata de Registro de Preço publicada com sucesso.";
                let link = `<a target="_blank" href="${response.data.link}">Clique aqui para acessar.</a>`;
                alert(`${msg}\n${link}`);
                limparFormulario(true);
                buscarAtas();
                tabelaAtas.bootstrapTable('refresh');
            });
        });

        botaoBuscarContrato.on('click', () => {
            if (inputEstaVazio('pn03_liclicita')) {
                return alert('Selecione a Contratação/Edital/Aviso.');
            }
            if (inputEstaVazio('ac16_sequencial')) {
                return alert('Selecione o contrato.');
            }

            const formData = new FormData();
            let codigoLicitacao = document.getElementById('ac16_sequencial').value;

            formData.append('ac16_sequencial', codigoLicitacao);
            PHPSession.appendFormData(formData);

            HttpClient
                .post(rotasApi.buscarContrato, {body: formData})
                .then(response => {
                    inputDadosBuscados.val('1').trigger('change');
                    preencheCamposContrato(response.data[0]);
                })
                .catch(e => {
                    inputDadosBuscados.val('0').trigger('change');
                    return alert(e.message);
                });
        })

        function buscarEntidade() {
            const formData = new FormData();

            formData.append('documento', cnpj.value);
            PHPSession.appendFormData(formData);

            HttpClient
                .post(rotasApi.buscarEntidade, {body: formData})
                .then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    orgao.value = response.data.razaoSocial;
                });
        }

        function limparFormulario(dado) {
            if (dado === true) {
                inputContratoId.value = '';
                contratoSistema.value = 'nao';
                contratoSistema.dispatchEvent(new Event('change'));
            }
            if (dado === 'contrato') {
                inputContratoId.value = '';
            }
            trDataCancelamento.hidden = true;
            trContratoSistema.hidden = false;
            numeroAtaRegistroPreco.value = '';
            inputJustificativa.value = '';
            anoAta.value = '';
            inputDataAssinatura.value = '';
            inputDataVigenciaInicio.value = '';
            inputDataVigenciaFim.value = '';
            inputDataCancelamento.value = '';
            tituloDocumento.value = "";
            inputDataRetificacao.value = '';
            numeroAtaRegistroPreco.disabled = false;
            anoAta.disabled = false;
            inputDataAssinatura.disabled = false;
            inputDataAssinatura.setReadOnly(false);
            inputDataVigenciaInicio.disabled = false;
            inputDataVigenciaInicio.setReadOnly(false);
            inputDataVigenciaFim.disabled = false;
            inputDataVigenciaFim.setReadOnly(false);
            inputDataCancelamento.disabled = false;
            inputDataCancelamento.setReadOnly(false);
            inputDataRetificacao.disabled = false;
            inputDataRetificacao.setReadOnly(false);
            anexoDocumento.value = "";
            btnSalvarDados.disabled = false;
        }

        function verificaCamposRetificacao() {
            if (inputCancelar.value === 'sim') {
                if (inputDataRetificacao.value === null) {
                    alert('Preencha a Data de Cancelamento.');
                    return false;
                }
                if (inputJustificativa.value === '') {
                    alert('Preencha a justificativa.');
                    return false;
                }
            }
            return true;
        }

        function verificaCampos() {
            if (inputContratoId.value === '' && contratoSistema.value === 'sim') {
                alert('Selecione um contrato.');
                return false;
            }
            if (licitacaoCodigo.value === '') {
                alert('Preencha a Contratação/Edital/Aviso.');
                return false;
            }
            if (numeroAtaRegistroPreco.value === '') {
                alert('Preencha o Número da Ata de Registro de Preço.');
                return false;
            }
            if (anoAta.value === '') {
                alert('Preencha o Ano da Ata.');
                return false;
            }
            if (anoAta.value < '2021') {
                alert('Ano da ata é anterior ao da Lei nº 14.133 (2021).');
                return false;
            }
            if (inputDataAssinatura.value === null) {
                alert('Preencha a Data da Assinatura.');
                return false;
            }
            if (inputDataVigenciaInicio.value === null) {
                alert('Preencha a Data Inicial da Vigência.');
                return false;
            }
            if (inputDataVigenciaFim.value === null) {
                alert('Preencha a Data Final da Vigência.');
                return false;
            }
            if (js_comparadata(inputDataAssinatura.__toLocaleDateString(), '01/04/2021', '<')) {
                alert('Data da assinatura para ata é anterior ao da Lei nº 14.133 (01/04/2021).');
                return false;
            }
            if (js_comparadata(inputDataVigenciaInicio.__toLocaleDateString(), '01/04/2021', '<')) {
                alert('Data de início de vigência é anterior ao da Lei nº 14.133 (01/04/2021).');
                return false;
            }
            if (tituloDocumento.value === '') {
                alert('Defina um título ao documento à ser anexado.');
                return false;
            }
            if (anexoDocumento.value === '') {
                alert('Nenhum documento anexado à Contratação/Edital/Aviso, verifique.');
                return false;
            }

            return true;
        }

        function inputEstaVazio(id) {
            let valorInput = jQuery(`#${id}`).val();

            return (
                valorInput === '' || valorInput === undefined ||
                valorInput === false || valorInput === 0 ||
                valorInput === null
            );
        }

        function preencheCamposContrato(dados) {
            if (dados.ac16_anousu) {
                jQuery('#anoAta').val(dados.ac16_anousu).attr('disabled', true);
            }

            if (dados.ac16_numero) {
                jQuery('#numeroAtaRegistroPreco').val(dados.ac16_numero).attr('disabled', true);
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
        }

        function buscarAtas() {
            const formData = new FormData;
            formData.append('pn03_codigo', licitacaoCodigo.value);
            formData.append('cnpj', cnpj.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(rotasApi.buscarAtas, {body: formData}).then(response => {
                if (response.error) {
                    return alert(response.message);
                }

                if (response.data) {
                    tabelaAtas.bootstrapTable('load', response.data.data);
                }
            });
        }

        function verificaIntegracaoAtiva() {
            const formData = new FormData();
            formData.append('documento', cnpj.value);
            PHPSession.appendFormData(formData);
            HttpClient.post(rotasApi.verificaIntegracao, {body: formData}).then(response => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                if (response.data === null) {
                    alert("Por favor, verifique a configuração de integração com o PNCP!");
                    btnSalvarDados.disabled = true;
                    return;
                }
                buscarEntidade();
            });
        }

        function desabilitarCampos(opcao) {
            numeroAtaRegistroPreco.disabled = opcao;
            anoAta.disabled = opcao;
            inputDataAssinatura.setReadOnly(opcao);
            inputDataVigenciaInicio.setReadOnly(opcao);
            inputDataVigenciaFim.setReadOnly(opcao);

        }

        verificaIntegracaoAtiva();

        const comprasLookup = new DBLookUp(licitacaoAncora, numeroCompra, licitacaoObjeto, {
            'sArquivo': 'func_compraspncp.php',
            'sLabel': 'Pesquisa de publicações',
            'sObjetoLookUp': 'db_iframe_compraspncp',
            'camposAdicionais': ['pn03_numero', 'pn03_ano', 'pn03_link', 'pn03_codigo'],
        });

        comprasLookup.setCallBack('onClick', retorno => {
            limparFormulario(true);
            licitacaoCodigo.value = retorno[5];
            const anoCompra = retorno[3];
            const numeroCompra = retorno[2];
            linkCompraSite = 'https://pncp.gov.br/app/editais/';
            linkCompraSite += `${cnpj.value}/${anoCompra}/${numeroCompra}`

            jQuery('#linkCompra').attr('href', linkCompraSite);
            buscarAtas();
        });

        new DBLookUp(ancoraContrato, inputContratoId, inputContratoId, {
            'arquivo': 'func_acordoinstitpncp.php',
            'label': 'Pesquisa de contratos',
            'objetoLookUp': 'db_iframe_acordoinstitpncp',
            'callBack': () => {
                limparFormulario();
                desabilitarCampos(true);
            }
        });

        jQuery(document).ready(jQuery => {
            window.operateEvents = {
                'click .excluir': (e, d, data) => {
                    if (!confirm('Confirma a exclusão data Ata de Registro de Preço?')) {
                        return false;
                    }
                    let formData = new FormData();
                    PHPSession.appendFormData(formData);
                    formData.append('pn03_codigo', licitacaoCodigo.value);
                    formData.append('cnpj', cnpj.value);
                    formData.append('sequencialAta', data.sequencialAta);
                    HttpClient.post(rotasApi.excluirAta, {body: formData}).then(response => {
                        if (response.error) {
                            alert(response.message);
                            return;
                        }
                        alert('Ata de Registro de Preço excluida com sucesso.');
                        limparFormulario();
                        tabelaAtas.bootstrapTable('removeAll');
                        buscarAtas();
                    });
                },
                'click .alterar': (e, d, data) => {
                    limparFormulario();
                    contratoSistema.value = 'nao'
                    contratoSistema.dispatchEvent(new Event('change'))
                    trCancelar.hidden = false;
                    inputDataCancelamento.setReadOnly(true);
                    inputDataRetificacao.setReadOnly(true);
                    inputCancelar.value = 'nao';
                    btnSalvarDados.disabled = false;
                    trDataCancelamento.hidden = true;
                    trContratoSistema.hidden = false;
                    trLinhaContrato.hidden = true;
                    trJustificativa.hidden = true;
                    trDataCancelamentoRetificacao.hidden = true;
                    desabilitarCampos(false);
                    if (data.cancelado) {
                        trContratoSistema.hidden = true;
                        numeroAtaRegistroPreco.value = data.numeroAtaRegistroPreco;
                        anoAta.value = data.anoAta;
                        inputDataAssinatura.value = data.dataAssinatura;
                        inputDataVigenciaInicio.value = data.dataVigenciaInicio;
                        inputDataVigenciaFim.value = data.dataVigenciaFim;
                        sequencialAta = data.sequencialAta;
                        desabilitarCampos(true);
                        trDataCancelamento.hidden = false;
                        trCancelar.hidden = true;
                        inputDataCancelamento.value = (new Date(data.dataCancelamento)).toLocaleString();
                        btnSalvarDados.disabled = true;
                    } else {
                        windowAta.show(0, 0, true);
                        btnRetificarAta.addEventListener('click', () => {
                            if (!verificaCamposRetificacao()) {
                                return false;
                            }
                            const formData = new FormData()
                            formData.append('sequencialAta', data.sequencialAta);
                            formData.append('pn03_codigo', licitacaoCodigo.value);
                            formData.append('cancelado', inputCancelar.value);
                            formData.append('dataCancelamento', js_formatar(
                                inputDataRetificacao.__toLocaleDateString(),
                                'd')
                            );
                            formData.append('justificativa', inputJustificativa.value);
                            formData.append('cnpj', cnpj.value);
                            formData.append('numeroAtaRegistroPreco', data.numeroAtaRegistroPreco);
                            formData.append('anoAta', data.anoAta);
                            formData.append('dataAssinatura', data.dataAssinatura);
                            formData.append('dataVigenciaInicio', data.dataVigenciaInicio);
                            formData.append('dataVigenciaFim', data.dataVigenciaFim);
                            PHPSession.appendFormData(formData);

                            HttpClient.post(rotasApi.retificarAtaRegistroPreco, {body: formData}).then(response => {
                                if (response.error) {
                                    alert(response.message);
                                    return;
                                }
                                alert('Ata de Registro de Preço retificada com sucesso.');
                                inputCancelar.value = 'nao';
                                inputCancelar.dispatchEvent(new Event('change'));
                                windowAta.destroy();
                                buscarAtas();
                                tabelaAtas.bootstrapTable('refresh');
                            });

                        })
                    }
                }
            };

            const colunasAtas = [
                {
                    field: 'numeroAtaRegistroPreco',
                    title: 'Número da Ata RP',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'anoAta',
                    title: 'Ano Ata',
                    halign: 'center',
                    align: 'center',
                },
                {
                    field: 'dataAssinatura',
                    title: 'Data Assinatura',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
                {
                    field: 'dataVigenciaInicio',
                    title: 'Data Vigência Inicio',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
                {
                    field: 'dataVigenciaFim',
                    title: 'Data Vigência Fim',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        let data = value.substring(0, 10);
                        return data.split('-').reverse().join('/');
                    }
                },
                {
                    field: 'cancelado',
                    title: 'Cancelado',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        return value ? 'Sim' : 'Não';
                    }
                },
                {
                    field: 'dataCancelamento',
                    title: 'Data Cancelamento',
                    halign: 'center',
                    align: 'center',
                    formatter: (value) => {
                        if (value !== null) {
                            let data = value.substring(0, 10);
                            return data.split('-').reverse().join('/');
                        }
                    }
                },
                {
                    field: 'acao',
                    title: 'Ações',
                    halign: 'center',
                    align: 'center',
                    formatter: (valor, data, index) => {
                        return ['<a class="alterar" href="javascript:void(0)" title="Alterar">',
                            '  <i class="fa fa-edit"></i>',
                            '</a>',
                            '&nbsp;&nbsp;',
                            '<a class="excluir" href="javascript:void(0)" title="Excluir">',
                            '  <i class="fas fa-trash-alt"></i>',
                            '</a>'].join('')
                    },
                    events: window.operateEvents
                }
            ];

            tabelaAtas.bootstrapTable({
                locale: 'pt-BR',
                height: 250,
                class: "table table-sm",
                columns: colunasAtas,
                showButtonText: true,
                useRowAttrFunc: true,
                reorderableRows: true,
            });
        });
    });
</script>
</body>
</html>
