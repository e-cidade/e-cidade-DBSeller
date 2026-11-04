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
$cldistancia->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j13_descr");
$clrotulo->label("j13_descr");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
 <tr>
  <td nowrap title="<?=@$Ted223_i_codigo?>">
   <?=@$Led223_i_codigo?>
  </td>
  <td>
   <?php db_input('ed223_i_codigo',10,$Ied223_i_codigo,true,'text',3,"")?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted223_i_bairroorigem?>">
   <?php db_ancora(@$Led223_i_bairroorigem,"js_pesquisaed223_i_bairroorigem(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed223_i_bairroorigem',10,$Ied223_i_bairroorigem,true,'text',$db_opcao," onchange='js_pesquisaed223_i_bairroorigem(false);'")?>
   <?php db_input('j13_descrorigem',40,@$Ij13_descrorigem,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted223_i_bairrodestino?>">
   <?php db_ancora(@$Led223_i_bairrodestino,"js_pesquisaed223_i_bairrodestino(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed223_i_bairrodestino',10,$Ied223_i_bairrodestino,true,'text',$db_opcao," onchange='js_pesquisaed223_i_bairrodestino(false);'")?>
   <?php db_input('j13_descrdestino',40,@$Ij13_descrdestino,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted223_f_km?>">
   <?=@$Led223_f_km?>
  </td>
  <td>
   <?php db_input('ed223_f_km',5,$Ied223_f_km,true,'text',$db_opcao,"")?>
  </td>
 </tr>
</table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed223_i_bairroorigem(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bairro','func_bairro.php?funcao_js=parent.js_mostrabairroorigem1|j13_codi|j13_descr','Pesquisa',true);
 }else{
  if(document.form1.ed223_i_bairroorigem.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bairro','func_bairro.php?pesquisa_chave='+document.form1.ed223_i_bairroorigem.value+'&funcao_js=parent.js_mostrabairroorigem','Pesquisa',false);
  }else{
   document.form1.j13_descrorigem.value = '';
  }
 }
}
function js_mostrabairroorigem(chave,erro){
 document.form1.j13_descrorigem.value = chave;
 if(erro==true){
  document.form1.ed223_i_bairroorigem.focus();
  document.form1.ed223_i_bairroorigem.value = '';
 }
}
function js_mostrabairroorigem1(chave1,chave2){
 document.form1.ed223_i_bairroorigem.value = chave1;
 document.form1.j13_descrorigem.value = chave2;
 db_iframe_bairro.hide();
}
function js_pesquisaed223_i_bairrodestino(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bairro','func_bairro.php?funcao_js=parent.js_mostrabairrodestino1|j13_codi|j13_descr','Pesquisa',true);
 }else{
  if(document.form1.ed223_i_bairrodestino.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bairro','func_bairro.php?pesquisa_chave='+document.form1.ed223_i_bairrodestino.value+'&funcao_js=parent.js_mostrabairrodestino','Pesquisa',false);
  }else{
   document.form1.j13_descrdestino.value = '';
  }
 }
}
function js_mostrabairrodestino(chave,erro){
 document.form1.j13_descrdestino.value = chave;
 if(erro==true){
  document.form1.ed223_i_bairrodestino.focus();
  document.form1.ed223_i_bairrodestino.value = '';
 }
}
function js_mostrabairrodestino1(chave1,chave2){
 document.form1.ed223_i_bairrodestino.value = chave1;
 document.form1.j13_descrdestino.value = chave2;
 db_iframe_bairro.hide();
}
function js_pesquisa(){
 js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_distancia','func_distancia.php?funcao_js=parent.js_preenchepesquisa|ed223_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
 db_iframe_distancia.hide();
 <?php
 if($db_opcao!=1){
  echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
 }
 ?>
}
</script>
