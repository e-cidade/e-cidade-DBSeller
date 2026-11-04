<?PHP
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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script  type="text/javascript" src="scripts/scripts.js"></script>
    <script  type="text/javascript" src="scripts/strings.js"></script>
    <script  type="text/javascript" src="scripts/prototype.js"></script>
    <script  type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script  type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>


    <link href="estilos.css" rel="stylesheet" type="text/css">

    <style>
        .textoErro {
          background-color: #fff984;
        }
    </style>
</head>

<body>

<div class="container">

    <fieldset style="width:600px">
        <legend class="bold">Alteração das contas de lançamento</legend>
        <table>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('Código Lançamento:', "pesquisaLancamento(true)", 1);
                    ?>
                </td>
                <td>
                    <?php db_input('c70_codlan', 10, 1, true, 'text', 1, "onchange='pesquisaLancamento(false)'"); ?>
                </td>
            </tr>
            <tr>
                <td class="bold" >
                    <a href="#" id="lblDocumento">Documento:</a>
                </td>
                <td>
                    <?php
                    db_input('c53_coddoc', 10, 1, true, 'text', 1);
                    db_input('c53_descr', 50, 4, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold">Data Inicial:</td>
                <td>
                    <?php
                    db_inputdata('data_inicial', null, null, null, true, 'text', 1);
                    echo "&nbsp;&nbsp;<b>Data Final:</b> ";
                    db_inputdata('data_final', null, null, null, true, 'text', 1);
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <p>
        <input type="button" id="btnPesquisa" value="Pesquisar" onclick="pesquisar()" />
    </p>
</div>
<div class="container" style="width: 60%">

    <fieldset>
        <legend class="bold">Resultado da pesquisa</legend>
        <p style="text-align: left">
            <label for="codigo_lancamento"><b>Lançamentos Disponíveis:</b></label>
            <select id="codigo_lancamento" style="width: 400px;">
                <option value="">Selecione</option>
            </select>
        </p>
        <div id="ctnResultadoPesquisa"></div>
        <div class="textoErro" style="width: 280px; border: 1px solid;">
            <p class="bold">Registros com lançamentos faltantes</p>
        </div>
    </fieldset>
    <p><input type="button" value="Incluir lançamento nos registros selecionados." onclick="salvarRegistros()" /></p>
</div>

</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>


<script>

    var gridResultado = new DBGrid('gridResultado');
    gridResultado.nameInstance = 'gridResultado';
    gridResultado.setCheckbox(0);
    gridResultado.setHeader(['Lançamento', 'Data', 'Valor', 'Lançamentos Encontrados', 'Executados']);
    gridResultado.setCellWidth(['20%', '20%', '20%', '20%', '20%']);
    gridResultado.setCellAlign(['center', 'center', 'right', 'center', 'center']);
    gridResultado.setHeight(350);
    gridResultado.show($('ctnResultadoPesquisa'));

    function salvarRegistros() {

        var lancamento = document.querySelector('#codigo_lancamento').value;
        if (lancamento === '') {
            return alert("Selecione um dos lançamentos disponíveis para inclusão.");
        }

        var registrosSelecionados = gridResultado.getSelection();
        if (registrosSelecionados.length === 0) {
            return alert("Não foram selecionados registros para processar.");
        }

        var mensagem = "Confirma a inclusão do lançamento nos registros selecionados?";
        if (!confirm(mensagem)) {
            return false;
        }

        var registrosEnvio = [];
        registrosSelecionados.each(
            function(linha) {
                registrosEnvio.push(linha[0]);
            }
        );

        AjaxRequest.create(
            'con4_ajustemanualancamentos.RPC.php',
            {'exec' : 'incluirLancamento', 'lancamento' : lancamento, 'registros' : registrosEnvio},
            function (response) {
                alert(response.message);

                var oDownload = new DBDownload();
                oDownload.addFile(response.arquivo, "Log de Processamento");
                oDownload.show();
                pesquisar();
            }
        ).execute();
    }


    function pesquisar() {

        if (document.querySelector('#c53_coddoc').value === '') {
            return alert("Campo Documento é de preenchimento obrigatório.");
        }

        var parametros = {
            'exec' : 'consultaAlteracaoContas',
            'codigo_lancamento' : document.querySelector('#c70_codlan').value,
            'documento' : document.querySelector('#c53_coddoc').value,
            'data_inicial' : document.querySelector('#data_inicial').value,
            'data_final' : document.querySelector('#data_final').value,
        };

        AjaxRequest.create(
            'con4_ajustemanualancamentos.RPC.php',
            parametros,
            carregarRegistros
        ).setMessage('Aguarde, pesquisando informações...').execute();
    }

    var oLookUpContratos = new DBLookUp($('lblDocumento'), $('c53_coddoc'), $('c53_descr'), {
        "sArquivo" : "func_conhistdoc.php",
        "sObjetoLookUp" : "db_iframe_conhistdoc",
        "sLabel" : "Pesquisar Documentos"
    });

    function carregarRegistros(response, erro) {

        if (erro) {
            return alert(response.message);
        }

        var comboLancamento = document.querySelector('#codigo_lancamento');
        comboLancamento.options.length = 1;
        response.consulta.lancamentosEvento.each(
            function(lancamentoEvento) {

                var option = document.createElement('option');
                option.value = lancamentoEvento.codigo;
                option.innerHTML = lancamentoEvento.ordem + " - " + lancamentoEvento.descricao;
                comboLancamento.appendChild(option);
            }
        );

        gridResultado.clearAll(true);
        response.consulta.lancamentos.each(
            function (lancamentoContabil, indice) {

                var lancamentosExecutados = lancamentoContabil.executados.split('|');
                var executados = [];
                lancamentosExecutados.each(
                    function (valores) {

                        var partes = valores.split('#');
                        if (Number(partes[0] != 1)) {
                            executados.push("<a href='#' onclick=\"excluir('"+partes[1]+"')\">"+partes[0]+"</a>");
                        } else {
                            executados.push("1");
                        }

                    }
                );


                gridResultado.addRow(
                    [
                        lancamentoContabil.codigo_lancamento,
                        js_formatar(lancamentoContabil.data, 'd'),
                        js_formatar(lancamentoContabil.valor, 'f'),
                        lancamentoContabil.total_executado,
                        executados.join(', ')
                    ]
                );

                if (Number(lancamentoContabil.total_executado) !== Number(response.consulta.totalLancamentosEvento)) {
                    gridResultado.aRows[indice].setClassName('textoErro');
                }
            }
        );
        gridResultado.renderRows();
    }

    function excluir(codigoExcluir) {

        if (!confirm('Confirma a exclusão do registro?')) {
            return false;
        }

        AjaxRequest.create(
            'con4_ajustemanualancamentos.RPC.php',
            {'exec': 'excluirRegistro', 'codigo' : codigoExcluir},
            function (response, erro) {

                alert(response.message);
                if (!erro) {
                    pesquisar();
                }
            }
        ).execute();
    }

    function pesquisaLancamento(mostrar) {

        var url = "func_conlancamlan.php?funcao_js=parent.preencheCampo|c70_codlan";
        if (!mostrar) {
            url = "func_conlancamlan.php?funcao_js=parent.confirmaCampo&pesquisa_chave="+document.querySelector('#c70_codlan').value;
        }

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conlancamlan', url, 'Pesquisar Lançamento Contábil', mostrar);
    }

    function preencheCampo(codigoLancamento) {
        document.querySelector('#c70_codlan').value = codigoLancamento;
        db_iframe_conlancamlan.hide();
    }

    function confirmaCampo(codigo, erro) {

        if (erro) {
            alert('Código '+document.querySelector('#c70_codlan').value+' do lançamento não encontrado.');
            document.querySelector('#c70_codlan').value = '';
            return false;
        }
    }

</script>
