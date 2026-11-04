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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_fis_cadfiscais_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clcadfiscais = new cl_fis_cadfiscais;
$clcadfiscais->rotulo->label("id_usuario");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body class="body-default">
<table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tid_usuario?>">
              <?=$Lid_usuario?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php 
		       db_input("id_usuario",5,$Iid_usuario,true,"text",4,"","chave_id_usuario");
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
      
      $sGestor  = 'select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = '.db_getsession('DB_id_usuario');
      $rsGestor = pg_query( $sGestor );
      $iGestor  = pg_num_rows( $rsGestor );
      
      if( $iGestor == 0 ){
        $where = "1=2";
      }else{
        $where = "coddepto=".db_getsession("DB_coddepto");
      }
      if( isset( $procfiscal ) ){
      
        $where .= " and fis_cadfiscais.id_usuario in (select y106_cadfiscais from fiscalizacao.fis_procfiscalfiscais 
                                                  inner join fiscalizacao.fis_processofiscalativo on processo_fiscal = y106_procfiscal and fiscal = y106_cadfiscais
                                                      where y106_procfiscal = $procfiscal and ativo = 't')";
      }
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_fis_cadfiscais.php")==true){
             include(modification("funcoes/db_func_fis_cadfiscais.php"));
           }else{
           $campos = "fis_cadfiscais.*,db_usuarios.nome";
           }
        }
        if(isset($chave_id_usuario) && (trim($chave_id_usuario)!="") ){
	         $sql = $clcadfiscais->sql_query_depto($chave_id_usuario,$campos,"fis_cadfiscais.id_usuario","fis_cadfiscais.id_usuario=$chave_id_usuario and $where");
        }else if(isset($chave_nome) && (trim($chave_nome)!="") ){
	         $sql = $clcadfiscais->sql_query_depto("",$campos,"fis_cadfiscais.id_usuario"," nome like '$chave_nome%' and $where");
        }else{
           $sql = $clcadfiscais->sql_query_depto("",$campos,"fis_cadfiscais.id_usuario","$where");
        }
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{

        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clcadfiscais->sql_record($clcadfiscais->sql_query_depto($pesquisa_chave,"*","fis_cadfiscais.id_usuario","fis_cadfiscais.id_usuario=$pesquisa_chave and $where"));
          if($clcadfiscais->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$id_usuario','$nome',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('','Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('','',false);</script>";
        }
      }
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
