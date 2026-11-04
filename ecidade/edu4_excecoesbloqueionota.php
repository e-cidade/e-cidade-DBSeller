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

use App\Domain\Educacao\Escola\Models\Escola;

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
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css" />
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            z-index: 999999999;
        }
    </style>
</head>
<body>
<div class="container">
    <fieldset>
        <legend>Liberar lançamento de Notas - Exceções</legend>
        <p>
            Lista de turmas com notas abertas para lançamento mesmo após fechamento do período.
        </p>
        <div style="width: 800px; clear: both; margin-top: 4px;">
            <table id="listagemExcecoes" class="table table-sm" data-height="300" style="width: 100%;">
            </table>
        </div>
    </fieldset>
</div>

<div id="modalAdicionarExcecao">
    <div class="container">
        <fieldset>
            <legend>Liberar lançamento de nota</legend>
            <table class="form-container">
                <tr>
                    <td><label for="escola">Escola: </label></td>
                    <td>
                        <?php
                        $escolas = Escola::all();
                        ?>
                        <select name="escola" id="escola">
                            <option value="">Selecione</option>
                            <?php
                            $escolas->each(function($escola) {
                                ?>
                                <option value="<?= $escola->getCodigo() ?>"><?= $escola->getCodigo() ?> - <?= $escola->getNome() ?></option>
                                <?php
                            });
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="calendario">Calendário: </label></td>
                    <td>
                        <select name="calendario" id="calendario">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="turma">Turma: </label></td>
                    <td>
                        <select name="turma" id="turma">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="regencia">Disciplina: </label></td>
                    <td>
                        <select name="regencia" id="regencia">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="periodo">Período: </label></td>
                    <td>
                        <select name="periodo" id="periodo">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="data_limite">Data Limite: </label></td>
                    <td><input type="text" name="data_limite" id="data_limite"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" class="btn btn-light" id="btnAdicionar">
                            <i class="fas fa-save"></i>
                            Adicionar
                        </button>
                    </td>
                </tr>
            </table>
        </fieldset>
    </div>
</div>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    $.noConflict();
    jQuery(document).ready(function ($) {
        let urlApi = "";
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
            buscarExcecoes();
        });

        const cboCalendario = document.getElementById('calendario');
        const cboTurma = document.getElementById('turma');
        const cboRegencia = document.getElementById('regencia');
        const cboPeriodo = document.getElementById('periodo');
        const modalAdicionarExcecao = document.getElementById('modalAdicionarExcecao');
        const inputDataLimite = new DBInputDate(document.getElementById('data_limite'));
        const btnAdicionar = document.getElementById('btnAdicionar');

        const select2Escola = jQuery('#escola').select2({language: 'pt-BR'});
        select2Escola.on('select2:close', function (e) {
            buscarCalendarios();
        });

        const buscarCalendarios = () => {
            if (empty(escola.value)) {
                cboCalendario.length = 1;
                cboCalendario.dispatchEvent(new Event('change'));
                return;
            }
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            HttpClient.get(`${urlApi}/educacao/escola/calendario/${escola.value}`, {body: formData})
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    cboCalendario.length = 1;
                    response.data.forEach((calendario) => {
                        console.log(calendario);
                        cboCalendario.options.add(new Option(calendario.nome, calendario.codigo));
                    });
                });
        }

        cboCalendario.addEventListener('change', () => {
            if (empty(cboCalendario.value)) {
                cboTurma.length = 1;
                cboTurma.dispatchEvent(new Event('change'));
                cboPeriodo.length = 1;
                cboPeriodo.dispatchEvent(new Event('change'));
                return;
            }
            HttpClient.get(`${urlApi}/educacao/escola/turmas?calendario=${cboCalendario.value}`)
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    cboTurma.length = 1;
                    const turmas = response.data.sort((a, b) => {
                        if (a.etapaDescricao < b.etapaDescricao) {
                            return -1;
                        }
                        if (a.etapaDescricao > b.etapaDescricao) {
                            return 1;
                        }
                        return 0;
                    })
                    turmas.forEach((item) => {
                        cboTurma.options.add(new Option(item.etapaDescricao + ' - ' + item.turmaDescricao, item.turmaEtapaCodigo));
                    });
                });

            HttpClient.get(`${urlApi}/educacao/escola/periodos-calendario/${cboCalendario.value}`)
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    cboPeriodo.length = 1;
                    response.data.forEach((item) => {
                        cboPeriodo.options.add(new Option(item.periodo_avaliacao.ed09_c_descr, item.ed53_i_codigo));
                    });
                });
        });

        cboTurma.addEventListener('change', () => {
            if (empty(cboTurma.value)) {
                cboRegencia.length = 0;
                cboRegencia.options.add(new Option('Selecione', ''));
                return;
            }
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            HttpClient.post(`${urlApi}/educacao/escola/regencia/turma/${cboTurma.value}`, {body: formData})
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    cboRegencia.length = 0;
                    cboRegencia.options.add(new Option('TODAS', ''));
                    response.data.forEach((item) => {
                        cboRegencia.options.add(new Option(item.disciplina_ensino.disciplina.ed232_c_descr, item.ed59_i_codigo));
                    });
                });
        });

        btnAdicionar.addEventListener('click', () => {
            if (empty(cboTurma.value)) {
                alert('Selecione uma turma');
                return;
            }
            if (empty(cboPeriodo.value)) {
                alert('Selecione um período');
                return;
            }
            if (empty(inputDataLimite.__toLocaleDateString())) {
                alert('Informe a data limite');
                return;
            }
            const formData = new FormData();
            console.log(formData);
            formData.append('turma', cboTurma.value);
            formData.append('regencia', cboRegencia.value);
            formData.append('periodocalendario', cboPeriodo.value);
            formData.append('datalimite', inputDataLimite.__toLocaleDateString());
            PHPSession.appendFormData(formData);
            HttpClient.post(`${urlApi}/educacao/secretaria/bloqueio-notas/salvar-excecao`, {body: formData})
                .then((response) => {
                    alert(response.message);
                    if (response.error) {
                        return;
                    }
                    fechaModal(windowAdicionarExcecao);
                    buscarExcecoes();
                });
        });

        window.events = {
            'click .encerrarLiberacao': function (e, value, row, index) {
                if (confirm('Deseja mesmo encerrar a exceção?')) {
                    const formData = new FormData();
                    PHPSession.appendFormData(formData);
                    HttpClient.post(`${urlApi}/educacao/secretaria/bloqueio-notas/encerrar-excecao/${row.ed363_codigo}`, {body: formData})
                        .then((response) => {
                            alert(response.message);
                            if (response.error) {
                                return;
                            }
                            buscarExcecoes();
                        });
                }
            },
        }

        const buttons = () => {
            return {
                btnAdd: {
                    text: 'Adicionar turma',
                    icon: 'fa-plus',
                    event: function () {
                        windowAdicionarExcecao.show(0, 0, true);
                    },
                    attributes: {
                        title: 'Adicionar exceção para uma turma'
                    },
                }
            }
        }
        var tabelaExcecoes = jQuery('#listagemExcecoes');
        tabelaExcecoes.bootstrapTable({
            locale: 'pt-BR',
            class: "table table-sm",
            buttons: buttons,
            showButtonText: true,
            search: true,
            columns: [
                {
                    title: 'Escola',
                    field: 'escola',
                    align: 'left'
                },
                {
                    title: 'Turma',
                    field: 'turma',
                    align: 'left'
                },
                {
                    title: 'Disciplina',
                    field: 'disciplina',
                    align: 'left'
                },
                {
                    title: 'Período',
                    field: 'periodo',
                    align: 'left'
                },
                {
                    title: 'Data Limite',
                    field: 'data_limite',
                    align: 'left'
                },
                {
                    title: 'Ações',
                    field: 'acoes',
                    align: 'left',
                    width: 100,
                    formatter: () => {
                        return `<a class="encerrarLiberacao"><i class="fas fa-trash"></i> Encerrar</a>`;
                    },
                    events: window.events,
                }
            ],
        });

        const buscarExcecoes = () => {
            const formData = new FormData();
            PHPSession.appendFormData(formData);
            HttpClient.get(`${urlApi}/educacao/secretaria/bloqueio-notas/listagem-excecoes`, {body: formData})
                .then((response) => {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }
                    tabelaExcecoes.bootstrapTable('load', response.data);
                });
        }

        const fechaModal = (window) => {
            select2Escola.val(null).trigger('change');
            inputDataLimite.value = null;
            buscarCalendarios();

            if (!!window.oDBMask) {
                window.oDBMask.destroy();
            }
            window.hide();
        }

        var windowAdicionarExcecao = new windowAux('windowAdicionarExcecao', 'Adicionar exceção para uma turma', 800, 600);
        windowAdicionarExcecao.setContent(modalAdicionarExcecao);
        windowAdicionarExcecao.setShutDownFunction(() => {
            fechaModal(windowAdicionarExcecao)
        });

        new DBMessageBoard('msgBoardTurmas',
            'Adicionar exceção de bloqueio de notas para uma turma',
            "\nAdicione as turmas que podem lançar notas mesmo após o termino do período de lançamento",
            windowAdicionarExcecao.getContentContainer()
        );
    });
</script>
</body>
</html>
