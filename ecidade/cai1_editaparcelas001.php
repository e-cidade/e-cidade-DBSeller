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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
$db_opcao = 1;
$db_botao = true;
$valor_disabled = '';
$permite_edit_data  = '1';

$sSql = "select *
         from cadtipoparc
         where k40_codigo = $lei";

$result = db_query($sSql) or die($sSql);
db_fieldsmemory($result, 0);

$valorparc = $valortot/$parcelas;

if($k40_permvalcadparc == 'f'){
  $valor_disabled = 'disabled';
}

if($k40_permdataparc == 'f'){
  $permite_edit_data = '3';
}

if(isset($incluir)){
  $array = array();
  for($i=0;$i < $parcelas;$i++){
    $parcela = $i + 1;
    $k189_data = 'k189_data'.$parcela;
    $k189_valor = 'k189_valor'.$parcela;

    if (is_null($$k189_valor)) {
        $k189_valor = 'k189_valorparcela'.$parcela;
    }

    $array[$i]['data']  = $$k189_data;
    $array[$i]['valor'] = $$k189_valor;
    $array[$i]['numpar'] = $parcela;
  }
  db_putsession('DB_parcelaseditadas', $array);

}
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor=#CCCCCC>
    <div class="container">
      <form name="form1" method="post" action="" onsubmit="return js_validacoes()">
        <input type='hidden' name='parcelas' value='<?php echo $parcelas?>'>
        <input type='hidden' name='valor' value='<?php echo $valortot?>'>
        <input type='hidden' name='vlrmin' value='<?php echo $vlrmin?>'>
        <input type='hidden' name='entradaminima' value='<?php echo $entradaminima?>'>
        <input type='hidden' name='permdataparc' value='<?php echo $k40_permdataparc?>'>
        <center>
          <fieldset style="margin-top: 20px;">
            <legend><b>Editar Parcelas</b></legend>
            <table border="0">
              <?php
                for($i=0;$i < $parcelas;$i++){
                  $parcela = $i + 1;

                  $sqlvenc = "select '" . date("Y",db_getsession("DB_datausu")) . "-" . date("m",db_getsession("DB_datausu")) . "-" . date("d",db_getsession("DB_datausu")) . "'::date + '$i months'::interval as venc";
                  $resultvenc = db_query($sqlvenc) or die($sqlvenc);

                  db_fieldsmemory($resultvenc,0);

                  $k189_data_dia = substr($venc,8,2);
                  $k189_data_mes = substr($venc,5,2);
                  $k189_data_ano = substr($venc,0,4);

                  $k189_valor = 'k189_valor'.$parcela;

                ?>
                <tr style="margin-bottom: 10px;">
                   <td colspan="3">
                     <br/>
                       <b>Parcela <?php echo $parcela?></b>
                   </td>
                </tr>

                <tr>
                   <td>
                     Valor
                   </td>
                  <td>
                    <?php
                      echo '<input type="text" name="'.'k189_valor'.$parcela.'" id="'.'k189_valor'.$parcela.'" class="field-size2" maxlength="10" '.$valor_disabled.'>';
                      echo '<input type="hidden" name="'.'k189_valorparcela'.$parcela.'" id="'.'k189_valorparcela'.$parcela.'" class="field-size2" maxlength="10" value="'.$valorparc.'">';
                      echo '<input type="hidden" name="k40_controlavencimento" id="k40_controlavencimento" class="field-size2" maxlength="10" value="'.$k40_controlavencimento.'">';
                    ?>
                  </td>
                   <td>
                     Vencimento
                   </td>
                  <td>
                    <?php
                    db_inputdata('k189_data'.$parcela,@$k189_data_dia,@$k189_data_mes,@$k189_data_ano,true,'text',$permite_edit_data,"")
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </table>
            <br>
            <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
          </fieldset>
        </center>
      </form>
    <div/>
  </body>
</html>
<script>
this.parcelas      = +document.form1.parcelas.value;
this.vlrmin        = +document.form1.vlrmin.value;
this.vlrtot        = +document.form1.valor.value;
this.entradaminima = +document.form1.entradaminima.value;
this.arrInputValor = [];

initFields();
js_atualizaparcela(true);

function initFields(){

  for(var i=0;i < this.parcelas;i++){

    if(document.form1.permdataparc.value != 'f'){
      $("dtjs_k189_data"+(i+1)).style.display = "none";
    }
    this.arrInputValor[i] = new DBInputValor($("k189_valor"+(i+1)));
    this.arrInputValor[i].value = $("k189_valorparcela"+(i+1)).value;
    $("k189_valor"+(i+1)).addEventListener('change', (e) => {
      var parcela = +e.target.name.substring(10, e.target.name.lenght()),
          resparc = this.parcelas - parcela;
      if(resparc == 0){
        js_atualizaparcelas(parcela, resparc, false);
      }else{
        js_atualizaparcelas(parcela, resparc, true);
      }
    });
  }
}


function js_atualizaparcelas(parcela, resparc, crescente){

  var ultpar  = this.arrInputValor[this.parcelas - 1],
      vlrultpar = ultpar.getValue(),
      pripar  = this.arrInputValor[0],
      vlrpripar = pripar.getValue(),
      parcelaatual = 0,

      totini = 0,
      totres = 0,
      vlrres = 0,
      vlratu = 0,
      vlrnov = 0,
      vlrpar = 0;

  if(crescente){

    var counter = parcela - 1;


    for(var i=0;i < this.parcelas;i++){

      var vlrparcatual = this.arrInputValor[i].getValue();

      if(i <= counter){
        totini = vlrparcatual + totini;
      }else{
        totres = vlrparcatual + totres;
      }

    }

     vlratu = totini + totres;
     vlrres = this.vlrtot - vlratu;
     vlrnov = totres + vlrres;
     vlrpar = (vlrnov / resparc).toFixed(2);

     for(var i=counter;i < this.parcelas;i++){

       if((i+1) >= this.parcelas){

         if((i+1) == (this.parcelas - 1)){
           parcelaatual = i + 1;
         }else{
           parcelaatual = i;
         }

       }else{
         parcelaatual = i + 1;
       }

       this.arrInputValor[parcelaatual].setValue(vlrpar);
     }

     js_atualizaparcela(true);

   }else{

     var counter = parcela - 2,
         restparc = parcela - 1;

     for(var i=counter;i >= 0;i--){

       var vlrparcatual = this.arrInputValor[i].getValue();

       totres = vlrparcatual + totres;
     }

     vlratu = totres + vlrultpar;
     vlrres = this.vlrtot - vlratu;
     vlrnov = totres + vlrres;
     vlrpar = vlrnov / restparc;

     for(var i=counter;i >= 0;i--){

       this.arrInputValor[i].setValue(vlrpar);

     }

     js_atualizaparcela(false);

   }

}

function js_atualizaparcela(ult){
  var
    valor     = 0,
    valorrest = 0;

  if(ult){
    var parc = this.arrInputValor[this.parcelas - 1];
  }else{
    var parc = this.arrInputValor[0];
  }

  var vlrparc = parc.getValue();

  for(var i=0;i < this.parcelas;i++){
    var
      parcela        = i+1;
      valorparcatual = this.arrInputValor[i].getValue();

    valor = valorparcatual + valor;

  }

  valorrest = parseFloat(this.vlrtot) - parseFloat(valor);
  valorAtu = (parseFloat(vlrparc) + valorrest).toFixed(2);
  if(ult){
    this.arrInputValor[this.parcelas - 1].setValue(valorAtu);
  }else{
    this.arrInputValor[0].setValue(valorAtu);
  }

  js_validatotal();
}

function js_validatotal(){

  var totparc  = 0;
  var ultpar  = document.getElementById("k189_valor"+ this.parcelas).value.replace(".", "").replace(",", ".");

  for(var i=0;i < this.parcelas;i++){
    var
      parcatual = i+1,
      valor = this.arrInputValor[i].getValue();

      totparc = parseFloat(valor) + parseFloat(totparc);

      if(valor > this.vlrtot){
        alert('Valor ('+ valor +') da parcela '+parcatual+' n\u00e3o pode ser maior que o valor do debito ('+this.vlrtot+')');
        initFields();
        js_atualizaparcela(true);
      }else if(totparc.toFixed(2) > this.vlrtot){
        //alert('Valor ('+ ultpar +') da parcela '+this.parcelas+' n\u00e3o pode ser negativo!');
        initFields();
        js_atualizaparcela(true);
      }

  }

}

function js_validacoes(){
  var totparc  = 0;
  var entrada = document.getElementById("k189_valor1").value.replace(".", "").replace(",", ".");
  var ultpar  = document.getElementById("k189_valor"+ this.parcelas).value.replace(".", "").replace(",", ".");
  var privenc = document.getElementById("k189_data1").value;
  if(this.parcelas > 1){
    var segvenc = document.getElementById("k189_data2").value;
    var proxvenc = document.getElementById("k189_data2_dia").value;
  }else{
    var segvenc = 0;
    var proxvenc = '';
  }

  for(var i=0;i < this.parcelas;i++){
    var
      parcatual = i+1,
      valor = this.arrInputValor[i].getValue(),
      data = document.getElementById("k189_data"+ parcatual).value,
      ano  = +document.getElementById("k189_data"+ parcatual+"_ano").value,
      now = new Date(),
      anoatu = now.getFullYear(),
      controlaVencimento = $('k40_controlavencimento').value;

    totparc = parseFloat(valor) + parseFloat(totparc);

    if(valor == ''){
      alert('Valor da parcela '+parcatual+' n\u00e3o pode ser vazio!');
      return false;
    }else if(valor == 0){
      alert('Valor da parcela '+parcatual+' n\u00e3o pode ser 0!');
      return false;
    }else if(data == ''){
      alert('Data da parcela '+parcatual+' n\u00e3o pode ser vazia!');
      return false;
    }else if(valor < this.vlrmin){
      alert('Valor ('+ valor +') da parcela '+parcatual+' n\u00e3o pode ser menor que a valor m\u00ednimo ('+this.vlrmin+')');
      return false;
    }else if(valor > this.vlrtot){
      alert('Valor ('+ valor +') da parcela '+parcatual+' n\u00e3o pode ser maior que o valor do debito ('+this.vlrtot+')');
      return false;
    }else if(totparc.toFixed(2) > this.vlrtot){
      alert('Valor ('+ ultpar +') da parcela '+this.parcelas+' n\u00e3o pode ser negativo!');
      document.getElementById("k189_valor"+ this.parcelas).value = document.getElementById("k189_valor"+ parcatual-1).value;
      return false;
    } else if(i == 0 && valor < this.entradaminima){
      alert('Valor de entrada (Parcela 1) deve ser maior que '+this.entradaminima+'');
      return false;
    }

    if(controlaVencimento == 't'){
      if(ano > anoatu){
        alert('O ano da data de vencimento deve ser ('+anoatu+')');
        return false;
      }else if(ano < anoatu){
        alert('O ano da data de vencimento n\u00e3o pode ser menor que o ano atual!');
        return false;
      }
    }
  }
  if(totparc.toFixed(2) < this.vlrtot.toFixed(2)){
    alert('Valor total das parcelas n\u00e3o pode ser menor que o valor do d\u00e9bito!');
    return false;
  }else if(totparc.toFixed(2) > this.vlrtot.toFixed(2)){
    alert('Valor total das parcelas n\u00e3o pode ser maior que o valor total do d\u00e9bito!');
    return false;
  }

  window.CurrentWindow.corpo.db_iframe_parcela.hide();
  window.CurrentWindow.corpo.debitos.js_atualizadebitos(entrada, ultpar, privenc, segvenc, proxvenc);
  return true;
}

</script>
