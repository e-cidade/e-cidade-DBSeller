<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

use App\Domain\Financeiro\Orcamento\Models\Unidade;
use ECidade\Configuracao\Cadastro\Repository\OrcamentoDotacaoRepository;
use ECidade\Configuracao\Cadastro\Services\PermissoesDespesaService;

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

$anousu = db_getsession('DB_anousu');
$orcamentoDotacaoRepository = new OrcamentoDotacaoRepository();
$orcamentoDotacaoRepository->scopeAnousu($anousu);

try {
    db_inicio_transacao();
    switch ($parametros->acao) {
        case 'buscarPermissoesPorUsuario':
            if (empty($parametros->id_usuario)) {
                throw new Exception("Informe o ID do Usuário.");
            }
            $daoPermemp = new cl_db_permemp();

            $where = ["db20_anousu = {$anousu}", "db21_id_usuario = {$parametros->id_usuario}"];

            $sqlPermissoes = $daoPermemp->sql_query_origem(
                null,
                "db20_codperm,db20_anousu,db20_orgao,db20_unidade,
                 db20_funcao,db20_subfuncao, db20_programa,db20_projativ,
                 db20_codele,db20_codigo,o15_loaespecificacao,
                 case when db20_tipoperm = 'M' then 'MANUTENÇÃO' else 'CONSULTA' end as db20_tipoperm,
                 o40_descr as descricao_orgao
                  ",
                'db20_orgao',
                implode(' AND ', $where)
            );

            $rs = db_query($sqlPermissoes);
            if (!$rs) {
                throw new Exception("Erro ao buscar permissões do Usuário");
            }

            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
            $retorno->permissoes = [];
            while ($permissao = pg_fetch_object($rs)) {
                $permissao->tipos_processo = [];

                $sqlPermissaoAtividade = $daoPermissaoAtividade->sql_query(
                    null,
                    'db_permemp_atividadesexecucao.*, atividadesexecucao.*, tipoproc.*',
                    null,
                    " db69_codperm = {$permissao->db20_codperm}"
                );

                $rsPermissoes = db_query($sqlPermissaoAtividade);
                if (!$rsPermissoes) {
                    throw new Exception("Erro ao buscar Atividades de Execução das Permissões");
                }

                while ($permissaoAtividade = pg_fetch_object($rsPermissoes)) {
                    $key = $permissaoAtividade->p51_codigo;
                    if (!array_key_exists($key, $permissao->tipos_processo)) {
                        $permissao->tipos_processo[$key] = (object)[
                            'codigo' => $permissaoAtividade->p51_codigo,
                            'descricao' => $permissaoAtividade->p51_descr,
                            'atividades' => []
                        ];
                    }

                    $permissao->tipos_processo[$key]->atividades[] = $permissaoAtividade;
                }
                $permissao->tipos_processo = array_values($permissao->tipos_processo);
                $retorno->permissoes[] = $permissao;
            }
            break;
        case 'buscarClassificacaoProgramatica':
            $service = new PermissoesDespesaService();
            $retorno->data = $service->buscarClassificacaoProgramatica();
            break;
        case 'buscarUnidades':
            $query = Unidade::select(['o41_unidade as codigo', 'o41_descr as descricao']);
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $query->where('o41_anousu', '=', db_getsession("DB_anousu"))
                    ->where('o41_orgao', '=', $parametros->orgao)
                    ->orderBy('o41_unidade');
            } else {
                $query->whereNull('o41_unidade');
            }
            $retorno->unidades = $query->get()->toArray();
            break;
        case 'buscarFuncoes':
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }

            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }

            $retorno->funcoes = $orcamentoDotacaoRepository->get(
                ["distinct o52_funcao as codigo, o52_descr as descricao"],
                ["o52_funcao"]
            );
            break;
        case 'buscarSubfuncoes':
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }
            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }
            if (isset($parametros->funcao) && !empty($parametros->funcao)) {
                $orcamentoDotacaoRepository->scopeFuncao($parametros->funcao);
            }

            $retorno->subfuncoes = $orcamentoDotacaoRepository->get(
                ["distinct o53_subfuncao as codigo, o53_descr as descricao"],
                ["o53_subfuncao"]
            );
            break;
        case 'buscarProgramas':
            $orcamentoDotacaoRepository->scopeAnousu($anousu);
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }
            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }
            if (isset($parametros->funcao) && !empty($parametros->funcao)) {
                $orcamentoDotacaoRepository->scopeFuncao($parametros->funcao);
            }
            if (isset($parametros->subfuncao) && !empty($parametros->subfuncao)) {
                $orcamentoDotacaoRepository->scopeSubfuncao($parametros->subfuncao);
            }

            $retorno->programas = $orcamentoDotacaoRepository->get(
                ["distinct o54_programa as codigo, o54_descr as descricao"],
                ["o54_programa"]
            );
            break;
        case 'buscarProjetoAtividade':
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }
            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }
            if (isset($parametros->funcao) && !empty($parametros->funcao)) {
                $orcamentoDotacaoRepository->scopeFuncao($parametros->funcao);
            }
            if (isset($parametros->subfuncao) && !empty($parametros->subfuncao)) {
                $orcamentoDotacaoRepository->scopeSubfuncao($parametros->subfuncao);
            }
            if (isset($parametros->programa) && !empty($parametros->programa)) {
                $orcamentoDotacaoRepository->scopePrograma($parametros->programa);
            }

            $retorno->projetoAtividade = $orcamentoDotacaoRepository->get(
                ["distinct o55_projativ as codigo, o55_descr as descricao"],
                ["o55_projativ"]
            );
            break;
        case 'buscarElementos':
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }
            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }
            if (isset($parametros->funcao) && !empty($parametros->funcao)) {
                $orcamentoDotacaoRepository->scopeFuncao($parametros->funcao);
            }
            if (isset($parametros->subfuncao) && !empty($parametros->subfuncao)) {
                $orcamentoDotacaoRepository->scopeSubfuncao($parametros->subfuncao);
            }
            if (isset($parametros->programa) && !empty($parametros->programa)) {
                $orcamentoDotacaoRepository->scopePrograma($parametros->programa);
            }
            if (isset($parametros->projetoAtividade) && !empty($parametros->projetoAtividade)) {
                $orcamentoDotacaoRepository->scopeProjetoAtividade($parametros->projetoAtividade);
            }

            $retorno->elementos = $orcamentoDotacaoRepository->get(
                ["distinct o56_codele, o56_elemento as codigo, o56_descr as descricao"],
                ["o56_elemento"]
            );
            break;
        case 'buscarRecursos':
            if (isset($parametros->orgao) && !empty($parametros->orgao)) {
                $orcamentoDotacaoRepository->scopeOrgao($parametros->orgao);
            }
            if (isset($parametros->unidade) && !empty($parametros->unidade)) {
                $orcamentoDotacaoRepository->scopeUnidade($parametros->unidade);
            }
            if (isset($parametros->funcao) && !empty($parametros->funcao)) {
                $orcamentoDotacaoRepository->scopeFuncao($parametros->funcao);
            }
            if (isset($parametros->subfuncao) && !empty($parametros->subfuncao)) {
                $orcamentoDotacaoRepository->scopeSubfuncao($parametros->subfuncao);
            }
            if (isset($parametros->programa) && !empty($parametros->programa)) {
                $orcamentoDotacaoRepository->scopePrograma($parametros->programa);
            }
            if (isset($parametros->projetoAtividade) && !empty($parametros->projetoAtividade)) {
                $orcamentoDotacaoRepository->scopeProjetoAtividade($parametros->projetoAtividade);
            }
            if (isset($parametros->elemento) && !empty($parametros->elemento)) {
                $orcamentoDotacaoRepository->scopeElemento($parametros->elemento);
            }

            $retorno->recursos = $orcamentoDotacaoRepository->get(
                ["distinct o15_codigo as codigo, o15_loaespecificacao || ' - ' || o15_descr as descricao"],
                ["o15_codigo"]
            );
            break;
        case 'salvarPermissaoUsuario':
            if (!isset($parametros->orgao)) {
                throw new Exception('Informe o Orgão');
            }
            if (!isset($parametros->unidade)) {
                throw new Exception('Informe a Unidade');
            }
            if (!isset($parametros->funcao)) {
                throw new Exception('Informe a Função');
            }
            if (!isset($parametros->subfuncao)) {
                throw new Exception('Informe a Subfunção');
            }
            if (!isset($parametros->programa)) {
                throw new Exception('Informe o Programa');
            }
            if (!isset($parametros->projetoAtividade)) {
                throw new Exception('Informe o Projeto Atividade');
            }
            if (!isset($parametros->elemento)) {
                throw new Exception('Informe o Elemento');
            }
            if (!isset($parametros->recurso)) {
                throw new Exception('Informe o Recurso');
            }
            if (empty($parametros->tipoPermissao)) {
                throw new Exception('Informe o Tipo de Permissão');
            }
            if (empty($parametros->codigoUsuario)) {
                throw new Exception('Informe o Usuário');
            }

            $db20_orgao = $parametros->orgao;
            $db20_unidade = $parametros->unidade;
            $db20_funcao = $parametros->funcao;
            $db20_subfuncao = $parametros->subfuncao;
            $db20_programa = $parametros->programa;
            $db20_projativ = $parametros->projetoAtividade;
            $db20_codele = $parametros->elemento;
            $db20_codigo = $parametros->recurso;
            $db20_codperm = $parametros->codigoPermissao;
            $db20_tipoperm = $parametros->tipoPermissao;

            $clorcelemento = new cl_orcelemento();

            $where = "";
            if (!empty($db20_codperm)) {
                $where = "db20_codperm <> {$db20_codperm} and ";
            }
            $cldb_permemp = new cl_db_permemp();
            $sql = $cldb_permemp->sql_query_origem(
                null,
                "db20_codperm",
                "",
                " {$where} db21_id_usuario = {$parametros->codigoUsuario} and
					      db20_anousu = {$anousu} and (db20_orgao = {$db20_orgao} and
                                                       db20_unidade = {$db20_unidade} and
                                                       db20_funcao = {$db20_funcao} and
                                                       db20_subfuncao = {$db20_subfuncao} and
                                                       db20_programa = {$db20_programa} and
                                                       db20_projativ = {$db20_projativ} and
                                                       db20_codele = {$db20_codele} and
                                                       db20_codigo = {$db20_codigo})"
            );
            $cldb_permemp->sql_record($sql);
            if ($cldb_permemp->numrows > 0) {
                $cldb_permemp->erro_status='0';
                throw new Exception("Seleção já cadastrada!");
            }

            if ($db20_orgao == 0) {
                if ($db20_unidade != 0 || $db20_unidade != 0 || $db20_subfuncao != 0 || $db20_programa != 0 || $db20_projativ != 0
                    || $db20_codele != 0 || $db20_codigo != 0) {
                    throw new Exception("O campo Órgão é de preenchimento obrigatório. Para incluir uma permissão é necessário informar o órgão e depois os demais campos.");
                }
                throw new Exception("Para incluir todas as permissões, selecionar a opção Incluir Todos.");
            }

            $cldb_permemp->db20_anousu    = $anousu;
            $cldb_permemp->db20_orgao     = $db20_orgao;
            $cldb_permemp->db20_unidade   = $db20_unidade;
            $cldb_permemp->db20_funcao    = $db20_funcao;
            $cldb_permemp->db20_subfuncao = $db20_subfuncao;
            $cldb_permemp->db20_programa  = $db20_programa;
            $cldb_permemp->db20_projativ  = $db20_projativ;
            $cldb_permemp->db20_codele    = $db20_codele;
            $cldb_permemp->db20_codigo    = $db20_codigo;
            $cldb_permemp->db20_codperm   = $db20_codperm;
            $cldb_permemp->db20_tipoperm  = $db20_tipoperm;

            if (!empty($db20_codperm)) {
                $cldb_permemp->alterar($db20_codperm);
            } else {
                $cldb_permemp->incluir($db20_codperm);
                $db20_codperm = $cldb_permemp->db20_codperm;
                if ($cldb_permemp->erro_status == 0) {
                    throw new Exception($cldb_permemp->erro_msg);
                }

                $cldb_usupermemp = new cl_db_usupermemp();
                $cldb_usupermemp->db21_codperm = $db20_codperm;
                $cldb_usupermemp->db21_id_usuario = $parametros->codigoUsuario;
                $cldb_usupermemp->incluir($db20_codperm, $parametros->codigoUsuario);
                if ($cldb_usupermemp->erro_status == 0) {
                    throw new Exception($cldb_usupermemp->erro_msg);
                }
            }
            // alterar vinculos
//            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
//            $daoPermissaoAtividade->excluir(null, " db69_codperm = {$db20_codperm}");
//            if ($daoPermissaoAtividade->erro_status == 0) {
//                throw new Exception($daoPermissaoAtividade->erro_msg);
//            }
//            if (!empty($parametros->atividades)) {
//                $codigosAtividades = explode(',', $parametros->atividades);
//                foreach ($codigosAtividades as $codigoAtividade) {
//                    $daoPermissaoAtividade->db69_codperm = $db20_codperm;
//                    $daoPermissaoAtividade->db69_atividadesexecucao = $codigoAtividade;
//                    $daoPermissaoAtividade->incluir();
//                    if ($daoPermissaoAtividade->erro_status == 0) {
//                        throw new Exception($daoPermissaoAtividade->erro_msg);
//                    }
//                }
//            }

            $retorno->mensagem = "Permissão salva com sucesso!";
            break;
        case 'excluirPermissaoUsuario':
            if (empty($parametros->codigoPermissao)) {
                throw new Exception('Informe o Código da Permissão');
            }
            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
            $daoPermissaoAtividade->excluir(null, " db69_codperm = {$parametros->codigoPermissao}");
            if ($daoPermissaoAtividade->erro_status == 0) {
                throw new Exception($daoPermissaoAtividade->erro_msg);
            }
            $cldb_permemp = new cl_db_permemp();
            $cldb_usupermemp = new cl_db_usupermemp();
            $cldb_usupermemp->excluir($parametros->codigoPermissao);
            if ($cldb_usupermemp->erro_status == 0) {
                throw new Exception('Erro ao excluir Permissão do Usuário');
            }
            $cldb_permemp->excluir($parametros->codigoPermissao);
            if ($cldb_permemp->erro_status == 0) {
                throw new Exception('Erro ao excluir Permissão do Usuário');
            }
            $retorno->mensagem = "Permissão excluida com sucesso!";
            break;
        case 'incluirTodasPermissoes':
            if (empty($parametros->tipoPermissao)) {
                throw new Exception('Informe o Tipo de Permissão');
            }
            if (empty($parametros->codigoUsuario)) {
                throw new Exception('Informe o Usuário');
            }
            $id_usuario = $parametros->codigoUsuario;

            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();

            $daoUsuarioPermissao = new cl_db_usupermemp();
            $daoPermissao = new cl_db_permemp();
            $dbwhere = " db21_id_usuario = $id_usuario and db20_anousu = {$anousu} ";
            // remove permissoes deste usuario
            $sqlPermissoesUsuario = $daoUsuarioPermissao->sql_query(null, $id_usuario, "*", null, $dbwhere);
            $rs = db_query($sqlPermissoesUsuario);
            $rows = pg_num_rows($rs);
            for ($x = 0; $x < $rows; $x++) {
                db_fieldsmemory($rs, $x);
                $daoPermissaoAtividade->excluir(null, " db69_codperm = {$db20_codperm}");
                if ($daoPermissaoAtividade->erro_status == 0) {
                    throw new Exception($daoPermissaoAtividade->erro_msg);
                }
                $daoUsuarioPermissao->excluir($db20_codperm, $id_usuario);
                if ($daoUsuarioPermissao->erro_status == 0) {
                    throw new Exception($daoUsuarioPermissao->erro_msg);
                }
                $daoPermissao->excluir($db20_codperm);
                if ($daoPermissao->erro_status == 0) {
                    throw new Exception($daoPermissao->erro_msg);
                }
            }

            // inclui todas as permissoes
            $daoOrgao = new cl_orcorgao();
            $sqlOrgaos = $daoOrgao->sql_query(
                null,
                null,
                "o40_orgao as db20_orgao",
                "o40_orgao",
                "o40_anousu = {$anousu} "
            );
            $rs = db_query($sqlOrgaos);
            if (!$rs) {
                throw new Exception('Erro ao buscar orgaos');
            }

            $numrows = pg_num_rows($rs);
            for ($i = 0; $i < $numrows; $i++) {
                db_fieldsmemory($rs, $i);

                $dbwhere = "  db21_id_usuario = {$id_usuario} ";

                $daoPermissao->db20_anousu = $anousu;
                $daoPermissao->db20_orgao = $db20_orgao;
                $daoPermissao->db20_unidade = '0';
                $daoPermissao->db20_funcao = '0';
                $daoPermissao->db20_subfuncao = '0';
                $daoPermissao->db20_programa = '0';
                $daoPermissao->db20_projativ = '0';
                $daoPermissao->db20_codele = '0';
                $daoPermissao->db20_codigo = '0';
                $daoPermissao->db20_codperm = '0';
                $daoPermissao->db20_tipoperm = $parametros->tipoPermissao;
                $daoPermissao->incluir(null);
                $db20_codperm = $daoPermissao->db20_codperm;
                if ($daoPermissao->erro_status == 0) {
                    throw new Exception($daoPermissao->erro_msg);
                }

                $daoUsuarioPermissao->db21_codperm = $db20_codperm;
                $daoUsuarioPermissao->db21_id_usuario = $id_usuario;
                $daoUsuarioPermissao->incluir($db20_codperm, $id_usuario);
                if ($daoUsuarioPermissao->erro_status == 0) {
                    throw new Exception($daoPermissao->erro_msg);
                }

//                if (!empty($parametros->atividades)) {
//                    $codigosAtividades = explode(',', $parametros->atividades);
//                    foreach ($codigosAtividades as $codigoAtividade) {
//                        $daoPermissaoAtividade->db69_codperm = $db20_codperm;
//                        $daoPermissaoAtividade->db69_atividadesexecucao = $codigoAtividade;
//                        $daoPermissaoAtividade->incluir();
//                        if ($daoPermissaoAtividade->erro_status == 0) {
//                            throw new Exception($daoPermissaoAtividade->erro_msg);
//                        }
//                    }
//                }

                unset($db20_codperm);
            }

            $retorno->mensagem = 'Inclusão efetuada com Sucesso';
            break;
        case 'excluirTodasPermissoes':
            if (empty($parametros->codigoUsuario)) {
                throw new Exception('Informe o Usuário');
            }
            $id_usuario = $parametros->codigoUsuario;
            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
            $daoUsuarioPermissao = new cl_db_usupermemp();
            $daoPermissao = new cl_db_permemp();
            $dbwhere = " db21_id_usuario = {$id_usuario} and db20_anousu = {$anousu} ";
            // remove permissoes deste usuario
            $sqlPermissoesUsuario = $daoUsuarioPermissao->sql_query(null, $id_usuario, "*", null, $dbwhere);
            $rs = db_query($sqlPermissoesUsuario);
            $rows = pg_num_rows($rs);
            for ($x = 0; $x < $rows; $x++) {
                db_fieldsmemory($rs, $x);
                $daoPermissaoAtividade->excluir(null, " db69_codperm = {$db20_codperm}");
                if ($daoPermissaoAtividade->erro_status == 0) {
                    throw new Exception($daoPermissaoAtividade->erro_msg);
                }
                $daoUsuarioPermissao->excluir($db20_codperm, $id_usuario);
                if ($daoUsuarioPermissao->erro_status == 0) {
                    throw new Exception($daoUsuarioPermissao->erro_msg);
                }
                $daoPermissao->excluir($db20_codperm);
                if ($daoPermissao->erro_status == 0) {
                    throw new Exception($daoPermissao->erro_msg);
                }
            }

            $retorno->mensagem = 'Exclusão efetuada com Sucesso';
            break;
        case 'buscarPermissoesPorTipoProcesso':
            if (empty($parametros->codigoTipoProcesso)) {
                throw new Exception('Informe o Tipo de Processo');
            }
            if (empty($parametros->codigoPermissao)) {
                throw new Exception('Informe o Código da Permissão');
            }
            $tipoProcesso = \App\Domain\Patrimonial\Protocolo\Model\TipoProcesso::find($parametros->codigoTipoProcesso);

            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
            $sql = $daoPermissaoAtividade->sql_query_file(
                null,
                'db69_atividadesexecucao',
                null,
                " db69_codperm = {$parametros->codigoPermissao} and db69_tipoprocesso = {$parametros->codigoTipoProcesso}"
            );
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Erro ao buscar Permissões de Atividades por Tipo de Processo.");
            }

            $retorno->atividades = db_utils::getCollectionByRecord($rs);
            $retorno->mensagem = '';
            break;
        case 'salvarPermissoesPorTipoProcesso':
            if (empty($parametros->codigoTipoProcesso)) {
                throw new Exception('Informe o Tipo de Processo');
            }
            if (empty($parametros->codigoPermissao)) {
                throw new Exception('Informe o Código da Permissão');
            }

            $daoPermissaoAtividade = new cl_db_permemp_atividadesexecucao();
            $daoPermissaoAtividade->excluir(null, " db69_codperm = {$parametros->codigoPermissao} and db69_tipoprocesso = {$parametros->codigoTipoProcesso}");
            if ($daoPermissaoAtividade->erro_status == 0) {
                throw new Exception($daoPermissaoAtividade->erro_msg);
            }
            if (!empty($parametros->atividadesSelecionadas)) {
                $codigosAtividades = explode(',', $parametros->atividadesSelecionadas);
                foreach ($codigosAtividades as $codigoAtividade) {
                    $daoPermissaoAtividade->db69_codperm = $parametros->codigoPermissao;
                    $daoPermissaoAtividade->db69_atividadesexecucao = $codigoAtividade;
                    $daoPermissaoAtividade->db69_tipoprocesso = $parametros->codigoTipoProcesso;
                    $daoPermissaoAtividade->incluir();

                    if ($daoPermissaoAtividade->erro_status == 0) {
                        throw new Exception($daoPermissaoAtividade->erro_msg);
                    }
                }
            }
            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
