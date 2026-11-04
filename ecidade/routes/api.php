<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$namespace = '\App\Http\Controllers';

Route::prefix('client')->group(base_path('routes/api/client/client.php'));

Route::prefix('assistencia-social')->middleware(['auth:api'])->group(function () {
    Route::prefix('habitacao')->group(base_path('routes/api/assistencia-social/habitacao.php'));
    Route::prefix('social')->group(base_path('routes/api/assistencia-social/social.php'));
});

Route::prefix('cidadao')->middleware(['auth:api'])->group(function () {
    Route::prefix('prefeitura-online')->group(base_path('routes/api/cidadao/prefeitura-online.php'));
});

Route::prefix('configuracao/relatorios-legais')
    ->namespace('App\Domain\Configuracao\RelarorioLegal\\')
    ->group(base_path('routes/api/configuracao/relatoriolegal.php'));

Route::prefix('educacao')->middleware(['auth:api'])->group(function () {
    Route::prefix('alimentacao-escola')->group(base_path('routes/api/educacao/alimentacao-escola.php'));
    Route::prefix('biblioteca')->group(base_path('routes/api/educacao/biblioteca.php'));
    Route::prefix('escola')->group(base_path('routes/api/educacao/escola.php'));
    Route::prefix('transporte-escolar')->group(base_path('routes/api/educacao/transporte-escolar.php'));
});

Route::prefix('educacao')
    ->middleware(['auth:api'])
    ->namespace('\App\Domain\Educacao\Secretaria\Controllers')
    ->group(function () {
        Route::prefix('secretaria')->group(base_path('routes/api/educacao/secretaria.php'));
    });

Route::prefix('educacao')
    ->middleware(['auth:api'])
    ->namespace('\App\Domain\Educacao\Censo\Controllers')
    ->group(function () {
        Route::prefix('censo')->group(base_path('routes/api/educacao/censo.php'));
    });

Route::prefix('educacao')
    ->middleware(['auth:api'])
    ->namespace('\App\Domain\Educacao\MatriculaOnline\Controllers')
    ->middleware(\App\Domain\Educacao\MatriculaOnline\Middlewares\AuthMatriculaOnLineMiddleware::class)
    ->group(function () {
        Route::prefix('matricula-online')->group(base_path('routes/api/educacao/matricula-online.php'));
    });

Route::prefix('educacao')
    ->middleware(['api'])
    ->namespace('\App\Domain\Educacao\CentralMatriculas\Controllers')
    ->group(function () {
     Route::prefix('central-de-matriculas')->group(base_path('routes/api/educacao/central-de-matriculas.php'));
});

Route::prefix('educacao')
    ->middleware(['api'])
    ->namespace('\App\Domain\Educacao\CentralMatriculas\Controllers')
    ->group(function () {
     Route::prefix('files')->group(base_path('routes/api/educacao/files.php'));
});

Route::prefix('financeiro')
    ->middleware(['auth:api'])
    ->namespace('App\Domain\Financeiro\\')
    ->group(function () {
        Route::prefix('custos')->namespace('Custos\Controllers\\')->group(
            base_path('routes/api/financeiro/custos.php')
        );

        Route::prefix('contabilidade')->namespace('Contabilidade\Controllers\\')->group(
            base_path('routes/api/financeiro/contabilidade.php')
        );

        Route::prefix('orcamento')->namespace('Orcamento\Controllers\\')->group(
            base_path('routes/api/financeiro/orcamento.php')
        );

        Route::prefix('planejamento')->namespace('Planejamento\Controllers\\')->group(
            base_path('routes/api/financeiro/planejamento.php')
        );

        Route::prefix('tesouraria')->namespace('Tesouraria\Controller\\')->group(
            base_path('routes/api/financeiro/tesouraria.php')
        );

        Route::prefix('empenho')->namespace('Empenho\Controller\\')->group(
            base_path('routes/api/financeiro/empenho.php')
        );
});

Route::prefix('financeiro')
    ->middleware(['api'])
    ->namespace('App\Domain\Financeiro\\')
    ->group(function () {

        Route::prefix('contabilidade')->namespace('Contabilidade\Controllers\\')->group(
            base_path('routes/api/financeiro/contabilidade-public.php')
        );
});

Route::prefix('gestor')->middleware(['auth:api'])->group(base_path('routes/api/gestor/gestor.php'));

Route::prefix('integracoes')
    ->namespace('App\Domain\Integracoes\\')
    ->middleware(['auth:api'])->group(function () {
        Route::prefix('efd-reinf')->namespace('EFDReinf\Controllers')->group(
            base_path('routes/api/integracoes/efd-reinf.php')
        );
    });

Route::prefix('patrimonial')->namespace('\App\Domain\Patrimonial')->group(function () {
    Route::prefix('compras')->group(base_path('routes/api/patrimonial/compras.php'));

    Route::prefix('licitacoes')->group(base_path('routes/api/patrimonial/licitacoes.php'));
    Route::prefix('material')->namespace('Material\Controllers\\')->group(
        base_path('routes/api/patrimonial/material.php')
    );
    Route::prefix('patrimonio')->namespace('Patrimonio\Controllers\\')->group(
        base_path('routes/api/patrimonial/patrimonio.php')
    );
    Route::prefix('protocolo')->group(base_path('routes/api/patrimonial/protocolo.php'));
    Route::prefix('transito')->group(base_path('routes/api/patrimonial/transito.php'));
    Route::prefix('veiculos')->group(base_path('routes/api/patrimonial/veiculos.php'));
});

Route::prefix('patrimonial')
    ->middleware(['auth:api'])
    ->namespace('App\Domain\Patrimonial\\')
    ->group(function () {
        Route::prefix('licitacoes')->namespace('Licitacoes\Controllers\\')
            ->group(base_path('routes/api/patrimonial/licitacoes.php'));
        Route::prefix('contratos')->namespace('Contratos\Controllers\\')
            ->group(base_path('routes/api/patrimonial/contratos.php'));
        Route::prefix('pncp')->namespace('PNCP\Controllers\\')
            ->group(base_path('routes/api/patrimonial/pncp.php'));
    });

Route::prefix('recursos-humanos')->middleware(['auth:api'])->group(function () {
    Route::prefix('e-social')->group(base_path('routes/api/recursos-humanos/e-social.php'));
    Route::prefix('est-probatorio')->group(base_path('routes/api/recursos-humanos/est-probatorio.php'));
    Route::prefix('pessoal')->group(base_path('routes/api/recursos-humanos/pessoal.php'));
    Route::prefix('rh')->group(base_path('routes/api/recursos-humanos/rh.php'));
});

Route::prefix('saude')->namespace('App\Domain\Saude\\')->group(function () {
    Route::prefix('agendamento')->group(base_path('routes/api/saude/agendamento.php'));
    Route::prefix('ambulatorial')->namespace('Ambulatorial\Controllers\\')->group(
        base_path('routes/api/saude/ambulatorial.php')
    );
    Route::prefix('esf')
        ->namespace('ESF\Controllers\\')
        ->middleware(\App\Domain\Saude\ESF\Middlewares\AuthEsfMiddleware::class)
        ->group(base_path('routes/api/saude/esf.php'));
    Route::prefix('farmacia')->namespace('Farmacia\Controllers\\')->group(base_path('routes/api/saude/farmacia.php'));
    Route::prefix('hiperdia')->group(base_path('routes/api/saude/hiperdia.php'));
    Route::prefix('laboratorio')->namespace('Laboratorio\Controllers\\')->group(
        base_path('routes/api/saude/laboratorio.php')
    );
    Route::prefix('samu')->group(base_path('routes/api/saude/samu.php'));
    Route::prefix('tfd')->namespace('TFD\Controllers\\')->group(base_path('routes/api/saude/tfd.php'));
    Route::prefix('vacinas')->group(base_path('routes/api/saude/vacinas.php'));
});

Route::prefix('tributario')->namespace('App\Domain\Tributario\\')->group(function () {
    Route::prefix('agua')->group(base_path('routes/api/tributario/agua.php'));
    Route::prefix('arrecadacao')->namespace('Arrecadacao\Controllers\\')->group(
        base_path('routes/api/tributario/arrecadacao.php')
    );
    Route::prefix('cadastro')->group(base_path('routes/api/tributario/cadastro.php'));
    Route::prefix('cemiterio')->group(base_path('routes/api/tributario/cemiterio.php'));
    Route::prefix('contribuicao')->group(base_path('routes/api/tributario/contribuicao.php'));
    Route::prefix('diversos')->group(base_path('routes/api/tributario/diversos.php'));
    Route::prefix('divida-ativa')->group(base_path('routes/api/tributario/divida-ativa.php'));
    Route::prefix('fiscal')->group(base_path('routes/api/tributario/fiscal.php'));
    Route::prefix('fiscalizacao')
        ->middleware(['auth:api'])
        ->namespace('Fiscalizacao\Controllers\\')
        ->group(base_path('routes/api/tributario/fiscalizacao.php'));
    Route::prefix('inflatores')->group(base_path('routes/api/tributario/inflatores.php'));
    Route::prefix('issqn')->namespace('ISSQN\Controller\\')->group(base_path('routes/api/tributario/issqn.php'));
    Route::prefix('itbi')->group(base_path('routes/api/tributario/itbi.php'));
    Route::prefix('juridico')->group(base_path('routes/api/tributario/juridico.php'));
    Route::prefix('marcas')->group(base_path('routes/api/tributario/marcas.php'));
    Route::prefix('meio-ambiente')->group(base_path('routes/api/tributario/meio-ambiente.php'));
    Route::prefix('notificacoes')->group(base_path('routes/api/tributario/notificacoes.php'));
    Route::prefix('projetos')->group(base_path('routes/api/tributario/projetos.php'));
    Route::prefix('caixa')
        ->namespace('Caixa\Controllers\\')
        ->middleware(['auth:api'])
        ->group(base_path('routes/api/tributario/caixa.php'));
});

Route::prefix('/dbpref')
    ->middleware(['api'])
    ->namespace($namespace)
    ->group(base_path('routes/api/dbpref/dbpref.php'));
