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
include(modification("classes/db_lote_classe.php"));
db_postmemory($HTTP_POST_VARS);

if (!isset($pesquisar)) {
    parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
}
$cllote = new cl_lote;
$cllote->rotulo->label();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script>
        window.ECIDADE_REQUEST_PATH = '<?= ECIDADE_REQUEST_PATH ?>'
    </script>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body class="body-default">
<div class="container">
    <form name="form2" method="post" action="">
        <fieldset>
            <legend>Filtros:</legend>
            <table class="form-container">
                <tr>
                    <td title="<?php echo $Tj34_idbql ?>">
                        <?php echo $Lj34_idbql ?>
                    </td>
                    <td>
                        <?php
                        db_input("j34_idbql", 10, $Ij34_idbql, true, "text", 4, "", "chave_j34_idbql");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?php echo $Tj34_setor ?>">
                        <?php echo $Lj34_setor ?>
                    </td>
                    <td>
                        <?php
                        db_input("j34_setor", 10, $Ij34_setor, true, "text", 4, "", "chave_j34_setor");
                        ?>
                        <?php echo $Lj34_quadra ?>

                        <?php
                        db_input("j34_quadra", 10, $Ij34_quadra, true, "text", 4, "", "chave_j34_quadra");
                        ?>
                        <?php echo $Lj34_lote ?>

                        <?php
                        db_input("j34_lote", 10, $Ij34_lote, true, "text", 4, "", "chave_j34_lote");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
    </form>
</div>

<?php

$sNumero = " case                                                                        ";
$sNumero .= "   when length(trim(cast(j15_numero as varchar))) > 0 and j39_matric is null ";
$sNumero .= "     then j15_numero                                                         ";
$sNumero .= "   else j39_numero                                                           ";
$sNumero .= " end as j39_numero ";

$sql = "";
if (!isset($pesquisa_chave)) {

    if (isset($campos) == false) {
        $campos = "lote.*";
    }
    if (!isset($pesquisar)) {

        $chave = "";
        if (isset($setor) && !empty($setor)) {
            $setor = str_pad($setor, 4, "0", STR_PAD_LEFT);
        }
        $chave .= "lote.j34_setor = '$setor' ";
        if (isset($quadra) && !empty($quadra)) {

            if (!empty($chave)) {
                $chave .= " and ";
            }
            $quadra = str_pad($quadra, 4, "0", STR_PAD_LEFT);
            $chave .= " lote.j34_quadra = '$quadra' ";
        }
        if (isset($lote) && !empty($lote)) {

            if (!empty($chave)) {
                $chave .= " and ";
            }
            $lote = str_pad($lote, 4, "0", STR_PAD_LEFT);
            $chave .= " lote.j34_lote = '$lote' ";
        }
        if ($chave != "") {

            // Filtra a query por tipo de imóvel Urbano
            $chave .= " and j01_tipoimovel = 1 ";

            $sCampos  = "distinct on (iptubase.j01_matric) iptubase.j01_matric, iptuant.j40_refant, ";
            $sCampos .= "iptubase.j01_baixa, ";
            $sCampos .= "(select rvnome as z01_nome ";
            $sCampos .= "   from fc_busca_envolvidos(false, (select fc_regrasconfig ";
            $sCampos .= "                                      from fc_regrasconfig(1)), 'M', iptubase.j01_matric) ";
            $sCampos .= "                                     limit 1)#";
            $sCampos .= "j34_idbql#lote.j34_setor#lote.j34_quadra#lote.j34_lote#loteloc.j06_setorloc#";
            $sCampos .= "loteloc.j06_quadraloc#loteloc.j06_lote#lote.j34_area#lote.j34_bairro#lote.j34_areal#";
            $sCampos .= "lote.j34_totcon#lote.j34_zona#lote.j34_quamat#lote.j34_areapreservada#j14_codigo,j14_tipo,";
            $sCampos .= "j14_nome,$sNumero,j39_compl";

            $sql = $cllote->sql_query_dados_lote("", $sCampos, "iptubase.j01_matric, lote.j34_setor#lote.j34_quadra#lote.j34_lote", $chave);
        }
    } else {

        if (isset($chave_j34_idbql) && (trim($chave_j34_idbql) != "")) {

            $sCampos  = "distinct on (iptubase.j01_matric) iptubase.j01_matric, iptuant.j40_refant, ";
            $sCampos .= "iptubase.j01_baixa, ";
            $sCampos .= "(select rvnome as z01_nome";
            $sCampos .= "   from fc_busca_envolvidos(false, (select fc_regrasconfig ";
            $sCampos .= "                                      from fc_regrasconfig(1)), 'M', iptubase.j01_matric)";
            $sCampos .= "                                     limit 1)#";
            $sCampos .= "lote.*,j14_codigo,j14_tipo,j14_nome,$sNumero,j39_compl";

            $sql = $cllote->sql_query_dados_lote($chave_j34_idbql, $sCampos, "iptubase.j01_matric, lote.j34_idbql");
        } else {

            $wx = "";
            $wlote = "";
            $wquadra = "";
            $wsetor = "";
            if (isset($chave_j34_setor) && ($chave_j34_setor != "")) {

                $chave_j34_setor = str_pad($chave_j34_setor, 4, "0", STR_PAD_LEFT);
                $wsetor = "lote.j34_setor='$chave_j34_setor'";
                $wx = " and ";
            }
            if (isset($chave_j34_quadra) && ($chave_j34_quadra != "")) {

                $chave_j34_quadra = str_pad($chave_j34_quadra, 4, "0", STR_PAD_LEFT);
                $wquadra = $wx . "lote.j34_quadra='$chave_j34_quadra'";
                $wx = " and ";
            }
            if (isset($chave_j34_lote) && ($chave_j34_lote != "")) {

                $chave_j34_lote = str_pad($chave_j34_lote, 4, "0", STR_PAD_LEFT);
                $wlote = $wx . "lote.j34_lote='$chave_j34_lote'";
                $wx = " and ";
            }

            $sCampos  = "distinct on (iptubase.j01_matric) iptubase.j01_matric, iptuant.j40_refant, ";
            $sCampos .= "iptubase.j01_baixa, ";
            $sCampos .= "(select rvnome as z01_nome";
            $sCampos .= "   from fc_busca_envolvidos(false, (select fc_regrasconfig ";
            $sCampos .= "                                      from fc_regrasconfig(1)), 'M', iptubase.j01_matric) ";
            $sCampos .= "                                     limit 1)#";
            $sCampos .= "lote.*,j14_codigo,j14_tipo,j14_nome,{$sNumero},j39_compl";

            if ($wx != "") {
                $sOrdem = "iptubase.j01_matric";
                $sSetorQuadraLote = $wsetor . $wquadra . $wlote;
                if (!empty($sSetorQuadraLote)) {
                    $sOrdem .= ", {$sSetorQuadraLote}";
                }

                $sql = $cllote->sql_query_dados_lote("", $sCampos, "lote.j34_setor", $sOrdem);
            } else if ($wx == "" && isset($pesquisar) || isset($filtroquery)) {
                $sql = $cllote->sql_query_dados_lote("", $sCampos, "lote.j34_idbql", "");
            }
        }
    }

    if ($sql != "") {
        echo '<div class="container">';
        echo '<fieldset>';
        echo '<legend> Resultados </legend>';
        db_lovrot($sql, 50, "()", "", $funcao_js, "", "NoMe", [], true, [], null, null, true);
        echo '</fieldset>';
        echo '</div>';
    }
} else {

    $result = $cllote->sql_record($cllote->sql_query_file($pesquisa_chave));
    if ($cllote->numrows != 0) {

        db_fieldsmemory($result, 0);
        echo "<script>" . $funcao_js . "('$j34_setor',false);</script>";
    } else {
        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
    }
}
?>

</body>
</html>
<?php
if (!isset($pesquisa_chave)) {
    ?>
    <script>
        document.form2.chave_j34_idbql.focus();
        document.form2.chave_j34_idbql.select();
    </script>
    <?php
}
?>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
