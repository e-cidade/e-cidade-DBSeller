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
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>
function js_relatorio(){
  jan = window.open('','rel','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
  jan.moveTo(0,0);
  return true;
}

</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<center> 
<form name="form1" method="post" action="sau2_cgscorretos002.php" target="rel">
	<table>
	    <tr height="25px">
	    	<td></td>
	    	<td></td>
	    </tr>
		<tr>
		    <td width="400px">
		        <fieldset><Legend><strong>CGSs Unificados</strong></legend>	    			
		        <table align="center">             
                    <tr>  
					  <td>
						 <label for="ordem"><b>Ordenar por:</b></label>
					  </td>   
                	  <td>
                	    <select name="ordem" >
                	        <option value="z01_v_nome">Nome</option>
                	        <option value="s127_i_numcgs">CGS</option>
							<option value="s127_i_codigo">Código</option>
                	    </select>
                	  </td>
                	</tr>
                	<tr>
                	  <td>
                	    <label for='l'><b>Mostrar Log's:</b></label>
                	  </td>
                	  <td>
                	    <input type="checkbox" id='l' name="log">
                	  </td>
                	</tr>                 
                </table>
	            </fieldset>
	        </td>
	    </tr>
	</table>
	<br>
	<button onClick="return js_relatorio()" type="submit" id="imprimir">Imprimir</button>
</form>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>