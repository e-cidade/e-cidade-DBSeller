<?php

use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;

Route::get('procedimentos/fundeb', function (Request $request) {
        return view('recursoshumanos.pessoal.procedimentos.fundeb');
});

Route::get('procedimentos/fundeb-processamento', function (Request $request) {
    return view('recursoshumanos.pessoal.procedimentos.fundeb-processamento');
});

// web/recursos-humanos/pessoal/
Route::get('rotinas_mensais/liberacao_contracheque_online', function () {
    return view('recursos-humanos.pessoal.contracheque.liberacaoonline');
});
Route::get('relatorios/tipo_guia_previdencia', function () {
    return view('recursos-humanos.pessoal.relatorios.tipo-guia-previdencia');
});

Route::prefix('ajuda-custo')->group(function () {
    Route::get('configuracao', function () {
        return view('recursos-humanos.pessoal.ajudacusto.configuracao');
    });
    Route::get('lancamento', function () {
        return view('recursos-humanos.pessoal.ajudacusto.lancamento');
    });
    Route::get('relatorio', function () {
        return view('recursos-humanos.pessoal.ajudacusto.relatorio');
    });
});

Route::get('previdencia_complementar_servidor', function () {
    return view('recursos-humanos.pessoal.servidorprevidenciacomplementar');
});

Route::get('historico-rubrica', function () {
    return view('recursos-humanos.pessoal.rubricas.historicorubrica');
});

Route::get('financeiro/previdencia', function () {
    return view('recursos-humanos.pessoal.relatorios.relatoriosfinanceiros.encargos-tributarios-mensais');
});

Route::get("contra-cheque-app-falhas/{batch_id}", function ($batch_id) {
    return view(
        'recursos-humanos.pessoal.contracheque.emissaoapp.falha',
        compact("batch_id")
    );
});
