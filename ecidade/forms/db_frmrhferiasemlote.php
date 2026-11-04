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
?>
<form name="form1" method="post" action="" onsubmit="return js_verificaFormulario();">

  <input type="hidden" id="iAnoFolha"    name="iAnoFolha">
  <input type="hidden" id="iMesFolha"    name="iMesFolha">

	<fieldset style="width:650px;">
	
		<legend><strong>Cadastro de Férias em Lote</strong></legend>
		
		<fieldset>
		
			<legend><strong>Dados de Férias</strong></legend>
			
		  <table>
				<tr>
				  <td nowrap="nowrap">
				  	<?php db_ancora(@$Lr44_selec, "js_pesquisar44_selec(true);", $db_opcao); ?>
				  </td>
				  <td colspan="3">
				    <?php 
				      db_input('r44_selec', 8, $Ir44_selec, true, 'text', $db_opcao, " onchange='js_pesquisar44_selec(false);' ");
				      db_input('r44_descr', 40, $Ir44_descr, true, 'text', 3);
				    ?>
				  </td>
				</tr>
				<tr>
				  <td nowrap="nowrap">
				  	<?php 
				  	  echo $Lr30_tipoapuracaomedia;
				  	?>
				  </td>
				  <td colspan="3">
				    <?php 
				      $aTiposApuracao = array('N' => "Período Aquisitivo Normal",
				                              'E' => "Período Específico");
				      db_select('r30_tipoapuracaomedia', $aTiposApuracao, true, 1, "style='width: 385px;' onchange='js_alteraApuracaoMedia();' ");
				    ?>
				  </td>
				</tr>
				<tr style="display:none;" id="camposperiodoespecifico">
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh109_periodoespecificoinicial; 
						?>
					</td>
					<td>
						<?php
						  db_inputdata('rh109_periodoespecificoinicial',
						               @$rh109_periodoespecificoinicial_dia,
						               @$rh109_periodoespecificoinicial_mes,
						               @$rh109_periodoespecificoinicial_ano,
						               true, 'text', $db_opcao, "");
						?>
					</td>
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh109_periodoespecificofinal;
						?>
					</td>
					<td>
						<?php 
						  db_inputdata('rh109_periodoespecificofinal',
						               @$rh109_periodoespecificofinal_dia,
						               @$rh109_periodoespecificofinal_mes,
						               @$rh109_periodoespecificofinal_ano,
						               true, 'text', $db_opcao, "");
						?>
					</td>
				</tr>
				<tr>
				  <td nowrap="nowrap">
				  	<strong>Trazer Férias já Processadas no Lote: </strong>
				  </td>
				  <td>
				    <?php 
				      $aOpcoesFeriasProcessadas = array('2' => 'NÃO',
				                                        '1' => 'SIM');
				      db_select("filtraferiasprocessadas", $aOpcoesFeriasProcessadas, true, 1, "style='width: 141px;'");
				    ?>
				  </td>
				  <td nowrap="nowrap">
				  	<strong>Períodos Aquisitivos: </strong>
				  </td>
				  <td>
				    <?php 
				      $aOpcoesPeriodoAquisito = array('3' => 'Todos',
				                                      '1' => 'Vencidos até',
				                                      '2' => 'Não vencidos');
				      db_select('periodoaquisitivo', $aOpcoesPeriodoAquisito, 
				                true, 1, " onchange='js_alteraPeriodosAquisitivos();' ");
				    ?>
				  </td>
				</tr>
				<tr style="display:none;" id="tr_vencidos">
					<td nowrap="nowrap">
					  <strong>Períodos vencidos até: </strong>
					</td>
					<td colspan="3">
					  <?php 
					    db_inputdata('periodosvencidosate', '', '', '', true, 'text', 1);
					  ?>
					</td>
				</tr>
				<tr>
				  <td nowrap="nowrap">
				    <strong>Tipo do processamento: </strong>
				  </td>
				  <td colspan="3">
				    <?php 
				      $aOpcoesTipoProcessamento = array('1' => 'Com confirmação',
				                                        '2' => 'Sem confirmação');
				      db_select('tipoprocessamento', $aOpcoesTipoProcessamento, true, 1);
				    ?>
				  </td>
				</tr>
			</table> 
		
		</fieldset>
		
		<fieldset>
		
			<legend><strong>Dados do Período a Gozar</strong></legend>
			
			  <table>
				<tr>
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh110_datainicial;
						?>
					</td>
					<td>
						<?php 
						  db_inputdata('rh110_datainicial',
						               @$rh110_datainicial_dia,
						               @$rh110_datainicial_mes,
						               @$rh110_datainicial_ano,
						               true, 'text', $db_opcao, "onChange='js_validaDataPeriodoGozo();'");
						?>
					</td>
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh110_datafinal;
						?>
					</td>
					<td>
						<?php 
						  db_inputdata('rh110_datafinal',
						               @$rh110_datafinal_dia,
						               @$rh110_datafinal_mes,
						               @$rh110_datafinal_ano,
						               true, 'text', $db_opcao, "onChange='js_validaDataPeriodoGozo();'");
						?>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh110_tipoponto;
						?>
					</td>
					<td>
						<?php 
						  $aOpcoesTipoPonto = array('S' => 'Salário',
						                            'C' => 'Complementar');
						  db_select('rh110_tipoponto', $aOpcoesTipoPonto, true, $db_opcao, "style='width: 125px;'");
						?>
					</td>
					<td nowrap="nowrap">
						<?php 
						  echo $Lrh110_pagaterco;
						?>
					</td>
					<td>
					  <?php
              $aOpcoesPagaTerco = array('false' => 'NÃO', 'true' => 'SIM');
              db_select('rh110_pagaterco', $aOpcoesPagaTerco, true, $db_opcao, "style='width: 125px;'");
            ?>
					</td>
				</tr>
				<tr>
				  <td nowrap="nowrap">
				    <strong>Ano / Mês pagamento: </strong>
				  </td>
				  <td colspan="3">
				  	<?php 
				  	  db_input("DBtxt23", 4,'rh110_anopagamento', true, "text", 1,"","rh110_anopagamento");
				  	?>
				  	&nbsp;/&nbsp;
				  	<?php
				  	  db_input("DBtxt25", 2, 'rh110_mespagamento', true, "text", 1,"","rh110_mespagamento");
				  	?>
				  </td>
				</tr>
				<tr>
				  <td colspan="4">
				  	<fieldset style="width: 596px;">
				  		<legend><strong>Observações</strong></legend>
				  		<?php 
				  		  db_textarea('rh110_observacao', 5, 80, "", true, null, 1);
				  		?>
				  	</fieldset>
				  </td>
				</tr>
			</table> 
		</fieldset> 
	</fieldset>
	
	<input type="button" value="Incluir" onclick="js_processaFormulario();" style="margin-top: 5px;"/>
	
</form>

<script>

var sUrlRPC = 'pes4_cadastroferias.RPC.php';
var oParam  = new Object();

js_carregaValoresDefaultFormulario();

function js_carregaValoresDefaultFormulario() {

  var msgDiv        = "Carregando dados do formulário \n Aguarde ...";
  js_divCarregando(msgDiv,'msgBox');
  
  oParam.sExec      = "carregaDadosDefaultFormulario";
  var oAjax  = new Ajax.Request(sUrlRPC,
                                     {method     : "post",
                                      parameters : 'json=' + Object.toJSON(oParam),
                                      onComplete :  js_retornoDadosDefaultFormulario
                                     });
  
}

function js_retornoDadosDefaultFormulario(oAjax) {
  
  var oRetorno = JSON.parse(oAjax.responseText);

  js_removeObj('msgBox');
  
  if (oRetorno.iStatus == 2){
    alert(oRetorno.sMessage.urlDecode());
    if (oRetorno.lVoltar) {
      js_voltar();
    }  
    return false;
  }  

  $('rh110_pagaterco').value    = ( (oRetorno.lPagaTerco == "t") ? true : false);
  $('rh110_tipoponto').value    = oRetorno.sTipoPonto;
  $('iAnoFolha').value          = oRetorno.iAnoFolha;
  $('rh110_anopagamento').value = oRetorno.iAnoFolha;
  $('iMesFolha').value          = oRetorno.iMesFolha;
  $('rh110_mespagamento').value = oRetorno.iMesFolha;
  
}


function js_validaDataPeriodoGozo() {

	  var iAnoFolha    = $F('rh110_anopagamento');
	  var iMesFolha    = $F('rh110_mespagamento');   

	  var dDataInicial = getDateInDatabaseFormat($F('rh110_datainicial'));
	  var iAnoPeriodoInicial = dDataInicial.substr(0,4);
	  var iMesPeriodoInicial = dDataInicial.substr(5,2);
	  var iDiaPeriodoInicial = dDataInicial.substr(8,2);
    var iDataInicialInvertida = iAnoPeriodoInicial+""+iMesPeriodoInicial+""+iDiaPeriodoInicial;

	  if ($F('rh110_datafinal') != '') {

		  var dDataFinal = getDateInDatabaseFormat($F('rh110_datafinal'));
	    var iAnoPeriodoFinal = dDataFinal.substr(0,4);
	    var iMesPeriodoFinal = dDataFinal.substr(5,2);
	    var iDiaPeriodoFinal = dDataFinal.substr(8,2);
	    var iDataFinalInvertida = iAnoPeriodoFinal+""+iMesPeriodoFinal+""+iDiaPeriodoFinal;
	  }
	  
    if (iAnoPeriodoInicial < iAnoFolha || iMesPeriodoInicial < iMesFolha) {
		  
	    alert('A data para gozo deve ficar entre o primeiro dia do mês de competência e até 180 dias após o fim do período de competência');   
	    $('rh110_datainicial').value ='';
	    $('rh110_datafinal').value   ='';
	    return false;
	  }
	  
	  if ($('rh110_datafinal').value != '' && $('rh110_datainicial').value != '' && iDataFinalInvertida < iDataInicialInvertida) {
		  
		  alert('A data final do período de gozo deve ser maior que a data inicial');
		  $('rh110_datafinal').value   ='';
		  return false;
	  }  

	  if (iDataFinalInvertida - iDataInicialInvertida > 30) {

		  alert('O período de férias não pode ser superior a 30 dias.');
		  $('rh110_datafinal').value   ='';
		  return false;
	  }

	  /*
	   * @todo implementar lógica para quando a data do periodo de gozo final for maior que 180 do mes da folha 
	   */

}
	

/**
 * Função que envia os dados ao RPC
 */
function js_processaFormulario() {

  if (js_verificaFormulario()) {

    var oParam                                 = new Object();
        oParam.sExec                           = 'salvarFeriasEmLote';
        oParam.iAnoFolha                       = $F('iAnoFolha');
        oParam.iMesFolha                       = $F('iMesFolha');
        oParam.iSelecao                        = $F('r44_selec');
        oParam.sTipoApuracaoMedia              = $F('r30_tipoapuracaomedia');
        oParam.sPeriodoEspecificoInicial       = $F('rh109_periodoespecificoinicial');
        oParam.sPeriodoEspecificoFinal         = $F('rh109_periodoespecificofinal');
        oParam.iFeriasProcessadas              = $F('filtraferiasprocessadas');
        oParam.iPeriodosAquisitivos            = $F('periodoaquisitivo');
        oParam.sPeriodosAquisitivosVencidosAte = $F('periodosvencidosate');
        oParam.iTipoProcessamento              = $F('tipoprocessamento');
        oParam.sDataInicialFerias              = $F('rh110_datainicial');
        oParam.sDataFinalFerias                = $F('rh110_datafinal');
        oParam.sTipoPonto	                     = $F('rh110_tipoponto');
        oParam.lPagaTerco                      = $F('rh110_pagaterco');
        oParam.iAnoPagamento                   = $F('rh110_anopagamento');
        oParam.iMesPagamento                   = $F('rh110_mespagamento');
        oParam.sObservacoes                    = $F('rh110_observacao');
        var dDataInicial                       = getDateInDatabaseFormat($F('rh110_datainicial'));
  	    var iAnoPeriodoInicial                 = dDataInicial.substr(0,4);
  	    var iMesPeriodoInicial                 = dDataInicial.substr(5,2);
  	    var iDiaPeriodoInicial                 = dDataInicial.substr(8,2);
        var iDataInicialInvertida              = iAnoPeriodoInicial+""+iMesPeriodoInicial+""+iDiaPeriodoInicial;
        var dDataFinal                         = getDateInDatabaseFormat($F('rh110_datafinal'));
	      var iAnoPeriodoFinal                   = dDataFinal.substr(0,4);
	      var iMesPeriodoFinal                   = dDataFinal.substr(5,2);
	      var iDiaPeriodoFinal                   = dDataFinal.substr(8,2);
        var iDataFinalInvertida                = iAnoPeriodoFinal+""+iMesPeriodoFinal+""+iDiaPeriodoFinal;
        oParam.iDiasGozo                       = (iDataFinalInvertida - iDataInicialInvertida)+1;
    js_divCarregando('Aguarde, processando operação...','msgBox');
    var oAjaxLista = new Ajax.Request(sUrlRPC,
                                      {method : 'post',
                                       parameters : 'json='+Object.toJSON(oParam),
                                       onComplete : js_retornoProcessaFormulario});
  }
}

/**
 * Função que processa o retorno do RPC.
 */
function js_retornoProcessaFormulario(oAjax) {

   js_removeObj('msgBox');
   var oRetorno = JSON.parse(oAjax.responseText);
   alert(oRetorno.iStatus);

   if (oRetorno.iStatus == '1') {

     alert('Operação realizada com sucesso.');
     location.href = "pes4_cadastroferiaslote001.php";
     return false;
   } else if (oRetorno.iStatus == '2') {

     alert(oRetorno.sMessage.urlDecode());
     return false;
   } else if (oRetorno.iStatus == '3') {

	   var sURL   = 'pes4_cadastroferias001.php';
	   js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_processamentoferiaslote', sURL, 'Cadastro de Férias em Lote', true);
	   return false;
   } else if (oRetorno.iStatus == '4') {

	   if (confirm('Houveram inconsistências na operação. Deseja verificar o relatório de inconsistências?')) {
		   window.open('pes4_cadastroferiaslote004.php', '', 'locatrion=0');
	   }
	   location.href = "pes4_cadastroferiaslote001.php";
	   return false;
   }
}

/**
 * Função que esconde o ifrane exibibo pela função js_retornoProcessaFormulario.
 */
function js_escondeIframeCadastroEmLote() {
	db_iframe_processamentoferiaslote.hide();
}

/**
 * Função que valida os dados antes do formulário ser submetido.
 * Verifica os dados do formulário e retorna um boolean com o status da verificação.
 */
function js_verificaFormulario() {

  if ($F('r44_selec') == '' ) {

    alert('Deve ser selecionada uma seleção. Favor verificar.');
  	return false;
  } else if ($F('rh110_datainicial') == '') {

    alert('Deve ser informada a data inicial do período de férias. Favor verificar.');
    return false;
  } else if ($F('rh110_datafinal') == '') {

    alert('Deve ser informada a data final do período de férias. Favor verificar.');
    return false;
  }
  return true;
}

/**
 * Função que busca as seleções.
 */
function js_pesquisar44_selec(mostra) {

  if (mostra == true) {

    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_selecao',
                        'func_selecao.php?funcao_js=parent.js_mostraSelecao|r44_selec|r44_descr',
                        'Pesquisa Seleções',true);
  } else {

    var iValorCampo = $F('r44_selec').trim();
    if (iValorCampo != '') {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_selecao',
                          'func_selecao.php?pesquisa_chave=' + iValorCampo + '&funcao_js=parent.js_mostraSelecao',
                          'Pesquisa Seleções', false);
    } else {
      $('r44_descr').value = '';
    }
  }
}

/**
 * Função de comportamento do retorno da função js_pesquisar44_selec.
 * Ele valida se o segundo argumento passado para a função é um boolean ou não
 * Pois possui comportamento distinto para cada uma das situações.
 */
function js_mostraSelecao() {
  
  db_iframe_selecao.hide();
  if (arguments[1] == false) {
    $('r44_descr').value = arguments[0];
  } else if (arguments[1] == true) {

	  $('r44_selec').value = '';
    $('r44_descr').value = arguments[0];
  } else {

    $('r44_selec').value = arguments[0];
    $('r44_descr').value = arguments[1];
  }
}

/**
 * Função que exibe ou esconde as informações de período específico no formulário.
 */
function js_alteraApuracaoMedia() {

  var sApuracaoMedia = $F('r30_tipoapuracaomedia');
  if (sApuracaoMedia == 'E') {
    $('camposperiodoespecifico').style.display = 'table-row';
  } else if (sApuracaoMedia == 'N') {

    $('rh109_periodoespecificoinicial').value = '';
    $('rh109_periodoespecificofinal').value = '';
    $('camposperiodoespecifico').style.display = 'none';
  }
}

/**
 * Função que exibe ou esconde o campo 'vencidos até'.
 */
function js_alteraPeriodosAquisitivos() {

  var iOpcaoPeriodoAquisitivo = $F('periodoaquisitivo');
  if (iOpcaoPeriodoAquisitivo == '1') {
    $('tr_vencidos').style.display = 'table-row';
  } else {

    $('periodosvencidosate').value = '';
    $('tr_vencidos').style.display = 'none';
  }
}
</script>