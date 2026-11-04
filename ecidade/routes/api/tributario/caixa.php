<?php

Route::prefix('vinculoreceitaprocedencia')->group(function () {
    Route::get('/', 'VinculoReceitaProcedenciaController@getDados');
    Route::post('/', 'VinculoReceitaProcedenciaController@salvar');
});
