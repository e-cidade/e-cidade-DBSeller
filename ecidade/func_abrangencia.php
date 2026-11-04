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

$oRotulo = new rotulocampo();
$oRotulo->label('pl18_codigo');
$oRotulo->label('pl18_descricao');

$where = [];
$campos = "pl18_codigo, pl18_descricao";

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
                <td width="4%" align="right" nowrap title="<?= $Tpl18_codigo ?>">
                    <label for="chave_pl18_codigo"><?= $Lpl18_codigo ?></label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("pl18_codigo", 10, $Ipl18_codigo, true, "text", 1, "", "chave_pl18_codigo"); ?>
                </td>
            </tr>
            <tr>
                <td width="4%" align="right" nowrap title="<?= $Tpl18_descricao ?>">
                    <label for="chave_pl18_descricao"> <?= $Lpl18_descricao ?></label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("pl18_descricao", 40, $Ipl18_descricao, true, "text", 4, "", "chave_pl18_descricao"); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_abrangencia.hide();">
</form>

<?php

if (!isset($pesquisa_chave)) {
    if (isset($chave_pl18_codigo) && (trim($chave_pl18_codigo) != "")) {
        $where[] = " pl18_codigo = {$chave_pl18_codigo} ";
    }
    if (isset($chave_pl18_descricao) && (trim($chave_pl18_descricao) != "")) {
        $where[] = " pl18_descricao like '{$chave_pl18_descricao}%' ";
    }
    $sWhere = implode(" and ", $where);

    $sql = "select {$campos} from planejamento.abrangencia ";
    if (!empty($where)) {
        $sql .= "where " . implode(' and ', $where);
    }

    $repassa = array();
    if (isset($chave_pl18_descricao)) {
        $repassa = ["chave_pl18_descricao" => $chave_pl18_descricao, "chave_pl18_descricao" => $chave_pl18_descricao];
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where = " pl18_codigo = {$pesquisa_chave} ";
        $sql = "select {$campos} from planejamento.abrangencia where {$where}";
        $result = db_query($sql);
        if (pg_num_rows($result) != 0) {
            $dados = db_utils::fieldsMemory($result, 0);
            echo "<script>" . $funcao_js . "('$dados->pl18_descricao', false);</script>";
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
        $('chave_pl18_codigo').value = '';
        $('chave_pl18_descricao').value = '';
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
