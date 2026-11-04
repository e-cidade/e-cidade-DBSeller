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
include(modification("classes/db_contabancaria_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);
$clcontabancaria = new cl_contabancaria;
$db_opcao = 22;
$db_botao = false;
if(isset($processar)){
	
  db_inicio_transacao();
  // var_dump($_POST);exit;
  
  $sDataProcessamento = implode('-',array_reverse(explode('/',$data)));
  
  db_query("create temp table w_concilia as 
            select k68_sequencial as concilia 
              from concilia 
             where k68_contabancaria in ({$db83_sequencial}) ;
               and k68_data >= '{$sDataProcessamento}'");
               
  db_query("delete from conciliapendcorrente 
             using w_concilia 
             where k89_concilia = concilia");
  
  db_query("delete from conciliapendextrato
             using w_concilia
             where k88_concilia = concilia");
  
  db_query("delete from conciliacor 
             using conciliaitem, w_concilia
             where k84_conciliaitem = k83_sequencial
               and k83_concilia     = concilia");
  
  db_query("delete from conciliaextrato 
             using conciliaitem, w_concilia
             where k87_conciliaitem = k83_sequencial
               and k83_concilia     = concilia");
  
  db_query("delete from conciliaitem 
             using w_concilia 
             where k83_concilia     = concilia");
  
  db_query("delete from concilia
             using w_concilia
             where k68_sequencial = concilia");
  
  db_fim_transacao();
  
}else if(isset($chavepesquisa)){
	
   $db_opcao = 2;
   $result = $clcontabancaria->sql_record($clcontabancaria->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $db_botao = true;
   
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table align="center">
  <tr> 
    <td> 
    <center>
			<?php
			  include(modification("forms/db_frmzeraconciliacao.php"));
			?>
    </center>
	</td>
  </tr>
</table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php

if(isset($alterar)){
  if($clcontabancaria->erro_status=="0"){
    $clcontabancaria->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($clcontabancaria->erro_campo!=""){
      echo "<script> document.form1.".$clcontabancaria->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$clcontabancaria->erro_campo.".focus();</script>";
    }
  }else{
    $clcontabancaria->erro(true,true);
  }
}
if($db_opcao==22 ){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","db83_descricao",true,1,"db83_descricao",true);
</script>