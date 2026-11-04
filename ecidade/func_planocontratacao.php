<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_planocontratacao_classe.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$clplanocontratacao = new cl_planocontratacao();
$clplanocontratacao->rotulo->label('pn05_codigo');
$clplanocontratacao->rotulo->label('pn05_codigo');

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
                <td><label for="chave_pn05_codigo"><?=$Lpn05_codigo?></label></td>
                <td><?php db_input("pn05_codigo", $Ipn05_codigo, true, "text", 4, "", "chave_pn05_codigo", '', '','width: 100px'); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_planocontratacao.hide();">
</form>
<?php
if (isset($pesquisa_chave) === false) {
    if (isset($campos) === false) {
        $campos = "planocontratacao.*, pn02_nome";
    }
    $where = 'pn05_status = 1 and pn05_instituicao = ' . $DB_instit;
        if(isset($chave_pn05_codigo) && (trim($chave_pn05_codigo)!="") ){
	         $sql = $clplanocontratacao->sql_query($chave_pn05_codigo,$campos,"pn05_codigo", $where);
        }else if(isset($chave_pn05_codigo) && (trim($chave_pn05_codigo)!="") ){
	         $sql = $clplanocontratacao->sql_query("",$campos,"pn05_codigo"," pn05_codigo like '$chave_pn05_codigo%' and " . $where);
        }else{
           $sql = $clplanocontratacao->sql_query("",$campos,"pn05_codigo",$where);
        }
        $repassa = array();
        if(isset($chave_pn05_codigo)){
          $repassa = array("chave_pn05_codigo"=>$chave_pn05_codigo,"chave_pn05_codigo"=>$chave_pn05_codigo);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
      } else {
        if ($pesquisa_chave != null && $pesquisa_chave != "") {
          $result = $clplanocontratacao->sql_record($clplanocontratacao->sql_query($pesquisa_chave));
          if($clplanocontratacao->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$pn05_codigo',false);</script>";
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
<?php if (isset($pesquisa_chave) === false) { ?>
    <script rel="script" type="text/javascript">
    </script>
<?php } ?>
<script rel="script" type="text/javascript">
    js_tabulacaoforms("form2","chave_pn05_codigo",true,1,"chave_pn05_codigo",true);
</script>
