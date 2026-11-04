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

//MODULO: orcamento
$clorcduplicacao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("c91_anousudestino");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$To75_sequencial?>">
       <?=@$Lo75_sequencial?>
    </td>
    <td> 
<?php
db_input('o75_sequencial',5,$Io75_sequencial,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_conaberturaexe?>">
       <?php
       db_ancora(@$Lo75_conaberturaexe,"js_pesquisao75_conaberturaexe(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('o75_conaberturaexe',5,$Io75_conaberturaexe,true,'text',$db_opcao," onchange='js_pesquisao75_conaberturaexe(false);'")
?>
       <?php
db_input('c91_anousudestino',5,$Ic91_anousudestino,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_tipo?>">
       <?=@$Lo75_tipo?>
    </td>
    <td> 
<?php
$x = array('1'=>'Despesa','2'=>'Receita');
db_select('o75_tipo',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_previsaoinicial?>">
       <?=@$Lo75_previsaoinicial?>
    </td>
    <td> 
<?php
db_input('o75_previsaoinicial',10,$Io75_previsaoinicial,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_acrescimos?>">
       <?=@$Lo75_acrescimos?>
    </td>
    <td> 
<?php
db_input('o75_acrescimos',10,$Io75_acrescimos,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_reducoes?>">
       <?=@$Lo75_reducoes?>
    </td>
    <td> 
<?php
db_input('o75_reducoes',10,$Io75_reducoes,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_atualizado?>">
       <?=@$Lo75_atualizado?>
    </td>
    <td> 
<?php
db_input('o75_atualizado',10,$Io75_atualizado,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_valorduplicar?>">
       <?=@$Lo75_valorduplicar?>
    </td>
    <td> 
<?php
db_input('o75_valorduplicar',5,$Io75_valorduplicar,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$To75_importar?>">
       <?=@$Lo75_importar?>
    </td>
    <td> 
<?php
$x = array("f"=>"NAO","t"=>"SIM");
db_select('o75_importar',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisao75_conaberturaexe(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_conaberturaexe','func_conaberturaexe.php?funcao_js=parent.js_mostraconaberturaexe1|c91_sequencial|c91_anousudestino','Pesquisa',true);
  }else{
     if(document.form1.o75_conaberturaexe.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_conaberturaexe','func_conaberturaexe.php?pesquisa_chave='+document.form1.o75_conaberturaexe.value+'&funcao_js=parent.js_mostraconaberturaexe','Pesquisa',false);
     }else{
       document.form1.c91_anousudestino.value = ''; 
     }
  }
}
function js_mostraconaberturaexe(chave,erro){
  document.form1.c91_anousudestino.value = chave; 
  if(erro==true){ 
    document.form1.o75_conaberturaexe.focus(); 
    document.form1.o75_conaberturaexe.value = ''; 
  }
}
function js_mostraconaberturaexe1(chave1,chave2){
  document.form1.o75_conaberturaexe.value = chave1;
  document.form1.c91_anousudestino.value = chave2;
  db_iframe_conaberturaexe.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_orcduplicacao','func_orcduplicacao.php?funcao_js=parent.js_preenchepesquisa|o75_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_orcduplicacao.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>