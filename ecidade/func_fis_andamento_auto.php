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
include modification("classes/db_fis_auto_classe.php");
db_postmemory($HTTP_POST_VARS);
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
    <td height="63" align="center" valign="top">
        <table width="35%" border="0" align="center" cellspacing="0">
	     <form name="form2" method="post" action="" >
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Ty50_codauto?>"><?=$Ly50_codauto?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("y50_codauto",10,$Iy50_codauto,true,"text",4,"","chave_y50_codauto"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tz01_numcgm?>"><?=$Lz01_numcgm?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("z01_numcgm",10,$Iz01_numcgm,true,"text",4,"","chave_z01_numcgm"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tj01_matric?>"><?=$Lj01_matric?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("j01_matric",10,$Ij01_matric,true,"text",4,"","chave_j01_matric"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Tq02_inscr?>"><?=$Lq02_inscr?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("q02_inscr",10,$Iq02_inscr,true,"text",4,"","chave_q02_inscr"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="<?=$Ty80_codsani?>"><?=$Ly80_codsani?></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("y80_codsani",10,$Iy80_codsani,true,"text",4,"","chave_y80_codsani"); ?>
            </td>
          </tr>
          <tr>
            <td width="4%" align="right" nowrap title="Notificação"><b>Notificação</b></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("y30_codnoti",10,@$y30_codnoti,true,"text",4,"","chave_y30_codnoti"); ?>
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
      	$dataAtual = date('Y-m-d');
        $sGestor  = 'select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = '.db_getsession('DB_id_usuario');
	$rsGestor = pg_query($sGestor);
	$iGestor  = pg_num_rows($rsGestor);
	$where2 = "";  	
	if( $iGestor > 0 ){
	    $where2 .= " AND CASE when y100_sequencial is not null then";
  	} else {
   		$where2 .= " and";
  	}

	$sFiscal  = " SELECT * FROM fiscalizacao.fis_cadfiscais WHERE id_usuario =".db_getsession('DB_id_usuario');
	$rsFiscal = pg_query($sFiscal);
	$iFiscal  = pg_num_rows($rsFiscal);

	if ($iFiscal > 0 ){
	   $fiscal = TRUE;
	} 


  	$where2 .= " CASE WHEN  fis_grupotipoandamento.sequencial <> 8 or fis_grupotipoandamento.sequencial is null THEN (   db_usuarios.id_usuario = ".db_getsession('DB_id_usuario');
	$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
	$where2 .= " and fis_processofiscalativo.ativo = 't') or (y56_id_usuario = ".db_getsession('DB_id_usuario').")";
	//$where .= " and fis_processofiscalativo.ativo = 't') else y50_setor=".db_getsession('DB_coddepto')."  end) ";
	if( $iGestor > 0 ){
	   $where2 .= " else 1=1 end";
	}
	$where2  .= " else 1=1 end";
	$where = $where2;
	if(!isset($pesquisa_chave)){	
		$andWhere = "";
      	
		if (isset($chave_y50_codauto) && (trim($chave_y50_codauto) != "")) {
			$andWhere .= ' and dl_Auto = '.$chave_y50_codauto;	
		} elseif (isset($chave_q02_inscr) && (trim($chave_q02_inscr) != "") ) {
			$andWhere .= " and dl_identificacao='Inscrição' and dl_codigo=".$chave_q02_inscr; 
		} elseif (isset($chave_j01_matric) && (trim($chave_j01_matric) != "")) {
			$andWhere .= " and dl_identificacao='Matrícula' and dl_codigo=".$chave_j01_matric;
		} elseif (isset($chave_z01_numcgm) && (trim($chave_z01_numcgm) != "")) {
			$andWhere .= " and dl_identificacao='Cgm' and dl_codigo=".$chave_z01_numcgm;
		} elseif (isset($chave_y80_codsani) && (trim($chave_y80_codsani) != "")) {
			$andWhere .= " and dl_identificacao='Sanitário' and dl_codigo=".$chave_y80_codsani;
		} elseif (isset($chave_y30_codnoti) && (trim($chave_y30_codnoti) != "")) {
			$andWhere .= " and dl_identificacao='Notificação' and dl_codigo=".$chave_y30_codnoti;			
		} elseif (isset($chave_y50_numbloco) && (trim($chave_y50_numbloco) != "")) {
			$andWhere .= " and x.y50_numbloco =".$chave_y50_numbloco;
		}

		$andWhere = ' order by dl_Auto desc ';
	
		$sql  = "select distinct dl_Auto, dl_identificacao, dl_codigo, z01_nome, tipo, y50_instit, y50_numbloco, y41_descr as dl_Andamento,dl_Processo_fiscal ";
		$sql .= "from (select y50_numbloco, y50_instit, y50_setor, y50_codauto  AS dl_Auto, y41_descr,y111_procfiscal as dl_Processo_fiscal, ";
		$sql .= "case when q02_numcgm is not null then 'Inscrição' else (case when j01_numcgm is not null then 'Matrícula' ";
		$sql .= " else (case when y80_numcgm is not null then 'Sanitário' else (case when z01_numcgm is not null then 'Cgm' ";
		$sql .= " else (case when y30_codnoti is not null then 'Notificação' else 'Nenhum' end) end ) end) end) end as dl_identificacao, ";
		$sql .= "case when y52_inscr is not null then y52_inscr else (case when y53_matric is not null then y53_matric ";
		$sql .= " else (case when y55_codsani is not null then y55_codsani else (case when z01_numcgm is not null then z01_numcgm ";
		$sql .= " else (case when y51_codnoti is not null then y51_codnoti end) end) end) end) end as dl_codigo, ";
		$sql .= " case when q02_numcgm is not null then q02_numcgm else (case when j01_numcgm is not null then j01_numcgm  ";
		$sql .= " else (case when y80_numcgm is not null then y80_numcgm else (case when z01_numcgm is not null then z01_numcgm ";
		$sql .= " else q02_numcgm end) end) end) end as z01_numcgm, y27_descr as tipo from fiscalizacao.fis_auto ";
		$sql .= "left join fiscalizacao.fis_tipofiscaliza on y50_codtipo = y27_codtipo ";
		$sql .= "left join fiscalizacao.fis_autousu on y56_codauto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto ";
		$sql .= "left join iptubase on j01_matric = y53_matric ";
		$sql .= "left join issbase on y52_inscr = q02_inscr ";
		$sql .= "left join cgm on z01_numcgm = y54_numcgm ";
		$sql .= "left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani ";
		$sql .= "left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti ";
		$sql .= "left join fiscalizacao.fis_procfiscalauto on y111_auto = y50_codauto ";
		$sql .= "left join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial ";
		$sql .= "left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial ";
		$sql .= "left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario ";
		$sql .= "left join db_usuarios  on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
		//$sql .= "left join fiscalizacao.fis_autousu on y56_id_usuario = db_usuarios.id_usuario and y56_codauto = y50_codauto ";		
		$sql .= "left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial ";
		$sql .= "left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
		$sql .= "left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario ";
		$sql .= "left join fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_autoandam where y58_codauto = y50_codauto) ";
		$sql .= "left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo ";
		$sql .= "left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam ";
		$sql .= "left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial ";

		if($fiscal){
			$sql .= " where ( (fis_grupotipoandamento.sequencial=3)) ";
		}else{
                        $sql .= " where (y39_codandam is null or (fis_grupotipoandamento.sequencial=3 or fis_grupotipoandamento.sequencial=11 or fis_grupotipoandamento.sequencial=14)) ";
		}
		$sql .= $where;
		$sql .= " ) as x inner join cgm on cgm.z01_numcgm = x.z01_numcgm ";
		$sql .= " where y50_instit = ".db_getsession('DB_instit')." and x.y50_setor=".db_getsession('DB_coddepto'). $andWhere;
        	db_lovrot($sql,12,"()","",$funcao_js);
	} else {
		if($pesquisa_chave!=null && $pesquisa_chave!="") {

			$andWhere = " and dl_Auto = ".$pesquisa_chave;	
		
         		$sql  = "select distinct dl_Auto, dl_identificacao, dl_codigo, z01_nome, tipo, y50_instit, y50_numbloco, y41_descr as dl_Andamento ";
			$sql .= "from (select y50_numbloco, y50_instit, y50_setor, y50_codauto  AS dl_Auto, y41_descr, ";
			$sql .= "case when q02_numcgm is not null then 'Inscrição' else (case when j01_numcgm is not null then 'Matrícula' ";
			$sql .= " else (case when y80_numcgm is not null then 'Sanitário' else (case when z01_numcgm is not null then 'Cgm' ";
			$sql .= " else (case when y30_codnoti is not null then 'Notificação' else 'Nenhum' end) end ) end) end) end as dl_identificacao, ";
			$sql .= "case when y52_inscr is not null then y52_inscr else (case when y53_matric is not null then y53_matric ";
			$sql .= " else (case when y55_codsani is not null then y55_codsani else (case when z01_numcgm is not null then z01_numcgm ";
			$sql .= " else (case when y51_codnoti is not null then y51_codnoti end) end) end) end) end as dl_codigo, ";
			$sql .= " case when q02_numcgm is not null then q02_numcgm else (case when j01_numcgm is not null then j01_numcgm  ";
			$sql .= " else (case when y80_numcgm is not null then y80_numcgm else (case when z01_numcgm is not null then z01_numcgm ";
			$sql .= " else q02_numcgm end) end) end) end as z01_numcgm, y27_descr as tipo from fiscalizacao.fis_auto ";
			$sql .= "left join fiscalizacao.fis_tipofiscaliza on y50_codtipo = y27_codtipo ";
			$sql .= "left join fiscalizacao.fis_autousu on y56_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto ";
			$sql .= "left join iptubase on j01_matric = y53_matric ";
			$sql .= "left join issbase on y52_inscr = q02_inscr ";
			$sql .= "left join cgm on z01_numcgm = y54_numcgm ";
			$sql .= "left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani ";
			$sql .= "left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_procfiscalauto on y111_auto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial ";
			$sql .= "left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial ";
			$sql .= "left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario ";
			$sql .= "left join db_usuarios  on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
			//$sql .= "left join fiscalizacao.fis_autousu on y56_id_usuario = db_usuarios.id_usuario and y56_codauto = y50_codauto ";
			$sql .= "left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial ";
			$sql .= "left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
			$sql .= "left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario ";
			$sql .= "left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti ";
			$sql .= "left join fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_autoandam where y58_codauto = y50_codauto) ";
			$sql .= "left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo ";
			$sql .= "left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam ";
			$sql .= "left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial ";	
	                if($fiscal){
                        $sql .= " where ( (fis_grupotipoandamento.sequencial=3)) ";
                }else{
                        $sql .= " where (y39_codandam is null or (fis_grupotipoandamento.sequencial=3 or fis_grupotipoandamento.sequencial=11 or fis_grupotipoandamento.sequencial=14				  )) ";
                }

		$sql .= $where;
			$sql .= " ) as x inner join cgm on cgm.z01_numcgm = x.z01_numcgm ";
			$sql .= " where y50_instit = ".db_getsession('DB_instit')." and x.y50_setor=".db_getsession('DB_coddepto'). $andWhere; 

			$result = $clauto->sql_record($sql);

			if($clauto->numrows!=0){
            			db_fieldsmemory($result,0);
            			echo "<script>".$funcao_js."('$z01_nome',false);</script>";
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

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
