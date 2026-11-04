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
require_once(modification("libs/db_utils.php"));
db_postmemory($HTTP_POST_VARS);

$oDaoSamKitMaterial = db_utils::getdao("sam_kit_material");
$db_opcao = 1;
$db_botao = true;
if (isset($incluir)) {

  db_inicio_transacao();
  $oDaoSamKitMaterial->incluir($sm03_sequencial);
  db_fim_transacao();

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
<br><br>
<center>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <?php
     include(modification("forms/db_frmsam_kit_material.php"));
    ?>
    </td>
  </tr>
</table>
</center>
</body>
</html>
<script>
js_tabulacaoforms("form2","sm03_descr",true,1,"sm03_descr",true);
</script>
<?php
if(isset($incluir)){
  if($oDaoSamKitMaterial->erro_status=="0"){
    $oDaoSamKitMaterial->erro(true,false);
    $db_botao=true;
    echo "<script> document.form2.db_opcao.disabled=false;</script>  ";
    if($oDaoSamKitMaterial->erro_campo!=""){
      echo "<script> document.form2.".$oDaoSamKitMaterial->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form2.".$oDaoSamKitMaterial->erro_campo.".focus();</script>";
    }
  }else{
    $oDaoSamKitMaterial->erro(true,false);
    db_redireciona('sam1_sam_kit_material005.php?chavepesquisa='.$oDaoSamKitMaterial->sm03_sequencial);
  }
}
?>