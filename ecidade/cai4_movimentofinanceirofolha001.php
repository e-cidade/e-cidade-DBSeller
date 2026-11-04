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
    <script  type="text/javascript" src="scripts/widgets/dbcomboBox.widget.js"></script>

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
        <legend class="bold">Movimento financerio da folha</legend>
        <table>
            <tr>
                <td nowrap title="" >
                    <b>Competência:</b>
                </td>
                <td nowrap id="">
                <?php db_input('ano', 10, 1, true, 'text', 1)?> /
                <?php db_input('mes', 5, 1, true, 'text', 1)?>
                </td>
            </tr>
            <tr>
                <td nowrap title="" >
                    <b>Tipo Folha:</b>
                </td>
                <td nowrap id="ctnCboTipoFolha">
                </td>
            </tr>
            <tr>
                <td nowrap title="" >
                    <b>Evento:</b>
                </td>
                <td nowrap id="ctnCboEvento">
                </td>
            </tr>
            <tr>
                <td nowrap title="" >
                    <b>Ação:</b>
                </td>
                <td nowrap id="ctnCboAcao">
                </td>
            </tr>


        </table>
    </fieldset>
    <p>
        <input type="button" id="btnPesquisa" value="Pesquisar" onclick="pesquisar()" />
    </p>
</div>
<div class="container" style="width: 90%">

    <fieldset>
        <legend class="bold">Movimento da folha por recurso</legend>
        <div id="ctnResultadoPesquisa"></div>

        <div class="textoErro" style="width: 100%; text-align: right; padding-right: 8px; border: 1px solid; background-color: #FFFFFF ">
            <p class="bold" id="totalizadores"></p>
        </div>

    </fieldset>
    <p>
        <input type="button" value="Processar Transferências." onclick="salvarRegistros()" />
    </p>
</div>

</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>


<script>
    var rpc = "cai4_movimentofinanceirofolha.RPC.php";

    var gridResultado = new DBGrid('gridResultado');
    gridResultado.nameInstance = 'gridResultado';
    gridResultado.setCheckbox(0);
    gridResultado.setHeader(['Recurso', 'Descrição', 'Reduzido', 'Conta', 'Valor Total', 'Valor Transf.']);
    gridResultado.setCellWidth(['8%', '30%', '8%', '30%', '12%', '12%']);
    gridResultado.setCellAlign(['center', 'left', 'center', 'left', 'right', 'right']);
    gridResultado.setHeight(350);
    gridResultado.show($('ctnResultadoPesquisa'));

    oCboTipoFolha = new DBComboBox("cboTipoFolha", "oCboTipoFolha", null, "100%");
    oCboTipoFolha.addItem("", "Selecione");
    oCboTipoFolha.addItem("salario",      "Salario");
    oCboTipoFolha.addItem("complementar", "Complementar");
    oCboTipoFolha.addItem("ferias",       "Férias");
    oCboTipoFolha.addItem("decimo",       "Décimo terceiro");
    oCboTipoFolha.show($('ctnCboTipoFolha'));

    oCboEvento = new DBComboBox("cboEvento", "oCboEvento", null, "100%");
    oCboEvento.addItem("", "Selecione");
    oCboEvento.addItem("proprio",   "Transferênca Bancária p/ Rec. Próprio");
    oCboEvento.addItem("vinculado", "Transferênca Bancária p/ Rec. Vinculado");
    oCboEvento.show($('ctnCboEvento'));

    oCboAcao = new DBComboBox("cboAcao", "oCboAcao", null, "100%");
    oCboAcao.addItem("", "Selecione");
    oCboAcao.addItem("financeiro", "Movimentar valor financeiro");
    oCboAcao.addItem("contabil",   "Movimentar apenas registro orçamentario/contábil");
    oCboAcao.show($('ctnCboAcao'));


    function salvarRegistros() {

        var registrosSelecionados = gridResultado.getSelection();
        if (registrosSelecionados.length === 0) {
             return alert("Não foram selecionados registros para processar.");
        }
        if (oCboTipoFolha.getValue() == "") {
            return alert("Por favor selecionte o tipo de folha.");
        }
        if (oCboEvento.getValue() == "") {
            return alert("Por favor selecionte o evento a ser realizado.");
        }
        if (oCboAcao.getValue() == "") {
            return alert("Por favor selecionte a ação a ser executada.");
        }

        var sTipoFolha = oCboTipoFolha.getValue();
        var sAcao      = oCboAcao.getValue();
        var sEvento    = oCboEvento.getValue();
        var sAno = $('ano').value;
        var sMes = $('mes').value;

        if (sAno == "" || sMes == "") {
            return alert("Por favor, informe a competência a ser processada.");
        }

        if (sTipoFolha = "") {
            return alert("Por favor, selecione o tipo de folha a ser processado.");
        }
        var mensagem = "Confirma a inclusão das transferências para os registros selecionados?";
        if (!confirm(mensagem)) {
            return false;
        }

        var parametros = {
            'exec' : 'processar',
            'tipofolha' : sTipoFolha,
            'acao' :   sAcao,
            'evento' : sEvento,
            'ano' : sAno,
            'mes' : sMes,
            'transferencias': registrosSelecionados
        };


        AjaxRequest.create(
            rpc,
            parametros,
            function (response) {
                alert(response.message);
            }
        ).execute();
    }


    function pesquisar() {

        var sTipoFolha = oCboTipoFolha.getValue();
        var sAno = $F('ano');
        var sMes = $F('mes');

        if (sTipoFolha == "" || sAno == "" || sMes == "") {
            return alert("Para pesquisar, informe o tipo de folha e a competência.");
        }

        var parametros = {
            'exec' : 'consultar',
            'tipofolha' : sTipoFolha,
            'ano' : sAno,
            'mes' : sMes,
        };

        AjaxRequest.create(
            rpc,
            parametros,
            carregarRegistros
        ).setMessage('Aguarde, pesquisando informações...').execute();
    }


    function carregarRegistros(response, erro) {

        if (erro) {
            return alert(response.message);
        }
        var totalGeral = 0;
        gridResultado.clearAll(true);
        response.transferencias.each(
            function (transferencia, indice) {

                lChecked   = false;
                lDisabled  = false;
                sDisableInput  = "";
                if (transferencia.livre == 'true'){
                    sDisableInput  = "disabled";
                    lChecked   = true;
                    lDisabled  = true;
                }

                totalGeral = (new Number(totalGeral) + new Number(transferencia.valor) );

                valorGrid = js_formatar(transferencia.valor, 'f');
                sInput = "<input name='input_"+transferencia.recurso+"' size='13' type='text' id='input_"+transferencia.recurso+"' value='"+valorGrid+"' "+sDisableInput+" style='text-align: right'>";
                gridResultado.addRow(
                    [
                        transferencia.recurso,
                        transferencia.descricao_recurso,
                        transferencia.conta,
                        transferencia.descricao_conta,
                        valorGrid,
                        sInput
                    ],
                    false,
                    lChecked,
                    lDisabled
                );
            }
        );
        $('totalizadores').innerHTML = "Total de transferências: "+js_formatar(totalGeral.valueOf(),"f");
        gridResultado.renderRows();
    }

</script>
