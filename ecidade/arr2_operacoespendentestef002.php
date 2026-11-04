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

    require_once(modification("libs/db_stdlib.php"));
    require_once(modification("libs/db_conecta.php"));
    require_once(modification("libs/db_sessoes.php"));
    require_once(modification("libs/db_usuariosonline.php"));
    require_once(modification("dbforms/db_funcoes.php"));
    require_once(modification("libs/db_app.utils.php"));
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">

    <style>
        #cntPendentes {
            width: 600px;
        }
    </style>
</head>

<body>
<div class="container">
    <form name="form1">
        <input type="hidden">
        <fieldset>
            <legend>Operações Pendentes TEF</legend>
            <table class="form-container">
                <tr>
                    <td style="width: 70px;">
                        <strong>Periodo:</strong>
                    </td>
                    <td>
                        <?php db_inputdata('dataInicio',"","","",true,'text',1) ?>
                        <b>até</b>
                        <?php db_inputdata('dataFim',"","","",true,'text',1) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                            db_ancora("<strong>Terminal:</strong>", "js_pesquisaTerminal(true);", 4);
                        ?>
                    </td>
                    <td>
                        <?php
                            db_input("terminal", 5, false, true, "text", 1, "onchange='js_pesquisaTerminal(false);'", "", "white");
                            db_input("descricaoTerminal", 30, false, true, "text", 5, "", "", "", "width: 191px;");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="filtrar" id="filtrar" type="button" onclick="js_buscarPendentes();" value="Filtrar">
    </form>
</div>
<span class="container" style="color: red !important;">As ações aqui realizadas não poderão ser desfeitas via manutenção!</span>
<div class="container" id="cntPendentes"></div>
<?php db_menu(); ?>
</body>

</html>
<script>
    const sApiUrl = "<?= ECIDADE_REQUEST_PATH ?>v4/api/";
    const iModulo = "<?= db_getsession('DB_modulo') ?>";
    const iMenuAcessado = "<?= db_getsession('DB_itemmenu_acessado') ?>";
    const iCodigoInstituicao = "<?= db_getsession('DB_instit') ?>";
    const iCodigoDepartamento = "<?= db_getsession('DB_coddepto') ?>";
    const iCodigoUsuario = "<?= db_getsession('DB_id_usuario') ?>";
    const sDataUsu = "<?= db_getsession('DB_datausu') ?>";

    var oGridPendentes = new DBGrid('gridPendentes');
    var aHeaders   = ["Operação", "Cartão", "Valor (R$)", "Parcelas", "AUT", "Ação"];
    var aCellWidth = ["30%", "20%", "15%", "10%", "10%", "15%"];
    var aCellAlign = ["left", "center", "right", "center", "center", "center"];

    oGridPendentes.nameInstance = 'oGridPendentes';
    oGridPendentes.setCellWidth(aCellWidth);
    oGridPendentes.setCellAlign(aCellAlign);
    oGridPendentes.setHeader(aHeaders);
    oGridPendentes.show($('cntPendentes'));

    function js_pesquisaTerminal(mostra)
    {
        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cfautent','func_cfautent.php?funcao_js=parent.js_mostraTerminal|k11_id|k11_local&tef=true','Terminal',true);
        }else{
            if (document.form1.terminal.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cfautent','func_cfautent.php?pesquisa_chave='+document.form1.terminal.value+'&funcao_js=parent.js_mostraTerminal1&tef=true','Terminal',false);
            }
        }
    }

    function js_mostraTerminal(chave1, chave2) {
        document.form1.terminal.value = chave1;
        document.form1.descricaoTerminal.value = chave2;
        db_iframe_cfautent.hide();
    }

    function js_mostraTerminal1(chave, erro) {
        document.form1.descricaoTerminal.value = chave;

        if (erro == true) {
            document.form1.terminal.focus();
            document.form1.terminal.value = "";
        }
    }

    function js_validaCampos() {
        const obj = document.form1;

        if (obj.dataInicio.value == "") {
            alert("Data inicial deve ser preenchida.");
            return false;
        }

        if (obj.dataFim.value == "") {
            alert("Data final deve ser preenchida");
            return false;
        }

        const dataInicio = new Date(obj.dataInicio.value);
        const dataFim = new Date(obj.dataFim.value);

        if (dataInicio > dataFim) {
            alert("A data inicial não pode ser maior que a final.");
            return false;
        }

        return true;
    }

    function js_buscarPendentes() {
        if (!js_validaCampos()) {
            return;
        }

        let dataInicio = document.getElementById("dataInicio").value;
        dataInicio = dataInicio.split("/");
        dataInicio = `${dataInicio[2]}-${dataInicio[1]}-${dataInicio[0]}`;

        let dataFim = document.getElementById("dataFim").value;
        dataFim = dataFim.split("/");
        dataFim = `${dataFim[2]}-${dataFim[1]}-${dataFim[0]}`;

        const data = new FormData();
        data.append('dataInicio', dataInicio);
        data.append('dataFim', dataFim);
        data.append('terminal', document.getElementById("terminal").value);
        data.append('DB_modulo', iModulo);
        data.append('DB_itemmenu_acessado', iMenuAcessado);

        HttpClient.post(`${sApiUrl}tributario/arrecadacao/tef/operacoes-pendentes`, {body: data}).then(response => {
            if (response.error) {
                alert(response.message);
                return false;
            }

            js_carregaGrid(response.data.pendentes);
        });
    }

    function js_carregaGrid(aPendentes) {
        oGridPendentes.clearAll(true);

        aPendentes.forEach((oOperacao, key) => {
            let aLinha = [];

            aLinha.push(oOperacao.operacoesTef.k195_descricao);
            aLinha.push(oOperacao.k198_cartao);
            aLinha.push(Number(oOperacao.k198_valor).toLocaleString('pt-BR', {minimumFractionDigits: 2}));
            aLinha.push(oOperacao.k198_parcela ? oOperacao.k198_parcela : "");
            aLinha.push(oOperacao.k198_codigoaprovacao);

            const oInput = document.createElement("input");
            oInput.setAttribute("type", "button");
            oInput.setAttribute("id", `input_${oOperacao.k198_sequencial}`);

            let iOperacao = 1;

            if (!oOperacao.k198_concluidobaixabanco) {
                oInput.setAttribute("value", "Desfazer");
            } else {
                oInput.setAttribute("value", "Confirmar");
                iOperacao = 2;
            }

            oInput.setAttribute("onclick", `js_ajustarSituacao(${oOperacao.k198_sequencial}, ${iOperacao})`);

            aLinha.push(oInput.outerHTML);

            oGridPendentes.addRow(aLinha);
        });

        oGridPendentes.renderRows();
    }

    function js_ajustarSituacao(sequencial, opcao) {
        const sAcao = parseInt(opcao) == 2 ? "confirmar" : "desfazer";

        if (!confirm(`Realmente deseja ${sAcao} esta operação?`)) {
            return;
        }

        const sRota = parseInt(opcao) == 2 ? "confirmar-operacao" : "desfazer-operacao";

        const data = new FormData();
        data.append('sequencial', sequencial);
        data.append('DB_instit', iCodigoInstituicao);
        data.append('DB_coddepto', iCodigoDepartamento);
        data.append('DB_id_usuario', iCodigoUsuario);
        data.append('DB_datausu', sDataUsu);

        HttpClient.post(`${sApiUrl}tributario/arrecadacao/tef/${sRota}`, {body: data}).then(response => {
            if (response.error) {
                alert(response.message);
                return false;
            }

            const oInput = document.getElementById(`input_${sequencial}`);
            const oParent = oInput.parentNode;

            oParent.removeChild(oInput);
            oParent.innerHTML = parseInt(opcao) == 2 ? "Confirmado" : "Desfeito";
        });
    }
</script>
