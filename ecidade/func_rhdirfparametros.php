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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_rhdirfparametros_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clrhdirfparametros = new cl_rhdirfparametros();
$clrhdirfparametros->rotulo->label("rh132_sequencial"); 
$clrhdirfparametros->rotulo->label("rh132_sequencial");

if (isset($chave_rh132_sequencial) && !DBNumber::isInteger($chave_rh132_sequencial)) {
  $chave_rh132_sequencial = '';
}


if (isset($chave_rh132_sequencial) && !DBNumber::isInteger($chave_rh132_sequencial)) {
  $chave_rh132_sequencial = '';
}

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
      <table width="35%" border="0" align="center" cellspacing="0">
        <form name="form2" method="post" action="" >
          <tr>
            <td width="4%" align="right" nowrap title="<?php echo $Trh132_sequencial; ?>">
              <?php echo $Lrh132_sequencial; ?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		            db_input("rh132_sequencial", 4, $Irh132_sequencial, true, "text", 4, "", "chave_rh132_sequencial");
		          ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?php echo $Trh132_sequencial; ?>">
              <?php echo $Lrh132_sequencial; ?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
                db_input("rh132_sequencial", 4, $Irh132_sequencial, true, "text", 4, "", "chave_rh132_sequencial");
		          ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_rhdirfparametros.hide();">
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" valign="top"> 
      <?php


      if (!isset($pesquisa_chave)) {

        if (!isset($campos)) {

          if (file_exists("funcoes/db_func_rhdirfparametros.php")) {
            include(modification("funcoes/db_func_rhdirfparametros.php"));
          } else {
            $campos = "rhdirfparametros.*";
          }
        }

        if (isset($chave_rh132_sequencial) && (trim($chave_rh132_sequencial) != "")) {
	         $sql = $clrhdirfparametros->sql_query($chave_rh132_sequencial,$campos, "rh132_sequencial");
        } else if (isset($chave_rh132_sequencial) && (trim($chave_rh132_sequencial)!="") ){
	         $sql = $clrhdirfparametros->sql_query("", $campos, "rh132_sequencial"," rh132_sequencial like '$chave_rh132_sequencial%' ");
        } else {
           $sql = $clrhdirfparametros->sql_query("", $campos, "rh132_sequencial","");
        }

        $repassa = array();
        if (isset($chave_rh132_sequencial)) {
          $repassa = array("chave_rh132_sequencial" => $chave_rh132_sequencial, "chave_rh132_sequencial" => $chave_rh132_sequencial);
        }

        db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
      } else {

        if ($pesquisa_chave != null && $pesquisa_chave != "") {

          $result = $clrhdirfparametros->sql_record($clrhdirfparametros->sql_query($pesquisa_chave));
          if ($clrhdirfparametros->numrows != 0) {

            db_fieldsmemory($result, 0);
            echo "<script>".$funcao_js."('$rh132_sequencial', false);</script>";
          } else {
	          echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }

        } else {
	        echo "<script>".$funcao_js."('',false);</script>";
        }
      }

      ?>
    </td>
  </tr>
</table>
</body>
</html>
<?php if (!isset($pesquisa_chave)) { ?>
  <script>
  </script>
<?php } ?>
<script>
  js_tabulacaoforms("form2", "chave_rh132_sequencial", true, 1, "chave_rh132_sequencial", true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
