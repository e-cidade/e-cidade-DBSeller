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
require_once(modification("classes/db_benstransf_classe.php"));
require_once(modification("classes/db_benstransfdes_classe.php"));
require_once(modification("classes/db_benstransfconf_classe.php"));
db_postmemory($_SERVER);
parse_str($_SERVER["QUERY_STRING"]);
$clbenstransf = new cl_benstransf;
$clbenstransfdes = new cl_benstransfdes;
$clbenstransfconf = new cl_benstransfconf;
$clbenstransf->rotulo->label("t93_codtran");
$clbenstransf->rotulo->label("t93_data");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table height="100%" width="100%" border="0"  align="center" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
        <form name="form1" method="post" action="" >
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tt93_codtran?>">
              <?=$Lt93_codtran?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("t93_codtran",10,$It93_codtran,true,"text",4,"","chave_t93_codtran");
		       ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tt93_data?>">
              <?=$Lt93_data?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
             	      db_inputdata('t93_data',@$t93_data_dia,@$t93_data_mes,@$t93_data_ano,true,'text',1,"");
		       ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_benstransf.hide();">
             </td>
          </tr>
        </form>
        </table>
      </td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <?php
      $id_depart    = db_getsession("DB_coddepto");
      $where_instit = " and t93_instit = ".db_getsession("DB_instit");
      $id_depart = db_getsession("DB_coddepto");

      if((isset($t93)) && $t93 == 'true'){
	    $campo_where = empty($ignorar_departamento) ? ' t93_depart ' : '';
      }else{
	    $campo_where = ' t95_codtran is not null and t95_codbem is not null ';
	    $campo_where .= empty($ignorar_departamento) ? 'and t94_depart' : '';
      }

      $where_db_param = " $campo_where and ";
      if (empty($ignorar_departamento)) {
        $where_db_param = " $campo_where = $id_depart and ";
      }

      if(isset($campos)==false){
         if(file_exists("funcoes/db_func_benstransf.php")==true){
           require_once(modification("funcoes/db_func_benstransf.php"));
         }else{
            $campos = "benstransf.*";
         }
      }
      if((isset($t93_data_ano) && trim($t93_data_ano) != "") && (isset($t93_data_mes) && trim($t93_data_mes) != "") && (isset($t93_data_dia) && trim($t93_data_dia) != "")){
	$chave_t93_data =  $t93_data_ano."-".$t93_data_mes."-".$t93_data_dia;
      }else if(isset($t93_data_ano) && trim($t93_data_ano) != ""){
	$chave_t93_data = $t93_data_ano."%";
      }
      if (isset($rel) && $rel == 'true'){
        $param = " in ";
      } else if (isset($rel) and $rel == 'ignorar_confirmacao') {
        $param = false;
      } else{
        $param = " not in ";
      }

      $verificaConfirmacaoTransfer = '1 = 1';
      if ($param !== false) {
        $verificaConfirmacaoTransfer = " t93_codtran $param (select t96_codtran from benstransfconf) ";
      }

      $campos = "distinct $campos";

      if(!isset($pesquisa_chave)){
        if(isset($chave_t93_codtran) && (trim($chave_t93_codtran)!="") ){
          $sql = $clbenstransf->sql_query_departamento_destino(null,$campos,"t93_codtran"," $where_db_param $verificaConfirmacaoTransfer and t93_codtran = $chave_t93_codtran $where_instit ");
        }else if(isset($chave_t93_data) && (trim($chave_t93_data)!="") ){
	  $sql = $clbenstransf->sql_query_departamento_destino("",$campos,"t93_data"," $where_db_param $verificaConfirmacaoTransfer and t93_data like '$chave_t93_data' $where_instit ");
        }else{
          $sql = $clbenstransf->sql_query_departamento_destino("",$campos,"t93_codtran"," $where_db_param $verificaConfirmacaoTransfer $where_instit ");
        }

        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clbenstransf->sql_record($clbenstransf->sql_query_departamento_destino(null,$campos,""," $where_db_param $verificaConfirmacaoTransfer and t93_codtran = $pesquisa_chave and t93_instit = ".db_getsession("DB_instit")));
          if($clbenstransf->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$nome',false);</script>";
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
