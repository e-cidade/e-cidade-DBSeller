<?php
// php5.6 artisan route:list --path=v4/api/tributario/fiscalizacao
use Illuminate\Support\Facades\Route;

Route::prefix('vistoria')->namespace('Vistoria')->group(function () {
    Route::get('demostraivo-calculo', 'CalculoVistoriaController@getCalculos');
    Route::get('exercicios-calculados', 'CalculoVistoriaController@getExerciciosCalculados');
    Route::get('tipos-vistorias-calculados', 'CalculoVistoriaController@getTiposVistoriasCalculados');
});
