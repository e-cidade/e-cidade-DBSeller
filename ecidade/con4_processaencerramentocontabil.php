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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type="text/css">

        .item-encerramento {
            margin-bottom: 10px !important;
            display: block;
        }
        .cor_verde {
            background-color:#d1f07c;
        }
    </style>
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    Antes de processar o encerrametno, certifique-se que foi executada a <b>Virada Anual dos RESTOS A PAGAR (item 13)</b>
</div>
<div class="container" style="width: 600px;">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend>Encerramento do Exercício</legend>
            <table>
                <tr>
                    <td>
                        <label class="bold" id="lbl_data" for="data">Data dos Lançamentos:</label>
                    </td>
                    <td>
                        <?php db_inputdata("data", '31', '12', db_getsession("DB_anousu"), true, 'text', 3); ?>
                    </td>
                </tr>
                <tr>
                    <td class="bold"><label for="executarConsistencia">Executar as consistências do encerramento antes do processamento?</label></td>
                    <td>
                        <select id="executarConsistencia">
                            <option value="0">Não</option>
                            <option value="1">Sim</option>
                        </select>
                    </td>
                </tr>
            </table>
            <br>
            <input name="processarVirada" type="button" id="processarVirada" value="Processar a Virada Anual dos RESTOS A PAGAR (item 13)"/>
        </fieldset>

        <fieldset style="margin-top: 10px;">
            <legend>Lançamentos de Encerramento de Execução Orçamentária da Despesa</legend>
        <div id="ctnGridDocumentoExecucaoOrcamentaria"></div>
        </fieldset>

        <input name="processarExecOrc" type="button" id="processarExecOrc" value="Processar Selecionados"  onclick="buscaLog('ExecucaoOrcamentaria')"/>
        <input name="cancelarExecOrc" type="button" id="cancelarTeste" value="Cancelar Selecionados" onclick="cancelar('ExecucaoOrcamentaria')"/>
        <fieldset style="margin-top: 20px;">
            <legend>Lançamentos de Encerramento do Exercício</legend>
        <div id="ctnGridDocumento"></div>
        </fieldset>

        <input name="processarTeste" type="button" id="processarTeste" value="Processar Selecionados"  onclick="processar('EncerramentoExercicio')"/>
        <input name="cancelarTeste" type="button" id="cancelarEncerramento" value="Cancelar Selecionados" onclick="cancelar('EncerramentoExercicio')"/>
        <input name="implantarSaldo" type="button" id="implantarSaldo" value="Implantar Saldo"/>

    </form>
</div>
<?php db_menu() ?>

<script type="text/javascript">

    var lBloquearLinhaEncerramentoExerc = true;

    const implantarSaldo = document.getElementById('implantarSaldo');
    const processarVirada = document.getElementById('processarVirada');
    const botaoProcessarExecOrc =  document.getElementById('processarExecOrc');
    const botaoCancelarTeste =  document.getElementById('cancelarTeste');
    const botaoProcessarTeste =  document.getElementById('processarTeste');
    const botaoImplantarSaldo =  document.getElementById('implantarSaldo');
    const botaoCancelarEncerramento =  document.getElementById('cancelarEncerramento');


    var oGridDadosExecOrc = new DBGrid('gridDadosExecOrc');
        oGridDadosExecOrc.nameInstance = 'oGridDadosExecOrc';
        oGridDadosExecOrc.setCheckbox(0);
        oGridDadosExecOrc.setCellWidth(new Array("20%", "80%"));
        oGridDadosExecOrc.setCellAlign(new Array('center', 'left'));
        oGridDadosExecOrc.setHeader(new Array("Documento", "Descrição"));
        oGridDadosExecOrc.show($('ctnGridDocumentoExecucaoOrcamentaria'));


    var oGridDados = new DBGrid('gridDados');
        oGridDados.nameInstance = 'oGridDados';
        oGridDados.setCheckbox(0);
        oGridDados.setCellWidth(new Array("20%", "80%"));
        oGridDados.setCellAlign(new Array('center', 'left'));
        oGridDados.setHeader(new Array("Documento", "Descrição"));
        oGridDados.show($('ctnGridDocumento'));

    function processar(sTipo) {
        let documentosSelecionados = null;

        switch(sTipo){
            case "ExecucaoOrcamentaria" :
                documentosSelecionados = getDocumentosSelecionadosExecOrc();
            break;
            case "EncerramentoExercicio" :
                documentosSelecionados = getDocumentosSelecionados();
            break;
        }

        if (documentosSelecionados.length == 0) {
            alert('Selecione no mínimo um documento para processamento.');
            return false;
        }

        var confirmacao = confirm('Você realmente deseja encerrar o exercício contábil? ');
        if (!confirmacao){
            return false;
        }

        bloquearBotoesTela(true);
        var oParametros = {
            exec: "processarEncerramento",
            encerramento: $F('executarConsistencia') == '1',
            documentos: documentosSelecionados,
            tipoProcessamento: sTipo,
        };

        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php', oParametros, function (oRetorno, lErro) {

            bloquearBotoesTela(false);
            if (oRetorno.erro) {
                alert(oRetorno.mensagem.urlDecode());
                return
            }

            alert(oRetorno.mensagem.urlDecode());
            if (oRetorno.encerrouTodosDocumentos ) {

                let mensagem = oRetorno.mensagem.urlDecode();
                mensagem += "\n\nDeseja encerrar o período contábil?";

                if (sTipo == "EncerramentoExercicio") {

                    if (confirm(mensagem)) {
                        encerrarPeriodoContabil();
                    }
                }

            }

            getDocumentosDoEncerramentoOrcamentaria(false);

        }).setMessage("Aguarde, efetuando encerramento...").execute();
    }

    const download = new DBDownload();

    function buscaLog(sTipo) {
        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php',
            {
               'exec' : 'buscarLog',
               'doc': [1025]
            },
            function (oRetorno, lErro) {
                if (oRetorno.erro == false && oRetorno.lRegistros == true) {

                    download.clear();
                    download.addFile(oRetorno.arquivoLog, "Log de Inconsistências_1025");
                    download.show();

                    if (!confirm("Existem Inconsistencia no DOC 1025, Continuar o Processamento ?")) {

                        return false;
                    }

                }

                processar(sTipo);

        }).setMessage("Aguarde, efetuando encerramento...").execute();
    }

    function encerrarPeriodoContabil() {
        bloquearBotoesTela(true);
        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php',
            { 'exec' : 'fecharPeriodoContabil' },
            function (oRetorno, lErro) {
                bloquearBotoesTela(false);
                alert(oRetorno.mensagem);

        }).setMessage("Aguarde, efetuando encerramento...").execute();
    }

    function cancelar(sTipo) {

        let documentosSelecionados = getDocumentosSelecionados();
        if (sTipo == 'ExecucaoOrcamentaria') {
            documentosSelecionados = getDocumentosSelecionadosExecOrc();
        }
        if (documentosSelecionados.length === 0) {
            alert("É necessário selecionar ao menos um documento para efetuar o cancelamento do encerramento.");
            return false;
        }

        var confirmacao = confirm('Você realmente cancelar o encerramento do exercício contábil?');
        if (!confirmacao){
            return false;
        }

        bloquearBotoesTela(true);
        var oParametros = {
            exec: "cancelarEncerramento",
            documentos: documentosSelecionados,
            sTipo : sTipo
        };

        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php', oParametros, function (oRetorno, lErro) {

            getDocumentosDoEncerramentoOrcamentaria(false);
            alert(oRetorno.mensagem.urlDecode());
            bloquearBotoesTela(false);

        }).setMessage("Aguarde, efetuando cancelamento...").execute();
    }

    function getDocumentosSelecionados(){

        var documentosSelecionados = [];
        var documentos = oGridDados.getSelection();
        for ( let documento of documentos ) {
            documentosSelecionados.push(documento[0]);
        }
        return documentosSelecionados;
    }

    function getDocumentosSelecionadosExecOrc(){

        var documentosSelecionados = [];
        var documentos = oGridDadosExecOrc.getSelection();
        for ( let documento of documentos ) {
            documentosSelecionados.push(documento[0]);
        }
        return documentosSelecionados;
    }

    function getDocumentosDoEncerramentoOrcamentaria(lBloquarLinha){

        let oParametros = {
            exec: "getDocumentosEncerramentoOrcamentaria",
        };

        lBloquearLinhaEncerramentoExerc = false;

        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php', oParametros , function (oRetorno, lErro) {
            oGridDadosExecOrc.clearAll(true);

            let lBloqueado = lBloquarLinha;
            // se algum item da grid de baixo estiver processado, não libera a primeira grid para desprocessar
            if (!oRetorno.lLiberarCancelamento) {
                lBloqueado = true;
            }

            oRetorno.documentos.each(
                function (documento, iInd) {

                    // se algum item da primeira gri nao estiver processado, nao liberamos o processamento de baixo
                    if (documento.processado == false) {
                        lBloquearLinhaEncerramentoExerc = true;
                    }



                    var aRow    = new Array();
                    aRow[0] = documento.codigo ;
                    aRow[1] = documento.descricao.urlDecode();

                    oGridDadosExecOrc.addRow(aRow, true, lBloqueado);

                    if (documento.processado) {
                        oGridDadosExecOrc.aRows[iInd].setClassName('cor_verde');
                    }

                });
                oGridDadosExecOrc.renderRows();


                getDocumentosDoEncerramento(lBloquearLinhaEncerramentoExerc);

        }).setMessage("Aguarde, pesquisando documentos...").execute();
    }

    function getDocumentosDoEncerramento(lBloquarLinha){
        let oParametros = {
            exec: "getDocumentosEncerramento",
        };

        new AjaxRequest('con4_processaencerramentoexercicio.RPC.php', oParametros , function (oRetorno, lErro) {
            oGridDados.clearAll(true);

            let lBloqueado = lBloquarLinha;
            let lChecked = lBloquarLinha;

            let liberaImplantaSaldo = true;

            oRetorno.documentos.each(function (documento, iInd) {

                var aRow = [];
                aRow[0] = documento.codigo ;
                aRow[1] = documento.descricao.urlDecode();
                oGridDados.addRow(aRow, true, lBloqueado);
                if (documento.processado) {
                    oGridDados.aRows[iInd].setClassName('cor_verde');
                }

                if (!documento.processado) {
                    liberaImplantaSaldo = false;
                }
            });

            oGridDados.renderRows();
            implantarSaldo.disabled = true;
            if (liberaImplantaSaldo) {
                implantarSaldo.disabled = false
            }
        }).setMessage("Aguarde, pesquisando documentos...").execute();
    }

    async function validaViradaRps() {
        lBloquearLinhaEncerramentoExerc = false;
        botaoProcessarExecOrc.disabled = true;
        botaoCancelarTeste.disabled = true;
        botaoProcessarTeste.disabled = true;
        botaoImplantarSaldo.disabled = true;
        botaoCancelarEncerramento.disabled = true;
        
        js_divCarregando('Aguarde, verificando virada anual de Restos a Pagar....', 'loading_message');
        let response = await CurrentWindow.axios.get(
            "v4/api/financeiro/contabilidade/procedimento/escrituracao-contabil/virada-rp",
        ).then(response => {
            if (response.data.data.processado == true) {
                getDocumentosDoEncerramentoOrcamentaria(false);
                botaoProcessarExecOrc.disabled = false;
                botaoCancelarTeste.disabled = false;
                bloquearBotoesTela(false);
            }
        });
        js_removeObj('loading_message');
    }

    processarVirada.addEventListener('click', async() => {
        js_divCarregando('Aguarde, executando virada anual de Restos a Pagar....', 'loading_message');
        let response = await CurrentWindow.axios.post(
            "v4/api/financeiro/contabilidade/procedimento/escrituracao-contabil/virada-rp",
        ).then(response => {
            if (response.data.error == false) {
                alert(response.data.message);
                getDocumentosDoEncerramentoOrcamentaria(false);
                botaoProcessarExecOrc.disabled = false;
                botaoCancelarTeste.disabled = false;
                bloquearBotoesTela(false);
            } else {
                alert("Erro ao efetuar a virada dos Restos a Pagar. Por Favor, entre em contato com o suporte.");
            }
        });
        js_removeObj('loading_message');
    });

    validaViradaRps();

    implantarSaldo.addEventListener('click', async () => {
        js_divCarregando('Processando implantação do saldo.', 'loading_message');
        let response = await CurrentWindow.axios.post(
            "v4/api/financeiro/contabilidade/procedimento/escrituracao-contabil/implantar-saldo-inicial",
        ).then(response => {
            bloquearBotoesTela(false);
            alert(response.data.message)
        });
        js_removeObj('loading_message');
    });

</script>
</body>
</html>
