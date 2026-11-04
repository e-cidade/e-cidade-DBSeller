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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("dbforms/db_funcoes.php");
include modification("classes/db_rhlota_classe.php");
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clrhlota = new cl_rhlota;
$clrhlota->rotulo->label("r70_codigo");
$clrhlota->rotulo->label("r70_estrut");
$clrhlota->rotulo->label("r70_descr");
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
            <td width="4%" align="right" nowrap title="<?=$Tr70_codigo?>">
              <?=$Lr70_codigo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
		       db_input("coddepto",4,$Icoddepto,true,"text",4,"","chave_coddepto");
		       ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tr70_descr?>">
              <?=$Lr70_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
		       db_input("descrdepto",40,$Idescrdepto,true,"text",4,"","chave_descrdepto");
		       ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_rhlota.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php 
      $dbwhere = "";
      if(isset($instit)){
        $dbwhere = " and r70_instit = $instit ";
      }
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_rhlota.php")==true){
             include modification("funcoes/db_func_rhlota.php");
           }else{
           $campos = "rhlota.*";
           }
        }
        if(isset($chave_coddepto) && (trim($chave_coddepto)!="") ){
	         $where = " and d.coddepto = ".$chave_coddepto; 
        }else if(isset($chave_descrdepto) && (trim($chave_descrdepto)!="") ){
	         $where = " and  d.descrdepto like '$chave_descrdepto%'";
        }else{
          $where = "";
        }
          $sSql   = "  select distinct d.coddepto, d.descrdepto, u.db17_ordem, case when u.db17_ordem = 1 then u.db17_ordem else d.coddepto + 10000 end as ordem ";
          $sSql  .= "    from db_depusu u                                       ";
          $sSql  .= "         inner join db_depart d on u.coddepto = d.coddepto ";
          $sSql  .= "   where instit       = ".db_getsession("DB_instit");
          $sSql  .= "     and u.id_usuario = ".db_getsession("DB_id_usuario");
          $sSql  .= "     and (d.limite is null or d.limite >= '" . date("Y-m-d",db_getsession("DB_datausu")) . "')";
          $sSql  .= $where;
          $sSql  .= "order by 4 ";

          // echo $sSql;
        db_lovrot($sSql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $where = " and d.coddepto = $pesquisa_chave ";

          $sSql   = "  select distinct d.coddepto, d.descrdepto, u.db17_ordem, case when u.db17_ordem = 1 then u.db17_ordem else d.coddepto + 10000 end as ordem ";
          $sSql  .= "    from db_depusu u                                       ";
          $sSql  .= "         inner join db_depart d on u.coddepto = d.coddepto ";
          $sSql  .= "   where instit       = ".db_getsession("DB_instit");
          $sSql  .= "     and u.id_usuario = ".db_getsession("DB_id_usuario");
          $sSql  .= "     and (d.limite is null or d.limite >= '" . date("Y-m-d",db_getsession("DB_datausu")) . "')";
          $sSql  .= $where;
          $sSql  .= "order by 4 ";
          $result = pg_query($sSql);
          if(pg_num_rows($result)!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$descrdepto',false);</script>";
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
