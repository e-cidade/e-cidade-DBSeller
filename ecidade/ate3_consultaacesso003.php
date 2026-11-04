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
function js_processamodulousu(codmod,usuario){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_acesso_usuario','ate3_consultaacesso004.php?coditem=<?=$coditem?>&codcli=<?=$codcli?>&codmod='+codmod+'&codusu='+usuario+'&dataini=<?=$dataini?>&datafim=<?=$datafim?>','Pesquisa',true,'30');
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<?php
  if($codcli!=0){
    $sql = "select at01_nomecli 
          from clientes 
          where at01_codcli = $codcli";
    $result_cli = db_query($sql);

    echo "<strong>Cliente:</strong> ".pg_result($result_cli,0,'at01_nomecli');
  }
  $sql = "select nome_modulo 
          from db_modulos
          where id_item = $codmod";
  $result_cli = db_query($sql);
  echo "<strong> Módulo: </strong>".pg_result($result_cli,0,'nome_modulo');

  $sql = "select descricao 
          from db_itensmenu
          where id_item = $coditem";
  $result_cli = db_query($sql);
  echo "<strong> Ítem: </strong>".pg_result($result_cli,0,'descricao');

  if($codcli==0){
    $sql = "select at01_nomecli,at99_id_usuario,at10_login,at10_nome,count(*) as dl_acessos,at99_itemcodmod as db_at99_itemcodmod
          from acesso_clientes 
               left join db_usuclientes on at10_codcli = at99_codcli and at10_usuario = at99_id_usuario
               left join clientes on at01_codcli = at99_codcli
          where at99_itemcodmod = $codmod and at99_itensacesso = $coditem
               and at99_data between '$dataini' and '$datafim' 
          group by at01_nomecli,at99_id_usuario,at10_login,at10_nome,at99_itemcodmod
          ";
  }else{
    $sql = "select at99_id_usuario,at10_login,at10_nome,count(*) as dl_acessos,at99_itemcodmod as db_at99_itemcodmod
          from acesso_clientes 
               left join db_usuclientes on at10_codcli = at99_codcli and at10_usuario = at99_id_usuario
          where at99_codcli = $codcli and at99_itemcodmod = $codmod and at99_itensacesso = $coditem
               and at99_data between '$dataini' and '$datafim' 
          group by at99_id_usuario,at10_login,at10_nome,at99_itemcodmod
          ";
  }
db_lovrot($sql,50,"()","","js_processamodulousu|at99_itemcodmod|at99_id_usuario");
  

?>
</tr>
</table>
</body>
</html>