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
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clrotulo = new rotulocampo;
$lFail    = false;
if(isset($uploadfile)) {

  // Nome do novo arquivo
  $nomearq = $_FILES["uploadfile"]["name"];

  // Nome do arquivo temporário gerado no /tmp
  $nometmp = $_FILES["uploadfile"]["tmp_name"];

  // Seta o nome do arquivo destino do upload
  $arquivoDocument = db_removeAcentuacao("tmp/$nomearq");


  // Faz um upload do arquivo para o local especificado
  if(copy($nometmp,$arquivoDocument)) {

    $href = $arquivoDocument;

  }else{

  	db_msgbox("Erro ao enviar arquivo.");
  	unlink($nometmp);
  	$lFail = true;
  }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script>
function js_enviar(){
	parent.document.form1.localrecebefoto.value = "<?=@$arquivoDocument?>";
	parent.document.getElementById("fotofunc").innerHTML = "<?=@$href?>";
	parent.db_iframe_localfoto.hide();
}
function js_testacampo(){
  if(document.form1.arquivofoto.value != ""){
    document.form1.submit();
  }else{
    alert("Informe o arquivo.");
  }
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<center id='teste'>
<table border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
<?=@$href;?>
</table>
</center>
</body>
</html>
<script>
    <?php
        if (isset($_GET["clone"]) && !isset($href)) {
            echo "var cloneFormulario='{$_GET["clone"]}';\n";
    ?>
            if (parent.$(cloneFormulario)) {
                var formteste = parent.$(cloneFormulario).cloneNode(true);
                $('teste').appendChild(formteste);
                formteste.submit();
            }
    <?php
        }

        if (isset($href)) {
            if (!$lFail) {
                echo "parent.$('namefile').value=\"{$href}\";\n";
            }

            echo "parent.endLoading();";
            echo "parent.$('teste').removeChild(parent.$('uploadIframe'));";
        }
    ?>
</script>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
