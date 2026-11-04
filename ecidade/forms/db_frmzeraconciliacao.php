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

//MODULO: Configuracoes
$clcontabancaria->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("db89_codagencia");
?>
<form name="form1" method="post" action="">
<center>
<fieldset>
  <legend>
    <b>Cadastro de Conta Bancária</b>
  </legend>
	<table border="0">
	  <tr>
	    <td nowrap title="<?=@$Tdb83_descricao?>">
	      <?=db_ancora(@$Ldb83_sequencial,"js_pesquisa();",$db_opcao);?>
	    </td>
	    <td> 
				<?php
				  db_input('db83_sequencial',10,$Idb83_sequencial,true,'text',3,"");
				  db_input('db83_descricao',50,$Idb83_descricao,true,'text',3,"");
				?>
	    </td>
	  </tr>
	  <tr>
	    <td nowrap title="<?=@$Tdb83_bancoagencia?>">
	      <b>Data de processamento : </b>
	    </td>
	    <td> 
				<?php
				  db_inputdata('data',null,null,null,true,'text',1);
	      ?>
	    </td>
	  </tr>

  </table>
</fieldset>  
</center>
<input name="processar" type="submit" id="processar" value="Processar" onclick="return js_processar();" >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>

function js_processar(){

   if ($F('data') == '') {
     alert("Informe a data de processamento ! ");
     return false;
   }

   if ($F('db83_sequencial') == '') {
     alert("Informe a conta para processamento ! ");
     return false;
   }
   
   return true;
   
}

function js_pesquisadb83_bancoagencia(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bancoagencia','func_bancoagencia.php?digito=true&funcao_js=parent.js_mostrabancoagencia1|db89_sequencial|db89_codagencia|db89_digito','Pesquisa',true);
  }else{
     if(document.form1.db83_bancoagencia.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_bancoagencia','func_bancoagencia.php?digito=true&pesquisa_chave='+document.form1.db83_bancoagencia.value+'&funcao_js=parent.js_mostrabancoagencia','Pesquisa',false);
     }else{
       document.form1.db89_codagencia.value = ''; 
     }
  }
}

function js_mostrabancoagencia(chave,chave1,erro){}

function js_mostrabancoagencia1(chave1,chave2,chave3){
  
  db_iframe_bancoagencia.hide();
  
}

function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_contabancaria','func_contabancaria.php?funcao_js=parent.js_preenchepesquisa|db83_sequencial','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_contabancaria.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>