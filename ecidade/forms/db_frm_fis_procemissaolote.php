<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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

//MODULO: fiscal
require_once(modification("classes/db_fis_tipofiscaliza_classe.php"));
require_once(modification("classes/db_db_depart_classe.php"));
require_once(modification("classes/db_fis_autotipo_classe.php"));
require_once(modification("classes/db_fis_fiscalprocrec_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));

$cltipofiscaliza = new cl_fis_tipofiscaliza;
$cldb_depart     = new cl_db_depart;
$clautotipo      = new cl_fis_autotipo;
$clfiscalprocrec = new cl_fis_fiscalprocrec;
$clfandam        = new cl_fis_fandam;

$clautotipo->rotulo->label();
$clfandam->rotulo->label();

$clrotulo = new rotulocampo;


$y122_data_dia = date("d",db_getsession("DB_datausu"));
$y122_data_mes = date("m",db_getsession("DB_datausu"));
$y122_data_ano = date("Y",db_getsession("DB_datausu"));


db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js,dbautocomplete.widget.js");
db_app::load("dbmessageBoard.widget.js, prototype.js, dbtextField.widget.js, dbcomboBox.widget.js");
db_app::load("estilos.css, grid.style.css, AjaxRequest.js");
?>
<form name="form1" method="post" action="">
  <fieldset style="margin: 40px auto 10px; width: 700px;">
    <legend>
      <strong>Emissão em Lote</strong>
    </legend>
  <table border="0" width="100%">
  <tr>
    <td nowrap title="Lote">
    <?php 
      db_ancora("<b>Lote:</b>","js_lote(true);",1);
    ?>
    </td> 
    <td>  
    <?php 
      db_input('y122_codigo',6,'',true,'text',1," onchange='js_lote(false);'");
      db_input('y122_descr',40,'',true,'text',3);
    ?>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <table width='100%'>
        <tr>
          <td rowspan='1'  valign='top' height='100%'>
            <fieldset><legend><b>Registros do Lote: </legend>
              <div id='ctnGriddadoslote' style='width: 100%; -moz-user-select: none'></div>
            </fieldset>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <?php 
  
  db_input('linha', 40, @$linha, true, 'hidden', 3, '' );
  db_input('tipoPeca', 20, $tipoPeca, true, 'hidden', 3, '');
  db_input('pecafiscal', 20, $pecafiscal, true, 'hidden', 3, '');

  ?>
    <tr>
    <td width="50px">
    <input type="checkbox" name="levantamento" value="1">
    </td>
    <td><b>Levantamento</b></td>
    </tr>
    <tr>
    <td width="50px">
    <input type="checkbox" name="objetofiscal" value="1">
    </td>
    <td><b>Auto/Notificação</b></td>
    </tr>
  </table>
  </fieldset>

  <center>
    <input name="imprimir" type="submit" id="imprimir" value="Imprimir">
  </center>

</form>
<script type="text/javascript">

function js_lote(lMostra) {

    if (lMostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?funcao_js=parent.js_preencheLote|t43_codlote|y29_tipofisc|tipopeca|y122_pecafiscal', 'Pesquisa Lote', true);
    } else {
      if (document.form1.y122_codigo.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?pesquisa_chave=' +document.form1.y122_codigo.value+ '&funcao_js=parent.js_mostraLote', 'Pesquisa Lote', false);
      } else {
        document.form1.y122_descr.value = '';
        js_limpacampos();
      }
    }

}
function js_limpacampos(){
  oDBGridDadosLote.clearAll(true);
}

function js_preencheLote(codigo, descricao, objpeca, objpecafiscal) {
  document.form1.y122_codigo.value = codigo;
  document.form1.y122_descr.value  = descricao;
  document.form1.tipoPeca.value    = objpeca;
  document.form1.pecafiscal.value  = objpecafiscal;

  js_limpacampos();
  db_iframe_lote.hide();
  js_consultaLote(codigo);
}

function js_mostraLote(sDescricao, objpeca, objpecafiscal, lErro) {

  if (lErro) {
    document.form1.y122_codigo.value = "";
    document.form1.y122_descr.value  = "";
    document.form1.tipoPeca.value    = "";
    document.form1.linha.value       = "";
    document.form1.pecafiscal.value  = "";
    return false; 
  }
  
  document.form1.y122_codigo.focus();
  document.form1.y122_descr.value  = sDescricao;
  document.form1.tipoPeca.value    = objpeca;
  document.form1.pecafiscal.value  = objpecafiscal;

  js_limpacampos();
  js_consultaLote(document.form1.y122_codigo.value);
}

function js_consultaLote(iCodLote) {
  js_divCarregando("Aguarde, efetuando pesquisa","msgBox");
  strJson = '{"exec":"getDadosLoteImpressao","iLote":'+iCodLote+'}';
  var url     = 'fis4_fis_levantlotearq.RPC.php';
  var oAjax   = new Ajax.Request(
                                 url,
                                {
                                 method: 'post',
                                 parameters: 'json='+strJson,
                                 onComplete: js_janelaSolicitacao
                                }
                               );

}

function js_janelaSolicitacao(oAjax) {
  oDBGridDadosLote.clearAll(true);
  var obj = JSON.parse(oAjax.responseText);
  if (obj.status && obj.status == 2) {
    js_removeObj("msgBox");
    alert(obj.sMensagem.urlDecode());
    return false ;
  }

  var iErros      = new Number(0);
  var saida       = "";
  var lhabilitado = "";
//  var lhabilitado = " disabled ";
  var lDisabled = true;
  if (obj) {

    var aLinha = new Array();
    var objpeca = "";
    for (var iInd = 0; iInd < obj.itenslote.length; iInd++) {

      with (obj.itenslote[iInd]) {

        aLinha[0]  = "<input "+lhabilitado+" name='chk"+iInd+"' id='chk"+iInd+"' value='chk-"+codigo+"||"+procfiscal+"||"+levanta+"' class='chkmarca' size=1 type='checkbox'>";
        aLinha[1]  = codigo;
        aLinha[2]  = z01_nome.urlDecode();
        aLinha[3]  = descandam.urlDecode();
        aLinha[4]  = dataemissao;
        oDBGridDadosLote.addRow(aLinha);
        oDBGridDadosLote.aRows[iInd].isSelected = true;
        objpeca = tipopeca;
      }

    }
    document.form1.linha.value    = obj.itenslote.length;
    oDBGridDadosLote.renderRows();
  }
  js_removeObj("msgBox");
  oWindowSolicitacao.show(60, 90);
}

function js_marca(){
  for (var a=0; a < document.form1.linha.value ; a++) {
    if(document.getElementById('chk'+a).disabled==false){
      if(document.getElementById('chk'+a).checked==false){
        document.getElementById('chk'+a).checked = true;
      }else{
        document.getElementById('chk'+a).checked = false;
      }
    }
  }
}

function js_init(){
  oDBGridDadosLote                = new DBGrid('oDBGridDadosLote');
  oDBGridDadosLote.nameInstance   = 'oDBGridDadosLote';
  oDBGridDadosLote.hasTotalizador = true;
  aHeader     = new Array();
  aHeader[0]  = "<a style=\"color:black;text-decoration:none;\" href=\"javascript:js_marca()\">M</a>";
  aHeader[1]  = 'Código';
  aHeader[2]  = 'Nome';
  aHeader[3]  = 'Andamento';
  aHeader[4]  = 'Emitido em:';

  oDBGridDadosLote.setHeader(aHeader);
 // oDBGridDadosLote.setHeight(200);
  oDBGridDadosLote.setCellWidth(['5%', '10%', '35%', '35%', '15%']);
  var aAligns = new Array();
  aAligns[0]  = 'center';
  aAligns[1]  = 'center';
  aAligns[2]  = 'center';
  aAligns[3]  = 'center';
  aAligns[4]  = 'center';

  oDBGridDadosLote.setCellAlign(aAligns);
  oDBGridDadosLote.show($('ctnGriddadoslote'));

  document.form1.y122_codigo.value = "";
  document.form1.y122_descr.value  = "";
  document.form1.tipoPeca.value    = "";
  document.form1.linha.value       = "";
  document.form1.pecafiscal.value  = "";
}

js_init();


</script>