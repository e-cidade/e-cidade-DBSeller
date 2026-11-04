<?php
Route::prefix('consulta')->group(function () {
    Route::post('acordos', 'AcordosController@buscarAcordos');
    Route::post('buscar-todos-acordos', 'AcordosController@buscarTodosAcordos');
    Route::get('buscar-acordo-posicoes/{acordo}', 'AcordosController@buscarPosicoes');
    Route::get('buscar-itens-posicao/{acordoposicao}', 'AcordosController@getItensPosicao');
    Route::get('buscar-descricao-origens', 'AcordosController@getDescricaoOrigens');
});

Route::post('realizar-alteracoes','AcordosController@realizarAlteracoes');
Route::post('enviar-evento-automatico','AcordosController@enviarEventoAutomatico');


Route::prefix('acordo')->group(function () {
    Route::get('buscar-configuracoes', 'ConfiguracaoContratoMensagemController@show');
    Route::post('salvar-dias/{id}', 'ConfiguracaoContratoMensagemController@addDia');
    Route::delete('deletar-dia/{id}', 'ConfiguracaoContratoMensagemController@removeDia');
    Route::put('salvar-config/{id}', 'ConfiguracaoContratoMensagemController@update');
});


