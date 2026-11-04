<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_operadorasaude_classe.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING']);

$cgm = new cl_cgm();
$cgm->rotulo->label('z01_nome');

$operadorasaude = new cl_operadorasaude();
$operadorasaude->rotulo->label('rh221_sequencial');

$where = ["rh221_ativo is TRUE"];
$where[] = "rh221_instituicao = " . db_getsession('DB_instit');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<form name="form2" method="post" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td><label for="chave_rh221_sequencial"><?= $Lrh221_sequencial ?></label></td>
                <td><?php db_input("rh221_sequencial", 8, $Irh221_sequencial, true, "text", 4, "",
                        "chave_rh221_sequencial"); ?></td>
            </tr>
            <tr>
                <td><label for="chave_z01_nome"><?= $Lz01_nome ?></label></td>
                <td><?php db_input("z01_nome", 8, $Iz01_nome, true, "text", 4, "", "chave_z01_nome"); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_operadorasaude.hide();">
</form>
<?php

if (isset($campos) === false) {
    $campos = array(
        'operadorasaude.rh221_sequencial',
        'cgm.z01_nome',
        'cgm.z01_cgccpf',
        'operadorasaude.rh221_ans :: INT'
    );

    $campos = implode(', ', $campos);
}
if (isset($pesquisa_chave) === false) {
    if (isset($chave_rh221_sequencial) && trim($chave_rh221_sequencial) !== '') {
        $where[] = "rh221_sequencial = {$chave_rh221_sequencial}";
    } elseif (isset($chave_z01_nome) && trim($chave_z01_nome !== '')) {
        $where[] = "z01_nome ILIKE '%{$chave_z01_nome}%'";
    }
    $sql = $operadorasaude->sql_query(null, $campos, 'rh221_sequencial', implode(' and ', $where));

    $repassa = array();

    if (isset($chave_rh221_sequencial)) {
        $repassa['chave_rh221_sequencial'] = $chave_rh221_sequencial;
    }

    if (isset($chave_z01_nome)) {
        $repassa['chave_z01_nome'] = $chave_z01_nome;
    }

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} elseif ($pesquisa_chave != null && $pesquisa_chave != "") {
    $where[] = "rh221_sequencial = {$pesquisa_chave}";
    $sql = $operadorasaude->sql_query(null, $campos, 'rh221_sequencial', implode(' and ', $where));
    $result = $operadorasaude->sql_record($sql);
    if ($operadorasaude->numrows != 0) {
        db_fieldsmemory($result, 0);
        echo "<script>{$funcao_js}('{$z01_nome}', false);</script>";
    } else {
        echo "<script>{$funcao_js}('Chave({$pesquisa_chave}) não encontrada', true);</script>";
    }
} else {
    echo "<script>{$funcao_js}('', false);</script>";
}
?>
</body>
</html>
<script rel="script" type="text/javascript">
    js_tabulacaoforms('form2', 'chave_z01_nome', true, 1, 'chave_z01_nome', true);
</script>
