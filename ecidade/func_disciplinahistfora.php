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

//MODULO: educação
include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_disciplina_classe.php"));
include(modification("classes/db_histmpsdiscfora_classe.php"));
include(modification("classes/db_historicompsfora_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$cldisciplina = new cl_disciplina;
$clhistmpsdiscfora = new cl_histmpsdiscfora;
$clhistoricompsfora = new cl_historicompsfora;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="POST">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
 <tr>
  <td align="center" valign="top">
   <?php
   $escola = db_getsession("DB_coddepto");
   $result  = $clhistoricompsfora->sql_record($clhistoricompsfora->sql_query("","ed29_i_ensino",""," ed99_i_codigo = $serie AND ed61_i_codigo = $curso"));
   db_fieldsmemory($result,0);
   echo "<b>Escolha a Disciplina:</b>";
    if(isset($campos)==false){
     if(file_exists("funcoes/db_func_disciplina.php")==true){
      include(modification("funcoes/db_func_disciplina.php"));
     }else{
      $campos = "disciplina.*";
     }
    }
    $sql = "SELECT $campos
            FROM disciplina
             inner join ensino on ed10_i_codigo = ed12_i_ensino
            WHERE ed12_i_ensino = $ed29_i_ensino
            EXCEPT
            SELECT $campos
            FROM histmpsdiscfora
             inner join disciplina on ed12_i_codigo = ed100_i_disciplina
             inner join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina
             inner join ensino on ed10_i_codigo = ed12_i_ensino
            WHERE ed100_i_historicompsfora = $serie
            ORDER BY ed59_i_ordenacao
           ";
    db_lovrot($sql,15,"()","",$funcao_js);
   ?>
  </td>
 </tr>
</table>
</form>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
