<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["api", "clientCredential"]], function () {
    $path = "\App\Domain\Tributario\Diversos\Controllers\\";
    $controller = $path . "ProcedenciaController";

    Route::get("rotulos-procedencia", $controller . "@getRotulos");
    Route::post("pesquisa-procedencias", $controller . "@getByParams");
});