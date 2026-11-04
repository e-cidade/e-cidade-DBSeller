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
include(modification("libs/db_utils.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_db_almox_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$oPost = db_utils::postmemory($_POST,0);
$oGet  = db_utils::postmemory($_GET,0);

$cldb_almox = new cl_db_almox;
$cldb_almox->rotulo->label("m91_codigo");
$cldb_almox->rotulo->label("m91_depto");
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
            <td width="4%" align="right" nowrap title="<?=$Tm91_codigo?>">
              <?=$Lm91_codigo?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
		        db_input("m91_codigo",6,$Im91_codigo,true,"text",4,"","chave_m91_codigo");
		      ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tm91_depto?>">
              <?=$Lm91_depto?>
            </td>
            <td width="96%" align="left" nowrap>
                <?php
		        db_input("m91_depto",6,$Im91_depto,true,"text",4,"","m91_depto");
		      ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_db_almox.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
        <?php

      if(!isset($pesquisa_chave)) {

      	if(isset($campos) == false ) {
          if(file_exists("funcoes/db_func_db_almox.php") == true) {
            include(modification("funcoes/db_func_db_almox.php"));
          } else {
            $campos = "m91_codigo, m91_depto, descrdepto";
          }
        }

        //parametros da consulta da classe ( $m91_codigo=null,$campos="*",$ordem=null,$dbwhere="")
        $instit = db_getsession("DB_instit");

        // se apenas o campo m91_codigo foi preenchido no formulário
        if (isset($chave_m91_codigo) && (trim($chave_m91_codigo) != "") && trim($m91_depto) == null) {
          $sqlDeposito = $cldb_almox->sql_query("",$campos,"m91_codigo"," m91_codigo like '$chave_m91_codigo%' and db_depart.instit = $instit");
        }

        // se apenas o campo depto foi preenchido como parametro para busca
        else if (isset($m91_depto) && (trim($m91_depto) != "") && trim($chave_m91_codigo) == null) {
          $sqlDeposito = $cldb_almox->sql_query("",$campos,"m91_codigo","m91_depto = $m91_depto and db_depart.instit = $instit");
        }

        // se os dois campos de busca foram preenchidos
        else if (isset($chave_m91_codigo) && (trim($chave_m91_codigo) != "") && (trim($m91_depto) != "")) {
          $sqlDeposito = $cldb_almox->sql_query("",$campos,"m91_codigo","m91_codigo = $chave_m91_codigo and m91_depto = $m91_depto and db_depart.instit = $instit ");
        }

        // se nenhum parametro foi preenchido
        if(!isset($sqlDeposito)) {
          $sqlDeposito = $cldb_almox->sql_query("",$campos,"m91_codigo","db_depart.instit = $instit");
        }
          db_lovrot($sqlDeposito,15,"()","",$funcao_js);

      } else {

      	if ($pesquisa_chave != null && $pesquisa_chave != "") {

      		if (isset($dpto) && $dpto == true) {
      			$result = $cldb_almox->sql_record($cldb_almox->sql_query(null,"*",null,"m91_depto = {$pesquisa_chave}"));
          } else {
            $instit = db_getsession("DB_instit");
            $result = $cldb_almox->sql_record($cldb_almox->sql_query($pesquisa_chave,"*",null,"m91_codigo = {$pesquisa_chave} and db_depart.instit = $instit "));
      		}

          if ($cldb_almox->numrows != 0) {
            db_fieldsmemory($result,0);
            if (isset($dpto) && $dpto == true || !empty($sDescricaoDepartamento)) {
            	echo "<script>".$funcao_js."('$descrdepto',false);</script>";
            } else {
                echo "<script>" . $funcao_js . "('$m91_codigo',false, '$m91_codigo', '$descrdepto');</script>";
            }
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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
