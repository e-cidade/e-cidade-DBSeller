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

//php5.6 artisan route:list --path=v4/api/educacao/central-de-matriculas

Route::get("emissao-protocolo", "InscricaoController@emissaoProtocolo");

Route::prefix('configuracoes')->group(function () {
    Route::get('noticias', "ConfiguracoesController@getNoticias");
    Route::get('cores', "ConfiguracoesController@getCores");
    Route::get('imagens', "ConfiguracoesController@getImagens");
    Route::get('documentos', "ConfiguracoesController@getDocumentos");
    Route::get('duvidas-frequentes', "ConfiguracoesController@getDuvidas");
    Route::get('mensagens', "ConfiguracoesController@getMensagens");
    Route::get('campos-opcionais', "ConfiguracoesController@getCamposOpcionais");
    Route::get('configuracoes-gerais', "ConfiguracoesController@getConfiguracaoGeral");
});

Route::prefix('processo-inscricao')->group(function () {
    Route::get("escolas-por-bairro", "EscolasController@escolasBairro");
    Route::get('dados-prefeitura', "ProcessoInscricaoController@getDadosPrefeitura");
    Route::get('estados', "ProcessoInscricaoController@getEstados");
    Route::get('paises', "ProcessoInscricaoController@getPaises");
    Route::get('zonas', "ProcessoInscricaoController@getZonasResidencia");
    Route::get('estados/{estado}/municipios', "ProcessoInscricaoController@getMunicipios");
    Route::get('tem-fase-abeta', "ProcessoInscricaoController@temFaseAberta");
    Route::get('fases/{data}', "ProcessoInscricaoController@getFasePorData");
    Route::get('fases-interno/{data}', "ProcessoInscricaoController@getFasePorDataInterno");
    Route::get('fases/{fase}/etapas/get-por-data/{data}', "ProcessoInscricaoController@getEtapasFasePorData");
    Route::get("fases/{fase}/etapas/{etapa}/escolas-disponiveis", "EscolasController@disponiveisPreMatricula");
    Route::get('redes-origem', "ProcessoInscricaoController@getRedesOrigem");
    Route::get('escolas-origem', "ProcessoInscricaoController@getEscolasOrigem");
    Route::get('necessidades-especiais', "ProcessoInscricaoController@getNecessidadesEspeciais");
    Route::get('necessidades-especiais/{necessidade}/subdivisoes', "ProcessoInscricaoController@getSubdivisoesNecessidadeEspecial");
    Route::get('bairros', "ProcessoInscricaoController@getBairros");
    Route::get('profissoes', "ProcessoInscricaoController@getProfissoes");
    Route::get('tipos-ruas', "ProcessoInscricaoController@getTiposRuas");
    Route::post("inscricao", "InscricaoController@inscricao");
    Route::delete("inscricao/{protocolo}", "InscricaoController@delete");
    Route::get("inscricao/consulta/{tipo}/{valor}", "InscricaoController@consulta");
    Route::get("inscricao/consultaCpf/{cpfAluno}/{tipo}/{valor}", "InscricaoController@consultaCpf");
    Route::get("inscricao/consulta-candidato/{tipo}/{dado}/{nascimento}", "InscricaoController@consultaCandidato");
    Route::get('renda-familiar', "ProcessoInscricaoController@getFaixasRenda");
    Route::get('orgaos-emissores', "ProcessoInscricaoController@getOrgaosEmissores");
    Route::get('aluno/cpf/{cpf}', "ProcessoInscricaoController@getAlunoByCpf");
    Route::get('aluno/escolacpf/{esocila}/{cpf}', "ProcessoInscricaoController@getAlunoEscolaByCpf");
    Route::get('aluno/visto/{visto}', "ProcessoInscricaoController@getAlunoByVisto");
    Route::get('aluno/rne/{rne}', "ProcessoInscricaoController@getAlunoByRne");
    Route::get('fases/{fase}/candidato/cpf/{cpf}', "ProcessoInscricaoController@getCandidatoByCpf");
    Route::get('fases/{fase}/candidato/visto/{visto}', "ProcessoInscricaoController@getCandidatoByVisto");
    Route::get('fases/{fase}/candidato/rne/{rne}', "ProcessoInscricaoController@getCandidatoByRne");
    Route::get("aluno/{escola}/cpf/{cpf}", "ProcessoInscricaoController@getAlunoEscolaByCpf");
    Route::get('cor-raca', "ProcessoInscricaoController@getCorRaca");

});

Route::get("etapas-lista-espera", "ListaEsperaController@getEtapasListaEspera");
Route::get("escolas-lista-espera/{etapa}", "ListaEsperaController@getEscolasListaEspera");
Route::get("turnos-lista-espera/{etapa}/{escola}", "ListaEsperaController@getTurnosListaEspera");
Route::get("candidatos-lista-espera/{etapa}/{escola}/{turno}", "ListaEsperaController@getCandidatosListaEspera");
Route::get("vagas-pcd", "ListaEsperaController@buscarVagasPcd");
Route::post("vagas-pcd", "ListaEsperaController@salvarVagasPcd");
Route::get("listas-impedidas", "ListaEsperaController@listaImpedida");
Route::post("processar-alocacoes", "ListaEsperaController@processarAlocacoes");

Route::prefix('observacoes-inscricao')->group(function () {
    Route::get("/{candidato}", "InscricaoController@buscarObservacoes");
    Route::post("/{candidato}", "InscricaoController@adicionarObservacao");
    Route::delete("/{observacao}", "InscricaoController@excluirObservacao");
});
