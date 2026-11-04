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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_fis_procfiscal_classe.php"));
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clprocfiscal = new cl_fis_procfiscal;
$clprocfiscal->rotulo->label("y100_sequencial");
$clprocfiscal->rotulo->label("y100_coddepto");

//echo "tipo = $tipo valor = $valor";
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
            <td width="4%" align="right" nowrap title="<?=$Ty100_sequencial?>">
              <?=$Ly100_sequencial?>
            </td>
            <td width="96%" align="left" nowrap>
              <?php 
		          db_input("y100_sequencial",10,$Iy100_sequencial,true,"text",4,"","chave_y100_sequencial");
		          ?>
            </td>
          </tr>
          <!--<tr>
          	<td align="right" nowrap ><b>Trazer apenas registros ligados a origem:</b></td>
					  <td>
					  	<?php 
					  	  //$arr_origem = array("S"=>"Sim","N"=>"Não");
	              				  //db_select("origem",$arr_origem,true,2);
						?>
					  </td>
					</tr>
					 <tr>
					 	<td align="right" ><b>Considerar:</b></td>
					  <td>
					  	<?php 
					  	  //$arr_cons = array("A"=>"Aberto","E"=>"Encerrado","T"=>"Todos");
	              				  //db_select("considerar",$arr_cons,true,2);
						?>
					  </td>
					</tr>-->
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_procfiscal.hide();">
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
			$where=" where 1=1 ";
      if(!isset($pesquisa_chave)){
        if((isset($origem) and $origem=="S") or (!isset($origem) and (isset($tipo)) )){
	        $where .= " and $tipo = $valor ";
        }
				if((isset($considerar) and $considerar=="A") or  (!isset($considerar))){
					 $where .= " and aberto is null ";
				}
				if(isset($considerar) and $considerar=="E"){
					 $where .= " and aberto >1 ";
				}

        if(isset($chave_y100_sequencial) && (trim($chave_y100_sequencial)!="") ){
        	 $where .= " and y100_sequencial = $chave_y100_sequencial  ";

        }

				$sql = "

select y100_sequencial,y100_dtinicial, y101_numcgm,z01_nome ,y103_inscr as q02_inscr,y102_matric as j01_matric,y104_codsani as y80_codsani,
       depart_protocolo as db_depart_protocolo,descr_depart as db_descr_depart ,y100_coddepto as db_depart_atual ,
       case when aberto >1 then 'Encerrado'
            else 'Aberto'
       end as dl_situacao
   from( select distinct
                y100_sequencial,
                y100_dtinicial,
                y101_numcgm,
				        z01_nome,
                y103_inscr,
                y102_matric,
                y104_codsani,
				        y100_coddepto,
                (select p61_coddepto
                   from protprocesso
                        inner join procandam  on p58_codandam = p61_codandam
                        inner join fiscalizacao.fis_procfiscalprot on y105_protprocesso = procandam.p61_codproc
                  where fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial

                ) as depart_protocolo,
								 (select descrdepto
                   from protprocesso
                        inner join procandam  on p58_codandam = p61_codandam
                        inner join fiscalizacao.fis_procfiscalprot on y105_protprocesso = procandam.p61_codproc
                        inner join db_depart      on coddepto          = p61_coddepto
                  where fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial
                ) as descr_depart,
                (select count(*)
                   from fiscalizacao.fis_procfiscalfases as qtd
                  where qtd.y108_procfiscal = fis_procfiscalfases.y108_procfiscal
                  group by y108_procfiscal having count(*) > 1 ) as aberto
           from fiscalizacao.fis_procfiscal
		left  join fiscalizacao.fis_procfiscalfases  on y108_procfiscal = y100_sequencial
		inner join fiscalizacao.fis_procfiscalcgm    on y101_procfiscal = y100_sequencial
		inner join cgm              on y101_numcgm     = z01_numcgm
		left  join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial
		left  join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial
		left  join fiscalizacao.fis_procfiscalsani   on y104_procfiscal = y100_sequencial
    inner join fiscalizacao.fis_procfiscalfiscais on fis_procfiscal.y100_sequencial = fis_procfiscalfiscais.y106_procfiscal 
    inner join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
    inner join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial
    left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = y106_cadfiscais and fis_datalimitefiscal.data > '$dataAtual'
          where y100_coddepto = ".db_getsession("DB_coddepto")."
            and y100_instit   =  ".db_getsession("DB_instit")." 
            and fis_processofiscalativo.ativo = 't' 
            and not exists (select 1 from fiscalizacao.fis_processoprorrogacaofinalizacao where fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial) 
            or exists (select 1 from fiscalizacao.fis_processoprorrogacaofinalizacao inner join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  where fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial and fis_processoprorrogacaofinalizacao.data_fim > '".date('Y-m-d')."')
            and fis_cadfiscais.id_usuario = ".db_getsession('DB_id_usuario').") as x
					$where ";

        $repassa = array();
        if(isset($chave_y100_coddepto)){
          $repassa = array("chave_y100_sequencial"=>$chave_y100_sequencial,"chave_y100_coddepto"=>$chave_y100_coddepto);
        }
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa,false);

      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){

					$sql = "
          SELECT y100_sequencial,
          y100_dtinicial,
          y101_numcgm,
          z01_nome,
          y103_inscr,
          y102_matric,
          y104_codsani,
          depart_protocolo AS db_depart_protocolo,
          descr_depart AS db_descr_depart,
          y100_coddepto AS db_depart_atual,
          CASE
          WHEN aberto >1 THEN 'Encerrado'
          ELSE 'Aberto'
          END AS dl_situacao,

          (SELECT p58_numero || '/' || p58_ano AS p58_numero
          FROM fiscalizacao.fis_procfiscalprot
          INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
          WHERE y105_procfiscal = y100_sequencial) AS p58_numero,

          (SELECT p58_codproc
          FROM fiscalizacao.fis_procfiscalprot
          INNER JOIN protprocesso ON y105_protprocesso = p58_codproc
          WHERE y105_procfiscal = y100_sequencial) AS DB_p58_codproc
          FROM
          (SELECT DISTINCT y100_sequencial,
             y100_dtinicial,
                y101_numcgm,
                   z01_nome,
                      y103_inscr,
                         y102_matric,
                            y104_codsani,
                               y100_coddepto,

                               (SELECT p61_coddepto
                               FROM protprocesso
                               INNER JOIN procandam ON p58_codandam = p61_codandam
                               INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
                               WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS depart_protocolo,

                               (SELECT descrdepto
                               FROM protprocesso
                               INNER JOIN procandam ON p58_codandam = p61_codandam
                               INNER JOIN fiscalizacao.fis_procfiscalprot ON y105_protprocesso = procandam.p61_codproc
                               INNER JOIN db_depart ON coddepto = p61_coddepto
                               WHERE fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial) AS descr_depart,

                               (SELECT count(*)
                               FROM fiscalizacao.fis_procfiscalfases AS qtd
                               WHERE qtd.y108_procfiscal = fis_procfiscalfases.y108_procfiscal
                               GROUP BY y108_procfiscal
                               HAVING count(*) > 1) AS aberto
                               FROM fiscalizacao.fis_procfiscal
                               LEFT JOIN fiscalizacao.fis_procfiscalfases ON y108_procfiscal = y100_sequencial
                               INNER JOIN fiscalizacao.fis_procfiscalcgm ON y101_procfiscal = y100_sequencial
                               INNER JOIN cgm ON y101_numcgm = z01_numcgm
                               INNER JOIN fiscalizacao.fis_procfiscalfiscais ON y106_procfiscal = y100_sequencial
                               INNER JOIN fiscalizacao.fis_cadfiscais ON fis_procfiscalfiscais.y106_cadfiscais = id_usuario
                               INNER JOIN fiscalizacao.fis_processofiscalativo ON fis_processofiscalativo.processo_fiscal = y100_sequencial
                               AND fis_processofiscalativo.fiscal = fis_procfiscalfiscais.y106_cadfiscais
                               LEFT JOIN fiscalizacao.fis_datalimitefiscal ON fis_datalimitefiscal.fiscal = fis_procfiscalfiscais.y106_cadfiscais
                               LEFT JOIN fiscalizacao.fis_procfiscalmatric ON y102_procfiscal = y100_sequencial
                               LEFT JOIN fiscalizacao.fis_procfiscalinscr ON y103_procfiscal = y100_sequencial
                               LEFT JOIN fiscalizacao.fis_procfiscalsani ON y104_procfiscal = y100_sequencial
                               WHERE y100_coddepto =  ".db_getsession("DB_coddepto")."
                               AND fis_cadfiscais.id_usuario = ".db_getsession('DB_id_usuario')."
                               AND (fis_datalimitefiscal.data IS NULL
                               OR fis_datalimitefiscal.data > '$dataAtual')
                               AND NOT EXISTS
                               (SELECT 1
                               FROM fiscalizacao.fis_processoprorrogacaofinalizacao
                               WHERE fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial
                               AND fis_processoprorrogacaofinalizacao.situacao = 2
                               ORDER BY data_abertura DESC LIMIT 1)
                               AND (y100_dtfinal >= CURRENT_DATE
                               OR y100_dtfinal IS NULL)
                               AND y100_instit =  ".db_getsession("DB_instit")."
                               and y100_sequencial = $pesquisa_chave
                               ) AS x
                               order by y100_sequencial";
                               //           echo "$sql" ;
         //   echo "$sql"	; die;
					$result = pg_query($sql);
					$linhas = pg_num_rows($result);

          if($linhas!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$z01_nome',false,'$db_depart_protocolo','$db_descr_depart','$db_depart_atual', '$z01_numcgm','$j01_matric','$q02_inscr','$y80_codsani','$y30_codnoti');</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }
      //die($sql);
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
