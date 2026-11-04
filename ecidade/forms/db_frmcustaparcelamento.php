<?php
require_once(modification("dbforms/db_classesgenericas.php"));

$cliframe_custasliberadas = new cl_iframe_seleciona;

$oDaoTaxa->rotulo->label();
$oRotulo = new rotulocampo;
$oRotulo->label("ar53_taxa");
$oRotulo->label("v70_sequencial");
$oRotulo->label("v50_inicial");
$oRotulo->label("v70_codforo");
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
		<form name="form" method="post" action="">
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

			<div class="container">
				<?php
				$chavepri = array("ar53_sequencial" => isset($ar53_sequencial) ? $ar53_sequencial : '');
				$sWhere = "";

				$oDaoParJuridico = new cl_parjuridico();
				$sqlParcelamentoHonorarios = $oDaoParJuridico->sql_query_file(
					db_getsession('DB_anousu'),
					db_getsession("DB_instit"),
					'v19_parcelacustasmanual, v19_parcelahonorariosmanual'
				);
				$rsParcelamentoHonorarios = db_query($sqlParcelamentoHonorarios);
				$parcelamentoHonorarios = db_utils::getCollectionByRecord($rsParcelamentoHonorarios)[0];

				$permiteParcelamentoHonorariosManual = $parcelamentoHonorarios->v19_parcelahonorariosmanual == 't';
				$permiteParcelamentoCustasManual = $parcelamentoHonorarios->v19_parcelacustasmanual == 't';

				if (isset($iInicial) && !empty($iInicial)) {
					$sWhere = "ar53_inicial = $iInicial";
				}

				if (isset($v70_sequencial) && !empty($v70_sequencial)) {
					$sWhere = "ar53_processoforo = $v70_sequencial";
				}

				if (!empty($sWhere) && ($permiteParcelamentoHonorariosManual || $permiteParcelamentoCustasManual)) {
					$sWhere .= " and ar53_parcelamento is null";
					$sqlMarcados = $oDaoCustasParcelamento->sql_query(null, "*, ar36_sequencial as taxa", "ar36_sequencial", $sWhere);

					$whereTaxa = "ar36_permiteparcelamento = 't' and ar36_debitoscomprocesso = 't'";

					if ($permiteParcelamentoHonorariosManual && !$permiteParcelamentoCustasManual) {
						$whereTaxa .= " and ar36_honorario is true";
					}

					if (!$permiteParcelamentoHonorariosManual && $permiteParcelamentoCustasManual) {
						$whereTaxa .= " and ar36_honorario is false";
					}

					$sqlPesquisaTaxa = $oDaoTaxa->sql_query_file(null, "*, ar36_sequencial as taxa", null, $whereTaxa);

					$cliframe_custasliberadas->sql           = $sqlPesquisaTaxa;
					$cliframe_custasliberadas->sql_marca     = $sqlMarcados;
					$cliframe_custasliberadas->campos        = "ar36_sequencial,ar36_descricao,ar36_perc,ar36_valormin,ar36_valormax";
					$cliframe_custasliberadas->legenda       = "Custas liberadas para parcelamento";
					$cliframe_custasliberadas->iframe_height = "200";
					$cliframe_custasliberadas->iframe_width  = "400";
					$cliframe_custasliberadas->iframe_nome   = "listataxas";
					$cliframe_custasliberadas->chaves        = 'taxa';
					$cliframe_custasliberadas->iframe_seleciona(1);
				?>
					<input type="hidden" name="taxas" id="taxas">
					<input name="incluir" type="submit" onclick="js_selecao();" id="db_opcao" value="atualizar" <?php echo ((isset($v70_sequencial) || isset($iInicial)) ? "" : "disabled"); ?>>
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
			if (document.form.v70_sequencial.value != '') {
				var sUrl = 'func_processoforo.php?pesquisa_chave=' + document.form.v70_sequencial.value + '&funcao_js=parent.js_mostraprocessoforo' + '&lAnuladas=false';
				js_OpenJanelaIframe('', 'db_iframe_processoforo', sUrl, 'Pesquisa', false);
			}
		}
	}

	function js_mostraprocessoforo(chave, erro, chave2) {
		document.form.v70_codforo.value = chave;
		$('v70_codforo').value = chave2;

		db_iframe_processoforo.hide();

		if (erro == true) {
			$('v70_sequencial').value = '';
			document.form.v70_codforo.focus();
			document.form.v70_codforo.value = '';
			$('v70_codforo').value = chave;
			return false;
		}

		$('iInicial').disable();
		$('buscar').click();
	}

	function js_mostraProcessoForoJanela(chave1, chave2) {

		document.form.v70_sequencial.value = chave1;
		document.form.v70_codforo.value = chave2;
		db_iframe_processoforo.hide();
		$('buscar').click();
	}

	function js_pesquisaInicial(lMostra) {
		v70_sequencial.value = '';
		v70_codforo.value = '';

		if (lMostra == true) {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?funcao_js=parent.mostraInicialJanela|0", "Pesquisa", true);
		} else {
			js_OpenJanelaIframe("top.corpo", "db_iframe_inicial", "func_inicial.php?pesquisa_chave=" + document.form.iInicial.value + "&funcao_js=parent.mostraInicial", "Pesquisa", false);
		}
	}

	function mostraInicialJanela(iInicial) {
		document.form.iInicial.value = iInicial;
		db_iframe_inicial.hide();
		mostraInicial(iInicial, false);
		$('buscar').click();
	}

	function mostraInicial(iInicial, lErro) {
		lAlteracao = false;

		if (lErro === true) {
			document.form.iInicial.value = "";
			document.form.iInicial.focus();
			alert("Código de Inicial do Foro inválido!");
		}

		$('v70_sequencial').disable()
		$('buscar').click();
	}

	function js_selecao() {
		const obj = listataxas.document.form1;
		let taxasSelecionadas = [];

		for (i = 0; i < obj.length; i++) {
			if (obj[i].type == "checkbox") {
				if (obj[i].checked == true) {
					taxasSelecionadas.push(obj[i].value);
				}
			}
		}

		taxas.value = taxasSelecionadas;
		document.form.incluir = 'true';

		document.form.submit();
	}

	$("v70_sequencial").addClassName("field-size2")
	$("v70_codforo").addClassName("field-size7")

	<?php echo (isset($sPosScripts) ? $sPosScripts : ""); ?>
</script>

</html>