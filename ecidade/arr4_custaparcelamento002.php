<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Tributario\Arrecadacao\Repository\HonorarioParcelamentoRepository;

db_postmemory($_POST);

$oDaoTaxa                        = new cl_taxa;
$oDaoCustasParcelamento          = new cl_custasparcelamento;
$honorarioParcelamentoRepository = new HonorarioParcelamentoRepository;
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

	if (isset($db_opcao) && $db_opcao == 'alterar') {
		$oDaoCustasParcelamento->ar53_sequencial = $ar53_sequencial;
		$oDaoCustasParcelamento->ar53_parcelas = $ar53_parcelas;
		$oDaoCustasParcelamento->alterar($ar36_sequencial);
	}

	db_fim_transacao();
} catch (Exception $error) {
	$sPosScripts .= 'alert(`' . $error->getMessage() . '`);' . "\n";
	db_fim_transacao(true);
}

require_once('forms/db_frmcustaparcelamentomanutencao.php');
