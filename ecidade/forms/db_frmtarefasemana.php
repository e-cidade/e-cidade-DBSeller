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

//MODULO: atendimento
$cltarefasemana->rotulo->label();
      if($db_opcao==1){
 	   $db_action="ate1_tarefasemana004.php";
      }else if($db_opcao==2||$db_opcao==22){
 	   $db_action="ate1_tarefasemana005.php";
      }else if($db_opcao==3||$db_opcao==33){
 	   $db_action="ate1_tarefasemana006.php";
      }  
?>
<form name="form1" method="post" action="<?=$db_action?>">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tat19_sequencial?>">
       <?=@$Lat19_sequencial?>
    </td>
    <td> 
<?php
db_input('at19_sequencial',8,$Iat19_sequencial,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tat19_descr?>">
       <?=@$Lat19_descr?>
    </td>
    <td> 
<?php
db_input('at19_descr',40,$Iat19_descr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tat19_dtini?>">
       <?=@$Lat19_dtini?>
    </td>
    <td> 
<?php
db_inputdata('at19_dtini',@$at19_dtini_dia,@$at19_dtini_mes,@$at19_dtini_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tat19_dtfim?>">
       <?=@$Lat19_dtfim?>
    </td>
    <td> 
<?php
db_inputdata('at19_dtfim',@$at19_dtfim_dia,@$at19_dtfim_mes,@$at19_dtfim_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tarefasemana','db_iframe_tarefasemana','func_tarefasemana.php?funcao_js=parent.js_preenchepesquisa|at19_sequencial','Pesquisa',true,'0');
}
function js_preenchepesquisa(chave){
  db_iframe_tarefasemana.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>