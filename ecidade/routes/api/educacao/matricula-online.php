<?php
// php5.6 artisan route:list --path=v4/api/educacao/matricula-online
Route::prefix('ciclos')->group(function () {
    Route::get('/', "CiclosController@todos");
    Route::post('/salvar', "CiclosController@salvar");
    Route::delete('/{codigo}/excluir', "CiclosController@excluir");
});

Route::get('/etapas-por-fase/{codFase}', "EtapasController@getEtapasPorFase");
Route::get('/escolas-por-fase-etapa/{codFase}/{codEtapa}', "EtapasController@getEscolas");

Route::prefix('fases')->group(function () {
    Route::get('/', "FasesController@index");
    Route::post('/salvar', "FasesController@salvar");
    Route::delete('/{codigo}/excluir', "FasesController@excluir");
});

Route::prefix('vagas')->group(function () {
    Route::get('/fases-abertas', "VagasController@getFasesAbertas");
    Route::post('/etapas-fases-abertas', "VagasController@getEtapasConfiguradasPorEscola");
    Route::post('/escolas-fases-abertas', "VagasController@getEscolasFaseAberta");
    Route::post('/turnos-fases-abertas', "VagasController@getTurnosFaseAberta");
    Route::post('/relatorio-vagas-parciais', "RelatoriosController@emitirRelatorioVagasParciais");
});

Route::prefix('relatorios')->group(function () {
    Route::post('/geral-inscricoes', "RelatoriosController@geralInscricoes");
    Route::post('/demanda-reprimida', "RelatoriosController@demandaReprimida");
});

Route::prefix('configuracoes')->group(function () {
    Route::prefix('duvidas-frequentes')->group(function () {
        Route::get('/', "DuvidasFrequentesController@index");
        Route::post('/salvar', "DuvidasFrequentesController@salvar");
        Route::delete('/{codigo}/excluir', "DuvidasFrequentesController@excluir");
    });
    Route::prefix('mensagens-personalizadas')->group(function () {
        Route::get('/', "MensagensPersonalizadasController@index");
        Route::get('/tipos/{codigo}/mensagem', "MensagensPersonalizadasController@getMensagemByTipo");
        Route::post('/salvar', "MensagensPersonalizadasController@salvar");
    });
    Route::prefix('documentos')->group(function () {
        Route::get('/', "DocumentosController@index");
        Route::post('/salvar', "DocumentosController@salvar");
    });

    Route::prefix('noticias')->group(function () {
        Route::get('/', "NoticiasController@index");
        Route::post('/salvar', "NoticiasController@salvar");
        Route::delete('/{codigo}/excluir', "NoticiasController@excluir");
    });
    Route::prefix('imagens-personalizadas')->group(function () {
        Route::get('/', "ImagensPersonalizadasController@index");
        Route::get('/{id}/download', "ImagensPersonalizadasController@download");
        Route::get('/tipos', "ImagensPersonalizadasController@getTipos");
        Route::post('/salvar', "ImagensPersonalizadasController@salvar");
        Route::delete('/{codigo}', "ImagensPersonalizadasController@excluir");
    });
    Route::prefix('cores-personalizadas')->group(function () {
        Route::get('/', "CoresPersonalizadasController@index");
        Route::post('/salvar', "CoresPersonalizadasController@salvar");
    });

    Route::prefix('campos-opcionais')->group(function () {
        Route::get('/', "CamposOpcionaisController@index");
        Route::post('/salvar', "CamposOpcionaisController@salvar");
    });

    Route::prefix('parametros')->group(function () {
        Route::get('/', "ParametrosController@index");
        Route::post('/salvar', "ParametrosController@salvar");
    });

    Route::prefix('parametros-notificacoes')->group(function () {
        Route::get('/getParametros', "ParametrosEnvioNotificacoesController@getParametros");
        Route::post('/salvar', "ParametrosEnvioNotificacoesController@salvar");
    });

    Route::prefix('zona-residencias')->group(function () {
        Route::get('/', "ZonaResidenciasController@index");
        Route::post('/salvar', "ZonaResidenciasController@salvar");
    });
});
