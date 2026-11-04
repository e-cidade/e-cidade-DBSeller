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
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_publicidadesigap_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oPost = db_utils::postMemory($_POST);
$oGet  = db_utils::postMemory($_GET);

$clpublicidadesigap = new cl_publicidadesigap;

$db_opcao = 3;
$db_botao = false;
$lSqlErro = false;
$iInstit  = db_getsession('DB_instit');

if (isset($oPost->excluir)) {
	
  db_inicio_transacao();
  
  $db_botao = true;
  
  $clpublicidadesigap->excluir($oPost->c48_sequencial);
  $sMsg = $clpublicidadesigap->erro_msg;
  if ($clpublicidadesigap->erro_status == 0) { 
    $lSqlErro = true;
  }
  
  db_fim_transacao($lSqlErro);
} else if (isset($oGet->chavepesquisa)) {

   $db_botao = true;
   
   $result = $clpublicidadesigap->sql_record($clpublicidadesigap->sql_query($oGet->chavepesquisa)); 
   db_fieldsmemory($result, 0);
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
<style type="text/css">
td {
  white-space: nowrap
}

fieldset table td:first-child {
  width: 160px;
  white-space: nowrap
}

#c48_sequencial {
  width: 80px;
}

#c48_ano {
  width: 40px;
}

#c48_mes {
  width: 25px;
}

#c48_descricao, #c48_meiocomunicacaosigap, #c48_tiporelatoriofiscal, #c48_meiocomunicacaosigap_select_descr, 
#c48_tiporelatoriofiscal_select_descr {
  width: 100%;
}
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" >
  <tr> 
    <td height="40px">&nbsp;</td>
  </tr>
</table>
<table width="630" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr> 
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC"> 
    <center>
      <?php
        include(modification("forms/db_frmpublicidadesigap.php"));
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
<script>
js_tabulacaoforms("form1","excluir",true,1,"excluir",true);
</script>
<?php
if (isset($oPost->excluir)) {
  db_msgbox($sMsg);
}

if ($db_botao == false) {
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>