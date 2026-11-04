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

use App\Domain\Financeiro\Orcamento\Models\ClassificacaoFonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\FontesSiconfi;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));

require_once(modification("dbforms/db_funcoes.php"));
db_postmemory($_POST);
db_postmemory($_GET);

$chave_o15_descr = isset($chave_o15_descr) ? stripslashes($chave_o15_descr) : '';
$dataSessao = date('Y-m-d', db_getsession('DB_datausu'));
$exercicio = !empty($_GET['exercicio']) ? $_GET['exercicio'] : db_getsession('DB_anousu');

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

$dao = new cl_orctiporec;
$dao->rotulo->label("o15_codigo");
$dao->rotulo->label("o15_descr");

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
                    <td title="<?= $To15_descr ?>">
                        <?= $Lo15_descr ?>
                    </td>
                    <td>
                        <?php
                        $chave_o15_descr = preg_replace("/[\'\"]/", "", $chave_o15_descr);
                        db_input("o15_descr", 100, $Io15_descr, true, "text", 4, "", "chave_o15_descr");
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

                <tr>
                    <td>
                        <label for="complementoRecurso">
                            <strong>Complemento:</strong>
                        </label>
                    </td>
                    <td>
                        <?php
                        $complementoRecurso = array("" => 'Selecione');
                        foreach (\ECidade\Financeiro\Orcamento\Recurso\Complemento::getAll() as $indice => $valor) {
                            $complementoRecurso[$indice] = $valor;
                        }
                        echo "<select id='complementoRecurso' name='o15_complemento'>";
                        foreach ($complementoRecurso as $value => $label) {
                            echo "<option value='{$value}' >{$label}</option>";
                        }
                        echo "</select>";
                        ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="mostrarRegistros">
                            <strong>Mostrar Registros:</strong>
                        </label>
                    </td>
                    <td>
                        <?php
                        $opcoes = ["ativos" => "Apenas Ativos", "inativos" => "Apenas Inativos", "todos" => "Todos"];
                        db_select("mostrarRegistrosPorValidade", $opcoes, true, 1);
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

$chave_o15_descr = addslashes($chave_o15_descr);

$where = ["exercicio = {$exercicio}"];

if (!empty($codigo) && empty($_POST)) {
    $where[] = "o15_codigo = {$codigo}";
}

if (isset($sem_recurso) && trim($sem_recurso) != "") {
    $where[] = "o15_codigo not in (" . $sem_recurso . ")";
}

if ((!isset($oGet->ativo) && !isset($mostrarRegistrosPorValidade))|| (isset($oGet->ativo) && $oGet->ativo == 0)) {
    $where[] = "(o15_datalimite is null or o15_datalimite > '{$dataSessao}')";
}

if (isset($mostrarRegistrosPorValidade)) {
    if ($mostrarRegistrosPorValidade == "ativos") {
        $where[] = "(o15_datalimite is null or o15_datalimite >= '{$dataSessao}')";
    } elseif ($mostrarRegistrosPorValidade == "inativos") {
        $where[] = "(o15_datalimite < '{$dataSessao}')";
    }
}

if (!empty($_POST['classificacao_recurso'])) {
    $where[] = "classificacaofr_id = {$_POST['classificacao_recurso']}";
}

if (isset($codigo_siconfi) && $codigo_siconfi !== '') {
    $where[] = "fonterecurso.codigo_siconfi = '{$codigo_siconfi}'";
}

if (isset($gestao) && $gestao !== '') {
    $where[] = "fonterecurso.gestao = '{$gestao}'";
}

if (isset($o15_recurso) && $o15_recurso !== '') {
    $where[] = "o15_recurso = '{$o15_recurso}'";
}

if (isset($o15_complemento) && $o15_complemento !== '') {
    $where[] = "o15_complemento = {$o15_complemento}";
}
$campos = "
    o15_codigo,
    fonterecurso.codigo_siconfi,
    fonterecurso.gestao,
    o15_recurso,
    fonterecurso.descricao,
    o15_complemento,
    o200_descricao
";
$orderBy = 'fonterecurso.gestao, o15_codigo';
if (!isset($pesquisa_chave)) {
    if (isset($chave_o15_descr) && (trim($chave_o15_descr) != "")) {
        $where[] = "descricao like '$chave_o15_descr%'";
    }
    if (isset($chave_codigo_siconfi) && (trim($chave_codigo_siconfi) != "")) {
        $where[] = "fonterecurso.codigo_siconfi like '_{$chave_codigo_siconfi}'";
    }

    $sql = $dao->sqlNovaFonteRecurso("", $campos, $orderBy, implode(' and ', $where));

    if (isset($chave_o15_descr)) {
        $chave_o15_descr = str_replace("\\", "", $chave_o15_descr);
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js);
    echo '  </fieldset> ';
    echo '</div> ';
} else {
    if (!empty($pesquisa_chave)) {
        $where = [
            "fonterecurso.exercicio = {$exercicio}",
            "o15_codigo = {$pesquisa_chave}"
        ];

        $sql = $dao->sqlNovaFonteRecurso(null, $campos, "", implode(' and ', $where));
        $rs = db_query($sql);
        if (pg_num_rows($rs) != 0) {
            db_fieldsmemory($rs, 0);

            echo "<script>" . $funcao_js . "(false, '$gestao', '$descricao',$o15_codigo, $o15_complemento, '$o200_descricao', '$o15_recurso');</script>";
        } else {
            echo "<script>" . $funcao_js . "(true, '', 'Chave(" . $pesquisa_chave . ") não Encontrado');</script>";
        }
    } else {
        echo "<script>" . $funcao_js . "(false, '');</script>";
    }
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
