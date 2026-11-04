<?php
Route::prefix("licitacon")->group(function () {
    Route::get("parametros", function () {
        return view("patrimonial.licitacao.licitacon.parametros");
    });

    Route::prefix('obras')->group(function () {
        Route::get("incluir", function () {
            return view("patrimonial.licitacao.licitacon.obras.incluir");
        });
    });
});
