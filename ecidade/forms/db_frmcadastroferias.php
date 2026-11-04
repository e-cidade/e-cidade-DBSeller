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

//MODULO: pessoal
$clrhferias->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
?>
<body bgcolor="#cccccc" style="margin-top:30px;" onLoad="a=1;" >
<form name="form1" method="post">
<center>
	<fieldset style="width:600px;">
	<legend><strong>Seleção de matrícula</strong></legend>	
		<table border="0">
		  <tr>
		    <td align="right" nowrap title="<?=@$Trh109_regist?>">
		      <?php
		      $mensagemlote='n';
		      db_input('mensagemlote', 4, 0,'', 'hidden', 3);
		      db_ancora(@$Lrh109_regist, "js_pesquisarh109_regist(true);", $db_opcao);
		      ?>
		    </td>
		    <td> 
		      <?php
			      db_input('rh109_regist', 10, $Irh109_regist, true, 'text', $db_opcao, " onchange='js_pesquisarh109_regist(false);'");      
			      db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3);
		      ?>
		    </td>
		  </tr>
		</table>
	</fieldset>
</center>
<br />
<input name="enviar" value="Enviar" type="submit" <?=($db_botao==false?"disabled":"")?> onblur="document.form1.rh109_regist.focus();" >
</form>
<script>      
function js_pesquisarh109_regist(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhpessoal','func_rhpessoalafasta.php?testarescisao=raf&afasta=true&funcao_js=parent.js_mostrapessoal1|rh01_regist|z01_nome|r45_dtafas|r45_dtreto&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',true);
  }else{
    if(document.form1.rh109_regist.value != ''){ 
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhpessoal','func_rhpessoalafasta.php?testarescisao=raf&afasta=true&pesquisa_chave='+document.form1.rh109_regist.value+'&funcao_js=parent.js_mostrapessoal&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',false);
    }else{
      document.form1.z01_nome.value = '';
    }
  }
}

function js_mostrapessoal(chave,chave2,chave3,erro){
	mostrar = false;
	if(erro == false){
		mostrar = true;
	}

  document.form1.z01_nome.value = chave;
  if(mostrar == false){
	  if(erro != true){
	    document.form1.z01_nome.value   = '';
	  }
    document.form1.rh109_regist.focus(); 
    document.form1.rh109_regist.value = '';
  }
}

function js_mostrapessoal1(chave1,chave2,chave3,chave4){
  db_iframe_rhpessoal.hide();
	mostrar = true;
	
  if(mostrar == true){
	  document.form1.rh109_regist.value = chave1;
	  document.form1.z01_nome.value   = chave2;
  }else{
    document.form1.rh109_regist.focus();
    document.form1.rh109_regist.value = '';
    document.form1.z01_nome.value   = '';
  }
}

function js_compara_datas(dataafast,dataretor){
  dataatual = "<?=date("Y-m-d",db_getsession("DB_datausu"))?>";

  afast = new Date(dataafast.substring(0,4),(dataafast.substring(5,7) - 1),dataafast.substring(8,10));
  retor = new Date(dataretor.substring(0,4),(dataretor.substring(5,7) - 1),dataretor.substring(8,10));
  atual = new Date(dataatual.substring(0,4),(dataatual.substring(5,7) - 1),dataatual.substring(8,10));

  if(atual > afast && atual < retor){
  	alert("Funcionário afastado.");
  	return false;
  }
  return true;
}
</script>