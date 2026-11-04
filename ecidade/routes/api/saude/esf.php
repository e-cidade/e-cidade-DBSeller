<?php
Route::middleware(["auth:api"])->group(function () {
    Route::prefix('equipes')->group(function () {
        Route::post('unidades-com-equipe', 'EquipesController@getUnidadesComEquipe');
    });

    Route::prefix('controle-vacinas')->group(function () {
        Route::get('imunobiologicos', 'ImunobiologicoController@getAll');
    });

    Route::prefix('vacinas')->group(function () {
        Route::get('by-paciente/{cgs}', 'VacinasController@getByPaciente');
    });

    Route::prefix('consulta')->group(function () {
        Route::get('equipes/by-unidade/{id}', 'EquipesController@getEquipesUnidade');
    });

    Route::prefix('relatorio')->group(function () {
        Route::post('controle-vacinas', 'VacinasController@relatorioControleVacinas');
        Route::post('indicador-desempenho', 'IndicadoresDesempenhoController@relatorio');
        Route::post('condicoes-saude', 'CondicoesSaudeController@relatorio');
    });
});
