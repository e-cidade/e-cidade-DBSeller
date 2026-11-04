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


//MODULO: educação
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$oDaoMatricula = db_utils::getdao('matricula');
$oDaoTurma     = db_utils::getdao('turma');

$iSerieFiltro  = "";
$sWhereTurma   = " ed57_i_codigo = ".$turma;
$sSqlTurma     = $oDaoTurma->sql_query_turmaserie("", "ed11_i_codigo", "", $sWhereTurma);
$rsTurmaFiltro = $oDaoTurma->sql_record($sSqlTurma);
$iLinhasFiltro = $oDaoTurma->numrows;

if ($iLinhasFiltro > 0) {
  $iSerieFiltro = db_utils::fieldsmemory($rsTurmaFiltro, 0)->ed11_i_codigo;
}

$oDaoMatricula->rotulo->label("ed60_i_codigo");
$clrotulo = new rotulocampo;
$clrotulo->label("ed47_v_nome");

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  </head>
  <body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table height="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
      <tr>
        <td height="63" align="center" valign="top">
          <table width="35%" border="0" align="center" cellspacing="0">
            <form name="form2" method="post" action="" >
              <tr>
                <td width="4%" align="right" nowrap title="<?=$Ted60_i_codigo?>">
                  <?=$Led60_i_codigo?>
                </td>
                <td width="96%" align="left" nowrap>
                  <?php db_input("ed60_i_codigo", 10, $Ied60_i_codigo, true, "text", 4, "", "chave_ed60_i_codigo"); ?>
                </td>
              </tr>
              <tr>
                <td width="4%" align="right" nowrap title="<?=$Ted47_v_nome?>">
                  <?=$Led47_v_nome?>
                </td>
                <td width="96%" align="left" nowrap>
                  <?php db_input("ed47_v_nome", 50, $Ied47_v_nome, true, "text", 4, "", "chave_ed47_v_nome"); ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                  <input name="limpar" type="reset" id="limpar" value="Limpar" >
                  <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_aluno.hide();">
                </td>
              </tr>
            </form>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          <?php
            $sCampos  = " DISTINCT (ed60_i_codigo), ed60_i_codigo,ed60_i_aluno,ed47_v_nome,ed60_i_turma, ";
            $sCampos .= " ed60_c_situacao,ed60_d_datamatricula,ed60_d_datasaida,ed60_c_concluida,ed60_i_numaluno ";
          
            if (!isset($pesquisa_chave)) {
              
              $sOrderBy = " ed60_i_numaluno,ed47_v_nome,ed60_i_codigo ";	
              $sWhere   = "";
            	
              if (isset($chave_ed60_i_codigo) && (trim($chave_ed60_i_codigo) != "")) {
                
              	$sWhere  = " ed60_i_codigo = $chave_ed60_i_codigo AND ed95_i_serie = $iSerieFiltro ";
              	$sWhere .= " AND ed74_i_dependencia = 2 ";
                
              } elseif (isset($chave_ed47_v_nome) && (trim($chave_ed47_v_nome) != "")) {
 
                $sWhere  = " ed47_v_nome like '$chave_ed47_v_nome%' AND ed74_i_dependencia = 2 ";
                $sWhere .= " AND ed95_i_serie = $iSerieFiltro ";
              
              } else {
              
                $sWhere = " ed95_i_serie = $iSerieFiltro AND ed74_i_dependencia = 2 ";
              
              }

              $sWhere .= " AND ed60_i_codigo NOT IN (";
              $sWhere .= "                        SELECT ed297_matricula ";
              $sWhere .= "                            FROM matriculadependencia ";
              $sWhere .= "                            WHERE ed297_turma = $turma ";
              $sWhere .= "                          ) ";
              $sSql    = $oDaoMatricula->sql_query_matriculadependencia("",$sCampos, $sOrderBy, $sWhere);
              
              $repassa = array();
              
              if (isset($chave_ed60_i_codigo)) {
                $repassa = array(
                                 "chave_ed60_i_codigo" => $chave_ed60_i_codigo,
                                 "chave_ed47_v_nome"   => $chave_ed47_v_nome
                                );
              }
            
              db_lovrot($sSql,15,"()","",$funcao_js,"","NoMe",$repassa);
            
            } else {
              
              if ($pesquisa_chave != null && $pesquisa_chave != "") {
                
                $sWhere  = " ed60_i_aluno = $pesquisa_chave AND ed60_i_turma = $turma AND ed60_c_concluida = 'N' ";
                $sWhere .= " ed74_i_dependencia = 2 ";
                $sWhere .= " AND ed60_i_codigo NOT IN (";
                $sWhere .= "   SELECT ed297_matricula FROM matriculadependencia WHERE ed297_turma = $chave_pesquisa ";
                $sWhere .= "     ) ";
                $sSql    = $oDaoMatricula->sql_query("", "*", "", $sWhere);
                $rsSql   = $oDaoMatricula->sql_record($sSql);
                
                if ($oDaoMatricula->numrows != 0) {
                  
                  db_fieldsmemory($rsSql, 0);
                  echo "<script>".$funcao_js."('$ed47_v_nome','$ed60_i_codigo','$ed60_c_situacao','$ed60_d_datamatricula','$ed60_d_datasaida',false);</script>";
                
                } else {
                  echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado','','','','',true);</script>";
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

<script>
  js_tabulacaoforms("form2","chave_ed60_i_codigo",true,1,"chave_ed60_i_codigo",true);
</script>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
