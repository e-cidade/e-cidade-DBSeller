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

require_once('src/Enums/TipoTributoEnum.php');
require_once('src/Enums/TipoMovimentoEnum.php');
require_once('src/IntegraDebitos/IntegraDebitosBaseService.php');
require_once('src/IntegraDebitos/IntegraDebitosProcessamentoService.php');

use IntegracaoExterna\Infisc\Enums\StatusProcessamentoEnum;
use IntegracaoExterna\Infisc\IntegraDebitos\IntegraDebitosProcessamentoService;

$sArquivoLog = "log/processamento_integra_debitos_infisc_".date("Ymd").".log";
$iParamLog = 0;
$nomeTabelaPrincipal = "integra_infisc.integra_debitos";
$validarTabela = false;

require_once("infisc_processamento_integra_debitos_header.php");

$sCaminhoScript = getcwd();
chdir('../../');

require_once("classes/db_issvar_classe.php");
require_once("classes/db_issvar_infisc_integra_debitos_classe.php");

chdir($sCaminhoScript);

/*
######################################################################################################
#                     INICIA O PROCESSAMENTOS DOS DADOS A PARTIR DAQUI                               #
######################################################################################################
*/

db_putsession('DB_acessado', '1');
db_putsession('DB_datausu', time());
db_putsession('DB_anousu', date('Y',time()));
db_putsession('DB_coddepto', '24');
db_putsession('DB_instit', '1');

const QUANTIDADE_ITEMS_PROCESSAR = 1000;

$debitos = $destinoManager->query(
    $nomeTabelaPrincipal,
    "*",
    [sprintf("status_processamento = '%s'", StatusProcessamentoEnum::PENDENTE)],
    QUANTIDADE_ITEMS_PROCESSAR,
    "sequencial ASC"
)->getCollection();

$integraDebitosProcessamentoService = new IntegraDebitosProcessamentoService($origemManager, $destinoManager);
$integraDebitosProcessamentoService->setNomeTabelaPrincipal($nomeTabelaPrincipal);

foreach ($debitos as $debito) {
    $origemManager->begin()->runRawQuery("select fc_startsession();")->runRawQuery("select fc_putsession('DB_instit', '1')");
    $destinoManager->begin()->runRawQuery("SET client_encoding = 'UTF8';");

    try {
        $integraDebitosProcessamentoService->setDados($debito)->processar();

        $integraDebitosProcessamentoService->atualizaStatusProcessamento(StatusProcessamentoEnum::PROCESSADO);

        $origemManager->commit();
        $destinoManager->commit();
    } catch (Exception $exception) {
        $origemManager->rollback();
        $destinoManager->rollback();

        $integraDebitosProcessamentoService->atualizaStatusProcessamento(StatusProcessamentoEnum::ERRO, utf8_encode($exception->getMessage()));

        db_log(
            "Erro ao processar o debito [sequencial: {$debito->sequencial} | erro: {$exception->getMessage()}]",
            $sArquivoLog,
            $iParamLog
        );
    }
}
