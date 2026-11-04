<?php
/**
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

use ECidade\Financeiro\Contabilidade\LancamentoContabil\LancamentosAjusteDDR;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_contacorrenteatributos.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson = new services_json();
$oParametros = $parametros = JSON::requestParameters();
$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->mensagem = '';

try {
    db_inicio_transacao();

    switch ($oParametros->exec) {
        case 'salvarLancamentos':
            $service = new LancamentosAjusteDDR($oParametros->DB_instit, $oParametros->DB_anousu, $oParametros->data_usuario);

            foreach ($oParametros->recursos as $recursoArrumar) {
                $recursoArrumar = JSON::create()->parse($recursoArrumar);
                $service->processarLancamentos($recursoArrumar);
            }

            $oRetorno->mensagem = "Lançamento(s) feito(s) com sucesso.";
            break;
    }
} catch (Exception $oErro) {
    $oRetorno->mensagem = $oErro->getMessage();
}
db_fim_transacao($oRetorno->erro);

echo Json::create()->stringify($oRetorno);
