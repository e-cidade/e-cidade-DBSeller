<?php

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use \App\Domain\Saude\Ambulatorial\Models\Cid;

Route::middleware(["auth:api"])->group(function () {
    Route::prefix('')->group(function () {
        Route::get('parametros', 'ParametrosController@get');
    });

    Route::prefix('cgs')->group(function () {
        Route::get('{cgs}', 'CgsController@get');
        Route::get('familiamicroarea/{cgs}', 'CgsController@getFamiliamicroarea');
    });

    Route::prefix('consulta')->group(function () {
        Route::get('problemas', 'ProblemasController@getAll');
        Route::get('problemaspaciente/by-paciente/{id}', 'ProblemasPacienteController@getByPaciente');
        Route::get('microarea', 'FamiliaMicroareaController@getMicroareas');
        Route::get('unidade/{id}', 'UnidadesController@get');
        Route::get('classificacoesderisco', 'ClassificacoesRiscoController@getClassificacoes');
        Route::get('setoresambulatoriais', 'SetoresAmbulatoriaisController@getSetores');
        Route::get('motivosdeatendimento', 'MotivosAtendimentoController@getMotivosAtendimento');
        Route::get('especialidadeprofissionais/ativas', 'EspecialidadeProfissionalController@getAtivas');
        Route::prefix('statuspaciente')->group(function () {
            Route::post('', 'StatusPacienteController@buscaDados');
            Route::post('buscapacienteprontuario', 'StatusPacienteController@buscaPacienteProntuario');
        });
        Route::prefix('unidades-encaminhadoras')->group(function () {   
            Route::post('', 'UnidadesEncaminhadorasController@get');
            Route::post('unidade', 'UnidadesEncaminhadorasController@getByUnidade');
        });  
        Route::prefix('unidades-origem')->group(function () {   
            Route::post('', 'UnidadesOrigemController@get');
            Route::post('unidade', 'UnidadesOrigemController@getByUnidade');
        });                
    });

    Route::prefix('procedimento')->group(function () {
        Route::prefix('acompanhamento-acs')->group(function () {
            Route::get('{id}', 'AcompanhamentoAcsController@get');
            Route::get('paciente/{id}', 'AcompanhamentoAcsController@getByPaciente');
            Route::post('save', 'AcompanhamentoAcsController@save');
            Route::post('delete', 'AcompanhamentoAcsController@delete');
        });

        Route::prefix('problemaspaciente')->group(function () {
            Route::post('salvar', 'ProblemasPacienteController@salvar');
            Route::post('apagar', 'ProblemasPacienteController@apagar');
        });

        Route::prefix('unificacao-cgs')->group(function () {
            
            Route::post('listar-unificacoes', 'UnificacaoCgsController@listarUnificacoes');
            Route::post('listar-cgs', 'UnificacaoCgsController@listarCgs');
            Route::post('detalhar', 'UnificacaoCgsController@detalhar');
            Route::post('salvar', 'UnificacaoCgsController@salvar');
            Route::post('excluir', 'UnificacaoCgsController@excluir');
            Route::post('processar', 'UnificacaoCgsController@processar');
        });        
    });

    Route::prefix('relatorio')->group(function () {
        Route::post('acompanhamento-acs', 'AcompanhamentoAcsController@relatorio');
    });

    Route::prefix('cadastro')->group(function () {
        Route::prefix('unidades-encaminhadoras')->group(function () {   
            Route::post('save', 'UnidadesEncaminhadorasController@save');
            Route::post('delete', 'UnidadesEncaminhadorasController@delete');
        });        
    });   
    
    Route::prefix('cadastro')->group(function () {
        Route::prefix('unidades-origem')->group(function () {   
            Route::post('save', 'UnidadesOrigemController@save');
            Route::post('delete', 'UnidadesOrigemController@delete');
        });        
    });            
});

Route::middleware(['clientCredential'])->group(function () {
     Route::get('/cid', function (Request $request) {
         $cid = Cid::select(
             DB::raw("
                 sd70_i_codigo as codigo ,
                 CONCAT(sd70_c_cid,' - ',sd70_c_nome)  as descricao
            ")
         )->whereRaw(
             "CONCAT(sd70_c_cid,' - ',sd70_c_nome) ilike '%{$request->get("descricao")}%'"
         )->limit(50)->get();
         return new DBJsonResponse($cid);
     });

     Route::post('cgs/find-or-create', 'CgsController@findOrCreate');
});
