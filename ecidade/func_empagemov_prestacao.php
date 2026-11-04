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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("dbforms/db_funcoes.php");
include modification("classes/db_empagemov_classe.php");
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clempagemov = new cl_empagemov;
$clempagemov->rotulo->label("e81_codmov");
$clempagemov->rotulo->label("e81_numemp");
$oRotulo = new rotulocampo();
$oRotulo->label("e60_numemp");
$oRotulo->label("e60_codemp");
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
            <td width="4%" align="right" nowrap title="<?=$Te81_codmov?>">
              <?=$Le81_codmov?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("e81_codmov",6,$Ie81_codmov,true,"text",4,"","chave_e81_codmov");
		       ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Te60_numemp?>">
              <?=$Le60_numemp?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("e81_numemp",10, $Ie60_numemp,true,"text",4,"","chave_e81_numemp");
		       ?>
            </td>
          </tr>
         <tr>
           <td width="4%" align="right" nowrap title="<?=$Te60_codemp?>">
             <?=$Le60_codemp?>
           </td>
           <td width="96%" align="left" nowrap>
             <?php
             db_input("e60_codemp",10, $Ie60_codemp,true,"text",4,"","chave_e60_codemp");
             ?>
           </td>
         </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_empagemov.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <?php

        $aWheres[] =   "e80_instit = " . db_getsession("DB_instit");
        if (isset($chave_empenho_conferido)) {

           $aWheres[] = "e45_conferido is not null";
           $aWheres[] = "not exists(select e170_sequencial from empprestarecibo where e170_emppresta = e45_sequencial)";

        }
       $aWheres[]  = "e81_cancelado is null";

        if (isset($chave_e81_codmov) && (trim($chave_e81_codmov)!="") ) {
          $aWheres[] = "e81_codmov = $chave_e81_codmov";
        }

        if (isset($chave_e81_numemp) && (trim($chave_e81_numemp)!="") ) {
           $aWheres[]  =  "e81_numemp = {$chave_e81_numemp}";
        }

        /* [Extensão] - Filtro da Despesa */

        if (isset($chave_e60_codemp) && (trim($chave_e60_codemp)!="") ) {

          $iAnoEmpenho         = db_getsession("DB_anousu");
          $aPartesCampoEmpenho = explode("/", $chave_e60_codemp);
          $iCodigoEmpenho      = $aPartesCampoEmpenho[0];
          if (count($aPartesCampoEmpenho) > 1){
            $iAnoEmpenho = $aPartesCampoEmpenho[1];
          };

          $aWheres[] =  "e60_codemp = '{$iCodigoEmpenho}' and e60_anousu = {$iAnoEmpenho}";
        }
        if (!isset($pesquisa_chave)) {

          if (isset($campos)==false) {
             if (file_exists("funcoes/db_func_empagemov.php") == true ) {
               include modification("funcoes/db_func_empagemov.php");
             } else {
               $campos = "empagemov.*";
             }
          }
          $campos = "e53_codord, {$campos}, sum(coalesce(e46_valor,0)) as e46_valor" ;

          $aWheres[] = "((e53_valor - e53_vlranu) > 0)";

          $aWheres[] = "((e46_valor + e53_vlranu ) < e53_valor) ";


          // soma os itens prestados e compara com o pagamento para ver se tem saldo a devolver
          //$aWheres[] = " (select sum(e46_valor) from empprestaitem where e46_emppresta = e45_sequencial ) < e53_vlrpag";
          
          $sWhere = implode(" and ", $aWheres);

          $sWhere .= " group by e53_codord,
         e81_codmov,
         e60_numemp,
         e60_codemp,
         z01_nome,
         empagemov.e81_valor ";


  	      $sql = $clempagemov->sql_query_empenho_conferidoPagamento( null,
                                             $campos,
                                             "e81_codmov",
                                             $sWhere
            );

          // echo $sql;

          db_lovrot($sql, 15, "()", "", $funcao_js);
        } else {
          if ($pesquisa_chave!=null && $pesquisa_chave!="") {
            $sql = $clempagemov->sql_query($pesquisa_chave);

            $result = $clempagemov->sql_record($sql);

            if ($clempagemov->numrows != 0) {
              db_fieldsmemory($result,0);
              echo "<script>".$funcao_js."('$e81_numemp',false);</script>";
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
