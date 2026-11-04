<?php
// php5.6 artisan route:list --path=v4/api/financeiro/planejamento

Route::prefix('consulta')->group(function () {
    Route::get('plano/{id}', "PlanejamentoController@show");
    Route::get('planos/{tipo}/{status}', "PlanejamentoController@index");
    Route::get('planos/{tipo}', "PlanejamentoController@porTipo");
    Route::get('planos-em-desenvolvimento/{tipo?}', "PlanejamentoController@planejamentoEmDesenvolvimento");
    Route::get('planos-ativos/{tipo?}', "PlanejamentoController@planejamentoAtivo");

    Route::get('situacoes/movimentar/plano/{id}', "PlanejamentoController@possiveisSituacoesAtualizar");
    Route::get('ods', "OdsController@get");
});

Route::post('identidade-organizacional', "PlanejamentoController@salvarIdentidadeOrganizacional");
Route::post('comissao', "PlanejamentoController@salvarComissao");
Route::post('objetivo-estrategico', "PlanejamentoController@salvarObjetivoEstrategico");
Route::post('objetivo-estrategico/excluir', "PlanejamentoController@removerObjetivoEstrategico");
Route::post('ppa', "PlanejamentoController@salvarPPA");
Route::post('ldo', "PlanejamentoController@salvarLDO");
Route::post('loa', "PlanejamentoController@salvarLOA");
Route::post('remove', "PlanejamentoController@remove");
Route::post('criarVinculo', "PlanejamentoController@criarVinculo");
// Essa rota cria um PPA com base em um outro plano de governo.
Route::post('criarPPA', "PlanejamentoController@criarPPA");

Route::post('status-planejamento/situacao', "StatusPlanejamentoController@store");

/**
 * @todo refatorar projecao/despesa/calcular
 */
Route::post('projecao/despesa/buscar', "ProjecaoDespesaController@buscar");
Route::post('projecao/despesa/recalcular', "ProjecaoDespesaController@recalcular");
Route::post(
    'projecao/despesa/criar-detalhamento-novas-dotacoes',
    "ProjecaoDespesaController@criarDetalhamentoNovasDotacoes"
);
Route::post('projecao/despesa/atualizar/valor-exercicio', "ProjecaoDespesaController@previsaoExercicio");
Route::post('projecao/despesa/atualizar/valor-base', "ProjecaoDespesaController@atualizarValorBase");

// projecao receita
Route::post('projecao/receita/recalcular', "ProjecaoReceitaController@recalcular");
Route::post('projecao/receita/buscar', "ProjecaoReceitaController@buscar");
Route::post('projecao/receita/atualizar/valor-exercicio', "ProjecaoReceitaController@previsaoExercicio");
Route::post('projecao/receita/atualizar/valor-base', "ProjecaoReceitaController@valorBase");
// manutenção da receita
Route::post('receita/previsao/buscar', "EstimativaReceitaController@buscar");
Route::post('receita/previsao/salvar', "EstimativaReceitaController@salvar");
Route::post('receita/previsao/remover', "EstimativaReceitaController@remover");
Route::post('receita/previsao/removerNaturezas', "EstimativaReceitaController@removerNaturezas");
Route::get('receita/previsao/{id}', "EstimativaReceitaController@show");
// cronograma de desembolso da receita
Route::post('receita/cronograma/buscar', "CronogramaDesembolsoReceitaController@buscar");
Route::post('receita/cronograma/salvar-metas', "CronogramaDesembolsoReceitaController@salvarMetas");
Route::post('receita/cronograma/recalcular', "CronogramaDesembolsoReceitaController@recalcular");


//fator de correcao
Route::post('fator-correcao/despesa', "FatorCorrecaoController@salvarDespesa");
Route::post('fator-correcao/receita', "FatorCorrecaoController@salvarReceita");
Route::post('fator-correcao/index', "FatorCorrecaoController@index");


Route::post('area-resultado/salvar', "AreaResultadoController@salvar");
Route::post('area-resultado/excluir', "AreaResultadoController@delete");

Route::post('areas-resultado/filtros', "AreaResultadoController@buscar");

Route::post('objetivos-estrategicos/filtros', "ObjetivoEstrategicoController@buscar");

Route::get('origens', "OrigemController@index");
Route::get('periodos', "PeriodoController@index");

Route::post('programas-estrategico/remover', "ProgramaEstrategioController@delete");
Route::post('programas-estrategico/salvar', "ProgramaEstrategioController@salvar");
// busca o plano aplicando os filtros informados
Route::post('programas-estrategico/filtros', "ProgramaEstrategioController@buscar");
Route::post('programas-estrategico/saldo/iniciativas', "ProgramaEstrategioController@calculaSaldoIniciativa");
Route::get('programas-estrategico', "ProgramaEstrategioController@index");
Route::get('programas-estrategico/{id}', "ProgramaEstrategioController@show");

// rotas do orgao do programa estratético
Route::post('orgao-programa/filtros', "OrgaoProgramaEstrategioController@buscar");
Route::post('orgao-programa/salvar', "OrgaoProgramaEstrategioController@salvar");

// rotas do objetivo do programa estratético
Route::post('objetivo-programa/filtros', "ObjetivoProgramaEstrategioController@buscar");
Route::post('objetivo-programa/salvar', "ObjetivoProgramaEstrategioController@salvar");
Route::post('objetivo-programa/remover', "ObjetivoProgramaEstrategioController@delete");
Route::post(
    'objetivo-programa/saldo/iniciativas',
    "ObjetivoProgramaEstrategioController@calculaSaldoIniciativa"
);


// rotas das metas dos objetivos do programa estratético
Route::post('meta-objetivo/salvar', "MetaObjetivoController@salvar");
Route::post('meta-objetivo/remover', "MetaObjetivoController@delete");

// rotas das indicador do programa estratético
Route::post('indicador-programa/salvar', "IndicadorProgramaEstrategicoController@salvar");
Route::post('indicador-programa/remover', "IndicadorProgramaEstrategicoController@delete");

// rotas das iniciativas dos objetivos do programa estratético
Route::post('iniciativa/filtros', "IniciativaController@buscar");
Route::post('iniciativa/salvar', "IniciativaController@salvar");
Route::post('iniciativa/remover', "IniciativaController@delete");
Route::get('iniciativa/{id}', "IniciativaController@show");
Route::get('iniciativa/regionalizacoes/{id}', "IniciativaController@getRegionalizacoes");


// rotas das metas da iniciativa
Route::post('metas-iniciativa/salvar', "MetasIniciativaController@salvar");

// rotas das regionalizações
Route::post('regionalizacao/salvar', "IniciativaController@salvarRegionalizacoes");
Route::post('regionalizacao/excluir', "IniciativaController@excluirRegionalizacoes");

// rotas das regionalizações
Route::post('abrangencia/salvar', "IniciativaController@salvarAbrangencias");
Route::post('abrangencia/excluir', "IniciativaController@excluirAbrangencias");

/**
 * Rotas de vínculos dos programas
 */
Route::post('programas-vincular-area/buscar', "ProgramaPorAreaController@buscar");
Route::post('programas-vincular-area/vincular', "ProgramaPorAreaController@vincular");

Route::post('programas-vincular-objetivo/buscar', "ProgramaPorObjetivoController@buscar");
Route::post('programas-vincular-objetivo/vincular', "ProgramaPorObjetivoController@vincular");

/**
 * rotas para manutenção dos vínculos das iniciativas com os objetivos dos programas estratégicos
 */
Route::post('iniciativa-vincular-objetivo/buscar', "IniciativaPorObjetivoController@buscar");
Route::post('iniciativa-vincular-objetivo/vincular', "IniciativaPorObjetivoController@vincular");

/**
 * Detalhamento da despesa
 */
Route::get('despesa/detalhamento/{id}', "DetalhamentoDespesaController@show");
Route::post('despesa/detalhamento/buscar', "DetalhamentoDespesaController@buscar");
Route::post('despesa/detalhamento/salvar', "DetalhamentoDespesaController@salvar");
Route::post('despesa/detalhamento/remover', "DetalhamentoDespesaController@delete");

Route::post('despesa/cronograma/buscar', "CronogramaDesembolsoDespesaController@buscar");
Route::post('despesa/cronograma/salvar', "CronogramaDesembolsoDespesaController@salvar");
Route::post('despesa/cronograma/recalcular', "CronogramaDesembolsoDespesaController@recalcular");
Route::post(
    'despesa/cronograma/recalcularGeral',
    "CronogramaDesembolsoDespesaController@recalcularGeral"
);

Route::prefix('relatorios')->group(function () {
    Route::post('programa-estrategico', "RelatorioProgramaTematicoController@emitir");
    Route::post('programa-gestao', "RelatorioProgramaGestaoController@emitir");
    Route::post('por-elemento', "RelatorioProjecaoPorElementoController@emitir");
    Route::post('projecao-receita', "RelatorioProjecaoReceitaController@emitir");
    Route::post('resumo-projecao-receita', "RelatorioProjecaoReceitaController@emitirResumo");
    Route::post('anexo-um', "AnexosLdoController@anexoUm");
    Route::post('anexo-dois', "AnexosLdoController@anexoDois");
    Route::post('anexo-tres', "AnexosLdoController@anexoTres");
    Route::post('anexo-quatro', "AnexosLdoController@anexoQuatro");
    Route::post('anexo-cinco', "AnexosLdoController@anexoCinco");
    Route::post('anexo-seis', "AnexosLdoController@anexoSeis");
    Route::post('anexo-sete', "AnexosLdoController@anexoSete");
    Route::post('anexo-oito', "AnexosLdoController@anexoOito");
    Route::post('projecao-despesa-agrupado', "RelatorioProjecaoDespesaController@agrupadoPor");
    Route::post(
        'projecao-despesa-agrupado-sintetico',
        "RelatorioProjecaoDespesaController@agrupadoSintetico"
    );
    Route::post('meta-arrecadacao', "RelatoriosCronogramaController@metaArrecadacao");
    Route::post('meta-arrecadacao-plano-padrao', "RelatoriosCronogramaController@metaArrecadacaoPlanoPadrao");
    Route::post('cotas-despesa', "RelatoriosCronogramaController@cotaDespesa");
    Route::post('meta-x-cotas', "RelatoriosCronogramaController@metaVersusCota");
    Route::post('previsao-rcl-outros-anexos', "RelatoriosPlanejamentoRclController@previsaoRclOutrosAnexos");

    // relatorios para auxiliar a conferencia dos recursos
    Route::post('projecao-receita-recurso', "RelatorioProjecaoReceitaController@emitirConferenciaRecurso");
    Route::post(
        'projecao-despesa-conferencia-recurso',
        "RelatorioProjecaoDespesaController@conferenciaRecurso"
    );
    Route::post('planejamento-por-recurso', "PlanejamentoController@porRecurso");

    Route::prefix('anexos4320')->group(function () {
        Route::post('orcamento', "AnexosLoaController@orcamento");
        Route::post('anexo2', "AnexosLoaController@anexoDois");
        Route::post('anexo3', "AnexosLoaController@anexoTres");
    });
    Route::post('acompanhamento-ppa-ldo', "RelatorioAcompanhamentoController@emitir");
});

Route::get('configuracao', "ConfiguracaoController@index");
Route::post('configuracao/salvar', "ConfiguracaoController@salvar");

//
Route::get('pib/{planejamento_id}', "PibController@show");
Route::post('pib', "PibController@store");

Route::post('recalcular-valores-sinteticos', "CalcularValoresSinteticosController@calcular");

Route::post('orcamento/gerar', "GerarOrcamentoController@exportar");
Route::post('orcamento/cancelar', "GerarOrcamentoController@cancelar");
