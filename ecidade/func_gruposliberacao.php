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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_gruposliberacao_classe.php');

db_postmemory($_POST);
parse_str($_SERVER['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

$clgruposliberacao = new cl_gruposliberacao();
$clgruposliberacao->rotulo->label('la71_sequencial');
$clgruposliberacao->rotulo->label('la71_descricao');

$whereUsuario = 'la71_dbusuarios = '.db_getsession("DB_id_usuario");

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="estilos.css">
    <script src="scripts/scripts.js"></script>
</head>
<body>
<form name="form2" method="post" class="container">
    <fieldset>
        <legend>Dados para Pesquisa</legend>
        <table width="35%" border="0" align="center" cellspacing="3" class="form-container">
            <tr>
                <td><label for="chave_la71_sequencial"><?= $Lla71_sequencial ?></label></td>
                <td><?php
                    db_input(
                        "la71_sequencial",
                        10,
                        $Ila71_sequencial,
                        true,
                        "text",
                        4,
                        "",
                        "chave_la71_sequencial"
                    ); ?></td>
            </tr>
            <tr>
                <td><label for="chave_la71_descricao"><?= $Lla71_descricao ?></label></td>
                <td><?php
                    db_input(
                        "la71_descricao",
                        50,
                        $Ila71_descricao,
                        true,
                        "text",
                        4,
                        "",
                        "chave_la71_descricao"
                    ); ?></td>
            </tr>
        </table>
    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onclick="parent.db_iframe_gruposliberacao.hide();">
</form>
<?php
if (isset($pesquisa_chave) === false) {
    if (isset($campos) === false) {
        if (file_exists("funcoes/db_func_gruposliberacao.php") === true) {
            include(modification("funcoes/db_func_gruposliberacao.php"));
        } else {
            $campos = "gruposliberacao.*";
        }
    }
    if (isset($chave_la71_sequencial) && (trim($chave_la71_sequencial) != "")) {
        $sql = $clgruposliberacao->sql_query(
            null,
            $campos,
            "la71_sequencial",
            " gruposliberacao.la71_sequencial = {$chave_la71_sequencial} and $whereUsuario"
        );
    } else {
        if (isset($chave_la71_descricao) && (trim($chave_la71_descricao) != "")) {
            $sql = $clgruposliberacao->sql_query(
                null,
                $campos,
                "la71_sequencial",
                " la71_descricao like '$chave_la71_descricao%' and {$whereUsuario}"
            );
        } else {
            $sql = $clgruposliberacao->sql_query(null, $campos, "la71_sequencial", $whereUsuario);
        }
    }
    $repassa = array();
    if (isset($chave_la71_sequencial)) {
        $repassa = array(
            "chave_la71_sequencial" => $chave_la71_sequencial,
            "chave_la71_descricao" => $chave_la71_descricao
        );
    }
    echo '<div class="container">';
    echo '  <fieldset>';
    echo '    <legend>Resultado da Pesquisa</legend>';
    db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
    echo '  </fieldset>';
    echo '</div>';
} else {
    if ($pesquisa_chave != null && $pesquisa_chave != "") {
        $whereUsuario .= " and la71_sequencial = {$pesquisa_chave}";
        $result = $clgruposliberacao->sql_record(
            $clgruposliberacao->sql_query($pesquisa_chave, '*', null, $whereUsuario)
        );
        if ($clgruposliberacao->numrows != 0) {
            db_fieldsmemory($result, 0);
            echo "<script>".$funcao_js."('$la71_descricao',false);</script>";
        } else {
            echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
        }
    } else {
        echo "<script>{$funcao_js}('', false);</script>";
    }
}
?>
</body>
</html>
<?php
if (isset($pesquisa_chave) === false) { ?>
    <script rel="script" type="text/javascript">
    </script>
    <?php
} ?>
<script rel="script" type="text/javascript">
    js_tabulacaoforms("form2", "chave_la71_sequencial", true, 1, "chave_la71_sequencial", true);
</script>
