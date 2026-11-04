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
require_once(modification("dbforms/db_funcoes.php"));

?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <style type="text/css">
        #ctnVagas {
            font-weight: bold;
        }
    </style>
</head>
<body class='body-default'>
<div class='container'>
    <fieldset>
        <legend>Troca Alunos de Turma</legend>
        <fieldset class="separator">
            <legend>Dados da Turma de Origem</legend>
        </fieldset>
        <table class="form-container">
            <tr>
                <td class="field-size2">
                    <label for="codigoTurmaOrigem">
                        <a href="#" id="ancoraTurmaOrigem">Turma: </a>
                    </label>
                </td>
                <td>
                    <input type="text" name="codigoTurmaOrigem" id="codigoTurmaOrigem" readonly
                           class="readonly field-size2">
                    <input type="text" name="nomeTurmaOrigem" id="nomeTurmaOrigem" readonly
                           class="readonly field-size7">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="etapasTurmaOrigem">Etapa: </label>
                </td>
                <td>
                    <select name="etapasTurmaOrigem" id="etapasTurmaOrigem">
                        <option value="">SELECIONE:</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="calendarioTurmaOrigem">Calendário: </label>
                </td>
                <td>
                    <input type="text" name="calendarioTurmaOrigem" id="calendarioTurmaOrigem" readonly
                           class="readonly field-size5">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="ensinoTurmaOrigem">Ensino: </label>
                </td>
                <td>
                    <input type="text" name="ensinoTurmaOrigem" id="ensinoTurmaOrigem" readonly
                           class="readonly field-size9">
                </td>
            </tr>
        </table>

        <fieldset style="width: 550px" class="separator">
            <legend>Alunos</legend>
            <div id="ctnGridAlunos"></div>
        </fieldset>
        <fieldset class="separator">
            <legend>Dados da Turma de Destino</legend>
        </fieldset>

        <table class="form-container">
            <tr>
                <td class="field-size2">
                    <label for="codigoTurmaDestino">
                        <a href="#" id="ancoraTurmaDestino">Turma: </a>
                    </label>
                </td>
                <td>
                    <input type="text" name="codigoTurmaDestino" id="codigoTurmaDestino" readonly
                           class="readonly field-size2">
                    <input type="text" name="nomeTurmaDestino" id="nomeTurmaDestino" readonly
                           class="readonly field-size7">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="etapaTurmaDestino">Etapa: </label>
                </td>
                <td>
                    <input type="text" name="etapaTurmaDestino" id="etapaTurmaDestino" readonly
                           class="readonly field-size3">
                    <label for="calendarioTurmaDestino">Calendário: </label>
                    <input type="text" name="calendarioTurmaDestino" id="calendarioTurmaDestino" readonly
                           class="readonly field-size5">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="ensinoTurmaOrigem">Ensino: </label>
                </td>
                <td>
                    <input type="text" name="ensinoTurmaDestino" id="ensinoTurmaDestino" readonly
                           class="readonly field-size9">
                </td>
            </tr>
        </table>
        <fieldset class="separator">
            <legend>Vagas</legend>
        </fieldset>
        <div id="ctnVagas"></div>
        <table class="form-container">
            <tr>
                <td>
                    <label for="dataAlteracao">Data da Alteração: </label>
                </td>
                <td>
                    <input type="text" id="dataAlteracao" name="dataAlteracao">
                    <label for="importarAvaliacoes">Importar aproveitamento da turma de origem: </label>
                    <select name="importarAvaliacoes" id="importarAvaliacoes" style="width: 85px" disabled>
                        <option value="S">SIM</option>
                        <option value="N">NÃO</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="container">
    <button id="btnTrocarAlunos" disabled>
        <i class="fas fa-exchange-alt"></i> Trocar alunos
    </button>
</div>
<div id="ctnModalTurmas">
    <div class="container">
        <fieldset style="width: 550px" class="separator">
            <legend>Importação de Aproveitamento</legend>
            <div id="ctnGridRegencias" style="width: 600px"></div>
            <div id="ctnProcedimentosAvaliacao"></div>
        </fieldset>
        <br/>
        <button id="btnSalvarAlteracoes">
            <i class="fas fa-save"></i> Salvar alterações
        </button>
        <br/>
    </div>
    <br/>
</div>
<?php
db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
);
?>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });

    const ancoraTurmaOrigem = document.getElementById("ancoraTurmaOrigem");
    const codigoTurmaOrigem = document.getElementById("codigoTurmaOrigem");
    const nomeTurmaOrigem = document.getElementById("nomeTurmaOrigem");
    const etapasTurmaOrigem = document.getElementById("etapasTurmaOrigem");
    const calendarioTurmaOrigem = document.getElementById("calendarioTurmaOrigem");
    const ensinoTurmaOrigem = document.getElementById("ensinoTurmaOrigem");
    const ancoraTurmaDestino = document.getElementById("ancoraTurmaDestino");

    const codigoTurmaDestino = document.getElementById("codigoTurmaDestino");
    const nomeTurmaDestino = document.getElementById("nomeTurmaDestino");
    const calendarioTurmaDestino = document.getElementById("calendarioTurmaDestino");
    const etapaTurmaDestino = document.getElementById("etapaTurmaDestino");
    const ensinoTurmaDestino = document.getElementById("ensinoTurmaDestino");

    const inputDataAlteracao = document.getElementById('dataAlteracao');
    const dataAlteracao = new DBInputDate(inputDataAlteracao);
    dataAlteracao.value = new Date().toLocaleString();

    const importarAvaliacoes = document.getElementById('importarAvaliacoes');


    const ctnVagas = document.getElementById('ctnVagas');

    const btnTrocarAlunos = document.getElementById("btnTrocarAlunos");
    const btnSalvarAlteracoes = document.getElementById("btnSalvarAlteracoes");

    const ctnGridAlunos = document.getElementById('ctnGridAlunos');
    const collectionAlunos = new Collection().setId('codigo');
    var gridAlunos = new DatagridCollection(collectionAlunos).configure({
        order: false,
        height: 200
    });
    gridAlunos.getGrid().setCheckbox(0);
    gridAlunos.addColumn('codigo', {label: "Código", width: '12%', align: 'center'});
    gridAlunos.addColumn('nome', {label: "Nome", width: '80%'});
    gridAlunos.show(ctnGridAlunos);

    const selectRegencias = document.createElement('select');
    selectRegencias.style.width = '100%';

    const ctnGridRegencias = document.getElementById('ctnGridRegencias');
    const collectionRegencias = new Collection().setId('regenciaOrigem');
    var gridRegencias = new DatagridCollection(collectionRegencias).configure({
        order: false,
        height: 180
    });
    gridRegencias.addColumn('disciplinaOrigem', {label: "Disciplina de Origem", width: '50%'});
    gridRegencias.addColumn('disciplinaDestino', {label: "Disciplina de Destino", width: '50%'})
        .transform(function (item, linha) {
            if (linha.equivalente === null || linha.regenciasDestinoSemVinculo != null) {
                return montaSelectRegencia(linha);
            }
            return linha.equivalente.disciplinaDestino;
        });
    gridRegencias.show(ctnGridRegencias);

    gridRegencias.setEvent('onafterrenderrows', () => {
        collectionRegencias.get().map((linha) => {
            const selectRegencia = $(`selectRegencia_${linha.regenciaOrigem}`);
            if (linha.equivalente != null && selectRegencia != undefined) {
                selectRegencia.value = linha.equivalente.regenciaDestino;
            }
        });
    });

    const selectPeriodos = document.createElement('select');
    selectPeriodos.style.width = '100%';

    const ctnProcedimentosAvaliacao = document.getElementById('ctnProcedimentosAvaliacao');
    const collectionProcedimentos = new Collection().setId('codigo');
    var gridProcedimentos = new DatagridCollection(collectionProcedimentos).configure({
        order: false,
        height: 100
    });
    gridProcedimentos.addColumn('procedimentoOrigem', {label: "Procedimento - Turma Origem", width: '50%'});
    gridProcedimentos.addColumn('procedimentoDestino', {label: "Procedimento - Turma Destino", width: '50%'})
    // .transform(function (item, linha) {
    // if (linha.equivalente !== null) {
    //     return linha.equivalente.disciplinaDestino;
    // }
    // if (linha.regenciasDestinoSemVinculo != null) {
    //     selectPeriodos.options.length = 0;
    //     selectPeriodos.options.add(new Option('', ''));
    //     linha.regenciasDestinoSemVinculo.map((regencia) => {
    //         selectPeriodos.options.add(
    //             new Option(`${regencia.disciplina_ensino.disciplina.ed232_c_descrcompleta}`, `selectPeriodos_${regencia.ed59_i_codigo}`)
    //         );
    //     });
    //     return selectPeriodos.outerHTML;
    // }
    // });
    gridProcedimentos.show(ctnProcedimentosAvaliacao);

    const ctnModalTurmas = document.getElementById('ctnModalTurmas');
    const windowTurmas = new windowAux('windowTurmas', 'Comparação entre Turma', 800, 500);
    windowTurmas.setContent(ctnModalTurmas);
    windowTurmas.setShutDownFunction(() => {
        closeWindowAux();
    });

    new DBMessageBoard('msgBoardTurmas',
        'Troca Aluno de Turma',
        'Caso as turmas de origem e destino tenham disciplinas e/ou períodos de avaliação diferentes, ' +
        'informe abaixo quais disciplinas e períodos de avaliação da turma de destino que vão receber as ' +
        'informações do aluno.',
        windowTurmas.getContentContainer()
    );

    const retornoTurmaOrigem = (campo1, campo2, codigo) => {
        limparTurmaDestino();
        limparGridAlunos();
        setParametrosTurmaDestino();
        HttpClient.get(`${urlApi}/educacao/escola/turma/${codigo}`).then((response) => {
            let turma = response.data;
            codigoTurmaOrigem.value = turma.ed57_i_codigo;
            nomeTurmaOrigem.value = turma.ed57_c_descr;
            calendarioTurmaOrigem.value = turma.calendario.ed52_c_descr;

            etapasTurmaOrigem.options.length = 0;
            etapasTurmaOrigem.add(new Option("SELECIONE: ", ""));
            turma.etapas.map((etapa) => {
                etapasTurmaOrigem.add(new Option(etapa.ed11_c_descr, etapa.ed11_i_codigo));
                ensinoTurmaOrigem.value = etapa.ensino.ed10_c_descr;
            });

            if (etapasTurmaOrigem.options.length === 2) {
                etapasTurmaOrigem.value = etapasTurmaOrigem.options[1].value;
            }
            etapasTurmaOrigem.dispatchEvent(new Event('change'));
        });
    }

    const lookUpTurmaOrigem = new DBLookUp(ancoraTurmaOrigem, codigoTurmaOrigem, nomeTurmaOrigem, {
        'sArquivo': 'func_turma.php',
        'sLabel': 'Pesquisar Turma',
        'fCallBack': retornoTurmaOrigem,
        'aCamposAdicionais': ['ed57_i_codigo'],
        'sObjetoLookUp': "db_iframe_turmaorigem"
    });
    lookUpTurmaOrigem.abrirJanela(true);

    const retornoTurmaDestino = (campo1, campo2, codigo, nometurma, codetapa, nomeetapa, ensino) => {
        HttpClient.get(`${urlApi}/educacao/escola/turma/${codigo}`).then((response) => {
            let turma = response.data;
            codigoTurmaDestino.value = turma.ed57_i_codigo;
            nomeTurmaDestino.value = turma.ed57_c_descr;
            calendarioTurmaDestino.value = turma.calendario.ed52_c_descr;
            etapaTurmaDestino.value = nomeetapa;
            ensinoTurmaDestino.value = ensino;
        });

        HttpClient.get(`${urlApi}/educacao/escola/vagas-por-turma/${codigo}`).then((response) => {
            let turnos = response.data.turnos_referentes;
            ctnVagas.innerHTML = "";
            let ctnTurnos = document.createElement('div');
            ctnTurnos.setAttribute('id', 'ctnTurnos');

            let tabela = document.createElement('table');
            tabela.setAttribute('class', 'form-container');
            turnos.map((turno) => {
                addLinhaVagas(turno, tabela);
                criaCheckboxTurno(ctnTurnos, turno);
            });
            ctnVagas.appendChild(tabela);
            ctnVagas.appendChild(ctnTurnos);

            btnTrocarAlunos.disabled = false;
            importarAvaliacoes.disabled = false;
        });

        let turmaOrigem = codigoTurmaOrigem.value;
        let etapaSelecionada = etapasTurmaOrigem.value;
        HttpClient.get(`${urlApi}/educacao/escola/regencias-turmas/${turmaOrigem}/${codigo}/${etapaSelecionada}`)
            .then((response) => {
                montaGradeComparativo(response.data, codigo);
            });
    }

    const lookUpTurmaDestino = new DBLookUp(ancoraTurmaDestino, codigoTurmaDestino, nomeTurmaDestino, {
        'sArquivo': 'func_turmatransf.php',
        'sLabel': 'Pesquisar Turma de Destino',
        'fCallBack': retornoTurmaDestino,
        'aCamposAdicionais': ['ed57_i_codigo', 'ed57_c_descr', 'codetapa', 'nomeetapa', 'ed10_c_descr'],
        'sObjetoLookUp': "db_iframe_turmadestino"
    });
    lookUpTurmaDestino.desabilitar();

    etapasTurmaOrigem.addEventListener('change', () => {
        limparTurmaDestino();
        limparGridAlunos();
        limparGridRegencias();
        setParametrosTurmaDestino();
        let etapaSelecionada = etapasTurmaOrigem.value;
        if (etapaSelecionada === "") {
            return;
        }

        let codigoTurma = codigoTurmaOrigem.value;
        HttpClient.get(`${urlApi}/educacao/escola/matriculas-por-turma/${codigoTurma}/${etapaSelecionada}`)
            .then((response) => {
                let matriculas = response.data;

                collectionAlunos.clear();
                matriculas.map((matricula) => {
                    collectionAlunos.add({
                        matricula: matricula.ed60_matricula,
                        codigo: matricula.aluno.ed47_i_codigo,
                        nome: matricula.aluno.ed47_v_nome
                    });
                });
                collectionAlunos.sort('ASC', ['nome', 'codigo']);

                gridAlunos.reload();
            });
    });

    const setParametrosTurmaDestino = () => {
        let etapaSelecionada = etapasTurmaOrigem.value;
        let turmaSelecionada = codigoTurmaOrigem.value;
        if (etapaSelecionada === "") {
            lookUpTurmaDestino.desabilitar();
            return;
        }
        let parametrosAdicionais = [
            'turmasprogressao=f',
            'filtrarPorAnoCalendario=true',
            'apenasensinodaturma=true'
        ];
        parametrosAdicionais.push(`turma=${turmaSelecionada}`);
        parametrosAdicionais.push(`etapaorig=${etapaSelecionada}`);
        lookUpTurmaDestino.setParametrosAdicionais(parametrosAdicionais);
        lookUpTurmaDestino.habilitar();
        codigoTurmaDestino.classList.add("readonly");
        codigoTurmaDestino.readOnly = true;
    }

    const limparTurmaOrigem = () => {
        ctnVagas.innerHTML = "";

        codigoTurmaOrigem.value = "";
        nomeTurmaOrigem.value = "";
        calendarioTurmaOrigem.value = "";
        etapasTurmaOrigem.options.length = 0;
        etapasTurmaOrigem.add(new Option('SELECIONE: ', ''));
        ensinoTurmaOrigem.value = "";

        btnTrocarAlunos.disabled = true;
        importarAvaliacoes.disabled = true;
    }

    const limparTurmaDestino = () => {
        ctnVagas.innerHTML = "";

        codigoTurmaDestino.value = "";
        nomeTurmaDestino.value = "";
        calendarioTurmaDestino.value = "";
        etapaTurmaDestino.value = "";
        ensinoTurmaDestino.value = "";

        btnTrocarAlunos.disabled = true;
        importarAvaliacoes.disabled = true;
        importarAvaliacoes.value = 'S';
    }

    const limparGridAlunos = () => {
        collectionAlunos.clear();
        gridAlunos.reload();
    }

    const limparGridRegencias = () => {
        collectionRegencias.clear();
        gridRegencias.reload();
    }

    const montaGradeComparativo = (turmas, turmaDestinoSelecionada) => {
        windowTurmas.show(0, 0, true);

        let turmaOrigemSelecionada = codigoTurmaOrigem.value;
        let turmaOrigem = turmas.filter((turma) => {
            return turma.ed57_i_codigo == turmaOrigemSelecionada;
        }).shift();
        let turmaDestino = turmas.filter((turma) => {
            return turma.ed57_i_codigo == turmaDestinoSelecionada;
        }).shift();

        let regenciasDestinoSemVinculo = turmaDestino.regenciasSemVinculo;

        gridRegencias.clear();
        collectionRegencias.clear();
        turmaOrigem.regenciasOrigem.map((regencia) => {
            let disciplina = regencia.disciplina_ensino.disciplina.ed232_c_descrcompleta;
            if (regencia.equivalente !== undefined) {
                let equivalente = regencia.equivalente;
                let disciplinaEquivalente = regencia.equivalente.disciplina_ensino.disciplina.ed232_c_descrcompleta;

                collectionRegencias.add({
                    regenciaOrigem: regencia.ed59_i_codigo,
                    disciplinaOrigem: disciplina,
                    equivalente: {
                        regenciaDestino: equivalente.ed59_i_codigo,
                        disciplinaDestino: disciplinaEquivalente,
                    },
                    regenciasDestinoSemVinculo: null
                });
            } else {
                collectionRegencias.add({
                    regenciaOrigem: regencia.ed59_i_codigo,
                    disciplinaOrigem: disciplina,
                    equivalente: null,
                    regenciasDestinoSemVinculo: regenciasDestinoSemVinculo
                });
            }
        });
        gridRegencias.reload();

        gridProcedimentos.clear();
        let procedimentosAvaliacao = turmaOrigem.etapa_regime_matricula.procedimento.procedimentos_avaliacao;
        let procedimentoInvalido = false;
        procedimentosAvaliacao.map((procedimento) => {
            let tipoAvaliacao = procedimento.forma_avaliacao.ed37_c_tipo;
            let formaAvaliacao = tipoAvaliacao === "NOTA" ? `NOTA (${procedimento.forma_avaliacao.ed37_i_menorvalor} a ${procedimento.forma_avaliacao.ed37_i_maiorvalor})` : tipoAvaliacao;
            let descricao = `${procedimento.periodo_avaliacao.ed09_c_descr} - ${formaAvaliacao}`;

            let procedimentoAvaliacao = {
                codigo: procedimento.ed41_i_codigo,
                procedimentoOrigem: descricao,
                procedimentoDestino: ''
            };

            if (procedimento.equivalente !== undefined) {
                procedimentoAvaliacao = {
                    ...procedimentoAvaliacao,
                    procedimentoDestino: descricao
                };
                if (procedimento.ed41_i_codigo !== procedimento.equivalente.ed41_i_codigo) {
                    procedimentoInvalido = true;
                }
            } else {
                procedimentoInvalido = true;
            }
            collectionProcedimentos.add(procedimentoAvaliacao);
        });
        gridProcedimentos.reload();
        if (procedimentoInvalido) {
            importarAvaliacoes.value = 'N';
            importarAvaliacoes.disabled = true;
            closeWindowAux();
            alert('O Procedimento de avaliação da turma selecionada é diferente do procedimento de' +
                ' avaliação da turma de origem, NÃO poderá ser importada as Avaliações.' );
        }
    }

    btnSalvarAlteracoes.addEventListener('click', () => {
        gridRegencias.getGrid().aRows.map((linha) => {
            const selectRegencia = $(`selectRegencia_${linha.itemCollection.ID}`);
            if (selectRegencia !== null) {
                let codigoEquivalente = selectRegencia.value;
                let disciplinaEquivalente = selectRegencia.innerText;

                if (codigoEquivalente === "") {
                    return;
                }

                collectionRegencias.add({
                    regenciaOrigem: linha.itemCollection.regenciaOrigem,
                    disciplinaOrigem: linha.itemCollection.disciplinaOrigem,
                    equivalente: {
                        regenciaDestino: codigoEquivalente,
                        disciplinaDestino: disciplinaEquivalente,
                    },
                    regenciasDestinoSemVinculo: linha.itemCollection.regenciasDestinoSemVinculo
                });
            }
        });
        closeWindowAux();
    });

    const montaSelectRegencia = (linha) => {
        selectRegencias.options.length = 0;
        selectRegencias.options.add(new Option('', ''));
        linha.regenciasDestinoSemVinculo.map((regencia) => {
            selectRegencias.setAttribute('id', `selectRegencia_${linha.regenciaOrigem}`);
            selectRegencias.options.add(
                new Option(`${regencia.disciplina_ensino.disciplina.ed232_c_descrcompleta}`, regencia.ed59_i_codigo)
            );
        });
        return selectRegencias.outerHTML;
    }

    const closeWindowAux = () => {
        if (!!windowTurmas.oDBMask) {
            windowTurmas.oDBMask.destroy();
        }
        windowTurmas.hide();
    }

    btnTrocarAlunos.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('turmaOrigem', codigoTurmaOrigem.value);
        formData.append('turmaDestino', codigoTurmaDestino.value);
        formData.append('etapaDestino', etapasTurmaOrigem.value);
        formData.append('dataAlteracao', dataAlteracao.__toLocaleDateString());
        formData.append('importarAvaliacoes', importarAvaliacoes.value);

        const matriculas = [];
        gridAlunos.getGrid().aRows.map((linha) => {
            if (linha.isSelected) {
                let aluno = linha.itemCollection;
                matriculas.push(aluno.matricula);
                formData.append('matriculas[]', aluno.matricula);
            }
        });
        if (matriculas.length === 0) {
            alert("Nenhum aluno selecionado.");
            return;
        }
        let turnosSelecionados = document.querySelectorAll('input[name="cboTurno"]:checked');
        if (turnosSelecionados.length === 0) {
            alert("Nenhum turno selecionado");
            return;
        }

        let turnos = [];
        turnosSelecionados.forEach((checkbox) => {
            formData.append('turnosReferentes[]', checkbox.value);
            turnos.push(checkbox.value);
        });

        let minimoVagasDisponiveis;
        turnos.map((turno) => {
            let inputVagasDisponiveis = document.getElementById(`vagasDisponiveis_${turno}`);
            let valorAtual = inputVagasDisponiveis.value;
            if (valorAtual < minimoVagasDisponiveis || empty(minimoVagasDisponiveis)) {
                minimoVagasDisponiveis = valorAtual;
            }
        });

        if (minimoVagasDisponiveis < matriculas.length) {
            alert("A quantidade de vagas disponíveis é menor que a quantidade" +
                " de Alunos selecionados para transferência.");
            return;
        }

        let regenciaSemVinculo = [];
        collectionRegencias.get().map((regencia) => {
            if (regencia.equivalente === null) {
                regenciaSemVinculo.push(regencia);
            }
            formData.append('regencias[]', JSON.stringify(regencia.build()));
        });
        collectionProcedimentos.get().map((procedimento) => {
            formData.append('procedimentosAvaliacao[]', JSON.stringify(procedimento.build()));
        });

        if (importarAvaliacoes.value === 'N' &&
            !confirm("Importar aproveitamento da turma de origem está marcado como NÃO. Caso este aluno tenha" +
                " algum aproveitamento na turma de origem, este terá que ser digitado manualmente!")) {
            return;
        }

        if (importarAvaliacoes.value === 'S' && regenciaSemVinculo.length > 0) {
            let msgDisciplinas = "Disciplinas que não foram informadas disciplinas equivalentes não terão seu" +
                " aproveitamento importado.\n";
            regenciaSemVinculo.map((regencia) => {
                msgDisciplinas += `\n${regencia.disciplinaOrigem}`;
            });
            msgDisciplinas += '\n\nDeseja continuar?';
            if (!confirm(msgDisciplinas)) {
                return;
            }
        }

        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/escola/procedimento/troca-de-turma`, {body: formData})
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                limparTurmaOrigem();
                limparTurmaDestino();
                limparGridAlunos();
                setParametrosTurmaDestino();
            });
    });

    const addLinhaVagas = (turno, tabela) => {
        let tr = tabela.insertRow();

        let tdPrincipal = tr.insertCell();
        tdPrincipal.appendChild(document.createTextNode(turno.turno.nome));
        tdPrincipal.classList.add('field-size2');

        let tdSecundaria = tr.insertCell();
        let idVagas = `vagas_${turno.ed336_turnoreferente}`;
        tdSecundaria.appendChild(criaInputVagas(turno.ed336_vagas, idVagas));

        tdSecundaria.appendChild(criaLabel(idVagas, ' Alunos matriculados: '));

        let idMatriculados = `matriculados_${turno.ed336_turnoreferente}`;
        tdSecundaria.appendChild(criaInputVagas(turno.matriculas_ativas, idMatriculados));

        tdSecundaria.appendChild(criaLabel(idVagas, ' Vagas disponíveis: '));

        let idVagasDisponiveis = `vagasDisponiveis_${turno.ed336_turnoreferente}`;
        tdSecundaria.appendChild(criaInputVagas((turno.ed336_vagas - turno.matriculas_ativas), idVagasDisponiveis));
    }

    const criaInputVagas = (valor, id) => {
        let inputVagas = document.createElement('input');
        inputVagas.setAttribute('id', id);
        inputVagas.setAttribute('type', 'text');
        inputVagas.setAttribute('readonly', 'readonly');
        inputVagas.classList.add('readonly');
        inputVagas.classList.add('field-size1');
        inputVagas.value = valor;

        return inputVagas;
    }

    const criaLabel = (referencia, valor) => {
        let label = document.createElement('label');
        label.setAttribute('for', referencia);
        label.innerText = valor;
        return label;
    }

    const criaCheckboxTurno = (container, turno) => {
        let cboTurno = document.createElement('input');
        cboTurno.setAttribute('type', 'checkbox');
        cboTurno.setAttribute('name', 'cboTurno');
        cboTurno.setAttribute('value', turno.ed336_turnoreferente);
        cboTurno.setAttribute('checked', 'checked');
        container.appendChild(cboTurno);
        container.appendChild(criaLabel(turno.ed336_turnoreferente, `${turno.turno.nome} `));
    }
</script>
</body>
</html>
