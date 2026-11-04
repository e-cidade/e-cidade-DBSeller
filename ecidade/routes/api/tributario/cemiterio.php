<?php

Route::middleware(['api', 'auth:api'])->prefix('parametros')->group(function () {
    $path = "\App\Domain\Tributario\Cemiterio\Controllers\Cemiterio\Parametros\\";
    $controller = $path . "ParametrosCemiterioController";

    Route::get('/busca-parametros', $controller . "@buscaParametros");
    Route::patch('/alteracao-parametros', $controller . "@alteraParametros");
});