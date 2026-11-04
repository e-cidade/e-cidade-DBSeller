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
$clitinerarioescolaproc->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed218_i_codigo");
$clrotulo->label("ed82_i_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted222_i_codigo?>">
       <?=@$Led222_i_codigo?>
    </td>
    <td> 
<?php
db_input('ed222_i_codigo',10,$Ied222_i_codigo,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted222_i_itinerario?>">
       <?php
       db_ancora(@$Led222_i_itinerario,"js_pesquisaed222_i_itinerario(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed222_i_itinerario',10,$Ied222_i_itinerario,true,'text',$db_opcao," onchange='js_pesquisaed222_i_itinerario(false);'")
?>
       <?php
db_input('ed218_i_codigo',10,$Ied218_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted222_i_escolaproc?>">
       <?php
       db_ancora(@$Led222_i_escolaproc,"js_pesquisaed222_i_escolaproc(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed222_i_escolaproc',10,$Ied222_i_escolaproc,true,'text',$db_opcao," onchange='js_pesquisaed222_i_escolaproc(false);'")
?>
       <?php
db_input('ed82_i_codigo',20,$Ied82_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed222_i_itinerario(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itinerario','func_itinerario.php?funcao_js=parent.js_mostraitinerario1|ed218_i_codigo|ed218_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed222_i_itinerario.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itinerario','func_itinerario.php?pesquisa_chave='+document.form1.ed222_i_itinerario.value+'&funcao_js=parent.js_mostraitinerario','Pesquisa',false);
     }else{
       document.form1.ed218_i_codigo.value = ''; 
     }
  }
}
function js_mostraitinerario(chave,erro){
  document.form1.ed218_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed222_i_itinerario.focus(); 
    document.form1.ed222_i_itinerario.value = ''; 
  }
}
function js_mostraitinerario1(chave1,chave2){
  document.form1.ed222_i_itinerario.value = chave1;
  document.form1.ed218_i_codigo.value = chave2;
  db_iframe_itinerario.hide();
}
function js_pesquisaed222_i_escolaproc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escolaproc','func_escolaproc.php?funcao_js=parent.js_mostraescolaproc1|ed82_i_codigo|ed82_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed222_i_escolaproc.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escolaproc','func_escolaproc.php?pesquisa_chave='+document.form1.ed222_i_escolaproc.value+'&funcao_js=parent.js_mostraescolaproc','Pesquisa',false);
     }else{
       document.form1.ed82_i_codigo.value = ''; 
     }
  }
}
function js_mostraescolaproc(chave,erro){
  document.form1.ed82_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed222_i_escolaproc.focus(); 
    document.form1.ed222_i_escolaproc.value = ''; 
  }
}
function js_mostraescolaproc1(chave1,chave2){
  document.form1.ed222_i_escolaproc.value = chave1;
  document.form1.ed82_i_codigo.value = chave2;
  db_iframe_escolaproc.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itinerarioescolaproc','func_itinerarioescolaproc.php?funcao_js=parent.js_preenchepesquisa|ed222_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_itinerarioescolaproc.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>