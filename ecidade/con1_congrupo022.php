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
include(modification("classes/db_congrupo_classe.php"));
include(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clcongrupo = new cl_congrupo;

$db_opcao = 22;
$db_botao = false;
$anousu   = db_getsession("DB_anousu");
if(isset($alterar)){
  db_inicio_transacao();
  $db_opcao = 2;
  $clcongrupo->alterar($c20_sequencial);
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result   = $clcongrupo->sql_record($clcongrupo->sql_query($chavepesquisa)); 
   if ($clcongrupo->numrows > 0){
     db_fieldsmemory($result,0);
     $db_botao = true;

     echo "<script> 
               (window.CurrentWindow || parent.CurrentWindow).corpo.document.formaba.conta.style.visibility='visible';
               (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_conta.disable='false';
               (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_conta.location.href = 'con1_conplanogrupoconta004.php?c21_anousu=$anousu&c21_congrupo=$c20_sequencial';
  	       </script>";
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
	include(modification("forms/db_frmcongrupo.php"));
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php
if(isset($alterar)){
  if($clcongrupo->erro_status=="0"){
    $clcongrupo->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clcongrupo->erro_campo!=""){
      echo "<script> document.form1.".$clcongrupo->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clcongrupo->erro_campo.".focus();</script>";
    }
  }else{
    $clcongrupo->erro(true,true);
  }
}
if($db_opcao==22){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>