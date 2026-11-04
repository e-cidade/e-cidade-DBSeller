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
include(modification("classes/db_cadimobil_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clcadimobil = new cl_cadimobil;
$clcadimobil->rotulo->label("j63_numcgm");
$clcadimobil->rotulo->label("j63_numcgm");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post">
<table height="100%" width="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tj63_numcgm?>">
              <?=$Lj63_numcgm?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("j63_numcgm",10,$Ij63_numcgm,true,"text",4,"","chave_j63_numcgm");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe.hide();">
             </td>
          </tr>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top" width="100%" height="100%"> 
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           $campos = "cadimobil.*";
        }
        if(isset($chave_j63_numcgm) && (trim($chave_j63_numcgm)!="") ){
	         $sql = $clcadimobil->sql_query($chave_j63_numcgm,$campos,"j63_numcgm");
        }else if(isset($chave_j63_numcgm) && (trim($chave_j63_numcgm)!="") ){
	         $sql = $clcadimobil->sql_query("",$campos,"j63_numcgm"," j63_numcgm like '$chave_j63_numcgm%' ");
        }else{
           $sql = $clcadimobil->sql_query("","j63_numcgm#z01_nome","j63_numcgm","");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clcadimobil->sql_record($clcadimobil->sql_query("","z01_nome","j63_numcgm","j63_numcgm=$pesquisa_chave"));
         
	 
	  
          if($clcadimobil->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$z01_nome',false);</script>";
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
 </form>  
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
  ?>
  <script>
document.form1.chave_j63_numcgm.focus();
document.form1.chave_j63_numcgm.select();
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
