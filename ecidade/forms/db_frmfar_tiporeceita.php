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

//MODULO: Farmácia
$clfar_tiporeceita->rotulo->label();
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tfa03_i_codigo?>">
       <?=@$Lfa03_i_codigo?>
    </td>
    <td>
        <?php
db_input('fa03_i_codigo',10,$Ifa03_i_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_descr?>">
       <?=@$Lfa03_c_descr?>
    </td>
    <td>
        <?php
db_input('fa03_c_descr',40,$Ifa03_c_descr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_profissional?>">
       <?=@$Lfa03_c_profissional?>
    </td>
    <td>
        <?php
      $x = array("N"=>"NAO","S"=>"SIM");
      db_select('fa03_c_profissional',$x,true,$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_posologia?>">
       <?=@$Lfa03_c_posologia?>
    </td>
    <td>
        <?php
      $x = array("N"=>"NAO","S"=>"SIM");
      db_select('fa03_c_posologia',$x,true,$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_requisitante?>">
       <?=@$Lfa03_c_requisitante?>
    </td>
    <td>
        <?php
      $x = array("N"=>"NAO","S"=>"SIM");
      db_select('fa03_c_requisitante',$x,true,$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_numeroreceita?>">
       <?=@$Lfa03_c_numeroreceita?>
    </td>
    <td>
        <?php
      $x = array("N"=>"NAO","S"=>"SIM");
      db_select('fa03_c_numeroreceita',$x,true,$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_c_quant?>">
       <?=@$Lfa03_c_quant?>
    </td>
    <td>
        <?php
      $x = array("N"=>"NAO","S"=>"SIM");
      db_select('fa03_c_quant',$x,true,$db_opcao,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=$Tfa03_data_prescricao?>">
      <?=$Lfa03_data_prescricao?>
    </td>
    <td style="display: flex; column-gap: 20px;">
        <?php
      $aX = array('f'=>"NÃO", 't'=>'SIM');
      db_select('fa03_data_prescricao', $aX, true, $db_opcao, "onchange='js_verificaprescricao();'");
      ?>
      <div id="diasPrescricaoDiv" style="display: none;">
          <label title="<?=$Tfa03_dias_prescricao?>" for="fa03_dias_prescricao"><?=$Lfa03_dias_prescricao?></label>
          <?php
            db_input('fa03_dias_prescricao',5,$Ifa03_dias_prescricao,true,'text',$db_opcao," ")
          ?>
      </div>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=$Tfa03_numero_notificacao?>">
      <?=$Lfa03_numero_notificacao?>
    </td>
    <td style="display: flex; column-gap: 20px;">
        <?php
      $aX = array('f'=>"NÃO", 't'=>'SIM');
      db_select('fa03_numero_notificacao', $aX, true, $db_opcao, "onchange='js_verificaprescricao();'");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tfa03_i_ativa?>">
      <?=@$Lfa03_i_ativa?>
    </td>
    <td>
        <?php
      $aX = array('1'=>"SIM", '2'=>'NÃO');
      db_select('fa03_i_ativa', $aX, true, $db_opcao, '');
      ?>
    </td>
  </tr>
   <tr>
    <td nowrap title="<?=@$Tfa03_i_prescricaomedica?>">
        <?php
       db_ancora(@$Lfa03_i_prescricaomedica,"js_pesquisafa03_i_prescricaomedica(true);",$db_opcao);
       ?>
    </td>
    <td>
        <?php
db_input('fa03_i_prescricaomedica',10,@$Ifa03_i_prescricaomedica,true,'text',$db_opcao," onchange='js_pesquisafa03_i_prescricaomedica(false);'")
?>
        <?php
db_input('fa20_c_prescricao',40,@$Ifa20_c_prescricao,true,'text',3,'')
       ?>
    </td>
  </tr>
  </table>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
  </center>
</form>
<script>
const btnSubmit = document.getElementById('db_opcao');
const selectDataPrescricao = document.getElementById('fa03_data_prescricao');
const inputDiasPrescricao = document.getElementById('fa03_dias_prescricao');

btnSubmit.addEventListener('click', js_submit);

js_verificaprescricao();

function js_pesquisafa03_i_prescricaomedica(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_far_prescricaomedica','func_far_prescricaomedica.php?funcao_js=parent.js_mostrafar_prescricaomedica1|fa20_i_codigo|fa20_c_prescricao','Pesquisa',true);
  }else{
     if(document.form1.fa03_i_prescricaomedica.value != ''){
        js_OpenJanelaIframe('','db_iframe_far_prescricaomedica','func_far_prescricaomedica.php?pesquisa_chave='+document.form1.fa03_i_prescricaomedica.value+'&funcao_js=parent.js_mostrafar_prescricaomedica','Pesquisa',false);
     }else{
       document.form1.fa20_i_codigo.value = '';
     }
  }
}
function js_mostrafar_prescricaomedica(chave,erro){
  document.form1.fa20_c_prescricao.value = chave;
  if(erro==true){
    document.form1.fa03_i_prescricaomedica.focus();
    document.form1.fa03_i_prescricaomedica.value = '';
  }
}
function js_mostrafar_prescricaomedica1(chave1,chave2){
  document.form1.fa03_i_prescricaomedica.value = chave1;
  document.form1.fa20_c_prescricao.value = chave2;
  db_iframe_far_prescricaomedica.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_far_tiporeceita','func_far_tiporeceita.php?funcao_js=parent.js_preenchepesquisa|fa03_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_far_tiporeceita.hide();
    <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
function js_verificaprescricao(){
    let diasPrescricaoDiv = document.getElementById('diasPrescricaoDiv');

    diasPrescricaoDiv.style.display = 'none';

    if (selectDataPrescricao.value == 't') {
        diasPrescricaoDiv.style.display = 'block';
    }
}
function js_submit(e){
    if (selectDataPrescricao.value == 't' && (inputDiasPrescricao.value == '' || inputDiasPrescricao.value <= 0)) {
        e.preventDefault();
        alert('O número de dias deve ser superior a zero.')
        return;
    }
}
</script>
