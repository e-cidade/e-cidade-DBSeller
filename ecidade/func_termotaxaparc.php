<?php
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_termotaxaparc_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cltermotaxaparc = new cl_termotaxaparc;
$cltermotaxaparc->rotulo->label("ar29_sequencial");
$cltermotaxaparc->rotulo->label("ar29_sequencial");
?>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
  <link href='estilos.css' rel='stylesheet' type='text/css'>
  <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
</head>
<body>
  <form name="form2" method="post" action="" class="container">
    <fieldset>
      <legend>Dados para Pesquisa</legend>
      <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
        <tr>
          <td><label><?=$Lar29_sequencial?></label></td>
          <td><?php db_input("ar29_sequencial",10,$Iar29_sequencial,true,"text",4,"","chave_ar29_sequencial"); ?></td>
        </tr>
        <tr>
          <td><label><?=$Lar29_sequencial?></label></td>
          <td><?php db_input("ar29_sequencial",10,$Iar29_sequencial,true,"text",4,"","chave_ar29_sequencial");?></td>
        </tr>
      </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_termotaxaparc.hide();">
  </form>
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_termotaxaparc.php")==true){
             include(modification("funcoes/db_func_termotaxaparc.php"));
           }else{
           $campos = "termotaxaparc.*";
           }
        }
        if(isset($chave_ar29_sequencial) && (trim($chave_ar29_sequencial)!="") ){
	         $sql = $cltermotaxaparc->sql_query($chave_ar29_sequencial,$campos,"ar29_sequencial");
        }else if(isset($chave_ar29_sequencial) && (trim($chave_ar29_sequencial)!="") ){
	         $sql = $cltermotaxaparc->sql_query("",$campos,"ar29_sequencial"," ar29_sequencial like '$chave_ar29_sequencial%' ");
        }else{
           $sql = $cltermotaxaparc->sql_query("",$campos,"ar29_sequencial","");
        }
        $repassa = array();
        if(isset($chave_ar29_sequencial)){
          $repassa = array("chave_ar29_sequencial"=>$chave_ar29_sequencial,"chave_ar29_sequencial"=>$chave_ar29_sequencial);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $cltermotaxaparc->sql_record($cltermotaxaparc->sql_query($pesquisa_chave));
          if($cltermotaxaparc->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$ar29_sequencial',false);</script>";
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
js_tabulacaoforms("form2","chave_ar29_sequencial",true,1,"chave_ar29_sequencial",true);
</script>
