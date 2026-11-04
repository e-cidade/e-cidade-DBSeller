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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <style>
        .mask {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
        }

        textarea {
            width: 398px;
            resize: none;
        }

        #div-erros {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 8px;
            margin-bottom: 6px;
            overflow-y: scroll;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        #div-erros div {
            width: 470px;
            padding-left: 5px;
            padding-right: 5px;
        }
    </style>
</head>
<body>
<?php validaDepartamentoLogado('unidade'); ?>
<input type="hidden" id="departamento-logado" value="<?= $_SESSION['DB_coddepto'] ?>">
<div class="container" id="container-inconsistencias">
    <div class="subcontainer">
        <fieldset>
            <legend>Ajustes Inconsistências Hórus</legend>
            <table class="form-container">
                <tr>
                    <td><label for="competencia">Competência: </label></td>
                    <td>
                        <input type="text" id="competencia" style="width: 52px;">
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
    <div class="subcontainer" style="width: 1000px; margin-top: 10px;">
        <fieldset>
            <legend>Inconsistências</legend>
            <table id="data-table-inconsistencias" class="table table-sm"></table>
        </fieldset>
    </div>
</div>
<div class="container" id="container-ajuste" style="display: none;">
    <div class="subcontainer">
        <b id="info-movimentacao"></b>
        <div style="display: flex; flex-direction: row;">
            <div id="div-movimentacao">
                <fieldset>
                    <legend>Dados da movimentação</legend>
                    <table class="form-container" id="table-movimentacao">
                        <input type="hidden" id="tipo">
                        <tr id="tr-lancamento">
                            <td><label for="codigo">Lançamento: </label></td>
                            <td>
                                <input type="text" id="codigo" class="readonly field-size2" readonly>
                                <label for="data">Data: </label>
                                <input type="text" id="data" class="readonly field-size2" readonly>
                            </td>
                        </tr>

                        <!-- ENTRADA/SAÌDA -->
                        <tr id="tr-movimentacao" style="display: none;">
                            <td><label for="tipo-movimentacao">Tipo Movimentação: </label></td>
                            <td><select id="tipo-movimentacao" onchange="verificaTipoMovimentacao();"></select></td>
                        </tr>

                        <!-- ENTRADA -->
                        <tr id="tr-nota" style="display: none;">
                            <td>
                                <label for="nota-fiscal">Nota Fiscal: </label>
                            </td>
                            <td>
                                <input type="text" id="nota-fiscal">
                                <label for="data-nota-fiscal">Data: </label>
                                <input type="text" id="data-nota-fiscal">
                            </td>
                        </tr>
                        <tr id="tr-lancamento-origem" style="display: none;">
                            <td>
                                <label for="lancamento-origem">Lançamento Origem: </label>
                            </td>
                            <td>
                                <input type="text" id="lancamento-origem" class="readonly" readonly>
                                <label for="data-lancamento-origem">Data: </label>
                                <input type="text" id="data-lancamento-origem" class="readonly" readonly>
                            </td>
                        </tr>

                        <!-- ENTRADA/SAÍDA -->
                        <tr id="tr-unidade" style="display: none;">
                            <td>
                                <label for="unidade" id="unidade-label">Unidade: </label>
                            </td>
                            <td>
                                <input type="text" id="unidade" data="sd02_i_codigo" class="field-size2">
                                <input id="unidade-descricao"
                                       class="readonly field-size8"
                                       data="descrdepto"
                                       readonly
                                       type="text">
                            </td>
                        </tr>
                        <tr id="tr-unidade-cnes" style="display: none;">
                            <td>
                                <label for="unidade-cnes">CNES: </label>
                            </td>
                            <td>
                                <input type="text" id="unidade-cnes" class="readonly field-size2" readonly>
                            </td>
                        </tr>
                        <tr id="tr-cgm" style="display: none;">
                            <td>
                                <label for="cgm" id="cgm-label">CGM: </label>
                            </td>
                            <td>
                                <input type="text" id="cgm" class="field-size2" data="z01_numcgm">
                                <input type="text" id="cgm-nome" class="readonly field-size8" data="z01_nome" readonly>
                            </td>
                        </tr>
                        <tr id="tr-cgm-cnpj">
                            <td>
                                <label for="cgm-cnpj">CNPJ: </label>
                            </td>
                            <td>
                                <input type="text" id="cgm-cnpj" class="readonly field-size3" readonly>
                            </td>
                        </tr>

                        <!-- DISPENSAÇÃO -->
                        <tr id="tr-paciente" style="display: none;">
                            <td>
                                <label for="nota-fiscal">Paciente: </label>
                            </td>
                            <td>
                                <input type="hidden" id="id-paciente">
                                <input type="text" id="nome-paciente" class="readonly field-size8" readonly>
                            </td>
                        </tr>
                        <tr id="tr-documentos-paciente">
                            <td>
                                <label for="cns-paciente">CNS: </label>
                            </td>
                            <td>
                                <input id="cns-paciente"
                                       maxlength="15"
                                       oninput="this.value = this.value.replace(/\D/g, '');"
                                       style="width: 153px"
                                       type="text">
                                <label for="cpf-paciente">CPF: </label>
                                <input id="cpf-paciente"
                                       maxlength="11"
                                       oninput="this.value = this.value.replace(/\D/g, '');"
                                       style="width: 153px"
                                       type="text">
                            </td>
                        </tr>
                    </table>

                    <fieldset>
                        <legend>Descrição do Lançamento</legend>
                        <textarea id="descricao" class="readonly" readonly></textarea>
                    </fieldset>
                </fieldset>
            </div>

            <div id="div-erros">
                <div>Inconsistências:</div>
                <div id="erros-movimentacao"></div>
            </div>
        </div>

        <button onclick="salvarMovimentacao();">
            <i class="fas fa-save"></i>
            Salvar
        </button>
        <button onclick="limparFormMovimentacao();">
            <i class="fas fa-eraser"></i>
            Limpar
        </button>
        <button onclick="voltar()">
            <i class="fas fa-backward"></i>
            Voltar
        </button>
    </div>
    <div class="subcontainer" style="width: 1000px; margin-top: 10px;">
        <fieldset>
            <legend>Medicamentos</legend>
            <table id="data-table-itens" class="table table-sm"></table>
        </fieldset>
    </div>
    <div class="subcontainer" id="modal-medicamento">
        <fieldset>
            <legend>Dados do medicamento</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="medicamento">Medicamento:</label>
                    </td>
                    <td colspan="3">
                        <input type="text" id="medicamento" class="field-size10 readonly" readonly>
                    </td>
                </tr>
                <tr style="text-align: justify">
                    <td>
                        <label for="lote">Lote:</label>
                    </td>
                    <td>
                        <input type="text" id="lote">
                    </td>
                    <td>
                        <label for="validade">Validade:</label>
                    </td>
                    <td>
                        <input type="text" id="validade">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fabricante" id="fabricante-label">Fabricante:</label>
                    </td>
                    <td colspan="3">
                        <input type="text" id="fabricante" data="m76_sequencial" class="field-size2">
                        <input type="text" id="fabricante-nome" class="field-size8 readonly" data="m76_nome" readonly>
                    </td>
                </tr>
                <tr id="tr-fabricante-cnpj">
                    <td>
                        <label for="fabricante-cnpj">CNPJ: </label>
                    </td>
                    <td colspan="3">
                        <input type="text" id="fabricante-cnpj" class="readonly field-size3" readonly>
                    </td>
                </tr>
            </table>
        </fieldset>
        <button onclick="salvarMedicamento();">
            <i class="fas fa-save"></i>
            Salvar
        </button>
        <button onclick="limparFormMedicamento()">
            <i class="fas fa-eraser"></i>
            Limpar
        </button>
    </div>
</div>
<div id="mask" class="mask" hidden></div>
<?php db_menu(); ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<script>
    $.noConflict();
    const ENTRADA_BNAFAR = '1';
    const SAIDA_BNAFAR = '2';
    const DISPENSACAO_BNAFAR = '3';

    const routes = {
        get: 'saude/farmacia/consulta/bnafar/inconsistencias',
        imprimir: 'saude/farmacia/procedimento/bnafar/consistir',
        getTiposMovimentacoes: 'saude/farmacia/consulta/bnafar/tipos-movimentacoes',
        salvarMovimentacao: 'saude/farmacia/procedimento/bnafar/salvar-movimentacao',
        getInfoUnidade: 'saude/ambulatorial/consulta/unidade',
        getInfoFabricante: 'patrimonial/material/consulta/fabricante'
    };

    const inputCompetencia = document.getElementById('competencia');
    const ctnInconsistencias = document.getElementById('container-inconsistencias');
    const ctnAjuste = document.getElementById('container-ajuste');

    const tabelaMovimentacoes = jQuery('#data-table-inconsistencias');
    const inputCodigo = document.getElementById('codigo');
    const selectTipoMovimentacao = document.getElementById('tipo-movimentacao');
    const inputNotaFiscal = document.getElementById('nota-fiscal');
    const inputLancamentoOrigem = document.getElementById('lancamento-origem');
    const inputDataLancamentoOrigem = document.getElementById('data-lancamento-origem');

    const unidade = {
        label: document.getElementById('unidade-label'),
        inputCodigo: document.getElementById('unidade'),
        inputDescricao: document.getElementById('unidade-descricao'),
        inputCNES: document.getElementById('unidade-cnes')
    };

    const cgm = {
        label: document.getElementById('cgm-label'),
        inputCodigo: document.getElementById('cgm'),
        inputNome: document.getElementById('cgm-nome'),
        inputCNPJ: document.getElementById('cgm-cnpj')
    }

    const paciente = {
        inputId: document.getElementById('id-paciente'),
        inputNome: document.getElementById('nome-paciente'),
        inputCns: document.getElementById('cns-paciente'),
        inputCpf: document.getElementById('cpf-paciente')
    }

    const tabelaItens = jQuery('#data-table-itens');

    const divModalMedicamento = document.getElementById('modal-medicamento');
    const medicamento = {
        inputDescricao: document.getElementById('medicamento'),
        inputLote: document.getElementById('lote'),
        dataValidade: new DBInputDate(document.getElementById('validade')),
        inputIdFabricante: document.getElementById('fabricante'),
        inputNomeFabricante: document.getElementById('fabricante-nome'),
        inputCnpjFabricante: document.getElementById('fabricante-cnpj')
    };

    const callbackUnidade = async () => {
        const limpaCampos = () => {
            unidade.inputCodigo.value = '';
            unidade.inputDescricao.value = '';
            unidade.inputCNES.value = '';
        };
        const route = `${PHPSession.requestApi}/${routes.getInfoUnidade}/${unidade.inputCodigo.value}`;
        const response = await HttpClient.get(route);
        if (response.error) {
            alert(response.message);
            limpaCampos();
            return;
        }
        if (response.data.cnes == '') {
            alert('Só é aceito cadastros que possuam CNES informado.');
            limpaCampos();
            return;
        }

        unidade.inputCNES.value = response.data.cnes;
    }

    const lookUpUnidade = new DBLookUp(
        unidade.label,
        unidade.inputCodigo,
        unidade.inputDescricao,
        {
            'sArquivo': 'func_unidades.php',
            'sLabel': 'Pesquisar Unidade',
            'sObjetoLookUp': 'db_iframe_unidades',
            'oCampoDocumento': unidade.inputCNES,
            'fCallBack': callbackUnidade
        }
    );

    const callbackCgm = async () => {
        const formData = new FormData();
        formData.append('exec', 'getInfoCgm');
        formData.append('iCgm', cgm.inputCodigo.value);

        const response = await HttpClient.post('sau4_ambulatorial.RPC.php', { body: formData });

        if (response.z01_cgccpf.length !== 14) {
            alert('Só é aceito cadastros que possuam CNPJ informado.');
            cgm.inputCodigo.value = '';
            cgm.inputNome.value = '';
            cgm.inputCNPJ.value = '';
            return;
        }

        cgm.inputCNPJ.value = response.z01_cgccpf;
    }

    const lookUpCgm = new DBLookUp(
        cgm.label,
        cgm.inputCodigo,
        cgm.inputNome,
        {
            'sArquivo': 'func_cgm.php',
            'sLabel': 'Pesquisar CGM',
            'sObjetoLookUp': "func_nome",
            'oCampoDocumento': cgm.inputCNPJ,
            'fCallBack': callbackCgm
        }
    );

    const callbackFabricante = async () => {
        const id = medicamento.inputIdFabricante.value;

        const response = await HttpClient.get(`${PHPSession.requestApi}/${routes.getInfoFabricante}/${id}`);
        if (response.error) {
            alert(response.message);
            return;
        }

        medicamento.inputCnpjFabricante.value = response.data.cnpj;
    };

    const lookUpFabricante = new DBLookUp(
        document.getElementById('fabricante-label'),
        medicamento.inputIdFabricante,
        medicamento.inputNomeFabricante,
        {
            'sArquivo': 'func_matfabricante.php',
            'sLabel': 'Pesquisar Fabricante',
            'sObjetoLookUp': 'db_iframe_matfabricante',
            'fCallBack': callbackFabricante
        }
    );

    const divMask = document.getElementById('mask');
    const fechaModal = () => {
        divMask.hidden = true;
        windowMedicamento.hide();
    }

    const windowMedicamento = new windowAux('windowMedicamento', 'Editar Medicamento', 1000, 550);
    windowMedicamento.setContent(divModalMedicamento);
    windowMedicamento.setShutDownFunction(() => {
        fechaModal()
    });

    inputCompetencia.addEventListener('blur', () => {
        const [mes, ano] = inputCompetencia.value.split("/");
        if (ano === undefined) {
            if (mes.length >= 5 && mes.length <= 6) {
                setCompetencia(mes.substring(mes.length - 4, mes.length), mes.substring(0, mes.length - 4));
                return;
            }
            alert('Competência inválida.');
            return;
        }
        setCompetencia(ano, mes);
    });

    unidade.inputCodigo.addEventListener('change', e => setReadOnly(cgm.inputCodigo, e.target.value !== '', lookUpCgm));
    cgm.inputCodigo.addEventListener('change', e => {
        setReadOnly(unidade.inputCodigo, e.target.value !== '', lookUpUnidade)
    });

    const dataNotaFiscal = new DBInputDate(document.getElementById('data-nota-fiscal'));

    let dadosMedicamentoAlteracao = {};
    const estoqueItens = [];
    const dataAtual = new Date();
    jQuery(document).ready(() => {
        setCompetencia(dataAtual.getFullYear(), dataAtual.getMonth() + 1)

        const buttons = () => {
            return {
                btnImprimir: {
                    text: 'Imprimir',
                    icon: 'fas fa-print',
                    event: imprimir
                }
            };
        }

        const acoes = {
            'click .editar': (e, b, dados) => {
                carregarInconsistencia(dados);
            }
        }

        tabelaMovimentacoes.bootstrapTable({
            height: 500,
            search: true,
            detailView: true,
            buttons: buttons,
            showButtonText: true,
            columns: [
                {
                    field: 'lancamento',
                    title: 'Lançamento',
                    width: 90
                },
                {
                    field: 'data',
                    title: 'Data',
                    width: 100
                },
                {
                    field: 'descricao',
                    title: 'Descrição',
                    width: 620
                },
                {
                    field: 'tipo',
                    title: 'Tipo',
                    width: 110
                },
                {
                    field: 'acoes',
                    title: 'Ações',
                    align: 'center',
                    width: 40,
                    formatter: () => {
                        return '<a class="editar" title="Editar"><i class="fas fa-pen"></i></a>';
                    },
                    events: acoes
                }
            ],
            detailFormatter: detalheInconsistencias,
            rowStyle: row => {
                if (row.erroBnafar) {
                    return { classes: 'alert-danger', css: { 'font-weight': 'bold' } };
                }

                return { classes: '' };
            }
        });
    });

    async function imprimir() {
        const [mes, ano] = inputCompetencia.value.split('/');
        const periodoInicio = new Date(ano, Number(mes) - 1, 1);
        const periodoFim = new Date(ano, Number(mes), 0);

        const formData = new FormData();
        formData.append('periodoInicio', periodoInicio.toISOString());
        formData.append('periodoFim', periodoFim.toISOString());

        const procedimentos = ['1', '2', '3'];
        for (const procedimento of procedimentos) {
            formData.append('procedimentos[]', procedimento);
        }

        PHPSession.appendFormData(formData);

        const response = await HttpClient.post(`${PHPSession.requestApi}/${routes.imprimir}`, { body: formData });
        if (response.error) {
            alert(response.message);
            return;
        }

        let possuiInconsistencias = false;

        const download = new DBDownload();
        download.addGroups('pdf', 'Inconsistências encontradas');
        for (const procedimento of response.data) {
            if (procedimento.inconsistente) {
                possuiInconsistencias = true;
                download.addFile(procedimento.relatorio.path, procedimento.relatorio.name, 'pdf');
            }
        }

        if (!possuiInconsistencias) {
            alert('Sem inconsistências na competência.');
            return;
        }

        download.show(null);
    }

    async function getInconsistencias() {
        if (PHPSession.requestApi === undefined) {
            await PHPSession.loadData();
        }

        const formData = new FormData();
        formData.append('competencia', inputCompetencia.value);
        PHPSession.appendFormData(formData);

        const url = `${PHPSession.requestApi}/${routes.get}`;
        const response = await HttpClient.post(url, {body: formData});
        if (response.error) {
            alert(response.message);
            return;
        }

        tabelaMovimentacoes.bootstrapTable('load', response.data);
    }

    function setCompetencia(ano, mes) {
        const data = new Date(ano, Number(mes) - 1);
        const validaCompetencia = () => {
            if (data.getMonth() > dataAtual.getMonth() && data.getFullYear() >= dataAtual.getFullYear()) {
                alert('A competência não pode ser maior que a competência atual.');
                return false;
            }
            return true;
        }

        if (!validaCompetencia()) {
            setCompetencia(dataAtual.getFullYear(), dataAtual.getMonth() + 1);
            return;
        }

        mes = mes.toString().padStart(2, '0');
        inputCompetencia.value = `${mes}/${ano}`;

        getInconsistencias();
    }

    async function getTiposMovimentacoes() {
        const movimentacao = document.getElementById('tipo').value;
        const url = `${PHPSession.requestApi}/${routes.getTiposMovimentacoes}/${movimentacao}`;
        const response = await HttpClient.get(url);

        const select = document.getElementById('tipo-movimentacao');
        select.options.length = 0;
        select.options.add(new Option('NÃO INFORMADO', ''));

        for (const tipo of response.data) {
            select.options.add(new Option(tipo.fa68_descricao, tipo.fa68_codigo));
        }
    }

    function ocultarTodosCampos() {
        const table = document.getElementById('table-movimentacao');
        const trs = table.getElementsByTagName('tr');

        for (let tr of trs) {
            tr.style.display = 'none';
        }
    }

    function setTipo(tipo) {
        const tiposMovimentacoes = {
            1: 'ENTRADA',
            2: 'SAÍDA',
            3: 'DISPENSAÇÃO'
        };

        let movimentacao = 1;
        for (let key in tiposMovimentacoes) {
            movimentacao = tiposMovimentacoes[key] === tipo ? key : movimentacao;
        }

        document.getElementById('tipo').value = movimentacao;
    }

    async function preparaTela(dados) {
        ctnInconsistencias.style.display = 'none';
        ctnAjuste.style.display = 'block';
        setTipo(dados.tipo);
        await getTiposMovimentacoes();
        ocultarTodosCampos();

        document.getElementById('tr-lancamento').style.display = '';
        document.getElementById('div-erros').style.height = document.getElementById('div-movimentacao').style.height;

        const movimentacao = document.getElementById('tipo').value;
        if (movimentacao === ENTRADA_BNAFAR) {
            document.getElementById('tr-movimentacao').style.display = '';
            document.getElementById('tr-unidade').style.display = '';
            document.getElementById('tr-unidade-cnes').style.display = '';
            document.getElementById('tr-cgm').style.display = '';
            document.getElementById('tr-cgm-cnpj').style.display = '';
            document.getElementById('tr-nota').style.display = '';
        }

        if (movimentacao === SAIDA_BNAFAR) {
            document.getElementById('tr-movimentacao').style.display = '';
            document.getElementById('tr-unidade').style.display = '';
            document.getElementById('tr-cgm').style.display = '';
        }

        if (movimentacao === DISPENSACAO_BNAFAR) {
            document.getElementById('tr-paciente').style.display = '';
            document.getElementById('tr-documentos-paciente').style.display = '';
        }
    }

    async function carregarInconsistencia(dados) {
        await preparaTela(dados);
        carregarMovimentacao(dados);

        const acoes = {
            'click .editar': (e, b, dados) => {
                editarMedicamento(dados);
            }
        };

        const formatterCamposDinamicos = value => value.inconsistente ? 'INCONSISTENTE' : value.valor;

        tabelaItens.bootstrapTable({
            height: 300,
            search: true,
            uniqueId: 'estoqueItem',
            detailView: true,
            columns: [
                {
                    field: 'descricao',
                    title: 'Descrição',
                    halign: 'center',
                    align: 'left',
                    width: 400
                },
                {
                    field: 'lote',
                    title: 'Lote',
                    align: 'center',
                    width: 120,
                    formatter: formatterCamposDinamicos
                },
                {
                    field: 'validade',
                    title: 'Validade',
                    align: 'center',
                    width: 80,
                    formatter: formatterCamposDinamicos
                },
                {
                    field: 'nomeFabricante',
                    title: 'Fabricante',
                    halign: 'center',
                    align: 'left',
                    width: 290,
                    formatter: (value, data) => {
                        if (data.idFabricante.inconsistente) {
                            return 'INCONSISTENTE';
                        }
                        return value;
                    }
                },
                {
                    field: 'acoes',
                    title: 'Ações',
                    align: 'center',
                    width: 40,
                    formatter: () => {
                        return '<a class="editar" title="Editar"><i class="fas fa-pen"></i></a>';
                    },
                    events: acoes
                }
            ],
            detailFormatter: detalheInconsistencias
        });

        tabelaItens.bootstrapTable('load', dados.itens);
    }

    function mostraJanelaMedicamento() {
        divMask.hidden = false
        windowMedicamento.show(50, 0);
        lookUpFabricante.oParametros.zIndex = ++windowMedicamento.zIndex;
    }

    function editarMedicamento(dados) {
        dadosMedicamentoAlteracao = dados;
        mostraJanelaMedicamento();
        limparFormMedicamento(true);
        medicamento.inputDescricao.value = dados.descricao;
        setValue(dados.lote, medicamento.inputLote);
        setDateValue(dados.validade, medicamento.dataValidade);
        setValue(dados.idFabricante, medicamento.inputIdFabricante, lookUpFabricante);
        medicamento.inputNomeFabricante.value = dados.nomeFabricante;
        medicamento.inputCnpjFabricante.value = dados.cnpjFabricante;
    }

    function limparFormMovimentacao() {
        if (!selectTipoMovimentacao.disabled) {
            selectTipoMovimentacao.value = '';
        }
        if (!inputNotaFiscal.readOnly) {
            inputNotaFiscal.value = '';
            dataNotaFiscal.setValue('');
        }
        if (!unidade.inputCodigo.readOnly) {
            unidade.inputCodigo.value = '';
            unidade.inputDescricao.value = '';
            unidade.inputCNES.value = '';
        }
        if (!cgm.inputCodigo.readOnly) {
            cgm.inputCodigo.value = '';
            cgm.inputNome.value = '';
            cgm.inputCNPJ.value = '';
        }
        if (!paciente.inputCns.readOnly) {
            paciente.inputCns.value = '';
        }
        if (!paciente.inputCpf.readOnly) {
            paciente.inputCpf.value = '';
        }
    }

    function voltar(recarregar = false) {
        ctnInconsistencias.style.display = 'block';
        ctnAjuste.style.display = 'none';
        if (recarregar) {
            getInconsistencias();
        }
    }

    function validaEntrada() {
        if (selectTipoMovimentacao.value === '') {
            throw Error('Informe o tipo de movimentação.');
        }
        if (unidade.inputCodigo.value === '' && cgm.inputCodigo.value === '') {
            throw new Error('Informe o CGM ou unidade origem.');
        }
        if (unidade.inputCodigo.value !== '' && cgm.inputCodigo.value !== '') {
            throw new Error('Informe somente CGM origem OU unidade origem.');
        }
        // Significa que a movimentação é de origem de transferência e a nota não é informada.
        if (document.getElementById('tr-nota').style.display === 'none') {
            return;
        }
        if (inputNotaFiscal.value === '') {
            throw Error('Informe a nota fiscal.');
        }
        if (empty(dataNotaFiscal.__toLocaleDateString())) {
            throw Error('O campo Data da Nota é obrigatório.');
        }
    }

    function validaSaida() {
        if (unidade.inputCodigo.value === '' && cgm.inputCodigo.value === '') {
            throw new Error('Informe o CGM ou unidade destino.');
        }
        if (unidade.inputCodigo.value !== '' && cgm.inputCodigo.value !== '') {
            throw new Error('Informe somente CGM origem OU unidade destino.');
        }
    }

    function validaDispensacao() {
        if (paciente.inputCns.value === '' && paciente.inputCpf.value === '') {
            throw Error('É necessário informar o CNS do paciente ou CPF.');
        }
        if (paciente.inputCns.value !== '' && paciente.inputCns.value.length !== 15) {
            throw Error('O CNS deve ser preenchido com 15 dígitos');
        }
        if (paciente.inputCpf.value !== '' && paciente.inputCpf.value.length !== 11) {
            throw Error('O CPF deve ser preenchido com 11 dígitos');
        }
    }

    async function getDadosMedicamentos() {
        const medicamentos = [];
        const medicamentosInconsistentes = [];
        for (const item of tabelaItens.bootstrapTable('getData')) {
            const medicamentoInconsistente = validaDadosMedicamento(item);
            if (medicamentoInconsistente) {
                medicamentosInconsistentes.push(medicamentoInconsistente)
            }

            medicamentos.push({
                id: item.id,
                descricao: item.descricao,
                estoqueItem: item.estoqueItem,
                lote: item.lote,
                validade: item.validade,
                fabricante: item.idFabricante
            });
        }

        const abortar = medicamentosInconsistentes.length && await new Promise(resolve => {
            const quantidade = medicamentosInconsistentes.length;
            const mensagem = `Existem ${quantidade} medicamentos com inconsistência. Deseja continuar?`;
            alertify.confirm(mensagem, confirm => resolve(!confirm));
        });

        if (abortar) {
            throw new Error('Operação cancelada!');
        }

        return medicamentos;
    }

    async function getFormDataMovimentacao() {
        const movimentacao = document.getElementById('tipo').value;
        const formData = new FormData();
        formData.append('lancamento', inputCodigo.value);
        formData.append('movimentacao', movimentacao);

        if (movimentacao === ENTRADA_BNAFAR) {
            validaEntrada();
            formData.append('tipoMovimentacao', selectTipoMovimentacao.value);
            formData.append('unidade', unidade.inputCodigo.value);
            formData.append('cgm', cgm.inputCodigo.value);
            if (inputNotaFiscal.value !== '') {
                formData.append('notaFiscal', inputNotaFiscal.value);
                formData.append('dataNotaFiscal', js_formatar(dataNotaFiscal.__toLocaleDateString(), 'd'));
            }
        }
        if (movimentacao === SAIDA_BNAFAR) {
            validaSaida();
            formData.append('tipoMovimentacao', selectTipoMovimentacao.value);
            formData.append('unidade', unidade.inputCodigo.value);
            formData.append('cgm', cgm.inputCodigo.value);
        }
        if (movimentacao === DISPENSACAO_BNAFAR) {
            validaDispensacao();
            formData.append('paciente', paciente.inputId.value);
            formData.append('cnsPaciente', paciente.inputCns.value);
            formData.append('cpfPaciente', paciente.inputCpf.value);
        }

        const medicamentos = await getDadosMedicamentos();
        formData.append('medicamentos', JSON.stringify(medicamentos));

        return formData;
    }

    async function salvarMovimentacao() {
        let formData = null;
        try {
            formData = await getFormDataMovimentacao();
        } catch (e) {
            alert(e.message);
            return;
        }
        PHPSession.appendFormData(formData);
        const url = `${PHPSession.requestApi}/${routes.salvarMovimentacao}`;
        const response = await HttpClient.post(url, {body: formData});

        alert(response.message);
        if (response.error) {
            return;
        }
        voltar(true);
    }

    /**
     * Salva os dados do medicamento em memória para envio posterior
     */
    function salvarMedicamento() {
        dadosMedicamentoAlteracao.descricao = medicamento.inputDescricao.value;
        dadosMedicamentoAlteracao.lote = {
            valor: medicamento.inputLote.value,
            inconsistente: false
        };
        dadosMedicamentoAlteracao.validade = {
            valor: medicamento.dataValidade.__toLocaleDateString(),
            inconsistente: false
        };
        dadosMedicamentoAlteracao.idFabricante = {
            valor: medicamento.inputIdFabricante.value,
            inconsistente: false
        }
        dadosMedicamentoAlteracao.nomeFabricante = medicamento.inputNomeFabricante.value;
        dadosMedicamentoAlteracao.cnpjFabricante = medicamento.inputCnpjFabricante.value;

        try {
            validaDadosMedicamento(dadosMedicamentoAlteracao);
        } catch (e) {
            alert(e.message);
            dadosMedicamentoAlteracao = {};
            return;
        }

        tabelaItens.bootstrapTable('updateByUniqueId', {
            id: dadosMedicamentoAlteracao.estoqueItem,
            row: dadosMedicamentoAlteracao
        });

        dadosMedicamentoAlteracao = {};
        fechaModal();
    }

    function verificaInconsistencias(dados) {
        let inconsistencias = [];
        for (const propriedade in dados) {
            if (dados[propriedade] === null) {
                continue;
            }

            if (dados[propriedade] instanceof Array) {
                for (const item of dados[propriedade]) {
                    inconsistencias = inconsistencias.concat(verificaInconsistencias(item));
                }
            }

            if (typeof dados[propriedade] === 'object' && dados[propriedade].inconsistente) {
                inconsistencias.push(dados[propriedade]);
            }
        }

        return inconsistencias;
    }

    function validaDadosMedicamento(dadosMedicamento) {
        const inconsistencias = verificaInconsistencias(dadosMedicamento);
        if (inconsistencias.length) {
            return inconsistencias[0];
        }
        if (dadosMedicamento.lote === '') {
            throw Error(`Informe o lote do medicamento: ${dadosMedicamento.descricao}.`);
        }
        if (dadosMedicamento.validade === '') {
            throw Error(`Informe a validade do medicamento: ${dadosMedicamento.descricao}.`);
        }
        if (dadosMedicamento.idFabricante === '') {
            throw Error(`Informe o fabricante do medicamento: ${dadosMedicamento.descricao}.`);
        }

        return false;
    }

    function limparFormMedicamento(removeReadOnly = false) {
        if (removeReadOnly) {
            medicamento.inputLote.classList.remove('readonly');
            medicamento.inputLote.readOnly = false;
            medicamento.dataValidade.setReadOnly(false);
            medicamento.inputIdFabricante.classList.remove('readonly');
            medicamento.inputIdFabricante.readOnly = false;
        }
        if (!medicamento.inputLote.readOnly) {
            medicamento.inputLote.value = '';
        }
        if (!medicamento.dataValidade.inputElement.readOnly) {
            medicamento.dataValidade.setValue('');
        }
        if (!medicamento.inputIdFabricante.readOnly) {
            medicamento.inputIdFabricante.value = '';
            medicamento.inputNomeFabricante.value = '';
            medicamento.inputCnpjFabricante.value = '';
        }
    }

    function carregarMovimentacao(dados) {
        const movimentacao = document.getElementById('tipo').value;

        inputCodigo.value = dados.lancamento;
        document.getElementById('info-movimentacao').innerText = dados.tipo;
        document.getElementById('data').value = dados.data;
        document.getElementById('erros-movimentacao').innerHTML = detalheInconsistencias(null, dados);
        document.getElementById('descricao').value = dados.descricao;
        for (const item of dados.itens) {
            estoqueItens.push(item.estoqueItem);
        }

        if (movimentacao === ENTRADA_BNAFAR) {
            if (!dados.nota.inconsistente && !dados.nota.valor) {
                document.getElementById('tr-lancamento-origem').style.display = '';
                document.getElementById('tr-nota').style.display = 'none';
            }

            setValue(dados.tipoMovimentacao, selectTipoMovimentacao);
            unidade.label.innerText = 'Unidade Origem: ';
            cgm.label.innerText = 'CGM Origem: ';
            if (dados.origem === 'unidade') {
                setValue(dados.idOrigem, unidade.inputCodigo, lookUpUnidade);
                unidade.inputDescricao.value = dados.nomeOrigem;
            } else {
                setValue(dados.idOrigem, cgm.inputCodigo, lookUpCgm);
                cgm.inputNome.value = dados.nomeOrigem;
            }

            setValue(dados.nota, inputNotaFiscal);
            dataNotaFiscal.setValue(dados.dataNota);
            dataNotaFiscal.setReadOnly(!dados.nota.inconsistente);
            inputLancamentoOrigem.value = dados.codigoTransferencia;
            inputDataLancamentoOrigem.value = dados.dataTransferencia;
            verificaTipoMovimentacao();
        }

        if (movimentacao === SAIDA_BNAFAR) {
            setValue(dados.tipoMovimentacao, selectTipoMovimentacao);
            unidade.label.innerText = 'Unidade Destino: ';
            cgm.label.innerText = 'CGM Destino: ';
            if (dados.destino === 'unidade') {
                setValue(dados.idDestino, unidade.inputCodigo, lookUpUnidade);
                unidade.inputDescricao.value = dados.nomeDestino;
            } else {
                setValue(dados.idDestino, cgm.inputCodigo, lookUpCgm);
                cgm.inputNome.value = dados.nomeDestino;
            }

            verificaTipoMovimentacao();
        }

        if (movimentacao === DISPENSACAO_BNAFAR) {
            paciente.inputId.value = dados.idPaciente;
            paciente.inputNome.value = dados.nomePaciente;
            setValue(dados.cnsPaciente, paciente.inputCns);
            setValue(dados.cpfPaciente, paciente.inputCpf);
        }
    }

    function setValue(campo, input, lookUp = null) {
        setReadOnly(input, !campo.inconsistente, lookUp);
        input.value = campo.valor !== null ? campo.valor : '';
    }

    function setReadOnly(input, readOnly, lookUp = null) {
        if (lookUp !== null) {
            lookUp.habilitar();
        }
        input.classList.remove('readonly');
        input.readOnly = readOnly;
        input.disabled = readOnly;
        if (readOnly) {
            if (lookUp !== null) {
                lookUp.desabilitar();
            }
            input.classList.add('readonly');
        }
    }

    function setDateValue(campo, objeto) {
        objeto.setReadOnly(!campo.inconsistente);
        objeto.setValue(campo.valor);
    }

    /**
     * detailFormatter bootstrapTable
     * @param index
     * @param row
     * @returns {string}
     */
    function detalheInconsistencias(index, row) {
        const inconsistencias = verificaInconsistencias(row);
        let erros = [];
        for (const inconsistencia of inconsistencias) {
            erros.push(inconsistencia.descricaoErro);
        }

        if (row.errosGenericos !== undefined) {
            erros = erros.concat(row.errosGenericos);
        }

        return `<p style="text-align: left;">${ erros.length ? erros.join('<br>') : 'Sem inconsistências.' }</p>`
    }

    function verificaTipoMovimentacao() {
        const movimentacao = document.getElementById('tipo').value;
        const tipoMovimentacao = selectTipoMovimentacao.value;

        document.getElementById('tr-unidade').style.display = 'none';
        document.getElementById('tr-unidade-cnes').style.display = 'none';
        document.getElementById('tr-cgm').style.display = 'none';
        document.getElementById('tr-cgm-cnpj').style.display = 'none';

        // Para "ajuste de estoque" a unidade destino é quem fez a movimentação.
        if (tipoMovimentacao === '3') {
            unidade.inputCodigo.value = document.getElementById('departamento-logado').value;
            setReadOnly(cgm.inputCodigo, true, lookUpCgm);
            cgm.inputCodigo.value = '';
            cgm.inputNome.value = '';
            return;
        }

        let deParaUnidade = ['7', '10', '11', '12', '14'];
        let deParaCgm = ['1', '2', '9', '11', '12', '13', '14', '15'];
        if (movimentacao === ENTRADA_BNAFAR) {
            deParaUnidade = ['4', '6', '7', '8'];
            deParaCgm = ['1', '4', '5', '6'];
        }

        if (deParaUnidade.includes(tipoMovimentacao)) {
            document.getElementById('tr-unidade').style.display = '';
            document.getElementById('tr-unidade-cnes').style.display = '';
        }
        if (deParaCgm.includes(tipoMovimentacao)) {
            document.getElementById('tr-cgm').style.display = '';
            document.getElementById('tr-cgm-cnpj').style.display = '';
        }

        // Limpa os campos de quem não está sendo exibido
        if (document.getElementById('tr-cgm').style.display === 'none') {
            cgm.inputCodigo.value = '';
            cgm.inputNome.value = '';
        }
        if (document.getElementById('tr-unidade').style.display === 'none'  || unidade.inputDescricao.value === '') {
            unidade.inputCodigo.value = '';
            unidade.inputDescricao.value = '';
        }

        if (cgm.inputCodigo.value !== '') {
            callbackCgm();
        }
        if (unidade.inputCodigo.value !== '') {
            callbackUnidade();
        }
    }
</script>
</body>
