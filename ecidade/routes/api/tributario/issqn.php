<?php
Route::middleware(['api', 'auth:api'])->prefix('alvaraeventos')->namespace('AlvaraEventos')->group(function () {

    $routePrefix = 'ordemservico';
    $path = "\App\Domain\Tributario\ISSQN\Controller\AlvaraEventos\\";
    $controller = $path . "OrdemServicoController";

    Route::get($routePrefix, $controller . "@index");
    Route::post($routePrefix, $controller . "@store");
    Route::get($routePrefix . '/show', $controller . "@show");
    Route::post($routePrefix . '/update', $controller . "@update");
    Route::post($routePrefix . '/delete', $controller . "@destroy");
    Route::post($routePrefix . '/processar', $controller . "@processar");
    Route::post($routePrefix . '/desprocessar', $controller . "@desprocessar");
    Route::get($routePrefix . '/getOrdemServico', $controller . "@getOrdemServico");

    $routePrefix = 'alvaraevento';
    $path = "\App\Domain\Tributario\ISSQN\Controller\AlvaraEventos\\";
    $controller = $path . "AlvaraEventoController";

    Route::get($routePrefix, $controller . "@index");
    Route::post($routePrefix, $controller . "@store");
    Route::get($routePrefix . '/show', $controller . "@show");
    Route::post($routePrefix . '/update', $controller . "@update");
    Route::post($routePrefix . '/delete', $controller . "@destroy");
    Route::get($routePrefix . '/getAlvaraEvento', $controller . "@getAlvaraEvento");

    $routePrefix = 'mensagempadrao';
    $path = "\App\Domain\Tributario\ISSQN\Controller\AlvaraEventos\\";
    $controller = $path . "MensagemPadraoController";

    Route::get($routePrefix, $controller . "@index");
});

Route::middleware(['api', 'auth:api'])->prefix('veiculos')->namespace('Veiculos')->group(function () {

    $routePrefix = 'veiculo';
    $path = "\App\Domain\Tributario\ISSQN\Controller\Veiculos\\";
    $controller = $path . "VeiculoController";

    Route::get($routePrefix, $controller . "@index");
    Route::post($routePrefix, $controller . "@store");
    Route::get($routePrefix . '/show', $controller . "@show");
    Route::post($routePrefix . '/update', $controller . "@update");
    Route::post($routePrefix . '/delete', $controller . "@destroy");
    Route::get($routePrefix . '/getVeiculo', $controller . "@getVeiculo");
    Route::post($routePrefix . '/desprocessar', $controller . "@desprocessar");


    $routePrefix = 'condutorauxiliar';
    $controller = $path . "CondutorAuxiliarController";

    Route::get($routePrefix, $controller . "@index");
    Route::post($routePrefix, $controller . "@store");
    Route::get($routePrefix . '/show', $controller . "@show");
    Route::post($routePrefix . '/update', $controller . "@update");
    Route::post($routePrefix . '/delete', $controller . "@destroy");
});

Route::middleware(['api'])->prefix('redesim')->group(function () {
    Route::middleware(['AuthRedesim'])->post("inclusao-inscricao", "RedesimController@incluirInscricao");
    Route::middleware(['auth:api'])->post("relatorio-inscricoes", "RedesimController@relatorioInscricoes");
    Route::middleware(['auth:api'])->get("inscricoes-cgm", "RedesimController@inscricoesCgm");
    Route::middleware(['auth:api'])->post("processar-evento-inclusao-inscricao", "RedesimController@processarEventoInclusaoInscricao");
    Route::middleware(['auth:api'])->post("processar-evento-alteracao-inscricao", "RedesimController@processarEventoAlteracaoInscricao");
    Route::middleware(['auth:api'])->post("enviar-resposta", "RedesimController@enviarResposta");
    Route::middleware(['auth:api'])->get("eventos", "RedesimController@eventos");
    Route::middleware(['auth:api'])->get("dados-estabelecimento", "RedesimController@dadosEstabelecimento");
});

Route::middleware(['api', 'auth:api'])->group(function () {
    $path = "\App\Domain\Tributario\ISSQN\Controller\InscricaoMunicipal\\";
    $controller = $path . "InscricaoMunicipalController";

    Route::post("buscar-inscricoes", $controller . "@getInscricoes");
    Route::get("buscar-labels-inscricoes", $controller . "@getLabelsInscricoes");
    Route::post("buscar-historico-inscricoes", $controller . "@getHistRiscoInscr");
    Route::get("buscar-setor-fiscal", $controller . "@getSetorFiscal");
    Route::post("buscar-iscr-salao-parceiro", $controller . "@getInscrDispSalaoParc");
    Route::post("buscar-parceiro", $controller . "@getDispParc");
});

Route::middleware(['api', 'auth:api'])->group(function () {
    $path = "\App\Domain\Tributario\ISSQN\Controller\SalaoParceiro\\";
    $controller = $path . "SalaoParceiroController";

    Route::post("save-salao-parceiro", $controller . "@saveSalaoParceiro");
    Route::post("get-salao-parceiro", $controller . "@getSalao");
    Route::post("get-parceiro", $controller . "@getParceiros");
    Route::post("get-all", $controller . "@getAll");
    Route::post("update-parceiro", $controller . "@updateParceiro");
    Route::post("update-salao", $controller . "@updateSalao");
    Route::post("get-atividades-salao", $controller . "@getAtividadesSalao");
    Route::post("save-atividades-salao", $controller . "@saveAtividadesSalao");
    Route::post("get-inscr-dialog", $controller . "@getInscricoesDialog");
    
});


Route::middleware(['api', 'auth:api'])->prefix('simples-nacional')->group(function () {

    $path = "\App\Domain\Tributario\ISSQN\Controller\SimplesNacional\\";
    $controller = $path . "SimplesNacionalController";

    Route::post('/importa-optantes', $controller . "@importaOptantes");
    Route::post('/exporta-arquivo', $controller . "@exportaArquivo");
    Route::get('/busca-arquivos', $controller . "@buscaTodosArquivos");
    Route::get('/busca-arquivo', $controller . "@buscarArquivoPorId");
    Route::post("definir-parametros-atualizacoes", $controller . "@setFrequenciaAtualizacoes");
    Route::get("buscar-parametros-atualizacoes", $controller . "@getInfoAtualizacoes");
});
