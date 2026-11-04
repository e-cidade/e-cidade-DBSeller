<?php
    // Autenticado como usuário
    Route::middleware(["api", "auth:api"])->group(function () {
        $path = "\App\Domain\Tributario\ITBI\Controllers\\";
    });

    // Autenticado como usuário ou aplicação
    Route::middleware(["api", "clientCredential"])->group(function () {
        $path = "\App\Domain\Tributario\ITBI\Controllers\\";

        Route::post("autenticacao", "{$path}AutenticacaoController@autenticar");
        Route::post("emissao", "{$path}EmissaoGuiaController@emitir");
        Route::get("tipos", "{$path}ItbiController@getTipos");
        Route::get("situacao", "{$path}ItbiController@getSituacao");
        Route::get("tipo-transacao", "{$path}ItbiController@getTipoTransacao");
        Route::get("forma-pagamento-tipo-transacao", "{$path}ItbiController@getFormaPagamentoTipoTransacao");
        Route::get("taxas-itbi", "{$path}ItbiController@getTaxasItbi");
        Route::get("rural/caracter", "{$path}ItbiController@getCaractImovelOrUtilImovel");
        Route::get("transmitente-principal", "{$path}ItbiController@getTransmitentePrincipal");
        Route::get("cartorios", "{$path}ItbiController@getCartorios");
        Route::get("benfeitorias", "{$path}ItbiController@getBenfeitoriasByMatric");
    });

    Route::prefix("benfeitoria")->middleware(["api", "clientCredential"])->group(function () {
        $path = "\App\Domain\Tributario\ITBI\Controllers\\";

        Route::get("tipo", "{$path}ItbiController@getTipoBenfeitoria");
        Route::get("especie", "{$path}ItbiController@getEspecieBenfeitoria");
        Route::get("padrao-construtivo", "{$path}ItbiController@getPadraoConstrutivoBenfeitoria");
    });

    Route::middleware(["auth:api"])->group(function () {

        Route::prefix('arquivoitbi')->group(function () {
 
          $path = "\App\Domain\Tributario\ITBI\Controllers\\";
          Route::post("processar-arquivo", "{$path}ItbiController@processarArquivo");
          Route::post("get-arquivos", "{$path}ItbiController@getArquivosItbi");
          Route::post("delete-arquivo", "{$path}ItbiController@deleteArquivo");
          Route::post("update-dados-arquivo", "{$path}ItbiController@updateDadosArquivo");
          Route::post("download-arquivo", "{$path}ItbiController@downloadArquivo");
        });
     });
