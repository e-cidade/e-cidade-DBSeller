<?php
Route::middleware(["auth:api"])->group(function () {
    Route::prefix('relatorio')->group(function () {
        Route::post('viagens-por-motorista', 'AgendaSaidaController@relatorioViagensPorMotorista');
    });

    Route::prefix('procedimento')->group(function () {
        Route::post('viagem/cancelamento', 'CancelamentoViagemController@cancelaVeiculo');
    });

    Route::prefix('consulta')->group(function () {
        Route::get('viagem/cancelamento/{cancelamento}', 'CancelamentoViagemController@getCancelamento');
        Route::get('viagem/{viagem}/passageiros-retorno', 'IndiqueVeiculoController@getPassageirosRetorno');
        Route::get('viagem/{viagem}/passageiros-cancelados', 'IndiqueVeiculoController@getPassageirosCancelados');
        Route::get('viagem/{viagem}/lotacao-cancelados', 'IndiqueVeiculoController@getLotacaoCancelados');
    });
});
