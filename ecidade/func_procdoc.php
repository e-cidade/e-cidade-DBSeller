<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_procdoc_classe.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$clprocdoc = new cl_procdoc();
$clprocdoc->rotulo->label('p56_coddoc');
$clprocdoc->rotulo->label('p56_descr');

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="estilos.css">
    <script src="scripts/scripts.js"></script>
</head>
<body>
<form name="form2" method="post" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td><label for="chave_p56_coddoc"><?=$Lp56_coddoc?></label></td>
                <td><?php db_input("p56_coddoc",20, $Ip56_coddoc, true, "text", 4, "", "chave_p56_coddoc"); ?></td>
            </tr>
            <tr>
                <td><label for="chave_p56_descr"><?=$Lp56_descr?></label></td>
                <td><?php db_input("p56_descr",'', $Ip56_descr, true, "text", 4, "", "chave_p56_descr"); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_procdoc.hide();">
</form>
<?php
if (isset($pesquisa_chave) === false) {
    if (isset($campos) === false) {
        if (file_exists("funcoes/db_func_procdoc.php") === true) {
            include(modification("funcoes/db_func_procdoc.php"));
        } else {
            $campos = "procdoc.*";
        }
    }
        if(isset($chave_p56_coddoc) && (trim($chave_p56_coddoc)!="") ){
	         $sql = $clprocdoc->sql_query($chave_p56_coddoc,$campos,"p56_coddoc");
        }else if(isset($chave_p56_descr) && (trim($chave_p56_descr)!="") ){
	         $sql = $clprocdoc->sql_query("",$campos,"p56_descr"," p56_descr ilike '$chave_p56_descr%' ");
        }else{
           $sql = $clprocdoc->sql_query("",$campos,"p56_coddoc","");
        }
        $repassa = array();
        if(isset($chave_p56_coddoc)){
          $repassa = array("chave_p56_coddoc"=>$chave_p56_coddoc,"chave_p56_coddoc"=>$chave_p56_coddoc);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      } else {
        if ($pesquisa_chave != null && $pesquisa_chave != "") {
          $result = $clprocdoc->sql_record($clprocdoc->sql_query($pesquisa_chave));
          if($clprocdoc->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$p56_coddoc',false, '$p56_descr');</script>";
        } else {
            echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>{$funcao_js}('', false);</script>";
    }
}
?>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
    ?>
  <script>
    document.form2.chave_p56_coddoc.focus();
    document.form2.chave_p56_coddoc.select();
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
