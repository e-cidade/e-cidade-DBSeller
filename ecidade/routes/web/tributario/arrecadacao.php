<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

Route::get('data-de-lancamento', function (Request $request) {
    return view("tributario.data_de_lancamento");
});

Route::prefix('procedimentos')
    ->group(function () {
        Route::get('cancelparcelista', function (Request $request) {
            return view('tributario.arrecadacao.cancelparcelista');
        });
    });

Route::prefix('cadastros')
->group(function () {
    Route::get('grupo-de-taxas', function (Request $request) {
        return view("tributario.arrecadacao.cadastros.grupo-de-taxas");
    });
});

Route::prefix('parametros')
->group(function () {
    Route::get('config-ordenacao-cgf', function (Request $request) {
        return view("tributario.arrecadacao.parametros.config-ordenacao-cgf");
    });
});