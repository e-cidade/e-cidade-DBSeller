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

db_postmemory($_GET);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<form name='form1'>
<?php
  db_criatermometro('termometro','Concluido...','blue',1);
?>

<?php
dropTables();

$exercicios = '';
if(isset($exerc)){
  $exercicios    = ' and v01_exerc in ('.str_replace("-",",",$exerc).') ';
}
$sSqlData = "create table posdiv_data as select cast('".$xdata."' as date) as data";
$rsData   = db_query($sSqlData);
if (! $rsData ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

db_atutermometro(10,100,'termometro','1','Criando tabela com os parcelamentos ...');

// echo "Criando tabela com os parcelamentos ... <br>";
$sSqlTermoOrigem  = " create table w_termo_com_origem as ";
$sSqlTermoOrigem .= "  select * ";
$sSqlTermoOrigem .= "    from ( select ( select rinumpre ";
$sSqlTermoOrigem .= "                       from fc_origemparcelamento(termo.v07_numpre) ";
$sSqlTermoOrigem .= "                      order by rdtlanc desc, ";
$sSqlTermoOrigem .= "                               riseq desc ";
$sSqlTermoOrigem .= "                       limit 1 ";
$sSqlTermoOrigem .= "                  ) as numpre_origem, ";
$sSqlTermoOrigem .= "                  termo.*  ";
$sSqlTermoOrigem .= "             from termo ";
$sSqlTermoOrigem .= "            where v07_instit = ".db_getsession('DB_instit');
$sSqlTermoOrigem .= "         ) as x ";
$rsTermoOrigem = db_query($sSqlTermoOrigem);
if (! $rsTermoOrigem ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

$sSqlIndice = "create index w_termo_com_origem_v07_parcel_in on w_termo_com_origem(v07_parcel) ";
$rsIndice   = db_query($sSqlIndice);
if (! $rsIndice ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

$sSqlIndice = "create index w_termo_com_origem_numpre_origem_in on w_termo_com_origem(numpre_origem) ";;
$rsIndice   = db_query($sSqlIndice);
if (! $rsIndice ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

$sSqlAnalyze = "analyze w_termo_com_origem";
$rsAnalyze   = db_query($sSqlAnalyze);
if (! $rsAnalyze ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

//echo "Criando tabela com o codigo de arrecadação atual ... <br> ";
db_atutermometro(60,100,'termometro','1','Criando tabela com o codigo de arrecadação atual ...');

$sSqlNumpresAtuais  = " create table w_numpres_atuais as  ";
$sSqlNumpresAtuais .= " select numpre_divida     as numpre_divida,  ";
$sSqlNumpresAtuais .= "        numpre_atual      as numpre_atual,  ";
$sSqlNumpresAtuais .= "        sum(valor_divida) as valor_divida, ";
$sSqlNumpresAtuais .= "        v01_exerc, ";
$sSqlNumpresAtuais .= "        v01_proced, ";
$sSqlNumpresAtuais .= "        v03_descr, ";
$sSqlNumpresAtuais .= "        v03_tributaria, ";
$sSqlNumpresAtuais .= "        sum(perc) as perc, ";
$sSqlNumpresAtuais .= "        tipo  ";
$sSqlNumpresAtuais .= "   from ( select v01_numpre   as numpre_divida,  ";
$sSqlNumpresAtuais .= "                 t.v07_numpre as numpre_atual,  ";
$sSqlNumpresAtuais .= "                 v01_valor    as valor_divida, ";
$sSqlNumpresAtuais .= "                 v01_exerc, ";
$sSqlNumpresAtuais .= "                 v01_proced, ";
$sSqlNumpresAtuais .= "                 v03_tributaria, ";
$sSqlNumpresAtuais .= "                 v03_descr, ";
$sSqlNumpresAtuais .= "                 0::numeric as perc, ";
$sSqlNumpresAtuais .= "                 'parcelamento de inicial de certidao de parcelamento de divida' as tipo  ";
$sSqlNumpresAtuais .= "            from termoini ";
$sSqlNumpresAtuais .= "                 inner join termo as t                  on t.v07_parcel              = termoini.parcel  ";
$sSqlNumpresAtuais .= "                 inner join inicialnumpre               on inicialnumpre.v59_inicial = termoini.inicial ";
$sSqlNumpresAtuais .= "                 inner join w_termo_com_origem as termo on termo.v07_numpre          = inicialnumpre.v59_numpre ";
$sSqlNumpresAtuais .= "                 inner join termodiv                    on termodiv.parcel           = termo.v07_parcel ";
$sSqlNumpresAtuais .= "                 inner join divida                      on divida.v01_coddiv  = termodiv.coddiv  ";
$sSqlNumpresAtuais .= "                                                       and divida.v01_instit  = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 inner join proced                      on proced.v03_codigo  = divida.v01_proced ";
$sSqlNumpresAtuais .= "           where v01_instit = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "             {$exercicios} "; 
$sSqlNumpresAtuais .= "         union ";
$sSqlNumpresAtuais .= "          select v01_numpre          as numpre_divida,  ";
$sSqlNumpresAtuais .= "                 termo.numpre_origem as numpre_atual,  ";
$sSqlNumpresAtuais .= "                 v01_valor           as valor_divida, ";
$sSqlNumpresAtuais .= "                 v01_exerc, ";
$sSqlNumpresAtuais .= "                 v01_proced, ";
$sSqlNumpresAtuais .= "                 v03_tributaria, ";
$sSqlNumpresAtuais .= "                 v03_descr, ";
$sSqlNumpresAtuais .= "                 0::numeric as perc, ";
$sSqlNumpresAtuais .= "                 'parcelamento de inicial de certidao de divida' as tipo  ";
$sSqlNumpresAtuais .= "            from termoini ";
$sSqlNumpresAtuais .= "                 inner join w_termo_com_origem as termo on termo.v07_parcel          = termoini.parcel  ";
$sSqlNumpresAtuais .= "                 inner join inicialnumpre               on inicialnumpre.v59_inicial = termoini.inicial ";
$sSqlNumpresAtuais .= "                 inner join divida                      on divida.v01_numpre         = inicialnumpre.v59_numpre  ";
$sSqlNumpresAtuais .= "                                                       and divida.v01_instit         = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 inner join proced                      on proced.v03_codigo         = divida.v01_proced ";
$sSqlNumpresAtuais .= "           where v01_instit = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 {$exercicios} ";
$sSqlNumpresAtuais .= "          union  ";
$sSqlNumpresAtuais .= "          select v01_numpre as numpre_divida,  ";
$sSqlNumpresAtuais .= "                 termo.numpre_origem as numpre_atual,   ";
$sSqlNumpresAtuais .= "                 v01_valor  as valor_divida, ";
$sSqlNumpresAtuais .= "                 v01_exerc, ";
$sSqlNumpresAtuais .= "                 v01_proced, ";
$sSqlNumpresAtuais .= "                 v03_tributaria, ";
$sSqlNumpresAtuais .= "                 v03_descr, ";
$sSqlNumpresAtuais .= "                 0::numeric as perc, ";
$sSqlNumpresAtuais .= "                'parcelamento de divida' as tipo  ";
$sSqlNumpresAtuais .= "            from termodiv  ";
$sSqlNumpresAtuais .= "                 inner join w_termo_com_origem as termo on termo.v07_parcel = termodiv.parcel  ";
$sSqlNumpresAtuais .= "                 inner join divida  on termodiv.coddiv = divida.v01_coddiv  ";
$sSqlNumpresAtuais .= "                                   and divida.v01_instit = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 inner join proced  on proced.v03_codigo = divida.v01_proced ";
$sSqlNumpresAtuais .= "           where v01_instit = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 {$exercicios} "; 
$sSqlNumpresAtuais .= "          union ";
$sSqlNumpresAtuais .= "          select v01_numpre as numpre_divida,  ";
$sSqlNumpresAtuais .= "                 v01_numpre as numpre_atual,   ";
$sSqlNumpresAtuais .= "                 v01_valor  as valor_divida, ";
$sSqlNumpresAtuais .= "                 v01_exerc, ";
$sSqlNumpresAtuais .= "                 v01_proced, ";
$sSqlNumpresAtuais .= "                 v03_tributaria, ";
$sSqlNumpresAtuais .= "                 v03_descr, ";
$sSqlNumpresAtuais .= "                 0::numeric as perc, ";
$sSqlNumpresAtuais .= "                 'divida ativa' as tipo  ";
$sSqlNumpresAtuais .= "            from divida ";
$sSqlNumpresAtuais .= "                 inner join proced   on proced.v03_codigo  = divida.v01_proced ";
$sSqlNumpresAtuais .= "           where v01_instit = ".db_getsession('DB_instit');
$sSqlNumpresAtuais .= "                 {$exercicios} "; 
$sSqlNumpresAtuais .= " ) as xxx ";
$sSqlNumpresAtuais .= " group by numpre_divida, ";
$sSqlNumpresAtuais .= "          numpre_atual, ";
$sSqlNumpresAtuais .= "          v01_exerc, ";
$sSqlNumpresAtuais .= "          v01_proced, ";
$sSqlNumpresAtuais .= "          v03_tributaria, ";
$sSqlNumpresAtuais .= "          v03_descr, ";
$sSqlNumpresAtuais .= "          tipo ";

$rsNumpresAtuais    = db_query($sSqlNumpresAtuais);
if (! $rsNumpresAtuais ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  echo $sSqlNumpresAtuais;
  exit;
}

$sSqlIndice = "create index i1_in on w_numpres_atuais(numpre_divida) ";
$rsIndice   = db_query($sSqlIndice);
if (! $rsIndice ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

$sSqlIndice = "create index i2_in on w_numpres_atuais(numpre_atual) ";
$rsIndice   = db_query($sSqlIndice);
if (! $rsIndice ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

$sSqlAnalyze = "analyze w_numpres_atuais";
$rsAnalyze   = db_query($sSqlAnalyze);
if (! $rsAnalyze ) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

db_atutermometro(70,100,'termometro','1','Atualizando o percentual de calculo ...');
// echo "Atualizando o percentual de calculo ... <br> ";
$sSqlUpdatePerc  = " update w_numpres_atuais ";
$sSqlUpdatePerc .= "    set perc = ( valor_divida / (select sum(valor_divida) ";
$sSqlUpdatePerc .= "                                   from w_numpres_atuais a ";
$sSqlUpdatePerc .= "                                  where w_numpres_atuais.numpre_atual = a.numpre_atual) * 100 ) ";
$rsUpdatePerc    = db_query($sSqlUpdatePerc);
if (! $rsUpdatePerc) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

db_atutermometro(85,100,'termometro','1','Criando tabela com os numpres calculados ...');
$sSqlCalculo  = " create table w_numpres_calculados as ";
$sSqlCalculo .= " select numpre_divida, ";
$sSqlCalculo .= "        numpre_atual,  ";
$sSqlCalculo .= "        v01_proced,  ";
$sSqlCalculo .= "        v01_exerc,  ";
$sSqlCalculo .= "        k22_tipo,  ";
$sSqlCalculo .= "        k00_descr,  ";
$sSqlCalculo .= "        v03_descr,  ";
$sSqlCalculo .= "        v03_tributaria,  ";
$sSqlCalculo .= "        perc, ";
$sSqlCalculo .= "        ( ( sum(k22_vlrhis)   / 100 ) * perc )  as vlrhis,  ";
$sSqlCalculo .= "        ( ( sum(k22_vlrcor)   / 100 ) * perc )  as vlrcor,  ";
$sSqlCalculo .= "        ( ( sum(k22_juros)    / 100 ) * perc )  as juros,  ";
$sSqlCalculo .= "        ( ( sum(k22_multa)    / 100 ) * perc )  as multa,  ";
$sSqlCalculo .= "        ( ( sum(k22_desconto) / 100 ) * perc )  as desconto,  ";
$sSqlCalculo .= "        ( ( sum(k22_vlrcor+k22_juros+k22_multa-k22_desconto) / 100 ) * perc ) as valor_total   ";
$sSqlCalculo .= "   from debitos ";
$sSqlCalculo .= "        inner join w_numpres_atuais on k22_numpre = numpre_atual ";
$sSqlCalculo .= "                                   and k22_data   = '$xdata' ";
$sSqlCalculo .= "                                   and k22_instit = ".db_getsession('DB_instit');
$sSqlCalculo .= "        inner join arretipo on k22_data   = '$xdata' ";
$sSqlCalculo .= "                           and k00_tipo = k22_tipo ";
$sSqlCalculo .= "  where k22_data   = '$xdata' ";
$sSqlCalculo .= "    and k22_instit = ".db_getsession('DB_instit');
/*
$sSqlCalculo .= "   from w_numpres_atuais  ";
$sSqlCalculo .= "        inner join debitos  on k22_data   = '$xdata'  ";
$sSqlCalculo .= "                           and k22_numpre = numpre_atual  ";
$sSqlCalculo .= "                           and k22_instit = ".db_getsession('DB_instit');
$sSqlCalculo .= "        inner join arretipo on k22_data   = '$xdata'  ";
$sSqlCalculo .= "                           and k00_tipo = k22_tipo  ";
*/
$sSqlCalculo .= "  group by numpre_divida, ";
$sSqlCalculo .= "           numpre_atual,  ";
$sSqlCalculo .= "           v01_proced, ";
$sSqlCalculo .= "           v03_tributaria, ";
$sSqlCalculo .= "           v03_descr, ";
$sSqlCalculo .= "           perc, ";
$sSqlCalculo .= "           v01_exerc,  ";
$sSqlCalculo .= "           k22_tipo,  ";
$sSqlCalculo .= "           k00_descr ; ";
$rsCalculo    = db_query($sSqlCalculo);
if (! $rsCalculo) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

db_atutermometro(95,100,'termometro','1','Verificando os valores ...');

$sSqlDiferenca  = " create table w_diferenca_numpre as  ";
$sSqlDiferenca .= " select numpre_atual, ";
$sSqlDiferenca .= "        round(sum(calculado),2)    as calculado,  ";
$sSqlDiferenca .= "        round(sum(valor_debito),2) as valor_debito, ";
$sSqlDiferenca .= "        round(sum(calculado)-sum(valor_debito),2) as diferenca ";
$sSqlDiferenca .= "   from ( select numpre_atual, ";
$sSqlDiferenca .= "                 sum(valor_total) as calculado,  ";
$sSqlDiferenca .= "                 0 as valor_debito  ";
$sSqlDiferenca .= "            from w_numpres_calculados  ";
$sSqlDiferenca .= "           where k22_tipo in (select k00_tipo from arretipo where k03_tipo in (5,6,18,15,13) and k00_instit = ".db_getsession('DB_instit').") ";
$sSqlDiferenca .= "           group by numpre_atual  ";
$sSqlDiferenca .= "       union all  ";
$sSqlDiferenca .= "          select k22_numpre, ";
$sSqlDiferenca .= "                 0, ";
$sSqlDiferenca .= "                 (sum(k22_vlrcor)+sum(k22_juros)+sum(k22_multa)-sum(k22_desconto))  ";
$sSqlDiferenca .= "            from debitos  ";
$sSqlDiferenca .= "           where k22_data = '$xdata'  ";
$sSqlDiferenca .= "             and k22_tipo in (select k00_tipo from arretipo where k03_tipo in (5,6,18,15,13) and k00_instit = ".db_getsession('DB_instit').") ";
$sSqlDiferenca .= "           group by k22_numpre ";
$sSqlDiferenca .= "        ) as x  ";
$sSqlDiferenca .= "   group by numpre_atual ";
$sSqlDiferenca .= "   having cast(round(sum(calculado),2) as numeric) <> cast(round(sum(valor_debito),2) as numeric) ";
$rsDiferenca    = db_query($sSqlDiferenca);
if (! $rsDiferenca) {
  echo "ERRO : ".pg_last_error()."<br>";
  dropTables();
  exit;
}

db_atutermometro(99,100,'termometro');
fechaJanela("Processamento concluido com sucesso !");

function fechaJanela($sMensagem = "") {

  if ($sMensagem != "") {
    db_msgbox($sMensagem);
  }

  echo "<script>";
  echo "  parent.db_iframe_processa.hide();";
  echo "  parent.document.form1.submit();";
  echo "</script>";
  exit;

}



function dropTables() {

  db_query("drop table posdiv_data");
  db_query("drop table w_termo_com_origem");
  db_query("drop table w_numpres_atuais");
  db_query("drop table w_diferenca_numpre");
  db_query("drop table w_numpres_calculados");

}

?>
</body>
</html>