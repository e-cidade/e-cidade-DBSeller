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
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body class='body-default'>
<div class='container'>
    <fieldset >
        <legend>Manutenção na grade de horário</legend>
        <table  class="form-container">
            <tr>
                <td><label><a href="#" id="ancoraTurma">Turma</a></label></td>
                <td>
                    <input type="text" id="ed57_i_codigo" readonly class="field-size2 readonly">
                    <input type="text" id="ed57_c_descr" readonly class="field-size8 readonly">
                </td>
            </tr>
            <tr>
                <td><label for="etapa">Etapa</label></td>
                <td>
                    <select id="etapa">
                        <option value="">Selecione a turma</option>
                    </select>
                </td>
            </tr>
        </table>
    </fieldset>
    <button type="button"  id="buscar">
        <i class="fas fa-search"></i>
        Buscar Grade de Horários
    </button>
</div>
<div class='container'>
    <fieldset  style="width: 1200px">
        <legend>Grade de Horários (Regencia Horário)</legend>

        <fieldset class="separator" >
            <legend>Filtros para grade</legend>
            <table>
                <tr>
                    <td><label class="bold" for="filtroDiaSemana">Dia da Semana:</label></td>
                    <td><select id="filtroDiaSemana" class="field-size3"></select></td>
                    <td><label class="bold" for="filtroDisciplina">Disciplina:</label></td>
                    <td><select id="filtroDisciplina" class="field-size3"></select></td>
                    <td><label class="bold" for="filtroRegente">Regente:</label></td>
                    <td><select id="filtroRegente" class="field-size6"></select></td>
                    <td><label class="bold" for="filtroAtivo">Status:</label></td>
                    <td>
                        <select id="filtroAtivo" class="field-size3">
                            <option value="">Todos</option>
                            <option value="true">Ativo</option>
                            <option value="false">Inativo</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <div id="ctnGrid"> </div>
    </fieldset>
    <button type="button" id="salvar">
        <i class="far fa-save"></i>
        Salvar
    </button>

    <button type="button" id="excluirSelecionados">
        <i class="far fa-trash-alt"></i>
        Excluir Selecionados
    </button>
</div>
<?php db_menu(); ?>
<script type="text/javascript">

    const btnExcluirSelecionados = document.getElementById('excluirSelecionados'),
        filtroDiaSemana = document.getElementById('filtroDiaSemana'),
        filtroDisciplina = document.getElementById('filtroDisciplina'),
        filtroAtivo = document.getElementById('filtroAtivo'),
        filtroRegente = document.getElementById('filtroRegente');

    var dadosGrid = [];
    const rpc = 'edu1_manutencao_horarios_regencia.RPC.php';
    const collection = new Collection();
    collection.setId('codigo');

    var gridHorarios = new DatagridCollection(collection).configure({
        order    : false,
        height   : 200
    });

    const resetFiltrosGrid = () => {
        filtroDiaSemana.options.length = 0;
        filtroDisciplina.options.length = 0;
        filtroRegente.options.length = 0;

        filtroDiaSemana.add(new Option('Todos', ''));
        filtroDisciplina.add(new Option('Todos', ''));
        filtroRegente.add(new Option('Todos', ''));
    };

    const atualizaFiltros = (regentes, periodos, diasSemana) => {
        resetFiltrosGrid();

        for (var regente of regentes) {
            filtroRegente.add(new Option(regente.descricao, regente.codigo));
        }
        for (var periodo of periodos) {
            filtroDisciplina.add(new Option(periodo.descricao, periodo.codigo));
        }
        for (var diaSemana of diasSemana) {
            filtroDiaSemana.add(new Option(diaSemana.descricao, diaSemana.codigo));
        }
    };

    const aplicaFiltrosGrid = () => {

        const copia = [];
        for (var item of dadosGrid) {
            copia.push(item.build());
        }
        const dados = copia.filter((item) => {
            var flag = true;
            if (!empty(filtroDiaSemana.value) && filtroDiaSemana.value != item.idiasemana) {
                flag = false;
            }
            if (!empty(filtroRegente.value) && filtroRegente.value != item.rechumano) {
                flag = false;
            }
            if (!empty(filtroDisciplina.value) && filtroDisciplina.value != item.disciplina) {
                flag = false;
            }
            if (!empty(filtroAtivo.value) && filtroAtivo.value == 'true' && !item.ativo) {
                flag = false;
            }
            if (!empty(filtroAtivo.value) && filtroAtivo.value == 'false' && item.ativo) {
                flag = false;
            }
           return flag;
        });

        atualizaGrid(dados);
    };

    filtroDiaSemana.addEventListener('change', aplicaFiltrosGrid);
    filtroDisciplina.addEventListener('change', aplicaFiltrosGrid);
    filtroRegente.addEventListener('change', aplicaFiltrosGrid);
    filtroAtivo.addEventListener('change', aplicaFiltrosGrid);


    const criarFiltrosGrid = (grade) => {
        const regentes = [], disciplina = [], diasSemana = [], regentesAdd = [], disciplinaAdd = [], diasSemanaAdd = [];

        for (var itemGrid of grade) {
            if (!regentesAdd.in_array(itemGrid.rechumano)) {
                regentesAdd.push(itemGrid.rechumano);
                regentes.push({"codigo": itemGrid.rechumano, "descricao": itemGrid.regente});
            }

            if (!diasSemanaAdd.in_array(itemGrid.idiasemana)) {
                diasSemanaAdd.push(itemGrid.idiasemana);
                diasSemana.push({"codigo": itemGrid.idiasemana, "descricao": itemGrid.dia_semana});
            }

            if (!disciplinaAdd.in_array(itemGrid.disciplina)) {
                disciplinaAdd.push(itemGrid.disciplina);
                disciplina.push({"codigo": itemGrid.disciplina, "descricao": itemGrid.disciplina});
            }
        }
        atualizaFiltros(regentes, disciplina, diasSemana);
    };

    const buscarHorariosRegentes = () => {

        gridHorarios.clear();
        const formData = new FormData();
        formData.append('turma', $F('ed57_i_codigo'));
        formData.append('etapa', $F('etapa'));
        formData.append('acao', 'buscarHorariosRegentes');
        HttpClient.post(rpc, {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return false;
            }

            if (response.grade.length === 0) {
                alert('Turma sem grade de horário configurada.');
                return ;
            }

            dadosGrid = response.grade;
            criarFiltrosGrid(dadosGrid);
            atualizaGrid(response.grade);
        });
    };

    const atualizaGrid = dados => {
        gridHorarios.clear();
        collection.add(dados);
        gridHorarios.reload();
    }

    const cboEtapas = $('etapa');
    const resetSelectEtapa = () => {
        cboEtapas.options.length = 0;
        cboEtapas.add(new Option('Selecione a turma', ''));
    };

    const buscaEtapasTurma = () => {
        const formData = new FormData();
        formData.append('turma', $F('ed57_i_codigo'));
        formData.append('acao', 'buscarEtapas');

        HttpClient.post(rpc, {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return false;
            }

            for (const etapa of response.etapas) {
                cboEtapas.add(new Option(etapa.nome, etapa.codigo));
            }

            if (response.etapas.length === 1) {
                cboEtapas.value = response.etapas[0].codigo;
                buscarHorariosRegentes();
            }
        });
    };

    const buscarEtapas = () => {
        resetSelectEtapa();
        if ($F('ed57_i_codigo') === '') {
            return;
        }

        buscaEtapasTurma();
    };

    const lookUpTurma =  new DBLookUp($('ancoraTurma'), $('ed57_i_codigo'), $('ed57_c_descr'), {
        "sArquivo"      : "func_turma.php",
        "sObjetoLookUp" : "db_iframe_turma",
        "sLabel"        : "Pesquisar Turma"
    });
    lookUpTurma.setCallBack('onClick', buscarEtapas);
    lookUpTurma.setCallBack('onChange', buscarEtapas);

    const changeValueSelectAtivo = () => {
        const selects = document.getElementsByClassName('cbo_ativo');
        for (const select of selects) {
            const idCollection = select.getAttribute('id-collection');
            const itemCollection = collection.get(idCollection);
            select.addEventListener('change', (e) => {
                itemCollection.ativo = e.target.value === 'true';
            });

            if (itemCollection.ativo) {
                select.options[0].selected = true;
            } else {
                select.options[1].selected = true;
            }
        }
    };

    const changeValueInputData = () => {
        const inputs = document.getElementsByClassName('data_inicio');

        for (const inputDataInicio of inputs) {
            const idCollection = inputDataInicio.getAttribute('id-collection');
            const itemCollection = collection.get(idCollection);
            const inputDate = new DBInputDate(inputDataInicio);
            inputDate.setValue(itemCollection.data_inicio);
            inputDate.getElements().inputText.addEventListener('blur', (e) => {
                itemCollection.data_inicio = js_formatar(e.target.value, 'd');
            });
        }

        const inputsDataFim = document.getElementsByClassName('data_fim');

        for (const inputDataFim of inputsDataFim) {
            const idCollection = inputDataFim.getAttribute('id-collection');
            const itemCollection = collection.get(idCollection);
            const inputDate = new DBInputDate(inputDataFim);
            inputDate.setValue(itemCollection.data_fim);
            inputDate.getElements().inputText.addEventListener('blur', (e) => {
                itemCollection.data_fim = js_formatar(e.target.value, 'd');
            });
        }

    };

    const criaElementoSelect = (itemCollection) => {
        const div = document.createElement('div');

        const elemento = document.createElement('select');
        elemento.add(new Option('Ativo', 'true'));
        elemento.add(new Option('Inativo', 'false'));
        elemento.setAttribute('id', 'ativo' + itemCollection.codigo);
        elemento.setAttribute('id-collection', itemCollection.codigo);
        elemento.addClassName('cbo_ativo');
        elemento.addClassName('field-size-2');

        div.appendChild(elemento);
        return div;
    };

    const criaElementoData = (itemCollection, tipo) => {
        const div = document.createElement('div');
        const input = document.createElement('input');
        input.type = 'text';

        input.setAttribute('id', tipo + itemCollection.codigo);
        input.setAttribute('id-collection', itemCollection.codigo);
        input.addClassName(tipo);
        input.addClassName('field-size-2');

        div.appendChild(input);
        return div;
    };

    gridHorarios.getGrid().setCheckbox(0);
    gridHorarios.addColumn('dia_semana', {'label': 'Dia', 'width': '10%', 'align' : 'left'});
    gridHorarios.addColumn('periodo', {'label': 'Período', 'width': '10%', 'align' : 'left'});
    gridHorarios.addColumn('disciplina', {'label': 'Disciplina', 'width': '15%', 'align' : 'left'});
    gridHorarios.addColumn('regente', {'label': 'Regente', 'width': '25%', 'align' : 'left'}).transformCallback = (valor, itemCollection) => {

        var tipo_vinculo = 'Horários de Regências';
        if (itemCollection.tipo_vinculo == 1) {
            tipo_vinculo = 'Vínculos Regente / Disciplina na Turma';
        }

        return '<label title="'+ tipo_vinculo +'">' + valor + '</label>';
    };
    gridHorarios.addColumn('ativo', {'label': 'Status', 'width': '10%', 'align' : 'left'}).transformCallback = function(valor, itemCollection ) {
        const cbo = criaElementoSelect(itemCollection);
        return cbo.outerHTML;
    };

    gridHorarios.addColumn('data_inicio', {'label': 'Data Inicio', 'width': '10%', 'align' : 'left'}).transformCallback = function( valor, itemCollection ) {
        const input = criaElementoData(itemCollection, 'data_inicio');
        return input.outerHTML;
    };

    gridHorarios.addColumn('data_fim', {'label': 'Data Fim', 'width': '10%', 'align' : 'left'}).transformCallback = function( valor, itemCollection ) {
        const input = criaElementoData(itemCollection, 'data_fim');
        return input.outerHTML;
    };

    gridHorarios.addAction('Excluir', 'Excluir', function(event, itemCollection) {

        var msgConfirm = 'Regente tem frequência lançada no sistema.\nUltimo registro de frequência lançado foi no dia ';
        msgConfirm += js_formatar(itemCollection.data_lancamento_frequencia, 'd');
        msgConfirm += '\nAo excluir, os lançamentos de frequência serão excluidos.\nDeseja Realmente excluir?';

        if (itemCollection.lancou_frequencia && !confirm(msgConfirm)) {
            return;
        }

        msgConfirm = 'Deseja realmente excluir esse período da grade de horários.';
        if (!itemCollection.lancou_frequencia && !confirm(msgConfirm)) {
            return;
        }

        const horarios = [{
            'codigo': itemCollection.codigo,
            'rechumano': itemCollection.rechumano
        }];

        excluirRegistro(horarios);
    }, true, 'fa-trash-alt');

    gridHorarios.show($('ctnGrid'));

    gridHorarios.setEvent('onafterrenderrows', function() {
        changeValueSelectAtivo();
        changeValueInputData();
    });

    document.getElementById('salvar').addEventListener('click', function () {
        const formData = new FormData();
        const gradeHorarios = collection.build();

        formData.append('gradeHorarios', JSON.stringify(gradeHorarios));
        formData.append('acao', 'atualizaGrade');
        HttpClient.post(rpc, {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return false;
            }

            buscarHorariosRegentes();
        });
    });

    document.getElementById('buscar').addEventListener('click', function () {
        buscarHorariosRegentes();
    });


    btnExcluirSelecionados.addEventListener('click', () => {
        const selecionados = [];
        const linhasGrid = gridHorarios.getGrid().aRows;
        var lancouFrequencia = false;
        for (var linha of linhasGrid) {
            if (linha.isSelected) {
                selecionados.push({
                    'codigo': linha.itemCollection.codigo,
                    'rechumano': linha.itemCollection.rechumano
                });

                if (linha.itemCollection.lancou_frequencia) {
                    lancouFrequencia = true;
                }
            }
        }

        var msgConfirm = 'Regente(s) tem frequência lançada no sistema em um ou mais período(s) selecionado(s).';
        msgConfirm += '\nAo excluir, os lançamentos de frequência serão excluidos.\nDeseja Realmente excluir?';
        if (lancouFrequencia && !confirm(msgConfirm)) {
            return;
        }

        msgConfirm = 'Deseja realmente excluir esse período da grade de horários.';
        if (!lancouFrequencia && !confirm(msgConfirm)) {
            return;
        }

        excluirRegistro(selecionados);
    });

    const excluirRegistro = (horarios) => {
        const formData = new FormData();
        formData.append('turma', $F('ed57_i_codigo'));
        formData.append('etapa', cboEtapas.value);
        formData.append('horarios', JSON.stringify(horarios));
        formData.append('acao', 'remover');

        HttpClient.post(rpc, {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return false;
            }

            buscarHorariosRegentes();
        });
    }

</script>
