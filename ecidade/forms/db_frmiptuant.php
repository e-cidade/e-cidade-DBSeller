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

//MODULO: cadastro
include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$cliptuant->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j01_numcgm");
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
     $j40_refant = "";
   }
} 
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tj40_matric?>">
       <?php
       db_ancora(@$Lj40_matric,"js_pesquisaj40_matric(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('j40_matric',10,$Ij40_matric,true,'text',$db_opcao," onchange='js_pesquisaj40_matric(false);'")
?>
       <?php
db_input('j01_numcgm',10,$Ij01_numcgm,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tj40_refant?>">
       <?=@$Lj40_refant?>
    </td>
    <td> 
<?php
db_input('j40_refant',20,$Ij40_refant,true,'text',$db_opcao,"")
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
 <table>
  <tr>
    <td valign="top"  align="center">  
    <?php
	 $chavepri= array("j40_matric"=>@$j40_matric);
	 $cliframe_alterar_excluir->chavepri=$chavepri;
	 $cliframe_alterar_excluir->sql     = $cliptuant->sql_query_file($j40_matric);
	 $cliframe_alterar_excluir->campos  ="j40_matric,j40_refant";
	 $cliframe_alterar_excluir->legenda="ITENS LANÇADOS";
	 $cliframe_alterar_excluir->iframe_height ="160";
	 $cliframe_alterar_excluir->iframe_width ="700";
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
function js_pesquisaj40_matric(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptuant','db_iframe_iptubase','func_iptubase.php?funcao_js=parent.js_mostraiptubase1|j01_matric|j01_numcgm','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.j40_matric.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_iptuant','db_iframe_iptubase','func_iptubase.php?pesquisa_chave='+document.form1.j40_matric.value+'&funcao_js=parent.js_mostraiptubase','Pesquisa',false);
     }else{
       document.form1.j01_numcgm.value = ''; 
     }
  }
}
function js_mostraiptubase(chave,erro){
  document.form1.j01_numcgm.value = chave; 
  if(erro==true){ 
    document.form1.j40_matric.focus(); 
    document.form1.j40_matric.value = ''; 
  }
}
function js_mostraiptubase1(chave1,chave2){
  document.form1.j40_matric.value = chave1;
  document.form1.j01_numcgm.value = chave2;
  db_iframe_iptubase.hide();
}
</script>