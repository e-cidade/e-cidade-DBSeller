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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_orctiporec_classe.php"));
db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
$clorctiporec = new cl_orctiporec;
$clorctiporec->rotulo->label("o15_codigo");
$clorctiporec->rotulo->label("o15_descr");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<div class="container">
    <form name="form2" method="post" action="">
        <fieldset class="container">
            <legend>Filtros</legend>

            <table width="35%" border="0" align="center" cellspacing="0">
                <tr>
                    <td width="4%" align="right" nowrap title="<?= $To15_codigo ?>">
                        <?= $Lo15_codigo ?>
                    </td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("o15_codigo", 4, $Io15_codigo, true, "text", 4, "", "chave_o15_codigo");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="4%" align="right" nowrap title="<?= $To15_descr ?>">
                        <?= $Lo15_descr ?>
                    </td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("o15_descr", 30, $Io15_descr, true, "text", 4, "", "chave_o15_descr");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">

                    </td>
                </tr>
            </table>

        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar"
               onClick="parent.db_iframe_orctiporec.hide();">
    </form>
</div>
<?php
$campos = ["distinct o15_codigo", "o15_descr"];
$where = ["o70_anousu = " . db_getsession('DB_anousu')];
if (!isset($pesquisa_chave)) {
    if (!empty($chave_o15_codigo)) {
        $where[] = "o15_codigo = {$chave_o15_codigo}";
    } else if (!empty($chave_o15_descr)) {
        $where[] = "o15_descr like '$chave_o15_descr%'";
    }

    $sql = $clorctiporec->sql_recurso_receita($campos, "o15_codigo", $where);

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js);
    echo '  </fieldset> ';
    echo '</div> ';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $where[] = "o15_codigo = {$pesquisa_chave}";
        $result = db_query($clorctiporec->sql_recurso_receita($campos, null, $where));
        if (pg_num_rows($result) > 0) {
            db_fieldsmemory($result, 0);
            echo "<script>" . $funcao_js . "('$o15_descr',false);</script>";
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

<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
