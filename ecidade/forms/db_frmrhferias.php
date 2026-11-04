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
<body bgcolor="#cccccc" style="margin-top:30px;" onLoad="a=1;" >
<center>
<form name="form1" method="post" action="">
  <input type="hidden" id="iAnoFolha"    name="iAnoFolha">
  <input type="hidden" id="iMesFolha"    name="iMesFolha">
  <input type="hidden" id="iDiasDireito" name="iDiasDireito">
  
  <fieldset style="width:650px;">
    
    <legend><strong>Cadastro de férias</strong></legend>
    
    <fieldset>
      <legend><strong>Dados das férias</strong></legend>
      
      <table style="width:650px;border:0;">
  
  
        <tr>
          <td nowrap title="<?=@$Trh109_regist?>">
             <?=@$Lrh109_regist?>
          </td>
          <td colspan="3"> 
            <?php db_input('rh109_regist',10,$Irh109_regist,true,'text',3,"") ?>
            <?php db_input('z01_nome', 48, $Iz01_nome, true, 'text', 3);  ?>
          </td>
        </tr>
  
        <tr>
          <td nowrap title="<?=@$Trh109_periodoaquisitivoinicial?>">
             <?=@$Lrh109_periodoaquisitivoinicial?>
          </td>
          <td> 
            <?php
              db_inputdata('rh109_periodoaquisitivoinicial',
                           @$rh109_periodoaquisitivoinicial_dia, 
                           @$rh109_periodoaquisitivoinicial_mes, 
                           @$rh109_periodoaquisitivoinicial_ano, 
                           true, 
                           'text', 
                           $db_opcao, 
                           "onchange='js_calculaDataFinalPeriodoAquisitivo();'",
                           "", "", "", "", "", 
                           "js_calculaDataFinalPeriodoAquisitivo();");
            ?>
          </td>
          <td nowrap title="<?=@$Trh109_periodoaquisitivofinal?>">
             <?=@$Lrh109_periodoaquisitivofinal?> </td>
          <td> 
            <?php
              db_inputdata('rh109_periodoaquisitivofinal', 
                           @$rh109_periodoaquisitivofinal_dia, 
                           @$rh109_periodoaquisitivofinal_mes, 
                           @$rh109_periodoaquisitivofinal_ano, 
                           true, 'text', $db_opcao, "");
            ?>
          </td>
        </tr>
        
        <tr>
        	<td>
        		<strong>Apuração da Média:</strong>
        	</td>
        	<td colspan="3">
        	  <?php
              $aOptionsApuracaoMedia = array('N' => 'Período Aquisitivo Normal', 
                                             'E' => 'Período Aquisitivo Específico');
              db_select('lDireitoApuracaoMedia', $aOptionsApuracaoMedia, true, 
                        $db_opcao, " style='width:456px;' onchange=js_alteraApuracaoMedia();");
            ?>
        	</td>
        </tr>
        
        <tr style="display:none;" id="camposperiodoinicial">
          <td>
          	<?php echo $Lrh109_periodoespecificoinicial?>
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
          <td>
          	<?php echo $Lrh109_periodoespecificofinal?>
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
          <td nowrap title="<?=@$Trh109_dias?>">
             <?=@$Lrh109_dias?>
          </td>
          <td> 
            <?php db_input('rh109_dias', 10, $Irh109_dias, true, 'text', 3, ""); ?>
          </td>
          <td nowrap title="<?=@$Trh109_faltasperiodoaquisitivo?>">
             <strong>Faltas: </strong>
          </td>
          <td> 
            <?php
              db_input('rh109_faltasperiodoaquisitivo', 10, $Irh109_faltasperiodoaquisitivo, true, 'text', $db_opcao, " onchange=js_calculaSaldoDiasDireito()");
            ?>
          </td>
        </tr>
  
        <tr>
          <td nowrap title="">
             <strong>Direito a férias:</strong>
          </td>
          <td clospan="3"> 
            <?php
              $aOptionsDireitoFerias = array('S' => 'SIM', 'N' => 'NÃO');
              db_select('lDireitoFerias', $aOptionsDireitoFerias, true, $db_opcao, "onchange=js_semDireitoFerias();");
            ?>
          </td>
        </tr>  
      </table>
    </fieldset>
  
    <fieldset id="FieldsetPeriodoGozar" style='margin-top:10px;' >
      <legend><strong>Dados do periodo a gozar</strong></legend>    
      <table style="border:0;width:650px;">
      
         <tr>
          <td nowrap title="<?php echo $Trh110_dias; ?>">
             <?php echo $Lrh110_dias; ?>
          </td>
          <td>
            <?php
              db_input('rh110_dias', 10, $Irh110_dias, true, 'text', $db_opcao, "onChange='js_calculaDataFinalPeriodoGozo();' js_verificaDiasGozo();");
            ?>
          </td>
          <td nowrap title="<?php echo $Trh110_diasabono; ?>">
             <?php echo $Lrh110_diasabono; ?>
          </td>
          <td>
            <?php
              db_input('rh110_diasabono', 10, $Irh110_diasabono, true, 'text', $db_opcao, "onChange='js_verificaDiasGozo();'");
            ?>
          </td>
        </tr>
      
        <tr>
          <td nowrap title="<?php echo $Trh110_datainicial; ?>">
             <?php echo $Lrh110_datainicial; ?>
          </td>
          <td>
            <?php
              db_inputdata('rh110_datainicial', 
                           $rh110_datainicial_dia, 
                           $rh110_datainicial_mes,
                           $rh110_datainicial_ano, 
                           true, 
                           'text', 
                           $db_opcao, 
                           " onchange = 'js_calculaDataFinalPeriodoGozo(); js_validaDataPeriodoGozo();' ");
            ?>
          </td>
          <td nowrap title="<?php echo $Trh110_datafinal; ?>">
             <?php echo $Lrh110_datafinal; ?>
          </td>
          <td>
            <?php
              db_inputdata('rh110_datafinal', 
                           $rh110_datafinal_dia, 
                           $rh110_datafinal_mes,
                           $rh110_datafinal_ano, 
                           true, 
                           'text', 
                           $db_opcao, 
                           "js_validaDataPeriodoGozo(); js_calculaDiasGozar();");
            ?>
          </td>
        </tr>
        
        <tr>
        	<td>
        		<?php echo $Lrh110_tipoponto; ?>
        	</td>
        	<td>
        		<?php
        		  $aOptionsTipoPonto = array('S' => 'Salário', 'C' => 'Complementar');
              db_select('rh110_tipoponto', $aOptionsTipoPonto, true, $db_opcao, "");
            ?>
        	</td>
        	<td>
        		<?php echo $Lrh110_pagaterco; ?>
        	</td>
        	<td>
        		<?php
              $aOptionsPagaTerco = array('false' => 'NÃO', 'true' => 'SIM');
              db_select('rh110_pagaterco', $aOptionsPagaTerco, true, $db_opcao, "");
            ?>
        	</td>
        </tr>
        
        <tr>
          <td nowrap title="Digite o Ano / Mês de competência">
             <strong>Ano / Mês pagamento:</strong>
          </td>
          <td colspan="3">
             <?php
              $rh110_anopagamento = db_anofolha();
              db_input("DBtxt23", 4,'rh110_anopagamento', true, "text", 1,"","rh110_anopagamento");
              ?>
              &nbsp;/&nbsp;
              <?php
              $rh110_mespagamento = db_mesfolha();
              db_input("DBtxt25", 2, 'rh110_mespagamento', true, "text", 1,"","rh110_mespagamento");
            ?>            
          </td>
        </tr>     
        
        <tr>
          <td colspan=4" nowrap title="<?php echo $Trh110_observacao; ?>">
            <fieldset>
              <legend><strong><?php echo $Lrh110_observacao; ?></strong></legend>
              <?php db_textarea("rh110_observacao", 5, 83,  "", true, null, 1); ?>
            </fieldset>        
          </td>
        </tr>            
              
      </table>
    </fieldset>
    
    <fieldset id="FieldsetPerdaDireito" style='margin-top:10px; display: none;'>
      <legend><strong>Dados da perda de direito</strong></legend>
          
      <table style="border:0;width:650px;">
        <tr>
          <td colspan=4" nowrap title="<?php echo $Trh110_observacao; ?>">
            <fieldset>
              <legend><strong><?php echo $Lrh110_observacao; ?></strong></legend>
              <?php db_textarea("sObsPerdaDireito", 5, 83,  "", true, null, 1); ?>
            </fieldset>        
          </td>
        </tr>        
      </table>
      
    </fieldset>
    
  </fieldset>  
  
  <br />
  
  <input type="button" id="incluir" onclick="js_cadastrarFerias();" value="Incluir" />
  <?php
    /**
     * Avaliamos se a variável de sessão 'aListaMatriculasProcessamentoEmLote' está setada.
     * Em caso positivo exibimos os botões de operação em lote no formulário. 
     */ 
    if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])
        && count($_SESSION['aListaMatriculasProcessamentoEmLote']) > 1) {
  ?>
  
  	
  	<input type="button" id="proxima" onclick="js_proximaMatriculaLote();" value="Próxima matrícula" />
  	<input type="button" id="cancelar" onclick="js_cancelarCadastroEmLote();" value="Cancelar" />
  <?php } else if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])
        && count($_SESSION['aListaMatriculasProcessamentoEmLote']) == 1) { ?>
  <input type="button" id="cancelar" onclick="js_cancelarCadastroEmLote();" value="Cancelar" />
  <?php } else { ?>  
  	<input type="button" id="voltar" onclick="js_voltar();" value="Voltar" />
  <?php } ?>
        
</form>
</center>
</body>

<script>
var sUrlRPC = 'pes4_cadastroferias.RPC.php';  
var oParam  = new Object();

js_carregaValoresDefaultFormulario();
js_buscaDadosFerias();
<?php if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) { ?>
		js_buscaNomeMatricula();
<?php } ?>

function js_buscaNomeMatricula() {

	var msgDiv        = "Carregando dados do formulário \n Aguarde ...";
	js_divCarregando(msgDiv,'msgBox');
	  
	var oParam = new Object();
	oParam.sExec = "buscaNomeMatricula";
	oParam.iMatricula = $('rh109_regist').value;
	var oAjax  = new Ajax.Request(sUrlRPC,
	                                     {method     : "post",
	                                      parameters : 'json=' + Object.toJSON(oParam),
	                                      onComplete :  function(oAjax) {
		                                      
	                                    	                var oRetorno = JSON.parse(oAjax.responseText);
	                                    	                js_removeObj('msgBox');
	                                    	                $('z01_nome').value = oRetorno.sNomeMatricula.urlDecode();
	                                                    }
	                                     });
}

/**
 * Se a variável aListaMatriculasProcessamentoEmLote estiver na sessão do PHP
 * devemos buscar alguns dados que foram informados no formulário de operação
 * em lote.
 */
<?php if (isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) { ?>

  $('rh110_datainicial').value              = parent.document.form1.rh110_datainicial.value;
  $('rh110_datafinal').value                = parent.document.form1.rh110_datafinal.value;
  $('rh110_tipoponto').value                = parent.document.form1.rh110_tipoponto.value;
  $('rh110_pagaterco').value                = parent.document.form1.rh110_pagaterco.value;
  $('rh110_anopagamento').value             = parent.document.form1.rh110_anopagamento.value;
  $('rh110_mespagamento').value             = parent.document.form1.rh110_mespagamento.value;
  $('rh110_observacao').value               = parent.document.form1.rh110_observacao.value;
  js_calculaDiasGozar();
<?php }; ?>

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

  $('rh110_pagaterco').value = ( (oRetorno.lPagaTerco == "t") ? true : false);
  $('rh110_tipoponto').value = oRetorno.sTipoPonto;
  $('iAnoFolha').value       = oRetorno.iAnoFolha;
  $('iMesFolha').value       = oRetorno.iMesFolha;
  
}

function js_buscaDadosFerias() {

  var msgDiv        = "Buscando dados das ferias do servidor\n Aguarde ...";
  js_divCarregando(msgDiv,'msgBox');
  
  oParam.iMatricula = $F("rh109_regist");
  oParam.sExec      = "buscaDadosFeriasCadastro";
  var oAjax  = new Ajax.Request(sUrlRPC,
                                {method     : "post",
                                 parameters : 'json=' + Object.toJSON(oParam),
                                 onComplete :  js_retornoDadosFerias
                                });
  
}

function js_retornoDadosFerias(oAjax) {

  js_removeObj('msgBox');

  var oRetorno = JSON.parse(oAjax.responseText);
  
  if (oRetorno.iStatus == 2){
    alert(oRetorno.sMessage.urlDecode());
    if (oRetorno.lVoltar) {
      js_voltar();
    }    
    return false;
  }  
  $('rh109_dias').value                      = oRetorno.iDiasDireito;
  <?php if (!isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) { ?>
    $('rh110_dias').value                      = oRetorno.iDiasDireito;
  <?php } ?>
  $('iDiasDireito').value                    = oRetorno.iDiasDireito;
  $('rh109_periodoaquisitivoinicial').value  = oRetorno.dPeriodoAquisitivoInicial;
  $('rh109_periodoaquisitivofinal').value    = oRetorno.dPeriodoAquisitivoFinal;

  js_verificaDireitoFerias();
  
}

function js_semDireitoFerias() {

  var lDireitoFerias   = $F("lDireitoFerias");
  var lErro            = false;
  var sMsgPerdaDireito = ""; 
  /*
   * Servidor perdeu direito a férias pelo numero de faltas
   */
  if ( $F('rh109_dias') == 0 && $F('rh109_faltasperiodoaquisitivo') > 0) {
    alert("Servidor perdeu seu direito a férias pois o numero de faltas é maior que 32 dias");
    sMsgPerdaDireito = "Perda de direito por excesso de faltas";
    lErro = true;    
  }  

  /*
   * Servidor já tirou todos os dias de férias para o período aquisitivo
   */
  if ( $F('rh109_dias') == 0 && $F('rh109_faltasperiodoaquisitivo') == 0) {
    alert("Servidor não possui dias para gozar de férias neste período aquisitivo");
    lErro = true;    
  }  

  if (lErro) {
    $("lDireitoFerias").value = "N";
    $("FieldsetPeriodoGozar").style.display = "none";
    $("FieldsetPerdaDireito").style.display = "block";

    $("sObsPerdaDireito").value = sMsgPerdaDireito; 
    return false;
  }  

  if (lDireitoFerias == "S") {
    
    $("FieldsetPeriodoGozar").style.display = "block";
    $("FieldsetPerdaDireito").style.display = "none";
    $("sObsPerdaDireito").value = "";
    
  } else {
                                        
    $("FieldsetPeriodoGozar").style.display = "none";
    $("FieldsetPerdaDireito").style.display = "block";
     
  }  
  
}

function js_alteraApuracaoMedia() {

  var ApuracaoMedia = $F('lDireitoApuracaoMedia');
  if (ApuracaoMedia == 'E') {
    $('camposperiodoinicial').style.display = 'table-row';
  } else if (ApuracaoMedia == 'N') {

    $('rh109_periodoespecificoinicial').value = '';
    $('rh109_periodoespecificofinal').value = '';
    $('camposperiodoinicial').style.display = 'none';
  }
}

function js_calculaDataFinalPeriodoAquisitivo() {

  var dPeriodoFinal      = document.getElementById('rh109_periodoaquisitivofinal');
  var dPeriodoInicial    = $F('rh109_periodoaquisitivoinicial');
  var iDias              = dPeriodoInicial.split('/')[0];
  var iMes               = dPeriodoInicial.split('/')[1];
  var iAno               = dPeriodoInicial.split('/')[2];

  if(iDias != "" && iMes != "" && iAno != ""){

    //retorna true ou false se o ano é bissesto a para total de dias 
     nsaldo    = new Number(364); //364 para fechar o calculo de ferias
     somadias  = new Number(iDias);
     somadias += new Number(nsaldo);
     
     var anoAtual = iAno;
     var anoNext  = new Number(iAno);

     
     //se ano atual for bissesto diminui  mais um dia para fechar o calculo de ferias
     if (checkleapyear(anoAtual)  ) { 
       somadias += new Number(1);
       //se data for maior que 29/02 em ano bissesto diminui mais um dia para fechar calculo
       if( iMes.value > 02 ){
         somadias -= new Number(1);
       }
     }
     
     //calcula proximo ano
     anoNext += new Number(1);
  
     //se ano posterior for bissesto e mes mair que 02 soma  mais um dia para fechar o calculo de ferias
     if(checkleapyear(anoNext) && (iMes > 2 ) ) { 
       somadias += new Number(1);
     }
   
     qualmess  = new Number(iMes);
     qualmess -= new Number(1);
   
     datafim = new Date(iAno, qualmess, somadias,1,0,0);
   
     iDiasFinal          = datafim.getDate() < 10 ? "0" + datafim.getDate() : datafim.getDate();    
     iMesFinal           = (datafim.getMonth() + 1) < 10 ? "0" + (datafim.getMonth() + 1) : (datafim.getMonth() + 1);
     iAnoFinal           = datafim.getFullYear();

     if (iDiasFinal.value != '') {
       dPeriodoFinal.value = iDiasFinal+'/'+iMesFinal+'/'+iAnoFinal; 
     }
     
   }

  js_verificaDireitoFerias();
}

function js_verificaDireitoFerias() {

  var msgDiv        = "Verificando direito a férias para o período aquisitivo informado\n Aguarde ...";
  js_divCarregando(msgDiv,'msgBox');
  
  oParam.iMatricula = $F("rh109_regist");
  oParam.dPeriodoAquisitivoInicial = $F("rh109_periodoaquisitivoinicial"); 
  oParam.dPeriodoAquisitivoFinal   = $F("rh109_periodoaquisitivofinal");
  oParam.sExec      = "verificaDireitoFerias";
  var oAjax  = new Ajax.Request(sUrlRPC,
                                     {method     : "post",
                                      parameters : 'json=' + Object.toJSON(oParam),
                                      onComplete :  js_retornoDireitoFerias
                                     });
  
}

function js_retornoDireitoFerias(oAjax) {

  js_removeObj('msgBox');
  var oRetorno = JSON.parse(oAjax.responseText);
  
  $('rh109_dias').value = oRetorno.iDiasDireito;
  <?php if (!isset($_SESSION['aListaMatriculasProcessamentoEmLote'])) { ?>
    $('rh110_dias').value = oRetorno.iDiasDireito;
  <?php } ?>
  if (oRetorno.iDiasDireito > 0) {
    $("lDireitoFerias").value = "S";
  }

  if (Number(oRetorno.iDiasDireito) < 30) {

    $('rh109_periodoaquisitivoinicial').readOnly         = true;
    $('rh109_periodoaquisitivofinal').readOnly           = true;
    
    $('rh109_periodoaquisitivoinicial').style.background = "#DEB887";
    $('rh109_periodoaquisitivofinal').style.background   = "#DEB887";

    document.getElementsByName('dtjs_rh109_periodoaquisitivoinicial')[0].disabled = true;
    document.getElementsByName('dtjs_rh109_periodoaquisitivofinal')[0].disabled   = true;
   
  }
  
  js_semDireitoFerias();     
   
  if (oRetorno.iStatus == 2){
    alert(oRetorno.sMessage.urlDecode());
    if (oRetorno.lVoltar) {
      js_voltar();
    }    
    return false;
  }
  
}

function js_calculaDataFinalPeriodoGozo() {

  if( Number($F('rh109_dias')) < Number($F('rh110_dias')) ) {
    alert("Dias a Gozar informado maior que os dias de direito a férias do servidor!");
    $('rh110_dias').value = $F('rh109_dias');
    return false;
  }

  if( $F('rh110_dias') <= 0) {
    alert("Dias a Gozar deve ser maior que zero!");
    $('rh110_dias').value = $F('rh109_dias');
    return false;    
  }    

  if ($('rh110_datainicial').value != "") {
    
    var dDataInicial = getDateInDatabaseFormat($F('rh110_datainicial'));
    var dDataFinal   = somaDataDiaMesAno(new Date (dDataInicial), new Number($F('rh110_dias')), 0, 0);
    var iDiaFinal    = dDataFinal.getDate();
    var iMesFinal    = dDataFinal.getMonth() + 1;
    if(Number(iDiaFinal) < 10) {
      iDiaFinal = '0' + iDiaFinal;
    }
    if (iMesFinal < 10) {
      iMesFinal = '0' + iMesFinal;
    }
    
    $('rh110_datafinal').value = iDiaFinal + '/' + iMesFinal + '/' + dDataFinal.getFullYear() ;
    
  }
  
}

function js_validaDataPeriodoGozo() {

  var iAnoFolha    = $F('rh110_anopagamento');
  var iMesFolha    = $F('rh110_mespagamento');   

  var dDataInicial = getDateInDatabaseFormat($F('rh110_datainicial'));
  var iAnoPeriodoInicial = dDataInicial.substr(0,4);
  var iMesPeriodoInicial = dDataInicial.substr(5,2);
  var iDiaPeriodoInicial = dDataInicial.substr(8,2);

  var dDataFinal = getDateInDatabaseFormat($F('rh110_datafinal'));
  var iAnoPeriodoFinal = dDataFinal.substr(0,4);
  var iMesPeriodoFinal = dDataFinal.substr(5,2);
  var iDiaPeriodoFinal = dDataFinal.substr(8,2);
  
  if (iAnoPeriodoInicial < iAnoFolha || iMesPeriodoInicial < iMesFolha) {
    alert('A data para gozo deve ficar entre o primeiro dia do mês de competência e até 180 dias após o fim do período de competência');   
    $('rh110_datainicial').value ='';
    $('rh110_datafinal').value ='';
    return false;
  }

  /*
   * @todo implementar lógica para quando a data do periodo de gozo final for maior que 180 do mes da folha 
   */

  //Verificamos se o período informado está dentro de outro período 
  if (dDataInicial != "" && dDataFinal != "") {

    oParam.sExec               = "verificaPeriodoGozo";
    oParam.iMatricula          = $F('rh109_regist');
    oParam.dDataPeriodoInicial = $F('rh110_datainicial');
    oParam.dDataPeriodoFinal   = $F('rh110_datafinal');
     
    var oAjax  = new Ajax.Request(sUrlRPC,
                                  {method: "post",
                                   parameters:'json='+Object.toJSON(oParam),
                                   onComplete: js_retornoValidaDataPeriodoGozo  
                                  });
    
  }  
  
}

function js_retornoValidaDataPeriodoGozo(oAjax) {

 var oRetorno = JSON.parse(oAjax.responseText);
 if ( oRetorno.iStatus == "2") {
   
   alert(oRetorno.sMessage.urlDecode());

   $('rh110_datainicial').value = '';
   $('rh110_datafinal').value   = '';
   
 }                                           
 
}

function js_verificaDiasGozo() {
  
  if (Number($F('rh109_dias')) < ( Number($F('rh110_dias'))+Number($F('rh110_diasabono')))) {
    alert('Quantidade de Dias a Gozar do período não poder ser maior que os dias de direito a férias do servidor!');
    $('rh110_diasabono').value = '';
    $('rh110_dias').value      = $F('rh109_dias');
    return false;
  }  

  return true;
}

function js_calculaSaldoDiasDireito() {

  iFaltas       = $F('rh109_faltasperiodoaquisitivo');
  iDiasDesconto = 0;
  if ( iFaltas > 5 && iFaltas <= 14) {
    iDiasDesconto = 6;
  }

  if (iFaltas > 14 && iFaltas <= 23) {
    iDiasDesconto = 12;
  }

  if (iFaltas > 23 && iFaltas <= 32) {
    iDiasDesconto = 18;
  }

  if (iFaltas > 32) {
    iDiasDesconto = 30;
  }

  var iDiasDireito = $F('iDiasDireito') - iDiasDesconto;
  $('rh109_dias').value = iDiasDireito < 0 ? 0 : iDiasDireito;
  $('rh110_dias').value = iDiasDireito < 0 ? 0 : iDiasDireito;

  if($F('iDiasDireito') - iDiasDesconto <= 0) {
    $("lDireitoFerias").value = "N";
    js_semDireitoFerias();
  } else {
    $("lDireitoFerias").value = "S";
    js_semDireitoFerias();    
  }    
    
   
}

function js_cadastrarFerias() {

  var iMatricula                = $('rh109_regist');
  var dPeriodoAquisitivoInicial = $('rh109_periodoaquisitivoinicial');
  var dPeriodoAquisitivoFinal   = $('rh109_periodoaquisitivofinal');
  var dPeriodoEspecificoInicial = $('rh109_periodoespecificoinicial');
  var dPeriodoEspecificoFinal   = $('rh109_periodoespecificofinal');  
  var iDiasDireito              = $('rh109_dias');
  var iFaltasPeriodo            = $('rh109_faltasperiodoaquisitivo'); 
  var lDireitoFerias            = $('lDireitoFerias');

  var iAnoCalculo               = $('iAnoFolha');
  var iMesCalculo               = $('iMesFolha');
  
  /**
   * variaveis periodo das ferias
   */  
  var iDiasGozar                = $("rh110_dias")        ;
  var dDataInicial              = $("rh110_datainicial") ;
  var dDataFinal                = $("rh110_datafinal")   ;
  var iAnoPagamento             = $("rh110_anopagamento"); 
  var iMesPagamento             = $("rh110_mespagamento"); 
  var iDiasAbono                = $("rh110_diasabono")   ;
  var lTerco                    = $('rh110_pagaterco');
  var sTipoPonto                = $('rh110_tipoponto');
  var sObservacao               = $("rh110_observacao")  ; 

  /**            
   * Data do periodo aquisitivo inicial
   */            
  if (dPeriodoAquisitivoInicial.value == "") {

    dPeriodoInicial.focus();
    alert("Período aquisitivo inicial não definido");
    return false;
  }

  /**
   * Data do periodo aquisitivo final
   */
  if (dPeriodoAquisitivoFinal.value == "") {

    dPeriodoFinal.focus();
    alert("Período aquisitivo final não definido");
    return false; 
  }   
  
  /**
   * Dias de direito a gozo
   */
  if (iDiasDireito.value == '') {

    iDiasDireito.focus();
    alert("Dias de direito a férias não definido.");
    return false;
  }

  /**
   * Tipo de ponto 
   * Salario ou complementar
   */  
  if (sTipoPonto.value == '') {

    sTipoPonto.value();
    alert("Tipo de ponto de calculo não definido.");
    return false;
  }

  if (lDireitoFerias.value == 'S') {
    
    /**
     * Dias que o servidor ira gozar
     */         
    if (iDiasGozar.value == '') {
  
      iDiasGozar.focus();
      alert("Total de dias a gozar não definido.");
      return false;
    }

    /**
     *
     */
    if (iDiasGozar.value > iDiasDireito.value) {
      
      iDiasGozar.focus();
      alert("Total de dias a gozar informado maior que os dias de direito.");
      return false;
    }  
  
    /**
     * Data inicial do gozo
     */
    if (dDataInicial.value == '') {
  
      dDataInicial.focus();
      alert("Data inicial do período a gozar, não definido.");
      return false;
    }

    var aDataInicial        = dDataInicial.value.split('/');
    var aDataFinal          = dDataFinal.value.split('/');
    
    var iPeriodoGozoInicial = new Number( aDataInicial[2] + aDataInicial[1] );
    var iPeriodoGozoFinal   = new Number( aDataFinal[2]   + aDataFinal[1] );
    var iPagamentoValidacao = new Number( iAnoPagamento.value +  iMesPagamento.value); 

    var iDataGozoInicial    = new Number(aDataInicial[2] + aDataInicial[1] + aDataInicial[0]);
    var iDataGozoFinal      = new Number(aDataFinal[2] + aDataFinal[1] + aDataFinal[0]);

    /**
     * Verifica se periodo do gozo é inferior que o do pagamento
     */
    if (iPeriodoGozoInicial < iPagamentoValidacao) {

      dDataFinal.focus();
      alert('O periodo do gozo não pode ser infeior ao do pagamento');
      return false;
    }

    /**
     * Verifica se a data inicial é maior que a final
     */
    if ( iDataGozoInicial > iDataGozoFinal ) {

      dDataInicial.focus();
      alert('A data final do gozo não pode ser inferior a data final');
      return false;
    }  

    /**
     * Verifica se os dias de abono é maior que 1/3 dos dias de direito
     */
    if (iDiasAbono.value > ( iDiasDireito.value / 3 ) ) {

      iDiasAbono.focus();
      alert('Os dias de abono não podem ser maiores que 1/3 dos dias de direito.');
      return false;
    }
    
    /**
     * Data final do gozo
     */    
    if (dDataFinal.value == '') {
  
      dDataFinal.focus();
      alert("Data final do período a gozar, não definido.");
      return false;
    }
  
    /**
     * Ano de pagamento das férias
     */   
    if (iAnoPagamento.value == '') {
  
      iAnoPagamento.focus();
      alert("Ano de pagamento não definido.");
      return false;
    }

    /**
     * Verifica se o periodo de pagamento é menor que o da folha, 
     */
    if (iAnoPagamento.value < iAnoCalculo.value ) {
      
      alert('O ano de pagamento não pode ser inferior ao de processamento da folha');
      return false;
    }
  
    /**
     * Mes de pagamento das férias
     */  
    if (iMesPagamento.value == '') {
  
      iMesPagamento.focus();
      alert("Mês de pagamento não definido.");
      return false;
    }

    if (iMesPagamento.value < iMesCalculo.value) {
    
      alert('O mês de pagamento não pode ser inferior ao de processamento da folha');
      return false;
    }

  }
  

  var msgDiv                        = "Processando Dados. \n Aguarde ...";
  oParam.sExec                      = 'salvarFerias';   
  oParam.iMatricula                 = iMatricula.value;     
  oParam.dPeriodoAquisitivoInicial  = dPeriodoAquisitivoInicial.value;
  oParam.dPeriodoAquisitivoFinal    = dPeriodoAquisitivoFinal.value;
  oParam.dPeriodoEspecificoInicial  = dPeriodoEspecificoInicial.value;
  oParam.dPeriodoEspecificoFinal    = dPeriodoEspecificoFinal.value;  
  oParam.iDiasDireito               = iDiasDireito.value;
  oParam.iFaltasPeriodo             = iFaltasPeriodo.value;
  oParam.lDireitoFerias             = lDireitoFerias.value;

  oParam.dadosPeriodo               = new Object();
  if (lDireitoFerias.value == 'N') {

    oParam.dadosPeriodo.iDiasGozar    = "0";
    oParam.dadosPeriodo.dDataInicial  = null;
    oParam.dadosPeriodo.dDataFinal    = null;
    oParam.dadosPeriodo.iAnoPagamento = iAnoCalculo.value;
    oParam.dadosPeriodo.iMesPagamento = iMesCalculo.value;
    oParam.dadosPeriodo.iDiasAbono    = "0";
    oParam.dadosPeriodo.lTerco        = "false";
    oParam.dadosPeriodo.sTipoPonto    = "";  
    if ($("sObsPerdaDireito").value == "") {
      oParam.dadosPeriodo.sObservacao   = "Perda de Direito" ; encodeURIComponent(tagString(sObservacao.value));
    } else {
      oParam.dadosPeriodo.sObservacao   = encodeURIComponent(tagString($("sObsPerdaDireito").value));
    }    
      
  } else {

    oParam.dadosPeriodo.iDiasGozar    = iDiasGozar.value;
    oParam.dadosPeriodo.dDataInicial  = dDataInicial.value;
    oParam.dadosPeriodo.dDataFinal    = dDataFinal.value;
    oParam.dadosPeriodo.iAnoPagamento = iAnoPagamento.value;
    oParam.dadosPeriodo.iMesPagamento = iMesPagamento.value;
    oParam.dadosPeriodo.iDiasAbono    = iDiasAbono.value;
    oParam.dadosPeriodo.lTerco        = lTerco.value;
    oParam.dadosPeriodo.sTipoPonto    = sTipoPonto.value;  
    oParam.dadosPeriodo.sObservacao   = encodeURIComponent(tagString(sObservacao.value));    

  }      

  js_divCarregando(msgDiv,'msgBox');  
   
  var oAjax  = new Ajax.Request(sUrlRPC,
                                     {method: "post",
                                      parameters:'json='+Object.toJSON(oParam),
                                      onComplete: js_retornoSalvarFerias
                                     });   
}

function js_retornoSalvarFerias(oAjax) {
  
  js_removeObj('msgBox');
  var oRetorno = JSON.parse(oAjax.responseText);

  alert(oRetorno.sMessage.urlDecode());

  if (oRetorno.iStatus == "1" && oRetorno.lVoltar) {
    js_voltar();
  }
}

function js_voltar() {
  location.href = 'pes4_cadastroferias001.php';
}

function js_proximaMatriculaLote() {
	window.location = 'pes4_cadastroferias001.php';
}

function js_cancelarCadastroEmLote() {

	var sUrlLimpaSessao = 'pes4_cadastroferias.RPC.php';
	var oParam = new Object();
	    oParam.sExec = 'limparSessaoCadastroEmLote';
	var oAjax = new Ajax.Request(sUrlLimpaSessao,
			                         {method : 'post',
                                parameters : 'json=' + Object.toJSON(oParam),
                                onComplete : js_retornoCancelarCadastroEmLote});
}

function js_retornoCancelarCadastroEmLote(oAjax) {

	var oRetorno = JSON.parse(oAjax.responseText);
	if (oRetorno.iStatus == 1) {
		parent.js_escondeIframeCadastroEmLote();
	} else if (oRetorno.iStatus == '2') {

		alert(oRetorno.sMessage.urlDecode());
		return false;
	}
}

function js_preenchepesquisa(chave) {

  db_iframe_rhferias.hide();
  <?php
  if ($db_opcao != 1) {
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}

function js_calculaDiasGozar() {

	var dDataInicial = getDateInDatabaseFormat($F('rh110_datainicial'));
	var iAnoPeriodoInicial = dDataInicial.substr(0,4);
	var iMesPeriodoInicial = dDataInicial.substr(5,2);
	var iDiaPeriodoInicial = dDataInicial.substr(8,2);

	var dDataFinal = getDateInDatabaseFormat($F('rh110_datafinal'));
	var iAnoPeriodoFinal = dDataFinal.substr(0,4);
	var iMesPeriodoFinal = dDataFinal.substr(5,2);
	var iDiaPeriodoFinal = dDataFinal.substr(8,2);

	var iDataInicial = iAnoPeriodoInicial+""+iMesPeriodoInicial+""+iDiaPeriodoInicial;
	var iDataFinal   = iAnoPeriodoFinal+""+iMesPeriodoFinal+""+iDiaPeriodoFinal;

	$('rh110_dias').value = (iDataFinal - iDataInicial) + 1;
}
</script>