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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
?>

<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
        <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">

        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/educacao/escola/ListaCalendario.classe.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/educacao/escola/ListaTurma.classe.js"></script>

        <style type="text/css">
            .elipse {
                width: 100%;
                overflow: hidden;
            }
        </style>

    </head>
    <body class="body-default">
        <div class="container" style="display: table; max-width: 600px">
            <form name="form1" id='form1' method="post">
                <fieldset style="width: 680px;">
                    <legend>Agenda de Sábado Letivo</legend>
                    <table class="form-container" style="margin-bottom: 10px;">
                        <tr>
                            <td>
                                <label>Calendário:</label>
                            </td>
                            <td id="calendario"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="data">Data:</label>
                            </td>
                            <td>
                                <select name="data" id="data">
                                    <option value="">Selecione uma Data</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>Turma:</label>
                            </td>
                            <td id="turma"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="disciplina">Disciplina:</label>
                            </td>
                            <td>
                                <select name="disciplina" id="disciplina">
                                    <option value="">Selecione uma Disciplina</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="turno">Turno:</label>
                            </td>
                            <td colspan="2" style="margin: 6px 0 2px 0;">
                                <input id="codigoTurno" class="readonly" name="codigoTurno" type="text" size="20" readonly style="margin: 6px 0 6px 0;"/>
                                <input id="descricaoTurno" class="readonly" name="descricaoTurno" type="text" size="60" readonly/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="regente"><a class="DBAncora" href="#" onclick="pesquisarRegente(true);">Regente:</a></label>
                            </td>
                            <td colspan="2">
                                <input id="codigoRegente"
                                       name="codigoRegente"
                                       type="text"
                                       size="20"
                                       onchange="pesquisarRegente(false);"
                                       onblur="js_ValidaMaiusculo(this,'f',event);"
                                       oninput="js_ValidaCampos(this,1,'Regente','f','f',event);"
                                       onkeydown="return js_controla_tecla_enter(this,event);" autocomplete="off" />
                                <input id="nomeRegente" class="readonly" name="nomeRegente" type="text" size="60" readonly/>
                            </td>
                        </tr>
                    </table>
                    <fieldset>
                        <legend>Periodos</legend>
                        <div id="gridPeriodos" style="text-align: center"></div>
                    </fieldset>
                </fieldset>
                <input type="button" value="Lançar" id="lancar" name="lancar" style="margin-bottom: 20px;"/>
                <fieldset>
                    <legend>Dias Letivos Lançados</legend>
                    <div id="gridPeriodosLancados"></div>
                </fieldset>
                <input type="button" value="Excluir" id="excluirPeriodosLancados" name="excluirPeriodosLancados" style="display: none;"/>
            </form>
        </div>
    </body>
    <script>
        const RPC = 'edu4_recuperacaodiasletivos.RPC.php';

        var codigoEscola = <?=db_getsession("DB_coddepto"); ?>;
        var calendarioDatas = [];
        var periodosLancados = [];

        var colecaoPeriodos = new Collection().setId('codigo');
        var gridPeriodos = DatagridCollection.create(colecaoPeriodos).configure({order: false, height: 160, alingn: 'center'});

        gridPeriodos.addColumn('codigo', {label: "Codigo", align: "center", width: "0%"});
        gridPeriodos.addColumn('descricao', {label: "Descrição", align: "center", width: "25%"});
        gridPeriodos.addColumn('horaInicial', {label: "Horário Inicial", align: "center", width: "35%"});
        gridPeriodos.addColumn('horaFinal', {label: "Horário Final", align: "center", width: "35%"});
        gridPeriodos.hideColumns([1]);
        gridPeriodos.getGrid().setCheckbox(0);
        gridPeriodos.show($('gridPeriodos'));

        var colecaoPeriodosLancados = new Collection().setId('identificador');

        var gridPeriodosLancados = DatagridCollection.create(colecaoPeriodosLancados).configure({order: false, height: 150, alingn: 'center'});

        gridPeriodosLancados.addColumn('periodos', {label: 'Períodos', align: 'center', width: '20%' }).transform((valor, linha) => {
            let periodosAula = valor.split(',').join(', ');
            return `<p class="elipse" title="${periodosAula}">${periodosAula}</p>`;
        });

        gridPeriodosLancados.addColumn('data', {label: 'Data', align: 'center', width: '15%' }).transform('date');
        gridPeriodosLancados.addColumn('disciplina', {label: 'Disciplina', align: 'center', width: '25%' });
        gridPeriodosLancados.addColumn('regente', {label: 'Regente', align: 'center', width: '25%' }).transform((valor, linha) => {
            return `<p class="elipse" title="${valor}">${valor}</p>`;
        });

        gridPeriodosLancados.addColumn('descricaoTurno', {label: 'Turno', align: 'center', width: '10%' });

        gridPeriodosLancados.getGrid().setCheckbox(0);
        gridPeriodosLancados.show($('gridPeriodosLancados'));



        var regente = {
            codigo: document.getElementById('codigoRegente'),
            descricao: document.getElementById('nomeRegente'),
        };
        var turno = {
            codigo: document.getElementById('codigoTurno'),
            descricao: document.getElementById('descricaoTurno'),
        };

        /**
         * Instancia o componente dos calendários
         * @type {DBViewFormularioEducacao.ListaCalendario}
         */
        var componenteCalendario = new DBViewFormularioEducacao.ListaCalendario();
        componenteCalendario.setEscola(codigoEscola);
        componenteCalendario.getCalendarios();
        componenteCalendario.setOnChangeCallBack(callBackCalendario);
        componenteCalendario.setCallBackLoad(callBackCalendario);

        /**
         * Instancia o componente das turmas
         * @type {DBViewFormularioEducacao.ListaTurma}
         */
        var componenteTurma = new DBViewFormularioEducacao.ListaTurma();
        componenteTurma.somenteComCriterioAvaliacao(false);
        componenteTurma.setCallbackOnChange(callBackTurma);
        componenteTurma.setCallBackLoad(callBackTurma);

        componenteCalendario.show($('calendario'));
        componenteTurma.show($('turma'));

        function callBackTurma() {

            var turmaSelecionada = componenteTurma.getSelecionados().codigo_turma;
            limparTodos();

            if (!empty(turmaSelecionada)) {
                var turnoTurma = componenteTurma.getTurnoTurma(turmaSelecionada);
                var etapa = componenteTurma.getSelecionados().codigo_etapa;

                turno.codigo.value = turnoTurma.codigoTurno;
                turno.descricao.value = turnoTurma.descricao.urlDecode();

                carregarDisciplinas(turmaSelecionada, etapa);
                carregarGridPeriodos(turnoTurma.periodos);
                buscarPeriodosLancados(turmaSelecionada, etapa);
            }
        }

        function callBackCalendario() {
            limparTodos();
            carregarDatas();
            componenteTurma.atualizarComponenete(codigoEscola, componenteCalendario.getSelecionados().iCalendario);
        }

        function carregarDatas() {
            var data = document.getElementById('data');
            data.options.length = 0;
            data.add(new Option('Selecione uma Data', ''));

            if (empty(componenteCalendario.getSelecionados().iCalendario)) {
                return;
            }

            if (calendarioDatas[componenteCalendario.getSelecionados().iCalendario] !== undefined) {
                for (var calendarioData of calendarioDatas[componenteCalendario.getSelecionados().iCalendario]) {
                    data.add(new Option(js_formatar(calendarioData.data.urlDecode(), 'd') + ' - ' + calendarioData.diaSemana.urlDecode(), calendarioData.data.urlDecode()));
                }
            } else {
                buscarDatas();
            }
        }


        function buscarDatas() {
            new AjaxRequest(RPC, {executa: 'buscarDataFeriadoLetivoPorCalendario', calendario: componenteCalendario.getSelecionados().iCalendario}, function (retorno) {
                calendarioDatas[componenteCalendario.getSelecionados().iCalendario] = retorno.datas;
                carregarDatas();
            })
                .execute();
        }

        function carregarDisciplinas(codigoTurma, codigoEtapa) {
            limparComponenteDisciplina();

            var disciplinas = document.getElementById('disciplina');
            buscarRegencias(codigoTurma, codigoEtapa)
                .then(function (regencias) {
                    for (var regencia of regencias) {
                        var option = new Option(regencia.disciplina, regencia.codigoRegencia);
                        option.setAttribute('codigoDisciplina', regencia.codigoDisciplina);

                        disciplinas.add(option);
                    }
                });
        }

        function limparComponenteDisciplina() {
            var disciplinas = document.getElementById('disciplina');

            disciplinas.options.length = 0;
            disciplinas.add(new Option('Selecione uma Disciplina', ''));
        }

        function limparComponenteTurno() {
            turno.codigo.value = '';
            turno.descricao.value = '';
        }

        function limparComponenteRegente() {
            regente.codigo.value = '';
            regente.descricao.value = '';
        }

        function limparTodos() {
            gridPeriodos.clear();
            gridPeriodosLancados.clear();
            limparComponenteDisciplina();
            limparComponenteTurno();
            limparComponenteRegente();
        }

        function pesquisarRegente(exbirJanela) {
            limparComponenteRegente();

            if (empty($F('data'))) {
                return alert('Por favor, selecione uma data.')
            }

            if (empty(componenteTurma.getSelecionados().codigo_turma)) {
                return alert('Por favor, selecione uma turma.')
            }

            if (empty($F('disciplina'))) {
                return alert('Por favor, selecione uma disciplina.')
            }

            var regencia = '&regencia=' + $F('disciplina');
            var data = "&data=" + $F('data').urlEncode();
            var pesquisa_chave = empty(regente.codigo.value) ? '' : '&pesquisa_chave=' + regente.codigo.value;

            js_OpenJanelaIframe('',
                'db_iframe_recursos_humano',
                'func_rechumanoreg.php?funcao_js=parent.carregarRegente|ed20_i_codigo|z01_nome&busca_regenciahorario=true'+regencia+data+pesquisa_chave,
                'Pesquisa de Recursos Humanos',
                exbirJanela
            );

        }

        function carregarRegente(codigoRegente, descricaoRegente) {
            db_iframe_recursos_humano.hide();

            if (descricaoRegente === true) {
                regente.codigo.value = '';
                regente.descricao.value = codigoRegente;
                return;
            }

            regente.codigo.value = codigoRegente;
            regente.descricao.value = descricaoRegente;
        }

        function lancar() {
            var lancamentoDiaLetivo = {
                data: $F('data'),
                turno: $F('codigoTurno'),
                regencia: $F('disciplina'),
                rechumano: $F('codigoRegente'),
                periodos: [],
            };

            for (var periodo of gridPeriodos.getGrid().getSelection()) {
                lancamentoDiaLetivo.periodos.push(periodo[0]);
            }

            salvar(lancamentoDiaLetivo);
        }

        function salvar(diaLetivo) {
            if (typeof diaLetivo !== 'object') {
                return alert('Erro ao ler dados para serem salvos.');
            }

            if (empty(diaLetivo.data)) {
                return alert('Informe uma data.');
            }

            if (empty(diaLetivo.turno)) {
                return alert('Informe um turno.');
            }

            if (empty(diaLetivo.regencia)) {
                return alert('Informe uma disciplina.');
            }

            if (empty(diaLetivo.rechumano)) {
                return alert('Informe um regente.');
            }

            if (empty(diaLetivo.periodos)) {
                return alert('Informe pelo menos um periodo.');
            }

            if (!validarPeriodosLancadosData(diaLetivo)) {
                return alert('Um ou mais periodos selecionados, já foram lançados para esta data.');
            }

            diaLetivo.executa = 'salvar';
            new AjaxRequest(RPC, diaLetivo, function (retorno) {
                alert(retorno.mensagem.urlDecode());
                if (!retorno.erro) {
                    var turmaSelecionada = componenteTurma.getSelecionados();
                    buscarPeriodosLancados(turmaSelecionada.codigo_turma, turmaSelecionada.codigo_etapa);
                }
            })
                .setMessage('Aguarde, salvando dados...')
                .execute();
        }

        function excluir(diaLetivo) {
            if (diaLetivo.regenciasHorario.length < 1) {
                return alert('Informe pelo menos um lançamento.')
            }

            new AjaxRequest(RPC, {executa: 'excluir', regenciasHorario: diaLetivo.regenciasHorario}, function (retorno) {
                alert(retorno.mensagem);
                if (!retorno.erro) {
                    var turmaSelecionada = componenteTurma.getSelecionados();
                    buscarPeriodosLancados(turmaSelecionada.codigo_turma, turmaSelecionada.codigo_etapa);
                }
            })
                .setMessage('Aguarde, excluindo...')
                .execute();
        }

        function carregarGridPeriodos(periodos) {
            gridPeriodos.clear();

            for (var periodo of periodos) {
                colecaoPeriodos.add({codigo: periodo.codigoPeriodo, descricao: periodo.descricaoPeriodo.urlDecode(), horaInicial: periodo.horaInicial, horaFinal: periodo.horaFinal})
            }

            gridPeriodos.reload();
        }

        function carregarGridPeriodosLancados() {
            gridPeriodosLancados.clear();
            colecaoPeriodosLancados.add(periodosLancados);

            if (colecaoPeriodosLancados.count() > 0 ) {
                document.getElementById('excluirPeriodosLancados').style.display = '';
            } else {
                document.getElementById('excluirPeriodosLancados').style.display = 'none';
            }

            gridPeriodosLancados.reload();
        }

        function buscarPeriodosLancados(codigoTurma, codigoEtapa) {
            periodosLancados = [];
            new AjaxRequest(RPC, {executa: 'buscarRegistros', turma: codigoTurma, etapa: codigoEtapa}, function (retorno, erro) {
                if (erro) {
                    return alert(retorno.mensagem);
                }

                for (var periodo of retorno.registros) {
                    periodosLancados.push(periodo);
                }

                carregarGridPeriodosLancados();
            })
                .execute();
        }

        function excluirPeriodosLancados() {
            var diasLetivos = {regenciasHorario: []};
            var linhasGrid = gridPeriodosLancados.getGrid().aRows;
            var selecionados = [];

            for (var linha of linhasGrid) {
                if (linha.isSelected) {
                    var item = linha.itemCollection.build();

                    var msg = "A disciplina " + item.disciplina + " possui faltas lançadas. ";
                    msg += "A exclusão irá apagar o lançamento de frequência (Procedimentos > Diário de Classe > Lançamentos - Frequência/Conteúdo)";
                    msg += " mas não irá atualizar o total de faltas no diário de classe do aluno.\nEsse procedimento deve ser ";
                    msg += "realizado manualmente acessando a rotina: Procedimentos > Diário de Classe > Lançamento Por Turma\n";
                    msg += "Deseja excluir?";
                    if (item.possui_falta_lancada && !confirm(msg)) {
                        return;
                    }
                    item.regencias.forEach(function (valor) {
                        selecionados.push(valor);
                    });
                }
            }

            diasLetivos.regenciasHorario = selecionados;
            excluir(diasLetivos);
        }

        document.getElementById('lancar').addEventListener('click', function () {
           lancar();
        });

        document.getElementById('excluirPeriodosLancados').addEventListener('click', function () {
           excluirPeriodosLancados();
        });

        document.getElementById('disciplina').addEventListener('change', function () {
           limparComponenteRegente();
        });

        function buscarRegencias(codigoTurma, codigoEtapa) {
            if (empty(codigoEtapa) || empty(codigoTurma)) {
                return alert('Informe uma turma e sua etapa.');
            }

            var oSelf = this;
            oSelf.regenciasTurma = [];
            return new Promise(function (resolve, reject) {
                if (oSelf.regenciasTurma[codigoTurma] !== undefined && oSelf.regenciasTurma[codigoTurma][codigoEtapa] !== undefined) {
                    return resolve(oSelf.regenciasTurma[codigoTurma][codigoEtapa]);
                } else {
                    if (oSelf.regenciasTurma[codigoTurma] === undefined) {
                        oSelf.regenciasTurma[codigoTurma] = new Array();
                    }

                    var parametros = {exec: 'getRegencias', iTurma: codigoTurma, iEtapa: codigoEtapa, somenteDisciplinasControlaFrequencia:true};
                    new AjaxRequest('edu4_turmas.RPC.php', parametros, function (retorno) {
                    var regencias = new Array();
                        for (var regencia of retorno.aRegencias) {
                            regencias.push({codigoRegencia: regencia.iRegencia, codigoDisciplina: regencia.iDisciplina, disciplina: regencia.sDisciplina.urlDecode()});
                        }

                        oSelf.regenciasTurma[codigoTurma][codigoEtapa] = regencias;
                        return resolve(oSelf.regenciasTurma[codigoTurma][codigoEtapa]);
                    }).execute();
                }
            });
            js_removeObj('msgBox');
        }

        function validarPeriodosLancadosData(diaLetivo) {
            var linhasGrid = gridPeriodosLancados.getGrid().aRows;
            var dataDialetivo = new Date(diaLetivo.data);

            for (var linha of linhasGrid) {
                var item = linha.itemCollection.build();
                var dataLancada = new Date(item.data);

                if (dataLancada.getTime() == dataDialetivo.getTime()) {
                    for (var periodo of diaLetivo.periodos) {
                        if (item.codigosPeriodos.indexOf(periodo) > -1) {
                            return false;
                        }
                    }
                }
            }

            return true;
        }

    </script>
    <?php db_menu(); ?>
</html>

