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

db_postmemory($HTTP_POST_VARS);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_processa(codigo,tipo,mes,scodigo){
  dataini = document.form1.dataini_ano.value+'-'+document.form1.dataini_mes.value+'-'+document.form1.dataini_dia.value;
  datafim = document.form1.datafim_ano.value+'-'+document.form1.datafim_mes.value+'-'+document.form1.datafim_dia.value;
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pesquisa','ate3_atendimento002.php?scodigo='+scodigo+'&tipoconsulta='+document.form1.tipoconsulta.value+'&mes='+mes+'&codigo='+codigo+'&tipo='+tipo+'&dataini='+dataini+'&datafim='+datafim,'Pesquisa',true,'30');
}

function js_troca(valor){
  if(valor > 0 ){
    document.form1.tipoconsulta.style.visibility = 'visible';
  }else{
    document.form1.tipoconsulta.style.visibility = 'hidden';
  }
}
function js_ordena(ordem){
  if( document.form1.ultima_ordenar.value === ordem ){
    ordem = ordem + " DESC ";
  }
  document.form1.ordenar.value = ordem;
  document.form1.pesquisar.click();
}


</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360" height="18">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<form name='form1' action='' method='post'>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="left" valign="top" bgcolor="#CCCCCC"> 
    <br>
    <strong>Área:</strong>
    <?php
    $sql = "select * from (select * from atendcadarea order by at25_descr ) as x union all select 9999,'NENHUMA'";
    $result = db_query($sql);
    //db_selectrecord("area",$result,true,2," ","","","0","js_troca(this.value)");
    db_selectrecord("area",$result,true,2," ","","","0","");

//    echo "<select style='visibility:hidden' name='tipoconsulta'>";
    echo "<select name='tipoconsulta'>";
    echo "<option value='0' ".($tipoconsulta==0?'selected':'').">Sem Quebra</option>";
    echo "<option value='1' ".($tipoconsulta==1?'selected':'').">Mês</option>";
    echo "<option value='2' ".($tipoconsulta==2?'selected':'').">Módulo</option>";
    echo "<option value='3' ".($tipoconsulta==3?'selected':'').">Usuário</option>";
    echo "<option value='4' ".($tipoconsulta==4?'selected':'').">Departamento</option>";
    echo "<option value='5' ".($tipoconsulta==5?'selected':'').">Procedimento</option>";
    echo "<option value='6' ".($tipoconsulta==6?'selected':'').">Cliente</option>";
    echo "</select>";	
 ?>
    </td>
  </tr>
  <tr>
    <td>
    <br>
    <strong>Intervalor de Data:</strong>
    <?php
    if(!isset($pesquisar)){
      $dataini_ano = date('Y',db_getsession('DB_datausu'));
      $dataini_mes = date('m',db_getsession('DB_datausu'));
      $dataini_dia = date('d',db_getsession('DB_datausu'));
      $datafim_ano = date('Y',db_getsession('DB_datausu'));
      $datafim_mes = date('m',db_getsession('DB_datausu'));
      $datafim_dia = date('d',db_getsession('DB_datausu'));
    }
    db_inputdata("dataini",@$dataini_dia,@$dataini_mes,@$dataini_ano,true,'text',2);
    ?>
    a
    <?php
    db_inputdata("datafim",@$datafim_dia,@$datafim_mes,@$datafim_ano,true,'text',2);
    ?>
      <input name='ordenar' value='' type='hidden'>
      <input name='ultima_ordenar' value='<?=(isset($pesquisar)?$ordenar:"")?>' type='hidden'>
      <input name='pesquisar' value='Pesquisar' type='submit'>
    </td>
  </tr>
  <td>
  <br>

<?php

if( isset($pesquisar) ){

  if($area!=9999){
    $sql = "select codigo,filtro, ";
  }else{
    $sql = "select ";
  }
  if( $tipoconsulta > 0){
    $sql .= " scodigo,mes, ";
  }
  $sql .= "
         total,
         duvidas ,
         round(duvidas*100/total) as perc_duvidas,
         duvidas_finalizados,
         erros,
         round(erros*100/total) as perc_erros,
         erros_finalizados,
         melhorias,
         round(melhorias*100/total) as perc_melhorias,
         melhorias_finalizados,
         base,
         round(base*100/total) as perc_base,
         base_finalizados,
         outros
  from
    (
  ";
  if($area!=9999){
    $sql .= " select at26_sequencial as codigo,at25_descr as filtro, ";
  }else{
    $sql .= " select ";
  }
  if($tipoconsulta != 0){
    if( $tipoconsulta == 1 ){
      $sql .= " 0 as scodigo, extract(month from at02_datafim) as mes, ";
    }else if( $tipoconsulta == 2 ){
      $sql .= " db_sysmodulo.codmod as scodigo, nomemod as mes , ";
    }else if( $tipoconsulta == 3 ){
      $sql .= " db_usuarios.id_usuario as scodigo,nome as mes , ";
    }else if( $tipoconsulta == 4 ){
      $sql .= " db_depart.coddepto as scodigo,descrdepto as mes , ";
    }else if( $tipoconsulta == 5 ){
      $sql .= " at29_syscadproced as scodigo,descrproced as mes , ";
    }else if( $tipoconsulta == 6 ){
      $sql .= " at01_codcli as scodigo,at01_nomecli as mes , ";
    }
  }
  $sql .= "
           sum(case when at54_sequencial =  1  then 1 else 0 end) as erros,
           sum(case when at54_sequencial =  1 and ( t.at40_progresso = 100 or ta.at40_progresso = 100 ) then 1 else 0 end) as erros_finalizados,
           sum(case when at54_sequencial =  2  then 1 else 0 end) as melhorias,
           sum(case when at54_sequencial =  2 and ( t.at40_progresso = 100 or ta.at40_progresso = 100 ) then 1 else 0 end) as melhorias_finalizados,
           sum(case when at54_sequencial = 13  then 1 else 0 end) as duvidas,
           sum(case when at54_sequencial = 13 and ( t.at40_progresso = 100 or ta.at40_progresso = 100 ) then 1 else 0  end) as duvidas_finalizados,
           sum(case when at54_sequencial =  6  then 1 else 0 end) as base,
           sum(case when at54_sequencial =  6 and ( t.at40_progresso = 100 or ta.at40_progresso = 100 ) then 1 else 0 end) as base_finalizados,
           sum(case when at54_sequencial not in (1,2,13,6) then 1 else 0 end) as outros,
           sum(1) as total
    from atendimento
         left join atenditem         on at02_codatend   = at05_codatend
         left join clientes on clientes.at01_codcli = at02_codcli
         
         left  join tarefaitem         on at44_atenditem  = at05_seq
         left  join tarefa  t       on at44_tarefa        = t.at40_sequencial

         left  join atenditemtarefa      on at18_atenditem  = at05_seq
         left  join tarefa  ta       on at18_tarefa        = ta.at40_sequencial
                  
         left  join tecnico         on at03_codatend   = at05_codatend
         left  join db_usuarios         on db_usuarios.id_usuario = at03_id_usuario
         left  join db_depusu           on db_usuarios.id_usuario = db_depusu.id_usuario
         left  join db_depart           on db_depusu.coddepto = db_depart.coddepto
         left join atenditemmotivo     on at34_atenditem  = at05_seq
         left join tarefacadmotivo     on at54_sequencial   = at34_tarefacadmotivo
         left join atenditemmod         on at22_atenditem    = at05_seq
         left join db_sysmodulo          on at22_modulo       = db_sysmodulo.codmod
         left join atenditemsyscadproced on at29_atenditem    = at05_seq
         left join db_syscadproced on at29_syscadproced = codproced
         left  join atendarea             on at02_codatend = at28_atendimento
         left  join atendcadarea          on at28_atendcadarea = at26_sequencial
         
    Where 1=1
      and at01_codcli not in (25)
      and (at02_datafim >= '$dataini_ano-$dataini_mes-$dataini_dia'
      and  at02_datafim <= '$datafim_ano-$datafim_mes-$datafim_dia'
      and  at26_sequencial not in (6,7)
  ";
  if( $area > 0 && $area != 9999 ){
    $sql .= " and  at28_atendcadarea = $area ";
  }
  
  if($tipoconsulta > 0 ){
    if($area==9999){
      $sql .= "
      ) group by scodigo,mes
        order by scodigo,mes
      ) as x ";
    }else{
      $sql .= "
      ) group by codigo,filtro, scodigo,mes
        order by codigo,filtro, scodigo,mes
      ) as x ";
    }
  }else{
    if($area!=9999){
      $sql .= "
        ) group by codigo,filtro
          order by codigo,filtro
      ) as x ";
    }else{
      $sql .= "
        ) 
      ) as x ";
    }
  }

  if($ordenar!=""){
    $sql = " select * from ($sql) as x order by $ordenar ";
  }

  //echo $sql;
  $result = db_query($sql);
  //db_criatabela($result);
  echo "<br><strong>Percentuais calculados pelo total de atendimentos da linha em análise.</strong><br>";
  echo "<table width='100%' border='1'>";
  echo "<tr>";
  if($area != 9999){
    echo "<td bgcolor='lightgreen' ><a href'#' onclick='js_ordena(\"filtro\");return false;'>Totalizador</a></td>";
  }

  
  if($tipoconsulta > 0){
    echo "<td bgcolor='lightgreen' ><a href'#' onclick='js_ordena(\"mes\");return false;'>";
    if( $tipoconsulta == 1 ){
      echo "Mês";
    }else if( $tipoconsulta == 2 ){
      echo "Módulo";
    }else if( $tipoconsulta == 3 ){
      echo "Atendente";
    }else if( $tipoconsulta == 4 ){
      echo "Departamento";
    }else if( $tipoconsulta == 5 ){
      echo "Procedimento";
    }else if( $tipoconsulta == 6 ){
      echo "Clientes";
    }
    echo "</a></td>";  
  }
  echo "<td bgcolor='lightgreen' title='Total de Atendimentos' align='center'><a href'#' onclick='js_ordena(\"total\");return false;'>Atendimentos</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Dúvidas' align='center'><a href'#' onclick='js_ordena(\"duvidas\");return false;'>Dúvidas</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Dúvidas' align='center'><a href'#' onclick='js_ordena(\"perc_duvidas\");return false;'>% At.</a></td>";
  echo "<td bgcolor='lightgreen' title='DÃºvidas Finalizadas' align='center'><a href'#' onclick='js_ordena(\"duvidas_finaluzadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Erros' align='center'><a href'#' onclick='js_ordena(\"erros\");return false;'>Erros</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Erros' align='center'><a href'#' onclick='js_ordena(\"perc_erros\");return false;'>% At.</a></td>";
  echo "<td bgcolor='lightgreen' title='Erros Finalizados' align='center'><a href'#' onclick='js_ordena(\"erros_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Melhorias' align='center'><a href'#' onclick='js_ordena(\"melhorias\");return false;'>Melhorias</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Melhorias' align='center'><a href'#' onclick='js_ordena(\"perc_melhorias\");return false;'>% At.</a></td>";
  echo "<td bgcolor='lightgreen' title='Melhorias Finalizadas' align='center'><a href'#' onclick='js_ordena(\"melhorias_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Base de Dados' align='center'><a href'#' onclick='js_ordena(\"base\");return false;'>Base Dados</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Base de dados' align='center'><a href'#' onclick='js_ordena(\"perc_base\");return false;'>% At.</a></td>";
  echo "<td bgcolor='lightgreen' title='Base de dados Finalizadas' align='center'><a href'#' onclick='js_ordena(\"base_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Outros Atendimentos' align='center'><a href'#' onclick='js_ordena(\"outros\");return false;'>Outros</a></td>";
  echo "</tr>";
  $totate = 0;
  $totduv = 0;
  $totfduv= 0;
  $toterr = 0;
  $totferr= 0;
  $totmel = 0;
  $totfmel= 0;
  $totbas = 0;
  $totfbas= 0;
  $totout= 0;
  for($i=0;$i<pg_numrows($result);$i++){
    db_fieldsmemory($result,$i);
    echo "<tr>";
    if($area!=9999){
      echo "<td bgcolor='lightgreen' align='left'>$filtro</td>";
    }
    if(!isset($scodigo)){
      $scodigo = "";
    }
    if(!isset($codigo)){
      $codigo = "";
    }
    if( $tipoconsulta > 0){
      
      echo "<td align='left'>$mes</td>";
      if( $tipoconsulta == 2 ){
        $scodigo = " codmod-$scodigo ";
      }else if( $tipoconsulta == 3 ){
        $scodigo = " id_usuario-$scodigo ";
      }else if( $tipoconsulta == 4 ){
        $scodigo = " coddepto-$scodigo ";
      }else if( $tipoconsulta == 5 ){
        $scodigo = " codproced-$scodigo ";
      }else if( $tipoconsulta == 6 ){
        $scodigo = " at01_codcli-$scodigo ";
      }else{
        $scodigo = "";
      }
    }else{
      $mes = 0;
    }
    echo "<td align='right' onclick='js_processa(\"$codigo\",0,\"$mes\",\"$scodigo\")'>&nbsp $total</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",1,\"$mes\",\"$scodigo\")'>&nbsp $duvidas</td>";
    echo "<td align='right'>&nbsp $perc_duvidas %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",2,\"$mes\",\"$scodigo\")'>&nbsp $duvidas_finalizados</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",3,\"$mes\",\"$scodigo\")'>&nbsp $erros</td>";
    echo "<td align='right'>&nbsp $perc_erros %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",4,\"$mes\",\"$scodigo\")'>&nbsp $erros_finalizados</tb>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",5,\"$mes\",\"$scodigo\")'>&nbsp $melhorias</td>";
    echo "<td align='right'>&nbsp $perc_melhorias %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",6,\"$mes\",\"$scodigo\")'>&nbsp $melhorias_finalizados</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",7,\"$mes\",\"$scodigo\")'>&nbsp $base</td>";
    echo "<td align='right'>&nbsp $perc_base %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",8,\"$mes\",\"$scodigo\")'>&nbsp $base_finalizados</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",8,\"$mes\",\"$scodigo\")'>&nbsp $outros</td>";
    echo "</tr>";
    $totate += $total;
    $totduv += $duvidas;
    $totfduv+= $duvidas_finalizados;
    $toterr += $erros;
    $totferr+= $erros_finalizados;
    $totmel += $melhorias;
    $totfmel+= $melhorias_finalizados;
    $totbas += $base;
    $totfbas+= $base_finalizados;
    $totout+= $outros;
  }
  echo "<tr>";
  if($area!=9999){
    echo "<td bgcolor='lightgreen' align='right'>Total:</td>";
    if( $tipoconsulta > 0){
      echo "<td bgcolor='lightgreen' align='right'>&nbsp</td>";
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $totate</td>";
    }else{
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $totate</td>";
    }
  }else{
    if( $tipoconsulta > 0){
      echo "<td bgcolor='lightgreen' align='right'>Total:</td>";
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $totate</td>";
    }else{
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $totate</td>";
    }
  }
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totduv</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp ".round($totduv*100/($totate==0?1:$totate))." %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totfduv</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $toterr</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp ".round($toterr*100/($totate==0?1:$totate))." %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totferr</tb>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totmel</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp ".round($totmel*100/($totate==0?1:$totate))." %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totfmel</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totbas</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp ".round($totbas*100/($totate==0?1:$totate))." %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totfbas</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $totout</td>";
  echo "</tr>";
  echo "</table>";


  // percentuais por tipo em relação ao total
  echo "<br><strong>Percentuais calculados pelo total de atendimentos da coluna em análise.</strong><br>";

  echo "<table width='100%' border='1'>";
  echo "<tr>";
  if($area != 9999){
    echo "<td bgcolor='lightgreen' ><a href'#' onclick='js_ordena(\"filtro\");return false;'>Totalizador</a></td>";
  }
  if($tipoconsulta > 0){
    echo "<td bgcolor='lightgreen' ><a href'#' onclick='js_ordena(\"mes\");return false;'>";
    if( $tipoconsulta == 1 ){
      echo "Mês";
    }else if( $tipoconsulta == 2 ){
      echo "Módulo";
    }else if( $tipoconsulta == 3 ){
      echo "Atendente";
    }else if( $tipoconsulta == 4 ){
      echo "Departamento";
    }else if( $tipoconsulta == 5 ){
      echo "Procedimento";
    }else if( $tipoconsulta == 6 ){
      echo "Clientes";
    }
    echo "</a></td>";  
  }
  echo "<td bgcolor='lightgreen' title='Total de Atendimentos' align='center'><a href'#' onclick='js_ordena(\"total\");return false;'>Atendimentos</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual do Total de Atendimentos' align='center'>% Atend.</td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Dúvidas' align='center'><a href'#' onclick='js_ordena(\"duvidas\");return false;'>Dúvidas</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Dúvidas' align='center'>% At.</td>";
  echo "<td bgcolor='lightgreen' title='DÃºvidas Finalizadas' align='center'><a href'#' onclick='js_ordena(\"duvidas_finaluzadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Erros' align='center'><a href'#' onclick='js_ordena(\"erros\");return false;'>Erros</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Erros' align='center'>% At.</td>";
  echo "<td bgcolor='lightgreen' title='Erros Finalizados' align='center'><a href'#' onclick='js_ordena(\"erros_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Melhorias' align='center'><a href'#' onclick='js_ordena(\"melhorias\");return false;'>Melhorias</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Melhorias' align='center'>% At.</td>";
  echo "<td bgcolor='lightgreen' title='Melhorias Finalizadas' align='center'><a href'#' onclick='js_ordena(\"melhorias_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Atendimentos de Base de Dados' align='center'><a href'#' onclick='js_ordena(\"base\");return false;'>Base Dados</a></td>";
  echo "<td bgcolor='lightgreen' title='Percentual dos Atendimentos de Base de dados' align='center'>% At.</td>";
  echo "<td bgcolor='lightgreen' title='Base de dados Finalizadas' align='center'><a href'#' onclick='js_ordena(\"base_finalizadas\");return false;'>Final.</a></td>";
  echo "<td bgcolor='lightgreen' title='Outros Tipos de Atendimentos' align='center'><a href'#' onclick='js_ordena(\"outros\");return false;'>Outros</a></td>";
  echo "</tr>";
  $xtotate = 0;
  $xtotduv = 0;
  $xtotfduv= 0;
  $xtoterr = 0;
  $xtotferr= 0;
  $xtotmel = 0;
  $xtotfmel= 0;
  $xtotbas = 0;
  $xtotfbas= 0;
  $xtotout= 0;
  for($i=0;$i<pg_numrows($result);$i++){
    db_fieldsmemory($result,$i);
    echo "<tr>";
    if($area!=9999){
      echo "<td bgcolor='lightgreen' align='left'>$filtro</td>";
    }
    if(!isset($scodigo)){
      $scodigo = "";
    }
    if( $tipoconsulta > 0){
      
      echo "<td align='left'>$mes</td>";
      if( $tipoconsulta == 2 ){
        $scodigo = " codmod-$scodigo ";
      }else if( $tipoconsulta == 3 ){
        $scodigo = " id_usuario-$scodigo ";
      }else if( $tipoconsulta == 4 ){
        $scodigo = " coddepto-$scodigo ";
      }else if( $tipoconsulta == 5 ){
        $scodigo = " codproced-$scodigo ";
      }else if( $tipoconsulta == 6 ){
        $scodigo = " at01_codcli-$scodigo ";
      }else{
        $scodigo = "";
      }
    }else{
      $mes = 0;
    }
    echo "<td align='right' onclick='js_processa(\"$codigo\",0,\"$mes\",\"$scodigo\")'>&nbsp $total</td>";
    echo "<td align='right'>&nbsp ".round($total*100/($totate==0?1:$totate))." %</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",1,\"$mes\",\"$scodigo\")'>&nbsp $duvidas</td>";
    echo "<td align='right'>&nbsp ".round($duvidas*100/($totduv==0?1:$totduv))." %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",2,\"$mes\",\"$scodigo\")'>&nbsp $duvidas_finalizados</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",3,\"$mes\",\"$scodigo\")'>&nbsp $erros</td>";
    echo "<td align='right'>&nbsp ".round($erros*100/($toterr==0?1:$toterr))." %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",4,\"$mes\",\"$scodigo\")'>&nbsp $erros_finalizados</tb>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",5,\"$mes\",\"$scodigo\")'>&nbsp $melhorias</td>";
    echo "<td align='right'>&nbsp ".round($melhorias*100/($totmel==0?1:$totmel))." %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",6,\"$mes\",\"$scodigo\")'>&nbsp $melhorias_finalizados</td>";
    echo "<td bgcolor='lightgreen' align='right' onclick='js_processa(\"$codigo\",7,\"$mes\",\"$scodigo\")'>&nbsp $base</td>";
    echo "<td align='right'>&nbsp ".round($base*100/($totbas==0?1:$totbas))." %</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",8,\"$mes\",\"$scodigo\")'>&nbsp $base_finalizados</td>";
    echo "<td align='right' onclick='js_processa(\"$codigo\",1,\"$mes\",\"$scodigo\")'>&nbsp $outros</td>";
    echo "</tr>";
    $xtotate += $total;
    $xtotduv += $duvidas;
    $xtotfduv+= $duvidas_finalizados;
    $xtoterr += $erros;
    $xtotferr+= $erros_finalizados;
    $xtotmel += $melhorias;
    $xtotfmel+= $melhorias_finalizados;
    $xtotbas += $base;
    $xtotfbas+= $base_finalizados;
    $xtotout += $outros;
  }
  echo "<tr>";
  if($area!=9999){
    echo "<td bgcolor='lightgreen' align='right'>Total:</td>";
    if( $tipoconsulta > 0){
      echo "<td bgcolor='lightgreen' align='right'>&nbsp</td>";
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotate</td>";
    }else{
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotate</td>";
    }
  }else{
    if( $tipoconsulta > 0){
      echo "<td bgcolor='lightgreen' align='right'>Total:</td>";
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotate</td>";
    }else{
      echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotate</td>";
    }
  }
  echo "<td bgcolor='lightgreen' align='right'>&nbsp 100%</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotduv</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp 100%</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotfduv</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $toterr</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp 100 %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotferr</tb>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotmel</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp 100 %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotfmel</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotbas</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp 100 %</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotfbas</td>";
  echo "<td bgcolor='lightgreen' align='right'>&nbsp $xtotout</td>";
  echo "</tr>";
  echo "</table>";









}

?>
	</td>
  </tr>
</table>
</form>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>