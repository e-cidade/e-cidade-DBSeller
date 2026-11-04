<?php

use ECidade\Enum\Educacao\Escola\FormaObtencaoEnum;

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
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container">
    <div style="float: left; width: 380px">
        <fieldset disabled style="color:darkgray">
            <legend>Parâmetros de configuração</legend>
            <table class="form-container" style="width: 260px">
                <tr>
                    <td><label for="automatizar">Processar automaticamente: </label></td>
                    <td>
                        <select name="automatizar" id="automatizar">
                            <option value="false">Não</option>
                            <option value="true">Sim</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="horaexecucao">Horário: </label></td>
                    <td>
                        <select name="horaexecucao" id="horaexecucao">
                            <option value="00:00">00:00</option>
                            <option value="01:00">01:00</option>
                            <option value="02:00">02:00</option>
                            <option value="03:00">03:00</option>
                            <option value="04:00">04:00</option>
                            <option value="05:00">05:00</option>
                            <option value="06:00">06:00</option>
                            <option value="07:00">07:00</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                            <option value="20:00">20:00</option>
                            <option value="21:00">21:00</option>
                            <option value="22:00">22:00</option>
                            <option value="23:00">23:00</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"><label>Dias da semana: </label></td>
                    <td>
                        <input type="checkbox" name="domingo" id="domingo" value="domingo">
                        <label for="domingo">Domingo</label>
                        <br>
                        <input type="checkbox" name="segunda" id="segunda" value="segunda">
                        <label for="segunda">Segunda</label>
                        <br>
                        <input type="checkbox" name="terca" id="terca" value="terca">
                        <label for="terca">Terça</label>
                        <br>
                        <input type="checkbox" name="quarta" id="quarta" value="quarta">
                        <label for="quarta">Quarta</label>
                        <br>
                        <input type="checkbox" name="quinta" id="quinta" value="quinta">
                        <label for="quinta">Quinta</label>
                        <br>
                        <input type="checkbox" name="sexta" id="sexta" value="sexta">
                        <label for="sexta">Sexta</label>
                        <br>
                        <input type="checkbox" name="sabado" id="sabado" value="sabado">
                        <label for="sabado">Sábado</label>
                    </td>
                </tr>
            </table>
            <br/>
            <button type="button" class="btn btn-light" style="color: darkgrey;" id="btnSalvar">
                <i class="fas fa-save"></i>
                Salvar
            </button>
        </fieldset>
    </div>
    <div style="float: right">
        <fieldset>
            <legend>Alocação emergencial</legend>
            <table class="form-container">
                <tr>
                    <td><a id="ancora_faseemergencial" href="#">Fase:</a></td>
                    <td>
                        <input type="text" value="" id="codigo_faseemergencial" name="codigo_faseemergencial"
                               lang="dl_codigo"
                               class="field-size2"/>
                        <input type="text" id="descricao_faseemergencial" name="descricao_faseemergencial"
                               lang="dl_descricao"
                               class="readonly field-size8"/>
                        <button type="button" class="btn btn-light" id="btnProcessarAgora">
                            <i class="fas fa-clock"></i>
                            Processar agora
                        </button>
                    </td>
                </tr>
            </table>
        </fieldset>
        <fieldset>
            <legend>Vagas PCD</legend>
            <table class="form-container">
                <tr>
                    <td><a id="ancora_fase" href="#">Fase:</a></td>
                    <td>
                        <input type="text" value="" id="codigo_fase" name="codigo_fase" lang="dl_codigo"
                               class="field-size2"/>
                        <input type="text" id="descricao_fase" name="descricao_fase" lang="dl_descricao"
                               class="readonly field-size8"/>
                    </td>
                </tr>
                <tr>
                    <td><a id="ancora_etapa" href="#">Etapa:</a></td>
                    <td>
                        <input type="text" value="" id="ed11_i_codigo" name="codigo_etapa"
                               class="field-size2"/>
                        <input type="text" id="ed11_c_descr" name="ed11_c_descr"
                               class="readonly field-size8"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <button type="button" class="btn btn-light" id="btnBuscarVagas">
                            <i class="fas fa-search"></i>
                            Buscar
                        </button>
                    </td>
                </tr>
            </table>
            <div style="width: 750px; clear: both; margin-top: 4px;">
                <table id="data-table" class="table table-sm" data-height="300" style="width: 100%;">
                </table>
            </div>
        </fieldset>
        <fieldset>
            <legend>Consulta lista de espera</legend>
            <table class="form-container">
                <tr>
                    <td style="width: 150px">Lista de Espera Impedida: </td>
                    <td>
                        <button class="btn btn-light" id="btnConsultaListaImpedida">
                            <i class="fas fa-search"></i>
                            Consultar
                        </button>
                    </td>
                </tr>
            </table>
            <div style="width: 750px; clear: both; margin-top: 4px;">
                <table id="table-filaimpedida" class="table table-sm" data-height="300" style="width: 100%;">
                </table>
            </div>
        </fieldset>
    </div>
</div>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    let urlApi = "";
    PHPSession.loadData().then(() => {
        urlApi = PHPSession.requestApi;
        /*buscarParametros();*/
    });

    const selectAutomatizar = document.getElementById('automatizar');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnBuscarVagas = document.getElementById('btnBuscarVagas');
    const btnProcessarAgora = document.getElementById('btnProcessarAgora');
    const btnConsultaListaImpedida = document.getElementById('btnConsultaListaImpedida');

    const buscarVagasPcd = () => {
        const fase = codigoFase.value;
        const etapa = codigoEtapa.value;
        if (fase === '' || etapa === '') {
            table.bootstrapTable('destroy');
            return;
        }

        HttpClient.get(`${urlApi}/educacao/central-de-matriculas/vagas-pcd?fase=${fase}&etapa=${etapa}`)
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                recriarTabelaVagasPcd(response.data.turnos);
                table.bootstrapTable('load', response.data.vagasPcd);
            });
    }

    const ancoraFase = document.getElementById('ancora_fase');
    const codigoFase = document.getElementById('codigo_fase');
    const labelFase = document.getElementById('descricao_fase');

    const lookUpFase = new DBLookUp(ancoraFase, codigoFase, labelFase, {
        'sArquivo': 'func_situacao_fase.php',
        'sLabel': 'Pesquisar Fase',
        'aParametrosAdicionais': ['encerrada=f', 'processada=t'],
        'sObjetoLookUp': "db_iframe_fase",
        'aCamposAdicionais': ['dl_codigo'],
        'fCallBack': function () {
            lookUpEtapa.setParametrosAdicionais(['iFase=' + codigoFase.value, "iTipoConsulta=3"]);
        },
    });

    const ancoraFaseemergencial = document.getElementById('ancora_faseemergencial');
    const codigoFaseemergencial = document.getElementById('codigo_faseemergencial');
    const labelFaseemergencial = document.getElementById('descricao_faseemergencial');

    const lookUpFaseemergencial = new DBLookUp(ancoraFaseemergencial, codigoFaseemergencial, labelFaseemergencial, {
        'sArquivo': 'func_situacao_fase.php',
        'sLabel': 'Pesquisar Fase',
        'aParametrosAdicionais': ['encerrada=f', 'processada=t'],
        'sObjetoLookUp': "db_iframe_fase",
        'aCamposAdicionais': ['dl_codigo'],
    });

    const ancoraEtapa = document.getElementById('ancora_etapa');
    const codigoEtapa = document.getElementById('ed11_i_codigo');
    const descricaoEtapa = document.getElementById('ed11_c_descr');
    const lookUpEtapa = new DBLookUp(ancoraEtapa, codigoEtapa, descricaoEtapa, {
        "sArquivo": 'func_etapamatriculaonline.php',
        "sLabel": 'Pesquisar Etapa',
        "sObjetoLookUp": "db_iframe_etapa_matricula_online",
        "aParametrosAdicionais": ["iTipoConsulta=3"],
    });

    codigoFase.addEventListener('change', () => {
        console.log(codigoFase)
        if (codigoFase.value === '') {
            codigoEtapa.value = '';
            codigoEtapa.dispatchEvent(new Event('change'));
        }
    });
    codigoEtapa.addEventListener('change', () => {
        if (codigoEtapa.value === '') {
            table.bootstrapTable('load', []);
        }
    });
    btnBuscarVagas.addEventListener('click', buscarVagasPcd);

    window.events = {
        'change .inputVagas': function (e, value, row, index) {
            const valor = e.target.value;
            const turnoAlterado = e.target.dataset.turno;
            const turno = row.vagas.find(vaga => vaga.turno == turnoAlterado);
            turno.vagas = valor;
        },
    }

    const buttons = () => {
        return {
            btnAdd: {
                text: 'Salvar',
                icon: 'fa-save',
                event: function () {
                    const vagas = table.bootstrapTable('getData');
                    const formData = new FormData();
                    formData.append('fase', codigoFase.value);
                    formData.append('etapa', codigoEtapa.value);
                    formData.append('vagas', JSON.stringify(vagas));
                    PHPSession.appendFormData(formData);
                    HttpClient.post(`${urlApi}/educacao/central-de-matriculas/vagas-pcd`, {body: formData})
                        .then((response) => {
                            alert(response.message);
                        });
                },
                attributes: {
                    title: 'Salvar vagas PcD'
                },
            }
        }
    }

    var table = jQuery('#data-table');
    const recriarTabelaVagasPcd = (turnos) => {
        table.bootstrapTable('destroy');
        const colunas = [];
        colunas.push({
            title: 'Escola',
            field: 'mo64_escola',
            align: 'left',
            formatter: (value, row) => {
                return `${row.escola.mo53_codigo} - ${row.escola.mo53_nome}`;
            }
        });

        turnos.forEach((turno) => {
            colunas.push({
                title: turno.descricao,
                field: `turno_${turno.codigo}`,
                align: 'center',
                width: 100,
                formatter: (value, row, index, field) => {
                    const codigoTurno = field.split('_')[1];
                    const turno = row.vagas.find((vaga) => vaga.turno == codigoTurno);
                    if (!turno) {
                        return '-';
                    }
                    return `<input type="number"
                                value="${turno.vagas}"
                                data-turno="${turno.turno}"
                                data-escola="${row.mo64_escola}"
                                class="inputVagas"
                                style="width: 60px" />
                            <i class="fa fa-info-circle" title="Pcd Matriculados: ${turno.deficientes}" style="margin-left: 5px"></i>
                            `;
                },
                events: events
            });
        });

        table.bootstrapTable({
            locale: 'pt-BR',
            class: "table table-sm",
            search: true,
            columns: colunas,
            showButtonText: true,
            buttons: buttons,
        });
    }

    const alterarSituacaoDiasSemana = () => {
        if (selectAutomatizar.value == 'false') {
            document.getElementById('horaexecucao').disabled = true;
            document.getElementById('horaexecucao').classList.add('readonly');
            document.getElementById('domingo').disabled = true;
            document.getElementById('segunda').disabled = true;
            document.getElementById('terca').disabled = true;
            document.getElementById('quarta').disabled = true;
            document.getElementById('quinta').disabled = true;
            document.getElementById('sexta').disabled = true;
            document.getElementById('sabado').disabled = true;
        } else {
            document.getElementById('horaexecucao').disabled = false;
            document.getElementById('horaexecucao').classList.remove('readonly');
            document.getElementById('domingo').disabled = false;
            document.getElementById('segunda').disabled = false;
            document.getElementById('terca').disabled = false;
            document.getElementById('quarta').disabled = false;
            document.getElementById('quinta').disabled = false;
            document.getElementById('sexta').disabled = false;
            document.getElementById('sabado').disabled = false;
        }
    }
    selectAutomatizar.addEventListener('change', alterarSituacaoDiasSemana);
    alterarSituacaoDiasSemana();

    const buscarParametros = () => {
        HttpClient.get(`${urlApi}/educacao/central-de-matriculas/parametros-alocacoes`)
            .then((response) => {
                if (response.error) {
                    alert('Erro ao buscar parâmetros');
                    return;
                }

                const {
                    mo63_ativa,
                    mo63_hora,
                    mo63_domingo,
                    mo63_segunda,
                    mo63_terca,
                    mo63_quarta,
                    mo63_quinta,
                    mo63_sexta,
                    mo63_sabado
                } = response.data;

                let horaexecucao = mo63_hora.split(':')[0] + ':' + mo63_hora.split(':')[1];
                selectAutomatizar.value = mo63_ativa;
                document.getElementById('horaexecucao').value = horaexecucao;
                document.getElementById('domingo').checked = mo63_domingo;
                document.getElementById('segunda').checked = mo63_segunda;
                document.getElementById('terca').checked = mo63_terca;
                document.getElementById('quarta').checked = mo63_quarta;
                document.getElementById('quinta').checked = mo63_quinta;
                document.getElementById('sexta').checked = mo63_sexta;
                document.getElementById('sabado').checked = mo63_sabado;

                alterarSituacaoDiasSemana();
            });
    }

    btnSalvar.addEventListener('click', () => {
        if (selectAutomatizar.value == 'true' &&
            !document.getElementById('domingo').checked &&
            !document.getElementById('segunda').checked &&
            !document.getElementById('terca').checked &&
            !document.getElementById('quarta').checked &&
            !document.getElementById('quinta').checked &&
            !document.getElementById('sexta').checked &&
            !document.getElementById('sabado').checked
        ) {
            alert('Para salvar você deve selecionar pelo menos um dia da semana.');
            return;
        }

        const formData = new FormData();
        formData.append('mo63_ativa', document.getElementById('automatizar').value);
        formData.append('mo63_hora', document.getElementById('horaexecucao').value);
        formData.append('mo63_domingo', document.getElementById('domingo').checked);
        formData.append('mo63_segunda', document.getElementById('segunda').checked);
        formData.append('mo63_terca', document.getElementById('terca').checked);
        formData.append('mo63_quarta', document.getElementById('quarta').checked);
        formData.append('mo63_quinta', document.getElementById('quinta').checked);
        formData.append('mo63_sexta', document.getElementById('sexta').checked);
        formData.append('mo63_sabado', document.getElementById('sabado').checked);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/central-de-matriculas/parametros-alocacoes`, {body: formData})
            .then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                /*buscarParametros();*/
            });
    });

    btnProcessarAgora.addEventListener('click', () => {
        if (codigoFaseemergencial.value === '') {
            alert('Selecione uma fase');
            return;
        }
        const formData = new FormData();
        formData.append('fase', codigoFaseemergencial.value);
        PHPSession.appendFormData(formData);
        HttpClient.post(`${urlApi}/educacao/central-de-matriculas/processar-alocacoes`, {body: formData})
            .then((response) => {
                alert(response.message);
            });
    });

    var tableFilaImpedida = jQuery('#table-filaimpedida');
    tableFilaImpedida.bootstrapTable({
        locale: 'pt-BR',
        class: "table table-sm",
        search: true,
        columns: [
            {
                title: 'Fase',
                field: 'fase',
                align: 'left',
                formatter: (value, row) => {
                    return `${row.fase_codigo} - ${row.fase_descricao}`;
                }
            },
            {
                title: 'Escola',
                field: 'escola',
                align: 'left',
                formatter: (value, row) => {
                    return `${row.escola_codigo} - ${row.escola_descricao}`;
                }
            },
            {
                title: 'Etapa',
                field: 'etapa',
                align: 'left',
                formatter: (value, row) => {
                    return `${row.etapa_codigo} - ${row.etapa_nome}`;
                }
            },
            {
                title: 'Turno',
                field: 'turno',
                align: 'left',
                formatter: (value, row) => {
                    return `${row.turno_codigo} - ${row.turno_nome}`;
                }
            },
            {
                title: 'Matrículas PcD',
                field: 'matriculas_pcd',
                align: 'left'
            },
            {
                title: 'VagasPcD',
                field: 'vagas_pcd',
                align: 'left'
            }
        ],
    });

    btnConsultaListaImpedida.addEventListener('click', () => {
        const formData = new FormData();
        PHPSession.appendFormData(formData);
        HttpClient.get(`${urlApi}/educacao/central-de-matriculas/listas-impedidas`, {body: formData})
            .then((response) => {
                if (response.error) {
                    alert(response.message);
                    return;
                }
                tableFilaImpedida.bootstrapTable('load', response.data);
            });
    });
</script>
</body>
</html>
