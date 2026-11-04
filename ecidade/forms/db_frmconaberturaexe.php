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

//MODULO: contabilidade
$clconaberturaexe->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nomeinst");
$clrotulo->label("nome");
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tc97_sequencial?>">
       <?=@$Lc97_sequencial?>
    </td>
    <td> 
<?php
db_input('c97_sequencial',5,$Ic97_sequencial,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc97_instit?>">
       <?php
       db_ancora(@$Lc97_instit,"js_pesquisac97_instit(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('c97_instit',5,$Ic97_instit,true,'text',$db_opcao," onchange='js_pesquisac97_instit(false);'")
?>
       <?php
db_input('nomeinst',80,$Inomeinst,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_id_usuario?>">
       <?php
       db_ancora(@$Lc91_id_usuario,"js_pesquisac91_id_usuario(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php
db_input('c91_id_usuario',5,$Ic91_id_usuario,true,'text',$db_opcao," onchange='js_pesquisac91_id_usuario(false);'")
?>
       <?php
db_input('nome',40,$Inome,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_anousuorigem?>">
       <?=@$Lc91_anousuorigem?>
    </td>
    <td> 
<?php
$c91_anousuorigem = db_getsession('DB_anousu');
db_input('c91_anousuorigem',5,$Ic91_anousuorigem,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_anousudestino?>">
       <?=@$Lc91_anousudestino?>
    </td>
    <td> 
<?php
$c91_anousudestino = db_getsession('DB_anousu');
db_input('c91_anousudestino',5,$Ic91_anousudestino,true,'text',3,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_data?>">
       <?=@$Lc91_data?>
    </td>
    <td> 
<?php
db_inputdata('c91_data',@$c91_data_dia,@$c91_data_mes,@$c91_data_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_hora?>">
       <?=@$Lc91_hora?>
    </td>
    <td> 
<?php
db_input('c91_hora',5,$Ic91_hora,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_situacao?>">
       <?=@$Lc91_situacao?>
    </td>
    <td> 
<?php
$x = array('1'=>'Ativo','2'=>'Processado','3'=>'Cancelado');
db_select('c91_situacao',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_tipo?>">
       <?=@$Lc91_tipo?>
    </td>
    <td> 
<?php
$x = array('1'=>'Contábil','2'=>'Dotações','3'=>'Receitas');
db_select('c91_tipo',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_ppa?>">
       <?=@$Lc91_ppa?>
    </td>
    <td> 
<?php
$x = array('1'=>'Com PPA','2'=>'Sem PPA');
db_select('c91_ppa',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tc91_origem?>">
       <?=@$Lc91_origem?>
    </td>
    <td> 
<?php
$x = array('1'=>'Previsão Inicial','2'=>'Previsão Atualizada');
db_select('c91_origem',$x,true,$db_opcao,"");
?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisac97_instit(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_config','func_db_config.php?funcao_js=parent.js_mostradb_config1|codigo|nomeinst','Pesquisa',true);
  }else{
     if(document.form1.c97_instit.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_config','func_db_config.php?pesquisa_chave='+document.form1.c97_instit.value+'&funcao_js=parent.js_mostradb_config','Pesquisa',false);
     }else{
       document.form1.nomeinst.value = ''; 
     }
  }
}
function js_mostradb_config(chave,erro){
  document.form1.nomeinst.value = chave; 
  if(erro==true){ 
    document.form1.c97_instit.focus(); 
    document.form1.c97_instit.value = ''; 
  }
}
function js_mostradb_config1(chave1,chave2){
  document.form1.c97_instit.value = chave1;
  document.form1.nomeinst.value = chave2;
  db_iframe_db_config.hide();
}
function js_pesquisac91_id_usuario(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_usuarios','func_db_usuarios.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome','Pesquisa',true);
  }else{
     if(document.form1.c91_id_usuario.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_usuarios','func_db_usuarios.php?pesquisa_chave='+document.form1.c91_id_usuario.value+'&funcao_js=parent.js_mostradb_usuarios','Pesquisa',false);
     }else{
       document.form1.nome.value = ''; 
     }
  }
}
function js_mostradb_usuarios(chave,erro){
  document.form1.nome.value = chave; 
  if(erro==true){ 
    document.form1.c91_id_usuario.focus(); 
    document.form1.c91_id_usuario.value = ''; 
  }
}
function js_mostradb_usuarios1(chave1,chave2){
  document.form1.c91_id_usuario.value = chave1;
  document.form1.nome.value = chave2;
  db_iframe_db_usuarios.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_conaberturaexe','func_conaberturaexe.php?funcao_js=parent.js_preenchepesquisa|c97_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_conaberturaexe.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>