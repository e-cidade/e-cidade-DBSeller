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
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

$clissbase  = new cl_issbase;
$cliptubase = new cl_iptubase;
$clcriaabas = new cl_criaabas;
$db_opcao   = 1;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<table width="100%" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table valign="top" marginwidth="0" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
     <?php
      $clcriaabas->identifica = array("isencao"=>"Dados da Isenção",'isencaocalc'=>"Cálculos"); 
	    $clcriaabas->sizecampo = array("isencao"=>"30","isencaolanc"=>"30", 'isencaocalc'=>"30");
	    $clcriaabas->src = array("isencao"=>"tri1_isencao004.php?origem=$origem&valorigem=$valorigem");
	    $clcriaabas->disabled   =  array("isencaocalc"=>"true"); 
	    $clcriaabas->cria_abas(); 

      // testa se a origem ja nao esta baixada 
      if(isset($origem) && $origem == "2"){
	      $rsBaixa = $clissbase->sql_record($clissbase->sql_query_file($valorigem,'q02_inscr',null,"q02_inscr = $valorigem and q02_dtbaix is not null"));
	      if($clissbase->numrows > 0){
	        db_msgbox("Inscrição baixada !");
          db_redireciona("tri4_cadisencaoalt001.php?origemmenu=2");
	      }
	    }else if(isset($origem) && $origem == "3"){
	      $rsBaixa = $cliptubase->sql_record($cliptubase->sql_query_file($valorigem,'j01_matric',null,"j01_baixa is not null"));
	      if($cliptubase->numrows > 0){
	        db_msgbox("Matrícula baixada !");
          db_redireciona("tri4_cadisencaoalt001.php?origemmenu=3");
	      }
	    }
     ?> 
    </td>
   </tr>
  </table>
  <form name="form1">
  </form>
      <?php
	    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
      ?>
  </body>
  </html>