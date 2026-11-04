<?php

use Illuminate\Support\Facades\Route;

$defaultNamespace = '\App\Domain\Tributario\Arrecadacao\Controller';

Route::prefix('/requisicao_api_pix')->group(
    function () use (&$defaultNamespace) {
        Route::get(
            '/geral',
            ($defaultNamespace .
                '\RequisicaoAPIPIXController' .
                '@geral'
            )
        );
    }
);
