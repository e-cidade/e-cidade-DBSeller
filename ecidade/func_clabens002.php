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
require_once(modification("classes/db_clabens_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clclabens = new cl_clabens;
$clclabens->rotulo->label("t64_codcla");
$clclabens->rotulo->label("t64_descr");
$clclabens->rotulo->label("t64_ativa");
$iInstituicaoSessao = db_getsession("DB_instit");
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  </head>
  <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" width="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
      <tr>
        <td height="63" align="center" valign="top">
          <table width="35%" border="0" align="center" cellspacing="0">
            <form name="form2" method="post" action="" >
              <tr>
                <td width="4%" align="right" nowrap title="<?php echo $Tt64_codcla;?>">
                  <?php echo $Lt64_codcla;?>
                </td>
                <td width="96%" align="left" nowrap>
                  <?php
                    db_input("t64_codcla", 10, $It64_codcla, true, "text", 4, "", "chave_t64_codcla");
                    ?>
                </td>
              <tr/>
              <tr>
                <td width="4%" align="right" nowrap title="<?php echo $Tt64_ativa;?>">
                      <?php echo $Lt64_ativa;?>
                </td>
                <td>
                  <?php
                    $x = array("t" => "Ativas", "f" => "Inativas", "1" => "Todas as classificações");
                    db_select('situacao', $x, true, 2);
                    ?>
                </td>
              </tr>
              <tr>
                <td width="4%" align="right" nowrap title="<?php echo $Tt64_descr;?>">
                  <?php echo $Lt64_descr;?>
                </td>
                <td width="96%" align="left" nowrap>
                  <?php
                    db_input("t64_descr", 50, $It64_descr, true, "text", 4, "", "chave_t64_descr");
                    ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                  <input name="limpar" type="reset" id="limpar" value="Limpar" >
                  <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_clabens.hide();">
                 </td>
              </tr>
            </form>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          <?php
            $campos = "clabens.t64_codcla,
                       clabens.t64_class,
                       clabens.t64_descr,
                       clabens.t64_obs,
                       clabens.t64_analitica,
                       clabens.t64_bemtipos,
                       clabens.t64_benstipodepreciacao,
                       clabens.t64_vidautil,
                       clabens.t64_instit,
                       clabens.t64_valorresidual
                      ";

            $param = "";
            $aParam = array();
            if (isset($analitica)) {
                if ($analitica) {
                    $aParam[] = " t64_analitica <> 'f' ";
                }
            }
            if (!isset($situacao)) {
                $aParam[] =" t64_ativa = 't' ";
            }
            if (isset($situacao)) {
                $aParam[] =" t64_ativa = '{$situacao}' ";
            }

            $aParam[] = "t64_instit = {$iInstituicaoSessao}";
            $param = implode(" and ", $aParam);
            if (!isset($pesquisa_chave)) {
                if (isset($chave_t64_codcla) && (trim($chave_t64_codcla)!="")) {
                     $sql = $clclabens->sql_query_file(
                         null,
                         $campos,
                         "t64_codcla",
                         " t64_codcla=$chave_t64_codcla and $param"
                     );
                } elseif (isset($chave_t64_class) && (trim($chave_t64_class)!="")) {
                     $sql = $clclabens->sql_query_file(
                         "",
                         $campos,
                         "t64_class",
                         "t64_class like '$chave_t64_class%' and $param"
                     );
                } elseif (isset($chave_t64_descr) && (trim($chave_t64_descr)!="")) {
                     $sql = $clclabens->sql_query_file(
                         "",
                         $campos,
                         "t64_descr",
                         " t64_descr like '$chave_t64_descr%' and $param "
                     );
                } else {
                    if ($param!="") {
                        $sql = $clclabens->sql_query_file("", $campos, "t64_codcla", " $param");
                    } else {
                        $sql = $clclabens->sql_query_file(
                            "",
                            $campos,
                            "t64_codcla",
                            "t64_instit = {$iInstituicaoSessao}".$sit
                        );
                    }
                }
                db_lovrot($sql, 15, "()", "", $funcao_js);
            } else {
                if ($pesquisa_chave!=null && $pesquisa_chave!="") {
                    $sWhere = "t64_codcla = '$pesquisa_chave'";

                    if (isset($lClass)) {
                        $sWhere = "t64_class = '$pesquisa_chave'";
                    }

                    $sWhere .= " and t64_instit = {$iInstituicaoSessao}";

                    $sSqlBens = $clclabens->sql_query_file(null, "*", null, $sWhere);
                    $result   = $clclabens->sql_record($sSqlBens);

                    if ($clclabens->numrows!=0) {
                        db_fieldsmemory($result, 0);
                        echo "<script>".$funcao_js."('$t64_descr',false, $t64_codcla, '{$t64_benstipodepreciacao}', '{$t46_descricao}', '{$t64_vidautil}');</script>";
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
<?php
if (!isset($pesquisa_chave)) {
    ?>
  <script>
  </script>
    <?php
}
?>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();

</script>
