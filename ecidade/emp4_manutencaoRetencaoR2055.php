<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2022  DBSeller Servicos de Informatica
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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("classes/empenho.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>

<!doctype html>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="ECIDADE_REQUEST_PATH" content="<?= ECIDADE_REQUEST_PATH ?>">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script src="public/js/app.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
</head>
<body>
    <form name='form1' action='javascript:;'>
        <input type="hidden" name="e158_sequecial" id="e158_sequencial">
        <input type="hidden" name="cgm" id="cgm">

        <div class="container">
            <!-- Dados da Prestação de serviço -->
            <fieldset>
                <legend>Dados da Prestação de serviço</legend>
                <table class="form-container">
                    <tr>
                        <td>
                            <label for="nome_prestador"><b>Razão Social / Nome: </b></label>
                        </td>
                        <td>
                            <input type="text" name="prestador" id="prestador" disabled size="50">
                            <input type="text" name="cgccpf" id="cgccpf" disabled size="20" data-mask="cgccpf">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="empenho"><b>Empenho: </b></label>
                        </td>
                        <td>
                            <input type="text" name="empenho" id="empenho" disabled hidden>
                            <input type="text" name="empenho_numero" id="empenho_numero" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="codigo_nota"><b>Nota de Liquidação: </b></label>
                        </td>
                        <td>
                            <input type="text" name="nota" id="nota" disabled>
                        </td>
                    </tr>
                    <!-- indicativo de aquisição de produção rural -->
                    <tr>
                        <td><b>Tipo de Aquisicão: <span style="color: red;">*</span></b></td>
                        <td>
                            <select name="indAqProd" id="indAqProd">
                                <option value="" selected disabled>Selecione</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <fieldset>
                <legend>Dados Adicionais</legend>
                <table class="form-container">
                    <tbody>
                        <tr>
                            <td>
                                <label for="e158_vlrsenar"><b>Senar 0,2%: </b></label>
                            </td>
                            <td>
                                <input type="text" name="e158_vlrsenar" id="e158_vlrsenar" data-mask="money">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="e158_vlrrat"><b>Gilrat 0,1%: </b></label>
                            </td>
                            <td>
                                <input type="text" name="e158_vlrrat" id="e158_vlrrat" data-mask="money">
                            </td>
                        </tr>
                        <tr style="display: none;" class="pessoa_fisica">
                            <td>
                                <label for="e158_vlrgilrat"><b>Valor CP 1,2%: (contribuição previdenciária)
                                    </b></label>
                            </td>
                            <td>
                                <input type="text" name="e158_vlrcp" id="e158_vlrcp" data-mask="money">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
            <br>
            <fieldset>
                <legend>Processos Judiciais</legend>
                <fieldset>
                    <table class="form-container">
                        <tr>
                            <td>
                                <label for="processo_numero"><a href="#" id="ancoraProcesso">Processo: </a></label>
                            </td>
                            <td>
                                <input type="hidden" name="processo_index" id="processo_index">
                                <input type="text" name="processo_numero" id="processo_numero" lang="efd02_processo" readonly>
                                <input type="hidden" name="processo_descricao" id="processo_descricao" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="processo_senar"><b>Valor não retido Senar: </b></label>
                            </td>
                            <td>
                                <input type="text" name="processo_senar" id="processo_senar" data-mask="money">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="processo_rat"><b>Valor não retido Gilrat: </b></label>
                            </td>
                            <td>
                                <input type="text" name="processo_rat" id="processo_rat" data-mask="money">
                            </td>
                        </tr>
                        <tr style="display: none;" class="pessoa_fisica">
                            <td>
                                <label for="processo_cp"><b>Valor não retido CP: </b></label>
                            </td>
                            <td>
                                <input type="text" name="processo_cp" id="processo_cp" data-mask="money">
                            </td>
                        </tr>
                        <br>
                        <tr>
                            <td>
                                <button onclick="js_addProcesso()">Lançar</button>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>Lançados</legend>
                    <div style="width: 700;">
                        <table id="data-table" class="table table-sm center" data-height="250"
                            data-virtual-scroll="true" style="width: 100%;">
                        </table>
                    </div>
                </fieldset>
            </fieldset>
        </div>
        <div class="center">
            <button onclick="js_sendForm()">Salvar</button>
        </div>
    </form>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
</body>
</html>
<script>
    $.noConflict();

    /**
     * API (controller) das retencoes
     * */
    const API = 'v4/api/integracoes/efd-reinf/retencao';

    /** Variavel de armazenamento dos dados da rentencao */
    let retencao = {};

    /** Variavel de armazenamento dos dados dos processos */
    let processos = [];

    /** processos a serem excluidos */
    let processosToRemove = [];

    /** Grid de processos */
    let table = jQuery('#data-table');

    /**
     * Loockup do cgm
     * */
    const lookUpProcesso = new DBLookUp($('ancoraProcesso'), $('processo_numero'), $('processo_descricao'), {
        'sArquivo': 'func_efd_processos.php',
        'sLabel': 'Pesquisar Processos EFD Reinf',
        'sObjetoLookUp': "db_iframe_efd_processos"
    });

    /**
     * Funcoes iniciadas no carregamento inicial
     */
    document.addEventListener('DOMContentLoaded', init);

    function init() {
        js_setRetencaoFromSessionStorage();
        js_fillForm(retencao);
        js_enablePessoFisica();
        js_getTipoaAquisicaoProducaoRuralLabels();
        js_getProcessos();
        js_montaGridProcesso();
    };

    /**
     * Funcao para prencher o formulario
     * onde a chave do objeto informado corresponde ao id do input, select..
     */
    function js_fillForm(fields) {
        let inputsNames = [...Object.keys(fields)]
        inputsNames.forEach(key => {
            let el = document.querySelector('#' + key);
            if (document.body.contains(el)) {
                el.value = fields[key] ?? ""
            }
        });

        js_maskInputs();
    }

    /**
     * Mascara dos inputs
     */
    function js_maskInputs() {
        let elemets = document.querySelectorAll('[data-mask]');

        for (const el of elemets) {
            switch (el.dataset.mask) {
                case 'money':
                    el.setAttribute('placeholder', '00,00');
                    el.value = (el.value != '') ? js_formatar(el.value, 'f') : '';
                    el.addEventListener('input', e => jsFormataMoeda(e.target));
                    break;
                case 'date':
                    el.setAttribute('placeholder', '__/__/____');
                    el.value = (el.value != '') ? js_formatar(el.value, 'd') : '';
                    break;
                case 'cgccpf':
                    if (el.value.length == 14) {
                        el.value = el.value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3.$4-$5");
                    } else {
                        el.value = el.value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
                    }
                    break;
            }
        }
    }

    /**
     * Funcao para recuperar os dados do sessionStoroge setado
     * na pagina pai e colocar na variavel global retencao
     */
    function js_setRetencaoFromSessionStorage() {
        let json = sessionStorage.getItem('retencao');
        console.log(json);
        retencao = JSON.parse(json);
    }

    /**
     * validar formulario
     */
    function js_validateForm() {
        if ($F('indAqProd') == '') {
            alert('Indicativo de aquisição deve ser informado');
            return false;
        }

        return true;
    }

    /**
     * Cria objeto com os valores dos inputs
     */
    function js_mapForm() {
        let obj = {};
        let value = '';
        let form = document.querySelector("[name='form1']");

        for (const el of form.elements) {

            switch (el.dataset.mask) {
                case 'money':
                    value = jsRemoveMascaraMoeda(el.value);
                    break;
                default:
                    value = el.value;
                    break;
            }

            obj[el.id] = value;
        }

        return obj;
    }

    /**
     * Envia formulario
     */
    async function js_sendForm() {
        if (!js_validateForm()) {
            return false;
        }

        js_divCarregando("Aguarde, Salvando dados da retenção", "msgRetencao");

        try {
            const action = `${API}/save-retencao`;

            let params = js_mapForm();
            params.evento = 'r2055';

            params.processos = (processos.length) ?  processos.map((i) => { i.json = ''; return i; }) : [];
            params.processosToRemove = (processosToRemove.length) ? processosToRemove.map((i) => { i.json = ''; return i; }) : [];

            const response = await axios.post(action, params);
            const responseData = response.data;

            if (responseData.error) {
                alert(responseData.message);
                return false;
            }

            parent.js_getRetencoes();
            js_getProcessos();
            alert('Os dados da retenção foram salvos com sucesso');

        } catch (error) {
            alert(`Erro ao salvar Retenção`);
            console.error('An error occurred:', error.message);
        } finally {
            js_removeObj("msgRetencao");
        }
    }

    /** Habilitar campos pesso física */
    function js_enablePessoFisica() {
        let prestador = $F('cgccpf');
        prestador = prestador.replace(/[^\d]/g, '');

        if (prestador.length == 11) {
            let campos = document.querySelectorAll('.pessoa_fisica')
            campos.forEach(i => i.style.display = 'table-row');
        }
    }

    /**
     * Labels do indicativo de aquisição
     * */
    async function js_getTipoaAquisicaoProducaoRuralLabels() {
        const RPC        = 'emp4_tipoaquisicaoproducaorural.RPC.php';
        const indAqProd  = document.querySelector("#indAqProd");

        let cgm      = retencao.cgm;
        let params   = JSON.stringify({exec: 'getLabels', cgm: cgm});
        let formData = new FormData();
        let response = '';
        let labels   = [];
        let option   = '';

        js_divCarregando('Buscando os tipos de aquisição para produtor rural...', 'aqProdLoad');

        try {
            formData.append('json', params);
            response = await fetch(RPC, {method: 'post', body: formData});
            data = await response.json();

            if (!data.erro && data.labels) {
                labels = data.labels;
                labels.forEach(item => {
                    option = document.createElement('option');
                    option.value = item.value;
                    option.innerHTML = item.value + ' - ' + item.name;
                    indAqProd.appendChild(option);
                });

                if (retencao.indAqProd) {
                    indAqProd.value = retencao.indAqProd;
                }
            }
        } catch (error) {
            console.error(error);
        }

        js_removeObj('aqProdLoad');
    }

    /**
     * Adiciona Processo na lista
     *
     */
    function js_addProcesso(event) {
        if ($F('processo_numero') == '') {
            alert('Você deve informar o processo');
            return false;
        }

        js_divCarregando('Adicionando...', 'processoGrid');

        let processo = {};
        let index = $F('processo_index');

        processo.numero = $F('processo_numero');
        processo.rat    = $F('processo_rat');
        processo.cp     = $F('processo_cp');
        processo.senar  = $F('processo_senar');
        processo.json   = JSON.stringify(processo);

        if (index) {
            Object.assign(processos[index], processo);
        } else {
            processos.push(processo);
        }

        js_clearFormProcesso();
        js_montaGridProcesso();
        js_removeObj('processoGrid');
    }

    /**
     * Limpa form processo
     */
    function js_clearFormProcesso() {
        document.querySelector('#processo_index').value = '';
        document.querySelector('#processo_numero').value = '';
        document.querySelector('#processo_rat').value = '';
        document.querySelector('#processo_cp').value = '';
        document.querySelector('#processo_senar').value = '';
    }

    /**
     * Remove processo da lista
     */
    function js_delProcesso(event) {
        let index = event.dataset.index;
        let data  = JSON.parse(event.dataset.processo);

        processos.splice(index, 1);

        //verificar se tem que excluir do banco
        if (data.id) {
            processosToRemove.push(data.id);
        }

        js_montaGridProcesso();
    }

    /**
     * Edita processo
     */
    function js_editProcesso(event) {
        let index = event.dataset.index;
        let data  = event.dataset.processo;
        let processo = processos[index];

        document.querySelector('#processo_index').value = index;
        document.querySelector('#processo_numero').value = processo.numero;
        document.querySelector('#processo_rat').value = processo.rat;
        document.querySelector('#processo_cp').value = processo.senar;
        document.querySelector('#processo_senar').value = processo.senar;
    }

    /**
     * Monta grid de processos
     */
    function js_montaGridProcesso() {
        const table = jQuery('#data-table');

        const callbackAcoes = (value, row, index, field) => {
            const btnAlterar = `<a href="#" data-processo='${row.json}' data-index="${index}" onclick="js_editProcesso(this)">
                <i class="fas fa-edit"></i></a> `;

            const btnExcluir = `<a href="#" data-processo='${row.json}' data-index="${index}" onclick="js_delProcesso(this)">
                <i class="fas fa-trash"></i></a>`;

            return btnAlterar + btnExcluir;
        }

        table.bootstrapTable('destroy');
        table.bootstrapTable({
            data: processos,
            locale: 'pt-BR',
            columns: [
                {
                    filed: 'json',
                    visible: false,
                },
                {
                    title: 'Número',
                    field: 'numero',
                    sortable: true,
                    align: 'center',
                },
                {
                    title: 'Valor Gilrat',
                    field: 'rat',
                    align: 'center',
                },
                {
                    title: 'Valor Senar',
                    field: 'senar',
                    align: 'center',
                },
                {
                    field: 'acoes',
                    title: 'Ações',
                    align: 'center',
                    formatter: callbackAcoes
                }
            ]
        });
    }

    /**
     * Recurso para trazer os
     * processos cadastrados
     */
    async function js_getProcessos() {
        const rpc      = 'emp4_aquisicaoproducaoruralprocessos.RPC.php';
        const formData = new FormData();
        const params   = {
            exec: 'getProcessos',
            retencao: retencao.e158_sequencial
        }

        formData.append('json', JSON.stringify(params));

        try {
            let response = await fetch(rpc, {method: 'post', body: formData});
            let data = await response.json();

            if (!data.lErro && data.processos) {
                let processos_formated = data.processos.map((i) => { i.json = JSON.stringify(i); return i;});
                processos = processos_formated;
                js_montaGridProcesso();
            }
        } catch (error) {
            console.error(error);
        }
    }
</script>
