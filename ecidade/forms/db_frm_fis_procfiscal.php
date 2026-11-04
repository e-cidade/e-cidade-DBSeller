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

//MODULO: fiscal
$clprocfiscal->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("nomeinst");
$clrotulo->label("y33_descricao");
$clrotulo->label("j01_matric");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_numcgm");
$clrotulo->label("q02_inscr");
$clrotulo->label("y80_codsani");
$clrotulo->label("p58_codproc");
$clrotulo->label("p58_numero");
$clrotulo->label("y106_principal");
$clprocfiscalfiscais->rotulo->label();
 
      if($db_opcao==1){
 	   $db_action="fis1_fis_procfiscal004.php";
      }else if($db_opcao==2||$db_opcao==22){
 	   $db_action="fis1_fis_procfiscal005.php";
      }else if($db_opcao==3||$db_opcao==33){
 	   $db_action="fis1_fis_procfiscal006.php";
      } 
			
			$departamento =  db_getsession("DB_coddepto"); 
			
?>
<fieldset style="width: 600px;">
<legend><strong>Processo Fiscal</strong></legend>
<form name="form1" method="post" action="<?=$db_action?>">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Ty100_sequencial?>">
       <?=@$Ly100_sequencial?>
    </td>
    <td> 
<?php 
db_input('y100_sequencial',10,$Iy100_sequencial,true,'text',3,"")
?>
    </td>
  </tr>
	<tr>
		<td colspan="2">
		<fieldset><Legend align="center"><b>Contribuinte</b></legend>
      <table border="0">
      	<tr>
    <td nowrap title="<?=@$Ty95_numcgm?>">
       <?php 
       db_ancora(@$Lz01_numcgm,"js_numcgm(true);",$db_opcao);
       ?>
    </td>
    <td> 
			<?php 
			db_input('z01_numcgm',10,$Iz01_numcgm,true,'text',$db_opcao," onchange='js_numcgm(false);'")
			?>
			<?php 
			db_input('z01_nome',40,$Iz01_nome,true,'text',3,'')
      ?>
    </td>
  </tr>
	<tr> 
    <td nowrap> 
		<?php 
		 db_ancora($Lj01_matric,'js_mostramatriculas(true);',$db_opcao); //4);
		?>
		</td>
    <td> 
    <?php 
 			db_input('j01_matric',10,$Ij01_matric,true,'text',$db_opcao,'onchange="js_mostramatriculas(false);"');  // 4,'onchange="js_mostramatriculas(false);"');
		?>
    </td>
    </tr>
	<tr> 
     <td nowrap> 
		    <?php 
 			  db_ancora($Lq02_inscr,'js_mostrainscricao(true)',$db_opcao);
			  ?>
     </td>
     <td> 
       <?php 
 			  db_input('q02_inscr',10,$Iq02_inscr,true,'text',$db_opcao,'onchange="js_mostrainscricao(false);"');   // 4,'onchange="js_mostrainscricao(false);"');
			 ?>
     </td>
  </tr>
	<tr>
      <td nowrap title="<?=@$Ty80_codsani?>">
         <?php 
         db_ancora(@$Ly80_codsani,"js_sanitario(true);",1);
         ?>
      </td>
      <td>
        <?php 
        db_input('y80_codsani',10,$Iy80_codsani,true,'text',1,"onchange='js_sanitario(false)'");
        
        ?>
      </td>
    </tr>
     	</table>
		</fieldset>
		</td>
	</tr>
	 
  <tr>
    <td nowrap title="<?=@$Ty100_procfiscalcadtipo?>">
       <?php 
       db_ancora(@$Ly100_procfiscalcadtipo,"js_pesquisay100_procfiscalcadtipo(true);",$db_opcao);
       ?>
    </td>
    <td> 
<?php 
db_input('y100_procfiscalcadtipo',10,$Iy100_procfiscalcadtipo,true,'text',$db_opcao," onchange='js_pesquisay100_procfiscalcadtipo(false);'")
?>
       <?php 
db_input('y33_descricao',40,$Iy33_descricao,true,'text',3,'')
       ?>
    </td>
  </tr>
  <tr>
    <td>
      <b>Data de criação</b>
    </td>
    <td>
      <?php 
        db_inputdata('ypl01_dtlanc',@$ypl01_dtlanc_dia,@$ypl01_dtlanc_mes,@$ypl01_dtlanc_ano,true,'text',3,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty100_dtinicial?>">
       <b>Data de início da fiscalização</b>
    </td>
    <td> 
<?php 
db_inputdata('y100_dtinicial',@$y100_dtinicial_dia,@$y100_dtinicial_mes,@$y100_dtinicial_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty100_dtfinal?>">
       <b>Data de término da ação fiscal</b>
    </td>
    <td> 
<?php 
db_inputdata('y100_dtfinal',@$y100_dtfinal_dia,@$y100_dtfinal_mes,@$y100_dtfinal_ano,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty100_obs?>">
       <?=@$Ly100_obs?>
    </td>
    <td> 
<?php 
db_textarea('y100_obs',3,52,$Iy100_obs,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
	<tr> 
    <td nowrap> 
		  <?php 
 			db_ancora("<b>Processo:</b>",' js_mostracodproc(true); ',4);
			?>
		  </td>
      <td> 
        <?php 
        db_input('p58_numero',14,$Ip58_numero,true,'text',4,'onchange="js_mostracodproc(false);"',"","","",14);
 			  db_input('p58_codproc',10,$Ip58_codproc,true,'hidden',4,'');
				
			  db_input('z01_nome1',40,$Iz01_nome,true,'text',3,'');
        ?>
			  
        </td>
      </tr>
	  <tr>
    <td></td>
    <td> 
<?php 
$y100_instit = db_getsession("DB_instit");
db_input('y100_instit',10,$Iy100_instit,true,'hidden',$db_opcao);
?>
    </td>
  </tr>
  </table>
  </center>
</fieldset>
<p align="center">
  <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" onClick="return js_validaDados();" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
  <?php 
	if($db_opcao != 1){
?>
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
<?php } ?>
</p>
</form>
<script>

function js_pesquisay100_instit(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_procfiscal','db_iframe_db_config','func_db_config.php?funcao_js=parent.js_mostradb_config1|codigo|nomeinst','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.y100_instit.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_procfiscal','db_iframe_db_config','func_db_config.php?pesquisa_chave='+document.form1.y100_instit.value+'&funcao_js=parent.js_mostradb_config','Pesquisa',false,'0','1','775','390');
     }else{
       document.form1.nomeinst.value = ''; 
     }
  }
}
function js_mostradb_config(chave,erro){
  document.form1.nomeinst.value = chave; 
  if(erro==true){ 
    document.form1.y100_instit.focus(); 
    document.form1.y100_instit.value = ''; 
  }
}
function js_mostradb_config1(chave1,chave2){
  document.form1.y100_instit.value = chave1;
  document.form1.nomeinst.value = chave2;
  db_iframe_db_config.hide();
}
function js_pesquisay100_procfiscalcadtipo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_procfiscal','db_iframe_procfiscalcadtipo','func_fis_procfiscalcadtipo.php?funcao_js=parent.js_mostraprocfiscalcadtipo1|y33_sequencial|y33_descricao','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.y100_procfiscalcadtipo.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_procfiscal','db_iframe_procfiscalcadtipo','func_fis_procfiscalcadtipo.php?pesquisa_chave='+document.form1.y100_procfiscalcadtipo.value+'&funcao_js=parent.js_mostraprocfiscalcadtipo','Pesquisa',false,'0','1','775','390');
     }else{
       document.form1.y33_descricao.value = ''; 
     }
  }
}
function js_mostraprocfiscalcadtipo(chave,erro){
  document.form1.y33_descricao.value = chave; 
  if(erro==true){ 
    document.form1.y100_procfiscalcadtipo.focus(); 
    document.form1.y100_procfiscalcadtipo.value = ''; 
  }
}
function js_mostraprocfiscalcadtipo1(chave1,chave2){
  document.form1.y100_procfiscalcadtipo.value = chave1;
  document.form1.y33_descricao.value = chave2;
  db_iframe_procfiscalcadtipo.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_procfiscal','db_iframe_procfiscal','func_fis_procfiscal_altera.php?funcao_js=parent.js_preenchepesquisa|y100_sequencial','Pesquisa');
}

function js_numcgm(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cgm','func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
  }else{
     if(document.form1.z01_numcgm.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_cgm','func_cgm.php?pesquisa_chave='+document.form1.z01_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
     }else{
       document.form1.z01_nome.value = ''; 
     }
  }
}
function js_mostracgm(erro,chave){
	document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.z01_numcgm.focus(); 
    document.form1.z01_numcgm.value = ""; 
		document.form1.y80_codsani.value= "";
    document.form1.q02_inscr.value  = "";
  	document.form1.j01_matric.value = "";
  }
}
function js_mostracgm1(chave1,chave2){
  document.form1.z01_numcgm.value = chave1;
  document.form1.z01_nome.value = chave2;
	document.form1.y80_codsani.value= "";
  document.form1.q02_inscr.value  = "";
 	document.form1.j01_matric.value = "";
  db_iframe_cgm.hide();
}


function js_mostrainscricao(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?funcao_js=parent.js_preencheinscricao|0|z01_nome|db_z01_numcgm','Pesquisa',true,'15');
  
  }else{
    js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?pesquisa_chave='+document.form1.q02_inscr.value+'&funcao_js=parent.js_preencheinscricao2','Pesquisa',false);
  }
}

function js_preencheinscricao(chave,chave1,chave2){
  document.form1.j01_matric.value = "";
  document.form1.z01_numcgm.value = chave2;
	document.form1.y80_codsani.value = ""; 
  document.form1.z01_nome.value = chave1;
  document.form1.q02_inscr.value = chave;
  db_iframe_issbase.hide();
}

function js_preencheinscricao2(chave,erro,chave2,chave3){
	//alert('chave='+chave+' chave1='+chave1+' chave2='+chave2+'chave3= '+chave3);
  document.form1.j01_matric.value = "";
  document.form1.z01_numcgm.value = chave3;
	document.form1.y80_codsani.value = ""; 
  document.form1.z01_nome.value = chave;
	if(erro==true){
		document.form1.y80_codsani.value = ''; 
		document.form1.j01_matric.value = "";
		document.form1.q02_inscr.value = "";
		document.form1.z01_numcgm.value = "";
	}
  db_iframe_issbase.hide();
}


function js_mostramatriculas(mostra){
  if(mostra==true){
     js_OpenJanelaIframe('','db_iframe_iptubase','func_iptubase.php?funcao_js=parent.js_preenchematriculas|j01_matric|z01_nome|db_z01_numcgm','Pesquisa',true,'15');
 
  }else{
     js_OpenJanelaIframe('','db_iframe_iptubase','func_iptubase.php?pesquisa_chave='+document.form1.j01_matric.value+'&funcao_js=parent.js_preenchematriculas2','Pesquisa',false);
  }
}
function js_preenchematriculas(chave,chave1,chave2){
  document.form1.q02_inscr.value = "";
  document.form1.z01_numcgm.value = chave2;
	document.form1.y80_codsani.value = ""; 
  document.form1.j01_matric.value = chave;
  document.form1.z01_nome.value = chave1;
  
  db_iframe_iptubase.hide();
}

function js_preenchematriculas2(chave,chave1,chave2){
  document.form1.q02_inscr.value = "";
  document.form1.z01_numcgm.value = chave2;
	document.form1.y80_codsani.value = ""; 
  document.form1.z01_nome.value = chave;
	if(erro==true){
		document.form1.y80_codsani.value = ''; 
		document.form1.j01_matric.value = "";
		document.form1.q02_inscr.value = "";
		document.form1.z01_numcgm.value = "";
	}
 
}

function js_sanitario(mostra){
  var sani=document.form1.y80_codsani.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_sanitario','func_fis_sanitario.php?funcao_js=parent.js_preenchesanitario|y80_codsani|z01_nome|db_z01_numcgm','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_sanitario','func_fis_sanitario.php?pesquisa_chave='+sani+'&funcao_js=parent.js_preenchesanitario1','Pesquisa',false);
  }
}
function js_preenchesanitario(chave,chave1,chave2){
  document.form1.y80_codsani.value = chave;
  document.form1.q02_inscr.value = "";
  document.form1.z01_numcgm.value = chave2;
	document.form1.j01_matric.value = "";
  document.form1.z01_nome.value = chave1;
  db_iframe_sanitario.hide();
}
function js_preenchesanitario1(chave,chave1,erro,chave2){
	
  document.form1.q02_inscr.value = "";
  document.form1.z01_numcgm.value = chave2;
	document.form1.j01_matric.value = "";
  document.form1.z01_nome.value = chave1;
  if(erro==true){ 
    document.form1.y80_codsani.focus(); 
    document.form1.y80_codsani.value = ''; 
		document.form1.j01_matric.value = "";
		document.form1.q02_inscr.value = "";
		document.form1.z01_numcgm.value = "";
  }
	
}

function js_mostracodproc(mostra){
 if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo.php?funcao_js=parent.js_mostraproc1|p58_codproc|p58_numcgm|z01_nome|p58_numero','Pesquisa',true,'15');
  }else{
    if(document.form1.p58_numero.value != '') {
        js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo.php?pesquisa_chave='+document.form1.p58_numero.value+'&funcao_js=parent.js_mostraproc&chave_p58_numero=1','Pesquisa',false);
    }
  }
}
function js_mostraproc(chave,obs,erro){
  if(erro==true){ 
    document.form1.p58_numero.focus(); 
    document.form1.p58_numero.value = ''; 
    document.form1.z01_nome1.value = ''; 
  }else{
    document.form1.z01_nome1.value = obs; 
    document.form1.p58_codproc.value = chave; 
    
  }   
}
function js_mostraproc1(chave1,n,z,chave4){  
  // document.form1.z01_numcgm.value = n;
  document.form1.z01_nome1.value = z;
  document.form1.p58_codproc.value = chave1;
  document.form1.p58_numero.value = chave4;
  db_iframe_proc.hide();
}

function js_preenchepesquisa(chave){
  db_iframe_procfiscal.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}

function js_pesquisay106_cadfiscais(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cadfiscais','func_fis_cadfiscaisalt.php?funcao_js=parent.js_mostracadfiscais1|id_usuario|nome','Pesquisa',true,'0','1','775','390');
  }else{
     if(document.form1.y106_cadfiscais.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_cadfiscais','func_fis_cadfiscaisalt.php?pesquisa_chave='+document.form1.y106_cadfiscais.value+'&funcao_js=parent.js_mostracadfiscais','Pesquisa',false);
     }else{
       document.form1.id_usuario.value = ''; 
     }
  }
}
function js_mostracadfiscais(chave,erro){
  document.form1.nome.value = chave; 
  if(erro==true){ 
    document.form1.y106_cadfiscais.focus(); 
    document.form1.y106_cadfiscais.value = ''; 
  }
}
function js_mostracadfiscais1(chave1,chave2){
  document.form1.y106_cadfiscais.value = chave1;
  document.form1.nome.value = chave2;
  db_iframe_cadfiscais.hide();
}

function jsMostraCodProcesso(){
    if(document.form1.p58_numero.value != '') {
        js_OpenJanelaIframe('','db_iframe_proc','func_fis_processoadministrativo.php?pesquisa_chave='+document.form1.p58_numero.value+'&funcao_js=parent.js_mostraproc&chave_p58_numero=1','Pesquisa',false);
    }
    return true;
}

var p58Numero = document.getElementById('p58_numero');
    
p58Numero.onmouseout = function(){
   // document.getElementById('db_opcao').disabled = 'true';
    var flag = jsMostraCodProcesso();
    if (flag == true) {
 //       document.getElementById('db_opcao').disabled = 'false'
    }   
}

function js_validaDados(){

	if(document.form1.z01_numcgm.value == "" && document.form1.j01_matric.value == "" && document.form1.q02_inscr.value == "" && document.form1.y80_codsani.value == "" ){
		alert('Informe um contribuinte');
		return false;
	}
	if(document.form1.y100_procfiscalcadtipo.value == ""){
		alert('Informe o tipo de Fiscalização.');
		return false;
	}
	
	if(document.form1.p58_numero.value == ""){
                alert('Informe o Processo.');
                return false;
        }

}



 </script>
