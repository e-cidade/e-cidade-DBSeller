<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

Route::get('procedimentos/implantacao-saldo-planilha', function (Request $request) {
    return view('patrimonial.material.procedimentos.implantacao-saldo-planilha');
});
