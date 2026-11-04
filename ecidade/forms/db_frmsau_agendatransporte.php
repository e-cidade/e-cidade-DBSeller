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


$clsau_agendatransporte->rotulo->label ();

//Veiculo
$clrotulo->label ( "s121_i_veiculo" );
$clrotulo->label ( "ve22_descr" );

//cgs_und
$clrotulo->label ( "z01_i_cgsund" );
$clrotulo->label ( "z01_v_nome" );
$clrotulo->label ( "z01_v_cgccpf" );
$clrotulo->label ( "z01_v_ident" );

//nome prestador
$clrotulo->label ( "z01_nome" );

//especialidade (Consulta)
$clrotulo->label ( "sd27_i_rhcbo" );
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
		<fieldset><legend><b>Agenda Transporte</b></legend>
		<table>
			<!-- CGS -->
			<tr align="center">
				<td>
				<table>
					<tr>
						<td nowrap title="<?=@$Ts124_i_numcgs?>">
								<?php
								db_ancora ( @$Ls124_i_numcgs, "", 3 );
								?>
							</td>
						<td colspan="3">
								<?php
								db_input ( 's124_i_codigo', 10, $Is124_i_codigo, true, 'hidden', 1);
								db_input ( 's124_i_numcgs', 10, $Is124_i_numcgs, true, 'text', 3 );
								db_input ( 'z01_v_nome', 48, $Iz01_v_nome, true, 'text', 3 );
								?>
							</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
				<fieldset><legend><b>Indique o Meio de Transporte</b></legend>
				<table>
					<!-- Data / Hora Marcada -->
					<tr>
						<td nowrap title="<?=@$Ts124_d_saida?>">
							<?=@$Ls124_d_saida?>
						</td>
						<td>
							<?php
							db_inputdata ( 's124_d_saida', @$s124_d_saida_dia, @$s124_d_saida_mes, @$s124_d_saida_ano, true, 'text', $db_opcao, "" );
							?>
						</td>
						<td>
							<?=@$Ls124_c_hora?>
						</td>
						<td>
							<?php
							db_input ( 's124_c_hora', 10, $Is124_c_hora, true, 'text', $db_opcao );
							?>
						</td>
					</tr>
					
					
					<!-- Tipo Veiculo -->
					<tr>
						<td nowrap title="<?=@$Ts124_c_veiculo?>">
							<?=@$Ls124_c_veiculo?>
						</td>
						<td>
							<?php
							$x = array ('P' => 'Prefeitura', 'R' => 'Próprio', 'O' => 'Ônibus' );
							db_select ( 's124_c_veiculo', $x, true, $db_opcao, "onChange='js_tipo( this.value )';" );
							?>
						</td>
						<td id="tipo_veiculo1"  nowrap title="<?=@$Ts121_i_veiculo?>">
							<?php
							db_ancora(@$Ls121_i_veiculo,"js_pesquisas121_i_veiculo(true);",$db_opcao);
							?>
						</td>
						<td id="tipo_veiculo2">
							<?php
							db_input('s121_i_veiculo',10,@$Is121_i_veiculo,true,'text',$db_opcao," onchange='js_pesquisas121_i_veiculo(false);'")
							?>
							<?php
							db_input('ve22_descr',30,@$Ive22_descr,true,'text',3,'')
							?>
						</td>
						
					</tr>
					
					<!--  Passagem -->
					<tr>
						<td nowrap title="<?=@$Ts124_c_passagem?>">
							<?=@$Ls124_c_passagem?>
						</td>
						<td>
							<?php
							db_input ( 's124_c_passagem', 10, $Is124_c_passagem, true, 'text', $db_opcao );
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
							onclick="js_limpa()">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<input name="fechar" 
							type="button" 
							id="btn_fechar" 
							value="Fechar"
							onclick="js_fechar()">
							
				</td>
			</tr>
			<tr>
				<td colspan="2">
						<?php
							
						$chavepri = array ("s124_i_codigo" => @$s124_i_codigo );
						
						$cliframe_alterar_excluir->chavepri = $chavepri;
						@$cliframe_alterar_excluir->sql = $clsau_agendatransporte->sql_query_ext ( "", "
																				s124_i_codigo, s124_d_saida, s124_c_hora, s124_c_passagem, s121_i_veiculo, ve01_placa||' - '||ve22_descr as ve22_descr,				
																				case s124_c_veiculo 
																					when 'P' then 'Prefeitura'
																					when 'R' then 'Próprio'																																																															when 'P' then 'Prefeitura'
																					when 'O' then 'Ônibus'
																					else 'Não Informado'
																				end as s124_c_veiculo															
																			", "s124_i_codigo desc", "s124_i_numcgs = " . ( int )$chavepesquisacgs );
							$cliframe_alterar_excluir->campos = "s124_i_codigo, s124_d_saida, s124_c_hora, s124_c_veiculo, s124_c_passagem, s121_i_veiculo, ve22_descr";
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
if( parent.iframe_a1 != undefined){
	document.getElementById("btn_fechar").style.visibility = "hidden";
}


/**
 * Inicia tipo de consulta com Consulta
 */

js_tipo( $F('s124_c_veiculo') );

/**
 * Pesquisa Veiculo
 */
function js_pesquisas121_i_veiculo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_veiculos','func_veiculos.php?funcao_js=parent.js_mostraveiculos1|ve01_codigo|ve01_placa|ve22_descr','Pesquisa',true);
  }else{
     if(document.form1.s121_i_veiculo.value != ''){ 
        js_OpenJanelaIframe('','db_iframe_veiculos','func_veiculos.php?pesquisa_chave='+document.form1.s121_i_veiculo.value+'&funcao_js=parent.js_mostraveiculos','Pesquisa',false);
     }else{
       document.form1.ve22_descr.value = ''; 
     }
  }
}

function js_mostraveiculos(erro,chave1,chave2,chave3){
  document.form1.ve22_descr.value = chave2+' - '+chave3;
  if(erro==true){ 
    document.form1.s121_i_veiculo.focus(); 
    document.form1.s121_i_veiculo.value = ''; 
  }
}
function js_mostraveiculos1(chave1,chave2,chave3){
  document.form1.s121_i_veiculo.value = chave1;
  document.form1.ve22_descr.value     = chave2+' - '+chave3;

  db_iframe_veiculos.hide();
}
 




/**
 * Selec tipo de Consulta
 */
function js_tipo( tipo ){
	var idCol1 = document.getElementById("tipo_veiculo1");
	var idCol2 = document.getElementById("tipo_veiculo2");
	idCol1.style.visibility = tipo=='P'?'':'hidden';
	idCol2.style.visibility = tipo=='P'?'':'hidden';
		
		 
} 

/**
 * Botão limpa
 */
function js_limpa(){
	location.href='sau4_agendaexterna004.php?chavepesquisacgs=<?=$s124_i_numcgs?>&s124_i_numcgs=<?=$s124_i_numcgs?>&z01_v_nome=<?=$z01_v_nome?>'; 
}

/**
 * Botão fechar
 */
function js_fechar(){
	parent.db_iframe_agendamento.hide();
 
}


</script>