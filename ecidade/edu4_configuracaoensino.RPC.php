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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

$parametros = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$retorno = new stdClass();
$retorno->erro = false;
try {
    db_inicio_transacao();

    switch ($parametros->exec) {
        case 'vinclularDisciplinas':

            /**
             * Reescrita da mesma forma salvar do get
             */

            db_fim_transacao(true);
            $disciplina = new cl_disciplina;
            $cadDisciplina = new cl_caddisciplina;

            $sql = $disciplina->sql_disciplinas_ensino($parametros->ensino);

            $rsDisciplinaEnsino = db_query($sql);
            $disciplinasEnsino = array();
            db_utils::makeCollectionFromRecord($rsDisciplinaEnsino, function ($dado) use (&$disciplinasEnsino) {
                $disciplinasEnsino[$dado->codigo_disciplina] = $dado;
            });

            $logExcluir = array();
            foreach ($parametros->disciplinas_remover as $codigo) {
                if (array_key_exists($codigo, $disciplinasEnsino)) {
                    $dadoDisciplina = $disciplinasEnsino[$codigo];
                    if (!empty($dadoDisciplina->com_vinculo)) {
                        $logExcluir[] = $dadoDisciplina->disciplina;
                        continue;
                    }
                }

                db_inicio_transacao();
                $disciplina->excluir("", "ed12_i_ensino = {$parametros->ensino} AND ed12_i_caddisciplina = {$codigo}");
                if ($disciplina->erro_status == 0) {
                    db_fim_transacao(true);
                    throw new Exception("Erro ao excluir disciplina");
                }
                db_fim_transacao();
            }

            if (!empty($logExcluir)) {
                $mensagem = "ATENÇÃO!!\nDisciplina(s) não podem ser excluídas deste ensino:\n";
                $mensagem .= implode("\n -> ", $logExcluir);
                $mensagem .= "\n\nDisciplina(s) vinculada(s) a alguma base curricular,turma ou histórico!";
                throw new Exception($mensagem);
            }

            foreach ($parametros->disciplinas_incluir as $codigo) {

                $lMatrizCurricular = false;
                if (in_array($codigo, $parametros->matriz_curricular_incluir)) {
                    $lMatrizCurricular = true;
                }

                db_inicio_transacao();

                if (array_key_exists($codigo, $disciplinasEnsino)) {

                    $hasMatriz = $disciplinasEnsino[$codigo]->ed12_matrizcurricular == 't';
                    if ($hasMatriz == $lMatrizCurricular) {
                        continue;
                    }

                    $disciplina = new cl_disciplina;
                    $disciplina->ed12_i_codigo = null;
                    $disciplina->ed12_matrizcurricular = $lMatrizCurricular ? 'true' : 'false';

                    $disciplina->alterar(null, "ed12_i_caddisciplina = {$codigo} and ed12_i_ensino = {$parametros->ensino}");
                    if ($disciplina->erro_status == "0") {
                        throw new Exception($disciplina->erro_msg);
                    }

                } else {

                    $disciplina->ed12_i_codigo = null;
                    $disciplina->ed12_i_ensino = $parametros->ensino;
                    $disciplina->ed12_i_caddisciplina = $codigo;
                    $disciplina->ed12_matrizcurricular = $lMatrizCurricular ? 'true' : 'false';
                    $disciplina->incluir(null);
                    if ($disciplina->erro_status == "0") {
                        throw new Exception($disciplina->erro_msg);
                    }
                }

                db_fim_transacao();
            }

            $retorno->mensagem = "Alterações efetuadas com Sucesso!";
            break;

    }
} catch (Exception $exception) {
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
