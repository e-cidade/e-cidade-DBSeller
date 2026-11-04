<?php

use Illuminate\Support\Facades\Route;
Route::get('informacoes/complementares/eventos-periodicos', function(){
    return view('recursos-humanos.esocial.eventosperiodicos.informacoes-complementares-eventos-periodicos');
});

Route::get('exame-toxicologico-motorista-profissional', function () {
   return view('recursos-humanos.esocial.segurancasaudetrabalho.exame-toxicologico-motorista-profissional');
});
