<?php
use Illuminate\Support\Facades\Route;


Route::prefix('lista')
    ->namespace('Notificacoes\Controllers\\')
    ->middleware(["api", "auth:api"])
    ->group(function ()  {
        Route::get("rotulos", "ListaNotificaoController@getRotulosPesquisaLista");
        Route::post("verificalista", "ListaNotificaoController@verificaLista");
        Route::post("getlista", "ListaNotificaoController@getLista");
        Route::post('verificapacelamento', "ListaNotificaoController@verificaPacelamento");
        Route::post('verificatodosparcelamentos', "ListaNotificaoController@verificaAllParcelamentos");
        Route::post('verificatipocda', "ListaNotificaoController@verificatipoCDA");
        Route::post('verificatodoscda', "ListaNotificaoController@verificaAllCDA");
    }
);