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
include(modification("dbforms/db_classesgenericas.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_orctiporec_classe.php"));
include(modification("classes/db_empempenho_classe.php"));
include(modification("libs/db_app.utils.php"));

db_postmemory($HTTP_POST_VARS);

$clorctiporec				=new cl_orctiporec;
$cliframe_seleciona = new cl_iframe_seleciona;
$clempempenho = new cl_empempenho;
$clempempenho->rotulo->label();
$clrotulo           = new rotulocampo;
$clrotulo->label('c60_codcon');
$clrotulo->label('c60_descr');
$clrotulo->label("k17_codigo");
$clrotulo->label("c50_descr");
$clrotulo->label("k17_hist");
$clrotulo->label("e60_codempini");
$clrotulo->label("e50_codord");
$clrotulo->label("z01_numcgm");
$clrotulo->label("o15_recurso");
$clrotulo->label("o200_descricao");
$db_opcao = 1;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">

<script>
function js_emite(){
  var_obj = document.getElementById('cgm').length;
  cods = "";
  vir  = "";
  for(y=0;y<var_obj;y++){
    var_if = parseInt(document.getElementById('cgm').options[y].value)
    cods += vir + var_if;
    vir = ",";
  }
  qry = 'codigos='+cods;
  qry+= '&situacao='+document.form1.situacao.value;
  qry+= '&data='+document.form1.data_ano.value+'-'+document.form1.data_mes.value+'-'+document.form1.data_dia.value;
  qry+= '&data1='+document.form1.data1_ano.value+'-'+document.form1.data1_mes.value+'-'+document.form1.data1_dia.value;
  qry+= '&slip1='+document.form1.k17_codigo.value;
  qry+= '&recurso='+document.form1.o15_codigo.value;
  qry+= '&slip2='+document.form1.k17_codigo02.value;
  qry+= '&hist='+document.form1.k17_hist.value;
	js_consulta(qry);
//  jan = window.open('cai2_relslip002.php?'+qry,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
//  jan.moveTo(0,0);
}
function js_pesquisae60_codempini(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho','func_empempenho.php?funcao_js=parent.js_mostraempempenho2|e60_codemp|e60_anousu','Pesquisa',true);
  }else{
   // js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho02','func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho','Pesquisa',false);
  }
}
function js_mostraempempenho2(chave1, chave2){
  $('e60_codempini').value = chave1 + '/' + chave2;
  db_iframe_empempenho.hide();
}
function js_pesquisae60_codempfim(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho','func_empempenho.php?funcao_js=parent.js_mostraempempenhofim2|e60_codemp|e60_anousu','Pesquisa',true);
  }else{
   // js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho02','func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho','Pesquisa',false);
  }
}
function js_mostraempempenhofim2(chave1, chave2){
  $('e60_codempfim').value = chave1 + '/' + chave2;
  db_iframe_empempenho.hide();
}
function js_pesquisak17_codigoini(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_slip','func_slip.php?funcao_js=parent.js_mostraslipini1|k17_codigo','Pesquisa',true);
  }else{
    slip01 = new Number($('k17_codigoini').value);
    if(slip01 != ""){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_slip','func_slip.php?pesquisa_chave='+slip01+'&funcao_js=parent.js_mostraslip','Pesquisa',false);
    }else{
        $('k17_codigoini').value='';
    }
  }
}
function js_mostraslip(chave,erro){
  if(erro==true){
    $('k17_codigoini').focus();
    $('k17_codigoini').value = '';
  }
}
function js_mostraslipini1(chave1,chave2){
  $('k17_codigoini').value = chave1;
  db_iframe_slip.hide();
}
function js_pesquisak17_codigofim(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_slip','func_slip.php?funcao_js=parent.js_mostraslipfim|k17_codigo','Pesquisa',true);
  }else{
    slip01 = new Number($('k17_codigofim').value);
    if(slip01 != ""){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_slip','func_slip.php?pesquisa_chave='+slip01+'&funcao_js=parent.js_mostraslipfim2','Pesquisa',false);
    }else{
        $('k17_codigofim').value='';
    }
	}
}
function js_mostraslipfim2(chave,erro){
  if(erro==true){
    $('k17_codigofim').focus();
    $('k17_codigofim').value = '';
  }
}
function js_mostraslipfim(chave1,chave2){
  $('k17_codigofim').value = chave1;
  db_iframe_slip.hide();
}
function js_pesquisae50_codordini(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?funcao_js=parent.js_mostrapagordemini1|e50_codord','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?pesquisa_chave='+$('e50_codordini').value+'&funcao_js=parent.js_mostrapagordemini','Pesquisa',false);
  }
}
function js_mostrapagordemini(chave,erro){
  if(erro==true){
    $('e50_codordini').focus();
    $('e50_codordini').value = '';
  }
}
function js_mostrapagordemini1(chave1,chave2){
  $('e50_codordini').value = chave1;
  db_iframe_pagordem.hide();
}
function js_pesquisae50_codordfim(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?funcao_js=parent.js_mostrapagordemfim1|e50_codord','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?pesquisa_chave='+$('e50_codordini').value+'&funcao_js=parent.js_mostrapagordemfim','Pesquisa',false);
  }
}
function js_mostrapagordemfim(chave,erro){
  if(erro==true){
    $('e50_codordfim').focus();
    $('e50_codordfim').value = '';
  }
}
function js_mostrapagordemfim1(chave1,chave2){
  $('e50_codordfim').value = chave1;
  db_iframe_pagordem.hide();
}
function js_pesquisa_cgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_forne','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
  }else{
     if($('z01_numcgm').value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_forne','func_nome.php?pesquisa_chave='+$('z01_numcgm').value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
     }else{
       $('z01_nome2').value = '';
     }
  }
}
function js_mostracgm(erro,chave){
  $('z01_nome2').value = chave;
  if(erro==true){
    $('z01_numcgm').value = '';
    $('z01_numcgm').focus();
  }
}
function js_mostracgm1(chave1,chave2){
   $('z01_numcgm').value = chave1;
   $('z01_nome2').value = chave2;
   db_iframe_forne.hide();
   //db_iframe_cgm.hide();
}

function js_pesquisa_contapagadora(mostra){

  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empagetipo','func_empagetipo.php?funcao_js=parent.js_mostracontapagadora1|e83_conta|e83_descr','Pesquisa',true);
  }else{
     if($('e83_conta').value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empagetipo','func_empagetipo.php?pesquisa_chave='+$('e83_conta').value+'&e83_conta='+$('e83_conta').value+'&funcao_js=parent.js_mostracontapagadora','Pesquisa',false);
     }else{
       $('e83_descr').value = '';
     }
  }
}
function js_mostracontapagadora(chave,erro){
	//alert(chave+'---'+erro);
  $('e83_descr').value = chave;
  if(erro==true){
    $('e83_conta').value = '';
    $('e83_conta').focus();
  }
}
function js_mostracontapagadora1(chave1,chave2){

   $('e83_conta').value = chave1;
   $('e83_descr').value = chave2;
   db_iframe_empagetipo.hide();
}
function js_pesquisa_banco(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_bancos','func_db_bancos.php?funcao_js=parent.js_mostrabanco1|db90_codban|db90_descr','Pesquisa',true);
  }else{
     if($('db90_codban').value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_bancos','func_db_bancos.php?pesquisa_chave='+$('db90_codban').value+'&funcao_js=parent.js_mostrabanco','Pesquisa',false);
     }else{
       $('db90_descr').value = '';
     }
  }
}
function js_mostrabanco(chave,erro){
  $('db90_descr').value = chave;
  if(erro==true){
    $('db90_codban').value = '';
    $('db90_codban').focus();
  }
}
function js_mostrabanco1(chave1,chave2){

   $('db90_codban').value = chave1;
   $('db90_descr').value = chave2;
   db_iframe_db_bancos.hide();
}

function js_pesquisa_recurso(mostra){

  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_orctiporec','func_orctiporec.php?funcao_js=parent.js_mostrarecurso1|o15_codigo|o15_descr','Pesquisa',true);
  }else{
     if($('o15_codigo').value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_orctiporec','func_orctiporec.php?pesquisa_chave='+$('o15_codigo').value+'&funcao_js=parent.js_mostrarecurso','Pesquisa',false);
     }else{
       $('o15_descr').value = '';
     }
  }
}
function js_mostrarecurso(chave,erro){

  $('o15_descr').value = chave;
  if(erro==true){
    $('o15_codigo').value = '';
    $('o15_codigo').focus();
  }
}
function js_mostrarecurso1(chave1,chave2){

   $('o15_codigo').value = chave1;
   $('o15_descr').value = chave2;
   db_iframe_orctiporec.hide();
}
function js_frmListaCheques(){

    oDBGridListaCheques = new DBGrid('cheques');
    oDBGridListaCheques.nameInstance = 'oDBGridListaCheques';
    oDBGridListaCheques.setHeader(
        ['Núm. do cheque', 'Empenho', 'Ordem Pag.', 'Credor', 'Data de emissão cheq.',
            'Anulado', 'Conta pagadora', 'Recurso', 'Valor', 'c63_conta']
    );
    oDBGridListaCheques.setHeight(120);
    oDBGridListaCheques.setCellAlign(new Array('right','right','right','left','center','center','left','right','right','right'));
    oDBGridListaCheques.setCellWidth(['9%', '8%', '10%','23%', '10%', '10%', '10%', '10%', '10%', '0%']);
    oDBGridListaCheques.aHeaders[9].lDisplayed = false;
    oDBGridListaCheques.show($('listacheques'));
    oDBGridListaCheques.renderRows();
}

function js_limpa(){
	$('e86_cheque').value		 	= '';
	$('e60_codempini').value 	= '';
	$('e60_codempfim').value 	= '';
	$('e50_codordini').value 	= '';
	$('e50_codordfim').value 	= '';
	$('k17_codigoini').value 	= '';
	$('k17_codigofim').value 	= '';
	$('z01_numcgm').value 		= '';
	$('z01_nome2').value 			= '';
	$('dtini').value 					= '';
	$('dtfim').value					= '';
	$('e83_conta').value			= '';
	$('e83_descr').value			= '';
	$('db90_codban').value		= '';
	$('db90_descr').value			= '';
	$('o15_codigo').value			= '';
	$('o15_descr').value			= '';
	oDBGridListaCheques.clearAll();
	oDBGridListaCheques.renderRows();
}

function js_pesquisa(){

	var oPesquisa = new Object();

	oPesquisa.exec = 'getCheques';
	oPesquisa.e86_cheque = $('e86_cheque').value;
	oPesquisa.e60_codempini = $('e60_codempini').value;
	oPesquisa.e60_codempfim = $('e60_codempfim').value;

	oPesquisa.e50_codordini = $('e50_codordini').value;
	oPesquisa.e50_codordfim = $('e50_codordfim').value;


	oPesquisa.k17_codigoini = $('k17_codigoini').value;
	oPesquisa.k17_codigofim = $('k17_codigofim').value;

	oPesquisa.z01_numcgm 		= $('z01_numcgm').value;
	oPesquisa.dtini					= "";
	oPesquisa.dtfim					= "";
	if($('dtini').value != "" && $('dtfim').value != ""){
		var dataini = $('dtini').value.split('/');
		oPesquisa.dtini					=	dataini[2]+'-'+dataini[1]+'-'+dataini[0];
		var datafim = $('dtfim').value.split('/');
		oPesquisa.dtfim					=	datafim[2]+'-'+datafim[1]+'-'+datafim[0];
		var difDatas = js_diferenca_datas(oPesquisa.dtini,oPesquisa.dtfim,3);
		if(difDatas == true){
			alert("Usuário:\n\nA data inicial deve ser menor que a data final !\n\n");
			$('dtini').value = "";
			$('dtini').focus();
			return false;
		}
	}else{
		if($('dtini').value != ""){
			var dataini = $('dtini').value.split('/');
			oPesquisa.dtini					=	dataini[2]+'-'+dataini[1]+'-'+dataini[0];
		}else {
			oPesquisa.dtini = "";
		}
		if($('dtfim').value != ""){
			var datafim = $('dtfim').value.split('/');
			oPesquisa.dtfim					=	datafim[2]+'-'+datafim[1]+'-'+datafim[0];
		}else{
			oPesquisa.dtfim	= "";
		}
	}
	oPesquisa.e83_conta =	$('e83_conta').value;
	oPesquisa.db90_codban =	$('db90_codban').value;
	oPesquisa.o15_codigo =	$('o15_codigo').value;

	var sDados = Object.toJSON(oPesquisa);
//	alert(sDados);
	var msgDiv = "Aguarde pesquisando ...";
	js_divCarregando(msgDiv,'msgBox');

	sUrl = 'emp4_consultacheques.RPC.php';
	var sQuery = 'dados='+sDados;
	var oAjax   = new Ajax.Request( sUrl, {
                                            method: 'post',
                                            parameters: sQuery,
                                            onComplete: js_retornoPesquisaCheque
                                          }
                                  );

}

function js_retornoPesquisaCheque(oAjax){
	js_removeObj("msgBox");
	//alert(oAjax.responseText);

	var aRetorno = JSON.parse(oAjax.responseText);

	var sExpReg  = new RegExp('\\\\n','g');
  if(aRetorno.status == 2 ){
  	alert(aRetorno.message.urlDecode().replace(sExpReg,'\n'));
  	return false;
  }

  js_RenderGridCheques(aRetorno.dados);

}

function js_RenderGridCheques(aDados){

	oDBGridListaCheques.clearAll(true);

	var iNumRows = aDados.length;

		if(iNumRows > 0){
			aDados.each(
				function (oDado,iInd){

						var aRow	= new Array();
						var link = "<a href='#' onClick='js_hist_cheque(\"e91_cheque="+oDado.e91_cheque;
						link +="&c63_agencia="+oDado.c63_agencia+"&c63_banco="+oDado.c63_banco+"&c63_conta="+oDado.c63_conta+"\");'>"+oDado.e91_cheque+"</a>";
						aRow[0] 	= link;
						aRow[1] 	= oDado.empenho.urlDecode();
						aRow[2] 	= oDado.codigo_origem;
						aRow[3] 	= oDado.credor.urlDecode();
						aRow[4] 	= js_formatar(oDado.e86_data,'d','');
						var conta =	oDado.c61_reduz+'-'+oDado.e83_descr.urlDecode();
						aRow[5] 	= oDado.anulado.urlDecode();
						aRow[6] 	= conta.substring(0,20);
						aRow[7] 	= oDado.recurso;
						aRow[8] 	= js_formatar(oDado.e91_valor,'f','');
						aRow[9] 	= oDado.c63_conta;

	 					oDBGridListaCheques.addRow(aRow);

				}
			);
		}

	oDBGridListaCheques.renderRows();

}

function js_hist_cheque(query){
	js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_dados_cheque','cai3_dadoscheque001.php?'+query,'Dados do Cheque',true);
}

</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc"
  onLoad="js_frmListaCheques();">
<table width="95%" align="center" style="margin-top: 20px;"><tr align="center"><td>
 	<fieldset>
 		<legend><b>Consulta cheque</b></legend>
 		<table>
 			<tr>
 				<td width="50%" valign="top">
 					<table>
 						<tr>
 							<td><b>Número do cheque:</b></td>
 							<td>
 								<?php
 									db_input('e86_cheque',13,1,true,"text",1);
 								?></td>
 							<td></td>
 							<td></td>
 						</tr>
 						<tr>
 							<td><?php db_ancora(@$Le60_codemp,"js_pesquisae60_codempini(true);",1);  ?></td>
 							<td>
 									<?php
 										$e60_codempini = "";
 										db_input("e60_codempini",13,$e60_codempini,true,"text",4);
 									?>
 							</td>
 							<td><?php db_ancora("<b>Até</b>","js_pesquisae60_codempfim(true);",1);  ?></td>
 							<td>
 									<?php
 										$e60_codempfim = "";
 										db_input("e60_codempfim",13,$e60_codempfim,true,"text",4);
 									?>
 							</td>
 						</tr>
 						<tr>
 							<td><?php db_ancora(@$Le50_codord,"js_pesquisae50_codordini(true);",$db_opcao);  ?></td>
 							<td>
 									<?php
 									$Ie50_codordini = "";
 									db_input('e50_codordini',13,$Ie50_codordini,true,'text',$db_opcao," onchange='js_pesquisae50_codordini(false);'");
 									?>
 							</td>
 							<td><?php db_ancora("<b>Até</b>","js_pesquisae50_codordfim(true);",$db_opcao);  ?></td>
 							<td>
 									<?php
 									$Ie50_codordfim = "";
 									db_input('e50_codordfim',13,$Ie50_codordfim,true,'text',$db_opcao," onchange='js_pesquisae50_codordfim(false);'");
 									?>
 							</td>
 						</tr>
 						<tr>
 							<td><?php db_ancora(@$Lk17_codigo,"js_pesquisak17_codigoini(true);",$db_opcao);  ?></td>
 							<td>
 									<?php
 										$Ik17_codigoini = "";
 										db_input('k17_codigoini',13,$Ik17_codigoini,true,'text',$db_opcao," onchange='js_pesquisak17_codigoini(false);'");
 									?>
 							</td>
 							<td><?php db_ancora("<b>Até</b>","js_pesquisak17_codigofim(true);",$db_opcao);  ?></td>
 							<td>
 									<?php
 										$Ik17_codigofim = "";
 										db_input('k17_codigofim',13,$Ik17_codigofim,true,'text',$db_opcao," onchange='js_pesquisak17_codigofim(false);'");
 									?>
 							</td>
 						</tr>
 						<tr>
 							<td><?php db_ancora("<b>Credor:</b>","js_pesquisa_cgm(true);",1);?></td>
 							<td colspan="3">
 									<?php
 									db_input("z01_numcgm",6,1,true,"text",4,"onchange='js_pesquisa_cgm(false);'");
	      					db_input("z01_nome2",30,"",true,"text",3);
      						?>
      				</td>
 						</tr>
 					</table>
 				</td>
 				<td valign="top" width="50%">
 				<fieldset>
 					<legend><b>Data da emissão</b></legend>
 					<table>
 						<tr>
 							<td><b>Data inicial:</b></td>
 							<td><?php db_inputdata("dtini","","","",true,"text",1); ?></td>
 							<td><b>Data final:</b></td>
 							<td><?php db_inputdata("dtfim","","","",true,"text",1); ?></td>
 						</tr>
 					</table>
 				</fieldset>
 				<table>
 					<tr>
 						<td><?php db_ancora("<b>Conta Pagadora:</b>","js_pesquisa_contapagadora(true);",1);?></td>
 						<td>
 							<?php

	      			db_input("e83_conta",6,1,true,"text",4,"onchange='js_pesquisa_contapagadora(false);'");
	      			db_input("e83_descr",30,"",true,"text",3);
      				?>
 						</td>
 					</tr>
 					<tr>
 						<td><?php db_ancora("<b>Banco:</b>","js_pesquisa_banco(true);",1);?></td>
 						<td>
 							<?php
 							$Idb90_codban = "";
	      			db_input("db90_codban",6,1,true,"text",4,"onchange='js_pesquisa_banco(false);'");
	      			db_input("db90_descr",30,"",true,"text",3);
      				?>
 						</td>
 					</tr>

                    <tr>
                        <td >
                            <a id="ancoraFonteRecurso" href="#">Fonte de Recursos:</a>
                        </td>
                        <td>
                            <?php
                            db_input('o15_codigo', 10, 1, true, 'hidden', 3);
                            db_input('o15_recurso', 10, $Io15_recurso, true, 'text', 1);
                            db_input('o15_descr', 10, 0, true, 'text', 3);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Complemento:</td>
                        <td>
                            <?=db_input('o200_descricao', 60, $Io200_descricao, true, 'text', 3);?>
                        </td>
                    </tr>
 				</table>
 				</td>
 			</tr>
 		</table>
 	</fieldset>
</td></tr>
<tr>
	<td colspan='2' align="center">
    <input  name="pesquisar" id="pesquisar" type="button" value="Pesquisar" onclick="js_pesquisa();" >
    <input  name="limpar" id="limpar" type="button" value="Limpar" onclick="js_limpa();" >
  </td>
 </tr>
 <tr>
 	<td colspan="2" align="center" valign="top">
 		<fieldset>
 			<legend><b>Cheques</b></legend>
 			<div id="listacheques">

 			</div>
 		</fieldset>
 	</td>
 </tr>
</table>

<?php
  db_menu();
?>
</body>
<script>
    const lookUpRecurso = new DBLookUp($('ancoraFonteRecurso'), $('o15_recurso'), $('o15_descr'), {
        'sArquivo': 'func_fonterecursocomplemento.php',
        'sLabel': 'Pesquisar Fonte de Recurso',
        'sObjetoLookUp': "db_iframe_orctiporec",
        'aCamposAdicionais': ['db_codigo', 'o200_descricao']
    });

    lookUpRecurso.setCallBack('onClick', (retorno) => {
        preencheForm(retorno[0], retorno[1], retorno[2], retorno[3]);
    });

    lookUpRecurso.setCallBack('onChange', (erro, retorno) => {
        if (erro) {
            return;
        }

        preencheForm(retorno[3], retorno[0], retorno[2], retorno[4]);
    });

    const preencheForm = (recurso, descricao, id, complemento) => {
        $('o15_codigo').value = id;
        $('o15_recurso').value = recurso;
        $('o15_descr').value = descricao;
        $('o200_descricao').value = complemento
    };
</script>
</html>
