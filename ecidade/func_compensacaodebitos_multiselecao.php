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
require_once(modification("libs/db_sql.php"));

$oGET          = db_utils::postMemory($_GET);
$oPOST         = db_utils::postMemory($_POST);
$iChave        = isset($oGET->pesquisa_chave) ? $oGET->pesquisa_chave : null;
$iCgm          = isset($oGET->cgm) ? $oGET->cgm : null;
$sSelecionados = isset($oGET->selecionados) ? $oGET->selecionados : null;
$iSequencial   = isset($oPOST->chave_codigo) ? $oPOST->chave_codigo : null;
?>
<html>
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
</head>
<body>

<?php
  if ($iCgm) :

    $iInstituicao = db_getsession('DB_instit');
    $sCamposDebitos = implode(", ", array(
      "distinct arrecad.k00_numpre",
      "arrecad.k00_numpar",
      "arrecad.k00_tipo",
      "arrecad.k00_dtvenc",
      "arretipo.k00_descr",
      "arrecad.k00_numcgm",
      "sum(arrecad.k00_valor) as dl_Valor_Hist"
    ));

    $aWhereDebitos = array(
      "arrecad.k00_valor > 0"
    );

    if ($iSequencial) {
      $aWhereDebitos[] = "arrecad.k00_numpre = {$iSequencial}";
    }

    if ($iCgm) {
      $aWhereDebitos[] = "arrenumcgm.k00_numcgm = {$iCgm}";
    }

    /**
     * Não exibe na lookup débitos que já foram selecionados
     */
    if ($sSelecionados) {

      $aWhereSelecionados = array();
      $aDebitos = explode('|', $sSelecionados);
      foreach ($aDebitos as $sDebito) {

        list($iNumpre, $iNumpar) = explode('/', $sDebito);
        $aWhereSelecionados[] = "(arrecad.k00_numpre, arrecad.k00_numpar) <> ({$iNumpre}, {$iNumpar})";
      }
      $sWhereSelecionados = ' (' . implode(' and ', $aWhereSelecionados) . ') ';
      $aWhereDebitos[] = $sWhereSelecionados;
    }

    $sWhereDebitos = implode(' and ', $aWhereDebitos);

    $sGroupBy = implode(", ", array(
      "arrecad.k00_numpre",
      "arrecad.k00_numpar",
      "arrecad.k00_tipo",
      "arrecad.k00_dtvenc",
      "arrecad.k00_numcgm",
      "arretipo.k00_descr"
    ));

    $sSql = "select {$sCamposDebitos} ";
    $sSql .= " from arrenumcgm ";
    $sSql .= "    inner join arrecad    on arrecad.k00_numpre    = arrenumcgm.k00_numpre";
    $sSql .= "    inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre and arreinstit.k00_instit = {$iInstituicao}";
    $sSql .= "    inner join arretipo   on arretipo.k00_tipo     = arrecad.k00_tipo";
    $sSql .= "    inner join tabrec     on k02_codigo            = arrecad.k00_receit";
    $sSql .= " where {$sWhereDebitos} ";
    $sSql .= " group by {$sGroupBy} ";
    $sSql .= " order by arrecad.k00_numpre, arrecad.k00_numpar, arrecad.k00_dtvenc";

    $aRepassa = array();
    if(isset($iSequencial)) :

      $aRepassa = array(
        "chave_codigo" => $iSequencial,
      );
    endif;
    ?>
   <?php
      $resultSql = db_query($sSql);
      $num_rows = pg_num_rows($resultSql);

      echo '<script>result = []</script>';
      for ($i = 0; $i < $num_rows; $i++) {
          $dados = db_utils::fieldsMemory($resultSql, $i);
          $result_valcorrigido = debitos_numpre($dados->k00_numpre, 0, $dados->k00_tipo, db_getsession("DB_datausu"), db_getsession("DB_anousu"), $dados->k00_numpar, true, "", " and y.k00_hist <> 918");
          $dados_valcorrigido = (db_utils::fieldsMemory($result_valcorrigido, 0));
          $dados->total_corrigido = $dados_valcorrigido->total;
          $dados->k00_descr = mb_convert_encoding($dados->k00_descr, "UTF-8", mb_detect_encoding($dados->k00_descr));
          $decode = (json_encode($dados));
          echo '<script>result.push(' .$decode .')</script>';
      }
      ?>

  <?php else : ?>
    <div class="container">
      <fieldset>
        <legend>Resultado da Pesquisa</legend>
        <p style="text-align: center;">É obrigatório informar um CGM.</p>
      </fieldset>
    </div>
  <?php endif ?>


  <div id="div-table">
      <table id="data-table"
             class="table table-sm"
             data-height="250"
             data-virtual-scroll="true"
             style="width: 100%;">

      </table>
  </div>
  <div style="float: right" id="div-button">
      <button class="buttonenviar" id="button"> Enviar </button>
      <button  class="buttonenviar" id="fechar" onClick="parent.db_iframe_compensacaodebitos.hide();"> Fechar </button>
  </div>

    <style>
        .buttonenviar {
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
            font-size: 12px;
            margin: 4px 2px;
            cursor: pointer;
            background-color: #2f5b8c;
        }
        .buttonenviar:hover {
            background-color: #233B56;
            color: lightgrey;
        }
    </style>
    <style>
        #div-table th {
            color: white;
        }
    </style>
</body>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<script type="text/javascript">
    $.noConflict();

    const colunas = [
        {
            field: 'checkbox',
            title: 'M',
            sortable: true,
            checkbox: true
        },
        {
            field: 'k00_numpre',
            title: 'Numpre',
            sortable: true,
        },
        {
            field: 'k00_numpar',
            title: 'Parcela',
            sortable: true,
        },
        {
            field: 'k00_dtvenc',
            title: 'Data Venc.',
            sortable: true,
        },
        {
            field: 'dl_valor_hist',
            title: 'Valor hist.',
            sortable: true,
        },
        {
            field: 'total_corrigido',
            title: 'Valor total',
            sortable: true,
        },
        {
            field: 'k00_descr',
            title: 'Tipo de Débito',
            sortable: true,
        },
        {
            field: 'k00_numcgm',
            title: 'CGM',
            sortable: true,
        }]

    const table = jQuery('#data-table');
    table.bootstrapTable({
        locale: 'pt-BR',
        cache: false,
        height: 500,
        search: true,
        pagination: true,
        showButtonText: true,
        columns: colunas
    });
    table.bootstrapTable('load', result);

    document.getElementById('button').onclick = function() {
        var selected = table.bootstrapTable('getSelections');
        selected.forEach(element => {
            parent.js_mostraDebitos(element.k00_numpre, element.k00_numpar,
                element.k00_tipo, element.k00_descr, element.k00_dtvenc, element.dl_valor_hist);
        });
        parent.db_iframe_compensacaodebitos.hide();
    };
</script>
</html>
