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

//MODULO: Ambulatorial
$clsau_agendaexterna->rotulo->label();
$clrotulo = new rotulocampo;
//especialidade (Consulta)
$clrotulo->label ( "sd27_i_rhcbo" );
$clrotulo->label ( "rh70_descr" );
$clrotulo->label ( "rh70_estrutural" );

$clrotulo->label ( "z01_v_nome" );
$clrotulo->label ( "z01_v_cgccpf" );
$clrotulo->label ( "z01_v_ident" );
//Exame
$clrotulo->label ( "s120_i_exame" );
$clrotulo->label ( "s108_i_codigo" );
$clrotulo->label ( "s108_c_exame" );
$clrotulo->label ( "s118_d_marcada" );
$clrotulo->label ( "s118_i_codigo" );

?>
<form name="form1" method="post" action="">
<center>
<table border="0">
             <tr>
						<td nowrap title="<?=@$Ts118_c_tipoagenda?>">
								<?=@$Ls118_c_tipoagenda?>
						</td>
						<td>
								<?php
								$x = array ('C' => 'Consulta', 'E' => 'Exame' );
								db_select ( 's118_c_tipoagenda', $x, true, $db_opcao, "onChange='js_tipo( this.value )';" );
								?>
						</td>
					</tr>
					<!-- Consulta -->
					<tr id="tipo_consulta">
						<td nowrap title="<?=@$Tsd27_i_rhcbo?>">
								<?php
								db_ancora ( @$Lsd27_i_rhcbo, "js_pesquisasd27_i_rhcbo(true);", 1 );
								?>								
						</td>
						<td>
								<?php
								db_input ( 'sd27_i_rhcbo', 10, $Isd27_i_rhcbo, true, 'text', 1, "onChange='js_pesquisasd27_i_rhcbo(false);'" );
								db_input ( 'rh70_estrutural',10,$Irh70_estrutural,true,'text',3,'');
								db_input ( 'rh70_descr', 48, @$Irh70_descr, true, 'text', 3 );
								?>
						</td>
					</tr>
					<!-- Exame -->
					<tr id="tipo_exame" style="display:none">
						<td nowrap title="<?=@$Ts120_i_exame?>">
								<?php
								db_ancora ( @$Ls120_i_exame, "js_pesquisas120_i_exame(true);", 1 );
								?>								
						</td>
						<td>
								<?php
								db_input ( 's120_i_exame', 10, $Is120_i_exame, true, 'text', 1, "onChange='js_pesquisas120_i_exame(false);'" );
								db_input ( 's108_c_exame', 48, @$Is108_c_exame, true, 'text', 3 );
								?>
						</td>
					</tr>
             <tr>
              
               <td nowrap title="<?=@$Ts118_d_marcada?>">
               <b>Para Dia</b> 
              </td>
     
           <td> 
             <?php
              db_inputdata('pdia',@$pdia_dia,@$pdia_mes,@$pdia_ano,true,'text',$db_opcao," onchange='js_diasem()' onFocus=\"nextfield='done'\" ", "", "", "parent.js_diasem(); ");
             ?>
          </td>
         </tr>
  <tr>
  <td colspan="3">
  <iframe id="frameagendados" name="frameagendados"  src=""   width="800" height="100" scrolling="yes" frameborder="0"></iframe>
  </td>
  </tr>
</table>
<table>
<tr><td><input id="imprimir" name="imprimir" value="Imprimir Prestadores" type="button" onclick="js_imprimir();"></td>
<td><input id="imprimir" name="imprimir" value="Imprimir Veiculos" type="button" onclick="js_imprimirveiculo();"></td></tr>
</table>
  
  </center>
</form>
<script>

js_tipo( $F('s118_c_tipoagenda') );

function js_tipo( tipo ){
	var idRow1 = document.getElementById("tipo_consulta");
	var idRow2 = document.getElementById("tipo_exame");
		idRow1.style.display = tipo=='C'?'':'none';
		idRow2.style.display = tipo=='E'?'':'none';
		 
} 

/**
 * Pesquisa Exames
 */
  function js_pesquisas120_i_exame(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_sau_exames','func_sau_exames.php?funcao_js=parent.js_mostraexame1|s108_i_codigo|s108_c_exame','Pesquisa',true);
  }else{
     if(document.form1.s120_i_exame.value != ''){ 
    	js_OpenJanelaIframe('','db_iframe_sau_exames','func_sau_exames.php?pesquisa_chave='+document.form1.s120_i_exame.value+'&funcao_js=parent.js_mostraexame','Pesquisa',false);
     }else{
       document.form1.s120_i_exame.value = '';
     }
  }
}
function js_mostraexame(chave,erro){
  document.form1.s108_c_exame.value = chave; 
  if(erro==true){ 
    document.form1.s120_i_exame.focus(); 
    document.form1.s120_i_exame.value = ''; 
  }
}

function js_mostraexame1(chave1,chave2){
  document.form1.s120_i_exame.value = chave1;
  document.form1.s108_c_exame.value = chave2;

  db_iframe_sau_exames.hide();
  
}



/**
 * Pesquisa Especialidade
 */
function js_pesquisasd27_i_rhcbo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_rhcbo','func_rhcbosaude.php?funcao_js=parent.js_mostrarhcbo1|rh70_sequencial|rh70_estrutural|rh70_descr','Pesquisa',true);
  }else{
     if(document.form1.sd27_i_rhcbo.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_rhcbo','func_rhcbosaude.php?pesquisa_chave='+document.form1.sd27_i_rhcbo.value+'&funcao_js=parent.js_mostrarhcbo','Pesquisa',false);
     }else{
       document.form1.rh70_estrutural.value = '';
       document.form1.rh70_descr.value = '';
     }
  }
}
function js_mostrarhcbo(chave1, chave2, chave3,erro){
  document.form1.rh70_estrutural.value = chave1;
  document.form1.rh70_descr.value = chave2;
  document.form1.sd27_i_rhcbo.value = chave3;
  if(erro==true){
    document.form1.sd27_i_rhcbo.focus(); 
    document.form1.sd27_i_rhcbo.value = ''; 
  }
}
function js_mostrarhcbo1(chave1,chave2,chave3){
  document.form1.sd27_i_rhcbo.value = chave1;
  document.form1.rh70_estrutural.value = chave2;
  document.form1.rh70_descr.value = chave3;
  db_iframe_rhcbo.hide();
}


function js_diasem(){
	obj = document.form1;
	
	a =  obj.pdia_ano.value;
	m = obj.pdia_mes.value;
	d =  obj.pdia_dia.value;
	data = new Date(a,m,d);
	dia= data.getDay();
	
	
	js_agendados();
	
}
function js_agendados(){
 	obj = document.form1;
 	pdia = document.getElementById('pdia').value;
  	a =  pdia.substr(6,4);
	m = (pdia.substr(3,2))-1;
	d =  pdia.substr(0,2);
	data = new Date(a,m,d);
	dia= data.getDay();
 	
	if( pdia != "" ){
 		x  = 'sau4_agendaexterna005.php';
  		x += '?s120_i_exame='+obj.s120_i_exame.value;
  		x += '&s118_c_tipoagenda='+obj.s118_c_tipoagenda.value;
  		x += '&sd27_i_rhcbo='+obj.sd27_i_rhcbo.value;  		
  	  	x += '&pdia='+pdia;
  		
  	}
  	iframe = document.getElementById('frameagendados');
  	iframe.src = x;
}

function js_imprimir(){	
     obj = document.form1;
 	pdia = document.getElementById('pdia').value;
  	a =  pdia.substr(6,4);
	m = (pdia.substr(3,2))-1;
	d =  pdia.substr(0,2);
	data = new Date(a,m,d);
	dia= data.getDay();
 	
	if( pdia != "" ){
 		x  = 'sau2_agendaexterna001.php';
  		x += '?s120_i_exame='+obj.s120_i_exame.value;
  		x += '&s118_c_tipoagenda='+obj.s118_c_tipoagenda.value;
  		x += '&sd27_i_rhcbo='+obj.sd27_i_rhcbo.value;  		
  	  	x += '&pdia='+pdia;
  	 jan = window.open(x,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
	  jan.moveTo(0,0);
  	}
  	
 }
function js_imprimirveiculo(){	
     obj = document.form1;
 	 pdia = document.getElementById('pdia').value;
  	 a =  pdia.substr(6,4);
	 m = (pdia.substr(3,2))-1;
	 d =  pdia.substr(0,2);
	 data = new Date(a,m,d);
	 dia= data.getDay();
 	
	if( pdia != "" ){
 		x  = 'sau2_agendaexterna002.php';		
  	  	x += '?pdia='+pdia;
  	 jan = window.open(x,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
	  jan.moveTo(0,0);
  	}
  	
 }

</script>