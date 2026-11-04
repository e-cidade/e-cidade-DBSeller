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

$sArquivoLog = "log/processamento_integra_debitos_movimentos_infisc_".date("Ymd").".log";
$iParamLog = 0;
$validarTabela = false;

require_once("infisc_processamento_integra_debitos_header.php");

require_once('src/IntegraDebitos/IntegraDebitosMovimentosProcessamentoService.php');
require_once('src/Enums/TipoInscricaoEnum.php');

$sCaminhoScript = getcwd();
chdir('../../');

chdir($sCaminhoScript);

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
db_putsession('DB_id_usuario', '1');
db_putsession('DB_coddepto', '24');
db_putsession('DB_instit', '1');

$integraDebitosMovimentosProcessamentoService = new IntegraDebitosMovimentosProcessamentoService($origemManager, $destinoManager);

$itensPendentes = $integraDebitosMovimentosProcessamentoService->buscarItensPendentes();

if (!$itensPendentes || count($itensPendentes) == 0) {
    db_log("Sem items para processar.", $sArquivoLog, $iParamLog);
    exit;
}

foreach ($itensPendentes as $itemPendente) {
    $origemManager->begin()->runRawQuery("select fc_startsession();");
    $destinoManager->begin()->runRawQuery("SET client_encoding = 'UTF8';");

    try {
        $integraDebitosMovimentosProcessamentoService->setDados($itemPendente)->processar();
        $integraDebitosMovimentosProcessamentoService->setarStatusProcessado();

        $origemManager->commit();
        $destinoManager->commit();
    } catch (\Exception $exception) {
        $origemManager->rollback();
        $destinoManager->rollback();

        $atualizado = $integraDebitosMovimentosProcessamentoService->setarStatusErro();

        if ($atualizado) {
            $mensagem = "Erro ao processar o item [{$itemPendente->q197_sequencial} | {$exception->getMessage()}]";
        } else {
            $mensagem = "Não foi possível setar item como erro [{$itemPendente->q197_sequencial}]";
        }

        db_log($mensagem, $sArquivoLog, $iParamLog);
    }
}
