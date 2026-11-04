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
require_once(modification("classes/db_rhlocaltrab_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clrhlocaltrab = new cl_rhlocaltrab;
$clrhlocaltrab->rotulo->label("rh55_codigo");
$clrhlocaltrab->rotulo->label("rh55_descr");
$clrhlocaltrab->rotulo->label("rh55_estrut");
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
            <td width="4%" align="right" nowrap title="<?=$Trh55_codigo?>">
              <?=$Lrh55_codigo?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("rh55_codigo",5,$Irh55_codigo,true,"text",4,"","chave_rh55_codigo");
		       ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Trh55_estrut?>">
              <?=$Lrh55_estrut?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("rh55_estrut",20,$Irh55_estrut,true,"text",4,"","chave_rh55_estrut");
		       ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Trh55_descr?>">
              <?=$Lrh55_descr?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("rh55_descr",40,$Irh55_descr,true,"text",4,"","chave_rh55_descr");
		       ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_rhlocaltrab.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <?php

      if (isset($filtro_lotacao) && $filtro_lotacao) {
          $sSql = "select count(*) as qtde_usuarios_permissao from db_usuariosrhlota";
          $rsSqlUsaFiltroLotacao = db_query($sSql);

          if (is_resource($rsSqlUsaFiltroLotacao) && db_utils::fieldsMemory($rsSqlUsaFiltroLotacao, 0)->qtde_usuarios_permissao > 0) {

             try {

                 $oDaoCfpess           = new cl_cfpess();
                 $oDaoDbUsuarioLotacao = new cl_db_usuariosrhlota();
                 $aEstruturais         = array();
                 $aResultados          = array();
                 $iInstit              = db_getsession('DB_instit');
                 $iAno                 = DBPessoal::getAnoFolha();
                 $iMes                 = DBPessoal::getMesFolha();
                 $iCodigoUsuario       = db_getsession('DB_id_usuario');

                 $sSqlMascaraLotacao = $oDaoCfpess->sql_query($iAno, $iMes, $iInstit, "db77_estrut");
                 $rsMascaraLotacao   = db_query($sSqlMascaraLotacao);

                 if (!$rsMascaraLotacao) {
                     throw new DBException("Erro ao buscar a mascara da lotação.");
                 }

                 if (pg_num_rows($rsMascaraLotacao) == 0) {
                     throw new BusinessException("Nenhuma lotação configurada para esta competência. Por favor verificar manutenção de parâmetros.");
                 }

                 $sMascara = db_utils::fieldsMemory($rsMascaraLotacao,0)->db77_estrut;

                 $sSqlLotacoesUsuario = $oDaoDbUsuarioLotacao->sql_query(null, "r70_estrut", null, "rh157_usuario = {$iCodigoUsuario}");
                 $rsLotacoesUsuario   = db_query($sSqlLotacoesUsuario);

                 if (!$rsLotacoesUsuario) {
                     throw new DBException("Erro ao buscar lotações do usuário.");
                 }

                 if (pg_num_rows($rsLotacoesUsuario) == 0) {
                     throw new BusinessException("Nenhuma lotação vinculada à este usuário.");
                 }

                 $aEstruturais = db_utils::getCollectionByRecord($rsLotacoesUsuario);

                 foreach ($aEstruturais as $oEstrutural) {
                     $iLocal = substr(trim($oEstrutural->r70_estrut),0,2);
                     if (!in_array($iLocal, $aResultados)) {
                       $aResultados[] = substr(trim($oEstrutural->r70_estrut),0,2);
                     }
                 }

                 $sWhereEstrutural = "substr(rh55_estrut,1,2) ~ '^(".implode('|',$aResultados).")'";

             } catch (Exception $oErro) {

                 db_msgbox($oErro->getMessage());
                 return true;
             }

          }
      }


      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_rhlocaltrab.php")==true){
             include(modification("funcoes/db_func_rhlocaltrab.php"));
           }else{
           $campos = "rhlocaltrab.*";
           }
        }
        $aWhere = array();
        if(isset($mInstituicoes) && !empty($mInstituicoes)) {
          if(is_array($mInstituicoes)) {
            $aWhere[] = " rh55_instit IN (". implode(", ", $mInstituicoes) .")";
          }
          if(!is_array($mInstituicoes)) {
            $aWhere[] = " rh55_instit IN (". $mInstituicoes .")";
          }
        } else {
          $aWhere[] = " rh55_instit = ".db_getsession("DB_instit");
        }

        if(isset($chave_rh55_estrut) && (trim($chave_rh55_estrut)!="") ){
          $aWhere[] = " rh55_estrut like '$chave_rh55_estrut%'";
        }

        if(isset($chave_rh55_descr) && (trim($chave_rh55_descr)!="") ){
          $aWhere[] = " rh55_descr like '$chave_rh55_descr%'";
        }

        if (isset($sWhereEstrutural)) {
            $aWhere[] = $sWhereEstrutural;
        }

        $dbwhere = implode(" AND ", $aWhere);

        if(isset($chave_rh55_codigo) && (trim($chave_rh55_codigo)!="") ){
           $sql = $clrhlocaltrab->sql_query($chave_rh55_codigo,null,$campos,"rh55_codigo", $dbwhere);
        }else if(isset($chave_rh55_estrut) && (trim($chave_rh55_estrut)!="") ){
           $sql = $clrhlocaltrab->sql_query("",null,$campos,"rh55_estrut", $dbwhere);
        }else if(isset($chave_rh55_descr) && (trim($chave_rh55_descr)!="") ){
	         $sql = $clrhlocaltrab->sql_query("",null,$campos,"rh55_descr", $dbwhere);
        }else{
           $sql = $clrhlocaltrab->sql_query("",null,$campos,"rh55_codigo", $dbwhere);
        }
        $repassa = array();
        if(isset($chave_rh55_descr)){
          $repassa = array("chave_rh55_codigo"=>$chave_rh55_codigo,"chave_rh55_descr"=>$chave_rh55_descr);
        }
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){

          $aWhere = array();
          $aWhere[] = "rh55_codigo = {$pesquisa_chave}";
          $aWhere[] = "rh55_instit = ".db_getsession("DB_instit");
          if (isset($sWhereEstrutural)) {
              $aWhere[] = $sWhereEstrutural;
          }
          $sWhere = implode(" and ", $aWhere);
          $result = $clrhlocaltrab->sql_record($clrhlocaltrab->sql_query(null, null, "*", null, $sWhere));

          if($clrhlocaltrab->numrows!=0){
            db_fieldsmemory($result,0);
            if ( isset($lRetornaEstrutural) ) {
              echo "<script>".$funcao_js."('$rh55_descr', '$rh55_estrut',false);</script>";
            } else {
              echo "<script>".$funcao_js."('$rh55_descr',false);</script>";
            }
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
<script>
js_tabulacaoforms("form2","chave_rh55_descr",true,1,"chave_rh55_descr",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
