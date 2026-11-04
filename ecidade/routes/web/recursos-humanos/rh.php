<?php
use Illuminate\Support\Facades\Route;

Route::get('relatorios/assentamento-por-periodo/configuracao', function () {
    return view('recursos-humanos/rh/relatorios/config-assent-periodo');
});

Route::prefix('relatorios')->group(function () {
    Route::get('certidao_tempo_contribuicao', function () {
        return view('recursos-humanos.rh.relatorios.certidao-tempo-contribuicao');
    });
});
