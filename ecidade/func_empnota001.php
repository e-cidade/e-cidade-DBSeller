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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_conlancamnota_classe.php"));
include(modification("classes/db_conlancamdoc_classe.php"));
include(modification("classes/db_empelemento_classe.php"));
include(modification("classes/db_empempenho_classe.php"));
include(modification("classes/db_empnotaele_classe.php"));
include(modification("classes/db_empnota_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require(modification("libs/db_utils.php"));

$oGet            = db_utils::postMemory($_GET);
$clconlancamnota = new cl_conlancamnota;
$clconlancamdoc  = new cl_conlancamdoc;
$clempelemento   = new cl_empelemento;
$clempempenho    = new cl_empempenho;
$clempnotaele    = new cl_empnotaele;
$clempnota       = new cl_empnota;

$clempnotaele->rotulo->label();
$clempnota->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("e69_data");
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script src="public/js/app.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
</style>
</head>
<body bgcolor=#CCCCCC  bgcolor="#CCCCCC" >
<fieldset>
<table  border="0" cellspacing="0" cellpadding="0" align="center">
<?php
  $sSqlNotasLiq  = "select e69_codnota, ";
  $sSqlNotasLiq .= "       e69_numero,  ";
  $sSqlNotasLiq .= "       e69_dtnota,  ";
  $sSqlNotasLiq .= "       e69_dtinclusao,";
  $sSqlNotasLiq .= "       e69_dtservidor,";
  $sSqlNotasLiq .= "       e70_valor,   ";
  $sSqlNotasLiq .= "       e70_vlrliq,  ";
  $sSqlNotasLiq .= "       e70_vlranu,  ";
  $sSqlNotasLiq .= "       e53_vlrpag   ";
  $sSqlNotasLiq .= "  from empnota      ";
  $sSqlNotasLiq .= "       inner join empnotaele   on e70_codnota = e69_codnota ";
  $sSqlNotasLiq .= "       left  join pagordemnota on e70_codnota = e71_codnota ";
  $sSqlNotasLiq .= "                               and e71_anulado is false      ";
  $sSqlNotasLiq .= "       left  join pagordemele  on e71_codord  = e53_codord  ";
  $sSqlNotasLiq .= " where e69_numemp = {$oGet->e60_numemp}";
  $sSqlNotasLiq .= " order by  e69_dtnota";
  $rsNotasLiq    = $clempnota->sql_record($sSqlNotasLiq);
  db_lovrot($sSqlNotasLiq,15,"()","","js_mostranota|e69_codnota", "", "NoMe",
      $variaveis_repassa = array(), $automatico = true, $totalizacao = array(), $acao = null, 'func_empnota001.php');
?>
</table>
</fieldset>
</body>
</html>
<script>
function js_mostranota(codnota){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_nota','emp2_consultanotas002.php?e69_codnota='+codnota,'Dados da Nota de Liquidacao',true)
}

var arquivos;

function visualizarDocumentos(){
    const idsArquivos = arquivos.data.map(function (arquivo) {
        return arquivo['codigo_arquivo'];
    });
    var visualizarEmOutraJanela = "<?php echo $visualizarEmOutraJanela; ?>";
    if (visualizarEmOutraJanela) {
        window.open(`db_visualizador_documentos.php?ids=${idsArquivos}`);
    } else {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_visualizador_imagens', `db_visualizador_documentos.php?ids=${idsArquivos}`, 'Visualizador de documentos', true);
    }
}

async function setArquivos(codnota) {
    arquivos = await getArquivos(codnota);
    if(arquivos.data.length > 0){
        visualizarDocumentos();
    }else{
        alert('Nenhum anexo para esta nota.');
        return false;
    }
}


async function getArquivos(codnota) {
    const apiUrl = await getEcidadeInfo();
    const resp = await window.axios.post(
        `${apiUrl}patrimonial/protocolo/processo/processodocumento/documentosPorNota/${codnota}`
    );
    return resp.data;
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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
