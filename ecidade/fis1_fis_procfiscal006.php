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
require_once(modification("classes/db_fis_procfiscal_classe.php"));
require_once(modification("classes/db_fis_procfiscalinscr_classe.php"));
require_once(modification("classes/db_fis_procfiscalmatric_classe.php"));
require_once(modification("classes/db_fis_procfiscalsani_classe.php"));
require_once(modification("classes/db_fis_procfiscalcgm_classe.php"));
require_once(modification("classes/db_fis_procfiscalprot_classe.php"));
require_once(modification("classes/db_fis_procfiscalfiscais_classe.php"));
require_once(modification("classes/db_fis_dataprocfiscal_classe.php"));

$clprocfiscal       = new cl_fis_procfiscal;
$clprocfiscalinscr  = new cl_fis_procfiscalinscr;
$clprocfiscalmatric = new cl_fis_procfiscalmatric;
$clprocfiscalsani   = new cl_fis_procfiscalsani;
$clprocfiscalcgm    = new cl_fis_procfiscalcgm;
$clprocfiscalprot   = new cl_fis_procfiscalprot;
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
$cldataprocfiscal   = new cl_fis_dataprocfiscal;
  
db_postmemory($HTTP_POST_VARS);
$db_opcao = 33;
$db_botao = false;
if(isset($excluir)){
  $sqlerro = false;
  db_inicio_transacao();
  
  $sConsultaAuto = db_query("select * from fiscalizacao.fis_procfiscalauto where y111_procfiscal = $y100_sequencial");
  if( pg_num_rows($sConsultaAuto) > 0 ){
    $sqlerro = true;
    $erro_msg = "Existem peças vinculadas a este processo fiscal!\nExclusão não permitida.";
  }

  if($sqlerro == false){
    $sConsultaFases = db_query("select * from fiscalizacao.fis_procfiscalfases where y108_procfiscal = $y100_sequencial");
    if( pg_num_rows($sConsultaFases) > 0 ){
      $sqlerro = true;
      $erro_msg = "Existem peças vinculadas a este processo fiscal!\nExclusão não permitida.";
    }
  }

  if($sqlerro == false){
    $sConsultaLev = db_query("select * from fiscalizacao.fis_procfiscallevanta where y112_procfiscal = $y100_sequencial");
    if( pg_num_rows($sConsultaLev) > 0 ){
      $sqlerro = true;
      $erro_msg = "Existem peças vinculadas a este processo fiscal!\nExclusão não permitida.";
    }
  }

  if($sqlerro == false){
    $sConsultaNot = db_query("select * from fiscalizacao.fis_procfiscalnotificacao where y110_procfiscal = $y100_sequencial");
    if( pg_num_rows($sConsultaNot) > 0 ){
      $sqlerro = true;
      $erro_msg = "Existem peças vinculadas a este processo fiscal!\nExclusão não permitida.";
    }
  }

  if($sqlerro == false){
    $sConsultaVis = db_query("select * from fiscalizacao.fis_procfiscalvistorias where y109_procfiscal = $y100_sequencial");
    if( pg_num_rows($sConsultaVis) > 0 ){
      $sqlerro = true;
      $erro_msg = "Existem peças vinculadas a este processo fiscal!\nExclusão não permitida.";
    }
  }

  if( $sqlerro == false ){
    if( isset($q02_inscr) and $q02_inscr != "" ){
      //----[Quando for Inscriçao]=====//
      $clprocfiscalinscr->excluir(null,"y103_procfiscal = $y100_sequencial");
      if( $clprocfiscalinscr->erro_status == 0 ){
        $sqlerro  = true;
        $erro_msg = $clprocfiscalinscr->erro_msg; 
      } 
    }
  }

  if( $sqlerro == false ){
    // se tiver matricula
    if( isset($j01_matric) and $j01_matric != "" ){
      $clprocfiscalmatric->excluir(null,"y102_procfiscal = $y100_sequencial");
      if( $clprocfiscalmatric->erro_status == 0 ){
        $sqlerro=true;
        $erro_msg = $clprocfiscalmatric->erro_msg; 
      } 
    }
  }

  if( $sqlerro == false ){
    // se tiver sanitario
    if( isset($y80_codsani) and $y80_codsani != "" ){
      $clprocfiscalsani->excluir(null,"y104_procfiscal = $y100_sequencial");
      if($clprocfiscalsani ->erro_status==0){
        $sqlerro=true;
        $erro_msg = $clprocfiscalsani->erro_msg; 
      } 
    }
  }

  if( $sqlerro==false ){
    // cgm
    if( $z01_numcgm == "" ){
      $sqlerro=true;
      $erro_msg = "Campo cgm não informado!";
    }else{
      $clprocfiscalcgm->excluir(null,"y101_procfiscal = $y100_sequencial");
      if($clprocfiscalcgm->erro_status==0){
        $sqlerro  = true;
        $erro_msg = $clprocfiscalcgm->erro_msg; 
      } 
    }
  } 

  if($sqlerro==false){
    // processo protocolo
    $clprocfiscalprot->y105_procfiscal   = $y100_sequencial;
    $clprocfiscalprot->y105_protprocesso = $p58_codproc;
    $clprocfiscalprot->excluir(null,"y105_procfiscal = $y100_sequencial");
    if( $clprocfiscalprot->erro_status == 0 ){
      $sqlerro=true;
      $erro_msg = $clprocfiscalprot->erro_msg; 
    } 
  }

  if( $sqlerro == false ){
    db_query("delete from fiscalizacao.fis_processofiscalativo where processo_fiscal = $y100_sequencial");
    db_query("delete from fiscalizacao.fis_processoprorrogacaofinalizacao where processo_fiscal = $y100_sequencial");
  }

  if($sqlerro == false){
    $clprocfiscalfiscais->excluir(null, "y106_procfiscal = $y100_sequencial");

    if($clprocfiscalfiscais->erro_status==0){
      $sqlerro=true;
    } 
  }
  if ( $sqlerro == false ){
    $erro_msg = $clprocfiscalfiscais->erro_msg; 
    $clprocfiscal->excluir($y100_sequencial);
    if($clprocfiscal->erro_status==0){
      $sqlerro=true;
    }
    $cldataprocfiscal->excluir($y100_sequencial); 
    $erro_msg = $clprocfiscal->erro_msg; 
  }
  db_fim_transacao($sqlerro);
   $db_opcao = 3;
   $db_botao = true;
}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $db_botao = true;
   $sql ="
          select fis_procfiscal.*,y103_inscr as q02_inscr,y102_matric as j01_matric,
                 y105_protprocesso as p58_codproc,y104_codsani,
                 y33_descricao,cgm.z01_nome,cgm.z01_numcgm,c.z01_nome as z01_nome1, p58_numero||'/'||p58_ano as p58_numero,
                 descrdepto,
                 y106_sequencial, y106_cadfiscais, nome
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
          where fis_procfiscal.y100_sequencial = $chavepesquisa";
   $result = $clprocfiscal->sql_record($sql); 
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
	include(modification("forms/db_frm_fis_procfiscal.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($excluir)){
  if($sqlerro==true){
    db_msgbox($erro_msg);
    if($clprocfiscal->erro_campo!=""){
      echo "<script> document.form1.".$clprocfiscal->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clprocfiscal->erro_campo.".focus();</script>";
    };
  }else{
   db_msgbox($erro_msg);
 echo "
  <script>
    function js_db_tranca(){
      parent.location.href='fis1_fis_procfiscal003.php';
    }\n
    js_db_tranca();
  </script>\n
 ";
  }
}
if(isset($chavepesquisa)){
 echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.procfiscalfiscais.disabled=false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_procfiscalfiscais.location.href='fis1_fis_procfiscalfiscais001.php?db_opcaoal=33&y106_sequencial=".@$y100_sequencial."';
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
