<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('cadastros')->group(function () {
    Route::get('orgao', function (Request $request) {
        return view("patrimonial.pncp.cadastros.orgao");
    });
    Route::get('documento-licitacao', function () {
        return view('patrimonial.pncp.cadastros.documento-licitacao');
    });
});

Route::prefix("procedimentos")->group(function () {
    Route::prefix('contratacao-edital-aviso')->group(function () {
        Route::get("exclusao-documento", function () {
            return view("patrimonial.pncp.procedimentos.contratacao-edital-aviso.exclusao-documento-contratacao");
        });
    });
});

Route::prefix('procedimentos')->group(function () {
   Route::prefix('contrato')->group(function () {
      Route::get('exclusao-contrato', function (Request $request) {
          return view("patrimonial.pncp.procedimentos.contrato.exclusao-contrato");
       });
   });
});
