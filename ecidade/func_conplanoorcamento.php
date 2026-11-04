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

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
$clconplanoorcamento = new cl_conplanoorcamento;
$clconplanoorcamento->rotulo->label("c60_codcon");
$clconplanoorcamento->rotulo->label("c60_anousu");
$clconplanoorcamento->rotulo->label("c60_descr");

$oGet  = db_utils::postMemory($_GET);
$oPOST = db_utils::postMemory($_POST);
$sDescricaoConta  = isset($chave_c60_descr)  ? $chave_c60_descr : null;
$chave_c60_codcon = isset($chave_c60_codcon) ? $chave_c60_codcon : null;

$get = (object)filter_input_array(INPUT_GET);
$instituicao = db_getsession('DB_instit');
$ano = isset($get->previsao) && !empty($get->ano) ? $get->ano : db_getsession('DB_anousu');

if (!empty($get->exercicio)) {
    $ano = $get->exercicio;
}

$filtrosEstrutural = !empty($get->filtrosEstrutural);

$filtrosEstruturalSintetico = !empty($get->filtrosEstruturalSintetico);

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
      <table width="35%" border="0" align="center" cellspacing="0">
        <form name="form2" method="post" action="" >
          <tr>
            <td class="field-size4" nowrap title="<?php echo $Tc60_codcon?>">
              <?php echo $Lc60_codcon?>
            </td>
            <td align="left" nowrap>
              <?php
              db_input("c60_codcon",6,$Ic60_codcon,true,"text",4,"","chave_c60_codcon");
              ?>
            </td>
          </tr>
          <tr>
            <td width="4%" nowrap title="<?php echo $Tc60_descr?>">
              <?php echo $Lc60_descr?>
            </td>
            <td align="left" nowrap>
              <?php
              db_input("c60_descr",50,$Ic60_descr,true,"text",4,"","chave_c60_descr");
              ?>
            </td>
          </tr>
          <tr>
            <td width="4%" nowrap title="Código Reduzido">
              <b>Reduzido:</b>
            </td>
            <td align="left" nowrap>
              <?php
              $SiReduzido = "Reduzido";
              db_input("iReduzido",50, 1,true,"text",4,"");
              ?>
            </td>
          </tr>
          <tr>
            <td width="4%" nowrap title="Estrutural">
              <b>Estrutural:</b>
            </td>
            <td align="left" nowrap>
              <?php
              $SsEstrutural = "Estrutural";
              db_input("sEstrutural",50, 1,true,"text",4,"");
              ?>
            </td>

          <tr id="linhaCategoriaEconomica" style="display: none">
              <td>
                  <label class="bold" for="categoriaEconomica">Categoria Econômica:</label>
            </td>
              <td>
                  <input class="field-size2" maxlength="1" type="text" name="categoriaEconomica" id="categoriaEconomica"/>
            </td>
          <tr id="linhaGrupoNatureza" style="display: none">
            <td>
              <label class="bold" for="grupoNatureza">Grupo de Natureza:</label>
            </td>
              <td>
                  <input class="field-size2" maxlength="1"  type="text" name="grupoNatureza" id="grupoNatureza"/>
            </td>
          <tr id="linhaModalidadeAplicacao" style="display: none">
            <td>
              <label class="bold" for="modalidadeAplicacao">Modalidade de Aplicação:</label>
            </td>
              <td>
                  <input class="field-size2" maxlength="2" type="text" name="modalidadeAplicacao" id="modalidadeAplicacao"/>
            </td>
          <tr  id="linhaElementoDespesa" style="display: none">
            <td>
              <label class="bold" for="elementoDespesa">Elemento de Despesa:</label>
            </td>
              <td>
                  <input class="field-size2" maxlength="2" type="text" name="elementoDespesa" id="elementoDespesa"/>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_conplanoorcamento.hide();">
                <?php
                  db_input("filtrosEstrutural",1,0,true,"hidden");
                ?>
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" valign="top">
        <?php

        $aWherePadrao = array(
            "conplanoorcamento.c60_anousu = {$ano}"
        );

        if (isset($get->previsao)) {
            $aWherePadrao[] = "c61_instit = {$instituicao}";
            $aWherePadrao[] = 'c61_reduz IS NOT NULL';
        }

        if (isset($get->apenasReceita)) {
            $aWherePadrao[] = "(c60_estrut like '4%' or c60_estrut like '9%')";
        }

        if (isset($get->apenasDespesa)) {
            $aWherePadrao[] = "c60_estrut like '3%'";
        }

        $chave_c60_descr = pg_escape_string(stripcslashes($sDescricaoConta));

        if (!empty($oGet->sSomenteEstrutural)) {
            $aWherePadrao[] = "c60_estrut ILIKE '{$oGet->sSomenteEstrutural}%'";
        }

        if (!empty($categoriaEconomica)) {
          $aWherePadrao[] = "c60_estrut ILIKE '_{$categoriaEconomica}%'";
        }

        if (!empty($grupoNatureza)) {
          $aWherePadrao[] = "c60_estrut ILIKE '__{$grupoNatureza}%'";
        }

        if (!empty($modalidadeAplicacao)) {
          $aWherePadrao[] = "c60_estrut ILIKE '___{$modalidadeAplicacao}%'";
        }

        if (!empty($elementoDespesa)) {
          $aWherePadrao[] = "c60_estrut ILIKE '_____{$elementoDespesa}%'";
        }

        if (!empty($filtrosEstruturalSintetico)) {
            $aWherePadrao[] = "not exists (select 1 from conplanoorcamentoanalitica  where c61_codcon = c60_codcon and c61_anousu = c60_anousu)";
            $aWherePadrao[] = "substr(c60_estrut,8) = '00000000' ";
            $aWherePadrao[] = "fc_estrutural_nivel(fc_estrutural_pai(fc_estruturaldespesa(c60_estrut))) in (4, 5)";
        }

        if (!isset($pesquisa_chave)) {
            $campos = 'conplanoorcamento.*';

            if (isset($chave_c60_codcon) && trim($chave_c60_codcon)) {
                $aWherePadrao[] = "c60_codcon = {$chave_c60_codcon}";
                $sql = $clconplanoorcamento->sql_query_geral(null, null, $campos, 'c60_estrut',
                    implode(' AND ', $aWherePadrao));
            } else {
                if (isset($chave_c60_descr) && trim($chave_c60_descr)) {
                    $sql = $clconplanoorcamento->sql_query_geral('', '', $campos, 'c60_estrut',
                        "c60_descr LIKE '{$chave_c60_descr}%' AND " . implode(' AND ', $aWherePadrao));
                } else {
                    if (isset($iReduzido) && trim($iReduzido)) {
                        $aWherePadrao[] = "c61_reduz = {$iReduzido}";
                        $sql = $clconplanoorcamento->sql_query_geral(null, null, $campos, 'c60_estrut',
                            implode(' AND ', $aWherePadrao));
                    } else {
                        if (isset($sEstrutural) && trim($sEstrutural)) {
                            $aWherePadrao[] = "c60_estrut ILIKE '{$sEstrutural}%'";
                            $sql = $clconplanoorcamento->sql_query_geral(null, null, $campos, 'c60_estrut',
                                implode(' AND ', $aWherePadrao));
                        } else {
                            $sql = $clconplanoorcamento->sql_query_geral('', '', $campos, 'c60_estrut',
                                implode(' AND ', $aWherePadrao));
                        }
                    }
                }
            }

            $repassa = array();

            if (isset($chave_c60_descr)) {
                $repassa = array('chave_c60_codcon' => $chave_c60_codcon, 'chave_c60_descr' => $chave_c60_descr);
            }

            if (!empty($filtrosEstruturalSintetico)) {
                $sSqlSinteticos =  "select   DISTINCT ON (substr(c60_estrut,1,7)) c60_codcon,
                       c60_anousu,
                       c60_estrut,
                       c60_descr,
                       c60_finali,
                       c60_codsis,
                       c60_codcla,
                       c60_consistemaconta,
                       c60_identificadorfinanceiro,
                       c60_naturezasaldo,
                       c60_funcao  FROM  ({$sql}) as  datatable";

                $sql = $sSqlSinteticos;
            }

            db_lovrot($sql, 15, '()', '', $funcao_js, '', 'NoMe', $repassa);
        } else {
            if ($pesquisa_chave != null && $pesquisa_chave != "") {
                $result = $clconplanoorcamento->sql_record($clconplanoorcamento->sql_query_geral($pesquisa_chave,
                    $ano));
                if ($clconplanoorcamento->numrows != 0) {
                    db_fieldsmemory($result, 0);

                    $sDescricao = $c60_descr;

                    if (!empty($lEstrutural)) {
                        $sDescricao = "{$c60_estrut} - {$c60_descr}";
                    }

                    echo "<script>{$funcao_js}('{$sDescricao}', false);</script>";
                } else {
                    echo "<script>{$funcao_js}('Chave({$pesquisa_chave}) não encontrada.', true);</script>";
                }
            } else {
                echo "<script>{$funcao_js}('', false);</script>";
            }
        }
        ?>
    </td>
  </tr>
</table>
</body>
</html>
<?php
if(!isset($pesquisa_chave)){
  ?>
  <script>
  </script>
  <?php
}
?>
<script>
  js_tabulacaoforms('form2', 'chave_c60_descr', true, 1, 'chave_c60_descr', true);
</script>
<script type="text/javascript">
  (function() {
    var query = frameElement.getAttribute('name').replace('IF', ''),
        input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();

  if ($F(filtrosEstrutural)) {

    $('linhaCategoriaEconomica').style.display = '';
    $('linhaGrupoNatureza').style.display = '';
    $('linhaModalidadeAplicacao').style.display = '';
    $('linhaElementoDespesa').style.display = '';
  }
</script>
