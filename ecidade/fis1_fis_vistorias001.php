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
include(modification("classes/db_fis_vistorias_classe.php"));
include(modification("classes/db_fis_tipovistorias_classe.php"));
include(modification("classes/db_fis_vistinscr_classe.php"));
include(modification("classes/db_fis_vistmatric_classe.php"));
include(modification("classes/db_fis_vistsanitario_classe.php"));
include(modification("classes/db_fis_vistcgm_classe.php"));
include(modification("classes/db_fis_vistlocal_classe.php"));
include(modification("classes/db_fis_vistexec_classe.php"));
include(modification("classes/db_fis_vistoriaandam_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_procfiscalvistorias_classe.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
// var_dump($HTTP_SERVER_VARS['QUERY_STRING']); exit();
if(isset($cgm)){
  $junto = "cgm=1";
}elseif(isset($matric)){
  $junto = "matric=1";
}elseif(isset($inscr)){
  $junto = "inscr=1";
}elseif(isset($sani)){
  $junto = "sani=1";
}elseif(isset($proc)){
  $junto = "proc=1";
}
if(!isset($abas)){
  echo "<script>location.href='fis1_fis_vistorias005.php?$junto'</script>";
  exit;
}

db_postmemory($HTTP_POST_VARS);


$clvistorias     = new cl_fis_vistorias;
$cltipovistorias = new cl_fis_tipovistorias;
$clvistinscr     = new cl_fis_vistinscr;
$clvistmatric    = new cl_fis_vistmatric;
$clvistcgm       = new cl_fis_vistcgm;
$clvistlocal     = new cl_fis_vistlocal;
$clvistexec      = new cl_fis_vistexec;
$clvistsanitario = new cl_fis_vistsanitario;
$clvistoriaandam = new cl_fis_vistoriaandam;
$clfandam        = new cl_fis_fandam;
$clprocfiscalvistorias = new cl_fis_procfiscalvistorias;
$db_opcao = 1;
$db_botao = true;
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
  db_inicio_transacao();
  $sqlerro = false;
  $result = $cltipovistorias->sql_record($cltipovistorias->sql_query("","*",""," y77_codtipo = $y70_tipovist and y77_instit = ".db_getsession('DB_instit') ));
  if($cltipovistorias->numrows > 0){
    db_fieldsmemory($result,0);
  }
  if ($sqlerro==false){

  $clfandam->y39_data=date("Y-m-d",db_getsession("DB_datausu"));
  $clfandam->y39_codtipo=@$y77_tipoandam;
  $clfandam->y39_obs=($y70_obs == ""?"0":$y70_obs);
  $clfandam->y39_id_usuario=$y70_id_usuario;
  $clfandam->y39_hora=db_hora();
  $clfandam->incluir("");
  $erro=$clfandam->erro_msg;
  if($clfandam->erro_status==0){
    $sqlerro = true;

  }
  }
  if ($sqlerro==false){
  $clvistorias->y70_ativo = 't'; 
  $clvistorias->y70_parcial = 't';
  $clvistorias->y70_ultandam=$clfandam->y39_codandam;
  $clvistorias->y70_coddepto=db_getsession("DB_coddepto");
  $clvistorias->y70_instit  =db_getsession('DB_instit');
  $clvistorias->incluir($y70_codvist);
  $y70_codvist = $clvistorias->y70_codvist;
  $erro=$clvistorias->erro_msg;
  if($clvistorias->erro_status==0){
    $sqlerro = true;
  }
  if ($sqlerro==false){
    if($procfiscal!=""){
      $clprocfiscalvistorias->y109_codvist    = $clvistorias->y70_codvist;
      $clprocfiscalvistorias->y109_procfiscal = $procfiscal;
      $clprocfiscalvistorias->incluir(null);
      if($clprocfiscalvistorias->erro_status==0){
        $erro=$clprocfiscalvistorias->erro_msg;
        $sqlerro = true;
      }
    }
  }
  
  if ($sqlerro==false && $y10_codigo != "" ){
  $clvistlocal->incluir($y70_codvist);
  $erro=$clvistlocal->erro_msg;
  if($clvistlocal->erro_status==0){
    $sqlerro = true;
  }
  }else{
      $clenderecopecas = db_utils::getDao("fis_enderecopecas");
      $clenderecopecas->end01_codpeca  = $y70_codvist;
      $clenderecopecas->end01_tipopeca = 'V';
      $clenderecopecas->end01_rua      = $j14_nome;
      $clenderecopecas->end01_numero   = $y10_numero;
      $clenderecopecas->end01_compl    = $y10_compl;
      $clenderecopecas->end01_bairro   = $j13_descr;

      $clenderecopecas->incluir();

      if( $clenderecopecas->erro_status == "0" ){
        $sqlerro = true;
        $erro    = $clenderecopecas->erro_msg;
      }
  }
  if ($sqlerro==false){
  $clvistexec->incluir($y70_codvist);
  $erro=$clvistexec->erro_msg;
  if($clvistexec->erro_status==0){
    $sqlerro = true;
  }
  }
  if ($sqlerro==false){
    $clvistoriaandam->incluir($y70_codvist,$clfandam->y39_codandam);
    $erro=$clvistoriaandam->erro_msg;
    if($clvistoriaandam->erro_status==0){
      $sqlerro = true;
    }
    
  }
  }
  if ($sqlerro==false){
  if(isset($z01_numcgm) && $z01_numcgm != ""){
    $clvistcgm->y73_numcgm=$z01_numcgm; 
    $clvistcgm->incluir($y70_codvist); 
    $erro=$clvistcgm->erro_msg;
    if($clvistcgm->erro_status==0){
      $sqlerro = true;
    }
  
  }elseif(isset($j01_matric) && $j01_matric != ""){

    $clvistmatric->y72_matric=$j01_matric; 
    $clvistmatric->incluir($y70_codvist); 
    $erro=$clvistmatric->erro_msg;
    if($clvistmatric->erro_status==0){
      $sqlerro = true;
    }
  }elseif(isset($q02_inscr)  && $q02_inscr  != ""){
    $clvistinscr->y71_inscr=$q02_inscr; 
    $clvistinscr->incluir($y70_codvist); 
    $erro=$clvistinscr->erro_msg;
    if($clvistinscr->erro_status==0){
      $sqlerro = true;
    }
  }elseif(isset($y80_codsani)  && $y80_codsani  != ""){ 
    $clvistsanitario->y74_codsani=$y80_codsani; 
    $clvistsanitario->incluir($y70_codvist); 
    $erro=$clvistsanitario->erro_msg;
    if($clvistsanitario->erro_status==0){
      $sqlerro = true;

    }
  }
  }
  db_fim_transacao();
}

if(!isset($pri)){
  include(modification("fis1_fis_vistorias004.php"));
  exit;
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
  include(modification("forms/db_frm_fis_vistorias.php"));

  ?>
    </center>
  </td>
  </tr>
</table>
</body>
</html>
<script>
js_setatabulacao();
</script>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
  if($clvistorias->erro_status=="0"){
    
    $clvistorias->erro(true,false);
    $db_botao=true;
    if($clvistorias->erro_campo!=""){ 
     
      echo "<script> document.form1.".$clvistorias->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clvistorias->erro_campo.".focus();</script>";
    };
  }else{
    if($sqlerro == true){
   
      db_msgbox($erro);
    }else{
     
      $clvistorias->erro(true,false);      
      echo "<script>parent.iframe_fiscais.location.href='fis1_fis_vistusuario001.php?y75_codvist=$y70_codvist&y39_codandam=".$clfandam->y39_codandam."&".$junto."';</script>";
      echo "<script>parent.iframe_testem.location.href='fis1_fis_vistestem001.php?y25_codvist=$y70_codvist&y39_codandam=".$clfandam->y39_codandam."&".$junto."';</script>";
      echo "<script>parent.mo_camada('fiscais');</script>";
      echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
      echo "<script>parent.iframe_vistorias.location.href='fis1_fis_vistorias002.php?abas=1&chavepesquisa=".$y70_codvist."&y39_codandam=".$clfandam->y39_codandam."&".$junto."';</script>";
    }
  };
};
?>