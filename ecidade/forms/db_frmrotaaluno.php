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
$clrotaaluno->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
 <tr>
  <td nowrap title="<?=@$Ted219_i_codigo?>">
   <?=@$Led219_i_codigo?>
  </td>
  <td>
   <?php db_input('ed219_i_codigo',10,$Ied219_i_codigo,true,'text',3,"")?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted219_i_rota?>">
   <?php db_ancora(@$Led219_i_rota,"js_pesquisaed219_i_rota(true);",$db_opcao1);?>
  </td>
  <td>
   <?php db_input('ed219_i_rota',10,$Ied219_i_rota,true,'text',$db_opcao1," onchange='js_pesquisaed219_i_rota(false);'")?>
   <?php db_input('ed217_c_descr',40,@$Ied217_c_descr,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted219_i_aluno?>">
   <?php db_ancora(@$Led219_i_aluno,"js_pesquisaed219_i_aluno(true);",$db_opcao1);?>
  </td>
  <td>
   <?php db_input('ed219_i_aluno',10,$Ied219_i_aluno,true,'text',3," onchange='js_pesquisaed219_i_aluno(false);'")?>
   <?php db_input('ed47_v_nome',60,@$Ied47_v_nome,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted219_d_datainicio?>">
   <?=@$Led219_d_datainicio?>
  </td>
  <td>
   <?php db_inputdata('ed219_d_datainicio',@$ed219_d_datainicio_dia,@$ed219_d_datainicio_mes,@$ed219_d_datainicio_ano,true,'text',$db_opcao,"")?>
  </td>
 </tr>
</table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed219_i_rota(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rota','func_rota.php?funcao_js=parent.js_mostrarota1|ed217_i_codigo|ed217_c_descr','Pesquisa',true);
 }else{
  if(document.form1.ed219_i_rota.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rota','func_rota.php?pesquisa_chave='+document.form1.ed219_i_rota.value+'&funcao_js=parent.js_mostrarota','Pesquisa',false);
  }else{
   document.form1.ed217_c_descr.value = '';
  }
 }
}
function js_mostrarota(chave,erro){
 document.form1.ed217_c_descr.value = chave;
 if(erro==true){
  document.form1.ed219_i_rota.focus();
  document.form1.ed219_i_rota.value = '';
 }
}
function js_mostrarota1(chave1,chave2){
 document.form1.ed219_i_rota.value = chave1;
 document.form1.ed217_c_descr.value = chave2;
 db_iframe_rota.hide();
}
function js_pesquisaed219_i_aluno(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_aluno','func_alunorota.php?funcao_js=parent.js_mostraaluno1|ed47_i_codigo|ed47_v_nome','Pesquisa',true);
 }else{
  if(document.form1.ed219_i_aluno.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_aluno','func_alunorota.php?pesquisa_chave='+document.form1.ed219_i_aluno.value+'&funcao_js=parent.js_mostraaluno','Pesquisa',false);
  }else{
   document.form1.ed47_v_nome.value = '';
  }
 }
}
function js_mostraaluno(chave,erro){
 document.form1.ed47_v_nome.value = chave;
 if(erro==true){
  document.form1.ed219_i_aluno.focus();
  document.form1.ed219_i_aluno.value = '';
 }
}
function js_mostraaluno1(chave1,chave2){
 document.form1.ed219_i_aluno.value = chave1;
 document.form1.ed47_v_nome.value = chave2;
 db_iframe_aluno.hide();
}
function js_pesquisa(){
 js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rotaaluno','func_rotaaluno.php?funcao_js=parent.js_preenchepesquisa|ed219_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
 db_iframe_rotaaluno.hide();
 <?php
 if($db_opcao!=1){
   echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
 }
 ?>
}
</script>
