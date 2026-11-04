<?php
// php5.6 artisan route:list --path=v4/api/patrimonial/licitacoes
Route::get('registrosDePreco', "ItensBloqueadosController@buscarRegistrosDePreco");
Route::post('itensBloqueados', "ItensBloqueadosController@emitir");
Route::post('tramita/importar', "TramitaController@importar");
Route::post('integracaocomprasbr/importar', "IntegracaoComprasBrController@import");
Route::post('integracaocomprasbr/exportar', "IntegracaoComprasBrController@export");
Route::post('integracaocomprasbr/reprocessar', "IntegracaoComprasBrController@reprocessar");

Route::prefix('bnc')->group(function () {
    Route::post('importar/{licitacao}', 'BncController@importar');
    Route::post('exportar/{licitacao}', 'BncController@exportar');
    Route::post('buscar/{licitacao}', 'BncController@buscar');
    Route::post('excluir/{licitacao}', 'BncController@excluir');
});

Route::prefix('licitacon')->group(function () {
    Route::prefix('obras')->group(function () {
        Route::get('orgao-fiscalizado/{instituicao}', 'LicitaconObrasController@verificarOrgaoFiscalizado');
        Route::get('incluir/{acordo}', 'LicitaconObrasController@incluir');
        Route::get('familias', 'LicitaconObrasController@buscarFamilias');
        Route::get('sub-familias', 'LicitaconObrasController@buscarSubFamilias');
        Route::get('detalhamento-caracteristicas', 'LicitaconObrasController@buscarDetalhamentoCaracteristicas');
        Route::get('buscar-usuario/{instituicao}', 'LicitaconObrasController@buscarUsuario');
        Route::get('salvar-usuario/{instituicao}', 'LicitaconObrasController@salvarUsuario');
    });
});
