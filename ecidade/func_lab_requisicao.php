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

$la22_d_data_ini_dia = '';
$la22_d_data_ini_mes = '';
$la22_d_data_ini_ano = '';
$la22_d_data_fim_dia = '';
$la22_d_data_fim_mes = '';
$la22_d_data_fim_ano = '';

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
db_postmemory($_GET);
parse_str($_SERVER["QUERY_STRING"], $queryString);

$cllab_requisicao = new cl_lab_requisicao;
$cllab_requisicao->rotulo->label("la22_i_codigo");

$clrotulo = new rotulocampo;
$clrotulo->label("z01_v_nome");
$clrotulo->label("la22_d_data");
$clrotulo->label("z01_i_cgsund");

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript"
          src="scripts/classes/saude/laboratorio/ViewNumeroControleInterno.classe.js"></script>
</head>
<body bgcolor=#CCCCCC>
<div class="container">
  <form name="form2" method="post" action="" class="form-container">
    <fieldset>
      <legend>Filtros</legend>
      <table>
        <tr>
          <td id="viewNumeroControleInterno" colspan="2"></td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tla22_i_codigo ?>">
            <label class="bold">Requisição:</label>
          </td>
          <td nowrap>
              <?php
              db_input("la22_i_codigo", 10, $Ila22_i_codigo, true, "text", 4, "", "la22_i_codigo");
              ?>
          </td>
        </tr>
        <tr>
          <td>
            <label class="bold">CGS:</label>
          </td>
          <td>
              <?php
              db_input("z01_i_cgsund", 40, $Iz01_i_cgsund, true, "text", 4, "", "chave_z01_i_cgsund");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?= $Tz01_v_nome ?>">
              <?= $Lz01_v_nome ?>
          </td>
          <td nowrap>
              <?php
              db_input("z01_v_nome", 40, $Iz01_v_nome, true, "text", 4, "", "chave_z01_v_nome");
              ?>
          </td>
        </tr>
        <tr>
          <td nowrap>
            <b>Data inicial:</b>
          </td>
          <td nowrap>
              <?php
              db_inputdata(
                'la22_d_data_ini',
                $la22_d_data_ini_dia,
                $la22_d_data_ini_mes,
                $la22_d_data_ini_ano,
                true,
                'text',
                1
              );
              ?>
              Ex: <?= date('d/m/Y')?>
          </td>
        </tr>
        <tr>
          <td nowrap>
            <b>Data fim:</b>
          </td>
          <td nowrap>
              <?php
              db_inputdata(
                'la22_d_data_fim',
                $la22_d_data_fim_dia,
                $la22_d_data_fim_mes,
                $la22_d_data_fim_ano,
                true,
                'text',
                1
              );
              ?>
              Ex: <?= date('d/m/Y')?>
          </td>
        </tr>
      </table>

    </fieldset>
    <input name="pesquisar" type="submit" id="pesquisar" value="Pesquisar">
    <input name="limpar" type="reset" id="limpar" value="Limpar" onclick="js_limpar()">
    <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_lab_requisicao.hide();">
  </form>
</div>
<div class="container">
  <table>
    <tr>
      <td>
          <?php
          if (!isset($pesquisa_chave)) {
              if (isset($campos) == false) {
                  $campos = "la22_i_codigo, z01_i_cgsund, z01_v_nome, la22_d_data, la22_c_hora, la22_i_departamento";
              }

              $aWhere = array();
              $where = "";
              $sep = "";
              $lCarrega = true;

              if (isset($la22_d_data_ini) && ($la22_d_data_ini != "")) {
                  $aDat = explode("/", $la22_d_data_ini);
                  $aWhere[] = " la22_d_data >= '" . $aDat[2] . "-" . $aDat[1] . "-" . $aDat[0] . "' ";
              }

              if (isset($autoriza)) {
                  $aWhere[] = " la22_i_autoriza = {$autoriza} ";
              }

              if (isset($la22_d_data_fim) && ($la22_d_data_fim != "")) {
                  $aDat = explode("/", $la22_d_data_fim);
                  $aWhere[] = " la22_d_data <= '" . $aDat[2] . "-" . $aDat[1] . "-" . $aDat[0] . "' ";
              }


              if (isset($iLaboratorioLogado)) {
                  $sWhereRequiItem = "";
                  if (isset($lSomenteConferidos)) {
                      $sWhereRequiItem = " and la21_c_situacao = '" . RequisicaoExame::CONFERIDO . "' ";
                  }

                  $sWhereAutorizados = "";
                  if (isset($lSomenteNaoDigitados)) {
                      $sWhereAutorizados = " and la21_c_situacao = '" . RequisicaoExame::NAO_DIGITADO . "' ";
                  }

                  $sWhereColetados = "";
                  if (isset($lSomenteColetados)) {
                      $sWhereColetados = " and la21_c_situacao = '" . RequisicaoExame::COLETADO . "' ";
                  }

                  $sWhereLaboratorioLogado = "";
                  if(isset($permissaoPorResponsavelLab)){
                    $sWhereLaboratorioLogado = " and la24_i_laboratorio = {$iLaboratorioLogado}";
                  }

                  $where .= " EXISTS( select 1 ";
                  $where .= "           from lab_requiitem ";
                  $where .= "                inner join lab_setorexame on lab_setorexame.la09_i_codigo = lab_requiitem.la21_i_setorexame";
                  $where .= "                inner join lab_labsetor   on lab_labsetor.la24_i_codigo   = lab_setorexame.la09_i_labsetor";
                  $where .= "          where lab_requiitem.la21_i_requisicao = lab_requisicao.la22_i_codigo";
                  $where .= "           {$sWhereLaboratorioLogado} {$sWhereRequiItem} {$sWhereAutorizados} {$sWhereColetados} ) ";

                  $aWhere[] = $where;
              }

              if (isset($iDepResitante)) {
                  $lCarrega = false;
                  $aWhere[] = " la22_i_departamento = {$iDepResitante} ";
              }

              if (isset($iCgs) && !empty($iCgs)) {
                  $aWhere[] = " la22_i_cgs = {$iCgs} ";
              }

              if (isset($la22_i_codigo) && (trim($la22_i_codigo) != "")) {
                  $aWhere[] = " la22_i_codigo = {$la22_i_codigo} ";
              } else {
                  if (isset($chave_z01_v_nome) && (trim($chave_z01_v_nome) != "")) {
                      $aWhere[] = " z01_v_nome like '{$chave_z01_v_nome}%' ";
                  } else {
                      if (isset($chave_z01_i_cgsund) && (trim($chave_z01_i_cgsund) != "")) {
                          $aWhere[] = " z01_i_cgsund = {$chave_z01_i_cgsund} ";
                      }
                  }
              }

              $flagErro = false;
              if (isset($la65_numero) && !empty($la65_numero)) {
                  if (empty($la65_ano)) {
                      echo "<script>alert('O ano do Número de Controle Interno deve ser informado.');</script>";
                      $flagErro = true;
                  } else {
                      $aWhere[] = " la65_numero = {$la65_numero} AND la65_ano = {$la65_ano} ";
                  }
              }

              if (!$flagErro) {
                  $where = implode(" and ", $aWhere);
                  $sql = $cllab_requisicao->sql_query("", $campos, "z01_v_nome", $where);

                  // Na rotina de requisição de exames, só carrega as requisições após informar um filtro
                  if (
                    !$lCarrega
                    && (
                      empty($la22_i_codigo) && empty($chave_z01_v_nome)
                      && empty($chave_z01_i_cgsund) && empty($la22_d_data_ini)
                      && empty($la22_d_data_fim) && empty($la65_numero)
                    )
                  ) {

                      $sql = "";
                  }

                  $repassa = array();
                  if (isset($la22_i_codigo)) {
                      $repassa = array(
                        "la22_i_codigo" => $la22_i_codigo,
                        "chave_z01_v_nome" => $chave_z01_v_nome,
                        "la22_i_departamento" => (!empty($la22_i_departamento) ? $la22_i_departamento : null),
                        "la22_d_data_ini" => $la22_d_data_ini,
                        "la22_d_data_fim" => $la22_d_data_fim,
                        "la22_d_data_ini_dia" => $la22_d_data_ini_dia,
                        "la22_d_data_ini_mes" => $la22_d_data_ini_mes,
                        "la22_d_data_ini_ano" => $la22_d_data_ini_ano,
                        "la22_d_data_fim_dia" => $la22_d_data_fim_dia,
                        "la22_d_data_fim_mes" => $la22_d_data_fim_mes,
                        "la22_d_data_fim_ano" => $la22_d_data_fim_ano,
                        "chave_z01_i_cgsund" => $chave_z01_i_cgsund
                      );
                  }                    
                  db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa, false);
              }
          } else {
              if ($pesquisa_chave != null && $pesquisa_chave != "") {
                  $sWhere = "la22_i_codigo = {$pesquisa_chave}";

                  if (isset($iCgs) && !empty($iCgs)) {
                      $sWhere .= " AND la22_i_cgs = {$iCgs} ";
                  }

                  if (isset($iLaboratorioLogado)) {
                      $sWhereRequiItem = "";
                      if (isset($lSomenteConferidos)) {
                          $sWhereRequiItem = " and la21_c_situacao = '" . RequisicaoExame::CONFERIDO . "' ";
                      }

                      $sWhereAutorizados = "";
                      if (isset($lSomenteNaoDigitados)) {
                          $sWhereAutorizados = " and la21_c_situacao = '" . RequisicaoExame::NAO_DIGITADO . "' ";
                      }

                      $sWhereColetados = "";
                      if (isset($lSomenteColetados)) {
                          $sWhereColetados = " and la21_c_situacao = '" . RequisicaoExame::COLETADO . "' ";
                      }

                      $sWhereLaboratorioLogado = "";
                      if(!isset($permissaoPorResponsavelLab)){
                        $sWhereLaboratorioLogado = " and la24_i_laboratorio = {$iLaboratorioLogado}";
                      }

                      $sWhere .= " AND EXISTS( select 1 ";
                      $sWhere .= "               from lab_requiitem ";
                      $sWhere .= "                    inner join lab_setorexame on lab_setorexame.la09_i_codigo = lab_requiitem.la21_i_setorexame";
                      $sWhere .= "                    inner join lab_labsetor   on lab_labsetor.la24_i_codigo   = lab_setorexame.la09_i_labsetor";
                      $sWhere .= "              where lab_requiitem.la21_i_requisicao = lab_requisicao.la22_i_codigo";
                      $sWhere .= "               {$sWhereLaboratorioLogado} {$sWhereRequiItem} {$sWhereAutorizados} {$sWhereColetados} ) ";
                  }

                  $sCampos = "z01_v_nome, z01_i_cgsund, la22_i_departamento";
                  $sSqlLabRequisicao = $cllab_requisicao->sql_query(null, $sCampos, null, $sWhere);
                  $result = $cllab_requisicao->sql_record($sSqlLabRequisicao);
                  if ($cllab_requisicao->numrows != 0) {
                      db_fieldsmemory($result, 0);
                      echo "<script>" . $funcao_js . "('{$z01_v_nome}',false, '{$z01_i_cgsund}', '{$la22_i_departamento}');</script>";
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
</div>
</body>
</html>
<script>
  window.onload = function(){
    const btnPesquisar = document.getElementById("pesquisar");

    document.body.addEventListener('keydown', function(event){
      if(event.which == 13){
        btnPesquisar.click();
      }
    });

  };

  function js_limpar() {

    document.form2.la22_i_codigo.value = "";
    document.form2.chave_z01_i_cgsund.value = "";
    document.form2.chave_z01_v_nome.value = "";
    document.form2.la22_d_data_ini.value = "";
    document.form2.la22_d_data_fim.value = "";
    document.form2.la22_d_data_ini_dia.value = "";
    document.form2.la22_d_data_ini_mes.value = "";
    document.form2.la22_d_data_ini_ano.value = "";
    document.form2.la22_d_data_fim_dia.value = "";
    document.form2.la22_d_data_fim_mes.value = "";
    document.form2.la22_d_data_fim_ano.value = "";
    document.form2.pesquisar.click();

  }

  $('la22_i_codigo').className = "field-size2";
  $('la22_d_data_ini').className = "field-size2";
  $('la22_d_data_fim').className = "field-size2";
  $('chave_z01_v_nome').className = "field-size9";
  $('chave_z01_i_cgsund').className = "field-size2";

  var viewNumeroControleInterno = new ViewNumeroControleInterno('viewNumeroControleInterno', false);
  viewNumeroControleInterno.setRequisicaoElemento($('la22_i_codigo'));
  viewNumeroControleInterno.show($('viewNumeroControleInterno'));

  if(viewNumeroControleInterno.getParametroAtivo()) {
    $('la22_i_codigo').setAttribute('style', 'margin-left:76px');
    $('la22_d_data_ini').setAttribute('style', 'margin-left:76px');
    $('la22_d_data_fim').setAttribute('style', 'margin-left:76px');
    $('chave_z01_v_nome').setAttribute('style', 'margin-left:76px');
    $('chave_z01_i_cgsund').setAttribute('style', 'margin-left:76px');
  }

  js_tabulacaoforms("form2", "la22_i_codigo", true, 1, "la22_i_codigo", true);

</script>
<script type="text/javascript">
  (function() {
    var query = frameElement.getAttribute('name').replace('IF', ''),
      input = document.querySelector('input[value="Fechar"]');
    input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
  })();
</script>
