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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_conn.php"));
require_once(modification('libs/db_sql.php'));

include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_grupotipoandamento_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clgrupotipoandamento = new cl_fis_grupotipoandamento;
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
      if(!isset($pesquisa_chave)){
        $campos = " sequencial
                    ,descricao
                      ,case manual_automatico 
                          when 1 then 'MANUAL' 
                          when 2 then 'AUTOMÁTICO' 
                      end as manual_automatico
                      ,case tipo_peca
                          when 1 then 'LEVANTAMENTO'
                          when 2 then 'NOTIFICAÇÃO'
                          when 3 then 'AUTO'
                          when 4 then 'INTIMAÇÃO'
                          when 5 then 'VISTORIA'
                      end as tipo_peca
                      ";
        if(isset($chave_sequencial) && (trim($chave_sequencial)!="") ){
	         $sql = "select ".$campos." from fiscalizacao.fis_grupotipoandamento chave_sequencial like '%$chave_sequencial%' ";
        }else{
          $sql = "select ".$campos." from fiscalizacao.fis_grupotipoandamento ";
        }

        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = "select * from fiscalizacao.fis_grupotipoandamento where sequencial = ".$pesquisa_chave;
          if($clgrupotipoandamento->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$chave_sequencial', '', false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      ?>
     </td>
   </tr>
</table>
</body>
</html>
<?php 
if(!isset($pesquisa_chave)){
  ?>
  <script>
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
