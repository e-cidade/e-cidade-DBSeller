<?php

use Illuminate\Support\Facades\Route;
Route::prefix('concessaodireitos')
    ->namespace('App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Controllers\\')
    ->group(function ()  {
        Route::post('configuracao', "AssentConfigController@showAssentConfig");
        Route::post('gravarconfiguracao', "AssentConfigController@gravarAssentConfig");
        Route::get('configuracao', "AssentConfigController@allAssentConfig");
        Route::post('deleteconfiguracao', "AssentConfigController@deleteAssentConfig");

        Route::post('assentperc', "AssentPercController@showAssentPerc");
        Route::post('gravarassentperc', "AssentPercController@gravarAssentPerc");
        Route::post('deleteassentperc', "AssentPercController@deleteAssentPerc");

        Route::post('assentform', "AssentFormController@mostrarAssentForm");
        Route::post('gravarassentform', "AssentFormController@gravarAssentForm");
        Route::post('deleteassentform', "AssentFormController@deleteAssentForm");

        Route::post('assentconcedeconfig', "AssentConcedeConfigController@showAssentAssentConcedeConfig");
        Route::post('gravarassentconcedeconfig', "AssentConcedeConfigController@gravarAssentConcedeConfig");
        Route::post(
            'deleteassentconcedeconfig',
            "AssentConcedeConfigController@deleteAssentAssentConcedeConfig"
        );

        Route::post('concessaocalculo', "ConcessaoCalculoController@concesaomatricula");

        Route::post('concessaocalculolog', "ConcessaoCalculoLogController@show");

        Route::post('gravaconcessaoassent', "ConcessaoAsentController@store");

        Route::post('processar', "ProcessamentoConcessaoController@store");
        Route::post('assentamentos', "ProcessamentoConcessaoController@show");
        Route::post('getprocessamento', "ProcessamentoConcessaoController@getprocessamento");
        Route::post('stopsprocessamento', "ProcessamentoConcessaoController@pararprocessamento");
    }
);

// Fundeb
Route::prefix('tipoasse')->group(function () {
    $path = "\App\Domain\RecursosHumanos\RH\ConcessaoDireitos\Controllers\\";
    $TipoAsseController = "{$path}TipoAsseController";
    Route::post('list', "{$TipoAsseController}@listAssentamento");
});

/**
 * Relatorios
 */
Route::prefix('relatorios')
    ->namespace('App\Domain\RecursosHumanos\RH\Relatorios\Controllers\\')
    ->group(function () {
        /**
         * Rotas destinadas a configuracao do relatorio
         * de assentamento por periodo
         */
        Route::prefix('configassentperiodo')->group(function () {
            Route::get('getConfig', 'ConfigAssentPeriodoController@getConfig');
            Route::post('saveConfig', 'ConfigAssentPeriodoController@saveConfig');
            Route::get('getAssentamentos', 'ConfigAssentPeriodoController@getAssentamentos');
        });
        /**
         * Rotas destinadas a geracao da certidao do tempo de contribuicao
         */
        Route::prefix('certidaotempocontribuicao')->group(function () {
            Route::post('gerar', 'CertidaoTempoContribuicaoController@gerar');
        });

    }
);

