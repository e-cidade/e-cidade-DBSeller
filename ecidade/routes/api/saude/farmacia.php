<?php
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('consulta')->group(function () {
        Route::get('demanda-reprimida/by-paciente/{cgs}', 'DemandaReprimidaController@getByPaciente');
        Route::post('medicamento/estoque', 'MedicamentoController@getEstoque');
        Route::post('bnafar/inconsistencias', 'InconsistenciasBnafarController@get');
        Route::get('bnafar/tipos-movimentacoes/{movimentacao}', 'TiposMovimentacoesBnafarController@get');
        Route::post('bnafar/protocolo', 'ProtocoloBnafarController@consultar');
    });

    Route::prefix('cadastro')->group(function () {
        Route::prefix('demanda-reprimida')->group(function () {
            Route::post('delete', 'DemandaReprimidaController@apagar');
            Route::post('save', 'DemandaReprimidaController@salvar');
        });
    });

    Route::prefix('procedimento')->group(function () {
        Route::prefix('bnafar')->group(function () {
            Route::post('validar', 'ExportacaoBnafarController@validar');
            Route::post('consistir', 'ExportacaoBnafarController@consistir');
            Route::post('exportar', 'ExportacaoBnafarController@exportar');
            Route::post('salvar-movimentacao', 'InconsistenciasBnafarController@salvarMovimentacao');
            Route::post('protocolo/reprocessar', 'ProtocoloBnafarController@reprocessar');
        });

        Route::post('atualizar/paciente/cgs-cpf', 'CgsUnidadeController@atualizarCgsCpf');
    });

    Route::prefix('relatorio')->group(function () {
        Route::post('demanda-reprimida', 'DemandaReprimidaController@relatorio');
        Route::post('bnafar/protocolo', 'ProtocoloBnafarController@relatorio');
    });
});
