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
include(modification("libs/db_utils.php"));
include(modification("libs/db_jsplibwebseller.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_sau_agendaexterna_ext_classe.php"));
$clsau_agendaexterna  = new cl_sau_agendaexterna_ext;
$clsau_agendaexterna->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("s118_i_prestador");
$clrotulo->label("s118_c_protocolo");
$clrotulo->label("s118_c_horamarcada");
$clrotulo->label("s118_d_data");
$clrotulo->label("z01_v_nome");
$db_opcao=1;

$db_botao = true;
$result=$clsau_agendaexterna->sql_record($clsau_agendaexterna->sql_query_ext("","*","","s118_i_codigo=$s118_i_codigo"));
if( $clsau_agendaexterna->numrows > 0 ){
db_fieldsmemory($result,0);
}
if(isset($incluir)){	    	  
		db_inicio_transacao();
		$clsau_agendaexterna->s118_i_codigo   = $s118_i_codigo;	
		$clsau_agendaexterna->s118_i_prestador      = $s118_i_prestador;
		$clsau_agendaexterna->s118_c_horamarcada = $s118_c_horamarcada;
		$clsau_agendaexterna->s118_d_data   = $s118_d_data;			
		$clsau_agendaexterna->s118_v_protocolo    = $s118_v_protocolo;	
		$clsau_agendaexterna->alterar($s118_i_codigo);
		db_fim_transacao();
}

  
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table align="center" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="center" valign="top" bgcolor="#CCCCCC">
    <br><br>
    <center>
<form name="form1" method="post" action="">
<center>
<table>
	<tr>
		<td>
			<fieldset><legend><b>Prestadora</b></legend>
				<table border="0">
				 	<!-- Prestadora -->
					<tr>
						<td nowrap title="<?=@$Ts118_i_prestador?>">
								<?php
								db_ancora ( @$Ls118_i_prestador, "js_pesquisas118_i_prestador(true);", 1 );
								?>
							</td>
						<td>
								<?php
								db_input ( 's118_i_prestador', 10, @$Is118_i_prestador, true, 'text', 1, "onChange='js_pesquisas118_i_prestador(false);'" );
								db_input ( 'z01_nome', 48, @$Iz01_nome, true, 'text', 3 );
								?>
								
							</td>
					</tr>
					<tr>
						<td nowrap title="<?=@$Ts118_v_protocolo?>">
							<?=@$Ls118_v_protocolo?>
						</td>
						<td>
							<?php
							db_input('s118_v_protocolo',10,@$Is118_v_protocolo,true,'text',$db_opcao,"")
							?>
						</td>
					</tr>
					<tr>
						<td nowrap title="<?=@$Ts118_d_data?>">
							<?=@$Ls118_d_data?>
						</td>
						<td>
							<?php
							db_inputdata('s118_d_data',@$s118_d_data_dia,@$s118_d_data_mes,@$s118_d_data_ano,true,'text',$db_opcao,"")
							?>
							<?=@$Ls118_c_horamarcada?>
							<?php
							db_input('s118_c_horamarcada',5,@@$Is118_c_horamarcada,true,'text',$db_opcao,"onKeyUp=\"mascara_hora(this.value,'s118_c_horamarcada', event)\"  ");
							?>
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>
 
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" >
<input name="fechar" type="button" id="fechar" value="Fechar" onclick="js_fechar();" >
 </center>
</form>
</center>
    </td>
  </tr>
</table>
<?//db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
</body>
</html>
<script>
js_tabulacaoforms("form1","s118_i_prestador",true,1,"s118_i_prestador",true);

/**
 * Pesquisa Prestador
 */
function js_pesquisas118_i_prestador(mostra){
	  if(mostra==true){	    
	    x  = 'func_sau_prestadores.php';
	    x  += '?funcao_js=parent.IFdb_iframe_agendamento.js_mostraprestador1|s110_i_codigo|z01_nome';
	    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_sau_prestadores',x,'Pesquisa',true);
	  }else{
	     if(document.form1.s118_i_prestador.value != ''){	
	        x  = 'func_sau_prestadores.php';
	        x += '?pesquisa_chave='+document.form1.s118_i_prestador.value;
	        x += '&funcao_js=parent.IFdb_iframe_agendamento.js_mostraprestador';
	        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_sau_prestadores',x,'Pesquisa',false);
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
  parent.db_iframe_sau_prestadores.hide();  
}

function js_fechar(){
	parent.db_iframe_agendamento.hide();
}
</script>