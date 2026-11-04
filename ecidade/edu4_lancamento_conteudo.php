<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/arrays.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/classes/educacao/escola/AccordionHabilidadesBNCC.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <style>

        div#accordion {
            font-size: 10pt;
        }

        div#accordion a {
            position: relative;
            color: #FFF;;
            display: block;
        }

        div#accordion > ul > li a {
            -webkit-box-shadow: 0px 1px 5px 0px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0px 1px 5px 0px rgba(0, 0, 0, 0.2);
            box-shadow: 0px 1px 5px 0px rgba(0, 0, 0, 0.2);
        }

        ul.nivel_1 li a {
            text-decoration: none;
        }

        ul {
            background-color: #2c5676;

            list-style: none;
            padding: 0;
            margin: 0;
        }

        .link-paddind {
            padding: 10px 40px 10px 25px;
        }

        div#accordion li.checkbox {
            padding: 2px 2px 2px 40px;
            background: #CCC;
        }

        div#accordion ul > li > ul > li > ul.nivel_3 {
            background: #6694b8;
        }

        div#accordion ul > li > ul > li > ul.nivel_4 {
            background: #CCC;
        }

        ul.nivel_1 > li > a:first-child {
            padding: 10px;
            display: block;
        }

        ul.nivel_2, ul.nivel_3 {
            background-color: #4a789c;
            overflow: hidden;
            transition: height 2s;
            -webkit-transition: height 2s;
        }

        div#accordion a i.fas {
            position: absolute;
            right: 20px;
        }

        .collapse {
            display: block;
            height: auto;
        }

        .collapsed {
            display: none;
            height: auto;
        }

        .conteudo {
            margin-top: 2px;
            border-bottom: 1px;
            border-bottom: 1px solid #999999;
        }

        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #pesquisaHabilidades {
            margin: 15px 0 12px 12px;
        }

        #disciplinaBncc {
            padding: 2px;
        }

        input[type=search] {
            padding: 11px;
        }

        kbd {
            text-indent: 0;
        }

        .hasError {
            border: 1px solid #D8000C;
            background-color: #ffe3e6;
        }
    </style>
</head>
<body class="body-default">
<div class="container">
    <form id="frmConteudo" method="post" action="">
        <fieldset>
            <legend>Registro de Aula</legend>
            <table class="form-container">
                <tr>
                    <td><label for="nomeRegente">Regente:</label></td>
                    <td>
                        <input type="hidden" id="codigoCgm" name="codigoCgm">
                        <input type="hidden" id="codigoUsuario" name="codigoUsuario">
                        <input type="text" id="nomeRegente" name="nomeRegente" class="readonly field-size8" disabled>
                    </td>
                </tr>
                <tr>
                    <td><label for="turma">Turmas:</label></td>
                    <td>
                        <select id="turma" name="turma">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="etapa">Etapa:</label></td>
                    <td>
                        <select id="etapa" name="etapa">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="regencia">Regência:</label></td>
                    <td>
                        <select id="regencia" name="regencia">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="turno">Turno:</label></td>
                    <td>
                        <select id="turno" name="turno">
                        </select>
                    </td>
                </tr>
                <tr id="trReferencia">
                    <td><label for="turnoReferencia">Referência:</label></td>
                    <td>
                        <select id="turnoReferencia" name="turnoReferencia">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="data">Data</label></td>
                    <td><input type="text" id="data" name="data"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <fieldset>
                            <legend>Conteúdo Desenvolvido</legend>
                            <textarea rows="3" class="field-size-max" id="conteudoDesenvolvido"
                                      name="conteudo"></textarea>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </fieldset>

        <button id="btnSalvarConteudo" type="button">
            <i class="fas fa-save"></i>
            Salvar Conteúdo
        </button>
        <button id="btnExcluir" type="button" disabled>
            <i class="fas fa-trash-alt"></i>
            Excluir Conteúdo
        </button>
        <button id="btnLancarHabilidade" type="button" disabled>
            <i class="fas fa-clipboard-list"></i>
            Lançar Habilidade
        </button>
        <input type="hidden" id="codigo" name="codigo">
    </form>
</div>

<div id="containerHabilidades" class="text-left">
    <div id="pesquisaHabilidades">
        <table>
            <tr id="linhaDisciplinaBNCC" style="display: none">
                <td><label for="disciplinaBncc">Disciplina BNCC:</label></td>
                <td>
                    <select id="disciplinaBncc" name="disciplinaBncc">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="search" id="inputPesquisar" placeholder="Pesquisar habilidades" maxlength="15">
        <button type="button" id="btnPesquisar"><i class="fas fa-search"></i></button>

    </div>
    <fieldset>
        <legend>Lista de Habilidades</legend>
        <div id="containerAccordion" style="height: 280px; width: 1065px; float: left; overflow: auto"></div>
        <div id="habilidadesAdicionados" style="float: right; width: 180px">
            <fieldset>
                <legend>Selecionadas:</legend>
                <select id="cboSelecionados" multiple="multiple" class="field-size-max" style="height: 260px;">
                </select>
            </fieldset>
        </div>

    </fieldset>
    <div class="subcontainer">
        <button id="btnSalvarHabilidade" type="button">
            <i class="fas fa-save"></i>
            Salvar
        </button>
    </div>
</div>
<?php db_menu(); ?>
<script>
    var turmasRegente;
    var ensinoInfantil = false;

    /**
     * Inputs/Elements Formulário
     */
    const formulario = document.getElementById('frmConteudo'),
        inputCodigoCgm = document.getElementById('codigoCgm'),
        inputCodigoUsuario = document.getElementById('codigoUsuario'),
        inputNomeRegente = document.getElementById('nomeRegente'),
        selectTurma = document.getElementById('turma'),
        selectEtapa = document.getElementById('etapa'),
        selectTurno = document.getElementById('turno'),
        selectTurnoReferencia = document.getElementById('turnoReferencia');
        selectRegencia = document.getElementById('regencia'),
        selectDisciplinaBncc = document.getElementById('disciplinaBncc'),
        linhaDisciplinaBNCC = document.getElementById('linhaDisciplinaBNCC'),
        data = new DBInputDate(document.getElementById('data')),
        inputData = document.getElementById('data'),
        inputConteudoDesenvolvido = document.getElementById('conteudoDesenvolvido'),
        inputCodigo = document.getElementById('codigo'),
        btnSalvarConteudo = document.getElementById('btnSalvarConteudo'),
        btnExcluir = document.getElementById('btnExcluir'),
        btnLancarHabilidade = document.getElementById('btnLancarHabilidade');

    /**
     * Inputs/Elements Modal
     */
    const btnPesquisar = document.getElementById('btnPesquisar'),
        btnSalvarHabilidade = document.getElementById('btnSalvarHabilidade'),
        containerHabilidades = document.getElementById('containerHabilidades'),
        containerAccordion = document.getElementById('containerAccordion'),
        cboSelecionados = document.getElementById('cboSelecionados'),
        inputPesquisar = document.getElementById('inputPesquisar');


    const montaSelect = (elemento, dados) => {
        elemento.options.length = 0;
        elemento.add(new Option('Selecione', ''));

        dados.map((turma) => {
            elemento.add(new Option(turma.nome, turma.codigo));
        });

        if (dados.length === 1) {
            elemento.value = dados[0].codigo;
            elemento.dispatchEvent(new Event('change'));
        }
    };

    const validaFormulario = () => {
        try {
            if (empty(inputCodigoCgm.value)) {
                throw 'Erro ao identificar o Regente.';
            }
            if (empty(selectTurma.value)) {
                throw 'Selecione a Turma.';
            }
            if (empty(selectEtapa.value)) {
                throw 'Selecione a Etapa';
            }
            if (empty(selectRegencia.value)) {
                throw 'Selecione a Regência';
            }
            if (empty(data.__toLocaleDateString())) {
                throw 'Informe a data do dia de aula.';
            }
            if (empty(selectTurno.value)) {
                throw 'Selecione um turno.';
            }
            if (empty(selectTurnoReferencia.value)) {
                throw 'Selecione um turno referência.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    const limpar = (limparEtapa, limparRegencia, limparTurno, limparReferencia = true) => {
        if (limparEtapa) {
            selectEtapa.options.length = 0;
            selectEtapa.add(new Option('Selecione', ''));
        }
        if (limparRegencia) {
            selectRegencia.options.length = 0;
            selectRegencia.add(new Option('Selecione', ''));
        }
        if (limparTurno) {
            selectTurno.options.length = 0;
            selectTurno.add(new Option('Selecione', ''));
        }
        if (limparReferencia) {
            selectTurnoReferencia.options.length = 0;
            selectTurnoReferencia.add(new Option('Selecione', ''));
        }
        inputConteudoDesenvolvido.value = '';
        btnLancarHabilidade.setAttribute('disabled', 'disabled');
        btnExcluir.setAttribute('disabled', 'disabled');
        inputPesquisar.value = '';
        inputPesquisar.removeClassName('hasError');
        inputCodigo.value = '';
        containerAccordion.innerHTML = '';
    };

    selectTurma.addEventListener('change', (event) => {
        limpar(true, true, true);

        turmasRegente.map((turma) => {
            if (event.target.value == turma.codigo) {
                ensinoInfantil = turma.ensinoInfantil;
                montaSelect(selectEtapa, turma.etapas)
            }
        });
        buscarConteudoDesenvolido()
    });

    selectEtapa.addEventListener('change', (event) => {
        limpar(false, true, true);

        turmasRegente.map((turma) => {
            if (selectTurma.value == turma.codigo) {
                turma.etapas.map((etapa) => {
                    if (event.target.value == etapa.codigo) {
                        montaSelect(selectRegencia, etapa.regencias);
                    }
                });
            }
        });
        buscarConteudoDesenvolido()
    });

    selectRegencia.addEventListener('change', (event) => {
        limpar(false, false, false);
        if (selectRegencia.value === '') {
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'buscarDisciplinaBNCC');
        formData.append('etapa', selectEtapa.value);
        formData.append('ensinoInfantil', ensinoInfantil);
        turmasRegente.map((turma) => {
            if (selectTurma.value == turma.codigo) {
                turma.etapas.map((etapa) => {
                    if (etapa.codigo == selectEtapa.value) {
                        etapa.regencias.map((regencia) => {
                            if (regencia.codigo == selectRegencia.value) {
                                formData.append('disciplina', regencia.codigo_disciplina);
                                formData.append('regencia', regencia.codigo);
                            }
                        })
                    }

                    selectTurno.add(new Option(turma.turno.descricao, turma.turno.codigo));
                    selectTurno.value = turma.turno.codigo;
                    selectTurno.setAttribute('disabled', 'disabled');
                    selectTurnoReferencia.options.length = 0;
                    const turnosReferencia = [];
                    turma.turnosReferentes.map((referencia)=> {
                        if(!turnosReferencia.find(item => item.descricao === referencia.descricao)){
                            turnosReferencia.push(referencia)
                        }
                    })

                    if(turnosReferencia.length === 0){
                        selectTurno.add(new Option('Selecione', ''));
                    }

                    turnosReferencia.map((referencia)=> {
                        selectTurnoReferencia.add(new Option(referencia.descricao, referencia.codigo));
                    })
                });
            }
        });

        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            montaSelect(selectDisciplinaBncc, response.disciplinasBNCC);
            linhaDisciplinaBNCC.style.display = "none";
            if (response.disciplinasBNCC.length > 1) {
                linhaDisciplinaBNCC.style.display = "table-row";
            }
        });
        buscarConteudoDesenvolido()
    });

    selectTurnoReferencia.addEventListener('change', () => {
        buscarConteudoDesenvolido();
    })

    inputData.addEventListener('blur', () => {
        inputConteudoDesenvolvido.value = '';
        inputCodigo.value = '';
        btnLancarHabilidade.setAttribute('disabled', 'disabled');
        btnExcluir.setAttribute('disabled', 'disabled');
        collection.clear();
        renderizaSelectHabilidadeSelecionadas();

        if (empty(data.__toLocaleDateString())) {
            return;
        }

        if (empty(selectRegencia.value)) {
            alert('Selecione a Regência.');
            return;
        }
        buscarConteudoDesenvolido();
    });

    function buscarConteudoDesenvolido() {
        if (!data.__toLocaleDateString()) {
            return;
        }
        const formData = new FormData();
        formData.append('acao', 'buscarConteudoDesenvolido');
        formData.append('regencia', selectRegencia.value);
        formData.append('data', js_formatar(data.__toLocaleDateString(), 'd'));
        formData.append('turno', selectTurno.value);
        formData.append('turnoReferencia', selectTurnoReferencia.value);
        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {

            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            inputConteudoDesenvolvido.value ='';
            inputCodigo.value = '';

            if (!empty(response.conteudo)) {
                inputConteudoDesenvolvido.value = response.conteudo;
                inputCodigo.value = response.codigo;
                btnLancarHabilidade.removeAttribute('disabled');
                btnExcluir.removeAttribute('disabled');
            }
        });
    }

    (function () {
        const formData = new FormData();
        formData.append('acao', 'buscarRegente');
        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {

            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            inputCodigoCgm.value = response.cgm.codigo;
            inputCodigoUsuario.value = response.cgm.usuario;
            inputNomeRegente.value = response.cgm.nome;
        }).then(function (erro) {
            if (erro) {
                return;
            }
            formData.set('acao', 'buscarTurmas');
            formData.append('cgm', inputCodigoCgm.value);
            HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }

                turmasRegente = response.turmas;
                montaSelect(selectTurma, turmasRegente);
            });
        })
    })();

    btnSalvarConteudo.addEventListener('click', () => {
        if (!validaFormulario()) {
            return;
        }
        const formData = new FormData(formulario);
        formData.append('acao', 'salvarConteudo');
        formData.append('turno', selectTurno.value);
        formData.set('data', js_formatar(data.__toLocaleDateString(), 'd'));

        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);

            if (response.erro) {
                return;
            }

            inputCodigo.value = response.codigo;
            btnLancarHabilidade.removeAttribute('disabled');
            btnExcluir.removeAttribute('disabled');
        });
    });


    const hideWindowHabilidades = () => {
        const msgBoardAlunos = document.getElementById('msgBoardAlunos');
        if (msgBoardAlunos) {
            msgBoardAlunos.parentNode.removeChild(msgBoardAlunos);
        }

        if (!!windowHabilidades.oDBMask) {
            windowHabilidades.oDBMask.destroy();
        }

        windowHabilidades.hide();
    };

    /**
     * @type {windowAux}
     */
    var windowHabilidades = new windowAux('windowHabilidades', 'Lançamento de Habilidades', 1300, 600);
    windowHabilidades.setContent(containerHabilidades);
    windowHabilidades.allowCloseWithEsc(true);
    windowHabilidades.setShutDownFunction(function () {
        hideWindowHabilidades();
    });

    const marcarCheckBoxHabilidades = (checkboxHabilidades) => {
        collection.add({"codigo": checkboxHabilidades.value, "title": checkboxHabilidades.nextSibling.innerHTML});
        checkboxHabilidades.checked = true;
        renderizaSelectHabilidadeSelecionadas()
    };

    btnPesquisar.addEventListener('click', () => {
        const valor = inputPesquisar.value.toUpperCase();

        inputPesquisar.removeClassName('hasError');
        if (valor.length < 8) {
            alert('O código da habilidade deve ter no mínimo 8 caracteres.');
            return;
        }

        const checkboxHabilidades = document.getElementById(valor);
        if (checkboxHabilidades) {
            marcarCheckBoxHabilidades(checkboxHabilidades);
        } else {
            inputPesquisar.addClassName('hasError');
        }
    });

    /**
     * Mapeado atalhos de teclado.
     * @param e
     */
    document.onkeyup = function (e) {
        var e = e || window.event; // for IE to cover IEs window event-object

        if ((e.ctrlKey && e.which === 13) && (e.target == inputPesquisar)) {
            btnPesquisar.click();
        }
    };

    const collection = new Collection();
    collection.setId('codigo');

    const renderizaSelectHabilidadeSelecionadas = () => {
        cboSelecionados.options.length = 0;
        collection.get().map((item) => {
            const option = new Option(item.codigo, item.codigo);
            option.setAttribute('title', item.title);
            cboSelecionados.add(option);
        });
    };

    const callbackMarcaCheckHabilidade = (event) => {
        if (event.target.checked) {
            collection.add({"codigo": event.target.value, "title": event.target.nextSibling.innerHTML});
        } else {
            collection.remove(event.target.value);
        }

        renderizaSelectHabilidadeSelecionadas()
    };

    cboSelecionados.addEventListener('dblclick', (event) => {
        collection.remove(event.target.value);
        const inputSelecionado = document.getElementById(event.target.value);
        inputSelecionado.checked = false;
        renderizaSelectHabilidadeSelecionadas();
    });

    btnLancarHabilidade.addEventListener('click', () => {
        const turma = selectTurma.options[selectTurma.selectedIndex].innerHTML;
        const etapa = selectEtapa.options[selectEtapa.selectedIndex].innerHTML;
        const disciplina = selectRegencia.options[selectRegencia.selectedIndex].innerHTML;

        new DBMessageBoard('msgBoardAlunos',
            `Informe as habilidades para turma ${turma}, etapa ${etapa}, regência ${disciplina} no dia ${data.__toLocaleDateString()}`,
            'Para usar a pesquisa, informe o código da habilidade e pressione <kbd>Ctrl</kbd>+<kbd>Enter</kbd> ou clique <i class="fas fa-search"></i>',
            windowHabilidades.getContentContainer()
        );

        windowHabilidades.show(0, 0, true);

        if (selectDisciplinaBncc.value != '') {
            selectDisciplinaBncc.dispatchEvent(new Event('change'));
        }

        inputPesquisar.focus();
    });

    const buscarHabilidadesDesenvolvida = () => {
        const formData = new FormData();
        formData.append('acao', 'buscarHabilidadesDesenvolvida');
        formData.append('disciplinaBncc', selectDisciplinaBncc.value);
        formData.append('codigo', inputCodigo.value);
        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            response.habilidades.map((habilidadeCodigo) => {
                const checkboxHabilidades = document.getElementById(habilidadeCodigo);

                if (checkboxHabilidades) {
                    marcarCheckBoxHabilidades(checkboxHabilidades);
                }
            });
        });
    };

    const montaAccordion = () => {
        collection.clear();
        renderizaSelectHabilidadeSelecionadas();

        AccordionHabilidadesBNCC.build(
            selectTurma.value,
            selectEtapa.value,
            selectDisciplinaBncc.value,
            data.getValue(),
            containerAccordion
        ).then((habilidades) => {
            AccordionHabilidadesBNCC.adicionarEventosCheckbox(callbackMarcaCheckHabilidade);
            if (!empty(inputCodigo.value)) {
                buscarHabilidadesDesenvolvida()
            }
        });
    };

    selectDisciplinaBncc.addEventListener('change', () => {
        montaAccordion();
    });

    btnSalvarHabilidade.addEventListener('click', () => {
        const formData = new FormData();
        formData.append('acao', 'salvarHabilidades');
        formData.append('disciplinaBncc', selectDisciplinaBncc.value);
        formData.append('codigo', inputCodigo.value);
        formData.append('turma', selectTurma.value);

        collection.build().map((habilidade) => {
            formData.append('habilidades[]', habilidade.codigo);
        });

        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }

            if (selectDisciplinaBncc.value != '') {
                buscarHabilidadesDesenvolvida()
            }
        });
    });

    btnExcluir.addEventListener('click', () => {

        if (!confirm('Tem certeza que deseja excluir o Conteúdo Desenvolvido para esta data e suas habilidades?')) {
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'excluirConteudoDesenvolvido');
        formData.append('codigo', inputCodigo.value);
        HttpClient.post('edu4_habilidades_bncc.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }
            limpar(false, false, false, false);
        });
    });

    function identificacaoUnica() {
        var dt = new Date().getTime();
        var uuid = 'xxxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = (dt + Math.random() * 16) % 16 | 0;
            dt = Math.floor(dt / 16);
            return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
        return uuid;
    }

</script>
</body>
