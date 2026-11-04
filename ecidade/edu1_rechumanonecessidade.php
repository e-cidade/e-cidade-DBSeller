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

require(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_app.utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
</head>
<body class="body-default">
<div class="container" style="width: 80%">
    <fieldset>
        <legend>
            <b>Necessidades Especiais</b>
        </legend>
        <div style="width: 100%" id='ctnDataGridNecessidadeEspeciais'>
        </div>
    </fieldset>
    <button type="button" class="btn btn-light" id='btnSalvar' onclick="js_salvar()">
        <i class="fa fa-save"></i>
        Salvar
    </button>
</div>
<div class="container" style="width: 80%">
    <fieldset>
        <legend>
            <b>Registro de Vacinas</b>
        </legend>
        <div id="ctnAdicionarVacina" style="70%">
            <table class="form-container">
                <tr>
                    <td>
                        <label for="vacina">Vacina: </label>
                    </td>
                    <td>
                        <select name="vacina" id="vacina" style="width: auto">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                    <td>
                        <label for="dose">Dose: </label>
                    </td>
                    <td>
                        <select name="dose" id="dose">
                            <option value="">Selecione</option>
                        </select>
                    </td>
                    <td>
                        <label for="data">Data da aplicação: </label>
                    </td>
                    <td>
                        <input type="date" id="data" />
                    </td>
                    <td>
                        <button type="button" class="btn btn-light" id='btnAdicionarVacina' onclick="js_incluirVacinacao()"
                                style="float:right">
                            <i class="fa fa-plus"></i>
                            Adicionar vacina
                        </button>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div style="width: 100%" id='ctnVacinas'>
        </div>
    </fieldset>
    <!--      <input type="button" value='Salvar' id='btnAdicionarVacina' onclick="js_salvar()">-->
</div>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">
    var sUrlRPC = 'edu4_recursohumano.RPC.php';
    var oGet = js_urlToObject(location.search);

    const cboVacinas = document.getElementById('vacina');
    const cboDoses = document.getElementById('dose');
    const inputData = document.getElementById('data');
    const dbinputdate = new DBInputDate(inputData);

    function js_init() {
        //Grid necessidades especiais
        oDataGridNecessidadesEspeciais = new DBGrid("gridNecessidadesEspeciais");
        oDataGridNecessidadesEspeciais.nameInstance = 'oDataGridNecessidadesEspeciais';
        oDataGridNecessidadesEspeciais.setCheckbox(0);
        oDataGridNecessidadesEspeciais.setHeight(300);
        oDataGridNecessidadesEspeciais.setCellWidth(new Array("5%", "95%"));
        oDataGridNecessidadesEspeciais.setHeader(new Array("Código", "Necessidade Especial"));

        oDataGridNecessidadesEspeciais.show($('ctnDataGridNecessidadeEspeciais'));

        // Grid Vacinas
        oGridVacinas = new DBGrid("gridVacinas");
        oGridVacinas.nameInstance = 'oGridVacinas';
        oGridVacinas.setHeight(150);
        oGridVacinas.setCellWidth(["", "", "", "", "30%", "10%", "10%"]);
        oGridVacinas.setHeader(["Código Vacinação", "Código Vacina", "Código Dose", "Vacina", "Dose", "Data", "Ações"]);
        oGridVacinas.aHeaders[0].lDisplayed = false;
        oGridVacinas.aHeaders[1].lDisplayed = false;
        oGridVacinas.aHeaders[2].lDisplayed = false;
        oGridVacinas.show($('ctnVacinas'));

        js_getNecessidades();
        js_getVacinas();
    }


    function js_getNecessidades() {
        var oParametro = new Object();
        oParametro.exec = "getNecessidadesEspeciais";
        oParametro.iRecursoHumano = oGet.iRecursoHumano
        js_divCarregando('Aguarde, carregando dados...', 'msgBox');
        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_carregarDados
            });

    }

    function js_carregarDados(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);
        oDataGridNecessidadesEspeciais.clearAll(true);
        oRetorno.aNecessidades.each(function (oNecessidade, iSeq) {

            var lSelecionado = oNecessidade.possui == 'f' ? false : true;
            var aLinha = new Array(oNecessidade.codigo, oNecessidade.necessidade.urlDecode());
            oDataGridNecessidadesEspeciais.addRow(aLinha, false, false, lSelecionado);

        });
        oDataGridNecessidadesEspeciais.renderRows();
    }

    function js_montarLinha(vacinaProfissional) {
        let codigoVacinacao = vacinaProfissional.ed181_codigo;

        const botoesAcoes = document.createElement('div');
        botoesAcoes.setAttribute("style", "text-align: center");

        // var oBtnAlterar = new Element('input', {
        //     type: 'button',
        //     value: 'A',
        //     id: 'alterar_' + codigoVacinacao
        // });
        var oBtnExcluir = new Element('input', {
            type: 'button',
            value: 'Excluir',
            id: 'excluir_' + codigoVacinacao
        });
        // oBtnAlterar.setAttribute('onclick', 'js_AlterarVacinacao(' + codigoVacinacao + ')');
        oBtnExcluir.setAttribute('onclick', 'js_excluirVacinacao(' + codigoVacinacao + ')');
        botoesAcoes.appendChild(oBtnExcluir);

        return [
            codigoVacinacao,
            vacinaProfissional.ed181_vacina,
            vacinaProfissional.ed181_dose,
            vacinaProfissional.vacina.ed178_descricao,
            vacinaProfissional.dose.ed180_descricao,
            js_formatar(vacinaProfissional.ed181_data, 'd'),
            // oBtnAlterar.outerHTML + ' ' + oBtnExcluir.outerHTML
            botoesAcoes.outerHTML
        ];
    }

    function js_getVacinas() {
        PHPSession.loadData().then(() => {
            HttpClient
                .get(`${PHPSession.requestApi}/educacao/escola/profissional/${oGet.iRecursoHumano}/vacinas`)
                .then(response => {
                    let vacinasProfissional = response.data;

                    vacinasProfissional.map((vacinaProfissional) => {
                        let aLinha = js_montarLinha(vacinaProfissional);
                        oGridVacinas.addRow(aLinha);
                    });
                    js_recarregaGrid(oGridVacinas);
                });

            HttpClient.get(`${PHPSession.requestApi}/educacao/escola/vacinas`).then(response => {
                let vacinas = response.data;

                cboVacinas.length = 0;
                cboVacinas.options.add(new Option('Selecione', ''));
                vacinas.map((vacina) => {
                    cboVacinas.options.add(new Option(vacina.ed178_descricao.trim(), vacina.ed178_codigo));
                });
            });
        });
    }

    cboVacinas.addEventListener("change", () => {
        let codigoVacina = cboVacinas.value;
        cboDoses.length = 0;
        cboDoses.options.add(new Option('Selecione', ''));
        if (empty(codigoVacina)) {
            return;
        }
        HttpClient.get(`${PHPSession.requestApi}/educacao/escola/vacinas/${codigoVacina}`).then(response => {
            let vacina = response.data;

            vacina.doses.map((dose) => {
                cboDoses.options.add(new Option(dose.ed180_descricao, dose.ed180_codigo));
                if (vacina.doses.length === 1) {
                    cboDoses.value = dose.ed180_codigo;
                }
            });
        });
    })

    function js_salvar() {
        var aSelecionados = oDataGridNecessidadesEspeciais.getSelection("object");
        var aNecessidadesEspeciais = new Array();
        aSelecionados.each(function (oNecessidade, iSeq) {
            aNecessidadesEspeciais.push(oNecessidade.aCells[0].getValue());
        });
        var oParametro = new Object();
        oParametro.exec = "salvarNecessidadesEspeciais";
        oParametro.iRecursoHumano = oGet.iRecursoHumano
        oParametro.aNecessidadesEspeciais = aNecessidadesEspeciais;
        js_divCarregando('Aguarde, salvando dados...', 'msgBox');
        var oAjax = new Ajax.Request(sUrlRPC,
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_retornoSalvar
            });
    }

    function js_retornoSalvar(oAjax) {
        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {
            alert('Alterações realizadas com sucesso');
        } else {
            alert(oRetorno.message.UrlDecode());
        }
    }

    function js_incluirVacinacao() {
        let codigoVacina = cboVacinas.value;
        let codigoDose = cboDoses.value;
        let data = inputData.value;

        if (empty(codigoVacina)) {
            alert("Selecione uma vacina");
            return;
        }
        if (empty(codigoDose)) {
            alert("Selecione uma dose");
            return;
        }
        if (empty(data)) {
            alert("Selecione uma data");
            return;
        }

        const formData = new FormData();
        formData.append('vacina', codigoVacina);
        formData.append('dose', codigoDose);
        formData.append('data', data);

        HttpClient.post(`${PHPSession.requestApi}/educacao/escola/profissional/${oGet.iRecursoHumano}/salvar-vacinacao`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            oGridVacinas.clearAll(true);
            js_getVacinas();
        });
    }

    function js_excluirVacinacao(codigo) {
        if (!confirm("Você tem certeza que deseja excluir o registro de vacinação do Profissional?")) {
            return;
        }

        HttpClient.post(`${PHPSession.requestApi}/educacao/escola/profissional/excluir-vacinacao/${codigo}`).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            oGridVacinas.getRows().map((row) => {
                if (row.aCells[0].content === codigo) {
                    oGridVacinas.removeRow([row.getRowNumber()]);
                }
            });
            js_recarregaGrid(oGridVacinas);
        });
    }

    function js_recarregaGrid(oGrid) {
        oGrid.renderRows();
        let contador = 0;
        oGrid.getRows().map((item) => {
            contador++;
        });
        document.getElementById(`${oGrid.sName}numrows`).innerText = contador;
    }

    js_init();
</script>
</body>
</html>
