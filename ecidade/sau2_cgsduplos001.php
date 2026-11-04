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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clcgs_und = new cl_cgs_und();
$clcgs_und->rotulo->label();

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js,dbautocomplete.widget.js");
db_app::load("dbmessageBoard.widget.js, prototype.js, dbtextField.widget.js, dbcomboBox.widget.js");
db_app::load("estilos.css, grid.style.css");
?>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0"
	marginheight="0" bgcolor="#cccccc">
<center>
<form name="form1" method="post">

<table border='0'>
	<tr height="20px">
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>
		<fieldset><Legend><strong>CGSs Múltiplos</strong></legend>
		<table>
			<tr>
				<td>
					<label><b>Nome:</b></label>
				</td>
				<td>
	               <?php
			            db_input ( 'z01_v_nome', 40, $Iz01_v_nome, true, 'text', 1	, "","","")
				   ?>
	            </td>	
			</tr>	
			
			<tr>
				<td>
					<label><b>Nome da mãe:</b></label>
				</td>
				<td>
	               <?php
			            db_input ( 'z01_v_mae', 40, $Iz01_v_mae, true, 'text', 1, "" )
					?>
	            </td>	
			</tr>	

			<tr>
				<td>
					<label><b>Data de nascimento:</b></label>
				</td>
				<td>
	                <?php
				        db_inputdata('z01_d_nasc', '' , '', '', true, 'text', 1);
				    ?>			            
	            </td>	
			</tr>	
			
			<tr height="40px">
		        <td></td>
		        <td></td>
	        </tr>
			<table>
				<tr>
					<td>
						<b>Para visualizar todos os CGSs múltiplos existentes no e-cidade apenas clique em "Imprimir"</b>
					</td>					
				</tr>
			</table>

		</table>
		</fieldset>
		</td>
	</tr>
	<br>
	<tr>
		<td colspan="2" align="center">
			<input name="relatorio" type="button"
			   value="Imprimir" onclick="imprimir();">
		</td>
	</tr>
</table>
</form>
</center>

<script language="JavaScript" type="text/javascript">

function imprimir(){
  let url = "sau2_cgsduplos002.php?nome="+document.form1.z01_v_nome.value;
  url += "&nome_mae="+document.form1.z01_v_mae.value;
  url += "&nascimento="+document.form1.z01_d_nasc.value;

  jan = window.open(url, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0');
  jan.moveTo(0,0);
}
</script>

<?php
   db_menu ( db_getsession ( "DB_id_usuario" ), db_getsession ( "DB_modulo" ), db_getsession ( "DB_anousu" ), db_getsession ( "DB_instit" ) );
?>
</body>
</html>