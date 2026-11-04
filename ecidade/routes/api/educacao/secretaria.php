<?php
// php5.6 artisan route:list --path=v4/api/educacao/secretaria

Route::prefix('parametros')->group(function () {
//    Route::get('globais',  "NotificacaoController@getParametrosGlobais"); // ainda nao existe
    Route::get('notificacoes', "ParamentrosNotificacaoController@show");
    Route::post('notificacoes', "ParamentrosNotificacaoController@update");

    $path = "\App\Domain\Educacao\Secretaria\Controllers\\";

    Route::get('notificacoes', $path  . "ParamentrosNotificacaoController@show");
    Route::post('notificacoes', $path  . "ParamentrosNotificacaoController@update");
    Route::get('bloqueio-notas/buscar', $path  . "ParamentrosBloqueioNotasController@show");
    Route::post('bloqueio-notas/salvar', $path  . "ParamentrosBloqueioNotasController@update");
});

Route::prefix('relatorios')->group(function () {
    Route::post('alunos/alunos-estrangeiros', "EmissoesRelatoriosController@emitirAlunosEstrangeiros");
    Route::get('/funcoes-atividades', "EmissoesRelatoriosController@emitirAtividades");
    Route::post('funcionarios-ativos-escola', "RelatorioFuncionariosController@funcionariosAtivosEscola");
});

Route::get('tipo-base/estrutura-curricular', "TiposBaseController@getEstruturasCurriculares");
Route::get('tipo-base/tipos-itinerario', "TiposBaseController@getTiposItinerarioFormativo");
Route::get(
    'tipo-base/composicoes-itinerario-inegrado',
    "TiposBaseController@getComposicaoItinerarioFormativoIntegrado"
);
Route::get(
    'tipo-base/tipos-curso-formacao-tec-prof',
    "TiposBaseController@getTiposCursoItinFormacaoTecnicaProfissional"
);
Route::get('tipo-base/buscarTodos', "TiposBaseController@getTiposBase");
Route::post('tipo-base/salvar', "TiposBaseController@salvar");
Route::post('tipo-base/excluir', "TiposBaseController@excluir");
Route::get('modelos-relatorio/getModelosHistorico', "ModelosRelatoriosController@getModelosHistorico");
Route::get('modelos-relatorio/getModelosRelatoriosPorTipo/{tipo}', "ModelosRelatoriosController@getModelosRelatoriosPorTipo");
Route::get('emissao-atas/getAlunosClassificacao/{calendario}/{turma}', "EmissaoAtasController@getAlunosClassificacao");
Route::get('emissao-atas/getAlunosReclassificacao/{escola}/{calendario}/{turma}', "EmissaoAtasController@getAlunosReclassificacao");
Route::get('emissao-atas/getAlunosAvanco/{calendario}/{turma}', "EmissaoAtasController@getAlunosAvanco");

Route::prefix('cursos')->group(function () {
    Route::get('/', "CursosController@index");
    Route::get('/{codigo}/disciplinas', "CursosController@getDisciplinasDoCurso");
    Route::get('/{codigo}/disciplinas/{disciplina}', "CursosController@getDisciplinaDoCurso");
    Route::post('salvar', "CursosController@salvar");
    Route::delete('/excluir/{codigo}', 'CursosController@excluir');
});

Route::prefix('ensinos')->group(function () {
    Route::get('/', "EnsinosController@index");
    Route::get('/{codigo}', "EnsinosController@getByCodigo");
});

Route::prefix('bases-curriculares')->group(function () {
    Route::get('/', "SecretariaController@getBases");
    Route::get('/{base}/etapas/{etapa}/disciplinas', "SecretariaController@getDisciplinasCursoBase");
});

Route::prefix('disciplinas')->group(function () {
    Route::get('/', "SecretariaController@getDisciplinas");
});

Route::prefix('bases-curriculares')->group(function () {
    Route::get('/', "SecretariaController@getBases");
    Route::get('/{base}/etapas/{etapa}/disciplinas', "SecretariaController@getDisciplinasCursoBase");
});

Route::prefix('tipos-insturmentos-avaliativos')->group(function () {
    Route::get('/', "TiposInstrumentosAvaliativosController@index");
    Route::get('/byEnsino/{ensino}', "TiposInstrumentosAvaliativosController@getByEnsino");
    Route::post('salvar', "TiposInstrumentosAvaliativosController@salvar");
    Route::delete('{codigo}/excluir', "TiposInstrumentosAvaliativosController@excluir");
});

Route::prefix('tabelas')->group(function () {
    Route::get('/funcoes-atividades', "SecretariaController@getAtividades");
});

Route::prefix('bloqueio-notas')->group(function () {
    $path = "\App\Domain\Educacao\Secretaria\Controllers\\";

    Route::get('listagem-excecoes', $path . "ParamentrosBloqueioNotasController@listagemExcecoes");
    Route::post('salvar-excecao', $path . "ParamentrosBloqueioNotasController@salvarExcecao");
    Route::post('encerrar-excecao/{codigo}', $path . "ParamentrosBloqueioNotasController@encerrarExcecao");
});
