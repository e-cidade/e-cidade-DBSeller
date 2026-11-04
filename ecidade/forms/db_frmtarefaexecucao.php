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
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$cltarefaexecucao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("at40_descr");
$clrotulo->label("at19_descr");
if(isset($db_opcaoal)){
   $db_opcao=33;
    $db_botao=false;
}else if(isset($opcao) && $opcao=="alterar"){
    $db_botao=true;
    $db_opcao = 2;
}else if(isset($opcao) && $opcao=="excluir"){
    $db_opcao = 3;
    $db_botao=true;
}else{  
    $db_opcao = 1;
    $db_botao=true;
    if(isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && $sqlerro==false ) ){
     $at09_tarefa = "";
     //$at09_semana = "";
     $at09_situacao = "";
     $at09_sequencial = "";
   }
} 
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tat09_sequencial?>">
       <?=@$Lat09_sequencial?>
    </td>
    <td> 
<?php
db_input('at09_sequencial',8,$Iat09_sequencial,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tat09_semana?>">
       <?php
       db_ancora(@$Lat09_semana,"js_pesquisaat09_semana(true);",3);
       ?>
    </td>
    <td> 
<?php
db_input('at09_semana',4,$Iat09_semana,true,'text',3," onchange='js_pesquisaat09_semana(false);'")
?>
       <?php
db_input('at19_descr',40,$Iat19_descr,true,'text',3,'')
       ?>
    </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Tat09_tarefa?>">
       <?php
       db_ancora(@$Lat09_tarefa,"js_pesquisaat09_tarefa(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('at09_tarefa',10,$Iat09_tarefa,true,'text',$db_opcao," onchange='js_pesquisaat09_tarefa(false);'")
?>
       <?php
db_input('at40_descr',1,$Iat40_descr,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tat09_situacao?>">
       <?=@$Lat09_situacao?>
    </td>
    <td> 
<?php
$x = array('0'=>'Planejada','1'=>'Não Planejada', '2'=>'Antecipada', '3'=>'Pendente');
db_select('at09_situacao',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  </tr>
    <td colspan="2" align="center">
 <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
 <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?=($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
    </td>
  </tr>
  </table>
 <table width="100%">
  <tr>
    <td valign="top"  align="center">  
    <?php
	 $chavepri= array("at09_sequencial"=>@$at09_sequencial);
	 $cliframe_alterar_excluir->chavepri=$chavepri;

	 $campos  = " at09_sequencial, ";
	 $campos .= " at09_tarefa, ";
   $campos .= " at09_semana, ";
   $campos .= " case ";
   $campos .= "   when at09_situacao = 0 then 'Planejada' ";
   $campos .= "   when at09_situacao = 1 then 'Não Planejada' ";
   $campos .= "   when at09_situacao = 2 then 'Antecipada' ";
   $campos .= "   when at09_situacao = 3 then 'Pendente' ";
   $campos .= "   else 'N/D' ";
   $campos .= " end as at09_situacao";

	 $cliframe_alterar_excluir->sql     = $cltarefaexecucao->sql_query_file(null, $campos, "at09_sequencial", "at09_semana = $at09_semana");
	 $cliframe_alterar_excluir->campos  = "at09_sequencial,at09_tarefa,at09_semana,at09_situacao";

	 $cliframe_alterar_excluir->legenda="Tarefas da Semana";
	 $cliframe_alterar_excluir->iframe_height ="160";
	 $cliframe_alterar_excluir->iframe_width ="100%";
	 $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
    ?>
    </td>
   </tr>
 </table>
  </center>
</form>
<script>
function js_cancelar(){
  var opcao = document.createElement("input");
  opcao.setAttribute("type","hidden");
  opcao.setAttribute("name","novo");
  opcao.setAttribute("value","true");
  document.form1.appendChild(opcao);
  document.form1.submit();
}
function js_pesquisaat09_tarefa(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tarefaexecucao','db_iframe_tarefa','func_tarefa.php?funcao_js=parent.js_mostratarefa1|at40_sequencial|at40_descr','Pesquisa',true,'0');
  }else{
     if(document.form1.at09_tarefa.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tarefaexecucao','db_iframe_tarefa','func_tarefa.php?pesquisa_chave='+document.form1.at09_tarefa.value+'&funcao_js=parent.js_mostratarefa','Pesquisa',false);
     }else{
       document.form1.at40_descr.value = ''; 
     }
  }
}
function js_mostratarefa(chave,erro){
  document.form1.at40_descr.value = chave; 
  if(erro==true){ 
    document.form1.at09_tarefa.focus(); 
    document.form1.at09_tarefa.value = ''; 
  }
}
function js_mostratarefa1(chave1,chave2){
  document.form1.at09_tarefa.value = chave1;
  document.form1.at40_descr.value = chave2;
  db_iframe_tarefa.hide();
}
function js_pesquisaat09_semana(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tarefaexecucao','db_iframe_tarefasemana','func_tarefasemana.php?funcao_js=parent.js_mostratarefasemana1|at19_sequencial|at19_descr','Pesquisa',true,'0');
  }else{
     if(document.form1.at09_semana.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_tarefaexecucao','db_iframe_tarefasemana','func_tarefasemana.php?pesquisa_chave='+document.form1.at09_semana.value+'&funcao_js=parent.js_mostratarefasemana','Pesquisa',false);
     }else{
       document.form1.at19_descr.value = ''; 
     }
  }
}
function js_mostratarefasemana(chave,erro){
  document.form1.at19_descr.value = chave; 
  if(erro==true){ 
    document.form1.at09_semana.focus(); 
    document.form1.at09_semana.value = ''; 
  }
}
function js_mostratarefasemana1(chave1,chave2){
  document.form1.at09_semana.value = chave1;
  document.form1.at19_descr.value = chave2;
  db_iframe_tarefasemana.hide();
}
</script>