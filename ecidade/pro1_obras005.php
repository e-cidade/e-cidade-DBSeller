<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2018  DBSeller Servicos de Informatica
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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clcriaabas = new cl_criaabas;
?>
  <html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
      <td width="360" height="18">&nbsp;</td>
      <td width="263">&nbsp;</td>
      <td width="25">&nbsp;</td>
      <td width="140">&nbsp;</td>
    </tr>
  </table>
  <table valign="top" marginwidth="0" width="790" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
          <?php
          $clcriaabas->identifica = array("obras" => "Obras", "constr" => "Construções", "areas" => "Áreas Complementares");
          $clcriaabas->title = array("obras" => "Obras", "constr" => "Construções", "areas" => "Áreas Complementares");
          $clcriaabas->src = array(
              "obras"  => "pro1_obras001.php?abas=1&pri=true",
              "constr" => "pro1_obrasconstr001.php",
              "areas" => "pro1_areascomplementares001.php"
          );
          $clcriaabas->cria_abas();
          ?>
      </td>
    </tr>
    <tr>
    </tr>
  </table>
  <?php
  db_menu();
  ?>
  </body>
  </html>
<?php
if (isset($db_opcao) && $db_opcao == 2) {
    echo "
         <script>
	   function js_src(){
        iframe_obras.location.href='pro1_obras002.php?abas=1';\n
	      document.formaba.constr.disabled=true; 
	      document.formaba.areas.disabled=true; 
	   }
	   js_src();
         </script>
       ";
    exit;
} else {
    if (isset($db_opcao) && $db_opcao == 3) {
        echo "
         <script>
	   function js_src(){
       iframe_obras.location.href='pro1_obras003.php?abas=1';\n
	     document.formaba.constr.disabled=true; 
	     document.formaba.areas.disabled=true; 
	   }
	   js_src();
         </script>
       ";
        exit;
    }
}
echo "
	 <script>
	    document.formaba.constr.disabled=true; 
	    document.formaba.areas.disabled=true; 
         </script>
       ";
exit;
