<?php
require_once(modification("dbforms/db_classesgenericas.php"));

$cliframe_custasliberadasmanutencao = new cl_iframe_alterar_excluir;

$oDaoTaxa->rotulo->label();
$oRotulo = new rotulocampo;
$oRotulo->label("ar53_taxa");
$oRotulo->label("v70_sequencial");
$oRotulo->label("v50_inicial");
$oRotulo->label("v70_codforo");
$oRotulo->label("ar36_sequencial");
$oRotulo->label("ar36_descricao");
$oRotulo->label("ar53_parcelas");
?>

<html>

<head>
	<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Expires" CONTENT="0">
	<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
	<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
	<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
	<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body class="body-default">
	<div class="container">
		<form name="form1" method="post" action="">
			<input type="hidden" name="ar53_sequencial" value="<?php echo (isset($ar53_sequencial) ? $ar53_sequencial : '') ?>">

			<fieldset class="container">
				<legend>Buscar Processos/Iniciais</legend>
				<table>
					<tr>
						<td title="<?= $Tv70_codforo ?>" style="width:fit-content;">
							<?php
							db_ancora($Lv70_codforo, "js_pesquisaprocessoforo(true);", 4);
							?>
						</td>
						<td>
							<?php
							db_input("v70_sequencial", 4, $Iv70_sequencial, true, "text", 1, "onchange='js_pesquisaprocessoforo(false);'");
							db_input("v70_codforo", 40, $Iv70_codforo, true, "text", 3, "");
							?>
						</td>
					</tr>
					<tr>
						<td style="width:fit-content;">
							<?php
							db_ancora("Inicial do Foro:", "js_pesquisaInicial(true);", 4);
							?>
						</td>
						<td>
							<?php
							db_input("iInicial", 20, 1, true, "text", 1, "onchange='js_pesquisaInicial(false);'", null, null, "width:83px;");
							?>
						</td>
					</tr>
				</table>
			</fieldset>

			<input name="buscar" type="submit" id="buscar" value="buscar">

			<?php
			if (isset($opcao) && $opcao == 'alterar') {
			?>

				<fieldset class="container">
					<legend>Custa selecionada</legend>
					<table>
						<tr>
							<td><?= $Lar36_sequencial ?></td>
							<td>
								<?php
								db_input("ar36_sequencial", 20, 1, true, "text", 3, "", null, null);
								?>
							</td>
						</tr>
						<tr>
							<td><?= $Lar36_descricao ?></td>
							<td>
								<?php
								db_input("ar36_descricao", 20, 1, true, "text", 3, "", null, null);
								?>
							</td>
						</tr>
						<tr>
							<td><?= $Lar53_parcelas ?></td>
							<td>
								<input type="number" name="ar53_parcelas" id="ar53_parcelas" value="<?= isset($ar53_parcelas) ? $ar53_parcelas : '' ?>">
							</td>
						</tr>
					</table>
				</fieldset>

				<input type="hidden" name="ar53_sequencial" value="<?= isset($ar53_sequencial) ? $ar53_sequencial : '' ?>">
				<input type="submit" name="db_opcao" id="db_opcao" value="alterar">
			<?php
			};
			?>

			<div class="container">
				<?php
				$chavepri = array("ar53_sequencial" => isset($ar53_sequencial) ? $ar53_sequencial : '');
				$sWhere = "";

				if (isset($iInicial) && !empty($iInicial)) {
					$sWhere .= " and ar53_inicial = $iInicial";
				}

				if (isset($v70_sequencial) && !empty($v70_sequencial)) {
					$sWhere .= " and ar53_processoforo = $v70_sequencial";
				}

				if (!empty($sWhere)) {

					$sWhere .= " and ar53_parcelamento is null";

					$aChavePri = array(
						'ar53_sequencial' => '',
						'ar36_sequencial' => '',
						'ar36_descricao' => '',
						'ar36_perc' => '',
						'ar53_parcelas' => '',
						'nome' => ''
					);

					$clIframeAlterarExcluir                = new cl_iframe_alterar_excluir;
					$clIframeAlterarExcluir->chavepri      = $aChavePri;
					$clIframeAlterarExcluir->sql           = $oDaoTaxa->sql_taxas($sWhere);;
					$clIframeAlterarExcluir->campos        = "ar36_sequencial,ar36_descricao,ar36_perc,ar53_parcelas,nome, data";
					$clIframeAlterarExcluir->legenda       = "Custas liberadas para parcelamento";
					$clIframeAlterarExcluir->iframe_height = "160";
					$clIframeAlterarExcluir->iframe_width  = "500";
					$clIframeAlterarExcluir->opcoes 	   = 2;
					$clIframeAlterarExcluir->iframe_alterar_excluir(1);

				?>
					<input type="hidden" name="taxas" id="taxas">
				<?php
				}
				?>
			</div>
		</form>
	</div>
	<?php db_menu(); ?>
</body>
<script>
	function js_pesquisaprocessoforo(mostra) {
		iInicial.value = '';

		if (mostra == true) {
			var sUrl = 'func_processoforo.php?lAnuladas=false&funcao_js=parent.js_mostraProcessoForoJanela|v70_sequencial|v70_codforo'
			js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', true)

		} else {
			if (document.form1.v70_sequencial.value != '') {
				var sUrl = 'func_processoforo.php?pesquisa_chave=' + document.form1.v70_sequencial.value + '&funcao_js=parent.js_mostraprocessoforo' + '&lAnuladas=false';
				js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', false);
			}
		}
	}

	function js_mostraprocessoforo(chave, erro, chave2) {
		document.form1.v70_codforo.value = chave;
		$('v70_codforo').value = chave2;

		db_iframe_processoforo.hide();

		if (erro == true) {
			$('v70_sequencial').value = '';
			document.form1.v70_codforo.focus();
			document.form1.v70_codforo.value = '';
			$('v70_codforo').value = chave;
			return false;
		}

		$('iInicial').disable();
		$('buscar').click();
	}

	function js_mostraProcessoForoJanela(chave1, chave2) {

		document.form1.v70_sequencial.value = chave1;
		document.form1.v70_codforo.value = chave2;
		db_iframe_processoforo.hide();
		$('buscar').click();
	}

	function js_pesquisaInicial(lMostra) {
		v70_sequencial.value = '';
		v70_codforo.value = '';

		if (lMostra == true) {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?funcao_js=parent.mostraInicialJanela|0", "Pesquisa", true);
		} else {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?pesquisa_chave=" + document.form1.iInicial.value + "&funcao_js=parent.mostraInicial", "Pesquisa", false);
		}
	}

	function mostraInicialJanela(iInicial) {
		document.form1.iInicial.value = iInicial;
		db_iframe_inicial.hide();
		mostraInicial(iInicial, false);
		$('buscar').click();
	}

	function mostraInicial(iInicial, lErro) {
		lAlteracao = false;

		if (lErro === true) {
			document.form1.iInicial.value = "";
			document.form1.iInicial.focus();
			alert("Código de Inicial do Foro inválido!");
		}

		$('v70_sequencial').disable()
		$('buscar').click();
	}

	$("v70_sequencial").addClassName("field-size2")
	$("v70_codforo").addClassName("field-size7")

	<?php echo (isset($sPosScripts) ? $sPosScripts : ""); ?>
</script>

</html>