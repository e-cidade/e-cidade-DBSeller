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
require_once(modification('libs/db_utils.php'));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$oDaoSamClasseGravidade = db_utils::getdao ("sam_class_gravidade");
$oDaoSamClasseGravidade->rotulo->label("sm02_sequencial");
$oDaoSamClasseGravidade->rotulo->label("sm02_descr");
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
            <td width="4%" align="right" nowrap title="<?=$Tsm02_sequencial?>">
              <?=$Lsm02_sequencial?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("sm02_sequencial",10,$Ism02_sequencial,true,"text",4,"","chave_sm02_sequencial");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tsm02_sequencial?>">
              <?=$Lsm02_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("sm02_descr",10,$Ism02_descr,true,"text",4,"","chave_sm02_descr");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_sam_class_gravidade.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_sam_class_gravidade.php")==true){
             include(modification("funcoes/db_func_sam_class_gravidade.php"));
           }else{
           $campos = "sam_class_gravidade.*";
           }
        }
        if(isset($chave_sm02_sequencial) && (trim($chave_sm02_sequencial)!="") ){
	         $sql = $oDaoSamClasseGravidade->sql_query($chave_sm02_sequencial,$campos,"sm02_sequencial");
        }else if(isset($chave_sm02_descr) && (trim($chave_sm02_descr)!="") ){
	         $sql = $oDaoSamClasseGravidade->sql_query("",$campos,"sm02_descr"," sm02_descr like '$chave_sm02_descr%' ");
        }else{
           $sql = $oDaoSamClasseGravidade->sql_query("",$campos,"sm02_sequencial","");
        }
        $repassa = array();
        if(isset($chave_sm02_sequencial)){
          $repassa = array("chave_sm02_sequencial"=>$chave_sm02_sequencial,"chave_sm02_descr"=>$chave_sm02_descr);
        }
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $oDaoSamClasseGravidade->sql_record($oDaoSamClasseGravidade->sql_query($pesquisa_chave));
          if($oDaoSamClasseGravidade->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$sm02_sequencial',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
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
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}
?>
<script>
js_tabulacaoforms("form2","chave_sm02_sequencial",true,1,"chave_sm02_sequencial",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
