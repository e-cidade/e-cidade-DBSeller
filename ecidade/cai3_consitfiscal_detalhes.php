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
include(modification("classes/db_vistorias_classe.php"));
include(modification("classes/db_auto_classe.php"));
include(modification("classes/db_fiscal_classe.php"));
include(modification("classes/db_levanta_classe.php"));
require_once(modification("classes/db_autotipo_classe.php"));

db_postmemory($HTTP_GET_VARS, 0);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clvistorias = new cl_vistorias;
$clauto = new cl_auto;
$clfiscal = new cl_fiscal;
$cllevanta = new cl_levanta;
$clautotipo      = new cl_autotipo;
$where = "";
?>
<html>

<head>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script>
    function js_vist(cod) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_consulta', 'fis3_consultavist002.php?y70_codvist=' + cod, 'Consulta Vistoria', true);
    }

    function js_auto(cod) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'fis3_consautoinf002.php?codauto=' + cod, 'Consulta Auto de Infração', true);
    }

    function js_notific(cod) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', 'fis3_consnotificinf002.php?codfiscal=' + cod, 'Consulta Notificação', true);
    }
  </script>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <?php
  $pesquisaLocalizada = false;
  if ($solicitacao == "VISTORIA") {
    $funcao_js = "js_vist|y70_codvist";
    if ($tipo == "CGM") {
      $where = "(vistcgm.y73_numcgm = $cod or y80_numcgm = $cod or q02_numcgm = $cod) and y70_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "MATRICULA") {
      $where = "vistmatric.y72_matric = $cod and y70_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "INSCRICAO") {
      $where = "vistinscr.y71_inscr = $cod and y70_instit = " . db_getsession('DB_instit');
    }
    $sql = $clvistorias->sql_query_cons(
      null,
      "y70_codvist,
    y70_data,
    y70_hora,
    y70_obs,
    y70_contato,
    (SELECT y77_codtipo||' - '||y77_descricao FROM fiscal.tipovistorias  WHERE y77_codtipo = y70_tipovist) as \"dl_tipo de vistoria\",
    y70_id_usuario,
    (SELECT coddepto||' - '||descrdepto  FROM configuracoes.db_depart WHERE coddepto = y70_coddepto) as dl_departamento,
    y70_numbloco,
    y70_parcial,
    y70_ativo,
    y70_instit,
    y70_ultandam",
      null,
      $where
    );
    $pesquisaLocalizada = true;
  } else if ($solicitacao == "AUTO") {
    $funcao_js = "js_auto|y50_codauto";
    if ($tipo == "CGM") {
      $where = "(autocgm.y54_numcgm = $cod or sanitario.y80_numcgm = $cod or q02_numcgm = $cod) and y50_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "MATRICULA") {
      $where = "automatric.y53_matric = $cod and y50_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "INSCRICAO") {
      $where = "autoinscr.y52_inscr = $cod and y50_instit = " . db_getsession('DB_instit');
    }
    $sql = $clauto->sql_query_info(
      null,
      "y50_codauto,
      TO_CHAR(y50_data, 'DD/MM/YYYY') AS y50_data,
      y50_hora,
      y50_obs,
      (SELECT coddepto||' - '||descrdepto  FROM configuracoes.db_depart WHERE coddepto = y50_setor) as dl_departamento,
      y50_nome,
      TO_CHAR(y50_dtvenc, 'DD/MM/YYYY') AS y50_dtvenc,
      y50_numbloco,
      y50_prazorec,
      y50_codtipo,
      y50_instit",
      null,
      $where
    );

    $autos = db_query($sql);
    $numRowsResult = pg_num_rows($autos);
  ?>
    <table class="DBLovrotTable" align="center">
      <tbody>
        <tr>
          <th colspan="12"> &nbsp; </th>
        </tr>
        <?php
        if ($numRowsResult > 0) {
          for ($x = 0; $x < $numRowsResult; $x++) {
            db_fieldsmemory($autos, $x);
        ?>
            <tr>
              <td title="Código do Auto de Infração"> <b>Código do Auto de Infração</b></td>
              <td title="Data do Auto de Infração" class="DBLovrotTdCabecalho"> <b>Data do Auto de Infração</b></td>
              <td title="Hora do Auto" class="DBLovrotTdCabecalho"> <b>Hora do Auto</b></td>
              <td title="Observação do auto" class="DBLovrotTdCabecalho"> <b>Observação do auto</b></td>
              <td title="Departamento" class="DBLovrotTdCabecalho"> <b>Departamento</b></td>
              <td title="Nome da Pessoa Autuada" class="DBLovrotTdCabecalho"> <b>Nome da Pessoa Autuada</b></td>
              <td title="Data do Vencimento Atualizada" class="DBLovrotTdCabecalho"> <b>Data do Vencimento Atualizada</b></td>
              <td title="Número do Bloco" class="DBLovrotTdCabecalho"> <b>Número do Bloco</b></td>
              <td title="Prazo p/ Recurso" class="DBLovrotTdCabecalho"> <b>Prazo p/ Recurso</b></td>
              <td title="Cod. Tipo" class="DBLovrotTdCabecalho"> <b>Cod. Tipo</b></td>
              <td title="Código da Instituição" class="DBLovrotTdCabecalho"> <b>Cod. Instituição</b></td>
            </tr>
            <tr>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_codauto; ?>"><?php echo $y50_codauto; ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_data; ?>"><?php echo $y50_data; ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_hora; ?>"><?php echo $y50_hora; ?></td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_obs; ?>"><?php echo mb_strimwidth($y50_obs, 0, 20, "..."); ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $dl_departamento; ?>"><?php echo mb_strimwidth($dl_departamento, 0, 15, "..."); ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_nome; ?>"><?php echo mb_strimwidth($y50_nome, 0, 24, "..."); ?></td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_dtvenc; ?>"><?php echo $y50_dtvenc; ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_numbloco; ?>"><?php echo $y50_numbloco; ?></td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_prazorec; ?>"><?php echo $y50_prazorec; ?> </td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_codtipo; ?>"><?php echo $y50_codtipo; ?></td>
              <td class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y50_instit; ?>"><?php echo $y50_instit; ?></td>
            </tr>
            <tr>
              <td colspan="3" bgcolor="#CDCDFF" title="Descrição da Procedência Fiscal" class="DBLovrotTdCabecalho"><b>Descrição da Procedência Fiscal</b></td>
            </tr>
            <?php
            $results = $clautotipo->sql_record($clautotipo->sql_query_baixa("", " y29_codtipo||'-'||y29_descr as y29_descr ", "", " y59_codauto = $y50_codauto"));
            $RowsResult = pg_num_rows($results);
            if ($RowsResult > 0) {
              for ($i = 0; $i < $RowsResult; $i++) {
                db_fieldsmemory($results, $i);
            ?>
                <tr>
                  <td colspan="3" class="DBLovrotRegistrosRetornados" bgcolor="#97B5E6" title="<?php echo $y29_descr; ?>"><?php echo $y29_descr; ?> </td>
                </tr>
              <?php
              }
            } else {
              ?>
              <tr>
                <th colspan="3">Nenhuma Procedência Cadastrada!!</th>
              </tr>
            <?php
            }
            ?>
            <tr>
              <th colspan="12"> &nbsp; </th>
            </tr>
            <tr>
              <th colspan="12"> &nbsp; </th>
            </tr>
          <?php
          }
        } else {
          ?>
          <tr>
            <th colspan="6">Nenhum Registro Cadastrado!!</th>
          </tr>
        <?php
        }
        ?>
      </tbody>
    </table>
  <?php


    $pesquisaLocalizada = false;
  } else if ($solicitacao == "NOTIFICA") {
    $funcao_js = "js_notific|y30_codnoti";
    if ($tipo == "CGM") {
      $where = "(fiscalcgm.y36_numcgm = $cod or sanitario.y80_numcgm = $cod or q02_numcgm = $cod) and y30_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "MATRICULA") {
      $where = "fiscalmatric.y35_matric = $cod and y30_instit = " . db_getsession('DB_instit');
    } else if ($tipo == "INSCRICAO") {
      $where = "fiscalinscr.y34_inscr = $cod and y30_instit = " . db_getsession('DB_instit');
    }
    $sql = $clfiscal->sql_query_cons(null, "fiscal.*", null, $where);
    $pesquisaLocalizada = true;
  } else if ($solicitacao == "LEVANTA") {
    $funcao_js = "";
    if ($tipo == "CGM") {
      $where = "levcgm.y93_numcgm = $cod or q02_numcgm = $cod";
    } else if ($tipo == "MATRICULA") {
      $where = "1=2";
    } else if ($tipo == "INSCRICAO") {
      $where = "levinscr.y62_inscr = $cod";
    }
    $sql = $cllevanta->sql_query_inf(
      null,
      "y60_codlev,	y60_data, q02_inscr,y60_contato, y60_dtini, y60_dtfim, y60_obs, y60_importado, y60_proces, y60_espontaneo",
      null,
      $where
    );
    $pesquisaLocalizada = true;
  }
  if ($pesquisaLocalizada == true) {
    $result = db_query($sql);
    if (pg_numrows($result) == 0) {
      echo "<br><br><b>Nenhum Registro Cadastrado!!<b>";
    } else {
      db_lovrot($sql, 20, "()", "", $funcao_js, "", "NoMe", array(), false);
    }
  }
  ?>
</body>
<style>
  td {
    border: 1px solid black;
  }
</style>

</html>