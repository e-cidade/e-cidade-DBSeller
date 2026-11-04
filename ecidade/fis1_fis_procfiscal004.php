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
require_once(modification("classes/db_fis_dataprocfiscal_classe.php"));
$clprocfiscal       = new cl_fis_procfiscal_estendida;
$clprocfiscalinscr  = new cl_fis_procfiscalinscr;
$clprocfiscalmatric = new cl_fis_procfiscalmatric;
$clprocfiscalsani   = new cl_fis_procfiscalsani;
$clprocfiscalcgm    = new cl_fis_procfiscalcgm;
$clprocfiscalprot   = new cl_fis_procfiscalprot;
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
$cldataprocfiscal   = new cl_fis_dataprocfiscal;
db_postmemory($HTTP_POST_VARS);
$db_opcao = 1;
$db_botao = true;
include(modification("classes/db_protprocesso_classe.php"));
$clprotprocesso = new cl_protprocesso;
if (isset($incluir)) {
	$sqlerro=false;
  	$cgm = true;
	db_inicio_transacao();
	if(isset($q02_inscr) and $q02_inscr!=""){
     		$cgm = false;
			$tWhere = " y103_inscr = ".$q02_inscr." and y100_instit = ".db_getsession('DB_instit')." and y100_coddepto = ".db_getsession("DB_coddepto");
			$result = db_query($clprocfiscalinscr->sql_query(null,"y103_procfiscal as procfiscal",null,$tWhere));
			if( pg_num_rows($result) > 0 ){
		          for($i=0; $i < pg_num_rows($result);$i++){

			  db_fieldsmemory($result,$i);
			  $result2 = db_query("select processo_fiscal from fiscalizacao.fis_processoprorrogacaofinalizacao where situacao = 2 and processo_fiscal = $procfiscal");
			 if(pg_num_rows($result2) == 0){		
			  $erro_msg = "Existe processo fiscal não finalizado para essa inscrição.\nProcesso Fiscal:$procfiscal ";
			  $sqlerro = true;
			  break;
			}
				}
			}
	}elseif (isset($j01_matric) and $j01_matric!="") {
			$tWhere = " y102_matric = ".$j01_matric." and y100_instit = ".db_getsession('DB_instit')." and y100_coddepto = ".db_getsession("DB_coddepto");
                                                                                                                                
                        $result = db_query($clprocfiscalmatric->sql_query(null,"y102_procfiscal as procfiscal",null,$tWhere));
                        if( pg_num_rows($result) > 0 ){
                          for($i=0; $i < pg_num_rows($result);$i++){

                          db_fieldsmemory($result,$i);                                                                          
                          $result2 = db_query("select processo_fiscal from fiscalizacao.fis_processoprorrogacaofinalizacao where situacao
 = 2 and processo_fiscal = $procfiscal");
                         if(pg_num_rows($result2) == 0){
                          $erro_msg = "Existe processo fiscal não finalizado para essa Matrícula.\nProcesso Fiscal:$procfiscal "
;                                                                                                                               
                          $sqlerro = true;
                          break;
                        }
                                }
                        }
	}elseif (isset($z01_numcgm) && $z01_numcgm != "") {
		$tWhere = " y101_numcgm = ".$z01_numcgm." and y100_instit = ".db_getsession('DB_instit')." and y100_coddepto = ".db_getsession("DB_coddepto");
                                                                                                                                
                        $result = db_query($clprocfiscalcgm->sql_query(null,"y101_procfiscal as procfiscal",null,$tWhere)
);
                        if( pg_num_rows($result) > 0 && $cgm == true ){
                          for($i=0; $i < pg_num_rows($result);$i++){

                          db_fieldsmemory($result,$i);                                                                          
                          $result2 = db_query("select processo_fiscal from fiscalizacao.fis_processoprorrogacaofinalizacao where situacao
 = 2 and processo_fiscal = $procfiscal");
                         if(pg_num_rows($result2) == 0){
                          $erro_msg = "Existe processo fiscal não finalizado para essa CGM.\nProcesso Fiscal:$procfiscal "
;                                                                                                                               
                          $sqlerro = true;
                          break;
                        }
                                }
                        }
	}
	if($sqlerro == false){

	$clprocfiscal->y100_coddepto = db_getsession("DB_coddepto");
	$clprocfiscal->incluir(null);
	if($clprocfiscal ->erro_status==0){
		$sqlerro=true;
		$erro_msg = $clprocfiscal->erro_msg; 
	}
	}
	if($sqlerro==false){
		$y100_sequencial = $clprocfiscal->y100_sequencial;
	}
	if($sqlerro==false){
	
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
	}

	if($sqlerro==false){
		// se tiver matricula
		if(isset($j01_matric) and $j01_matric!=""){
			
			$clprocfiscalmatric->y102_matric     = $j01_matric;
			$clprocfiscalmatric->y102_procfiscal = $y100_sequencial;
			$clprocfiscalmatric->incluir(null);
			if($clprocfiscalmatric ->erro_status==0){
				$sqlerro=true;
				$erro_msg = $clprocfiscalmatric->erro_msg; 
			} 
		}
	}

	if($sqlerro==false){
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
	}

	if($sqlerro==false){
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
*/      $where .= " and p58_codproc not in (SELECT p67_codproc FROM procarquiv INNER JOIN arqandam ON p69_codarquiv = p67_codarquiv and p69_codandam = p58_codandam WHERE p69_arquivado = 't')";	
	$aPartesNumero = explode("/", $p58_numero);
   	$iAno = db_getsession("DB_anousu");
   	if (count($aPartesNumero) > 1 && !empty($aPartesNumero[1])) {
      $iAno = $aPartesNumero[1];
   	}
   	$iNumero = $aPartesNumero[0];
   	$where  .= " and p58_ano = {$iAno} and p58_numero = '{$iNumero}'";
   	if($sqlerro == false){
	$sql     = $clprotprocesso->sqlGetProcessoAdministrativo("p58_codproc", $where,"p58_codproc desc");
   	$rsProt = db_query($sql);
   	if(pg_num_rows($rsProt) == 0){
   		$sqlerro = true;
   		$erro_msg = "Processo Invalido, Verifique o Processo Digitado!";
   	}else{
		db_fieldsmemory($rsProt,0);	
	}
	}
	if($sqlerro==false){
		// processo protocolo
		if($p58_codproc==""){
			$sqlerro=true;
			$erro_msg = "Campo processo não informado!";
		}else{
			$clprocfiscalprot->y105_procfiscal   = $y100_sequencial;
			$clprocfiscalprot->y105_protprocesso = $p58_codproc;
			$clprocfiscalprot->incluir(null);
			if($clprocfiscalprot ->erro_status==0){
				$sqlerro=true;
				$erro_msg = $clprocfiscalprot->erro_msg; 
			} 
		}
	}
	if( $sqlerro == false ){
	  $cldataprocfiscal->ypl01_procfiscal = $y100_sequencial;
	  $cldataprocfiscal->incluir();
	  if( $cldataprocfiscal->erro_status == 0 ){
	      $sqlerro  = true;
	  }
	}
	if($sqlerro == true){
		unset($y100_sequencial);
	}

	db_fim_transacao($sqlerro);

	$db_opcao = 1;
	$db_botao = true;
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
<table align="center" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	     <?php 
    	   include(modification("forms/db_frm_fis_procfiscal.php"));
    	 ?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($incluir)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    // if($clprocfiscal->erro_campo!=""){
    //   echo "<script> document.form1.".$clprocfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
    //   echo "<script> document.form1.".$clprocfiscal->erro_campo.".focus();</script>";
    // };
  }else{
   db_msgbox("Inclusão efetuada com sucesso!");
   db_redireciona("fis1_fis_procfiscal005.php?liberaaba=true&chavepesquisa=$y100_sequencial");
  }
}
?>
