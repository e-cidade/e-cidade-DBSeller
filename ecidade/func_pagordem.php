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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("classes/db_pagordem_classe.php");
require_once modification("classes/db_empempenho_classe.php");

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clpagordem = new cl_pagordem;
$clempempenho = new cl_empempenho;
$rotulo = new rotulocampo;

$clpagordem->rotulo->label("e50_codord");
$clpagordem->rotulo->label("e50_numemp");

$rotulo->label("e60_codemp");
$rotulo->label("e60_numemp");
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

        <fieldset style="width: 500px">
            <legend><b>Filtros</b></legend>
            <fieldset class="separator">
                <legend><b>Empenho:</b></legend>
                <table width="100%" border="0" cellspacing="0">
                    <tr>
                        <td nowrap title="<?= $Te60_numemp ?>"><?= $Le60_codemp ?> </td>
                        <td nowrap>
                            <input name="chave_e60_codemp" size="10" type='text' onKeyPress="return js_mascara(event);">
                        </td>
                        <td nowrap title="<?= $Te50_numemp ?>"><?= $Le60_numemp ?></td>
                        <td nowrap>
                            <?php
                            db_input("e50_numemp", 10, $Ie50_numemp, true, "text", 4, "", "chave_e50_numemp");
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="separator">
                <legend><b>Pagamento:</b></legend>
                <table>
                    <tr>
                        <td width="4%" nowrap title="<?= $Te50_codord ?>">
                            <b>Nota de Liquidação: </b>
                        </td>
                        <td width="96%" align="left" nowrap colspan="3">
                            <?php
                            db_input("e50_codord", 10, $Ie50_codord, true, "text", 4, "", "chave_e50_codord");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Data Inicial:</td>
                        <td>
                            <?php
                            db_inputdata('data_inicial', '01', '01', date('Y'), true, 'text', 1);
                            ?>
                        </td>
                        <td class="bold">Data Final:</td>
                        <td>
                            <?php
                            list($diaFinal, $mesFinal, $anoFinal) = explode('-', date('d-m-Y', db_getsession('DB_datausu')));
                            db_inputdata('data_final', $diaFinal, $mesFinal, $anoFinal, true, 'text', 1);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>

        </fieldset>
        <p>
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
            <input name="limpar" type="reset" id="limpar" value="Limpar">
            <input name="Fechar" type="button" id="fechar" value="Fechar"
                   onClick="parent.db_iframe_pagordem.hide();">
        </p>
    </form>
</div>
<div class="container">
    <fieldset>
        <legend class="bold">Registros Encontrados</legend>
        <table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
            <tr>
                <td align="center" valign="top">

                    <?php
                    $whereage = "";
                    if (isset($e80_codage) && trim($e80_codage) != "") {
                        db_input("e80_codage", 8, "", true, "hidden", 3);
                        $whereage = "e80_codage is null";
                    }

                    $dbwhere = " e60_instit = " . db_getsession("DB_instit");
                    if (empty($chave_e50_numemp) && empty($chave_e60_codemp) && empty($pesquisa_chave)) {
                        $dbwhere .= " and e60_anousu = " . db_getsession("DB_anousu");
                    }

                    if (!empty($data_inicial)) {

                        $data = new DBDate($data_inicial);
                        $dbwhere .= " and e50_data >= '{$data->getDate()}' ";
                    }

                    if (!empty($data_final)) {

                        $data = new DBDate($data_final);
                        $dbwhere .= " and e50_data <= '{$data->getDate()}' ";
                    }

                    /* [Extensão] - Filtro da Despesa - Parte 1 */

                    if (!isset($pesquisa_chave)) {

                        if (isset($campos) == false) {

                            if (file_exists("funcoes/db_func_pagordem.php") == true) {
                                include modification("funcoes/db_func_pagordem.php");
                            } else {
                                $campos = "pagordem.*";
                            }

                        }

                        if (isset($chave_e50_codord) && (trim($chave_e50_codord) != "")) {
                            if (strlen($whereage) > 0) {
                                $sql = $clpagordem->sql_query_pagordemagenda("", $campos, "e50_codord",
                                    "$dbwhere and e50_codord = '$chave_e50_codord' and $whereage ");
                            } else {
                                $sql = $clpagordem->sql_query_pagordemele("", $campos, "e50_codord",
                                    "$dbwhere and e50_codord = '$chave_e50_codord' ");
                            }
                        } else {
                            if (isset($chave_e50_numemp) && (trim($chave_e50_numemp) != "")) {

                                if (strlen($whereage) > 0) {
                                    $sql = $clpagordem->sql_query_pagordemagenda("", $campos, "e50_numemp",
                                        "$dbwhere and e50_numemp = {$chave_e50_numemp} and $whereage ");
                                } else {
                                    $sql = $clpagordem->sql_query_pagordemele("", $campos, "e50_numemp",
                                        "$dbwhere and e50_numemp = {$chave_e50_numemp} ");
                                }
                            } else {
                                if (isset($chave_e60_codemp) && (trim($chave_e60_codemp) != "")) {

                                    $arr =explode("/", $chave_e60_codemp);
                                    if (count($arr) == 2 && isset($arr[1]) && $arr[1] != '') {
                                        $dbwhere_ano = " and e60_anousu = " . $arr[1];
                                    } else {
                                        $dbwhere_ano = " and e60_anousu = " . db_getsession("DB_anousu");
                                    }

                                    if (strlen($whereage) > 0) {
                                        $sql = $clpagordem->sql_query_pagordemagenda("", $campos, "e50_numemp",
                                            "$dbwhere and e60_codemp =  '" . $arr[0] . "' $dbwhere_ano and $whereage ");
                                    } else {
                                        $sql = $clpagordem->sql_query_pagordemele("", $campos, "e50_numemp",
                                            "$dbwhere and e60_codemp =  '" . $arr[0] . "' $dbwhere_ano");
                                    }

                                } else {

                                    if (isset($filtroquery) || isset($pesquisar)) {

                                        if (strlen($whereage) > 0) {
                                            $sql = $clpagordem->sql_query_pagordemagenda("", $campos, "e50_codord",
                                                "$dbwhere and $whereage");
                                        } else {
                                            $sql = $clpagordem->sql_query_pagordemele("", $campos, "e50_codord", "$dbwhere");
                                        }
                                    }

                                }
                            }
                        }
                        if (!empty($sql)) {
                            db_lovrot($sql, 15, "()", "", $funcao_js);
                        } else {
                            echo "<p class='bold'>Nenhum registro encontrado.</p>";
                        }

                    } else {
                        if ($pesquisa_chave != null && $pesquisa_chave != "") {

                            if ($whereage != "") {
                                $whereage .= " and ";
                            }
                            $whereage .= $dbwhere;

                            if (strlen($whereage) > 0) {
                                $sSql = $clpagordem->sql_query_pagordemagenda(null, "*", null,
                                    "e50_codord = '$pesquisa_chave' and $whereage");
                            } else {
                                $sSql = $clpagordem->sql_query($pesquisa_chave);
                            }

                            /* [Extensão] - Filtro da Despesa - Parte 2 */

                            $result = $clpagordem->sql_record($sSql);
                            if ($clpagordem->numrows != 0) {

                                db_fieldsmemory($result, 0);
                                echo "<script>" . $funcao_js . "('$e50_numemp',false);</script>";
                            } else {
                                echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                            }
                        } else {
                            echo "<script>" . $funcao_js . "('',false);</script>";
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
</div>

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
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
