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
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_ruas_classe.php"));
require_once(modification("classes/db_face_classe.php"));
$oStd = new db_stdClass();
db_postmemory($_POST);
db_postmemory($_GET);
$clface = new cl_face;
$clruas = new cl_ruas;
$clruas->rotulo->label("j14_codigo");
$clruas->rotulo->label("j14_nome");

$idquadra = $oStd->db_stripTagsJson($idquadra);
$idsetor  = $oStd->db_stripTagsJson($idsetor);

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
            <td width="4%" align="right" nowrap title="<?=$Tj14_codigo?>">
              <?=$Lj14_codigo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("j14_codigo",7,$Ij14_codigo,true,"text",4,"","chave_j14_codigo");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tj14_nome?>">
              <?=$Lj14_nome?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php
		       db_input("j14_nome",40,$Ij14_nome,true,"text",4,"","chave_j14_nome");
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
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           $campos = "ruas.*";
        }
        if(isset($chave_j14_codigo) && (trim($chave_j14_codigo)!="") ){
          $sql= $clface->sql_query('','j37_codigo, j14_nome',''," j37_setor = '$idsetor' and j37_quadra = '$idquadra' and j37_codigo=$chave_j14_codigo" );
        }else if(isset($chave_j14_nome) && (trim($chave_j14_nome)!="") ){
          $sql= $clface->sql_query('','j37_codigo, j14_nome',''," j37_setor = '$idsetor' and j37_quadra = '$idquadra' and j14_nome='$chave_j14_nome'" );
        }else{
          $sql= $clface->sql_query('','j37_codigo, j14_nome',''," j37_setor = '$idsetor' and j37_quadra = '$idquadra'");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clruas->sql_record($clface->sql_query('','j37_codigo,j14_nome',''," j37_setor = '$idsetor' and j37_quadra = '$idquadra' and j37_codigo=$pesquisa_chave" ));          
          if($clruas->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$j14_nome',false);</script>";
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
document.form2.chave_j14_codigo.focus();
document.form2.chave_j14_codigo.select();
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
