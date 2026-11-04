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
        select:disabled,
        input:disabled {
            color: dimgrey;
        }
    </style>
</head>

<body>
    <div class="container">
        <fieldset>
            <legend>Contratação/Edital/Aviso</legend>
            <table class="form-container">
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
                            <label for="codigoUnidade">Unidade Compradora:</label>
                        </td>
                        <td>
                            <select id="codigoUnidade" name="codigoUnidade">
                                <option value="0" selected disabled>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="origem">Origem:</label>
                        </td>
                        <td>
                            <select name="origem" id="origem">
                                <option selected disabled>Selecione</option>
                                <option value="2" selected>Licitação</option>
                                <option value="3">Solicitação</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="trLicitacao">
                        <td>
                            <label for="l20_codigo" id="licitacao_ancora">Licitação: </label>
                        </td>
                        <td>
                            <input type="text" id="l20_codigo" style="width: 195px" disabled />
                            <input type="text" id="l20_anousu" hidden />
                            <button type="button" id="btnCarregarLicitacao">
                                <i class="fas fa-search"></i>
                                Buscar Dados
                            </button>
                            <button type="button" id="btnConsultarLicitacao">
                                <i class="fas fa-search"></i>
                                Consultar
                            </button>
                        </td>
                    </tr>
                    <tr id="trSolicitacao" style="display:none">
                        <td>
                            <label for="pc10_numero" id="solicitacao_ancora">Solicitação: </label>
                        </td>
                        <td>
                            <input type="text" id="pc10_numero" style="width: 195px" disabled />
                            <input type="text" id="pc10_resumo" hidden />
                            <button type="button" id="btnCarregarSolicitacao">
                                <i class="fas fa-search"></i>
                                Buscar Dados
                            </button>
                            <button type="button" id="btnConsultarSolicitacao">
                                <i class="fas fa-search"></i>
                                Consultar
                            </button>
                        </td>
                    </tr>
                    <tr id="trSituacaoLicitacao" style="display:none">
                        <td>
                            <label for="situacaoLicitacaoId">Situação:</label>
                        </td>
                        <td>
                            <select name="situacaoLicitacaoId" id="situacaoLicitacaoId">
                                <option value="0">Selecione</option>
                                <option value="1">Divulgada no PNCP</option>
                                <option value="2">Revogada</option>
                                <option value="3">Anulada</option>
                                <option value="4">Suspensa</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="modalidadeId">Modalidade de Contratação:</label>
                        </td>
                        <td>
                            <select name="modalidadeId" id="modalidadeId">
                                <option value="0" selected disabled>Selecione</option>
                                <option value="1">Leilão - Eletrônico</option>
                                <option value="2">Diálogo Competitivo</option>
                                <option value="3">Concurso</option>
                                <option value="4">Concorrência - Eletrônica</option>
                                <option value="5">Concorrência - Presencial</option>
                                <option value="6">Pregão - Eletrônico</option>
                                <option value="7">Pregão - Presencial</option>
                                <option value="8">Dispensa de Licitação</option>
                                <option value="9">Inexigibilidade</option>
                                <option value="10">Manifestação de Interesse</option>
                                <option value="11">Pré-qualificação</option>
                                <option value="12">Credenciamento</option>
                                <option value="13">Leilão - Presencial</option>
                                <option value="14">Inaplicabilidade da Licitação</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="tipoInstrumentoConvocatorioId">Instrumento Convocatório:</label>
                        </td>
                        <td>
                            <select name="tipoInstrumentoConvocatorioId" id="tipoInstrumentoConvocatorioId">
                                <option value="0" selected disabled>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="modoDisputaId">Modo de Disputa:</label>
                        </td>
                        <td>
                            <select name="modoDisputaId" id="modoDisputaId">
                                <option value="0" selected disabled>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="numeroCompra">Número da Contratação:</label>
                        </td>
                        <td>
                            <input type="text" id="numeroCompra" class="field-size9" maxlength="50">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="anoCompra">Ano da Contratação:</label>
                        </td>
                        <td>
                            <input type="text" id="anoCompra" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="numeroProcesso">Número do Processo:</label>
                        </td>
                        <td>
                            <input type="text" id="numeroProcesso" maxlength="50" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="objetoCompra">Objeto da Contratação:</label>
                        </td>
                        <td>
                            <input type="text" id="objetoCompra" class="field-size9">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="sistemaRegistroPreco">SRP:</label>
                        </td>
                        <td>
                            <select name="sistemaRegistroPreco" id="sistemaRegistroPreco">
                                <option value="0" selected disabled>Selecione</option>
                                <option value=true>Sim</option>
                                <option value=false>Não</option>
                            </select>
                        </td>
                    </tr>

                    <tr id="trDataAberturaLicicacao">
                        <td>
                            <label for="dataAberturaLicitacao">Data Abertura Licitação:</label>
                        </td>
                        <td>
                            <input id="dataAberturaLicitacao">
                            <input type="time" id="horaAberturaLicitacao">
                        </td>
                    </tr>
                    <tr id="trDataAberturaProposta">
                        <td>
                            <label for="dataAberturaProposta">Data Abertura Proposta:</label>
                        </td>
                        <td>
                            <input type="date" id="dataAberturaProposta">
                            <input type="time" id="horaAberturaProposta">
                        </td>
                    </tr>

                    <tr id="trDataEncerramentoProposta">
                        <td>
                            <label for="dataEncerramentoProposta">Data Encerramento Proposta:</label>
                        </td>
                        <td>
                            <input id="dataEncerramentoProposta">
                            <input type="time" id="horaEncerramentoProposta">
                        </td>
                    </tr>
                    <tr id="trDataHomologacao">
                        <td>
                            <label for="dataHomologacao">Data Homologação:</label>
                        </td>
                        <td>
                            <input id="dataHomologacao">
                            <input type="time" id="horaHomologacao">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="amparoLegalId">Amparo Legal:</label>
                        </td>
                        <td>
                            <select name="amparoLegalId" id="amparoLegalId">
                                <option selected disabled>Selecione</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="linkOrigem">Link sistema origem:</label>
                        </td>
                        <td>
                            <input type="text" id="linkOrigem" class="field-size9" style="background: #E6E4F1">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="infoComplementar">Informação Complementar:</label>
                        </td>
                        <td>
                            <select name="infoComplementar" id="infoComplementar">
                                <option value="0">Sim</option>
                                <option value="1" selected>Não</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="trInformacaoComplementar" style="display: none">
                        <td>
                            <label for="infoComplementarText"></label>
                        </td>
                        <td>
                            <textarea maxlength="5120" name="infoComplementarText" id="infoComplementarText" cols="30"
                                rows="5">
                    </textarea>
                        </td>
                    </tr>
                    <tr id="trJustificativa" hidden>
                        <td>
                            <label for="justificativaPresencial">Justificativa Presencial:</label>
                        </td>
                        <td>
                            <textarea maxlength="5120" name="justificativaPresencial" id="justificativaPresencial"
                                cols="30" rows="5">
                    </textarea>
                        </td>
                    </tr>
                    <tr>
                        <input type="hidden" id="documento" value="<?php echo $instituicao->getCNPJ(); ?>">
                        <input type="hidden" id="sequencialCompra" value="">
                    </tr>

                </table>
        </fieldset>
        <button type="button" id="btnSalvarDados">
            <i class="fas fa-save"></i>
            Enviar Dados
        </button>
        <button type="button" id="btnSalvarDadosSolicitacao" style="display: none">
            <i class="fas fa-save"></i>
            Enviar Dados
        </button>
        <button type="button" id="btnAnexarDocumentos">
            <i class="fas fa-paperclip"></i>
            Anexar Documento
        </button>
        <button type="button" id="btnAlterar">
            <i class="fas fa-edit"></i>
            Alterar
        </button>
    </div>
    <div id="modalAnexos" class="container">
        <fieldset>
            <legend>Configuração</legend>
            <form id="anexo" enctype="multipart/form-data">
                <table class="form-container">
                    <tr>
                        <td>
                            <label for="tipoDocumento">Tipo de documento:</label>
                        </td>
                        <td>
                            <select id="tipoDocumento" name="tipoDocumento">
                                <optgroup label="Tipos de Documento">
                                    <option value="1">Aviso de Contratação Direta</option>
                                    <option value="2">Edital</option>
                                </optgroup>
                                <optgroup label="Outros anexos">
                                    <option value="3">Minuta do Contrato</option>
                                    <option value="4">Termo de Referência</option>
                                    <option value="5">Anteprojeto</option>
                                    <option value="6">Projeto Básico</option>
                                    <option value="7">Estudo Técnico Preliminar</option>
                                    <option value="8">Projeto Executivo</option>
                                    <option value="9">Mapa de Riscos</option>
                                    <option value="10">DFD</option>
                                    <option value="19">Minuta de Ata de Registro de Preços</option>
                                    <option value="20">Ato que autoriza a Contratação Direta</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="tituloDocumento">Título:</label>
                        </td>
                        <td>
                            <input type="text" id="tituloDocumento" name="tituloDocumento" maxlength="50"
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
        <div class="subcontainer" style="display: none">
            <fieldset id="anexoTable" style="width: 600px">
                <legend>Itens</legend>
                <table id="table-anexos" class="table table-sm" style="width: 100%;">
                </table>
            </fieldset>
        </div>
    </div>
    <div class="subcontainer">
        <fieldset id="ctnTable">
            <legend>Itens</legend>
            <table id="tabelaItensLicitacao" class="table table-sm" style="width: 100%;">
            </table>

            <table id="tabelaItensSolicitacao" class="table table-sm" style="width: 100%;display:none">
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
            var apiUrl
            await PHPSession.loadData().then(() => {
                apiUrl = PHPSession.requestApi;
            });

            const licitacaoRow = document.getElementById('trLicitacao');
            const solicitacaoRow = document.getElementById('trSolicitacao');
            const licitacaoAncora = document.getElementById('licitacao_ancora');
            const solicitacaoAncora = document.getElementById('solicitacao_ancora');
            const licitacaoCodigo = document.getElementById('l20_codigo');
            const solicitacaoNumero = document.getElementById('pc10_numero');
            const licitacaoObjeto = document.getElementById('l20_anousu');
            const situacaoLicitacao = document.getElementById('situacaoLicitacaoId');
            const orgao = document.getElementById("orgao");
            const codigoUnidade = document.getElementById("codigoUnidade");
            const numeroCompra = document.getElementById("numeroCompra");
            const sequencialCompra = document.getElementById("sequencialCompra");
            const anoCompra = document.getElementById("anoCompra");
            const justificativaPresencial = document.getElementById("justificativaPresencial");
            const numeroProcesso = document.getElementById("numeroProcesso");
            const objetoCompra = document.getElementById("objetoCompra");
            const trDataEncerramentoProposta = document.getElementById('trDataEncerramentoProposta');
            const trDataAberturaLicicacao = document.getElementById('trDataAberturaLicicacao');
            const trDataAberturaProposta = document.getElementById('trDataAberturaProposta');
            const trSituacaoLicitacao = document.getElementById('trSituacaoLicitacao');
            const dataAberturaLicitacao = new DBInputDate(document.getElementById("dataAberturaLicitacao"));
            const horaAberturaLicitacao = document.getElementById("horaAberturaLicitacao");
            const dataAberturaProposta = new DBInputDate(document.getElementById("dataAberturaProposta"));
            const horaAberturaProposta = document.getElementById("horaAberturaProposta");
            const dataEncerramentoProposta = new DBInputDate(document.getElementById("dataEncerramentoProposta"));
            const trDataHomologacao = document.getElementById('trDataHomologacao');
            const dataHomologacao = new DBInputDate(document.getElementById("dataHomologacao"));
            const horaEncerramentoProposta = document.getElementById("horaEncerramentoProposta");
            const origem = document.getElementById("origem");
            const modalidadeId = document.getElementById("modalidadeId");
            const modoDisputaId = document.getElementById("modoDisputaId");
            const btnConsultarLicitacao = document.getElementById("btnConsultarLicitacao");
            const btnConsultarSolicitacao = document.getElementById("btnConsultarSolicitacao");
            const trJustificativa = document.getElementById("trJustificativa");
            const tipoInstrumentoConvocatorioId = document.getElementById("tipoInstrumentoConvocatorioId");
            const sistemaRegistroPreco = document.getElementById("sistemaRegistroPreco");
            const infoComplementarText = document.getElementById("infoComplementarText");
            const amparoLegalId = document.getElementById("amparoLegalId");
            const trInformacaoComplementar = document.getElementById("trInformacaoComplementar");
            const tipoDocumento = document.getElementById('tipoDocumento');
            const documento = document.getElementById("documento");
            const linkOrigem = document.getElementById("linkOrigem");
            const infoComplementar = document.getElementById("infoComplementar");
            const tituloDocumento = document.getElementById("tituloDocumento");
            const anexoDocumento = document.getElementById("anexoDocumento");
            const frmAnexos = document.getElementById("anexo");
            const btnCarregarLicitacao = document.getElementById("btnCarregarLicitacao");
            const btnCarregarSolicitacao = document.getElementById("btnCarregarSolicitacao");
            const btnSalvarDados = document.getElementById("btnSalvarDados");
            const btnSalvarDadosSolicitacao = document.getElementById("btnSalvarDadosSolicitacao");
            const btnAnexarDocumentos = document.getElementById("btnAnexarDocumentos");
            const btnAlterar = document.getElementById("btnAlterar");
            const btnLancarAnexo = document.getElementById("btnLancarAnexo");
            const tabelaAnexos = jQuery('#table-anexos');
            const tabelaItensLicitacao = jQuery('#tabelaItensLicitacao');
            const tabelaItensSolicitacao = jQuery('#tabelaItensSolicitacao');
            const routes = {
                buscarEntidade: `${apiUrl}/patrimonial/pncp/unidades/buscarEntidade/`,
                buscarUnidadesAtivas: `${apiUrl}/patrimonial/pncp/unidades/buscarUnidadesAtivas/`,
                buscarAmparosLegais: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarAmparosLegais/`,
                buscarInstrumentoConvocatorio: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarInstrumentoConvocatorio/`,
                buscarModoDisputa: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarModoDisputa/`,
                verificaIntegracao: `${apiUrl}/patrimonial/pncp/integracao/verificaIntegracao/`,
                buscarLicitacao: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarLicitacao`,
                buscarSolicitacao: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarSolicitacao`,
                incluirCompraEditalAviso: `${apiUrl}/patrimonial/pncp/compraEditalAviso/incluirCompraEditalAviso`,
                alterarCompraEditalAviso: `${apiUrl}/patrimonial/pncp/compraEditalAviso/alterarCompraEditalAviso`,
                buscarEditas: `${apiUrl}/patrimonial/pncp/compraEditalAviso/buscarEditais`,
                incluirRespostaItem: `${apiUrl}/patrimonial/pncp/compraEditalAviso/incluirRespostaItem`,
            }
            let verificaBuscarDados = 0;
            let unidades = 0;

            origem.addEventListener('change', function () {
                limparFormulario();

                tabelaItensLicitacao.bootstrapTable('destroy');

                if (this.value === '3') {
                    alteraListaItens('solicitacao');

                    licitacaoRow.style.display = 'none';
                    solicitacaoRow.style.display = 'table-row';
                    btnSalvarDadosSolicitacao.style.display = 'inline-block';
                    btnSalvarDados.style.display = 'none';
                } else {
                    alteraListaItens('licitacao');

                    licitacaoRow.style.display = 'table-row';
                    solicitacaoRow.style.display = 'none';
                    btnSalvarDadosSolicitacao.style.display = 'none';
                    btnSalvarDados.style.display = 'inline-block';
                }
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

            btnLancarAnexo.addEventListener('click', () => {
                windowItens.destroy();
            });

            btnConsultarLicitacao.addEventListener('click', () => {
                if (licitacaoCodigo.value === "") {
                    alert('Selecione a licitação para consultar mais detalhes.');
                    return;
                }
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe',
                    'lic3_licitacao002.php?l20_codigo=' + licitacaoCodigo.value,
                    'Pesquisa',
                    true
                );
            });

            btnConsultarSolicitacao.addEventListener('click', () => {
                if (solicitacaoNumero.value === "") {
                    return alert('Selecione a solicitação para consultar mais detalhes.');
                }

                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_solicitacao',
                    'com3_conssolicitacao002.php?pc10_numero=' + solicitacaoNumero.value,
                    'Consulta Solicitação',
                    true
                );
            });

            btnAnexarDocumentos.addEventListener('click', () => {
                windowItens.show(0, 0, true);
            });

            btnCarregarLicitacao.addEventListener('click', carregarLicitacao);

            btnCarregarSolicitacao.addEventListener('click', carregarSolicitacao);

            btnSalvarDadosSolicitacao.addEventListener('click', () => {
                if (verificaBuscarDados === 0) {
                    btnSalvarDados.disabled = true;
                    return alert("Realize a busca de dados antes de enviar a Solicitação.");
                }

                if (!verificaCampos('solicitacao')) {
                    return false;
                }

                let itens = [];
                const itensSolicitacao = tabelaItensSolicitacao.bootstrapTable('getData');
                for (const item of itensSolicitacao) {
                    const numeroItem = item.pc11_seq.toString();
                    let descricao = item.solicitacao_processo_compra_material.processo_compra_material.pc01_descrmater
                    let tipoPessoaId;
                    let criterioJulgamentoOpcao;
                    let tipoBeneficioOpcao;
                    let selectIncentivoFiscalPPBOpcao;
                    let selectOrcamentoSigilosoOpcao;
                    let percentualMargemPreferenciaNormalOpcao;
                    let percentualMargemPreferenciaAdicionalOpcao;
                    let itemCategoriaId = 3;
                    let materialOuServico = 'M';
                    let unidadeMedida = 'UNIDADE';
                    let fornecedor = getDadosFornecedorItem(item);
                    const dadosFornecedor = {};
                    item.descricao = item.pc11_descr;

                    if (fornecedor) {
                        if (fornecedor.z01_cgccpf.length === 0) {
                            return alert('O fornecedor deve possuir cpf/cnpj para realizar o envio da contratação.')
                        }

                        tipoPessoaId = fornecedor.z01_cgccpf.trim().length === 14 ? 'PJ' : 'PF';
                        if (fornecedor.z01_nacion === 2) {
                            tipoPessoaId = 'PE';
                        }

                        dadosFornecedor.quantidadeHomologada = fornecedor.pc23_quant;
                        dadosFornecedor.valorUnitarioHomologado = fornecedor.pc23_vlrun;
                        dadosFornecedor.valorTotalHomologado = fornecedor.pc23_valor;
                        dadosFornecedor.tipoPessoaId = tipoPessoaId;
                        dadosFornecedor.niFornecedor = fornecedor.z01_cgccpf;
                        dadosFornecedor.nomeRazaoSocialFornecedor = fornecedor.z01_nome;
                        dadosFornecedor.percentualDesconto = fornecedor.pc23_percentualdesconto;
                        dadosFornecedor.porteFornecedorId = 5;
                        dadosFornecedor.numcgm = fornecedor.z01_numcgm;
                    }

                    if (item.item_unidade) {
                        unidadeMedida = item.item_unidade.material_unidade.m61_descr
                    }

                    if (modalidadeId.value === '1' || modalidadeId.value === '13') {
                        itemCategoriaId = item.itemCategoriaId;
                    }

                    document.getElementsByClassName('selectMaterial').forEach((material) => {
                        const numero = material.dataset.numero;
                        if (numero === numeroItem) {
                            materialOuServico = material.value;
                        }
                    });
                    document.getElementsByClassName('selectCriterioJulgamento').forEach((criterioJulgamento) => {
                        const numero = criterioJulgamento.dataset.numero;
                        if (numero === numeroItem) {
                            criterioJulgamentoOpcao = criterioJulgamento.value;
                        }
                    });
                    document.getElementsByClassName('selectTipoBeneficio').forEach((tipoBeneficio) => {
                        const numero = tipoBeneficio.dataset.numero;
                        if (numero === numeroItem) {
                            tipoBeneficioOpcao = tipoBeneficio.value;
                        }
                    });
                    document.getElementsByClassName('selectIncentivoFiscalPPB').forEach((selectIncentivoFiscalPPB) => {
                        const numero = selectIncentivoFiscalPPB.dataset.numero;
                        if (numero === numeroItem) {
                            selectIncentivoFiscalPPBOpcao = selectIncentivoFiscalPPB.value;
                        }
                    });
                    document.getElementsByClassName('selectOrcamentoSigiloso').forEach((selectOrcamentoSigiloso) => {
                        const numero = selectOrcamentoSigiloso.dataset.numero;
                        if (numero === numeroItem) {
                            selectOrcamentoSigilosoOpcao = selectOrcamentoSigiloso.value === 'true';
                        }
                    });
                    document.getElementsByClassName('selectPercentualMargemPreferenciaNormal').forEach((percentualMargemPreferenciaNormal) => {
                        const numero = percentualMargemPreferenciaNormal.dataset.numero;
                        if (numero === numeroItem) {
                            percentualMargemPreferenciaNormalOpcao = percentualMargemPreferenciaNormal.value;
                        }
                    });
                    document.getElementsByClassName('selectPercentualMargemPreferenciaAdicional').forEach((percentualMargemPreferenciaAdicional) => {
                        const numero = percentualMargemPreferenciaAdicional.dataset.numero;
                        if (numero === numeroItem) {
                            percentualMargemPreferenciaAdicionalOpcao = percentualMargemPreferenciaAdicional.value;
                        }
                    });

                    let dadosItens = {};
                    if (percentualMargemPreferenciaNormalOpcao > 0) {
                        dadosItens.percentualMargemPreferenciaNormal = percentualMargemPreferenciaNormalOpcao.replace(',', '.');
                    }

                    if (percentualMargemPreferenciaAdicionalOpcao > 0) {
                        dadosItens.percentualMargemPreferenciaAdicional = percentualMargemPreferenciaAdicionalOpcao.replace(',', '.');
                    }

                    dadosItens.numeroItem = item.pc11_seq;
                    dadosItens.aplicabilidadeMargemPreferenciaNormal = percentualMargemPreferenciaNormalOpcao ? true : false;
                    dadosItens.aplicabilidadeMargemPreferenciaAdicional = percentualMargemPreferenciaAdicionalOpcao ? true : false;
                    dadosItens.materialOuServico = materialOuServico;
                    dadosItens.criterioJulgamentoId = item.criterioJulgamentoId ?? criterioJulgamentoOpcao;
                    dadosItens.indicadorSubcontratacao = item.indicadorSubcontratacao;
                    dadosItens.descricao = descricao;
                    dadosItens.quantidade = item.pc11_quant;
                    dadosItens.unidadeMedida = unidadeMedida;
                    dadosItens.valorUnitarioEstimado = item.pc11_vlrun;
                    dadosItens.dadosFornecedor = dadosFornecedor;
                    dadosItens.situacao = 'Homologada';
                    dadosItens.itemCategoriaId = itemCategoriaId;
                    dadosItens.dataHomologacao = js_formatar(dataHomologacao.__toLocaleDateString(), 'd')
                        .split('-')
                        .reverse()
                        .join('/');
                    dadosItens.codigoRegistroImobiliario = item.codigoRegistroImobiliario;
                    dadosItens.valorTotal = item.valor_total;
                    dadosItens.tipoBeneficioId = tipoBeneficioOpcao;
                    dadosItens.incentivoProdutivoBasico = selectIncentivoFiscalPPBOpcao;
                    dadosItens.orcamentoSigiloso = selectOrcamentoSigilosoOpcao;

                    itens.push(dadosItens);
                }

                itens.forEach((item) => {
                    if (item.itemCategoriaId !== '1') {
                        delete item.codigoRegistroImobiliario;
                    }
                });
                itens = JSON.stringify(itens);

                const formData = new FormData(frmAnexos);
                formData.append('licitacao', licitacaoCodigo.value);
                formData.append('anoCompra', anoCompra.value);
                formData.append('itensCompra', itens);
                formData.append('unidadeCompradora', codigoUnidade.value);
                formData.append('instrumentoConvocatorio', tipoInstrumentoConvocatorioId.value);
                formData.append('modalidade', modalidadeId.value);
                formData.append('modoDisputa', modoDisputaId.value);
                formData.append('numeroCompra', numeroCompra.value);
                formData.append('numeroProcesso', numeroProcesso.value);
                formData.append('objetoCompra', objetoCompra.value);
                if (infoComplementar.value === '0') {
                    formData.append('informacaoComplementar', infoComplementarText.value);
                }
                formData.append('amparoLegal', amparoLegalId.value);
                formData.append('srp', sistemaRegistroPreco.value);
                formData.append('dataAberturaProposta', js_formatar(dataAberturaProposta.__toLocaleDateString(), 'd'));
                formData.append('horaAberturaProposta', horaAberturaProposta.value);
                if (tipoInstrumentoConvocatorioId.value !== '3') {
                    formData.append('dataEncerramentoProposta', js_formatar(
                        dataEncerramentoProposta.__toLocaleDateString(),
                        'd'
                    ));
                    formData.append('horaEncerramentoProposta', horaEncerramentoProposta.value);
                }
                formData.append('cnpj', documento.value);
                formData.append('justificativaPresencial', justificativaPresencial.value);
                formData.append('linkSistemaOrigem', linkOrigem.value);
                formData.append('solicitacao', solicitacaoNumero.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.incluirCompraEditalAviso, { body: formData }).then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    let link = `<a target="_blank" href="${response.data}">Clique aqui para acessar</a>`;
                    let msgCompra = `Publicação de Contratação/Edital/Aviso efetuada com sucesso no PNCP!`;
                    let erroResposta = `Não foi possível enviar o(s) Resultado(s)`;
                    HttpClient.post(routes.incluirRespostaItem, { body: formData }).then(response => {
                        if (response.error) {
                            alert(`${msgCompra}\n${link}\n\n${erroResposta}\nMotivo: ${response.message}`);
                            return limparFormulario(true);

                        }
                        alert(`${msgCompra}\n ${link}`);
                        limparFormulario(true);
                    });
                });
            });

            btnSalvarDados.addEventListener('click', () => {
                if (verificaBuscarDados === 0) {
                    btnSalvarDados.disabled = true;
                    return alert("Realize a busca de dados antes de enviar a Licitação.");
                }

                if (!verificaCampos()) {
                    return false;
                }

                let itens = [];

                for (const item of tabelaItensLicitacao.bootstrapTable('getData')) {
                    let descricao = item.descricao
                    let materialOuServico;
                    let tipoBeneficioOpcao;
                    let selectIncentivoFiscalPPBOpcao;
                    let selectOrcamentoSigilosoOpcao;
                    let percentualMargemPreferenciaNormalOpcao;
                    let percentualMargemPreferenciaAdicionalOpcao;
                    let itemCategoriaId = 3;
                    if (item.descricao !== item.resumo && item.resumo !== "") {
                        descricao = `${item.descricao} - ${item.resumo}`
                    }

                    document.getElementsByClassName('selectMaterial').forEach((material) => {
                        const numero = material.dataset.numero;
                        if (numero === item.numeroItem) {
                            materialOuServico = material.value;
                        }
                    });
                    document.getElementsByClassName('selectTipoBeneficio').forEach((tipoBeneficio) => {
                        const numero = tipoBeneficio.dataset.numero;
                        if (numero === item.numeroItem) {
                            tipoBeneficioOpcao = tipoBeneficio.value;
                        }
                    });
                    document.getElementsByClassName('selectIncentivoFiscalPPB').forEach((selectIncentivoFiscalPPB) => {
                        const numero = selectIncentivoFiscalPPB.dataset.numero;
                        if (numero === item.numeroItem) {
                            selectIncentivoFiscalPPBOpcao = selectIncentivoFiscalPPB.value;
                        }
                    });
                    document.getElementsByClassName('selectOrcamentoSigiloso').forEach((selectOrcamentoSigiloso) => {
                        const numero = selectOrcamentoSigiloso.dataset.numero;
                        if (numero === item.numeroItem) {
                            selectOrcamentoSigilosoOpcao = selectOrcamentoSigiloso.value === 'true';
                        }
                    });
                    document.getElementsByClassName('selectCriterioJulgamento').forEach((selectCriterioJulgamento) => {
                        const numero = selectCriterioJulgamento.dataset.numero;
                        if (numero === item.numeroItem) {
                            selectCriterioJulgamentoOpcao = selectCriterioJulgamento.value;
                        }
                    });

                    document.getElementsByClassName('selectPercentualMargemPreferenciaNormal').forEach((percentualMargemPreferenciaNormal) => {
                        const numero = percentualMargemPreferenciaNormal.dataset.numero;

                        if (numero === item.numeroItem) {
                            percentualMargemPreferenciaNormalOpcao = percentualMargemPreferenciaNormal.value;
                        }
                    });

                    document.getElementsByClassName('selectPercentualMargemPreferenciaAdicional').forEach((percentualMargemPreferenciaAdicional) => {
                        const numero = percentualMargemPreferenciaAdicional.dataset.numero;
                        if (numero === item.numeroItem) {
                            percentualMargemPreferenciaAdicionalOpcao = percentualMargemPreferenciaAdicional.value;
                        }
                    });

                    if (modalidadeId.value === '1' || modalidadeId.value === '13') {
                        itemCategoriaId = item.itemCategoriaId;
                    }

                    let dadosItens = {};
                    if (percentualMargemPreferenciaNormalOpcao > 0) {
                        dadosItens.percentualMargemPreferenciaNormal = percentualMargemPreferenciaNormalOpcao.replace(',', '.');
                    }

                    if (percentualMargemPreferenciaAdicionalOpcao > 0) {
                        dadosItens.percentualMargemPreferenciaAdicional = percentualMargemPreferenciaAdicionalOpcao.replace(',', '.');
                    }

                    dadosItens.numeroItem = item.numeroItem;
                    dadosItens.aplicabilidadeMargemPreferenciaNormal = percentualMargemPreferenciaNormalOpcao ? true : false;
                    dadosItens.aplicabilidadeMargemPreferenciaAdicional = percentualMargemPreferenciaAdicionalOpcao ? true : false;
                    dadosItens.materialOuServico = materialOuServico;
                    dadosItens.tipoBeneficioId = tipoBeneficioOpcao;
                    dadosItens.criterioJulgamentoId = selectCriterioJulgamentoOpcao;
                    dadosItens.indicadorSubcontratacao = item.indicadorSubcontratacao;
                    dadosItens.incentivoProdutivoBasico = selectIncentivoFiscalPPBOpcao;
                    dadosItens.descricao = descricao;
                    dadosItens.quantidade = item.quantidade;
                    dadosItens.unidadeMedida = item.unidadeMedida;
                    dadosItens.valorUnitarioEstimado = item.valorUnitarioEstimado;
                    dadosItens.valorTotal = item.valorTotalEstimado;
                    dadosItens.dadosFornecedor = item.dadosFornecedor;
                    dadosItens.situacao = item.situacao;
                    dadosItens.orcamentoSigiloso = selectOrcamentoSigilosoOpcao;
                    dadosItens.itemCategoriaId = itemCategoriaId;
                    dadosItens.dataHomologacao = item.dataHomologacao;
                    dadosItens.codigoRegistroImobiliario = item.codigoRegistroImobiliario;


                    itens.push(dadosItens);
                }
                itens.forEach((item) => {
                    if (item.itemCategoriaId !== '1') {
                        delete item.codigoRegistroImobiliario;
                    }
                });

                itens = JSON.stringify(itens);

                const formData = new FormData(frmAnexos);
                formData.append('licitacao', licitacaoCodigo.value);
                formData.append('anoCompra', anoCompra.value);
                formData.append('itensCompra', itens);
                formData.append('unidadeCompradora', codigoUnidade.value);
                formData.append('instrumentoConvocatorio', tipoInstrumentoConvocatorioId.value);
                formData.append('modalidade', modalidadeId.value);
                formData.append('modoDisputa', modoDisputaId.value);
                formData.append('numeroCompra', numeroCompra.value);
                formData.append('numeroProcesso', numeroProcesso.value);
                formData.append('objetoCompra', objetoCompra.value);
                if (infoComplementar.value === '0') {
                    formData.append('informacaoComplementar', infoComplementarText.value);
                }
                formData.append('amparoLegal', amparoLegalId.value);
                formData.append('srp', sistemaRegistroPreco.value);
                formData.append('dataAberturaProposta', js_formatar(dataAberturaProposta.__toLocaleDateString(), 'd'));
                formData.append('horaAberturaProposta', horaAberturaProposta.value);
                if (tipoInstrumentoConvocatorioId.value !== '3') {
                    formData.append('dataEncerramentoProposta', js_formatar(
                        dataEncerramentoProposta.__toLocaleDateString(),
                        'd'
                    ));
                    formData.append('horaEncerramentoProposta', horaEncerramentoProposta.value);
                }
                formData.append('justificativaPresencial', justificativaPresencial.value);
                formData.append('linkSistemaOrigem', linkOrigem.value);
                formData.append('cnpj', documento.value);

                PHPSession.appendFormData(formData);

                HttpClient.post(routes.incluirCompraEditalAviso, { body: formData }).then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    let link = `<a target="_blank" href="${response.data}">Clique aqui para acessar</a>`;
                    let evento = `Um evento automático foi criado para esta ação.`;
                    let msgCompra = `Publicação de Contratação/Edital/Aviso efetuada com sucesso no PNCP!`;
                    let erroResposta = `Não foi possível enviar o(s) Resultado(s)`;
                    let erroFinal = `Para um novo envio com o(s) resultado(s), acesse:\nContratação/Edital/Aviso > Inserir Resultado`;

                    HttpClient.post(routes.incluirRespostaItem, { body: formData }).then(response => {
                        if (response.error) {
                            alert(`${msgCompra}\n${link}\n\n${evento}\n\n${erroResposta}\nMotivo: ${response.message}\n\n${erroFinal}`);
                            return limparFormulario(true);
                        }
                        alert(`${msgCompra}\n ${link}\n\n${evento}`);
                        limparFormulario(true);
                    });
                });
            });

            btnAlterar.addEventListener('click', () => {
                if (!sequencialCompra.value || sequencialCompra.value === '') {
                    return alert('Realize a busca da compra para alterar.\n Caso a compra não se encontre no PNCP não é possível realizar a alteração.');
                }

                if (!anoCompra.value || anoCompra.value === '') {
                    return alert('O ano da compra é obrigatório.')
                }

                if (!documento.value || documento.value === '') {
                    return alert('O cnpj da compra é obrigatório.')
                }

                const formData = new FormData(frmAnexos);
                formData.append('licitacao', licitacaoCodigo.value);
                formData.append('anoCompra', anoCompra.value);
                formData.append('unidadeCompradora', codigoUnidade.value);
                formData.append('instrumentoConvocatorio', tipoInstrumentoConvocatorioId.value);
                formData.append('modalidade', modalidadeId.value);
                formData.append('modoDisputa', modoDisputaId.value);
                formData.append('numeroCompra', numeroCompra.value);
                formData.append('sequencialCompra', sequencialCompra.value);
                formData.append('numeroProcesso', numeroProcesso.value);
                formData.append('objetoCompra', objetoCompra.value);

                if (situacaoLicitacao.value !== '0' && situacaoLicitacao.value !== '') {
                    formData.append('situacaoLicitacao', situacaoLicitacao.value);
                }

                if (infoComplementar.value === '0') {
                    formData.append('informacaoComplementar', infoComplementarText.value);
                }

                formData.append('amparoLegal', amparoLegalId.value);
                formData.append('srp', sistemaRegistroPreco.value);
                formData.append('dataAberturaProposta', js_formatar(dataAberturaProposta.__toLocaleDateString(), 'd'));
                formData.append('horaAberturaProposta', horaAberturaProposta.value);

                if (tipoInstrumentoConvocatorioId.value !== '3') {
                    if (dataEncerramentoProposta.value !== '' && dataEncerramentoProposta.value !== null) {
                        let dataEncerramento = js_formatar(dataEncerramentoProposta.__toLocaleDateString(), 'd');
                        formData.append('dataEncerramentoProposta', dataEncerramento);
                        formData.append('horaEncerramentoProposta', horaEncerramentoProposta.value);
                    }
                }

                formData.append('justificativaPresencial', justificativaPresencial.value);
                formData.append('linkSistemaOrigem', linkOrigem.value);
                formData.append('cnpj', documento.value);

                PHPSession.appendFormData(formData);
                HttpClient.post(routes.alterarCompraEditalAviso, { body: formData }).then(response => {
                    if (response.error) {
                        return alert(response.message);
                    }

                    alert(`Compra atualizada com sucesso!`);
                    limparFormulario(true);
                });
            });

            modalidadeId.addEventListener('change', async () => {
                const itens = tabelaItensLicitacao.bootstrapTable('getData');
                const selectsMaterial = document.getElementsByClassName('selectMaterial');
                const selectsOrcamentoSigiloso = document.getElementsByClassName('selectOrcamentoSigiloso');
                const selectsTipoBeneficio = document.getElementsByClassName('selectTipoBeneficio');
                const selectsIncentivoFiscalPPB = document.getElementsByClassName('selectIncentivoFiscalPPB');
                const selectsItemCategoriaId = document.getElementsByClassName('selectItemCategoriaId');
                const formData = new FormData();
                const ATO_AUTORIZA_CONTRATACAO_DIRETA = 3;

                if (origem.value === '2') {
                    await carregarLicitacao();
                }

                for (const selectItemCategoria of selectsItemCategoriaId) {
                    selectItemCategoria.options.length = 0;
                    buildSelectCategoria(selectItemCategoria, modalidadeId.value);
                }

                justificativaPresencial.value = '';
                trJustificativa.hidden = true;
                trJustificativa.hidden = !(
                    modalidadeId.value === '5' ||
                    modalidadeId.value === '7' ||
                    modalidadeId.value === '13'
                );

                if (modalidadeId.value === '1' || modalidadeId.value === '13') {
                    sistemaRegistroPreco.value = 'false';
                    sistemaRegistroPreco.disabled = true;
                    selectsOrcamentoSigiloso.forEach((selectOrcamentoSigiloso) => {
                        const numero = selectOrcamentoSigiloso.dataset.numero;
                        itens.map((item) => {
                            if (item.numeroItem === numero) {
                                selectOrcamentoSigiloso.value = 'false';
                                selectOrcamentoSigiloso.disabled = true;
                            }
                        })
                    });
                    selectsMaterial.forEach((selectMaterial) => {
                        const numero = selectMaterial.dataset.numero;
                        itens.map((item) => {
                            if (item.numeroItem === numero) {
                                selectMaterial.value = "M";
                                selectMaterial.disabled = true;
                            }
                        })
                    });
                    selectsTipoBeneficio.forEach((selectTipoBeneficio) => {
                        itens.map((item) => {
                            const numero = selectTipoBeneficio.dataset.numero;
                            if (item.numeroItem === numero) {
                                selectTipoBeneficio.value = '5';
                                selectTipoBeneficio.disabled = true;

                            }
                        })
                    });
                    selectsIncentivoFiscalPPB.forEach((selectIncentivoFiscalPPB) => {
                        const numero = selectIncentivoFiscalPPB.dataset.numero;
                        itens.map((item) => {
                            if (item.numeroItem === numero) {
                                selectIncentivoFiscalPPB.value = 'false';
                                selectIncentivoFiscalPPB.disabled = true;
                            }
                        })
                    });
                }

                formData.append('modalidadeCompra', modalidadeId.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.buscarInstrumentoConvocatorio, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message)
                        return;
                    }
                    deleteChild(amparoLegalId);
                    let select = document.createElement('option');
                    select.value = 0;
                    let selectDescricao = document.createTextNode('Selecione');
                    select.appendChild(selectDescricao);
                    amparoLegalId.appendChild(select);
                    deleteChild(tipoInstrumentoConvocatorioId);
                    mostrarInstrumentoConvocatorio(response.data);
                    trDataAberturaLicicacao.hidden = false;
                    trDataAberturaProposta.hidden = false;
                    trDataEncerramentoProposta.hidden = false;
                    dataEncerramentoProposta.value = '';
                    horaEncerramentoProposta.value = '';

                    if (response.data[1] === undefined) {
                        return;
                    }

                    if (response.data[1].codigo === 3) {
                        trDataAberturaLicicacao.hidden = true;
                        trDataAberturaProposta.hidden = true;
                        trDataEncerramentoProposta.hidden = true;
                    }

                    tipoInstrumentoConvocatorioId.disabled = false;
                    if (response.data.length === 2) {
                        if (response.data[1].codigo === ATO_AUTORIZA_CONTRATACAO_DIRETA) {
                            tipoInstrumentoConvocatorioId.value = "3";
                        } else {
                            tipoInstrumentoConvocatorioId.value = "1";
                        }
                        tipoInstrumentoConvocatorioId.dispatchEvent(new Event('change'));
                        tipoInstrumentoConvocatorioId.disabled = true;
                    }

                    for (let i = 0; i <= tipoInstrumentoConvocatorioId.options.length; i++) {
                        if (tipoInstrumentoConvocatorioId.options[i] !== undefined) {
                            if (tipoInstrumentoConvocatorioId.options[i].value === "0") {
                                tipoInstrumentoConvocatorioId.options[i].disabled = true;
                            }
                        }
                    }

                    configurarCriterioJulgamento();
                })
            })

            tipoInstrumentoConvocatorioId.addEventListener('change', () => {
                const formData = new FormData();
                if (tipoInstrumentoConvocatorioId.value === '0') {
                    return false;
                }
                formData.append('modalidadeCompra', modalidadeId.value);
                formData.append('instrumentoConvocatorio', tipoInstrumentoConvocatorioId.value);
                PHPSession.appendFormData(formData);
                trDataAberturaLicicacao.hidden = false;
                trDataAberturaProposta.hidden = false;
                trDataEncerramentoProposta.hidden = false;
                dataEncerramentoProposta.value = '';
                horaEncerramentoProposta.value = '';
                if (tipoInstrumentoConvocatorioId.value === '3') {
                    trDataAberturaLicicacao.hidden = true;
                    trDataAberturaProposta.hidden = true;
                    trDataEncerramentoProposta.hidden = true;
                }
                HttpClient.post(routes.buscarAmparosLegais, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message)
                        return;
                    }

                    amparoLegalId.disabled = response.data.length === 1;
                    deleteChild(amparoLegalId);
                    mostrarAmparosLegais(response.data);
                });
                HttpClient.post(routes.buscarModoDisputa, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message)
                        return;
                    }

                    modoDisputaId.disabled = response.data.length === 1;
                    deleteChild(modoDisputaId);
                    mostrarModosDipusta(response.data);

                    for (let i = 0; i <= modoDisputaId.options.length; i++) {
                        if (modoDisputaId.options[i] !== undefined) {
                            if (modoDisputaId.options[i].value === "0") {
                                modoDisputaId.options[i].disabled = true;
                            }
                        }
                    }
                })
            })

            infoComplementar.addEventListener('change', () => {
                trInformacaoComplementar.style.display = 'none'
                infoComplementarText.value = '';
                if (infoComplementar.value === '0') {
                    trInformacaoComplementar.style.display = ''
                }
            })

            function configurarCriterioJulgamento() {
                const criteriosJulgamento = document.getElementsByClassName('selectCriterioJulgamento');
                const itens = tabelaItensLicitacao.bootstrapTable('getData');
                criteriosJulgamento.forEach((criterios) => {
                    criterios.options.forEach((option) => {
                        criterios.disabled = false;
                        let julgamentosPermitidos = [];
                        if (modalidadeId.value === '1' || modalidadeId.value === '13') {
                            option.disabled = !(option.value === '5');
                            option.selected = option.value === '5';
                            criterios.disabled = true;
                        }

                        if (modalidadeId.value === '2') {
                            julgamentosPermitidos = ['1', '2', '5', '6', '8', '9'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }

                        if (modalidadeId.value === '3') {
                            julgamentosPermitidos = ['8', '9'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }

                        if (modalidadeId.value === '4' || modalidadeId.value === '5') {
                            julgamentosPermitidos = ['1', '2', '4', '5', '6', '8', '9'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }

                        if (modalidadeId.value === '6' || modalidadeId.value === '7') {
                            julgamentosPermitidos = ['1', '2', '5'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }

                        if (modalidadeId.value === '8') {
                            julgamentosPermitidos = ['1', '2', '5', '7'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }

                        if (['9', '10', '11', '12'].includes(modalidadeId.value)) {
                            option.disabled = !(option.value === '7');
                            option.selected = option.value === '7';
                            criterios.disabled = true;
                        }

                        if (modalidadeId.value === '14') {
                            julgamentosPermitidos = ['1', '2', '4', '5', '6', '8', '9'];
                            option.disabled = !julgamentosPermitidos.includes(option.value);
                        }
                    })
                });
            }

            function getDadosFornecedorItem(item) {
                if (!item.processo_compra_item) {
                    return [];
                }

                let fornecedor = item.processo_compra_item.orcamento_item_processo;
                if (fornecedor === null) {
                    return [];
                }

                return fornecedor.orcamento_item.julgamento_vencedor;
            }

            function alteraListaItens(origem) {
                let opcoesTabela = {
                    locale: 'pt-BR',
                    height: 350,
                    class: "table table-sm",
                    detailView: true
                }

                if (origem === 'solicitacao') {
                    tabelaItensLicitacao.bootstrapTable('destroy');
                    tabelaItensLicitacao.css('display', 'none');
                    tabelaItensSolicitacao.css('display', 'table');
                    trDataHomologacao.style.display = 'table-row'
                    modalidadeId.options.forEach(function (el) {
                        el.disabled = el.value !== "8" && el.value !== "9";
                        el.disabled = el.value !== "9" && el.value !== "8";
                    });

                    opcoesTabela.detailFormatter = formatterFornecedorSolicitacao;
                    opcoesTabela.columns = [
                        {
                            field: 'pc11_seq',
                            title: 'Nº'
                        },
                        {
                            field: 'solicitacao_processo_compra_material.processo_compra_material.pc01_servico',
                            title: 'Material/Serviço',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterMaterial,
                            events: operateEvents
                        },
                        {
                            field: 'tipoBeneficioId',
                            title: 'Tipo Benefício',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterTipoBeneficio,
                            events: operateEvents
                        },
                        {
                            field: 'incentivoProdutivoBasico',
                            title: 'Incentivo Fiscal PPB',
                            halign: 'center',
                            align: 'center',
                            formatter: formmaterIncentivoFiscalPPB,
                            events: operateEvents
                        },
                        {
                            field: 'solicitacao_processo_compra_material.processo_compra_material.pc01_descrmater',
                            title: 'Descrição',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'pc11_quant',
                            title: 'Quantidade',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'item_unidade.material_unidade.m61_descr',
                            title: 'Unidade de medida',
                            halign: 'center',
                            align: 'center',
                            formatter: (value) => value ? value : 'UNIDADE'
                        },
                        {
                            field: 'pc11_vlrun',
                            title: 'Vlr unit. estimado',
                            halign: 'center',
                            align: 'center',
                            formatter: (value, row) => {
                                return value.replace('.', ',');
                            }
                        },
                        {
                            field: 'valor_total',
                            title: 'Valor Total',
                            halign: 'center',
                            align: 'center'
                        },
                        {
                            field: 'criterioJulgamentoId',
                            title: 'Critério Julgamento',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterCriterioJulgamento,
                            events: operateEvents
                        },
                        {
                            field: 'itemCategoriaId',
                            title: 'Categoria Item',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterItemCategoriaId,
                            events: operateEvents
                        },
                        {
                            field: 'codigoRegistroImobiliario',
                            title: 'Registro Imobiliario',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterCodigoRegistroImobiliario,
                            events: operateEvents
                        },
                        {
                            field: 'orcamentoSigiloso',
                            title: 'Orçamento Sigiloso',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterOrcamentoSigiloso,
                            events: operateEvents
                        },
                        {
                            field: 'indicadorSubcontratacao',
                            title: 'Indicador sub-contratação',
                            visible: false,
                            halign: 'center',
                            align: 'center',
                            formatter: formatterIndicadorSubContratacao,
                            events: operateEvents
                        },
                        {
                            field: 'percentualMargemPreferenciaNormal',
                            title: '% Margem Pref. Normal',
                            formatter: formatterPercentualMargemPreferenciaNormal,
                        },
                        {
                            field: 'percentualMargemPreferenciaAdicional',
                            title: '% Margem Pref. Adicional',
                            formatter: formatterPercentualMargemPreferenciaAdicional,
                        },
                    ];
                    tabelaItensSolicitacao.bootstrapTable(opcoesTabela);
                }

                if (origem === 'licitacao') {
                    tabelaItensSolicitacao.bootstrapTable('destroy');
                    tabelaItensSolicitacao.css('display', 'none');
                    tabelaItensLicitacao.css('display', 'table');
                    trDataHomologacao.style.display = 'none'
                    modalidadeId.options.forEach(function (el) {
                        el.disabled = false;
                    });

                    opcoesTabela.detailFormatter = detailFormatter;
                    opcoesTabela.columns = [
                        {
                            field: 'numeroItem',
                            title: 'Nº',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'materialOuServico',
                            title: 'Material/Serviço',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterMaterial,
                            events: operateEvents
                        },
                        {
                            field: 'tipoBeneficioId',
                            title: 'Tipo Benefício',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterTipoBeneficio,
                            events: operateEvents
                        },
                        {
                            field: 'incentivoProdutivoBasico',
                            title: 'Incentivo Fiscal PPB',
                            halign: 'center',
                            align: 'center',
                            formatter: formmaterIncentivoFiscalPPB,
                            events: operateEvents
                        },
                        {
                            field: 'descricao',
                            title: 'Descrição',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'quantidade',
                            title: 'Quantidade',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'unidadeMedida',
                            title: 'Unidade de medida',
                            halign: 'center',
                            align: 'center',
                        },
                        {
                            field: 'valorUnitarioEstimado',
                            title: 'Vlr unit. estimado',
                            halign: 'center',
                            align: 'center',
                            formatter: (value, row) => {
                                return value.replace('.', ',');
                            }
                        },
                        {
                            field: 'valorTotalEstimado',
                            title: 'Valor Total',
                            halign: 'center',
                            align: 'center',
                            formatter: (value, row) => {
                                return value.replace('.', ',');
                            }
                        },
                        {
                            field: 'criterioJulgamentoId',
                            title: 'Critério Julgamento',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterCriterioJulgamento,
                            events: operateEvents
                        },
                        {
                            field: 'itemCategoriaId',
                            title: 'Categoria Item',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterItemCategoriaId,
                            events: operateEvents
                        },
                        {
                            field: 'codigoRegistroImobiliario',
                            title: 'Registro Imobiliario',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterCodigoRegistroImobiliario,
                            events: operateEvents
                        },
                        {
                            field: 'orcamentoSigiloso',
                            title: 'Orçamento Sigiloso',
                            halign: 'center',
                            align: 'center',
                            formatter: formatterOrcamentoSigiloso,
                            events: operateEvents
                        },
                        {
                            field: 'indicadorSubcontratacao',
                            title: 'Indicador sub-contratação',
                            visible: false,
                            halign: 'center',
                            align: 'center',
                            formatter: formatterIndicadorSubContratacao,
                            events: operateEvents
                        },
                        {
                            field: 'percentualMargemPreferenciaNormal',
                            title: '% Margem Pref. Normal',
                            formatter: formatterPercentualMargemPreferenciaNormal,
                        },
                        {
                            field: 'percentualMargemPreferenciaAdicional',
                            title: '% Margem Pref. Adicional',
                            formatter: formatterPercentualMargemPreferenciaAdicional,
                        },
                    ];
                    opcoesTabela.onPostBody = (data) => {
                        const selectsMaterial = document.getElementsByClassName('selectMaterial');
                        const selectsOrcamentoSigiloso = document.getElementsByClassName('selectOrcamentoSigiloso');
                        const selectsCriterioJulgamento = document.getElementsByClassName('selectCriterioJulgamento');
                        const selectsTipoBeneficio = document.getElementsByClassName('selectTipoBeneficio');
                        const selectsIncentivoFiscalPPB = document.getElementsByClassName('selectIncentivoFiscalPPB');
                        const selectsSubcontratacao = document.getElementsByClassName('selectIndicadorSubContratacao');
                        const selectsItemCategoriaId = document.getElementsByClassName('selectItemCategoriaId');
                        selectsSubcontratacao.forEach((selectSubcontratacao) => {
                            const numero = selectSubcontratacao.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectSubcontratacao.value = item.indicadorSubcontratacao;
                                }
                            })
                        });
                        selectsItemCategoriaId.forEach((selectItemCategoriaId) => {
                            const numero = selectItemCategoriaId.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectItemCategoriaId.value = item.itemCategoriaId;
                                }
                            })
                        });
                        selectsMaterial.forEach((selectMaterial) => {
                            const numero = selectMaterial.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectMaterial.value = item.materialOuServico;
                                }
                            })
                        });
                        selectsOrcamentoSigiloso.forEach((selectOrcamentoSigiloso) => {
                            const numero = selectOrcamentoSigiloso.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectOrcamentoSigiloso.value = item.orcamentoSigiloso;
                                }
                            })
                        });
                        selectsCriterioJulgamento.forEach((selectCriterioJulgamento) => {
                            const numero = selectCriterioJulgamento.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectCriterioJulgamento.value = item.criterioJulgamentoId
                                }
                            })
                        });
                        selectsTipoBeneficio.forEach((selectTipoBeneficio) => {
                            const numero = selectTipoBeneficio.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectTipoBeneficio.value = item.tipoBeneficioId
                                }
                            })
                        });
                        selectsIncentivoFiscalPPB.forEach((selectIncentivoFiscalPPB) => {
                            const numero = selectIncentivoFiscalPPB.dataset.numero;
                            data.map((item) => {
                                if (item.numeroItem === numero) {
                                    selectIncentivoFiscalPPB.value = item.incentivoProdutivoBasico
                                }
                            })
                        });
                    }
                    tabelaItensLicitacao.bootstrapTable(opcoesTabela);
                }
            }

            function verificaCampos(tipo = 'licitacao') {
                if (licitacaoCodigo.value === '' && tipo === 'licitacao') {
                    alert('Código da licitação não pode ser vazio.');
                    return false;
                }

                if (tipo === 'solicitacao') {
                    if (solicitacaoNumero.value === '') {
                        alert('Código da solicitacao não pode ser vazio.');
                        return false;
                    }

                    if (dataHomologacao.value === null) {
                        alert('Data de homologação não pode ser vazia.');
                        return false;
                    }
                }

                if (modalidadeId.value === '0') {
                    alert('Selecione a Modalidade da Compra.');
                    return false;
                }
                if (tipoInstrumentoConvocatorioId.value === '0') {
                    alert('Selecione o Instrumento Convocatório.');
                    return false;
                }
                if (modoDisputaId.value === '0') {
                    alert('Selecione o Modo de Disputa.');
                    return false;
                }
                if (numeroCompra.value === '') {
                    alert('Número da compra não pode ser vazio.');
                    return false;
                }
                if (anoCompra.value === '') {
                    alert('Ano da compra não pode ser vazio.');
                    return false;
                }
                if (numeroProcesso.value === '') {
                    alert('Número do processo não pode ser vazio.');
                    return false;
                }
                if (objetoCompra.value === '') {
                    alert('Objeto da compra não pode ser vazio.');
                    return false;
                }
                if (sistemaRegistroPreco.value === '') {
                    alert('SRP não pode ser vazio.');
                    return false;
                }

                if (dataAberturaProposta.value === null && tipoInstrumentoConvocatorioId.value !== '3') {
                    alert('Preencha a Data de Abertura da Proposta.');
                    return false;
                }

                if (horaAberturaProposta.value === "" && tipoInstrumentoConvocatorioId.value !== '3') {
                    alert('Preencha a Hora de Abertura da Proposta.');
                    return false;
                }

                if (dataEncerramentoProposta.value === null && tipoInstrumentoConvocatorioId.value !== '3') {
                    alert('Preencha a Data de Encerramento da Proposta.');
                    return false;
                }

                if (horaEncerramentoProposta.value === "" && tipoInstrumentoConvocatorioId.value !== '3') {
                    alert('Preencha a Hora de Encerramento da Proposta.');
                    return false;
                }
                if (amparoLegalId.value === '0') {
                    alert('Selecione o Amparo Legal.');
                    return false;
                }
                if (infoComplementar.value === "0" && infoComplementarText.value === "") {
                    alert("Preencha a informação complementar.");
                    return false;
                }
                if ((modalidadeId.value === '5' && justificativaPresencial.value === '')
                    || (modalidadeId.value === '7' && justificativaPresencial.value === '')
                    || (modalidadeId.value === '13' && justificativaPresencial.value === '')) {
                    alert('Preencha a justificativa presencial.');
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

            function verificaUnidadeLicitacao() {
                if (codigoUnidade.value === "0") {
                    alert('Selecione a Unidade Compradora.')
                    return false;
                }

                if (licitacaoCodigo.value === '') {
                    alert('Selecione a licitação.');
                    return false;
                }
                return true;
            }

            function getExtension(path) {
                var r = /\.([^./]+)$/.exec(path);
                return r && r[1] || '';
            }

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

            function verificaIntegracao() {
                const formData = new FormData();
                formData.append('documento', documento.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.verificaIntegracao, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    if (response.data === null) {
                        alert("Por favor, verifique a configuração de integração com o PNCP!");
                        desabilitaCompra()
                    } else {
                        buscarEntidade();
                        buscarUnidades();
                    }
                });
            }

            verificaIntegracao();

            function desabilitaCompra() {
                btnAnexarDocumentos.disabled = true;
                btnCarregarLicitacao.disabled = true;
                btnSalvarDados.disabled = true;
            }

            function buscarEntidade() {
                const formData = new FormData();
                formData.append('documento', documento.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.buscarEntidade, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    orgao.value = response.data.razaoSocial;
                });
            }

            function mostrarUnidades(unidadeCompradora) {
                unidadeCompradora.forEach((unidadeCompradora) => {
                    let unidade = document.createElement('option');
                    unidade.value = unidadeCompradora.pn02_unidade;
                    let unidadeNome = document.createTextNode(unidadeCompradora.pn02_nome);
                    unidade.appendChild(unidadeNome);
                    codigoUnidade.appendChild(unidade);
                });
            }

            function limparFormulario(salvar) {
                if (salvar === true) {
                    licitacaoCodigo.value = '';
                    solicitacaoNumero.value = '';
                    if (unidades > 1) {
                        codigoUnidade.value = "0";
                    }
                }
                verificaBuscarDados = 0;
                deleteChild(amparoLegalId);
                let amparoLegal = document.createElement('option');
                amparoLegal.value = "0";
                let amparoLegalNome = document.createTextNode('Selecione');
                amparoLegal.appendChild(amparoLegalNome);
                amparoLegalId.appendChild(amparoLegal);

                situacaoLicitacao.value = '0';
                sequencialCompra.value = '';
                tipoDocumento.value = "1";
                modalidadeId.value = "0";
                tipoInstrumentoConvocatorioId.value = "0";
                modoDisputaId.value = "0";
                numeroCompra.value = "";
                anoCompra.value = "";
                numeroProcesso.value = "";
                dataHomologacao.value = "";
                objetoCompra.value = "";
                sistemaRegistroPreco.value = "0";
                sistemaRegistroPreco.disabled = false;
                dataAberturaLicitacao.value = "";
                dataAberturaProposta.value = "";
                dataEncerramentoProposta.value = "";
                amparoLegalId.disabled = false;
                amparoLegalId.value = "0";
                linkOrigem.value = "";
                trDataEncerramentoProposta.hidden = false;
                trDataAberturaLicicacao.hidden = false;
                trDataAberturaProposta.hidden = false;
                infoComplementar.value = "1";
                infoComplementarText.value = "";
                infoComplementar.dispatchEvent(new Event('change'));
                tituloDocumento.value = "";
                anexoDocumento.value = "";
                horaAberturaLicitacao.value = "";
                horaAberturaProposta.value = "";
                horaEncerramentoProposta.value = "";
                numeroCompra.disabled = false;
                anoCompra.disabled = false;
                numeroCompra.disabled = false;
                objetoCompra.disabled = false;
                dataAberturaLicitacao.disabled = false;
                dataAberturaLicitacao.setReadOnly(false);
                horaAberturaLicitacao.disabled = false;
                numeroProcesso.disabled = false;
                tabelaItensLicitacao.bootstrapTable('removeAll');
                tabelaItensSolicitacao.bootstrapTable('removeAll');
                tipoInstrumentoConvocatorioId.disabled = false;
                justificativaPresencial.value = '';
                trJustificativa.hidden = true;
            }

            function buscarUnidades() {
                const formData = new FormData();
                formData.append('documento', documento.value);
                PHPSession.appendFormData(formData);
                HttpClient.post(routes.buscarUnidadesAtivas, { body: formData }).then(response => {
                    if (response.error) {
                        alert(response.message)
                    }
                    unidades = response.data.length;
                    if (response.data.length === 0) {
                        alert('Não foi possível identificar nenhuma unidade compradora,' +
                            ' por favor, verifique o cadastro de unidade.');
                        desabilitaCompra();
                    } else if (response.data.length === 1) {
                        deleteChild(codigoUnidade);
                        codigoUnidade.disabled = true;
                        let unidade = document.createElement('option');
                        unidade.value = response.data[0].pn02_unidade;
                        let unidadeNome = document.createTextNode(response.data[0].pn02_nome);
                        unidade.appendChild(unidadeNome);
                        codigoUnidade.appendChild(unidade);
                    } else {
                        mostrarUnidades(response.data);
                    }
                })
            }

            function deleteChild(campo) {
                var child = campo.lastElementChild;
                while (child) {
                    campo.removeChild(child);
                    child = campo.lastElementChild;
                }
            }

            function mostrarAmparosLegais(amaparosLegais) {
                amaparosLegais.forEach((amaparosLegais) => {
                    let amparoLegal = document.createElement('option');
                    amparoLegal.value = amaparosLegais.codigo;
                    let amparoLegalDescricao = document.createTextNode(amaparosLegais.descricao);
                    amparoLegal.appendChild(amparoLegalDescricao);
                    amparoLegalId.appendChild(amparoLegal);
                });
            }

            function mostrarInstrumentoConvocatorio(instrumentosConvocatorios) {
                instrumentosConvocatorios.forEach((instrumentosConvocatorios) => {
                    let instrumentoConvocatorio = document.createElement('option');
                    instrumentoConvocatorio.value = instrumentosConvocatorios.codigo;
                    let instrumentoConvocatorioDescricao = document.createTextNode(instrumentosConvocatorios.descricao);
                    instrumentoConvocatorio.appendChild(instrumentoConvocatorioDescricao);
                    tipoInstrumentoConvocatorioId.appendChild(instrumentoConvocatorio);
                });
            }

            function mostrarModosDipusta(modosDisputa) {
                modosDisputa.forEach((modosDisputa) => {
                    let modoDisputa = document.createElement('option');
                    modoDisputa.value = modosDisputa.codigo;
                    let modoDisputaDescricao = document.createTextNode(modosDisputa.descricao);
                    modoDisputa.appendChild(modoDisputaDescricao);
                    modoDisputaId.appendChild(modoDisputa);
                });
            }

            function preencheLicitacao(licitacao) {
                numeroCompra.value = licitacao.numeroCompra;
                numeroProcesso.value = licitacao.numeroProcesso;
                objetoCompra.value = licitacao.objetoCompra;
                sistemaRegistroPreco.value = licitacao.sistemaRegistroPreco;
                dataAberturaLicitacao.value = licitacao.dataAberturaProposta;
                horaAberturaLicitacao.value = licitacao.horaAberturaProposta;
                dataAberturaProposta.value = licitacao.dataAberturaProposta;
                horaAberturaProposta.value = licitacao.horaAberturaProposta;
                anoCompra.value = licitacao.anoCompra;
                numeroCompra.disabled = true;
                anoCompra.disabled = false;
                numeroProcesso.disabled = true;
                objetoCompra.disabled = true;
                sistemaRegistroPreco.disabled = true;
                dataAberturaLicitacao.setReadOnly(true);
                horaAberturaLicitacao.disabled = true;
                anoCompra.disabled = true;
            }

            function preencheSolicitacao(solicitacao) {
                numeroCompra.value = solicitacao.pc10_numero;
                dataAberturaLicitacao.value = solicitacao.pc10_data;
                horaAberturaLicitacao.value = '00:00';
                dataAberturaProposta.value = solicitacao.pc10_data;
                horaAberturaProposta.value = '00:00';
                anoCompra.value = solicitacao.pc10_data.split('-')[0];
                objetoCompra.value = solicitacao.pc10_resumo;
                sistemaRegistroPreco.selectedIndex = 2
                numeroProcesso.disabled = false;
                objetoCompra.disabled = false;
                numeroCompra.disabled = true;
                anoCompra.disabled = true;

                if (solicitacao.processo_administrativo) {
                    numeroProcesso.value = solicitacao.processo_administrativo.pc90_numeroprocesso;
                }
            }

            const modalAnexos = document.getElementById('modalAnexos');
            let windowItens = new windowAux('windowItens', 'Anexar Documento', 550, 250);
            windowItens.setContent(modalAnexos);
            windowItens.allowCloseWithEsc(true);
            windowItens.setShutDownFunction(function () {
                windowItens.oDBMask.destroy();
            });

            new DBLookUp(licitacaoAncora, licitacaoCodigo, licitacaoObjeto, {
                'sArquivo': 'func_liclicitapncp.php',
                'sLabel': 'Pesquisa de Licitação',
                'sObjetoLookUp': 'db_iframe_liclicita',
                'fCallBack': limparFormulario
            });

            new DBLookUp(solicitacaoAncora, solicitacaoNumero, solicitacaoNumero, {
                'sArquivo': 'func_solicita.php',
                'sLabel': 'Pesquisa de Solicitação',
                'sObjetoLookUp': 'db_iframe_solicita',
                'aParametrosAdicionais': ['nada=true'],
                'fCallBack': limparFormulario
            });

            function buildSelectCategoria(select, modalidade) {
                if (!['1', '13'].includes(modalidade)) {
                    select.options.add(new Option('Não se aplica', '3'));
                    return;
                }
                select.options.add(new Option('Selecione', '0'));
                select.options.add(new Option('Bens Imóveis', '1'));
                select.options.add(new Option('Bens Móveis', '2'));
            }

            async function carregarLicitacao() {
                if (!verificaUnidadeLicitacao()) {
                    return false;
                }
                verificaBuscarDados = 1;

                const formData = new FormData();
                formData.append('licitacao', licitacaoCodigo.value);
                PHPSession.appendFormData(formData);

                const response = await HttpClient.post(routes.buscarLicitacao, { body: formData })
                if (response.error) {
                    verificaBuscarDados = 0;
                    alert(response.message);
                    return;
                }


                trSituacaoLicitacao.style.display = 'none';
                sequencialCompra.value = '';
                if (response.data.link) {
                    let msg = "Contratação/Edital/Aviso já encontra-se publicada no PNCP!";
                    let link = `<a target="_blank" href="${response.data.link}">Clique aqui para acessar.</a>`;
                    sequencialCompra.value = response.data.sequencialCompra;
                    trSituacaoLicitacao.style.display = 'table-row';

                    alert(`${msg}\n${link}`);
                }

                btnSalvarDados.disabled = false;
                tabelaItensLicitacao.bootstrapTable('showColumn', 'indicadorSubcontratacao');
                preencheLicitacao(response.data);
                if (response.data.itens.length > 0) {
                    if (response.data.itens[0].dadosFornecedor.length < 1) {
                        tabelaItensLicitacao.bootstrapTable('hideColumn', 'indicadorSubcontratacao');
                    }
                }
                tabelaItensLicitacao.bootstrapTable('load', response.data.itens);
                configurarCriterioJulgamento();
            }

            async function carregarSolicitacao() {
                if (codigoUnidade.value === '0' || codigoUnidade.value === '') {
                    return alert('Selecione a Unidade Compradora.')
                }

                verificaBuscarDados = 1;
                const formData = new FormData();
                formData.append('pc10_numero', solicitacaoNumero.value);
                PHPSession.appendFormData(formData);
                const response = await HttpClient.post(
                    routes.buscarSolicitacao,
                    { body: formData }
                );

                if (response.error) {
                    return alert(response.message);
                }

                btnSalvarDados.disabled = false;
                if (response.data.itens.length === 0) {
                    alert('Solicitação não possui nenhum item para realizar envio.');
                    return switchPermitidoEnvio(false);
                }

                sequencialCompra.value = '';
                trSituacaoLicitacao.style.display = 'none';
                if (response.data.compra_pncp) {
                    sequencialCompra.value = response.data.compra_pncp.pn03_numero
                    trSituacaoLicitacao.style.display = 'table-row';

                    alert('Contratação/Edital/Aviso já encontra-se publicada no PNCP!');
                }

                const fornecedor = getDadosFornecedorItem(response.data.itens[0]);
                if (fornecedor === undefined || fornecedor.length === 0) {
                    alert('Itens da solicitação não possuem julgamento para realizar o envio.');
                    return switchPermitidoEnvio(false);
                }

                preencheSolicitacao(response.data);
                switchPermitidoEnvio(true);

                tabelaItensSolicitacao.bootstrapTable('showColumn', 'indicadorSubcontratacao');
                tabelaItensSolicitacao.bootstrapTable('load', response.data.itens);
            }

            function switchPermitidoEnvio(permitido) {
                if (permitido === true) {
                    return btnSalvarDadosSolicitacao.disabled = false;
                }

                btnSalvarDadosSolicitacao.disabled = true;
            }

            function formatterTipoBeneficio(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectTipoBeneficio'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='1'>Participação exclusiva para ME/EPP</option>" +
                    "<option value='2'>Subcontratação para ME/EPP</option>" +
                    "<option value='3'>Cota reservada para ME/EPP</option>" +
                    "<option value='4'>Sem benefício</option>" +
                    "<option value='5'>Não se aplica</option>" +
                    "</select>"
            }

            function formmaterIncentivoFiscalPPB(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectIncentivoFiscalPPB'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='true'>Possui o incentivo</option>" +
                    "<option value='false'>Não possui o incentivo</option>" +
                    "</select>"
            }

            function formatterMaterial(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectMaterial'>" +
                    "<option value='S'>Servico</option>" +
                    "<option value='M'>Material</option>" +
                    "</select>"
            }

            function formatterOrcamentoSigiloso(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectOrcamentoSigiloso'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='true'>Sigiloso</option>" +
                    "<option value='false'>Não Sigiloso</option>" +
                    "</select>"
            }

            function formatterCriterioJulgamento(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectCriterioJulgamento'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='1'>Menor Preço</option>" +
                    "<option value='2'>Maior desconto</option>" +
                    "<option value='4'>Técnica e preço</option>" +
                    "<option value='5'>Maior lance</option>" +
                    "<option value='6'>Maior retorno econômico</option>" +
                    "<option value='7'>Não se aplica</option>" +
                    "<option value='8'>Melhor técnica</option>" +
                    "<option value='9'>Conteúdo artístico</option>" +
                    "</select>"
            }

            function formatterIndicadorSubContratacao(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectIndicadorSubContratacao'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='true'>Sim</option>" +
                    "<option value='false'>Não</option>" +
                    "</select>"
            }

            function formatterItemCategoriaId(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<select data-numero='" + numeroItem + "' class='selectItemCategoriaId'>" +
                    "<option value='0' selected>Selecione</option>" +
                    "<option value='1'>Bens Imóveis</option>" +
                    "<option value='2'>Bens Móveis</option>" +
                    "<option value='3'>Não se aplica</option>" +
                    "</select>"
            }

            function formatterCodigoRegistroImobiliario(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<input maxlength='255' data-numero='" + numeroItem + "' class='selectCodigoRegistroImobiliario'>"
            }

            function formatterPercentualMargemPreferenciaNormal(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<input maxlength='255' data-numero='" + numeroItem + "' class='selectPercentualMargemPreferenciaNormal'>"
            }

            function formatterPercentualMargemPreferenciaAdicional(value, row) {
                const numeroItem = row.numeroItem ? row.numeroItem : row.pc11_seq;
                return "<input maxlength='255' data-numero='" + numeroItem + "' class='selectPercentualMargemPreferenciaAdicional'>"
            }

            const formatterFornecedorSolicitacao = (index, value) => {
                if (value.processo_compra_item === null) {
                    return;
                }

                let fornecedor = getDadosFornecedorItem(value);
                let cpf = fornecedor.z01_cgccpf;
                if (fornecedor.z01_cgccpf === undefined || fornecedor.z01_cgccpf.length === 0) {
                    cpf = 'Não informado';
                }

                let tipoPessoaId = fornecedor.z01_cgccpf.trim().length === 14 ? 'PJ' : 'PF';
                if (fornecedor.z01_nacion === 2) {
                    tipoPessoaId = 'PE';
                }

                const dados = [
                    [
                        {
                            label: "Fornecedor:",
                            valor: `${fornecedor.z01_numcgm} - ${cpf}`
                        },
                        {
                            label: "Nome:",
                            valor: fornecedor.z01_nome
                        },
                        {
                            label: "Tipo Pessoa:",
                            valor: tipoPessoaId
                        },
                        {
                            label: "Tipo Empresa:",
                            valor: tipoEmpresa(fornecedor.porteFornecedorId)
                        },
                        {
                            label: "Valor Unitário:",
                            valor: fornecedor.pc23_vlrun
                        },
                        {
                            label: "Valor Total:",
                            valor: fornecedor.pc23_valor
                        },
                    ],
                ];
                return detailFormaterTable.createDetail(dados, 'Resultado(s)');
            }

            const detailFormatter = (index, row) => {
                let dados = formataDadosAnalitico(index, row);
                if (row.dadosFornecedor.length === 0) {
                    return;
                }
                return detailFormaterTable.createDetail(dados, 'Resultado(s)');
            }

            const formataDadosAnalitico = (value, dadosLinha) => {
                let dadosFornecedor = dadosLinha.dadosFornecedor;
                return [
                    [
                        {
                            label: "Fornecedor:",
                            valor: `${dadosFornecedor.numcgm} - ${dadosFornecedor.niFornecedor}`
                        },
                        {
                            label: "Nome:",
                            valor: dadosFornecedor.nomeRazaoSocialFornecedor
                        },
                        {
                            label: "Tipo Pessoa:",
                            valor: dadosFornecedor.tipoPessoaId
                        },
                        {
                            label: "Tipo Empresa:",
                            valor: tipoEmpresa(dadosFornecedor.porteFornecedorId)
                        },
                        {
                            label: "Data Homologacao:",
                            valor: dadosLinha.dataHomologacao
                        },
                        {
                            label: "Valor Unitário:",
                            valor: dadosLinha.valorUnitarioHomologado
                        },
                        {
                            label: "Valor Total:",
                            valor: dadosLinha.valorTotalHomologado
                        },
                    ],
                ];
            };

            function tipoEmpresa(tipoEmpresa) {
                switch (tipoEmpresa) {
                    case 1:
                        return 'ME';
                        break;
                    case 2:
                        return 'EPP';
                        break;
                    default:
                        return 'Normal';
                }
            }

            jQuery(document).ready(jQuery => {
                window.operateEvents = {
                    'change .selectMaterial': (e, value, row, index) => {
                        row.materialOuServico = e.target.value;
                    },
                    'change .selectOrcamentoSigiloso': (e, value, row, index) => {
                        row.orcamentoSigiloso = e.target.value;
                    },
                    'change .selectCodigoRegistroImobiliario': (e, value, row, index) => {
                        row.codigoRegistroImobiliario = e.target.value;
                    },
                    'change .selectItemCategoriaId': (e, value, row, index) => {
                        row.itemCategoriaId = e.target.value;
                    },
                    'change .selectCriterioJulgamento': (e, value, row, index) => {
                        row.criterioJulgamentoId = e.target.value;
                    },
                    'change .selectTipoBeneficio': (e, value, row, index) => {
                        row.tipoBeneficioId = e.target.value;
                    },
                    'change .selectIncentivoFiscalPPB': (e, value, row, index) => {
                        row.incentivoProdutivoBasico = e.target.value;
                    },
                    'change .selectIndicadorSubContratacao': (e, value, row, index) => {
                        row.indicadorSubcontratacao = e.target.value;
                    },
                }

                const colunasAnexos = [
                    {
                        field: '',
                        title: 'Tipo Documento',
                        halign: 'center',
                        align: 'center',
                    },
                    {
                        field: 'nomeArquivo',
                        title: 'Título',
                        halign: 'center',
                        align: 'center',
                    },
                    {
                        field: '',
                        title: 'Ações',
                        halign: 'center',
                        align: 'center',
                    },
                ];
                tabelaAnexos.bootstrapTable({
                    locale: 'pt-BR',
                    height: 100,
                    class: "table table-sm",
                    columns: colunasAnexos,
                    showButtonText: true,
                    useRowAttrFunc: true,
                    reorderableRows: true,
                    detailView: true,
                });

                alteraListaItens('licitacao');
            });
        })
            ;
    </script>
</body>
