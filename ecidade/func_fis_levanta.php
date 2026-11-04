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
require_once(modification("classes/db_fis_levanta_classe.php"));

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$cllevanta = new cl_fis_levanta;
$clrotulo  = new rotulocampo();
$cllevanta->rotulo->label("y60_codlev");
$clrotulo->label("q02_inscr");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body>
<table height="100%" border="0"align="center">
  <tr>
    <td height="63" align="center" valign="top">

        <table width="80%" border="0" align="center">
       <form name="form1" method="post" action="" >
          <tr>
            <td width="30%" align="right" nowrap title="<?=$Ty60_codlev?>">
              <?=$Ly60_codlev?>
            </td>
            <td align="left" nowrap>
              <?php 
                db_input("y60_codlev",10,$Iy60_codlev,true,"text",4,"","chave_y60_codlev");
              ?>
            </td>
          </tr>
   <tr>
     <td title="<?=$Tq02_inscr?>" align="right">
    <?php 
     db_ancora($Lq02_inscr,' js_inscr(true); ',1);
    ?>
     </td>
     <td nowrap>
    <?php 
     db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
    db_input('z01_nome',30,0,true,'text',3,"","z01_nomeinscr");
    ?>
     </td>
   </tr>
   <tr>
    <td title="<?=$Tz01_numcgm?>" nowrap align="right">
    <?php 
     db_ancora($Lz01_nome,' js_cgm(true); ',1);
    ?>
     </td>
     <td nowrap>
    <?php 
     db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
     db_input('z01_nome',30,0,true,'text',3,"","z01_nomecgm");
    ?>
     </td>
   </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar" >
              <input name="limpar" type="reset" id="limpar" value="Limpar" >
              <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe.hide();"/>
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
      $userSql = "select * from db_usuarios where db_usuarios.id_usuario in (select * from fiscalizacao.fis_cadgestorfiscal) and db_usuarios.id_usuario = ".db_getsession("DB_id_usuario");

      $resultUser = pg_query($userSql);
      $linhasUser = pg_num_rows($resultUser);

      if(!isset($pesquisa_chave)){
        if(isset($campos)==false){
           if(file_exists("funcoes/db_func_fis_levanta.php")==true){
             include(modification("funcoes/db_func_fis_levanta.php"));
           }else{
           $campos = "fis_levanta.*";
           }
        }
        if(isset($chave_y60_codlev) && (trim($chave_y60_codlev)!="")){
          #echo '1 if' . "<br>";
          #$sql = $cllevanta->sql_query_pesquisa(null,"*",null,"y60_codlev=$chave_y60_codlev");
          #$sql = "select distinct y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, y60_data,y60_importado from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y117_levanta from fiscalizacao.fis_levanta left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev left join issbase on y62_inscr = q02_inscr left join cgm empresa on q02_numcgm = empresa.z01_numcgm left join cgm on y93_numcgm = cgm.z01_numcgm left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta ) as x where y60_importado = false and y60_codlev=$chave_y60_codlev AND y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta)"; 
          // $sql = "select distinct y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, y60_data,y60_importado from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y117_levanta from fiscalizacao.fis_levanta left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev left join issbase on y62_inscr = q02_inscr left join cgm empresa on q02_numcgm = empresa.z01_numcgm left join cgm on y93_numcgm = cgm.z01_numcgm left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta inner join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev inner join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal inner join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial inner join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario left join fiscalizacao.fis_datalimitefiscal on fiscal = id_usuario) as x where y60_importado = false and y60_codlev=$chave_y60_codlev AND y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) and fis_datalimitefiscal.data > '$dataAtual' order by y60_codlev desc"; 

           $sql = "select distinct y60_codlev, 
          case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, 
          case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, 
          case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, 
          y60_data,
          y60_importado,
          y112_procfiscal 
          from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm,y112_procfiscal 
            from fiscalizacao.fis_levanta 
            left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev 
            left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev 
            left join issbase on y62_inscr = q02_inscr 
            left join cgm empresa on q02_numcgm = empresa.z01_numcgm 
            left join cgm on y93_numcgm = cgm.z01_numcgm 
            left join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev
            left join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal
            left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
            left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
            left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
            left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'";
            
           if($linhasUser == 0){
            $sql .= "      where 

                  (CASE WHEN y112_procfiscal is not null then ((fis_processoprorrogacaofinalizacao.situacao = 2 ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario")."
                  and fis_processofiscalativo.ativo = 't')) else 1 =1 end)";
            }
            $sql .="            ) as x where x.y60_codlev = $chave_y60_codlev  AND x.y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) order by y60_codlev desc ";

        }else if(isset($q02_inscr) && (trim($q02_inscr)!="")){
          #echo '2 if' . "<br>";
          #$sql = $cllevanta->sql_query_pesquisa(null,"*",null,"x.y62_inscr=$q02_inscr");
          #$sql = "select y60_codlev,y60_data,y62_inscr,y60_importado, y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem from fiscalizacao.fis_levanta inner join fiscalizacao.fis_levinscr on y60_codlev = y62_codlev left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta where y117_levanta is null and y60_importado = false and y62_inscr = $q02_inscr";
          #$sql = "select distinct y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, y60_data,y60_importado from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y117_levanta from fiscalizacao.fis_levanta left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev left join issbase on y62_inscr = q02_inscr left join cgm empresa on q02_numcgm = empresa.z01_numcgm left join cgm on y93_numcgm = cgm.z01_numcgm left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta ) as x where y60_importado = false and x.y62_inscr=$q02_inscr AND y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta)"; 
          // $sql = "select distinct y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, y60_data,y60_importado from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y117_levanta from fiscalizacao.fis_levanta left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev left join issbase on y62_inscr = q02_inscr left join cgm empresa on q02_numcgm = empresa.z01_numcgm left join cgm on y93_numcgm = cgm.z01_numcgm left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta inner join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev inner join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal inner join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial inner join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario left join fiscalizacao.fis_datalimitefiscal on fiscal = id_usuario and (fis_datalimitefiscal.data >= '$dataAtual' or fis_datalimitefiscal.data is null )) as x where y60_importado = false and x.y62_inscr=$q02_inscr AND y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) order by y60_codlev desc "; 
          $sql = "select distinct y60_codlev, 
          case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, 
          case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, 
          case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, 
          y60_data,
          y60_importado,
          y112_procfiscal 
          from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm,y112_procfiscal 
            from fiscalizacao.fis_levanta 
            left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev 
            left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev 
            left join issbase on y62_inscr = q02_inscr 
            left join cgm empresa on q02_numcgm = empresa.z01_numcgm 
            left join cgm on y93_numcgm = cgm.z01_numcgm 
            left join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev
            left join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal
            left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
            left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
            left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
            left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'";
            
           if($linhasUser == 0){
            $sql .= "      where 

                  (CASE WHEN y112_procfiscal is not null then ((fis_processoprorrogacaofinalizacao.situacao = 2 ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario")."
                  and fis_processofiscalativo.ativo = 't')) else 1 =1 end) and  y62_inscr = $q02_inscr ";
            }
            $sql .="            ) as x where x.y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) order by y60_codlev desc ";
        }else if(isset($z01_numcgm) && (trim($z01_numcgm)!="")){
          #echo '3 if' . "<br>";
          #$sql = $cllevanta->sql_query_pesquisa(null,"*",null,"x.y93_numcgm=$z01_numcgm ");
          #$sql = "select y60_codlev,y60_data,y62_inscr,y60_importado from fiscalizacao.fis_levanta inner join fiscalizacao.fis_levinscr on y60_codlev = y62_codlev left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta where y117_levanta is null and y60_importado = false and y62_inscr = $q02_inscr";
          #$sql = "select distinct y60_codlev, case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem,y60_data,y60_importado from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm, y117_levanta from fiscalizacao.fis_levanta left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev left join issbase on y62_inscr = q02_inscr left join cgm empresa on q02_numcgm = empresa.z01_numcgm left join cgm on y93_numcgm = cgm.z01_numcgm left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta ) as x where y60_importado = false and x.y93_numcgm=$z01_numcgm AND y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta)"; 
         $sql = "select distinct y60_codlev, 
          case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, 
          case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, 
          case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, 
          y60_data,
          y60_importado,
          y112_procfiscal 
          from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm,y112_procfiscal 
            from fiscalizacao.fis_levanta 
            left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev 
            left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev 
            left join issbase on y62_inscr = q02_inscr 
            left join cgm empresa on q02_numcgm = empresa.z01_numcgm 
            left join cgm on y93_numcgm = cgm.z01_numcgm 
            left join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev
            left join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal
            left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
            left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
            left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
            left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'";
            
           if($linhasUser == 0){
            $sql .= "      where 

                  (CASE WHEN y112_procfiscal is not null then ((fis_processoprorrogacaofinalizacao.situacao = 2 ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario")."
                  and fis_processofiscalativo.ativo = 't')) else 1 =1 end) and  y93_numcgm = $z01_numcgm ";
            }
            $sql .="            ) as x where x.y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) order by y60_codlev desc ";


        }else{
          #echo 'else' . "<br>";
          #$sql = $cllevanta->sql_query_pesquisa();
          #$sql = "select y60_codlev,y60_data,y62_inscr,y60_importado from fiscalizacao.fis_levanta inner join fiscalizacao.fis_levinscr on y60_codlev = y62_codlev left join fiscalizacao.fis_autolevanta on y60_codlev = y117_levanta where y117_levanta is null and y60_importado = false and y62_inscr = $q02_inscr";
          
          $sql = "select distinct y60_codlev, 
          case when y62_inscr is null then 'CGM' else 'INSCRIÇÃO' end as DBtxttipo_origem, 
          case when y62_inscr is null then y93_numcgm else y62_inscr end as DBtxtcod_origem, 
          case when y62_inscr is null then nome_cgm else nome_empresa end as DBtxtnome_origem, 
          y60_data,
          y60_importado,
          y112_procfiscal 
          from ( select fis_levanta.*,y62_inscr, y93_numcgm,empresa.z01_nome as nome_empresa,cgm.z01_nome as nome_cgm,y112_procfiscal 
            from fiscalizacao.fis_levanta 
            left join fiscalizacao.fis_levinscr on y62_codlev = y60_codlev 
            left join fiscalizacao.fis_levcgm on y93_codlev = y60_codlev 
            left join issbase on y62_inscr = q02_inscr 
            left join cgm empresa on q02_numcgm = empresa.z01_numcgm 
            left join cgm on y93_numcgm = cgm.z01_numcgm 
            left join fiscalizacao.fis_procfiscallevanta on y112_levanta = y60_codlev
            left join fiscalizacao.fis_procfiscal on y100_sequencial = y112_procfiscal
            left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
            left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
            left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
            left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
            left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'";
            
     if($linhasUser == 0){
      $sql .= "      where 

            (CASE WHEN y112_procfiscal is not null then ((fis_processoprorrogacaofinalizacao.situacao = 2 ) or (fis_processofiscalativo.fiscal = ".db_getsession("DB_id_usuario")."
            and fis_processofiscalativo.ativo = 't')) else 1 =1 end)";
      }
      $sql .="            ) as x where x.y60_codlev NOT IN(SELECT y117_levanta FROM fiscalizacao.fis_autolevanta) order by y60_codlev desc ";
        
        }
        
        # retorna o select
        db_lovrot($sql,15,"()","",$funcao_js);
      }else{

        if($pesquisa_chave!=null && $pesquisa_chave!=""){

          $sWhere = "";
          if(isset($q02_inscr) && (trim($q02_inscr)!="")){
            $sWhere .= " x.y62_inscr=$q02_inscr ";
          }
          if(isset($z01_numcgm) && (trim($z01_numcgm)!="")){

            $sWhere .= (empty($sWhere)) ? "" : " and ";
            $sWhere .= " x.y93_numcgm=$z01_numcgm ";
          }

          $sWhere .= (empty($sWhere)) ? "" : " and ";
          $sWhere .= " y60_codlev=$pesquisa_chave ";

          $result = $cllevanta->sql_record($cllevanta->sql_query_pesquisa(null,"*",null,$sWhere));
          if($cllevanta->numrows!=0){
            db_fieldsmemory($result,0);
            echo "<script>".$funcao_js."('$dbtxtnome_origem',false);</script>";
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

function js_limparFormulario() {
  document.getElementById('limpar').click();;
}

function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true,0,0,780,430);
  }else{
    if(inscr!=""){
      js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
    }else{
      document.form1.z01_nomeinscr.value = "";
    }
  }
}
function js_mostrainscr(chave1,chave2){
  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;
  db_iframe_issbase.hide();
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nomeinscr.value = chave;
  if(erro==true){
    document.form1.q02_inscr.focus();
    document.form1.q02_inscr.value = '';
  }
}

function js_cgm(mostra){
  var cgm=document.form1.z01_numcgm.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_numcgm','func_nome.php?funcao_js=parent.js_mostracgm|z01_numcgm|z01_nome','Pesquisa',true,0,0,780,430);
  }else{
    if(cgm!=""){
      js_OpenJanelaIframe('','db_iframe_numcgm','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
    }else{
      document.form1.z01_nomecgm.value = '';
    }
  }
}
function js_mostracgm(chave1,chave2){
  document.form1.z01_numcgm.value = chave1;
  document.form1.z01_nomecgm.value = chave2;
  db_iframe_numcgm.hide();
}
function js_mostracgm1(erro,chave){
  document.form1.z01_nomecgm.value = chave;
  if(erro==true){
    document.form1.z01_numcgm.focus();
    document.form1.z01_numcgm.value = '';
  }
}

</script>

<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
