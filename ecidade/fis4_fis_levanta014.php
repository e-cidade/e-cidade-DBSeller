<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
include(modification("classes/db_fis_levanta_classe.php"));
include(modification("classes/db_fis_levinscr_classe.php"));
include(modification("classes/db_fis_levcgm_classe.php"));
include(modification("classes/db_fis_procfiscallevanta_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_levusu_classe.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$cllevanta = new cl_fis_levanta;
$cllevinscr= new cl_fis_levinscr;
$cllevcgm  = new cl_fis_levcgm;
$clprocfiscallevanta = new cl_fis_procfiscallevanta;
$db_botao = true;
$db_opcao = 1;

if(isset($procfiscal) && $procfiscal != ''){
  $sqlProc = "select 
                p58_numero || '/' || p58_ano as p58_numero, 
                p58_codproc as y60_proces, 
                p58_requer
              from fiscalizacao.fis_procfiscal 
                inner join fiscalizacao.fis_procfiscalprot on y100_sequencial = y105_procfiscal 
                inner join protprocesso on y105_protprocesso = p58_codproc 
              where y100_sequencial = ".$procfiscal;
  $rsProc = db_query($sqlProc);
  if(pg_num_rows($rsProc) > 0){
    db_fieldsmemory($rsProc, 0);
  }

    $sTipoProc  = " select y103_inscr as q02_inscr, y102_matric as j01_matric, y104_codsani as y80_codsani, y101_numcgm as z01_numcgm  from fiscalizacao.fis_procfiscal ";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalsani   on y104_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalcgm    on y101_procfiscal = y100_sequencial";
    $sTipoProc .= "     where y100_sequencial = ".$procfiscal;
    $rsTipoProc = db_query($sTipoProc);
    db_fieldsmemory($rsTipoProc,0);
    if($q02_inscr != ""){
        unset($z01_numcgm );
    }elseif($j01_matric != ""){
        unset($z01_numcgm );
    }elseif($y104_codsani != ""){
        unset($z01_numcgm );
    }
}
if(isset($incluir)){
  $sqlerro=false;
  db_inicio_transacao();
  $db_opcao = 1;
  $cllevanta->y60_espontaneo=$y60_espontaneo;
  $cllevanta->incluir($y60_codlev);
  $y60_codlev=$cllevanta->y60_codlev;
  if($cllevanta->erro_status==0){
    $sqlerro=true;
  }
	if($sqlerro==false){
		if($procfiscal!=""){

			$clprocfiscallevanta->y112_procfiscal = $procfiscal;
			$clprocfiscallevanta->y112_levanta    = $y60_codlev;
			$clprocfiscallevanta->incluir(null);
			if($clprocfiscallevanta->erro_status==0){
				$erro=$clprocfiscallevanta->erro_msg;
			  $sqlerro = true;
			}

      // Traz os fiscais vinculados ao processo fiscal
/*      $sQueryFiscais = " select db_usuarios.id_usuario from db_usuarios ";
      $sQueryFiscais .= " inner join fiscalizacao.fis_cadfiscais on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
      $sQueryFiscais .= " inner join fiscalizacao.fis_procfiscalfiscais on fis_cadfiscais.id_usuario = y106_cadfiscais ";
      $sQueryFiscais .= " inner join fiscalizacao.fis_procfiscal on y106_procfiscal = y100_sequencial ";
      $sQueryFiscais .= " inner join fiscalizacao.fis_processofiscalativo on y100_sequencial = processo_fiscal ";
      $sQueryFiscais .= " and fis_cadfiscais.id_usuario = fiscal ";
      $sQueryFiscais .= " where y100_sequencial = $procfiscal ";

      $rsResultFiscais = pg_query($sQueryFiscais);
      $iCountFiscais = pg_num_rows($rsResultFiscais);

      // Se houver fiscais...
      if ($iCountFiscais > 0) {
        for ($i = 0; $i < $iCountFiscais; $i++) {
          db_fieldsmemory($rsResultFiscais, $i);
          // Relaciona os fiscais com o levantamento na tabela levusu*/
          $cllevusu = new cl_fis_levusu;
          $cllevusu->incluir($y60_codlev,db_getsession('DB_id_usuario'));
          if($cllevusu->erro_status == '0'){
            $erro=$cllevusu->erro_msg;
            $sqlerro=true;
          }
        //}
     // }
		}
	}
  if(!$sqlerro){
    if($tipo=='z01_numcgm'){
      $cllevcgm->y93_numcgm=$valor;
      $cllevcgm->y93_codlev=$y60_codlev;
      $cllevcgm->incluir($y60_codlev,$valor);
      if($cllevcgm->erro_status==0){
         $sqlerro=true;
      }
    }else if($tipo=='q02_inscr'){
      $cllevinscr->y62_inscr=$valor;
      $cllevinscr->y62_codlev=$y60_codlev;
      $cllevinscr->incluir($y60_codlev,$valor);
      if($cllevinscr->erro_status==0){
         $sqlerro=true;
      }
    }
  }
  db_fim_transacao($sqlerro);
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
	include(modification("forms/db_frm_fis_levanta.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($incluir)){
  if($cllevanta->erro_status=="0"){
    $cllevanta->erro(true,false);
  }else{
    $cllevanta->erro(true,false);
    db_redireciona("fis4_fis_levanta015.php?chavepesquisa=$y60_codlev");
  }
}
?>
