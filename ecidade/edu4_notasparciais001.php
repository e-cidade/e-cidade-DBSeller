<?php

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
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
</head>
<body>
<div class="container">
    <form id="frmConteudo" method="post" action="">
        <fieldset>
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td><label for="ano">Ano: </label></td>
                    <td>
                        <select id="ano" name="ano">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                    <td style="width: 16px"></td>
                    <td style="width: 16px"></td>
                    <td><label for="turma">Turma: </label></td>
                    <td>
                        <select id="turma" name="turma">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>

<div class="container">
    <fieldset style="border: 2px groove #FFF">
        <legend>Disciplinas da Turma</legend>
        <div class="alert alert-info" role="alert" id="situacao-andamento" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Avaliações: EM ANDAMENTO</strong>
        </div>
        <div class="alert alert-danger" role="alert" id="situacao-encerrada" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Avaliações: ENCERRADAS</strong>
            <br/>
            Não é possível editar as avaliações, pois as mesmas já foram encerradas!
        </div>
        <div style="width: 900px">
            <table id="data-table" class="table table-sm" data-height="350" style="width: 100%;">
            </table>
        </div>
    </fieldset>
</div>

<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    const cboAno = document.getElementById('ano');
    const cboTurma = document.getElementById('turma');
    const divAndamento = document.getElementById('situacao-andamento');
    const divEncerrada = document.getElementById('situacao-encerrada');

    let filtroAnos = [];

    PHPSession.loadData().then(() => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/educacao/escola/turmas/usuario`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            filtroAnos = response.data;
            cboAno.length = 1;
            filtroAnos.forEach(item => {
                cboAno.options.add(new Option(item.ano, item.ano));
            });
        });
    });

    cboAno.addEventListener('change', () => {
        const anoSelecionado = filtroAnos.find(item => {
            return item.ano === cboAno.value;
        });
        cboTurma.length = 1;
        anoSelecionado.turmas.forEach(turma => {
            cboTurma.options.add(new Option(`${turma.etapa} - ${turma.descricao}`, turma.turmaserieregimemat));
        });
        cboTurma.dispatchEvent(new Event('change'));
    });

    let turma;
    cboTurma.addEventListener('change', () => {
        const turmaSelecionada = cboTurma.value;
        table.bootstrapTable('load', []);
        divAndamento.style.display = 'none';
        divEncerrada.style.display = 'none';
        if (empty(turmaSelecionada)) {
            return;
        }

        const ano = filtroAnos.find(item => {
            return item.ano === cboAno.value;
        });

        turma = ano.turmas.find(item => {
            return item.turmaserieregimemat === turmaSelecionada;
        });

        if (turma.encerrada) {
            divEncerrada.style.display = 'block';
        } else {
            divAndamento.style.display = 'block';
        }

        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.post(
            `${PHPSession.requestApi}/educacao/escola/turmas/${turmaSelecionada}/regencias-por-usuario`,
            {body: formData}
        ).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const disciplinas = response.data;
            table.bootstrapTable('load', disciplinas);
        });
    });

    window.operateEvents = {
        'click .lancarAvaliacoes': function (e, value, row, index) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'iframe_lancamento_notas',
                `edu4_notasparciais002.php?regencia=${row.codigo_regencia}&encerrada=${turma.encerrada}&grade=003`,
                'Lançamento de Notas Parciais'
            );
        },
        'click .fechamentoAvaliacoes': function (e, value, row, index) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'iframe_fechamento_notas',
                `edu4_notasparciais002.php?regencia=${row.codigo_regencia}&encerrada=${turma.encerrada}&grade=004`,
                'Fechamento de Período'
            );
        }
    }
    var table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        search: false,
        columns: [
            {
                title: 'Disciplina',
                field: 'disciplina',
                align: 'left',
                width: 240
            },
            {
                title: 'Professores',
                field: 'professor',
                align: 'left',
                width: 240,
                formatter: (value, row) => {
                    return row.professores.join('<br>');
                }
            },
            {
                title: 'Avaliações',
                field: 'avaliacoes',
                align: 'center',
                width: 150,
                formatter: () => {
                    return `<button type="button" class="btn btn-light lancarAvaliacoes">
                                    <i class="fas fa-pencil-alt"></i>
                                    Lançar avaliações
                                </button>`;
                },
                events: window.operateEvents
            },
            {
                title: 'Fechamento',
                field: 'codigo_regencia',
                align: 'center',
                width: 150,
                events: window.operateEvents,
                formatter: () => {
                    return `<button type="button" class="btn btn-light fechamentoAvaliacoes">
                                    <i class="fas fa-pencil-ruler"></i>
                                    Fechamento
                                </button>`;
                }
            }
        ]
    });
</script>
</body>
</html>
