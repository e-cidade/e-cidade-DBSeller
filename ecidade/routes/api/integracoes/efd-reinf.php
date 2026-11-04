<?php
// php5.6 artisan route:list --path=v4/api/integracoes/efd-reinf/
Route::prefix('configuracao')
    ->middleware('legacySession')
    ->group(function () {
        Route::post('/get', 'ConfiguracaoController@getConfig');
        Route::post('/save', 'ConfiguracaoController@saveConfig');
});

Route::prefix('unidaderesponsavel')
    ->group(function () {
        Route::post('/get', 'UnidadeResponsavelController@get');
        Route::post('/save', 'UnidadeResponsavelController@save');
        Route::post('/delete', 'UnidadeResponsavelController@delete');
});

Route::prefix('retencao')
    ->group(function () {
        Route::get('/get-retencoes', 'ManutencaoRetencaoController@getRetencoes');
        Route::post('/save-retencao', 'ManutencaoRetencaoController@saveRetencao');
        Route::get('/tipo-servico-nota', 'ManutencaoRetencaoController@getTipoServicoNota');
        Route::get('/composicao-base-calculo', 'ComposicaoBaseCalculoController@index');
});

Route::prefix('r4099responsavel')
    ->group(function () {
        Route::get('/get', 'ReabFechDadosRespController@get');
        Route::get('/get-contribuinte', 'ReabFechDadosRespController@getContribuinte');
        Route::post('/save', 'ReabFechDadosRespController@save');
});


Route::prefix('events')->group(function () {
    Route::get('checkPeriodIsClosed', 'EventsController@checkPeriodIsClosed');
});
