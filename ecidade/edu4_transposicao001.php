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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" name="viewport">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css" />
    <link type="text/css" href="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css" rel="stylesheet">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <style>
        .modal {
            background-color: rgba(0, 0, 0, 0.7);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 999999;
        }

        .modal .modalContent {
            background-color: #F7F7F7;
            position: relative;
            top: 10%;
            margin: 0 auto;
            width: 50vw;
            min-height: 410px;
            min-width: 800px;
            padding: 10px;
        }

        #aviso-servidor {
            margin-top: 2rem;
            background-color: #ffcbcb;
            padding: 1rem 2rem;
            border-left: 2px solid red;
        }

        .hidden-aviso {
            display: none;
        }
    </style>
</head>

<body class="body-default">
<div class="container">
    <input type="hidden" id="escolaOrigem" value="<?= db_getsession('DB_coddepto') ?>" />
    <fieldset>
        <legend>Transposição/Inscrição</legend>
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="ed57_i_codigo" id="turma_ancora">Turma: </label></td>
                <td>
                    <input type="text" id="ed57_i_codigo" />
                    <input type="text" id="ed57_c_descr" />
                </td>
            </tr>
            <tr>
                <td><label for="anoDestino">Ano de Destino: &nbsp;&nbsp;</label></td>
                <td>
                    <input type="text" id="anoDestino" class="field-size2" value="" disabled>
                </td>
            </tr>
            <tr>
                <td><label for="cboEtapa">Etapa: </label></td>
                <td>
                    <select name="cboEtapa" id="cboEtapa">
                        <option value="">Selecione uma etapa de destino</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnBuscar">
        <i class="fa fa-search"></i>
        Buscar
    </button>
    <div id="aviso-servidor" class="hidden-aviso">
        <p>O campo só deve ser preenchido caso o responsável pelo aluno seja servidor ativo da prefeitura!</p>
    </div>
</div>
<div class="container">
    <fieldset style="border: 2px groove #FFF">
        <legend>Selecionar opções de escolas</legend>
        <div style="width: 1200px">
            <table id="data-table" class="table table-sm" data-height="370" style="width: 100%;">
            </table>
        </div>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnEmitir">
        <i class="far fa-id-card"></i>
        Imprimir
    </button>
    <button type="button" class="btn btn-light" id="btnSalvar">
        <i class="fa fa-save"></i>
        Salvar
    </button>
</div>

<div class="modal" id="modalIrmaoEscola" style="display: none;">
    <div class="modalContent" style="text-align: center">
        <table id="table-irmao" class="table table-sm" data-height="350" style="width: 100%"></table>
        <br />
        <button type="button" class="btn btn-light" id="btnFechar">
            <i class="far fa-window-close"></i>
            Fechar
        </button>
        <button type="button" class="btn btn-light" id="btnLimparSelecao">
            <i class="fas fa-eraser"></i>
            Não tem Irmão na Escola
        </button>
    </div>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var urlApi;
    window.addEventListener('load', () => {
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
        });
    });
    let escolasDisponiveis = [];
    let candidatoAtual;
    let escolaOpcaoAtual;
    let irmaoOpcaoAtual;
    let servidorOpcaoAtual;
    let anoTransposicaoAtiva;
    let etapaOrigem;

    const turmaAncora = document.getElementById("turma_ancora");
    const turmaCodigo = document.getElementById("ed57_i_codigo");
    const turmaDescricao = document.getElementById("ed57_c_descr");
    const cboEtapa = document.getElementById("cboEtapa");
    const inputAnoDestino = document.getElementById("anoDestino");
    const inputEscolaOrigem = document.getElementById("escolaOrigem");
    const btnBuscar = document.getElementById("btnBuscar");
    const btnEmitir = document.getElementById("btnEmitir");
    const btnSalvar = document.getElementById("btnSalvar");
    const btnFechar = document.getElementById("btnFechar");
    const btnLimparSelecao = document.getElementById("btnLimparSelecao");
    const mensagemAvisoServidor = document.getElementById("aviso-servidor");

    const modalIrmaoEscola = document.getElementById("modalIrmaoEscola");

    const retornoTurma = () => {
        cboEtapa.length = 1;
        if (empty(turmaCodigo.value)) {
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'buscarProximaEtapa');
        formData.append('turma', turmaCodigo.value);
        HttpClient.post(`edu4_transposicao.RPC.php`, {
            body: formData
        }).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            etapaOrigem = response.etapaOrigem;

            inputAnoDestino.value = response.anoDestino;
            response.etapasDestino.map(etapa => {
                cboEtapa.options.add(new Option(etapa.descricao, etapa.codigo));
            });

            anoTransposicaoAtiva = response.transposicao.ed353_ativa;
            btnSalvar.disabled = !anoTransposicaoAtiva;
            if (!anoTransposicaoAtiva) {
                alert("As incrições para Transposição estão encerradas para o ano selecionado!\n Informações apenas para visualização.");
            }
        });
    }

    new DBLookUp(turmaAncora, turmaCodigo, turmaDescricao, {
        'sArquivo': 'func_turma.php',
        'sLabel': 'Pesquisar Turma',
        'sObjetoLookUp': "db_iframe_turma",
        'aParametrosAdicionais': ['turmasAtivas=true'],
        'fCallBack': retornoTurma
    });

    window.events = {
        'change select': function(e, value, row, index) {
            const opcao = e.target.dataset.opcao;
            if (row.escola1 === parseInt(e.target.value) ||
                row.escola2 === parseInt(e.target.value) ||
                row.escola3 === parseInt(e.target.value)
            ) {
                alert('Opção já selecionada!');
                e.target.value = '';
                return;
            }
            row[opcao] = parseInt(e.target.value);
            if (isNaN(row[opcao])) {
                row[opcao] = '';
            }
            const numero = opcao.split('').reverse()[0];
            row[`irmao${numero}`] = '';
            row[`servidor${numero}`] = '';
            let candidatos = table.bootstrapTable('getData');
            table.bootstrapTable('load', candidatos);
        },
        'click .selecionarIrmao': function(e, value, row, index) {
            const candidatos = table.bootstrapTable('getData').map((candidato) => {
                if (candidato.codigo_aluno === candidatoAtual) {
                    candidato[irmaoOpcaoAtual] = parseInt(row.codigo);
                }
                return candidato;
            });
            table.bootstrapTable('load', candidatos);
            modalIrmaoEscola.style.display = 'none';
        },
        'click .input-servidor': function () {
            mensagemAvisoServidor.classList.remove('hidden-aviso')
        },
        'blur .input-servidor': function(e, value, row, index) {
            mensagemAvisoServidor.classList.add('hidden-aviso')
            const candidatoAtual = row.codigo_aluno;
            const servidorOpcaoAtual = e.target.dataset.servidoropcao;
            const matricula = e.target.value; // Captura o valor inserido no campo de input

            const candidatos = table.bootstrapTable('getData').map((candidato) => {
                if (candidato.codigo_aluno === candidatoAtual) {
                    candidato[servidorOpcaoAtual] = matricula !== '' ? parseInt(matricula) : matricula;
                }
                return candidato;
            });

            // table.bootstrapTable('load', candidatos); // Recarrega a tabela com os dados atualizados
        },
        'click .abrirModal': function(e, value, row, index) {
            candidatoAtual = row.codigo_aluno;
            escolaOpcaoAtual = e.target.dataset.escolaopcao;
            irmaoOpcaoAtual = e.target.dataset.irmaoopcao;

            if (empty(row[escolaOpcaoAtual]) || isNaN(row[escolaOpcaoAtual])) {
                e.target.checked = false;
                limparSelecaoIrmaoEscola();
                alert('Selecione a Escola de Destino antes de selecionar o Irmão na Escola.');
                return;
            }
            modalIrmaoEscola.style.display = 'block';
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            HttpClient.get(
                `${urlApi}/educacao/escola/alunos-matriculados/${row[escolaOpcaoAtual]}`).then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                tableIrmao.bootstrapTable('load', response.data);
            });
        },
    }

    const formatterOpcaoEscola = (value, row, index, field) => {
        let cboEscolas = document.createElement('select');
        cboEscolas.setAttribute('name', 'selectEscolaOpcao');
        cboEscolas.setAttribute('id', `${field}-${row.codigo_aluno}`);
        cboEscolas.style.width = '220px';
        cboEscolas.dataset.opcao = field;
        cboEscolas.dataset.matricula = row.codigo_matricula;

        cboEscolas.disabled = !anoTransposicaoAtiva || row.situacao !== "MATRICULADO";

        cboEscolas.add(new Option('Selecione', ''));
        escolasDisponiveis.map((escola) => {
            cboEscolas.add(new Option(`${escola.ed18_i_codigo} - ${escola.ed18_c_nome}`, escola.ed18_i_codigo));
        });

        return cboEscolas.outerHTML;
    }

    const formatterIrmaoEscola = (value, row, index, field) => {
        const opcao = field.split('').reverse()[0];
        let texto = 'Selecionar';
        let desabilitar = '';
        if (row.situacao !== "MATRICULADO" || !anoTransposicaoAtiva) {
            desabilitar = ' disabled ';
        }

        if (!empty(value)) {
            texto = value;
            return `<button type="button" class="abrirModal" data-escolaopcao="escola${opcao}" data-irmaoopcao="${field}" ${desabilitar}> ${texto} </button>
                <button type="button" class="btn btn-light" onclick="limparSelecaoIrmaoEscola(${row.codigo_aluno}, '${field}')" ${desabilitar}>
                    <i class="fas fa-trash"></i>
                </button>`;
        }
        return `<button type="button" class="abrirModal" data-escolaopcao="escola${opcao}" data-irmaoopcao="${field}" ${desabilitar}> ${texto} </button>`;
    }

   const formatterServidorEscola = (value, row, index, field) => {
        const opcao = field.split('').reverse()[0];

        let desabilitar = '';
        if (row.situacao !== "MATRICULADO" || !anoTransposicaoAtiva) {
            desabilitar = ' disabled ';
        }
        return `<input type="text" class="input-servidor" placeholder="Matrícula do Servidor" value="${value !== null ? value : ''}" data-servidoropcao="servidor${opcao}" ${desabilitar} />`;
    }

    function rowStyle(row) {
        if (row.situacao == "DESIGNADO") {
            return {
                classes: 'success'
            }
        }
        if (row.situacao != "MATRICULADO") {
            return {
                classes: 'danger'
            }
        }
        return {}
    }

    var table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        cache: 'false',
        fixedColumns: true,
        fixedNumber: 2,
        rowStyle: rowStyle,
        columns: [{
            title: 'Código',
            field: 'codigo_aluno',
            align: 'left',
            width: '100'
        },
            {
                title: 'Candidato',
                field: 'nome_aluno',
                align: 'left',
                formatter: (value, row) => {
                    let situacao = '';
                    if (row.situacao != 'MATRICULADO') {
                        situacao = `(${row.situacao})`;
                    }
                    return `<div style="width: 250px">${value} ${situacao}</div>`;
                }
            },
            {
                title: 'Responsável é servidor?',
                field: 'servidor1',
                align: 'left',
                valign: 'center',
                width: '100',
                events: events,
                formatter: formatterServidorEscola
            },
            {
                title: 'Opção 1',
                field: 'escola1',
                align: 'left',
                valign: 'center',
                formatter: formatterOpcaoEscola,
                events: events,
                width: '100',
            },
            {
                title: 'Irmão na Escola',
                field: 'irmao1',
                align: 'left',
                valign: 'center',
                width: '100',
                events: events,
                formatter: formatterIrmaoEscola
            },
            {
                title: 'Opção 2',
                field: 'escola2',
                align: 'left',
                valign: 'center',
                formatter: formatterOpcaoEscola,
                events: events,
                width: '100',
            },
            {
                title: 'Irmão na Escola',
                field: 'irmao2',
                align: 'left',
                valign: 'center',
                width: '100',
                events: events,
                formatter: formatterIrmaoEscola
            },
            {
                title: 'Opção 3',
                field: 'escola3',
                align: 'left',
                valign: 'center',
                formatter: formatterOpcaoEscola,
                events: events,
                width: '100',
            },
            {
                title: 'Irmão na Escola',
                field: 'irmao3',
                align: 'left',
                valign: 'center',
                width: '100',
                events: events,
                formatter: formatterIrmaoEscola
            }
        ],
        onPostBody: (data) => {
            const alunos = data;
            let selectEscolas = document.getElementsByName('selectEscolaOpcao')
            selectEscolas.forEach((selectEscola) => {
                const id = selectEscola.getAttribute('id');
                const opcao = selectEscola.dataset.opcao;
                alunos.map((aluno) => {
                    if (aluno.codigo_matricula === selectEscola.dataset.matricula) {
                        if (!empty(aluno[opcao])) {
                            selectEscola.value = aluno[opcao]
                        }
                    }
                });

                jQuery(`#${id}`).select2({
                    language: 'pt-BR'
                });
            })
        },
    })

    var tableIrmao = jQuery('#table-irmao');
    tableIrmao.bootstrapTable({
        locale: 'pt-BR',
        search: true,
        columns: [{
            title: 'Código',
            field: 'codigo',
            align: 'left',
            width: '100'
        },
            {
                title: 'Aluno',
                field: 'nome',
                align: 'left'
            },
            {
                title: 'Escolher',
                field: 'escolher',
                align: 'center',
                formatter: () => {
                    return `<a class="selecionarIrmao">Selecionar</a>`;
                },
                events: events
            }
        ]
    });

    btnBuscar.addEventListener('click', () => {
        if (empty(turmaCodigo.value)) {
            alert('Selecione uma Turma de Origem');
            return;
        }
        if (empty(cboEtapa.value)) {
            alert('Selecione uma Etapa de Destino');
            return;
        }
        buscarCandidatos();
    });

    const buscarCandidatos = () => {
        const formData = new FormData();
        formData.append('turma', turmaCodigo.value);
        formData.append('etapaDestino', cboEtapa.value);
        formData.append('anoDestino', inputAnoDestino.value);
        PHPSession.appendFormData(formData);
        HttpClient
            .post(`${urlApi}/educacao/escola/transposicao/buscar-candidatos`, {
                body: formData
            })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                let data = response.data;
                escolasDisponiveis = data.escolasDisponiveis;
                table.bootstrapTable('showColumn', 'servidor1')
                table.bootstrapTable('load', data.candidatos);
            });
    }

    btnEmitir.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('etapaDestino', cboEtapa.value);
        formData.append('anoDestino', inputAnoDestino.value);
        formData.append('escolaOrigem', inputEscolaOrigem.value);
        PHPSession.appendFormData(formData);
        HttpClient
            .post(`${urlApi}/educacao/escola/transposicao/relatorio`, {
                body: formData
            })
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }

                window.open(
                    response.data.file,
                    '',
                    'height=' + screen.height + ',width=' + screen.width + 'scrollbars=1,location=0'
                );
            });
    });

    btnSalvar.addEventListener('click', () => {
        const transposicaoData = table.bootstrapTable('getData');
        let validacaoFlag = false;

        const newData = transposicaoData.filter(row => {
            if ((row.escola1 == '' && row.escola2 == '') || (row.escola1 != '' && row.escola2 != '')) {
                return row;
            }
            validacaoFlag = true
        })

        if (validacaoFlag) {
            alert('Você precisa escolher no mínimo duas opções de escolas para os candidatos. Os candidatos com duas opções já estão salvos. Selecione 2 opções para os candidatos restantes.');
            return; // Adiciona um retorno para evitar salvar quando houver candidatos incompletos
        }

        const formData = new FormData();
        formData.append('etapaDestino', cboEtapa.value);
        formData.append('anoDestino', inputAnoDestino.value);
        formData.append('candidatos', JSON.stringify(newData));
        formData.append('etapaOrigem', etapaOrigem);

        PHPSession.appendFormData(formData);

        HttpClient.post(`${urlApi}/educacao/escola/transposicao/salvar-candidatos`, {
            body: formData
        })
            .then((response) => {
                alert(response.message);
                buscarCandidatos();
            });
    });

    btnFechar.addEventListener('click', () => {
        escolaOpcaoAtual = '';
        irmaoOpcaoAtual = '';
        let candidatos = table.bootstrapTable('getData');
        table.bootstrapTable('load', candidatos);
        tableIrmao.bootstrapTable('load', []);
        modalIrmaoEscola.style.display = 'none';
    });

    const limparSelecaoIrmaoEscola = (codCandidato = null, codIrmao = null) => {
        if (codCandidato != null && codIrmao != null) {
            candidatoAtual = codCandidato
            irmaoOpcaoAtual = codIrmao
        }
        const candidatos = table.bootstrapTable('getData').map((candidato) => {
            if (candidato.codigo_aluno == candidatoAtual) {
                candidato[irmaoOpcaoAtual] = '';
            }
            return candidato;
        });
        table.bootstrapTable('load', candidatos);
        modalIrmaoEscola.style.display = 'none';
    }

    btnLimparSelecao.addEventListener('click', () => {
        limparSelecaoIrmaoEscola();
    });

</script>
</body>

</html>


