<?php
// php artisan route:list --path=v4/api/financeiro/orcamento
Route::prefix('cadastro')->group(function () {

    Route::post('especificacao-recurso/salvar', "EspecificacaoRecursoController@salvar");
    Route::post('especificacao-recurso/excluir', "EspecificacaoRecursoController@excluir");

    // manutenção dos recursos antes de 2022
    Route::post('recurso/salvar', "RecursoAntes2022Controller@salvar")->middleware('legacySession');
    Route::post('recurso/excluir', "RecursoAntes2022Controller@excluir")->middleware('legacySession');

    // manutenção dos recursos a partir de 2022
    Route::post('recurso/salvar-atualizado', "RecursoController@salvar");
    Route::get('recurso/{id}/{exercicio}', "RecursoController@buscar");
    Route::get('recursos/inativar/{exercicio}', "RecursoController@recursosInativar");
    Route::post('recurso/inativar', "RecursoController@inativar");
    Route::post('recurso/excluir', "RecursoController@excluir");

    Route::get('recursos/depreciados/{exercicio}', "RecursoController@depreciados2022");

    Route::get('complemento', "ComplementoController@get");
    Route::post('complemento/salvar', "ComplementoController@salvar")->middleware('legacySession');
    Route::post('complemento/excluir', "ComplementoController@excluir")->middleware('legacySession');
});

Route::get('utiliza-decimal/{exercicio}', "ParametroController@utilizaDecimal");

Route::prefix('relatorios')->group(function () {
    Route::post('siconfi-recursos-2022', "RecursoController@listaSiconfi2022");
    Route::post('meta-arrecadacao', "RelatoriosCronogramaController@metaArrecadacao");
    Route::post('cotas-despesa', "RelatoriosCronogramaController@cotaDespesa");
    Route::post('meta-x-cotas', "RelatoriosCronogramaController@metaVersusCota");

    // anexos do orçamento
    Route::post('anexo-3', "AnexosController@anexo3");
    Route::post('anexo-2-receita', "AnexosController@receitaAnexo2");
});

Route::prefix('de-para-siconfi')->group(function () {
    Route::get('exportar/{exercicio}', "RecursoController@exportarPlanilhaSiconfi");
    Route::post('importar', "RecursoController@importarPlanilhaSiconfi");
});

/**
 * Rotas envolvendo recursos
 */
Route::prefix('recursos')->group(function () {
    Route::get('byFilters', "RecursoController@byFilters");
    Route::get('classificacoes', "ClassificacaoFonteRecursoController@get");
    Route::get('complementos', "ComplementoController@byFilters");
    Route::get('confere-vinculo', "RecursoController@confereVinculo");
    // @todo se for criar outra rota com GET tem que ser em cima dessa.
    Route::get('{exercicio}/{data?}', "RecursoController@get");

});

Route::prefix('despesa')->group(function () {
    Route::get('naturezas-despesas', "NaturezaDespesaController@index");
    Route::get('dotacoes', "DotacaoController@index");
});

Route::prefix('receita')->group(function () {
    Route::get('natureza-receita', "NaturezaReceitaController@index");
    Route::get('receitas', "ReceitaController@index");
});

/**
 * @todo mover a rota para o prefixo recursos
 */
Route::get('classificacao/com-siconfi', "ClassificacaoFonteRecursoController@comSiconfi");

Route::get('tipos-detalhamento', "RecursoController@tiposDetalhamento");
Route::get('buscar-dotacoes/{elemento}', "BuscarDadosDotacoesController@getDotacoes");
Route::get('buscar-saldo-dotacao/{codigoDotacao}', "BuscarDadosDotacoesController@getSaldoDotacao");
Route::get('get-desdobramento/{codigoMaterial}', "BuscarDadosDotacoesController@getDesdobramentoMaterial");
Route::get('get-desdobramento-item/{codele}', "BuscarDadosDotacoesController@getDesdobramentoItem");
Route::get('buscar-filtros-dotacoes', "BuscarDadosDotacoesController@buscarDadosFiltrosDotacoes");


Route::prefix('acompanhamento')->group(function () {
    Route::get('cronograma/bases-calculo-despesa', 'AcompanhamentoDesembolsoDespesaController@baseCalculo');
    Route::get('cronograma/despesa/{exercicio}', 'AcompanhamentoDesembolsoDespesaController@buscar');
    Route::post('cronograma/despesa/salvar', 'AcompanhamentoDesembolsoDespesaController@salvarEstimativa');
    Route::post('cronograma/despesa/recalcular', 'AcompanhamentoDesembolsoDespesaController@recalcular');

    Route::get('cronograma/bases-calculo-receita', 'AcompanhamentoDesembolsoReceitaController@baseCalculo');
    Route::get('cronograma/receita/{exercicio}', 'AcompanhamentoDesembolsoReceitaController@buscar');
    Route::post('cronograma/receita/salvar', 'AcompanhamentoDesembolsoReceitaController@salvarEstimativa');
    Route::post('cronograma/receita/recalcular', 'AcompanhamentoDesembolsoReceitaController@recalcular');
});
