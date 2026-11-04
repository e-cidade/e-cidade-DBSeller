<?php

Route::prefix('cadastros')->group(function () {
    Route::get('grupos-liberacao', function () {
        return view(
            'saude.laboratorio.cadastros.grupos-liberacao',
            [
                'usuario' => db_getsession("DB_id_usuario"),
                'departamento' => db_getsession("DB_coddepto")
            ]
        );
    });
});
