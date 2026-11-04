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
include(modification("classes/db_protelac_classe.php"));

db_postmemory($HTTP_POST_VARS);

parse_str($_SERVER['QUERY_STRING'], $query);

$clprotelac = new cl_protelac;

$clprotelac->rotulo->label("h19_codigo");
$clprotelac->rotulo->label("h19_assent");
$clprotelac->rotulo->label("h19_instit");
$iInstit = db_getsession("DB_instit");

?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
        <tr>
            <td height="63" align="center" valign="top">
                <table width="35%" border="0" align="center" cellspacing="0">
                    <form name="form2" method="post" action="">
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Th19_codigo ?>">
                                <?= $Lh19_codigo ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("h19_codigo", 6, $Ih19_codigo, true, "text", 4, "", "chave_h19_codigo");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="4%" align="right" nowrap title="<?= $Th19_assent ?>">
                                <?= $Lh19_assent ?>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input("h19_assent", 6, $Ih19_assent, true, "text", 4, "", "chave_h19_assent");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                                <input name="limpar" type="reset" id="limpar" value="Limpar">
                                <input name="fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_protelac.hide();">
                            </td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top">
                <?php
                if (!isset($pesquisa_chave)) {
                    if (isset($campos) == false) {
                        if (file_exists("funcoes/db_func_protelac.php") == true) {
                            include(modification("funcoes/db_func_protelac.php"));
                        } else {
                            $campos = "protelac.*";
                        }
                    }
                    if (isset($chave_h19_codigo) && (trim($chave_h19_codigo) != "")) {
                        $sql = $clprotelac->sql_query($chave_h19_codigo, $iInstit, $campos, "h19_codigo");
                    } else if (isset($chave_h19_assent) && (trim($chave_h19_assent) != "")) {
                        $sql = $clprotelac->sql_query("", $campos, $iInstit, "h19_assent", " h19_assent like '$chave_h19_assent%' ");
                    } else {
                        $sql = $clprotelac->sql_query("", $iInstit, $campos, "h19_codigo", "");
                    }

                    $repassa = array();

                    if (isset($chave_h19_assent)) {
                        $repassa = array("chave_h19_codigo" => $chave_h19_codigo, "chave_h19_assent" => $chave_h19_assent);
                    }

                    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
                } else {
                    if ($pesquisa_chave != null && $pesquisa_chave != "") {
                        $result = $clprotelac->sql_record($clprotelac->sql_query($pesquisa_chave, $iInstit));
                        if ($clprotelac->numrows != 0) {
                            db_fieldsmemory($result, 0);
                            echo "<script>" . $funcao_js . "('$h19_assent',false);</script>";
                        } else {
                            echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") n�o Encontrado',true);</script>";
                        }
                    } else {
                        echo "<script>" . $funcao_js . "('',false);</script>";
                    }
                }
                ?>
            </td>
        </tr>
    </table>
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
    js_tabulacaoforms("form2", "chave_h19_assent", true, 1, "chave_h19_assent", true);
</script>
<script type="text/javascript">
    (function() {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
