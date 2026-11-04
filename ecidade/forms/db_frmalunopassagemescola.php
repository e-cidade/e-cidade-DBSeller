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
$clalunopassagemescola->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed215_i_codigo");
$clrotulo->label("ed18_i_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted226_i_codigo?>">
       <?=@$Led226_i_codigo?>
    </td>
    <td> 
<?php
db_input('ed226_i_codigo',10,$Ied226_i_codigo,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted226_i_alunopassagem?>">
       <?php
       db_ancora(@$Led226_i_alunopassagem,"js_pesquisaed226_i_alunopassagem(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed226_i_alunopassagem',10,$Ied226_i_alunopassagem,true,'text',$db_opcao," onchange='js_pesquisaed226_i_alunopassagem(false);'")
?>
       <?php
db_input('ed215_i_codigo',10,$Ied215_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted226_i_escola?>">
       <?php
       db_ancora(@$Led226_i_escola,"js_pesquisaed226_i_escola(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed226_i_escola',10,$Ied226_i_escola,true,'text',$db_opcao," onchange='js_pesquisaed226_i_escola(false);'")
?>
       <?php
db_input('ed18_i_codigo',20,$Ied18_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed226_i_alunopassagem(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagem','func_alunopassagem.php?funcao_js=parent.js_mostraalunopassagem1|ed215_i_codigo|ed215_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed226_i_alunopassagem.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagem','func_alunopassagem.php?pesquisa_chave='+document.form1.ed226_i_alunopassagem.value+'&funcao_js=parent.js_mostraalunopassagem','Pesquisa',false);
     }else{
       document.form1.ed215_i_codigo.value = ''; 
     }
  }
}
function js_mostraalunopassagem(chave,erro){
  document.form1.ed215_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed226_i_alunopassagem.focus(); 
    document.form1.ed226_i_alunopassagem.value = ''; 
  }
}
function js_mostraalunopassagem1(chave1,chave2){
  document.form1.ed226_i_alunopassagem.value = chave1;
  document.form1.ed215_i_codigo.value = chave2;
  db_iframe_alunopassagem.hide();
}
function js_pesquisaed226_i_escola(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escola','func_escola.php?funcao_js=parent.js_mostraescola1|ed18_i_codigo|ed18_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed226_i_escola.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escola','func_escola.php?pesquisa_chave='+document.form1.ed226_i_escola.value+'&funcao_js=parent.js_mostraescola','Pesquisa',false);
     }else{
       document.form1.ed18_i_codigo.value = ''; 
     }
  }
}
function js_mostraescola(chave,erro){
  document.form1.ed18_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed226_i_escola.focus(); 
    document.form1.ed226_i_escola.value = ''; 
  }
}
function js_mostraescola1(chave1,chave2){
  document.form1.ed226_i_escola.value = chave1;
  document.form1.ed18_i_codigo.value = chave2;
  db_iframe_escola.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_alunopassagemescola','func_alunopassagemescola.php?funcao_js=parent.js_preenchepesquisa|ed226_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_alunopassagemescola.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>