<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

// php5.6 artisan route:list --path=v4/api/financeiro/contabilidade
Route::prefix('procedimento')->group(function () {

    Route::post(
        'manutencao/fonte-recurso/despesa/lancamentos',
        "ManutencaoFonteRecursoController@lancamentosDespesa"
    );
    Route::post(
        'manutencao/fonte-recurso/receita/lancamentos',
        "ManutencaoFonteRecursoController@lancamentosReceita"
    );
    Route::post(
        'manutencao/fonte-recurso/atualizarComplemento',
        "ManutencaoFonteRecursoController@atualizarComplemento"
    );
    Route::get(
        'encerramento-periodo-contabil/{instituicao}',
        "EncerramentoPeriodoContabilController@ultimaData"
    );

    Route::prefix('apropriacao')->group(function () {
        Route::prefix('decimo-ferias')->group(function () {
            Route::prefix('apropriar')->group(function () {
                Route::get('competencia', "ApropriacaoDecimoFeriasController@buscarCompetenciaApropriar");
                Route::post('buscarValores', "ApropriacaoDecimoFeriasController@buscarValores");
                Route::post('processar', "ApropriacaoDecimoFeriasController@apropriar");
            });

            Route::prefix('estornar')->group(function () {
                Route::get('competencia', "ApropriacaoDecimoFeriasController@buscarCompetenciaEstornar");
                Route::post('processar', "ApropriacaoDecimoFeriasController@estornar");
            });
        });
    });

    Route::prefix('escrituracao-contabil')->group(function () {
        Route::post('implantar-saldo-inicial', "ExercicioContabilController@implantarSaldoInicial");
        Route::post('virada-rp', "ExercicioContabilController@virarRestosPagar");
        Route::get('virada-rp', "ExercicioContabilController@validaVirarRestorPagar");
    });

    Route::prefix('mapeamento-empenho-rp-manual')->group(function() {
        Route::get('/', 'MapeamentoEmpenhoRPManualController@index');
        Route::post('/', 'MapeamentoEmpenhoRPManualController@save');
        Route::delete('/{id}', 'MapeamentoEmpenhoRPManualController@delete');
    });

    Route::prefix('mapeamento-empenho-rp-conta')->group(function () {
        Route::get('/', 'MapeamentoEmpenhoRPContaController@index');
        Route::get('/contas', 'MapeamentoEmpenhoRPContaController@contas');
        Route::get('/empenhos', 'MapeamentoEmpenhoRPContaController@empenhos');
        Route::get('/empenhos-conta', 'MapeamentoEmpenhoRPContaController@empenhosConta');

        Route::post('/', 'MapeamentoEmpenhoRPContaController@save');
        Route::delete('/', 'MapeamentoEmpenhoRPContaController@delete');
        Route::delete('/empenhos', 'MapeamentoEmpenhoRPContaController@deleteAll');
    });
});

Route::prefix('relatorio')->group(function () {
    Route::post(
        'notificacao-recebimento-recursos-federais',
        "NotificacaoRecebimentoRecursosFederaisController@processar"
    );
    Route::post('balancete-receita', 'BalanceteReceitaController@emitirBalancete');
    Route::post('balancete-receita-por-complemento', 'BalanceteReceitaController@emitirPorComplemento');
    Route::post('balancete-receita-plano-padrao', 'BalanceteReceitaController@emitirPlanoPadrao');
    Route::post('balancete-despena-por-complemento', 'BalanceteDespesaController@emitirPorComplemento');
    Route::post('balancete-despena-plano-padrao', 'BalanceteDespesaController@emitirPlanoPadrao');
    Route::prefix('balancete')->group(function () {
        Route::post('verificacao/complemento', 'BalanceteVerificacaoController@emitirPorComplemento');
        Route::post('verificacao/informacao-complementar', 'BalanceteVerificacaoController@emitirInformacaoComplementar');
    });

    Route::post('demonstrativo-evolucao-receita', 'EvolucaoReceitaController@demonstrativoEvolucaoReceita');
    Route::post('demonstrativo-evolucao-despesa', 'EvolucaoDespesaController@demonstrativoEvolucaoDespesa');
    Route::post('anexo17', "AnexoXVIIController@processar");

    Route::post('superavit-por-recurso', "RelatorioSuperavitController@porRecurso");
});

Route::prefix('consulta')->group(function () {
    Route::post(
        'lancamento/conta-pcasp/documentos',
        "ConsultaLancamentoPcaspController@getValoresPorDocumento"
    );
    Route::post(
        'lancamento/conta-pcasp/recursos',
        "ConsultaLancamentoPcaspController@getValoresPorRecurso"
    );
    Route::post(
        'lancamento/conta-pcasp/info',
        "ConsultaLancamentoPcaspController@getInfoLancamentos"
    );
});

Route::prefix('')->group(function () {
    $path = "\App\Domain\Financeiro\Contabilidade\Controllers\\";
    Route::post(
        'relatorio-disponibilidade-recurso',
        $path . "DisponibilidadeRecursoController@processarSaldoDisponibilidadeRecurso"
    );
    Route::post(
        'relatorio-conferencia-por-recurso',
        $path . "DisponibilidadeRecursoController@relatorioConferenciaPorRecurso"
    );
    Route::post(
        'obter-dados-conferencia-por-recurso',
        $path . "DisponibilidadeRecursoController@obterDadosConferenciaPorRecurso"
    );
});

Route::prefix('relatorio/rreo')->group(function () {
    Route::post('anexo-1', "RREOAnexosController@anexoUm");
    Route::post('anexo-3-in-rs', "RREOAnexosController@anexoTresInRs");
    Route::post('anexo-3-mdf', "RREOAnexosController@anexoTresMdf");
    Route::post('anexo-4', "RREOAnexosController@anexoQuatro");
    Route::post('anexo-6', "RREOAnexosController@anexoSeis");
    Route::post('anexo-8', "RREOAnexosController@anexoOito");
    Route::post('anexo-12', "RREOAnexosController@anexoDoze");
});

Route::prefix('relatorio/rgf')->group(function () {
    Route::post('anexo-1-in-rs', "RGFAnexosController@anexoUmInRs");
    Route::post('anexo-1-mdf', "RGFAnexosController@anexoUmMdf");
    Route::post('anexo-2', "RGFAnexosController@anexoDois");
    Route::post('anexo-5', "RGFAnexosController@anexoCinco");
});

Route::prefix('relatorio-legal')->group(function () {

    //valores manuais
    Route::get('linha/valor-manual', "LrfValorManualController@get");
    Route::post('linha/valor-manual', "LrfValorManualController@salvar");
    Route::delete('linha/valor-manual/{id}', 'LrfValorManualController@delete');
    Route::get('linhas-manuais/{codigo}/{tipo}', "AnexosLrfController@linhasManuais");

    //notas explicativas
    Route::get('notas-explicativas', 'LrfNotaExplicativaController@get');
    Route::post('notas-explicativas', 'LrfNotaExplicativaController@salvar');
    Route::delete('notas-explicativas/{id}', 'LrfNotaExplicativaController@delete');


    //vers?es dos anexos
    Route::prefix('versoes')->group(function () {
        Route::get('anexo/{anexo}/{tipo}', "AnexosLrfController@versoesAnexo");
    });

    Route::post('emitir', 'AnexosLrfController@emitir');
    Route::post('emitir-agora', 'AnexosLrfController@emitirAgora');

    Route::get('emissoes', 'LrfEmissaoController@get');
    Route::delete('emissao/{codigo}', 'LrfEmissaoController@delete');
    Route::post('emissao/publicar', 'LrfEmissaoController@publicar');
});

Route::prefix('plano-contas')->group(function () {
    Route::prefix('importar')->group(function () {
        Route::post('pcasp', "ImportarPlanoContasController@pcasp");
        Route::post('atualizar-pcasp', "ImportarPlanoContasController@atualizarPcasp");
        Route::post('orcamentario/despesa', "ImportarPlanoContasController@despesa");
        Route::post('orcamentario/atualizar-despesa', "ImportarPlanoContasController@atualizarDespesa");
        Route::post('orcamentario/receita', "ImportarPlanoContasController@receita");
        Route::post('orcamentario/atualizar-receita', "ImportarPlanoContasController@atualizarReceita");
    });

    Route::prefix('emitir')->group(function () {
        Route::get('pcasp/{tipo}/{exercicio}', "EmissaoPlanoContasController@pcasp");
        Route::post('pcasp/mapeamento', "EmissaoPlanoContasController@mapeamento");
        Route::get('orcamentario/{tipoPlano}/{origem}/{exercicio}', "EmissaoPlanoContasController@orcamentario");
        Route::post('orcamentario/receita/mapeamento', "PlanoOrcamentarioReceitaController@mapeamento");
        Route::post('orcamentario/despesa/mapeamento', "PlanoOrcamentarioDespesaController@mapeamento");
    });

    Route::prefix('consulta')->group(function () {
        // consulta quais mapeamentos foram realizados
        Route::get('mapeamentos/{tipo}/{exercicio}', "PcaspController@mapeamentosRealizados");

        Route::post('pcasp/padrao', "PcaspController@getContasPadrao");
        Route::post('pcasp/ecidade', "PcaspController@getContasEcidade");

        // despesa
        Route::post('orcamentario/despesa/padrao', "PlanoOrcamentarioDespesaController@getContasPadrao");
        Route::post('orcamentario/despesa/ecidade', "PlanoOrcamentarioDespesaController@getContasMapearEcidade");

        //receita
        Route::post('orcamentario/receita/padrao', "PlanoOrcamentarioReceitaController@getContasPadrao");
        Route::post('orcamentario/receita/ecidade', "PlanoOrcamentarioReceitaController@getContasEcidade");
    });

    Route::post('pcasp/salvar-conta-caixa', 'PcaspController@salvarContaCaixa');
    Route::post('pcasp/salvar-conta-bancaria', 'PcaspController@salvarContaBancaria');
    Route::post('pcasp/salvar-conta-extra', 'PcaspController@salvarContaExtra');
    Route::post('pcasp/salvar-outras-contas', "PcaspController@salvarOutrasContas");
    Route::post('pcasp/remover-reduzido', "PcaspController@removerReduzido");

    Route::post('pcasp/vincular', "PcaspController@vincular");
    Route::post('pcasp/vincular-geral', "PcaspController@vincularGeral");
    // importa o vinculo do exerc?cio anterior
    Route::post('pcasp/importar-vinculo', "PcaspController@importarVinculo");
    Route::post('pcasp/editar-estruturais', "PcaspController@editarEstruturais");

    Route::get('pcasp/conta-corrente/{codcon}/{exercicio}', "PcaspContaCorrenteController@buscarPorPcasp");
    Route::post('pcasp/conta-corrente/salvar', "PcaspContaCorrenteController@adicionar");
    Route::post('pcasp/conta-corrente/remover', "PcaspContaCorrenteController@remover");

    //despesa
    Route::post('orcamentario/despesa/vincular', "PlanoOrcamentarioDespesaController@vincular");
    Route::post('orcamentario/despesa/vinculo-geral', "PlanoOrcamentarioDespesaController@vinculoGeral");
    // importa o vinculo do exerc?cio anterior
    Route::post('orcamentario/despesa/importar-vinculo', "PlanoOrcamentarioDespesaController@importarVinculo");
    Route::post('orcamentario/despesa/desvincular', "PlanoOrcamentarioDespesaController@desvincular");

    //receita
    Route::post('orcamentario/receita/vincular', "PlanoOrcamentarioReceitaController@vincular");
    Route::post('orcamentario/receita/vinculo-geral', "PlanoOrcamentarioReceitaController@vinculoGeral");
    // importa o vinculo do exerc?cio anterior
    Route::post('orcamentario/receita/importar-vinculo', "PlanoOrcamentarioReceitaController@importarVinculo");
    Route::post('orcamentario/receita/desvincular', "PlanoOrcamentarioReceitaController@desvincular");

    Route::prefix('exclusao-geral')->group(function () {
        // receita
        Route::get(
            'orcamentario/receita/{estrutural}/{exercicio}',
            "PlanoOrcamentarioReceitaController@getReceitasSemUso"
        );

        Route::post('orcamentario/receita', "PlanoOrcamentarioReceitaController@exclusaoGeral");
        // despesa
        Route::get(
            'orcamentario/despesa/{estrutural}/{exercicio}',
            "PlanoOrcamentarioDespesaController@getDespesasSemUso"
        );
        Route::post('orcamentario/despesa', "PlanoOrcamentarioDespesaController@exclusaoGeral");


        Route::get('pcasp/{estrutural}/{exercicio}', 'PcaspController@contasSemUso');
        Route::post('pcasp', 'PcaspController@exclusaoGeral');
    });

    Route::get('pcasp/estrural-existe/{estrutural}/{exercicio}', "PcaspController@estruturalEstrutural");
});

Route::prefix('msc')->group(function () {
    Route::post('importar', "MatrizController@importar");
    Route::post('emitir', 'MatrizController@emitir');
    Route::post('consistencia/atributoFP/{exercicio}', 'MatrizController@atributoFP');
});

Route::post('relatorio/atributos-plano-conas', "RelatorioConferenciaController@atributosPlanoContasMSC");


Route::prefix('BalancetesMensais')->group(function () {
    Route::post('balancete-mensal-anexo1', "BalancetesMensaisController@processarAnexo1");
});

Route::prefix('relatorio-tce')->group(function () {
    Route::post('exportar', "RelatorioTCEController@exportar");
    Route::post('buscar', "RelatorioTCEController@buscar");
});

Route::get('sistemas', "PcaspController@sistemas");
Route::get('sistema-conta', "PcaspController@sistemaConta");

Route::prefix('conta-corrente')->group(function () {
    Route::prefix('implantacao')->group(function () {
        Route::post('ddr', "ContaCorrente@implantarDDR");
        Route::get('ddr/template', "ContaCorrente@template");
    });
    Route::prefix('cadastro')->group(function (){
        Route::get('verificaExistenciaExeContaCorrente', "ContaCorrente@verificaExistenciaExeContaCorrente");
        Route::post('configurarSaldoInicialPorRecurso', "ContaCorrente@configurarSaldoInicialPorRecurso");
    });

});

Route::prefix('siai')->group(function () {
    Route::post('anexo-3-rreo', "SIAIController@anexoTresRREO");
    Route::post('anexo-4-rreo', "SIAIController@anexoQuatroRREO");
    Route::post('anexo-6-rreo', "SIAIController@anexoSeisRREO");
    Route::post('anexo-7-rreo', "SIAIController@anexoSeteRREO");
    Route::post('anexo-8-rreo', "SIAIController@anexoOitoRREO");
    Route::post('anexo-12-rreo', "SIAIController@anexoDozeRREO");
    Route::post('anexo-13-rreo', "SIAIController@anexoTrezeRREO");
    Route::post('anexo-1-rgf', "SIAIController@anexoUmRGF");
    Route::post('anexo-2-rgf', "SIAIController@anexoDoisRGF");
    Route::post('anexo-3-rgf', "SIAIController@anexoTresRGF");
    Route::post('anexo-4-rgf', "SIAIController@anexoQuatroRGF");
});


/**
 * Rotas para automatizar corre??es de problemas gerados pelo sistema.
 */
Route::prefix('fix')->group(function () {
    Route::prefix('lancamento')->group(function () {
        // cria os recursos para os lancamentos que foram gerados sem recurso.
        Route::post('criar-recurso', 'ManutencaoLancamentosController@criarRecursos');
        Route::post('recurso-empenho', 'ManutencaoLancamentosController@corrigirRecursosEmpenho');
        Route::post('documento-142', 'ManutencaoLancamentosController@corrigirDocumento142');
        Route::post('suplementacoes', 'ManutencaoLancamentosController@corrigirSuplementacoes');
        Route::post('conlancamrecurso', 'ManutencaoLancamentosController@corrigirConlancamrecurso');
        Route::post('slips', 'ManutencaoLancamentosController@corrigirSlips');
        Route::post('contas-bancarias', 'ManutencaoLancamentosController@corrigirContasBancarias');
        Route::post('estoque-patrimonio', 'ManutencaoLancamentosController@corrigirEstoquePatrimonio');
    });

    Route::post('ajuste-saldo-contas-msc', 'ManutencaoLancamentosController@ajusteSaldoContaMsc');
    Route::post('gera-csv-ajuste-saldo-contas', 'ManutencaoLancamentosController@geraCsvSaldoContaMsc');
});

/**
 * Rotas para pesquisa/manuten??o Hist?rico Lancamento
 */
Route::get('historico', 'HistoricoController@index');

Route::post('lancamento-manual', "LancamentoManualController@store");
Route::post('lancamento-manual/retificar', "LancamentoManualController@retificar");
Route::delete('lancamento-manual/{lancamento}', "LancamentoManualController@destroy");
Route::delete('lancamento-manual/excluir-lote/{lote}', "LancamentoManualController@destroyLote");
Route::get('lancamento-manual', 'LancamentoManualController@getLancamentosManuais');
Route::get('lancamento-manual/proximo-lote', "LancamentoManualController@proximoLote");
Route::get('lancamento-manual/nota-lancamento', "LancamentoManualController@notaLancamento");
Route::get('reduzidos/pcasp', 'ReduzidoPcaspController@index');

/**
 * Rotas do modulo comtabilidade/tce
 */
Route::prefix('tce')->group(function () {

    /**
     * Sigfis - TCE RJ
     */
    Route::prefix('rj/sigfis')->group(function () {

        Route::prefix('unidadegestora')->group(function () {
            Route::get('/', 'SigfisUnidadeGestoraController@index');
            Route::get('/{codigo}', 'SigfisUnidadeGestoraController@show');
            Route::post('/', 'SigfisUnidadeGestoraController@store');
        });
    });
});

Route::prefix('deliberacao')->group(function () {
    Route::prefix('285')->group(function () {
        Route::post('modelo-5', 'DeliberacaoController@modelo5');
    });
});
