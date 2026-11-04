<?php
use Illuminate\Support\Facades\Route;

Route::get('/campos-opcionais', function () {
    return response(file_get_contents(public_path('config-js/campos-opcionais.json')));
});

Route::get('/duvidas', function () {
    return response(file_get_contents(public_path('config-js/duvidas.json')));
});

Route::get('/instituicao', function () {
    return response(file_get_contents(public_path('config-js/instituicao.json')));
});

Route::get('/mensagens', function () {
    return response(file_get_contents(public_path('config-js/mensagens.json')));
});

Route::get('/noticias', function () {
    return response(file_get_contents(public_path('config-js/noticias.json')));
});

Route::get('/parametros', function () {
    return response(file_get_contents(public_path('config-js/parametros.json')));
});

Route::get('/redes', function () {
    return response(file_get_contents(public_path('config-js/redes.json')));
});

Route::get('/zonas', function () {
    return response(file_get_contents(public_path('config-js/zonas.json')));
});

Route::get('/cores', function () {
    return response(file_get_contents(public_path('config-js/cores.json')));
});

Route::get('/rendas', function () {
    return response(file_get_contents(public_path('config-js/rendas.json')));
});

Route::get('/imagens', function () {
    return response(file_get_contents(public_path('config-js/imagens.json')));
});

Route::get('/orgaos', function () {
    return response(file_get_contents(public_path('config-js/orgaos.json')));
});
