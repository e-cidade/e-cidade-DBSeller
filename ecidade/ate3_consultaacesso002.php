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
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_pesquisaitemcad(codcli,codmod,item){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_acesso_item','ate3_consultaacesso003.php?codcli='+codcli+'&codmod='+codmod+'&coditem='+item+'&dataini=<?=$dataini?>&datafim=<?=$datafim?>','Pesquisa',true,'30');
}
function js_processamodulo(item){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_acesso_','ate3_consultaacesso002.php?tipototal=1&codcli=<?=$codcli?>&codmod='+item+'&dataini=<?=$dataini?>&datafim=<?=$datafim?>','Pesquisa',true,'30');
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<?php

if($tipototal==1){




  /***************/
  function submenus($item,$id,$mod) {
    global $conta;
    global $wid;
    global $ambiente;
    global $libcliente;
    global $HTTP_POST_VARS;
    global $matcli;
    global $codcli;
		
    $sub = db_query("
    select m.id_item_filho,i.descricao,i.help,i.funcao,m.id_item,m.modulo, libcliente
    from db_menu m 
         inner join db_itensmenu i on i.id_item = m.id_item_filho 
    where m.modulo = $mod and m.id_item = $item and i.itemativo = 1
    order by menusequencia
    ");			  

    $numrows = pg_numrows($sub);
    if($numrows > 0) {
      for($x = 0;$x < $numrows;$x++) {
        $libcliente = pg_result($sub,$x,"libcliente");
        $valor = pg_result($sub,$x,"id_item_filho");
        $funcao= pg_result($sub,$x,"funcao");
        
        echo "<img src=\"imagens/alinha.gif\" height=\"5\" id=\"Img".$conta."\" width=\"".$wid."\" >";
        if($funcao != "" && isset($matcli[$valor]) ){
          echo "<input size=\"1\" onClick=\"js_pesquisaitemcad($codcli,$mod,$valor)\" type=\"button\" id=\"ID$valor\" name=\"CHECK$valor\" ".($libcliente=="f"?"style=\"background-color:blue\" title=\"Bloqueado Cliente\"":"")." value=\"".(isset($matcli[$valor])?$matcli[$valor]:0)."\" >";
        }
        echo "<label for=\"ID$valor\">".pg_result($sub,$x,"descricao")."</label>";			

		echo "<br>\n";
        $wid += 15;
        $conta++;
        submenus(pg_result($sub,$x,"id_item_filho"),$id,$mod);
        $wid -= 15;
      }
    }
  }
  /**************/
  ?>
  <table border="1" width="100%" height="100%" cellspacing="0" cellpadding="0">	  
  <tr> 
  <td colspan='5'> 
  <?php
  
  if($codcli==0){
    $sql = "select at99_itensacesso,nome_modulo,count(*) as at99_acessos
          from acesso_clientes 
               inner join clientes on at01_codcli = at99_codcli 
               inner join db_modulos on id_item = at99_itemcodmod 
          where at99_itemcodmod = $codmod
            and at99_data between '$dataini' and '$datafim' 
          group by nome_modulo,at99_itensacesso
          ";
  }else{
    $sql = "select at01_nomecli,at99_itensacesso,nome_modulo,count(*) as at99_acessos
          from acesso_clientes 
               inner join clientes on at01_codcli = at99_codcli 
               inner join db_modulos on id_item = at99_itemcodmod 
          where ".($codcli!=0?"at99_codcli = $codcli and":"")." at99_itemcodmod = $codmod
            and at99_data between '$dataini' and '$datafim' 
          group by at01_nomecli,nome_modulo,at99_itensacesso
          ";
  }
$result_cli = db_query($sql);
  
for($i=0;$i<pg_numrows($result_cli);$i++){
  db_fieldsmemory($result_cli,$i);
  $matcli[$at99_itensacesso] = $at99_acessos;
}
  
  if($codcli==0){
    echo "<strong>Módulo:</strong> ".pg_result($result_cli,0,'nome_modulo');
  }else{
    echo "<strong>Cliente:</strong> ".pg_result($result_cli,0,'at01_nomecli')." <strong>Módulo:</strong> ".pg_result($result_cli,0,'nome_modulo');
  }
  
?>
  </td>
  </tr>
  <tr> 
  <td> 
<?php

$SQL = "select i.id_item as pai,m.id_item,m.id_item_filho,m.modulo,i.descricao,i.help,i.funcao 
  from db_itensmenu i 
  inner join db_menu m 
  on m.id_item_filho = i.id_item 
  where m.modulo = $codmod
  and i.itemativo = 1							   
  and m.id_item = $codmod order by menusequencia ";
  
  
  $wid = 15;
  
  $result = db_query($SQL);			
  for($i = 0;$i < pg_numrows($result);$i++) {
    $valor = pg_result($result,$i,"id_item_filho");
    echo "<td id=\"col$i\" valign=\"top\" nowrap>\n
    <label for=\"ID$valor\">".pg_result($result,$i,"descricao")."</label><br>\n";
    submenus(pg_result($result,$i,"pai"),"col".$i,db_strpos($codmod,"##"));
    echo "</td>\n";
  }	   


echo "</tr>";
echo "</table>";


}else{

  $sql = "select at01_nomecli 
          from clientes 
          where at01_codcli = $codcli";
  $result_cli = db_query($sql);

  echo "<strong>Cliente:</strong> ".pg_result($result_cli,0,'at01_nomecli');
  
  $sql = "select at25_descr as nome_modulo 
          from atendcadarea 
          where at26_sequencial = $codmod";
  $result_cli = db_query($sql);
  echo "<strong> Área:</strong> ".pg_result($result_cli,0,'nome_modulo');

  $sql = "select at99_itemcodmod,nome_modulo,count(*) as dl_acessos
          from acesso_clientes 
               inner join clientes on at01_codcli = at99_codcli 
               inner join db_modulos on id_item = at99_itemcodmod 
               inner join atendcadareamod on at26_id_item = at99_itemcodmod 
               inner join atendcadarea on at26_sequencial = at26_codarea     
          where at99_codcli = $codcli and at26_codarea = $codmod
            and at99_data between '$dataini' and '$datafim' 
          group by at99_itemcodmod,nome_modulo
          ";



  db_lovrot($sql,50,"()","","js_processamodulo|at99_itemcodmod");

}



?>
</body>
</html>