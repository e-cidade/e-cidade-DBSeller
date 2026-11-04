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
include(modification("classes/db_rescisao_classe.php"));
include(modification("classes/db_rhcadregime_classe.php"));
include(modification("classes/db_codmovsefip_classe.php"));
include(modification("dbforms/db_funcoes.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clrescisao = new cl_rescisao;
$clrhcadregime = new cl_rhcadregime;
$clcodmovsefip = new cl_codmovsefip;

$db_botao = false;
$db_opcao = 33;

$iInstituicao = db_getsession('DB_instit');

if(isset($excluir)){
  try {
    db_inicio_transacao();
    $naoExcluir = $clrescisao->naoExcluirComRescisaoCadastrada($r59_regime,$r59_causa,$r59_caub,$r59_menos1,$iInstituicao);
    $exclusao = db_query($naoExcluir);

    if(pg_num_rows($exclusao) > 0) {
      throw new Exception("Não foi possível excluir a causa, causa já utilizada");
    }

    $db_opcao = 3;
    $clrescisao->excluir($r59_anousu,$r59_mesusu,$r59_regime,$r59_causa,$r59_caub,$r59_menos1);
    db_fim_transacao();

  } catch (Exception $e) {
      db_fim_transacao(true);  
      db_msgbox($e->getMessage()); 
  }

}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $result = $clrescisao->sql_record($clrescisao->sql_query($chavepesquisa,$chavepesquisa1,$chavepesquisa2,$chavepesquisa3,$chavepesquisa4,$chavepesquisa5)); 
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
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="25%" height="18">&nbsp;</td>
    <td width="25%">&nbsp;</td>
    <td width="25%">&nbsp;</td>
    <td width="25%">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php
	include(modification("forms/db_frmrescisao.php"));
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
if(isset($excluir)){
  if($clrescisao->erro_status=="0"){
    $clrescisao->erro(true,false);
  }else{
    $clrescisao->erro(true,true);
  }
}
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>
<script>
js_tabulacaoforms("form1","excluir",true,1,"excluir",true);
</script>