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

require_once(modification("classes/db_obrasalvara_classe.php"));

$oDaoObrasAlvara = new cl_obrasalvara;
$oDaoObrasEnvioRegAlvara = new cl_obrasenvioregalvara;
$oGet            = db_utils::postMemory($_GET);

$oDaoParProjetos = db_utils::getDao('parprojetos');
$sSqlParametros  = $oDaoParProjetos->sql_query_pesquisaParametros( db_getsession('DB_anousu') ); 
$rsParametros    = $oDaoParProjetos->sql_record($sSqlParametros);
if ($oDaoParProjetos->erro_status != "0") {
    $oParametros = db_utils::fieldsMemory($rsParametros, 0);
    $db_opcao    = 3;
} else {
   db_msgbox(_M('tributario.projetos.pro3_consultaobra002_alvara.paremetros_nao_configurados'));
   exit;
} 

$iTipoRelatorio = $oParametros->ob21_tipocartaalvara;


/**
 * Solicitação alvara
 */   
$rsObrasAlvara = $oDaoObrasAlvara->sql_record($oDaoObrasAlvara->sql_query(null, "*", "", "ob04_codobra = {$oGet->parametro}"));

if($oDaoObrasAlvara->numrows > 0){

  $oObrasAlvara = db_utils::fieldsMemory($rsObrasAlvara, 0, true);

  $rsObrasEnvioRegAlvara = $oDaoObrasEnvioRegAlvara->sql_record($oDaoObrasEnvioRegAlvara->sql_query(null, "*", "", "ob31_codalvara = {$oObrasAlvara->ob04_alvara}"));
  if($oDaoObrasEnvioRegAlvara->numrows > 0){
    $oObrasEnvioRegAlvara = db_utils::fieldsMemory($rsObrasEnvioRegAlvara, 0, true);
  }

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>

  <style>
    #elemento_principal {
      width: 100%;
    } 
    #elemento_principal tr td:first-child {
      width: 150px;
    }
  </style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <br />
  <br />
	<fieldset style="margin-bottom: 10px;">
	  <legend><B>Dados do Alvará: </B></legend>
	  <table id="elemento_principal">
    <tr> 
      <td nowrap><strong>Cod. Alvará:</strong></td>
      <td nowrap bgcolor="#FFFFFF"><?php echo $oObrasAlvara->ob04_alvara; ?></td>
    </tr>
    <tr>
      <td nowrap><strong>Data:</strong></td>
      <td nowrap bgcolor="#FFFFFF"><?php echo $oObrasAlvara->ob04_data; ?></td>
    </tr>
    <tr> 
      <td nowrap><strong>Situação:</strong></td>
      <td nowrap bgcolor="#FFFFFF"><?php echo $oObrasAlvara->ob04_ativo == 't' ? 'Ativo' : 'Cancelado' ?></td>
    </tr>
    <tr>
      <td nowrap><strong>Data da Situação:</strong></td>
      <td nowrap bgcolor="#FFFFFF">
          <?php
          $mensagem = "Sem Alteração";

          if(!empty($oObrasAlvara->ob04_datacancelamentoreativacao)) {
              $data = new DBDate($oObrasAlvara->ob04_datacancelamentoreativacao);
              $mensagem = "{$data->getDate(DBDate::DATA_PTBR)}";
          }

          echo $mensagem;
          ?>
      </td>
    </tr>
    <tr>
      <td nowrap><strong>Protocolo Sisobra:</strong></td>
      <td nowrap bgcolor="#FFFFFF"><?php echo $oObrasEnvioRegAlvara->ob31_protocolo?></td>
    </tr>
  </table>
</fieldset>
<center>
  <input name="emite2" id="emite2" type="button" value="Emitir Carta de Alvará" onclick="js_emite(<?=$iTipoRelatorio; ?>);" > 
</center>
<fieldset style="margin-top: 5px;">
  <legend>Histórico de Renovações</legend>
  <div id="gridHistorico"></div>
</fieldset>


<?php

/**
 * Se não existir habite-se
 */   
} else { 
	 
	echo "<br /><br />                                              ";
	echo "<center>                                                  ";
	echo "  <strong>Nenhum alvará liberado para está obra.</strong> ";
	echo "</center>                                                 ";
	echo "<br /><br />                                              ";
}
?> 
<script>

  js_historicoAlvara(<?=$oGet->parametro?>);

  function js_historicoAlvara(iCodigoObra){

    var oParam         = {};
    oParam.exec        = 'getHistorico';
    oParam.iCodigoObra = iCodigoObra;

    var oAjax = new Ajax.Request( 'pro4_obrasalvara.RPC.php',
                                 {
                                   method: 'POST',
                                   parameters: 'json=' + Object.toJSON(oParam),
                                   onComplete: function (oAjax){
                                     
                                     var oRetorno = JSON.parse(oAjax.responseText);
                                     js_montaGrid(oRetorno.aHistoricos);
                                   }
                                 });
  }

  function js_montaGrid(aHistoricos){

    var oGridHistorico = new DBGrid('historico_renovacoes');
    oGridHistorico.setHeader(new Array('Data Inicial', 'Data Final'));
    oGridHistorico.setCellWidth(new Array('50%', '50%'));
    oGridHistorico.setHeight(80);
    oGridHistorico.show($('gridHistorico'));
    
    oGridHistorico.clearAll(true);


    for(var iHistorico = 0; iHistorico < aHistoricos.length; iHistorico++){

      var oHistorico = aHistoricos[iHistorico];
      var aLinha = new Array();
      aLinha[0] = js_formatar(oHistorico.ob35_datainicial, 'd');
      aLinha[1] = js_formatar(oHistorico.ob35_datafinal, 'd');

      oGridHistorico.addRow(aLinha);
    }

    oGridHistorico.renderRows();
  }

  function js_emite(iTipoRelatorio) {

    /**
     * Verifica qual relatório abrir, 0 pdf, 1 office
     */   
    if(iTipoRelatorio == 0) {
      sTipoArquivoRelatorio = "pro2_execobra002.php";
    } else {
      sTipoArquivoRelatorio = "pro2_execobra003.php";
    }

    jan = window.open(sTipoArquivoRelatorio+'?codigo=<?=$oGet->parametro?>',
      '',
      'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
</script>
</body>
</html>
