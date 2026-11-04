<?php

use ECidade\Financeiro\Contabilidade\Relatorio\DCASP\RelatorioBalancoPatrimonialFactory;

require_once(modification('fpdf151/assinatura.php'));
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
include_once modification('libs/db_sessoes.php');
include_once modification('libs/db_usuariosonline.php');
require_once(modification('libs/db_sql.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('libs/db_app.utils.php'));
require_once(modification('libs/db_libtxt.php'));
require_once(modification('dbforms/db_funcoes.php'));
require_once(modification('libs/db_libpostgres.php'));
require_once(modification('libs/db_libcontabilidade.php'));
require_once(modification('libs/db_liborcamento.php'));
require_once(modification('fpdf151/PDFDocument.php'));

try {
    $get = (object)filter_input_array(INPUT_GET);

    $codigoPeriodo = $get->periodo;
    $ano = db_getsession('DB_anousu');

    if (empty($codigoPeriodo)) {
        throw new Exception('Por favor informe um período.');
    }

    if (empty($get->db_selinstit)) {
        throw new Exception('Por favor informe uma instituição.');
    }

    $relatorio = RelatorioBalancoPatrimonialFactory::getInstance($ano, $codigoPeriodo);

    $quadros = array();

    if ($get->lQuadroPrincipal === 'true') {
        $quadros[] = $relatorio->getQuadroPrincipal();
    }

    if ($get->lQuadroAtivoPassado === 'true') {
        $quadros[] = $relatorio->getQuadroAtivosPassivos();
    }

    if ($get->lQuadroCompensacao === 'true') {
        $quadros[] = $relatorio->getQuadroContasCompensacao();
    }

    if ($get->lQuadroSuperavitDeficit === 'true') {
        $quadros[] = $relatorio->getQuadroSuperavit();
    }

    if (empty($quadros)) {
        throw new Exception('Por favor informe um relatório.');
    }

    $exibirExercicioAnterior = isset($get->imprimirValorExercicioAnterior) && $get->imprimirValorExercicioAnterior === 'true';

    $relatorio->setExibirExercicioAnterior($exibirExercicioAnterior);
    $relatorio->setInstituicoes(str_replace('-', ',', $get->db_selinstit));
    $relatorio->setExibirQuadros($quadros);
    $relatorio->fonteRecursoApresentar($get->apresentarRecurso);
    $relatorio->emitir();
} catch (Exception $e) {
    db_redireciona("db_erros.php?db_erro={$e->getMessage()}");
}
