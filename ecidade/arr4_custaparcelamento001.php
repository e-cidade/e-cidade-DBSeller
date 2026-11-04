<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Arrecadacao\Repository\HonorarioParcelamentoRepository;

db_postmemory($_POST);

$oDaoCustasParcelamento = new cl_custasparcelamento;
$oDaoTaxa               = new cl_taxa;

$honorarioParcelamentoRepository = new HonorarioParcelamentoRepository;

$excluir     = (isset($excluir) && !empty($excluir)) ? $excluir : (isset($opcao) && $opcao == 'excluir');
$db_opcao    = $excluir ? 3 : 1;
$db_botao    = true;
$sPosScripts = "";

try {
	if (isset($opcao)) {
		$oDaoCustasParcelamento->ar53_sequencial = $ar53_sequencial;
		$sSqlCustasParcelamento = $oDaoCustasParcelamento->sql_query($ar53_sequencial);
		$rsCustasParcelamento = db_query($sSqlCustasParcelamento);

		if (!$rsCustasParcelamento) {
			throw new Exception("Erro ao buscar custas com os parâmetros informados.");
		}

		$custasParclamento = db_utils::getCollectionByRecord($rsCustasParcelamento)[0];

		$iInicial = $custasParclamento->v50_inicial;
		$v70_sequencial = $custasParclamento->v70_sequencial;
	}

	db_inicio_transacao();

	if (isset($incluir)) {

		if (isset($taxas)) {

			$sWhere = "";

			if (isset($iInicial) && !empty($iInicial)) {
				$inicial = new Inicial;
				$inicial->setCodigo($iInicial);

				$validaInicial = $honorarioParcelamentoRepository->getValidaInicial($inicial);

				if (!$validaInicial->liberaConsulta) {
					throw new Exception($validaInicial->mensagem);
				}

				$sWhere = "ar53_inicial = $iInicial and ar53_parcelamento is null";

				if (isset($taxas) && !empty($taxas)) {
					$sWhere .= " and ar53_taxa not in ($taxas)";
				}
			}

			if (isset($v70_sequencial) && !empty($v70_sequencial)) {
				$sWhere = "ar53_processoforo = $v70_sequencial and ar53_parcelamento is null";

				if (isset($taxas) && !empty($taxas)) {
					$sWhere .= " and ar53_taxa not in ($taxas)";
				}
			}

			if (!empty($sWhere)) {
				$oDaoCustasParcelamento->excluir(null, $sWhere);
			}

			$taxasSelecionadas = explode(',', $taxas);

			foreach ($taxasSelecionadas as $ar29_taxa) {

				if (!isset($ar29_taxa) || empty($ar29_taxa)) {
					continue;
				}

				$sWhere = "ar53_taxa = $ar29_taxa and ar53_parcelamento is null";
				if (isset($iInicial) && !empty($iInicial)) {
					$sWhere .= " and ar53_inicial = $iInicial";
				}

				if (isset($v70_sequencial) && !empty($v70_sequencial)) {
					$sWhere .= " and ar53_processoforo = $v70_sequencial";
				}

				$sqlTaxaJaCadastrada = $oDaoCustasParcelamento->sql_query(null, '*', null, $sWhere);
				$rsTaxaJaCadastrada = db_query($sqlTaxaJaCadastrada);
				$taxaJaCadastrada = pg_num_rows($rsTaxaJaCadastrada) > 0;

				if (!$taxaJaCadastrada) {
					$ar53_sequencial = "(select nextval('custasparcelamento_ar53_sequencial_seq') as sequencial)";
					$oDaoCustasParcelamento->ar53_sequencial = $ar53_sequencial;
					$oDaoCustasParcelamento->ar53_parcelamento = null;
					$oDaoCustasParcelamento->ar53_usuario = db_getsession("DB_id_usuario");
					$oDaoCustasParcelamento->ar53_data = date("Y-m-d", db_getsession("DB_datausu"));
					$oDaoCustasParcelamento->ar53_parcelas = 1000;
					$oDaoCustasParcelamento->ar53_taxa = $ar29_taxa;
					$oDaoCustasParcelamento->ar53_inicial = (isset($iInicial) && !empty($iInicial)) ? $iInicial : 'null';
					$oDaoCustasParcelamento->ar53_processoforo = (isset($v70_sequencial) && !empty($v70_sequencial)) ? $v70_sequencial : 'null';
					$oDaoCustasParcelamento->incluir($ar53_sequencial);

					if ($oDaoCustasParcelamento->erro_status == "0") {
						$db_botao = true;
						$sPosScripts .= "document.form.db_opcao.disabled = false;\n";

						if ($oDaoCustasParcelamento->erro_campo != "") {
							$sPosScripts .= "document.form.{$oDaoCustasParcelamento->erro_campo}.classList.add('form-error');";
							$sPosScripts .= "document.form.{$oDaoCustasParcelamento->erro_campo}.focus();";
						}
					}
				}
			}
		}

	} elseif (isset($excluir) && $excluir) {
		$db_opcao = 3;
		$oDaoCustasParcelamento->excluir($ar53_sequencial);
	}

	db_fim_transacao();
} catch (Exception $error) {
	$sPosScripts .= 'alert(`' . $error->getMessage() . '`);' . "\n";
	db_fim_transacao(true);
}

require_once('forms/db_frmcustaparcelamento.php');
