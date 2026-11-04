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
require_once(modification('classes/db_fis_lancamento_classe.php'));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clauto = new cl_fis_lancamento;

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
		
		$where  = ' where (case when (fis_grupotipoandamento.sequencial <> 8) then ' ;
		$where .= ' db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
		$where .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
		$where .= " and fis_processofiscalativo.ativo = 't' ";
		$where .= " when  nl01_codlanc not in (select nl19_codlanc from fiscalizacao.fis_autoandam) then";
		$where .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
		$where .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual')";
		$where .= " and fis_processofiscalativo.ativo = 't'";
		$where .= " else 1=1 end ) ";

		if(!isset($pesquisa_chave)){

				$sql = "SELECT DISTINCT(dl_Lanc),
                        dl_identificacao,
                        dl_codigo,
                        z01_nome,
                        tipo,
                        nl01_instit,
                        nl01_numbloco,
                        y41_descr as dl_Andamento,
                        y111_procfiscal as dl_Processo_Fiscal
                      FROM
                        (SELECT nl01_numbloco,
                          nl01_instit,
                          nl01_setor,
                          nl01_codlanc AS dl_Lanc,
                          y41_descr,
                          CASE
                              WHEN q02_numcgm IS NOT NULL THEN 'Inscrição'
                              ELSE (CASE WHEN j01_numcgm IS NOT NULL THEN 'Matrícula' ELSE (CASE WHEN y80_numcgm IS NOT NULL THEN 'Sanitário ' ELSE (CASE WHEN z01_numcgm IS NOT NULL THEN 'Cgm' ELSE (CASE WHEN y30_codnoti IS NOT NULL THEN 'Notificação' ELSE 'Nenhum' END) END) END) END)
                          END AS dl_identificacao,
                          CASE
                              WHEN y52_inscr IS NOT NULL THEN y52_inscr
                              ELSE (CASE WHEN y53_matric IS NOT NULL THEN y53_matric ELSE (CASE WHEN y55_codsani IS NOT NULL THEN y55_codsani ELSE (CASE WHEN z01_numcgm IS NOT NULL THEN z01_numcgm ELSE (CASE WHEN y51_codnoti IS NOT NULL THEN y51_codnoti END) END) END) END)
                          END AS dl_codigo,
                          CASE
                              WHEN q02_numcgm IS NOT NULL THEN q02_numcgm
                              ELSE (CASE WHEN j01_numcgm IS NOT NULL THEN j01_numcgm ELSE (CASE WHEN y80_numcgm IS NOT NULL THEN y80_numcgm ELSE (CASE WHEN z01_numcgm IS NOT NULL THEN z01_numcgm ELSE q02_numcgm END) END) END)
                          END AS z01_numcgm,
                          y27_descr AS tipo,
                          y111_procfiscal
                        FROM fiscalizacao.fis_auto
                        LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                        LEFT JOIN fiscalizacao.fis_autousu ON y56_codlanc = nl01_codlanc 
                        LEFT JOIN fiscalizacao.fis_autocgm ON y54_codlanc = nl01_codlanc
                        LEFT JOIN fiscalizacao.fis_autoinscr ON y52_codlanc = nl01_codlanc
                        LEFT JOIN fiscalizacao.fis_automatric ON y53_codlanc = nl01_codlanc
                        LEFT JOIN fiscalizacao.fis_autosanitario ON y55_codlanc = nl01_codlanc
                        LEFT JOIN iptubase ON j01_matric = y53_matric
                        LEFT JOIN issbase ON y52_inscr = q02_inscr
                        LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                        LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                        LEFT JOIN fiscalizacao.fis_autofiscal ON y51_codlanc = nl01_codlanc
                        LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
                        LEFT JOIN fiscalizacao.fis_procfiscalauto on y111_auto = nl01_codlanc
                        LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                        left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                        left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                        left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                        left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                        left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario
			left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                        LEFT JOIN fiscalizacao.fis_cadgestorfiscal ON fis_cadfiscais.id_usuario = fis_cadgestorfiscal.id_usuario
                        LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(nl19_codandam) from fiscalizacao.fis_autoandam where nl19_codlanc = nl01_codlanc) 
                        LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
			LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam
                        LEFT JOIN fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial
			$where
                    ) AS x
                    INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                    WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')." ORDER BY dl_Lanc DESC";
	
        			db_lovrot($sql,15,"()","",$funcao_js);
		} else {
			if($pesquisa_chave!=null && $pesquisa_chave!="") {

				$result = $clauto->sql_record($clauto->sql_query_busca($pesquisa_chave," nl01_instit = ".db_getsession('DB_instit')." and dl_Processo_Fiscal=$pesquisa_chave and x.nl01_setor=".db_getsession("DB_coddepto"), $where));									
          		

	
				if($clauto->numrows!=0) {
            				db_fieldsmemory($result,0);
					echo "<script>".$funcao_js."('$z01_nome',false);</script>";
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
