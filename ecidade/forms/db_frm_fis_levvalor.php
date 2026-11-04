<?php
/**
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
require_once  modification("dbforms/db_classesgenericas.php");
require_once  modification("classes/db_parissqn_classe.php");
require_once  modification("classes/db_issbase_classe.php");
require_once  modification("classes/db_fis_levinscr_classe.php");

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clparissqn               = new cl_parissqn;
$clissbase                = new cl_issbase;
$cllevinscr               = new cl_fis_levinscr;

$cllevvalor->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("y60_contato");
$data = date("d-m-Y",db_getsession("DB_datausu"));
$data = explode('-',$data);
$dia  = $data[0];
$mes  = $data[1];
$ano  = $data[2];

?>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
 jQuery(document).ready(function($) {
    $('input[name=dtjs_y63_dtvenc]').hide();
    $('#y63_dtvenc').attr({
      'style'     : 'background-color:#DEB887',
      'readonly' : true
    });

    function vencimento(){
      var ano = $('#y63_ano').val();
      var mes = $('#y63_mes').val();
      Hoje = new Date();
      Data = Hoje.getDate();
      Dia = Hoje.getDay();
      Mesx = Hoje.getMonth();
      Anoa = Hoje.getFullYear();
      Mesx = parseInt(Mesx)+parseInt(1);
      if( Anoa == ano ){
        if(mes > Mesx){
          alert('Mês Inválido! Mês não pode ser superior ao mês Corrente!');
          document.form1.y63_mes.value = Mesx;
          mes = Mesx;
        }
      }
      $.ajax({
        type     : 'post',
        url      : 'fis1_fis_levvenci001.php',
        data     : { ano : ano, mes : mes },
        dataType : 'json',
        success  : function(d){
          if(d.erro != 0){
            alert(d.erro);
          }else{
            $('#y63_dtvenc').val(d.vencimento);
            $('#y63_dtvenc_dia').val(d.dia);
            $('#y63_dtvenc_mes').val(d.mes);
            $('#y63_dtvenc_ano').val(d.ano);
            js_deflaciona();
          }
        }
      });
    }
    vencimento();
    $('#y63_mes').change(function(event) {
      vencimento();
    });

    $('#y63_ano').change(function(event) {
      vencimento();

    });

  });

  function js_calcula(campo,evt){

    obj = document.form1;
    if( campo.name == 'y63_bruto' ){

      bruto = new Number(campo.value);
      aliq  = new Number(obj.y63_aliquota.value);
    }
    if( campo.name == 'y63_aliquota' ){

      aliq  = new Number(campo.value);
      bruto = new Number(obj.y63_bruto.value);
    }
    if( campo.name == 'y63_pago' ){

      aliq  = new Number(obj.y63_aliquota.value);
      bruto = new Number(obj.y63_bruto.value);
    }

    if(aliq > 300 || aliq < 0 ){

      alert('Aliquota inválida!');
      obj.y63_aliquota.value = '';
      obj.y63_aliquota.focus();
      return false;
    }

    total = new Number((bruto * aliq) / 100);
    obj.apagar.value = total;

    pago   = new Number(obj.y63_pago.value);
    valtot = new Number(total-pago);
    js_deflaciona();

    var evt = (evt) ? evt : (window.event) ? window.event : "";
    if(evt.keyCode==13){
      document.form1.<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>.click();
    }
  }

 function js_deflaciona(){
      var venci = $('#y63_dtvenc').val();

      if($('#y68_pgto').val() != ""){

        var pago  = $('#y68_pgto').val();
        var valor = $('#y63_pago').val();
        var pagts = false;
      }else{
        var valor = $('#valores').val();
        var pagts = true;
      }
      var apagar = $('#apagar').val();

      $.ajax({
        type     : 'post',
        url      : 'fis1_fis_deflacionar001.php',
        data     : { venci : venci, valor : valor , pago : pago, apagar : apagar, pagts : pagts },
        dataType : 'json',
        success  : function(d){
          if(d.erro != 0){
            alert(d.erro);
          }else{
            $('#vlrcons').val(d.valor);
            $('#y63_saldo').val(d.saldo);

          }
        }
      });
    }
  function js_over(cods){

    var tab  = document.getElementById('tab2') ;
    arr_cods = cods.split("-");
    str      = document.form1.str.value;
    arr_dad  = str.split("#");
    str2      = document.form1.str2.value;
    str3      = document.form1.str3.value;
    arr_dad2  = str2.split("#");
    arr_dad3  = str3.split("#");
    use1      = false;
    use2      = false;
    use3      = false;
    if(arr_dad.length>0){
       for(i=0; i < arr_dad.length; i++){
          arr_linh = arr_dad[i].split("-");
          if(arr_cods[0] == arr_linh[0] && arr_cods[1] == arr_linh[1] ){
            use1 = true;
        }
      }
      if(use1 == true){
        novalinha  = document.getElementById('tab2').insertRow(document.getElementById('tab2').rows.length);
        novacoluna = novalinha.insertCell(0);
        novacoluna.innerHTML = "Ordem";
        novacoluna = novalinha.insertCell(1);
        novacoluna.innerHTML = "Documento";
        novacoluna = novalinha.insertCell(2);
        novacoluna.innerHTML = "Valor";
      }
    }
    if(arr_dad2.length>0){
       for(i=0; i < arr_dad2.length; i++){
          arr_linh2 = arr_dad2[i].split("-");
          if(arr_cods[0] == arr_linh2[0] && arr_cods[1] == arr_linh2[1] ){
            use2 = true;
          }
        }
        if(use2 == true){

          novalinha  = document.getElementById('tab3').insertRow(document.getElementById('tab3').rows.length);
          novacoluna = novalinha.insertCell(0);
          novacoluna.innerHTML = "Valor do Pag.";
          novacoluna = novalinha.insertCell(1);
          novacoluna.innerHTML = "Valor do Pag. Consid.";
          novacoluna = novalinha.insertCell(2);
          novacoluna.innerHTML = "Data do Pagamento";
        }
    }

    if(arr_dad3.length>0){
      for(i=0; i < arr_dad3.length; i++){
        arr_linh3 = arr_dad3[i].split("-");
        if(arr_cods[0] == arr_linh3[0] && arr_cods[1] == arr_linh3[1]  && arr_linh3[2] != ''){
          use3 = true;
        }
      }
      if(use3 == true){
          novalinha  = document.getElementById('tab4').insertRow(document.getElementById('tab4').rows.length);
          novacoluna = novalinha.insertCell(0);
          novacoluna.innerHTML = "Observação";
      }
    }
    for(i=0; i < arr_dad.length; i++){
      arr_linh = arr_dad[i].split("-");
      if(arr_cods[0] == arr_linh[0] && arr_cods[1] == arr_linh[1] ){
        novalinha  = document.getElementById('tab2').insertRow(document.getElementById('tab2').rows.length);
        novacoluna = novalinha.insertCell(0);
        novacoluna.innerHTML = arr_linh[4];
        novacoluna = novalinha.insertCell(1);
        novacoluna.innerHTML = arr_linh[2];
        novacoluna = novalinha.insertCell(2);
        novacoluna.innerHTML = arr_linh[3];
      }
    }
    for(i=0; i < arr_dad2.length; i++){
      arr_linh2 = arr_dad2[i].split("-");
      if(arr_cods[0] == arr_linh2[0] && arr_cods[1] == arr_linh2[1] ){
        novalinha  = document.getElementById('tab3').insertRow(document.getElementById('tab3').rows.length);
        novacoluna = novalinha.insertCell(0);
        novacoluna.innerHTML = arr_linh2[3];
        novacoluna = novalinha.insertCell(1);
        novacoluna.innerHTML = arr_linh2[2];
        novacoluna = novalinha.insertCell(2);
        novacoluna.innerHTML = arr_linh2[4];
      }
    }
    for(i=0; i < arr_dad3.length; i++){
      arr_linh3 = arr_dad3[i].split("-");
      if(arr_cods[0] == arr_linh3[0] && arr_cods[1] == arr_linh3[1] && arr_linh3[2] != ''){
        novalinha  = document.getElementById('tab4').insertRow(document.getElementById('tab4').rows.length);
        novacoluna = novalinha.insertCell(0);
        novacoluna.innerHTML = arr_linh3[2];
      }
    }
    document.getElementById('tab2').style.visibility = 'visible';
    document.getElementById('tab3').style.visibility = 'visible';
    document.getElementById('tab4').style.visibility = 'visible';
  }

  function js_out(cods){

    var tab = document.getElementById('tab2') ;
    while(tab.rows.length>0){
        tab.deleteRow(tab.rows.length-1);
    }

    var tab2 = document.getElementById('tab3') ;
    while(tab2.rows.length>0){
        tab2.deleteRow(tab2.rows.length-1);
    }
    var tab3 = document.getElementById('tab4') ;
    while(tab3.rows.length>0){
        tab3.deleteRow(tab3.rows.length-1);
    }
    document.getElementById('tab2').style.visibility = 'hidden';
    document.getElementById('tab3').style.visibility = 'hidden';
    document.getElementById('tab4').style.visibility = 'hidden';
  }

</script>
<form name="form1" method="post" action="fis1_fis_levvalor001.php">
<center>
  <table id="tab2" border="1" style="position:absolute; z-index:1; top:; left:10; border: 1px none #000000; background-color: #CCCCCC; background-color:#999999; font-weight:bold;opacity: 0.8;">
  </table>
  <table id="tab3" border="1" style="position:absolute; top:; left:200; border: 1px none #000000; background-color: #999999; font-weight:bold; opacity: 0.8;">
  </table>
  <table id="tab4" border="1" style="position:absolute; top:; left:550; border: 1px none #000000; background-color: #999999; font-weight:bold; opacity: 0.8;">
  </table>
<table border="0">
  <tr>
    <td align='center'>
<fieldset>
  <legend>Dados</legend>
<table border="0" >
  <tr>
    <td nowrap title="<?=@$Ty63_sequencia?>">
       <?=@$Ly63_sequencia?>
    </td>
    <td>
<?php 

db_input('valores',60,0,true,'hidden',3);
db_input('notas',60,0,true,'hidden',3);
db_input('y63_sequencia',10,$Iy63_sequencia,true,'text',3);
?>
    </td>
    <td>
      <?php 
      db_input('y63_codlev',4,$Iy63_codlev,true,'hidden',3)
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty63_ano?>">
       <?=@$Ly63_ano?>
    </td>
    <td>
      <?php 
      $result=$cllevanta->sql_record($cllevanta->sql_query_file($y63_codlev,"y60_dtini,y60_dtfim"));
      db_fieldsmemory($result,0);

      $arr_ini = explode("-",$y60_dtini);
      $arr_fim = explode("-",$y60_dtfim);
      $ini = $arr_ini[0];
      $fim = $arr_fim[0];


      $anos=array();

      //Pega os exercícios do periodo
      for($i=$ini; $i<$fim+1; $i++){
       $anos[$i]=$i;
      }

      db_select("y63_ano",$anos,true,$db_opcao,"","","","","");
      ?>
    </td>
    <td nowrap title="<?=@$Ty63_mes?>">
       <?=@$Ly63_mes?>
    </td>
    <td>
    <?php

      if(empty($y63_mes)){
        $y63_mes = $arr_ini[1];
      }

      $result=array("1"=>"Janeiro","2"=>"Fevereiro","3"=>"Marco","4"=>"Abril","5"=>"Maio","6"=>"Junho","7"=>"Julho","8"=>"Agosto","9"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro");

      $meses = array();

      $meses_ini = intval($arr_ini[1]);
      $meses_fim = intval($arr_fim[1]);


      $ano = array();

      for($j = $meses_ini; $j<=12; $j++){
          $meses[$j] = $result[$j];
      }

      $ano[$ini] = $meses;

      while($ini < $fim ) {
          $meses = "";

          for($j=1; $j <= 12; $j++){
              $meses[$j] = $result[$j];
          }
          $ini++;
          $ano[$ini] = $meses;
      }
      $meses = "";

      for($j=1; $j <= $meses_fim; $j++){
           $meses[$j] = $result[$j];
      }
      $ano[$fim ] = $meses;

      db_select("y63_mes",$result,true,$db_opcao,"","","","","");

      $Ly63_dtvencult=$cllevanta->sql_record($cllevanta->sql_query_file($y63_codlev,"y60_dtini,y60_dtfim"));
      db_fieldsmemory($Ly63_dtvencult,0);

      $arr_ini = explode("-",$y60_dtini);
      $arr_fim = explode("-",$y60_dtfim);
      $ini = $arr_ini[0];
      $fim = $arr_fim[0];

      $anos=array();

      //Pega os exercícios do periodo
      for($i=$ini; $i<$fim+1; $i++){
       $anos[$i]=$i;
      }

      $meses = array();

      if(empty($y63_dtvenc_mes)){

        $y63_dtvenc_dia = $arr_ini[2];
        $y63_dtvenc_mes = $arr_ini[1];
        $y63_dtvenc_ano = $arr_ini[0];
      }
      db_inputdata('y63_dtvenc','','','',true,'text',$db_opcao,"");
    ?>
  </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty63_histor?>">
       <?=@$Ly63_histor?>
    </td>
    <td colspan='6'>
    <?php
    db_textarea('y63_histor',0,70,$Iy63_histor,true,'text',$db_opcao,"")
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty63_bruto?>">
       <?=@$Ly63_bruto?>
    </td>
    <td>
    <?php

    $db_op = $db_opcao;
    if(isset($y63_sequencia) && $y63_sequencia != null){

      $cllevantanotas->sql_record($cllevantanotas->sql_query_file(null,"y79_codigo","","y79_sequencia=$y63_sequencia"));
      if( $cllevantanotas->numrows>0){
        $db_op = 3;
      }
    }
    ?>
   <input title="Valor  Campo:y63_bruto" name="y63_bruto"  type="text"     id="y63_bruto"  value="<?=@$y63_bruto?>"  size="10"
    maxlength="15"  onblur="js_ValidaMaiusculo(this,'f',event);js_calcula(this,event);"
                  onKeyUp="js_ValidaCampos(this,4,'Valor','f','f',event);"
                        onchange='js_calcula(this,event);' onkeypress="return mascaraValor(event, this);">

    </td>
    <td nowrap title="<?=@$Ty63_aliquota?>">
       <?=@$Ly63_aliquota?>
    </td>
    <td>
    <?php
    if(empty($y63_aliquota)){
 //       $result44  = $cllevinscr->sql_record($cllevinscr->sql_query_file($y63_codlev,"y62_inscr"));
 //       $numrows44 = $cllevinscr->numrows;
 //       if ( $numrows44 > 0) {
 //         db_fieldsmemory($result44, 0);
 //         $result33  = $clissbase->sql_record($clissbase->sql_query_aliquota($y62_inscr,"q81_valexe"));
 //         $numrows33 = $clissbase->numrows;
  // if($numrows33 > 0){
 //           // db_fieldsmemory($result33, 0);
 //           //$y63_aliquota = $q81_valexe;
 //         } else {
 //           $result66=$clparissqn->sql_record($clparissqn->sql_query_file('','q60_aliq'));
 //           // db_fieldsmemory($result66,0);
 //           //$y63_aliquota=$q60_aliq;
 //         }
 //       } else {
 //        $result66=$clparissqn->sql_record($clparissqn->sql_query_file('','q60_aliq'));
 //        // db_fieldsmemory($result66,0);
 //        //$y63_aliquota=$q60_aliq;
 //       }
  // $sAliquota  = 'select y63_aliquota from fiscalizacao.fis_levvalor where y63_codlev  = '.$y63_codlev.' order by y63_sequencia desc limit 1';
  // $rsAliquota = $cllevvalor->sql_record($sAliquota);
  // db_fieldsmemory($rsAliquota, 0);
  // if (pg_num_rows($rsAliquota) == 0) {
  //  $y63_aliquota=$y63_aliquota;
  // }
    }

    db_input('y63_aliquota',10,$Iy63_aliquota,true,'text',$db_opcao,"onchange='js_calcula(this);'",null,null,null,8)
    ?>

    </td>

    <td nowrap title="Valor à pagar">
      <strong>Valor a Pagar:</strong>
    </td>
    <td>
    <?php
      db_input('apagar',8,0,true,'text',3);
      if($db_opcao!=3){

      echo '<input name="nota" type="button" value="Notas" onclick="js_nota();" >';
    }
    ?>
    </td>
  </tr>
  <tr>
     <td nowrap title="<?=@$Ty63_pago?>">
       <?=@$Ly63_pago?>
    </td>
    <td>
    <?php
    empty($y63_pago) ? $y63_pago = '0' : '';
    ?>
    <input type="text" autocomplete="off" maxlength="15" size="10" value="<?=(($y63_pago==0)?'':$y63_pago)?>" id="y63_pago" name="y63_pago" title="Valor Pago Campo:y63_pago" onblur="js_ValidaMaiusculo(this,'f',event);js_calcula(this,event);" onKeyUp="js_ValidaCampos(this,4,'Valor','f','f',event);" onchange="js_calcula(this,event);js_valores(this.value);" onkeypress="return mascaraValor(event, this);">
   </td>

   <td align="left"><b>Data de Pagamento: </b>
    </td>
    <td>
    <?php 
    db_inputdata('y68_pgto',@$y68_pgto_dia,@$y68_pgto_mes,@$y68_pgto_ano,true,'text',$db_opcao,"onchange='js_deflaciona();'","","","","","","js_deflaciona();");
    ?></td>

   <td align="left"><b>Valor Considerado: </b>
   <td>
    <?php 
      db_input('vlrcons',8,0,true,'text',3);
    if($db_opcao!=3){

      echo '<input name="pgto" type="button" value="Pagamentos" onclick="js_pgto();" >';
    }
    ?>
    </td>
  </tr>
  <tr>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
    <td align='right'>
       <?=@$Ly63_saldo?>
    </td>
    <td nowrap title="<?=@$Ty63_saldo?>">
    <?php
      db_input('y63_saldo',8,$Iy63_saldo,true,'text',3);
      if($db_opcao!=3){
        echo '<input name="calc" type="button" value="Calcular" onclick="js_deflaciona();" >';
      }
    ?>
    </td>

  </tr>
 </table>
</fieldset>
  </td>
</tr>
  <tr>
    <td align="center">
    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >

  <?php 
  if(isset($opcao)){
  ?>
  <input name="novo" type="button" value="Novo" onclick="js_novo();" >
  <?php 
  }
  ?>
    </td>
  </tr>
<tr>
  <td valign="top" align='left'><br/>
   <?php
    if(isset($db_opcaoal)){
       db_input("db_opcaoal",10,"",true,"hidden",3);
    }
    $chavepri= array("y63_codlev"=>@$y63_codlev,"y63_sequencia"=>@$y63_sequencia);
    $cliframe_alterar_excluir->chavepri      = $chavepri;
    $cliframe_alterar_excluir->sql           = $cllevvalor->sql_query_file("","y63_sequencia,y63_mes||'/'||y63_ano as y63_mes,y63_mes as mes,y63_ano,y63_codlev,y63_sequencia,y63_bruto,y63_aliquota,(select sum(yl63_pagoriginal) from fiscalizacao.fis_valordefla where yl63_sequencia =y63_sequencia )as y63_pago, y63_pago as y68_valor,y63_saldo,y63_dtvenc,substr(y63_histor,0,20) as y63_histor,((y63_bruto*y63_aliquota)/100) as y63_apagar
                ,(select case when count(y68_pgto)> 1 then null when count(y68_pgto)= 0 then  y63_dtvenc else (select y68_pgto from fiscalizacao.fis_levvalorpgtos where y68_sequencia = y63_sequencia limit 1) end as teste from fiscalizacao.fis_levvalorpgtos  where y68_sequencia = y63_sequencia) as y68_pgto"," 3,4 ","y63_codlev=$y63_codlev");
    $cliframe_alterar_excluir->campos        = "y63_sequencia,y63_mes,y63_bruto,y63_aliquota,y63_apagar,y63_dtvenc,y63_pago,y68_pgto,y68_valor,y63_saldo,y63_histor";
    $cliframe_alterar_excluir->legenda       = "VALORES LANÇADOS";
    $cliframe_alterar_excluir->iframe_height = "140";
    $cliframe_alterar_excluir->iframe_width  = "840";
    $cliframe_alterar_excluir->js_mouseout   = "parent.js_out";
    $cliframe_alterar_excluir->js_mouseover  = "parent.js_over";
    $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);

    $sql = $cllevvalor->sql_query_file("","sum(y63_bruto) as tot_bruto,sum(y63_pago) as tot_pago,sum(y63_saldo) as tot_saldo,sum(y63_pago+y63_saldo) as tot_apagar","","y63_codlev=$y63_codlev");
    $result = $cllevvalor->sql_record($sql);
    if ($cllevvalor->numrows>0){
      db_fieldsmemory($result,0);
    }

    $results = db_query("select sum(yl63_pagoriginal) as pagoriginal from fiscalizacao.fis_levvalor inner join fiscalizacao.fis_levvalorpgtos on y63_sequencia = y68_sequencia inner join fiscalizacao.fis_valordefla on yl63_sequencia = y68_sequencia and y68_seq = yl63_seq where y63_codlev=$y63_codlev");
    if(pg_num_rows($results) > 0 ){
      db_fieldsmemory($results,0);
    }
   ?>
   </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty63_bruto?>" colspan='2' style="text-align:center">
       <strong>Total Bruto:</strong>
        <?php
          $tot_bruto = trim(db_formatar($tot_bruto, 'p'));
          db_input('tot_bruto',10,$Iy63_bruto,true,'text',3);
        ?>
      <strong>Total Imposto:</strong>
        <?php
          $tot_apagar = trim(db_formatar($tot_apagar, 'p'));
          db_input('tot_apagar',10,0,true,'text',3);
        ?>
       <strong>Total Pago:</strong>
        <?php
          $pagoriginal = trim(db_formatar($pagoriginal, 'p'));
          db_input('pagoriginal',10,$Iy63_pago,true,'text',3);
        ?>
        <strong>Total Considerado:</strong>
        <?php
          $tot_pago = trim(db_formatar($tot_pago, 'p'));
          db_input('tot_pago',10,$Iy63_pago,true,'text',3);
        ?>
       <strong>Total Saldo à Pagar:</strong>
        <?php
          $tot_saldo = trim(db_formatar($tot_saldo, 'p'));
          db_input('tot_saldo',10,$Iy63_saldo,true,'text',3)
        ?>
    </td>
  </tr>
  </table>
  </center>
<?php
  $result = $cllevantanotas->sql_record($cllevantanotas->sql_query(null,"y63_sequencia,y63_codlev,y79_documento,y79_ordem,y79_valor","y79_ordem","y63_codlev=$y63_codlev"));
  $numrows = $cllevantanotas->numrows;
  $str = '';
  $sep = '';
  for($i=0; $i<$numrows; $i++){

    db_fieldsmemory($result,$i);
    $str .= "$sep$y63_codlev-$y63_sequencia-$y79_documento-$y79_valor-$y79_ordem";
    $sep = '#';
  }
  db_input('str',10,0,true,'hidden',3);

  $result2 = db_query("select * from fiscalizacao.fis_levvalor inner join fiscalizacao.fis_levvalorpgtos on y63_sequencia = y68_sequencia inner join fiscalizacao.fis_valordefla on yl63_sequencia = y68_sequencia and y68_seq = yl63_seq where y63_codlev=$y63_codlev");
  $numrows2 = pg_num_rows($result2);
  $str2 = '';
  $sep = '';
  for($x=0; $x<$numrows2; $x++){

    db_fieldsmemory($result2,$x);
    $str2 .= "$sep$y63_codlev-$y63_sequencia-$y68_valor-$yl63_pagoriginal-".db_formatar($y68_pgto,"d");
    $sep = '#';
  }

  $result3 = db_query("select * from fiscalizacao.fis_levvalor where y63_codlev=$y63_codlev");
  $numrows3 = pg_num_rows($result3);
  $str3 = '';
  $sep = '';
  for($f=0; $f<$numrows3; $f++){
    db_fieldsmemory($result3,$f);
    $str3 .= "$sep$y63_codlev-$y63_sequencia-$y63_histor";
    $sep = '#';
  }
  db_input('str2',10,0,true,'hidden',3);
  db_input('str3',10,0,true,'hidden',3);
?>


</form>
<script type="text/javascript">
function js_novo(){

  obj = document.createElement('input');
  obj.setAttribute('name','novo');
  obj.setAttribute('type','hidden');
  obj.setAttribute('value','ok');
  document.form1.appendChild(obj);
  document.form1.submit();
}

//mostra iframe dos pagamentos
function js_pgto(){

   valores = document.form1.valores.value;

   if((window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levvalor.db_iframe_pgto){
      db_iframe_pgto.show();
   }else{

     valores=document.form1.valores.value;
      if(valores!=""){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levvalor','db_iframe_pgto','fis1_fis_levvalorpgtos001.php?<?=($db_opcao==33?'db_opcao=33&':'')?>valores='+valores,'Pesquisa',true,0);
      }else{
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levvalor','db_iframe_pgto','fis1_fis_levvalorpgtos001.php?<?=($db_opcao==33?'db_opcao=33':'')?>','Pesquisa',true,0);
      }
   }
}
//esconde o iframe dos pagamentos
function js_fecha(){
  db_iframe_pgto.hide();
  js_calcula(document.form1.y63_pago);
  if(document.form1.y63_pago.value > 0 ){
    document.form1.y68_pgto.readOnly=true;
    document.form1.y68_pgto.value = '';
    document.form1.dtjs_y68_pgto.disabled = true;
    document.form1.y68_pgto.style.backgroundColor = '#DEB887';
  }else{
    document.form1.y68_pgto.readOnly = false;
    document.form1.dtjs_y68_pgto.disabled = false;
    document.form1.y68_pgto.style.backgroundColor = '';
  }
}

//mostra iframe de notas
function js_nota(){
   notas = document.form1.notas.value;

   if((window.CurrentWindow || parent.CurrentWindow).corpo.iframe_levvalor.db_iframe_nota){
      db_iframe_nota.show();
   }else{
      if(notas!=""){
         js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levvalor','db_iframe_nota','fis1_fis_levantanotas001.php?<?=($db_opcao==33?'db_opcao=33&':'')?>notas='+notas,'Pesquisa',true,0);
      }else{
         js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levvalor','db_iframe_nota','fis1_fis_levantanotas001.php?<?=($db_opcao==33?'db_opcao=33':'')?>','Pesquisa',true,0);
      }
   }
}
//esconde o iframe de notas
function js_fecha02(){
  db_iframe_nota.hide();

  if(document.form1.y63_bruto.value>0){
    document.form1.y63_bruto.readOnly=true;;
    document.form1.y63_bruto.style.backgroundColor = '#DEB887';
  }else{
    document.form1.y63_bruto.readOnly=false;;
    document.form1.y63_bruto.style.backgroundColor = '';
  }
  js_calcula(document.form1.y63_pago);
}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_levvalor','db_iframe_levvalor','func_fis_levvalor.php?funcao_js=parent.js_preenchepesquisa|y63_sequencia','Pesquisa',true,0);
}
function js_preenchepesquisa(chave){
  db_iframe_levvalor.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}

function js_valores(){
    var valor = document.getElementById('y63_pago').value
    document.getElementById('valores').value = valor+'-';
}

$("#y63_ano").change(function(){
  var datas = <?php echo json_encode($ano); ?>;
  var iAno  = <?php echo json_encode($aAno); ?>;
$.each(datas, function (key, data) {
  if(key == $('#y63_ano').val()) {
  $('#y63_mes').find('option').remove();

   $.each(data, function (index, data) {
        if(iAno == index){
         $('#y63_mes').append("<option value="+index+" selected>"+data+"</option>");
        }else{
         $('#y63_mes').append("<option value="+index+" >"+data+"</option>");
        }
   });

  }
});
});

  var datas = <?php echo json_encode($ano); ?>;
  var iMes  = <?php echo json_encode($aMes); ?>;
$.each(datas, function (key, data) {
  if(key == $('#y63_ano').val()) {
  $('#y63_mes').find('option').remove();

   $.each(data, function (index, data) {
        if(iMes == index){
          $('#y63_mes').append("<option value="+index+" selected >"+data+"</option>");
        }else{

         $('#y63_mes').append("<option value="+index+" >"+data+"</option>");
        }
   });

  }
});

</script>
