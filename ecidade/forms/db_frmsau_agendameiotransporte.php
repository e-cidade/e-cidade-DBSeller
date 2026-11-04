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
$clsau_agendatransporte->rotulo->label();

$clrotulo = new rotulocampo;

//Veiculo
$clrotulo->label ( "s121_i_veiculo" );
$clrotulo->label ( "ve01_quantcapacidad" );
$clrotulo->label ( "ve22_descr" );

//cgs_und
$clrotulo->label ( "z01_v_nome" );
$clrotulo->label ( "z01_v_cgccpf" );
$clrotulo->label ( "z01_v_ident" );

//Agenda Externa
$clrotulo->label ( "s118_i_codigo" );

?><!--  -->
<form name="form1" method="post" action="">
<table>
	<tr>
		<td>
		<fieldset><legend><b>Agendamento do Meio de Transporte</b></legend>
		<table>
		
			<!-- Veiculo -->
			<tr>
				<td id="tipo_veiculo1"  nowrap title="<?=@$Ts121_i_veiculo?>">
					<?php
					db_ancora(@$Ls121_i_veiculo,"js_pesquisas121_i_veiculo(true);",$db_opcao);
					?>
				</td>
				<td id="tipo_veiculo2">
					<?php
					db_input('s124_i_codigo',10,@$Is124_i_codigo,true,'hidden',$db_opcao);
					db_input('s121_i_veiculo',10,@$Is121_i_veiculo,true,'text',$db_opcao," onchange='js_pesquisas121_i_veiculo(false);'")
					?>
					<?php
					db_input('ve22_descr',30,@$Ive22_descr,true,'text',3,'')
					?>
				</td>
				<td>
					<?=@$Lve01_quantcapacidad?>
					<?php
					db_input('ve01_quantcapacidad',10,@$Ive01_quantcapacidad,true,'text',3,'')
					?>
				</td>					
			</tr>
		
			<!-- Data / Hora Marcada -->
			<tr>
				<td nowrap title="<?=@$Ts124_d_saida?>">
					<?=@$Ls124_d_saida?>
				</td>
				<td colspan="2">
					<?php
					db_inputdata ( 's124_d_saida', @$s124_d_saida_dia, @$s124_d_saida_mes, @$s124_d_saida_ano, true, 'text', $db_opcao, "onChange=js_reloadtransporte();", "", "",  "parent.js_reloadtransporte();" );
					?>
					<?=@$Ls124_c_hora?>
					<?php
					db_input ( 's124_c_hora', 10, $Is124_c_hora, true, 'text', $db_opcao );
					?>
				</td>
			</tr>
					
					
			<tr>
				<td colspan="3" align="center">
				<fieldset><legend><b>Dados do Paciente</b></legend>
				<table>				
					<!-- CGS -->
					<tr align="center">
						<td>
						<table>
							<tr>
								<td nowrap title="<?=@$Ts124_i_numcgs?>">
										<?php
										db_ancora ( @$Ls124_i_numcgs, "js_pesquisas124_i_numcgs(true);", $db_opcao );
										?>
									</td>
								<td colspan="3">
										<?php
										db_input ( 's118_i_codigo', 10, $Is118_i_codigo, true, 'hidden', 3 );
										db_input ( 's124_i_numcgs', 10, $Is124_i_numcgs, true, 'text', $db_opcao, "onChange='js_pesquisas124_i_numcgs(false);'" );
										db_input ( 'z01_v_nome', 48, $Iz01_v_nome, true, 'text', 3 );
										?>
									</td>
							</tr>
							<tr>
								<td nowrap title="<?=@$Tz01_v_cgccpf?>">
										<?=@$Lz01_v_ident?>
								</td>
								<td>
										<?php
										db_input ( 'z01_v_ident', 15, $Iz01_v_ident, true, 'text', 3, "" );
										?>
										<?=@$Lz01_v_cgccpf?>
										<?php
										db_input ( 'z01_v_cgccpf', 15, $Iz01_v_cgccpf, true, 'text', 3, "" );
										?>
								</td>
							</tr>
						</table>
						</td>
					</tr>				
				</table>
				</fieldset>
				</td>
			</tr>
			<tr align="center" >
				<td align="center" colspan="3">
					<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" 
							type="submit" 
							id="db_opcao" 
							value="<?=($db_opcao==1?"Lanca":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" >						
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<input name="limpa" 
							type="button" 
							id="btn_limpa" 
							value="Limpa"
							onclick="js_limpa()">
					<input name="imprimir"
					       type="button"
					       value="imprimir"
					       onclick="js_imprimir('<?=$s124_d_saida?>',<?=$s121_i_veiculo?>)">
				</td>
			</tr>
			<tr>
				<td colspan="3" >
						<?php
						$chavepri = array ("s124_i_codigo" => @$s124_i_codigo );
						
						$cliframe_alterar_excluir->chavepri = $chavepri;
						@$cliframe_alterar_excluir->sql = $clsau_agendatransporte->sql_query_ext ( "", "*", "s124_i_codigo", 
																								"s124_d_saida = '$s124_d_saida' and
																								 s121_i_veiculo = $s121_i_veiculo  
																								" );
						
						$cliframe_alterar_excluir->campos = "s124_i_numcgs, z01_v_nome, z01_v_ident, z01_v_cgccpf, s124_d_saida, s124_c_hora";
						$cliframe_alterar_excluir->legenda = "Passageiros";
						$cliframe_alterar_excluir->alignlegenda = "left";
						$cliframe_alterar_excluir->msg_vazio = "Não foi encontrado nenhum registro.";
						$cliframe_alterar_excluir->textocabec = "#DEB887";
						$cliframe_alterar_excluir->textocorpo = "#444444";
						$cliframe_alterar_excluir->fundocabec = "#444444";
						$cliframe_alterar_excluir->fundocorpo = "#eaeaea";
						$cliframe_alterar_excluir->tamfontecabec = 9;
						$cliframe_alterar_excluir->tamfontecorpo = 9;
						$cliframe_alterar_excluir->formulario = false;
						$cliframe_alterar_excluir->opcoes = 3;
						$cliframe_alterar_excluir->iframe_alterar_excluir( $db_opcao );
						
						?>
				</td>
			</tr>

		</table>
		</fieldset>
		</td>
	</tr>
</table>
</form>

<!--
/**
 * Funções JavaScript
 */
-->
<script>


/**
 * Pesquisa Veiculo
 */
function js_pesquisas121_i_veiculo(mostra){
var strParam = '';

	strParam += 'func_veiculos.php';
	strParam += '?funcao_js=parent.js_mostraveiculos1|ve01_codigo|ve01_placa|ve22_descr|ve01_quantcapacidad';
	strParam += "&campos=distinct veiculos.ve01_codigo,veiculos.ve01_placa,veiccadmodelo.ve22_descr,veiculos.ve01_quantcapacidad"; 

	if(mostra==true){
		js_OpenJanelaIframe('','db_iframe_veiculos',strParam,'Pesquisa',true);
  	}else{
     	if(document.form1.s121_i_veiculo.value != ''){ 
     		strParam += '&chave_ve01_codigo='+document.form1.s121_i_veiculo.value;
        	js_OpenJanelaIframe('','db_iframe_veiculos',strParam,'Pesquisa',true);
     	}else{
       		document.form1.ve22_descr.value = ''; 
     	}
  }
}

function js_mostraveiculos1(chave1,chave2,chave3,chave4){
  document.form1.s121_i_veiculo.value = chave1;
  document.form1.ve22_descr.value     = chave2+' - '+chave3;
  document.form1.ve01_quantcapacidad.value = chave4;

  db_iframe_veiculos.hide();
}


/**
 * Pesquisa CGS
 */
function js_pesquisas124_i_numcgs(mostra){
var strParam = '';

	if( $F('ve22_descr') == '' ){
  		alert('Veículo não informado.');
  		document.form1.s121_i_veiculo.focus();
	}else if( $F('s124_d_saida') == '' ){
  		alert('Data de Saída não informada.');
  		document.form1.s124_d_saida.focus();
  	}else{
		strParam += 'func_sau_agendaexterna.php';
		strParam += '?funcao_js=parent.js_mostracgs1|s118_i_codigo|s118_i_numcgs|z01_v_nome|z01_v_ident|z01_v_cgccpf';
		strParam += '&chave_s118_d_marcada='+$F('s124_d_saida');
		strParam += '&campos=distinct s118_i_numcgs, z01_v_nome, z01_v_ident, z01_v_cgccpf';
	
		if(mostra==true){
			js_OpenJanelaIframe('','db_iframe_sau_agendaexterna',strParam,'Pesquisa',true);
	  	}else{
	     	if(document.form1.s124_i_numcgs.value != ''){ 
	     		strParam += '&chave_s118_i_numcgs='+document.form1.s124_i_numcgs.value;
	        	js_OpenJanelaIframe('','db_iframe_sau_agendaexterna',strParam,'Pesquisa',true);
	     	}else{
	       		document.form1.z01_v_nome.value = ''; 
	     	}
	  	}
		document.form1.s124_i_numcgs.value = '';
		document.form1.z01_v_nome.value    = '';
		document.form1.z01_v_ident.value   = '';
		document.form1.z01_v_cgccpf.value  = '';
  	}
}

function js_mostracgs1(chave1,chave2,chave3,chave4,chave5){
  //document.form1.s118_i_codigo.value = chave1;
  document.form1.s124_i_numcgs.value = chave2;
  document.form1.z01_v_nome.value    = chave3;
  document.form1.z01_v_ident.value   = chave4;
  document.form1.z01_v_cgccpf.value  = chave5;

  db_iframe_sau_agendaexterna.hide();
}

/**
 * Botão limpa
 */
function js_limpa(){
	if( document.getElementById('db_opcao').value != 'Lanca' ){
		js_reloadtransporte();
	}else{
		location.href='sau4_agendaexterna002.php';
	} 
}

/**
 * Ancora da Data Selecionada
 */
function js_reloadtransporte(){
	if( $F('ve22_descr') == '' ){
  		alert('Veículo não informado.');
  		document.form1.s121_i_veiculo.focus();
	}else if( $F('s124_d_saida') == '' ){
  		alert('Data de Saída não informada.');
  		document.form1.s124_d_saida.focus();
  	}else{
  		var strParam = '';
  		var strData_ano = $F('s124_d_saida').substr(6,4);
  		var strData_mes = $F('s124_d_saida').substr(3,2);
  		var strData_dia = $F('s124_d_saida').substr(0,2);
  		
  		strParam += 'sau4_agendaexterna002.php';
  		strParam += '?s121_i_veiculo='+$F('s121_i_veiculo');
  		strParam += '&ve22_descr='+$F('ve22_descr');
  		strParam += '&ve01_quantcapacidad='+$F('ve01_quantcapacidad');
  		strParam += '&s124_d_saida_ano='+strData_ano;
  		strParam += '&s124_d_saida_mes='+strData_mes;
  		strParam += '&s124_d_saida_dia='+strData_dia;
  		strParam += '&s124_d_saida='+strData_ano+'/'+strData_mes+'/'+strData_dia;
  		strParam += '&s124_c_hora='+$F('s124_c_hora');
  		
  		location.href=strParam;
  		
  	}
} 
/**
* Relatorio de Relação de Pacientes na Viagen
*/
function js_imprimir(data,veiculo){
   str='sau2_agendaexterna003.php?data='+data+'&veiculo='+veiculo;
   jan = window.open(str,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');    
   jan.moveTo(0,0);
}
</script>