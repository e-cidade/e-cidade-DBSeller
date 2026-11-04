<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;

// php5.6 artisan route:list --path=web/educacao/escola

Route::prefix('relatorios')->group(function (){
   Route::get('/emissao-carteira', function(Request $request) {
       return view('educacao.transporte-escolar.emissao-carteira-estudante');
   });
});
