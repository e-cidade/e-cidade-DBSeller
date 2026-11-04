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
//include modification("classes/db_fis_fiscal_classe.php");
require_once(modification('classes/db_fis_fiscal_estendida_classe.php'));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clfiscal = new cl_fis_fiscal_estendida;
$clfiscal->rotulo->label("y30_codnoti");
$clfiscal->rotulo->label("y30_data");
// Ticket 108335
if(isset($intimacao) && $intimacao == 1){
  $Ty30_codnoti = 'Código da Intimação

Campo:y30_codnoti                             ';

  $Ty30_data = 'Data da Intimação

Campo:y30_data                             ';

  $Ly30_codnoti = '<strong>Código da Intimação:</strong>';
  $Ly30_data    = '<strong>Data da Intimação:</strong>';
}
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
              <?=$Ly30_codnoti?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       db_input("y30_codnoti",20,$Iy30_codnoti,true,"text",4,"","chave_y30_codnoti");
		       ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Ty30_data?>">
              <?=$Ly30_data?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php
		       // db_inputdata("y30_data",10,$Iy30_data,true,"text",4,"","chave_y30_data");
           db_inputdata('y30_data',@$y30_data_dia,@$y30_data_mes,@$y30_data_ano,true,'text',4,"");
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
        $rsGestor = pg_query($sGestor);
        $iGestor  = pg_num_rows($rsGestor);

      	if(isset($intimacao) && $intimacao == 1){

        	$whereIntimacao  = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = true) ';
          if( $iGestor > 0 ){
          // die('aa');
            $whereIntimacao .= " and CASE when y100_sequencial is not null then";
          }else{
             $whereIntimacao .= " and ";
          }
      		$whereIntimacao .= ' (y39_codandam is null or fis_grupotipoandamento.sequencial = 4 ';
      		$whereIntimacao .= '  ) ';
      		$whereIntimacao .= " and fis_processofiscalativo.ativo = 't' and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') and fis_processofiscalativo.fiscal = ".db_getsession('DB_id_usuario')." or fis_fiscalusuario.y38_id_usuario = ".db_getsession('DB_id_usuario')."  ";
          if( $iGestor > 0 ){
            $whereIntimacao .= " else 1=1 end";
          }
      	}else{
          $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = false) ';
          if( $iGestor > 0 ){
            $whereIntimacao .= " and CASE when y100_sequencial is not null then";
          }else{
           $whereIntimacao .= " and ";
          }

      		$whereIntimacao .= ' (y39_codandam is null or fis_grupotipoandamento.sequencial = 2 ';
      		$whereIntimacao .= ' ) ';
      		$whereIntimacao .= " and fis_processofiscalativo.ativo = 't' and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') or fis_processofiscalativo.fiscal = ".db_getsession('DB_id_usuario')." ";
           if( $iGestor > 0 ){
            $whereIntimacao .= " else 1=1 end";
          }
        }

      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_fis_fiscal.php")==true){
             include modification("funcoes/db_func_fis_fiscal.php");
           }else{
           $campos = "fis_fiscal.*";
           }
        }
        if(isset($chave_y30_codnoti) && (trim($chave_y30_codnoti)!="") ){
           $sql = $clfiscal->getIntimacaoOuNotificacao($chave_y30_codnoti,$campos,"y30_codnoti"," y30_setor=".db_getsession("DB_coddepto")." and y30_instit = ".db_getsession('DB_instit').$whereIntimacao );
        }else if(isset($y30_data) && (trim($y30_data)!="") ){
      	   $sql = $clfiscal->getIntimacaoOuNotificacao("",$campos,"y30_data"," y30_data = '$y30_data_ano-$y30_data_mes-$y30_data_dia' and y30_setor= ".db_getsession("DB_coddepto")." and y30_instit = ".db_getsession('DB_instit').$whereIntimacao );
        }else{
           $sql = $clfiscal->getIntimacaoOuNotificacao("",$campos,"y30_codnoti"," y30_setor=".db_getsession("DB_coddepto")." and y30_instit = ".db_getsession('DB_instit').$whereIntimacao );
        }
// die($sql);
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clfiscal->sql_record($clfiscal->getIntimacaoOuNotificacao($pesquisa_chave,"*",null,"y30_setor=".db_getsession("DB_coddepto")." and y30_codnoti = $pesquisa_chave "." and y30_instit = ".db_getsession('DB_instit').$whereIntimacao ));

	if($clfiscal->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$y30_nome',false);</script>";
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
if (isset($intimacao) and $intimacao == '1') {
	echo '<script> document.getElementsByClassName("DBLovrotInputCabecalho")[0].value="Código da Intimação";</script>';
}

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
