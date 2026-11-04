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
include(modification("classes/db_movcasadassefip_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clmovcasadassefip = new cl_movcasadassefip;
$clmovcasadassefip->rotulo->label("r67_anousu");
$clmovcasadassefip->rotulo->label("r67_mesusu");
$clmovcasadassefip->rotulo->label("r67_afast");
$clmovcasadassefip->rotulo->label("r67_reto");
$clmovcasadassefip->rotulo->label("r67_reto");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
$str_valores = "";
$str_virgula = "";
if(isset($pesquisa_chave)){
	$r67_anousu = db_anofolha();
	$r67_mesusu = db_mesfolha();
  // die($clmovcasadassefip->sql_query($r67_anousu,$r67_mesusu,$pesquisa_chave,null,"distinct r67_reto"));
	$res = $clmovcasadassefip->sql_record($clmovcasadassefip->sql_query($r67_anousu,$r67_mesusu,$pesquisa_chave,null,"distinct r67_reto"));
	if($clmovcasadassefip->numrows == 0){
		$res = $clmovcasadassefip->sql_record($clmovcasadassefip->sql_query($r67_anousu,$r67_mesusu,null,null,"distinct r67_reto"));
	}
  for($i=0; $i<$clmovcasadassefip->numrows; $i++){
  	db_fieldsmemory($res, $i);
		$str_valores.= $str_virgula.$r67_reto;
		$str_virgula = ",";
  }
}
?>
</body>
</html>
<?php
if(trim($str_valores) != ""){
  ?>
  <script>
    parent.js_listarretorno("<?=$str_valores?>");
  </script>
  <?php
}else{
  ?>
  <script>
    parent.db_iframe_listaretorno.hide();
  </script>
  <?php
}
?>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
