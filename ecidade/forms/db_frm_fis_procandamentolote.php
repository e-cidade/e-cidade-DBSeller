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
$clrotulo->label("y59_valor");
$clrotulo->label("y59_fator");
$clrotulo->label("y29_descr");

$y122_data_dia = date("d",db_getsession("DB_datausu"));
$y122_data_mes = date("m",db_getsession("DB_datausu"));
$y122_data_ano = date("Y",db_getsession("DB_datausu"));
$y122_data = $y122_data_ano.'-'.$y122_data_mes.'-'.$y122_data_dia;

db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js,dbautocomplete.widget.js");
db_app::load("dbmessageBoard.widget.js, prototype.js, dbtextField.widget.js, dbcomboBox.widget.js");
db_app::load("estilos.css, grid.style.css, AjaxRequest.js");
?>
<form name="form1" method="post" action="">
  <fieldset style="margin: 40px auto 10px; width: 700px;">
    <legend>
      <strong>Andamento em Lote</strong>
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
      db_input('y122_codigo',10,'',true,'text',1," onchange='js_lote(false);'");
      db_input('y122_descr',54,'',true,'text',3);
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty39_codtipo?>">
       <?php 
       db_ancora(@$Ly39_codtipo,"js_pesquisay39_codtipo(true);",$db_opcao);
       ?>
    </td>
    <td>
      <?php 
      db_input('y39_codtipo',10,$Iy39_codtipo,true,'text',$db_opcao," onchange='js_pesquisay39_codtipo(false);'")
      ?>
      <?php 
      db_input('y41_descr',54,$Iy41_descr,true,'text',3,'')
      ?>
      <?php 
      db_input('y41_permcalc',10,$Iy41_permcalc,true,'hidden',3,'')
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
  <tr>
    <td nowrap title="<?=@$Ty39_obs?>">
       <?=@$Ly39_obs?>
    </td>
    <td>
    <?php 
    db_textarea('y39_obs',3,65,$Iy39_obs,true,'text',$db_opcao,"")
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty39_id_usuario?>">
    </td>
    <td>
    <?php 
    db_input('y39_id_usuario',5,$Iy39_id_usuario,true,'hidden',$db_opcao,"");
    echo "<script>document.form1.y39_id_usuario.value = '".db_getsession("DB_id_usuario")."'</script>";
    ?>
    <?php 
    db_input('nome',20,$Inome,true,'hidden',3,'')
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty60_proces?>">
    <?php 
    db_ancora("<b>Processo:</b>",' js_mostracodproc(true); ',4);
    ?>
    </td>
    <td>
    <?php 
    db_input('processoano',14,$Ip58_numero,true,'text',4,'onchange="js_mostracodproc(false);"',"","","",14);
    db_input('y60_proces',10,$Iy60_proces,true,'hidden',4,'');

    db_input('p58_requer',50,$Ip58_requer,true,'text',3,'');
    db_input('pa01_codigo',5,$Ipa01_codigo,true,'hidden',3,'');
    ?>

    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty39_hora?>">
       <?=@$Ly39_hora?>
    </td>
    <td>
    <?php
    if ($y39_hora == '' or $y39_hora == null) {
      $y39_hora = date('H:i');
    }
    db_input('y39_hora',5,$Iy39_hora,true,'text',$db_opcao,"");
    ?>
    </td>
  </tr>
  <?php db_input('sequencial', 20, $sequencial, true, 'hidden', 3, ''); ?>

  <?php 
    $rsDiasParam = db_query("select nl27_autodvenc as pa01_autodvenc,nl27_autodprazo as pa01_autodprazo from fiscalizacao.fis_parnotificacaolancamento where nl27_instit = ".db_getsession('DB_instit'));
    if(pg_num_rows($rsDiasParam) > 0)
      db_fieldsmemory($rsDiasParam,0);
      db_input('pa01_autodvenc', 20, $se, true, 'hidden', 3, '');
      db_input('pa01_autodprazo', 20, $s, true, 'hidden', 3, '');
      ?>
      <tr id="tr_data_ciencia" style="display: none;">
      <td><b>Data de ciência:</b></td>
      <td align="left">
      <?php
      db_inputdata('data_ciencia',@$data_ciencia_dia,@$data_ciencia_mes,@$data_ciencia_ano,true,'text',$db_opcao,"onchange='soma30dias();'","","","parent.soma30dias();");
        //db_inputdata('data_ciencia',@$data_ciencia_dia,@$data_ciencia_mes,@$data_ciencia_ano,true,'text',$db_opcao,"onchange='soma30dias();'","","","parent.somadata2();");
      ?>
    </td>
  </tr>
  <tr id="div_data" style="display:none;">
    <td><strong>Data de vencimento:</strong> </td>
    <td align="left">
    <?php 
    db_inputdata('y50_dtvenc',@$y50_dtvenc_dia,@$y50_dtvenc_mes,@$y50_dtvenc_ano,true,'text',3,"")
    ?>
    </td>
  </tr>

        <!--<tr>
          <td width="175"><strong>Data de Vencimento:</strong> </td>
          <td>
              <?php 
  db_inputdata('y50_dtvenc_tmp',@$y50_dtvenc_tmp_dia,@$y50_dtvenc_tmp_mes,@$y50_dtvenc_tmp_ano,true,'text',3,"")
               ?>
          </td>
        </tr>-->
  <tr id="prazorec" style="display:none;">
    <td ><strong>Data do prazo de recurso:</strong> </td>
    <td align="left">
    <?php 
    db_inputdata('y50_prazorec',@$y50_prazorec_dia,@$y50_prazorec_mes,@$y50_prazorec_ano,true,'text',3,"")
    ?>
    </td>
  </tr>


  <?php 
  db_input('linha', 40, @$linha, true, 'hidden', 3, '' );
  db_input('tipoPeca', 20, $tipoPeca, true, 'hidden', 3, '');
  db_input('pecafiscal', 20, $pecafiscal, true, 'hidden', 3, '');

  ?>
  </table>
  </fieldset>
<!-- <input name="processar" type="submit" id="processar" value="Processar">  -->
  <?php 
  if(isset($processar)) {
  ?>

  <?php 
  } else {
  ?>
  <center>
    <input name="processar" type="submit" id="processar" value="Processar">
  </center>
  <?php 
  }
  ?>

</form>
<script type="text/javascript">
document.form1.y50_dtvenc.value = '';
document.form1.y50_prazorec.value = '';

function js_lote(lMostra) {

    if (lMostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?funcao_js=parent.js_preencheLote|t43_codlote|y29_tipofisc|tipopeca|y122_pecafiscal', 'Pesquisa Lote', true);
    } else {
      if (document.form1.y122_codigo.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?pesquisa_chave=' +document.form1.y122_codigo.value+ '&funcao_js=parent.js_mostraLote', 'Pesquisa Lote', false);
      } else {
        document.form1.y122_descr.value = '';
      }
    }
}
function js_limpacampos(){
  document.form1.y39_codtipo.value  = '';
  document.form1.y41_descr.value    = '';
  document.form1.y41_permcalc.value = '';
  oDBGridDadosLote.clearAll(true);
}

function js_preencheLote(codigo, descricao, objpeca, objpecafiscal) {
  document.form1.y122_codigo.value = codigo;
  document.form1.y122_descr.value  = descricao;
  document.form1.tipoPeca.value    = objpeca;
  document.form1.pecafiscal.value  = objpecafiscal;
  js_limpacampos();
  db_iframe_lote.hide();
}

function js_mostraLote(sDescricao, objpeca, objpecafiscal, lErro) {

  if (lErro) {
    document.form1.y122_codigo.value = "";
    document.form1.y122_descr.value  = "";
    document.form1.tipoPeca.value    = "";
    document.form1.linha.value       = "";
    document.form1.tipoPeca.value    = "";
    document.form1.pecafiscal.value  = "";
    return false;
  }

  document.form1.y122_codigo.focus();
  document.form1.y122_descr.value  = sDescricao;
  document.form1.tipoPeca.value    = objpeca;
  document.form1.pecafiscal.value  = objpecafiscal;
  js_limpacampos();
}

function js_consultaLote(iCodLote) {
  js_divCarregando("Aguarde, efetuando pesquisa","msgBox");
  strJson = '{"exec":"getDadosLote","iLote":'+iCodLote+'}';
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

        if (document.form1.y39_codtipo.value == tipoandam){
          lhabilitado = " disabled ";
        } else {
          lhabilitado = "";
        }

        aLinha[0]  = "<input "+lhabilitado+" name='chk"+iInd+"' id='chk"+iInd+"' value='chk-"+codigo+"' class='chkmarca' size=1 type='checkbox'>";
        aLinha[1]  = codigo;
        aLinha[2]  = inscr;
        aLinha[3]  = z01_nome.urlDecode();
        aLinha[4]  = descandam.urlDecode();
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
  aHeader[2]  = 'Inscrição';
  aHeader[3]  = 'Nome';
  aHeader[4]  = 'Andamento';

  oDBGridDadosLote.setHeader(aHeader);
 // oDBGridDadosLote.setHeight(200);
  oDBGridDadosLote.setCellWidth(['5%', '10%', '15%', '35%', '35%']);
  var aAligns = new Array();
  aAligns[0]  = 'center';
  aAligns[1]  = 'center';
  aAligns[2]  = 'center';
  aAligns[3]  = 'left';
  aAligns[4]  = 'left';
  oDBGridDadosLote.setCellAlign(aAligns);
  oDBGridDadosLote.show($('ctnGriddadoslote'));
}

js_init();

function js_pesquisay39_codtipo(mostra){
  oDBGridDadosLote.clearAll(true);
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_tipoandam','func_fis_tipoandamentos.php?funcao_js=parent.js_mostratipoandam1|y41_codtipo|dl_data_ciencia_andamento|y41_permcalc|y41_descr<?=$getIntimacao?>&tipoPeca='+document.form1.tipoPeca.value+'&codigo=<?=$codigo?>','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_tipoandam','func_fis_tipoandamentos.php?pesquisa_chave='+document.form1.y39_codtipo.value+'&funcao_js=parent.js_mostratipoandam<?=$getIntimacao?>&tipoPeca='+document.form1.tipoPeca.value+'&codigo=<?=$codigo?>','Pesquisa',false);
  }
}
function js_mostratipoandam(chave,chave2,chave3,erro){
  document.form1.y41_descr.value = chave;
  document.form1.y41_permcalc.value = chave3;

  if (chave2 == 't') {
    document.getElementById('tr_data_ciencia').removeAttribute('style');
    //js_consultaformula();
  }
  if(erro==true){
    document.form1.y39_codtipo.focus();
    document.form1.y39_codtipo.value = '';
    document.form1.y41_descr.value = '';
    return false;
  }
  js_consultaformula();
}
function js_mostratipoandam1(chave1,chave2,chave3,chave4){
  document.form1.y39_codtipo.value = chave1;
  document.form1.y41_permcalc.value = chave3;
  document.form1.y41_descr.value = chave4;
  if (chave2 == 't') {
    document.getElementById('tr_data_ciencia').removeAttribute('style');
    //js_consultaformula();
  }
  js_consultaformula();
  db_iframe_tipoandam.hide();
}
function js_pesquisay39_id_usuario(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_db_usuarios','func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_db_usuarios','func_db_usuarios.php?pesquisa_chave='+document.form1.y39_id_usuario.value+'&funcao_js=parent.js_mostradb_usuarios','Pesquisa',false);
  }
}
function js_mostradb_usuarios(chave,erro){
  document.form1.nome.value = chave;
  if(erro==true){
    document.form1.y39_id_usuario.focus();
    document.form1.y39_id_usuario.value = '';
  }
}
function js_mostradb_usuarios1(chave1,chave2){
  document.form1.y39_id_usuario.value = chave1;
  document.form1.nome.value = chave2;
  db_iframe_db_usuarios.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_fandam','func_<?=(!isset($pesqandam) && !isset($auto)?"fis_vistoriaandam.php":(!isset($auto)?"fis_fiscalandam.php":"fis_autoandam.php"))?>?funcao_js=parent.js_preenchepesquisa|y39_codandam<?=$getIntimacao?>','Pesquisa',true);
}

function js_consultaformula(){
  var oConsulta = document.form1.y41_permcalc.value;

  if (oConsulta == 't'){
    document.getElementById('div_data').removeAttribute('style');
    document.getElementById('prazorec').removeAttribute('style');
    document.getElementById('y50_dtvenc').disabled = false;
    document.getElementById('y50_dtvenc_dia').disabled = false;
    document.getElementById('y50_dtvenc_mes').disabled = false;
    document.getElementById('y50_dtvenc_ano').disabled = false;
    document.getElementById('y50_prazorec').disabled = false;
    document.getElementById('y50_prazorec_dia').disabled = false;
    document.getElementById('y50_prazorec_mes').disabled = false;
    document.getElementById('y50_prazorec_ano').disabled = false;
  }else{
    document.getElementById('div_data').style.display = "none";
    document.getElementById('prazorec').style.display = "none";
    document.getElementById('y50_dtvenc').disabled = true;
    document.getElementById('y50_dtvenc_dia').disabled = true;
    document.getElementById('y50_dtvenc_mes').disabled = true;
    document.getElementById('y50_dtvenc_ano').disabled = true;
    document.getElementById('y50_prazorec').disabled = true;
    document.getElementById('y50_prazorec_dia').disabled = true;
    document.getElementById('y50_prazorec_mes').disabled = true;
    document.getElementById('y50_prazorec_ano').disabled = true;
  }

  if(document.form1.y41_permcalc.value != "undefined"){
    var codigolote = document.form1.y122_codigo.value;
    js_consultaLote(codigolote);
  }
}

function js_mostracodproc(mostra){
   <?php 
    if (isset($ProcFiscal) && $ProcFiscal != ""){

  ?>
    url= '&ProcFiscal='+<?php  echo $ProcFiscal?>;
  <?php 
  }else{
    ?>
    url = '';
    <?php 
   }?>

 if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo_andamentos.php?funcao_js=parent.js_mostraproc1|p58_codproc|p58_numero|z01_nome'+url,'Pesquisa',true,'15');
  }else{
        js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo_andamentos.php?pesquisa_chave='+document.form1.processoano.value+'&funcao_js=parent.js_mostraproc&chave_p58_numero=1'+url,'Pesquisa',false);
  }
}
function js_mostraproc(chave,obs,erro){
  if(erro==true){
    document.form1.processoano.focus();
    document.form1.y60_proces.value = '';
    document.form1.processoano.value = '';
    document.form1.p58_requer.value = '';
  }else{
    document.form1.p58_requer.value = obs;
    document.form1.y60_proces.value = chave;
  }
}

function js_mostraproc1(chave1,n,z,chave2){
  document.form1.p58_requer.value = z;
  document.form1.y60_proces.value = chave1;
  document.form1.processoano.value = n;
  db_iframe_proc.hide();
}

function somadatafor(dias){
  datahoje = document.form1.data_ciencia.value;
  dia = datahoje.substr(0,2);
  mes = datahoje.substr(3,2);
  ano = datahoje.substr(6,4);

  for(i=0;i<dias;i++){
    if (mes == 01 || mes == 03 || mes == 05 || mes == 07 || mes == 08 || mes == 10 || mes == 12){
      if(mes == 12 && dia == 31){
      mes = 01;
      ano++;
      dia = 00;
      }
    if(dia == 31 && mes != 12){
        mes++;
      dia = 00;
      }
    }

    if(mes == 04 || mes == 06 || mes == 09 || mes == 11){
      if(dia == 30){
      dia = 00;
      mes++;
      }
    }

    if(mes == 02){
      if(ano % 4 == 0 && ano % 100 != 0){
        if(dia == 29){
        dia = 00;
        mes++;
        }
      }
    else{
      if(dia == 28){
      dia = 00;
      mes++;
      }
    }
  }

  dia++;

  }
  dia = dia.toString();
  mes = mes.toString();
  if(dia.length == 1){
    dia = "0"+dia;
  }
  if(mes.length == 1){
    mes = "0"+mes;
  }

  nova_data = dia+"/"+mes+"/"+ano ;
  return nova_data;
}

function somadata(dias,dias2){
  document.form1.y50_prazorec.value = somadatafor(dias);
  document.form1.y50_prazorec_dia.value = dia;
  document.form1.y50_prazorec_mes.value = mes;
  document.form1.y50_prazorec_ano.value = ano;
  document.form1.y50_dtvenc_tmp.value = somadatafor(dias2);
  document.form1.y50_dtvenc_tmp_dia.value = dia;
  document.form1.y50_dtvenc_tmp_mes.value = mes;
  document.form1.y50_dtvenc_tmp_ano.value = ano;
}

function somadata2(){
  document.form1.y50_dtvenc.value = somadatafor(<?php  echo $pa01_autodvenc;?>);
  document.form1.y50_dtvenc_dia.value = dia;
  document.form1.y50_dtvenc_mes.value = mes;
  document.form1.y50_dtvenc_ano.value = ano;
  document.form1.y50_prazorec.value = somadatafor(<?php  echo $pa01_autodprazo;?>);
  document.form1.y50_prazorec_dia.value = dia;
  document.form1.y50_prazorec_mes.value = mes;
  document.form1.y50_prazorec_ano.value = ano;
  js_diaultil();
}

function soma30dias() {
  var dados = somadatafor(30);
  var dataCiencia = ano+"/"+mes+"/"+dia;

  strJson = '{"exec":"getVencimentoLote","iData":"'+dataCiencia+'"}';
  var url     = 'fis4_fis_levantlotearq.RPC.php';
  var oAjax   = new Ajax.Request(
                                 url,
                                {
                                 method: 'post',
                                 parameters: 'json='+strJson,
                                 onComplete: js_retornoDataVencimento
                                }
                               );
}

function js_retornoDataVencimento(oAjax) {
  var oRetorno = eval("("+oAjax.responseText+")");
  dia = oRetorno.proximodiautil.dia;
  mes = oRetorno.proximodiautil.mes;
  ano = oRetorno.proximodiautil.ano;
  document.form1.y50_dtvenc.value = dia+"/"+mes+"/"+ano;
  document.form1.y50_dtvenc_dia.value = dia;
  document.form1.y50_dtvenc_mes.value = mes;
  document.form1.y50_dtvenc_ano.value = ano;
  document.form1.y50_prazorec.value = dia+"/"+mes+"/"+ano;
  document.form1.y50_prazorec_dia.value = dia;
  document.form1.y50_prazorec_mes.value = mes;
  document.form1.y50_prazorec_ano.value = ano;
}

</script>
<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">

var jQuery = $.noConflict();

function js_diaultil(){
  var vencim  = jQuery('#y50_dtvenc').val();
  var prazor  = jQuery('#y50_prazorec').val();
  var ciencia = jQuery('#data_ciencia').val();

  jQuery.ajax({
    type     : 'post',
    url      : 'fis1_fis_diautil.php',
    data     : { vencim : vencim, prazor : prazor, ciencia : ciencia, paramNotificacao : true },
    dataType : 'json',
    success  : function(d){
      if(d.erro != 0){
        alert(d.erro);
      }else{
        jQuery('#y50_dtvenc').val(d.sRetornoV_dia+"/"+d.sRetornoV_mes+"/"+d.sRetornoV_ano);
        jQuery('#y50_dtvenc_ano').val(d.sRetornoV_ano);
        jQuery('#y50_dtvenc_mes').val(d.sRetornoV_mes);
        jQuery('#y50_dtvenc_dia').val(d.sRetornoV_dia);
        jQuery('#y50_prazorec').val(d.sRetornoP_dia+"/"+d.sRetornoP_mes+"/"+d.sRetornoP_ano);
        jQuery('#y50_prazorec_ano').val(d.sRetornoP_ano);
        jQuery('#y50_prazorec_mes').val(d.sRetornoP_mes);
        jQuery('#y50_prazorec_dia').val(d.sRetornoP_dia);
      }
    }
  });
}
 </script>
