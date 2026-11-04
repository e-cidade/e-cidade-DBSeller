<?php

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Usuario\Models\Usuario;

Route::prefix('relatorios')->group(function () {
    Route::get('gerador', function () {
        return view('configuracao.relatorios.gerador', [
            'usuario' => utf8_encode(Usuario::find(session('DB_id_usuario'))->nome),
            'departamento' => Departamento::find(session('DB_coddepto'))->descrdepto
        ]);
    });
});

Route::get('gerador/relatorios/{relatorio}', function ($relatorio) {
    return view('configuracao.relatorios.tela-dinamica', [ 'relatorio' => $relatorio ]);
});

Route::get("jobs", function (){
    return view("configuracao.jobs.index");
});

Route::get('favoritos', function (){
    return view("configuracao.favoritos.index");
});
