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
include(modification("classes/db_cidadao_classe.php"));
require_once(modification("libs/db_utils.php"));

db_postmemory($HTTP_POST_VARS);
$oSource = $oGet = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);

$campos = isset($oGet->campos) ? implode(explode('|', $oGet->campos), ', ') : 'escola.cboocupacao.*';
$funcao_js = isset($oGet->funcao_js) ? $oGet->funcao_js : '';

if (get_object_vars($oPost)) {
    $oSource = $oPost;
}

$codigo = isset($oSource->codigo) ? $oSource->codigo : '';
$searchTitulo = $titulo = isset($oSource->titulo) ? $oSource->titulo : '';
$searchTitulo = isset($oSource->searchTitulo) ? $oSource->searchTitulo : $searchTitulo;

$pesquisa = !empty($HTTP_POST_VARS);

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body class="body-default">
    <div class="container">
        <form name="form1" method="post" action="">
            <fieldset>
            <legend>Filtros</legend>
                <table class="form-container">
                    <tr>
                        <td style="min-width: 65px"><label for="mo304_codigo">Código:</label></td>
                        <td>
                        <?php
                            db_input(
                                "",
                                10,
                                $codigo,
                                true,
                                "text",
                                4,
                                "onFocus=\"nextfield='pesquisar2'\"",
                                "codigo"
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="mo304_titulo">Descrição:</label></td>
                        <td>
                        <?php
                            db_input(
                                "",
                                50,
                                '',
                                true,
                                "text",
                                4,
                                " onFocus=\"nextfield='pesquisar2'\"",
                                "titulo"
                            );
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
                <input name="pesquisar" type="submit" style="float:left!important;margin-right:.5rem"
                    id="pesquisar2" value="Pesquisar">
                <input name="limpar"    type="submit" style="float:left!important" id="limpar"
                    value="Limpar" onClick="form1?.reset();">
                <input name="fechar"    type="button" style="float:right!important" id="fechar"
                    value="Fechar" onClick="parent.db_iframe_cidadao.hide();">
        </form>
    </div>
    <div class="container">
        <?php
        $dbWhere = [];

        if (trim($codigo) != '') {
            $dbWhere[] = " mo304_codigo = '{$codigo}' ";
        } elseif (trim($titulo) != '') {
            $dbWhere[] = " mo304_titulo ILIKE '%{$titulo}%' ";
        }

        $dbWhere = implode(' and ', $dbWhere);
        $dbWhere = $dbWhere ? ' where ' . $dbWhere : '';

        $sql = "select {$campos} from escola.cboocupacao {$dbWhere} order by mo304_codigo ASC ";

        if ($codigo && $dbWhere) {
            $result = db_query($sql);
            $numrows = pg_numrows($result);

            if ($numrows == 1) {
                $dasd  = db_fieldsmemory($result, 0);
                echo "<script>" . $funcao_js . "('$mo304_codigo', '$mo304_titulo');</script>";
            } else {
                echo "<script>" . $funcao_js . "('', '', 'Ocupação não encontrado');</script>";
            }
        } else {
            $repassa = array();
            $repassa = [
                'searchTitulo' => $searchTitulo,
            ];

            db_lovrot($sql, 15, "()", "", $funcao_js . '|mo304_codigo|mo304_titulo', "", "NoMe", $repassa);
        }
        ?>
    </div>
    </body>
</html>
<script type="text/javascript">

(function() {
    var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();

document.getElementById('titulo').value = '<?=$searchTitulo?>';

let columns = ['Código', 'Descrição'];
document.querySelectorAll('.DBLovrotTdCabecalho').forEach((elem, key) => {
    elem.innerHTML = columns[key]
});
console.log();
</script>
