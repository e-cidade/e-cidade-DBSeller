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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_iptubase_classe.php"));
include_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);
$clcriaabas = new cl_criaabas;
?>

<html>
	<head>
		<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<meta http-equiv="Expires" CONTENT="0">
			<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
			<link href="estilos.css" rel="stylesheet" type="text/css">
	</head>
	
	<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
		<table width="790" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
		  <tr> 
		    <td width="360" height="18" >&nbsp;</td>
		  </tr>
		</table>
		
		<center>
			<table valign="top" marginwidth="0" width="790" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align='center'>
						<form name='form1' action='ate2_relatoriotempo002.php' method='POST'>
							<fieldset><legend><strong>Filtros</strong></legend>
								<table>
									<tr>
										<td>
											<label id='label_data' for='dataInicial'><strong>Data Tarefa:</strong></label>
										</td>
										<td>
											<?php
												db_inputdata('dataInicial','','','', true, 'text', 1);
											?>
											até
											<?php
												db_inputdata('dataFinal','','','', true, 'text', 1);
											?>
										</td>
									</tr>
									<tr>
										<td>
											<label id='label_ordenarPor' for='ordenarPor'><strong>Ordenar por:</strong></label>
										</td>
										<td>
											<?php
												$aOrdenar = array('1'=>'Tarefa', '2'=>'Data Cria&ccedil;&atilde;o', '3'=>'Data Atualiza&ccedil;&atilde;o', '4'=>'Tempo Atualização');
												db_select('ordenarPor', $aOrdenar, true, 1, 'style="width: 200px;"');
												db_input('desc', '', '', '', 'checkbox', 1, 'onclick = "enviaVal(this.checked)"');
											?>	
											Decrescente
											<input type='hidden' name='checkvalue' />
										</td>											
									</tr>
									<tr>
										<td>
											<label id='label_tarefasAutorizadas' for='tarefasAutorizadas'><strong>Tarefas:</strong></label>
										</td>
										<td>
											<?php
												$aTarefas = array('1'=>'Todas', '2'=>'Autorizadas', '3'=>'N&atilde;o Autorizadas');
												db_select('tarefasAutorizadas', $aTarefas, true, 1, 'style="width: 200px;"');
											?>												
										</td>
									</tr>
									<tr>
										<td>
											<label id='label_considerarTarefas' for='considerarTarefas'><strong>Considerar:</strong></label>
										</td>
										<td>
											<?php
												$aConsiderTarefas = array('1'=>'Todas', '2'=>'Conclu&iacute;das', '3'=>'Em Andamento');
												db_select('considerarTarefas', $aConsiderTarefas, true, 1, 'style="width: 200px;"');
											?>
										</td>
									</tr>
								</table>	
												<input name="processar" type="button"  id="processar" value="Processar" onclick="js_relatorio()" >								
							</fieldset>
						</form>															
					</td>
				</tr>
			</table>
		</center>		
		
	<?php
		db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
	?>	
	</body>
</html>

<script>
	function js_relatorio() {
		var dataInicial_				= document.form1.dataInicial.value;
		var dataFinal_					= document.form1.dataFinal.value;
		var ordenarPor_				= document.form1.ordenarPor.value;
		var tarefasAutorizadas_	= document.form1.tarefasAutorizadas.value;
		var considerarTarefas_	= document.form1.considerarTarefas.value;
		var desc_						= document.form1.checkvalue.value;
		
		erro ='';
		if(dataInicial_ == '' || dataFinal_ =='') {
			erro += '- O intervalo entre datas precisa ser preenchido\n';				
			alert(erro);
			return false;	
		}
	
		var url = 'ate2_relatoriotempo002.php?dataInicial='+dataInicial_+'&dataFinal='+dataFinal_+'&ordenarPor='+ordenarPor_+'&tarefasAutorizadas='+tarefasAutorizadas_+'&considerarTarefas='+considerarTarefas_+'&desc='+desc_+'';

		report  = window.open(url, '', 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
		report.moveTo(0,0);
	}
	function enviaVal(estado) {
		document.form1.checkvalue.value = estado;
	}
		
</script>