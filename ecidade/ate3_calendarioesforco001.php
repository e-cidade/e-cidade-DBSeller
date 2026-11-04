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
include(modification("db_calendario.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_calend_classe.php"));
include(modification("classes/db_db_depart_classe.php"));
include(modification("classes/db_db_usuarios_classe.php"));
include(modification("classes/db_atendcadarea_classe.php"));
include(modification("classes/db_db_projetosativid_classe.php"));


$cldb_depart         = new cl_db_depart;
$cldb_usuarios       = new cl_db_usuarios;
$clatendcadarea      = new cl_atendcadarea;
$cldb_projetosativid = new cl_db_projetosativid;
$clrotulo            = new rotulocampo; 
$cale                = new db_calendario;
//$cale->pagina_alvo_relatorio = "con2_calendario003.php";
//$cale->monta_javascript("js_troca_cliente",""," location.href = 'ate3_calendariovisita001.php?cliente='+document.form1.cliente.value+'&tecnico='+document.form1.tecnico.value+'&situacao='+document.form1.situacao.value+'&at40_sequencial='+document.form1.at40_sequencial.value+'&tipo_pesquisa='+document.form1.tipo_pesquisa.value+'&at30_codigo='+document.form1.at30_codigo.value;");
//$cale->monta_javascript("js_pesquisa_tarefa",""," js_OpenJanelaIframe('','db_iframe_tarefa','func_tarefa.php?funcao_js=parent.js_mostra_tarefa|at40_sequencial','Pesquisa',true) ");
//$cale->monta_javascript("js_mostra_tarefa","tarefa"," document.form1.at40_sequencial.value = tarefa; db_iframe_tarefa.hide(); js_troca_cliente();");
$cale->monta_inicio_pagina(true);

echo "<form name='form1' method='post'>";
echo "<table>";
echo "<tr>";

echo "<td><strong>Departamento:</strong></td>";
echo "<td>";

$result = $cldb_depart->sql_record($cldb_depart->sql_query_file(null,"*","descrdepto"));
if(!isset($departamento)){
  global $departamento;
  $departamento = 0;
}
db_selectrecord("departamento",$result,true,2,"",'','','0','document.form1.submit()');

echo "</td>";

echo "<td><strong>Usuário:</strong></td>";
echo "<td>";

if($departamento == 0) {
  $result = $cldb_usuarios->sql_record($cldb_usuarios->sql_query_file(null,"*","nome"," usuarioativo = '1' "));
} else {
  $result = $cldb_usuarios->sql_record($cldb_usuarios->sql_query_file(null,"*","nome"," usuarioativo = '1' and exists (select 1 from db_depusu where db_depusu.id_usuario=db_usuarios.id_usuario and coddepto=$departamento) "));
}
if(!isset($usuarios)){
  global $usuarios;
  $usuarios = 0;
}
db_selectrecord("usuarios",$result,true,2,"",'','','0','document.form1.submit()');

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


if($departamento != 0 && $usuarios == 0 ){

  $cale->pagina_alvo = "ate3_calendarioesforco002.php";
  $cale->sql_cruzamento = " 

  select 
  nome,
  at43_diaini as dl_datacalend,
  turno,
  cast( sum( cast(at43_horafim as time) -
  cast(at43_horainidia as time) ) as time ) as esforco
  from 

  ( select *, case when substr(at43_horainidia,1,2)::integer < 12 then 'm' else 't' end as turno
    from
       tarefalog
       inner join db_usuarios u on u.id_usuario = at43_usuario
       inner join db_depusu d on d.id_usuario = u.id_usuario
       inner join db_depart a on a.coddepto = d.coddepto
  where 
    trim(at43_horainidia) != '' and trim(at43_horafim) != ''  and 
    a.coddepto = $departamento and 
    at43_diaini between '".$exercicio."-01-01' and '".$exercicio."-12-31'
  ) as x

  group by 
                 nome,
                 turno,
                 at43_diaini

  order by nome,turno, dl_datacalend

  ";
  $cale->sql_segundoacesso = " 
  select 
  at43_tarefa,
  nome,
  at43_diaini as dl_datacalend,
  dl_turno,
  at43_horainidia ,
  at43_horafim ,
  at40_obs,
  at43_descr,
  at43_obs
 
  from 

  ( select *, case when substr(at43_horainidia,1,2)::integer < 12 then 'M' else 'T' end as dl_turno
    from
       tarefalog
       inner join tarefa on at43_tarefa = at40_sequencial
       inner join db_usuarios u on u.id_usuario = at43_usuario
       inner join db_depusu d on d.id_usuario = u.id_usuario
       inner join db_depart a on a.coddepto = d.coddepto
  where 
    trim(at43_horainidia) != '' and trim(at43_horafim) != ''  and 
  ";
  if($departamento != 0){
       $cale->sql_segundoacesso .= "  a.coddepto = $departamento and ";
  }
  if($usuarios != 0){
       $cale->sql_segundoacesso .= "  at43_usuario = $usuarios and ";
  }
  $cale->sql_segundoacesso .= "
    at43_diaini between '".$exercicio."-01-01' and '".$exercicio."-12-31'
  ) as x

  order by nome,dl_turno, dl_datacalend
  ";




}else{

  $cale->pagina_alvo = "ate3_calendarioesforco004.php";
  $cale->sql_cruzamento = " 

  select 
  descrdepto,
  at43_diaini as dl_datacalend,
  turno,
  cast( sum( cast(at43_horafim as time) -
  cast(at43_horainidia as time) ) as time ) as esforco
  from 

  ( select *, case when substr(at43_horainidia,1,2)::integer < 12 then 'm' else 't' end as turno
    from
       tarefalog
       inner join db_usuarios u on u.id_usuario = at43_usuario
       inner join db_depusu d on d.id_usuario = u.id_usuario
       inner join db_depart a on a.coddepto = d.coddepto
  where 
    trim(at43_horainidia) != '' and trim(at43_horafim) != ''  and 
  ";
  if($departamento != 0){
       $cale->sql_cruzamento .= "  a.coddepto = $departamento and ";
  }
  if($usuarios != 0){
       $cale->sql_cruzamento .= "  at43_usuario = $usuarios and ";
  }
  $cale->sql_cruzamento .= "
    at43_diaini between '".$exercicio."-01-01' and '".$exercicio."-12-31'
  ) as x

  group by 
                 descrdepto,
                 turno,
                 at43_diaini

  order by descrdepto,turno, dl_datacalend

  ";
  
  $cale->sql_segundoacesso = " 
  select 
  at43_tarefa,
  descrdepto,
  at43_diaini as dl_datacalend,
  dl_turno,
  at43_horainidia,
  at43_horafim ,
  at40_obs,
  at43_descr,
  at43_obs
  from 

  ( select *, case when substr(at43_horainidia,1,2)::integer < 12 then 'M' else 'T' end::varchar(1) as dl_turno 
    from
       tarefalog
       inner join tarefa on at43_tarefa = at40_sequencial
       inner join db_usuarios u on u.id_usuario = at43_usuario
       inner join db_depusu d on d.id_usuario = u.id_usuario
       inner join db_depart a on a.coddepto = d.coddepto
  where 
    trim(at43_horainidia) != '' and trim(at43_horafim) != ''  and 
  ";
  if($departamento != 0){
       $cale->sql_segundoacesso .= "  a.coddepto = $departamento and ";
  }
  if($usuarios != 0){
       $cale->sql_segundoacesso .= "  at43_usuario = $usuarios and ";
  }
  $cale->sql_segundoacesso .= "
    at43_diaini between '".$exercicio."-01-01' and '".$exercicio."-12-31'
  ) as x

  order by descrdepto,dl_turno, dl_datacalend
  ";


}

 
$cale->monta_calendario_semanal_turno(@$exercicio,@$metodo,(!isset($metodo)?@date("Y-m-d",db_getsession("DB_datausu")):"$data"));
$cale->monta_fim_pagina(false);