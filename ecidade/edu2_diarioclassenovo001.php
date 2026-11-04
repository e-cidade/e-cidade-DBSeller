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

require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$iEscola = db_getsession("DB_coddepto");
$user = db_getsession("DB_id_usuario");

?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/prototype.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/strings.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/DBFormCache.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/DBFormSelectCache.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/classes/educacao/escola/ListaCalendario.classe.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/classes/educacao/escola/ListaTurma.classe.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/classes/educacao/escola/ListaPeriodoAvaliacao.classe.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/classes/educacao/escola/ListaDisciplinas.classe.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/widgets/DBToggleList.widget.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/AjaxRequest.js'></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        .DBToggleListBox .toggleListActionButons {
            margin: 8% 0 10px 7%;
        }
    </style>
</head>

<body class="body-default">
    <div class="container">
        <form>
            <fieldset>
                <legend>Relatório Diário de Classe</legend>
                <table class="form-container">
                    <tr>
                        <td class="field-size5">
                            <label>Seleção:</label>
                        </td>
                        <td>
                            <select id="tipoSelecao">
                                <option value="1" selected>Multiplas Disciplinas</option>
                                <option value="2">Por Disciplina</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size5">
                            <label>Selecione o Calendário:</label>
                        </td>
                        <td>
                            <select id="listaCalendarios">
                                <option value="" selected>Selecione a Calendario</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size5">
                            <label>Selecione a Turma:</label>
                        </td>
                        <td>
                            <select id="listaTurmas">
                                <option value="" selected>Selecione a Turma</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="listaEtapas">
                        <td class="field-size5">
                            <label>Etapas:</label>
                        </td>
                        <td class="field-size-max">
                            <select id="cboEtapas" name="cboEtapas">
                                <option value="">Selecione a Etapa</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size5">
                            <label>Selecione o Período de Avaliação:</label>
                        </td>
                        <td id="listaPeriodos" class="field-size-max">
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size5">
                            <label>Data de corte:</label>
                        </td>
                        <td>
                            <input type="hidden" id="data_corte_dia" name="datacorte_dia">
                            <input type="hidden" id="data_corte_mes" name="datacorte_mes">
                            <input type="hidden" id="data_corte_ano" name="datacorte_ano">
                            <input style="float: left" type="text" id="data_corte" name="data_corte" onBlur='js_validaDbData(this);' onkeydown="return js_mascaraData(this,event);" onFocus="js_validaEntrada(this);" onpaste="return false" ondrop="return false" disabled />
                            <div style="width: 600px; height: 20px; float: left; position: relative; margin-left: 5px;"><span id="avisoTempoBimestre"> </span></div>
                        </td>

                    </tr>

                    <tr id="linhaDisciplina" style="display: none">
                        <td class="field-size5">
                            <label>Selecione a Disciplina:</label>
                        </td>
                        <td class="field-size-max">
                            <select id="unicaDisciplina">
                                <option>Selecione uma Disciplina</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="linhaRegente" style="display: none">
                        <td class="field-size5">
                            <label>Selecione o Regente:</label>
                        </td>
                        <td class="field-size-max">
                            <select id="regente">
                                <option>Selecione o Regente</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <fieldset id="fieldsetDisciplinas" class='separator'>
                    <legend>Disciplinas</legend>
                    <div id='listaDisciplinas' style="padding-left: 19%;"></div>
                </fieldset>

                <table class="form-container">
                    <tr>
                        <td colspan="2">
                            <fieldset class="separator">
                                <legend>Configuração do Relatório</legend>
                                <table>
                                    <tr>
                                        <td>
                                            <label>Selecione um modelo:</label>
                                        </td>
                                        <td class="field-size-max">
                                            <select id="listaModelos" onchange="liberaFiltrosPorModelo();">
                                                <option value="2">Modelo 1 - Uma disciplina por página (Área)</option>
                                                <option value="4" disabled="disabled">Modelo 2 - Todas disciplinas em uma
                                                    página (Currículo)
                                                </option>
                                                <option value="3">Modelo 3 - Duas páginas por disciplina (Página 1 -
                                                    Presenças / Página 2 - Avaliações)
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <fieldset class="separator">
                                <legend>Exibir Colunas</legend>
                                <table>
                                    <tr>
                                        <td>
                                            <label for="exibirAvaliacoes">
                                                <input id="exibirAvaliacoes" type="checkbox" name="colunas">
                                                Avaliações
                                            </label>
                                            <label for="exibirAlunos">
                                                <input id="exibirAlunos" type="checkbox" name="colunas">
                                                Alunos
                                            </label>
                                            <label for="exibirDataPeriodo">
                                                <input id="exibirDataPeriodo" type="checkbox" name="colunas">
                                                Data do Período
                                            </label>
                                            <label for="exibirTotalFaltas">
                                                <input id="exibirTotalFaltas" type="checkbox" name="colunas">
                                                Total de Faltas
                                            </label>
                                            <label for="exibirSexo">
                                                <input id="exibirSexo" type="checkbox" name="colunas">
                                                Sexo
                                            </label>
                                            <label for="exibirIdade">
                                                <input id="exibirIdade" type="checkbox" name="colunas">
                                                Idade
                                            </label>
                                            <label for="exibirFaltasAbonadas">
                                                <input id="exibirFaltasAbonadas" type="checkbox" name="colunas">
                                                Faltas Abonadas
                                            </label>
                                            <label for="exibirAulasDadas">
                                                <input id="exibirAulasDadas" type="checkbox" name="colunas">
                                                Aulas Dadas
                                            </label>
                                            <br>
                                            <label for="exibirCodigo">
                                                <input id="exibirCodigo" type="checkbox" name="colunas">
                                                Código
                                            </label>
                                            <label for="exibirNascimento">
                                                <input id="exibirNascimento" type="checkbox" name="colunas">
                                                Nascimento
                                            </label>
                                            <label for="exibirResultadoAnterior">
                                                <input id="exibirResultadoAnterior" type="checkbox" name="colunas">
                                                Resultado Anterior
                                            </label>
                                            <label for="exibirParecer">
                                                <input id="exibirParecer" type="checkbox" name="colunas">
                                                Parecer
                                            </label>
                                            <label for="pautaUnica">
                                                <input id="pautaUnica" type="checkbox" name="colunas">
                                                Pauta Única
                                            </label>
                                            <label for="exibirTodasDisciplinas">
                                                <input id="exibirTodasDisciplinas" type="checkbox" name="colunas">
                                                Anos Iniciais/Currículo - Modelo 2
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Registro:</label>
                        </td>
                        <td>
                            <select id="registro">
                                <option value="M">Manual</option>
                                <option value="F">Frequência digital</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Exibir Pontos</label>
                        </td>
                        <td>
                            <select id="exibirPontos">
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-size7">
                            <label>Informar Dias Letivos:</label>
                        </td>
                        <td>
                            <select id="diasLetivos" onchange="mostraQuantidadeColunas();">
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none" id="situacaoAlunoDiario">
                        <td>
                            <label>Exibir Situação do Aluno:</label>
                        </td>
                        <td>
                            <select id="exibirSituacaoAlunoDiario">
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Quantidade de Colunas (Presenças):</label>
                        </td>
                        <td>
                            <select id="quantidadeColunas" disabled="disabled">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Mostrar somente alunos ativos (Matriculados):</label>
                        </td>
                        <td>
                            <select id="alunosAtivos">
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none" id="alunosRetorno">
                        <td>
                            <label>Retorno de Aluno transferido:</label>
                        </td>
                        <td>
                            <select id="exibirAlunosRetorno">
                                <option value="S">Todas matrículas</option>
                                <option value="N">Ultima matrícula</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Exibir Trocas de Turma:</label>
                        </td>
                        <td>
                            <select id="trocaTurma">
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input type="hidden" value=<?php echo $iEscola ?> id="iEscola">
            <input type="button" id="imprimir" name="imprimir" value="Imprimir" onclick="validaDados();">
        </form>
    </div>
    <script rel="script" type="text/javascript" src="scripts/session.js"></script>
</body>

<?php
db_menu();
?>

<script>
    const user = '<?php echo $user; ?>';
    const tipoSelecao = $('tipoSelecao');
    const MENSAGENS_DIARIO_CLASSE_NOVO = 'educacao.escola.edu2_diarioclassenovo001.';

    var aColunas = document.getElementsByName('colunas');
    var lTemDisciplinaGlobal = false;
    var iEscola = $F("iEscola");
    var oPeriodo = new DBViewFormularioEducacao.ListaPeriodoAvaliacao();
    oPeriodo.somentePeriodoCalculaCargaHoraria(true);
    var oDisciplina = new DBViewFormularioEducacao.ListaDisciplinas();
    var sRpc = "edu4_turmas.RPC.php";

    // oTurma.show($('listaTurmas'));
    oPeriodo.show($('listaPeriodos'));
    oDisciplina.show($('listaDisciplinas'));

    for (var oElemento in aColunas) {

        aColunas[oElemento].checked = false;
        aColunas[oElemento].disabled = true;

        if (
            aColunas[oElemento].id == "exibirAvaliacoes" ||
            aColunas[oElemento].id == "exibirTotalFaltas" ||
            aColunas[oElemento].id == "exibirDataPeriodo" ||
            aColunas[oElemento].id == "exibirAlunos" ||
            aColunas[oElemento].id == "exibirAulasDadas"
        ) {
            aColunas[oElemento].checked = true;
            aColunas[oElemento].disabled = false;
        }
        if (aColunas[oElemento].id == "pautaUnica") {
            aColunas[oElemento].disabled = false;
        }
    }

    const cboAlunosAtivos = document.getElementById('alunosAtivos');
    cboAlunosAtivos.addEventListener('change', () => {
        const valor = cboAlunosAtivos.value;
        if (valor == 'N') {
            $('alunosRetorno').style.display = 'table-row';
            return;
        }
        $('exibirAlunosRetorno').value = 'S';
        $('alunosRetorno').style.display = 'none';
    });

    const cboDiasLetivos = document.getElementById('diasLetivos');
    cboDiasLetivos.addEventListener('change', () => {
        const valor = cboDiasLetivos.value;
        if (valor == 'N') {
            $('situacaoAlunoDiario').style.display = 'table-row';
            return;
        }
        $('exibirSituacaoAlunoDiario').value = 'S';
        $('situacaoAlunoDiario').style.display = 'none';
    });

    /**
     * Preenche o combo da quantidade de colunas a serem exibidas
     */
    function preencheQuantidadeColunas() {

        for (var iContador = 40; iContador <= 70; iContador++) {
            $("quantidadeColunas").add(new Option(iContador, iContador));
        }
    }

    /**
     * Controla se o select de quantidade de colunas deve estar habilitado
     */
    function mostraQuantidadeColunas() {
        $("quantidadeColunas").disabled = true;
        $("quantidadeColunas").value = 40;

        if ($("diasLetivos").value == 'N') {
            $("quantidadeColunas").disabled = false;
        }
    }

    /**
     * Controla os filtros que devem ser liberados, de acordo com o modelo selecionado
     */
    function liberaFiltrosPorModelo() {
        for (var oElemento in aColunas) {

            aColunas[oElemento].checked = false;
            aColunas[oElemento].disabled = true;

            if (aColunas[oElemento].id == "exibirAvaliacoes" ||
                aColunas[oElemento].id == "exibirTotalFaltas" ||
                aColunas[oElemento].id == "exibirDataPeriodo" ||
                aColunas[oElemento].id == "exibirAulasDadas" ||
                aColunas[oElemento].id == "exibirAlunos") {
                aColunas[oElemento].checked = true;
                aColunas[oElemento].disabled = false;
            }
            if (aColunas[oElemento].id == "pautaUnica") {
                aColunas[oElemento].disabled = false;
            }
        }

        if ($("listaModelos").value == 4) {

            for (var oElemento in aColunas) {
                aColunas[oElemento].checked = false;
                aColunas[oElemento].disabled = true;

                if (aColunas[oElemento].id == "exibirTodasDisciplinas") {
                    aColunas[oElemento].checked = true;
                    aColunas[oElemento].disabled = false;
                }

                if (aColunas[oElemento].id == "exibirAvaliacoes") {
                    aColunas[oElemento].checked = true;
                    aColunas[oElemento].disabled = false;
                }

                if (aColunas[oElemento].id == "exibirDataPeriodo") {
                    aColunas[oElemento].checked = true;
                    aColunas[oElemento].disabled = false;
                }

                if (aColunas[oElemento].id == "exibirAulasDadas") {
                    aColunas[oElemento].checked = true;
                    aColunas[oElemento].disabled = false;
                }
            }
        }

        if ($("listaModelos").value == 3) {

            for (var oElemento in aColunas) {
                aColunas[oElemento].checked = true;
                aColunas[oElemento].disabled = false;

                if (aColunas[oElemento].id == "exibirAvaliacoes" ||
                    aColunas[oElemento].id == "pautaUnica" ||
                    aColunas[oElemento].id == "exibirTodasDisciplinas") {
                    aColunas[oElemento].checked = false;
                    aColunas[oElemento].disabled = true;
                }

                if (aColunas[oElemento].id == "exibirDataPeriodo") {
                    aColunas[oElemento].checked = false;
                    aColunas[oElemento].disabled = false;
                }
            }
        }
    }


    function functionChangeTurma() {

        var oTurmaSelecionada = document.querySelector("#listaTurmas").value

        $('cboEtapas').options.length = 0;
        $('cboEtapas').add(new Option('Selecione uma Etapa', ''));

        js_divCarregando('Buscando etapas, aguarde...', 'msgBox');
        var oAjaxRequest = new AjaxRequest(
            'edu_educacaobase.RPC.php', {
                'exec': 'pesquisaEtapa',
                'turma': oTurmaSelecionada,
            },
            (response) => {
                const etapas = [];
                response.dados.map((etapa) => {
                    $('cboEtapas').add(new Option(etapa.ed11_c_descr.urlDecode(), etapa.ed11_i_codigo));
                    etapas.push(etapa.ed11_i_codigo);
                });

                if (response.dados.length === 1) {
                    $('cboEtapas').value = response.dados[0].ed11_i_codigo;
                    $('cboEtapas').dispatchEvent(new Event('change'));
                } else {
                    $('cboEtapas').add(new Option("TODAS ETAPAS", etapas.join(',')));
                }
            }
        );
        js_removeObj('msgBox')
        oAjaxRequest.execute();
    }

    $('cboEtapas').addEventListener('change', (event) => {

        if ($F('cboEtapas') === "") {
            return;
        }
        const etapaSelecionada = $F('cboEtapas').split(',')[0];

        validaTurma(document.querySelector('#listaTurmas').value, etapaSelecionada);
        oPeriodo.getPeriodos(document.querySelector('#listaTurmas').value, etapaSelecionada, 2);
    });

    $('listaPeriodos').addEventListener('change', (event) => {
        oPeriodoSelecionado = oPeriodo.getSelecionado().iCodigo;

        if (oPeriodoSelecionado != '') {
            document.getElementById("data_corte").disabled = false;
        }

        escreveDataDoBimestre(oPeriodoSelecionado, document.querySelector('#listaTurmas').value);
    })

    function escreveDataDoBimestre(icodigo, iturma) {
        var oParametro = new Object();
        oParametro.exec = 'getPeriodoCalendario';
        oParametro.iCodigo = icodigo;
        oParametro.iTurma = iturma;

        var oDadosRequisicao = new Object();
        oDadosRequisicao.method = 'post';
        oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametro);
        oDadosRequisicao.onComplete = retornoDataDoBimestre;

        js_divCarregando(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "validando_turma"), "msgBox");
        new Ajax.Request(sRpc, oDadosRequisicao);
    }

    var dataFim = "";
    var dataInicio = "";

    function retornoDataDoBimestre(oResponse) {
        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oResponse.responseText);

        oRetorno.aPeriodos.each(function(oPeriodo) {

            document.getElementById("avisoTempoBimestre").innerHTML = "Para REIMPRESSÃO de Pauta, digite a data em que ocorreu a 1ª impressão. <br> Essa data de corte deverá estar compreendida entre <strong> " + oPeriodo.sDataInicio + "</strong> e <strong>" + oPeriodo.sDataFim + "</strong>";
            dataFim = oPeriodo.sDataFim;
            dataInicio = oPeriodo.sDataInicio;
        });
    }

    /**
     * Buscas as seguintes informações da turma:
     *   -> lTipoEja             - Verifica se a turma é do tipo EJA
     *   -> lTemDisciplinaGlobal - Verifica se o controle de frequência da turma é individual ou globalizada
     * @param  {integer} iTurma
     * @param  {integer} iEtapa
     */
    function validaTurma(iTurma, iEtapa) {

        var oParametro = new Object();
        oParametro.exec = 'getInformacoesTurma';
        oParametro.iTurma = iTurma;
        oParametro.iEtapa = iEtapa;

        var oDadosRequisicao = new Object();
        oDadosRequisicao.method = 'post';
        oDadosRequisicao.parameters = 'json=' + Object.toJSON(oParametro);
        oDadosRequisicao.onComplete = retornoValidaTurma;

        js_divCarregando(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "validando_turma"), "msgBox");
        new Ajax.Request(sRpc, oDadosRequisicao);
    }

    /**
     * Verifica se o tipo da turma é igual a EJA e libera modelo de relatório  "Turma EJA"
     * Verifica se a frequência da turma é individual ou globalizada e busca as disciplinas
     * @param  {Object} oResponse
     */
    function retornoValidaTurma(oResponse) {

        js_removeObj("msgBox");
        var oRetorno = JSON.parse(oResponse.responseText);

        $('listaModelos').options[1].disabled = true;
        $('listaModelos').options[1].selected = false;

        lTemDisciplinaGlobal = false;

        if (oRetorno.lFrequenciaGlobal) {

            lTemDisciplinaGlobal = true;
            $('listaModelos').options[1].disabled = false;
        }

        buscaDisciplina(oRetorno.iTurma, oRetorno.iEtapa);
    }

    /**
     * Busca as disciplinas da turma.
     * Foi alterado, para buscar todas disciplinas da turma, não importando mais se a disciplina não controla frequência
     * @param  {integer} iTurma
     * @param  {integer} iEtapa
     */
    function buscaDisciplina(iTurma, iEtapa) {

        oDisciplina.clear();
        oDisciplina.setSomenteDisciplinasGlobais(false);
        oDisciplina.setCallBackLoad(function() {
            populaComboUnicaDisciplina(oDisciplina.regencias);
        })
        oDisciplina.getDisciplinas(iTurma, iEtapa, false);

    }

    $('registro').onchange = function() {
        validaTipoRegistro();
    }

    /**
     * Valida se mostra ou oculta os campos
     *   -> Exibir Pontos
     *   -> Dias Letivos
     *   -> Quantidade de Colunas
     * Conforme o tipo de registro (Manual ou Frequência/Conteúdo)
     */
    function validaTipoRegistro() {

        $('exibirPontos').disabled = false;
        $('diasLetivos').disabled = false;
        $('quantidadeColunas').disabled = true;

        if ($F('registro') == "F") {

            $('exibirPontos').value = "S";
            $('diasLetivos').value = "S";
            $('quantidadeColunas').value = 40;
            $('exibirPontos').disabled = true;
            $('diasLetivos').disabled = true;
            $('quantidadeColunas').disabled = true;
        }
    }

    /**
     * Valida se os dados obrigatórios estão setados para imprimir o relatório. Campos obrigatórios:
     *   -> Calendário
     *   -> Turma
     *   -> Disciplina
     *   -> Período
     */
    function validaDados() {

        var iCalendarioSelecionado = document.querySelector("#listaCalendarios").value
        var iTurmaSelecionada = document.querySelector("#listaTurmas").value
        var iPeriodoSelecionado = oPeriodo.getSelecionado().iCodigo;

        var aDisciplinaSelecionadas = oDisciplina.getSelecionados();

        if (iCalendarioSelecionado == "") {

            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "selecione_calendario"));
            return false;
        }

        if (iTurmaSelecionada == "") {

            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "selecione_turma"));
            return false;
        }

        if (iPeriodoSelecionado == "") {

            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "selecione_periodo"));
            return false;
        }


        if (tipoSelecao.value == 1 && aDisciplinaSelecionadas.length == 0) {

            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "selecione_disciplinas"));
            return false;
        }

        if (tipoSelecao.value == 2 && $F('unicaDisciplina') == '') {
            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "selecione_disciplinas"));
            return false;
        }

        if ($F('cboEtapas') === "") {
            alert("Selecione a Etapa.");
            return;
        }

        if (document.getElementById("data_corte").value != "") {
            if (formatarData(document.getElementById("data_corte").value) < formatarData(dataInicio) ||
                formatarData(document.getElementById("data_corte").value) > formatarData(dataFim)) {
                alert("Data não compreendida no intervalo do Bimestre");
                return false;
            }
        }

        imprimir();
    }

    function formatarData(dataString) {
        var partesData = dataString.split("/");
        var dataFormatada = new Date(partesData[2], partesData[1] - 1, partesData[0]);
        return dataFormatada;
    }

    /**
     * Busca todos os parâmetros selecionados na tela e os passa via GET para o relatório para que possa imprimir
     * as informações na tela
     */
    function imprimir() {

        const formData = new FormData();
        var iCalendarioSelecionado =  document.querySelector('#listaCalendarios').value
        var iTurmaSelecionada = document.querySelector('#listaTurmas').value
        var iPeriodoSelecionado = oPeriodo.getSelecionado().iCodigo;
        var aDisciplinaSelecionadas = oDisciplina.getSelecionados();
        var iDataCorte = document.getElementById("data_corte").value;
        var aRegencias = [];
        var aRegenciasSemGrade = [];

        if (tipoSelecao.value == 1) {
            aDisciplinaSelecionadas.each(function(disciplina) {
                if ($F('registro') == 'F' && !disciplina.lTemGradeHorario) {

                    aRegenciasSemGrade.push(disciplina.sRegencia);
                    return;
                }
                formData.append('disciplinas[]', disciplina.iDisciplina);
                aRegencias.push(disciplina.iRegencia);
            });
        }
        regente = '';
        if (tipoSelecao.value == 2) {
            const unicaDisciplina = $('unicaDisciplina');
            const option = unicaDisciplina.options[unicaDisciplina.selectedIndex];
            const lTemGradeHorario = option.getAttribute('lTemGradeHorario');
            if ($F('registro') == 'F' && !lTemGradeHorario) {
                aRegenciasSemGrade.push(option.innerHTML);
            }
            formData.append('disciplinas[]', unicaDisciplina.value);
            aRegencias.push(unicaDisciplina.value);

            const optionRegente = $('regente').options[$('regente').selectedIndex];
            regente = optionRegente.value;
        }

        if (aRegenciasSemGrade.length > 0) {
            alert(_M(MENSAGENS_DIARIO_CLASSE_NOVO + "regencias_sem_grade", {
                'sRegencias': aRegenciasSemGrade.implode(', ')
            }));
        }

        if (iDataCorte != '') {
            formData.append('dataCorte', iDataCorte);
        }

        if (aRegencias.length == 0) {
            return;
        }

        const url = "<?php echo ECIDADE_REQUEST_PATH; ?>",
            apiUrl = `${url}v4/api`;

        formData.append('turma', iTurmaSelecionada);
        formData.append('tipo_turma', 1);

        $F('cboEtapas').split(',').map((etapa) => {
            formData.append('etapa[]', etapa);
        });

        formData.append('periodo', iPeriodoSelecionado);
        formData.append('regente', regente);

        formData.append('modelo', $F('listaModelos'));

        for (const cboName in aColunas) {
            if (aColunas[cboName].id) {
                formData.append(aColunas[cboName].id, aColunas[cboName].checked ? '1' : '0');
            }
        }

        formData.append('colunas', $F('quantidadeColunas'));

        formData.append('registroManual', ($F('registro') === 'M') ? '1' : '0');
        formData.append('exibirPontos', $F('exibirPontos') === 'S' ? '1' : '0');
        formData.append('exibirDiasLetivos', $F('diasLetivos') === 'S' ? '1' : '0');
        formData.append('exibirSituacaoAlunoDiario', $F('exibirSituacaoAlunoDiario') === 'S' ? '1' : '0');
        formData.append('apenasAlunosAtivos', $F('alunosAtivos') === 'S' ? '1' : '0');
        formData.append('exibirTrocaTurma', $F('trocaTurma') === 'S' ? '1' : '0');
        formData.append('exibirAlunosRetorno', $F('exibirAlunosRetorno') === 'S' ? '1' : '0');
        formData.append('exibirAulasDadas', $F('exibirAulasDadas') === 'on' ? '1' : '0');
        PHPSession.appendFormData(formData);

        HttpClient.post(
            `${apiUrl}/educacao/escola/relatorios/diarioclasse/turmasEscolarizacao`, {
                body: formData
            }
        ).then(response => {
            if (response.error === true) {
                alert(response.message);
                return;
            }

            jan = window.open(response.data, '', 'scrollbars=1,location=0');
            jan.moveTo(0, 0);
        });

        return false;
    }

    /**
     * Limpa os campos período, turma e disciplina
     */
    function limpaElementos() {
        oPeriodo.limpaElemento();
        oDisciplina.clear();
        document.querySelector('#listaTurmas').options.length = 0
        document.querySelector('#listaTurmas').add(new Option('Selecione uma Turma', ''));


        $('cboEtapas').options.length = 0;
        $('cboEtapas').add(new Option('Selecione uma Etapa', ''));
    }

    preencheQuantidadeColunas();

    $('tipoSelecao').addEventListener('change', function(event) {
        tipoSelecaoDisciplina(event.target.value);
    });

    tipoSelecaoDisciplina = (tipo) => {
        if (tipo == 1) {
            $('linhaDisciplina').style.display = 'none';
            $('linhaRegente').style.display = 'none';
            $('fieldsetDisciplinas').style.display = 'block';
        }
        if (tipo == 2) {
            $('linhaDisciplina').style.display = 'table-row';
            $('linhaRegente').style.display = 'table-row';
            $('fieldsetDisciplinas').style.display = 'none';
        }
    };

    populaComboUnicaDisciplina = regencias => {
        const regente = $('regente');
        regente.options.length = 0;
        regente.add(new Option('Selecione o Regente', ''));

        const unicaDisciplina = $('unicaDisciplina');
        unicaDisciplina.options.length = 0;
        unicaDisciplina.add(new Option('Selecione uma Disciplina', ''));

        regencias.each(function(regencia) {
            const option = new Option(regencia.sDisciplina.urlDecode(), regencia.iDisciplina);
            option.setAttribute('lTemGradeHorario', regencia.lTemGradeHorario);
            option.setAttribute('regencia', regencia.iRegencia);
            unicaDisciplina.add(option);
        });
    };

    $('unicaDisciplina').addEventListener('change', () => {
        const regente = $('regente');
        regente.options.length = 0;
        regente.add(new Option('Selecione o Regente', ''));

        if ($F('unicaDisciplina') != '') {
            buscarRegente();
        }
    });

    buscarRegente = () => {
        const parametros = {
            "exec": "buscarRegentePorRegencia",
            "regencia": $('unicaDisciplina').options[unicaDisciplina.selectedIndex].getAttribute('regencia')
        };

        const regente = $('regente');
        var oAjaxRequest = new AjaxRequest('edu4_turmas.RPC.php', parametros, function(retorno, erro) {
            if (erro) {
                alert(retorno.message);
                return;
            }

            retorno.regentes.forEach(function(professor) {
                regente.add(new Option(professor.nome.urlDecode(), professor.nome.urlDecode()));
            });

            if (retorno.regentes.length == 1) {
                regente.value = retorno.regentes[0].nome.urlDecode();
            }
        });

        oAjaxRequest.setMessage('Buscando regentes.');
        oAjaxRequest.execute();
    }

    window.addEventListener('load', async () => {
        const montaSelect = async (options, elemento) => {
            let first = elemento.options[0]
            elemento.options.length = 0
            elemento.add(first)

            options.forEach(linha => {
                let option = new Option(linha.nome, linha.codigo)
                elemento.add(option)
            })
        }

        const montaSelectTurmas = async (options, elemento) => {
            let first = elemento.options[0]
            elemento.options.length = 0
            elemento.add(first)

            options.forEach(linha => {
                let option = new Option(linha.nome, linha.codigo)
                elemento.add(option)
            });
        }

        const escola = document.querySelector('#iEscola').value;
        const selectCalendario = document.querySelector('#listaCalendarios');
        const selectTurmas = document.querySelector('#listaTurmas');
        const routes = {
            calendarios: `v4/api/educacao/escola/${escola}/calendario?validaUsuario=${user}`,
            turmas: `v4/api/educacao/escola/turmas-por-calendario`
        }

        js_divCarregando('Buscando calendários, aguarde...', 'msgBox');

        const calendarios = (await CurrentWindow.axios.get(routes.calendarios)).data.data;

        montaSelect(calendarios, selectCalendario);
        js_removeObj('msgBox');

        selectCalendario.addEventListener('change', async () => {
            const codCalendario = calendarios.find(calendario => selectCalendario.value == calendario.codigo);
            let turmas;

            if (codCalendario) {
                js_divCarregando('Aguarde, buscando turmas', 'msgBox');
                const codigoCalendario = codCalendario.codigo;
                const response = (await CurrentWindow.axios.get(`${routes.turmas}/${codigoCalendario}?validaUsuario=${user}`));
                turmas = response.data.data;
            }

            montaSelectTurmas(turmas, selectTurmas);
            js_removeObj('msgBox');
        });

        selectTurmas.addEventListener('change', () => {
            functionChangeTurma();
        })
    })

</script>

</html>
