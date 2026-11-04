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
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_auto_classe.php"));
db_postmemory($HTTP_POST_VARS);
db_postmemory($HTTP_SERVER_VARS);//exit;
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clauto = new cl_fis_auto;
$clauto->rotulo->label("y50_codauto");
$clauto->rotulo->label("y50_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("z01_numcgm");
$clrotulo->label("y80_codsani");
$clrotulo->label("q02_inscr");
$clrotulo->label("j01_matric");
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
    <td align="center" valign="top">
      <?php
//      if(!isset($pesquisa_chave)){
		  if(isset($origem) && $origem == "cgm"){
				$sql = " select y50_codauto,y50_data,y50_nome,y50_dtvenc from fiscalizacao.fis_auto inner join fiscalizacao.fis_autocgm on fis_auto.y50_codauto = fis_autocgm.y54_codauto where fis_autocgm.y54_numcgm = $num ";
		  }elseif(isset($origem) && $origem == "matric"){
				$sql = " select y50_codauto,y50_data,y50_nome,y50_dtvenc from fiscalizacao.fis_auto inner join fiscalizacao.fis_automatric on fis_auto.y50_codauto = fis_automatric.y53_codauto where fis_automatric.y53_matric = $num ";
		  }elseif(isset($origem) && $origem == "inscr"){
				$sql = " select y50_codauto,y50_data,y50_nome,y50_dtvenc from fiscalizacao.fis_auto inner join fiscalizacao.fis_autoinscr on fis_auto.y50_codauto =  fis_autoinscr.y52_codauto where fis_autoinscr.y52_inscr = $num ";
		  }elseif(isset($origem) && $origem == "sani"){
				$sql = " select y50_codauto,y50_data,y50_nome,y50_dtvenc from fiscalizacao.fis_auto inner join fiscalizacao.fis_autosanitario on fis_auto.y50_codauto = fis_autosanitario.y55_codauto where fis_autosanitario.y55_codsani = $num ";
		  }
      //die($sql);
      db_lovrot($sql,12,"()","",$funcao_js,"","NoMe",array(),false);
      /*
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
          $result = $clauto->sql_record($clauto->sql_query_busca($pesquisa_chave,"dl_Auto=$pesquisa_chave and x.y50_setor=".db_getsession("DB_coddepto")));
          if($clauto->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$z01_nome',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }*/
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
