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

<!Doctype html>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="ECIDADE_REQUEST_PATH" content="<?= ECIDADE_REQUEST_PATH ?>">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

    <!-- bootstrap table -->
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <style>
        .bad-row {
            color: #dc3545;
        }

        .warning-row {
            color: #ffc107;
        }

        .ok-row {
            color: #28a745;
        }
    </style>
</head>

<body>
    <form name='form1' action='javascript:;'>
        <div class="container">
            <fieldset style="width: 40%">
                <legend>Filtros</legend>

                <table class="form-container">
                    <!-- instit -->
                    <tr>
                        <td>
                            <input type="hidden" id="instit" value="<?= db_getsession('DB_instit') ?>">
                        </td>
                    </tr>

                    <!-- Evento -->
                    <tr>
                        <td>
                            <label for="evento">Evento Reinf: </label>
                        </td>
                        <td>
                            <select name="evento" id="evento">
                                <option value="r2010">R-2010</option>
                                <option value="r2055">R-2055</option>
                                <option value="r4010">R-4010</option>
                                <option value="r4020">R-4020</option>
                                <option value="r4040">R-4040</option>
                            </select>
                        </td>
                    </tr>

                    <!-- cgm -->
                    <tr>
                        <td title="Número do cgm">
                            <label for="ancoraCgm">
                                <a href="#" id="ancoraCgm">Cgm: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" name="z01_numcgm" id="z01_numcgm">
                            <input type="text" name="z01_nome" id="z01_nome">
                        </td>
                    </tr>

                    <!-- orgao / unidade -->
                    <tr class="d-none" id="trOrgao">
                        <td>
                            <label for="o40_orgao">
                                <a href="#" id="ancoraOrgao">Órgão: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="o40_orgao">
                            <input type="text" id="o40_descr">
                        </td>
                    </tr>
                    <tr class="d-none" id="trUnidade">
                        <td>
                            <label for="o41_unidade">
                                <a href="#" id="ancoraUnidade">Unidade: </a>
                            </label>
                        </td>
                        <td>
                            <input type="text" id="o41_unidade">
                            <input type="text" id="o41_descr">
                        </td>
                    </tr>

                    <!--nota fiscal-->
                    <tr class="d-none" id="filtro-nf">
                        <td><label for="nota" id="">Nota Fiscal: </label></td>
                        <td><input type="text" name="nota" id="nota"></td>
                    </tr>

                    <!-- periodo -->
                    <tr>
                        <td>
                            <span style="cursor: help;" title="" id="title-periodo">
                                Período:
                            </span>
                        </td>
                        <td>
                            <?php db_inputdata("dataNotaInicial", null, null, null, true, "text", 1); ?> à
                            <?php db_inputdata("dataNotaFinal", null, null, null, true, "text", 1); ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <button name="pesquisar" id="pesquisar" onClick="js_getRetencoes()">Pesquisar</button>
        </div>
        <br>
    </form>
    <div style="width: 70%; display: none;" class="subcontainer" id="notasContainer">
        <div class="alert alert-danger d-none" id="rowErrorAlert">
            <i class="fa fa-exclamation-triangle"></i> <b>Existem notas inconsistentes.</b>
        </div>
        <fieldset>
            <legend>Notas</legend>
            <table id="gridNotas" class="table table-sm" data-height="250" data-virtual-scroll="true" style="width: 100%;">
            </table>
        </fieldset>
    </div>
    <script src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script src="assets/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
    <script src="scripts/classes/http/http.js"></script>
    <script src="scripts/session.js"></script>
    <script src="public/js/app.js"></script>
    <script src="scripts/classes/efdreinf/retencao/GridR2010.js"></script>
    <script src="scripts/classes/efdreinf/retencao/GridR2055.js"></script>
    <script src="scripts/classes/efdreinf/retencao/GridR4010.js"></script>
    <script src="scripts/classes/efdreinf/retencao/GridR4020.js"></script>
    <script src="scripts/classes/efdreinf/retencao/GridR4040.js"></script>
    <script>
        $.noConflict();

        /**
         * Sessoes
         */
        const instit = $F('instit');

        /*
         * EFDReinf - Configuracoes
         */
        let efdConfig = false;

        /**
         * API (controller) das retencoes
         * */
        const API = 'v4/api/integracoes/efd-reinf/retencao';

        /**
         * Loockup do cgm
         * */
        const lookUpCgm = new DBLookUp($('ancoraCgm'), $('z01_numcgm'), $('z01_nome'), {
            'sArquivo': 'func_cgm.php',
            'sLabel': 'Pesquisar Cgm',
            'sObjetoLookUp': "db_iframe_cgm"
        });

        /**
         * Loockup do Orgao
         * */
        const lookUpOrgao = new DBLookUp($('ancoraOrgao'), $('o40_orgao'), $('o40_descr'), {
            'sArquivo': 'func_orcorgao.php',
            'sLabel': 'Pesquisar Órgão',
            'sObjetoLookUp': 'db_iframe_orgao',
            'aParametrosAdicionais': [`instit=${instit}`],
            'fCallBack': () => {
                lookUpUnidade.desabilitar();

                $('o41_unidade').value = '';
                $('o41_descr').value = '';

                if ($F('o40_orgao')) {
                    let param = [
                        'orgao=' + $F('o40_orgao'),
                        `instit=${instit}`
                    ];
                    lookUpUnidade.setParametrosAdicionais(param);
                    lookUpUnidade.habilitar();
                }
            }
        });

        /**
         * Loockup do Unidade
         */
        const lookUpUnidade = new DBLookUp($('ancoraUnidade'), $('o41_unidade'), $('o41_descr'), {
            'sArquivo': 'func_orcunidade.php',
            'sLabel': 'Pesquisar Unidade',
            'sObjetoLookUp': 'db_iframe_unidade',
        });
        lookUpUnidade.desabilitar();

        /**
         * Janela auxiliar para exibir os alertas das retencoes
         */
        const windowAuxRetencao = new windowAux('win', '', 500, 350);

        /**
         * Init grid
         */
        const gridNotas = jQuery('#gridNotas');

        /**
         * Container das notas
         */
        const notasContainer = document.querySelector('#notasContainer');

        /**
         * Evento El
         */
        const eventos = document.querySelector('#evento');
        eventos.addEventListener('change', changeTitlePeriodo);
        eventos.dispatchEvent(new Event('change'));

        /**
         * Entrypoint
         */
        PHPSession.loadData().then(async () => {
            await getEfdConfig()
            enabledFiltroOrgaounidade();
        });

        function enabledFiltroOrgaounidade() {
            if (efdConfig && efdConfig.efd07_filtraorgaounidade) {
                const trOrgao = document.querySelector('#trOrgao');
                const trUnidade = document.querySelector('#trUnidade');

                trOrgao.classList.remove('d-none');
                trUnidade.classList.remove('d-none');
            }
        }

        /*
         * Request para obter as configuracoes do efd-reinf
         */
        async function getEfdConfig() {
            const url = PHPSession.requestApi;
            const api = url + '/integracoes/efd-reinf/configuracao/get';

            let response = false;
            const formData = new FormData();

            formData.append('get', true);
            PHPSession.appendFormData(formData);

            response = await HttpClient.post(api, {
                body: formData
            });
            if (response.error) {
                let msg = "Erro ao buscar as configurações do efd-reinf: \n" + response.message;
                alert(msg);
                return false;
            }

            if (response.data) {
                efdConfig = response.data;
            }
        }

        /**
         * Request para retornar as retencoes de
         * acordo com os filtros de pesquisa
         * */
        async function js_getRetencoes() {
            if (!js_validateFilter()) {
                return false;
            }

            js_divCarregando("Aguarde, pesquisando retencões.", "msgBox");

            const action = `${API}/get-retencoes`;

            let params = {
                nota: $F('nota'),
                cgm: $F('z01_numcgm'),
                periodo: [$F('dataNotaInicial'), $F('dataNotaFinal')],
                evento: $F('evento')
            }

            // se tiver o filtro orgaounidade habilitado
            if ($F('o40_orgao') || $F('o41_unidade')) {
                params.orgaoUnidade = true;
                params.orgao = $F('o40_orgao');
                params.unidade = $F('o41_unidade');
            }

            try {
                const response = await window.axios.get(action, {
                    params: params
                });
                const responseData = response.data;
                const closingDates = await getClosingDates();

                if (responseData.error) {
                    alert(responseData.message);
                    return false;
                }

                // monta grid
                notasContainer.style.display = 'block';
                switch ($F('evento')) {
                    case 'r2010':
                        new GridR2010(responseData.data, gridNotas, closingDates);
                        break;

                    case 'r2055':
                        new GridR2055(responseData.data, gridNotas, closingDates);
                        break;

                    case 'r4010':
                        new GridR4010(responseData.data, gridNotas, closingDates);
                        break;

                    case 'r4020':
                        new GridR4020(responseData.data, gridNotas, closingDates);
                        break;

                    case 'r4040':
                        new GridR4040(responseData.data, gridNotas);
                        break;

                    default:
                        alert('Erro: Evento não encontrado.');
                        break;
                }

                showAlertRowsWithErros();
            } catch (error) {
                alert('Erro ao buscar retenções');
                console.error('An error occurred:', error.message);
            } finally {
                js_removeObj("msgBox");
            }
        }

        /**
         * Modal/Action da janela de alterar a retencao
         * */
        function js_manutencaoRentecao(ev, evento) {
            let retencao = ev.dataset.retencao;
            let action = '';

            switch (evento) {
                case 'r2010':
                    action = 'emp4_manutencaoRetencaoR2010.php';
                    break;

                case 'r2055':
                    action = 'emp4_manutencaoRetencaoR2055.php';
                    break;

                case 'r4010':
                    action = 'web/integracoes/efd-reinf/retencao/manutencaoR4010';
                    break;

                case 'r4020':
                    action = 'web/integracoes/efd-reinf/retencao/manutencaoR4020';
                    break;

                case 'r4040':
                    action = 'web/integracoes/efd-reinf/retencao/manutencaoR4040';
                    break;
                default:
                    alert('Erro: Evento não encontrado.');
                    break;
            }

            sessionStorage.removeItem('retencao');
            sessionStorage.setItem('retencao', retencao);

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_retencao',
                action,
                'Manutenção da Retenção', true);
        }

        /** Funcao para validar forumalario de filtros */
        function js_validateFilter() {
            if ($F('dataNotaInicial') == '' && $F('dataNotaFinal') == '') {
                alert('Você deve selecionar um período');
                return false;
            }

            return true;
        }

        /** modal com os erros da retenção */
        function js_errosRetencao(ev) {
            let erros = JSON.parse(ev.dataset.erros);

            if (erros.length == 0) {
                alert('Sem detalhes para este item');
                return;
            }

            windowAuxRetencao.setTitle('Detalhes');
            windowAuxRetencao.setContent(js_pageErrosRetencao(erros));
            windowAuxRetencao.show();
        }

        /** html da pag de erros */
        function js_pageErrosRetencao(list) {
            const container = document.createElement('div');
            const title = document.createElement('h3');
            const ul = document.createElement('ul');

            title.innerHTML = "Os seguintes campos estão vazios ou precisam de revisão";
            title.classList.add('text-center');

            list.forEach(i => {
                let li = document.createElement('li');
                li.innerHTML = i;
                ul.appendChild(li);
            });

            container.appendChild(title);
            container.appendChild(ul);

            return container;
        }

        function buttons() {
            return {
                btnFilterError: {
                    text: 'Inconsistentes',
                    icon: 'fa-exclamation-triangle',
                    event(e) {
                        gridNotas.bootstrapTable('filterBy', {
                            statusCode: 0
                        });
                    }
                },
                btnFilterSuccess: {
                    text: 'Corretas',
                    icon: 'fa-check-circle',
                    event() {
                        gridNotas.bootstrapTable('filterBy', {
                            statusCode: 1
                        });
                    }
                },
                btnRefresh: {
                    text: 'Todas',
                    icon: 'fa-th-list',
                    event() {
                        gridNotas.bootstrapTable('refreshOptions', {});
                    }
                }
            }
        }

        function changeTitlePeriodo() {
            let title = '';

            switch (evento.value) {
                case 'r2010':
                case 'r2055':
                    title = 'Período da emissão da Nota Fiscal'
                    document.querySelector('#filtro-nf').classList.remove('d-none');
                    break;
                case 'r4010':
                case 'r4020':
                case 'r4040':
                    title = 'Período do Pagamento'
                    document.querySelector('#filtro-nf').classList.add('d-none');
                    break;
            }

            document.querySelector('#title-periodo').title = title;
        }

        /**
         * Data do fechamento do periodo filtrado (R-2099, R-4099)
         */
        async function getClosingDates() {
            const selectedEvent = document.querySelector('#evento');
            const closingEvent = (selectedEvent.value.startsWith('r2')) ? 'R-2099' : 'R-4099';
            const startDate = document.querySelector('#dataNotaInicial')?.value;
            const endDate = document.querySelector('#dataNotaFinal')?.value;
            const competences = {};

            if (startDate) {
                const [startYear, startMonth] = startDate.split('/').reverse();
                const startComptence = `${startYear}-${startMonth.padStart(2, '0')}`;
                const closedPeriod = await getClosingDateCompetence(closingEvent, startComptence);
                if (closedPeriod) {
                    competences[startComptence] = closedPeriod.date;
                }
            }

            if (endDate) {
                const [endYear, endMonth] = endDate.split('/').reverse();
                const endCompetence = `${endYear}-${endMonth.padStart(2, '0')}`;
                if (!competences.hasOwnProperty(endCompetence)) {
                    const closedPeriod = await getClosingDateCompetence(closingEvent, endCompetence);
                    if (closedPeriod) {
                        competences[endCompetence] = closedPeriod.date;
                    }
                }
            }

            return (Object.keys(competences).length === 0) ? false : competences;
        }

        async function getClosingDateCompetence(event, competence) {
            try {
                const url = '/v4/api/integracoes/efd-reinf/events/checkPeriodIsClosed';
                const params = {event, competence}
                const req = await CurrentWindow.axios.get(url, {params});
                const {data: resp} = req;
                const {data: eventData} = resp;
                if (!eventData) return false;
                return eventData;
            } catch (error) {
                console.error(error);
                return false;
            }
        }

        function showAlertRowsWithErros() {
            const gridNotas = document.querySelector('#gridNotas');
            const dataErros = gridNotas.querySelector('[data-erros]');
            const rowErrorAlert = document.querySelector('#rowErrorAlert');

            if (dataErros) {
                rowErrorAlert.classList.remove('d-none');
                rowErrorAlert.style.display = 'inline-block';
            } else {
                rowErrorAlert.classList.add('d-none');
            }
        }
    </script>
</body>

</html>
