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
require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('libs/db_liborcamento.php'));
require_once(modification('libs/db_libcontabilidade.php'));
require_once(modification('dbforms/db_funcoes.php'));
require_once(modification('model/relatorioContabil.model.php'));

$app = require_once ECIDADE_PATH . 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoXIII as FactoryAnexo;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\Layout\AnexoXIII as FactoryLayout;

try {
    $get = (object)filter_input_array(INPUT_GET);
    $ano = db_getsession('DB_anousu');

    if (empty($get->periodo)) {
        throw new Exception(_M('financeiro.contabilidade.con2_emissaoAnexoVII.periodo_invalido'));
    }

    $instituicao = $get->insituicoes && $ano > 2017 ? $get->insituicoes : db_getsession('DB_instit');

    $relatorio = FactoryAnexo::getInstance($ano, $get->periodo);
    $relatorio->setInstituicoes($instituicao);

    $layout = FactoryLayout::getInstance($ano);
    $layout->definirRelatorio($relatorio);
    $layout->imprimir();
} catch (Exception $exception) {
    db_redireciona('db_erros.php?fechar=true&db_erro=' . urlencode($exception->getMessage()));
    exit(402);
}
