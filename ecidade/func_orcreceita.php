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
require_once(modification("classes/db_orcreceita_classe.php"));
require_once(modification("classes/db_orcfontes_classe.php"));
db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
$clorcreceita = new cl_orcreceita;
$clorcfontes = new cl_orcfontes;
$clorcreceita->rotulo->label("o70_anousu");
$clorcreceita->rotulo->label("o70_codrec");
$clorcreceita->rotulo->label("o70_codfon");
$clorcfontes->rotulo->label("o57_fonte");
$clorcfontes->rotulo->label("o57_descr");

$estrutural = false;

if (isset($pesquisa_estrutural)) {
    $estrutural = true;
}

$where = [];
if (isset($lReceitaLancada)) {
    $where = ['o70_reclan is false'];
    if ($lReceitaLancada == 'true') {
        $where = ['o70_reclan is true'];
    }
}
$where[] = "o70_anousu = " . db_getsession("DB_anousu");
$where[] = "o70_instit = " . db_getsession("DB_instit");

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
                        <td width="4%" align="right" nowrap title="<?= $To70_codrec ?>"><?= $Lo70_codrec ?></td>
                        <td width="96%" align="left" nowrap>
                            <?php db_input("o70_codrec", 6, $Io70_codrec, true, "text", 4, "", "chave_o70_codrec"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $To70_codfon ?>"><?= $Lo70_codfon ?></td>
                        <td width="96%" align="left" nowrap>
                            <?php db_input("o70_codfon", 6, $Io70_codfon, true, "text", 4, "", "chave_o70_codfon"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $To57_fonte ?>"><?= $Lo57_fonte ?></td>
                        <td width="96%" align="left" nowrap>
                            <?php db_input("o57_fonte", 15, $Io57_fonte, true, "text", 4, "", "chave_o57_fonte"); ?> %
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="right" nowrap title="<?= $To57_descr ?>"><?= $Lo57_descr ?></td>
                        <td width="96%" align="left" nowrap>
                            <?php db_input("o57_descr", 40, $Io57_descr, true, "text", 4, "", "chave_o57_descr"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_orcreceita.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php

            if (!isset($pesquisa_chave) && !$estrutural) {
                if (isset($campos) == false) {
                    if (file_exists("funcoes/db_func_orcreceita.php") == true) {
                        include(modification("funcoes/db_func_orcreceita.php"));
                    } else {
                        $campos = "orcreceita.*";
                    }
                }

                if (isset($chave_o70_codrec) && (trim($chave_o70_codrec) != "")) {
                    $where[] = "o70_codrec = {$chave_o70_codrec}";
                } elseif (isset($chave_o70_codfon) && (trim($chave_o70_codfon) != "")) {
                    $where[] = "o70_codfon like '{$chave_o70_codfon}%'";
                } elseif (isset($chave_o57_descr) && (trim($chave_o57_descr) != "")) {
                    $where[] = "upper(o57_descr) like '{$chave_o57_descr}%'";
                } elseif (isset($chave_o57_fonte) && (trim($chave_o57_fonte) != "")) {
                    $where[] = "o57_fonte  like '{$chave_o57_fonte}%'";
                }
                $where = implode(' and ', $where);
                $sql = $clorcreceita->sql_query(null, null, $campos, "o70_anousu#o70_codrec", $where);

                db_lovrot($sql, 15, "()", "", $funcao_js);
            } else {
                if ($pesquisa_chave != null && $pesquisa_chave != "") {
                    $where[] = "o70_codrec = {$pesquisa_chave}";
                    $where = implode(' and ', $where);
                    $sql = $clorcreceita->sql_query(null, null, "*", null, $where);
                    if (isset($o50_estrutreceita) && (trim($o50_estrutreceita) != "")) {
                        $sql = "select * from ({$sql}) as x where o50_estrutreceita  = '{$o50_estrutreceita}'";
                    }
                    $result = $clorcreceita->sql_record($sql);
                    if ($clorcreceita->numrows != 0) {
                        db_fieldsmemory($result, 0);
                        echo "<script>" . $funcao_js . "('$o57_descr',false);</script>";
                    } else {
                        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                    }
                } else {
                    if ($estrutural) {
                        $campos = "
                            orcreceita.o70_codrec,
                            fc_estruturalreceita(" . db_getsession("DB_anousu") . ",orcreceita.o70_codrec) as o50_estrutreceita,
                            o57_descr,
                            c58_descr as DL_Caracteristica_Peculiar,
                            o15_recurso,
                            o15_descr,
                            orcreceita.o70_valor,
                            orcreceita.o70_reclan,
                            nomeinst,
                            orcreceita.o70_anousu as db_o70_anousu,
                            o57_fonte as db_o57_fonte,
                            o57_descr as db_o57_descr
                        ";
                        $where = implode(' and ', $where);
                        $sql = $clorcreceita->sql_query(null, null, $campos, null, $where);
                        if (isset($o50_estrutreceita) && (trim($o50_estrutreceita) != "")) {
                            $sql = "select * from ({$sql}) as x where o50_estrutreceita  = '{$o50_estrutreceita}'";
                        }

                        $result = $clorcreceita->sql_record($sql);
                        if ($clorcreceita->numrows != 0) {
                            db_fieldsmemory($result, 0);
                            echo "<script>" . $funcao_js . "('$o57_descr',false);</script>";
                        } else {
                            echo "<script>" . $funcao_js . "('Estrutural (" . $o50_estrutreceita . ") não Encontrado',true);</script>";
                        }   
                    } else {
                        echo "<script>" . $funcao_js . "('',false);</script>";
                    }
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
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
