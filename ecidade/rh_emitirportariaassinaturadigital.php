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
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_utils.php");
require_once modification("dbforms/db_funcoes.php");

?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
    <script language="JavaScript" type="text/javascript"
            src="scripts/widgets/datagrid/plugins/DBHint.plugin.js"></script>
</head>

<body class='body-default'>
<div class='container' style='width:400px;'>
    <form action="post" name='form1'>
        <fieldset>
            <legend>Impressão da Portaria Assinada</legend>
            <table class='form-container'>
                <tr>
                    <td title="Portaria">
                        <?php db_ancora("Portaria:", "js_pesquisaPortariaIncial();", 1, ""); ?>
                        <?php db_input("portaria", 15, '', true, "text", 1, ""); ?>
                    </td>

                </tr>
            </table>
        </fieldset>
    </form>
    <input type="button" name="imprimir" id="imprimir" value="Imprimir" onClick="js_imprimir()">
</div>

</body>
<script>
    function js_pesquisaPortariaIncial() {
        js_OpenJanelaIframe('', 'db_iframe_portariai', 'func_portaria.php?funcao_js=parent.js_mostraportariai|h31_numero', 'Pesquisa', true);
    }

    function js_mostraportariai(chave) {
        document.form1.portaria.value = chave;
        db_iframe_portariai.hide();
    }

    function js_imprimir() {

        const parametros = new FormData();

        parametros.append('exec', 'emitirPortariaAssinada');
        parametros.append('numero_portaria', $F('portaria'));

        var rpc = 'rh_processaassinaturadigital.RPC.php';

        fetch(rpc, {
            method: 'POST',
            body: parametros,
            credentials: 'include',
        }).then(function (response) {
            return response.json();
        }).then(function (response) {
            if (response.erro) {
                return alert(response.mensagem);
            }

            var sCaminhoDownloadArquivo = response.file.urlDecode();

            window.open("db_download.php?arquivo=" + sCaminhoDownloadArquivo);

        });
    }

</script>

</html>