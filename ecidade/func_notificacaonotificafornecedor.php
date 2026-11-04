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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_notificacaonotificafornecedor_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$oGet  = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);

$clnotificacaonotificafornecedor = new cl_notificacaonotificafornecedor;
$clnotificacaonotificafornecedor->rotulo->label("pc87_sequencial");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
<?php
  if (!isset($oGet->notificacao)) {
?>
  <tr> 
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tpc87_sequencial?>">
              <?=$Lpc87_sequencial?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		            db_input("pc87_sequencial",10,$Ipc87_sequencial,true,"text",4,"","chave_pc87_sequencial");
		          ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_notificacaonotificafornecedor.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
<?php
  }
?>
  <tr> 
    <td align="center" valign="top"> 
      <?php
      if (!isset($oGet->pesquisa_chave)) {
      	
        if (isset($campos) == false) {
        	
          if (file_exists("funcoes/db_func_notificacaonotificafornecedor.php") == true) {
            include(modification("funcoes/db_func_notificacaonotificafornecedor.php"));
          } else {
            $campos = "notificacaonotificafornecedor.*";
          }
        }
        
        if (!isset($oGet->notificacao)) {
          
	        if (isset($oPost->chave_pc87_sequencial) && (trim($oPost->chave_pc87_sequencial) != "")) {
	          $sSql = $clnotificacaonotificafornecedor->sql_query($oPost->chave_pc87_sequencial, $campos, "pc87_sequencial");
	        } else if (isset($oPost->chave_pc87_sequencial) && (trim($oPost->chave_pc87_sequencial) != "")) {
	          
	          $sWhere = " pc87_sequencial like '$oPost->chave_pc87_sequencial%' ";
	          $sSql = $clnotificacaonotificafornecedor->sql_query(null, $campos, "pc87_sequencial", $sWhere);
	        } else {
	          $sSql = $clnotificacaonotificafornecedor->sql_query(null, $campos, "pc87_sequencial", "");
	        }
        } else {
                  	
          $sAnd    = "";
          $sWhere  = "";
          if (isset($oGet->z01_numcgm) && !empty($oGet->z01_numcgm)) {
            
            $sWhere .= " {$sAnd} pc86_numcgm = {$oGet->z01_numcgm} ";
            $sAnd    = " and ";
          }
          
          if ( isset($oGet->datainicial) && isset($oGet->datafinal) ) {
                    
            $dtDataInicial = implode("-", array_reverse(explode("/", $oGet->datainicial)));
            $dtDataFinal   = implode("-", array_reverse(explode("/", $oGet->datafinal)));
            if ( ( !empty($dtDataInicial) ) && ( !empty($dtDataFinal) ) ) {
              
              $sWhere    .= " {$sAnd} pc86_data between '{$dtDataInicial}' and '{$dtDataFinal}' ";
              $sAnd       = " and ";
            } else if ( !empty($dtDataInicial) ) {
              
              $sWhere .= " {$sAnd} ( pc86_data >= '{$dtDataInicial}' ) ";
              $sAnd    = " and ";
            } else if ( !empty($dtDataFinal) ) {
              
              $sWhere .= " {$sAnd} ( pc86_data <= '{$dtDataFinal}' ) ";
              $sAnd    = " and ";
            }  
          }

          $sSql    = $clnotificacaonotificafornecedor->sql_query(null, $campos, "pc87_sequencial", $sWhere);
        }
        
        $repassa = array();
        if (isset($chave_pc87_sequencial)) {
          $repassa = array("chave_pc87_sequencial" => $oPost->chave_pc87_sequencial);
        }
        
        db_lovrot($sSql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
      } else {
      	
        if ($oGet->pesquisa_chave != null && $oGet->pesquisa_chave != "") {
        	
        	$sSql   = $clnotificacaonotificafornecedor->sql_query($oGet->pesquisa_chave);
          $result = $clnotificacaonotificafornecedor->sql_record($sSql);
          if ($clnotificacaonotificafornecedor->numrows != 0) {
          	
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$pc87_sequencial',false);</script>";
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
<?php
if(!isset($oGet->pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}

if (!isset($oGet->notificacao)) {
?>
<script>
  js_tabulacaoforms("form2","chave_pc87_sequencial",true,1,"chave_pc87_sequencial",true);
</script>
<?php
}
?>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
