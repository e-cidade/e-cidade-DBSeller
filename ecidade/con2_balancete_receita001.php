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
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/datagrid.widget.js"></script>
</head>
<body>
<div id='ctnAbas'></div>
<div id='ctnAbaEmissao' class='subcontainer'>
    <fieldset>
        <legend>Balancete da receita</legend>
        <form name="formulario" id="formulario">
            <table class="form-container">
                <tr class="text-left">
                    <td><label class="bold" for="natureza">Natureza da Receita:</label></td>
                    <td colspan="3">
                        <input type="text" name="natureza" id="natureza" class="field-size-max">
                    </td>
                </tr>
                <tr>
                    <td><label for="apenasComMovimentacao">Mostrar apenas receitas com movimentação:</label></td>
                    <td>
                        <select name="apenasComMovimentacao" id="apenasComMovimentacao">
                            <option value="1" checked>Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label for="ementario">Selecione o plano que deseja agrupar os dados:</label></td>
                    <td>
                        <select name="ementario" id="ementario">
                            <option value="ecidade" checked>Plano e-Cidade</option>
                            <option value="uniao">Plano União/Federação</option>
                            <option value="estadual">Plano Estadual/Regional</option>
                        </select>
                    </td>
                </tr>

                <tr id="linhaNivelAgrupar">
                    <td><label class="bold" for="nivelAgrupar">Tipo de Agrupamento das Deduções: </label></td>
                    <td>
                        <select id="nivelAgrupar" name="nivelAgrupar">
                            <option value="0">Lista Deduções Grupo 9</option>
                            <option value="2">Deduções no Mesmo Grupo</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><label for="apresentar">Apresentar:</label></td>
                    <td>
                        <select name="apresentar" id="apresentar">
                            <option selected value="3">Fonte Recurso</option>
                            <option value="9">Subrecurso</option>
                            <option value="7">Código Siconfi</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td id="ctnInstituicao" colspan="4" style="font-weight: normal">
                        <input type="hidden" name="db_selinstit" id="db_selinstit" value="">
                    </td>
                </tr>
                <tr>
                    <td><label for="dadosEmissao">Dados da emissão:</label></td>
                    <td colspan="3">
                        <select name="dadosEmissao" id="dadosEmissao">
                            <option value="orcamento" checked>Orçamento</option>
                            <option value="balanco">Balanço</option>
                        </select>
                    </td>
                </tr>
            </table>
            <fieldset class="separator" id="ctnSaldoPorDatas" style="display: none">
                <legend>Saldo por datas</legend>
                <table class="form-container">
                    <tr>
                        <td>Data Inicial:</td>
                        <td><input id="dataInicio" name="dataInicio" type="text"/></td>
                        <td>Data Final:</td>
                        <td><input id="dataFinal" name="dataFinal" type="text"/></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </fieldset>
    <button id="emitir" type="button">
        <i class="fas fa-print"></i>
        Emitir
    </button>
</div>
<div id='ctnAbaRecursos' class='container' style="display: none">
    <fieldset>
        <fieldset class="subcontainer" style="width: 1000px">
            <legend>Selecione os recursos na grid abaixo se quiser filtrar um ou mais recurso</legend>
            <table id="data-table"
                   class="table table-sm"
                   data-locale="pt-BR"
                   data-cache="false"
                   data-height="600"
                   data-search="true"
                   style="width: 100%;">
            </table>
        </fieldset>
    </fieldset>
</div>

</body>
<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>

<script type="text/javascript" src="scripts/widgets/DBViewInstituicao.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript" src="scripts/classes/DBViewFiltroRecursos.classe.js"></script>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script>
    jQuery.noConflict();

    const recursosSelecionados = [];
    const inputNatureza = document.getElementById('natureza');
    const inputEmentario = document.getElementById('ementario')
    const inputNivelAgrupar = document.getElementById('nivelAgrupar')
    const dadosEmissao = document.getElementById('dadosEmissao')
    const ctnSaldoPorDatas = document.getElementById('ctnSaldoPorDatas')
    const inputDataInicio = new DBInputDate(document.getElementById('dataInicio'));
    const inputDataFinal = new DBInputDate(document.getElementById('dataFinal'));
    const dataHoje = new Date();

    inputDataInicio.setValue(`${dataHoje.getUTCFullYear()}-01-01`);
    inputDataFinal.setValue(dataHoje.toLocaleString());

    $('dataInicio').observe('blur', () => {
        buscarRecursos();
    });

    const ctnAbaRecursos = document.getElementById('ctnAbaRecursos');

    inputEmentario.addEventListener('change', function () {
        linhaNivelAgrupar.style.display = 'table-row';
        inputNivelAgrupar.value = 0;
        if (inputEmentario.value !== 'ecidade') {
            linhaNivelAgrupar.style.display = 'none';
        }
    });

    dadosEmissao.addEventListener('change', function () {
        ctnSaldoPorDatas.style.display = 'none';
        if (dadosEmissao.value === 'balanco') {
            ctnSaldoPorDatas.style.display = '';
        }
    });

    const routs = {
        recursos: 'financeiro/orcamento/recursos',
        relatorio: 'financeiro/contabilidade/relatorio/balancete-receita',
    };

    inputNatureza.addEventListener('input', (e) => {
        var expr = new RegExp("[^0-9,]+");
        if (inputNatureza.value.match(expr)) {
            if (inputNatureza.value != '') {
                inputNatureza.disabled = true;
                alert("Natureza da Receita deve ser preenchido somente com números e vírgulas!");
                inputNatureza.disabled = false;
                inputNatureza.value = '';
                inputNatureza.focus();
                return false;
            }
        }
    })

    // Objetos para controle das Abas
    const dBAba = new DBAbas($('ctnAbas'));
    dBAba.adicionarAba("Relatório", document.getElementById('ctnAbaEmissao'));
    dBAba.adicionarAba("Recursos", ctnAbaRecursos);

    var viewInstituicao = new DBViewInstituicao('viewInstituicao', document.getElementById('ctnInstituicao'));
    viewInstituicao.iHeight = 150;
    viewInstituicao.show();

    const montaColunas = () => {
        return [{
            field: 'check',
            checkbox: true,
            align: 'center',
            valign: 'middle',
        },
            {
                title: 'Recurso',
                field: 'o15_recurso',
                halign: 'center',
                valign: 'middle',
                align: 'left',
                width: '100',
                sortable: true
            },
            {
                title: 'Siconfi',
                field: 'codigo_siconfi',
                halign: 'center',
                valign: 'middle',
                align: 'left',
                width: '100',
                sortable: true
            },
            {
                title: 'Gestão',
                field: 'gestao',
                halign: 'center',
                valign: 'middle',
                align: 'left',
                width: '100',
                sortable: true
            },
            {
                title: 'Recurso',
                field: 'descricao_recurso',
                halign: 'center',
                valign: 'middle',
                align: 'left',
                sortable: true
            },
            {
                title: 'Complemento',
                field: 'descricao_complemento',
                halign: 'center',
                valign: 'middle',
                align: 'left',
                sortable: true
            }
        ];
    };

    const adicionaRecurso = (recurso) => {
        let index = recursosSelecionados.findIndex(obj => obj.id == recurso.id);
        if (index < 0) {
            recursosSelecionados.push(recurso);
        }
    };

    const removeRecurso = (recurso) => {
        let index = recursosSelecionados.findIndex(obj => obj.id == recurso.id);
        if (index >= 0) {
            recursosSelecionados.splice(index, 1)
        }
    };

    var table = jQuery('#data-table');
    table.bootstrapTable({
        columns: montaColunas(),
        data: [],
        onPostBody: (data) => {
            data.map((recurso, index) => {
                if (recurso.selecionado) {
                    table.bootstrapTable('check', index);
                    adicionaRecurso(recurso);
                } else {
                    removeRecurso(recurso)
                }
            });
        },
        onCheckAll: (rowsAfter) => {
            rowsAfter.map(recurso => {
                recurso.selecionado = true;
                adicionaRecurso(recurso);
            });
        },
        onUncheckAll: (rowsAfter, rowsBefore) => {
            rowsBefore.map(recurso => {
                recurso.selecionado = false;

                removeRecurso(recurso);
            });
        },
        onCheck: (row) => {
            row.selecionado = true;
            adicionaRecurso(row);
        },
        onUncheck: (row) => {
            row.selecionado = false;
            removeRecurso(row);
        }
    })

    const buscarRecursos = (exercicio, dataFinal) => {

        HttpClient.get(`${PHPSession.requestApi}/${routs.recursos}/${exercicio}/${dataFinal}`).then(response => {

            let dados = response.data.map((recurso) => {
                recurso.descricao_recurso = recurso.descricao;
                recurso.descricao_complemento = `${recurso.o15_complemento} - ${recurso.o200_descricao}`;
                recurso.check = false
                return recurso;
            });

            table.bootstrapTable('load', dados);
        });
    };

    PHPSession.loadData().then(() => {
        ctnAbaRecursos.style.display = '';
        buscarRecursos(PHPSession.getValueSession('DB_anousu'), js_formatar(inputDataInicio.inputElement.value, 'd'));
    });

    const validarInputs = () => {
        try {
            if (inputNatureza.value !== '') {
                let codigo = Number(inputNatureza.value.substring(0, 1))
                if (![4, 9].includes(codigo) && inputEmentario.value === 'ecidade') {
                    throw 'O código da natureza deve começar com 4 ou 9.';
                }
            }

            if (inputDataInicio.value.getUTCFullYear() != inputDataFinal.value.getUTCFullYear()) {
                throw 'As datas devem estar dentro do mesmo exercício.';
            }

            if (js_comparadata(inputDataInicio.inputElement.value, inputDataFinal.inputElement.value, '>')) {
                throw 'Data de inicio deve ser menor que a data final.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    }

    document.getElementById('emitir').addEventListener('click', () => {

        if (!validarInputs()) {
            return
        }

        const formData = new FormData(document.getElementById('formulario'));

        formData.append('instituicoes', JSON.stringify(viewInstituicao.getInstituicoesSelecionadas()));
        for (let recurso of recursosSelecionados) {
            formData.append('recursos[]', recurso.o15_codigo);
        }

        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/${routs.relatorio}`, {body: formData}).then((response) => {
            if (response.error) {
                alert(response.message);
                return;
            }

            const download = new DBDownload();
            download.addFile(response.data.pdf, "Balancete da Receita - PDF");
            download.addFile(response.data.csv, "Balancete da Receita - CSV");
            download.show();
        });
    });
</script>
