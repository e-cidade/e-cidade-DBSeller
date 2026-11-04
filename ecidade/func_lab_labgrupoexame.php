<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_lab_grupo_classe.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$cllab_grupo = new cl_lab_grupo();
$cllab_labgrupoexame = new cl_lab_labgrupoexame();
$cllab_labdepart = new cl_lab_labdepart();
$cllab_grupo->rotulo->label('la66_codigo');
$cllab_grupo->rotulo->label('la66_codigo');

$la03_i_departamento = $departamento;
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
                <td><label for="chave_la66_codigo"><?=$Lla66_codigo?></label></td>
                <td><?php db_input("la66_codigo",10, $Ila66_codigo, true, "text", 4, "", "chave_la66_codigo"); ?></td>
            </tr>
            <tr>
                <td><label for="chave_la66_codigo"><?=$Lla66_codigo?></label></td>
                <td><?php db_input("la66_codigo",10, $Ila66_codigo, true, "text", 4, "", "chave_la66_codigo"); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" >
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_lab_labgrupoexame.hide();">
</form>
<?php
    $whereLaboratorio = "";
    if($departamento){
        $resultado = db_query($cllab_labdepart->sql_query_file(null, "la03_i_laboratorio", null, " la03_i_departamento = ". $departamento));
        db_fieldsmemory($resultado,0);
        $whereLaboratorio = " la02_i_codigo = ".$la03_i_laboratorio;
    }
    if (isset($pesquisa_chave) === false) {
        if (isset($campos) === false) {
            if (file_exists("funcoes/db_func_lab_grupo.php") === true) {
                include(modification("funcoes/db_func_lab_grupo.php"));
            } else {
                $campos = "lab_grupo.*";
            }
        }
        $campos .= ",la67_codigo";
        if(isset($chave_la66_codigo) && (trim($chave_la66_codigo)!="") ){
            $sql = $cllab_labgrupoexame->sql_query($chave_la66_codigo,$campos,"la66_codigo", $whereLaboratorio);
        }else if(isset($chave_la66_codigo) && (trim($chave_la66_codigo)!="") ){
            if($whereLaboratorio){
                $sql = $cllab_labgrupoexame->sql_query("",$campos,"la66_codigo"," la66_codigo like '$chave_la66_codigo%' and ".$whereLaboratorio);
            }else{
                $sql = $cllab_labgrupoexame->sql_query("",$campos,"la66_codigo"," la66_codigo like '$chave_la66_codigo%'");
            }
        }else{
            $sql = $cllab_labgrupoexame->sql_query("",$campos,"la66_codigo"," la02_i_codigo = ".$la03_i_laboratorio, null, $whereLaboratorio);
        }
        $repassa = array();
        if(isset($chave_la66_codigo)){
          $repassa = array("chave_la66_codigo"=>$chave_la66_codigo,"chave_la66_descricao"=>$chave_la66_descricao);
        }
        echo '<div class="container">';
        echo '  <fieldset>';
        echo '    <legend>Resultado da Pesquisa</legend>';
          db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
        echo '  </fieldset>';
        echo '</div>';
    } else {
        if ($pesquisa_chave != null && $pesquisa_chave != "") {
            $campos = "la66_codigo,la66_descricao,la67_codigo";
            $where = " la66_codigo = $pesquisa_chave";
            if($whereLaboratorio){
                $where .= " and ".$whereLaboratorio;
            }
            $sql =  $cllab_labgrupoexame->sql_query("",$campos,"la66_codigo",$where);
            $result = db_query($sql);
            if(pg_num_rows($result)!=0){
                db_fieldsmemory($result,0);
                echo "<script>".$funcao_js."('$la66_codigo','$la66_descricao', '$la67_codigo');</script>";
            } else {
                echo "<script>".$funcao_js."(false,'CÓDIGO DE GRUPO INEXISTENTE');</script>";
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
    js_tabulacaoforms("form2","chave_la66_codigo",true,1,"chave_la66_codigo",true);
</script>
