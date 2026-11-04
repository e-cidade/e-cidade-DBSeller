<?php

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
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
</head>
<body class="body-default">
<div class="container">
    <fieldset>
        <legend>Cria os recursos para os lançamentos que não o possuem</legend>
        <table class="form-container">
            <tr>
                <td><label for="exercicio">Exercício: </label></td>
                <td><input type="number" id="exercicio" name="exercicio" maxlength="4"></td>
            </tr>
        </table>
        <button type="button" id="btnProcessar">
            <i class="fas fa-cog"></i>
            Criar Recursos
        </button>

        <button type="button" id="btnCorrigir">
            <i class="fas fa-cog"></i>
            Corrigir Lançamentos de Empenhos
        </button>
        <br>
        <br>
        <button type="button" id="btnCorrigir142">
            <i class="fas fa-cog"></i>
            Lançamentos Documento 142
        </button>
        <button type="button" id="btnSuplementacoes">
            <i class="fas fa-cog"></i>
            Lançamentos Suplementações
        </button>
        <br>
        <br>

        <button type="button" id="btnSlips">
            <i class="fas fa-cog"></i>
            Documentos de slip (exceto 142)
        </button>
    </fieldset>

    <br />

    <fieldset>
        <legend>Ajustar lançamentos a partir da data informada</legend>
        <h3>Informe a data inicial que deseja alterar os lançamentos.</h3>
        <table class="form-container">
            <tr>
                <td><label for="dataLancamentos">A partir:</label></td>
                <td><input id="dataLancamentos" name="dataLancamentos" type="text"/></td>
            </tr>
        </table>
        <div style="text-align: left">

        <button type="button" id="btnConLancamRecurso">
            <i class="fas fa-cog"></i>
            Refaz ConLancamRecurso
        </button>

        <hr>
        <button type="button" id="btnEstoquePatrimonio">
            <i class="fas fa-cog"></i>
            Ajuste Estoque / Patrimônio
        </button>
        </div>
    </fieldset>

</div>

<?php db_menu() ?>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
<script type="text/javascript">

    let exercicio = document.getElementById('exercicio');
    exercicio.value = (new Date()).getFullYear();

    const inputDataInicio = new DBInputDate(document.getElementById('dataLancamentos'));

    let routs = {
        criar: 'v4/api/financeiro/contabilidade/fix/lancamento/criar-recurso',
        empemho: 'v4/api/financeiro/contabilidade/fix/lancamento/recurso-empenho',
        doc142: 'v4/api/financeiro/contabilidade/fix/lancamento/documento-142',
        suplementacoes: 'v4/api/financeiro/contabilidade/fix/lancamento/suplementacoes',
        conlancamrecurso: 'v4/api/financeiro/contabilidade/fix/lancamento/conlancamrecurso',
        slips: 'v4/api/financeiro/contabilidade/fix/lancamento/slips',
        estoquePatrimonio: 'v4/api/financeiro/contabilidade/fix/lancamento/estoque-patrimonio'
    };

    document.getElementById('btnProcessar').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(routs.criar, {"exercicio": exercicio.value});
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnCorrigir').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(routs.empemho, {"exercicio": exercicio.value});
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnCorrigir142').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(routs.doc142, {"exercicio": exercicio.value});
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnSuplementacoes').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(routs.suplementacoes, {"exercicio": exercicio.value});
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnConLancamRecurso').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar os registros de acordo com a data informada ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const post = {"exercicio": exercicio.value,"data": inputDataInicio.__toLocaleDateString()}
        const response = await CurrentWindow.axios.post(routs.conlancamrecurso, post);
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnSlips').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const response = await CurrentWindow.axios.post(routs.slips, {"exercicio": exercicio.value});
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });

    document.getElementById('btnEstoquePatrimonio').addEventListener('click', async () => {
        let msg = `Tem certeza que deseja processar o exercício ${exercicio.value}?`;
        if (!confirm(msg)) {
            return false;
        }
        js_divCarregando('Processando.', 'loading_message');
        const post = {"data": inputDataInicio.__toLocaleDateString()}
        const response = await CurrentWindow.axios.post(routs.estoquePatrimonio, post);
        js_removeObj('loading_message');
        alert('Processamento concluído.')
    });
</script>
