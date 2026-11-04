<?php

Route::prefix('relatorios')->group(function () {
    Route::prefix('recursos')->group(function () {
        Route::get('confere-vinculo', function () {
            return view(
                'financeiro.orcamento.recursos.relatorios.confere-vinculo',
                [
                    "exercicio" => session('DB_anousu'),
                    "dataSistema" => date('Y-m-d', session('DB_datausu'))
                ]);
        });
    });
});
