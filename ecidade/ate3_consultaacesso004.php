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

  if($codcli!=0){
    $sql = "select at10_nome 
          from db_usuclientes
          where at10_codcli = $codcli and at10_usuario = $codusu";
    $result_cli = db_query($sql);
    echo "<strong> Usuário: </strong>".pg_result($result_cli,0,'at10_nome');
  }
  if($codcli!=0){
    $sql = "select at99_itensacesso,descricao,at99_data,count(*) as dl_acessos
          from acesso_clientes 
               inner join db_itensmenu on id_item = at99_itensacesso
          where at99_codcli = $codcli and at99_itemcodmod = $codmod and at99_id_usuario = $codusu
               and at99_data between '$dataini' and '$datafim' and id_item = $coditem
          group by at99_itensacesso,descricao,at99_data
          order by at99_data desc
          ";
  }else{
    $sql = "select at01_nomecli,at99_itensacesso,descricao,at99_data,count(*) as dl_acessos
          from acesso_clientes 
               inner join db_itensmenu on id_item = at99_itensacesso
               inner join clientes on at01_codcli = at99_codcli
          where at99_itemcodmod = $codmod and at99_id_usuario = $codusu
               and at99_data between '$dataini' and '$datafim' and id_item = $coditem
          group by at01_nomecli,at99_itensacesso,descricao,at99_data
          order by at99_data desc
          ";
  }
db_lovrot($sql,50,"()","","");
  




?>
</tr>
</table>
</body>
</html>