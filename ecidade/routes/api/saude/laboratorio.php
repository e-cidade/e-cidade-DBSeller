<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

use App\Domain\Saude\Ambulatorial\Middlewares\HasCgsMiddleware;

Route::middleware(['clientCredential', HasCgsMiddleware::class])->group(function () {
    Route::prefix('exames')->group(function () {
        Route::get('requisicao/{idRequisicao}', 'RequisicaoExamesController@get');
        Route::get('resultado/{idRequisicaoExame}', 'ResultadoExamesController@get');
    });
    
});

Route::middleware(["auth:api"])->group(function () {

    Route::prefix('consulta')->group(function () {
        Route::get('departamentos/{departamento}/laboratorios', 'LaboratorioController@getByDepartamento');
        Route::get('grupos-liberacao/{usuario}', 'GrupoLiberacaoController@getAllByUsuario');
        Route::get('laboratorio/{laboratorio}/setores', 'GrupoLiberacaoController@getLaboratorioSetores');
        Route::get('laboratorio/{laboratorio}/setores/{setor}/exames', 'LaboratorioController@getLaboratorioSetorExames');
        Route::get('grupos-liberacao/{grupoLiberacaoId}/exames', 'GrupoLiberacaoController@getExamesByGrupo');
    });

    Route::prefix('cadastro')->group(function () {
        Route::post('grupos-liberacao', 'GrupoLiberacaoController@criarGrupoLiberacao');
        Route::put('grupos-liberacao', 'GrupoLiberacaoController@atualizarGrupoLiberacao');
        Route::post('grupos-liberacao/novo-exame', 'GrupoLiberacaoController@adicionarGrupoLiberacaoExame');
        Route::delete('grupos-liberacao/{grupoId}/exame/{exameId}', 'GrupoLiberacaoController@excluirExameFromGrupoLiberacao');
        Route::delete('grupos-liberacao/{grupo}', 'GrupoLiberacaoController@excluirGrupoLiberacao');
    });
        
    Route::prefix('triagem-laboratorial')->group(function () {
        Route::post('buscar', 'TriagemLaboratorialController@buscar');
        Route::post('buscar-requisicoes', 'TriagemLaboratorialController@buscarRequisicoes');
        Route::post('exportar-pendencias', 'TriagemLaboratorialController@exportarPendenciasTriagem');
        Route::post('processar', 'TriagemLaboratorialController@processar');
    });
});
