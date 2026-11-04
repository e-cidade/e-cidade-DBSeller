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

//session_start();
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));

?>
<html>
<head>
<style type="text/css">

table{
  margin-top: -1rem;
}

th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
  color: #FFFFFF;
  background-color: #18171B;

}

td {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
  color: #000000;
  padding: 1rem;
}

body{
    background-color: #CCCCCC;
    height: 100%;
}

form{
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    min-height: 10vh;
}
.botaoEnviar{
    width: 120px;
    padding: 6px 12px;
    border: 1px;
    border-radius: 6px;
    background-color: #4A789C;
    font-size: 16px;
    color: #FFFFFF;
}

</style>
<meta http-equiv="Content-Type" content="text/html; CHARSET=ISO-8859-1">
</head>

  <body bgcolor=#CCCCCC bgcolor="#CCCCCC" leftmargin="0" topmargin="10" marginwidth="0" marginheight="0" onLoad="js_iniciar();">
    <form>
      <div id="botao">
        <input type="submit" name="Atualizar" value="Atualizar" class="botaoEnviar" onClick="location.reload()"/>
      </div>
    </form>
  <center>
    <table id="tabUsu" border="1" cellpadding="3" cellspacing="0" bordercolor="#FFFFFF">
      <tr bgcolor="#474242">      
        <th>Base</th>
        <th>Pid</th>
        <th>Usuário</th>
	    <th>Menu</th>
        <th>Fonte</th>
        <th>Item Menu</th>
	    <th>Estado</th>
        <th>Data-Hora Início</th>
      </tr>
    <?php

    $sql = "
    SELECT *
    FROM
      (SELECT datname AS base,
              pid,
    
         (SELECT id_usuario || ' - ' || login
          FROM db_usuarios
          WHERE id_usuario = (split_part(application_name, '_', 2))::integer) AS idusuario_login,
              CASE
                  WHEN split_part(application_name,'_',3)::integer <> 0 THEN fc_montamenu(
                    (SELECT funcao
                     FROM db_itensmenu
                     WHERE id_item = split_part(application_name,'_',3)::integer))
                  ELSE ''
              END AS menu,
              CASE
                  WHEN split_part(application_name,'_',3)::integer <> 0 THEN
                         (SELECT funcao
                          FROM db_itensmenu
                          WHERE id_item = split_part(application_name,'_',3)::integer)
                  ELSE ''
              END AS programa,
              CASE
                  WHEN split_part(application_name,'_',3)::integer <> 0 THEN split_part(application_name,'_',3)::integer
                  ELSE NULL
              END AS item_menu,
              STATE,
              to_char(backend_start, 'DD/MM HH24:MI')
       FROM pg_stat_activity
       WHERE application_name ILIKE 'ecidade_%'
         AND pid <> pg_backend_pid()
       ORDER BY 8 DESC) AS xxx; ";

    //echo $sql ; exit;

    $result = pg_query($sql);

    while ($processos = pg_fetch_object($result)) {
      echo "<tr>
		    <td nowrap>".$processos->base."</td>
		    <td nowrap>".$processos->pid."</td>
		    <td nowrap>".$processos->base."</td>
		    <td nowrap>".$processos->pid."</td>
            <td nowrap>".$processos->idusuario_login."</td>
            <td nowrap>".$processos->menu."</td>
            <td nowrap>".$processos->programa."</td>
            <td nowrap>".$processos->item_menu."</td>
            <td nowrap>".$processos->state."</td>
            <td nowrap>".$processos->to_char."</td>  
		</tr>\n";
    }	
?>

</table>
</center>
</body>
</html>
<script>
  function js_iniciar() {
    setTimeout("location.reload()", 30 * 1000);  
  }
</script>
