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
include(modification("libs/db_liborcamento.php"));
include(modification("libs/db_libcontabilidade.php"));

$iAnoUsu            = db_getsession("DB_anousu");
$oGet               = db_utils::postMemory($_GET);
$codigoRelatorio    = $oGet->c83_codrel;

$oRelatorio = new relatorioContabil($codigoRelatorio, false);
$clrotulo   = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');
$sTitulo    = $oRelatorio->getDescricao();
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">

<script>
  
	function js_emite(){

    if (document.form1.o116_periodo.value == 0) {

      alert("Selecione um período!");
      return;
    }

    query = "periodo="+document.form1.o116_periodo.value;

    jan = window.open('con2_balancoorcamentariorpps_002_2014.php?'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }
</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
  <table  align="center" width="450">
    <form name="form1" method="post" action="" >
      <tr>
         <td >&nbsp;</td>
         <td >&nbsp;</td>
      </tr>
			<tr>
        <td colspan="3" class='table_header'><?php echo $sTitulo; ?></td>
    		</td>
  		</tr>
      <tr>
        <td align="center" colspan="3">
	  			<fieldset>
						<legend>
							 <b>Selecionar Período</b>
						</legend>
						<table>
          		<tr>
            		<td align="right" >
										<strong>Período :</strong> </td>
            		<td align="left" >
                  <?php
                  $aPeriodos         = $oRelatorio->getPeriodos();
                  $aListaPeriodos    = array();
                  $aListaPeriodos[0] = "Selecione";

                  foreach ($aPeriodos as $oPeriodo) {
                    $aListaPeriodos[$oPeriodo->o114_sequencial] = $oPeriodo->o114_descricao;
                  }

                  db_select("o116_periodo", $aListaPeriodos, true, 1);
             			?>
            		</td>
           		</tr>
	  			  </table>
	  			</fieldset>
				</td>
      </tr>
      <tr>
        <td align="center">
          <input  name="emite" id="emite" type="button" value="Imprimir" onclick="js_emite();">
        </td>
      </tr>
  </form>
    </table>
</body>
</html>