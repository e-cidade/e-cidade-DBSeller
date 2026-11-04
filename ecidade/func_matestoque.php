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
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_matestoque_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$oGet = db_utils::postMemory($_GET);

$oDaoMateEstoque = new cl_matestoque;
$clrotulo = new rotulocampo;
$oDaoMateEstoque->rotulo->label("m70_codigo");
$oDaoMateEstoque->rotulo->label("m70_codmatmater");
$clrotulo->label("m60_descr");
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
            <td width="4%" align="right" nowrap title="<?=$Tm70_codigo?>">
              <?=$Lm70_codigo?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php db_input("m70_codigo",10,$Im70_codigo,true,"text",4,"","chave_m70_codigo"); ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tm70_codmatmater?>">
              <?=$Lm70_codmatmater?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php db_input("m70_codmatmater",10,$Im70_codmatmater,true,"text",4,"","chave_m70_codmatmater"); ?>
            </td>
          </tr>
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Tm60_descr?>">
              <?=$Lm60_descr?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php db_input("m60_descr",40,$Im60_descr,true,"text",4,"","chave_m60_descr"); ?>
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="center"> 
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar"> 
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_matestoque.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr> 
    <td align="center" valign="top"> 
      <?php

      $aWhere   = array();
      $sCampos  = "m60_codmater, m60_descr, m70_coddepto, m70_quant, m70_valor";
      $sOrdem   = 'm70_codmatmater';
      $sRetorno = 'm70_codmatmater';

      /**
       * PK da matestoque 
       */
      if (!empty($chave_m70_codigo)) {
        $aWhere[] = "m70_codigo = $chave_m70_codigo";
      } 

      if (!empty($pesquisa_chave)) {

        /**
         * PK da matestoque 
         */
        $sWhereChave = "m70_codigo = $pesquisa_chave";

        /**
         * Pesquisa pelo manterial 
         */
        if (isset($oGet->material)) {

          $sWhereChave = "m60_codmater = " . $pesquisa_chave;
          $sRetorno = 'm60_descr';
        } 

        $aWhere[] = $sWhereChave;
      } 

      /**
       * Codigo do material 
       */
      if (!empty($chave_m70_codmatmater)) {
        $aWhere[] = "m70_codmatmater = $chave_m70_codmatmater";
      } 

      /**
       * Descricao 
       */
      if (!empty($chave_m60_descr)) {

        $sOrdem = 'm60_descr';
        $aWhere[] = "m60_descr like '%$chave_m60_descr%'";
      } 

      /**
       * Pesquisa apenas que forem servico true ou false 
       */
      if (!empty($oGet->servico)) {
        $aWhere[] = "(select true from matestoqueitem where m71_codmatestoque = m70_codigo and m71_servico is $oGet->servico limit 1)";
      }

      $sWhere = implode(' and ', $aWhere);
      $sSql = $oDaoMateEstoque->sql_query(null, $sCampos, $sOrdem, $sWhere);


      /**
       * Exibe grid lovrot 
       */
      if (empty($pesquisa_chave)) {

        db_lovrot($sSql, 15, "()", "", $funcao_js);

      } else {

        $rsMateEstoque = $oDaoMateEstoque->sql_record($sSql);

        if ($oDaoMateEstoque->numrows > 0) {

          db_fieldsmemory($rsMateEstoque,0);
          echo "<script>".$funcao_js."('" . $$sRetorno . "', false);</script>";

        } else {
          echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
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
