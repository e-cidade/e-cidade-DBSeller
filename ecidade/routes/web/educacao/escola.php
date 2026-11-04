<?php
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;


// php5.6 artisan route:list --path=web/educacao/escola
Route::get('alunos/historico-escolar/{aluno}', function ($aluno) {
    return view("educacao.escola.relatorios.alunos.historico-escolar", ['aluno' => $aluno]);
});

Route::prefix('relatorios')->group(function () {
    Route::prefix('alunos')->group(function () {
        Route::get('historico-escolar', function (Request $request) {
            return view("educacao.escola.relatorios.alunos.historico-escolar");
        });

        Route::get('ficha-individual-aluno', function (Request $request) {
            return view("educacao.escola.relatorios.alunos.ficha-individual-aluno");
        });

        Route::get('atestado-frequencia', function (Request $request) {
            return view("educacao.escola.relatorios.alunos.atestado-frequencia");
        });

        Route::get('ficha-matricula', function (Request $request) {
           return view("educacao.escola.relatorios.alunos.ficha-matricula");
        });
    });

    Route::get('recursos-humanos/atividades', function (Request $request) {
        return view("educacao.escola.relatorios.recursos-humanos.atividades");
    });
});

Route::prefix('cadastros')->group(function () {
    Route::get('bases-curriculares', function (Request $request) {
        return view("educacao.escola.cadastros.bases-curriculares");
    });
});

Route::prefix('procedimentos')->group(function () {
    Route::prefix('diario-classe')->group(function () {
        Route::get('acompanhamento-alunos-pcd', function (Request $request) {
            return view("educacao.escola.procedimentos.diario-classe.acompanhamento-alunos-pcd");
        });
        Route::get('registro-aula', function (Request $request) {
            return view("educacao.escola.procedimentos.diario-classe.registro-aula");
        });
        Route::get('atualizador-aulas-dadas', function (Request $request) {
            return view("educacao.escola.procedimentos.diario-classe.atualizador-aulas-dadas");
        });
    });
});

