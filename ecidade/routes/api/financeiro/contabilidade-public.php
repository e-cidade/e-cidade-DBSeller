<?php

Route::prefix('relatorio-legal')->group(function () {
    Route::get('emissao/download/{codigo}', 'LrfEmissaoController@download');
});
