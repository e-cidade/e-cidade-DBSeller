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

//MODULO: cemiterio
$cljazigos->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("cm01_i_codigo");
$clrotulo->label("z01_nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0" cellspadding="0" cellspacing="0">
  <tr>
    <td nowrap title="<?=@$Tcm03_i_codigo?>">
       <?=@$Lcm03_i_codigo?>
    </td>
    <td colspan="3">
<?php
db_input('cm03_i_codigo',10,$Icm03_i_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_i_proprietario?>">
       <?php
       db_ancora(@$Lcm03_i_proprietario,"js_pesquisacm03_i_proprietario(true);",$db_opcao);
       ?>
    </td>
    <td colspan="3">
<?php
db_input('cm03_i_proprietario',10,$Icm03_i_proprietario,true,'text',$db_opcao," onchange='js_pesquisacm03_i_proprietario(false);'")
?>
       <?php
db_input('z01_nome',40,$Iz01_nome,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_c_termo?>">
       <?=@$Lcm03_c_termo?>
    </td>
    <td>
<?php
db_input('cm03_c_termo',10,$Icm03_c_termo,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tcm03_d_datatermo?>">
       <?=@$Lcm03_d_datatermo?>
    </td>
    <td>
<?php
db_inputdata('cm03_d_datatermo',@$cm03_d_datatermo_dia,@$cm03_d_datatermo_mes,@$cm03_d_datatermo_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_c_carta?>">
       <?=@$Lcm03_c_carta?>
    </td>
    <td>
<?php
db_input('cm03_c_carta',10,$Icm03_c_carta,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tcm03_d_datacarta?>">
       <?=@$Lcm03_d_datacarta?>
    </td>
    <td>
<?php
db_inputdata('cm03_d_datacarta',@$cm03_d_datacarta_dia,@$cm03_d_datacarta_mes,@$cm03_d_datacarta_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_d_aquisicao?>">
       <?=@$Lcm03_d_aquisicao?>
    </td>
    <td>
<?php
db_inputdata('cm03_d_aquisicao',@$cm03_d_aquisicao_dia,@$cm03_d_aquisicao_mes,@$cm03_d_aquisicao_ano,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tcm03_c_base?>">
       <?=@$Lcm03_c_base?>
    </td>
    <td>
<?php
db_input('cm03_c_base',10,$Icm03_c_base,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_c_estrutura?>">
       <?=@$Lcm03_c_estrutura?>
    </td>
    <td>
<?php
db_input('cm03_c_estrutura',10,$Icm03_c_estrutura,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tcm03_c_pronto?>">
       <?=@$Lcm03_c_pronto?>
    </td>
    <td>
<?php
db_input('cm03_c_pronto',10,$Icm03_c_pronto,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_c_quadra?>">
       <?=@$Lcm03_c_quadra?>
    </td>
    <td>
<?php
db_input('cm03_c_quadra',3,$Icm03_c_quadra,true,'text',$db_opcao,"")
?>
    </td>
    <td nowrap title="<?=@$Tcm03_i_lote?>">
       <?=@$Lcm03_i_lote?>
    </td>
    <td>
<?php
db_input('cm03_i_lote',10,$Icm03_i_lote,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tcm03_f_metragem1?>">
       <?=@$Lcm03_f_metragem1?>
    </td>
    <td colspan="3">
     <?php db_input('cm03_f_metragem1',10,$Icm03_f_metragem1,true,'text',$db_opcao,"")?>x
     <?php db_input('cm03_f_metragem2',10,$Icm03_f_metragem2,true,'text',$db_opcao,"")?>
    </td>
  </tr>
  </table>
  </center>
<?php if(@$antigo==""){?>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
<?php }?>
</form>
<script>
function js_pesquisacm03_i_sepultamento(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_sepultamentos','func_sepultamentos.php?funcao_js=parent.js_mostrasepultamentos1|cm01_i_codigo|z01_nome','Pesquisa',true);
  }else{
     if(document.form1.cm03_i_sepultamento.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_sepultamentos','func_sepultamentos.php?pesquisa_chave='+document.form1.cm03_i_sepultamento.value+'&funcao_js=parent.js_mostrasepultamentos','Pesquisa',false);
     }else{
       document.form1.z01_nome1.value = '';
     }
  }
}
function js_mostrasepultamentos(chave,erro){
  document.form1.z01_nome1.value = chave;
  if(erro==true){
    document.form1.cm03_i_sepultamento.focus();
    document.form1.cm03_i_sepultamento.value = '';
  }
}
function js_mostrasepultamentos1(chave1,chave2){
  document.form1.cm03_i_sepultamento.value = chave1;
  document.form1.z01_nome1.value = chave2;
  db_iframe_sepultamentos.hide();
}
function js_pesquisacm03_i_proprietario(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cgm','func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
  }else{
     if(document.form1.cm03_i_proprietario.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cgm','func_cgm.php?pesquisa_chave='+document.form1.cm03_i_proprietario.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
     }else{
       document.form1.z01_nome.value = '';
     }
  }
}
function js_mostracgm(chave,erro){
  document.form1.z01_nome.value = chave;
  if(erro==true){
    document.form1.cm03_i_proprietario.focus();
    document.form1.cm03_i_proprietario.value = '';
  }
}
function js_mostracgm1(chave1,chave2){
  document.form1.cm03_i_proprietario.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_cgm.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_jazigos','func_jazigos.php?funcao_js=parent.js_preenchepesquisa|cm03_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_jazigos.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}


</script>
