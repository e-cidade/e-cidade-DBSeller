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
include modification("classes/db_fis_lancamento_classe.php");

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$cllancamento = new cl_fis_lancamento;
$cllancamento->rotulo->label("nl01_codlanc");
$cllancamento->rotulo->label("y50_nome");

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
            <td width="4%" align="right" nowrap title="<?=$Tnl01_codlanc?>"><b>Notificação de Lançamento</b></td>
            <td width="96%" align="left" nowrap>
              <?php  db_input("nl01_codlanc",10,$Inl01_codlanc,true,"text",4,"","chave_nl01_codlanc"); ?>
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

  
  $where2 = "where 1=1";
  if( $iGestor > 0 ){
    $where2 .= " AND CASE when y100_sequencial is not null then";
  } else {
   $where2 .= " and";
  } 
  $where2 .= ' (case when fis_grupotipoandamento.sequencial = 21 then ' ;
  $where2 .= ' db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
  $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
  $where2 .= " and fis_processofiscalativo.ativo = 't'";
  $where2 .= " when  nl01_codlanc not in (select nl19_codlanc from fiscalizacao.fis_lancandam) then";
  $where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
  $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual')";
  $where2 .= " and fis_processofiscalativo.ativo = 't'";
  $where2 .= " else 1=1 end ) ";
  
  if( $iGestor > 0 ){
    $where2 .= " else 1=1 end";
  }
        if(!isset($pesquisa_chave)){

    $where = "";
    if (isset($db_opcao) && ($db_opcao == 3 || $db_opcao == 33)) {
      $where = " and not exists (select 1 from fiscalizacao.fis_lancamentonumpre where nl16_codlanc = dl_Notificacao_Lancamento) ";
    }elseif(isset($baixa)){
      $where = " and dl_Notificacao_Lancamento not in (select y59_codlancamento from lancamentotipo inner join lancamentotipobaixa on y86_codlancamentotipo = y59_codigo) ";
    }

    if(isset($cgf)){
      if(isset($chave_nl01_codlanc) && (trim($chave_nl01_codlanc)!="") ){
          $sql = $cllancamento->sql_query_busca($chave_nl01_codlanc," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$chave_nl01_codlanc ".$where, $where2);
        }elseif(isset($chave_q02_inscr) && (trim($chave_q02_inscr)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Inscrição' and dl_codigo=$chave_q02_inscr ".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_j01_matric) && (trim($chave_j01_matric)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Matrícula' and dl_codigo=$chave_j01_matric ".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_z01_numcgm) && (trim($chave_z01_numcgm)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Cgm' and dl_codigo=$chave_z01_numcgm ".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y80_codsani) && (trim($chave_y80_codsani)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Sanitário' and dl_codigo=$chave_y80_codsani ".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y30_codnoti) && (trim($chave_y30_codnoti)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Notificação' and dl_codigo=$chave_y30_codnoti ".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y50_numbloco) && (trim($chave_y50_numbloco)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and x.y50_numbloco = '$chave_y50_numbloco'".$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }else{
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit').$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }  
    }else if(isset($fislancamento)){
        
        if(isset($retifica) || isset($baixa)){
          $andamento = 'AND y39_codtipo = 0 order by dl_Notificacao_Lancamento desc';
        }else{
          $andamento = 'AND (y39_codtipo is null or y39_codtipo = 2) order by dl_Notificacao_Lancamento desc';
        }
        
        if(isset($chave_nl01_codlanc) && (trim($chave_nl01_codlanc)!="") ){

          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial 
      $where2 
                    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_Notificacao_Lancamento=$chave_nl01_codlanc " ;
        }elseif(isset($chave_q02_inscr) && (trim($chave_q02_inscr)!="") ){
           $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial 
                  $where2        
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_identificacao='Inscrição' and dl_codigo=$chave_q02_inscr order by dl_Notificacao_Lancamento desc " ;
        }elseif(isset($chave_j01_matric) && (trim($chave_j01_matric)!="") ){                              
           $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
                  $where2        
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_identificacao='Matrícula' and dl_codigo=$chave_j01_matric order by dl_Notificacao_Lancamento desc " ;
        }elseif(isset($chave_z01_numcgm) && (trim($chave_z01_numcgm)!="") ){
          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
                  $where2      
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_identificacao='Cgm' and dl_codigo=$chave_z01_numcgm order by dl_Notificacao_Lancamento desc " ;
        }elseif(isset($chave_y80_codsani) && (trim($chave_y80_codsani)!="")){
          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial 
                  $where2        
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_identificacao='Sanitário' and dl_codigo=$chave_y80_codsani order by dl_Notificacao_Lancamento desc " ;
        }elseif(isset($chave_y30_codnoti) && (trim($chave_y30_codnoti)!="")){
        
          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
                  $where2        
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    and dl_identificacao='Notificação' and dl_codigo=$chave_y30_codnoti order by dl_Notificacao_Lancamento desc " ;
        }elseif(isset($chave_y50_numbloco) && (trim($chave_y50_numbloco)!="")){
         
          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                            y27_descr AS tipo
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
         LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
                  $where2        
    ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')."
                    x.y50_numbloco = '$chave_y50_numbloco order by dl_Notificacao_Lancamento desc" ;
        }else{          
          $sql = "SELECT DISTINCT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
                         y41_descr as dl_Andamento,
                         dl_grupo,
                         y111_procfiscal as dl_Processo_Fiscal
                  FROM
                    (SELECT y50_numbloco,
                      fis_grupotipoandamento.sequencial as dl_grupo,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
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
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti
                     LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
         LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial 
      $where2
                 ) AS x
                  INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                  WHERE nl01_instit = ".db_getsession('DB_instit')."
                    AND x.nl01_setor=".db_getsession('DB_coddepto')." order by dl_Notificacao_Lancamento desc"; 
        }
      }else{
        if(isset($chave_nl01_codlanc) && (trim($chave_nl01_codlanc)!="") ){
          $sql = $cllancamento->sql_query_busca($chave_nl01_codlanc," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$chave_nl01_codlanc and  x.nl01_setor = ".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_q02_inscr) && (trim($chave_q02_inscr)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Inscrição' and dl_codigo=$chave_q02_inscr and  x.nl01_setor = ".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_j01_matric) && (trim($chave_j01_matric)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Matricula' and dl_codigo=$chave_j01_matric and  x.nl01_setor = ".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_z01_numcgm) && (trim($chave_z01_numcgm)!="") ){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Cgm' and dl_codigo=$chave_z01_numcgm and  x.nl01_setor = ".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y80_codsani) && (trim($chave_y80_codsani)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Sanitario' and dl_codigo=$chave_y80_codsani and  x.nl01_setor = ".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y30_codnoti) && (trim($chave_y30_codnoti)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_identificacao='Notificacao' and dl_codigo=$chave_y30_codnoti and  x.nl01_setor=".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_y50_numbloco) && (trim($chave_y50_numbloco)!="")){
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and x.y50_numbloco = '$chave_y50_numbloco' and  x.nl01_setor=".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_procfiscal) && (trim($chave_procfiscal)!="")){
               $sql = $cllancamento->sql_query_busca_procfis(null," nl01_instit = ".db_getsession('DB_instit')." and x.nl01_setor=".db_getsession("DB_coddepto").$where.' and dl_Processo_Fiscal = '.$chave_procfiscal.' order by dl_Notificacao_Lancamento desc', $where2);
        }elseif(isset($chave_procfiscal) && (trim($chave_procfiscal)=="")){
               $sql = $cllancamento->sql_query_busca_procfis(null," nl01_instit = ".db_getsession('DB_instit')." and x.nl01_setor=".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);
        }else{
          $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and x.nl01_setor=".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc' , $where2);
        }
      }  

        db_lovrot($sql,12,"()","",$funcao_js);
  
  }else{
        
  if($pesquisa_chave!=null && $pesquisa_chave!=""){
          if(isset($baixa)){
            $where = " and dl_Notificacao_Lancamento not in (select y59_codlancamento from lancamentotipo inner join lancamentotipobaixa on y86_codlancamentotipo = y59_codigo)";
           }else{
            $where = "";
           }
          if(isset($fislancamento)){

              $sql = "SELECT dl_Notificacao_Lancamento,
                         dl_identificacao,
                         dl_codigo,
                         z01_nome,
                         tipo,
                         nl01_instit as dl_instit,
                         y50_numbloco,
      dl_Andamento
                  FROM
                    (SELECT y50_numbloco,
                            nl01_instit,
                            nl01_setor,
                            nl01_codlanc AS dl_Notificacao_Lancamento,
                            y41_descr as dl_Andamento,
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
                     FROM fiscalizacao.fis_lancamento
                     LEFT JOIN fiscalizacao.fis_tipofiscaliza ON nl01_codtipo=y27_codtipo
                     LEFT JOIN fiscalizacao.fis_lancusu ON nl14_codlanc = nl01_codlanc 
                     LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_lancsanitario ON nl08_codlanc = nl01_codlanc
                     LEFT JOIN iptubase ON j01_matric = y53_matric
                     LEFT JOIN issbase ON y52_inscr = q02_inscr
                     LEFT JOIN cgm ON z01_numcgm = y54_numcgm
                     LEFT JOIN fiscalizacao.fis_sanitario ON y80_codsani = y55_codsani
                     LEFT JOIN fiscalizacao.fis_lancfiscal ON nl07_codlanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_fiscal ON y51_codnoti = y30_codnoti

        LEFT JOIN fiscalizacao.fis_procfiscallanc on nl09_lanc = nl01_codlanc
                     LEFT JOIN fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial
                     left join fiscalizacao.fis_procfiscalfiscais on y106_procfiscal = y100_sequencial 
                     left join fiscalizacao.fis_cadfiscais on y106_cadfiscais = id_usuario
                     left join db_usuarios        on db_usuarios.id_usuario = fis_cadfiscais.id_usuario
                     left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial  
                     left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario and fis_datalimitefiscal.data > '$dataAtual'

                     LEFT JOIN fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_lancandam where nl19_codlanc = nl01_codlanc) 
                     LEFT JOIN fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo 
         LEFT JOIN fiscalizacao.fis_grupotipoandamento_tipoandam ON y41_codtipo = fi30_tipoandam
                     LEFT JOIN fiscalizacao.fis_grupotipoandamento ON fi30_grupo = fis_grupotipoandamento.sequencial
      $where2
                        ) AS x
                          INNER JOIN cgm ON cgm.z01_numcgm = x.z01_numcgm
                          WHERE nl01_instit  = ".db_getsession('DB_instit')."
                            AND x.nl01_setor = ".db_getsession('DB_coddepto')."
                            and dl_Notificacao_Lancamento=$pesquisa_chave";
                             $result = $cllancamento->sql_record($sql);
          }else if(isset($cgf)){
            $result = $cllancamento->sql_record($cllancamento->sql_query_busca($chave_nl01_codlanc," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$pesquisa_chave ".$where, $where2));
          }else{ 
            $result = $cllancamento->sql_record($cllancamento->sql_query_busca($pesquisa_chave," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento=$pesquisa_chave and x.nl01_setor=".db_getsession("DB_coddepto").$where, $where2));
          }
          if($cllancamento->numrows!=0){
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
