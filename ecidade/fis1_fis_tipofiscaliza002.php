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
require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("classes/db_fis_tipofiscaliza_classe.php");
include modification("classes/db_fis_fisdoc_classe.php");
include modification("dbforms/db_funcoes.php");

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if(!isset($abas)){
  echo "<script>location.href='fis1_fis_fiscaltipo005.php?db_opcao=2'</script>";
  exit;
}

echo "<script>parent.document.formaba.tipodep.disabled=false;</script>";
db_postmemory($HTTP_POST_VARS);
$cltipofiscaliza = new cl_fis_tipofiscaliza;
$clfisdoc = new cl_fis_fisdoc;
$db_opcao = 22;
$db_botao = false;
$sqlerro  = false;
if(isset($alterar)){
  db_inicio_transacao();
  $db_opcao = 2;
  $cltipofiscaliza->alterar($y27_codtipo);

  if($cltipofiscaliza->erro_status==0){
    $sqlerro = true;
  }

  $clfisdoc->fd01_codtipo = $cltipofiscaliza->y27_codtipo;
  $clfisdoc->fd01_doc     = $y32_templateautoinfracao;
  $clfisdoc->fd01_instit  = db_getsession('DB_instit');

  $Metodo  = " select * from fiscalizacao.fis_fisdoc where ";
  $Metodo .= "fd01_codtipo =".$y27_codtipo." and fd01_instit = ".db_getsession('DB_instit');
  $ResultMetodo = db_query($Metodo);

  if(pg_num_rows($ResultMetodo) == 0){
    $clfisdoc->incluir();
  }else{
    $clfisdoc->alterar();
  }

  if($clfisdoc->erro_status==0){
    $sqlerro = true;
  } 
  
  db_fim_transacao($sqlerro);
}else if(isset($chavepesquisa)){
   $db_opcao = 2;
   $result = $cltipofiscaliza->sql_record($cltipofiscaliza->sql_query($chavepesquisa)); 
   db_fieldsmemory($result,0);
   $db_botao = true;
   $resul2 = db_query("select fd01_doc as y32_templateautoinfracao, db82_descricao as db82_descricaoautodeinfracao from fiscalizacao.fis_fisdoc 
                        inner join db_documentotemplate on fd01_doc = db82_sequencial and fd01_instit = db82_instit 
                      where fd01_codtipo = ".$chavepesquisa);
   db_fieldsmemory($resul2,0);
   if(isset($incluido)){
    $dbopcao = 1;
   }else{
    $dbopcao = 2;
   }
   echo "<script>parent.iframe_tipodep.location.href='fis1_fis_tipofiscalizadep001.php?y27_codtipo=$chavepesquisa&chavepesquisa=$chavepesquisa&db_opcao=".$dbopcao."';</script>";
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" width="100%" align="left" valign="top" bgcolor="#CCCCCC"> 
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
if(isset($alterar)){
  if($cltipofiscaliza->erro_status=="0"){
    $cltipofiscaliza->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if($cltipofiscaliza->erro_campo!=""){
      echo "<script> document.form1.".$cltipofiscaliza->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$cltipofiscaliza->erro_campo.".focus();</script>";
    };
  }else{
    $cltipofiscaliza->erro(true,false);
    echo "<script>parent.mo_camada('tipodep');</script>";
    echo "<script>parent.document.formaba.tipodep.disabled=false;</script>";

  };
};
if($db_opcao==22 & isset($abas)){
  echo "<script>document.form1.pesquisar.click();</script>";
}
?>