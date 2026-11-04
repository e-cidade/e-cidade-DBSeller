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

//MODULO: escola
$clparametrodependenciaetapa->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed295_sequencial");
$clrotulo->label("ed11_i_codigo");
$clrotulo->label("ed29_i_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted296_sequencial?>">
       <?=@$Led296_sequencial?>
    </td>
    <td> 
<?php
db_input('ed296_sequencial',10,$Ied296_sequencial,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted296_parametrodependencia?>">
       <?php
       db_ancora(@$Led296_parametrodependencia,"js_pesquisaed296_parametrodependencia(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed296_parametrodependencia',10,$Ied296_parametrodependencia,true,'text',$db_opcao," onchange='js_pesquisaed296_parametrodependencia(false);'")
?>
       <?php
db_input('ed295_sequencial',10,$Ied295_sequencial,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted296_etapa?>">
       <?php
       db_ancora(@$Led296_etapa,"js_pesquisaed296_etapa(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed296_etapa',10,$Ied296_etapa,true,'text',$db_opcao," onchange='js_pesquisaed296_etapa(false);'")
?>
       <?php
db_input('ed11_i_codigo',20,$Ied11_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted296_cursoedu?>">
       <?php
       db_ancora(@$Led296_cursoedu,"js_pesquisaed296_cursoedu(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed296_cursoedu',10,$Ied296_cursoedu,true,'text',$db_opcao," onchange='js_pesquisaed296_cursoedu(false);'")
?>
       <?php
db_input('ed29_i_codigo',20,$Ied29_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed296_parametrodependencia(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_parametrodependencia','func_parametrodependencia.php?funcao_js=parent.js_mostraparametrodependencia1|ed295_sequencial|ed295_sequencial','Pesquisa',true);
  }else{
     if(document.form1.ed296_parametrodependencia.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_parametrodependencia','func_parametrodependencia.php?pesquisa_chave='+document.form1.ed296_parametrodependencia.value+'&funcao_js=parent.js_mostraparametrodependencia','Pesquisa',false);
     }else{
       document.form1.ed295_sequencial.value = ''; 
     }
  }
}
function js_mostraparametrodependencia(chave,erro){
  document.form1.ed295_sequencial.value = chave; 
  if(erro==true){ 
    document.form1.ed296_parametrodependencia.focus(); 
    document.form1.ed296_parametrodependencia.value = ''; 
  }
}
function js_mostraparametrodependencia1(chave1,chave2){
  document.form1.ed296_parametrodependencia.value = chave1;
  document.form1.ed295_sequencial.value = chave2;
  db_iframe_parametrodependencia.hide();
}
function js_pesquisaed296_etapa(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_serie','func_serie.php?funcao_js=parent.js_mostraserie1|ed11_i_codigo|ed11_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed296_etapa.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_serie','func_serie.php?pesquisa_chave='+document.form1.ed296_etapa.value+'&funcao_js=parent.js_mostraserie','Pesquisa',false);
     }else{
       document.form1.ed11_i_codigo.value = ''; 
     }
  }
}
function js_mostraserie(chave,erro){
  document.form1.ed11_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed296_etapa.focus(); 
    document.form1.ed296_etapa.value = ''; 
  }
}
function js_mostraserie1(chave1,chave2){
  document.form1.ed296_etapa.value = chave1;
  document.form1.ed11_i_codigo.value = chave2;
  db_iframe_serie.hide();
}
function js_pesquisaed296_cursoedu(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cursoedu','func_cursoedu.php?funcao_js=parent.js_mostracursoedu1|ed29_i_codigo|ed29_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed296_cursoedu.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cursoedu','func_cursoedu.php?pesquisa_chave='+document.form1.ed296_cursoedu.value+'&funcao_js=parent.js_mostracursoedu','Pesquisa',false);
     }else{
       document.form1.ed29_i_codigo.value = ''; 
     }
  }
}
function js_mostracursoedu(chave,erro){
  document.form1.ed29_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed296_cursoedu.focus(); 
    document.form1.ed296_cursoedu.value = ''; 
  }
}
function js_mostracursoedu1(chave1,chave2){
  document.form1.ed296_cursoedu.value = chave1;
  document.form1.ed29_i_codigo.value = chave2;
  db_iframe_cursoedu.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_parametrodependenciaetapa','func_parametrodependenciaetapa.php?funcao_js=parent.js_preenchepesquisa|ed296_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_parametrodependenciaetapa.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>