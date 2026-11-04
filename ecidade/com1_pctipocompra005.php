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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_pctipocompra_classe.php"));
require_once(modification("classes/db_pctipocompratribunal_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_GET);
db_postmemory($_POST);

$clpctipocompra = new cl_pctipocompra;
$clpctipocompratribunal = new cl_pctipocompratribunal;

$db_opcao = 22;
$db_botao = false;
$sqlerro = false;
$iInstit = db_getsession('DB_instit');

if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Alterar") {
    db_inicio_transacao();

    $db_opcao = 2;
    $db_botao = true;

    $clpctipocompra->alterar($pc50_codcom);
    $sMensagem = $clpctipocompra->erro_msg;
    if ($clpctipocompra->erro_status == 0) {
        $sqlerro = true;
    }

    db_fim_transacao($sqlerro);
} elseif (isset($chavepesquisa)) {
    $db_opcao = 2;
    $db_botao = true;

    $sSql = $clpctipocompra->sql_query($chavepesquisa);
    $result = $clpctipocompra->sql_record($clpctipocompra->sql_query($chavepesquisa));
    db_fieldsmemory($result, 0);
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
include(modification("forms/db_frmpctipocompra.php"));
?>

</body>
</body>
</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Alterar") {
    if ($clpctipocompra->erro_status == "0") {
        db_msgbox($sMensagem);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
        if ($clpctipocompra->erro_campo != "") {
            echo "<script> document.form1." . $clpctipocompra->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clpctipocompra->erro_campo . ".focus();</script>";
        }
    } else {
        db_msgbox($sMensagem);
    }
}

if (isset($chavepesquisa)) {
    echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.tipocompras.disabled=false;
         parent.document.formaba.faixavalores.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_faixavalores.location.href='com1_pctipocomprafaixavalor001.php?pc50_codcom=" . @$pc50_codcom . "';
     ";
    if (isset($liberaaba)) {
        echo "  parent.mo_camada('faixavalores');";
    }
    echo "}\n
    js_db_libera();
  </script>\n
 ";
}

if ($db_opcao == 22) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
