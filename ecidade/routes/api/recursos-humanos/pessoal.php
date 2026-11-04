<?php

// Jetom
Route::prefix('jetom')->namespace('Jetom')->group(function () {
    $pathJetom = "\App\Domain\RecursosHumanos\Pessoal\Controller\Jetom\\";
    $funcao = $pathJetom . "FuncaoController";
    $comissao = $pathJetom . "ComissaoController";
    $comissaoFuncao = $pathJetom . "ComissaoFuncaoController";
    $comissaoConfiguracao = $pathJetom . "ComissaoConfiguracaoController";
    $tipoSessao = $pathJetom . "TipoSessaoController";
    $comissaoServidor = $pathJetom . "ComissaoServidorController";
    $comissaoSessao = $pathJetom . "SessaoController";
    $comissaoTipoSessao = $pathJetom . "ComissaoTipoSessaoController";
    $permissaoComissao = $pathJetom . "PermissaoComissaoController";
    $importarArquivoPonto = $pathJetom . "ArquivoPontoController";

    Route::get('tiposessao', $tipoSessao . "@index");
    Route::get('tiposessao/all', $tipoSessao . "@all");

    Route::get('funcao', $funcao . "@index");
    Route::get('funcao/all', $funcao . "@all");
    Route::get('funcao/show', $funcao . "@show");
    Route::get('funcao/find', $funcao . "@find");
    Route::post('funcao/', $funcao . "@store");
    Route::post('funcao/alterar', $funcao . "@edit");
    Route::post('funcao/deletar', $funcao . "@delete");

    Route::get('comissao', $comissao . "@index");
    Route::get('comissao/show', $comissao . "@show");
    Route::get('comissao/getComissao', $comissao . "@getComissao");
    Route::post('comissao/save', $comissao . "@store");
    Route::post('comissao/update', $comissao . "@update");
    Route::post('comissao/delete', $comissao . "@delete");

    Route::get('comissao/funcao', $comissaoFuncao . "@index");
    Route::post('comissao/funcao', $comissaoFuncao . "@store");
    Route::get('comissao/funcao/show', $comissaoFuncao . "@show");
    Route::post('comissao/funcao/update', $comissaoFuncao . "@update");
    Route::post('comissao/funcao/delete', $comissaoFuncao . "@destroy");

    Route::get('comissao/config', $comissaoConfiguracao . "@index");
    Route::post('comissao/config', $comissaoConfiguracao . "@store");
    Route::get('comissao/config/show', $comissaoConfiguracao . "@show");
    Route::post('comissao/config/update', $comissaoConfiguracao . "@update");
    Route::post('comissao/config/delete', $comissaoConfiguracao . "@destroy");

    Route::get( 'comissao/servidor', $comissaoServidor . "@index");
    Route::post('comissao/servidor', $comissaoServidor . "@store");
    Route::get( 'comissao/servidor/show', $comissaoServidor . "@show");
    Route::post('comissao/servidor/update', $comissaoServidor . "@update");
    Route::post('comissao/servidor/delete', $comissaoServidor . "@destroy");

    Route::get( 'comissao/tiposessao', $comissaoTipoSessao . "@index");
    Route::post('comissao/tiposessao', $comissaoTipoSessao . "@store");
    Route::get( 'comissao/tiposessao/show', $comissaoTipoSessao . "@show");
    Route::post('comissao/tiposessao/update', $comissaoTipoSessao . "@update");
    Route::post('comissao/tiposessao/delete', $comissaoTipoSessao . "@destroy");

    Route::resource('comissao/permissao', $permissaoComissao, [
        'only' => [ 'store', 'index', 'show' ],
        'parameters' => [ 'permissao' => 'id' ]
    ]);

    Route::post('comissao/permissao/update', $permissaoComissao . "@update");
    Route::post('comissao/permissao/delete', $permissaoComissao . "@destroy");

    Route::post('sessao/processar', "{$comissaoSessao}@processar");
    Route::resource('sessao', $comissaoSessao, [
        'only' => [ 'store', 'index', 'show', 'destroy' ]
    ]);

    Route::post('importar/arquivoponto', "{$importarArquivoPonto}@importar");
    Route::resource('importar', $importarArquivoPonto, [
        'only' => [ 'store', 'index', 'show', 'destroy' ]
    ]);
});

// Fundeb
Route::prefix('fundeb')->namespace('Fundeb')->group(function () {
    $pathFundeb = "\App\Domain\RecursosHumanos\Pessoal\Controller\Fundeb\\";
    $rhcargosfundeb = $pathFundeb . "RhCargosFundebController";
    $rhparametrosfundeb = $pathFundeb . "RhParametrosFundebController";
    $rhcalculofundeb = $pathFundeb . "RhCalculoFundebController";
    $rhprocessamentofundeb = $pathFundeb . "RhProcessamentoFundebController";

    Route::post('rhcargosfundeb/salvar', $rhcargosfundeb . "@save");
    Route::post('rhcargosfundeb/find', $rhcargosfundeb . "@show");
    Route::post('rhcargosfundeb/remover', $rhcargosfundeb . "@delete");

    Route::post('rhparametrosfundeb/salvar', $rhparametrosfundeb . "@save");
    Route::post('rhparametrosfundeb/showCargos', $rhparametrosfundeb . "@showCargos");
    Route::post('rhparametrosfundeb/showFuncoes', $rhparametrosfundeb . "@showFuncoes");
    Route::post('rhparametrosfundeb/showLocais', $rhparametrosfundeb . "@showLocais");
    Route::post('rhparametrosfundeb/showAssentamentos', $rhparametrosfundeb . "@showAssentamentos");
    Route::post('rhparametrosfundeb/showRubricaAbatimento', $rhparametrosfundeb . "@showRubricaAbatimento");

    Route::delete('rhparametrosfundeb/removerCargo/{id}', $rhparametrosfundeb . "@deleteCargo");
    Route::delete('rhparametrosfundeb/removerFuncao/{id}', $rhparametrosfundeb . "@deleteFuncao");
    Route::delete('rhparametrosfundeb/removerLocal/{local}', $rhparametrosfundeb . "@deleteLocal");
    Route::delete('rhparametrosfundeb/removerAssentamento/{assentamento}', $rhparametrosfundeb . "@deleteAssentamento");
    Route::delete('rhparametrosfundeb/removerRubricaAbatimento/{rubrica}', $rhparametrosfundeb . "@deleteRubricaAbatimento");

    Route::post('rhcalculofundeb/salvar', $rhcalculofundeb . "@save");

    Route::post('rhprocessamentofundeb/salvar', $rhprocessamentofundeb . "@save");
});

Route::prefix('contra-cheques')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $contraChequeController = "{$path}ContraChequesController";
    Route::post('processar-competencia', "{$contraChequeController}@processarEmissao");
    Route::post('emitidos', "{$contraChequeController}@buscarEmitidos");
    Route::post('cancelar-emissao', "{$contraChequeController}@cancelarEmissao");
    Route::get('falhas/{batch_id}', "{$contraChequeController}@falhas");
    Route::post('reprocessar/falha', "{$contraChequeController}@reprocessarFalha");

    /**
     * Rotas destinadas a configuracao  de liberacao de contracheques
     */
    Route::prefix('liberacaoonline')->group(function () {
        $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\Contracheque\\";
        $liberacaoContrachequeController = "{$path}LiberacaoContrachequeController";
        Route::get('getConfig', "{$liberacaoContrachequeController}@getConfig");
        Route::post('saveConfig', "{$liberacaoContrachequeController}@saveConfig");
    });
});

Route::prefix('rhfuncao')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhFuncaoController = "{$path}RhFuncaoController";
    Route::post('list', "{$RhFuncaoController}@listCargo");
    Route::get('list', "{$RhFuncaoController}@listCargo");
    Route::get('list-regime', "{$RhFuncaoController}@listRegime");
});

Route::prefix('rhcargo')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhCargoController = "{$path}RhCargoController";
    Route::post('list', "{$RhCargoController}@listFuncao");
});

Route::prefix('rhlocaltrab')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhLocalTrabController = "{$path}RhLocalTrabController";
    Route::post('list', "{$RhLocalTrabController}@listLocal");
});

Route::prefix('rhrubricas')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhRubricasController = "{$path}RhRubricasController";
    Route::post('list', "{$RhRubricasController}@listRubrica");
    Route::post('gerar-observacoes', "{$RhRubricasController}@gerarObservacaoRubrica");
});

Route::prefix('rhlota')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhLotaController = "{$path}RhLotaController";
    Route::post('list', "{$RhLotaController}@listLotacao");
});

Route::prefix('selecao')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $controller = "{$path}SelecaoController";
    Route::get('list', "{$controller}@getList");
    Route::get('find/{codigo}', "{$controller}@find");
});

Route::prefix('lotacao')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $controller = "{$path}LotacaoController";

    Route::get('list', "{$controller}@getList");
    Route::get('list-active', "{$controller}@getListActive");
    Route::get('find/{codigo}', "{$controller}@find");
});


Route::prefix('tabelaprevidencia')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $controller = "{$path}TabelaPrevidenciaController";
    Route::get('list', "{$controller}@getList");
});

Route::prefix('relatorios')->group(function () {
    Route::prefix('tipoguiaprevidencia')->group(function () {
        $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
        $controller = "{$path}TipoGuiaPrevidenciaController";
        Route::post('emitir', "{$controller}@emitir");
    });
    Route::prefix('previdenciaencargostributarios')->group(function () {
        $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
        $controller = "{$path}PrevidenciaEncargosTributariosController";
        Route::post('emitir', "{$controller}@emitir");
    });
});


Route::prefix('rhdepend')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $rhDependeController = "{$path}RhDependController";
    Route::post('list', "{$rhDependeController}@listDependente");
    Route::get('dependentes/{servidor}', "{$rhDependeController}@getDependenteByServidor");
});

Route::prefix('ajudacusto')->group(function () {
    $controller = "\App\Domain\RecursosHumanos\Pessoal\Controller\AjudaCusto\AjudaCustoController";
    Route::post('config-salvar', "{$controller}@salvarConfig");
    Route::get('config-buscar/{instituicao}', "{$controller}@buscarConfig");
    Route::get('lancamentos/{id}', "{$controller}@lancamentos");
    Route::resource('lancamento', "{$controller}");
    Route::post('processamento', "{$controller}@processar");
    Route::post('relatorio', "{$controller}@relatorio");
});

Route::prefix('rhpessoal')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $RhFuncaoController = "{$path}RhPessoalController";
    Route::post('list', "{$RhFuncaoController}@listMatriculas");
});

Route::prefix('previdenciacomplementar')->group(function () {
    $path = "\App\Domain\RecursosHumanos\Pessoal\Controller\\";
    $previdenciaComplementar = "{$path}PrevidenciaComplementarController";
    Route::post('busca', "{$previdenciaComplementar}@get");
    Route::post('salvar', "{$previdenciaComplementar}@save");
    Route::post('excluir', "{$previdenciaComplementar}@delete");
});
