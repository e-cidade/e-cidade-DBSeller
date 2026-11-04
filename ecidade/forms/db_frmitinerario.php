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
$clitinerario->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed217_i_codigo");
$clrotulo->label("nome");
$clrotulo->label("ed18_i_codigo");
$clrotulo->label("ed18_c_nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted218_i_codigo?>">
       <?=@$Led218_i_codigo?>
    </td>
    <td> 
        <?php
        db_input('ed218_i_codigo',10,$Ied218_i_codigo,true,'text',3,"")
        ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted218_d_datacad?>">
       <?=@$Led218_d_datacad?>
    </td>
    <td> 
        <?php
        db_inputdata('ed218_d_datacad',@$ed218_d_datacad_dia,@$ed218_d_datacad_mes,@$ed218_d_datacad_ano,true,'text',$db_opcao,"")
        ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted218_v_nome?>">
       <?=@$Led218_v_nome?>
    </td>
    <td> 
        <?php
        db_input('ed218_v_nome',50,$Ied218_v_nome,true,'text',$db_opcao,"")
        ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted218_i_linha?>">
       <?php
       db_ancora(@$Led218_i_linha,"js_pesquisaed218_i_linha(true);",$db_opcao);
       ?>
    </td>
    <td> 
        <?php
        db_input('ed218_i_linha',10,$Ied218_i_linha,true,'text',3," onchange='js_pesquisaed218_i_linha(false);'")
        ?>
       <?php
        db_input('ed217_c_origem',40,@$Ied217_c_origem,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted18_i_codigo?>">
       <?php
       db_ancora("<b>Escola:</b>","js_pesquisaed18_i_codigo(true);",$db_opcao);
       ?>
    </td>
    <td>
        <?php
        db_input('ed18_i_codigo',10,@$Ied18_i_codigo,true,'text',3," onchange='js_pesquisaed18_i_codigo(false);'")
        ?>
       <?php
        db_input('ed18_c_nome',60,@$Ied18_c_nome,true,'text',3,'');
        db_input('tipoescola',10,@$Itipoescola,true,'hidden',$db_opcao,'');
        db_input('ed221_i_codigo',10,@$Ied221_i_codigo,true,'hidden',$db_opcao,'');
        db_input('origemescola',10,@$Iorigemescola,true,'hidden',$db_opcao,'');
       ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed218_i_linha(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_linha','func_linha.php?funcao_js=parent.js_mostralinha1|ed217_i_codigo|ed217_c_origem','Pesquisa',true);
  }else{
     if(document.form1.ed218_i_linha.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_linha','func_linha.php?pesquisa_chave='+document.form1.ed218_i_linha.value+'&funcao_js=parent.js_mostralinha','Pesquisa',false);
     }else{
       document.form1.ed217_c_origem.value = '';
     }
  }
}
function js_mostralinha(chave,erro){
  document.form1.ed217_i_codigo.value = chave;
  if(erro==true){
    document.form1.ed218_i_linha.focus();
    document.form1.ed218_i_linha.value = '';
  }
}
function js_mostralinha1(chave1,chave2){
  document.form1.ed218_i_linha.value = chave1;
  document.form1.ed217_c_origem.value = chave2;
  db_iframe_linha.hide();
}


function js_pesquisaed18_i_codigo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escola','func_itinerarioescola.php?funcao_js=parent.js_mostraescola1|ed18_i_codigo|ed18_c_nome|tipoescola','Pesquisa',true);
  }else{
     if(document.form1.ed18_i_escola.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_escola','func_itinerarioescola.php?pesquisa_chave='+document.form1.ed18_i_codigo.value+'&funcao_js=parent.js_mostraescola','Pesquisa',false);
     }else{
       document.form1.ed18_c_nome.value = '';
     }
  }
}
function js_mostraescola(chave,chave1,erro){
  document.form1.ed18_c_nome.value = chave;
  document.form1.tipoescola.value = chave1;
  if(erro==true){
    document.form1.ed18_i_codigo.focus();
    document.form1.ed18_i_codigo.value = '';
  }
}
function js_mostraescola1(chave1,chave2,chave3){
  document.form1.ed18_i_codigo.value = chave1;
  document.form1.ed18_c_nome.value = chave2;
  document.form1.tipoescola.value = chave3;
  db_iframe_escola.hide();
}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_itinerario','func_itinerario.php?funcao_js=parent.js_preenchepesquisa|ed218_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_itinerario.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>