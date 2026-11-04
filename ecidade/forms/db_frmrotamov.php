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

//MODULO: educação
$clrotamov->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$clrotulo->label("nome");
$clrotulo->label("ve60_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
 <tr>
  <td nowrap title="<?=@$Ted220_i_codigo?>">
   <?=@$Led220_i_codigo?>
  </td>
  <td>
   <?php db_input('ed220_i_codigo',10,$Ied220_i_codigo,true,'text',3,"")?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted220_i_rota?>">
   <?php db_ancora(@$Led220_i_rota,"js_pesquisaed220_i_rota(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed220_i_rota',10,$Ied220_i_rota,true,'text',$db_opcao," onchange='js_pesquisaed220_i_rota(false);'")?>
   <?php db_input('ed217_c_descr',40,@$Ied217_c_descr,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted220_i_veicretirada?>">
   <?php db_ancora(@$Led220_i_veicretirada,"js_pesquisaed220_i_veicretirada(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed220_i_veicretirada',10,$Ied220_i_veicretirada,true,'text',$db_opcao," onchange='js_pesquisaed220_i_veicretirada(false);'")?>
   <?php db_input('ve60_codigo',10,$Ive60_codigo,true,'text',3,'')?>
  </td>
 </tr>
</table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed220_i_rota(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rota','func_rota.php?funcao_js=parent.js_mostrarota1|ed217_i_codigo|ed217_c_descr','Pesquisa',true);
 }else{
  if(document.form1.ed220_i_rota.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rota','func_rota.php?pesquisa_chave='+document.form1.ed220_i_rota.value+'&funcao_js=parent.js_mostrarota','Pesquisa',false);
  }else{
   document.form1.ed217_c_descr.value = '';
  }
 }
}
function js_mostrarota(chave,erro){
 document.form1.ed217_c_descr.value = chave;
 if(erro==true){
  document.form1.ed220_i_rota.focus();
  document.form1.ed220_i_rota.value = '';
 }
}
function js_mostrarota1(chave1,chave2){
 document.form1.ed220_i_rota.value = chave1;
 document.form1.ed217_c_descr.value = chave2;
 db_iframe_rota.hide();
}
function js_pesquisaed220_i_veicretirada(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_veicretirada','func_veicretirada.php?funcao_js=parent.js_mostraveicretirada1|ve60_codigo|ve60_codigo','Pesquisa',true);
 }else{
  if(document.form1.ed220_i_veicretirada.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_veicretirada','func_veicretirada.php?pesquisa_chave='+document.form1.ed220_i_veicretirada.value+'&funcao_js=parent.js_mostraveicretirada','Pesquisa',false);
  }else{
   document.form1.ve60_codigo.value = '';
  }
 }
}
function js_mostraveicretirada(chave,erro){
 document.form1.ve60_codigo.value = chave;
 if(erro==true){
  document.form1.ed220_i_veicretirada.focus();
  document.form1.ed220_i_veicretirada.value = '';
 }
}
function js_mostraveicretirada1(chave1,chave2){
 document.form1.ed220_i_veicretirada.value = chave1;
 document.form1.ve60_codigo.value = chave2;
 db_iframe_veicretirada.hide();
}
function js_pesquisa(){
 js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rotamov','func_rotamov.php?funcao_js=parent.js_preenchepesquisa|ed220_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
 db_iframe_rotamov.hide();
 <?php
 if($db_opcao!=1){
   echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
 }
 ?>
}
</script>
