<?php

use Illuminate\Support\Facades\Route;

Route::get('procedimentos/zoneamento/cadastro_macrozonas', function () {
    return view("tributario.cadastro.procedimentos.zoneamento.cadastro_macrozonas");
});

Route::get('procedimentos/zoneamento/cadastro_zonas', function () {
    return view("tributario.cadastro.procedimentos.zoneamento.cadastro_zonas");
});

Route::get('procedimentos/zoneamento/alteracao_zonas', function () {
    return view("tributario.cadastro.procedimentos.zoneamento.alteracao_zonas");
});

Route::get('procedimentos/zoneamento/alteracao_macrozonas', function () {
    return view("tributario.cadastro.procedimentos.zoneamento.alteracao_macrozonas");
});