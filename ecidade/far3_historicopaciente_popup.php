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

require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_app.utils.php'));
require_once(modification('libs/db_utils.php'));
include(modification("dbforms/db_funcoes.php"));

$oDaoFarRetiradaitens = new cl_far_retiradaitens;

function novaLinha($iReq, $sLote, $dData, $iQuantidade, $iCodMedicamento, $sMedicamento, 
                   $sLogin, $dValLote, $sTipo, $sMotivo, $iTipo) {

  if (empty($iReq)) {
    $iReq = '&nbsp;';
  }
  if (empty($sLote)) {
    $sLote = '&nbsp;';
  }
  if (empty($dValLote)) {
    $dValLote = '&nbsp;';
  }
  if (empty($dData)) {
    $dData = '&nbsp;';
  }
  if (empty($iQuantidade)) {
    $iQuantidade = '&nbsp;';
  }
  if (empty($iCodMedicamento)) {
    $iCodMedicamento = '&nbsp;';
  }
  if (empty($sMedicamento)) {
    $sMedicamento = '&nbsp;';
  }
  if (empty($sLogin)) {
    $sLogin = '&nbsp;';
  }
  if (empty($sMotivo)) {
    $sMotivo = '&nbsp;';
  }

  if ($iTipo == 2) {
    $sStyle = 'style="background-color: #FFFFAA;"';
  } else {
    $sStyle ='';
  }
  
  echo '<tr bgcolor=\'#ffffff\' '.$sStyle.'>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$dData.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$sTipo.'</td>
          <td align=\'right\' style="border: 1px solid #000000" nowrap>'.$iCodMedicamento.'</td>
          <td align=\'left\' style="border: 1px solid #000000" nowrap>'.$sMedicamento.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$iQuantidade.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$sLote.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$dValLote.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$iReq.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$sMotivo.'</td>
          <td align=\'center\' style="border: 1px solid #000000" nowrap>'.$sLogin.'</td>
        </tr>';

}

function formataData($dData, $iTipo = 1) {

  if (empty($dData)) {
    return '';
  }

  if ($iTipo == 1) {

    $dData = explode('/',$dData);
    $dData = $dData[2].'-'.$dData[1].'-'.$dData[0];
    return $dData;
  
  }
 
 $dData = explode('-',$dData);
 $dData = @$dData[2].'/'.@$dData[1].'/'.@$dData[0];
 return $dData;

}

// Alterado busca da quantidade retirada quando a retirada foi fracionada em lotes. Busca a quantidade retirada de cada lote.
// Se não busca o total retirado
$sCampos  = 'fa04_d_data, fa01_i_codigo, m60_descr,  m77_lote, fa07_i_matrequi, login, tipo,';
$sCampos  .= ' case ';
$sCampos  .= '   when fa09_f_quant is not null ';
$sCampos  .= '     then fa09_f_quant ';
$sCampos  .= '   else fa06_f_quant ';
$sCampos  .= ' end as fa06_f_quant, ';
$sCampos .= "  CASE ";
$sCampos .= "    WHEN fa04_tiporetirada = 1 AND tipo = 1 ";
$sCampos .= "      THEN 'Normal' ";
$sCampos .= "    WHEN fa04_tiporetirada = 2 AND tipo = 1 ";
$sCampos .= "      THEN 'Não padronizada' ";
$sCampos .= "    WHEN fa23_i_cancelamento = 2 AND tipo = 2 ";
$sCampos .= "      THEN 'Devolução' ";
$sCampos .= "    WHEN fa23_i_cancelamento = 1 AND fa04_tiporetirada = 1 AND tipo = 2 ";
$sCampos .= "      THEN 'Cancelamento' ";
$sCampos .= "    ELSE 'Cancelamento N. P.' ";
$sCampos .= "  END AS stipo, ";
$sCampos .= " m77_dtvalidade, fa23_c_motivo, ";
$sCampos .= 'fa22_d_data, fa23_i_quantidade ';
$sSql     = $oDaoFarRetiradaitens->sql_query_historicoretiradasdevolucoes($cgs_get, 
                                                                          $sCampos, 
                                                                          'fa06_i_codigo desc, tipo asc'
                                                                         );

$rs       = $oDaoFarRetiradaitens->sql_record($sSql);
$iLinhas  = $oDaoFarRetiradaitens->numrows;

?>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<link rel='stylesheet' type='text/css' href='estilos.css'>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<center>
  <table border='0' cellspacing='0' width='730'>
    <?php
    if (!isset($lConsultaGeral)) {
    ?>
    <tr>
      <td align='left' nowrap>
        <b>Paciente:</b>
      </td>
      <td>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td nowrap width='12%'>
        <?php db_input('cgs_get', 10, '', true, 'text', 3, ""); ?>
      </td>
      <td align='left' nowrap>
        <?php db_input('nome', 75, '', true, 'text', 3, ""); ?>
      </td>
    </tr>
    <?php
    }
    ?>
    <tr>
      <td colspan='2' align='left' nowrap>
        <b>Hist&oacute;rico de Retiradas de Medicamentos:</b>
      </td>
    </tr>
  </table>

  <table width='730' cellspacing='0' border='0'>
    <tr bgcolor='#efefef'>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Data</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Tipo de Retirada</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>C&oacute;digo</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Medicamento</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Quantidade</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Lote</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Validade</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Requisi&ccedil;&atilde;o</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Motivo da Devolução</b>
      </td>
      <td align='center' style="border: 1px solid #000000" nowrap>
        <b>Usuário</b>
      </td>
    </tr>
<?php
for($iCont = 0; $iCont < $iLinhas; $iCont++) {

  $oDados = db_utils::fieldsmemory($rs, $iCont);
  if ($oDados->tipo == 2) {

    $oDados->fa04_d_data  = $oDados->fa22_d_data;
    $oDados->fa06_f_quant = $oDados->fa23_i_quantidade;

  }
  novaLinha($oDados->fa07_i_matrequi, $oDados->m77_lote, formataData($oDados->fa04_d_data, 2), 
            $oDados->fa06_f_quant, $oDados->fa01_i_codigo, $oDados->m60_descr, $oDados->login,
            formataData($oDados->m77_dtvalidade, 2), $oDados->stipo, $oDados->fa23_c_motivo,
            $oDados->tipo
           );

}

?>
  </table>
</center>

<?php
if (!isset($lConsultaGeral)) {
?>
<center>
  <table width='100%'>
    <tr>
      <td align='center'>
        <font color='#FF0000' face='arial'>
            <?php
            if ($iLinhas > 0) {
            ?>
            <input type='button' value='Relatório' onclick='js_relatorio()'>
            <?php
            } else {
            ?>
            	<b>Nenhum registro encontrado.<br>
            <?php
            }
            ?>
            <input type='button' value='Fechar' onclick='js_fechar()'>            
          </b>
        </font>
      </td>
    </tr>
  </table>
</center>
<?php
}
?>
<script>

function js_relatorio() {

  oJan = window.open('far2_historicoretirada002.php?iCgs=<?=$cgs_get?>&sNome=<?=@$nome?>', '',
                     'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+
                     ',scrollbars=1,location=0 '
                    );
  oJan.moveTo(0, 0);

}
function js_fechar() {
  parent.db_iframe_historico.hide();
}

</script>