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
include(modification("classes/db_clientes_classe.php"));
include(modification("classes/db_atendemail_classe.php"));
include(modification("classes/db_clientesmodulos_classe.php"));
$clclientes = new cl_clientes;
  /*
$clatendemail = new cl_atendemail;
$clclientesmodulos = new cl_clientesmodulos;
  */
db_postmemory($HTTP_POST_VARS);
   $db_opcao = 1;
$db_botao = true;
if(isset($incluir)){
  $sqlerro=false;
  db_inicio_transacao();
  $clclientes->incluir($at01_codcli);
  if($clclientes->erro_status==0){
    $sqlerro=true;
  } 
  $erro_msg = $clclientes->erro_msg; 
  db_fim_transacao($sqlerro);
   $at01_codcli= $clclientes->at01_codcli;
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
<table align="center">
  <tr> 
    <td> 
    <center>
	<?php
	include(modification("forms/db_frmclientes.php"));
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
    if($clclientes->erro_campo!=""){
      echo "<script> document.form1.".$clclientes->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clclientes->erro_campo.".focus();</script>";
    };
  }else{
   db_msgbox($erro_msg);
   db_redireciona("ate1_clientes005.php?liberaaba=true&chavepesquisa=$at01_codcli");
  }
}
?>