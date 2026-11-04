<?php
// php5.6 artisan route:list --path=web/educacao/matricula-online
Route::prefix('cadastros')->group(function () {
    Route::get('fases', function () {
        return view("educacao.matricula-online.cadastros.fases");
    });

    Route::get('ciclos', function () {
        return view("educacao.matricula-online.cadastros.ciclos");
    });

    Route::get('inscricao', function () {
        return view("educacao.matricula-online.cadastros.inscricao");
    });
});

Route::prefix('relatorios')->group(function () {
   Route::get('previsao-vagas-parciais', function () {
       return view('educacao.matricula-online.relatorios.previsao-vagas-parciais');
   });
   Route::get('geral-inscricoes', function () {
       return view('educacao.matricula-online.relatorios.geral-inscricoes');
   });
    Route::get('demanda-reprimida', function () {
        return view('educacao.matricula-online.relatorios.demanda-reprimida');
    });
});

Route::prefix('configuracao')->group(function () {
    Route::get('duvidas-frequentes', function () {
        return view("educacao.matricula-online.configuracao.duvidas-frequentes");
    });
    Route::get('mensagens-personalizadas', function () {
        return view("educacao.matricula-online.configuracao.mensagens-personalizadas");
    });
    Route::get('documentos', function () {
        return view("educacao.matricula-online.configuracao.documentos");
    });
    Route::get('imagens-personalizadas', function () {
        return view("educacao.matricula-online.configuracao.imagens-personalizadas");
    });
    Route::get('noticias', function () {
        return view("educacao.matricula-online.configuracao.noticias");
    });
    Route::get('cores-personalizadas', function () {
        return view("educacao.matricula-online.configuracao.cores-personalizadas");
    });
    Route::get('campos-opcionais', function () {
        return view("educacao.matricula-online.configuracao.campos-opcionais");
    });
    Route::get('parametros', function () {
        return view("educacao.matricula-online.configuracao.parametros");
    });
    Route::get('parametros-envio-notificacoes', function () {
        return view("educacao.matricula-online.configuracao.parametros-envio-notificacoes");
    });
});
