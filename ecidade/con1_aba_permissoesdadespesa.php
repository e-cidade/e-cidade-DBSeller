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
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.css">
    <link rel="stylesheet" type="text/css"
          href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css"/>
    <style>
        .select2-search--dropdown .select2-search__field {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 18px !important;
        }

        .select2-selection--single {
            height: 19px !important;
            cursor: default !important;
            border: 1px solid #999 !important;
            border-radius: 2px !important;
        }

        .select2-selection__rendered {
            line-height: 17px !important;
        }

        /* Evita o "achatamento" da option vazia */
        .select2-results__option:empty {
            min-height: 12px;
        }
    </style>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Manutenção de Permissões da Despesa</legend>
        <input type="hidden" id="codigoPermissao" value="">
        <table class="form-container" style="border-collapse: separate;">
            <tr>
                <td><label for="id_usuario" id="ancora_usuario">Usuário: </label></td>
                <td>
                    <input type="text" id="id_usuario" value="<?= isset($id_usuario) ? $id_usuario : '' ?>"/>
                    <input type="text" id="nome" value="<?= isset($nome) ? $nome : '' ?>"/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: right">
                    <button type="button" class="btn btn-light" id="btnIncluirTodos">
                        <i class="fas fa-plus"></i>
                        Incluir Todos
                    </button>
                    <button type="button" class="btn btn-light" id="btnExcluirTodos">
                        <i class="fas fa-trash"></i>
                        Excluir Todos
                    </button>
                </td>
            </tr>
            <tr>
                <td><label for="orgao">Orgão:</label></td>
                <td>
                    <select id="orgao" name="orgao">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="unidade">Unidade:</label></td>
                <td>
                    <select id="unidade" name="unidade">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="funcao">Função:</label></td>
                <td>
                    <select id="funcao" name="funcao">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="subfuncao">Subfunção:</label></td>
                <td>
                    <select id="subfuncao" name="subfuncao">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="programa">Programa:</label></td>
                <td>
                    <select id="programa" name="programa">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="projetoAtividade">Projeto/Atividade:</label></td>
                <td>
                    <select id="projetoAtividade" name="projetoAtividade">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="elemento">Elemento:</label></td>
                <td>
                    <select id="elemento" name="elemento">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="recurso">Recurso:</label></td>
                <td>
                    <select id="recurso" name="recurso">
                        <option value="0">0 - Todos...</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="tipoPermissao">Tipo de Permissão:</label></td>
                <td>
                    <select id="tipoPermissao" name="tipoPermissao">
                        <option value="M">Manutenção</option>
                        <option value="C">Consulta</option>
                    </select>
                </td>
            </tr>

        </table>
    </fieldset>
    <button type="button" class="btn btn-light" id="btnIncluir">
        <i class="fas fa-plus"></i>
        Incluir
    </button>
    <button type="button" class="btn btn-light" id="btnCancelar">
        <i class="fas fa-ban"></i>
        Cancelar
    </button>
</div>
<div class="container">
    <fieldset>
        <legend>Permissões Cadastradas</legend>
        <div style="width: 1000px">
            <table id="data-table"
                   class="table table-sm"
                   data-height="500"
                   data-virtual-scroll="true"
                   style="width: 100%;">
            </table>
        </div>
    </fieldset>
</div>
<div id="modalPermissoes" class="container">
    <fieldset>
        <legend>Tipos de Processo: </legend>
        <label for="tipoProcesso"></label>
        <select name="tipoProcesso" id="tipoProcesso">
            <option value="">Selecione</option>
        <?php
        $tiposProcessos = \App\Domain\Patrimonial\Protocolo\Model\TipoProcesso::has('atividades')->get();
        foreach ($tiposProcessos as $tipoProcesso) {
            ?>
            <option value="<?=$tipoProcesso->p51_codigo?>"><?=$tipoProcesso->p51_descr?></option>
            <?php
        }
        ?>
        </select>
    </fieldset>
    <fieldset>
        <legend>Atividades: </legend>
        <?php
        $atividadesExecucao = \App\Domain\Patrimonial\Protocolo\Model\AtividadeExecucao::all();
        foreach ($atividadesExecucao as $atividadeExecucao) {
            ?>
            <label>
                <input type="checkbox" name="atividades" value="<?=$atividadeExecucao->p114_codigo?>">
                <?=$atividadeExecucao->p114_atividade?>&nbsp;&nbsp;
            </label>
            <?php
        }
        ?>
    </fieldset>
    <button class="btn btn-light" id="btnSalvarAtividades" style="margin-top: 10px">
        <i class="fas fa-save" aria-hidden="true"></i>
        Salvar
    </button>
</div>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/jquery-tablednd/jquery.tablednd.js"></script>
<script src="assets/bootstrap-table/extensions/reorder-rows/bootstrap-table-reorder-rows.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
<script type="text/javascript">
    const usuarioAncora = document.getElementById("ancora_usuario");
    const usuarioCodigo = document.getElementById("id_usuario");
    const usuarioDescricao = document.getElementById("nome");
    const selectOrgao = document.getElementById("orgao");
    const selectUnidade = document.getElementById("unidade");
    const selectFuncao = document.getElementById("funcao");
    const selectSubfuncao = document.getElementById("subfuncao");
    const selectPrograma = document.getElementById("programa");
    const selectProjetoAtividade = document.getElementById("projetoAtividade");
    const selectElemento = document.getElementById("elemento");
    const selectRecurso = document.getElementById("recurso");
    const selectTipoPermissao = document.getElementById("tipoPermissao");
    const inputCodigoPermissao = document.getElementById("codigoPermissao");
    const txtIncluir = document.getElementById("txtIncluir");
    const selectTiposProcessos = document.getElementById("tipoProcesso");

    const btnIncluir = document.getElementById("btnIncluir");
    const btnCancelar = document.getElementById("btnCancelar");
    const btnIncluirTodos = document.getElementById("btnIncluirTodos");
    const btnExcluirTodos = document.getElementById("btnExcluirTodos");
    const btnSalvarAtividades = document.getElementById("btnSalvarAtividades");

    const modalPermissoes = document.getElementById("modalPermissoes");
    const hideWindowPermissoes = () => {
        if (!!windowPermissoes.oDBMask) {
            windowPermissoes.oDBMask.destroy();
        }
        limparCheckboxs();
        buscarPermissoes();
        inputCodigoPermissao.value = '';
        selectTiposProcessos.value = '';
        windowPermissoes.hide();
    }
    var windowPermissoes = new windowAux('windowPermissoes', 'Configurar permissões do usuário', 600, 300);
    windowPermissoes.setContent(modalPermissoes);
    windowPermissoes.allowCloseWithEsc(true);
    windowPermissoes.setShutDownFunction(function () {
        hideWindowPermissoes();
    });

    const usuarioLookup = new DBLookUp(usuarioAncora, usuarioCodigo, usuarioDescricao);
    usuarioLookup.desabilitar();

    const select2Orgao = $("#orgao").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Unidade = $("#unidade").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Funcao = $("#funcao").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Subfuncao = $("#subfuncao").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Programa = $("#programa").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2ProjetoAtividade = $("#projetoAtividade").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Elemento = $("#elemento").select2({minimumResultsForSearch: 3, language: 'pt-BR'});
    const select2Recurso = $("#recurso").select2({minimumResultsForSearch: 3, language: 'pt-BR'});

    var desabilidarTriggerChange = false;

    select2Orgao.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarUnidades');
        formData.append('orgao', selectOrgao.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectUnidade, response.unidades);
            select2Unidade.val(0);
            select2Unidade.trigger('change');
        });
    });

    select2Unidade.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarFuncoes');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectFuncao, response.funcoes);
            select2Funcao.val(0);
            select2Funcao.trigger('change');
        });
    });

    select2Funcao.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarSubfuncoes');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectSubfuncao, response.subfuncoes);
            select2Subfuncao.val(0);
            select2Subfuncao.trigger('change');
        });
    });

    select2Subfuncao.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarProgramas');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        formData.append('subfuncao', selectSubfuncao.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectPrograma, response.programas);
            select2Programa.val(0);
            select2Programa.trigger('change');
        });
    });

    select2Programa.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarProjetoAtividade');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        formData.append('subfuncao', selectSubfuncao.value);
        formData.append('programa', selectPrograma.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectProjetoAtividade, response.projetoAtividade);
            select2ProjetoAtividade.val(0);
            select2ProjetoAtividade.trigger('change');
        });
    });

    select2ProjetoAtividade.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarElementos');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        formData.append('subfuncao', selectSubfuncao.value);
        formData.append('programa', selectPrograma.value);
        formData.append('projetoAtividade', selectProjetoAtividade.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2Elementos(selectElemento, response.elementos);
            select2Elemento.val(0);
            select2Elemento.trigger('change');
        });
    });

    select2Elemento.on('change', (e) => {
        if (desabilidarTriggerChange) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarRecursos');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        formData.append('subfuncao', selectSubfuncao.value);
        formData.append('programa', selectPrograma.value);
        formData.append('projetoAtividade', selectProjetoAtividade.value);
        formData.append('elemento', selectElemento.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            montarSelect2(selectRecurso, response.recursos);
        });
    });

    var table = jQuery('#data-table');

    const callbackAcoes = (value, row) => {
        let btnPermissoes = `<a href="#" class="gerenciarPermissoes"><i class="fas fa-cogs"></i></a>&nbsp;&nbsp;&nbsp;`;
        let btnAlterar = `<a href="#" class="alterar"><i class="fas fa-edit"></i></i></a>&nbsp;&nbsp;&nbsp;`;
        let btnExcluir = `<a href="#" class=" excluir"><i class="fas fa-trash"></i></a>`;

        return `${btnPermissoes} ${btnAlterar} ${btnExcluir}`;
    }

    const cboAtividades = document.querySelectorAll('input[name=atividades]');

    const limparCheckboxs = () => {
        [...cboAtividades].map(check => check.checked = false);
    }

    window.operateEvents = {
        'click .excluir': function (e, value, row, index) {
            if (!confirm('Deseja excluir permissão?')) {
                return;
            }
            const formData = new FormData();
            formData.append('acao', 'excluirPermissaoUsuario');
            formData.append('codigoPermissao', row.db20_codperm);
            formData.append('codigoUsuario', usuarioCodigo.value);
            HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
                alert(response.mensagem);
                buscarPermissoes();
                select2Orgao.val(0);
                select2Orgao.trigger('change');
            });
        },
        'click .alterar': function (e, value, row, index) {
            desabilidarTriggerChange = true;
            inputCodigoPermissao.value = row.db20_codperm;
            select2Orgao.val(row.db20_orgao);
            select2Orgao.trigger('change');
            select2Unidade.val(row.db20_unidade);
            select2Unidade.trigger('change');
            select2Funcao.val(row.db20_funcao);
            select2Funcao.trigger('change');
            select2Subfuncao.val(row.db20_subfuncao);
            select2Subfuncao.trigger('change');
            select2Programa.val(row.db20_programa);
            select2Programa.trigger('change');
            select2ProjetoAtividade.val(row.db20_projativ);
            select2ProjetoAtividade.trigger('change');
            select2Elemento.val(row.db20_codele);
            select2Elemento.trigger('change');
            if (row.db20_tipoperm == "CONSULTA") {
                selectTipoPermissao.value = 'C';
            } else {
                selectTipoPermissao.value = 'M';
            }

            desabilidarTriggerChange = false;
            mudarBotaoIncluir('Alterar');
        },
        'click .gerenciarPermissoes':  function (e, value, row, index) {
            inputCodigoPermissao.value = row.db20_codperm;
            windowPermissoes.show(0, 0, true);
        }
    }

    const formatarLinhaTipoProcesso = (dadosLinha) => {
        return dadosLinha.atividades.map((atividade) => {
            return [
                {
                    label: "Código:",
                    valor: `${atividade.p114_codigo}`
                },
                {
                    label: "Atividade:",
                    valor: `${atividade.p114_atividade}`
                },
            ];
        });
    };

    const detailFormatter = (index, row) => {
        let detalhes = [];
        row.tipos_processo.map((tipoProcesso) => {
            let dados = formatarLinhaTipoProcesso(tipoProcesso);
            detalhes.push(detailFormaterTable.createDetail(dados, `Tipo de Processo: ${tipoProcesso.descricao}`));
        });
        return detalhes.join('');
    }

    const titleDescricao = (value, descricao) => {
        return `<span title="${descricao}">${value}</span>`;
    }

    table.bootstrapTable({
        data: [],
        locale: 'pt-BR',
        class: "table table-sm",
        search: true,
        detailView: true,
        detailFormatter: detailFormatter,
        columns: [
            {
                title: 'Exercício',
                field: 'db20_anousu',
                align: 'left',
            }, {
                title: 'Orgão',
                field: 'db20_orgao',
                align: 'left',
                formatter: (value, row) => titleDescricao(value, row.descricao_orgao)
            }, {
                title: 'Unidade',
                field: 'db20_unidade',
                align: 'left',
            }, {
                title: 'Função',
                field: 'db20_funcao',
                align: 'left'
            }, {
                title: 'Subfunção',
                field: 'db20_subfuncao',
                align: 'left',
            }, {
                title: 'Programa',
                field: 'db20_programa',
                align: 'left',
            }, {
                title: 'Projeto/Atividade',
                field: 'db20_projativ',
                align: 'left',
            }, {
                title: 'Elemento',
                field: 'db20_codele',
                align: 'left',
            }, {
                title: 'Recurso',
                field: 'db20_codigo',
                align: 'left',
            }, {
                title: 'Especificação',
                field: 'o15_loaespecificacao',
                align: 'left',
            }, {
                title: 'Tipo de Permissão',
                field: 'db20_tipoperm',
                align: 'left',
            }, {
                field: 'acoes',
                title: 'Ações',
                align: 'center',
                events: window.operateEvents,
                formatter: callbackAcoes
            }
        ]
    });

    const montarSelect2 = (elementoSelect, dados) => {
        elementoSelect.options.length = 1;

        dados.map((dado) => {
            elementoSelect.options.add(new Option(`${dado.codigo} - ${dado.descricao}`, dado.codigo));
        });
    }

    const montarSelect2Elementos = (elementoSelect, dados) => {
        elementoSelect.options.length = 1;

        dados.map((dado) => {
            elementoSelect.options.add(new Option(`${dado.codigo} - ${dado.descricao}`, dado.o56_codele));
        });
    }

    const mudarBotaoIncluir = (texto) => {
        if (texto == "Incluir") {
            btnIncluir.innerHTML = '<i class="fas fa-plus"></i>Incluir';
        }
        if (texto == "Alterar") {
            btnIncluir.innerHTML = '<i class="fas fa-save"></i>Alterar';
        }
    }

    const buscarClassificacaoProgramatica = () => {
        js_divCarregando('Carregando...', 'programatica');
        const formData = new FormData();
        formData.append('acao', 'buscarClassificacaoProgramatica');
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            const dados = response.data;
            montarSelect2(selectOrgao, dados.orgaos);
            montarSelect2(selectFuncao, dados.funcoes);
            montarSelect2(selectSubfuncao, dados.subfuncoes);
            montarSelect2(selectPrograma, dados.programas);
            montarSelect2(selectProjetoAtividade, dados.projetoAtividade);
            montarSelect2Elementos(selectElemento, dados.elementos);
            montarSelect2(selectRecurso, dados.recursos);
            js_removeObj('programatica');
        });
    }
    buscarClassificacaoProgramatica();

    const buscarPermissoes = () => {
        const formData = new FormData();
        formData.append('acao', 'buscarPermissoesPorUsuario');
        formData.append('id_usuario', usuarioCodigo.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            table.bootstrapTable('load', response.permissoes);
        });
    }
    buscarPermissoes();

    const getAtividadesSelecionadas = () => {
        let atividadesSelecionadas = [...cboAtividades].filter(cbo => cbo.checked);
        return atividadesSelecionadas.map(element => element.value)
    }

    btnIncluir.addEventListener('click', (e) => {
        const formData = new FormData();
        formData.append('acao', 'salvarPermissaoUsuario');
        formData.append('orgao', selectOrgao.value);
        formData.append('unidade', selectUnidade.value);
        formData.append('funcao', selectFuncao.value);
        formData.append('subfuncao', selectSubfuncao.value);
        formData.append('programa', selectPrograma.value);
        formData.append('projetoAtividade', selectProjetoAtividade.value);
        formData.append('elemento', selectElemento.value);
        formData.append('recurso', selectRecurso.value);
        formData.append('tipoPermissao', selectTipoPermissao.value);
        formData.append('codigoPermissao', inputCodigoPermissao.value);
        formData.append('codigoUsuario', usuarioCodigo.value);
        formData.append('atividades', getAtividadesSelecionadas());
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }
            buscarPermissoes();
            select2Orgao.val(0);
            select2Orgao.trigger('change');
            inputCodigoPermissao.value = "";
            mudarBotaoIncluir('Incluir');
            limparCheckboxs();
        });
    });

    btnIncluirTodos.addEventListener('click', (e) => {
        const formData = new FormData();
        formData.append('acao', 'incluirTodasPermissoes');
        formData.append('codigoUsuario', usuarioCodigo.value);
        formData.append('tipoPermissao', selectTipoPermissao.value);
        formData.append('atividades', getAtividadesSelecionadas());
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }
            buscarPermissoes();
            select2Orgao.val(0);
            select2Orgao.trigger('change');
            inputCodigoPermissao.value = "";
            mudarBotaoIncluir('Incluir');
            limparCheckboxs();
        });
    });

    btnExcluirTodos.addEventListener('click', (e) => {
        const formData = new FormData();
        formData.append('acao', 'excluirTodasPermissoes');
        formData.append('codigoUsuario', usuarioCodigo.value);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }
            buscarPermissoes();
            select2Orgao.val(0);
            select2Orgao.trigger('change');
            inputCodigoPermissao.value = "";
            mudarBotaoIncluir('Incluir');
            limparCheckboxs();
        });
    });

    btnCancelar.addEventListener('click', (e) => {
        location.href = `con1_aba_permissoesdadespesa.php?id_usuario=${usuarioCodigo.value}&nome=${usuarioDescricao.value}`;
    });

    selectTiposProcessos.addEventListener('change', () => {
        const codigoTipoProcesso = selectTiposProcessos.value;
        const codigoPermissao = inputCodigoPermissao.value;
        if (empty(codigoTipoProcesso)) {
            limparCheckboxs();
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'buscarPermissoesPorTipoProcesso');
        formData.append('codigoTipoProcesso', codigoTipoProcesso);
        formData.append('codigoPermissao', codigoPermissao);
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            limparCheckboxs();

            response.atividades.map((atividade) => {
                cboAtividades.forEach((checkbox) => {
                    if (checkbox.value == atividade.db69_atividadesexecucao) {
                        checkbox.checked = true;
                    }
                });
            });
        });
    });

    btnSalvarAtividades.addEventListener('click', () => {
        const codigoTipoProcesso = selectTiposProcessos.value;
        const codigoPermissao = inputCodigoPermissao.value;

        const formData = new FormData();
        formData.append('acao', 'salvarPermissoesPorTipoProcesso');
        formData.append('codigoTipoProcesso', codigoTipoProcesso);
        formData.append('codigoPermissao', codigoPermissao);
        formData.append('atividadesSelecionadas', getAtividadesSelecionadas());
        HttpClient.post('con1_permissoesdadespesa.RPC.php', {body: formData}).then((response) => {
            if (response.erro) {
                alert(response.mensagem);
            }
        });
    })
</script>
</body>
</html>
