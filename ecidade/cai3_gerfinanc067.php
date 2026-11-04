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

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_POST_VARS);

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  
<?php
if(isset($emite)){

	$data1 = $data1_ano."-".$data1_mes."-".$data1_dia;
	$data2 = $data2_ano."-".$data2_mes."-".$data2_dia;
	echo"
<script>
	location.href ='cai3_gerfinanc068.php?data1=$data1&data2=$data2&cgm=$cgm&iCgm=$iCgm';
</script>
    "; 
}
	
?>
<form name="form1" method="post" action="">

 <center>

  <fieldset style="width: 300px; margin-top: 10px;">
  <legend>Filtros de Pesquisa</legend>

      <table  align="center">
          <tr>
             <td ><strong>CGM:</strong></td>
             <td ><?php db_input("iCgm", 10, '', true, "text", 1);  ?></td>
          </tr>
          <tr>
              <td ><strong>Data inicial : </strong></td>
             <td align='center' >
             <?php
             db_inputdata('data1','','','',true,'text',1,"");
             ?>
             </td>
          </tr>
          <tr>
              <td ><strong>Data final : </strong></td>
             <td>
             <?php
             db_inputdata('data2','','','',true,'text',1,"");
             ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" align = "center">

            </td>
          </tr>
      </table>
  </fieldset>

   <input style="margin-top: 5px;"  name="emite" type="submit" value="Pesquisar"> </center>

</form>

  </body>
</html>
