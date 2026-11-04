<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$daoSerie = new cl_serie();
$camposSerie = "ed11_i_codigo, ed11_c_descr ||' - '|| ed10_c_descr , ed11_i_ensino, ed11_i_sequencia";
$sqlSerie = $daoSerie->sql_query_equiv("", $camposSerie, "ed11_i_ensino, ed11_i_sequencia");
$rsSerie = db_query($sqlSerie);

$situacoesMatricula = [
    ''                 => '',
    'APROVADO'         => 'APROVADO',
    'CANCELADO'        => 'CANCELADO',
    'CANDIDATO'        => 'CANDIDATO',
    'CONCLUÍDO'        => 'CONCLUÍDO',
    'EVADIDO'          => 'EVADIDO',
    'ABANDONO ESCOLAR' => 'ABANDONO ESCOLAR',
    'FALECIDO'         => 'FALECIDO',
    'MATRICULADO'      => 'MATRICULADO',
    'REPETENTE'        => 'REPETENTE',
    'TRANSFERIDO FORA' => 'TRANSFERIDO FORA',
    'TRANSFERIDO REDE' => 'TRANSFERIDO REDE'
];

$escola = db_getsession('DB_coddepto');
$where = [
    "ed18_i_codigo = {$escola}",
    "exists ( select 1
                from matricula
                join turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
               where matricula.ed60_i_aluno = aluno.ed47_i_codigo
                 and turma.ed57_i_escola = {$escola})",
];
?>

<html lang='pt-br'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link type="text/css" rel="stylesheet" href="estilos.css">
    <link type="text/css" rel="stylesheet" href="estilos/DBFormularios.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <title>DBSeller Inform&aacute;tica Ltda</title>
</head>
<body>
<form name="form2" method="post" action="" class="container">
    <fieldset>
        <legend>Filtros:</legend>
        <table class="form-container">
            <tr>
                <td >
                    <label for="chave_ed47_i_codigo" class="bold">Código:</label>
                </td>
                <td >
                    <input type="text" name="chave_ed47_i_codigo" id="chave_ed47_i_codigo" class="field-size2"
                           oninput="js_ValidaCampos(this, 1, 'Código', 't', 'f', event);">
                </td>
                <td >
                    <label for="chave_ed47_v_nome" class="bold">Nome:</label>
                </td>
                <td >
                    <input type="text" name="chave_ed47_v_nome" id="chave_ed47_v_nome" class="field-size6"
                           oninput="js_ValidaCampos(this, 2, 'Nome', 't', 'f', event);">
                </td>
            </tr>

            <tr>
                <td>
                    <label for="chave_ed223_i_serie" class="bold">Etapa:</label>
                </td>
                <td colspan="3">
                    <?php
                        db_selectrecord("ed223_i_serie", $rsSerie, "", "", "", "chave_ed223_i_serie", "", "  ", "", 1);
                    ?>
                </td>
            </tr>

            <tr>
                <td>
                    <label for="situacao" class="bold">Situação:</label>
                </td>
                <td colspan="3">
                    <?php

                    db_select('situacao', $situacoesMatricula, true, 1);
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar"    type="reset"  id="limpar"     value="Limpar" >
    <input name="Fechar"    type="button" id="fechar"     value="Fechar" onClick="parent.db_iframe_aluno.hide();">
</form>
<?php
$campos = array(
    'ed47_i_codigo',
    'ed47_v_nome',
    'ed56_c_situacao',
    'ed11_c_descr as dl_serie',
    '(select ed57_c_descr
        from matricula
        join turma on ed57_i_codigo = ed60_i_turma
       where ed47_i_codigo = ed60_i_aluno
       order by ed60_i_codigo desc limit 1) as dl_turma',
    'ed18_c_nome as dl_escola',
    'ed29_c_descr as dl_curso',
    'ed52_c_descr as dl_calendario',
);

$ordem = array('to_ascii(ed47_v_nome)');
$campos = implode(', ', $campos);

$sql = "
  select {$campos}
   from aluno
   join alunocurso  on alunocurso.ed56_i_aluno        = aluno.ed47_i_codigo
   join escola      on escola.ed18_i_codigo           = alunocurso.ed56_i_escola
   join calendario  on  calendario.ed52_i_codigo      = alunocurso.ed56_i_calendario
   join base        on  base.ed31_i_codigo            = alunocurso.ed56_i_base
   join cursoedu    on  cursoedu.ed29_i_codigo        = base.ed31_i_curso
   join alunopossib on  alunopossib.ed79_i_alunocurso = alunocurso.ed56_i_codigo
   join serie       on  serie.ed11_i_codigo           = alunopossib.ed79_i_serie
";

if (!isset($pesquisa_chave)) {
    if (isset($chave_ed47_i_codigo) && (trim($chave_ed47_i_codigo) != "")) {
        $where[] = "ed47_i_codigo = {$chave_ed47_i_codigo}";
    }

    if (isset($chave_ed47_v_nome) && (trim($chave_ed47_v_nome) != "")) {
        $where[] = "ed47_v_nome ilike '{$chave_ed47_v_nome}%'";
    }

    if (isset($chave_ed223_i_serie) && (trim($chave_ed223_i_serie) != "")) {
        $where[] = "ed11_i_codigo = {$chave_ed223_i_serie}";
    }

    if (isset($situacao) && (trim($situacao) != "")) {
        $where[] = "trim(ed56_c_situacao) = '{$situacao}'";
    }

    $sql .= " where " . implode(' and ', $where);
    $sql .= " order by " . implode(', ', $ordem);

    $repassa = array();
    if (isset($chave_ed47_i_codigo)) {
        $repassa = [
            "chave_ed47_i_codigo" => $chave_ed47_i_codigo,
            "chave_ed47_v_nome" => $chave_ed47_v_nome,
            "chave_ed223_i_serie" => $chave_ed223_i_serie,
            "situacao" => $situacao,
        ];
    }

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where[] = "ed47_i_codigo = {$pesquisa_chave}";

        $sql .= " where " . implode(' and ', $where);
        $sql .= " order by " . implode(', ', $ordem);

        $rs = db_query($sql);

        if (pg_numrows($rs) != 0) {
            db_fieldsmemory($rs, 0);
            echo "<script>" . $funcao_js . "('$ed47_v_nome', false);</script>";
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
<script>

    $('limpar').onclick = function () {

        $('chave_ed11_i_codigo').value = '';
        $('chave_ed11_c_descr').value = '';
        $('pesquisar2').click();
    }

</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
