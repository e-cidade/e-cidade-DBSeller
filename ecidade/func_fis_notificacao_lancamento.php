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
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('classes/db_fis_lancamento_classe.php'));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$cllancamento = new cl_fis_lancamento;
$clrotulo     = new rotulocampo;

$clrotulo->label("y30_codnoti");

$dataAtual = date('Y-m-d');
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
            <td width="4%" align="right" nowrap title="<?=$Ty30_codnoti?>">
               <b> Notificação de Lançamento: </b> 
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
               db_input("notificacao",20,3,true,"text",4,"","chave_notificacao");
              ?>
              </td>
            </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php 
    $sWhere = array();

    $dataAtual = date('Y-m-d');
    
    $sGestor  = 'select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = '.db_getsession('DB_id_usuario');
    $rsGestor = pg_query($sGestor);
    $iGestor  = pg_num_rows($rsGestor);

    
    $where2 = "where ((fis_grupotipoandamento.sequencial IN (21,22,23)))";
    if( $iGestor > 0 ){
      $where2 .= " AND CASE when y100_sequencial is not null then";
    } else {
     $where2 .= " and";
    }
    $where2 .= ' (case when fis_grupotipoandamento.sequencial is null then ' ;
    $where2 .= ' db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
    $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
    $where2 .= " and fis_processofiscalativo.ativo = 't'";
    $where2 .= " when  nl01_codlanc not in (select nl19_codlanc from fiscalizacao.fis_lancandam) then";
    $where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
    $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual')";
    $where2 .= " and fis_processofiscalativo.ativo = 't'";
    $where2 .= " else 1=1 end ) ";

    if( $iGestor > 0 ){
      $where2 .= " else 1=1 end";
    }
  if ( !isset( $pesquisa_chave ) ) {
    

    $aWhere  = implode(" and ", $sWhere);

    if ( isset($chave_notificacao) && ( trim($chave_notificacao)!= "" ) ) {

      $sql = $cllancamento->sql_query_busca( $chave_notificacao , "",$where2);
    } elseif (isset($chave_nl01_numbloco)){
       $sql = $cllancamento->sql_query_busca( null , $aWhere." and nl01_numbloco = '".$chave_nl01_numbloco."'",$where2);
    }else{
      $sql = $cllancamento->sql_query_busca( null, "", $where2);
    }
    db_lovrot( $sql , 15 , "()" , "" , $funcao_js );

  } else {

    if ($pesquisa_chave!=null && $pesquisa_chave!="") {
      $aWhere  = implode(" and ", $sWhere);

      $result = $cllancamento->sql_record($cllancamento->sql_query_busca( null , "dl_Notificacao_Lancamento = ".$pesquisa_chave,$where2));
      if ($cllancamento->numrows!=0) {
        db_fieldsmemory($result,0);
        echo "<script>".$funcao_js."('$z01_nome',false);</script>";
      } else {
        echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
      }
    } else {
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
