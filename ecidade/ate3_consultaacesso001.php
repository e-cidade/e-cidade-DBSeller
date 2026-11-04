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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_clientes_classe.php"));
include(modification("classes/db_atendcadarea_classe.php"));
include(modification("classes/db_db_modulos_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clclientes          = new cl_clientes;
$clatendcadarea      = new cl_atendcadarea;
$cldb_modulos      = new cl_db_modulos;
$clrotulo            = new rotulocampo; 
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_processa(tipototal,codcli,codmod){
  dataini = document.form1.dataini_ano.value+'-'+document.form1.dataini_mes.value+'-'+document.form1.dataini_dia.value;
  datafim = document.form1.datafim_ano.value+'-'+document.form1.datafim_mes.value+'-'+document.form1.datafim_dia.value;
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pesquisa','ate3_consultaacesso002.php?tipototal='+tipototal+'&codcli='+codcli+'&codmod='+codmod+'&dataini='+dataini+'&datafim='+datafim,'Pesquisa',true,'30');
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
function js_pesquisautilizacao(tipototal,codmod){
  dataini = document.form1.dataini_ano.value+'-'+document.form1.dataini_mes.value+'-'+document.form1.dataini_dia.value;
  datafim = document.form1.datafim_ano.value+'-'+document.form1.datafim_mes.value+'-'+document.form1.datafim_dia.value;
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pesquisa_utilizacao','ate3_consultaacesso002.php?tipototal='+tipototal+'&codcli=0&codmod='+codmod+'&dataini='+dataini+'&datafim='+datafim,'Pesquisa',true,'30');  
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
<form name='form1' method='post'>
<table>
<tr>
<td><strong>Cliente:</strong></td>
<td>
<?php

$resultcli = $clclientes->sql_record($clclientes->sql_query(null,'*','at01_nomecli', "at01_ativo is true"));
if(!isset($cliente)){
  global $cliente;
  $cliente = 0;
}
//db_selectrecord("cliente",$resultcli,true,2,"",'','','0','document.form1.submit()');
db_selectrecord("cliente",$resultcli,true,2,"",'','','0','');

echo "</td>";
echo "<td><strong>Área:</strong></td>";
echo "<td>";

$resultarea = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo","at25_descr"," at26_sequencial in (select at26_codarea from atendcadareamod) "));
if(!isset($area)){
  global $area;
  $area = 0;
}
db_selectrecord("area",$resultarea,true,2,"",'','','0','');

echo "</td>";
echo "<td><strong>Módulo:</strong></td>";
echo "<td>";

$resultmod = $cldb_modulos->sql_record($cldb_modulos->sql_query(null,"id_item as id_modulo,nome_modulo",'nome_modulo' ));
if(!isset($id_modulo)){
  global $id_modulo;
  $id_modulo = 0;
}
db_selectrecord("id_modulo",$resultmod,true,2,"",'','','0','');


?>
</td>
</tr>

  <tr>
    <td colspan='3'>
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
       <select name='tipototal'>
       <option value='1' <?=(isset($tipototal) && $tipototal==1?'selected':'')?>>Por Módulo</option>
       <option value='2' <?=(isset($tipototal) && $tipototal==2?'selected':'')?>>Por Área</option>
       </select>
       
      <input name='ordenar' value='' type='hidden'>
      <input name='ultima_ordenar' value='<?=(isset($pesquisar)?$ordenar:"")?>' type='hidden'>
      <input name='pesquisar' value='Pesquisar' type='submit'>
    </td>
  </tr>
</table>
</form>
<table width='100%' border=1>
<tr>
<?php
if(isset($pesquisar)){

  $sql = "select at99_codcli,
                 at01_nomecli, 
                 at99_itemcodmod, 
                 nome_modulo, 
                 count(*) as totacesso
          from acesso_clientes 
               inner join clientes on at01_codcli = at99_codcli 
               inner join db_modulos on id_item = at99_itemcodmod 
          where at99_data between '$dataini_ano-$dataini_mes-$dataini_dia' and '$datafim_ano-$datafim_mes-$datafim_dia'
          and at01_ativo = true" ;
          
  
          
          
  if($cliente != 0){
    $sql .= " and at99_codcli = $cliente ";
  }
  if($area != 0){
    $sql .= " and at99_itemcodmod in ( select at26_id_item from atendcadareamod where at26_codarea = $area)";
    if($tipototal==1){
      $resultmod = $cldb_modulos->sql_record($cldb_modulos->sql_query(null,"id_item as id_modulo,nome_modulo",'nome_modulo'," id_item in ( select at26_id_item from atendcadareamod where at26_codarea = $area) " ));
    }else{    
      $resultmod = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo",'at25_descr'," at26_sequencial = $area " ));
    }
  }
  if($id_modulo != 0){
    $sql .= " and at99_itemcodmod = $id_modulo ";

    if($tipototal==1){
      $resultmod = $cldb_modulos->sql_record($cldb_modulos->sql_query(null,"id_item as id_modulo,nome_modulo",'nome_modulo'," id_item = $id_modulo" ));
    }else{    
      $resultmod = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo",'at25_descr'," at26_sequencial in ( select at26_codarea from atendcadareamod where at26_id_item = $id_modulo ) " ));
    }

  }
  

  $sql .= "
          group by at99_codcli,at01_nomecli, at99_itemcodmod, nome_modulo
          order by at01_nomecli
          ";
  if($tipototal==2){
    $sql = "select  at99_codcli, at01_nomecli, at26_codarea as at99_itemcodmod, at25_descr as nome_modulo,sum(totacesso) as totacesso 
            from ($sql) as x 
                 inner join atendcadareamod on at26_id_item = at99_itemcodmod 
                 inner join atendcadarea on at26_sequencial = at26_codarea     
            group by at99_codcli, at01_nomecli,at26_codarea,at25_descr 
            order by at99_codcli";
  }
  $result = db_query($sql);
  
  echo "<td  bgcolor='lightgreen' rowspan='2'><strong>Clientes</strong></td>";
  echo "<td  bgcolor='lightgreen' colspan='".(pg_numrows($resultmod)+1)."'><strong>Módulos</strong></td>";
  echo "</tr>";
  echo "<tr>";
  
  if($tipototal==2){
    if($area!=0){
      $resultmod = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo","at25_descr"," at26_sequencial = $area "));
    }else if($id_modulo!=0){
      $resultmod = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo","at25_descr"," at26_sequencial in (select at26_codarea from atendcadareamod where at26_id_item = $id_modulo ) "));
    }else{
      $resultmod = $clatendcadarea->sql_record($clatendcadarea->sql_query(null,"at26_sequencial as id_modulo,at25_descr as nome_modulo","at25_descr"," at26_sequencial in (select at26_codarea from atendcadareamod) "));
    }
  }

  $tmatcli_col = array();
  for($i=0;$i<pg_numrows($resultmod);$i++){
  
    if($tipototal==2){
      echo "<td  bgcolor='lightgreen' title='Área:".pg_result($resultmod,$i,'id_modulo')."'>".pg_result($resultmod,$i,'nome_modulo')."</td>"; 
    }else{
      echo "<td  bgcolor='lightgreen' title='Clique Acesso Geral - Módulo:".pg_result($resultmod,$i,'id_modulo')."'><a href=\"#\" onclick='js_pesquisautilizacao($tipototal,".pg_result($resultmod,$i,'id_modulo').")'>".pg_result($resultmod,$i,'nome_modulo')."</a></td>"; 
    }
    $tmatcli_col[pg_result($resultmod,$i,'id_modulo')] = 0; 
  }  
  echo "<td  bgcolor='lightgreen' title='Total dos Acessos'>Total</td>"; 
  
  $codcli = 0;
  $tmatcli_lin = array();
  
  for($i=0;$i<pg_numrows($result);$i++){
     db_fieldsmemory($result,$i);
     if($codcli==0 || $codcli != $at99_codcli ){
       if($codcli > 0 && $codcli != $at99_codcli){
         $tmatcli_lin[$codcli] = 0;
         for($x=0;$x<pg_numrows($resultmod);$x++){
           db_fieldsmemory($resultmod,$x);
           $cor = "";
           if( $tipototal == 1 ){
             // verifica se tem contratado e esta implantado
             $sql = "select at74_data from clientesmodulos 
                     where at74_id_item = ".$id_modulo." and at74_codcli = $codcli ";
             $ress = db_query($sql);
             if( pg_numrows($ress) > 0 ){
               if( pg_result($ress,0,'at74_data') != "" ){
                 $cor = " bgcolor='lightblue' ";
               }else{
                 if( $matcli[$id_modulo] > 0 ){
                   $cor = " bgcolor='read' ";
                 }
               }
             }else{
               if( $matcli[$id_modulo] > 0 ){
                 $cor = " bgcolor='red' ";
               }
             }
           }
           if(isset($matcli[$id_modulo])){
             echo "<td $cor onclick='js_processa($tipototal,$codcli,$id_modulo)' align='right'>".$matcli[$id_modulo]."</td>";
             $tmatcli_lin[$codcli] += $matcli[$id_modulo];
             $tmatcli_col[$id_modulo] += $matcli[$id_modulo];
           }else{
             echo "<td $cor align='right'>0</td>";
           }
         }         
         echo "<td  bgcolor='lightgreen' align='right'>".$tmatcli_lin[$codcli]."</td>";         
       }
       echo "</tr>";
       echo "<tr>";
       echo "<td bgcolor='lightgreen' title='$at99_codcli' >$at01_nomecli</td>";
       $codcli = $at99_codcli;
       $matcli = array();
     }
     $matcli[$at99_itemcodmod] = $totacesso;
  }
  if(pg_numrows($result)>0){
  
    $tmatcli_lin[$codcli] = 0;
    for($x=0;$x<pg_numrows($resultmod);$x++){
       db_fieldsmemory($resultmod,$x);

       $cor = "";
       if( $tipototal == 1 ){
         // verifica se tem contratado e esta implantado
         $sql = "select at74_data from clientesmodulos 
                 where at74_id_item = ".$id_modulo." and at74_codcli = $codcli ";
         $ress = db_query($sql);
         if( pg_numrows($ress) > 0 ){
           if( pg_result($ress,0,'at74_data') != "" ){
             $cor = " bgcolor='lightblue' ";
           }else{
             if( $matcli[$id_modulo] > 0 ){
               $cor = " bgcolor='read' ";
             }
           }
         }else{
           if( $matcli[$id_modulo] > 0 ){
             $cor = " bgcolor='red' ";
           }
         }
       }


       if(isset($matcli[$id_modulo])){

          echo "<td $cor onclick='js_processa($tipototal,$codcli,$id_modulo)' align='right'>".$matcli[$id_modulo]."</td>";
          $tmatcli_lin[$codcli] += $matcli[$id_modulo];
          $tmatcli_col[$id_modulo] += $matcli[$id_modulo];
       }else{
          echo "<td $cor align='right'>0</td>";
       }
    }         
    echo "<td bgcolor='lightgreen' align='right'>".$tmatcli_lin[$codcli]."</td>";         
    echo "</tr>";
    echo "<tr>";
    echo "<td  bgcolor='lightgreen' align='left'>Total</td>";         
    $totatend=0;
    for($i=0;$i<count($tmatcli_col);$i++){
      echo "<td  bgcolor='lightgreen' align='right'>".$tmatcli_col[ key($tmatcli_col) ]."</td>";         
      $totatend += $tmatcli_col[ key($tmatcli_col) ];
      next($tmatcli_col);
    }
    echo "<td bgcolor='lightgreen' align='right'>".$totatend."</td>";         
    echo "</tr>";
  
  }  
}

?>
</tr>
</table>
<?php
if(isset($pesquisar)){
  echo "<table>";
  echo "<tr>";
  echo "<td bgcolor='lightblue'>&nbsp&nbsp&nbsp&nbsp</td><td>Contratado</td>";
  echo "<td bgcolor='read'>&nbsp&nbsp&nbsp&nbsp</td><td>Contratado Não Implantado</td>";
  echo "<td bgcolor='red'>&nbsp&nbsp&nbsp&nbsp</td><td>Não Contratado</td>";
  echo "</tr>";
  echo "</table>";
}
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>