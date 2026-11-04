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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);



require(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_tipoandam_classe.php"));
require_once(modification('classes/db_fis_parametrosandamento_classe.php'));
require_once(modification('classes/db_fis_usuariosparametrosandamento_classe.php'));
require_once(modification('classes/db_fis_deptosparametrosandamento_classe.php'));
require_once(modification("classes/db_fis_grupotipoandamento_tipoandam_classe.php"));
require_once(modification("classes/db_fis_tipoandam_datafim_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$cltipoandam = new cl_fis_tipoandam;
$clparametrosandamento = new cl_fis_parametrosandamento;
$clgrupotipoandamento_tipoandam = new cl_fis_grupotipoandamento_tipoandam;
$cltipoandam_datafim            = new cl_fis_tipoandam_datafim;
$db_opcao = 1;
$db_botao = true;
$sqlerro = false;

if(!isset($abas)){
    echo "<script>location.href='fis1_fis_tipoandam004.php?db_opcao=1'</script>";
    exit;
}

if(isset($incluir)){
  db_inicio_transacao();
	
  $cltipoandam->y41_instit = db_getsession('DB_instit');
  $cltipoandam->incluir($y41_codtipo);
  $erro_msg = $cltipoandam->erro_msg;
  
  if ($cltipoandam->erro_status == '0'){ 
    $sqlerro = true;
    $erro_msg = $cltipoandam->erro_msg;
  }

  if(!$sqlerro){
    $clparametrosandamento->tem_data_ciencia = $tem_data_ciencia;
    $clparametrosandamento->tipoandam = $cltipoandam->y41_codtipo;
    $clparametrosandamento->tem_data_recurso = $tem_data_recurso;
    $clparametrosandamento->incluir();
    $erro_msg = $clparametrosandamento->erro_msg;
    
    if ($clparametrosandamento->erro_status == '0') {
      $sqlerro = true;  
      $erro_msg = $clparametrosandamento->erro_msg;
    }
  }

  if(!$sqlerro){
    $clgrupotipoandamento_tipoandam->fi30_grupo     = $grupo;
    $clgrupotipoandamento_tipoandam->fi30_tipoandam = $cltipoandam->y41_codtipo;
    $clgrupotipoandamento_tipoandam->incluir();
    $erro_msg = $clgrupotipoandamento_tipoandam->erro_msg;

    if ($clgrupotipoandamento_tipoandam->erro_status == '0') {
      $sqlErro = true;  
      $erro_msg = $clgrupotipoandamento_tipoandam->erro_msg;
    }
  }

  if(!$sqlerro){
    if($fi31_data != ""){
      $cltipoandam_datafim->fi31_data      = $fi31_data;
      $cltipoandam_datafim->fi31_tipoandam = $cltipoandam->y41_codtipo;
      $cltipoandam_datafim->incluir();
      $erro_msg = $cltipoandam_datafim->erro_msg;

      if ($cltipoandam_datafim->erro_status == '0') {
        $sqlErro = true;  
        $erro_msg = $cltipoandam_datafim->erro_msg;
      }
    }
  }

  db_fim_transacao($sqlerro);
} // fim incluir
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC onLoad="a=1" >
<?php 
 require_once(modification("forms/db_frm_fis_tipoandam.php"));
?>
</body>
</html>
<?php 
if(isset($incluir)){
  if($sqlerro){
    // $cltipoandam->erro(true,false);
    db_msgbox($erro_msg);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    // if($cltipoandam->erro_campo!=""){
    //   echo "<script> document.form1.".$cltipoandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
    //   echo "<script> document.form1.".$cltipoandam->erro_campo.".focus();</script>";
    // }
  }else{
    // $cltipoandam->erro(true,true);
    echo "<script>alert(\"".$erro_msg."\");</script>";
    echo "<script>location.href='fis1_fis_tipoandam002.php?abas=1&chavepesquisa=$cltipoandam->y41_codtipo'</script>";
  }
}
?>
