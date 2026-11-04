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

require_once('src/Enums/TipoLancamentoEnum.php');
require_once('src/IntegraDebitos/IntegraDebitosBaseService.php');
require_once('src/IntegraDebitos/IntegraDebitosMovimentosBaixaProcessamentoService.php');

use IntegracaoExterna\Infisc\Enums\StatusProcessamentoEnum;
use IntegracaoExterna\Infisc\IntegraDebitos\IntegraDebitosMovimentosBaixaProcessamentoService;

$sArquivoLog = "log/processamento_integra_debitos_movimentos_baixa_infisc_".date("Ymd").".log";
$iParamLog = 0;
$nomeTabelaPrincipal = "integra_infisc.integra_debitos_movimentos_baixa";
$validarTabela = false;

require_once("infisc_processamento_integra_debitos_header.php");



/*
######################################################################################################
#                     INICIA O PROCESSAMENTOS DOS DADOS A PARTIR DAQUI                               #
######################################################################################################
*/

global $conn;
$conn = $connOrigem;

db_putsession('DB_acessado', '1');
db_putsession('DB_datausu', time());
db_putsession('DB_anousu', date('Y',time()));
db_putsession('DB_coddepto', '24');
db_putsession('DB_instit', '1');

$dadosTerminal = $origemManager->query(
    "cfautent",
    "k11_ipterm",
    [
        ["k11_infisc", 'true', "%s"]
    ],
    1
)->get();

if (!$dadosTerminal || !$dadosTerminal->k11_ipterm) {
    db_log("Nenhum terminal cadastrado.", $sArquivoLog, $iParamLog);
    exit;
}

db_putsession('DB_ip', $dadosTerminal->k11_ipterm);

const QUANTIDADE_ITEMS_PROCESSAR = 1000;

$movimentos = $destinoManager->query(
    $nomeTabelaPrincipal,
    "*",
    [sprintf("status_processamento = '%s'", StatusProcessamentoEnum::PENDENTE)],
    QUANTIDADE_ITEMS_PROCESSAR,
    "sequencial ASC"
)->getCollection();

$integraDebitosMovimentosBaixaProcessamentoService = new IntegraDebitosMovimentosBaixaProcessamentoService($origemManager, $destinoManager);
$integraDebitosMovimentosBaixaProcessamentoService->setNomeTabelaPrincipal($nomeTabelaPrincipal);

$sCaminhoScript = getcwd();

foreach ($movimentos as $movimento) {
    $origemManager->begin()->runRawQuery("select fc_startsession();")
                           ->runRawQuery("select fc_putsession('DB_instit', '1')")
                           ->runRawQuery("select fc_putsession('DB_use_pcasp', 'false')")
                           ->runRawQuery(
                               sprintf("select fc_putsession('DB_anousu', '%s')", db_getsession("DB_anousu"))
                           )
                           ->runRawQuery(
                               sprintf("select fc_putsession('DB_id_usuario', '%s')", db_getsession("DB_id_usuario"))
                           )
                           ->runRawQuery(
                               sprintf("select fc_putsession('DB_datausu', '%s')", date('Y-m-d', db_getsession('DB_datausu')))
                           );

    $destinoManager->begin()->runRawQuery("SET client_encoding = 'UTF8';");

    try {
        chdir('../../');
            $integraDebitosMovimentosBaixaProcessamentoService->setCancelaDebitoModel(new cancelamentoDebitos());
            $integraDebitosMovimentosBaixaProcessamentoService->setCancelamentoIssqnVariavel(new CancelamentoISSQNVariavel());
        chdir($sCaminhoScript);

        $integraDebitosMovimentosBaixaProcessamentoService->setDados($movimento)->processar();

        $integraDebitosMovimentosBaixaProcessamentoService->atualizaStatusProcessamento(StatusProcessamentoEnum::PROCESSADO);

        $origemManager->commit();
        $destinoManager->commit();
    } catch (Exception $exception) {
        $origemManager->rollback();
        $destinoManager->rollback();

        $integraDebitosMovimentosBaixaProcessamentoService->atualizaStatusProcessamento(StatusProcessamentoEnum::ERRO, $exception->getMessage());

        db_log(
            "Erro ao processar o movimento [sequencial: {$movimento->sequencial} | erro: {$exception->getMessage()}]",
            $sArquivoLog,
            $iParamLog
        );
    }
}
