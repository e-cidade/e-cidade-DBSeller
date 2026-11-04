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

include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
db_postmemory($HTTP_POST_VARS);

?>

<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <?php
        db_app::load("scripts.js");
        db_app::load("estilos.css");
    ?>
    <link rel="stylesheet" type="text/css"
          href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css"/>
    <style>
        iframe {
            margin: 0;
            border-width: 0;
        }

        .select2-selection--multiple:before {
            content: "";
            position: absolute;
            right: 7px;
            top: 27%;
            border: solid black;
            border-width: 0 1.5px 1.5px 0;
            padding: 2.5px;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }

        .select2-selection--multiple {
            min-height: 0 !important;
            height: 18px;
            cursor: default !important;
            border: 1px solid #999 !important;
            border-radius: 2px !important;
        }

        .select2-search__field {
            visibility: hidden;
        }

        .select2-selection__rendered {
            height: 18px;
        }

        .select2-selection__choice {
            margin-top: 0 !important;
            display: inline;
            background-color: transparent !important;
            padding: 0 !important;
            border: none !important;
            font-weight: normal !important;
            margin-right: 34px !important;
            float: none !important;
            font-size: 10px !important;
        }

        .select2-selection__choice__remove {
            display: none !important;
        }

        .select2-results__option--highlighted[aria-selected=true]:hover {
            background-color: #5897fb !important;
        }

        .select2-results__option[aria-selected=true] {
            background-color: #A9A9A9 !important;
        }

        /* Evita o "achatamento" da option vazia */
        .select2-results__option:empty {
            min-height: 12px;
        }
    </style>
</head>
<body class="body-default">
<div class="container">
    <form id="formPadrao" method="post" action="">
        <fieldset id="fieldsetFiltros" style="width: 850px">
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td nowrap class="bold field-size5">
                        <label for="vinculo">Vínculos:</label>
                        <select id="vinculo" name="vinculo" style="font-size:9px;width:200px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="escola">Escolas:</label>
                        <select id="escola" name="escola" style="font-size:9px;width:300px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="disciplina">Disciplinas:</label>
                        <select id='disciplina' name="disciplina" style="font-size:9px;width:150px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                </tr>
            </table>
            <table class="form-container">
                <tr>
                    <td nowrap style="height:90px;">
                        <label for="etapa">Etapa:</label>
                        <select id="etapa" multiple="multiple" name="etapa" style="font-size:9px;width:80px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="dia">Dias:</label>
                        <select name="dia" id="dia" style="font-size:9px;width:80px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="turno">Turnos:</label>
                        <select id="turno" multiple="multiple" name="turno" style="font-size:9px;width:80px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="horarios">Horários:</label>
                        <select name="horarios" id="periodo" style="font-size:9px;width:80px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                    <td nowrap>
                        <label for="funcionarios">Professores:</label>
                        <select name="funcionarios" id="funcionario" style="font-size:9px;width:200px;height:18px;">
                            <option value="" disabled></option>
                        </select>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 5px">
                <button name="pesquisar" type="button"
                        id="pesquisar">
                    <i class="fas fa-search"></i>
                    Pesquisar
                </button>
            </div>
        </fieldset>
    </form>
</div>
<fieldset>
    <legend>Quadro Geral de Horários</legend>
    <iframe name="dados" id="dados" src="" width="100%" height="800"></iframe>
</fieldset>
<?php
    db_menu(
        db_getsession("DB_id_usuario"),
        db_getsession("DB_modulo"),
        db_getsession("DB_anousu"),
        db_getsession("DB_instit")
    );
?>
<script src="scripts/jquery-2.1.1.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript">

    $.noConflict();
    jQuery(document).ready(function ($) {
        require_once('scripts/widgets/DBToogle.widget.js');

        const selectEscolas = document.getElementById('escola'),
            selectDisciplinas = document.getElementById('disciplina'),
            selectEtapas = document.getElementById('etapa'),
            selectDias = document.getElementById('dia'),
            selectTurnos = document.getElementById('turno'),
            selectPeriodos = document.getElementById('periodo'),
            btnPesquisar = document.getElementById('pesquisar'),
            selectVinculos = document.getElementById('vinculo'),
            selectProfessor = document.getElementById('funcionario');

        selectEscolas.addEventListener('change', buscaDisciplinas);
        selectDisciplinas.addEventListener('change', pesquisaEtapas);
        selectDias.addEventListener('change', pesquisaTurnos);
        selectPeriodos.addEventListener('change', pesquisaFuncionario);
        btnPesquisar.addEventListener('click', pesquisaQuadro);
        selectVinculos.addEventListener('change', pesquisaFuncionario);

        /* Transforma o select multiple turno no componente Select2, inicializando o componente. */
        const select2Turnos = jQuery('#turno').select2({language: 'pt-BR', closeOnSelect: false});
        const select2Etapas = jQuery('#etapa').select2({language: 'pt-BR', closeOnSelect: false});

        select2Turnos.on('select2:close', function (e) {
            pesquisaHorarios();
            pesquisaFuncionario();
        });
        select2Etapas.on('select2:close', function (e) {
            pesquisaDiasSemana();
        });

        var toggle = new DBToogle('fieldsetFiltros', true);

        var UrlRPC = 'edu_quadrogeraldehorarios.RPC.php';

        const formData = new FormData();
        formData.append('acao', 'pesquisaEscola');
        formData.append('iEscola', selectEscolas.value)

        /* Executa ao carregar a página, busca as escolas */
        HttpClient.post(UrlRPC, {body: formData}).then((response) => {
            let oElemento = selectEscolas;

            oElemento.options.length = 0;
            oElemento.add(new Option('', ''));
            oElemento.add(new Option('TODAS', 0));

            response.dados.each(({codigo_escola, nome_escola}) => {
                oElemento.add(new Option(`${codigo_escola} - ${nome_escola}`, codigo_escola));
            });

            pesquisaTurnos();
        });

        function buscaVinculos() {
            const formData = new FormData();
            formData.append('acao', 'getVinculos');
            formData.append('iEscola', selectEscolas.value)

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                selectVinculos.options.length = 0;
                selectVinculos.add(new Option('', ''));
                selectVinculos.add(new Option('TODAS', 0));
                selectVinculos.selectedIndex = 1;
                selectVinculos.dispatchEvent(new Event('change'))

                response.dados.each(({ed128_codigo, ed128_descricao: sDescricao}) => {
                    selectVinculos.add(new Option(sDescricao, ed128_codigo));
                });
            });
        }

        buscaVinculos()

        function buscaDisciplinas(event) {
            let iEscola = event.target.value;

            if (iEscola === '') {
                selectDisciplinas.length = 0;
                selectDisciplinas.add(new Option('', ''));
                return;
            }

            const formData = new FormData();
            formData.append('acao', 'getDisciplina');
            formData.append('iCodigoEscola', iEscola)

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                let disciplinas = response.aDisciplinas;
                let oElemento = selectDisciplinas;

                oElemento.options.length = 0;
                oElemento.add(new Option('', ''));
                oElemento.add(new Option('TODAS', 0));

                disciplinas.each(function (oDadosResponse) {
                    oElemento.add(new Option(oDadosResponse.sDescricaoDisciplina, oDadosResponse.iCodigoCadDisciplina));
                });
            });

            pesquisaTurnos();
            pesquisaFuncionario();
        }

        function pesquisaEtapas() {
            let iDisciplina = selectDisciplinas.value;
            let iEscola = selectEscolas.value;
            let iAnoAtual = String((new Date).getFullYear());

            const formData = new FormData();
            formData.append('acao', 'getSerie')
            formData.append('iCodigoEscola', iEscola);
            formData.append('iCodigoDisciplina', iDisciplina);
            formData.append('iAnoAtual', iAnoAtual);

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                const oElemento = selectEtapas;
                oElemento.options.length = 0;
                if (!response.erro) {
                    let etapas = response.dados;
                    etapas.each(({ed11_c_descr, ed11_i_codigo}) => {
                        oElemento.add(new Option(ed11_c_descr, ed11_i_codigo));
                    });
                }
            });
        }

        pesquisaDiasSemana();

        function pesquisaDiasSemana() {
            let iEscola = selectEscolas.value

            const formData = new FormData();
            formData.append('acao', 'getDiasSemana');
            formData.append('iCodigoEscola', iEscola);

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                selectDias.options.length = 0;
                selectDias.add(new Option('', ''));
                selectDias.add(new Option('TODAS', 0));
                selectDias.selectedIndex = 1;
                selectDias.dispatchEvent(new Event('change'))

                response.dados.each(({codigo_dia, descricao_dia: sDescricao}) => {
                    selectDias.add(new Option(sDescricao, codigo_dia));
                });

                pesquisaFuncionario()
            })
        }

        function pesquisaTurnos() {
            let iEscola = selectEscolas.value;

            const formData = new FormData();
            formData.append('acao', 'getTurnos')
            formData.append('iCodigoEscola', iEscola);

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                const oElemento = selectTurnos;
                oElemento.options.length = 0;
                if (!response.erro) {
                    let turnos = response.dados;
                    oElemento.add(new Option('', ''));
                    oElemento.add(new Option('TODOS', 0));

                    turnos.each(({descricao_turno, id}) => {
                        oElemento.add(new Option(descricao_turno, id));
                    });
                }
            })
        }

        function pesquisaHorarios() {
            let aCodigoTurno = $('#turno').val();
            let iCodigoEscola = selectEscolas.value;

            const formData = new FormData();
            formData.append('acao', 'getHorarios');
            formData.append('iCodigoTurno', aCodigoTurno);
            formData.append('iCodigoEscola', iCodigoEscola);

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                let periodos = response.aPeriodos;
                let oElemento = selectPeriodos;

                oElemento.options.length = 0;
                oElemento.add(new Option('', ''));
                oElemento.add(new Option('TODAS', 0));

                periodos.each(function (oDadosRetorno) {
                    let sDescricao = oDadosRetorno.sDescricaoPeriodo;
                    oElemento.add(new Option(sDescricao, oDadosRetorno.iCodigoPeriodo));
                });
            })
        }

        function pesquisaFuncionario() {
            if (selectEscolas.value === '') {
                return;
            }

            let iCodigoEscola = selectEscolas.value;
            let iCodigoDisciplina = selectDisciplinas.value;
            let iCodigoEtapa = $('#etapa').val();
            let iCodigoDia = selectDias.value;
            let aCodigoTurno = $('#turno').val();
            let iCodigoPeriodo = selectPeriodos.value;

            const formData = new FormData();
            formData.append('acao', 'buscaFuncionarios');
            formData.append('iCodigoEscola', iCodigoEscola);
            formData.append('iCodigoDisciplina', iCodigoDisciplina);
            formData.append('iCodigoEtapa', iCodigoEtapa);
            formData.append('iCodigoDia', iCodigoDia);
            formData.append('iCodigoTurno', aCodigoTurno);
            formData.append('iCodigoPeriodo', iCodigoPeriodo);

            HttpClient.post(UrlRPC, {body: formData}).then((response) => {
                let oElemento = selectProfessor;

                oElemento.options.length = 0;
                oElemento.add(new Option('', ''));
                oElemento.add(new Option('TODAS', 0));

                if (selectVinculos.value === '0') {
                    oElemento.add(new Option('SEM PROFESSOR', 1));
                }

                response.dados.each(function ({ed20_i_codigo, matricula, z01_nome}) {
                    oElemento.add(new Option(`${matricula} - ${z01_nome}`, ed20_i_codigo));
                });
            })
        }

        function pesquisaQuadro() {
            let iCodigoEscola = selectEscolas.value;
            let iCodigoDisciplina = selectDisciplinas.value;
            let iCodigoEtapa = $('#etapa').val();
            let iCodigoDia = selectDias.value;
            let aCodigoTurno = $('#turno').val();
            let iCodigoPeriodo = selectPeriodos.value;
            let aCodVinculo = selectVinculos.value;
            let aCodigoFuncionario = selectProfessor.value;
            const dados = document.getElementById('dados');
            dados.src = `edu3_quadrogeraldehorarioshtml002.php?iEscola=${iCodigoEscola}&iDisciplina=${iCodigoDisciplina}&iEtapa=${iCodigoEtapa}&iDia=${iCodigoDia}&iTurno=${aCodigoTurno}&iVinculo=${aCodVinculo}&iPeriodo=${iCodigoPeriodo}&iFuncionario=${aCodigoFuncionario}`;
            toggle.show(false);
        }
    });
</script>
</body>
</html>
