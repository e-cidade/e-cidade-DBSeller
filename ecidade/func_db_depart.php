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
parse_str($_SERVER["QUERY_STRING"], $queryString);

$cldb_depart = new cl_db_depart;
$cldb_config = new cl_db_config;
$clorcorgao = new cl_orcorgao;
$clorcunidade = new cl_orcunidade;
$cldb_depart->rotulo->label("coddepto");
$cldb_depart->rotulo->label("descrdepto");
$cldb_config->rotulo->label("codigo");
$clorcorgao->rotulo->label("o40_descr");

/**
 * Verifica se foi inserido o parâmetro automático na URL e altera o parâmetro
 * da função db_lovrot para permitir alteração de departamento entre instituições
 * com apenas um departamento
 * 
 * Exemplo: &automatico=0
 * 
 * @var int|bool $automatico
 */
if (isset($automatico)) {
    $automatico = (bool) $automatico;
} else {
    $automatico = true;
}

/**
 * Variável do post para filtrar por múltiplos órgãos
 */
if (!empty($_POST['filtrar_ids_orgao'])) {
    $orgao = $_POST['filtrar_ids_orgao'];
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
        <form name="form1" method="post" action="">
            <input type="hidden" id="filtrar_ids_orgao" name="filtrar_ids_orgao" value="">
            <?php
            if (isset($todasinstit) and $todasinstit == 1) {
                ?>
              <tr>
                <td width="4%" align="right" nowrap title="<?php echo $Tcodigo ?>">
                    <?php echo $Lcodigo ?>
                </td>
                <td width="96%" align="left" nowrap>
                    <?php
                    if (!isset($instituicao)) {
                        $instituicao = db_getsession("DB_instit");
                    }
                    $resultinstit = $cldb_config->sql_record($cldb_config->sql_query_usu(null, "codigo,nomeinst",
                      "prefeitura desc, nomeinst", " db_userinst.id_usuario = " . db_getsession("DB_id_usuario")));
                    db_selectrecord('instituicao', $resultinstit, true, 1, "", "", "", "0-Todos", "js_orgao()");
                    ?>
                </td>
              </tr>
                <?php
            }
            ?>

            <?php if (empty($_GET['ids_orgao'])) : ?>
                <tr>
                    <td width="4%" align="right" nowrap title="<?php echo $To40_descr ?>">
                        <b>Órgão:</b>
                    </td>
                    <td width="96%" align="left" nowrap>
                        <?php
                        $instit = db_getsession("DB_instit");
                        $anousu = db_getsession("DB_anousu");
                        if (isset($instituicao) && $instituicao != 0) {
                            $instit = $instituicao;
                        }

                        $resultorgao = $clorcorgao->sql_record($clorcorgao->sql_query_orgao(null, null,
                        "distinct o40_orgao,o40_descr", "o40_descr", "o40_instit=$instit and  o40_anousu=$anousu"));

                        if ($resultorgao) {
                            db_selectrecord('orgao', $resultorgao, true, 1, "", "", "", "0-Todos", "js_orgao()");
                        }
                        ?>
                    </td>
                </tr>
            <?php endif ?>

          <tr>
            <td width="4%" align="right" nowrap title="<?php echo $To40_descr ?>">
                <b>Unidade:</b>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
                $instit = db_getsession("DB_instit");
                $anousu = db_getsession("DB_anousu");
                if (isset($instituicao) && $instituicao != 0) {
                    $instit = $instituicao;
                }

                $sql = "";
                if (isset($orgao) && $orgao != 0) {
                    $idsOrgaos = !empty($_GET['ids_orgaos']) ? $_GET['ids_orgaos'] : $orgao;

                    $sql = $clorcunidade->sql_query(null, null, null,
                    "distinct o41_orgao||'.'||o41_unidade , o41_descr", '',
                    "o41_instit=$instit and o41_anousu=$anousu and o41_orgao in ($idsOrgaos) ");
                } else {
                    $sql = $clorcunidade->sql_query(null, null, null,
                        "distinct o41_orgao||'.'||o41_unidade,o41_descr, o41_orgao, o41_unidade",
                      'o41_orgao, o41_unidade', "o41_instit=$instit and o41_anousu=$anousu");
                }

                $resultunidade = $clorcunidade->sql_record($sql);

                if ($resultunidade) {
                    db_selectrecord('unidade', $resultunidade, true, 1, "", "", "", "0-Todos", "js_unidade()");
                }
                ?>
            </td>
          </tr>


          <tr>
            <td width="4%" align="right" nowrap title="<?php echo $Tcoddepto ?>">
                <?php echo $Lcoddepto ?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
                db_input("coddepto", 5, $Icoddepto, true, "text", 4, "", "chave_coddepto");
                ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?php echo $Tdescrdepto ?>">
                <?php echo $Ldescrdepto ?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
                db_input("descrdepto", 40, $Idescrdepto, true, "text", 4, "", "chave_descrdepto");
                ?>
            </td>
          </tr>

          <tr>

            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="button" id="limpar" value="Limpar" onClick="js_limpar();">
              <input name="Fechar" type="button" id="fechar" value="Fechar"
                     onClick="parent.db_iframe_db_depart.hide();">
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" valign="top">
        <?php

        if (isset($todasinstit) and $todasinstit == 1) {
            if ($instituicao == 0) {
                $listainstit = "";

                for ($x = 0; $x < pg_num_rows($resultinstit); $x++) {
                    db_fieldsmemory($resultinstit, $x);
                    $listainstit .= $codigo . ($x == pg_num_rows($resultinstit) - 1 ? " " : ", ");
                }

                $whereGeral = " and instit in ($listainstit) ";
            } else {
                $whereGeral = " and instit = $instituicao";
            }
        } else {
            $whereGeral = " and instit = " . db_getsession("DB_instit");
        }

        /**
         * Trazer somente departamentos atendidos por algum almoxarifado
         */
        if (isset($lDepartamentosAtendidos) && $lDepartamentosAtendidos == '1') {
            $whereGeral .= " and exists (select 1 from db_almoxdepto where m92_depto = coddepto) ";
        }

        if (isset($depusu) && trim($depusu) != '') {
            $whereGeral .= " and exists( select db_depusu.coddepto
                                 from db_depusu
                                where db_depusu.coddepto   = db_depart.coddepto
                                  and db_depusu.id_usuario = {$depusu} limit 1 )";
        }

        if (isset($lSomenteAtivos)) {
            $whereGeral .= " and db21_ativo = 1";
        }

        if (isset($lFiltrarUnidadeSaude)) {
            $whereGeral .= " and db_depart.coddepto not in(select sd02_i_codigo from unidades) ";
        }

        if (!isset($pesquisa_chave)) {
            if (isset($campos) == false) {
                if (file_exists("funcoes/db_func_db_depart.php") == true) {
                    include(modification("funcoes/db_func_db_depart.php"));
                } else {
                    $campos = "db_depart.*";
                }
            }

            $campos = "distinct " . $campos;

            if (isset($chave_coddepto) && (trim($chave_coddepto) != "")) {
                $sWhere = " coddepto = $chave_coddepto and (limite is null or limite >= '" . date("Y-m-d",
                    db_getsession("DB_datausu")) . "') ";
                $sql = $cldb_depart->sql_query_div(null, $campos, "coddepto", $sWhere . $whereGeral);
            } else {
                if (isset($chave_descrdepto) && (trim($chave_descrdepto) != "")) {
                    $sWhere = " descrdepto like '$chave_descrdepto%' ";
                    $sWhere .= "and (limite is null or limite >= '" . date("Y-m-d",
                        db_getsession("DB_datausu")) . "') ";
                    $sql = $cldb_depart->sql_query_div("", $campos, "descrdepto", $sWhere . $whereGeral);
                } else {
                    if (isset($orgao) && $orgao != 0) {
                        if (isset($unidade) && $unidade != 0) {
                            $aUnidades = explode(".", $unidade);
                            list($iOrgao, $iUnidade) = $aUnidades;
                            $where = "o40_orgao in ($orgao) and db01_unidade = $iUnidade and db01_anousu = {$anousu} and";
                        } else {
                            $where = "o40_orgao in ($orgao) and db01_anousu = {$anousu}and";
                        }

                        if (isset($unidades) && $unidades != "") {
                            $where = "db01_unidade in $unidades and db01_anousu = {$anousu} and ";
                        }

                        $sWhere = "{$where} (limite is null or limite >= '" . date("Y-m-d",
                            db_getsession("DB_datausu")) . "') ";
                        $sql = $cldb_depart->sql_query_div("", $campos, "coddepto", $sWhere . $whereGeral);
                    } else {
                        if (isset($orgao) && $orgao == 0 && isset($unidade) && $unidade != 0) {
                            $aUnidades = explode(".", $unidade);
                            list($iOrgao, $iUnidade) = $aUnidades;

                            $where = "db01_orgao = {$iOrgao} and db01_unidade = $iUnidade  and db01_anousu = {$anousu}  and ";
                            if (isset($unidades) && $unidades != "") {
                                $where = "db01_unidade in $unidades  and db01_anousu = {$anousu}  and ";
                            }

                            $sWhere = "$where (limite is null or limite >= '" . date("Y-m-d",
                                db_getsession("DB_datausu")) . "') ";
                            $sql = $cldb_depart->sql_query_div("", $campos, "coddepto", $sWhere . $whereGeral);
                        } else {
                            $where = "";
                            if (isset($unidades) && $unidades != "0") {
                                $where = "db01_unidade in $unidades and ";
                            }

                            if ($orgao) {
                                $where = "db01_orgao in ($orgao) and ";
                            }

                            $sWhere = $where . "(limite is null or limite >= '" . date("Y-m-d",
                                db_getsession("DB_datausu")) . "') ";
                            $sql = $cldb_depart->sql_query("", $campos, "coddepto", $sWhere . $whereGeral);
                        }
                    }
                    //die($sql);
                }
            }

            db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", array(), $automatico);
        } else {
            if ($pesquisa_chave != null && $pesquisa_chave != "") {
                if (isset($unidades) && $unidades != "") {
                    $whereGeral .= " and db01_unidade in $unidades ";
                }

                $sWhere = "coddepto = $pesquisa_chave $whereGeral and (limite is null or limite >= '" . date("Y-m-d",
                    db_getsession("DB_datausu")) . "')";

                if (isset($_GET['ids_orgao']) && !empty($_GET['ids_orgao'])) {
                    $sWhere .= "AND o40_orgao IN ({$_GET['ids_orgao']})";
                }
                
                $result = $cldb_depart->sql_record($cldb_depart->sql_query_div(null, "*", null, $sWhere));

                if ($cldb_depart->numrows != 0) {
                    db_fieldsmemory($result, 0);
                    echo "<script>" . $funcao_js . "('$descrdepto',false);</script>";
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
<script>


  /**
   * INI
   * Rotina: Patrimônio > Relatórios > Conferência > Bens por Departamento (novo).
   * Nessa rotina podem ser passados múltiplos Órgãos para filtrar.
   *  */
  const idsOrgaoGet = '<?php echo $_GET['ids_orgao']; ?>';
  const idsOrgaoPost = '<?php echo $_POST['filtrar_ids_orgao']; ?>';
  const orgaosElem = document.getElementById('filtrar_ids_orgao');

  /**
   * Seta os órgãos para serem enviados para o POST.
   * A condição !idsOrgaoPost é para evitar loop.
   */
  if (idsOrgaoGet && !idsOrgaoPost) {
    orgaosElem.value = idsOrgaoGet;
    document.form1.submit();
  }
  // FIM

  function js_orgao() {

    if(document.form1.orgao.value == "0") {
      document.form1.unidade.value = 0;
      //document.form1.submit();
    }

    document.form1.submit();

  }

  function js_unidade() {
    if(document.form1.unidade.value != "0") {
    }
    document.form1.submit();
  }

  function js_limpar() {
    document.form1.chave_coddepto.value = "";
    document.form1.chave_descrdepto.value = "";
  }
</script>

<script type="text/javascript">
  (function() {
    var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();
</script>
