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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('classes/db_fis_fiscal_estendida_classe.php'));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clfiscal = new cl_fis_fiscal_estendida;
$clfiscal->rotulo->label("y100_sequencial");
$clfiscal->rotulo->label("y100_coddepto");

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
		$dataAtual = date('Y-m-d');
		$where  = " y30_setor = ".db_getsession('DB_coddepto')." and y30_instit = ".db_getsession('DB_instit');
		
		$where2  = ' where (case when (fis_grupotipoandamento.sequencial <> 7 and fis_grupotipoandamento.sequencial <> 9) then ' ;
		$where2 .= ' db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
		$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
		$where2 .= " and fis_processofiscalativo.ativo = 't' ";
		$where2 .= " when  y30_codnoti not in (select y49_codnoti from fiscalizacao.fis_fiscalandam) then";
		$where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
		$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual')";
		$where2 .= " and fis_processofiscalativo.ativo = 't'";
		$where2 .= " else 1=1 end ) ";

		if (isset($intimacao) and $intimacao == 1) {
			$where .= " and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = true) ";
		} else {
			$where .= " and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = false) ";
		}

		if(!isset($pesquisa_chave)){
			$campos=" distinct y30_codnoti as dl_Codigo,identifica as dl_Identificacao, codigo as dl_Codigo_Ident, z01_nome, y30_numbloco, y41_descr as dl_Andamento, y100_sequencial as dl_Processo_Fiscal";
        			if(isset($chave_y100_sequencial) && (trim($chave_y100_sequencial)!="") ){	
					$where2 .= " and y100_sequencial = $chave_y100_sequencial ";	
        			}
				$sql = $clfiscal->sql_query_info($chave_y30_codnoti, $campos, $where." ", $where2);	
				//die($sql);											
				$repassa = array();
        			if(isset($chave_y100_coddepto)){
          				$repassa = array("chave_y100_sequencial"=>$chave_y100_sequencial,"chave_y100_coddepto"=>$chave_y100_coddepto);
        			}
        			db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa,false);
		} else {
			if($pesquisa_chave!=null && $pesquisa_chave!="") {
				$campos=" distinct y30_codnoti as dl_Codigo,identifica as dl_Identificacao, codigo as dl_Codigo_Ident, z01_nome, y30_numbloco, y41_descr as dl_Andamento, y100_sequencial as dl_Processo_Fiscal";
				$where .= " and y100_sequencial = $pesquisa_chave ";
				$sql = $clfiscal->sql_query_info($pesquisa_chave,$campos, $where, $where2);
				db_lovrot($sql,15,"()","",$funcao_js,"");
				//$result = $clfiscal->sql_record($clfiscal->sql_query_info($pesquisa_chave,$campos, $where, $where2));
													
          			if($clfiscal->numrows!=0) {
            				//db_fieldsmemory($result,0);
					//echo "<script>".$funcao_js."($dl_Codigo, $dl_Processo_Fiscal, '$z01_nome' );</script>";

            				/*if(isset($procLev) && $procLev == 1){
            					echo "<script>".$funcao_js."('$z01_nome', false, '$y101_numcgm', '$y103_inscr' );</script>";
            				} else {
            					echo "<script>".$funcao_js."('$z01_nome',false,$db_depart_protocolo,'$db_descr_depart',$db_depart_atual  );</script>";
            				}*/
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
<script>
js_tabulacaoforms("form2","chave_y100_coddepto",true,1,"chave_y100_coddepto",true);
</script>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
