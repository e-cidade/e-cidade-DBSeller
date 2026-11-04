<?php

use App\Domain\Tributario\Cadastro\Controllers\EnderecoController;
use App\Domain\Tributario\Cadastro\Controllers\CadastroController;

Route::middleware(["api", "clientCredential"])->group(function () {
  $path = "\App\Domain\Tributario\Cadastro\Controllers\\";

  Route::get("dados-matricula", "{$path}CadastroController@dadosImovel");
  Route::get("setor-registro-imoveis", "{$path}CadastroController@getSetorRegImoveis");
  Route::get("localidade-rural", "{$path}CadastroController@getLocalidadeRural");
  Route::get("bairros", "{$path}CadastroController@getBairros");
  Route::get("logradouros", "{$path}CadastroController@getLogradouros");
  Route::post("buscar-imoveis", "{$path}CadastroController@getListaImoveis");
  Route::get("buscar-labels-imoveis", "{$path}CadastroController@getLabelsListaImoveis");
  Route::get("endereco-base-cliente", "{$path}CadastroController@getEnderecoBaseCliente");
  Route::get("bairro-base-cliente", "{$path}CadastroController@getBairroBaseCliente");
        Route::get("buscar-condominios", "{$path}CadastroController@getMatriculaCondominios");
        Route::get("buscar-logradouro", "{$path}CadastroController@getMatriculaLogradouros");
        Route::get("buscar-loteamento", "{$path}CadastroController@getMatriculaLoteamento");

  /**
   * Rotas para endereços dos forms do processo-eletronico
   */
  Route::get("paises", "\\" . EnderecoController::class . "@getPaises");
  Route::get("estados", "\\" . EnderecoController::class . "@getEstados");
  Route::get("cidades", "\\" . EnderecoController::class . "@getCidades");
  Route::get("endereco-por-cep", "\\" . EnderecoController::class . "@getEnderecoLocalidadeCep");
  Route::get("escritorios-contabeis", "\\" . CadastroController::class . "@getEscritoriosContabeis");
  Route::get("atividade", "\\" . CadastroController::class . "@getAtividadesPorTipo");
  Route::get("zonas", "\\" . CadastroController::class . "@getZonas");
});

Route::middleware(["api", "clientCredential"])->group(function () {
  $path = "\App\Domain\Tributario\Cadastro\Controllers\\";

  Route::get("dados-isencao/{id}", "{$path}CadastroIsencaoIptuController@getDadosIsencao");
});

Route::middleware(["auth:api"])->group(function () {

  Route::prefix('configuracao')->group(function () {

    $path = "\App\Domain\Tributario\Cadastro\Controllers\\";
    Route::get("listar", "{$path}ParametrosController@listar");
    Route::post("salvar", "{$path}ParametrosController@salvar");
  });
});

Route::middleware(["auth:api"])->group(function () {

  Route::prefix('construcao')->group(function () {

    $path = "\App\Domain\Tributario\Cadastro\Controllers\\";
    Route::post("lista-principal", "{$path}CadastroController@getValidIdPrinc");
  });
});


Route::middleware(['api', 'auth:api'])->group(function () {
  $path = "\App\Domain\Tributario\Cadastro\Controllers\Zoneamento\\";
  $controller = $path . "ZoneamentoController";

  Route::post("next-id-macrozona", $controller . "@getNextSeqMacrozona");
  Route::post("next-id-zona", $controller . "@getNextSeqZona");
  Route::post("save-macrozona", $controller . "@saveMacroZona");
  Route::post("get-macrozonas", $controller . "@getMacrozonasDialog");
  Route::post("get-zonas", $controller . "@getZonasDialog");
  Route::post("get-macrozonas-by-id", $controller . "@getMacrozonaById");
  Route::post("save-zona", $controller . "@saveZona");
  Route::post("update-zona", $controller . "@updateZona");
  Route::post("update-macrozona", $controller . "@updateMacrozona");
  
});