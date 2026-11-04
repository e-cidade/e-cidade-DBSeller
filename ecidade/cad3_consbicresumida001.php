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
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("dbforms/db_funcoes.php");

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

$iMatricula = '';
if (isset($matricula)) {
    $iMatricula = $matricula;
}

$sParametro = '';
if (isset($parametro)) {
  $sParametro = $parametro;
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, prototype.js");
db_app::load("estilos.css, grid.style.css");
?>
<script type="text/javascript">

function js_emite() {

  var iMatricula  = '<?=$iMatricula?>';
  var sParametro  = '<?=$sParametro?>';
  var dadosSigilosos = document.getElementById('imprimeDadosSigilosos').checked;
  
  if (sParametro.toLowerCase() == 'completa') {
    var lGeraCalculo   = document.getElementById('geraCalculo').checked;
    jan = window.open('cad3_conscadastro_impressao.php?tipo=2&sigiloso='+dadosSigilosos+'&geracalculo='+lGeraCalculo+'&parametro=' + iMatricula,'','location=0,HEIGHT=600,WIDTH=600');
  } else if (sParametro.toLowerCase() == 'resumida') {
    var dadosVenais    = document.getElementById('imprimeVenais').checked;
    jan = window.open('cad3_conscadastro_impressao.php?tipo=1&sigiloso='+dadosSigilosos+'&imprimevenais='+dadosVenais+'&parametro=' + iMatricula,'','location=0,HEIGHT=600,WIDTH=600');
  } else {
    alert('Nenhuma opção válida foi selecionada, contate o suporte!');
  }

  jan.moveTo(0,0);
}
</script>
</head>
<body class="body-default">
<form name="form1" method="post" action="">
<center>
<table align="center" width="100%" border="0">
  <tr>
    <td align="center">
      <u>Imprime BIC <?php echo($sParametro) ?> (Novo)</u>
    </td>
  </tr>
  <tr>
    <td align="left">
      <table width="30%" border="0">
        <tr>
          <td>
            <strong>Imprimir Dados Sigilosos: </strong>
          </td>
          <td>
            <input type="checkbox" id='imprimeDadosSigilosos' name='imprimeDadosSigilosos' <?=$checkSigilo?> checked="checked">
          </td>
        </tr>
        <?php if ($sParametro == 'Resumida'): ?>
        <tr>
          <td>
            <strong>Imprimir Valores Venais Último Cálculo:</strong>
          </td>
          <td>
            <input type="checkbox" id='imprimeVenais' name='imprimeVenais' class="checkbox" checked="checked">
          </td>
        </tr>
        <?php endif; ?>
        <?php if ($sParametro == 'Completa'): ?>
          <tr>
          <td>
            <strong>Imprimir Demonstrativo de Cálculo?</strong>
          </td>
          <td>
            <input type="checkbox" id='geraCalculo' name='geraCalculo' class="checkbox" checked="checked">
          </td>
        </tr>
        <?php endif; ?>
      </table>
    </td>
  </tr>
</table>
<div style="display: flex; justify-content: center; align-items: center;">
  <input type="button" name="imprimir" id=  "imprimir" value="Imprimir" onclick="return js_emite();">
</div>
</center>
</form>
</body>
</html>
