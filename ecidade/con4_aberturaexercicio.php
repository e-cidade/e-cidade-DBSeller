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
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="alert alert-primary text-left" role="alert">
    <h3>Sobre os Documentos 2032 e 2033</h3>
    Antes de processar esses documentos, certifique-se que foi executada a <b>Virada Anual dos RESTOS A PAGAR (item 13)</b>
</div>
<div class="container">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend>Abertura do Exercício</legend>
            <table>
                <tr>
                    <td>
                        <label class="bold" id="lbl_data" for="data">Data dos Lançamentos:</label>
                    </td>
                    <td>
                        <?php db_inputdata("data", '01', '01', db_getsession("DB_anousu"), true, 'text', 1); ?>
                    </td>
                </tr>
            </table>
            <fieldset class=" separator " style="width:  500px;">
                <legend>Selecione os documentos</legend>
                <div id='ctnGridHabilidade'></div>
                <div id='ctnGrid'></div>
            </fieldset>
        </fieldset>

        <input name="processarTeste" type="button" id="processarTeste" value="Processar Abertura"
               onclick="processar()"/>
        <input name="cancelarTeste" type="button" id="cancelarTeste" value="Cancelar Abertura"
               onclick="cancelar()"/>
    </form>
</div>

<script type="text/javascript">

    const collectionDocumentos = new Collection().setId("codigo");

    var gridDocumentos = new DatagridCollection(collectionDocumentos).configure({order: false, height: 100});
    collectionDocumentos.add(
        [
            {"codigo": 2001, "descricao": 'ABERTURA DO ORÇAMENTO DESPESA', "processado": false},
            {"codigo": 2003, "descricao": 'ABERTURA DO ORÇAMENTO RECEITA', "processado": false},
            {"codigo": 2021, "descricao": 'RECURSOS DO EXERCÍCIO ANTERIOR - CONTROLES', "processado": false},
            {"codigo": 2032, "descricao": 'TRANSFERENCIA DE SALDOS DE RPNP INSCRITOS NO EX. ANTERIOR', "processado": false},
            {"codigo": 2033, "descricao": 'TRANSFERENCIA DE SALDOS DE RPP INSCRITOS NO EX. ANTERIOR', "processado": false},
            {"codigo": 2034, "descricao": 'TRANSFERENCIA DE SALDOS DE RPNP INSCRITOS A LIQUIDAR', "processado": false},
            {"codigo": 2035, "descricao": 'TRANSFERENCIA DE SALDOS DE RPNP INSCRITOS EM LIQUIDAÇÃO', "processado": false}
        ]
    );
    gridDocumentos.grid.setCheckbox(0);
    gridDocumentos.addColumn("codigo", {label : "Código", align : "center", width : "10%"});
    gridDocumentos.addColumn("descricao", {label : "Documento", align : "left", width : "85%" });

    gridDocumentos.setEvent('onafterrenderrows', function() {
        const linhasGrid = gridDocumentos.getGrid().aRows;
        for (let linha of linhasGrid) {
            let data = collectionDocumentos.get(linha.aCells[1].content)
            if (data.processado) {
                document.getElementById(linha.sId).style.backgroundColor = '#d1f07c'
            }
        }
    });

    gridDocumentos.show($('ctnGrid'));

    console.log(collectionDocumentos.get())

    function bloqueiaItensProcessados(documentosEncerrados) {

        if (documentosEncerrados.length > 0) {
            for (let documento of documentosEncerrados) {
                collectionDocumentos.get(documento).processado = true;
            }
            gridDocumentos.reload()
        }
    }

    function validaDocumentos() {
        collectionDocumentos.get().each(linha => linha.processado = false)
        new AjaxRequest('con4_processaaberturaexercicio.RPC.php', {exec: "validaSituacaoDocumentos", data: $F("data")},
            function (retorno, erro) {
                bloqueiaItensProcessados(retorno.documentosEncerrados);
            }
        ).setMessage("Aguarde, efetuando abertura...").execute();
    }

    validaDocumentos();

    function getDoccumentosSelecionados() {
        const documentos = gridDocumentos.getGrid().getSelection();
        if (documentos.length === 0) {
            throw 'Selecione ao menos um documento.'
        }

        return documentosSelecionados = documentos.map(linha => {
            return linha[0]
        });
    }

    function processar() {

        try {
            const documentosSelecionados = getDoccumentosSelecionados();
            if (!confirm('Você realmente deseja abrir o exercício contábil? ')){
                return false;
            }
            bloquearBotoesTela(true);
            var oParametros = {
                exec: "processarAbertura",
                encerramento: false,
                documentos: documentosSelecionados,
                data: $F("data"),
            };

            new AjaxRequest('con4_processaaberturaexercicio.RPC.php', oParametros, function (oRetorno, lErro) {
                alert(oRetorno.mensagem.urlDecode());
                bloquearBotoesTela(false);
                if (!lErro) {
                    bloqueiaItensProcessados(oRetorno.documentosEncerrados);
                }
            }).setMessage("Aguarde, efetuando abertura...").execute();
        } catch (e) {
            alert(e);
            return
        }
    }

    function cancelar() {

        try {
            const documentosSelecionados = getDoccumentosSelecionados();
            if (!confirm('Você realmente deseja cancelar a abertura do exercício contábil? ')){
                return false;
            }
            bloquearBotoesTela(true);
            var oParametros = {
                exec: "cancelarAbertura",
                data: $F("data"),
                documentos: documentosSelecionados,
            };

            new AjaxRequest('con4_processaaberturaexercicio.RPC.php', oParametros, function (oRetorno, lErro) {
                alert(oRetorno.mensagem.urlDecode());
                bloquearBotoesTela(false);
                validaDocumentos();
            }).setMessage("Aguarde, efetuando cancelamento...").execute();
        } catch (e) {
            alert(e);
            return
        }
    }
</script>
</body>

</html>
<?php db_menu(); ?>
