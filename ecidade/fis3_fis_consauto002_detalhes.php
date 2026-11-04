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
include modification("classes/db_fis_autotipo_classe.php");
include modification("classes/db_fis_autorec_classe.php");
include modification("classes/db_fis_autousu_classe.php");
include modification("classes/db_fis_autotestem_classe.php");
include modification("classes/db_fis_autonumpre_classe.php");
include modification("classes/db_fis_autorespons_classe.php");
$clautotipo= new cl_fis_autotipo;
$clautorec= new cl_fis_autorec;
$clautousu= new cl_fis_autousu;
$clautotestem= new cl_fis_autotestem;
$clautonumpre= new cl_fis_autonumpre;
$clautorespons = new cl_fis_autorespons;
?>
<html>
<head>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
db_postmemory($HTTP_GET_VARS,0);
$pesquisaLocalizada = false;
if ($solicitacao == "Proced") {
  $sql = $clautotipo->sql_query_baixa("","y59_codtipo,y29_descr,y29_descr_obs,y59_valor,y87_dtbaixa,p58_codproc",""," y59_codauto = $auto");
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Receita") {
  $sql = $clautorec->sql_query("","","y57_receit,y57_descr,y57_valor",""," y57_codauto = $auto and y50_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Fiscais") {
  $sql = $clautousu->sql_query("","","y56_id_usuario,nome,y56_obs ",""," y56_codauto = $auto and y50_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Testemunha") {
  $sql = $clautotestem->sql_query("","","y24_numcgm,z01_nome",""," y24_codauto = $auto and y50_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Responsavel") {
  $sql = $clautorespons->sql_query("","","y124_numcgm as z01_numcgm,z01_nome,case y124_tipo when 1 then 'SOLIDÁRIO' else 'SUBSIDIÁRIO' end as tipo",""," y124_codauto = $auto and y50_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Calculo") {
  $sSqlAutoNumpre = $clautonumpre->sql_query("","*","","y17_codauto=$auto and y50_instit = ".db_getsession('DB_instit') );
  $result_calc    = $clautonumpre->sql_record($sSqlAutoNumpre);
  if ($clautonumpre->numrows>0){
    db_fieldsmemory($result_calc,0);
    echo "<br><br><br><b><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auto já Calculado!! Numpre:".@$y17_numpre."<b>";
  }else{
    echo "<br><br><b><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Auto não foi calculado!!<b>";
  }
} else if ($solicitacao = "Andamento") {
  $sql="select
            y39_codandam,
            y41_descr,
            y39_data,
            y41_obs
        from
          fiscalizacao.fis_autoandam
        inner join fiscalizacao.fis_fandam on y39_codandam = y58_codandam
        inner join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
        where
          y58_codauto = ".$auto."
        order by y58_codandam desc";
  $pesquisaLocalizada = true;
}
if ($pesquisaLocalizada==true) {
  $result = pg_exec($sql);
  if(pg_numrows($result) == 0){
    echo "<br><br><b>Nenhum Registro Cadastrado!!<b>";
  }else{
    db_lovrot($sql,5,"","","");
  }
}
?>
</body>
</html>
