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
include(modification("classes/db_conlancam_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clconlancam = new cl_conlancam;
$clconlancam->rotulo->label("c70_codlan");
$clconlancam->rotulo->label("c70_anousu");

function carrega_destinatario(){
 $texto = "";
 $res = db_query("select id_usuario,nome from db_usuarios order by nome ");    
 for ($x=0;$x < pg_numrows($res);$x++){
     db_fieldsmemory($res,$x);
	global $id_usuario,$nome,$cod_destinatario;
	//if ($cod_destinatario==$id_usuario){
     //     $texto .="<option value=$id_usuario selected>$id_usuario $nome </option>";
	//} else{
	  $texto .="<option value=$id_usuario>$id_usuario $nome </option>";
	//}
 }
 return $texto;

}
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
 <td height="63" align="center" valign="top">
 <table width="35%" border="0" align="center" cellspacing="0">
   <form name="form2" method="post" action="" >
          <tr> 
            <td  align="right" nowrap title="<?=$Tc70_codlan?>"> Ordem   </td>
            <td  align="left" nowrap colspan=2><?php  db_input("ordem",10,'',true,"text",3);  ?></td>
          </tr>
          <tr> 
            <td  align="right" nowrap>Descrição       </td>
            <td  align="right" nowrap colspan=2 ><textarea name=descricao></textarea>
            </td>
          </tr>
          <tr> 
            <td  align="right" nowrap> Título       </td>
            <td  align="right" nowrap colspan=2><input type=text name=titulo ></td>
          </tr>
          <tr> 
            <td  align="right" nowrap> Responável     </td>
            <td  align="right" nowrap colspan=2>          
                  <select name=responsavel>
                     <?php echo carrega_destinatario(); ?>
                  </select>      
            </td>
          </tr>                        
          <tr> 
            <td  align="right" nowrap> Data   xxxxxxxxx  </td>
            <td  align="right" nowrap> Hora   xxxxxxx          </td>
            <td  align="right" nowrap> Prazo/Tempo  xxxxxxx          </td>
          </tr>              
             
          
        </form>
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
