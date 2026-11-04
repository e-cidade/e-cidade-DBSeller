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

//MODULO: saude


$clsau_agendaexterna->rotulo->label ();

//cgs_und
$clrotulo->label ( "z01_v_nome" );
$clrotulo->label ( "z01_v_cgccpf" );
$clrotulo->label ( "z01_v_ident" );

//nome prestador
$clrotulo->label ( "z01_nome" );

//especialidade (Consulta)
$clrotulo->label ( "s119_i_especialidade" );
$clrotulo->label ( "rh70_descr" );
$clrotulo->label ( "rh70_estrutural" );


//Exame
$clrotulo->label ( "s120_i_exame" );
$clrotulo->label ( "s108_i_codigo" );
$clrotulo->label ( "s108_c_exame" );

?>
<form name="form1" method="post" action="">
<table>
	<tr>
		<td>
		<fieldset><legend><b>Agenda Externa</b></legend>
		<table>
			<!-- CGS -->
			<tr align="center">
				<td>
				<table>
					<tr>
						<td nowrap title="<?=@$Ts118_i_numcgs?>">
								<?php
								db_ancora ( @$Ls118_i_numcgs, "js_pesquisas118_i_numcgs(true);", 1 );
								?>
							</td>
						<td colspan="3">
								<?php
								db_input ( 's118_i_codigo', 10, $Is118_i_codigo, true, 'hidden', 1);
								db_input ( 's118_i_numcgs', 10, $Is118_i_numcgs, true, 'text', $db_opcao==1?1:3, "onChange='js_pesquisas118_i_numcgs(false);'" );
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
							</td>
						<td align="center">
								<?=@$Lz01_v_cgccpf?>
								<?php
								db_input ( 'z01_v_cgccpf', 15, $Iz01_v_cgccpf, true, 'text', 3, "" );
								?>
							</td>
						<td align="right">
								<?=@$Ls118_d_preferencia?>
								<?php
								db_inputdata ( 's118_d_preferencia', @$s118_d_preferencia_dia, @$s118_d_preferencia_mes, @$s118_d_preferencia_ano, true, 'text', $db_opcao, "" );
								?>								
							</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
				<fieldset><legend><b>Lançamento</b></legend>
				<table>
					<!-- Tipo Agenda -->
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
					
					<!--  Encaminhamento -->
					<tr>
						<td nowrap title="<?=@$Ts118_c_tipoagenda?>">
							<?=@$Ls118_v_encaminhamento?>
						</td>
						<td>
							<?php
							db_input ( 's118_v_encaminhamento', 10, $Is118_v_encaminhamento, true, 'text', $db_opcao );
							?>						
						</td>
					
					</tr>

					<!-- Consulta -->
					<tr id="tipo_consulta">
						<td nowrap title="<?=@$Ts119_i_especialidade?>">
								<?php
								db_ancora ( @$Ls119_i_especialidade, "js_pesquisasd27_i_rhcbo(true);", 1 );
								?>								
						</td>
						<td>
								<?php
								db_input ( 's119_i_especialidade', 10, $Is119_i_especialidade, true, 'text', 1, "onChange='js_pesquisasd27_i_rhcbo(false);'" );
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

					<!-- Prestadora -->
					<tr>
						<td nowrap title="<?=@$Ts118_i_prestador?>">
								<?php
								db_ancora ( @$Ls118_i_prestador, "js_pesquisas118_i_prestador(true);", 1 );
								?>
							</td>
						<td>
								<?php
								db_input ( 's118_i_prestador', 10, $Is118_i_prestador, true, 'text', 1, "onChange='js_pesquisas118_i_prestador(false);'" );
								db_input ( 'z01_nome', 48, $Iz01_nome, true, 'text', 3 );
								?>
								
							</td>
					</tr>

					<!-- Data / Hora Marcada -->
					<tr>
						<td nowrap title="<?=@$Ts118_d_marcada?>">
								<?=@$Ls118_d_marcada?>
							</td>
						<td>
								<?php
								db_inputdata ( 's118_d_marcada', @$s118_d_marcada_dia, @$s118_d_marcada_mes, @$s118_d_marcada_ano, true, 'text', $db_opcao, "" );
								?>
								<?=@$Ls118_c_horamarcada?>
								<?php
								db_input ( 's118_c_horamarcada', 10, $Is118_c_horamarcada, true, 'text', $db_opcao );
								?>
								<?=@$Ls118_v_protocolo?>
								<?php
								db_input ( 's118_v_protocolo', 10, $Is118_v_protocolo, true, 'text', $db_opcao );
								?>
						</td>
					</tr>

				</table>
				</fieldset>
				</td>
			</tr>
			<tr align="center">
				<td align="center">
					<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" 
							type="submit" 
							id="db_opcao" 
							value="<?=($db_opcao==1?"Lanca":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" >						
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<input name="limpa" 
							type="button" 
							id="btn_limpa" 
							value="Limpa"
							onclick="js_pesquisas118_i_numcgs(false)">
				</td>
			</tr>
			<tr>
				<td colspan="2">
						<?php
						$chavepri = array ("s118_i_codigo" => @$s118_i_codigo );
						
						$cliframe_alterar_excluir->chavepri = $chavepri;
						@$cliframe_alterar_excluir->sql = $clsau_agendaexterna->sql_query_ext ( "", "s118_i_codigo,
																			
																				case s118_c_tipoagenda 
																					when 'C' then 'Consulta'
																					else 'Exame'
																				end as s118_c_tipoagenda,
																				
																				case when not s119_i_especialidade is null then
																					rh70_descr
																				else
																					s108_c_exame
																				end as rh70_descr,
																				
																				s118_i_prestador,
																				z01_nome,
																				
																				s118_d_marcada,
																				s118_c_horamarcada															
																			", "s118_i_codigo", "s118_i_numcgs = " . ( int )$chavepesquisacgs );
						
						$cliframe_alterar_excluir->campos = "s118_i_codigo, s118_c_tipoagenda, rh70_descr, s118_i_prestador, z01_nome, s118_d_marcada, s118_c_horamarcada";
						$cliframe_alterar_excluir->legenda = "Consulta / Exames";
						$cliframe_alterar_excluir->alignlegenda = "left";
						$cliframe_alterar_excluir->msg_vazio = "Não foi encontrado nenhum registro.";
						$cliframe_alterar_excluir->textocabec = "#DEB887";
						$cliframe_alterar_excluir->textocorpo = "#444444";
						$cliframe_alterar_excluir->fundocabec = "#444444";
						$cliframe_alterar_excluir->fundocorpo = "#eaeaea";
						$cliframe_alterar_excluir->tamfontecabec = 9;
						$cliframe_alterar_excluir->tamfontecorpo = 9;
						$cliframe_alterar_excluir->formulario = false;
						$cliframe_alterar_excluir->iframe_alterar_excluir ( $db_opcao );
						
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
 * Inicia tipo de consulta com Consulta
 */

js_tipo( $F('s118_c_tipoagenda') );

/**
 * Pesquisa CGS
 */
function js_pesquisas118_i_numcgs(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_cgs_und',"func_cgs_und.php?funcao_js=parent.js_mostracgs1|z01_i_cgsund|z01_v_nome|z01_v_cgccpf|z01_v_ident&retornacgs=p.p.document.form1.z01_i_cgsund.value&retornanome=p.p.IFdb_iframe_agendamento.document.form1.z01_v_nome.value",'Pesquisa',true);
  }else{
     if(document.form1.s118_i_numcgs.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_cgs_und',
        	'func_cgs_und.php?chave_z01_i_cgsund='+document.form1.s118_i_numcgs.value+'&funcao_js=parent.js_mostracgs1|z01_i_cgsund|z01_v_nome|z01_v_cgccpf|z01_v_identc&retornacgs=p.p.document.form1.z01_i_cgsund.value&retornanome=p.p.document.form1.z01_v_nome.value','Pesquisa',true);
     }else{
       document.form1.z01_v_nome.value = '';
     }
  }
}
function js_mostracgs1(chave1,chave2,chave3,chave4){
	if( chave1 != "" ){
		db_iframe_cgs_und.hide();
		strParam  = '<?=basename ( $GLOBALS ["HTTP_SERVER_VARS"] ["PHP_SELF"] )?>';
		strParam += '?chavepesquisacgs='+chave1;
		strParam += '&s118_i_numcgs='+chave1;
		strParam += '&z01_v_nome='+chave2;
		strParam += '&z01_v_cgccpf='+chave3;
		strParam += '&z01_v_ident='+chave4;
		
  		location.href = strParam;
	}
}

/**
 * Pesquisa Especialidade
 */
function js_pesquisasd27_i_rhcbo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_rhcbo','func_rhcbosaude.php?funcao_js=parent.js_mostrarhcbo1|rh70_sequencial|rh70_estrutural|rh70_descr','Pesquisa',true);
  }else{
     if(document.form1.s119_i_especialidade.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_rhcbo','func_rhcbosaude.php?pesquisa_chave='+document.form1.s119_i_especialidade.value+'&funcao_js=parent.js_mostrarhcbo','Pesquisa',false);
     }else{
       document.form1.rh70_estrutural.value = '';
       document.form1.rh70_descr.value = '';
     }
  }
}
function js_mostrarhcbo(chave1, chave2, chave3,erro){
  document.form1.rh70_estrutural.value = chave1;
  document.form1.rh70_descr.value = chave2;
  document.form1.s119_i_especialidade.value = chave3;
  if(erro==true){
    document.form1.s119_i_especialidade.focus(); 
    document.form1.s119_i_especialidade.value = ''; 
  }
}
function js_mostrarhcbo1(chave1,chave2,chave3){
  document.form1.s119_i_especialidade.value = chave1;
  document.form1.rh70_estrutural.value = chave2;
  document.form1.rh70_descr.value = chave3;
  db_iframe_rhcbo.hide();
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
 * Pesquisa Prestador
 */
function js_pesquisas118_i_prestador(mostra){
	  if(mostra==true){
	    x  = 'func_sau_prestadores.php?funcao_js=parent.js_mostraprestador1|s110_i_codigo|z01_nome';
	    js_OpenJanelaIframe('', 'db_iframe_sau_prestadores',x,'Pesquisa',true);
	  }else{
	     if(document.form1.s118_i_prestador.value != ''){
	        x  = 'func_sau_prestadores.php';
	        x += '?pesquisa_chave='+document.form1.s118_i_prestador.value;
	        x += '&funcao_js=parent.js_mostraprestador';
	        js_OpenJanelaIframe('','db_iframe_sau_prestadores',x,'Pesquisa',false);
	     }
	  }
}
function js_mostraprestador(chave1,erro){

  	document.form1.z01_nome.value = chave1; 
  	if(erro==true){ 
    	document.form1.s118_i_prestador.focus(); 
    	document.form1.s118_i_prestador.value = ''; 
  	}
}
function js_mostraprestador1(chave1,chave2){
  document.form1.s118_i_prestador.value = chave1;
  document.form1.z01_nome.value = chave2;
  
  db_iframe_sau_prestadores.hide();
  
}
 




/**
 * Selec tipo de Consulta
 */
function js_tipo( tipo ){
	var idRow1 = document.getElementById("tipo_consulta");
	var idRow2 = document.getElementById("tipo_exame");
		idRow1.style.display = tipo=='C'?'':'none';
		idRow2.style.display = tipo=='E'?'':'none';
		 
} 


</script>