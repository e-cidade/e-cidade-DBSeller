<?php

use Illuminate\Support\Facades\Route;

$path = "\App\Domain\RecursosHumanos\ESocial\Controller\InformacoesComplementares\\";
$informacoesComplementares = "{$path}InformacoesComplementaresController";
Route::get('index', "{$informacoesComplementares}@index");
Route::get('index/{periodo}/{empregador}',"{$informacoesComplementares}@indexPeriodo");
Route::post('store', "{$informacoesComplementares}@store");

Route::prefix('exame-toxicologico')->group(function () {
    $pathExame = "\App\Domain\RecursosHumanos\ESocial\Controller\\";
    $exames = "{$pathExame}ExameToxicologicoController";
    Route::post('store',"{$exames}@store");
    Route::get('index', "{$exames}@index");
});
