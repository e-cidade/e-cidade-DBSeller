<?php
// php5.6 artisan route:list --path=v4/api/configuracao
use App\Domain\Configuracao\Job\Controller\JobsFailedController;
use App\Domain\Configuracao\Job\Controller\JobsController;
use App\Domain\Configuracao\Usuario\Controller\UsuarioController;

Route::middleware(['api', 'auth:api'])->group(function () {

    Route::prefix('instituicao')->namespace('Instituicao\Controller\\')->group(function () {
        Route::post('departamento-principal', 'InstituicaoController@configurarDepartamentoPrincipal');
        Route::get('/', "InstituicaoController@index");
        Route::get("/search", "InstituicaoController@search");
        Route::get('/usuarioLogado', 'InstituicaoController@usuarioLogado');
        Route::get('/{id}', "InstituicaoController@show");
        Route::get('/{instituicao}/departamentos', "InstituicaoController@searchDepartamentos");
        Route::get(
            "/{instituicao}/departamento/{departamento}",
            "InstituicaoController@searchDepartamentoUsuarios"
        );
    });

    Route::prefix('organograma')->namespace('Configuracao\Controllers\\')->group(function () {
        Route::get('{instit}', 'OrganogramaController@get');
        Route::get('{instit}/{departamento}', 'OrganogramaController@get');
        Route::post('filtrar', 'OrganogramaController@getByDepartamento');
        Route::post('salvar', 'OrganogramaController@salvar');
    });

    Route::prefix('campos')->namespace('Configuracao\Controllers\\')->group(function () {
        Route::get('{nomecam}', 'CamposController@getCampo');
    });

    Route::prefix("bancos")
        ->namespace('Banco\Controller\\')
        ->group(function () {
            Route::get('', 'BancoController@index');
        });

    Route::prefix("banco-pix")->namespace("Banco")->group(function () {
        $path           = "\App\Domain\Configuracao\Banco\Controller\\";
        $pathController = $path . "BancoPixController";

        Route::get("/listar", $pathController . "@listar");
        // Route::post("/salvar", $pathController . "@salvar");
        Route::post("/validar/{db90_codban?}", $pathController . "@validationData");
        Route::post("/atualizar/{db90_codban}", $pathController . "@atualizar");
        Route::get("/{db90_codban}", $pathController . "@pegarBancoPix");

        Route::delete("/excluir/{db90_codban}", $pathController . "@deletar");
    });

    Route::prefix('menu')->namespace('Menu\Controllers\\')->group(function () {
        Route::post('permissoes/saude/duplicar', 'PermissoesController@duplicarSaude');
        Route::get('modulos', 'MenusController@getModulos');
        Route::get('modulos/{modulo}/itens', 'MenusController@getItens');
        Route::post('modulos/{modulo}/itens/{item}', 'MenusController@salvar');
        Route::post('salvar-favorito', 'MenusController@salvarFavorito');
        Route::delete('deletar-favorito', 'MenusController@deletarFavorito');
        Route::get('buscar-favoritos', 'MenusController@buscarFavoritos');
    });

    Route::prefix('usuario')->namespace('Usuario\Controller\\')->group(function () {
        Route::get('', 'UsuarioController@get');
        Route::get('notificacoes', 'NotificacoesController@get');
        Route::put('notificacoes/{id}', 'NotificacoesController@markAsRead');
        Route::get('/search', "\\".UsuarioController::class."@search");
    });

    Route::prefix('gerador')->namespace('GeradorRelatorio\Controllers\\')->group(function () {
        Route::post('relatorios/novo', 'RelatorioController@begin');
        Route::post('relatorios/imprimir', 'ImprimirRelatorioController@handle');
        Route::post('relatorios/importar', 'ImportarExportarRelatorioController@importar');
        Route::get('relatorios/grupos', 'GrupoRelatorioController@index');
        Route::get('relatorios/tipos', 'TipoRelatorioController@index');
        Route::get('relatorios/{relatorio}/exportar', 'ImportarExportarRelatorioController@exportar');
        Route::post('relatorios/{relatorio}/template', 'RelatorioController@template');
        Route::resource('relatorios', 'RelatorioController');
        Route::post('executar-sql', 'ExecutaSqlController@handle');
    });

    Route::prefix('consultas')->namespace('Configuracao\Controllers\\')->group(function () {
        Route::get('postgres/views', 'PostgresViewsController@index');
    });
});

Route::prefix('usuario')->namespace('Usuario')->group(function () {
    $routePrefix = 'assinantes';
    $path = "\App\Domain\Configuracao\Usuario\Controller\\";
    $controller = $path . "AssinanteController";
    Route::get($routePrefix, $controller . "@index");
    Route::get('{idUsuario}/' . $routePrefix, $controller . "@getByIdUsuario");
});

Route::prefix('documentos-assinar')->namespace('DocumentosAssinatura')->group(function () {

    $path = "\App\Domain\Configuracao\DocumentosAssinatura\Controller\\";
    $controller = $path . "DocumentosAssinatura";

    Route::get('documento/{file_id}/assinado-por', $controller . "@getSignersSignedFromFile");
    Route::get('documento/{file_id}/assinantes', $controller . "@getSignersFromFile");
    Route::get('assinante', $controller . "@toSign");
    Route::get('{file_id}', $controller . "@getFile");
    Route::post('novo-documento', $controller . "@newSignFile");
    Route::post('atualizar-assinantes', $controller . "@updateSigners");
    Route::post('atualizar-assinado-por', $controller . "@updateSignedSigners");
});

Route::prefix('documento-assinado')->namespace('DocumentosAssinatura')->group(function () {

    $path = "\App\Domain\Configuracao\DocumentosAssinatura\Controller\\";
    $controller = $path . "DocumentosAssinatura";

    Route::post('', $controller . "@newSignFile");
});

Route::prefix('assinantes')->namespace('Assinantes')->group(function () {

    $path = "\App\Domain\Configuracao\Usuario\Controller\\";
    $controller = $path . "AssinanteController";

    // Route::post('',             $controller . "@newSignerPermission");
    // Route::put('',              $controller . "@updateSignerPermission");
    Route::post('', $controller . "@saveSignerPermission");
    Route::get('', $controller . "@getAllSignersPermission");
    Route::delete('{file_id}', $controller . "@deleteSignerPermission");
});

Route::prefix('entrega-continua')->middleware(['api'])->group(function () {
    $path = "\App\Domain\Configuracao\EntregaContinua\Controllers\\";
    Route::post('/migrate', $path . "MigrateController@migrate");
});


Route::prefix('plugin')->middleware(['api'])->group(function () {
    $path = "\App\Domain\Configuracao\Plugin\Controllers\\";
    Route::post('/getconfig', $path . "PluginController@getConfig");
});

Route::prefix("jobs")->name("jobs.")->group(function(){
    Route::apiResource("failed", "\\".JobsFailedController::class);
    Route::post("failed/retry-many", "\\".JobsFailedController::class."@retryMany");
    Route::post("failed/destroy-many", "\\".JobsFailedController::class."@destroyMany");
});

Route::resource("jobs", "\\" . JobsController::class);
Route::post("jobs/destroy-many", "\\" . JobsController::class . "@destroyMany");
