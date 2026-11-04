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
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_matmater_classe.php"));
include(modification("classes/db_matparamconsulta_classe.php"));

db_postmemory($_POST);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clmatmater         = new cl_matmater;
$clmatparamconsulta = new cl_matparamconsulta;

$clmatmater->rotulo->label("m60_codmater");
$clmatmater->rotulo->label("m60_descr");

// verifica se é o primeiro acesso do usuário na tela, se for
// pega as configurações de procedimentos >> consulta
// caso o usuário tenha alterado algum parâmetro na lookup
// pega as opções que ele definiu e desconsidera as configurações
// dos procedimentos
if (!isset($m38_visualizacaoitens) or !isset($m38_visualizacaomatestoque)) {
  $sSql     = $clmatparamconsulta->sql_query(db_getsession("DB_instit"));
  $rsResult = $clmatparamconsulta->sql_record($sSql);
  db_fieldsmemory($rsResult, 0);
}

$clmatparam = new cl_matparam();
$result_param = $clmatparam->sql_record($clmatparam->sql_query_file());
if ($clmatparam->numrows) {
  db_fieldsmemory($result_param, 0);
  if ($m90_reqsemest == 't') {
    $m38_visualizacaomatestoque = 't';
  }
}

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
            <?php
            if (isset($codigoAlmoxarifado)) {
              echo "<input type='hidden' name='codigoAlmoxarifado' value={$codigoAlmoxarifado}>";
            }
            ?>
            <tr>
              <td width="4%" align="right" nowrap title="<?= $Tm60_codmater ?>">
                <?= $Lm60_codmater ?>
              </td>
              <td width="96%" align="left" nowrap>
              <?php
                db_input("m60_codmater", 10, $Im60_codmater, true, "text", 4, "", "chave_m60_codmater");
                ?>
              </td>
            </tr>

            <tr>
              <td width="4%" align="right" nowrap title="<?= $Tm60_descr ?>">
                <?= $Lm60_descr ?>
              </td>
              <td width="96%" align="left" nowrap>
              <?php
                db_input("m60_descr", 40, $Im60_descr, true, "text", 4, "", "chave_m60_descr");
                ?>
              </td>
            </tr>

            <tr>
              <td align="right" nowrap title="<?= @$Tm38_visualizacaoitens ?>">
                <b> Mostrar apenas itens da instituição: </b>
              </td>
              <td>
						<?php
                $x = array('1' => 'Não', '2' => 'Sim');
                db_select('m38_visualizacaoitens', $x, true, 2, "");
                ?>
              </td>
            </tr>

            <tr>
              <td align="right" nowrap title="<?= @$Tm38_visualizacaomatestoque ?>">
                <?php
                if (isset($codigoAlmoxarifado)) {
                  echo "<b> Mostrar apenas materiais com estoque : </b>";
                } else {
                  echo "<b> Mostrar apenas materiais com estoque: </b>";
                }
                ?>
              </td>
              <td>
						<?php
                $x = array('f' => 'Não', 't' => 'Sim');
                db_select('m38_visualizacaomatestoque', $x, true, 1);
                ?>
              </td>
            </tr>

            <tr>
              <td colspan="2" align="center">
                <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                <input name="limpar" type="reset" id="limpar" value="Limpar">
                <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_matmater.hide();">
              </td>
            </tr>
          </form>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
        <?php
        $where = [];

        if (isset($lServico) && $lServico == 't') {
          $where[] = "m60_servico is true";
        } else {
          $where[] = "m60_servico is false";
        }
        if (isset($nosetmaterial) && trim($nosetmaterial) != "") {
          $where[] = "m70_codmatmater not in ($nosetmaterial) ";
        }

        if (!isset($pesquisa_chave)) {

          if (isset($campos) == false) {

            if (file_exists("funcoes/db_func_matmater.php") == true) {
              include(modification("funcoes/db_func_matmater.php"));
            } else {
              $campos = "matmater.*";
            }
          }

          // visualizar apenas com movimentação na instituição que está sendo acessada
          if ($m38_visualizacaoitens == 2) {
            $where[] = "( db_depart.instit = " . db_getsession("DB_instit") . " or db_depart.instit is null ) ";
          }

          if (isset($chave_m60_codmater) && (trim($chave_m60_codmater) != "")) {
            $ordem = "m60_codmater";
            $where[] = "m60_codmater={$chave_m60_codmater} and m60_ativo is true";
          } else if (isset($chave_m60_descr) && (trim($chave_m60_descr) != "")) {
            $ordem = "m60_descr";
            $where[] = "m60_descr like '{$chave_m60_descr}%' and m60_ativo is true";
          } else {
            $ordem = "m60_codmater";
            $where[] = "m60_ativo is true";
          }
          $where = implode(' AND ', $where);

          if ((isset($m38_visualizacaomatestoque) && $m38_visualizacaomatestoque == 'f') || (isset($lServico) && $lServico == 't')) {
            $sql = $clmatmater->sql_query_com_almoxarifado('', $campos, $ordem, $where);
          } else if (isset($m38_visualizacaomatestoque) && $m38_visualizacaomatestoque == 't') {

            $sql = "WITH saldo AS (
              SELECT (Coalesce(SUM(CASE WHEN matestoquetipo.m81_tipo = 1 THEN matestoqueinimei.m82_quant END),0) - Coalesce(SUM(CASE WHEN matestoquetipo.m81_tipo = 2 THEN m82_quant END),0)) AS quantidade,
                      m60_codmater AS codigo_material ,
                      m60_descr AS material,
                      instit
                FROM matestoqueini
                INNER JOIN matestoquetipo ON m80_codtipo = m81_codtipo
                INNER JOIN matestoqueinimei ON m82_matestoqueini = m80_codigo
                LEFT JOIN matestoqueinimeipm ON m82_codigo = m89_matestoqueinimei
                INNER JOIN matestoqueitem ON m82_matestoqueitem = m71_codlanc
                INNER JOIN matestoque ON m71_codmatestoque = m70_codigo
                INNER JOIN matmater ON m60_codmater = m70_codmatmater
                INNER JOIN db_depart  on db_depart.coddepto  = matestoque.m70_coddepto
                WHERE {$where}
                GROUP BY m60_codmater, m60_descr, instit
            ), estoque AS (
                SELECT
                    codigo_material,
                    instit,
                    material,
                    quantidade - coalesce((
                      SELECT
                        SUM(coalesce(case when m81_tipo = 4 then m82_quant end, 0)) AS saida
                      FROM matestoqueinimei
                      INNER JOIN matestoqueitem ON m71_codlanc = m82_matestoqueitem
                      INNER JOIN matestoque trans ON m71_codmatestoque = trans.m70_codigo
                      INNER JOIN matestoqueini ON m80_codigo = m82_matestoqueini
                      LEFT JOIN matestoqueinil ON m80_codigo = m86_matestoqueini
                      INNER JOIN matestoquetipo ON m80_codtipo = m81_codtipo
                      WHERE trans.m70_codigo = codigo_material
                           AND m81_codtipo = 7
                           AND quantidade > 0
                           AND m86_matestoqueini IS NULL), 0) AS quantidade
               FROM saldo
            )
            SELECT distinct {$campos}, cast(quantidade as int) as DL_saldo
            FROM estoque
            INNER JOIN matmater
               ON matmater.m60_codmater = codigo_material
            INNER JOIN matestoque
               ON m60_codmater = m70_codmatmater
             LEFT  JOIN db_depart
               ON db_depart.coddepto         = matestoque.m70_coddepto
            WHERE {$where}
              AND quantidade > 0
            ORDER BY {$ordem}";
          }

          db_lovrot($sql, 15, "()", "", $funcao_js);
        } else {

          if ($pesquisa_chave != null && $pesquisa_chave != "") {

            if (isset($codigoAlmoxarifado) && isset($m38_visualizacaomatestoque) && $m38_visualizacaomatestoque == 't') {
              $where[] = "matestoque.m70_codmatmater IS NOT NULL";
              $where[] = "(m70_quant > 0 OR m70_valor > 0)";
              $where[] = "db_almox.m91_codigo = {$codigoAlmoxarifado} ";
            }

            $where = implode(' AND ', $where);

            $sSql = $clmatmater->sql_query_com_almoxarifado(
              $pesquisa_chave,
              " matmater.*, matunid.*, matestoque.*,db_depart.* ",
              null,
              "m60_codmater=$pesquisa_chave and
                                                 m60_ativo is true {$sWhereConfig}"
            );
            $result = $clmatmater->sql_record($sSql);

            if ($clmatmater->numrows != 0) {

              db_fieldsmemory($result, 0);
              $m60_descr = str_replace(chr(10), " ", $m60_descr);
              $m60_descr = addslashes($m60_descr);
              echo "<script>" . $funcao_js . "('" . $m60_descr . "',false,$pesquisa_chave, '" . $m60_servico . "');</script>";
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
    var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();
</script>
