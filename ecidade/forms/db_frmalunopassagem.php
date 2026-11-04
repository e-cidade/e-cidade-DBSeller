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
$clalunopassagem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nome");
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed18_i_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
 <tr>
  <td nowrap title="<?=@$Ted215_i_codigo?>">
   <?=@$Led215_i_codigo?>
  </td>
  <td>
   <?php db_input('ed215_i_codigo',10,$Ied215_i_codigo,true,'text',3,"")?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted215_i_aluno?>">
   <?php db_ancora(@$Led215_i_aluno,"js_pesquisaed215_i_aluno(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed215_i_aluno',10,$Ied215_i_aluno,true,'text',3," onchange='js_pesquisaed215_i_aluno(false);'")?>
   <?php db_input('ed47_v_nome',60,@$Ied47_v_nome,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="">
   <b>Bairro do Aluno:</b>
  </td>
  <td>
   <?php db_input('bairroaluno',40,@$bairroaluno,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted18_i_codigo?>">
   <?php db_ancora("<b>Escola:</b>","js_pesquisaed18_i_codigo(true);",3);?>
  </td>
  <td>
   <?php db_input('ed18_i_codigo',10,@$Ied18_i_codigo,true,'text',3," onchange='js_pesquisaed18_i_codigo(false);'")?>
   <?php db_input('ed18_c_nome',60,@$Ied18_c_nome,true,'text',3,'');?>
   <?php db_input('tipoescola',10,@$Itipoescola,true,'hidden',$db_opcao,'');?>
   <?php db_input('ed226_i_codigo',10,@$Ied226_i_codigo,true,'hidden',$db_opcao,'');?>
   <?php db_input('origemescola',10,@$Iorigemescola,true,'hidden',$db_opcao,'');?>
  </td>
 </tr>
 <tr>
  <td nowrap title="">
   <b>Bairro da Escola:</b>
  </td>
  <td>
   <?php db_input('bairroescola',40,@$Ibairroescola,true,'text',3,'')?>
  </td>
 </tr>
  <tr>
  <td nowrap title="<?=@$Ted215_i_linha?>">
   <?php db_ancora(@$Led215_i_linha,"js_pesquisaed215_i_linha(true);",$db_opcao);?>
  </td>
  <td>
   <?php db_input('ed215_i_linha',10,$Ied215_i_linha,true,'text',3," onchange='js_pesquisaed215_i_linha(false);'")?>
   <?php db_input('ed217_c_origem',60,@$Ied217_c_origem,true,'text',3,'')?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted215_i_ano?>">
   <b>Ano</b>
  </td>
  <td>
   <?php db_input('ed215_i_ano',4,$Ied215_i_ano,true,'text',$db_opcao,"")?>
  </td>
 </tr>
 <tr>
  <td nowrap title="<?=@$Ted215_d_datacad?>">
   <?=@$Led215_d_datacad?>
  </td>
  <td>
   <?php db_inputdata('ed215_d_datacad',@$ed215_d_datacad_dia,@$ed215_d_datacad_mes,@$ed215_d_datacad_ano,true,'text',$db_opcao,"")?>
  </td>
 </tr>
</table>
</center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed215_i_linha(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_linha','func_linha.php?funcao_js=parent.js_mostralinha1|ed217_i_codigo|ed217_c_origem','Pesquisa',true);
  }else{
     if(document.form1.ed215_i_linha.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_linha','func_linha.php?pesquisa_chave='+document.form1.ed215_i_linha.value+'&funcao_js=parent.js_mostralinha','Pesquisa',false);
     }else{
       document.form1.ed217_c_origem.value = '';
     }
  }
}
function js_mostralinha(chave,erro){
  document.form1.ed217_i_codigo.value = chave;
  if(erro==true){
    document.form1.ed215_i_linha.focus();
    document.form1.ed215_i_linha.value = '';
  }
}
function js_mostralinha1(chave1,chave2){
  document.form1.ed215_i_linha.value = chave1;
  document.form1.ed217_c_origem.value = chave2;
  db_iframe_linha.hide();
}



function js_pesquisaed215_i_aluno(mostra){
 if(mostra==true){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagemescola','func_alunopassagemescola.php?funcao_js=parent.js_mostraalunopassagemescola1|ed47_i_codigo|ed47_v_nome|bairroaluno|ed18_i_codigo|ed18_c_nome|bairroescola|tipoescola','Pesquisa',true);
 }else{
  if(document.form1.ed215_i_aluno.value != ''){
   js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagemescola','func_alunopassagemescola.php?pesquisa_chave='+document.form1.ed215_i_aluno.value+'&funcao_js=parent.js_mostraalunopassagemescola','Pesquisa',false);
  }else{
   document.form1.ed47_v_nome.value = '';
  }
 }
}
function js_mostraalunopassagemescola(chave1,chave2,chave3,chave4,chave5,chave6,erro){
 document.form1.ed47_v_nome.value = chave1
 document.form1.bairroaluno.value = chave2;
 document.form1.ed18_i_codigo.value = chave3;
 document.form1.ed18_c_nome.value = chave4;
 document.form1.bairroescola.value = chave5;
 document.form1.tipoescola.value = chave6;
 if(erro==true){
  document.form1.ed215_i_aluno.focus();
  document.form1.ed215_i_aluno.value = '';
 }
}
function js_mostraalunopassagemescola1(chave1,chave2,chave3,chave4,chave5,chave6,chave7){
 document.form1.ed215_i_aluno.value = chave1;
 document.form1.ed47_v_nome.value = chave2;
 document.form1.bairroaluno.value = chave3;
 document.form1.ed18_i_codigo.value = chave4;
 document.form1.ed18_c_nome.value = chave5;
 document.form1.bairroescola.value = chave6;
 document.form1.tipoescola.value = chave7;
 db_iframe_alunopassagemescola.hide();
}
function js_pesquisa(){
 js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagem','func_alunopassagem.php?funcao_js=parent.js_preenchepesquisa|ed215_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
 db_iframe_alunopassagem.hide();
 <?php
 if($db_opcao!=1){
   echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
 }
 ?>
}
</script>
