<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
db_postmemory($_GET);

$where = [];

if (!empty($_GET['declarante'])) {
    $where[] = "e167_declarante ilike '%{$_GET['declarante']}%'";
}
if (isset($_GET['excluiGrupo10'])) {
    $where[] = "e167_codigo::char(2) <> '10'";
}

$dao = new cl_naturezarendimento;
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body>
<div class="container">
    <form name="form2" method="post" action="">
        <fieldset style="width: 800px;">
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td><label for="chave_grupo">Grupo:</label></td>
                    <td>
                        <select id="chave_grupo" name="chave_grupo">
                            <option value="">Selecione se deseja filtrar</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="chave_descricao">Classificação:</label></td>
                    <td>
                        <input type="text" name="chave_descricao" id="chave_descricao">
                    </td>
                </tr>
            </table>

        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_naturezarendimento.hide();">
    </form>
</div>
<?php

$campos = "
e167_sequencial as db_codigo,
e167_codigo::char(2) as grupo,
e167_codigo::int as e167_codigo,
e167_descricao::varchar(200) as e167_descricao,
e167_tributo,
e167_declarante
";
$orderBy = 'grupo, e167_codigo, e167_descricao';

if (!isset($pesquisa_chave)) {
    if (!empty($chave_grupo)) {
        $where[] = "e167_codigo::char(2) = '{$chave_grupo}'";
    }

    if (isset($chave_descricao) && (trim($chave_descricao) != "")) {
        $where[] = "e167_descricao like '%$chave_descricao%'";
    }

    $sql = $dao->sql_query_file("", $campos, $orderBy, implode(' and ', $where));

    if (isset($chave_descricao)) {
        $chave_descricao = str_replace("\\", "", $chave_descricao);
    }

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js);
    echo '  </fieldset> ';
    echo '</div> ';
} else {
    if (!empty($pesquisa_chave)) {
        $where[] = "e167_codigo = '{$pesquisa_chave}'";
        $sql = $dao->sql_query_file(null, $campos, "", implode(' and ', $where));
        $rs = db_query($sql);
        if (pg_num_rows($rs) != 0) {
            db_fieldsmemory($rs, 0);

            echo "<script>" . $funcao_js . "(false, '$e167_codigo', '$e167_descricao', $db_codigo);</script>";
        } else {
            echo "<script>" . $funcao_js . "(true, '', 'Chave(" . $pesquisa_chave . ") não Encontrado');</script>";
        }
    } else {
        echo "<script>" . $funcao_js . "(false, '');</script>";
    }
}
?>
