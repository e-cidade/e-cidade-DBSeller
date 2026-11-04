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

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
  ${$key} = $value;
}

db_postmemory($HTTP_POST_VARS);

$clsiopecategoria = new cl_siopecategoria;
$clsiopecategoria->rotulo->label();

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
            <td width="4%" align="right" nowrap title="<?=$Tsi03_id?>">
              <?=$Lsi03_id?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
     		       db_input("si03_id", 10, $Isi03_id, true, "text", 4, "", "chave_id_siopecategoria");
              ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="">
              <?=$Lsi03_descricao?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
     		       db_input("si03_descricao", 50, $Isi03_descricao, true, "text", 4, "", "chave_descricao");
		          ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_categoriasiope.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <?php

      $campos = "si03_id, si03_descricao, si03_siopecategoriatipo, si02_descricao as Tipo";

      if (!isset($pesquisa_chave)) {

        if (isset($chave_id_siopecategoria) && (trim($chave_id_siopecategoria) != "")) {
            $sSql = $clsiopecategoria->sql_query(null, $campos, "si03_id");
        }else if (isset($chave_descricao) && (trim($chave_descricao)!="")) {
            $sWhere = "si03_descricao ilike '{$chave_descricao}%'";
            $sSql = $clsiopecategoria->sql_query(null, $campos, "si03_id", $sWhere);
        }else{
            $sSql = $clsiopecategoria->sql_query(null, $campos, "si03_id");
        }
        $repassa = array();

        if(isset($chave_descricao)){
          $repassa = array("chave_id_siopecategoria"=>$chave_id_siopecategoria,"chave_descricao"=>$chave_descricao);
        }

        db_lovrot($sSql, 15,"()", "", $funcao_js, "", "NoMe", $repassa);

      }else{


        if ($pesquisa_chave != null && $pesquisa_chave != "" ) {
            $sWhere = "si03_id = {$pesquisa_chave} ";
            $sSql = $clsiopecategoria->sql_query(null, "si03_descricao", "si03_id", $sWhere);
            $result = $clsiopecategoria->sql_record($sSql);

          if( $clsiopecategoria->numrows !=0 ){

            db_fieldsmemory($result, 0);

            echo "<script>".$funcao_js."('$si03_descricao',false);</script>";

          } else {

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
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
