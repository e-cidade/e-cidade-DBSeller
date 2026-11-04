<?php
// php5.6 artisan route:list --path=v4/api/educacao/escola
Route::prefix('relatorios')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\Relatorios\\";

    Route::post('diarioclasse/turmasEspeciais', $path  . "DiarioClasse@turmasEspeciais");
    Route::post('diarioclasse/turmasEscolarizacao', $path  . "DiarioClasse@turmasEscolarizacao");

    Route::post('vacinacao', $path . "Vacinacao@emitir");
    Route::post('emitirRelatorioTurmasAee/', "{$path}HorariosTurma@emitirHorariosTurmaAee");
    $path = "\App\Domain\Educacao\Escola\Controllers\\";
    Route::post('/ata-resultados-finais-siga', "{$path}AtasResultadosFinaisController@ataResultadosFinaisSiga");
    Route::post('/espelho-periodo', "{$path}EspelhoNotasController@espelhoPeriodo");
    Route::post('/espelho-anual', "{$path}EspelhoNotasController@espelhoAnual");
});


Route::prefix('diario-classe')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\\";
    Route::prefix('registro-aula')->group(function () use($path) {
        Route::get('get-dados-regente/{escola}/{user}', "{$path}RegistroAulaController@getDadosRegente");
        Route::get('get-conteudos-regente-escola', "{$path}RegistroAulaController@getConteudosRegenteEscola");
        Route::post('salvar-conteudo', "{$path}RegistroAulaController@salvarConteudo");
        Route::post('salvar-habilidade-desenvolvida', "{$path}RegistroAulaController@salvarHabilidadeDesenvolvida");
        Route::delete('excluir-conteudo/{codigo}', "{$path}RegistroAulaController@excluirConteudo");
        Route::get('get-disciplinas-bncc/{regencia}', "{$path}RegistroAulaController@getDisciplinasBnccByRegencia");
        Route::get('get-habilidades-bncc', "{$path}RegistroAulaController@getHabilidadesBnccByRegenciaDisciplinaBncc");
        Route::get('get-habilidades-desenvolvidas', "{$path}RegistroAulaController@getHabilidadesDesenvolvidas");
        Route::post('update-conteudos-lote', "{$path}RegistroAulaController@updateConteudosLote");
    });
});
$path = "\App\Domain\Educacao\Escola\Controllers\\";

Route::prefix('turmas')->group(function () use ($path) {
    Route::get('/', "{$path}TurmasController@index");
    Route::post('/usuario', "{$path}TurmasController@turmasProfissional");
    Route::post('/{turma}/periodos-avaliacao', "{$path}TurmasController@getPeriodosAvaliacao");
    Route::post('/{codigoTurma}/regencias-por-usuario', "{$path}RegenciaController@regenciasProfissional");
});

Route::post('/notas-parciais/periodo', "{$path}AvaliacaoParcialController@buscarNotasPeriodo");

Route::prefix('regencia')->group(function () use ($path) {
    Route::post('/avaliacoes-periodo', "{$path}AvaliacaoParcialController@buscarAvaliacoesRegenciaPeriodo");
    Route::post('/adicionar-avaliacoes-periodo', "{$path}AvaliacaoParcialController@adicionarAvaliacoesRegenciaPeriodo");
    Route::post('/excluir-avaliacao', "{$path}AvaliacaoParcialController@excluirAvaliacaoParcial");
    Route::post('/turma/{turma}', "{$path}RegenciaController@porTurma");
});

Route::post('/aproveitamento/salvar', "{$path}AvaliacaoParcialController@salvarAproveitamentos");
Route::post('/avaliacaoparcial/salvar-formaobtencao', "{$path}RegenciaController@salvarFormaobtencao");
Route::post('/resultado-parcial/fechar-periodo', "{$path}AvaliacaoParcialController@encerrarPeriodoAluno");
Route::post('/resultado-parcial/fechar-todos', "{$path}AvaliacaoParcialController@encerrarPeriodos");
Route::post('/resultado-parcial/reabrir-todos', "{$path}AvaliacaoParcialController@reabrirPeriodos");
Route::post('/resultado-parcial/reabrir-periodo', "{$path}AvaliacaoParcialController@reabrirPeriodoAluno");

Route::get('turma/{codigo}', "{$path}TurmasController@buscar")->name("turma");
Route::get('turma/{codigo}/disciplinas', "{$path}TurmasController@buscarDisciplinas");
Route::get('turma/{codigo}/etapas/{etapa}/regencias', "{$path}TurmasController@getRegenciasPorEtapa");

Route::prefix('turmas')->group(function () use ($path) {
    Route::get('/', "{$path}TurmasController@index");
    Route::post('/usuario', "{$path}TurmasController@turmasProfissional");
    Route::post('/{turma}/periodos-avaliacao', "{$path}TurmasController@getPeriodosAvaliacao");
//    Route::post('/{codigoTurma}/regencias-por-usuario', "{$path}RegenciaController@regenciasProfissional");
});

Route::get('turmas-por-calendario/{codigoCalendario}', "{$path}TurmasController@buscarTurmasPorCalendario");
Route::get('turmas-por-escola-calendarios/{codigoEscola}/{calendarios}', "{$path}TurmasController@buscaTurmasPorEscolaCalendarios");
Route::get('turmasMulti/{codigoCalendario}', "{$path}TurmasController@buscarTurmasMultiEtapas");
Route::get('matriculas-por-turma/{turma}/{etapa}', "{$path}TurmasController@matriculasEtapa");
Route::get('vagas-por-turma/{turma}', "{$path}TurmasController@vagas");
Route::get('regencias-turmas/{turmaOrigem}/{turmaDestino}/{etapa}', "{$path}TurmasController@regenciasTurmas");
Route::post('procedimento/troca-de-turma', "{$path}TurmasController@trocarAlunosTurma");
Route::post('turmasEspeciais/', "{$path}TurmasController@buscarTurmasEspeciaisPorCalendarioEscola");
Route::post('turmasEspeciaisaee/', "{$path}TurmasController@buscarTurmasEspeciaisPorCalendarioEscolaAEE");
Route::get('tiposAtendimentos/', "{$path}TurmasController@getTiposAtendimentos");
Route::get('calendario/{escola}', "{$path}CalendarioController@buscarCalendariosAtivosEscola");

Route::get('calendarioaee/{escola}', "{$path}CalendarioController@buscarCalendariosAtivosEscolaAEE");
Route::get('periodos-por-calendario/{codigoCalendario}', "{$path}CalendarioController@buscarPeriodosCalendario");
Route::get('secretarios/{escola}', "{$path}EscolasController@getSecretarios");

Route::get('/vacinas', "{$path}VacinasController@index");
Route::get('/vacinas/{vacina}', "{$path}VacinasController@show");
Route::get('/profissionais-geral', $path . "ProfissionalController@profissionais");
Route::get('/profissional/{profissional}/vacinas', "{$path}ProfissionalController@vacinas");
Route::post('/profissional/{profissional}/salvar-vacinacao', $path  . "ProfissionalController@vacinar");
Route::post('/profissional/excluir-vacinacao/{profissionalVacinacao}', $path  . "ProfissionalController@deleteVacinacao");
Route::get('/alunos-matriculados/{escola}', $path . "AlunosController@buscarAlunosMatriculadosPorEscola");
Route::get('/periodos-calendario/{calendario}', "{$path}CalendarioController@periodosCalendario");

Route::prefix('transposicao')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\\";
    Route::post('buscar-candidatos', $path . "TransposicaoController@buscarCandidatos");
    Route::post('salvar-candidatos', $path . "TransposicaoController@salvarCandidatos");
    Route::post('relatorio', $path . "TransposicaoController@relatorio");
    Route::post('vagas-etapa', $path . "TransposicaoController@vagasPorEtapa");
    Route::post('vagas', $path . "TransposicaoController@salvarVagas");
    Route::post('criterios-ordenacao', $path . "CriteriosTransposicaoController@criteriosOrdenacao");
    Route::post('salvar-criterios-ordenacao', $path . "CriteriosTransposicaoController@salvarCriteriosOrdenacao");

    Route::post('/toggle', $path . "TransposicaoController@toggle");

    Route::post('/relatorio-inscritos', $path . "TransposicaoController@relatorioGeralInscritos");
    Route::post('/processar', $path . "TransposicaoController@processar");

    Route::post('/matriculas-designados', $path . "TransposicaoController@matriculasDesignados");
    Route::post('/aluno-designado', $path . "TransposicaoController@alunoDesignado");
    Route::post('/cancelar-designacao', $path . "TransposicaoController@cancelarDesignacao");
});

Route::get('/', "{$path}EscolasController@getEscolas");
Route::get('/{escola}', "{$path}EscolasController@getEscola");
Route::get('{escola}/calendario', "{$path}CalendarioController@buscarCalendariosAtivosEscola");
Route::get('{escola}/diretores', "{$path}EscolasController@getDiretores");
Route::get('{escola}/cursos', "{$path}CursoController@getCursosByEscola");
Route::get('{escola}/profissionais', "{$path}ProfissionalController@get");
Route::get('{escola}/regimes-matricula/{ensino}', "{$path}EscolasController@getRegimesMatriculaByEnsino");
Route::get('{escola}/regimes-matricula/{codigo}/', "{$path}EscolasController@getRegimeMatriculaByCodigo");
Route::get('{escola}/regimes-matricula/{codigo}/etapas', "{$path}EtapasController@getByRegimeMatricula");
Route::get('{escola}/etapas/{codigo}', "{$path}EtapasController@getByCodigo");
Route::get('{escola}/cursos/{codigo}', "{$path}CursoController@getCurso");
Route::get('{escola}/bases-curriculares', "{$path}BasesCurricularesController@index");
Route::delete('{escola}/bases-curriculares/{codigo}/excluir', "{$path}BasesCurricularesController@excluir");

Route::get('bases-curriculares/baseEscola/{codigo}/atos', "{$path}BasesCurricularesController@getBaseAtos");
Route::post('bases-curriculares/baseEscola/atos/salvar', "{$path}BasesCurricularesController@salvarBaseAto");
Route::delete('bases-curriculares/baseEscola/atos/{codigo}/excluir', "{$path}BasesCurricularesController@excluirBaseAto");
Route::post('bases-curriculares/salvarBaseContinuacao', "{$path}BasesCurricularesController@salvarBaseContinuacao");
Route::post('bases-curriculares/salvar', "{$path}BasesCurricularesController@salvar");
Route::get('bases-curriculares/{base}/etapas/{etapa}/disciplinas', "{$path}BasesCurricularesController@getDisciplinasPorBaseEtapa");
Route::post('bases-curriculares/disciplinas/salvar', "{$path}BasesCurricularesController@salvarDisciplinaBase");
Route::delete('bases-curriculares/disciplinas/{codigo}/excluir', "{$path}BasesCurricularesController@excluirDisciplinaBase");

Route::post('etapas/etapas-intervalo', "{$path}EtapasController@getEtapasNoIntervalo");
Route::get('parametros/{escola}', "{$path}ParametrosController@index");
Route::post('alunos/historicos-por-escola/', "{$path}AlunosController@getHistoricosAlunosByEscola");
Route::get('alunos/alunos-por-turma/{turma}', "{$path}AlunosController@getAlunosPorTurma");
Route::get('alunos/alunos-por-turma-especial/{turma}', "{$path}AlunosController@getAlunosPorTurmaEspecial");
Route::get('alunos/{aluno}', "{$path}AlunosController@getAluno");
Route::get('alunos/detalhes-aluno-especial/{aluno}', "{$path}AlunosController@getDetalhesAlunosEspecial");
Route::get('alunos/deficiencias/{aluno}', "{$path}AlunosController@getDeficienciasByAluno");
Route::post('alunos/historicos-alunos-transf-fora/', "{$path}AlunosController@getHistoricosAlunosTransferidosFora");
Route::put('alunos/atualiza-contato-responsavel', "{$path}AlunosController@atualizaContatoResponsavel");
Route::post('debug', function () {
    return response()->json(true);
})->name("debug");

Route::get(
    'alunoAtendimentosEspecial/atendimentos/{aluno}',
    "{$path}AlunoAtendimentoEspecialController@getAtendimentosByAluno"
);
Route::post(
    'alunoAtendimentosespecial/atendimentos',
    "{$path}AlunoAtendimentoEspecialController@getDatasAtendimentosByAlunos"
);
Route::get(
    'alunoAtendimentosespecial/profissionais-especializados/{turma}',
    "{$path}AlunoAtendimentoEspecialController@getProfissionaisEspecializadosPorTurma"
);
Route::delete(
    'alunoAtendimentosEspecial/excluir/{codigo}',
    "{$path}AlunoAtendimentoEspecialController@deleteAtendimentosByCodigo"
);
Route::post('alunoAtendimentosEspecial/save', "{$path}AlunoAtendimentoEspecialController@persistAtendimentos");
Route::post('alunoAtendimentosEspecial/atualizar', "{$path}AlunoAtendimentoEspecialController@updateAtendimentos");

Route::get(
    'alunoAtendimentosEspecial/recursos/{aluno}',
    "{$path}AlunoAtendimentoEspecialController@getRecursosByAluno"
);

Route::get(
    'alunoAtendimentosEspecial/busca-recursos/',
    "{$path}AlunoAtendimentoEspecialController@getRecursos"
);
Route::delete(
    'alunoAtendimentosEspecial/exclui-recursos/{recurso}',
    "{$path}AlunoAtendimentoEspecialController@deleteRecursoByCodigo"
);
Route::post('alunoAtendimentosEspecial/salva-recursos', "{$path}AlunoAtendimentoEspecialController@persistRecurso");
Route::post('alunoAtendimentosEspecial/edita-recursos', "{$path}AlunoAtendimentoEspecialController@updateRecurso");

Route::prefix('recursos-humanos')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\\";
    Route::get('profissionais-com-superior/{escola}/{ativos}', $path . "ProfissionalController@getProfissionaisComSuperior");
    Route::get('profissionais/{escola}', $path . "ProfissionalController@get");
    Route::get('profissionais/{escola}/{cgm}', $path . "ProfissionalController@getByCgm");
    Route::get('profissionais/{escola}/{cgm}/atividades', $path . "ProfissionalController@getAtividades");
    Route::get('profissional', $path . "ProfissionalController@getProfissional");
    Route::post('salvar-formacao-superior-profissional', $path . "ProfissionalController@salvarFormacaoSuperiorPofissional");
    Route::post('buscar-formacoes-superior-profissional', $path . "ProfissionalController@getFormacoesSuperiorDoProfissional");
    Route::post('excluir-formacao-superior-profissional', $path . "ProfissionalController@excluirFormacaoSuperiorDoProfissional");
    Route::post('buscar-documento-pos-graducao', $path . "ProfissionalController@buscarDocumentoPosGraduacao");
    Route::post('salvar-documento-pos-graducao', $path . "ProfissionalController@salvarDocumentoPosGraduacao");
    Route::post('excluir-documento-pos-graducao', $path . "ProfissionalController@excluirDocumentoPosGraduacao");
});

Route::prefix('matriculaescolaaluno')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\MatriculaEscolaAlunoController@";

    Route::get('{escola}/{aluno}', $path . 'buscar');
    Route::get('{escola}/{aluno}/{matricula}', $path . 'incluir');
});

$path = "\App\Domain\Educacao\Escola\Controllers\MatriculaEscolaAlunoController@";
Route::get('matriculainicialescolaaluno/{escola}/{aluno}/{matriculainicial}', $path . 'incluirMatriculaInicial');

Route::prefix('atestado-frequencia')->group(function () {
    // rh-obs
    $path = "\App\Domain\Educacao\Escola\Controllers\\";
    Route::get('salvar-obs-rh/{rechumanoescola}', $path . 'AtestadoFrequenciaController@getGlobalObs');
    Route::post('salvar-obs-rh', $path . 'AtestadoFrequenciaController@globalObs');
});

Route::prefix('ocupacoes')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\\OcupacoesController@";

    Route::get('ocupacao', $path . 'listar');
    Route::post('ocupacao', $path . 'incluir');
    Route::get('{ocupacao}/deletar', $path . 'deletar');
    Route::get('{codigo}', $path . 'buscar');
    Route::post('ocupacao/{item}', $path . 'editar');
});

$path = "\App\Domain\Educacao\Escola\Controllers\\";
Route::get('ocupacao/{aluno}', $path . "OcupacoesController@buscaOcupacao");
Route::get('ocupacao/{aluno}/{column}/{value}', $path . "OcupacoesController@salvaOcupacao");
Route::get('ocupacao-limpar/{aluno}/{column}', $path . "OcupacoesController@limparOcupacao");

Route::prefix('tipoausencia')->group(function () {
    $path = "\App\Domain\Educacao\Escola\Controllers\\TipoAusenciaController@";

    Route::get('ausencia', $path . 'listar');
    Route::post('ausencia', $path . 'incluir');
    Route::get('{codigo}/deletar', $path . 'deletar');
    Route::get('{codigo}', $path . 'buscar');
    Route::post('ausencia/{item}', $path . 'editar');
});
