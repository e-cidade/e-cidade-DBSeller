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
include(modification("classes/db_relac_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($_SERVER['QUERY_STRING'], $query);

$clrelac = new cl_relac;
$clrelac->rotulo->label("r55_codeve");
$clrelac->rotulo->label("r55_descr");

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
            <td width="4%" align="right" nowrap title="<?php echo $Tr55_codeve?>">
              <?php echo $Lr55_codeve?>
            </td>
            <td width="96%" align="left" nowrap> 
            <?php
		          db_input("r55_codeve",4,$Ir55_codeve,true,"text",4,"","chave_r55_codeve");
		        ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?php echo $Tr55_descr?>">
              <?php echo $Lr55_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
            <?php
		          db_input("r55_descr",40,$Ir55_descr,true,"text",4,"","chave_r55_descr");
		        ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_relac.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
      if(isset($campos)==false){
         if(file_exists("funcoes/db_func_relac.php")==true){
           include(modification("funcoes/db_func_relac.php"));
         }else{
           $campos = "relac.*";
         }
      }
      if(!isset($pesquisa_chave)){
        if(isset($chave_r55_codeve) && (trim($chave_r55_codeve)!="") ){
	         $sql = $clrelac->sql_query($chave_r55_codeve,db_getsession("DB_instit"),$campos,"r55_codeve");
        }else if(isset($chave_r55_descr) && (trim($chave_r55_descr)!="") ){
	         $sql = $clrelac->sql_query("",db_getsession("DB_instit"),$campos,"r55_descr"," r55_descr like '$chave_r55_descr%' ");
        }else{
           $sql = $clrelac->sql_query("",db_getsession("DB_instit"),$campos,"r55_codeve","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clrelac->sql_record($clrelac->sql_query($pesquisa_chave,db_getsession("DB_instit")));
          if($clrelac->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$r55_descr',false);</script>";
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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
