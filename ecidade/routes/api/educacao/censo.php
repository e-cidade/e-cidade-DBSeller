<?php
// php5.6 artisan route:list --path=v4/api/educacao/censo
Route::prefix('tabelas-censo')->group(function () {
    Route::get('unidades-curriculares', "TabelasCensoController@getUnidadesCurriculares");
    Route::post('unidades-curriculares/salvar', "TabelasCensoController@salvarUnidadeCurricular");
    Route::delete('unidades-curriculares/{codigo}/excluir', "TabelasCensoController@excluirUnidadeCurricular");

    Route::get('areas-pos-graduacao', "TabelasCensoController@getAreasPosGraduacao");
    Route::get('tipos-pos-graduacao', "TabelasCensoController@getTiposPosGraduacao");
    Route::get('cursos-profissionalizantes', "TabelasCensoController@getCursosProfissionalizantes");
    Route::get('cursos-profissionalizantes/{codigo}', "TabelasCensoController@getCursoProfissionalizantesByCodigo");
});
