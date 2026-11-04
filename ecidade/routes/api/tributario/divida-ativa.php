<?php

use Illuminate\Support\Facades\Route;

Route::prefix('lancamento-historico')
    ->namespace('DividaAtiva\Controllers\LancamentoHistorico')
    ->group(function() {
        Route::get('dividas', 'LancamentoHistoricoController@getDividas');
        Route::get('dividas-exercicios', 'LancamentoHistoricoController@getDividasExercicios');
        Route::get('selecionar-todas-dividas', 'LancamentoHistoricoController@getAllDividasToSelect');
        Route::post('dividas', 'LancamentoHistoricoController@saveHistorico');
    });