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
include(modification("libs/db_liborcamento.php"));
include(modification("libs/db_libcontabilidade.php"));
include(modification("classes/db_conlancamval_classe.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_conlancam_classe.php"));
include(modification("classes/db_conlancamcompl_classe.php"));
include(modification("classes/db_conlancamdig_classe.php"));
include(modification("classes/db_conlancamdoc_classe.php"));
include_once(modification("classes/db_conplano_classe.php"));
include_once(modification("libs/db_menu_estrutural.php")); // teste carlos

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clconplano     = new cl_conplano;
$clconlancamval = new cl_conlancamval;
$clconlancamcompl = new cl_conlancamcompl;
$clconlancamdig   = new cl_conlancamdig;
$clconlancam      = new cl_conlancam;
$clconlancamdoc   = new cl_conlancamdoc;


$db_opcao = 3;
$db_botao = false;
 if(isset($chavepesquisa)){
      if (isset($sequen)){
	 $result = $clconlancamval->sql_record(
	     $clconlancamval->sql_query("","*","","c69_codlan=$chavepesquisa and c69_sequen=$sequen")); 
      }	else {
         $result = $clconlancamval->sql_record($clconlancamval->sql_query($chavepesquisa)); 
      }	 
      db_fieldsmemory($result,0);
      $db_botao = false;

      $rr = $clconlancamdoc->sql_record($clconlancamdoc->sql_query_file($c69_codlan)); 
      if (($clconlancamdoc->numrows)>0){
           db_fieldsmemory($rr,0);

      }
  }

$consulta=true; 

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
<table width="790" border="1" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
	<?php
	include(modification("forms/db_frmconlancamval.php"));
	?>
    
    </center>
  </td>
  <td width="50%" align="left" valign="top"> 
  <!-- inicio estrutural  --->
  <?php //monta conplano, recebe conplano.reduz
      //--
       $res_debito=$clconplano->sql_record($clconplano->sql_query_file("","","c60_estrut, c60_descr as debito_descr","","c60_codcon in (select c61_codcon from conplanoreduz where c61_reduz=$c69_debito and c61_anousu=".db_getsession("DB_anousu")." )"));
       db_fieldsmemory($res_debito,0);
       $estrut_debito=$c60_estrut;
       //--
       $res_credito=$clconplano->sql_record($clconplano->sql_query_file("","","c60_estrut,c60_descr as credito_descr","","c60_codcon in (select c61_codcon from conplanoreduz where c61_reduz=$c69_credito and c61_anousu=".db_getsession("DB_anousu").")"));
       db_fieldsmemory($res_credito,0);
       $estrut_credito=$c60_estrut;
       //--
       $estrutura=new menu_estrutural; 
       $estrutura->estrut_debito=$estrut_debito;
       $estrutura->estrut_debito_descr=$debito_descr;
       $estrutura->estrut_credito=$estrut_credito;
       $estrutura->estrut_credito_descr=$credito_descr;
       $estrutura->show();  
  ?> 
  <!--- // fim estrutural --->
  </td>
  </tr>
</table>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
