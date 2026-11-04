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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_lancamento_classe.php"));
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_SERVER_VARS);//exit;
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cllancamento = new cl_fis_lancamento;
$cllancamento->rotulo->label("nl01_codlanc");
$cllancamento->rotulo->label("nl01_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("z01_numcgm");
$clrotulo->label("y80_codsani");
$clrotulo->label("q02_inscr");
$clrotulo->label("j01_matric");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr> 
    <td align="center" valign="top"> 
      <?php 
		  if(isset($origem) && $origem == "cgm"){
				$sql = " select nl01_codlanc,nl01_data,nl01_nome,nl01_dtvenc from fiscalizacao.fis_lancamento inner join lancamentocgm on fis_lancamento.nl01_codlanc = lancamentocgm.nl06_codlanc where lancamentocgm.nl06_numcgm = $num ";
		  }elseif(isset($origem) && $origem == "matric"){
				$sql = " select nl01_codlanc,nl01_data,nl01_nome,nl01_dtvenc from fiscalizacao.fis_lancamento inner join lancamentomatric on fis_lancamento.nl01_codlanc = lancamentomatric.nl05_codlanc where lancamentomatric.nl05_matric = $num ";
		  }elseif(isset($origem) && $origem == "inscr"){
				$sql = " select nl01_codlanc,nl01_data,nl01_nome,nl01_dtvenc from fiscalizacao.fis_lancamento inner join lancamentoinscr on fis_lancamento.nl01_codlanc =  lancamentoinscr.nl04_codlanc where lancamentoinscr.nl04_inscr = $num ";
		  }elseif(isset($origem) && $origem == "sani"){
				$sql = " select nl01_codlanc,nl01_data,nl01_nome,nl01_dtvenc from fiscalizacao.fis_lancamento inner join lancamentosanitario on fis_lancamento.nl01_codlanc = lancamentosanitario.nl08_codlanc where lancamentosanitario.nl08_codsani = $num ";   
		  }
      db_lovrot($sql,12,"()","",$funcao_js,"","NoMe",array(),false);
      ?>
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
