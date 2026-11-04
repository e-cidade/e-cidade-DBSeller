<?php
// php5.6 artisan route:list --path=v4/api/financeiro/empenho/

Route::prefix('relatorio')->group(function () {
    Route::post('retencoesEfdReinf', "RelatorioRetencoesEfdReinfController@emitirRelatorio");
    Route::post('relatorio-subcontratacao', "RelatorioSubcontratacaoController@emitirRelatorio");
});

Route::prefix('conferencia-extra-orcamentaria')->group(function () {
    Route::post('exportar', "ConferenciaExtraOrcamentariaController@exportar");
});

Route::prefix('retencaosubcontratacao')->group(function () {
    Route::post('update', 'RetencaoReceitasSubcontratacaoController@update');
    Route::post('delete', 'RetencaoReceitasSubcontratacaoController@delete');
});

Route::prefix('naturezarendimentos')->group(function () {
    Route::get('grupo', 'NaturezaRendimentoController@getNaturezasPorGrupo');
    Route::get('grupos', 'NaturezaRendimentoController@getGrupos');
    Route::get('naturezas', 'NaturezaRendimentoController@getNaturezas');
});

Route::get('empenhos/dialog-pesquisa', 'EmpenhosController@dialogPesquisa');

Route::prefix('sigfis')->group(function () {
    Route::get('tipodocliquidacao', 'SigfisTipodocLiquidacaoController@getTipos');
});
