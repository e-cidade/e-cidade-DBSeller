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

?>
<div class="container">

    <form id="formulario" name="formulario" method="post" action="">
        <fieldset>
            <legend>Manutenção dos Componentes Curriculares / Disciplinas</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <a id="ancoraAreaConhecimento" href="#">Área de Conhecimento:</a>
                    </td>
                    <td colspan="3">
                        <input type="text" id="codigoArea" name="codigoArea" lang="ed293_sequencial">
                        <input type="text" id="descricaoArea" name="descricaoArea" lang="ed293_descr">
                    </td>
                </tr>
                <tr>
                    <td><label for="nome">Nome Disciplina:</label></td>
                    <td colspan="3"><input type="text" id="nome" name="nome" class="field-size-max"> </td>
                </tr>
                <tr>
                    <td><label for="nomeCompleto">Nome Completo:</label></td>
                    <td colspan="3"><input type="text" id="nomeCompleto" name="nomeCompleto" class="field-size-max"> </td>
                </tr>
                <tr>
                    <td><label for="sigla">Sigla:</label></td>
                    <td><input type="text" id="sigla" name="sigla" class="field-size2"> </td>

                    <td align="right"><label for="colorWell">Cor:</label>
                    <input type="color" value="#ffffff" id="colorWell" name="corhtml"></td>

                </tr>
            </table>
            <div id="ctnLancador"></div>
        </fieldset>
        <input type="hidden" id="codigo" name="codigo" >
        <button type="button" id="btnSalvar">
            <i class="far fa-save"></i>
            Salvar
        </button>

        <button type="reset" id="btnCancelar" disabled>
            <i class="far fa-window-close"></i>
            Cancelar
        </button>

    </form>
</div>
<div class="subcontainer" id="ctnGridCadastrados" style="width: 1000px;">

</div>

<script type="text/javascript">
    const formulario = document.getElementById('formulario');
    const inputCodigo = document.getElementById('codigo');
    const ancoraAreaConhecimento = document.getElementById('ancoraAreaConhecimento');
    const inputCodigoArea = document.getElementById('codigoArea');
    const inputDescricaoArea = document.getElementById('descricaoArea');
    const inputNome = document.getElementById('nome');
    const inputNomeCompleto = document.getElementById('nomeCompleto');
    const inputSigla = document.getElementById('sigla');
    const inputCor = document.getElementById('colorWell');
    const btnSalvar = document.getElementById('btnSalvar');
    const btnCancelar = document.getElementById('btnCancelar');

    new DBLookUp(ancoraAreaConhecimento, inputCodigoArea, inputDescricaoArea, {
        "sArquivo": "func_areaconhecimento.php",
        "sObjetoLookUp": "db_iframe_areaconhecimento",
        "sLabel": "Pesquisa de Área de Conhecimento"
    });

    var lancadorCensoDisciplina = new DBLancador("lancadorCensoDisciplina");
    lancadorCensoDisciplina.setNomeInstancia("lancadorCensoDisciplina");
    lancadorCensoDisciplina.setLabelAncora("Código Censo ");
    lancadorCensoDisciplina.setTextoFieldset("Disciplina Censo");
    lancadorCensoDisciplina.setParametrosPesquisa("func_censodisciplina.php", ['ed265_i_codigo', 'ed265_c_descr']);
    lancadorCensoDisciplina.setGridHeight("120px");
    lancadorCensoDisciplina.show($("ctnLancador"));

    const collection = new Collection();
    collection.setId('codigo');

    var gridDisciplinas = new DatagridCollection(collection).configure({
        order    : true,
        height   : 200
    });

    gridDisciplinas.addColumn("area", {
        label : "Área de Conhecimento",
        align : "left",
        width : "20%"
    });
    gridDisciplinas.addColumn("nome", {
        label : "Disciplina",
        align : "left",
        width : "40%"
    }).transformCallback = function( texto, linha ) {
        return "<label title ='"+ linha.nome_completo +"' >" + texto +" </label>";
    };
    gridDisciplinas.addColumn("sigla", {
        label : "Sigla",
        align : "left",
        width : "12%"
    });
    gridDisciplinas.addColumn("censo", {
        label : "Cód. Censo",
        align : "left",
        width : "10%"
    });
    gridDisciplinas.addColumn("cor", {
        label : "Cor",
        align : "left",
        width : "8%"
    });

    gridDisciplinas.addAction('A', 'Alterar', function (event, linha) {

        btnCancelar.click();
        if (linha.area_conhecimento) {
            inputCodigoArea.value = linha.area_conhecimento.codigo;
            inputDescricaoArea.value = linha.area_conhecimento.descricao;
        }

        inputCodigo.value = linha.codigo;
        inputNome.value = linha.nome;
        inputNomeCompleto.value = linha.nome_completo;
        inputSigla.value = linha.sigla;
        inputCor.value = linha.corhtml;

        const censo = linha.censo_disciplinas.map((disciplina) => {
           return [disciplina.codigo, disciplina.descricao];
        });

        lancadorCensoDisciplina.carregarRegistros(censo);
        btnCancelar.removeAttribute('disabled');
    }, true,  'fa-edit');

    gridDisciplinas.addAction('E', 'Excluir', function (event, linha) {
        if (!confirm(`Confirma a exclusão da disciplina ${linha.nome}`)) {
            return;
        }

        const formData = new FormData();
        formData.append('acao', 'excluir');
        formData.append('codigo', linha.codigo);

        HttpClient.post('edu4_componentecurricular.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }

            collection.remove(linha.codigo);
            gridDisciplinas.reload();
        });
        limpar();
    }, true, 'fa-trash-alt');

    gridDisciplinas.show($('ctnGridCadastrados'));

    const filtraCodigosDisciplinaCenso = (censoDisciplinas) => {
        const codigos = censoDisciplinas.map((censo) => {
            return censo.codigo;
        });

        return codigos.join(', ');
    };


    const adicionarCollection = (disciplina) => {
        disciplina.censo = filtraCodigosDisciplinaCenso(disciplina.censo_disciplinas);
        disciplina.area = disciplina.area_conhecimento ? disciplina.area_conhecimento.descricao : '';
        disciplina.cor =  "<input type='color' value='"+ disciplina.corhtml +"' width='10px' disabled='disabled'>";
        collection.add(disciplina);
    };

    function init() {

        const formData = new FormData();
        formData.append('acao', 'get');
        HttpClient.post('edu4_componentecurricular.RPC.php', {body: formData}).then(response => {
            if (response.erro) {
                return;
            }
            gridDisciplinas.clear();
            response.disciplinas.map((disciplina) => {
                adicionarCollection(disciplina);
            });
            gridDisciplinas.reload();
        });
    }

    btnCancelar.addEventListener('click', () => {
        inputCodigo.value = '';
        lancadorCensoDisciplina.clearAll();
        btnCancelar.setAttribute('disabled', 'disabled');
    });

    const validarFormulario = () => {

        try {
            if (empty(inputCodigoArea.value)) {
                throw 'Informe a "Área de Conhecimento".';
            }
            if (empty(inputNome.value)) {
                throw 'Informe o "Nome" da disciplina".';
            }
            if (empty(inputNomeCompleto.value)) {
                throw 'Informe o "Nome Completo" da disciplina.';
            }
            if (empty(inputSigla.value)) {
                throw 'Informe a "Sigla" da disciplina.';
            }
            if (lancadorCensoDisciplina.getRegistros().length === 0) {
                throw 'Informe ao menos uma "Disciplina do Censo".';
            }
            if (empty(inputCor.value)) {
                throw 'Informe a "Cor" da disciplina.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    const limpar = () => {
        btnCancelar.removeAttribute('disabled');
        btnCancelar.click();
        btnCancelar.setAttribute('disabled', 'disabled');
        inputCodigoArea.value = '';
        inputDescricaoArea.value = '';
        inputNome.value = '';
        inputNomeCompleto.value = '';
        inputSigla.value = '';
    };

    btnSalvar.addEventListener('click', () => {

        btnCancelar.setAttribute('disabled', 'disabled');

        if (!validarFormulario()) {
            return;
        }

        const formData = new FormData(formulario);
        formData.append('acao', 'salvar');
        lancadorCensoDisciplina.getRegistros().map(function (linha) {
            formData.append('censoDisciplina[]',  linha.sCodigo)
        });

        HttpClient.post('edu4_componentecurricular.RPC.php', {body: formData}).then(response => {
            alert(response.mensagem);
            if (response.erro) {
                return;
            }
            limpar();
            init();
        });
    });

    var colorWell;
    var defaultColor = "#ffffff";

    window.addEventListener("load", startup, false);

    function startup() {
      colorWell = document.querySelector("#colorWell");
      colorWell.value = defaultColor;
      colorWell.addEventListener("input", updateFirst, false);
      colorWell.addEventListener("change", updateAll, false);
      colorWell.select();
    }

    function updateFirst(event) {
      var p = document.querySelector("p");

      if (p) {
        p.style.color = event.target.value;
      }
    }

    function updateAll(event) {
      document.querySelectorAll("p").forEach(function(p) {
        p.style.color = event.target.value;
      });
    }

    init();
</script>
