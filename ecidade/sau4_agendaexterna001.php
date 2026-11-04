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

include(modification("classes/db_sau_agendaexterna_ext_classe.php"));
include(modification("classes/db_sau_agendaexternaespec_classe.php"));
include(modification("classes/db_sau_agendaexternaexame_classe.php"));
include(modification("classes/db_cgs_und_classe.php"));


include(modification("dbforms/db_funcoes.php"));
include(modification("dbforms/db_classesgenericas.php"));

//$z01_d_cadast_dia = date("d",db_getsession("DB_datausu"));
//$z01_d_cadast_mes = date("m",db_getsession("DB_datausu"));
//$z01_d_cadast_ano = date("Y",db_getsession("DB_datausu"));
//$z01_i_login = DB_getsession("DB_id_usuario");


db_postmemory($HTTP_POST_VARS);

$db_opcao = 1;

$clrotulo                 = new rotulocampo;
$clsau_agendaexterna      = new cl_sau_agendaexterna_ext;
$clsau_agendaexternaespec = new cl_sau_agendaexternaespec;
$clsau_agendaexternaexame = new cl_sau_agendaexternaexame;

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

if( isset($opcao) ){
	$db_opcao = $opcao=="alterar"?2:3;

	$result = $clsau_agendaexterna->sql_record( $clsau_agendaexterna->sql_query_ext($s118_i_codigo) );
	if( $clsau_agendaexterna->numrows > 0 ){
		db_fieldsmemory($result,0); 
	}
}

if( isset($incluir) ){
	db_inicio_transacao();
	
	$clsau_agendaexterna->s118_d_data  = date("Y-m-d",db_getsession("DB_datausu"));
	$clsau_agendaexterna->s118_i_login = DB_getsession("DB_id_usuario");
	$clsau_agendaexterna->incluir("");		
	
	if( $clsau_agendaexterna->numrows_incluir > 0 ){
		if( $s118_c_tipoagenda == "C" ){
			$clsau_agendaexternaespec->s119_i_agendaexterna = $clsau_agendaexterna->s118_i_codigo;
			$clsau_agendaexternaespec->s119_i_especialidade = $s119_i_especialidade;
			$clsau_agendaexternaespec->incluir("");
			if( $clsau_agendaexternaespec->numrows_incluir == 0 ){
				$clsau_agendaexterna->numrows_incluir = 0;
				$clsau_agendaexterna->erro_msg   = $clsau_agendaexternaespec->erro_msg;
				$clsau_agendaexterna->erro_campo = $clsau_agendaexternaespec->erro_campo;
			}
			
		}else{
			$clsau_agendaexternaexame->s120_i_agendaexterna = $clsau_agendaexterna->s118_i_codigo;
			$clsau_agendaexternaexame->s120_i_exame         = $s120_i_exame;
			$clsau_agendaexternaexame->incluir("");
			if( $clsau_agendaexternaexame->numrows_incluir == 0 ){
				$clsau_agendaexterna->numrows_incluir = 0;
				$clsau_agendaexterna->erro_msg   = $clsau_agendaexternaexame->erro_msg;
				$clsau_agendaexterna->erro_campo = $clsau_agendaexternaexame->erro_campo;
			}
		}
	}
	
	db_fim_transacao();
}else if( isset($alterar) ){
	db_inicio_transacao();
	
	$clsau_agendaexternaespec->excluir(null, "s119_i_agendaexterna = $s118_i_codigo");
	$clsau_agendaexternaexame->excluir(null, "s120_i_agendaexterna = $s118_i_codigo");
	$clsau_agendaexterna->s118_i_prestador = (int)$s118_i_prestador==0?'null':$s118_i_prestador; 
	$clsau_agendaexterna->alterar($s118_i_codigo);
	
	
	if( $s118_c_tipoagenda == "C" ){
		$clsau_agendaexternaespec->s119_i_agendaexterna = $clsau_agendaexterna->s118_i_codigo;
		$clsau_agendaexternaespec->s119_i_especialidade = $s119_i_especialidade;
		$clsau_agendaexternaespec->incluir("");
		if( $clsau_agendaexternaespec->numrows_incluir == 0 ){
			$clsau_agendaexterna->numrows_incluir = 0;
			$clsau_agendaexterna->erro_msg   = $clsau_agendaexternaespec->erro_msg;
			$clsau_agendaexterna->erro_campo = $clsau_agendaexternaespec->erro_campo;
		}
		
	}else{
		$clsau_agendaexternaexame->s120_i_agendaexterna = $clsau_agendaexterna->s118_i_codigo;
		$clsau_agendaexternaexame->s120_i_exame         = $s120_i_exame;
		$clsau_agendaexternaexame->incluir("");
		if( $clsau_agendaexternaexame->numrows_incluir == 0 ){
			$clsau_agendaexterna->numrows_incluir = 0;
			$clsau_agendaexterna->erro_msg   = $clsau_agendaexternaexame->erro_msg;
			$clsau_agendaexterna->erro_campo = $clsau_agendaexternaexame->erro_campo;
		}
	}
	
	db_fim_transacao();
	
}else if( isset($excluir) ){
	db_inicio_transacao();
	
	$clsau_agendaexternaespec->excluir(null, "s119_i_agendaexterna = $s118_i_codigo");
	$clsau_agendaexternaexame->excluir(null, "s120_i_agendaexterna = $s118_i_codigo");
	$clsau_agendaexterna->excluir($s118_i_codigo);
			
	db_fim_transacao();
	
}

if( isset($chavepesquisacgs) && $chavepesquisacgs != 0 ){
	?>
	<script>
		parent.document.formaba.a2.disabled = false;
		parent.iframe_a2.location.href='sau4_agendaexterna004.php?chavepesquisacgs=<?=$s118_i_numcgs?>&s124_i_numcgs=<?=$s118_i_numcgs?>&z01_v_nome=<?=$z01_v_nome?>';
		//parent.mo_camada('a4');
	</script>
	<?php
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
<!-- 
<table width="100%" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td>&nbsp;</td>
 </tr>
</table>
-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="left" valign="top" bgcolor="#CCCCCC">
    <center>
        <?php
        include(modification("forms/db_frmagendaexterna.php"));
        ?>
    </center>
    </td>
  </tr>
</table>
<?php
// db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
  js_tabulacaoforms("form1","s118_i_numcgs",true,1,"s118_i_numcgs",true);
</script>

<?php
if(isset($incluir) || isset($alterar) ){
	if(( isset($incluir) && $clsau_agendaexterna->numrows_incluir==0)  || (isset($alterar) && $clsau_agendaexterna->numrows_alterar==0) ){
		$clsau_agendaexterna->erro(true,false);
		$db_botao=true;
		echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
		if($clsau_agendaexterna->erro_campo!=""){
			echo "<script> document.form1.".$clsau_agendaexterna->erro_campo.".style.backgroundColor='#99A9AE';</script>";
			echo "<script> document.form1.".$clsau_agendaexterna->erro_campo.".focus();</script>";
		}
	}else{
		$clsau_agendaexterna->erro(true,false);
		db_redireciona("sau4_agendaexterna001.php?chavepesquisacgs=$s118_i_numcgs&s118_i_numcgs=$s118_i_numcgs&z01_v_nome=$z01_v_nome&z01_v_cgccpf=$z01_v_cgccpf&z01_v_ident=$z01_v_ident" );
	}
}
if( isset($excluir)  ){
	if($clsau_agendaexterna->numrows_excluir==0){
		$clsau_agendaexterna->erro(true,false);
	}else{
		//$clsau_agendaexterna->erro(true,false);
		db_redireciona("sau4_agendaexterna001.php?chavepesquisacgs=$s118_i_numcgs&s118_i_numcgs=$s118_i_numcgs&z01_v_nome=$z01_v_nome&z01_v_cgccpf=$z01_v_cgccpf&z01_v_ident=$z01_v_ident" );		
	}
}


?>