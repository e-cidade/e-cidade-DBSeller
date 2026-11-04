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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_lab_atributo_classe.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"], $queryString);

$cllab_atributo = new cl_lab_atributo;
$cllab_atributo->rotulo->label("la25_i_codigo");
$cllab_atributo->rotulo->label("la25_c_descr");
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
                        <td width="4%" align="right" nowrap title="<?php
                        echo $Tla25_i_codigo ?>">
                            <?php
                            echo $Lla25_i_codigo ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("la25_i_codigo", 10, $Ila25_i_codigo, true, "text", 4, "", "chave_la25_i_codigo");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?php
                        echo $Tla25_c_descr ?>">
                            <?php
                            echo $Lla25_c_descr ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("la25_c_descr", 10, $Ila25_c_descr, true, "text", 4, "", "chave_la25_c_descr");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_lab_atributo.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php
            $campos = "lab_atributo.la25_i_codigo, ";
            $campos .= "lab_atributo.la25_c_estrutural, ";
            $campos .= "lab_atributo.la25_c_descr, ";
            $campos .= "lab_atributo.la25_i_nivel, ";
            $campos .= "lab_atributo.la25_sigla, ";
            $campos .= "lab_atributo.la25_formula, ";
            $campos .= "(select count(*) from lab_atributo as a ";
            $campos .= "     inner join lab_exameatributoligacao on la26_i_exameatributopai = a.la25_i_codigo";
            $campos .= "   where a.la25_i_codigo=lab_atributo.la25_i_codigo";
            $campos .= ") as filhos";

            if (!isset($pesquisa_chave)) {
                $sep = "";
                $where = "";

                if (isset($sintetico)) {
                    $where = " lab_atributo.la25_c_tipo='1' ";
                    $sep = " and ";
                }

                if (isset($analitico)) {
                    $where = " lab_atributo.la25_c_tipo='2' and lab_atributo.la25_i_nivel>1 ";
                    $sep = " and ";
                }

                if (isset($chave_la25_i_codigo) && (trim($chave_la25_i_codigo) != "")) {
                    $sql = $cllab_atributo->sql_query(
                        "",
                        $campos,
                        "la25_i_codigo",
                        " la25_i_codigo=$chave_la25_i_codigo $sep$where "
                    );
                } else {
                    if (isset($chave_la25_c_descr) && (trim($chave_la25_c_descr) != "")) {
                        $sql = $cllab_atributo->sql_query(
                            "",
                            $campos,
                            "la25_c_descr",
                            " la25_c_descr like '$chave_la25_c_descr%' $sep$where "
                        );
                    } else {
                        $sql = $cllab_atributo->sql_query("", $campos, "la25_i_codigo", "$where");
                    }
                }

                $repassa = array();

                if (isset($chave_la25_i_codigo)) {
                    $repassa = array(
                        "chave_la25_i_codigo" => $chave_la25_i_codigo,
                        "chave_la25_c_descr" => $chave_la25_c_descr
                    );
                }
                db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
            } else {
                if ($pesquisa_chave != null && $pesquisa_chave != "") {
                    $sql = $cllab_atributo->sql_query($pesquisa_chave, $campos);

                    if (isset($sintetico)) {
                        $where = " lab_atributo.la25_c_tipo='1'";
                        $where .= " and lab_atributo.la25_i_codigo = $pesquisa_chave";
                        $sql = $cllab_atributo->sql_query($pesquisa_chave, $campos, null, $where);
                    }
                    $result = $cllab_atributo->sql_record($sql);

                    if ($cllab_atributo->numrows != 0) {
                        db_fieldsmemory($result, 0);

                        if (($filhos == "") || ($filhos == null)) {
                            $filhos = 0;
                        }
                        echo "<script>".$funcao_js."('$la25_c_descr',false,'$la25_c_estrutural',$la25_i_nivel,$filhos,'$la25_sigla','$la25_formula');</script>";
                    } else {
                        echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
                    }
                } else {
                    echo "<script>".$funcao_js."('',false);</script>";
                }
            }
            ?>
        </td>
    </tr>
</table>
</body>
</html>
<script>
    js_tabulacaoforms("form2", "chave_la25_i_codigo", true, 1, "chave_la25_i_codigo", true);
</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
