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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type='text/javascript' src='scripts/prototype.js'></script>
    <script type='text/javascript' src='scripts/widgets/DBToggleList.widget.js'></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Filtros - Espelho do Período</legend>
        <table class="form-container">
            <tr>
                <td style="width: 75px"><label for="calendario">Calendário: </label></td>
                <td class="field-size5">
                    <select id="calendario" name="calendario">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="turma">Turma: </label></td>
                <td>
                    <select id="turma" name="turma">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="periodo">Período: </label></td>
                <td>
                    <select id="periodo" name="periodo">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="alunosAtivos">Somente alunos ativos: </label></td>
                <td>
                    <select id="alunosAtivos" name="alunosAtivos">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="exibirNotas">Exibir notas lançadas: </label></td>
                <td>
                    <select id="exibirNotas" name="exibirNotas">
                        <option value="true">Sim</option>
                        <option value="false">Não</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="umaPorPagina">Uma disciplina por Página: </label></td>
                <td>
                    <select id="umaPorPagina" name="umaPorPagina">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
        </table>
        <fieldset class="separator">
        <legend>Disciplinas</legend>
        <div id="listaDisciplinas"></div>
        </fieldset>
        <br>
        <button id="btnGerar" class="btn btn-light">
            <i class="fa fa-print"></i>
            Imprimir
        </button>
    </fieldset>
</div>

<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    const escola = <?=db_getsession('DB_coddepto')?>;
    const user = <?=db_getsession("DB_id_usuario")?>;
    const turmasObj = [];
    var etapaSelecionada = 0;
    const routes = {
        calendarios: `v4/api/educacao/escola/${escola}/calendario?validaUsuario=${user}`,
        turmas: `v4/api/educacao/escola/turmas-por-calendario`,
        periodos: `v4/api/educacao/escola/periodos-por-calendario`,
        regencias: `v4/api/educacao/escola/turma`,
        espelho: `v4/api/educacao/escola/relatorios/espelho-periodo`
    }

    const cboCalendario = document.getElementById('calendario');
    const cboTurma = document.getElementById('turma');
    const cboPeriodo = document.getElementById('periodo');
    const cboAlunosAtivos = document.getElementById('alunosAtivos');
    const cboExibirNotas = document.getElementById('exibirNotas');
    const cboUmaPorPagina = document.getElementById('umaPorPagina');
    const btnGerar = document.getElementById('btnGerar');
    const listaDisciplinas = document.getElementById('listaDisciplinas');
    const oToggleRegencia = new DBToggleList([{'sId' : "sRegencia", 'sLabel' : "Regência"}]);
    oToggleRegencia.closeOrderButtons();
    oToggleRegencia.show(listaDisciplinas);

    window.addEventListener('load', async () => {
        const montaSelect = async (calendarios) => {
            calendarios.forEach(calendario => {
                cboCalendario.add(new Option(calendario.nome, calendario.codigo));
            });
        }

        CurrentWindow.axios.get(routes.calendarios).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const calendarios = response.data.data;
            montaSelect(calendarios);
        });
    })

    cboCalendario.addEventListener('change', async () => {
        const calendario = cboCalendario.value;
        if (calendario === '') {
            limparSelect(cboTurma);
            limparSelect(cboPeriodo);
            return;
        }

        await CurrentWindow.axios.get(`${routes.turmas}/${calendario}?validaUsuario=${user}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const turmas = response.data.data;
            limparSelect(cboTurma);
            turmas.forEach(oTurma => {
                if (oTurma.etapas !== undefined) {
                    if (oTurma.etapas.length > 1) {
                        oTurma.etapas.forEach(etapa => {
                            const nome = oTurma.nome + ' - ' + etapa.nome;
                            var element = {
                                codigo: oTurma.codigo,
                                etapa: etapa.codigo,
                                nomeTurma: oTurma.nome,
                                nomeEtapa: etapa.nome
                            }
                            turmasObj.push(element);
                            cboTurma.add(new Option(`${nome}`, oTurma.codigo));
                        });
                    } else {
                        if (Array.isArray(oTurma.etapas)) {
                            const nome = oTurma.nome + ' - ' + oTurma.etapas[0].nome;
                            var element = {
                                codigo: oTurma.codigo,
                                etapa: oTurma.etapas[0].codigo,
                                nomeTurma: oTurma.nome,
                                nomeEtapa: oTurma.etapas[0].nome
                            }
                            turmasObj.push(element);
                            cboTurma.add(new Option(`${nome}`, oTurma.codigo));
                        } else {
                            const nome = oTurma.nome + ' - ' + oTurma.etapas.nome;
                            var element = {
                                codigo: oTurma.codigo,
                                etapa: etapas.codigo,
                                nomeTurma: oTurma.nome,
                                nomeEtapa: oTurma.etapas.nome
                            }
                            turmasObj.push(element);
                            cboTurma.add(new Option(`${nome}`, oTurma.codigo));
                        }
                    }
                } else {
                    const nome = oTurma.nome + ' - ' + oTurma.etapa.nome;
                    var element = {
                        codigo: oTurma.codigo,
                        etapa: etapa.codigo,
                        nomeTurma: oTurma.nome,
                        nomeEtapa: oTurma.etapa.nome
                    }
                    turmasObj.push(element);
                    cboTurma.add(new Option(`${nome}`, oTurma.codigo));
                }
            });
        });

        await await CurrentWindow.axios.get(`${routes.periodos}/${calendario}`).then(responsePeriodo => {
            if (responsePeriodo.error) {
                alert(responsePeriodo.message);
                return;
            }
            const periodos = responsePeriodo.data.data;
            limparSelect(cboPeriodo);
            periodos.forEach(periodo => {
                cboPeriodo.add(new Option(periodo.ed09_c_descr, periodo.ed09_i_codigo));
            });
        });
    });

    btnGerar.addEventListener('click', function () {
        if (cboCalendario.value === '') {
            alert('Selecione um calendário');
            return;
        }
        if (cboTurma.value === '') {
            alert('Selecione uma turma');
            return;
        }
        if (cboPeriodo.value === '') {
            alert('Selecione um período');
            return;
        }

        let disciplinas = [];
        oToggleRegencia.getSelected().forEach(regencia => {
            disciplinas.push(regencia.iRegencia);
        });

        const postBody = {
            codigoTurma: cboTurma.value,
            periodo: cboPeriodo.value,
            somenteAlunosAtivos: cboAlunosAtivos.value,
            exibirNotas: cboExibirNotas.value,
            umaPorPagina: cboUmaPorPagina.value,
            etapa: etapaSelecionada,
            disciplinas: disciplinas.join(',')
        }

        js_divCarregando('Processando.', 'loading_message');
        CurrentWindow.axios.post(`${routes.espelho}`, postBody).then(response => {
            if (response.error) {
                js_removeObj('loading_message');
                alert(response.message);
                return;
            }
            window.open(response.data.data.path);
            js_removeObj('loading_message');
        });
    })

    const limparSelect = (elemento) => {
        elemento.innerHTML = '';
        elemento.add(new Option('Selecione', ''));
        elemento.dispatchEvent(new Event('change'));
    }

    cboTurma.addEventListener('change', async () => {
        const turma = cboTurma.value;
        if (turma === '') {
            return;
        }

        let etapa = 0;
        turmasObj.forEach(turmaSeparada => {
            if (turmaSeparada.codigo == turma) {
                let nomeSelecionado = turmaSeparada.nomeTurma + ' - ' + turmaSeparada.nomeEtapa;
                if (cboTurma.selectedOptions[0].text.includes(nomeSelecionado)) {
                    etapa = turmaSeparada.etapa;
                    return;
                }
            }
        })

        etapaSelecionada = etapa;

        await CurrentWindow.axios.get(`${routes.regencias}/${turma}/etapas/${etapa}/regencias?validaUsuario`).then(responseRegencias => {
            if (responseRegencias.error) {
                alert(responseRegencias.message);
                return;
            }
            const disciplinas = responseRegencias.data.data;
            oToggleRegencia.clearAll();
            disciplinas.forEach(disciplina => {
                oToggleRegencia.addSelect({
                    'iRegencia': disciplina.codigo,
                    'sRegencia': disciplina.disciplina.disciplina.nome,
                });
            });
            oToggleRegencia.renderRows();
        });
    });
</script>
</body>
</html>
