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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis3_fis_fandam005.php?db_opcao=2'</script>";
  exit;
}
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_vistorias_classe.php"));
include(modification("classes/db_fis_vistusuario_classe.php"));
include(modification("classes/db_fis_vistoriaandam_classe.php"));
include(modification("classes/db_fis_tipovistorias_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clvistorias     = new cl_fis_vistorias;
$clvistusuario   = new cl_fis_vistusuario;$cldatacienciaandamento = new cl_fis_datacienciaandamento;
$clvistoriaandam = new cl_fis_vistoriaandam;
$cltipovistorias = new cl_fis_tipovistorias;
$clfandamusu     = new cl_fis_fandamusu;
$clfandam        = new cl_fis_fandam;
$clrotulo        = new rotulocampo;
$clrotulo->label("y39_codandam");
$db_opcao = 22;
$db_botao = false;
echo "<script>parent.document.formaba.fiscais.disabled=true;</script>";
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  db_inicio_transacao();
  $db_opcao = 2;
  $sqlerro=false;
  $clfandam->alterar($y39_codandam);
  $erro=$clfandam->erro_msg;
  if($clfandam->erro_status==0){
    $sqlerro = true;
  }
  if ($sqlerro == false){
    if(isset($sequencial) and $sequencial != '') {
    $cldatacienciaandamento->codigo_peca = $clvistorias->y70_codvist;
    $cldatacienciaandamento->data_ciencia = $data_ciencia;
    $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
    $cldatacienciaandamento->alterar($sequencial);
    } else {
    $cldatacienciaandamento->codigo_peca = $clvistorias->y70_codvist;
    $cldatacienciaandamento->data_ciencia = $data_ciencia;
    $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
    $cldatacienciaandamento->incluir();
    }
  }
  if ($cldatacienciaandamento->erro_status == '0') {
    $sqlerro = true;
  }db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $clfandam->sql_record($clfandam->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $result = $clvistoriaandam->sql_record($clvistoriaandam->sql_query_file("","","*",""," fis_vistoriaandam.y68_codandam = $chavepesquisa and y70_instit = ".db_getsession('DB_instit') )); 
   if($clvistoriaandam->numrows > 0){
     db_fieldsmemory($result,0);
     $y70_ultandam = $y68_codandam;
   }
   $db_botao = false;
   $result = $clvistorias->sql_record($clvistorias->sql_query(@$y68_codvist,"fis_vistorias.*,fis_tipovistorias.y77_descricao",null," y70_instit = ".db_getsession('DB_instit') )); 
   if($clvistorias->numrows > 0){
     db_fieldsmemory($result,0);
     $result = $clvistusuario->sql_record($clvistusuario->sql_query($y70_codvist)); 
     if($clvistusuario->numrows == 0){
       $db_opcao = 22;
       echo "<script>alert('Não existem fiscais cadastrados para esta vistoria!');</script>";
       echo "<script>location.href='fis3_fis_fandam002.php?abas=1';</script>";
       exit;
       $db_botao = false;
     }
     $db_botao = false;
   }

     $sSqlGetProcessoAdministrativo  = " select p58_numero || '/' || p58_ano as p58_numero, p58_requer ";
     $sSqlGetProcessoAdministrativo .= " from fiscalizacao.fis_vistorias ";
     $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalvistorias on y109_codvist = y70_codvist ";
     $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscal on y109_procfiscal = y100_sequencial ";
     $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalprot on y105_procfiscal = y100_sequencial ";
     $sSqlGetProcessoAdministrativo .= " inner join protprocesso on y105_protprocesso = p58_codproc ";
     $sSqlGetProcessoAdministrativo .= " where y70_codvist = $y70_codvist ";
    
     $rsGetProcessoAdministrativo = db_query($sSqlGetProcessoAdministrativo);
     if (pg_numrows($rsGetProcessoAdministrativo) > 0){
         db_fieldsmemory($rsGetProcessoAdministrativo,0);
     }

   echo "<script>parent.iframe_fiscais.location.href='fis3_fis_fandamusu001.php?y39_codandam=$y70_ultandam';</script>";
   echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" bgcolor="#cccccc" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <center>
    <td height="50" width="600" align="center" valign="top" bgcolor="#CCCCCC"> 
    <fieldset width="100%">
    <legend align="center">VISTORIA</legend>
	<?php 
        $clvistorias->rotulo->label();
        db_ancora(@$Ly70_codvist,"js_vist('".@$y70_codvist."');",$db_opcao);
        db_input('y70_codvist',20,$Iy70_codvist,true,'text',3,"");
	?>
    </fieldset>	
	</td>
  </tr>
  <tr>  
    <td height="230" width="100%" align="center" valign="top" bgcolor="#CCCCCC"> 
    <fieldset>
    <legend align="center">ANDAMENTO</legend>
	<?php 
	$db_opcao=2;
        if($db_opcao==2 && !isset($chavepesquisa)){
	  $db_opcao=22;
        }
        $db_botao = true;
	require_once(modification(Modification::getFile("forms/db_frm_fis_fandam.php")));
        if($db_opcao==22 && !isset($chavepesquisa)){
          echo "<script>document.form1.pesquisar.click();</script>";
        }
	?>
    </fieldset>	
	</td>
    </center>
  </tr>
</table>
</body>
</html>
<script>
function js_vist(codigo){
  js_OpenJanelaIframe('','db_iframe_vistorias','fis3_fis_vistorias006.php?y70_codvist='+codigo,'Pesquisa',true);
}
</script>
<?php 
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Alterar"){
  if($clvistorias->erro_status=="0"){
    $clvistorias->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clvistorias->erro_campo!=""){
      echo "<script> document.form1.".$clfandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clfandam->erro_campo.".focus();</script>";
    };
  }else{
    if($sqlerro==true){
      db_msgbox($erro);
    }else{
      $clvistorias->erro(true,false);
      echo "<script>parent.iframe_fiscais.location.href='fis3_fis_fandamusu001.php?y39_codandam=$y39_codandam';</script>";
      echo "<script>parent.mo_camada('fiscais');</script>";
      echo "<script>parent.document.formaba.fiscais.disabled=false;</script>";
      echo "<script>parent.iframe_fandam.location.href='fis3_fis_fandam002.php?abas=1&y70_codvist=$y70_codvist&chavepesquisa=".$y39_codandam."';</script>";
    }
  };
};
?>
