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

/**
 * Carregamos as variáveis passadas por GET para o objeto $oGet.
 */
$oGet = db_utils::postMemory($_GET);

/**
 * Carregamos as DAOs necessárias e chamamos os métodos que carregam os labels.
 */
$oDaoIptuEnder = db_utils::getDao('iptuender');
$oDaoCgm       = db_utils::getDao('cgm');
$oDaoIptuEnder->rotulo->label();
$oDaoCgm->rotulo->label();

/**
 * Verificamos a funcionalidade atual do formulário e alteramos o label
 * conforme a necessidade.
 */
$sLabelFormulario = "Manutenção de Endereço de Entreda";

if (isset($oGet->db_opcao) && $oGet->db_opcao == 3) {
  $sLabelFormulario = "Exclusão de Endereço de Entrega";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

	<center>
	  <?php 
	    $sUrlFornulario = "cad4_iptuendergrupo002.php?db_opcao=1";
	    if (isset($oGet->db_opcao) && $oGet->db_opcao == "3") {
	    	$sUrlFornulario = "cad4_iptuendergrupo002.php?db_opcao=3";
	    }
	  ?>
		<form method="post" action="<?php echo $sUrlFornulario; ?>" onsubmit="return js_verificaFormulario();">
			<fieldset style="margin-top: 30px; width:390px;">
				<legend><strong><?php echo $sLabelFormulario; ?></strong></legend>
				<table>
					<tr>
						<td>
							<?php 
								db_ancora($Lj43_matric, 'js_pesquisaMatricula(true);', 1);
							?>
						</td>
						<td>
							<?php 
								db_input('j43_matric', 5, 0, true, 'text', 1, 'onchange="js_pesquisaMatricula(false);"');
								db_input('z01_nome', 30, 0, true, 'text', 3);
							?>
						</td>
					</tr>
					<tr>
						<td>
							<?php 
								db_ancora($Lz01_numcgm, 'js_pesquisaCgm(true);', 1);
							?>
						</td>
						<td>
							<?php
								db_input('z01_numcgm', 5, 0, true, 'text', 1, 'onchange="js_pesquisaCgm(false);"');
								db_input('z01_nome_cgm', 30, 0, true, 'text', 3);
							?>
						</td>
					</tr>
				</table>
			</fieldset>
			<input type="hidden" name="db_opcao" id="db_opcao" value="<?php $oGet->db_opcao; ?>" />
			<input style="margin-top: 5px;" type="submit" value="Prosseguir" />
		</form>
	</center>

	<?php 
		db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
	?>

</body>
</html>
<script>
/**
 * Função que valida se o formulário está devidamente preenchido antes de o mesmo for submetido.
 */
function js_verificaFormulario() {

	var iMatriculaFormulario = $F('j43_matric');
	var iCgmFormulario       = $F('z01_numcgm');

	if (iMatriculaFormulario == '' && iCgmFormulario == '') {

		alert('Para proseguir deve ser informado uma matrícula ou um CGM. Favor verificar.');
		return false;
	}

	return true;
}

/**
 * Função que efetua a pesquisa de matrícula.
 */
function js_pesquisaMatricula(mostra) {

	if (mostra == true) {
		js_OpenJanelaIframe('CurrentWindow.corpo', 
				                'db_iframe_matricula', 
				                'func_iptubase.php?funcao_js=parent.js_retornoPesquisaMatricula|j01_matric|z01_nome',
				                'Pesquisa Matrículas',
				                true);
	} else {

		var iMatriculaFormulario = $F('j43_matric');
		if (iMatriculaFormulario == '') {
			$('z01_nome').value = '';
		} else {
			js_OpenJanelaIframe('CurrentWindow.corpo',
					                'db_iframe_matricula',
					                'func_iptubase.php?pesquisa_chave='+iMatriculaFormulario+
					                '&funcao_js=parent.js_retornoPesquisaMatricula',
					                'Pesquisa Matrículas',
					                false);
		}
	}
}

/**
 * Função que recebe o retorno da pesquisa de matrícula e trata o resultado.
 */
function js_retornoPesquisaMatricula() {

	db_iframe_matricula.hide();
	if (arguments[1] == true) {

		$('z01_nome').value   = arguments[0];
		$('j43_matric').value = '';
	} else if (arguments[1] == false) {
		$('z01_nome').value   = arguments[0];
	} else {

		$('z01_nome').value   = arguments[1];
		$('j43_matric').value = arguments[0];
	}
}

/**
 * Função que efetua a pesquisa de CGM.
 */
function js_pesquisaCgm(mostra) {

	if (mostra == true) {
		js_OpenJanelaIframe('CurrentWindow.corpo',
				                'db_iframe_cgm',
				                'func_cgm.php?funcao_js=parent.js_retornoPesquisaCgm|z01_numcgm|z01_nome',
				                'Pesquisa CGM',
				                true);
	} else {

		var iCgmFormulario = $F('z01_numcgm');
		if (iCgmFormulario == ''){
			$('z01_nome_cgm').value = '';
		} else {
			js_OpenJanelaIframe('CurrentWindow.corpo',
					                'db_iframe_cgm',
					                'func_cgm.php?pesquisa_chave='+iCgmFormulario+
					                '&funcao_js=parent.js_retornoPesquisaCgm',
					                'Pesquisa CGM',
					                false);
		}
	}
}

/**
 * Função que recebe o retorno da pesquisa de cgm e trata o resultado.
 */
function js_retornoPesquisaCgm() {

	db_iframe_cgm.hide();
	if (arguments[0] == true) {

		$('z01_numcgm').value   = '';
		$('z01_nome_cgm').value = arguments[1];
	} else if (arguments[0] == false) {
		$('z01_nome_cgm').value = arguments[1];
	} else {

		$('z01_numcgm').value   = arguments[0];
		$('z01_nome_cgm').value = arguments[1];
	}
}
</script>