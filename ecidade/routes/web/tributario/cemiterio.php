<?php

use Illuminate\Support\Facades\Route;

// web/tributario/procedimentos/parametros
Route::get('procedimentos/parametros', function () {
    return view("tributario.cemiterio.procedimentos.parametros");
});
