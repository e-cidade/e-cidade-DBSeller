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

/**
 *
 *  ------------------------------------------ ATENÇÃO -------------------------------------------------------
 * Essa função tem por objetivo substituir a func_orctiporec quando não podemos olhar o complemento do recurso
 *
 */

use App\Domain\Financeiro\Orcamento\Models\ClassificacaoFonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\FontesSiconfi;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clconplanoexe = new cl_conplanoexe;
$clorctiporec = new cl_orctiporec;
$clorctiporec->rotulo->label("o15_codigo");
$clorctiporec->rotulo->label("o15_descr");

$exercicio = !empty($_GET['exercicio']) ? $_GET['exercicio'] : db_getsession('DB_anousu');
$dataSessao = date('Y-m-d', db_getsession('DB_datausu'));

// lista de campos
$campos = "distinct gestao, o15_recurso, descricao, o15_datalimite, ";
$campos .= "array_to_string(array_accum(o15_codigo), ',') as db_ids_recursos";

$group = "gestao, o15_recurso, descricao, o15_datalimite ";

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body>
<div class="container" style="width: 800px;">
    <form name="form2" method="post" action="">
        <fieldset>
            <legend>Filtros</legend>
            <table class="form-container">
                <tr>
                    <td title="<?= $To15_descr ?>"><?= $Lo15_descr ?></td>
                    <td>
                        <?php
                        db_input("o15_descr", 30, $Io15_descr, true, "text", 4, "", "chave_o15_descr");
                        ?>
                    </td>
                </tr>

                <tr>
                    <td><label for="classificacao_recurso">Classificação:</label></td>
                    <td>
                        <?php
                        $classificacoes = ClassificacaoFonteRecurso::orderBy('id')->get();
                        ?>
                        <select id="classificacao_recurso" name="classificacao_recurso">
                            <option value="">Selecione se deseja filtrar</option>
                            <?php foreach ($classificacoes as $classificacao) : ?>
                                <option value="<?= $classificacao->id ?>"><?= $classificacao->descricao ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="chave_codigo_siconfi">Recursos Siconfi:</label></td>
                    <td>
                        <?php
                        $fontesSiconfi = ["" => 'Selecione'];
                        FontesSiconfi::all()->each(function (FontesSiconfi $fonteSiconfi) use (&$fontesSiconfi) {
                            $fontesSiconfi[$fonteSiconfi->codigo_siconfi] = sprintf(
                                '%s - %s',
                                $fonteSiconfi->codigo_siconfi,
                                $fonteSiconfi->descricao
                            );
                        });

                        echo "<select id='chave_codigo_siconfi' name='chave_codigo_siconfi'>";
                        foreach ($fontesSiconfi as $value => $label) {
                            echo "<option value='{$value}' >{$label}</option>";
                        }
                        echo "</select>";
                        ?>
                    </td>
                </tr>
            </table>

        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_orctiporec.hide();">
    </form>
</div>

<?php
$where = ["exercicio = {$exercicio}"];
if (isset($sem_recurso) && trim($sem_recurso) != "") {
    $where[] = "o15_codigo not in ({$sem_recurso})";
}
if (isset($sFiltroTipo) && $sFiltroTipo != '') {
    $where[] = "o15_tipo = {$sFiltroTipo}";
}

if (!isset($ativo) || (isset($ativo) && $ativo == 0)) {
    $where[] = "(o15_datalimite is null or o15_datalimite > '{$dataSessao}')";
}

if (isset($gestao) && $gestao !== '') {
    $where[] = "gestao = '{$gestao}'";
}

if (isset($o15_recurso) && $o15_recurso !== '') {
    $where[] = "o15_recurso = '{$o15_recurso}'";
}

if (isset($o15_complemento) && $o15_complemento !== '') {
    $where[] = "o15_complemento = {$o15_complemento}";
}

if (!empty($_POST['classificacao_recurso'])) {
    $where[] = "classificacaofr_id = {$_POST['classificacao_recurso']}";
}

if (!isset($pesquisa_chave)) {
    $orderBy = 'o15_recurso';
    if (isset($chave_o15_descr) && (trim($chave_o15_descr) != "")) {
        $where[] = "o15_descr like '{$chave_o15_descr}%'";
    }
    if (isset($chave_codigo_siconfi) && (trim($chave_codigo_siconfi) != "")) {
        $where[] = "codigo_siconfi like '_{$chave_codigo_siconfi}'";
    }

    $where = implode(' and ', $where);
    $where .= " group by {$group} ";
    $sql = $clorctiporec->sqlNovaFonteRecurso("", $campos, $orderBy, $where);

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js);
    echo '  </fieldset> ';
    echo '</div> ';
} elseif (!empty($pesquisa_chave)) {
    $where[] = "o15_recurso = '{$pesquisa_chave}'";
    $where = implode(' and ', $where);

    $where .= " group by {$group} ";
    $sql = $clorctiporec->sqlNovaFonteRecurso(null, $campos, "", $where);

    $result = db_query($sql);
    if (pg_num_rows($result) != 0) {
        db_fieldsmemory($result, 0);
        echo "<script>" . $funcao_js . "('$descricao',false, '$pesquisa_chave', '$db_ids_recursos');</script>";
    } else {
        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
    }
} else {
    echo "<script>" . $funcao_js . "('',false);</script>";
}

?>
</body>
</html>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
