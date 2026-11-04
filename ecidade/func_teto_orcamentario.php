<?php
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_teto_orcamentario_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clteto_orcamentario = new cl_teto_orcamentario;
$clteto_orcamentario->rotulo->label("c40_sequencial");
$clteto_orcamentario->rotulo->label("c40_sequencial");
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
                <td><label><?= $Lc40_sequencial ?></label></td>
                <td><?php db_input("c40_sequencial", 10, $Ic40_sequencial, true, "text", 4, "",
                        "chave_c40_sequencial"); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_teto_orcamentario.hide();">
</form>
<?php
if (!isset($pesquisa_chave)) {
    if (isset($campos) == false) {
        if (file_exists("funcoes/db_func_teto_orcamentario.php") == true) {
            include(modification("funcoes/db_func_teto_orcamentario.php"));
        } else {
            $campos = "teto_orcamentario.*";
        }
    }
    if (isset($chave_c40_sequencial) && (trim($chave_c40_sequencial) != "")) {
        $sql = $clteto_orcamentario->sql_query($chave_c40_sequencial, $campos, "c40_sequencial");
    } else {
        if (isset($chave_c40_sequencial) && (trim($chave_c40_sequencial) != "")) {
            $sql = $clteto_orcamentario->sql_query("", $campos, "c40_sequencial",
                " c40_sequencial like '$chave_c40_sequencial%' ");
        } else {
            $sql = $clteto_orcamentario->sql_query("", $campos, "c40_sequencial", "");
        }
    }
    $repassa = array();
    if (isset($chave_c40_sequencial)) {
        $repassa = array(
            "chave_c40_sequencial" => $chave_c40_sequencial,
            "chave_c40_sequencial" => $chave_c40_sequencial
        );
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $result = $clteto_orcamentario->sql_record($clteto_orcamentario->sql_query($pesquisa_chave));
        if ($clteto_orcamentario->numrows != 0) {
            db_fieldsmemory($result, 0);
            echo "<script>" . $funcao_js . "('$c40_sequencial',false);</script>";
        } else {
            echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>" . $funcao_js . "('',false);</script>";
    }
}
?>
</body>
</html>
<?php
if (!isset($pesquisa_chave)) {
    ?>
    <script>
    </script>
    <?php
}
?>
<script>
    js_tabulacaoforms('form2', 'chave_c40_sequencial', true, 1, 'chave_c40_sequencial', true);
</script>
