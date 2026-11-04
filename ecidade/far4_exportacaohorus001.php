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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
</head>
<body>
<?php validaDepartamentoLogado('unidade'); ?>
<div class="container">
    <fieldset>
        <legend>Exportação Hórus</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="inputCompetencia" class="bold">Competência: </label>
                </td>
                <td>
                    <input type="text" id="inputCompetencia" style="width: 52px;"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="periodo-inicio" class="bold">Período: </label>
                </td>
                <td>
                    <input type="text" id="periodo-inicio" class="field-size1" value="01">
                    <label for="periodo-fim" class="bold"> Até: </label>
                    <input type="text" id="periodo-fim" class="field-size1">
                </td>
            </tr>
            <tr>
        </table>
        <fieldset class="separator">
            <legend>Procedimentos</legend>
            <table class="form-container">
                <tr>
                    <td style="width: 20px">
                        <input id="checkboxEntrada"
                               data-permite-envio="false"
                               data-id="1"
                               name="procedimento"
                               type="checkbox"
                               value="1">
                    </td>
                    <td>
                        <label class="field-size5" for="checkboxEntrada"> Entrada </label>
                    </td>
                    <td class="field-size4" style="text-align: right">
                        <span id="situacaoEntrada" data-id="1"></span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20px">
                        <input id="checkboxSaida"
                               data-permite-envio="false"
                               data-id="2"
                               name="procedimento"
                               type="checkbox"
                               value="2">
                    </td>
                    <td>
                        <label for="checkboxSaida"> Saída </label>
                    </td>
                    <td class="field-size4" style="text-align: right">
                        <span id="situacaoSaida" data-id="2"></span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 20px">
                        <input id="checkboxDispensacao"
                               data-permite-envio="false"
                               data-id="3"
                               name="procedimento"
                               type="checkbox"
                               value="3">
                    </td>
                    <td>
                        <label for="checkboxDispensacao"> Dispensação </label>
                    </td>
                    <td class="field-size4" style="text-align: right">
                        <span id="situacaoDispensacao" data-id="3"></span>
                    </td>
                </tr>
            </table>
        </fieldset>
    </fieldset>
    <button type="button" onclick="openConsultaProtocolo()">
        <i class="fas fa-search"></i>
        Protocolos
    </button>
    <button type="button" id="btnConsistirDados" onclick="consistirDados()" readonly disabled>
        <i class="fas fa-check-double"></i>
        Consistir Dados
    </button>
    <button type="button" id="btnProcessar" onclick="processar()" readonly disabled>
        <i class="fas fa-cog"></i>
        Processar
    </button>
</div>

<div id="wndProtocolos">
    <div class="subcontainer">
        <fieldset>
            <legend></legend>
            <table id="data-table-protocolos" class="table table-sm"></table>
        </fieldset>
    </div>
</div>
</body>
</html>
<?php db_menu(); ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script>
    $.noConflict();
    const routes = {
        consultaProtocolo: 'saude/farmacia/consulta/bnafar/protocolo',
        relatorioProtocolo: 'saude/farmacia/relatorio/bnafar/protocolo',
        validar: 'saude/farmacia/procedimento/bnafar/validar',
        consistir: 'saude/farmacia/procedimento/bnafar/consistir',
        exportarLote: 'saude/farmacia/procedimento/bnafar/exportar',
        reprocessar: 'saude/farmacia/procedimento/bnafar/protocolo/reprocessar'
    };

    const inputCompetencia = document.getElementById('inputCompetencia');
    const inputPeriodoInicio = document.getElementById('periodo-inicio');
    const inputPeriodoFim = document.getElementById('periodo-fim');
    const procedimentos = document.getElementsByName('procedimento');
    const btnConsistirDados = document.getElementById('btnConsistirDados');
    const btnProcessar = document.getElementById('btnProcessar');

    const dataAtual = new Date();

    const wndProtocolos = new windowAux('wndProtocolos', 'Consulta de Protocolos', 900, 560);
    wndProtocolos.setContent(document.getElementById('wndProtocolos'));
    wndProtocolos.setShutDownFunction(() => {
       if (!!wndProtocolos.oDBMask) {
           wndProtocolos.oDBMask.destroy();
       }
       wndProtocolos.hide();
    });

    const tableProtocolos = jQuery('#data-table-protocolos');
    tableProtocolos.createTable = () => {
        const buttons = () => {
            return {
                btnImprimir: {
                    text: 'Imprimir Página',
                    icon: 'fas fa-print',
                    event: imprimirProtocolo
                }
            };
        };

        const tableHistorico = (row, detail) => {
            const table = detail.html('<table></table>').find('table');

            table.bootstrapTable({
                columns: [
                    {
                        field: 'data',
                        title: 'Data',
                        halign: 'center',
                        align: 'center'
                    },
                    {
                        field: 'erro',
                        title: 'Erro',
                        halign: 'center',
                        align: 'center'
                    },
                ],
                data: row.historico
            });
        };

        const acoes = {
            'click .reprocessar': async (e, d, row) => {
                const formData = new FormData();
                formData.append('protocolo', row.protocolo);
                formData.append('procedimento', row.procedimento);

                PHPSession.appendFormData(formData);
                const response = await HttpClient.post(
                    `${PHPSession.requestApi}/${routes.reprocessar}`,
                    { body: formData }
                );
                if (response.error) {
                    alert(response.message);
                    return;
                }

                openConsultaProtocolo();
            }
        };

        tableProtocolos.bootstrapTable('destroy');
        tableProtocolos.bootstrapTable({
            height: 464,
            buttons: buttons,
            showButtonText: true,
            sidePagination: 'server',
            pagination: true,
            detailView: true,
            pageSize: 10,
            pageList: [10],
            columns: [
                {
                    field: 'protocolo',
                    title: 'Protocolo',
                    halign: 'center',
                    width: 60
                },
                {
                    field: 'codigoIbge',
                    title: 'IBGE',
                    halign: 'center',
                    width: 60
                },
                {
                    field: 'usuarioEnvio',
                    title: 'Usuário BNAFAR',
                    halign: 'center',
                    width: 110
                },
                {
                    field: 'dataProtocolo',
                    title: 'Data',
                    halign: 'center',
                    width: 130
                },
                {
                    field: 'situacao',
                    title: 'Situação',
                    halign: 'center',
                    width: 130
                },
                {
                    field: 'tipoServico',
                    title: 'Serviço',
                    halign: 'center',
                    width: 110
                },
                {
                    field: 'tipoOperacao',
                    title: 'Operação',
                    halign: 'center',
                    width: 80
                },
                {
                    field: 'situacaoSistema',
                    title: 'Situação no Sistema',
                    halign: 'center',
                    width: 130
                },
                {
                    field: 'acoes',
                    title: 'Ações',
                    halign: 'center',
                    align: 'center',
                    events: acoes,
                    formatter: (a, row) => {
                        if (!row.permiteReprocessamento) {
                            return '&nbsp;';
                        }

                        return '<a class="reprocessar" title="Reprocessar"><i class="fas fa-sync"></i></a>'
                    }
                }
            ],
            onPageChange: (number, size) => {
                consultarProtocolos(number, size);
            },
            onExpandRow: (index, row, detail) => {
                tableHistorico(row, detail);
            }
        });
    };

    inputCompetencia.addEventListener('change', () => {
        const [mes, ano] = inputCompetencia.value.split("/");
        if (ano === undefined) {
            if (mes.length >= 5 && mes.length <= 6) {
                setCompetencia(mes.substring(mes.length - 4, mes.length), mes.substring(0, mes.length - 4));
                return;
            }
            alert('Competência inválida.');
            setCompetencia(dataAtual.getFullYear(), dataAtual.getMonth() + 1);
            return;
        }
        setCompetencia(ano, mes);
    });

    inputPeriodoInicio.addEventListener('change', () => callbackPeriodo(inputPeriodoInicio, inputPeriodoFim));
    inputPeriodoFim.addEventListener('change', () => callbackPeriodo(inputPeriodoFim, inputPeriodoInicio));

    window.onload = () => {
        setCompetencia(dataAtual.getFullYear(), dataAtual.getMonth() + 1);
    }

    async function imprimirProtocolo() {
        const data = tableProtocolos.bootstrapTable('getData');
        const formData = new FormData();
        formData.append('data', JSON.stringify(data));

        PHPSession.appendFormData(formData);
        const response = await HttpClient.post(
            `${PHPSession.requestApi}/${routes.relatorioProtocolo}`,
            { body: formData }
        );
        if (response.error) {
            alert(response.message);
            return;
        }

        window.open(response.data.path, 'relatorio_protocolo', 'popup');
    }

    function openConsultaProtocolo() {
        wndProtocolos.show(0, 0, true);
        tableProtocolos.createTable();

        consultarProtocolos();
    }

    async function consultarProtocolos(pagina = 1, tamanho = 10) {
        const formData = new FormData();
        const competencia = inputCompetencia.value;
        formData.append('competencia', competencia);
        formData.append('pagina', pagina.toString());
        formData.append('tamanho', tamanho.toString());

        PHPSession.appendFormData(formData);

        const response = await HttpClient.post(
            `${PHPSession.requestApi}/${routes.consultaProtocolo}`,
            { body: formData }
        );
        if (response.error) {
            alert(response.message);
            return;
        }

        tableProtocolos.bootstrapTable('load', response.data);
    }

    async function consistirDados() {
        const formData = new FormData();
        const [periodoInicio, periodoFim] = getPeriodo();
        formData.append('periodoInicio', periodoInicio.toISOString());
        formData.append('periodoFim', periodoFim.toISOString());

        const procedimentos = document.querySelectorAll('[data-permite-envio="true"]');
        for (let procedimento of procedimentos) {
            formData.append('procedimentos[]', procedimento.value);
        }

        PHPSession.appendFormData(formData);

        const response = await HttpClient.post(`${PHPSession.requestApi}/${routes.consistir}`, { body: formData });
        if (response.error) {
            alert(response.message);
            return;
        }

        callbackConsistirDados(response.data);
    }

    function callbackConsistirDados(dados) {
        inputPeriodoInicio.readOnly = true;
        inputPeriodoInicio.classList.add('readonly');
        inputPeriodoFim.readOnly = true;
        inputPeriodoFim.classList.add('readonly');

        let possuiInconsistencia = false;
        const download = new DBDownload();
        download.addGroups('pdf', 'Inconsistências encontradas');
        for (const procedimento of dados) {
            const chkProcedimento = document.querySelector(`input[data-id="${procedimento.tipo}"]`);
            chkProcedimento.removeAttribute("disabled");
            chkProcedimento.removeAttribute("readonly");
            chkProcedimento.checked = false;

            if (procedimento.inconsistente) {
                chkProcedimento.setAttribute("readonly", "readOnly");
                chkProcedimento.setAttribute("disabled", "disabled");
                possuiInconsistencia = true;
                download.addFile(procedimento.relatorio.path, procedimento.relatorio.name, 'pdf');
            }
        }

        if (possuiInconsistencia && confirm('Deseja imprimir o relatório com as inconsistências dos dados?')) {
            download.show(null);
        }

        btnProcessar.removeAttribute('readonly');
        btnProcessar.removeAttribute('disabled');
    }

    async function processar() {
        const formData = new FormData();

        for (let procedimento of procedimentos) {
            if (procedimento.checked && procedimento.dataset.permiteEnvio === 'true') {
                formData.append('procedimentos[]', procedimento.value);
            }
        }

        if (!formData.has('procedimentos[]')) {
            alert('Selecione ao menos um arquivo para a exportação.');
            return;
        }

        const [periodoInicio, periodoFim] = getPeriodo();
        formData.append('periodoInicio', periodoInicio.toISOString());
        formData.append('periodoFim', periodoFim.toISOString());

        PHPSession.appendFormData(formData);

        const response = await HttpClient.post(`${PHPSession.requestApi}/${routes.exportarLote}`, { body: formData });
        alert(response.message);
        if (response.error) {
            return;
        }

        await validar();
        // se o botão consistir ficou disponível, significa que o usuário ainda tem procedimentos que podem processar
        if (!btnConsistirDados.disabled) {
            btnProcessar.removeAttribute('readonly');
            btnProcessar.removeAttribute('disabled');
        }
    }

    /**
     * Verifica a competência a ser gerada e a situação dos procedimentos
     */
    async function validar() {
        if (PHPSession.requestApi === undefined) {
            await PHPSession.loadData();
        }

        const formData = new FormData();
        const [periodoInicio, periodoFim] = getPeriodo();
        formData.append('periodoInicio', periodoInicio.toISOString());
        formData.append('periodoFim', periodoFim.toISOString());

        PHPSession.appendFormData(formData);

        const response = await HttpClient.post(`${PHPSession.requestApi}/${routes.validar}`, { body: formData });
        if (response.error) {
            alert(response.message);
            return;
        }

        callbackValidar(response.data);
    }

    function callbackValidar(procedimentos) {
        btnConsistirDados.setAttribute('disabled', 'disabled');
        btnProcessar.setAttribute('disabled', 'disabled');
        inputPeriodoInicio.readOnly = false;
        inputPeriodoInicio.classList.remove('readonly');
        inputPeriodoFim.readOnly = false;
        inputPeriodoFim.classList.remove('readonly');
        /**
         * Percorre os procedimentos e define a mensagem e a cor que deve ser impresso na tela
         * Também valida se é permitido o envio do procedimento e
         * caso nenhum seja permitido mantém o botão processar bloqueado.
         */
        for (const procedimento of procedimentos) {
            const spanSituacao = document.querySelector(`span[data-id="${procedimento.tipo}"]`);
            const chkProcedimento = document.querySelector(`input[data-id="${procedimento.tipo}"]`);

            spanSituacao.innerHTML = procedimento.situacao;
            spanSituacao.style.color = procedimento.corSituacao;

            chkProcedimento.setAttribute("readonly", "readOnly");
            chkProcedimento.setAttribute("disabled", "disabled");
            chkProcedimento.checked = false;
            chkProcedimento.dataset.permiteEnvio = 'false';

            if (procedimento.permiteEnvio) {
                chkProcedimento.dataset.permiteEnvio = 'true';
                btnConsistirDados.removeAttribute("disabled");
                btnConsistirDados.removeAttribute("readonly");
            }
        }
    }

    function setCompetencia(ano, mes) {
        const data = new Date(ano, Number(mes) - 1);
        if (data.getMonth() > dataAtual.getMonth() && data.getFullYear() >= dataAtual.getFullYear()) {
            alert('A competência não pode ser maior que a competência atual.');
            setCompetencia(dataAtual.getFullYear(), dataAtual.getMonth() + 1);
            return;
        }

        mes = mes.toString().padStart(2, '0');
        inputCompetencia.value = `${mes}/${ano}`;
        inputPeriodoInicio.value = '01';
        inputPeriodoFim.value = (new Date(data.getFullYear(), data.getMonth() + 1, 0)).getDate();

        validar();
    }

    function callbackPeriodo(input, outroInput) {
        btnConsistirDados.setAttribute('disabled', 'disabled');
        btnProcessar.setAttribute('disabled', 'disabled');

        if (isNaN(input.value) || input.value > 31 || input.value <= 0) {
            alert('Valor inválido.')
            input.value = '';
        }

        if (input.value === '') {
            const ultimoDia = (new Date(dataAtual.getFullYear(), dataAtual.getMonth() + 1, 0)).getDate();
            input.value = Number(outroInput.value) === ultimoDia ? '01' : ultimoDia;
        }

        if (input.value.length === 1) {
            input.value = `0${input.value}`;
        }

        validar();
    }

    function getPeriodo() {
        const [mes, ano] = inputCompetencia.value.split('/');
        const periodoInicio = new Date(ano, Number(mes) - 1, Number(inputPeriodoInicio.value));
        const periodoFim = new Date(ano, Number(mes) - 1, Number(inputPeriodoFim.value));

        return [periodoInicio, periodoFim];
    }
</script>
