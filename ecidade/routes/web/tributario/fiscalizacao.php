<?php
// php artisan route:list --path=web/tributario/fiscalizacao
use Illuminate\Support\Facades\Route;

Route::prefix('vistoria')->group(function () {
    Route::get('demostrivo-calculo/{numcgm}', function ($numcgm) {
        return view(
            'tributario.fiscalizacao.vistoria.demostrativo-calculo',
            ['numcgm' => $numcgm]
        );
    });
});
