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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_procfiscal_estendida_classe.php"));
require_once(modification("classes/db_fis_procfiscalinscr_classe.php"));
require_once(modification("classes/db_fis_procfiscalmatric_classe.php"));
require_once(modification("classes/db_fis_procfiscalsani_classe.php"));
require_once(modification("classes/db_fis_procfiscalcgm_classe.php"));
require_once(modification("classes/db_fis_procfiscalprot_classe.php"));
require_once(modification("classes/db_fis_procfiscalfiscais_classe.php"));

$clprocfiscal       = new cl_fis_procfiscal_estendida;
$clprocfiscalinscr  = new cl_fis_procfiscalinscr;
$clprocfiscalmatric = new cl_fis_procfiscalmatric;
$clprocfiscalsani   = new cl_fis_procfiscalsani;
$clprocfiscalcgm    = new cl_fis_procfiscalcgm;
$clprocfiscalprot   = new cl_fis_procfiscalprot;
  
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
include(modification("classes/db_protprocesso_classe.php"));
$clprotprocesso = new cl_protprocesso;
db_postmemory($HTTP_POST_VARS);
   $db_opcao = 22;
$db_botao = false;

if(isset($alterar)){
//echo '<pre>'; var_dump($_POST); die;
	$sqlerro=false;
	db_inicio_transacao();
	$clprocfiscal->y100_coddepto = db_getsession("DB_coddepto");
	$clprocfiscal->alterar($y100_sequencial);
	$erro_msg = $clprocfiscal->erro_msg; 
	if($clprocfiscal->erro_status==0){
		$sqlerro=true;
	} 

// excluir todos par adepois incluir
	$clprocfiscalinscr->excluir(null," y103_procfiscal = $y100_sequencial ");
	if($clprocfiscalinscr->erro_status==0){
		$sqlerro=true;
		$erro_msg = $clprocfiscalinscr->erro_msg; 
	} 

	$clprocfiscalmatric->excluir(null," y102_procfiscal = $y100_sequencial ");
	if($clprocfiscalmatric->erro_status==0){
		$sqlerro=true;
		$erro_msg = $clprocfiscalmatric->erro_msg; 
	} 


	$clprocfiscalsani->excluir(null," y104_procfiscal = $y100_sequencial ");
	if($clprocfiscalsani->erro_status==0){
		$sqlerro=true;
		$erro_msg = $clprocfiscalsani->erro_msg; 
	}

	$clprocfiscalcgm->excluir(null," y101_procfiscal = $y100_sequencial ");
	if($clprocfiscalcgm->erro_status==0){
		$sqlerro=true;
		$erro_msg = $clprocfiscalcgm->erro_msg; 
	}

// se tiver inscricao
	if(isset($q02_inscr) and $q02_inscr!=""){
		$clprocfiscalinscr->y103_inscr = $q02_inscr;
		$clprocfiscalinscr->y103_procfiscal = $y100_sequencial;
		$clprocfiscalinscr->incluir(null);
		if($clprocfiscalinscr ->erro_status==0){
			$sqlerro=true;
			$erro_msg = $clprocfiscalinscr->erro_msg; 
		} 
	}

// se tiver matricula
	if(isset($j01_matric) and $j01_matric!=""){
		$clprocfiscalmatric->y102_matric     = $j01_matric;
		$clprocfiscalmatric->y102_procfiscal = $y100_sequencial;
		$clprocfiscalmatric->incluir();
		if($clprocfiscalmatric ->erro_status==0){
			$sqlerro=true;
			$erro_msg = $clprocfiscalmatric->erro_msg; 
		} 
	}
// se tiver sanitario
	if(isset($y80_codsani) and $y80_codsani!=""){
		$clprocfiscalsani->y104_codsani    = $y80_codsani;
		$clprocfiscalsani->y104_procfiscal = $y100_sequencial;
		$clprocfiscalsani->incluir(null);
		if($clprocfiscalsani ->erro_status==0){
			$sqlerro=true;
			$erro_msg = $clprocfiscalsani->erro_msg; 
		} 
	}
// cgm
	if($z01_numcgm==""){
		$sqlerro=true;
		$erro_msg = "Campo cgm não informado!";
	}else{
		$clprocfiscalcgm->y101_numcgm     = $z01_numcgm;
		$clprocfiscalcgm->y101_procfiscal = $y100_sequencial;
		$clprocfiscalcgm->incluir(null);
		if($clprocfiscalcgm ->erro_status==0){
			$sqlerro=true;
			$erro_msg = $clprocfiscalcgm->erro_msg; 
		} 
	}

    $where = " p58_instit = ".db_getsession("DB_instit") . " and coddepto = ".db_getsession("DB_coddepto");
	$sqlVerificaTipo = "select * from fiscalizacao.fis_tipoprocessoadministrativo";
	$resultVerificaTipo = db_query($sqlVerificaTipo);
	db_fieldsmemory($resultVerificaTipo, 0);
	if (isset($verificatipo) and $verificatipo == 't') {
		$where .= " and p58_codigo in (select protprocesso.p58_codigo from protprocesso ";
		$where .= " inner join tipoproc on protprocesso.p58_codigo = tipoproc.p51_codigo ";
		$where .= " inner join fiscalizacao.fis_parfiscal on tipoproc.p51_codigo = fis_parfiscal.y32_tipoprocpadrao) ";
	}
/*	$where .= " and p58_codigo not in (select distinct p58_codproc from protprocesso ";
	$where .= " inner join procandam on p61_codproc = p58_codproc and p58_codandam = p61_codandam ";
	$where .= " inner join arqandam on p61_codandam = p69_codandam where p61_coddepto = ".db_getsession('DB_coddepto')." and p69_arquivado = true) " ;
*/      $where .= " and p58_codproc not in (SELECT p67_codproc FROM procarquiv INNER JOIN arqandam ON p69_codarquiv = p67_codarquiv WHERE p69_arquivado = 't')";	

	$aPartesNumero = explode("/", $p58_numero);
   	$iAno = db_getsession("DB_anousu");
   	if (count($aPartesNumero) > 1 && !empty($aPartesNumero[1])) {
      $iAno = $aPartesNumero[1];
   	}
   	$iNumero = $aPartesNumero[0];
   	$where  .= " and p58_ano = {$iAno} and p58_numero = '{$iNumero}'";
   	$sql     = $clprotprocesso->sqlGetProcessoAdministrativo("p58_codproc", $where,"p58_codproc desc");
	$rsProt = db_query($sql);
   	if(pg_num_rows($rsProt) == 0){
   		$sqlerro = true;
   		$erro_msg = "Processo Invalido, Verifique o Processo Digitado!";
   	}else{
		db_fieldsmemory($rsProt,0);
	}

// processo protocolo
	if($p58_codproc==""){
		$sqlerro=true;
		$erro_msg = "Campo Processo não informado!";
	}else{
		$sqlprot = "	select y105_sequencial from fiscalizacao.fis_procfiscalprot where y105_procfiscal = $y100_sequencial";
		$resultprot = pg_query($sqlprot);
		db_fieldsmemory($resultprot,0);
		$clprocfiscalprot->y105_sequencial= $y105_sequencial;
		$clprocfiscalprot->y105_protprocesso = $p58_codproc;
		$clprocfiscalprot->alterar($y105_sequencial);
		if($clprocfiscalprot ->erro_status==0){
			$sqlerro=true;
			$erro_msg = $clprocfiscalprot->erro_msg; 
		} 
	}

	db_fim_transacao($sqlerro);
	$db_opcao = 2;
	$db_botao = true;
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $db_botao = true;
	 $sql ="
				  select fis_procfiscal.*,y103_inscr as q02_inscr,y102_matric as j01_matric,
					       y105_protprocesso as p58_codproc,y104_codsani,
					       y33_descricao,cgm.z01_nome,cgm.z01_numcgm,c.z01_nome as z01_nome1, p58_numero||'/'||p58_ano as p58_numero,
					       descrdepto,
					       y106_sequencial, y106_cadfiscais, nome, ypl01_dtlanc
					from fiscalizacao.fis_procfiscal
					inner join db_depart         on db_depart.coddepto               = fis_procfiscal.y100_coddepto 
					inner join fiscalizacao.fis_procfiscalcadtipo on fis_procfiscalcadtipo.y33_sequencial = fis_procfiscal.y100_procfiscalcadtipo 
					inner join fiscalizacao.fis_procfiscalcgm     on y101_procfiscal                  = fis_procfiscal.y100_sequencial
					inner join cgm               on cgm.z01_numcgm                   = y101_numcgm
					inner join fiscalizacao.fis_procfiscalprot    on fis_procfiscalprot.y105_procfiscal   = fis_procfiscal.y100_sequencial
					inner join protprocesso      on protprocesso.p58_codproc         = fis_procfiscalprot.y105_protprocesso
					inner join cgm c             on protprocesso.p58_numcgm          = c.z01_numcgm
					left  join fiscalizacao.fis_procfiscalmatric  on y102_procfiscal                  = fis_procfiscal.y100_sequencial
					left  join fiscalizacao.fis_procfiscalinscr   on y103_procfiscal                  = fis_procfiscal.y100_sequencial
					left  join fiscalizacao.fis_procfiscalsani    on y104_procfiscal                  = fis_procfiscal.y100_sequencial
					left  join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal                  = fis_procfiscal.y100_sequencial and y106_principal = true
					left  join db_usuarios       on id_usuario                       = y106_cadfiscais
					left join fiscalizacao.fis_processofiscalativo on processo_fiscal = fis_procfiscal.y100_sequencial and fiscal = y106_cadfiscais
					left join fiscalizacao.fis_dataprocfiscal on ypl01_procfiscal = y100_sequencial
					where fis_procfiscal.y100_sequencial = $chavepesquisa";
	 $result = pg_query($sql);
	 
   db_fieldsmemory($result,0);
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
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php 
	//include(modification("forms/db_frm_fis_procfiscal.php"));
	include(modification("forms/db_frm_fis_procfiscal.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($alterar)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clprocfiscal->erro_campo!=""){
      echo "<script> document.form1.".$clprocfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clprocfiscal->erro_campo.".focus();</script>";
    };
  }else{
	db_msgbox($erro_msg);
	echo "<script>location.href='fis1_fis_procfiscal005.php?chavepesquisa=".$y100_sequencial."';</script>";
   	echo "<script>
   			var iframe = (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_procfiscalfiscais;
			iframe.src = iframe.src;
			console.log(iframe);
		</script>";
  }
}
if(isset($chavepesquisa)){
 echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.procfiscalfiscais.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_procfiscalfiscais.location.href='fis1_fis_procfiscalfiscais001.php?y106_procfiscal=".@$y100_sequencial."';
         parent.document.formaba.processosfiscais.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_processosfiscais.location.href='fis1_fis_processosfiscais001.php?y106_procfiscal=".@$y100_sequencial."';
     ";
         if(isset($liberaaba)){
           echo "  parent.mo_camada('procfiscalfiscais');";
         }
 echo"}\n
    js_db_libera();
  </script>\n
 ";
}
 if($db_opcao==22||$db_opcao==33){
    echo "<script>document.form1.pesquisar.click();</script>";
 }
?>
