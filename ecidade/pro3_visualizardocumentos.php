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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oGet = db_utils::postMemory($_GET);
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();
?>

<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="body-default">
  	<div class="container">
  		<fieldset>
  			<legend>Visualizar documentos</legend>
  			<input type="button" name="visualizarDocumentos" id="visualizarDocumentos" value="Visualizar Documentos" \>
  		</fieldset>
  	</div>
  </body>
</html>
<script>
    var tela_documento = "<?php echo $oGet->tela_documento; ?>";
    var visualizarEmOutraJanela = "<?php echo $visualizarEmOutraJanela; ?>";
    $('visualizarDocumentos').onclick = function () {

	var codigoProcesso = '<?php echo $oGet->codigo_processo;?>';

	if (!codigoProcesso) {
		alert("Código do processo não informado.");
	}

	getEcidadeInfo().then(apiUrl => {
    const data = new FormData();
    data.append('codigoProcesso', codigoProcesso);

    HttpClient.post(`${apiUrl}patrimonial/protocolo/processo/processodocumento/documentosPorProcesso`, {body: data}).then(response => {
        if(response.error == true){
        	alert(response.message);
            return;
        }

        var codigosEStorage = [];

        response.data.forEach((documento) => {
        	codigosEStorage.push(documento.id_estorage);
        });


        if (codigosEStorage.length == 0) {
        	alert("Nenhum documento encontrado para o processo.");
        	return false;
        }

            if (visualizarEmOutraJanela == "true") {
                window.open(`db_visualizador_documentos.php?ids=${codigosEStorage}`);
            } else {
                if (tela_documento) {
                    var width = CurrentWindow.corpo.innerWidth - 100;
                    var height = CurrentWindow.corpo.innerHeight - 100;
                    js_OpenJanelaIframe('window.parent', 'db_visualizador_imagens', `db_visualizador_documentos.php?ids=${codigosEStorage}`, 'Visualizador de documentos', true, 0, 0, width, height);
                } else {
                    const url = CurrentWindow.corpo.CurrentWindow.ECIDADE_REQUEST_PATH;
                    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_visualizador_imagens', `${url}db_visualizador_documentos.php?ids=${codigosEStorage}`, 'Visualizador de documentos', true);
                }
            }
        });
    });
}


function getEcidadeInfo() {

    const data = new FormData();
    data.append('acao', 'info');
    return HttpClient.post('con4_ecidadeinfo.RPC.php', { body: data }).then(function (response) {
        if (response.erro) {
            alert(response.mensagem);
            return;
        }

        return response.url + 'v4/api/';
    });

}

</script>
