<?php
// php5.6 artisan route:list --path=web/financeiro/tesouraria/
Route::prefix('slip')->group(function () {
    Route::prefix('gerar')->group(function () {
        Route::get('cobertura-recurso-extra', function () {
            return view('financeiro.tesouraria.slips.gerar.cobertura-recurso-extra');
        });
    });
});


//web/financeiro/tesouraria/decendio/
Route::prefix('decendio')->group(function () {
    /**
     * Configura nas receitas quais os tipos de desdobramento que elas executaram
     */
    Route::get('receita', function () {
        return view('financeiro.tesouraria.decendio.receita');
    });

    /**
     * Esse menu é para clientes que ainda usam desdobramento.
     * Serve para configurar as receitas por tipo de desdobramento que elas executaram
     */
    Route::get('receita-por-desdobramento', function () {
        return view('financeiro.tesouraria.decendio.receita-por-desdobramento');
    });
    /**
     *
     * configura os percentuais dos tipos de desdobramento e os recursos que cada um vai ter
     */
    Route::get('percentuais', function () {
        return view('financeiro.tesouraria.decendio.percentuais');
    });
    /**
     *
     */
    Route::get('gerar-slips', function () {
        return view(
            'financeiro.tesouraria.decendio.gerar-slips',
            [
                "exercicio" => session('DB_anousu'),
                "instituicao" => session('DB_instit'),
                "dataSistema" => date('Y-m-d', session('DB_datausu'))
            ]
        );
    });

    Route::get('relatorio', function () {
        return view(
            'financeiro.tesouraria.decendio.relatorio',
            [
                "exercicio" => session('DB_anousu'),
                "instituicao" => session('DB_instit'),
                "dataSistema" => date('Y-m-d', session('DB_datausu'))
            ]
        );
    });
});
