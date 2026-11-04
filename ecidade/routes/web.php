<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::prefix('assistencia-social')->group(function () {
    Route::prefix('habitacao')->group(base_path('routes/web/assistencia-social/habitacao.php'));
    Route::prefix('social')->group(base_path('routes/web/assistencia-social/social.php'));
});

Route::prefix('cidadao')->group(function () {
    Route::prefix('prefeitura-online')->group(base_path('routes/web/cidadao/prefeitura-online.php'));
});

Route::prefix('configuracao')->group(base_path('routes/web/configuracao/configuracao.php'));

Route::prefix('educacao')->group(function () {
    Route::prefix('alimentacao-escolar')->group(base_path('routes/web/educacao/alimentacao-escolar.php'));
    Route::prefix('biblioteca')->group(base_path('routes/web/educacao/biblioteca.php'));
    Route::prefix('escola')->group(base_path('routes/web/educacao/escola.php'));
    Route::prefix('secretaria')->group(base_path('routes/web/educacao/secretaria.php'));
    Route::prefix('transporte-escolar')->group(base_path('routes/web/educacao/transporte-escolar.php'));
    Route::prefix('matricula-online')->group(base_path('routes/web/educacao/matricula-online.php'));
});

Route::prefix('financeiro')->group(function () {
    Route::prefix('contabilidade')
        ->namespace('App\Domain\Financeiro\Contabilidade\Controllers\\')
        ->group(base_path('routes/web/financeiro/contabilidade.php'));
    Route::prefix('custos')->group(base_path('routes/web/financeiro/custos.php'));
    Route::prefix('empenho')->group(base_path('routes/web/financeiro/empenho.php'));
    Route::prefix('orcamento')->group(base_path('routes/web/financeiro/orcamento.php'));
    Route::prefix('planejamento')->group(base_path('routes/web/financeiro/planejamento.php'));
    Route::prefix('tesouraria')->group(base_path('routes/web/financeiro/tesouraria.php'));
});

Route::prefix('gestor')->group(function () {
    Route::prefix('gestor')->group(base_path('routes/web/gestor/gestor.php'));
});

Route::prefix('integracoes')->group(function () {
    Route::prefix('efd-reinf')->group(base_path('routes/web/integracoes/efd-reinf.php'));
});

Route::prefix('patrimonial')->group(function () {
    Route::prefix('compras')->group(base_path('routes/web/patrimonial/compras.php'));
    Route::prefix('contratos')->group(base_path('routes/web/patrimonial/contratos.php'));
    Route::prefix('licitacoes')->group(base_path('routes/web/patrimonial/licitacoes.php'));
    Route::prefix('material')->group(base_path('routes/web/patrimonial/material.php'));
    Route::prefix('patrimonio')->group(base_path('routes/web/patrimonial/patrimonio.php'));
    Route::prefix('pncp')->group(base_path('routes/web/patrimonial/pncp.php'));
    Route::prefix('protocolo')->group(base_path('routes/web/patrimonial/protocolo.php'));
    Route::prefix('transito')->group(base_path('routes/web/patrimonial/transito.php'));
    Route::prefix('veiculos')->group(base_path('routes/web/patrimonial/veiculos.php'));
});

Route::prefix('saude')->group(function () {
    Route::prefix('agendamento')->group(base_path('routes/web/saude/agendamento.php'));
    Route::prefix('ambulatorial')->group(base_path('routes/web/saude/ambulatorial.php'));
    Route::prefix('farmacia')->group(base_path('routes/web/saude/farmacia.php'));
    Route::prefix('hiperdia')->group(base_path('routes/web/saude/hiperdia.php'));
    Route::prefix('laboratorio')->group(base_path('routes/web/saude/laboratorio.php'));
    Route::prefix('samu')->group(base_path('routes/web/saude/samu.php'));
    Route::prefix('tfd')->group(base_path('routes/web/saude/tfd.php'));
    Route::prefix('vacinas')->group(base_path('routes/web/saude/vacinas.php'));
});

Route::prefix('tributario')->group(function () {
    Route::prefix('agua')->group(base_path('routes/web/tributario/agua.php'));
    Route::prefix('arrecadacao')->group(base_path('routes/web/tributario/arrecadacao.php'));
    Route::prefix('cadastro')->group(base_path('routes/web/tributario/cadastro.php'));
    Route::prefix('cemiterio')->group(base_path('routes/web/tributario/cemiterio.php'));
    Route::prefix('contribuicao')->group(base_path('routes/web/tributario/contribuicao.php'));
    Route::prefix('diversos')->group(base_path('routes/web/tributario/diversos.php'));
    Route::prefix('divida-ativa')->group(base_path('routes/web/tributario/divida-ativa.php'));
    Route::prefix('fiscal')->group(base_path('routes/web/tributario/fiscal.php'));
    Route::prefix('fiscalizacao')->group(base_path('routes/web/tributario/fiscalizacao.php'));
    Route::prefix('inflatores')->group(base_path('routes/web/tributario/inflatores.php'));
    Route::prefix('issqn')->group(base_path('routes/web/tributario/issqn.php'));
    Route::prefix('itbi')->group(base_path('routes/web/tributario/itbi.php'));
    Route::prefix('juridico')->group(base_path('routes/web/tributario/juridico.php'));
    Route::prefix('marcas')->group(base_path('routes/web/tributario/marcas.php'));
    Route::prefix('meio-ambiente')->group(base_path('routes/web/tributario/meio-ambiente.php'));
    Route::prefix('notificacoes')->group(base_path('routes/web/tributario/notificacoes.php'));
    Route::prefix('projetos')->group(base_path('routes/web/tributario/projetos.php'));
});

Route::prefix('recursos-humanos')->group(function() {
    Route::prefix('esocial')->group(base_path('routes/web/recursos-humanos/e-social.php'));
    //Route::prefix('est-probatorio')->group(base_path('routes/web/recursos-humanos/est-probatorio.php'));
    // web/informacoes/complementares/eventoPeriodicos
    Route::prefix('pessoal')->group(base_path('routes/web/recursos-humanos/pessoal.php'));
    Route::prefix('rh')->group(base_path('routes/web/recursos-humanos/rh.php'));
    // Route::prefix('pessoal')->group(base_path('routes/web/recursos-humanos/pessoal.php'));
});
