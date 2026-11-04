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
$oRotulo->label('pl9_codigo');

$where = array();
if (!empty($_GET['planejamento'])) {
    $where[] = "pl9_planejamento = {$_GET['planejamento']}";
}

if (!empty($_GET['exercicio'])) {
    $where[] = "pl9_anoorcamento = {$_GET['exercicio']}";
}

$campos = "pl9_codigo as db_codigo, lpad(pl9_orcprograma, 4, '0') as dl_programa, o54_descr dl_descricao";
$sql = "
    select {$campos} from planejamento.programaestrategico
      join orcprograma on (o54_anousu, o54_programa) = (pl9_anoorcamento, pl9_orcprograma)
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
                <td width="4%" align="right" nowrap>
                    <label for="chave_pl9_orcprograma">Programa:</label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("pl9_orcprograma", 10, 1, true, "text", 1, "", "chave_pl9_orcprograma"); ?>
                </td>
            </tr>
            <tr>
                <td width="4%" align="right" nowrap>
                    <label for="chave_descricao">Descrição: </label>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php db_input("descricao", 40, 2, true, "text", 4, "", "chave_descricao"); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_programa.hide();">
</form>

<?php

if (!isset($pesquisa_chave)) {
    if (isset($chave_pl9_orcprograma) && (trim($chave_pl9_orcprograma) != "")) {
        $where[] = " pl9_orcprograma = {$chave_pl9_orcprograma} ";
    }
    if (isset($chave_descricao) && (trim($chave_descricao) != "")) {
        $where[] = " descricao like '{$chave_descricao}%' ";
    }
    $sWhere = implode(" and ", $where);

    if (!empty($where)) {
        $sql .= "where " . implode(' and ', $where);
    }

    $repassa = array();
    if (isset($chave_descricao)) {
        $repassa = array("chave_descricao" => $chave_descricao, "chave_descricao" => $chave_descricao);
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where[] = " pl9_orcprograma = {$pesquisa_chave} ";
        $where = implode(' and ', $where);
        $sql .= " where {$where}";

        $result = db_query($sql);
        if (pg_num_rows($result) != 0) {
            $dados = db_utils::fieldsMemory($result, 0);
            echo "<script>" . $funcao_js . "('$dados->descricao', false, '$dados->db_codigo');</script>";
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
        $('chave_pl9_orcprograma').value = '';
        $('chave_descricao').value = '';
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
