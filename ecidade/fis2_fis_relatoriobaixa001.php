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
include(modification("classes/db_fis_sanitario_classe.php"));
include(modification("classes/db_fis_saniatividade_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
$clsaniatividade = new cl_fis_saniatividade;
$clsaniatividade->rotulo->label();
$db_opcao=1;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#CCCCCC">
<form name="form1" method="post" action="">
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<center>
<table border="0">
<br><br><strong>Relatório de Atividades Baixadas</strong>
<br><br><br><br>	
  <tr>
    <td nowrap title="<?=@$Ty83_dtfim?>">
       <?=@$Ly83_dtfim?>
    </td>
    <td> 
<?php 
db_inputdata('',@$dia,@$mes,@$ano,true,'text',$db_opcao,"")
?>
&nbsp;&nbsp;&nbsp;À&nbsp;&nbsp;&nbsp;
<?php 
db_inputdata('a',@$diaa,@$mesa,@$anoa,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  </table>
<input name="consultar" type="button" value="Relatório" onClick="js_consultasani();js_limpacampos();" >
  </center>
</form>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<script>
function js_limpacampos(){
    document.form1._dia.value = ''; 
    document.form1._mes.value = ''; 
    document.form1._ano.value = ''; 
    document.form1.a_dia.value = ''; 
    document.form1.a_mes.value = ''; 
    document.form1.a_ano.value = ''; 
}
function js_consultasani(){
  jan = window.open('fis2_fis_relatoriobaixa002.php?dataini='+document.form1._ano.value+'-'+document.form1._mes.value+'-'+document.form1._dia.value+'&datafim='+document.form1.a_ano.value+'-'+document.form1.a_mes.value+'-'+document.form1.a_dia.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0');
  jan.moveTo(0,0);
}
</script>