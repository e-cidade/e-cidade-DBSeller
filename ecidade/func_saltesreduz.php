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
require_once(modification("classes/db_saltes_classe.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clsaltes = new cl_saltes;
$clsaltes->rotulo->label("k13_conta");
$clsaltes->rotulo->label("k13_descr");
$clsaltes->rotulo->label("k13_reduz");

$tipoConta = !empty($_GET['tipoConta']) ? $_GET['tipoConta'] : "";
$tipoConta = !empty($_POST['tipoConta']) ? $_POST['tipoConta'] : $tipoConta;
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body onload='document.form2.chave_k13_reduz.focus();'>
<div class="container">
    <form name="form2" method="post" action="">
        <fieldset>
            <legend>Filtros</legend>

            <table class="form-container">
                <tr>
                    <td title="<?php echo $Tk13_conta ?>">
                        <?php echo $Lk13_reduz ?>
                    </td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k13_reduz", 5, $Ik13_reduz, true, "text", 4, "", "chave_k13_reduz");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?php echo $Tk13_descr ?>">
                        <?php echo $Lk13_descr ?>
                    </td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        db_input("k13_descr", 40, $Ik13_descr, true, "text", 4, "", "chave_k13_descr");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Tipo de Conta:</td>
                    <td>
                        <?php
                        $a = [
                            "extras" => 'Apenas ContaExtra',
                            "todas" => 'Todas'
                        ];
                        db_select('tipoConta', $a, false, 2);
                        ?>
                    </td>
                </tr>
            </table>

        </fieldset>
        <button name="pesquisar" type="submit" id="pesquisar2"><i class="fas fa-search"></i> Pesquisar</button>
        <button name="limpar" type="reset" id="limpar"><i class="fas fa-trash-alt"></i> Limpar</button>
        <button name="Fechar" type="button" id="fechar" onClick="parent.db_iframe_saltes.hide();">
            <i class="fas fa-window-close"></i> Fechar
        </button>
    </form>
</div>
<?php
$dbwhere = "";

/* [Extensão] - Filtro da Despesa */

$where = ["c61_instit = " . db_getsession("DB_instit")];

if (isset($ver_datalimite) && trim(@$ver_datalimite) == "1") {
    $where[] = "(k13_limite is null or k13_limite >= '" . date("Y-m-d", db_getsession("DB_datausu")) . "')";
}

if (!isset($pesquisa_chave)) {
    if (isset($campos) == false) {
        if (file_exists("funcoes/db_func_saltes.php") == true) {
            include(modification("funcoes/db_func_saltes.php"));
        } else {
            $campos = "saltes.*";
        }
    }

    if (!empty($tipoConta) && $tipoConta === 'extras') {
        $where['tipoConta'] = "substring(codigo_siconfi, 2) in ('860', '861', '862', '869')";
    }

    if (!empty($tipoConta) && $tipoConta === 'todas') {
        unset($where['tipoConta']);
    }

    if (isset($chave_k13_reduz) && (trim($chave_k13_reduz) != "")) {
        $where[] = "k13_reduz::text like '$chave_k13_reduz%'";
    }
    if (isset($chave_k13_descr) && (trim($chave_k13_descr) != "")) {
        $where[] = "k13_descr like '$chave_k13_descr%'";
    }

    $where = implode(' and ', $where);
    $sql = $clsaltes->sql_query_anousu(null, $campos, "k13_conta", $where);

    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", array(), false);
    echo '  </fieldset> ';
    echo '</div> ';
} else if ($pesquisa_chave != null && $pesquisa_chave != "") {
    $where[] = "k13_conta = $pesquisa_chave";
    $where = implode(' and ', $where);
    $sql = $clsaltes->sql_query_anousu(null, "*", "", $where);

    $result = $clsaltes->sql_record($sql);
    if ($clsaltes->numrows != 0) {
        db_fieldsmemory($result, 0);
        echo "<script>" . $funcao_js . "('$k13_descr',false);</script>";
    } else {
        echo "<script>" . $funcao_js . "('Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
    }
} else {
    echo "<script>" . $funcao_js . "('',false);</script>";
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
