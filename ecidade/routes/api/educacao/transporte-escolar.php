<?php

// php5.6 artisan route:list --path=v4/api/educacao/transporte-escolar

use Illuminate\Support\Facades\Route;

$path = "\App\Domain\Educacao\TransporteEscolar\Controllers\\";

Route::post('/busca-linhas/{escola}', "{$path}TransporteEscolarController@getLinhas");
Route::post('/get-alunos-vinculados/', "{$path}TransporteEscolarController@getAlunosVinculados");
Route::post('/emitir-carteira ', "{$path}TransporteEscolarController@emitirCarteira");


