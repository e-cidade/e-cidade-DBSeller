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
include(modification("classes/db_tiporegracompensacao_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cltiporegracompensacao = new cl_tiporegracompensacao;
$cltiporegracompensacao->rotulo->label("k154_sequencial");
$cltiporegracompensacao->rotulo->label("k154_descricao");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC>
<table align="center">
  <tr> 
    <td>
    <fieldset><legend><strong>Tipo de Regra</strong></legend>
      <table align="center">
      <form name="form2" method="post" action="" >
        <tr> 
          <td nowrap title="<?=$Tk154_sequencial?>">
            <?=$Lk154_sequencial?>
          </td>
          <td nowrap> 
          <?php
            db_input("k154_sequencial",10,$Ik154_sequencial,true,"text",4,"","chave_k154_sequencial");
          ?>
          </td>
        </tr>
        <tr> 
          <td nowrap title="<?=$Tk154_descricao?>">
            <?=$Lk154_descricao?>
          </td>
          <td nowrap> 
          <?php
            db_input("k154_descricao",40,$Ik154_descricao,true,"text",4,"","chave_k154_descricao");
          ?>
          </td>
        </tr>
        <tr> 
          <td colspan="2" align="center"> 
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
            <input name="limpar" type="reset" id="limpar" value="Limpar" >
            <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_tiporegracompensacao.hide();">
          </td>
        </tr>
      </form>
      </table>
    </td>
    </fieldset>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
    <fieldset>
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_tiporegracompensacao.php")==true){
             include(modification("funcoes/db_func_tiporegracompensacao.php"));
           }else{
           $campos = "tiporegracompensacao.*";
           }
        }
        if(isset($chave_k154_sequencial) && (trim($chave_k154_sequencial)!="") ){
	         $sql = $cltiporegracompensacao->sql_query($chave_k154_sequencial,$campos,"k154_sequencial");
        }else if(isset($chave_k154_descricao) && (trim($chave_k154_descricao)!="") ){
	         $sql = $cltiporegracompensacao->sql_query("",$campos,"k154_descricao"," k154_descricao ilike '$chave_k154_descricao%' ");
        }else{
           $sql = $cltiporegracompensacao->sql_query("",$campos,"k154_sequencial","");
        }
        $repassa = array();
        if(isset($chave_k154_sequencial) || isset($chave_k154_descricao)){
          $repassa = array("chave_k154_sequencial"=>$chave_k154_sequencial,"chave_k154_descricao"=>$chave_k154_descricao);
        }
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $cltiporegracompensacao->sql_record($cltiporegracompensacao->sql_query($pesquisa_chave));
          if($cltiporegracompensacao->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$k154_descricao',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </fieldset>
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
js_tabulacaoforms("form2","chave_k154_sequencial",true,1,"chave_k154_sequencial",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
