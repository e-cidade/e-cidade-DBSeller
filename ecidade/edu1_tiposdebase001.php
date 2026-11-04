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
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body class="body-default">
<div class="container" id="" style="width: 25vw;">
    <form id="formTipoBase" name="">
        <fieldset>
            <legend>Tipos de Base</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="ed182_descricao">Descrição:</label>
                    </td>
                    <td>
                        <input type="hidden" name="ed182_id">
                        <input type="text" name="ed182_descricao" style="width: 200px;">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="ed182_estrutura_curricular">Estrutura Curricular:</label>
                    </td>
                    <td>
                        <select name="ed182_estrutura_curricular" id="estruturaCurricular" style="width: 200px;">
                            <option value="">Selecione </option>
                        </select>
                    </td>
                </tr>
            </table>
            <table class="form-container" id="ctnItinerarioFormativo" style="display: none">
                <tr>
                    <td>
                        <fieldset>
                            <legend>Tipo do Itinerário Formativo</legend>
                            <table style="margin: 0 auto;" id="tableTipoItinerario">

                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <table class="form-container" id="ctnComposicaoItinerarioIntegrado" style="display: none">
                <tr>
                    <td>
                        <fieldset>
                            <legend>Composição do Itinerário Formativo Integrado</legend>
                            <table style="margin: 0 auto;" id="tableComposicoesItinerarioIntegrado">

                            </table>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <table class="form-container">
                <tr id="ctnTipoCursoItinerario" style="display: none">
                    <td>
                        <label for="ed182_tipo_curso_itinerario_tec_prof">Tipo do curso do itinerário </label><br>
                        <label> formação técnica e profissional:</label>
                    </td>
                    <td>
                        <select name="ed182_tipo_curso_itinerario_tec_prof"
                            id="tipoCursoItinerario"
                            style="width: 200px;"
                        >
                            <option value="">Selecione </option>
                        </select>
                    </td>
                </tr>
                <tr id="ctnItinerarioConcomitante" style="display: none">
                    <td>
                        <label for="ed182_itinerario_concomitante">Itinerário concomitante intercomplementar</label><br>
                        <label> à matrícula de formação geral básica:</label>
                    </td>
                    <td>
                        <select name="ed182_itinerario_concomitante" id="itinerarioConcomitante" style="width: 200px;">
                            <option value="" selected>Selecione </option>
                            <option value="1">Sim </option>
                            <option value="0">Não </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="ed182_ativo">Ativo:</label>
                    </td>
                    <td>
                        <select name="ed182_ativo" id="ativo" style="width: 200px;">
                            <option value="1" selected>Sim </option>
                            <option value="0">Não </option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
        <button id='btnSalvar'>
            <i class="fas fa-save"></i>
            Salvar
        </button>
    </form>
</div>
<div class="container">
    <table id="data-table">
    </table>
</div>

</body>

<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>

<script type="text/javascript">
    const routs = {
        getEstruturaCurricular: `educacao/secretaria/tipo-base/estrutura-curricular/`,
        getTipoItinerarioFormativo: `educacao/secretaria/tipo-base/tipos-itinerario/`,
        getComposicaoItinerarioFormativoIntegrado: `educacao/secretaria/tipo-base/composicoes-itinerario-inegrado/`,
        getTiposCursoItinFormacaoTecnicaProfissional: `educacao/secretaria/tipo-base/tipos-curso-formacao-tec-prof/`,
        salvarTipoBase: `educacao/secretaria/tipo-base/salvar`,
        getTiposBase: `educacao/secretaria/tipo-base/buscarTodos`,
        excluir: `educacao/secretaria/tipo-base/excluir`
    }

    //select's
    const selectEstruturaCurricular = document.querySelector('#estruturaCurricular');
    const selectTipoCursoItinerario = document.querySelector('#tipoCursoItinerario');

    //tables dos checkox's
    const tableTipoItinerario = document.querySelector('#tableTipoItinerario');
    const tableComposicoesItinerarioIntegrado = document.querySelector('#tableComposicoesItinerarioIntegrado');

    //elementos com visibilidade variavel
    const ctnItinerarioFormativo = document.querySelector('#ctnItinerarioFormativo');
    const ctnComposicaoItinerarioIntegrado = document.querySelector('#ctnComposicaoItinerarioIntegrado');
    const ctnTipoCursoItinerario = document.querySelector('#ctnTipoCursoItinerario');
    const ctnItinerarioConcomitante = document.querySelector('#ctnItinerarioConcomitante');

    //array de parametros variaveis multiselecao
    const aComposicoesItinerarios = [];
    const aTiposItinerario = [];

    //data-table
    const table = $('#data-table');

    //botao salvar
    const btnSalvar = document.querySelector('#btnSalvar');

    //formulario
    const formulario = document.querySelector('#formTipoBase');

    //busca dados e povoa select #selectEstruturaCurricular
    const carregaEstruturaCurricular = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${routs.getEstruturaCurricular}`).then(response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            response.data.map((key, estrutura) => {
                selectEstruturaCurricular.add(new Option(key, estrutura));
            })
        });
    }

    //busca dados e povoa tabela #tableTipoItinerario
    const carregaTipoItinerario = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${routs.getTipoItinerarioFormativo}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                var tr1 = document.createElement('tr');
                var tr2 = document.createElement('tr');
                await Object.keys(response.data).map((key) => {
                    var td = document.createElement('td');
                    var input = document.createElement('input');
                    input.setAttribute('name', 'ed182_tipo_itinerario_informativo[]');
                    input.setAttribute('class', 'tipoItinerario');
                    input.setAttribute('type', 'checkbox');
                    input.setAttribute('value', key);
                    var label = document.createElement('label');
                    label.innerHTML = ' ' + response.data[key];
                    td.appendChild(input);
                    td.appendChild(label);
                    if (key <= 3) {
                        tr1.appendChild(td);
                    } else {
                        tr2.appendChild(td);
                    }
                })

                tableTipoItinerario.appendChild(tr1);
                tableTipoItinerario.appendChild(tr2);
        });
    }

    //busca dados e povoa tabela #tableComposicoesItinerarioIntegrado
    const carregaComposicaoItinerarioIntegrado = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${routs.getComposicaoItinerarioFormativoIntegrado}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                var tr1 = document.createElement('tr');
                var tr2 = document.createElement('tr');
                await Object.keys(response.data).map((key) => {
                    var td = document.createElement('td');
                    var input = document.createElement('input');
                    input.setAttribute('name', 'ed182_compos_itinerario_integrado[]');
                    input.setAttribute('class', 'composicaoItineraioIntegrado');
                    input.setAttribute('type', 'checkbox');
                    input.setAttribute('value', key);
                    var label = document.createElement('label');
                    label.innerHTML = ' ' + response.data[key];
                    td.appendChild(input);
                    td.appendChild(label);
                    if (key <= 3) {
                        tr1.appendChild(td);
                    } else {
                        tr2.appendChild(td);
                    }
                })

                tableComposicoesItinerarioIntegrado.appendChild(tr1);
                tableComposicoesItinerarioIntegrado.appendChild(tr2);
        });
    }

    //busca dados e povoa select #selectTipoCursoItinerario
    const carregaTipoCursoItinerario = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${routs.getTiposCursoItinFormacaoTecnicaProfissional}`)
            .then( async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                await Object.keys(response.data).map((key) => {
                    selectTipoCursoItinerario.add(new Option(response.data[key], key));
                })
        });
    }

    const carregaEventos = async () => {
        await selectEstruturaCurricular.addEventListener('change', async event => {
            event.preventDefault();
            if (event.target.value == 1) {
                ctnItinerarioFormativo.style.display = '';
            } else {
                ctnItinerarioFormativo.style.display = 'none';
                Object.keys(tiposItinerario).forEach(key => {
                    tiposItinerario[key].checked = false;
                    aTiposItinerario.length = 0;
                })
                ctnTipoCursoItinerario.style.display = 'none';
                ctnComposicaoItinerarioIntegrado.style.display = 'none'
                Object.keys(composicaoItineraioIntegrado).forEach(key => {
                    composicaoItineraioIntegrado[key].checked = false;
                    aComposicoesItinerarios.length = 0;
                })
                ctnItinerarioConcomitante.style.display = 'none';
            }
        })

        const tiposItinerario = document.querySelectorAll('[class=tipoItinerario]');
        await Object.keys(tiposItinerario).forEach(async key => {
            tiposItinerario[key].addEventListener('change', event => {
                event.preventDefault();
                if (tiposItinerario[key].value == 6) {
                    if (tiposItinerario[key].checked) {
                            ctnComposicaoItinerarioIntegrado.style.display = '';
                    } else {
                        ctnComposicaoItinerarioIntegrado.style.display = 'none'
                        Object.keys(composicaoItineraioIntegrado).forEach(key => {
                            composicaoItineraioIntegrado[key].checked = false;
                            aComposicoesItinerarios.length = 0;
                        })
                    }
                }

                if (tiposItinerario[key].checked) {
                    aTiposItinerario.push(tiposItinerario[key].value);

                } else {
                    aTiposItinerario.splice(aTiposItinerario.indexOf(tiposItinerario[key].value), 1);
                }

                if (aTiposItinerario.length > 4) {
                    alert("Você pode selecionar no máximo quatro opções!");
                    tiposItinerario[key].checked = false;
                    aTiposItinerario.splice(aTiposItinerario.indexOf(tiposItinerario[key].value));
                }
            })
        })

        const composicaoItineraioIntegrado = document.querySelectorAll('[class=composicaoItineraioIntegrado]');
        await Object.keys(composicaoItineraioIntegrado).forEach(async key => {
            composicaoItineraioIntegrado[key].addEventListener('change', (event) => {
                if (composicaoItineraioIntegrado[key].value == 5) {
                    if (composicaoItineraioIntegrado[key].checked) {
                        ctnTipoCursoItinerario.style.display = '';
                        ctnItinerarioConcomitante.style.display = '';
                    } else {
                        ctnTipoCursoItinerario.style.display = 'none';
                        ctnItinerarioConcomitante.style.display = 'none';
                    }
                }

                if (composicaoItineraioIntegrado[key].checked) {
                    aComposicoesItinerarios.push(composicaoItineraioIntegrado[key].value);

                } else {
                    aComposicoesItinerarios.splice(aComposicoesItinerarios.indexOf(composicaoItineraioIntegrado[key].value), 1);

                }

                if (aComposicoesItinerarios.length > 4) {
                    alert("Você pode selecionar no máximo quatro opções!");
                    composicaoItineraioIntegrado[key].checked = false;
                    aComposicoesItinerarios.splice(aComposicoesItinerarios.indexOf(composicaoItineraioIntegrado[key].value));
                }
            })
        })

        btnSalvar.addEventListener('click', async event => {
            event.preventDefault();
            if (formulario.ed182_descricao.value == '') {
                alert('O campo descrição é obrigatório!');
                return;
            }
            if (formulario.ed182_estrutura_curricular.value == '') {
                alert('O campo estrutura curricular é obrigatório!');
                return;
            }

            let formData = new FormData(formulario);
            await HttpClient.post(`${PHPSession.requestApi}/${routs.salvarTipoBase}`, {body: formData}).then(response => {
                alert(response.message);
                if(response.error) {
                    return;
                }
                document.location.reload(true);
            })
        })
    }

    const carregaDataTable = async () => {
        const acoesEventos = {
            'click .alterar': (e, value, row, index) => {

                formulario.ed182_id.value = row.ed182_id;
                formulario.ed182_descricao.value = row.ed182_descricao;

                Object.keys(selectEstruturaCurricular.options).map(index => {
                    if (selectEstruturaCurricular.options[index].value == row.ed182_estrutura_curricular.id) {
                        selectEstruturaCurricular.options[index].selected = true;
                        selectEstruturaCurricular.dispatchEvent(new Event('change'));
                    }
                })

                if (row.ed182_estrutura_curricular.id == 1) {
                    checks = document.querySelectorAll('.tipoItinerario')
                    Object.keys(checks).map(index => {
                        row.ed182_tipo_itinerario_informativo.map(tipo => {
                            if (checks[index].value == tipo.id) {
                                checks[index].checked = true;
                                checks[index].dispatchEvent(new Event('change'));
                            }
                        })
                    })
                }

                if (row.ed182_tipo_itinerario_informativo.length > 0) {
                    row.ed182_tipo_itinerario_informativo.map(tipo => {
                        if (tipo.id == 6) {
                            if (row.ed182_compos_itinerario_integrado.length > 0) {
                                checks = document.querySelectorAll('.composicaoItineraioIntegrado');
                                Object.keys(checks).map(index => {
                                    row.ed182_compos_itinerario_integrado.map(tipo => {
                                        if (checks[index].value == tipo.id) {
                                            checks[index].checked = true;
                                            checks[index].dispatchEvent(new Event('change'));
                                        }
                                    })
                                })
                            }
                        }
                    })
                }

                Object.keys(selectTipoCursoItinerario.options).map(index => {
                    if (selectTipoCursoItinerario.options[index].value == row.ed182_tipo_curso_itinerario_tec_prof.id) {
                        selectTipoCursoItinerario.options[index].selected = true;
                    }
                })

                let selectConcomitante = document.querySelector('#itinerarioConcomitante');
                Object.keys(selectConcomitante.options).map(index => {
                    id = row.ed182_itinerario_concomitante ? '1' : '0';
                    if (selectConcomitante.options[index].value == id) {
                        selectConcomitante.options[index].selected = true;
                    }
                })

                let seletcAtivo = document.querySelector('#ativo');
                Object.keys(seletcAtivo.options).map(index => {
                    id = row.ed182_iativo ? 1 : 0;
                    if (seletcAtivo.options[index].value == id) {
                        seletcAtivo.options[index].selected = true;
                    }
                })
            },
            'click .excluir': (e, value, row, index) => {
                if (confirm("Tem certeza que deseja excluir?")) {
                    let formData = new FormData();
                    formData.append('id', row.ed182_id);
                    HttpClient.post(`${PHPSession.requestApi}/${routs.excluir}`, {body: formData}).then(response => {
                        alert(response.message);
                        if(response.error) {
                            return;
                        }
                        document.location.reload(true);
                    })
                }
            }
        }

        const formataAcoes = (value, row, index) => {
            template =  `<a class="alterar" href="javascript:void(0)" title="Alterar">
                            <i class="fa fa-edit"></i>
                        </a>
                        &nbsp;&nbsp;
                        <a class="excluir" href="javascript:void(0)" title="Excluir">
                            <i class="fas fa-trash-alt"></i>
                        </a>`;

            if ( row.ed182_id == 1 || row.ed182_id == 2 || row.ed182_id == 3) {
                template = `<a class="info" href="javascript:void(0)" title="info">
                                <abbr title="Não pode ser excluido nem editado!"><i class="fas fa-info"></i></abbr>
                            </a>`;
            }
            return template;
        };

        const formataCampos = (value, row, index, field) => {
            let template = "";
            if (field == 'ed182_estrutura_curricular'){
                template = row.ed182_estrutura_curricular.descricao
            }

            if (field == 'ed182_tipo_curso_itinerario_tec_prof'){
                template =  row.ed182_tipo_curso_itinerario_tec_prof != null ?
                    row.ed182_tipo_curso_itinerario_tec_prof.descricao :
                    'Não se aplica'
            }

            if (field == 'ed182_itinerario_concomitante'){
                template = row.ed182_itinerario_concomitante && row.ed182_itinerario_concomitante != null ?
                    "Sim" : "Não";
            }

            if (field == 'ed182_ativo'){
                template = row.ed182_ativo ? "Sim" : "Não";
            }

            if (field == 'ed182_tipo_itinerario_informativo'){
                if (row.ed182_tipo_itinerario_informativo == null) {
                    template = 'Não se aplica'
                } else {
                    template = `<table class="table table-striped text-center">
                                    <tbody>`;
                        row.ed182_tipo_itinerario_informativo.map(tipo => {
                            template += `<tr>
                                            <td>${tipo.descricao}</td></tr>
                                        </tr>`;
                        })
                    template += `   </tbody>
                                </table>`;
                }
            }

            if (field == 'ed182_compos_itinerario_integrado'){
                if (row.ed182_compos_itinerario_integrado == null) {
                    template = 'Não se aplica';
                } else {
                    template = `<table class="table table-striped text-center">
                                    <tbody>`;
                        row.ed182_compos_itinerario_integrado.map(tipo => {
                            template += `<tr>
                                            <td>${tipo.descricao}</td></tr>
                                        </tr>`;
                        })
                    template += `   </tbody>
                                </table>`;
                }
            }
            return template;
        };

        const colunas = [
            {
                title: 'Descrição',
                field: 'ed182_descricao',
                halign: 'center',
                align: 'center',
                width: '100px;'
            },
            {
                title: 'Estrutura Curricular',
                field: 'ed182_estrutura_curricular',
                halign: 'center',
                align: 'center',
                width: '150;',
                formatter: formataCampos
            },
            {
                title: 'Tipo do Itinerario Fromativo',
                field: 'ed182_tipo_itinerario_informativo',
                halign: 'center',
                align: 'center',
                width:'300px;',
                formatter: formataCampos
            },
            {
                title: 'Composição do Itinerário Fromativo Integrado',
                field: 'ed182_compos_itinerario_integrado',
                halign: 'center',
                align: 'center',
                width: '70px;',
                formatter: formataCampos
            },
            {
                title: 'Tipo do curso do itinerário',
                field: 'ed182_tipo_curso_itinerario_tec_prof',
                halign: 'center',
                align: 'center',
                width: '250;',
                formatter: formataCampos
            },
            {
                title: 'Itinerário concomitante intercomplementar',
                field: 'ed182_itinerario_concomitante',
                halign: 'center',
                align: 'center',
                width: '80px;',
                formatter: formataCampos
            },
            {
                title: 'Ativo',
                field: 'ed182_ativo',
                halign: 'center',
                align: 'center',
                width: '80px;',
                formatter: formataCampos
            },
            {
                title: 'Ações',
                halign: 'center',
                align: 'center',
                width: '80px;',
                formatter: formataAcoes,
                events: acoesEventos
            }
        ];

        table.bootstrapTable({
            columns: colunas,
            uniqueId: "id",
            locale: 'pt-BR',
            cache: false,
            search: true,
            class: "table table-sm"

        });

        await HttpClient.get(`${PHPSession.requestApi}/${routs.getTiposBase}`).then(async response => {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }

            table.bootstrapTable('load', response.data);
        });
    }

    window.addEventListener('load', async () => {
        PHPSession.loadData().then( async () => {
            await carregaEstruturaCurricular();
            await carregaTipoItinerario();
            await carregaComposicaoItinerarioIntegrado();
            await carregaTipoCursoItinerario();
            await carregaDataTable();
            await carregaEventos();
        });
    })
</script>
<?php db_menu() ?>
