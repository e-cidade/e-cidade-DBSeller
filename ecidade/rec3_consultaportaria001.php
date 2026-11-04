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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_portaria_classe.php"));

$clportaria = new cl_portaria();
$clportaria->rotulo->label();




?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/geradorrelatorios.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
<center>
<form name="form1">
<input type="hidden" name="id_estorage" id="id_estorage" value="">
<table style="padding-top:30px;">
  <tr>
    <td>
      <fieldset>
        <legend align="center">
          <b>Impressão da Portaria</b>
        </legend>
        <table>
          <tr>
            <td>
              <?php
                db_ancora('Portaria', "js_pesquisaPortaria();", 1);
              ?>
            </td>
            <td>
              <input name="portaria" id="portaria" type="text" size="15" onblur="js_pesquisaPortaria()" disabled>
            </td>
          </tr>
        </table>
      </fieldset>
    </td>
  </tr>
  <tr align="center">
    <td>
      <input type="button" name="imprimir" id="imprimir" value="Imprimir" onClick="js_valida()">
    </td>
  </tr>
</table>
</form>
</center>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>

<script>


 function js_valida(){
 	var doc = document.form1;

 	if (doc.portaria.value == "") {
	  alert("Você deve informar o ano ou portaria.");

	  return false;
 	}

   buscaArquivoEstorage();
 }

  function buscaArquivoEstorage() {
      var parametros = `idestorage=${$('id_estorage').value}&exec=getArquivoEstorage`;

      const oAjax   = new Ajax.Request(
          'rh_processaassinaturadigital.RPC.php',
          {
            method: 'post',
            parameters: parametros,
            onComplete: imprimePortaria
          }
    );
  }

  function imprimePortaria(oAjax) {
    const response = JSON.parse(oAjax.responseText);

    if (response.path) {
      window.open(`db_download.php?arquivo=${response.path}`);
    } else {
      const aux = $('portaria').value.split('/');
      const parametros = `sAcao=consultaPortarias&iPortariaInicial=${aux[0]}&iPortariaFinal=${aux[0]}&iAnoUsu=${aux[1]}`;

      var oAjax   = new Ajax.Request(
        'rec1_portariasRPC.php',
        {
          method: 'post',
          parameters: parametros,
          onComplete: js_retornoEmite
        }
      );
    }
  }

  function js_retornoEmite(oAjax) {
    var aRetorno = JSON.parse(oAjax.responseText);

    if (aRetorno.erro == true) {
      alert(aRetorno.msg.urlDecode());

      return false;
    } else {
      js_imprimeRelatorio(aRetorno.iModIndividual,js_downloadArquivo,JSON.stringify(aRetorno.aParametros));
    }

  }


 function js_pesquisaPortaria(){
    $chave = $('portaria').value;

    js_OpenJanelaIframe('', 'db_iframe_portaria', 'func_portaria.php?flag_reemissao=1&funcao_js=parent.js_mostraportaria|h31_numero|h31_anousu|id_estorage', 'Pesquisa', true);
 }

 function js_mostraportaria(numero, ano, id_estorage){
    $('portaria').value = `${numero}/${ano}`;
    $('id_estorage').value = id_estorage;

    db_iframe_portaria.hide();
 }

</script>
