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
include(modification("classes/db_andpadrao_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$clandpadrao = new cl_andpadrao;
$db_opcao = 33;
$db_botao = false;
file_put_contents('tmp/debug', print_r($HTTP_POST_VARS, true), FILE_APPEND);
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){
  db_inicio_transacao();
  $sqlerro=false;
  $clandpadrao->excluir($p53_codigo,$p53_ordem);
  if ($clandpadrao->erro_status==0){
  	$sqlerro=true;
  }
  $result_ordem = $clandpadrao->sql_record($clandpadrao->sql_query_file($p53_codigo,null,"*","p53_ordem"));
  if ($clandpadrao->numrows>0){
  	$numrows_ordem=$clandpadrao->numrows;
  	for ($w=0;$w<$numrows_ordem;$w++){
  		db_fieldsmemory($result_ordem,$w);
  		if ($sqlerro==false){
  			$ordem=$p53_ordem;
  			$p53_ordem=$p53_ordem+100;
        $result_alt=db_query("insert into andpadrao select p53_codigo, p53_coddepto, p53_dias, {$p53_ordem} from andpadrao where p53_codigo = $p53_codigo and p53_ordem=$ordem");
  			$result_alt_tabela_filha=db_query("update camposandpadrao set p110_andpadrao_ordem = $p53_ordem where p110_andpadrao_codigo = $p53_codigo and p110_andpadrao_ordem=$ordem");
        $result_alt=db_query("delete from camposandpadrao where p110_andpadrao_codigo = $p53_codigo and p110_andpadrao_ordem=$ordem");
        $result_alt=db_query("delete from andpadrao where p53_codigo = $p53_codigo and p53_ordem=$ordem");
  		}
  	}
  }
  $result_ordem = $clandpadrao->sql_record($clandpadrao->sql_query_file($p53_codigo,null,"*","p53_ordem"));
  if ($clandpadrao->numrows>0){
  	$numrows_ordem=$clandpadrao->numrows;
  	for ($w=0;$w<$numrows_ordem;$w++){
  		db_fieldsmemory($result_ordem,$w);
  		if ($sqlerro==false){
  			$ordem=$p53_ordem;
  			$p53_ordem=$w+1;
  			$result_alt=db_query("insert into andpadrao select p53_codigo, p53_coddepto, p53_dias, {$p53_ordem} from andpadrao where p53_codigo = $p53_codigo and p53_ordem=$ordem");
        $result_alt_tabela_filha=db_query("update camposandpadrao set p110_andpadrao_ordem = $p53_ordem where p110_andpadrao_codigo = $p53_codigo and p110_andpadrao_ordem=$ordem");
        $result_alt=db_query("delete from camposandpadrao where p110_andpadrao_codigo = $p53_codigo and p110_andpadrao_ordem = $ordem");
        $result_alt=db_query("delete from andpadrao where p53_codigo = $p53_codigo and p53_ordem=$ordem");
  			/*
  			$clandpadrao->alterar($p53_codigo,$ordem);
  			if ($clandpadrao->erro_status==0){
  				$erro_msg=$clandpadrao->erro_msg;
  				db_msgbox($erro_msg);
  				$sqlerro=true;
  				break;
 			}*/
  		}
  	}
  }
  db_fim_transacao($sqlerro);
}else if(isset($chavepesquisa)){
$db_opcao = 3;
   $result = $clandpadrao->sql_record($clandpadrao->sql_query($chavepesquisa,$chavepesquisa1));
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
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<div class="container">
<?php
include(modification("forms/db_frmandpadrao.php"));
?>
</div>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Excluir"){
if($clandpadrao->erro_status=="0"){
  $clandpadrao->erro(true,false);
}else{
  $clandpadrao->erro(true,false);
  echo "<script>location.href='pro1_andpadrao001.php?p53_codigo=$p53_codigo&p51_descr=$p51_descr'</script>";
};
}
?>
