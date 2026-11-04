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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("classes/db_fis_tipofiscaliza_classe.php");
include modification("classes/db_fis_fisdoc_classe.php");
include modification("dbforms/db_funcoes.php");


if(!isset($abas)){
  echo "<script>location.href='fis1_fis_fiscaltipo005.php?db_opcao=3'</script>";
  exit;
}

db_postmemory($HTTP_POST_VARS);
$cltipofiscaliza = new cl_fis_tipofiscaliza;
$clfisdoc = new cl_fis_fisdoc;
$clfisdocdep = new cl_fis_fisdocdep;
$db_botao = false;
$db_opcao = 33;
if(isset($excluir)){
  db_inicio_transacao();
  $db_opcao = 3;
  $cltipofiscaliza->excluir($y27_codtipo);

  if(isset($y32_templateautoinfracao) && $y32_templateautoinfracao != ''){

    $clfisdoc->fd01_codtipo = $y27_codtipo;
    $clfisdoc->fd01_doc     = $y32_templateautoinfracao;
    $clfisdoc->fd01_instit  = db_getsession('DB_instit');

    $clfisdoc->excluir();
  }
  
  $resuldep = db_query("select * from fiscalizacao.fis_fisdocdep where fd02_codtipo = ".$y27_codtipo);
  // echo "select * from fiscalizacao.fis_fisdocdep where fd02_codtipo = ".$y27_codtipo; exit;
  $var = pg_num_rows($resuldep);
  if(pg_num_rows($resuldep) > 0){
  // echo $var; exit;
    $clfisdocdep->fd02_codtipo = $y27_codtipo;
    $clfisdocdep->fd02_instit = db_getsession('DB_instit');
    $clfisdocdep->excluirtodos();
  }

  
  db_fim_transacao();
}else if(isset($chavepesquisa)){
   $db_opcao = 3;
   $result = $cltipofiscaliza->sql_record($cltipofiscaliza->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $resul2 = db_query("select fd01_doc as y32_templateautoinfracao, db82_descricao as db82_descricaoautodeinfracao from fiscalizacao.fis_fisdoc 
                        inner join db_documentotemplate on fd01_doc = db82_sequencial and fd01_instit = db82_instit 
                      where fd01_codtipo = ".$chavepesquisa);
   db_fieldsmemory($resul2,0);
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
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php 
	include modification("forms/db_frm_fis_tipofiscaliza.php");
	?>
    </center>
	</td>
  </tr>
</table>
</body>
</html>
<?php 
if(isset($excluir)){
  if($cltipofiscaliza->erro_status=="0"){
    $cltipofiscaliza->erro(true,false);
  }else{
    $cltipofiscaliza->erro(true,false);
    echo "<script>location.href='fis1_fis_tipofiscaliza003.php?abas=1';</script>";
    // echo "<script>parent.mo_camada('tipodep');</script>";
    // echo "<script>parent.document.formaba.tipodep.disabled=false;</script>";
  };
};
if($db_opcao==33){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>