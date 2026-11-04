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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
include_once(modification("libs/db_sessoes.php"));
include_once(modification("libs/db_usuariosonline.php"));
include_once(modification("dbforms/db_funcoes.php"));
include_once(modification("libs/db_utils.php"));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">

<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>

<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="js_tipoprocessamento()" bgcolor="#cccccc">
<?php

if (db_getsession("DB_id_usuario") == 1) {
  
?>

<table align="center" border=0 width="100%">
 <tr><td height="15"></td></tr>
 <tr>
  <td>
  <form name="form1" method="POST" action="arr4_manutencaovinculosnumpre002.php">
   <table align="center" border=0 width="600">
     <tr>
      <td height="50">
      </td>
     </tr>

     <tr>
      <td width=192> <strong>Numpre: </strong> </td>
      <td> <?php db_input('numpre',11,1,true,"text",1) ?> </td>
     </tr>
     
     <tr>
      <td width=192> <strong>Tipo de Vinculo: </strong> </td>
      <td>
        <?php 
         $aTipos = array("0"=>"Selecione","1"=>"Matricula","2"=>"Inscrição");
         db_select("tipo", $aTipos,true,1);
        ?> 
      </td>
     </tr>
     
     <tr>
      <td align="center" colspan=2 style='padding:30px'>
       <input type="button" name="btnProcessar" id="btnProcessar" value="Processar" onClick="js_valida()"> 
      </td>
     </tr>
     
   </table>
   </form>
  </td>
 </tr>    
</table>

<?php
 } else {
   db_msgbox("Procedimento não disponível!");
 }
 db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_valida() {

 if ($F("numpre") == "") {
    alert("Informe o numpre");
    return false;
 }

 if ($F("tipo") == "0") {
    alert("Informe o tipo de vinculo");
    return false;
 }
 
 document.form1.submit();
 
} 
</script>