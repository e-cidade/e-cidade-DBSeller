<?php
// php5.6 artisan route:list --path=web/saude/ambulatorial
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;

Route::get('consultas/status-paciente', function (Request $request) {
    return view("saude.ambulatorial.consultas.status-paciente");
});

Route::get('cadastros/unidade-encaminhadora', function (Request $request) {
    return view("saude.ambulatorial.cadastros.unidade-encaminhadora");
});

Route::get('cadastros/unidade-origem', function (Request $request) {
    return view("saude.ambulatorial.cadastros.unidade-origem");
});

Route::get('procedimentos/unificacao-cgs', function () {
    return view('saude.ambulatorial.procedimentos.unificacao-cgs');
});

Route::get('consultas/consulta-cgs-unificado', function () {
    return view('saude.ambulatorial.consultas.consulta-cgs-unificado');
});
