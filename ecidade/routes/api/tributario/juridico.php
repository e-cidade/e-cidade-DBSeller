<?php

use Illuminate\Support\Facades\Route;

$path = "\App\Domain\Tributario\Juridico\Controllers\\";

Route::prefix('anulacaocda')
->middleware(["api", "auth:api"])
->group(function ()  use ($path)  {
    Route::post("pesquisar", $path."AnulacaoCDAPorListaController@verificaLista");
    Route::post("processar", $path."AnulacaoCDAPorListaController@processar");
    Route::get("getprocessamento", $path."AnulacaoCDAPorListaController@getprocessamento");
});

Route::prefix('inclusaoiniciallista')
->middleware(["api", "auth:api"])
->group(function ()  use ($path)  {
    Route::post("pesquisar", $path."IncluirInicialListaController@verificaLista");
    Route::post("processar", $path."IncluirInicialListaController@processar");
    Route::get("getprocessamento", $path."IncluirInicialListaController@getprocessamento");
    Route::post("gethistorico", $path."IncluirInicialListaController@gethistorico");
});