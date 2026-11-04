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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_termoanuproc_classe.php"));
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link rel="stylesheet" type="text/css" href="estilos.css" >
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
  <style>
  .table_header2  {
         font-weight:bold;text-align:left;
         padding:1px;
         border-bottom:1px outset black;
         border-right:1px outset black;           
         background-color:#EEEFF2;    
         cursor: default;    
  }
  .linhagrid2{
              border-right:1px inset black;
              border-bottom:1px inset black;
              cursor:default;
              font-family: Arial, Helvetica, sans-serif;
              font-size: 12px;
              text-align:right;
              width: 80px
   }
   .final {
     border-bottom:1px solid #000000;

   }
   .normal {
     background-color: #FFFFFF;
   }
   .teste {
     background-color: #FFFF66;
   }
   .pendente {
    /*ackground-color: #CF3232;*/
     background-color: #FF4649;
   }
   .liberada {
/*     background-color: #D1F07C; 
     background-color:  #AECF00 */
     /*background-color: #71d671*/
      background-color: #8ae58a;
   }

  </style>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/tablenavigation.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbtextFieldData.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbautocomplete.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table width="790" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<form name="form1" method="post" action="">
<center>
<br>
<table  border =0 style='width:90%' >
  <tr>
    <td align='left'>
      <input name='txtFiltros' id='txtFiltros' type='button' value='Filtros >' onClick='js_MostraFiltros();'>
      <br><br>
      <table width="100%">
        <tr>
          <td rowspan="1" valign="top" height="100%">
            <fieldset><legend><b>Tarefas</legend>
              <div id='grid_tarefas' style="width: 100%;-moz-user-select:none">
              </div>
            </fieldset>
          </td>
          <td id='contdata'>          
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</center>
</form>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
/*
$('btnFechar').observe("click",js_fecharJanela);
$('btnIncluir').observe("click",js_ProcessaInclusao);
*/

var sUrlRPC = 'ate4_tarefas.RPC.php';
      
function js_filtroByTipo(){

  var aObjs = new Array($('chkConcluidas'), $('chkTeste'), $('chkExecucao'));
  
  aObjs.each( function (oObj) {

    aCollectionLinhas = document.getElementsByTagName('tr');

	  for (var i=0; i < aCollectionLinhas.length; i++){
	
	    if (aCollectionLinhas[i].className == oObj.value) {
	      if (oObj.checked){
	        aCollectionLinhas[i].style.display = '';	        
	      }else{
	        aCollectionLinhas[i].style.display = 'none';	            
	      }
	    }     
	  }
  });
  
  for(var i=0; i < aCollectionLinhas.length; i++) {
    if(aCollectionLinhas[i].className == "normal") {
      aCollectionLinhas[i].style.display = '';
    }
  }
}

function js_filtroByDate() {

  var cont = 1;
  var row  = 0;
  var cell = $$('input[type=radio].data_filtro:checked')[0].value; 
  
  while ( cont != 0 ) {
  
    var oRow  = document.getElementById("grid_tarefasrowgrid_tarefas"+row);
    var oCell = document.getElementById("grid_tarefasrow"+row+"cell"+cell+"");  
    
    if (oCell == null) {
      cont = 0;
      continue;
    }
    
    if(oRow.style.display != 'none') {    
      var sCellContent = oCell.innerHTML;
      
      var dt_filtro = new Date(sCellContent.substr(6,4), sCellContent.substr(3,2)-1, sCellContent.substr(0,2));    
      var dt_ini    = new Date($F('dt_inicial').substr(6,4), $F('dt_inicial').substr(3,2)-1, $F('dt_inicial').substr(0,2)); 
      var dt_fim    = new Date($F('dt_final').substr(6,4), $F('dt_final').substr(3,2)-1, $F('dt_final').substr(0,2));
      
      if ($F('dt_inicial') && $F('dt_final')) {
        
        if ( !(dt_filtro >= dt_ini && dt_filtro <= dt_fim) ) {
          oRow.style.display = 'none';
        } else {
          oRow.style.display = '';          
        }
      
      } else if ($F('dt_inicial')) {
        
        if ( !(dt_filtro >= dt_ini) ) {
          oRow.style.display = 'none';
        } else {
          oRow.style.display = '';
        }
        
      } else if ($F('dt_final')) {
        
        if ( !(dt_filtro <= dt_fim) ) {
          oRow.style.display = 'none';
        } else {
          oRow.style.display = '';
        }
      } else {
        oRow.style.display = '';
      }          
    }       
    
    row++;
  }
}

function js_filtroByResponsavel() {
  
  if ( $F('responsavel') != "" ) {
    var cont = 1;
    var row  = 0;
    
    while ( cont != 0 ) {
    
      var oRow  = document.getElementById("grid_tarefasrowgrid_tarefas"+row);
      var oCell = document.getElementById("grid_tarefasrow"+row+"cell3");  
      
      if (oCell == null) {
        cont = 0;
        continue;
      }
      
      if(oRow.style.display != 'none') {    
        
        var compResponsavel = new String(oCell.innerHTML);    
        var sResponsavel    = new String($F('responsavel'));
        
        if (sResponsavel.valueOf() == compResponsavel.valueOf()) {
          oRow.style.display = '';
        } else {
          oRow.style.display = 'none';
        }
      
      }       
      
      row++;
    }
  }
}  

function js_countRowsGrid() {
  
  var cont = 1;
  var row  = 0;
  var count = 0;
    
  while ( cont != 0 ) {
    
    var oRow  = document.getElementById("grid_tarefasrowgrid_tarefas"+row);  
    var oCell = document.getElementById("grid_tarefasrow"+row+"cell2");      
     
    if (oCell == null) {
      cont = 0;
      continue;
    }
      
    if(oRow.style.display != 'none') {    
      count++;      
    }       
      
    row++;
  }
  
  $('grid_tarefasnumrows').innerHTML = count;
  
}  

function js_filtro() {
  js_divCarregando("Aplicando filtros...","msgBox");
  js_filtroByTipo();
  js_filtroByDate();
  js_filtroByResponsavel();  
  js_countRowsGrid();
  js_removeObj("msgBox");
}

function js_clearFiltro() {
  $('dt_inicial').value       = "";
  $('dt_final').value         = "";
  $('responsavel').value      = "";    
  $('inicioprevisto').checked = true;
  $('chkConcluidas').checked  = true;
  $('chkTeste').checked       = true;
  $('chkExecucao').checked    = true;
  
  js_filtro();
}


var db_textfield_date_ini = new DBTextFieldData("dt_inicial" ,"db_textfield_date_ini", "", "");
var input_dt_ini = db_textfield_date_ini.sStringConteudo;

var db_textfield_date_fim = new DBTextFieldData("dt_final","db_textfield_date_fim", "", "");
var input_dt_fim = db_textfield_date_fim.sStringConteudo;

var sContent  = "<br>";    
    sContent += "<fieldset style='width:90% align:center'><legend><b>Mostrar Tarefas :<b></legend> ";
    sContent += " <table>        ";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap><input type='checkbox' id='chkConcluidas' value='liberada' onClick='js_filtro()' checked><label for='chkConcluidas'>Concluídas</label></td>  ";
    sContent += "     <td nowrap><input type='checkbox' id='chkTeste'      value='teste'    onClick='js_filtro()' checked><label for='chkTeste'>Teste</label></td>  ";
    sContent += "     <td nowrap><input type='checkbox' id='chkExecucao'   value='pendente' onClick='js_filtro()' checked><label for='chkExecucao'>Execução</label></td> ";
    sContent += " </table>     ";
    sContent += " </fieldset>  ";
    sContent += "<br>";    
    sContent += "<fieldset style='width:90% align:center'><legend><b>Periodo :<b></legend> ";
    sContent += " <table>        ";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap>Pesquisar por: </td>";
    sContent += "     <td nowrap>";
    sContent += "       <input type=radio name='data_filtro' class='data_filtro' id='inicioprevisto' value=4 checked> ";
    sContent += "       <label for='inicioprevisto'>Início Previsto</label>";
    sContent += "     </td>";
    sContent += "   </tr>      ";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap></td>";
    sContent += "     <td nowrap>";
    sContent += "       <input type=radio name='data_filtro' class='data_filtro' id='finalprevisto' value=5> ";
    sContent += "       <label for='finalprevisto'>Final Previsto</label>"; 
    sContent += "     </td>";
    sContent += "   </tr>      ";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap>Periodo: </td>  ";
    sContent += "     <td nowrap align=left> "+input_dt_ini+" a "+input_dt_fim+" </td>  ";
    sContent += "   </tr>      ";
    sContent += " </table>     ";
    sContent += " </fieldset>  ";
    sContent += "<br>";    
    sContent += "<fieldset style='width:90% align:center'><legend><b>Responsável :<b></legend> ";
    sContent += " <table>        ";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap>Responsável: </td>";
    sContent += "     <td nowrap>";
    sContent += "       <select name='responsavel' id='responsavel' onchange='js_filtro()'>";
    sContent += "         <option value='' selected>Selecione...</option>";
    sContent += "       </select>";        
    sContent += "     </td>";
    sContent += "   </tr>      ";
    sContent += " </table>     ";
    sContent += " </fieldset>  ";    
    sContent += " <table align=center>";
    sContent += "   <tr>         ";
    sContent += "     <td nowrap align=center colspan=3> ";
    sContent += "       <input type='button' name='filtroDate' id='filtroDate' onclick='js_filtro();' value='Filtrar'>";
    sContent += "       <input type='button' name='clearfiltroDate' id='clearfiltroDate' onclick='js_clearFiltro();' value='Limpar'>";
    sContent += "     </td> ";
    sContent += "   </tr>      ";
    sContent += " </table>     ";    
        
windowFiltros = new windowAux('filtros', 'Filtros', 410, 380);
windowFiltros.setContent(sContent);

$('dt_inicial').observe('blur', js_filtro);
$('dt_final').observe('blur', js_filtro);

oMessage   = new DBMessageBoard("msgboard1", 
                                "Filtros", 
                                " - Os filtros são cumulativos.",
                                $("windowfiltros_content"));
oMessage.show();


function js_MostraFiltros(){
  windowFiltros.show(60,300);
}

function js_criaJanela(oAjax){

	var obj = JSON.parse(oAjax.responseText);
	if (obj.status && obj.status == 2){
	   js_removeObj("msgBox");
	   alert(obj.sMensagem.urlDecode());
	   return false ;
	}

	with (obj.oDetalheTarefa){

    var sPrevisto  = js_formatar(inicio_previsto,'d')+" - "+hora_inicio_previsto.urlDecode()+" até "+js_formatar(final_previsto,'d')+" - "+hora_final_previsto.urlDecode();
    var sExecutado = js_formatar(inicio_previsto,'d')+" - "+hora_inicio_previsto.urlDecode()+" até "+js_formatar(inicio_teste,'d')+" - "+horaini_teste.urlDecode();
    var sContent  = "<table align='center' width='100%' style='border:1px solid #000000; border-collapse:collapse;font-family:Arial,Helvetica, sans-serif;font-size:8px; padding:5px;background-color:white;vertical-align:bottom'> ";
        sContent += "  <tr>   ";
        sContent += "    <td><b>Tarefa : </b></td> ";
        sContent += "    <td>"+tarefa+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td><b>Responsavel : </b></td> ";
        sContent += "    <td>"+responsavel.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td><b>Previsto :</b></td> ";
        sContent += "    <td>"+sPrevisto+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td class='"+getClassName(cod_situacao)+"'><b>Executado :</b></td> ";
        sContent += "    <td class='"+getClassName(cod_situacao)+"'>"+sExecutado+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr> ";
        sContent += "    <td nowrap valign='top'><b>Descrição :</b></td> ";
        sContent += "    <td>"+descricao_completa.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr class='final'>   ";
        sContent += "    <td nowrap valign='top'><b>Observações :</b></td> ";
        sContent += "    <td>"+tarefa_obs.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "</table>  ";
  }

  sContent += "<table align='center' width='100%' style='border:1px solid #000000; border-collapse:collapse;font-family:Arial,Helvetica, sans-serif;font-size:8px; padding:5px;background-color:white;vertical-align:bottom'> ";
  sContent += "  <tr> ";
  sContent += "    <td class='table_header2' width='50%'><b>Descrição</b></td> ";
  sContent += "    <td class='table_header2'><b>Esforço</b></td> ";
  sContent += "  </tr>  ";

	for (var iInd = 0; iInd < obj.aSituacaoTarefas.length; iInd++) {
	  with (obj.aSituacaoTarefas[iInd]){

      sContent += "  <tr> ";
      sContent += "    <td class='linhagrid'>"+at46_descr.urlDecode()+"</td> ";
      sContent += "    <td class='linhagrid'>"+esforco.urlDecode()+"</td> ";
      sContent += "  </tr>  ";

    }
  }

  sContent += "</table> ";

  windowDetalhesTarefas = new windowAux('detalhetarefa', 'Detalhamento da Tarefa : '+obj.oDetalheTarefa.tarefa, 700, 500);
  windowDetalhesTarefas.setContent(sContent);
  $("window"+windowDetalhesTarefas.idWindow+"_btnclose").observe("click", function(){
    windowDetalhesTarefas.destroy();
  });
  document.observe("keydown", function(event){ 
    if (event.which == 27) {
     windowDetalhesTarefas.destroy();
    }      
  });  
	
  js_removeObj("msgBox");
  windowDetalhesTarefas.show(60,300);

}


function js_LancarPrevisao(iCodTarefa){

  js_divCarregando("Aguarde, buscando previsões lançadas Tarefa["+iCodTarefa+"]","msgBox");
 
  strJson = '{"exec":"getPrevisaoTarefa","iCodTarefa":"'+iCodTarefa+'"}';
  var oAjax   = new Ajax.Request( sUrlRPC, {
                                             method: 'post', 
                                             parameters: 'json='+strJson, 
                                             onComplete: js_janelaPrevisao
                                           }
                                );

  
}

function js_janelaPrevisao(oAjax) {

	var obj = JSON.parse(oAjax.responseText);
	if (obj.status && obj.status == 2){
	   js_removeObj("msgBox");
	   alert(obj.sMensagem.urlDecode());
	   return false ;
	}

  var sContent  = " <table width='100%'>  ";
      sContent += "   <tr>                ";
      sContent += "     <td rowspan='1' valign='top' height='100%'> ";
      sContent += "       <fieldset><legend><b>Previsoes tarefa : "+obj.iCodTarefa+"</legend>   ";
      sContent += "         <div id='grid_previsao' style='width: 100%;-moz-user-select:none'> ";
      sContent += "         </div>    ";
      sContent += "       </fieldset> ";
      sContent += "     </td>   ";
      sContent += "   </tr>     ";
      sContent += "   <tr>      ";
      sContent += "     <td align='center'> ";
      sContent += "       <input type='button' name='btnSalvar' id='btnSalvar' value='Salvar' onClick='js_salvar("+obj.iCodTarefa+");'>    ";
      sContent += "     </td>    ";
      sContent += "   </tr>     ";
      sContent += " </table>    ";

  oWindowPrevisao = new windowAux('previsaotarefa', 'Lançar Previsão : ',1050, 400);
  oWindowPrevisao.setContent(sContent);

  $("window"+oWindowPrevisao.idWindow+"_btnclose").observe("click", function(){
    oWindowPrevisao.destroy();
  });
  
  document.observe("keydown", function(event){ 
    if (event.which == 27) {
     oWindowPrevisao.destroy();
    }      
  });  

	oDBGridPrevisao = new DBGrid('grid_previsao');
	oDBGridPrevisao.nameInstance = 'oDBGridPrevisao';
	oDBGridPrevisao.hasTotalizador = true;
	//oDBGridPrevisao.setCheckbox(0);
	aHeader = new Array();
	aHeader[0] = 'Código';
	aHeader[1] = 'Fase';
	aHeader[2] = 'Responsavel';
	aHeader[3] = 'Qtd.';
	aHeader[4] = 'Dt. Ini.';
	aHeader[5] = 'H. Ini.';
	aHeader[6] = 'Dt. Fim.';
	aHeader[7] = 'H. Fim.';
	oDBGridPrevisao.setHeader(aHeader);
	oDBGridPrevisao.setHeight(200);
	//oDBGridPrevisao.aHeader[11].lDisplayed = false;
	oDBGridPrevisao.allowSelectColumns(true);
	//oDBGridPrevisao.setCellWidth(new Array(100,100));
	var aAligns = new Array();
	aAligns[0] = 'center';
	aAligns[1] = 'left';
	aAligns[2] = 'left';
	aAligns[3] = 'center';
	aAligns[4] = 'center';
	aAligns[5] = 'center';
	aAligns[6] = 'center';
	aAligns[7] = 'center';

	oDBGridPrevisao.setCellAlign(aAligns);
	oDBGridPrevisao.show($('grid_previsao'));

	oDBGridPrevisao.clearAll(true);

	if (obj) {
	  var aLinha = new Array();
		for (var iInd = 0; iInd < obj.aRegistros.length; iInd++) {
			with(obj.aRegistros[iInd]){

      var sValorDataIni = "";
      if (at82_dataini != ""){
        sValorDataIni = js_formatar(at82_dataini,'d');
      }


        eval("oTxtFieldDataIni"+iInd+" = new DBTextFieldData('data_ini_"+iInd+"','oTxtFieldDataIni"+iInd+"','"+sValorDataIni+"');");
        eval("oTxtFieldDataFim"+iInd+" = new DBTextFieldData('data_fim_"+iInd+"','oTxtFieldDataFim"+iInd+"','"+js_formatar(at82_datafim,'d')+"');");

        eval("oTxtFieldDataIni"+iInd+" = new DBTextFieldData('data_ini_"+iInd+"','oTxtFieldDataIni"+iInd+"','"+sValorDataIni+"');");
        eval("oTxtFieldDataFim"+iInd+" = new DBTextFieldData('data_fim_"+iInd+"','oTxtFieldDataFim"+iInd+"','"+js_formatar(at82_datafim,'d')+"');");
				aLinha[0]  = at46_codigo;
				aLinha[1]  = at46_descr.urlDecode();
				aLinha[2]  = "<input name='idresp"+iInd+"' id='idresp"+iInd+"' value='"+id_usuario+"'size=7 type='text' disabled >";
        aLinha[2] += "<input name='responsavel_"+iInd+"' id='responsavel_"+iInd+"' type='text' size='50' value='"+nome.urlDecode()+"' onChange='js_limpaId(this,$(\"idresp"+iInd+"\"));' onFocus='js_criaAutoComplete(this,$(\"idresp"+iInd+"\"));'>";
        aLinha[3]  = "<input name='qtd_"+iInd+"' id='qtd_"+iInd+"' type='text' size='5' value='"+at82_qtdhoras+"'>";
        aLinha[4]  = eval("oTxtFieldDataIni"+iInd+".toInnerHtml();");
        aLinha[5]  = "<input name='horaini_"+iInd+"' id='horaini_"+iInd+"' type='text' size='5' value='"+at82_horaini.urlDecode()+"' onChange='js_verifica_hora(this)'>";
        aLinha[6]  = eval("oTxtFieldDataFim"+iInd+".toInnerHtml();");
        aLinha[7]  = "<input name='horafim_"+iInd+"' id='horafim_"+iInd+"' type='text' size='5' value='"+at82_horafim.urlDecode()+"' onChange='js_verifica_hora(this)'>";
				
		    oDBGridPrevisao.addRow(aLinha);
		    oDBGridPrevisao.aRows[iInd].isSelected = true;

			}
		}
    oDBGridPrevisao.renderRows();
	}

  js_removeObj("msgBox");
  oWindowPrevisao.show(60,90);
  
}

function js_limpaId(oResp,oObjId){
  if (oResp.value == '') {
    oObjId.value = '';
  }
}

function js_salvar(iCodTarefa){

  aLinhas = oDBGridPrevisao.getSelection("object");
// alert(oDBGridPrevisao.getSelection("object")[0].aCells[1].getValue());
// alert(aLinhas[0].aCells[6].getValue());
// return false;
  aPrevisoes = new Array();

  for (var iInd = 0; iInd < aLinhas.length; iInd++) {

    with(aLinhas[iInd]){

      oObjTmp = new Object();
      oObjTmp.iCodSituacao = aCells[0].getValue();
      oObjTmp.iCodUsuario  = aCells[2].getValue();
      oObjTmp.nQtdHoras    = aCells[3].getValue();
      oObjTmp.sDtIni       = aCells[4].getValue();
      oObjTmp.sHoraIni     = aCells[5].getValue();
      oObjTmp.sDtFim       = aCells[6].getValue();
      oObjTmp.sHoraFim     = aCells[7].getValue();
      aPrevisoes[iInd]     = oObjTmp;

    }
  }

//  alert(Object.toJSON(aPrevisoes));
  oDados = new Object();
  oDados.exec       = "lancarRegistros";
  oDados.iCodTarefa = iCodTarefa;
  oDados.aRegistros = aPrevisoes;
  js_divCarregando("Aguarde, Lançando registros ","msgBox");
 
  strJson = Object.toJSON(oDados);
  var oAjax   = new Ajax.Request( sUrlRPC, {
                                             method: 'post', 
                                             parameters: 'json='+strJson, 
                                             onComplete: js_RetornoSalvar
                                           }
                                );

}

function js_RetornoSalvar(oAjax){

	var obj = JSON.parse(oAjax.responseText);
	alert(obj.sMensagem.urlDecode());
  js_removeObj("msgBox");
  oWindowPrevisao.destroy();
  js_init();  
}


function js_criaAutoComplete(oObj,oObjId){

  oAutoComplete = new dbAutoComplete(oObj,'ate4_pesquisaresponsavel.RPC.php');
  oAutoComplete.setTxtFieldId(oObjId);
  oAutoComplete.show();

}

function js_JanelaRegistros(oAjax){

	var obj = JSON.parse(oAjax.responseText);
	if (obj.status && obj.status == 2){
	   js_removeObj("msgBox");
	   alert(obj.sMensagem.urlDecode());
	   return false ;
	}

  if (obj) {

    var sContent  = "<table align='center' width='100%' style='border:1px solid #000000; border-collapse:collapse;font-family:Arial,Helvetica, sans-serif;font-size:8px; padding:5px;background-color:white;vertical-align:bottom'> ";
		for (var iInd = 0; iInd < obj.length; iInd++) {

      with (obj[iInd]){

        var sPeriodo  = js_formatar(at43_diaini,'d')+" - "+at43_horainidia.urlDecode()+" até "+js_formatar(at43_diafim,'d')+" - "+at43_horafim.urlDecode();
        sContent += "  <tr>   ";
        sContent += "    <td nowrap valign='top'><b>Tarefa : </b></td> ";
        sContent += "    <td nowrap valign='top'>"+at43_tarefa+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td nowrap valign='top'><b>Usuário : </b></td> ";
        sContent += "    <td nowrap valign='top'>"+nome.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td nowrap valign='top'><b>Periodo : </b></td> ";
        sContent += "    <td nowrap valign='top'>"+sPeriodo.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr>   ";
        sContent += "    <td nowrap valign='top'><b>Descrição :</b></td> ";
        sContent += "    <td valign='top'>"+at43_descr.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
        sContent += "  <tr class='final'>   ";
        sContent += "    <td nowrap valign='top'><b>Executado :</b></td> ";
        sContent += "    <td valign='top'>"+at43_obs.urlDecode()+"</td> ";
        sContent += "  </tr>  ";
      }
    }
    
    sContent += "</table> ";
    windowRegistros = new windowAux('registrostarefa', 'Registros', 700, 500);
    windowRegistros.setContent(sContent);
    $("window"+windowRegistros.idWindow+"_btnclose").observe("click", function(){
      windowRegistros.destroy();
    });
    document.observe("keydown", function(event){ 
      if (event.which == 27) {
       windowRegistros.destroy();
      }      
    });  
    
    js_removeObj("msgBox");
    windowRegistros.show(60,300);
  }

}

function js_RegistrosTarefa(iCodTarefa) {

  js_divCarregando("Aguarde, buscando registros Tarefa["+iCodTarefa+"]","msgBox");
 
  strJson = '{"exec":"getRegistros","iCodTarefa":"'+iCodTarefa+'"}';
  var oAjax   = new Ajax.Request( sUrlRPC, {
                                             method: 'post', 
                                             parameters: 'json='+strJson, 
                                             onComplete: js_JanelaRegistros
                                           }
                                );
}

function js_DetalhesTarefa(iCodTarefa) {

  js_divCarregando("Aguarde, buscando detalhes Tarefa["+iCodTarefa+"]","msgBox");
 
  strJson = '{"exec":"getDetalheTarefa","iCodTarefa":"'+iCodTarefa+'"}';
  var oAjax   = new Ajax.Request( sUrlRPC, {
                                             method: 'post', 
                                             parameters: 'json='+strJson, 
                                             onComplete: js_criaJanela
                                           }
                                );
}

function js_consultaTarefas() {

  js_divCarregando("Aguarde, buscando registros","msgBox");
 
  strJson = '{"exec":"getTarefas"}';
  var oAjax   = new Ajax.Request( sUrlRPC, {
                                             method: 'post', 
                                             parameters: 'json='+strJson, 
                                             onComplete: js_carregaGrid
                                           }
                                );
}

function js_carregaGrid(oAjax) {
   
	  var obj = JSON.parse(oAjax.responseText);
	  js_removeObj("msgBox");
	  if (obj.status && obj.status == 2){
	     js_removeObj("msgBox");
	     alert(obj.sMensagem.urlDecode());
	     return false ;
	  }
	  
		oDBGridTarefas.clearAll(true);

		if (obj) {
      
      addRows(obj, 0);      
      oDBGridTarefas.renderRows();
		}
	 	js_removeObj("msgBox");	 	
}

function addRows (aObj, niv) {
  
  aObj.each( function (oTarefa, i) {
    // CRIA A VARIAVEL DA LINHA COMO ARRAY VAZIO //
    var aLinha = new Array();
      
    // ÍNDICE DA LINHA //
    var iInd = oDBGridTarefas.aRows.length;
  
    // SE TAREFA JA ESTIVER AUTORIZADA O BOTÃO DE AUTORIZAÇÃO FICA DISABLED //
    var sDisable = "";
    if (oTarefa.autorizada == 't') {
      sDisable = "disabled";
    }
      
    // IDENTAÇÃO COM IMAGENS DA TREEVIEW //
    var sIdent = "&nbsp;";
    if (niv>0) {
	    for (var iNiv=0; iNiv < niv; iNiv++) {
	      sIdent += "&nbsp;&nbsp;";
	    }
	    
	    if (i == aObj.length-1) 
	      sIdent += "<img src='imagens/tree/join2.gif?t=<?php echo time(); ?>' border='0' id='img"+iInd+"'>";
	    else sIdent += "<img src='imagens/tree/joinbottom2.gif?t=<?php echo time(); ?>' border='0' id='img"+iInd+"'>";	      
	  }
    sIdent += "&nbsp;";
      
    // SE FOR TAREFA FILHA INICIA COMO DISPLAY:NONE //
    var sDisplay = "";
    if (niv>0) 
      sDisplay = "none";
    
    // BOTÃO DE TREEVIEW SE TAREFA TIVER FILHAS //
    var button = "";
    if(oTarefa.iTemfilhas == '1') {
        
      var iLin = iInd;
      var sVals = "";       
      oTarefa.aFilhas.each( function (oFilha, iN) {
	      sVals += (iLin+1);
	      if(iN < oTarefa.aFilhas.length-1)
	        sVals += ",";	        
	      iLin++;
	    });
	      
	    button = "<img src='imagens/treeplus.gif?t="+Math.random()+"' border='0' id='img"+iInd+"' onclick='showHide(this, ["+sVals+"])' style='cursor:pointer'>";	      
    }      
    
    // CARREGA O ARRAY DE LINHAS //
    aLinha[0]  = oTarefa.tarefa+"<input type='hidden' name='hid"+iInd+"' id='hid"+iInd+"' value='"+iInd+"'> ";
    aLinha[1]  = button; //sIdent+oTarefa.descricao.urlDecode();
    aLinha[2]  = sIdent+oTarefa.descricao.urlDecode();
    aLinha[3]  = "&nbsp;"+oTarefa.responsavel.urlDecode();
    aLinha[4]  = js_formatar(oTarefa.inicio_previsto,'d')+" - "+oTarefa.hora_inicio_previsto.urlDecode();
    aLinha[5]  = js_formatar(oTarefa.final_previsto,'d')+" - "+oTarefa.hora_final_previsto.urlDecode();
    aLinha[6]  = js_formatar(oTarefa.inicio_teste,'d')+" - "+oTarefa.horaini_teste.urlDecode();
    aLinha[7]  = js_formatar(oTarefa.data_fecha,'d')+" - "+oTarefa.hora_fecha.urlDecode();
    aLinha[8]  = oTarefa.progresso;
    aLinha[9]  = oTarefa.situacao_atual.urlDecode();
    aLinha[10]  = "<input type='button' name='btnAutorizar"+iInd+"' id='btnAutorizar"+iInd+"' value='Autorizar' "+sDisable+" style='width:100%' onClick='js_janelaAutorizar("+oTarefa.tarefa+");'> ";

    // INCLUI LINHA NO DBGRID //
    oDBGridTarefas.addRow(aLinha);
      
    // SE FOR FILHA O ESTILO DAS COLUNAS É DIFERENTE //
    if (niv>0) {	    
	    oDBGridTarefas.aRows[iInd].aCells.each ( function (oCell) { 
		    oCell.sStyle += ";border-right-width:0px;border-left-width:0px;padding:0px;";		  
		  });     
    }
      
    // FUNÇÕES DAS COLUNAS //
    oDBGridTarefas.aRows[iInd].sStyle += ";display:"+sDisplay+";";
    oDBGridTarefas.aRows[iInd].setClassName(getClassName(oTarefa.cod_situacao));
    oDBGridTarefas.aRows[iInd].aCells[2].sEvents  = "onClick='js_RegistrosTarefa(\""+oTarefa.tarefa+"\")'";
    oDBGridTarefas.aRows[iInd].aCells[2].sStyle  += ";cursor:pointer";
    oDBGridTarefas.aRows[iInd].aCells[0].sEvents  = "onClick='js_DetalhesTarefa(\""+oTarefa.tarefa+"\")'";
    oDBGridTarefas.aRows[iInd].aCells[0].sStyle  += ";cursor:pointer";
    oDBGridTarefas.aRows[iInd].aCells[4].sEvents  = "onClick='js_LancarPrevisao(\""+oTarefa.tarefa+"\")'";
    oDBGridTarefas.aRows[iInd].aCells[5].sEvents  = "onClick='js_LancarPrevisao(\""+oTarefa.tarefa+"\")'";
    oDBGridTarefas.aRows[iInd].aCells[0].sStyle  += ";padding:0;xp";
    
    // VERIFICA SE JA EXISTE O RESPONSAVEL NO SELECT DE FILTRO, SE NÃO EXISTIR INCLUI //
    var exists = $('responsavel').innerHTML.match(oTarefa.responsavel.urlDecode());      
    if (exists == null) {
      $('responsavel').innerHTML += "<option value='"+oTarefa.responsavel.urlDecode()+"'>"+oTarefa.responsavel.urlDecode()+"</option>";
    }
    
    // SE TIVER FILHAS ENTRA NA RECURSÃO //
    if (oTarefa.iTemfilhas == '1') {        
      addRows(oTarefa.aFilhas, niv+1);
    }
    
  });
}

function showHide(butt, aLinhas) {
  
  var exists = butt.src.match("imagens/treeminus.gif");
   
  if (exists == null) {
    butt.src = "imagens/treeminus.gif?t="+Math.random();
  } else {
    butt.src = "imagens/treeplus.gif?t="+Math.random();
  }
  
  /*var aT = new Array(2, 7);
  alert(aT[0]);*/
  
  aLinhas.each( function (iLinha) {
    var oRow  = document.getElementById("grid_tarefasrowgrid_tarefas"+iLinha);  
    if (oRow.style.display != 'none') {    
      oRow.style.display = 'none';
    } else {
      oRow.style.display = '';
    }   
  });
}


function js_janelaAutorizar(iCodTarefa) {

/*
	var db_textfield_date_ini = new DBTextFieldData("dt_inicial_autorizar" ,"db_textfield_date_ini", "", "");
	var input_dt_ini = db_textfield_date_ini.sStringConteudo;
	
	var db_textfield_date_fim = new DBTextFieldData("dt_final_autorizar","db_textfield_date_fim", "", "");
	var input_dt_fim = db_textfield_date_fim.sStringConteudo;*/

 
	var sContent  = "<br>";
	    sContent += "<fieldset style='width:90% align:center'><legend><b>Periodo :<b></legend> ";
	    sContent += " <table>        ";
	    sContent += "   <tr>         ";
	    sContent += "     <td nowrap>Periodo: </td>  ";
	    sContent += "     <td nowrap align=left>  </td>  ";
	    sContent += "   </tr>      ";
	    sContent += " </table>     ";
	    sContent += " </fieldset>  ";
	    sContent += "<br>";    
	    sContent += "<fieldset style='width:90% align:center'><legend><b>Responsável :<b></legend> ";
	    sContent += " <table>        ";
	    sContent += "   <tr>         ";
	    sContent += "     <td nowrap>Responsável: </td>";
	    sContent += "     <td nowrap>";
	//    sContent += "       <select name='responsavel' id='responsavel' onchange='js_filtro()'>";
	//    sContent += "         <option value='' selected>Selecione...</option>";
	 //   sContent += "       </select>";        
	    sContent += "     </td>";
	    sContent += "   </tr>      ";
	    sContent += " </table>     ";
	    sContent += " </fieldset>  ";    
	    sContent += " <table align=center>";
	    sContent += "   <tr>         ";
	    sContent += "     <td nowrap align=center colspan=3> ";
	    //sContent += "       <input type='button' name='filtroDate' id='filtroDate' onclick='js_filtro();' value='Filtrar'>";
	    //sContent += "       <input type='button' name='clearfiltroDate' id='clearfiltroDate' onclick='js_clearFiltro();' value='Limpar'>";
	    sContent += "     </td> ";
	    sContent += "   </tr>      ";
	    sContent += " </table>     ";    
	        
  windowAutorizar = new windowAux('autorizar', 'Autorizar Tarefa', 410, 380);
	windowAutorizar.setContent(sContent);
	
	//$('dt_inicial').observe('blur', js_filtro);
	//$('dt_final').observe('blur', js_filtro);
	/*
	oMessage   = new DBMessageBoard("msgboard1", 
	                                "Autorizar Tarefa", 
	                                " - Autorizar Tarefa.",
	                                $("windowautorizar_content"));
	oMessage.show();*/
}

function js_autorizar(iCodTarefa){

  alert(iCodTarefa);
}


function getClassName(iCodSituacao){
    
    switch (iCodSituacao){
      case '1':
        return 'pendente';
        break;
      case '2':
        return 'normal';
        break;
      case '3':
        return 'liberada';
        break;
      case '4':
        return 'teste';
        break;
      case '5':
        return 'liberada';
        break;
      case '7':
        return 'liberada';
        break;
      default:
        return 'normal';
      
    }

}



function js_Detalhes(iCodTarefa){

  alert(iCodTarefa);

}

function js_init(){

	oDBGridTarefas = new DBGrid('grid_tarefas');
	oDBGridTarefas.nameInstance = 'oDBGridTarefas';
	oDBGridTarefas.hasTotalizador = true;
	//oDBGridTarefas.setCheckbox(0);
	aHeader = new Array();
	aHeader[0] = 'Código';
	aHeader[1] = '';
	aHeader[2] = 'Descrição';
	aHeader[3] = 'Responsavel';
	aHeader[4] = 'Ini. prev.';
	aHeader[5] = 'Fim prev.';
	aHeader[6] = 'Ini. test.';
	aHeader[7] = 'Fim test.';
	aHeader[8] = 'Progresso';
	aHeader[9] = 'Situação Atual';
	aHeader[10] = 'Ação';
	oDBGridTarefas.setHeader(aHeader);
	oDBGridTarefas.setHeight(450);
	//oDBGridTarefas.aHeader[11].lDisplayed = false;
	oDBGridTarefas.allowSelectColumns(true);
	//oDBGridTarefas.setCellWidth(new Array(100,100));
	var aAligns = new Array();
	aAligns[0] = 'center';
	aAligns[1] = 'center';
	aAligns[2] = 'left';
	aAligns[3] = 'left';
	aAligns[4] = 'center';
	aAligns[5] = 'center';
	aAligns[6] = 'center';
	aAligns[7] = 'center';
	aAligns[8] = 'center';
	aAligns[9] = 'center';
	aAligns[10] = 'center';
	

//oDBGridTarefas.aHeaders[2].lDisplayed = false;
  oDBGridTarefas.aHeaders[6].lDisplayed = false;
  oDBGridTarefas.aHeaders[7].lDisplayed = false;
  oDBGridTarefas.aHeaders[9].lDisplayed = false;

	oDBGridTarefas.setCellAlign(aAligns);
	oDBGridTarefas.show($('grid_tarefas'));
  js_consultaTarefas();

}

function js_verifica_hora(oObj){

  erro= 0;
  ms  = "";
  hs  = "";

  tam = "";
  pos = "";
  valor = oObj.value;
  tam = valor.length;
  pos = valor.indexOf(":");  
  if(pos!=-1){
    if(pos==0 || pos>2){
      erro++;
    }else{
      if(pos==1){
        hs = "0"+valor.substr(0,1);
        ms = valor.substr(pos+1,2);
      }else if(pos==2){
        hs = valor.substr(0,2);
        ms = valor.substr(pos+1,2);
      }
      if(ms==""){
        ms = "00";
      }
    }
  }else{
    if(tam>=4){
      hs = valor.substr(0,2);
      ms = valor.substr(2,2);
    }else if(tam==3){
      hs = "0"+valor.substr(0,1);
      ms = valor.substr(1,2);
    }else if(tam==2){
      hs = valor;
      ms = "00";
    }else if(tam==1){
      hs = "0"+valor;
      ms = "00";
    }
  }
  if(ms!="" && hs!=""){
    if(hs>24 || hs<0 || ms>60 || ms<0){
      erro++
    }else{
      if(ms==60){
        ms = "59";
      }
      if(hs==24){
        hs = "00";
      }
      hora = hs;
      minu = ms;
    }    
  }

  if(erro>0){
    alert("Informe uma hora válida.");
  }
  if(valor!=""){    
    oObj.focus();
    oObj.value = hora+":"+minu;
  }
}

js_init();
</script>
