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
require_once(modification("classes/db_tabrec_classe.php"));
db_postmemory($_POST);
db_postmemory($_GET);
parse_str($_SERVER["QUERY_STRING"]);

$cltabrec = new cl_tabrec();
$cltabrec->rotulo->label("k02_codigo");
$cltabrec->rotulo->label("k02_descr");
$cltabrec->rotulo->label("k02_drecei");

$clrotulo = new rotulocampo();

$clrotulo->label("o70_codrec");
$clrotulo->label("c61_reduz");

$dataSessao = date('Y-m-d', db_getsession("DB_datausu"));
$where = ["(k02_limite is null or k02_limite >= '{$dataSessao}')"];
$campos = ['dados.*', 'k00_descr'];

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript"
            src="scripts/scripts.js"></script>
</head>
<body>
<div class="container">
    <form name="form2" method="post" action="">
        <fieldset>
            <legend>Filtros</legend>
            <table width="35%" border="0" align="center" cellspacing="0">
                <tr>
                    <td width="4%" align="right" nowrap title="<?= $Tk02_codigo ?>"><?= $Lk02_codigo ?></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k02_codigo", 4, $Ik02_codigo, true, "text", 4, "", "chave_k02_codigo");
                        ?>
                    </td>
                    <td width="4%" align="right" nowrap title="<?= $Tk02_descr ?>"><?= $Lk02_descr ?></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k02_descr", 15, $Ik02_descr, true, "text", 4, "", "chave_k02_descr");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="4%" align="right" nowrap><b> Estrutural </b></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k02_estorc", 15, '', true, "text", 4, "", "chave_k02_estorc");
                        ?>
                    </td>

                    <td width="4%" align="right" nowrap title="<?= $Tk02_drecei ?>"><?= $Lk02_drecei ?></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k02_drecei", 40, $Ik02_drecei, true, "text", 4, "", "chave_k02_drecei");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="4%" align="right" nowrap><strong> Extra-Orcamentário:
                        </strong></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("c61_reduz", 8, $Ic61_reduz, true, "text", 2, "", "chave_c61_reduz");
                        ?>
                    </td>

                    <td width="4%" align="right" nowrap><strong>Orçamentário:</strong></td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("o70_codrec", 8, $Io70_codrec, true, "text", 2, "", "chave_o70_codrec");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center">

                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar">
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_tabrec.hide();">
    </form>
</div>

<?php

if (!empty($tiporec)) {
    $where[] = "k02_tipo = '{$tiporec}'";
}

if (!isset($pesquisa_chave)) {
    if (isset($chave_k02_codigo) && (trim($chave_k02_codigo) != "")) {
        $where[] = "k02_codigo = {$chave_k02_codigo}";
    } else if (isset($chave_k02_descr) && (trim($chave_k02_descr) != "")) {
        $where[] = "upper(k02_descr) like '{$chave_k02_descr}%'";
    } else if (isset($chave_k02_drecei) && (trim($chave_k02_drecei) != "")) {
        $where[] = "upper(k02_drecei) like '{$chave_k02_drecei}%'";
    } else if (isset($chave_c61_reduz) && (trim($chave_c61_reduz) != "")) {
        $where[] = "c61_reduz = {$chave_c61_reduz}";
    } else if (isset($chave_o70_codrec) && (trim($chave_o70_codrec) != "")) {
        $where[] = "o70_codrec = {$chave_o70_codrec}";
    } else if (isset($chave_k02_estorc) && (trim($chave_k02_estorc) != "")) {
        $where[] = "k02_estorc like '{$chave_k02_estorc}%'";
    }

    $sql = $cltabrec->sql_filtroReceitas($campos, $where);

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';

    db_lovrot($sql, 15, "()", "", $funcao_js);

    echo '  </fieldset>';
    echo '</div>';

} else {
    $where[] = "dados.k02_codigo = {$pesquisa_chave}";

    $sql = $cltabrec->sql_filtroReceitas($campos, $where);

    $result = db_query($sql);
    if (pg_num_rows($result) != 0) {
        db_fieldsmemory($result, 0);
        echo "<script>" . $funcao_js . "('$k02_drecei',false,'$db_id_recurso','$dl_tipo_debito','$k00_descr','$k02_descr');</script>";
    } else {
        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
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
