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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_saltes_classe.php"));
require_once(modification("classes/db_corrente_classe.php"));

$oGet = db_utils::postMemory($_GET);

$clsaltes   = new cl_saltes;
$clcorrente = new cl_corrente;
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_GET_VARS);
$db_opcao = 1;
$db_botao = true;

?>
<html>
<head>
	<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Expires" CONTENT="0">
	<?php
    db_app::load("scripts.js, prototype.js, strings.js, arrays.js, dbcomboBox.widget.js"); 
    db_app::load("estilos.css");
  ?>
</head>
<body>
<div class="container">
  <label> 
    <b>Conciliação Bancária</b>
  </label>
  <form name="form1" method="post" action="">
    <fieldset>
      <legend> Dados da conciliação:</legend>
      <table class="form-container">
        <tr>
          <td>Contas:</td>
          <td id="ctnCboContas"></td>
        </tr>
        <tr>
          <td>Datas disponíveis para conciliação:</td>
          <td id="ctnCboDatas"></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Layout:</td>
          <td id="ctnCboLayout"></td>
        </tr>
        <tr id='tipo_emissao' style="display: none;">
          <td>Tipo de Emissão:</td>
          <td id="ctnCboTipoEmissao"></td>
        </tr>
        <tr id='justificativa' style="display: none;">
          <td>Justificativa:</td>
          <td id="ctnCboJustificativa"></td>
        </tr>
        <tr id='digita_data' >
          <td nowrap='nowrap' >Data para Emissão:</td>
          <td nowrap='nowrap' id='dataemissao'>
            <?php 
               $dataemissao_dia = (!isset($dataemissao_dia)?null:$dataemissao_dia);
               $dataemissao_mes = (!isset($dataemissao_mes)?null:$dataemissao_mes);
               $dataemissao_ano = (!isset($dataemissao_ano)?null:$dataemissao_ano);
               db_inputdata('dataemissao',$dataemissao_dia,$dataemissao_mes,$dataemissao_ano,true,( isset($dia)?'text':'hidden'),2);
            ?>
          </td>
        </tr>
      </table>
    </fieldset>
    <input name="continuar" type="Button" id="continuar" value="Imprimir" onClick='js_abreConciliacao();' >
  </form>
</div>
<?php
if (!isset($oGet->concilia)) {
  db_menu();
}
?>
</body>
</html>
<script>

var oUrl = js_urlToObject();
var sRpc  = 'cai4_relconciliacao.RPC_manual.php';

var oCboContas = new DBComboBox("cboContas", "oCboContas", null, "400px");
oCboContas.addItem("", "Selecione uma Conta");
oCboContas.addEvent("onChange", "js_pesquisaDatas();");
oCboContas.show($('ctnCboContas'));

var oCboDatas = new DBComboBox("cboDatas", "oCboDatas", null, "400px");
oCboDatas.addItem("", "Selecione uma Data");
oCboDatas.show($('ctnCboDatas'));

var oCboLayout = new DBComboBox("cboLayout", "oCboLayout", null, "400px");
oCboLayout.addItem("1", "1 - Modelo Simplificado (Padrão)");
oCboLayout.addItem("2", "2 - Modelo Detalhado");
oCboLayout.addEvent("onChange", "js_verificaLayout();");
oCboLayout.show($('ctnCboLayout'));

var oCboTipoEmissao = new DBComboBox("cboTipoEmissao", "oCboTipoEmissao", null, "400px");
oCboTipoEmissao.addItem("f", "Sintético");
oCboTipoEmissao.addItem("t", "Analítico");
oCboTipoEmissao.addEvent("onChange", "js_verificaTipoEmissao();");
oCboTipoEmissao.show($('ctnCboTipoEmissao'));

var oCboJustificativa = new DBComboBox("cboJustificativa", "oCboJustificativa", null, "400px");
oCboJustificativa.addItem("1", "Sim");
oCboJustificativa.addItem("0", "Não");
oCboJustificativa.setValue(1);
oCboJustificativa.show($('ctnCboJustificativa'));
js_pesquisaContas();

/**
 * Pesquisa as contas
 * se vier o codigo da conciliacao, busca somente a conta da conciliacao
 */
function js_pesquisaContas() {

  var oObject          = new Object();
  oObject.exec         = "buscaContas";

  if (oUrl.concilia && oUrl.concilia != "") {
    oObject.concilia   = oUrl.concilia;
  }

 // if (oUrl.dia && oUrl.dia != "") {
 //   oObject.dia = oUrl.dia;
 // }
  
  
  //js_divCarregando('Buscando Contas ...','msgBox');
  var objAjax   = new Ajax.Request (sRpc,{
                                         method:'post',
                                         parameters:'json='+Object.toJSON(oObject), 
                                         onComplete:js_retornoContas
                                        }
                                   );
}

function js_retornoContas(oJson) {

  var oRetorno = JSON.parse(oJson.responseText);

  oCboContas.clearItens();
  oCboContas.addItem("", "Selecione uma Conta");
  oRetorno.dados.each(function(oLinha, iContador) {
    
    oCboContas.addItem(oLinha.sequencial, oLinha.descricao.urlDecode());
  });

  if (oRetorno.dados.length == 1) {

    oCboContas.setValue(oRetorno.dados[0].sequencial);
    oCboContas.setDisable(true);
    js_pesquisaDatas();
  }
  
}

/**
 * Pesquisa as datas para a conta selecionada
 * Possui duas formas de apresentacao
 * >>>> 1 - Retorna a data do ultimo movimento do mes/ano e apresenta sempre o ultimo dia do mes
 * >>>> 2 - Retorno as datas de todos movimentos dos meses/ano apresentando a data real 
 */
function js_pesquisaDatas() {

  var oObject    = new Object();
  oObject.exec   = 'buscaData';

  if (oUrl.concilia && oUrl.concilia != "") {
    oObject.concilia = oUrl.concilia;
  }
  oObject.dia   = 'true';
  oObject.conta  = $F("cboContas");

  
//js_divCarregando('Buscando datas disponiveis ...','msgBox');
  var objAjax   = new Ajax.Request (sRpc,{
                                         method:'post',
                                         parameters:'json='+Object.toJSON(oObject), 
                                         onComplete:js_retornoDatas
                                        }
                                   );
  
} 

function js_retornoDatas(oJson) {

  var oRetorno = JSON.parse(oJson.responseText);
  oCboDatas.clearItens();
  oCboDatas.addItem("", "Selecione uma Data");

  oRetorno.dados.each(function(oData, iContador) {

    var sDataUser        = oData.dia+"/"+oData.mes+"/"+oData.ano;

    //if (typeof(oUrl.concilia) == 'undefined' && typeof(oUrl.dia) == 'undefined') {
    //  sDataUser        = js_getUltimoDiaMes(oData.mes, oData.ano)+"/"+oData.mes+"/"+oData.ano;
   // }
    
    sDataConciliacao = oData.ano+"-"+oData.mes+"-"+oData.dia;
    oCboDatas.addItem(sDataConciliacao, sDataUser);
  });

  $('cboDatas').removeAttribute('disabled');
  $('cboDatas').style.backgroundColor = "#FFF";

  if (oRetorno.dados.length == 1) {

    oCboDatas.setValue(sDataConciliacao);
    oCboDatas.setDisable(true);
  }   
}


js_verificaLayout();
function js_verificaLayout() {
  if ($F('cboLayout') == '1') {
    $('tipo_emissao').style.display = 'table-row';
  } else {
	$('tipo_emissao').style.display = 'none';
	js_verificaTipoEmissao();
  }	
}

/**
 * Se selecionado a forma analitica de impresao do relatorio, apresenta opcao de apresentar justificativa
 */
function js_verificaTipoEmissao() {

  if ($F('cboTipoEmissao') == 't' || $F('cboLayout') == '2') {
    $('justificativa').style.display = 'table-row';
  } else {
    $('justificativa').style.display = 'none';
  }
}
    
function js_getUltimoDiaMes(iMes, iAno) {
  if (checkleapyear(iAno)) {
    var fev = 29;
  }else{
    var fev = 28;
  } 
  //                  01  02 03 04 05 06 07 08 09 10 11 12 
  var dia = new Array(31,fev,31,30,31,30,31,31,30,31,30,31);
  return dia[iMes - 1];
}

/**
 * Imprime o relatorio
 */
function js_abreConciliacao() {

  if (oCboContas.getValue() == '' &&  oCboDatas.getValue() == '' && document.form1.dataemissao.value == ''  ) {

    alert('Selecione uma conta e uma data');
    return false;
  }

  if( document.form1.dataemissao.value != ""){
    var sData  = document.form1.dataemissao_ano.value+'-'+document.form1.dataemissao_mes.value+'-'+document.form1.dataemissao_dia.value;
    var sDataApresentacao =  document.form1.dataemissao.value;
  }else{
    var sData             = oCboDatas.getValue();         
    var sDataApresentacao = oCboDatas.getLabel();
  }

  var iConta            = oCboContas.getValue();        
  var analitico         = oCboTipoEmissao.getValue();   
  var justificativa     = oCboJustificativa.getValue(); 

  var sUrl = 'cai4_relatorioconciliacao_manual.php?';
  if (oCboLayout.getValue() == 2) {
	sUrl = 'cai4_relatorioconciliacaomanual002_layout2.php?';
  }
  var sParametro        = 'sDataConciliacao='+sData+'&iConta='+iConta;
  sParametro           += "&datausuario="+sDataApresentacao;
  sParametro           += '&justificativa='+justificativa; 
  sParametro           += '&analitico='+analitico;
  if (typeof(oUrl.concilia) == 'undefined') {
    sParametro +='&lReemissao=true';
  } else {
    sParametro +='&concilia='+oUrl.concilia;
  }
  var oJanela           = window.open(sUrl+sParametro,'', 'location=0'); 
}
</script>
