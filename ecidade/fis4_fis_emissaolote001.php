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
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("libs/db_utils.php");
require_once  modification("classes/db_db_config_classe.php");
require_once  modification("dbforms/db_funcoes.php");
require_once  modification("dbforms/db_classesgenericas.php");

db_postmemory($_POST);

$erro   = false;
$instit = db_getsession("DB_instit");
$clrotulo           = new rotulocampo;

$cldb_config         = new cl_db_config;

$db_opcao = 1;
$db_botao = true;
$situacao = 0;

$iInstitSessao = db_getsession("DB_instit");
$result = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc"));
db_fieldsmemory($result, 0);

$_debug = false;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, prototype.js, estilos.css");
?>
</head>
<body class="body-default" onLoad="a=1">
<?php
  include modification("forms/db_frm_fis_procemissaolote.php");
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if (isset($imprimir)) {
	$codinfra    = "";
	$procFiscais = "";
	$levanta     = "";
	$contProc    = 0;
	$virgula     = "";

	foreach ($_POST as $value){
		if(substr($value,0,4) == "chk-"){
			$Item = substr($value,4);
			$xItem = explode("||",$Item);
			if($contProc > 0){
				$codinfra    .= ",";
				$procFiscais .= ",";
				$levanta     .= ",";
			}
			$codinfra    .= $xItem[0];
			$procFiscais .= $xItem[1];
			$levanta     .= $xItem[2];
			$contProc++;
		}
	}

	if($procFiscais == ""){
		db_msgbox("Informe os códigos para imprimir os relatórios!");
		exit;
	}

	if((isset($levantamento) && $levantamento == "1") || (isset($objetofiscal) && $objetofiscal == "1")){

		if($pecafiscal == "2" && $objetofiscal == "1"){

		    echo "<script>";
		    echo "window.open('fis2_fis_notlanclote002.php?procfiscalini=$procFiscais&codlote=$y122_codigo','',
		                      'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');";
		    echo "jan.moveTo(0,0);";
		    echo "</script>";
		    $tabela = "fis_levantlotearqlanc";
		    $campo  = "y126_dataemissao";
		    $campolev = "y126_levanta";

	    } else if($pecafiscal == "1" && $objetofiscal == "1"){

	    		echo "<script>";
	    		echo "jan = window.open('fis2_fis_autolote002.php?procfiscalini=$procFiscais&auto=$codinfra&codlote=$y122_codigo','','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');";
	    		echo "jan.moveTo(0,0);";
				echo "</script>";
				$tabela = "fis_levantlotearqauto";
				$campo  = "y125_dataemissao";
				$campolev = "y125_levanta";

		}

	    if($levantamento == "1"){

	   		echo "<script>";
		    echo "window.open('fis2_fis_levantamentolote002.php?procfiscalini=$procFiscais&codlote=$y122_codigo&pecafiscal=$pecafiscal','',
		                      'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');";
		    echo "jan.moveTo(0,0);";
		    echo "</script>";

	    }
	    $codItem = explode(",",$levanta);
	    $hoje = date('Y-m-d');
	    for($abc = 0; $abc < count($codItem); $abc++){
	    	$LevItem = $codItem[$abc];
	    	$sqlUp = " UPDATE $tabela SET $campo = '$hoje' WHERE $campolev = $LevItem ";
	    	db_query($sqlUp);
	    }

	} else {
		$sqlerro = true;
		db_msgbox("Informe o tipo de relatório que deseja imprimir!");
		exit;
	}

}
?>
