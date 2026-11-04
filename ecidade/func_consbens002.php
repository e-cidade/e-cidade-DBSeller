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
include(modification("classes/db_apolitem_classe.php"));
include(modification("dbforms/db_funcoes.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$clapolitem = new cl_apolitem;
$clrotulo = new rotulocampo;

$clrotulo->label("t81_codapo");//código da apolice
$clrotulo->label("t81_apolice");//descrição da apólice
$clrotulo->label("t81_venc");//vencimento da apólice
$clrotulo->label("t80_segura");//observação
$clrotulo->label("z01_nome");//observação
$clrotulo->label("t80_contato");//observação

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="../scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td  align="center" valign="top" bgcolor="#CCCCCC"> 
 <table border='1' cellspacing="0" cellpadding="0" align ="center" >   
 <?php
              
      $result = $clapolitem->sql_record($clapolitem->sql_query(null,null,"*",null," t82_codbem=$t82_codbem and t81_venc >='".date("Y-m-d",db_getsession("DB_datausu"))."'"));
      $numrows = $clapolitem->numrows;
      if($numrows>0){
	echo "
	    <tr>
	      <td align='center'><b>$RLt81_codapo</b></td>
	      <td align='center'><b>$RLt81_apolice</b></td>
	      <td align='center'><b>$RLt81_venc</b></td>
	      <td align='center'><b>$RLt80_segura</b></td>
	      <td align='center'><b>$RLt80_contato</b></td>
	      <td align='center'><b>$RLz01_nome</b></td>
	    </tr>
	";
         for($i=0; $i<$numrows; $i++){
	    db_fieldsmemory($result,$i);
	    echo "<tr>
	      <td align='center'><small>$t81_codapo</small></td>
	      <td align='center'><small>$t81_apolice</small></td>
	      <td align='center'><small>".db_formatar($t81_venc,"d")."</small></td>
	      <td align='center'><small>$t80_segura</small></td>
	      <td align='center'><small>$t80_contato</small></td>
	      <td align='center'><small>$z01_nome</small></td>
	    </tr>"; 
         }
      }	 
 ?>
 </table>
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
