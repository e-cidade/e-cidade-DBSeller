<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
include(modification("classes/db_fis_lancamento_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cllancamento = new cl_fis_lancamento;
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
            <td width="4%" align="right" nowrap title="<?=$Tnl01_codlanc?>">
              <b> Lançamento: </b>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
		       db_input("nl01_codlanc",10,$Inl01_codlanc,true,"text",4,"","chave_nl01_codlanc");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tnl01_nome?>">
              <b> Descrição: </b>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
		       db_input("nl01_nome",50,$Inl01_nome,true,"text",4,"","chave_nl01_nome");
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
           if(file_exists("funcoes/db_func_fis_lancamento.php")==true){
             include(modification("funcoes/db_func_fis_lancamento.php"));
           }else{
           $campos = "fis_lancamento.*";
           }
        }
		
		if (isset($db_opcao) && ($db_opcao == 3 || $db_opcao == 33)) {
		  $where = " and not exists (select 1 from fiscalizacao.fis_lancamentonumpre where nl16_codlanc = dl_lancamento) ";	
		}
		
        if(isset($chave_nl01_codlanc) && (trim($chave_nl01_codlanc)!="") ){
	         $sql = $cllancamento->sql_query($chave_nl01_codlanc,$campos,"nl01_codlanc","  nl01_instit = ".db_getsession('DB_instit').$where);
        }else if(isset($chave_nl01_nome) && (trim($chave_nl01_nome)!="") ){
	         $sql = $cllancamento->sql_query("",$campos,"nl01_nome"," nl01_nome like '$chave_nl01_nome%' and  nl01_instit = ".db_getsession('DB_instit').$where);
        }else{
           $sql = $cllancamento->sql_query("",$campos,"nl01_codlanc"," nl01_instit = ".db_getsession('DB_instit').$where);
        }

        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $cllancamento->sql_record($cllancamento->sql_query($pesquisa_chave,"*",null," nl01_instit = ".db_getsession('DB_instit')." and nl01_codlanc = $pesquisa_chave "));
          if($cllancamento->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$nl01_nome',false);</script>";
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
