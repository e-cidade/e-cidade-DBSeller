<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;

Route::get('alterar-dotacao', function (Request $request) {
    return view("patrimonial.contratos.alterar-dotacao");
});

Route::get('mensageria-acordos-config', function () {
    return view("patrimonial.contratos.mensageria-acordos-config");
});
