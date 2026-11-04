<?php
$pathControllers = '\App\Domain\Tributario\Arrecadacao\Controller\\';

Route::prefix('dbpref-pix')->middleware(["clientCredential", "legacySession"])
->group(function () use($pathControllers) {
    Route::post('gerar', $pathControllers . "RecibobarpixController@gerarPixDBPref");
    Route::post('validar', $pathControllers . "RecibobarpixController@validaEmissao");
});
