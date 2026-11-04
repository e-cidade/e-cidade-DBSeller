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
$clmatriculadisciplina->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed297_sequencial");
$clrotulo->label("ed12_i_codigo");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ted298_sequencial?>">
       <?=@$Led298_sequencial?>
    </td>
    <td> 
<?php
db_input('ed298_sequencial',10,$Ied298_sequencial,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted298_matriculadependencia?>">
       <?php
       db_ancora(@$Led298_matriculadependencia,"js_pesquisaed298_matriculadependencia(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed298_matriculadependencia',10,$Ied298_matriculadependencia,true,'text',$db_opcao," onchange='js_pesquisaed298_matriculadependencia(false);'")
?>
       <?php
db_input('ed297_sequencial',10,$Ied297_sequencial,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ted298_disciplina?>">
       <?php
       db_ancora(@$Led298_disciplina,"js_pesquisaed298_disciplina(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('ed298_disciplina',10,$Ied298_disciplina,true,'text',$db_opcao," onchange='js_pesquisaed298_disciplina(false);'")
?>
       <?php
db_input('ed12_i_codigo',20,$Ied12_i_codigo,true,'text',3,'')
       ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisaed298_matriculadependencia(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matriculadependecia','func_matriculadependecia.php?funcao_js=parent.js_mostramatriculadependecia1|ed297_sequencial|ed297_sequencial','Pesquisa',true);
  }else{
     if(document.form1.ed298_matriculadependencia.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matriculadependecia','func_matriculadependecia.php?pesquisa_chave='+document.form1.ed298_matriculadependencia.value+'&funcao_js=parent.js_mostramatriculadependecia','Pesquisa',false);
     }else{
       document.form1.ed297_sequencial.value = ''; 
     }
  }
}
function js_mostramatriculadependecia(chave,erro){
  document.form1.ed297_sequencial.value = chave; 
  if(erro==true){ 
    document.form1.ed298_matriculadependencia.focus(); 
    document.form1.ed298_matriculadependencia.value = ''; 
  }
}
function js_mostramatriculadependecia1(chave1,chave2){
  document.form1.ed298_matriculadependencia.value = chave1;
  document.form1.ed297_sequencial.value = chave2;
  db_iframe_matriculadependecia.hide();
}
function js_pesquisaed298_disciplina(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_disciplina','func_disciplina.php?funcao_js=parent.js_mostradisciplina1|ed12_i_codigo|ed12_i_codigo','Pesquisa',true);
  }else{
     if(document.form1.ed298_disciplina.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_disciplina','func_disciplina.php?pesquisa_chave='+document.form1.ed298_disciplina.value+'&funcao_js=parent.js_mostradisciplina','Pesquisa',false);
     }else{
       document.form1.ed12_i_codigo.value = ''; 
     }
  }
}
function js_mostradisciplina(chave,erro){
  document.form1.ed12_i_codigo.value = chave; 
  if(erro==true){ 
    document.form1.ed298_disciplina.focus(); 
    document.form1.ed298_disciplina.value = ''; 
  }
}
function js_mostradisciplina1(chave1,chave2){
  document.form1.ed298_disciplina.value = chave1;
  document.form1.ed12_i_codigo.value = chave2;
  db_iframe_disciplina.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matriculadisciplina','func_matriculadisciplina.php?funcao_js=parent.js_preenchepesquisa|ed298_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_matriculadisciplina.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>