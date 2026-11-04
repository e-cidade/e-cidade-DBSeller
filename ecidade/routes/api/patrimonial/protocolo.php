<?php

use App\Domain\Patrimonial\Protocolo\Controller\DocumentoAndamentoController;
use App\Domain\Patrimonial\Protocolo\Controller\Processo\ProcessoController;
use Illuminate\Support\Facades\Route;
use App\Domain\Patrimonial\Protocolo\Controller\Processo\ProtocoloDocumentoController;
use App\Domain\Patrimonial\Protocolo\Controller\Processo\TipoDocumentoController;

Route::prefix('processo')->namespace('Processo')->middleware(["auth:api"])->group(function () {

    $routePrefix = 'processo';
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\Processo\\";
    $controller = $path . "ProcessoController";

    Route::get($routePrefix, $controller . "@index");

    $routePrefix = 'processodocumento';
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\Processo\\";
    $controller = $path . "ProcessoDocumentoController";

    Route::get($routePrefix, $controller . "@index");
    Route::post($routePrefix . '/download', $controller . "@download");
    Route::post($routePrefix . '/documentosPorProcesso', $controller . "@documentosPorProcesso");
    Route::post($routePrefix . '/documentosPorProcAndamInt', $controller . "@documentosPorProcAndamInt");
    Route::post($routePrefix . '/documentosPorNota/{codigo_nota}', $controller . "@documentosPorNota");

    /**
     * Andamento padrao
     */
    $routePrefixAndamentoPadrao = '{tipo_processo}/andamento-padrao/';
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\Processo\AndamentoPadrao\\";

    /**
     * Campos dinamicos do andamento padrao
     */
    $routePrefixCampoDinamicos = $routePrefixAndamentoPadrao . 'campos-dinamicos';
    $controller = $path . "CamposDinamicosController";
    Route::get($routePrefixCampoDinamicos, $controller . "@index");
    Route::delete($routePrefixCampoDinamicos, $controller . "@delete");

    $routePrefixCampoDinamicos .= '/{ordem}';
    Route::post($routePrefixCampoDinamicos, $controller . "@salvar");

    /**
     * Respostas dos Campos dinamicos do andamento padrao de um processo
     */
    $controller = $path . "CamposDinamicosRespostaController";
    $routePrefixCampoDinamicos = 'andamento-padrao/campos-dinamicos/resposta';
    Route::post($routePrefixCampoDinamicos, $controller . "@salvar");
    Route::get($routePrefixCampoDinamicos, $controller . "@getUltimaResposta");

    /**
     * Campos dinamicos do andamento padrao de um processo
     */
    $controller = $path . "CamposDinamicosController";
    $routePrefixCampoDinamicos = 'andamento-padrao/campos-dinamicos/{codigo_processo}';
    Route::get($routePrefixCampoDinamicos, $controller . "@getByProcessoDepto");
});

Route::group(["middleware" => ["api", "clientCredential"]], function () {
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\\";

    Route::get("cgm-cpf-cnpj", "{$path}CgmController@getCgmByCpfCnpj");
    Route::get("cgm", "{$path}CgmController@getByNumcgm");
    Route::post("pesquisa-geral-cgm", "{$path}CgmController@getByParams");
    Route::get("rotulos-pesquisa-geral-cgm", "{$path}CgmController@getRotulosPesquisaCgm");
    //Route::get("cgm/search", "{$path}CgmController@search");
    Route::get("rua-cep", "{$path}ProtocoloController@getRuaByCep");
    Route::get("rua-cep-municipio", "{$path}ProtocoloController@getRuaByCepMunicipio");
    Route::get("endereco-localidade-cep", "{$path}CgmController@getLocalidadeCep");
    Route::post("salvar-cgm", "{$path}CgmController@saveCgm");
    //Route::post("verifica-permissao-cgm", "{$path}CgmController@verificaPermissaoCgm");
    Route::get("verifica-cgm", "{$path}CgmController@verificaCgm");
});

Route::group(["middleware" => ["api", "auth:api"]], function () {
    /**
     * Atividades a serem executadas em um tipo de processo
     */
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\AtividadesExecucaoController";
    Route::get('/atividades-execucao', $path . "@index");
    Route::get('/atividades-execucao/tipo-processo/{tipoProcesso}', $path . "@TipoProcesso");
    Route::post('/atividades-execucao/excluir-vinculo', $path . "@excluirVinculo");
    Route::post('/atividades-execucao/vincular', $path . "@vincularAtividade");
    Route::post('/atividades-execucao/reordenar-vinculos', $path . "@reordenarVinculos");


    Route::post('/documentos/usuario', "\\" . DocumentoAndamentoController::class . "@index");
    Route::get('/documentos/usuario/total', "\\" . DocumentoAndamentoController::class . "@total");
    Route::post('/documentos/conferir', "\\" . DocumentoAndamentoController::class . "@conferir");
    Route::post('/documentos/arquivar', "\\" . DocumentoAndamentoController::class . "@arquivar");
    Route::post('/documentos/conferir-em-lote', "\\" . DocumentoAndamentoController::class . "@conferirLote");
    Route::post('/documentos/devolver', "\\" . DocumentoAndamentoController::class . "@devolver");
    Route::post('/documentos/atribuir-permissao', "\\" . DocumentoAndamentoController::class . "@atribuirPermissao");
    Route::post('/documentos/remover-permissao', "\\" . DocumentoAndamentoController::class . "@removerPermissao");
    Route::post(
        '/documentos/substituir-permissao',
        "\\" . DocumentoAndamentoController::class . "@substituirPermissao"
    );
    Route::get(
        '/documentos/atividade-em-execucao/usuario/{usuario_id?}',
        "\\" . DocumentoAndamentoController::class . "@usuarioAtividadesEmExcetuacao"
    );
    Route::post(
        '/documentos/atividades-executadas',
        "\\" . DocumentoAndamentoController::class . "@buscarAtividadesExecutadas"
    );
    Route::get(
        '/documentos/usuario/orgaos',
        "\\" . DocumentoAndamentoController::class . "@orgaosDoUsuario"
    );
    Route::post("documento/", "\\" . ProtocoloDocumentoController::class . "@store");
});

Route::group(["middleware" => ["api", "clientCredential"]], function () {
    $path = "\App\Domain\Patrimonial\Protocolo\Controller\DocumentoAndamentoController";
    Route::post('/documentos/buscar-por-identificador', $path . "@buscarPorIdentificador");
});

Route::prefix('processo')
    ->namespace('Protocolo\Controller\Processo\\')
    ->middleware(["api", "auth:api"])
    ->group(function () {
        Route::get("rotulos", "ProcessoController@rotulosPesquisa");
        Route::post("getprocesso", "ProcessoController@getProcesso");
        Route::post("getMensagem", "ProcessoController@getMensagens");
        Route::post("sendMensagem", "ProcessoController@sendMensagem");
        Route::post("criarDespacho", "ProcessoController@criarDespacho");
        Route::post("vincularDespachoMensagem", "ProcessoController@vincularDespachoMensagem");
        Route::post("getCgmByCpfCnpj", "ProcessoController@getCgmByCpfCnpj");
        Route::get("despachos-anteriores/{cod_proc}", "AndamentoProcessoController@despachosAnteriores");
        Route::get("departamentos-instituicao", "AndamentoProcessoController@buscarDepartamentosByInstituicao");
        Route::get("usuarios-departamento/{cod_departamento}", "AndamentoProcessoController@buscarUsuariosPorDepartamento");
        //Route::get("buscarProcessos", "ProcessoController@buscarProcessos");
        //Route::post("processar", "ProcessoController@processar")->middleware('permissaoMenu');
        Route::get("verifica-solicitacoes-assinatura/{id_andamento}", "AndamentoProcessoController@verificaSolicitacaoAssinatura");
        //Route::get("instituicoes", "AndamentoProcessoController@buscarInstituicoes");
        Route::post("transferir", "AndamentoProcessoController@transferir")->middleware('permissaoMenu:229289');
        Route::post("arquivar", "ProcessoController@arquivar")->middleware('permissaoMenu:229287');
        //Route::get("presenteDepartamento/{cod_proc}", "ProcessoController@verificaPresencaDepartamento");
        Route::get("search", "\\". ProcessoController::class."@pesquisaProcesso");
    }
);
