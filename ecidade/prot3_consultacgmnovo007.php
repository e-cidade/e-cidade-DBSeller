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
require_once(modification("model/CgmFactory.model.php"));

$oGet = db_utils::postMemory($_GET, false);

if (isset($oGet->cgm)) {
  
  $oCgm = CgmFactory::getInstance('', $oGet->cgm);
}

$oDaoCgm      = db_utils::getDao('cgm');
$sSqlBuscaCGM = $oDaoCgm->sql_query_file($oCgm->getCodigo(), "*", null, "");
$rsBuscaCGM   = $oDaoCgm->sql_record($sSqlBuscaCGM);
$oRegistroCgm = db_utils::fieldsMemory($rsBuscaCGM, 0);

$oDaoDbUsuarios   = db_utils::getDao('db_usuarios');
$sSqlBuscaUsuario = $oDaoDbUsuarios->sql_query_file($oRegistroCgm->z01_login, 'nome');
$rsBuscaUsuario   = $oDaoDbUsuarios->sql_record($sSqlBuscaUsuario);
$oRegistroUsuario = db_utils::fieldsMemory($rsBuscaUsuario, 0);

$clcgmalt = new cl_cgmalt();

$rsVerificaAlt = $clcgmalt->sql_record($clcgmalt->sql_query_file(null, "*", null, "z05_numcgm = $oGet->cgm and z05_tipo_alt = 'A'"));
?>
<html>
<head>
<title>Dados do Cadastro de Veículos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type='text/css'>
.valores {background-color:#FFFFFF; width:35%;}
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<table>
		<tr>
			<td>
				<strong>Login: </strong>
			</td>
			<td class="valores">
				<?php 
				  echo $oRegistroUsuario->nome;
				?>
			</td>
			<td>
				<strong>Data Cadastro: </strong>
			</td>
			<td class="valores">
				<?php 
				  echo implode('/', array_reverse(explode('-', $oRegistroCgm->z01_cadast)));
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php if ($clcgmalt->numrows > 0) { ?>
					<strong style="color:blue;">
						<a  href="#" onclick="js_pesquisacgmalt('<?php echo $oGet->cgm ?>');">Alterações:</a>
					</strong>
				<?php } else { ?>
					<strong>Ultima Alteração: </strong>
				<?php } ?>
			</td>
			<td class="valores">
				<?php 
				  echo implode('/', array_reverse(explode('-', $oRegistroCgm->z01_ultalt)));
				?>
			</td>
		</tr>
	</table>
</body>

<script>

	function js_pesquisacgmalt(numcgm) {
		js_OpenJanelaIframe(
			'parent',
			'db_iframe_cgmaltres',
			'func_cgmaltresum.php?pesquisa_numcgm=' + numcgm + '&funcao_js=parent.js_mostracgmalt|z05_sequencia|z05_numcgm',
			'Pesquisa',
			true,
			0,
			0,
			screen.availWidth - 100
		);
	}
</script>
</html>