<?php
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_basecurricular_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clbasecurricular = new cl_basecurricular;
$clbasecurricular->rotulo->label("ed141_sequencial");
$clbasecurricular->rotulo->label("ed141_descricao");
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
          <td><label><?=$Led141_sequencial?></label></td>
          <td><?php db_input("ed141_sequencial",10,$Ied141_sequencial,true,"text",4,"","chave_ed141_sequencial"); ?></td>
        </tr>
        <tr>
          <td><label><?=$Led141_descricao?></label></td>
          <td><?php db_input("ed141_descricao",10,$Ied141_descricao,true,"text",4,"","chave_ed141_descricao");?></td>
        </tr>
      </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_basecurricular.hide();">
  </form>
      <?php
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_basecurricular.php")==true){
             include(modification("funcoes/db_func_basecurricular.php"));
           }else{
           $campos = "basecurricular.*";
           }
        }
        if(isset($chave_ed141_sequencial) && (trim($chave_ed141_sequencial)!="") ){
	         $sql = $clbasecurricular->sql_query($chave_ed141_sequencial,$campos,"ed141_sequencial");
        }else if(isset($chave_ed141_descricao) && (trim($chave_ed141_descricao)!="") ){
	         $sql = $clbasecurricular->sql_query("",$campos,"ed141_descricao"," ed141_descricao like '$chave_ed141_descricao%' ");
        }else{
           $sql = $clbasecurricular->sql_query("",$campos,"ed141_sequencial","");
        }
        $repassa = array();
        if(isset($chave_ed141_descricao)){
          $repassa = array("chave_ed141_sequencial"=>$chave_ed141_sequencial,"chave_ed141_descricao"=>$chave_ed141_descricao);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clbasecurricular->sql_record($clbasecurricular->sql_query($pesquisa_chave));
          if($clbasecurricular->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$ed141_descricao',false);</script>";
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
js_tabulacaoforms("form2","chave_ed141_descricao",true,1,"chave_ed141_descricao",true);
</script>
