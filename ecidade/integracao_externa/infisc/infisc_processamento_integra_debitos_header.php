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

require_once('src/Enums/StatusProcessamentoEnum.php');
require_once('src/Managers/QueryManager.php');

use IntegracaoExterna\Infisc\Enums\StatusProcessamentoEnum;
use IntegracaoExterna\Infisc\Managers\QueryManager;

ini_set('display_errors', 1);
ini_set('default_charset', 'UTF-8');
ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

require_once('libs/db_functions.php');

if (!isset($sArquivoLog)) {
    db_log("Informe o nome do arquivo de log!");
    exit;
}

if (!isset($iParamLog)) {
    db_log("Informe o parâametro de configuração de apresentação do log!");
    exit;
}

if (!isset($nomeTabelaPrincipal) && (!isset($validarTabela) || $validarTabela)) {
    db_log("Informe o nome da tabela principal a ser processada!");
    exit;
}

/**
 *  A variável iParamLog define o tipo de log que deve ser gerado :
 *  0 - Imprime log na tela e no arquivo
 *  1 - Imprime log somente da tela
 *  2 - Imprime log somente no arquivo
 */

if ($iParamLog == 1) {
    $sArquivoLog = null;
}

// Declarando variáveis necessárias para que a inclusão das bibliotecas não retorne mensagens
$HTTP_SERVER_VARS['HTTP_HOST']      = '';
$HTTP_SERVER_VARS['PHP_SELF']       = '';
$HTTP_SERVER_VARS["HTTP_REFERER"]   = '';
$HTTP_POST_VARS                     = array();
$HTTP_GET_VARS                      = array();

define('DB_BIBLIOT', '');

require_once('libs/db_conecta.php');
require_once('libs/dbportal.constants.php');
require_once('libs/databaseVersioning.php');

require_once('src/Enums/StatusProcessamentoEnum.php');
require_once('src/Managers/QueryManager.php');

$sCaminhoScript = getcwd();
chdir('../../');

require_once("libs/db_stdlib.php");
require_once("libs/db_utils.php");
require_once("libs/db_sql.php");

chdir($sCaminhoScript);

$origemManager = new QueryManager($connOrigem);
$destinoManager = new QueryManager($connDestino);

$origemManager->begin()->runRawQuery("select fc_startsession();");
$destinoManager->begin()->runRawQuery("SET client_encoding = 'UTF8';");

$erroAtualizacaoBancoDados = false;

try {
    /**
     *  Verifica se existem atualizações de base de dados e as aplica na mesma
     */
    $bancoDadosAtualizado = upgradeDatabase($connDestino, '.');

    if (!$bancoDadosAtualizado) {
        throw new Exception("Falha ao atualizar base de dados!");
    }

    $origemManager->commit();
    $destinoManager->commit();
} catch (Exception $exception) {
    $origemManager->rollback();
    $destinoManager->rollback();

    $erroAtualizacaoBancoDados = true;
}

if ($erroAtualizacaoBancoDados) {
    db_log("Falha ao atualizar base de dados!", $sArquivoLog, $iParamLog);
    exit;
}

/*
######################################################################################################
#                     INICIA O PROCESSAMENTOS DOS DADOS A PARTIR DAQUI                               #
######################################################################################################
*/

if (!isset($validarTabela) || $validarTabela) {
    $existeDebitoErroProcessamento = $destinoManager->query(
        $nomeTabelaPrincipal,
        "sequencial",
        [sprintf("status_processamento = '%s'", StatusProcessamentoEnum::ERRO)]
    )->exists();

    if ($existeDebitoErroProcessamento) {
        db_log("Existe debito com erro no processamento.", $sArquivoLog, $iParamLog);
        exit;
    }
}

$ConfigINI = parse_ini_file("libs/db_config.ini");

if (!isset($ConfigINI["IdUsuario"]) || !$ConfigINI["IdUsuario"]) {
    db_log("Informe o usuário do sistema.", $sArquivoLog, $iParamLog);
    exit;
}

db_putsession("DB_id_usuario", $ConfigINI["IdUsuario"]);
