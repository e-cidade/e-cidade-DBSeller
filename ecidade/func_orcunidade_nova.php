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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$clorcunidade = new cl_orcunidade;
$clorcunidade->rotulo->label("o41_anousu");
$clorcunidade->rotulo->label("o41_orgao");
$clorcunidade->rotulo->label("o41_unidade");
$clorcunidade->rotulo->label("o41_descr");

$where = array();

$ano = !empty($_GET['ano']) ? $_GET['ano'] : db_getsession('DB_anousu');
$where[] = "o41_anousu = {$ano}";

// se informar uma lista de orgãos
if (!empty($_GET['orgaos'])) {
    $where[] = " o41_orgao in ({$_GET['orgaos']})";
}
// se informar um orgão
if (!empty($_GET['orgao'])) {
    $where[] = " o41_orgao = {$_GET['orgao']}";
}

if (!empty($_GET['instituicao'])) {
    $where[] = " o41_instit = {$_GET['instituicao']}";
}

$campos = "
o41_anousu,
o41_orgao,
o41_unidade,
o41_descr,
o41_cnpj,
o41_instit,
nomeinst as db_nomeinst
";

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body class='body-default'>

<form name="form2" method="post" action="" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td width="4%" align="right" nowrap title="<?= $To41_orgao ?>">
                    <label for="chave_o41_orgao"><?= $Lo41_orgao ?></label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("o41_orgao", 5, $Io41_orgao, true, "text", 1, "", "chave_o41_orgao"); ?>
                </td>
            </tr>
            <tr>
                <td width="4%" align="right" nowrap title="<?= $To41_unidade ?>">
                    <label for="chave_o41_unidade"> <?= $Lo41_unidade ?></label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("o41_unidade", 5, $Io41_unidade, true, "text", 4, "", "chave_o41_unidade"); ?>
                </td>
            </tr>
            <tr>
                <td width="4%" align="right" nowrap title="<?= $To41_descr ?>">
                    <label for="chave_o41_descr"> <?= $Lo41_descr ?></label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("o41_descr", 40, $Io41_descr, true, "text", 4, "", "chave_o41_descr"); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_orcunidade.hide();">
</form>

<?php

if (!isset($pesquisa_chave)) {
    if (isset($chave_o41_orgao) && (trim($chave_o41_orgao) != "")) {
        $where[] = " o41_orgao = {$chave_o41_orgao} ";
    }

    if (isset($chave_o41_unidade) && (trim($chave_o41_unidade) != "")) {
        $where[] = " o41_unidade = {$chave_o41_unidade} ";
    }

    if (isset($chave_o41_descr) && (trim($chave_o41_descr) != "")) {
        $where[] = " o41_descr like '{$chave_o41_descr}%' ";
    }

    $where = implode(' and ', $where);
    $sql = $clorcunidade->sql_query(null, null, null, $campos, "o41_orgao, o41_unidade", $where);

    $repassa = array();
    if (isset($chave_o41_descr)) {
        $repassa = array("chave_o41_descr" => $chave_o41_descr, "chave_o41_descr" => $chave_o41_descr);
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where[] = " o41_unidade = {$pesquisa_chave} ";
        $where = implode(' and ', $where);
        $sql = $clorcunidade->sql_query(null, null, null, $campos, "o41_orgao, o41_unidade", $where);

        $result = db_query($sql);
        if (pg_num_rows($result) != 0) {
            $dados = db_utils::fieldsMemory($result, 0);
            echo "<script>" . $funcao_js . "('$dados->o41_descr', false, '$dados->o41_instit', '$dados->o41_orgao');</script>";
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
        $('chave_o41_orgao').value = '';
        $('chave_o41_descr').value = '';
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
