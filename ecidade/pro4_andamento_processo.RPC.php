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

use ECidade\Patrimonial\Protocolo\Servicos\AndamentoProcessoService;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

if (isset($_POST['getExtArq'])){
    $getExtArq = (bool) $_POST['getExtArq'];
}else{
    $getExtArq = true;
}

$definicoesCamposDinamicos = array();

if (!empty($_REQUEST['campos'])) {
    array_map(function ($campo) use (&$definicoesCamposDinamicos) {
        $definicoesCamposDinamicos[$campo] = FILTER_DEFAULT;
        $definicoesCamposDinamicos['sequencial_' . $campo] = FILTER_VALIDATE_INT;
    }, explode(",", $_REQUEST['campos']));
}

$definicoes = array_merge($definicoesCamposDinamicos, array(
    'codigoTransferencia' => FILTER_SANITIZE_STRING,
    'departamentoDestino' => FILTER_VALIDATE_INT,
    'recebimentoDestino' => FILTER_VALIDATE_INT,
    'despachoAnexos' => FILTER_REQUIRE_ARRAY,
    'despachoInterno' => FILTER_SANITIZE_STRING,
    'despachoPublico' => FILTER_VALIDATE_BOOLEAN,
    'codigoProcesso' => FILTER_VALIDATE_INT,
    'mensagemPreitura' => FILTER_VALIDATE_BOOLEAN,
    'campos' => FILTER_SANITIZE_STRING,
    'acao' => FILTER_SANITIZE_STRING,
    'anoProcesso' => null,
    'ultimaTransferencia' => FILTER_VALIDATE_INT,
    'filtros' => FILTER_REQUIRE_ARRAY,
    'hash' => FILTER_SANITIZE_STRING,
    'codigoAndamento' => FILTER_VALIDATE_INT,
    'mensagem'=> FILTER_SANITIZE_STRING,
    'respostaOuvidoria'=>FILTER_VALIDATE_BOOLEAN,
    'filtraDepartamentosPorDataLimite'=>FILTER_VALIDATE_INT
));

$parametros = JSON::requestParameters($definicoes);

try {
    db_inicio_transacao();

    $retorno = new stdClass();
    $retorno->erro = false;
    $retorno->processamento = new stdClass();

    $servico = new AndamentoProcessoService($parametros);

    switch ($parametros->acao) {
        case 'buscarProcessos':
            $retorno->processos = $servico->buscarProcessos();
            break;
        case 'buscaNovosProcessos':
            $retorno->processos = $servico->buscaNovosProcessos();
            break;
        case 'apenasReceber':
            $retorno->receberProcesso = $servico->apenasReceber();
            $retorno->processo = $servico->buscarProcessos();
            break;
        case 'processar':
            $retorno->processamento = $servico->processar();
            $retorno->processo = $servico->buscarProcessos();
            break;
        case 'transferir':
            $retorno->processamento->transferido = $servico->transferir();
            $retorno->processo = $servico->buscarProcessos();
            break;
        case 'uploadAnexosDespacho':
            $retorno->uploads = $servico->upload();
            break;
        case 'prepararDocumentos':
            $retorno->documentos = $servico->prepararDocumentos($getExtArq);
            break;
        case "respostaPrefeitura":
            $retorno->respotaCidadao = $servico->salvarMensagemOuvidoria();
            break;
        case "mensagemPrefeitura":
            $retorno->mensagemPrefeitura = $servico->salvarMensagemOuvidoria();
            break;
    }
} catch (Exception $exception) {
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);

echo JSON::create()->stringify($retorno);
