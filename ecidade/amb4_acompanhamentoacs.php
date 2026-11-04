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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('dbforms/db_funcoes.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
</head>
<body>
    <?php validaDepartamentoLogado('unidade'); ?>
    <div id="alert" class="alert-info" role="alert" style="text-align: center;" hidden></div>
    <div class="container">
        <fieldset>
            <legend>Acompanhamento ACS</legend>
            <input id="id" hidden>
            <table class="form-container">
                <tr>
                    <td>
                        <label id="unidade">Unidade:</label>
                    </td>
                    <td>
                        <input type="text" id="sd02_i_codigo" value="<?=db_getsession('DB_coddepto')?>"
                            class="field-size2">
                        <input type="text" id="descrdepto" class="readonly field-size9">
                    </td>
                </tr>
                <tr>
                    <td>
                        <a id="profissional">Profissional:</a>
                    </td>
                    <td>
                        <input type="text" id="sd03_i_codigo" value="<?=buscaCgmLogado('profissionalSaude');?>"
                            class="field-size2">
                        <input type="text" id="z01_nome" class="readonly field-size9">
                    </td>
                </tr>
                <tr>
                    <td>
                        <a id="paciente">Paciente:</a>
                    </td>
                    <td>
                        <input type="text" id="z01_i_cgsund" class="field-size2">
                        <input type="text" id="z01_v_nome" class="field-size9">
                    </td>
                </tr>
            </table>
            <table class="form-container">
                <tr>
                    <td>
                        <label>Microárea:</label>
                    </td>
                    <td>
                        <input type="text" id="microarea" class="readonly" readonly>
                    </td>
                    <td>
                        <label>Família:</label> &nbsp;
                    </td>
                    <td>
                        <input type="text" id="familia" class="readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Data Atendimento:</label>
                    </td>
                    <td>
                        <input type="text" id="data_atendimento">
                    </td>
                    <td>
                        <label>Hora Atendimento:</label> &nbsp;
                    </td>
                    <td>
                        <input type="time" id="hora_atendimento">
                    </td>
                </tr>
                <tr>
            </table>
            <fieldset class="separator">
                <legend>Evolução</legend>
            </fieldset>
            <div class="subcontainer">
                <textarea id="evolucao"
                          rows="10" cols="65" maxlength="1600"
                          style="min-width: 540px; max-width: 540px;">
                </textarea>
            </div>
        </fieldset>
        <button type="button" id="btnSalvar">
            <i class="fas fa-save"></i>
            Salvar
        </button>
        <button type="button" id="btnLimpar">
            <i class="fas fa-eraser"></i>
            Limpar
        </button>
    </div>
    &nbsp;
    <div id="divAcompanhamentos" class="subcontainer" style="width: 800px; display: none;">
        <fieldset>
            <legend>Acompanhamentos do Paciente</legend>
            <table id="data-table-acompanhamentos"
                class="table table-sm">
            </table>
        </fieldset>
    </div>
    <?php db_menu(); ?>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script>
$.noConflict();
const routes = {
    salvar: 'saude/ambulatorial/procedimento/acompanhamento-acs/save',
    apagar: 'saude/ambulatorial/procedimento/acompanhamento-acs/delete',
    familiamicroarea: 'saude/ambulatorial/cgs/familiamicroarea',
    autocomplete: 'sau4_pesquisacgs.RPC.php?action',
    getByPaciente: 'saude/ambulatorial/procedimento/acompanhamento-acs/paciente',
    relatorio: 'saude/ambulatorial/relatorio/acompanhamento-acs'
};

const divAlert = document.getElementById('alert');
const inputId = document.getElementById('id');

const unidade = {
    ancora: document.getElementById('unidade'),
    inputId: document.getElementById('sd02_i_codigo'),
    inputNome: document.getElementById('descrdepto')
};

const profissional = {
    ancora: document.getElementById('profissional'),
    inputId: document.getElementById('sd03_i_codigo'),
    inputNome: document.getElementById('z01_nome')
};

const paciente = {
    ancora: document.getElementById('paciente'),
    inputId: document.getElementById('z01_i_cgsund'),
    inputNome: document.getElementById('z01_v_nome'),
    inputMicroarea: document.getElementById('microarea'),
    inputFamilia: document.getElementById('familia')
};
const inputHoraAtendimento = document.getElementById('hora_atendimento');
const inputEvolucao = document.getElementById('evolucao');
inputEvolucao.value = '';

const btn = {
    salvar: document.getElementById('btnSalvar'),
    limpar: document.getElementById('btnLimpar')
};

const divAcompanhamentos = document.getElementById('divAcompanhamentos');
const tabelaAcompanhamentos = jQuery('#data-table-acompanhamentos');

const data = new Date();
document.getElementById('data_atendimento').value = data.toLocaleDateString('pt-BR');
const dataAtendimento = new DBInputDate(document.getElementById('data_atendimento'));

inputHoraAtendimento.value = data.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});

const lookUpUnidade = new DBLookUp(unidade.ancora, unidade.inputId, unidade.inputNome, {
    'sArquivo': 'func_unidades.php',
    'sObjetoLookUp': 'db_iframe_unidades',
    'sLabel': 'Pesquisar Unidades'
});

const lookUpProfissional = new DBLookUp(profissional.ancora, profissional.inputId, profissional.inputNome, {
    'sArquivo': 'func_medicos.php',
    'sObjwroLookUp': 'db_iframe_medicos',
    'sLabel': 'Pesquisar Profissionais',
    'aParametrosAdicionais': [`chave_sd06_i_unidade=${unidade.inputId.value}`],
});

const callbackPaciente = () => {
    let idCgs = paciente.inputId.value;

    HttpClient.get(`${PHPSession.requestApi}/${routes.familiamicroarea}/${idCgs}`).then(response => {
        let familia = response.data.familia;
        let microarea = response.data.microarea;
        paciente.inputFamilia.value = familia.sd33_v_descricao;
        paciente.inputMicroarea.value = microarea.sd34_v_descricao;
    });

    HttpClient.get(`${PHPSession.requestApi}/${routes.getByPaciente}/${idCgs}`).then(response => {
        divAcompanhamentos.style.display = '';
        tabelaAcompanhamentos.bootstrapTable('load', response.data);
    });
};

const lookUpPaciente = new DBLookUp(paciente.ancora, paciente.inputId, paciente.inputNome, {
    'sArquivo': 'func_cgs_und.php',
    'sObjetoLookUp': 'db_iframe_cgs',
    'sLabel': 'Pesquisar Pacientes',
    'fCallBack': callbackPaciente
});

paciente.inputNome.classList.remove('readonly');
paciente.inputNome.readOnly = false;

const pacienteAutoComplete = new dbAutoComplete(paciente.inputNome, routes.autocomplete);
pacienteAutoComplete.setTxtFieldId(paciente.inputId);
pacienteAutoComplete.show();
pacienteAutoComplete.setCallBackFunction((id, label) => {
    if (id == '') {
        alert('Paciente inválido.');
        paciente.inputNome.value = '';
        return false;
    }

    paciente.inputId.value = id;
    paciente.inputNome.value = label;
    callbackPaciente();
});

paciente.inputId.addEventListener('change', () => {
    if (paciente.inputId.value == '') {
        paciente.inputNome.value = '';
        paciente.inputMicroarea.value = '';
        paciente.inputFamilia.value = '';
        divAcompanhamentos.style.display = 'none';
        divAlert.hidden = true;
        inputId.value = '';

        return false;
    }
});

btn.limpar.addEventListener('click', () => {
    divAlert.hidden = true;
    inputId.value = '';
    paciente.inputId.value = '';
    paciente.inputId.dispatchEvent(new Event('change'));
    inputEvolucao.value = '';
});

btn.salvar.addEventListener('click', () => {
    if (!validaFormulario()) {
        return;
    }

    const formData = new FormData();

    if (inputId.value != '') {
        formData.append('id', inputId.value);
    }

    let dataHora = `${js_formatar(dataAtendimento.__toLocaleDateString(), 'd')} ${inputHoraAtendimento.value}`;

    formData.append('unidade', unidade.inputId.value);
    formData.append('profissional', profissional.inputId.value);
    formData.append('paciente', paciente.inputId.value);
    formData.append('data_hora', dataHora);
    formData.append('evolucao', inputEvolucao.value);

    PHPSession.appendFormData(formData);

    HttpClient.post(`${PHPSession.requestApi}/${routes.salvar}`, {body: formData}).then(response => {
        alert (response.message);
        if (response.error) {
            return;
        }
        callbackPaciente();
        divAlert.hidden = true;
        inputId.value = '';
        inputEvolucao.value = '';
    })
});

unidade.inputId.dispatchEvent(new Event('change'));
lookUpUnidade.desabilitar();
profissional.inputId.dispatchEvent(new Event('change'));

jQuery(document).ready(jQuery => {
    const buttons = () => {
        return {
            btnImprimir: {
                html:
                    `<div style="text-align: right; margin-right: 5px;">
                        <button onClick="imprimir();"> Imprimir <i class="fas fa-print"></i></i></button>
                    </div>`
            }
        };
    }

    const actions = {
        'click .alterar': (e, d, data) => {
            inputId.value = data.s168_id;
            unidade.inputId.value = data.s168_unidade;
            profissional.inputId.value = data.s168_profissional;
            paciente.inputId.value = data.s168_paciente;
            dataAtendimento.setValue(formataTimestamp(data.s168_data_hora, 'data'));
            inputHoraAtendimento.value = formataTimestamp(data.s168_data_hora, 'hora');
            inputEvolucao.value = data.s168_evolucao;

            unidade.inputId.dispatchEvent(new Event('change'));
            profissional.inputId.dispatchEvent(new Event('change'));
            paciente.inputId.dispatchEvent(new Event('change'));

            if (divAlert.firstChild) {
                divAlert.removeChild(divAlert.firstChild);
            }

            let p = document.createElement('p');
            p.innerHTML = `Editando registro de código ${data.s168_id}`;
            divAlert.appendChild(p);
            divAlert.hidden = false;
        },
        'click .apagar': (e, d, data) => {
            if (!confirm('Confirma a exclusão do registro?')) {
                return false;
            }
            if (inputId.value == data.s168_id) {
                divAlert.hidden = true;
                inputId.value = '';
                inputEvolucao.value = '';
            }

            const formData = new FormData();

            formData.append('id', data.s168_id);

            PHPSession.appendFormData(formData);

            HttpClient.post(`${PHPSession.requestApi}/${routes.apagar}`, {body: formData}).then(response => {
                alert(response.message);
                if(response.error) {
                    return;
                }
                callbackPaciente();
            })
        }
    };

    tabelaAcompanhamentos.bootstrapTable({
        height: 300,
        buttons: buttons,
        search: true,
        detailView: true,
        columns: [
            {
                checkbox: true,
                width: 20
            },
            {
                field: 's168_id',
                visible: false
            },
            {
                field: 'profissional',
                title: 'Profissional',
                halign: 'center',
                align: 'left',
                width: 500,
                formatter: (a, data) => {
                    return data.profissional.cgm.z01_nome;
                }
            },
            {
                field: 'data',
                title: 'Data',
                halign: 'center',
                align: 'center',
                width: 100,
                formatter: (a, data) => {
                    return formataTimestamp(data.s168_data_hora, 'data');
                }
            },
            {
                field: 'hora',
                title: 'Hora',
                halign: 'center',
                align: 'center',
                width: 60,
                formatter: (a, data) => {
                    return formataTimestamp(data.s168_data_hora, 'hora');
                }
            },
            {
                field: 'actions',
                title: 'Ações',
                halign: 'center',
                align: 'center',
                width: 80,
                formatter: () => {
                    let btnAlterar = '<a class="alterar"><i class="fas fa-edit"></i></i></a>';
                    let btnApagar = '<a class="apagar"><i class="fas fa-trash-alt"></i></a>';
                    return `${btnAlterar}&nbsp;${btnApagar}`
                },
                events: actions
            }
        ],
        idField: 's168_id',
        detailFormatter: (index, row) => {
            return `<p style="text-align: left;"><b>Evolução</b>: ${row.s168_evolucao}</p>`;
        }
    });
});

function imprimir()
{
    let acompanhamentos = tabelaAcompanhamentos.bootstrapTable('getSelections');

    if (acompanhamentos.length == 0) {
        alert('Selecione os acompanhamentos para imprimir!');
        return false;
    }


    const formData = new FormData();

    for (let acompanhamento of acompanhamentos) {
        formData.append('ids[]', acompanhamento.s168_id);
    }

    PHPSession.appendFormData(formData);

    HttpClient.post(`${PHPSession.requestApi}/${routes.relatorio}`, {body: formData}).then(response => {
        if (response.error) {
            alert(response.message);
            return false;
        }

        window.open(response.data.path, 'relatorio_acs', "popup");
    });
}

function formataTimestamp(timestamp, tipo)
{
    let dado = '';

    switch(tipo) {
        case 'data':
            let data = timestamp.split(' ').shift();
            dado = js_formatar(data, 'd');
            break;
        case 'hora':
            let hora = timestamp.split(' ').pop();
            hora = hora.split(':');
            hora.pop();
            hora = hora.join(':');
            dado = hora;
            break;
        default:
            break;
    }

    return dado;
}

function validaFormulario()
{
    if (unidade.inputId.value == '') {
        alert('Informe a unidade!');
        return false;
    }
    if (profissional.inputId.value == '') {
        alert('Informe o profissional!');
        return false;
    }
    if (paciente.inputId.value == '') {
        alert('Informe o paciente!');
        return false;
    }
    if (empty(dataAtendimento.__toLocaleDateString())) {
        alert('Informe a data de atendimento!');
        return false;
    }
    if (inputHoraAtendimento.value == '') {
        alert('Informe a hora do atendimento!');
        return false;
    }
    if (inputEvolucao.value == '') {
        alert('Informe a evolução do acompanhamento!');
        return false;
    }

    return true;
}
</script>
</body>
</html>
