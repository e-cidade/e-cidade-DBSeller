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
$clalunopassagemqtd->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nome");
$clrotulo->label("ed215_i_codigo");
$clrotulo->label("ed230_f_valor");
$clrotulo->label("ed47_v_nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
<tr>
    <td nowrap title="<?=@$Ted227_i_codigo?>">
       <?=@$Led227_i_codigo?>
    </td>
    <td>
<?php
db_input('ed227_i_codigo',10,$Ied227_i_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_i_alunopassagem?>">
       <?php
       db_ancora(@$Led227_i_alunopassagem,"js_pesquisaed227_i_alunopassagem(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed227_i_alunopassagem',10,$Ied227_i_alunopassagem,true,'text',3," onchange='js_pesquisaed227_i_alunopassagem(false);'")
?>
       <?php
db_input('ed47_v_nome',40,$Ied47_v_nome,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_i_valorpassagem?>">
       <?php
       db_ancora(@$Led227_i_valorpassagem,"js_pesquisaed227_i_valorpassagem(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed227_i_valorpassagem',10,$Ied227_i_valorpassagem,true,'text',3," onchange='js_pesquisaed227_i_valorpassagem(false);'")
?>
       <?php
db_input('ed230_f_valor',40,$Ied230_f_valor,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_i_qtde?>">
       <?=@$Led227_i_qtde?>
    </td>
    <td> 
<?php
db_input('ed227_i_qtde',10,$Ied227_i_qtde,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_d_datainicio?>">
       <?=@$Led227_d_datainicio?>
    </td>
    <td> 
<?php
db_inputdata('ed227_d_datainicio',@$ed227_d_datainicio_dia,@$ed227_d_datainicio_mes,@$ed227_d_datainicio_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_d_datafim?>">
       <?=@$Led227_d_datafim?>
    </td>
    <td> 
<?php
db_inputdata('ed227_d_datafim',@$ed227_d_datafim_dia,@$ed227_d_datafim_mes,@$ed227_d_datafim_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted227_d_datacad?>">
       <?=@$Led227_d_datacad?>
    </td>
    <td> 
<?php
db_inputdata('ed227_d_datacad',@$ed227_d_datacad_dia,@$ed227_d_datacad_mes,@$ed227_d_datacad_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed227_i_alunopassagem(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagem','func_alunopassagem.php?funcao_js=parent.js_mostraalunopassagem1|ed215_i_codigo|ed47_v_nome','Pesquisa',true);
  }else{
     if(document.form1.ed227_i_alunopassagem.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagem','func_alunopassagem.php?pesquisa_chave='+document.form1.ed227_i_alunopassagem.value+'&funcao_js=parent.js_mostraalunopassagem','Pesquisa',false);
     }else{
       document.form1.ed215_i_codigo.value = '';
     }
  }
}
function js_mostraalunopassagem(chave,erro){
  document.form1.ed227_i_alunopassagem.value = chave;
  if(erro==true){ 
    document.form1.ed215_i_codigo.focus();
    document.form1.ed227_i_alunopassagem.value = ''; 
  }
}
function js_mostraalunopassagem1(chave1,chave2){
  document.form1.ed227_i_alunopassagem.value = chave1;
  document.form1.ed47_v_nome.value = chave2;
  db_iframe_alunopassagem.hide();
}
function js_pesquisaed227_i_valorpassagem(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_valorpassagem','func_valorpassagem.php?funcao_js=parent.js_mostravalorpassagem1|ed230_i_codigo|ed230_f_valor','Pesquisa',true);
  }else{
     if(document.form1.ed227_i_valorpassagem.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_valorpassagem','func_valorpassagem.php?pesquisa_chave='+document.form1.ed227_i_valorpassagem.value+'&funcao_js=parent.js_mostravalorpassagem','Pesquisa',false);
     }else{
       document.form1.ed230_f_valor.value = '';
     }
  }
}
function js_mostravalorpassagem(chave,erro){
  document.form1.ed230_f_valor.value = chave;
  if(erro==true){ 
    document.form1.ed227_i_valorpassagem.focus(); 
    document.form1.ed227_i_valorpassagem.value = ''; 
  }
}
function js_mostravalorpassagem1(chave1,chave2){
  document.form1.ed227_i_valorpassagem.value = chave1;
  document.form1.ed230_f_valor.value = chave2;
  db_iframe_valorpassagem.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagemqtd','func_alunopassagemqtd.php?funcao_js=parent.js_preenchepesquisa|ed227_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_alunopassagemqtd.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>