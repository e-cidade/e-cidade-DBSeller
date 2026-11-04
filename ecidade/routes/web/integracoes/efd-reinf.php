<?php

use Illuminate\Support\Facades\Route;

// Retencoes
Route::prefix('retencao')->group(function () {
    Route::get('manutencaoR4010', function () {
        return view('integracao.EFDReinf.Retencao.manutencaoR4010');
    });

    Route::get('manutencaoR4020', function () {
        return view('integracao.EFDReinf.Retencao.manutencaoR4020');
    });

    Route::get('manutencaoR4040', function () {
        return view('integracao.EFDReinf.Retencao.manutencaoR4040');
    });

    Route::get('dadosrespR4099', function () {
        return view('integracao.EFDReinf.Forms.dadosRespR4099');
    });
});

