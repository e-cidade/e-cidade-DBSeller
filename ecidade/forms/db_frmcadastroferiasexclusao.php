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

$clrhferias->rotulo->label();
$clrhferiasperiodo->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
?>
<body bgcolor="#cccccc" style="margin-top:30px;" onLoad="js_verificaExistenciaPeriodo();" border=0>
<center>
<form name="form1" method="post" action="">

  <input type="hidden" name="rh109_sequencial" id="rh109_sequencial" value="">
  <input type="hidden" name="aCodigoPeriodos"  id="aCodigoPeriodos"  value="">
  
  <fieldset style="width:650px;">
    
    <legend><strong>Exclusão de Cadastro de Férias</strong></legend>
    
    <fieldset>
      <legend><strong>Dados das férias</strong></legend>
      
      <table style="width:650px;" border=0>
  
        <tr>
          <td style="width:210px;" nowrap title="<?=@$Trh109_regist?>">
             <?=@$Lrh109_regist?>
          </td>
          <td> 
            <?php db_input('rh109_regist', 10,$Irh109_regist,true, 'text',3)?>
          </td>
          <td colspan="2">  
            <?php db_input('z01_nome'    , 45,$Iz01_nome    ,true, 'text',3);  ?>
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
                           3);
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
                           true, 
                           'text', 
                           3);
            ?>
          </td>          
        </tr>
  
        <tr>
          <td nowrap title="<?=@$Trh109_periodoespecificoinicial?>">
             <?=@$Lrh109_periodoespecificoinicial?>
          </td>
          <td> 
            <?php
              db_inputdata('rh109_periodoespecificoinicial', 
                           @$rh109_periodoespecificoinicial_dia, 
                           @$rh109_periodoespecificoinicial_mes, 
                           @$rh109_periodoespecificoinicial_ano, 
                           true, 
                           'text', 
                           3);
            ?>
          </td>
          <td nowrap title="<?=@$Trh109_periodoespecificofinal?>">
             <?=@$Lrh109_periodoespecificofinal?> </td>
          <td> 
            <?php
              db_inputdata('rh109_periodoespecificofinal', 
                           @$rh109_periodoespecificofinal_dia, 
                           @$rh109_periodoespecificofinal_mes, 
                           @$rh109_periodoespecificofinal_ano, 
                           true, 
                           'text',
                           3);
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Trh109_dias?>">
             <?=@$Lrh109_dias?>
          </td>
          <td> 
            <?php db_input('rh109_dias', 10, $Irh109_dias, true, 'text', 3); ?>
          </td>
          <td nowrap title="<?=@$Trh109_faltasperiodoaquisitivo?>">
             <?=@$Lrh109_faltasperiodoaquisitivo?>
          </td>
          <td> 
            <?php
              db_input('rh109_faltasperiodoaquisitivo', 10, $Irh109_faltasperiodoaquisitivo, true, 'text', 3);
            ?>
          </td>          
        </tr>
      </table>
    </fieldset>
  
    <fieldset>
      <legend><strong>Dados do periodo a gozar</strong></legend>    
      <table style="border:0;width:650px;">
      
         <tr>
           <td>
             <div id="gridPeriodo"></div>
           </td>  
         </tr>     
              
      </table>
    </fieldset>
    
  </fieldset>  
  
  <br />
  
  <input type="button" name="excluir"   onclick="js_excluirFerias();" value="Excluir" />  
  <input type="button" name="pesquisar" onclick="js_pesquisar();"     value="Pesquisar" />
  <input name="voltar" type="button" id="voltar" value="Voltar" onclick="js_voltar();" >

</form>
</center>
</body>

<script>
var sUrlRPC = 'pes4_cadastroferias.RPC.php';  
var oParam  = new Object();

js_verificaExistenciaPeriodo();

function js_verificaExistenciaPeriodo() {

  document.form1.excluir.disabled = true;
  
  var iMatricula         = $F('rh109_regist');
	var msgDiv             = "Pesquisando férias cadastradas. \n Aguarde ...";  
                        
	oParam.sExec           = 'buscaDadosFeriasExclusao';
  oParam.iMatricula      = iMatricula;
  oParam.lExclusao       = true;

  js_divCarregando(msgDiv,'msgBox');

  var oAjaxLista  = new Ajax.Request(sUrlRPC,
          {method     :  "post",
           parameters : 'json=' + Object.toJSON(oParam),
           onComplete :  js_retornoVerificaFeriasCadastradas
          });  
}

function js_retornoVerificaFeriasCadastradas(oAjax) {
	
	  js_removeObj('msgBox');
	  var aRetorno = JSON.parse(oAjax.responseText);

	  if ( aRetorno.iStatus == 2 ){
		  
	     alert(aRetorno.sMessage.urlDecode());
	     js_voltar();
	     return false;
	  }

	  document.form1.excluir.disabled = false;
	  
	  $('rh109_sequencial').value               = aRetorno.oDadosFerias.iCodigoFerias;
	  $('rh109_periodoaquisitivoinicial').value = aRetorno.oDadosFerias.dPeriodoAquisitivoInicial;
	  $('rh109_periodoaquisitivofinal').value   = aRetorno.oDadosFerias.dPeriodoAquisitivoFinal;
	  $('rh109_periodoespecificoinicial').value = aRetorno.oDadosFerias.dPeriodoEspecificoInicial;
	  $('rh109_periodoespecificofinal').value   = aRetorno.oDadosFerias.dPeriodoEspecificoFinal;	  
	  $('rh109_dias').value                     = aRetorno.oDadosFerias.nDias;
	  $('rh109_faltasperiodoaquisitivo').value  = aRetorno.oDadosFerias.nFaltasPeriodoAquisitivo;
		 
	  js_montaGridFeriasPeriodo();

	  oDBGridFeriasPeriodo.clearAll(true);
	  
	  var aLinha = new Array();
	  var sSep   = "";
	  
	  for(iInd = 0; iInd < aRetorno.oDadosFerias.aPeriodosGozo.length; iInd++){
		  
	    with ( aRetorno.oDadosFerias.aPeriodosGozo[iInd] ){

          if (aRetorno.oDadosFerias.aPeriodosGozo[iInd].lPagaTerco == 't') {
            sPagaTerco = 'Sim';
          } else { 
            sPagaTerco = 'Não';
          }

          if (aRetorno.oDadosFerias.aPeriodosGozo[iInd].iTipoPonto == 1) {
            sTipoPonto = 'Salário';
          } else { 
            sTipoPonto = 'Complementar';
          } 

			    aLinha[0] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].iDiasGozo;
			    aLinha[1] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].iDiasAbono;
			    aLinha[2] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].dPeriodoInicial;
			    aLinha[3] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].dPeriodoFinal;
			    aLinha[4] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].iMesPagamento;
			    aLinha[5] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].iAnoPagamento;
			    aLinha[6] = sPagaTerco;
			    aLinha[7] = sTipoPonto;			    
			    aLinha[8] = aRetorno.oDadosFerias.aPeriodosGozo[iInd].sObservacao.urlDecode();
			    

			    $('aCodigoPeriodos').value += sSep+aRetorno.oDadosFerias.aPeriodosGozo[iInd].iCodigoPeriodo;
			    sSep = ",";
			    
			    oDBGridFeriasPeriodo.addRow(aLinha);
	    }
	  }

	  oDBGridFeriasPeriodo.renderRows();

		for(var iInd = 0; iInd < aRetorno.oDadosFerias.aPeriodosGozo.length; iInd++){
		  
	    with ( aRetorno.oDadosFerias.aPeriodosGozo[iInd] ){

	      var oCelulaObservacao = $(oDBGridFeriasPeriodo.aRows[iInd].aCells[8].sId);

	      var oDBHint 					= eval("oDBHint_"+iInd+"_8 = new DBHint('oDBHint_"+iInd+"_8')");

	      oDBHint.setWidth		 (350);
        oDBHint.setText			 (aRetorno.oDadosFerias.aPeriodosGozo[iInd].sObservacao.urlDecode());
        oDBHint.setShowEvents(["onmouseover"]);
        oDBHint.setHideEvents(["onmouseout"]);
        oDBHint.setPosition	 ('B', 'L');
        oDBHint.make				 (oCelulaObservacao);
	
	    }
	    
		}

}

function js_montaGridFeriasPeriodo() {

  oDBGridFeriasPeriodo              = new DBGrid('feriasPeriodo');
	oDBGridFeriasPeriodo.nameInstance = 'oDBGridFeriasPeriodo';
  
  aHeader     = new Array();
	aHeader[0]  = 'Qtd. Dias';
	aHeader[1]  = 'Dias Abono';
	aHeader[2]  = 'Dt. Inicial';
	aHeader[3]  = 'Dt. Final';
	aHeader[4]  = 'Mes';
	aHeader[5]  = 'Ano';
	aHeader[6]  = 'Paga 1/3';
	aHeader[7]  = 'Ponto';	
	aHeader[8]  = 'Obs.';

	oDBGridFeriasPeriodo.setHeader(aHeader);
	oDBGridFeriasPeriodo.setHeight(250);

	var aAligns = new Array();
	aAligns[0]  = 'center';
	aAligns[1]  = 'center';
	aAligns[2]  = 'center';
	aAligns[3]  = 'center';
	aAligns[4]  = 'center';
	aAligns[5]  = 'center';
	aAligns[6]  = 'center';
	aAligns[7]  = 'center';	
	aAligns[8]  = 'left';

	oDBGridFeriasPeriodo.setCellAlign(aAligns);
	oDBGridFeriasPeriodo.show($('gridPeriodo'));  
  
}


////////////////  fim verificação existencia de ferias na competencia da folha
function js_excluirFerias() {

  var iMatricula       = $F('rh109_regist');
  var oParametros      = new Object();
  var msgDiv           = "Processando Dados. \n Aguarde ...";

  oParametros.iCodigoFerias   = $F('rh109_sequencial');
  oParametros.aCodigoPeriodos = $('aCodigoPeriodos').value.split(",");
  oParametros.sExec           = 'excluirFerias';  
  
  js_divCarregando(msgDiv,'msgBox');
   
   var oAjaxLista  = new Ajax.Request(sUrlRPC,
                                      {method: "post",
                                       parameters:'json='+Object.toJSON(oParametros),
                                       onComplete: js_retornoExcluirFerias
                                      });   
}

function js_retornoExcluirFerias(oAjax) {
  
  js_removeObj('msgBox');
  var oRetorno = JSON.parse(oAjax.responseText);
  alert(oRetorno.sMessage.urlDecode());
  js_voltar();
  
}

function js_voltar() {

  location.href = 'pes4_cadastroferias003.php';
}

function js_preenchepesquisa(chave) {

  db_iframe_rhferias.hide();
  
  <?php
  if ($db_opcao != 1) {
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>