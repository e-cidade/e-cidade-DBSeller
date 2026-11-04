<?php
/**
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oGet = db_utils::postMemory($_GET);
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type='text/css'>
        .valores {
            background-color: #FFFFFF
        }
    </style>
</head>
<body>
<div class="container">
    <fieldset style="width: 600px;">
        <legend class="bold">Dados do Arquivamento</legend>
        <table>
            <tr>
                <td><b><label for="data">Data:</label></b></td>
                <td class="valores" id="data" style="width: 100%"></td>
            </tr>
            <tr>
                <td><b><label for="usuario">Usuário:</label></b></td>
                <td class="valores" id="usuario"></td>
            </tr>
        </table>
        <fieldset>
            <legend><b><label for="historico">Histórico</label></b></legend>
            <textarea id="historico" style="width: 100%; height: 100px" readonly></textarea>
        </fieldset>
    </fieldset>
</div>
</body>
</html>

<script>
    const oGet = js_urlToObject();

    new AjaxRequest(
        'prot4_processoarquivamento.RPC.php',
        {exec : 'getDadosArquivamento', codigo_arquivamento : oGet.codigo_arquivamento},
        function (oRetorno, lErro) {

            if (lErro) {
                return alert(oRetorno.message.urlDecode());
            }

            $('data').innerHTML      = oRetorno.data;
            $('usuario').innerHTML   = oRetorno.usuario.urlDecode();
            $('historico').innerHTML = oRetorno.historico.urlDecode();
        }
    ).setMessage('Aguarde, buscando informações...').execute();
</script>
