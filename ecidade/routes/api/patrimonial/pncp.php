<?php
Route::prefix('integracao')->group(function () {
    Route::post('habilitar', 'IntegracaoController@habilitar');
    Route::post('verificaIntegracao', 'IntegracaoController@verificaIntegracao');
    Route::post('orgao', 'IntegracaoController@incluirOrgao');
    Route::post('bloquear-unidade-requisitante', 'IntegracaoController@bloquearUnidade');
});

Route::prefix('unidades')->group(function () {
    Route::post('incluir', 'UnidadesController@incluir');
    Route::post('buscarEntidade', 'UnidadesController@buscarEntidade');
    Route::post('buscarUnidades', 'UnidadesController@buscarUnidades');
    Route::post('buscarUnidadesAtivas', 'UnidadesController@buscarUnidadesAtivas');
});

Route::prefix('compraEditalAviso')->group(function () {
    Route::post('buscarAmparosLegais', 'CompraEditalAvisoController@buscarAmparosLegais');
    Route::post('buscarInstrumentoConvocatorio', 'CompraEditalAvisoController@buscarInstrumentoConvocatorio');
    Route::post('buscarModoDisputa', 'CompraEditalAvisoController@buscarModoDisputa');
    Route::post('buscarLicitacao', 'CompraEditalAvisoController@buscarLicitacao');
    Route::post('buscarSolicitacao', 'CompraEditalAvisoController@buscarSolicitacao');
    Route::post('incluirCompraEditalAviso', 'CompraEditalAvisoController@incluirCompraEditalAviso');
    Route::post('alterarCompraEditalAviso', 'CompraEditalAvisoController@alterarCompraEditalAviso');
    Route::post('importarCompraEditalAviso', 'CompraEditalAvisoController@importarCompraEditalAviso');
    Route::post('buscarEditais', 'CompraEditalAvisoController@buscarEditais');
    Route::post('incluirRespostaItem', 'CompraEditalAvisoController@incluirRespostaItem');
    Route::post('buscarCompras', 'CompraEditalAvisoController@buscarCompras');
    Route::post('buscarCompra', 'CompraEditalAvisoController@buscarCompra');
    Route::post('excluirCompra', 'CompraEditalAvisoController@excluirCompra');
    Route::post('incluir-documento', 'CompraEditalAvisoController@incluirDocumento');
    Route::post('buscar-documentos', "CompraEditalAvisoController@buscarDocumentos");
    Route::post('excluir-documento', "CompraEditalAvisoController@excluirDocumento");
});

Route::prefix('ataRegistroPreco')->group(function () {
    Route::post('incluir', 'AtaRegistroPrecoController@incluir');
    Route::post('buscar', 'AtaRegistroPrecoController@buscar');
    Route::post('excluir', 'AtaRegistroPrecoController@excluir');
    Route::post('retificar', 'AtaRegistroPrecoController@retificar');
});

Route::prefix('planoContratacoes')->group(function () {
    Route::prefix('relatorios') ->group(function () {
        Route::post('itens-plano', 'PlanoContratacoesController@relatorioItensPlano');
    });
    Route::post('buscarUnidades', 'PlanoContratacoesController@buscarUnidades');
    Route::post('incluir', 'PlanoContratacoesController@incluir');
    Route::post('elaboracao', 'PlanoContratacoesController@elaboracao');
    Route::post('incluir-item', 'PlanoContratacoesController@incluirItem');
    Route::post('buscar-elemento-despesa', 'PlanoContratacoesController@buscarElementoDespesa');
    Route::post('buscar-unidades', 'PlanoContratacoesController@buscarUnidades');
    Route::post('buscar-itens', 'PlanoContratacoesController@buscarItens');
    Route::post('remover-item', 'PlanoContratacoesController@removerItem');
    Route::post('editar-item', 'PlanoContratacoesController@editarItem');
    Route::post('buscar-planos', 'PlanoContratacoesController@buscarPlanos');
    Route::post('editar-plano', 'PlanoContratacoesController@editarPlano');
    Route::post('excluir-plano', 'PlanoContratacoesController@excluirPlano');

});

Route::prefix('contratos')->group(function () {
    Route::post('incluirContrato', 'ContratosController@incluirContrato');
    Route::post('incluirDocumento', 'ContratosController@incluirDocumento');
    Route::post('buscarContratos', 'ContratosController@buscarContratos');
    Route::post('excluirContrato', 'ContratosController@excluirContrato');
    Route::post('buscar-documentos', 'ContratosController@buscarDocumentos');
    Route::post('excluir-documento', 'ContratosController@excluirDocumento');
});

Route::prefix('termos')->group(function () {
    Route::post('incluir', 'TermosController@incluir');
    Route::post('buscar', 'TermosController@buscar');
    Route::post('excluir', 'TermosController@excluir');
    Route::post('retificar', 'TermosController@retificar');

    Route::prefix('documentos')->group(function () {
        Route::post('incluir', 'TermosController@incluirDocumento');
        Route::post('buscar', 'TermosController@buscarDocumentos');
        Route::post('excluir', 'TermosController@excluirDocumento');
    });
});
