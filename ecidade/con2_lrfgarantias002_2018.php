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


require_once(modification("fpdf151/pdf.php"));
require_once(modification("fpdf151/assinatura.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));

$oGet = db_utils::postMemory($_GET);
$ano = db_getsession('DB_anousu');

use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoIII;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Layout\AnexoIII as Layout;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\ProcessamentoRelatorioLegal;

$app = require_once ECIDADE_PATH . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    if (db_getsession('DB_id_usuario') != 1) {
        if (!ProcessamentoRelatorioLegal::verificarAnexoLiberado(ProcessamentoRelatorioLegal::ANEXO_III)) {
            db_redireciona('db_erros.php?db_erro='. urlencode("O relatório não está disponível para o exercício {$anousu}. Aguarde a liberação."));
        }
    }

    if (empty($oGet->periodo)) {
        throw new Exception("Informe o período.");
    }

    $periodo = new Periodo($oGet->periodo);
    $oAnexo = AnexoIII::getInstance($ano, $periodo);

    $oLayout = new Layout($ano, $periodo);
    $oLayout->setAnexo($oAnexo);
    $oLayout->emitir();
} catch (Exception $e) {
    db_redireciona('db_erros.php?fechar=true&db_erro=' . urlencode($e->getMessage()));
}
