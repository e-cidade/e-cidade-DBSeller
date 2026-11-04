<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clOrcUnidade = new cl_orcunidade;
$clDBConfig = new cl_db_config();

$unidades = [];

$rsInstit = $clDBConfig->sql_record($clDBConfig->sql_query_file(db_getsession("DB_instit"), "nomeinst"));
$oLinhaInstit = db_utils::fieldsMemory($rsInstit, 0);
$id = "00.".db_getsession("DB_instit");
$conteudo = "00.".str_pad(db_getsession("DB_instit"), 2, "0", STR_PAD_LEFT)." - ".$oLinhaInstit->nomeinst;
$unidades[$id] = $conteudo;

$sqlOrcUnidade = $clOrcUnidade->sql_query_file(
    null,
    null,
    null,
    "o41_orgao as orgao, o41_unidade as unidade, o41_descr",
    "o41_orgao, o41_unidade",
    "o41_instit = ".db_getsession("DB_instit")." and o41_anousu = ".db_getsession("DB_anousu")
    );
$rsUnidades = $clOrcUnidade->sql_record($sqlOrcUnidade);

for ($i = 0; $i < $clOrcUnidade->numrows; $i++) {
    $oLinhaUnidade = db_utils::fieldsMemory($rsUnidades, $i);
    
    $id = $oLinhaUnidade->orgao.".".$oLinhaUnidade->unidade;
    $conteudo  = $oLinhaUnidade->orgao.".".str_pad($oLinhaUnidade->unidade, 2, "0", STR_PAD_LEFT);
    $conteudo .= " - ".$oLinhaUnidade->o41_descr;
    $unidades[$id] = $conteudo;
}

$periodo = array("6"  => " 1º Bimestre",
    "7"  => " 2º Bimestre",
    "8"  => " 3º Bimestre",
    "9"  => " 4º Bimestre",
    "10" => " 5º Bimestre",
    "11" => " 6º Bimestre");
?>

<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
  <fieldset style="width:600px">
    <legend>Gerar arquivo Plano Plurianual - PPA (XML)</legend>
      <table class="form-container">
        <tr>
          <td>Unidade:</td>
          <td colspan="5">
            <?php                  
              db_select("orgaoUnidade", $unidades, true, 2, 'onchange="getDadosTCE()"');
            ?>
          </td>
        </tr>
        <tr>
          <td>Cód. TCE:</td>
          <td><input type="text" size="4" id='codUnidade' value='' name="codUnidade" /></td>
          <td>Cód. TCE (XML):</td>
          <td><input type="text" size="4" id='codUnidadeXml' value='' name="codUnidadeXml" /></td>              
          <td>Nome TCE:</td>
          <td><input type="text" size="40" id='nomeUnidade' value='' name="nomeUnidade" /></td>
        </tr>
      </table>
  </fieldset>
  <input type="button" id='processar' value='Processar' name='Processar' onclick="processarGeracaoArquivo();" />
</div>
</body>
</html>
<?php db_menu();?>

<script type="text/javascript">
const sURL = "con4_processarSIAI.RPC.php";

window.onload = function() {
  getDadosTCE();
}

function getDadosTCE() {
  var oParam           = new Object();
  oParam.exec          = "getDadosTCE";
  if ($('orgaoUnidade')) {
    var codigo_unidade = $F('orgaoUnidade').split(".");
    oParam.orgao   = codigo_unidade[0];
    oParam.unidade = codigo_unidade[1];
  }

  js_divCarregando('Aguarde, obtendo os dados', 'msgBox');
  var oAjax = new Ajax.Request(sURL,
    {
      method:'post',
      parameters:'json='+Object.toJSON(oParam),
      onComplete:retornoDadosTCE
    });
}

function retornoDadosTCE(oAjax) {
  js_removeObj('msgBox');
  var oRetorno = eval("("+oAjax.responseText+")");
  if (oRetorno.status == 1) {
    $('codUnidade').value  = oRetorno.codigoOrgao;
    $('codUnidadeXml').value  = oRetorno.codigoOrgaoXml;      
    $('nomeUnidade').value = oRetorno.nomeUnidade;
  } else {
      alert(oRetorno.msg);
  }

  if ($F('codUnidade') == "" || $F('codUnidadeXml') == "") {
      alert("Código do TCE para a unidade informada não encontrado, por favor preencha manualmente");
      $('codUnidade').focus();
      return false;
  }

  if ($F('nomeUnidade') == "") {
      alert("Nome do orgão/unidade do TCE não informado no cadastro da unidade, por favor preencha manualmente");
      $('nomeUnidade').focus();
      return false;
  }
}
	
function processarGeracaoArquivo() {

  var oParam           = new Object();
  oParam.exec          = "processarGeracaoPPA";
  if ($('orgaoUnidade')) {
    var codigo_unidade = $F('orgaoUnidade').split(".");
    oParam.orgao   = codigo_unidade[0];
    oParam.unidade = codigo_unidade[1];
  }
  oParam.codigoOrgaoTCE    = document.getElementById('codUnidade').value;
  oParam.codigoOrgaoTCEXml = document.getElementById('codUnidadeXml').value;
  oParam.nomeUnidadeTCE    = document.getElementById('nomeUnidade').value;
  js_divCarregando('Aguarde, Processando Arquivos', 'msgBox');
  var oAjax = new Ajax.Request(sURL,
                               {
                                 method:'post',
                                 parameters:'json='+Object.toJSON(oParam),
                                 onComplete:retornoProcessaSiai
                               }
                             );
}

function retornoProcessaSiai(oAjax) {

  js_removeObj('msgBox');
  var oRetorno = eval("("+oAjax.responseText+")");

  if (oRetorno.lErro) {
    alert(oRetorno.sMensagem.urlDecode());
    return false;
  }
  
  var oDownload = new DBDownload();
  oDownload.addGroups("Arquivos");
  oDownload.addFile(oRetorno.sCaminhoArquivo.urlDecode(), oRetorno.sNomeArquivo.urlDecode(), "Arquivos");
  oDownload.addFile(oRetorno.sCaminhoArquivoLog.urlDecode(), oRetorno.sNomeArquivoLog.urlDecode(), "Arquivos");
  oDownload.show();
  
}
</script>