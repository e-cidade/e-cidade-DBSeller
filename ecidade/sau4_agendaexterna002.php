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

include(modification("classes/db_sau_agendatransporte_ext_classe.php"));
include(modification("classes/db_sau_agendaveiculo_classe.php"));

include(modification("classes/db_cgs_und_classe.php"));


include(modification("dbforms/db_funcoes.php"));
include(modification("dbforms/db_classesgenericas.php"));


db_postmemory($HTTP_POST_VARS);

$db_opcao = 1;

$clrotulo                 = new rotulocampo;
$clsau_agendatransporte   = new cl_sau_agendatransporte_ext;
$clsau_agendaveiculo      = new cl_sau_agendaveiculo;
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;


//Pesquisa
if( isset($s124_d_saida) && isset($s121_i_veiculo) ){
	$result = $clsau_agendatransporte->sql_record( $clsau_agendatransporte->sql_query_ext ( "", "*", "s124_i_codigo limit 1", 
																								"s124_d_saida = '$s124_d_saida' and
																								 s121_i_veiculo = $s121_i_veiculo" ) );  
	if( $clsau_agendatransporte->numrows > 0 ){																							
		$s124_c_hora = pg_result($result,0,"s124_c_hora"); 	
	}
}

//Opções Alterar/Excluir
if( isset($opcao) ){
	$db_opcao = $opcao=="alterar"?2:3;

	$result = $clsau_agendatransporte->sql_record( $clsau_agendatransporte->sql_query_ext($s124_i_codigo) );
	if( $clsau_agendatransporte->numrows > 0 ){
		db_fieldsmemory($result,0);
		$ve22_descr = $ve01_placa." - ".$ve22_descr; 
	}
}

if( isset($incluir) ){
	db_inicio_transacao();
	
	$clsau_agendatransporte->s124_d_data    = date("Y-m-d",db_getsession("DB_datausu"));
	$clsau_agendatransporte->s124_i_login   = DB_getsession("DB_id_usuario");
	$clsau_agendatransporte->s124_c_veiculo = 'P'; //P-Prefeitura
	$clsau_agendatransporte->incluir("");		
	
	if( $clsau_agendatransporte->numrows_incluir > 0 ){
		$clsau_agendaveiculo->s121_i_agendatransporte = $clsau_agendatransporte->s124_i_codigo;
		$clsau_agendaveiculo->s121_i_veiculo          = $s121_i_veiculo; 
		$clsau_agendaveiculo->incluir("");
		if( $clsau_agendaveiculo->numrows_incluir == 0 ){
			$clsau_agendatransporte->numrows_incluir = 0;
			$clsau_agendatransporte->erro_msg   = $clsau_agendaveiculo->erro_msg;
			$clsau_agendatransporte->erro_campo = $clsau_agendaveiculo->erro_campo;
		}
	}
	
	db_fim_transacao();
}else if( isset($alteracao) ){
	db_inicio_transacao();
	
	$clsau_agendaveiculo->excluir(null, "s121_i_agendatransporte = $s124_i_codigo");
	$clsau_agendatransporte->alterar($s124_i_codigo);
	
	if( $clsau_agendatransporte->numrows_alterar > 0 ){
		if( $s124_c_veiculo == "P" ){
			$clsau_agendaveiculo->s121_i_agendatransporte = $clsau_agendatransporte->s124_i_codigo;
			$clsau_agendaveiculo->s121_i_veiculo          = $s121_i_veiculo; 
			$clsau_agendaveiculo->incluir("");
			if( $clsau_agendaveiculo->numrows_incluir == 0 ){
				$clsau_agendatransporte->numrows_incluir = 0;
				$clsau_agendatransporte->erro_msg   = $clsau_agendaveiculo->erro_msg;
				$clsau_agendatransporte->erro_campo = $clsau_agendaveiculo->erro_campo;
			}
		}
	}
		
	db_fim_transacao();
	
}else if( isset($excluir) ){
	db_inicio_transacao();
	
	$clsau_agendaveiculo->excluir(null, "s121_i_agendatransporte = $s124_i_codigo");
	$clsau_agendatransporte->excluir($s124_i_codigo);
			
	db_fim_transacao();	
}

?>


<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/strings.js"></script>

<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
 
<table width="100%" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td>&nbsp;</td>
 </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
        <?php
        include(modification("forms/db_frmsau_agendameiotransporte.php"));
        ?>
    </center>
    </td>
  </tr>
</table>
<?php
	db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));

	if( isset($s124_d_saida) && isset($s121_i_veiculo) ){
 		echo "<script>js_tabulacaoforms('form1','s124_i_numcgs',true,1,'s124_i-numcgs',true);</script>";
	}else{
		echo "<script>js_tabulacaoforms('form1','s121_i_veiculo',true,1,'s121_i_veiculo',true);</script>";
	}
?>
</body>
</html>

<?php
if(isset($incluir) || isset($alterar) ){
	if( ( isset($incluir) && $clsau_agendatransporte->numrows_incluir==0)  || (isset($alterar) && $clsau_agendatransporte->numrows_alterar==0) ){
		$clsau_agendatransporte->erro(true,false);
		$db_botao=true;
		echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
		if($clsau_agendatransporte->erro_campo!=""){
			echo "<script> document.form1.".$clsau_agendatransporte->erro_campo.".style.backgroundColor='#99A9AE';</script>";
			echo "<script> document.form1.".$clsau_agendatransporte->erro_campo.".focus();</script>";
		}
	}else{
		$clsau_agendatransporte->erro(true,false);
		//db_redireciona("sau4_agendaexterna002.php?chavepesquisacgs=$s124_i_numcgs&s124_i_numcgs=$s124_i_numcgs&z01_v_nome=$z01_v_nome" );
	}
	echo "<script>js_reloadtransporte()</script>";
}
if( isset($excluir)  ){
	if($clsau_agendatransporte->numrows_excluir==0){
		$clsau_agendatransporte->erro(true,false);
	}else{
		$clsau_agendatransporte->erro(true,false);
		echo "<script>js_reloadtransporte()</script>";
		//db_redireciona("sau4_agendaexterna004.php?chavepesquisacgs=$s124_i_numcgs&s124_i_numcgs=$s124_i_numcgs&z01_v_nome=$z01_v_nome" );
	}
}
?>