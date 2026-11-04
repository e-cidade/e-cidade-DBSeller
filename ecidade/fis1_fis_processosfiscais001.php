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
include(modification("classes/db_fis_procfiscalfiscais_classe.php"));
include(modification("classes/db_fis_procfiscal_classe.php"));
include(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_autousu_classe.php"));
require_once(modification("classes/db_fis_fiscalusuario_classe.php"));
require_once(modification("classes/db_fis_processosfiscais_classe.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
$clprocfiscal = new cl_fis_procfiscal;
$clautousu   = new cl_fis_autousu;
$clfiscalusuario   = new cl_fis_fiscalusuario;
//$clprocessofiscalativo = new cl_fis_processofiscalativo;
$clprocessosfiscais = new cl_fis_processosfiscais;
$db_opcao = 22;
$db_botao = false;
if(isset($alterar) || isset($excluir) || isset($incluir)){
  $sqlerro = false;
}
if(isset($incluir)){
  if($sqlerro==false){
    db_inicio_transacao();

    $result = $clprocessosfiscais->sql_record($clprocessosfiscais->sql_query_file($y106_procfiscal,$y60_proces));
    if($result!=false && $clprocessosfiscais->numrows>0){
      $sqlerro  = true;
      $erro_msg = "Processo Administrativo Já Cadastrado para esse Processo Fiscal";
    }
    if($sqlerro == false){

      $clprocessosfiscais->pf01_procfiscal  = $y106_procfiscal;
      $clprocessosfiscais->pf01_processo    = $y60_proces;
      $clprocessosfiscais->incluir();
      $erro_msg = $clprocessosfiscais->erro_msg;
      if($clprocessosfiscais->erro_status == '0'){
        $sqlerro = true;
      }
    }
    db_fim_transacao($sqlerro);
  }
}else if(isset($alterar)){
  if($sqlerro==false){
    db_inicio_transacao();
    $result = $clprocessosfiscais->sql_record($clprocessosfiscais->sql_query_file($y106_procfiscal,$y60_proces));
    if($result!=false && $clprocessosfiscais->numrows>0){
      $sqlerro  = true;
      $erro_msg = "Processo Administrativo Já Cadastrado para esse Processo Fiscal";
    }
    $clprocessosfiscais->pf01_procfiscal  = $y106_procfiscal;
    $clprocessosfiscais->pf01_processo    = $y60_proces;
    $clprocessosfiscais->alterar($pf01_codigo);
    $erro_msg = $clprocessosfiscais->erro_msg;
    if($clprocessosfiscais->erro_status == '0'){
      $sqlerro = true;
    }

    db_fim_transacao($sqlerro);
  }
}else if(isset($excluir)){
  if($sqlerro==false){
    db_inicio_transacao();
    $clprocessosfiscais->excluir($pf01_codigo);
    $erro_msg = $clprocessosfiscais->erro_msg;
    if($clprocessosfiscais->erro_status==0){
      $sqlerro=true;
    }

    db_fim_transacao($sqlerro);
  }
}else if(isset($opcao)){
   $campos = "pf01_codigo,pf01_procfiscal as y106_procfiscal, (select p58_numero || '/' || p58_ano as p58_numero from protprocesso where pf01_processo = p58_codproc) as p58_numero";
   $result = $clprocessosfiscais->sql_record($clprocessosfiscais->sql_query_file(null,null,$campos,null,"pf01_codigo = ".$y106_sequencial));
   if($result!=false && $clprocessosfiscais->numrows>0){
     db_fieldsmemory($result,0);
   }
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
  
  include(modification("forms/db_frm_fis_processosfiscais.php"));

  ?>
    </center>
  </td>
  </tr>
</table>
</body>
</html>

<?php 
if(isset($alterar) || isset($excluir) || isset($incluir)){
    db_msgbox($erro_msg);
    
    
}
?>
