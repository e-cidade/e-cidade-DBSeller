<?php

// php5.6 artisan route:list --path=web/financeiro/contabilidade
use App\Domain\Financeiro\Contabilidade\Services\EncerramentoPeriodoContabilService;

Route::prefix('conta-corrente')->group(function () {
    Route::prefix('implantacao')->group(function () {
        Route::get('ddr', function () {
            return view('financeiro.contabilidade.conta-corrente.implantacao-ddr');
        });
    });
    Route::prefix('cadastro')->group(function (){
        Route::get('por-recurso', function () {
            return view('financeiro.contabilidade.conta-corrente.cadastro-por-recurso', [
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu')
            ]);
        });
    });
});

Route::prefix('plano-contas')->group(function () {
    Route::prefix('mapeamento')->group(function () {
        Route::get('vinculo-automatico', function () {
            return view('financeiro.contabilidade.plano-contas.vinculo-automatico');
        });
    });
});

Route::prefix('msc')->group(function () {
    Route::prefix('lrf')->group(function () {
        Route::get('anexo/{tipo}/{codigo}/{consolida}', 'AnexosLrfController@viewAnexo');
    });

    Route::get('emissao', function () {
        return view(
            'financeiro.contabilidade.msc.emissao',
            [
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu')
            ]);
    });
});

Route::prefix('procedimento')->group(function () {
    Route::prefix('rotinas-mensais')->group(function () {
        Route::prefix('apropriacao')->group(function () {
            Route::get('decimo-ferias/apropriar', function () {
                return view(
                    'financeiro.contabilidade.apropriacao.decimo-ferias.processamento',
                    ["estornar" => 0]
                );
            });
            Route::get('decimo-ferias/estornar', function () {
                return view(
                    'financeiro.contabilidade.apropriacao.decimo-ferias.processamento',
                    ["estornar" => 1]
                );
            });
        });
    });

    Route::get('mapeamento-empenho-rp-manual', function () {
        return view(
            'financeiro.contabilidade.procedimentos.mapeamento-empenho-rp-manual',
            [
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu')
            ]
        );
    });

    Route::get('mapeamento-empenho-rp-conta', function () {
        return view(
            'financeiro.contabilidade.procedimentos.mapeamento-empenho-rp-conta',
            [
                'instituicao' => session('DB_instit'),
                'exercicio' => session('DB_anousu')
            ]
        );
    });

    Route::prefix('lancamento')->group(function () {
        Route::get('manual', function () {
            $dataEncerramento = EncerramentoPeriodoContabilService::ultimaData(
                session('DB_instit'),
                session('DB_anousu')
            );

            return view(
                'financeiro.contabilidade.lancamento-manual.manutencao',
                [
                    "instituicao" => session('DB_instit'),
                    "exercicio" => session('DB_anousu'),
                    "dataEncerramento" => $dataEncerramento,
                    "dataSistema" => date('Y-m-d', session('DB_datausu'))
                ]
            );
        });
    });
});

Route::prefix('relatorios')->group(function () {
    Route::prefix('balancetes')->group(function () {
        Route::prefix('receita')->group(function () {
            Route::get('recurso', function () {
                return view(
                    'financeiro.contabilidade.relatorios.balancetes.receita-recurso',
                    [
                        "exercicio" => session('DB_anousu'),
                        "dataSistema" => date('Y-m-d', session('DB_datausu'))
                    ]
                );
            });
        });

        Route::get('verificacao', function () {
            return view(
                'financeiro.contabilidade.relatorios.balancetes.verificacao',
                [
                    "exercicio" => session('DB_anousu'),
                    "dataSistema" => date('Y-m-d', session('DB_datausu'))
                ]
            );
        });
        Route::get('informacao-complementar', function () {
            return view(
                'financeiro.contabilidade.relatorios.balancetes.informacao-complementar',
                [
                    "exercicio" => session('DB_anousu'),
                    "dataSistema" => date('Y-m-d', session('DB_datausu'))
                ]
            );
        });
    });
    Route::prefix('conferencia')->group(function () {
        Route::get('superavit-por-recurso', 'RelatorioSuperavitController@viewPorRecurso');
    });
});

/**
 * Rotas do modulo comtabilidade/tce
 */
Route::prefix('tce')->group(function () {

    /**
     * Sigfis - TCE RJ
     */
    Route::prefix('rj/sigfis')->group(function () {
        Route::get('unidadegestora', function () {
            return view('financeiro.contabilidade.tce.rj.sigfis.unidadegestora');
        });
    });
});

Route::prefix('deliberacao')->group(function () {
    Route::prefix('285')->group(function () {
        Route::get('modelo-5', function () {
            return view(
                'financeiro.contabilidade.deliberacao.v285.modelo-5',
                [
                    "instituicao" => session('DB_instit'),
                    "exercicio" => session('DB_anousu'),
                    "dataSistema" => date('Y-m-d', session('DB_datausu'))
                ]);
        });
    });
});
