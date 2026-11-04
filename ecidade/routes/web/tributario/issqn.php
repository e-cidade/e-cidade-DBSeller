<?php

use Illuminate\Support\Facades\Route;

Route::get('procedimentos/simples-nacional/novos-estabelecimentos', function () {
    return view("tributario.issqn.procedimentos.simples-nacional.novos-estabelecimentos");
});

Route::get('procedimentos/empresas-optantes-do-simples/atualizacao-de-cadastros', function () {
    return view("tributario.issqn.procedimentos.empresas-optantes-do-simples.atualizacao-de-cadastros");
});

Route::get('cadastros/salao-parceiro/inclusao-salao-parceiro', function () {
    return view("tributario.issqn.cadastros.salao-parceiro.inclusao-salao-parceiro");
});

Route::get('cadastros/salao-parceiro/alterar-salao-parceiro', function () {
    return view("tributario.issqn.cadastros.salao-parceiro.alterar-salao-parceiro");
});

Route::get('cadastros/salao-parceiro/inclusao-deducao-salao', function () {
    return view("tributario.issqn.cadastros.salao-parceiro.inclusao-deducao-salao");
});