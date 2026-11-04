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
db_postmemory($HTTP_GET_VARS);
//parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
$clprocfiscal = new cl_fis_procfiscal;
$clprocfiscal->rotulo->label("y100_sequencial");
$clprocfiscal->rotulo->label("y100_coddepto");

$usuarioLogado = db_getsession("DB_id_usuario");
$userSql = "select * from db_usuarios where db_usuarios.id_usuario in (select * from fiscalizacao.fis_cadgestorfiscal) and db_usuarios.id_usuario = {$usuarioLogado}";

$resultUser = pg_query($userSql);
$linhasUser = pg_num_rows($resultUser);
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
          <tr> 
            <td width="4%" align="right" nowrap title="<?=$Ty100_coddepto?>">
              <?=$Ly100_coddepto?>
            </td>
            <td width="96%" align="left" nowrap> 
              <?php 
		       db_input("y100_coddepto",10,$Iy100_coddepto,true,"text",4,"","chave_y100_coddepto");
		       ?>
            </td>
          </tr>
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
			$where="";
			if(isset($cgm) and $cgm!=""){
	      $where = " and y101_numcgm = $cgm ";
      }
			if(isset($matric) and $matric!= ""){
				$where = " and y102_matric = $matric ";
			}
			if(isset($inscr) and $inscr!= ""){
				$where = " and y103_inscr = $inscr ";
			}			
			if(isset($processo) and $processo!= ""){
        $aExplodeNumero = explode("/", $processo);
        if (count($aExplodeNumero) > 1) {
          $whereprot = " and p58_numero = '".$aExplodeNumero[0]."' and p58_ano = ".$aExplodeNumero[1];
        } else {
          $whereprot = " and p58_numero = '".$aExplodeNumero[0]."' and p58_ano = ".db_getsession("DB_anousu");
        }
       $where = " and y105_protprocesso in (select p58_codproc from protprocesso where 1=1 $whereprot) ";
	    }	
	    if (isset($datainicial) && $datainicial != '') {
	    	$where = " and y100_dtinicial >= '".implode('-',array_reverse(explode('/',$datainicial)))."'"; 
	    }
	    if (isset($datafinal) && $datafinal != '') {
        $where = " and y100_dtfinal <= '".implode('-',array_reverse(explode('/',$datafinal)))."'";
      }
	    
			
      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_fis_procfiscal.php")==true){
             include(modification("funcoes/db_func_fis_procfiscal.php"));
           }else{
           $campos = "fis_procfiscal.*";
           }
        }
        if(isset($chave_y100_sequencial) && (trim($chave_y100_sequencial)!="") ){
        	 $where .= " and y100_sequencial = $chave_y100_sequencial  ";
	        
        }else if(isset($chave_y100_coddepto) && (trim($chave_y100_coddepto)!="") ){
	         $where .= " and y100_coddepto like '$chave_y100_coddepto%'  ";
        }
              
        $sql  = "select distinct y100_sequencial,y100_dtinicial,y100_dtfinal,y33_descricao,descrdepto, z01_nome,";
        $sql .= " (SELECT 
    CASE WHEN fis_processoprorrogacaofinalizacao.data_fim IS NOT NULL AND fis_processoprorrogacaofinalizacao.situacao = 2 THEN 'Sim' ELSE 'Não' END AS finalizado
    FROM fiscalizacao.fis_processoprorrogacaofinalizacao 
    WHERE processo_fiscal = y100_sequencial ORDER BY fis_processoprorrogacaofinalizacao.data_abertura DESC LIMIT 1
    ) AS dl_Finalizado";
        $sql .= " from fiscalizacao.fis_procfiscal ";
        $sql .= " inner join db_depart         on db_depart.coddepto = fis_procfiscal.y100_coddepto ";
        $sql .= " inner join fiscalizacao.fis_procfiscalcgm     on y101_procfiscal    = y100_sequencial";
        $sql .= " inner join cgm               on cgm.z01_numcgm     = y101_numcgm";
        $sql .= " inner join fiscalizacao.fis_procfiscalcadtipo on y100_procfiscalcadtipo = y33_sequencial";
        $sql .= " left join fiscalizacao.fis_procfiscalfases   on y108_procfiscal    = y100_sequencial";
        $sql .= " left join fiscalizacao.fis_procfiscalmatric  on y102_procfiscal    = y100_sequencial ";
        $sql .= " left join fiscalizacao.fis_procfiscalinscr   on y103_procfiscal    = y100_sequencial";
        $sql .= " left join fiscalizacao.fis_procfiscalsani    on y104_procfiscal    = y100_sequencial";
        $sql .= " left join fiscalizacao.fis_procfiscalprot    on y105_procfiscal    = y100_sequencial";
        $sql .= " left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial";
        $sql .= " left join fiscalizacao.fis_cadfiscais         on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario";
        $sql .= " left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario";
        $sql .= " left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial ";
        $sql .= " left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
        $sql .= " left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'";
        $sql .= " where y100_coddepto= ".db_getsession("DB_coddepto");
        if($linhasUser == 0){
            $sql .= " and ((fis_processoprorrogacaofinalizacao.sequencial in (select 1 from fiscalizacao.fis_processoprorrogacaofinalizacao 
                      where processo_fiscal = y100_sequencial and fis_processoprorrogacaofinalizacao.situacao = 2 order by 1 desc) ";
            $sql .= " ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario");
            $sql .= " and fis_processofiscalativo.ativo = 't'))";
          }
        $sql .= " $where";
        $sql .= " order by y100_sequencial desc";
				$repassa = array();
        //die($sql);
	if(isset($chave_y100_coddepto)){
          $repassa = array("chave_y100_sequencial"=>$chave_y100_sequencial,"chave_y100_coddepto"=>$chave_y100_coddepto);
        }
        
        db_lovrot($sql,15,"()","",$funcao_js,"","NoMe",$repassa);
      }else{
        if($pesquisa_chave!=null && $pesquisa_chave!=""){
        	
          $sql  = "select distinct y100_sequencial,y100_dtinicial,y100_dtfinal,y33_descricao,descrdepto, z01_nome,";
          $sql .= " (SELECT 
    CASE WHEN fis_processoprorrogacaofinalizacao.data_fim IS NOT NULL AND fis_processoprorrogacaofinalizacao.situacao = 2 THEN 'Sim' ELSE 'Não' END AS finalizado
    FROM fiscalizacao.fis_processoprorrogacaofinalizacao 
    WHERE processo_fiscal = y100_sequencial ORDER BY fis_processoprorrogacaofinalizacao.data_abertura DESC LIMIT 1
    ) AS dl_Finalizado";
          $sql .= " from fiscalizacao.fis_procfiscal";
          $sql .= " inner join db_depart       on db_depart.coddepto = fis_procfiscal.y100_coddepto ";
          $sql .= " inner join fiscalizacao.fis_procfiscalcgm   on y101_procfiscal    = y100_sequencial";
          $sql .= " inner join cgm             on cgm.z01_numcgm     = y101_numcgm";
          $sql .= " inner join fiscalizacao.fis_procfiscalcadtipo on y100_procfiscalcadtipo = y33_sequencial";
          $sql .= " left  join fiscalizacao.fis_procfiscalfases on y108_procfiscal    = y100_sequencial";
          $sql .= " left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial";
          $sql .= " left join fiscalizacao.fis_cadfiscais         on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario";
          $sql .= " left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario";
          $sql .= " left join fiscalizacao.fis_processofiscalativo on processo_fiscal = y100_sequencial ";
          $sql .= " left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
          $sql .= " left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual' ";
          $sql .= " where y100_coddepto= ".db_getsession("DB_coddepto");
         if($linhasUser == 0){
            $sql .= " and ((fis_processoprorrogacaofinalizacao.sequencial in (select 1 from fiscalizacao.fis_processoprorrogacaofinalizacao 
                      where processo_fiscal = y100_sequencial and fis_processoprorrogacaofinalizacao.situacao = 2 order by 1 desc) ";
            $sql .= " ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario");
            $sql .= " and fis_processofiscalativo.ativo = 't'))";
          }
          $sql .= " $where";
          $sql .= " and y100_sequencial = $pesquisa_chave";
          $sql .= " order by y100_sequencial desc";

					$result = pg_query($sql);
					$linhas = pg_num_rows($result);
					
					
          if($linhas!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$z01_nome',false);</script>";
          }else{
	         echo "<script>".$funcao_js."('Chave(".$pesquisa_chave.") não Encontrado',true);</script>";
          }
        }else{
	       echo "<script>".$funcao_js."('',false);</script>";
        }
      }

      // die($sql);
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


<?php 

$rs = pg_query($sql);
$linhas = pg_num_rows($rs);

if($linhas==0){
	if(isset($cgm) and $cgm!=""){
	  echo " <script> parent.js_limpa('CGM',$cgm); </script> ";
  }
	if(isset($matric) and $matric!= ""){
		echo " <script> parent.js_limpa('MATRICULA',$matric); </script> ";		
	}
	if(isset($inscr) and $inscr!= ""){
    echo " <script> parent.js_limpa('INSCRIÇÃO',$inscr); </script> ";	
	}	
	if(isset($processo) and $processo!= ""){
    echo " <script> parent.js_limpa('PROCESSO','$processo'); </script> ";	
	}	
}
?>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
