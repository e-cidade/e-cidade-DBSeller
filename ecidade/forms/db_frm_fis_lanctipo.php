<?php

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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

include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$cllanctipo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nl01_codlanc");
$clrotulo->label("nl01_nome");
$clrotulo->label("y29_descr");
$clrotulo->label("y39_codandam");
$clrotulo->label("nl18_valor");
$clrotulo->label("nl18_fator");

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

if(isset($opcao) && $opcao == "alterar"){

  $result = $cllanctipo->sql_record($cllanctipo->sql_query_baixa(null,"*",null,"nl18_codlanc=$nl18_codlanc and nl18_codtipo=$db_codigo and nl24_dtbaixa is null"));  
  // if ($cllanctipo->numrows!=0){
  echo "<script>parent.iframe_lanctipo.location.href='fis1_fis_lanctipo002.php?chavepesquisa=$nl18_codlanc&chavepesquisa1=$db_codigo'</script>";
  // }else{
  //   db_msgbox('Procedência já baixada!!');
  //   $nl18_codtipo = "";
  // }
}
if(isset($opcao) && $opcao == "excluir"){
  $result = $cllanctipo->sql_record($cllanctipo->sql_query_baixa(null,"*",null,"nl18_codlanc=$nl18_codlanc and nl18_codtipo=$db_codigo and nl24_dtbaixa is null"));

  // if ($cllanctipo->numrows!=0){
    echo "<script>parent.iframe_lanctipo.location.href='fis1_fis_lanctipo003.php?chavepesquisa=$nl18_codlanc&chavepesquisa1=$db_codigo&sequencial=$db_sequencial'</script>";
  // }else{
    // db_msgbox('Procedência já baixada!!');
    // $nl18_codtipo ="";
  // }
}

$cllanclevanta = db_utils::getDao("fis_lanclevanta");
$sWhere         = " nl15_lancamento = $nl18_codlanc";
$sSql           = $cllanclevanta->sql_query_file( null, "*", null, $sWhere );
$rsSql          = $cllanclevanta->sql_record( $sSql );
if( $cllanclevanta->numrows > 0 ){
  $lBloqueia = 3;
}else{
  $lBloqueia = $db_opcao;
}

$lBloqueia = $db_opcao;

$sqlCodTipo = "SELECT nl01_codtipo from fiscalizacao.fis_lancamento where nl01_codlanc = $nl18_codlanc";
$rsSCodTipo = db_query($sqlCodTipo);
db_fieldsmemory($rsSCodTipo,0);

?>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<center>
<form name="form1" method="post" action="">
<input type='hidden' name='andamento' value=''>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tnl18_codlanc?>">
       <?php 
       db_ancora("Notificação de Lançamento","js_pesquisanl18_codlanc(true);",1);
       ?>
    </td>
    <td>
      <?php 
        db_input('nl18_codlanc',10,'',true,'text',3," onchange='js_pesquisanl18_codlanc(false);'");
        db_input('nl01_codlanc',10,$Inl01_codlanc,true,'hidden',3,"");
        db_input('y45_percentual',10,$Inl01_codlanc,true,'hidden',3,"");
        db_input('y45_vlrfixo',10,$Inl01_codlanc,true,'hidden',3,"");
        db_input('y39_codandam',20,'',true,'hidden',3,"");
        db_input('nl01_nome',40,$Inl01_nome,true,'text',3,'');
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl18_codtipo?>">
       <?php 
       db_ancora("<b>Tipo</b>","js_pesquisanl18_codtipo(true);",$lBloqueia);
       ?>
    </td>
    <td>
      <input type="hidden" name="nl01_codtipo" value="<?=$nl01_codtipo?>">
      <?php 
        db_input('nl18_codtipo',10,'',true,'text',$lBloqueia," onchange='js_pesquisanl18_codtipo(false);'");
        db_input('nl18_codtipo',10,'',true,'hidden',$db_opcao,"","nl18_codtipo_old");
        echo "<script>document.form1.nl18_codtipo_old.value='".@$nl18_codtipo."'</script>";
      ?>
      <?php 
        db_input('y29_descr',40,$Iy29_descr,true,'text',3,'')
      ?>
    </td>
  </tr>
  <tr>
    <td id="labelValor" nowrap title="<?=@$Tnl18_valor?>">
       <b>Valor</b>
    </td>
    <td>
    <?php
      $fixo = false;
      if (isset($nl18_codtipo)&&$nl18_codtipo!=""){
        $res = $clfiscalprocrec->sql_record($clfiscalprocrec->sql_query_file($nl18_codtipo));
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
        db_input('nl18_valor',10,4,true,'text',3,"");
      }else{
        db_input('nl18_valor',10,4,true,'text',$lBloqueia,"onkeypress=\"$sMascaraValor\" onchange='js_validaValor();'");
      }
    ?>
    </td>
  </tr>
  <tr class="hide">
   <td nowrap title="<?=@$Tnl18_tipo?>">
    <?=@$Lnl18_tipo?>
   </td>
   <td>
    <?php 
      $x = array('0'=>'Nenhum','1'=>'Acrescimo','2'=>'Redução');
      db_select('nl18_tipo',$x,true,$db_opcao,"");
     ?>
    </td>
  </tr>
    <tr>
      <td nowrap >
        <?php 
          db_ancora("Multa Fiscal:","js_pesquisanl18_codtipopercentual(true);",1);
        ?>
      </td>
      <td>
      <?php 
        db_input('nl28_codtipo',10,'',true,'text',$db_opcao," onchange='js_pesquisanl18_codtipopercentual(false);'");
        db_input('nl28_codtipo',10,'',true,'hidden',$db_opcao,"","nl28_codtipo_old");
        echo "<script>document.form1.nl28_codtipo_old.value='".@$nl28_codtipo."'</script>";
      ?>
      <?php 
        db_input('y29_descr2',40,$Iy29_descr,true,'text',3,'')
      ?>
      </td>
    </tr>

    <tr>
        <td><b>Valor:</b></td>
        <td><?php  db_input('nl28_valor',10,4,true,'text',$db_opcao,""); ?></td>

    </tr>
    <tr class="hide">
   <td nowrap title="<?=@$Tnl18_fator?>">
     <?=@$Lnl18_fator?>
   </td>
    <td>
  <?php 
  db_input('nl18_fator',8,@$Inl18_fator,true,'text',$db_opcao,"")
  ?>
  </td>
 </tr>
  <tr>
    <td colspan="2" align="center">
      <?php
        if ($db_opcao == 1) {
          $sValorBotao = "Incluir";
        }else if ($db_opcao==2||$db_opcao==22) {
          $sValorBotao = "Alterar";
        } else if ($db_opcao==3||$db_opcao==33) {
          $sValorBotao = "Excluir";
        } else {
          $sValorBotao = $db_opcao;
        }
      ?>
      <input <?php ($db_opcao == 1?"disabled":"")?> name="db_opcao" type="submit" id="db_opcao" onclick="return js_validaFormulario();" value="<?php echo $sValorBotao;?>" <?=($db_botao==false?"disabled":"")?> <?=($db_opcao==1||$db_opcao==2||$db_opcao==22?"":"")?> />
      <?php 
      if(($db_opcao==2||$db_opcao==22||$db_opcao==3||$db_opcao==33)){
      ?>
        <input name="novo" type="button" id="novo" value="Novo" onclick="location.href='fis1_fis_lanctipo001.php?nl18_codlanc=<?=$nl18_codlanc?>'">
      <?php 
      }
      ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
    <?php 
    $sSql = $cllanctipo->sql_query_baixa(""," nl18_codigo as db_sequencial,nl18_codtipo as db_codigo,nl18_codlanc as db_lancamento,y29_descr,y29_descr_obs,nl18_valor as valor, '' as db_tipo ",""," nl18_codlanc = $nl18_codlanc");
    $sSql .= " union all ";
    $sSql .= " SELECT nl28_codigo as db_sequencial, nl28_codtipo AS db_codigo,";
    $sSql .= "   nl28_codlanc AS db_lancamento,";
    $sSql .= "   y29_descr,";
    $sSql .= "   y29_descr_obs,";
    $sSql .= "   nl28_valor AS valor, 'Multa Fiscal' as db_tipo ";
    $sSql .= " FROM fiscalizacao.fis_lancmulta";
    $sSql .= " INNER JOIN fiscalizacao.fis_fiscalproc ON fis_fiscalproc.y29_codtipo = fis_lancmulta.nl28_codtipo";
    $sSql .= " ";
    $sSql .= " WHERE nl28_codlanc = ".$nl18_codlanc;

    $chavepri = array( "db_lancamento" => @$nl18_codlanc,
                       "db_codigo"     => @$nl18_codtipo,
                       "db_sequencial" => @$db_sequencial);

    $cliframe_alterar_excluir->chavepri       = $chavepri;
    $cliframe_alterar_excluir->campos         = "db_sequencial,db_codigo,db_lancamento,y29_descr,y29_descr_obs,valor,db_tipo";
    $cliframe_alterar_excluir->sql            = $sSql;
    $cliframe_alterar_excluir->legenda        = "Procedências da Notificação de Lançamento";
    $cliframe_alterar_excluir->msg_vazio      = "<font size='1'>Nenhuma Procedência Cadastrada!</font>";
    $cliframe_alterar_excluir->textocabec     = "darkblue";
    $cliframe_alterar_excluir->textocorpo     = "black";
    $cliframe_alterar_excluir->fundocabec     = "#aacccc";
    $cliframe_alterar_excluir->fundocorpo     = "#ccddcc";
    $cliframe_alterar_excluir->iframe_height  = "150";
    $cliframe_alterar_excluir->iframe_width   = "750";
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <fieldset>
      <legend align="center"><strong>Escolha um Andamento Padrão</strong></legend>
<?php

$cllanctipo1 = new cl_fis_lanctipo;
$cllancmulta1 = new cl_fis_lancmulta;
$cllancandam = new cl_fis_lancandam;
$cllanctipo1->rotulo->label();

if( !empty($nl18_codlanc) ){

  $sSql = " union all ".$cllancmulta1->sql_query(""," distinct(y41_codtipo),y41_descr,nl28_codlanc ",""," nl28_codlanc = $nl18_codlanc");
    $result = $cllanctipo1->sql_record("select distinct * from ( ".$cllanctipo1->sql_query(""," distinct(y41_codtipo),y41_descr,nl18_codlanc ",""," nl18_codlanc = $nl18_codlanc").$sSql." ) as x ");
}

if($cllanctipo1->numrows > 0){

  db_fieldsmemory($result,0);
  $result1 = $cllancandam->sql_record($cllancandam->sql_query_file("",""," max(nl19_codandam) as nl19_codandam ",""," nl19_codlanc = $nl18_codlanc"));
  if($cllancandam->numrows > 0){

    db_fieldsmemory($result1,0);

    if( !empty($nl19_codandam) ){

      $result1 = $clfandam->sql_record($clfandam->sql_query_file("","*",""," y39_codandam = $nl19_codandam"));
    }

    if($clfandam->numrows > 0){

      db_fieldsmemory($result1,0);
      if($cllanctipo1->numrows == 1){

        if($y41_codtipo != $y39_codtipo){
          db_inicio_transacao();
      	  $clfandam->y39_codtipo = $y41_codtipo;
      	  $clfandam->y39_codandam = $nl19_codandam;
      	  $clfandam->alterar("");
          db_fim_transacao();
	      }
      }

    }else{

      if(isset($y41_codtipo) && $y41_codtipo != ""){

      	db_inicio_transacao();
      	$clfandam->y39_codtipo = $y41_codtipo;
      	$clfandam->y39_obs="0";
      	$clfandam->y39_id_usuario= db_getsession("DB_id_usuario");
      	$clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
      	$clfandam->y39_hora=db_hora();
      	$clfandam->incluir(null);
      	$cllancltandam->nl20_codlanc = $nl18_codlanc;
      	$cllancultandam->nl20_codandam = $clfandam->y39_codandam;
      	$cllancultandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
      	$cllancandam->nl19_codauto = $nl18_codlanc;
      	$cllancandam->nl19_codandam = $clfandam->y39_codandam;
      	$cllancandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
      	$y39_codtipo = $y41_codtipo;
      	db_fim_transacao();
      }
    }
  }else{
    if(isset($y41_codtipo) && $y41_codtipo != ""){

      db_inicio_transacao();
      $clfandam->y39_codtipo = $y41_codtipo;
      $clfandam->y39_obs="0";
      $clfandam->y39_id_usuario= db_getsession("DB_id_usuario");
      $clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
      $clfandam->y39_hora=db_hora();
      $clfandam->incluir("");
      $cllancultandam->nl20_codlanc = $nl18_codlanc;
      $cllancultandam->nl20_codandam = $clfandam->y39_codandam;
      $cllancultandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
      $cllancandam->nl19_codauto = $nl18_codlanc;
      $cllancandam->nl19_codandam = $clfandam->y39_codandam;
      $cllancandam->incluir($nl18_codlanc,$clfandam->y39_codandam);
      $y39_codtipo = $y41_codtipo;
      db_fim_transacao();
    }
  }

  echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
  // echo "<script>parent.iframe_receitas.location.href='fis1_fis_autorec001.php?nl18_codlanc=".$nl18_codlanc."&abas=1';</script>\n";
  echo "<script>parent.iframe_fiscais.location.href='fis1_fis_lancusu001.php?nl14_codlanc=".$nl18_codlanc."&abas=1&y39_codandam=$y39_codandam';</script>\n";
  echo "<table cellpadding='1' cellspacing='2' border='0' width='600'>";
  for($i=0;$i<$cllanctipo1->numrows;$i++){
    db_fieldsmemory($result,$i);
    echo "<tr bgcolor='#999999'>
	    <td align='center' valign='center'>
	      <input  type='radio' name='tipoandam' ".($y41_codtipo == $y39_codtipo?"checked":"")." value='$y41_codtipo' onChange='document.form1.andamento.value=this.value;document.form1.action=\"fis1_fis_lanctipo001.php\";document.form1.submit();'>
	      ".($i == 0?"<script>document.form1.andamento.value='$y41_codtipo'</script>":"")."
	    </td>
	    <td><small>TIPO DE ANDAMENTO</small></td>
	    <td>$y41_descr</td>
	  </tr>";
  }
  echo "</table>";
}
?>
      </fieldset>
    </td>
  </tr>
  </table>
</form>
</center>
<script type="text/javascript">

var sCaminhoMensagens = "tributario.fiscal.db_frmlanctipo.";

function js_validaValor() {

  if ( $F('y45_percentual') == 't') {

    if ($F('nl18_valor') > 100 || $F('nl18_valor') <= 0) {

      alert( _M( sCaminhoMensagens + 'erro_percentual_invalido') );
      document.form1.nl18_valor.value = "";
      return false;
    }
  }
}

function js_validaFormulario(){

  if(document.form1.nl18_codtipo.value != "" ){

    document.form1.nl18_valor.disabled="";

    var oRegex = /Chave\(*[0-9]*\)/;
    if( empty($F('y29_descr')) || oRegex.test($F('y29_descr')) ){
      $('nl18_codtipo').value = null;
    }

    if( !isNumeric( $F('nl18_codtipo') ) || empty($F('nl18_codtipo')) ){

      alert( _M( sCaminhoMensagens + 'procedencia_obrigatorio') );
      return false;
    }

    if( !isNumeric( $F('nl18_valor'), true ) ){

      alert( _M( sCaminhoMensagens + 'valor_invalido') );
      return false;
    }
  }

  return true;
}

function js_setatabulacao(){
  js_tabulacaoforms("form1","nl18_codtipo",true,1,"nl18_codtipo",true);
}

function js_pesquisanl18_codlanc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_lanc','func_fis_lancamento.php?funcao_js=parent.js_mostralanc1|dl_lancamento|dl_descricao','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_lanc','func_fis_lancamento.php?pesquisa_chave='+document.form1.nl18_codlanc.value+'&funcao_js=parent.js_mostralanc','Pesquisa',false);
  }
}
function js_mostralanc(chave,erro){
  document.form1.nl01_nome.value = chave;
  if(erro==true){
    document.form1.nl18_codlanc.focus();
    document.form1.nl18_codlanc.value = '';
  }
}
function js_mostralanc1(chave1,chave2){
  document.form1.nl18_codlanc.value = chave1;
  document.form1.nl01_nome.value = chave2;
  db_iframe_lanc.hide();
}



function js_pesquisanl18_codtipo(mostra){
  
  tipofisc = document.form1.nl01_codtipo.value;
  if (tipofisc!=""){
    if(mostra==true){
      js_OpenJanelaIframe('','db_iframe','func_fis_fiscalprocaltlancamento.php?tipofisc='+tipofisc+'&valor=true&funcao_js=parent.js_mostrafiscalproc1|y29_codtipo|y29_descr|y45_vlrfixo|y45_valor|y45_percentual|dl_Proc','Pesquisa',true);
    }else{
      js_OpenJanelaIframe('','db_iframe','func_fis_fiscalprocaltlancamento.php?tipofisc='+tipofisc+'&pesquisa_chave='+document.form1.nl18_codtipo.value+'&funcao_js=parent.js_mostrafiscalproc','Pesquisa',false);
    }
  }else{
    alert('Informe um tipo de fiscalização para o auto!');
  }
}

function js_mostrafiscalproc(chave,erro,fixo,valor,percentual,usaproc){

  document.form1.y45_percentual.value = percentual;
  document.form1.y29_descr.value      = chave;
  if(erro==true){

    document.form1.nl18_codtipo.focus();
    document.form1.nl18_codtipo.value = '';
  }else{

    if (fixo=='t'){

      document.form1.nl18_valor.value      = valor;
      document.form1.nl18_valor.disabled   = "true";
    }else if (fixo=='f'){

      document.form1.nl18_valor.value    = valor;
      document.form1.nl18_valor.disabled = "";
    }

    js_atribuiMascara( percentual );
    js_OpenJanelaIframe('','db_iframe_lancnome','func_fis_lancamentonome.php?pesquisa_chave='+document.form1.nl18_codlanc.value+'&codtipo='+document.form1.nl18_codtipo.value+'&funcao_js=parent.js_mostrare','Pesquisa',false);
  }
}

function js_mostrafiscalproc1(chave1,chave2,fixo,valor,percentual,usaproc){

  document.form1.y45_percentual.value = percentual;
  document.form1.nl18_codtipo.value    = chave1;
  document.form1.y29_descr.value      = chave2;
  document.form1.y45_vlrfixo.value = fixo;

  if (fixo=='t'){
    document.form1.nl18_valor.value    = valor;
    document.form1.nl18_valor.disabled = "true";
  }else if (fixo=='f'){
    document.form1.nl18_valor.value     = "";
    document.form1.nl18_valor.disabled  = "";
    document.getElementById('labelValor').innerHTML = "<b>Valor</b>";
  }

  if (percentual == 't') {
    document.getElementById('labelValor').innerHTML = "<b>Valor (%)</b>";
  } else {
    document.getElementById('labelValor').innerHTML = "<b>Valor (R$)</b>";
  }

  js_atribuiMascara( percentual );
  js_OpenJanelaIframe('','db_iframe_lancnome','func_fis_lancamentonome.php?pesquisa_chave='+document.form1.nl18_codlanc.value+'&codtipo='+chave1+'&funcao_js=parent.js_mostrare','Pesquisa',false);
  db_iframe.hide();
}

function js_pesquisanl18_codtipopercentual(mostra){
  tipofisc = document.form1.nl01_codtipo.value;
  if (tipofisc!=""){
    codprinc = document.form1.nl18_codtipo.value;
    if(mostra==true){
      js_OpenJanelaIframe('','db_iframe','func_fis_fiscalprocaltlancamento.php?tipofisc='+tipofisc+'&codprinc='+codprinc+'&funcao_js=parent.js_mostrafiscalprocpercentual1|y29_codtipo|y29_descr|y45_vlrfixo|y45_valor|y45_percentual|dl_Proc','Pesquisa',true);
    }else{
      js_OpenJanelaIframe('','db_iframe','func_fis_fiscalprocaltlancamento.php?tipofisc='+tipofisc+'&codprinc='+codprinc+'&pesquisa_chave='+document.form1.nl28_codtipo.value+'&funcao_js=parent.js_mostrafiscalprocpercentual','Pesquisa',false);
    }
  }else{
    alert('Informe um tipo de fiscalização para o auto!');
  }
}

function js_mostrafiscalprocpercentual(chave,erro,fixo,valor,percentual,usaproc){

  document.form1.y29_descr2.value     = chave;
  if(erro==true){
    document.form1.nl28_codtipo.focus();
    document.form1.nl28_codtipo.value = '';
  }

}

function js_mostrafiscalprocpercentual1(chave1,chave2,fixo,valor,percentual,usaproc){

  document.form1.nl28_codtipo.value  = chave1;
  document.form1.y29_descr2.value     = chave2;
  db_iframe.hide();

}


function js_atribuiMascara( lPercentual ){

  $('nl18_valor').stopObserving('keypress');
  $('nl18_valor').onkeypress = '';
  if( lPercentual == "f" || lPercentual == ""){

    $('nl18_valor').observe('keypress', function(event){
      return mascaraValor(event, $('nl18_valor'));
    });
  }
}


function js_mostrare(retorna){

  if (retorna==true){
    alert('Cgm já reincidente nesta procedência!!');
  }
}

function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_lanctipo','func_fis_lanctipo.php?funcao_js=parent.js_preenchepesquisa|nl18_codlanc|1','Pesquisa',true);
}
function js_preenchepesquisa(chave,chave1){
  db_iframe_lanctipo.hide();
  <?php 
    if($db_opcao == 2 || $db_opcao == 22){
      echo " location.href = 'fis1_fis_lanctipo002.php?abas=1&chavepesquisa='+chave;";
    }elseif($db_opcao == 33 || $db_opcao == 3){
      echo " location.href = 'fis1_fis_lanctipo003.php?abas=1&chavepesquisa='+chave;";
    }
  ?>
}
</script>
<?php
if( $db_opcao == 2 ){
  echo "<script>js_atribuiMascara('$y45_percentual');</script>";
}

if(isset($nl18_codlanc) && $nl18_codlanc != ""){
  echo "<script>js_OpenJanelaIframe('','db_iframe_fiscal','func_fis_lancamento.php?pesquisa_chave=$nl18_codlanc&funcao_js=parent.js_mostralanc','Pesquisa',false);</script>";
  echo "<script>document.form1.nl01_codlanc.value='$nl18_codlanc';</script>";
}
?>
