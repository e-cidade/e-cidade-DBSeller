<?php

// php artisan route:list --path=web/tributario/divida-ativa
use Illuminate\Support\Facades\Route;

Route::get('lancamento-historico/', function () {
    return view(
        'tributario.divida-ativa.procedimentos.lancamento-historico'
    );
});
