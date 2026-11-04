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

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("ate3_calendarioimplanta003.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_calend_classe.php"));
include(modification("classes/db_clientes_classe.php"));
include(modification("classes/db_atendcadarea_classe.php"));
include(modification("classes/db_db_projetosativid_classe.php"));


$clclientes          = new cl_clientes;
$clatendcadarea      = new cl_atendcadarea;
$cldb_projetosativid = new cl_db_projetosativid;
$clrotulo            = new rotulocampo; 
$cale                = new db_calendario;
$cale->pagina_alvo = "ate3_calendarioimplanta002.php";
//$cale->pagina_alvo_relatorio = "con2_calendario003.php";
//$cale->monta_javascript("js_troca_cliente",""," location.href = 'ate3_calendariovisita001.php?cliente='+document.form1.cliente.value+'&tecnico='+document.form1.tecnico.value+'&situacao='+document.form1.situacao.value+'&at40_sequencial='+document.form1.at40_sequencial.value+'&tipo_pesquisa='+document.form1.tipo_pesquisa.value+'&at30_codigo='+document.form1.at30_codigo.value;");
//$cale->monta_javascript("js_pesquisa_tarefa",""," js_OpenJanelaIframe('','db_iframe_tarefa','func_tarefa.php?funcao_js=parent.js_mostra_tarefa|at40_sequencial','Pesquisa',true) ");
//$cale->monta_javascript("js_mostra_tarefa","tarefa"," document.form1.at40_sequencial.value = tarefa; db_iframe_tarefa.hide(); js_troca_cliente();");
$cale->monta_inicio_pagina(true);

echo "<form name='form1' method='post'>";
echo "<table>";
echo "<tr>";
echo "<td><strong>Cliente:</strong></td>";
echo "<td>";

$result = $clclientes->sql_record($clclientes->sql_query());
if(!isset($cliente)){
  global $cliente;
  $cliente = 0;
}
db_selectrecord("cliente",$result,true,2,"",'','','0','document.form1.submit()');

echo "</td>";
echo "<td><strong>Área:</strong></td>";
echo "<td>";

$result = $clatendcadarea->sql_record($clatendcadarea->sql_query());
if(!isset($area)){
  global $area;
  $area = 0;
}
db_selectrecord("area",$result,true,2,"",'','','0','document.form1.submit()');

echo "</td>";
echo "<td><strong>Exercício:</strong></td>";
echo "<td>";

$exerc = array();
for($i=(db_getsession('DB_anousu')-2);$i<(db_getsession('DB_anousu')+5);$i++){
  $exerc[$i] = $i;
}
if(!isset($exercicio)){
  global $exercicio;
  $exercicio = db_getsession('DB_anousu');
}

db_select("exercicio",$exerc,true,2,' onchange="document.form1.submit()"');

echo "</td>";

echo "</tr>";
echo "</table>";
echo "</form>";


$cale->sql_cruzamento = " 
   select at01_nomecli,extract(week from dl_datacalend) as dl_datacalend, at62_descr, dl_datacalend as data,at60_codproced,codmod, at62_cor, at60_descricao, at64_situacao
   from (
    select * from 
    (select '<strong>'||upper(nomemod)||'</strong><br>'||at60_codproj||'-'||descrproced as at01_nomecli,data_projeto as dl_datacalend,at62_descr,at60_codproced,m.codmod, at62_cor, at60_descricao, at64_situacao
     from db_projetoscliente
          inner join db_syscadproced on codproced = at60_codproced
          inner join db_sysmodulo m on m.codmod = db_syscadproced.codmod
        inner join db_projetosativcli on at64_codproj = at60_codproj
        inner join db_projetosativid on at62_codigo = at64_codativ

        inner join clientes on at01_codcli = at60_codcli
        inner join 
        (select at64_sequencial as seq,at64_dtini+generate_series(0, at64_dtfim - at64_dtini ) as data_projeto from db_projetosativcli ) as x on seq =  at64_sequencial
    where 1 = 1
";
if($cliente != 0){
   $cale->sql_cruzamento .= " and at01_codcli = $cliente ";
}
if($area != 0){
   $cale->sql_cruzamento .= " and codarea = $area ";
}

$cale->sql_cruzamento .= "
     and data_projeto between '$exercicio-01-01' and '$exercicio-12-31'";
$cale->sql_cruzamento .= "
   ) as x
   ) as x              
   order by data
  ";



$cale->sql_segundoacesso = " 
   select m.codmod,upper(nomemod) as nomemod,at60_codproced,descrproced,at60_inicio as dl_datacalend,descrproced, at62_descr,at64_dtini,at64_dtfim,at64_descricao
   from db_projetoscliente
        inner join db_syscadproced on codproced = at60_codproced
        inner join db_sysmodulo m on m.codmod = db_syscadproced.codmod
        inner join db_projetosativcli on at64_codproj = at60_codproj
        inner join db_projetosativid on at62_codigo = at64_codativ
        inner join clientes on at01_codcli = at60_codcli
   where 1 = 1 
 ";
if($cliente != 0){
   $cale->sql_segundoacesso .= " and at01_codcli = $cliente ";
}
if($area != 0){
   $cale->sql_segundoacesso .= " and codarea = $area ";
}
$cale->sql_segundoacesso .= "
  order by dl_datacalend              
 ";
 
$result = $cldb_projetosativid->sql_record($cldb_projetosativid->sql_query_file(null,"*","at62_descr"));

echo "<table>";
echo "<tr>";
echo "<td><Strong>Legenda:<br>";
for($i=0;$i<$cldb_projetosativid->numrows;$i++){
  db_fieldsmemory($result,$i);
  echo "<font size=\"-1\" color=\"$at62_cor\">$at62_descr</font> &nbsp&nbsp";
}
echo "</strong></td>";
echo "</tr>";
echo "<tr><td><strong>*-Vencido &nbsp&nbsp 1-Autorizado &nbsp&nbsp 2-Nao Autorizado &nbsp&nbsp 3-Concluido</strong></td></tr>";
echo "</table>";

$cale->monta_calendario_anual_semana($exercicio,$cliente,$exercicio."-01-01") ;//date("Y-m-d",db_getsession("DB_datausu")));
$cale->monta_fim_pagina(false);