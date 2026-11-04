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
include(modification("classes/db_bases_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clbases = new cl_bases;
$clbases->rotulo->label("r08_mesusu");
$clbases->rotulo->label("r08_codigo");
$clbases->rotulo->label("r08_descr");
?>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
  <link href='estilos.css' rel='stylesheet' type='text/css'>
  <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
  <style>
    #form-container input[name=chave_r08_descr] {
      width: 150px !important;
    }
  </style>
</head>
<body>
  <form name="form2" method="post" action="" class="container">
    <fieldset>
      <legend>Dados para Pesquisa</legend>
      <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
        <tr>
          <td><label><?=$Lr08_codigo?></label></td>
          <td><?php db_input("r08_codigo",4,$Ir08_codigo,true,"text",4,"","chave_r08_codigo"); ?></td>
        </tr>
        <tr>
          <td><label><?=$Lr08_descr?></label></td>
          <td><?php db_input("r08_descr",40,$Ir08_descr,true,"text",4,"","chave_r08_descr");?></td>
        </tr>
      </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_bases.hide();">
  </form>
  
      <?php
      $db_where = "r08_instit = ".db_getsession("DB_instit")." and r08_anousu = ".db_anofolha()." and r08_mesusu = ".db_mesfolha()."  ";
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_bases.php")==true){
             include(modification("funcoes/db_func_bases.php"));
           }else{
           $campos = "bases.*";
           }
        }
        if(isset($chave_r08_codigo) && (trim($chave_r08_codigo)!="") ){
	         $sql = $clbases->sql_query(db_anofolha(),db_mesfolha(),$chave_r08_codigo,db_getsession('DB_instit'),$campos,"r08_codigo");
        }else if(isset($chave_r08_descr) && (trim($chave_r08_descr)!="") ){
	         $sql = $clbases->sql_query(db_anofolha(),db_mesfolha(),null,db_getsession('DB_instit'),$campos,"r08_descr"," r08_descr like '$chave_r08_descr%' and $db_where ");
        }else{
           $sql = $clbases->sql_query(db_anofolha(),db_mesfolha(),null,db_getsession('DB_instit'),$campos,"r08_anousu#r08_mesusu#r08_codigo","");
        }
        $repassa = array();
        if(isset($chave_r08_descr)){
          $repassa = array("chave_r08_codigo"=>$chave_r08_codigo,"chave_r08_descr"=>$chave_r08_descr);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clbases->sql_record($clbases->sql_query(db_anofolha(),db_mesfolha(),$pesquisa_chave,db_getsession('DB_instit')));
          if($clbases->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$r08_descr',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
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
js_tabulacaoforms("form2","chave_r08_descr",true,1,"chave_r08_descr",true);
</script>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
