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
include modification("classes/db_fis_lancusu_classe.php");
include modification("classes/db_fis_lanctestem_classe.php");
include modification("classes/db_fis_lancnumpre_classe.php");
include modification("classes/db_fis_lanctipo_classe.php");
include modification("classes/db_fis_lancrec_classe.php");
include modification("classes/db_fis_lancrespons_classe.php");

$clautotipo   = new cl_fis_autotipo;
$cllanctipo   = new cl_fis_lanctipo;
$clautorec    = new cl_fis_autorec;
$cllancrec    = new cl_fis_lancrec;
$cllancusu    = new cl_fis_lancusu;
$cllanctestem = new cl_fis_lanctestem;
$cllancnumpre = new cl_fis_lancnumpre;
$cllancrespons   = new cl_fis_lancrespons;

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
  $sql = $cllanctipo->sql_query_baixa("","nl18_codtipo as dl_Codigo,y29_descr,y29_descr_obs, nl18_valor as dl_Valor,nl24_dtbaixa as dl_Dt_Baixa,p58_codproc",""," nl18_codlanc = $codlanc");
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Receita") {
  $sql = $cllancrec->sql_query("","","nl22_receit as Dl_Receita,nl22_descr as dl_Descricao ,nl22_valor as dl_Valor",""," nl22_codlanc = $codlanc and nl01_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Fiscais") {
  $sql = $cllancusu->sql_query("","","nl14_id_usuario as Usuario,nome,nl14_obs  as Obs",""," nl14_codlanc = $codlanc and nl01_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Testemunha") {
  $sql = $cllanctestem->sql_query("","","nl21_numcgm,z01_nome",""," nl21_codlanc = $codlanc and nl01_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Responsavel") {
  $sql = $cllancrespons->sql_query("","","nl12_numcgm as z01_numcgm,z01_nome,case nl12_tipo when 1 then 'SOLIDÁRIO' else 'SUBSIDIÁRIO' end as tipo",""," nl12_codlanc = $codlanc and nl01_instit = ".db_getsession('DB_instit') );
  $pesquisaLocalizada = true;
} else if ($solicitacao == "Calculo") {
  $sSqllancnumpre = $cllancnumpre->sql_query("","*","","nl26_codlanc=$codlanc and nl01_instit = ".db_getsession('DB_instit') );
  $result_calc    = $cllancnumpre->sql_record($sSqllancnumpre);
  if ($cllancnumpre->numrows>0){
    db_fieldsmemory($result_calc,0);    
    echo "<br><br><br><b><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notificaçao de Lançamento já Calculado!! Numpre:".@$nl26_numprelanc."<b>";
  }else{
    echo "<br><br><b><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Notificaçao de Lançamento não foi calculado!!<b>";
  }
} else if ($solicitacao = "Andamento") {
  $sql="select 
            y39_codandam, 
            y41_descr, 
            y39_data, 
            y41_obs 
        from 
          lancandam 
        inner join fiscalizacao.fis_fandam on y39_codandam = nl19_codandam 
        inner join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo 
        where 
          nl19_codlanc = ".$codlanc." 
        order by nl19_codandam desc";
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