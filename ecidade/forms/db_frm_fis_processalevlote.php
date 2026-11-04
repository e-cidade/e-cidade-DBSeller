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

$cltipofiscaliza = new cl_fis_tipofiscaliza;
$cldb_depart     = new cl_db_depart;
$clautotipo      = new cl_fis_autotipo;
$clfiscalprocrec = new cl_fis_fiscalprocrec;

$clautotipo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y59_valor");
$clrotulo->label("y59_fator");
$clrotulo->label("y29_descr");

?>

<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<form name="form1" enctype="multipart/form-data" method="post" action="">
  <fieldset style="margin: 40px auto 10px; width: 700px;">
    <legend>
      <strong>Levantamento Fiscal em Lote</strong>
    </legend>
  <table border="0">
  <tr>
    <td align="left" nowrap><strong>Peça Fiscal: </strong></td>
    <td nowrap>
      <?php 
        $vPeca = array (0 => "Selecione" , 1 => "Auto de Infração", 2 => "Notificação em Lote" );
        db_select ( "y122_pecafiscal", $vPeca, true, 1, "onchange='js_paragrafo(this.value)'" );
      ?>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap><strong>Data do Levantamento:</strong></td>
    <td nowrap>
    <?php 
    db_inputdata('y122_data',@$y39_data_dia,@$y39_data_mes,@$y39_data_ano,true,'text',1,"")
    ?>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap><strong>Hora:</strong></td>
    <td>
    <?php
    db_input('y122_hora',10,'Hora',true,'text',$db_opcao,'OnKeyUp="Mascara_Hora(this.value)"');
    ?>
    </td>
  </tr>
  <tr>
    <td align="left" nowrap><strong>Tipo de Fiscalização:</strong></td>
    <td nowrap colspan="2" >
    <?php 
      db_ancora(@$Ly29_tipofisc,"js_pesquisa_tipofisc(true);",$db_opcao);
      $result_tipofisc=$cltipofiscaliza->sql_record("select * from fiscalizacao.fis_tipofiscaliza inner join fiscalizacao.fis_fisdocdep on
      y27_codtipo = fd02_codtipo and fd02_instit = y27_instit where y27_instit =".db_getsession('DB_instit')." and fd02_coddep =".db_getsession('DB_coddepto'));
      db_selectrecord("y122_tipofiscal",$result_tipofisc,true,$db_opcao);
    ?>
    </td>
  </tr>

    <tr id="listaParagrafoAuto">
        <td><strong>Lista Paragrafos:</strong></td>
        <td>
        <select name="paragrafosAuto" id="paragrafosAuto">
        <option value=""></option>
            <?php
            $pSql  = 'select distinct pl09_paragrafo, pl09_descr from fiscalizacao.fis_paragrafo where pl09_status = true and pl09_tipo = 1  and pl09_coddepto = '.db_getsession('DB_coddepto');
            $pRs   = db_query($pSql);
            $pRows = pg_num_rows($pRs);
            for($i=0; $i<$pRows; $i++){
                db_fieldsmemory($pRs, $i);
                echo '<option value="'.$pl09_paragrafo.'">'.$pl09_descr.'</option>';
            }
            ?>
        </select>
        <input id="addParagrafoAuto" name="addParagrafoAuto" readonly="" type="button" value="Adicionar">
        </td>
    </tr>
    <tr id="listaParagrafoLanc">
                    <td><strong>Lista Paragrafos:</strong></td>
                    <td>
                      <select name="paragrafosLanc" id="paragrafosLanc">
                        <option value=""></option>
                        <?php
                            $pSql  = 'select distinct pl09_paragrafo, pl09_descr from fiscalizacao.fis_paragrafo where pl09_status = true and pl09_tipo = 4  and pl09_coddepto = '.db_getsession('DB_coddepto');


                            $pRs   = db_query($pSql);
                            $pRows = pg_num_rows($pRs);
                            for($i=0; $i<$pRows; $i++){
                                db_fieldsmemory($pRs, $i);
                                echo '<option value="'.$pl09_paragrafo.'">'.$pl09_descr.'</option>';
                            }
                        ?>
                    </select>
                    <input id="addParagrafoLanc" name="addParagrafoLanc" readonly="" type="button" value="Adicionar">
                    </td>
    </tr>
                <?php
                    $textoRelato       = '';
                    $textoInfringencia = '';
                    $textoSancao       = '';
                    $textoBaseLegal    = '';
                    $codRelato         = '';
                    $codInfringencia   = '';
                    $codSancao         = '';
                    $codBaseLegal      = '';
                ?>


                <tr class="trParagrafo" id="paraRelato" <?=(($textoRelato=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="RELATO" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoAuto[]" value="" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codRelato?>" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="1" <?=(($textoRelato=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoRelato=='')?'disabled="disabled"':'')?>><?=$textoRelato?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>


                <tr class="trParagrafo" id="paraInfringencia" <?=(($textoInfringencia=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="INFRINGÊNCIA" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoAuto[]" value="" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codInfringencia?>" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="2" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>><?=$textoInfringencia?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraSancao" <?=(($textoSancao=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="SANÇÃO" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoAuto[]" value="" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codSancao?>" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="3" <?=(($textoSancao=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoSancao=='')?'disabled="disabled"':'')?>><?=$textoSancao?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraBaseLegal" <?=(($textoBaseLegal=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="BASE LEGAL" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoAuto[]" value="" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codBaseLegal?>" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>>
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>><?=$textoBaseLegal?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

    <td align="left" nowrap><strong>Arquivo:</strong></td>
    <td nowrap>
    <?php 
    db_input("arquivo",50,"Arquivo",true,"file",4)
    ?>
  </tr>
  <tr>
    <td align="left" nowrap><strong>Observação:</strong></td>
    <td>
      <?php 
      db_textarea('y122_observacao',1,50,'Observacao',true,'text',1,"","","","",52)
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty59_codtipo?>">
       <?php 
       db_ancora(@$Ly59_codtipo,"js_pesquisay59_codtipo(true);",$db_opcao);
       ?>
    </td>
    <td >
      <?php 
      db_input('y122_procedencia',10,$Iy59_codtipo,true,'text',$db_opcao," onchange='js_pesquisay59_codtipo(false);'");
      db_input('y122_procedencia',10,$Iy59_codtipo,true,'hidden',$db_opcao,"","y59_codtipo_old");
      echo "<script>document.form1.y59_codtipo_old.value='".@$y59_codtipo."'</script>";
      ?>
      <?php 
      db_input('y29_descr',38,$Iy29_descr,true,'text',3,'');
      db_input('y45_percentual',10,$Iy59_codtipo,true,'hidden',3,"");
      db_input('y45_vlrfixo',10,$Iy59_codtipo,true,'hidden',3,"");
      ?>
    </td>
  </tr>
  <tr>
    <td id="labelValor" nowrap title="<?=@$Ty59_valor?>">
       <b>Valor</b>
    </td>
    <td>
    <?php
    $fixo = false;
    if (isset($y122_procedencia)&&$y122_procedencia!=""){

      $res = $clfiscalprocrec->sql_record($clfiscalprocrec->sql_query_file($y122_procedencia));
      if ($clfiscalprocrec->numrows!=0){

        db_fieldsmemory($res,0);
        if ($y45_vlrfixo == 't'){
          $fixo = true;
        }
      }
    }

    $sMascaraValor = "return mascaraValor(event, this);";
    if( isset($y45_percentual) ){
      if( $y45_percentual == 't' ){
        $sMascaraValor = "";
      }
    }

    if( $fixo == true ){
      db_input('y122_valor',10,4,true,'text',3,"");
    }else{
      db_input('y122_valor',10,4,true,'text',$db_opcao,"onkeypress=\"$sMascaraValor\" onchange='js_validaValor();'");
    }
    ?>
    </td>
  </tr>

  </table>
  </fieldset>
<!-- <input name="processar" type="submit" id="processar" value="Processar">  -->
  <?php 
  if(isset($processar) && $sqlerro == false) {
  ?>
<center>
<input name="arq_tmpname" type="hidden" id="arq_tmpname" value="<?=$DOCUMENT_ROOT."/tmp/".$arq_tmpname?>">
</center>
  <?php 
  } else {
  ?>
  <center>
    <input name="processar" type="submit" id="processar" value="Processar">
    <input type="button" id='downXLS' name='downXLS' value='Baixar Planilha Modelo' onclick='js_baixamodelo()'/>
  </center>
  <?php 
  }
  ?>

</form>
<script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
  var sCaminhoMensagens = "tributario.fiscal.db_frmautotipo.";
  var $a = jQuery.noConflict();
  jQuery(document).ready(function($a) {
    $a('body').on('click', '.remover', function() {
      var idTr = $a(this).closest('tr').attr('id');
      $a('#'+idTr).hide();
      $a('#'+idTr).find('input.rm1').val('').attr('disabled','disabled');
      $a('#'+idTr).find('input.rm2').attr('disabled','disabled');
      $a('#'+idTr).find('textarea').val('');
    });

    $a('#addParagrafoAuto').click(function() {
      // function js_pesquisa(){
      var pParagrafo = $a('#paragrafosAuto').val();
      js_OpenJanelaIframe('','db_iframe_addparagrafo','fis1_fis_addparagrago001.php?pParagrafo='+pParagrafo+'&pTipo=1','Pesquisa',true);
    });
    $a('#addParagrafoLanc').click(function() {
      // function js_pesquisa(){
      var pParagrafo = $a('#paragrafosLanc').val();
      js_OpenJanelaIframe('','db_iframe_addparagrafo','fis1_fis_addparagragolanc001.php?pParagrafo='+pParagrafo+'&pTipo=4','Pesquisa',true);
    });
  });

function js_pesquisay59_codtipo(mostra){

  tipofisc = document.form1.y122_tipofiscal.value;
  if(document.form1.y122_pecafiscal.value == '1'){
    var url = 'func_fis_fiscalprocaltauto.php';
  }
  if(document.form1.y122_pecafiscal.value == '2'){
    var url = 'func_fis_fiscalprocaltlancamento.php';
  }
  if(document.form1.y122_pecafiscal.value == '0'){
    tipofisc == "";
    alert('Informe um tipo de fiscalização para o auto!');
    return false;
  }

  if (tipofisc!=""){
    if(mostra==true){
      js_OpenJanelaIframe('','db_iframe',url+'?tipofisc='+tipofisc+'&funcao_js=parent.js_mostrafiscalproc1|y29_codtipo|y29_descr|y45_vlrfixo|y45_valor|y45_percentual','Pesquisa',true);
    }else{
      js_OpenJanelaIframe('','db_iframe',url+'?tipofisc='+tipofisc+'&pesquisa_chave='+document.form1.y122_procedencia.value+'&funcao_js=parent.js_mostrafiscalproc','Pesquisa',false);
    }
  }else{
    alert('Informe um tipo de peça fiscal para o lote!');
  }
}

function js_mostrafiscalproc(chave,erro,fixo,valor,percentual){

  document.form1.y45_percentual.value = percentual;
  document.form1.y29_descr.value      = chave;
  if(erro==true){

    document.form1.y122_procedencia.focus();
    document.form1.y122_procedencia.value = '';
  }else{

    if (fixo=='t'){

      document.form1.y122_valor.value      = valor;
      document.form1.y122_valor.disabled   = "true";
    }else if (fixo=='f'){

      document.form1.y122_valor.value    = valor;
      document.form1.y122_valor.disabled = "";
    }

    js_atribuiMascara( percentual );
  }
}

function js_mostrafiscalproc1(chave1,chave2,fixo,valor,percentual){

  document.form1.y45_percentual.value   = percentual;
  document.form1.y122_procedencia.value = chave1;
  document.form1.y29_descr.value        = chave2;
  document.form1.y45_vlrfixo.value      = fixo;

  if (fixo=='t'){
    document.form1.y122_valor.value    = valor;
    document.form1.y122_valor.disabled = "true";
  }else if (fixo=='f'){
    document.form1.y122_valor.value     = "";
    document.form1.y122_valor.disabled  = "";
    document.getElementById('labelValor').innerHTML = "<b>Valor</b>";
  }

  if (percentual == 't') {
    document.getElementById('labelValor').innerHTML = "<b>Valor (%)</b>";
  } else {
    document.getElementById('labelValor').innerHTML = "<b>Valor (R$)</b>";
  }

  js_atribuiMascara( percentual );
  db_iframe.hide();
}

function js_atribuiMascara( lPercentual ){

  $('y122_valor').stopObserving('keypress');
  $('y122_valor').onkeypress = '';
  if( lPercentual == "f" || lPercentual == ""){

    $('y122_valor').observe('keypress', function(event){
      return mascaraValor(event, $('y122_valor'));
    });
  }
}

function js_validaValor() {

  if ( $F('y45_percentual') == 't') {

    if ($F('y122_valor') > 150 || $F('y122_valor') <= 0) {

      alert( _M( sCaminhoMensagens + 'erro_percentual_invalido') );
      document.form1.y122_valor.value = "";
      return false;
    }
  }
}
function Mascara_Hora(Hora){
  var hora01 = '';
  hora01 = hora01 + Hora;
  if (hora01.length == 2){
  hora01 = hora01 + ':';
    document.form1.y122_hora.value = hora01;
  }
  if (hora01.length == 5){
  Verifica_Hora();
  }
}

function Verifica_Hora(){
  hrs = (document.form1.y122_hora.value.substring(0,2));
  min = (document.form1.y122_hora.value.substring(3,5));

  estado = "";
  if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){
  estado = "errada";
  }

  if (document.form1.y122_hora.value == "") {
    estado = "errada";
  }

  if (estado == "errada") {
    alert("Hora inválida!");
    document.form1.y122_hora.focus();
    document.form1.y122_hora.value = "";
  }
}

function js_paragrafo(pecafiscal){
  if(pecafiscal == '1'){
    $('listaParagrafoAuto').show();
    $('listaParagrafoLanc').hide();
  } else if(pecafiscal == '2'){
    $('listaParagrafoAuto').hide();
    $('listaParagrafoLanc').show();
  } else {
    $('listaParagrafoAuto').hide();
    $('listaParagrafoLanc').hide();
  }
}
js_paragrafo(document.form1.y122_pecafiscal.value);

function js_baixamodelo() {

  var sUrl = "documentos/templates/fiscal/planilha_levantamento_fiscal_massa_iss.xls";
  window.open(sUrl,'_blank');
}
</script>
