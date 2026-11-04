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
$cllinha->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nome");
$clrotulo->label("ed226_i_codigo");
$clrotulo->label("ed226_c_descr");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted217_i_codigo?>">
       <?=@$Led217_i_codigo?>
    </td>
    <td> 
<?php
db_input('ed217_i_codigo',10,$Ied217_i_codigo,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_i_tipolinha?>">
       <?php
       db_ancora(@$Led217_i_tipolinha,"js_pesquisaed217_i_tipolinha(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed217_i_tipolinha',10,$Ied217_i_tipolinha,true,'text',3," onchange='js_pesquisaed217_i_tipolinha(false);'")
?>
       <?php
db_input('ed226_c_descr',40,@$Ied226_c_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_d_datacad?>">
       <?=@$Led217_d_datacad?>
    </td>
    <td>
<?php
db_inputdata('ed217_d_datacad',@$ed217_d_datacad_dia,@$ed217_d_datacad_mes,@$ed217_d_datacad_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_c_origem?>">
       <?=@$Led217_c_origem?>
    </td>
    <td> 
<?php
db_input('ed217_c_origem',30,$Ied217_c_origem,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_c_destino?>">
       <?=@$Led217_c_destino?>
    </td>
    <td> 
<?php
db_input('ed217_c_destino',30,$Ied217_c_destino,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_c_gratuita?>">
       <?=@$Led217_c_gratuita?>
    </td>
    <td> 
<?php
$x = array('N'=>'NÃO','S'=>'SIM');
db_select('ed217_c_gratuita',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted217_f_kmdia?>">
       <?=@$Led217_f_kmdia?>
    </td>
    <td> 
<?php
db_input('ed217_f_kmdia',10,$Ied217_f_kmdia,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>

function js_pesquisaed217_i_tipolinha(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_tipolinha','func_tipolinha.php?funcao_js=parent.js_mostratipolinha1|ed226_i_codigo|ed226_c_descr','Pesquisa',true);
  }else{
     if(document.form1.ed217_i_tipolinha.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_tipolinha','func_tipolinha.php?pesquisa_chave='+document.form1.ed217_i_tipolinha.value+'&funcao_js=parent.js_mostratipolinha','Pesquisa',false);
     }else{
       document.form1.ed226_c_descr.value = '';
     }
  }
}
function js_mostratipolinha(chave,erro){
  document.form1.ed226_i_codigo.value = chave;
  if(erro==true){ 
    document.form1.ed217_i_tipolinha.focus(); 
    document.form1.ed217_i_tipolinha.value = ''; 
  }
}
function js_mostratipolinha1(chave1,chave2){
  document.form1.ed217_i_tipolinha.value = chave1;
  document.form1.ed226_c_descr.value = chave2;
  db_iframe_tipolinha.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_linha','func_linha.php?funcao_js=parent.js_preenchepesquisa|ed217_i_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_linha.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>