<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;

Route::get('lista', function (Request $request) {
    return view("tributario.lista");
});