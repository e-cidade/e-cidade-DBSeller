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
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clretencaotiporec = new cl_retencaotiporec;
$clretencaotiporec->rotulo->label();

$oGet = db_utils::postMemory($_GET);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>

    <div class="container">

        <form name="form2" method="post" action="" >

        <fieldset>
        <legend>Retenções</legend>
            <table>
              <tr>
                <td title="<?=$Te21_sequencial?>">
                  <?=$Le21_sequencial?>
                </td>
                <td>
                  <?php
                    db_input("e21_sequencial", 10, $Ie21_sequencial, true, "text", 4, "", "chave_e21_sequencial");
                    ?>
                </td>
              </tr>
              <tr>
                <td title="<?=$Te21_descricao?>">
                  <?=$Le21_descricao?>
                </td>
                <td>
                  <?php
                    db_input("e21_descricao", 40, $Ie21_descricao, true, "text", 4, "", "chave_e21_descricao");
                    ?>
                </td>
              </tr>
              <?php
                if (!isset($somenteAtivo)) {
                    ?>
              <tr>
                <td title="<?=$Te21_ativo?>">
                    <?=$Le21_ativo?>
                </td>
                <td>
                    <?php
                    db_select("e21_ativo", ["" => "Todos", "t" => "Ativo", "f" => "Inativo"], true, 1);
                    ?>
                </td>
              </tr>
                <?php } ?>
            </table>
        </fieldset>

        <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
        <input name="limpar" type="reset" id="limpar" value="Limpar" >
        <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_retencaotiporec.hide();">

        </form>

      <?php
        $where = [];
        $where[] = ' e21_instit = '.db_getsession("DB_instit");

        if (isset($tipo) && trim($tipo) != '') {
            $where[] = " e21_retencaotiporecgrupo = {$tipo} ";
        }

        if (isset($oGet->chave_pesquisa_in) && trim($oGet->chave_pesquisa_in) != '') {
            $where[] = " e21_sequencial in({$oGet->chave_pesquisa_in})";
        }

        if (!isset($somenteAtivo)) {
            if (isset($e21_ativo) && !empty($e21_ativo)) {
                $where[] = " e21_ativo = '{$e21_ativo}' ";
            }
        } else {
            $where[] = " e21_ativo is true ";
        }

        if (isset($tipoCalc) && trim($tipoCalc) != '' && preg_match('/^\d+(,\d+)*$/', $tipoCalc)) {
            $where[] = " e21_retencaotipocalc in ($tipoCalc) ";
        }

        if (!isset($pesquisa_chave)) {
            if (isset($campos)==false) {
                if (file_exists("funcoes/db_func_retencaotiporec.php")==true) {
                    include(modification("funcoes/db_func_retencaotiporec.php"));
                } else {
                    $campos = "retencaotiporec.*";
                }
            }

            if (isset($chave_e21_sequencial) && (trim($chave_e21_sequencial)!="")) {
                 $where[] = " e21_sequencial = {$chave_e21_sequencial}";
            } elseif (isset($chave_e21_descricao) && (trim($chave_e21_descricao)!="")) {
                $where[] = " e21_descricao ilike '$chave_e21_descricao%' ";
            }

            $sql = $clretencaotiporec->sql_query(null, $campos, "e21_sequencial", implode(" and ", $where));

            $repassa = array();
            if (isset($chave_e21_sequencial)) {
                $repassa = array("chave_e21_sequencial" => $chave_e21_sequencial,
                           "chave_e21_descricao"  => $chave_e21_descricao);
            }

            db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
        } else {
            if ($pesquisa_chave!=null && $pesquisa_chave!="") {
                $where[] = "e21_sequencial = {$pesquisa_chave}";

                $result = $clretencaotiporec->sql_record(
                    $clretencaotiporec->sql_query(
                        null,
                        "*",
                        null,
                        implode(" and ", $where)
                    )
                );

                if ($clretencaotiporec->numrows!=0) {
                      db_fieldsmemory($result, 0);
                      echo "<script>".$funcao_js."('$e21_descricao',false,'{$e21_aliquota}');</script>";
                } else {
                     echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
                }
            } else {
                echo "<script>".$funcao_js."('',false);</script>";
            }
        }
        ?>

    </div>

</body>
</html>
<script>
js_tabulacaoforms("form2","chave_e21_sequencial",true,1,"chave_e21_sequencial",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
