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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_lab_coletaitem_classe.php"));
require_once(modification("libs/db_stdlibwebseller.php"));

use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Model\ColetaItem as ModelColetaItem;
use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Relatorio\Modelo3;
use ECidade\Saude\Laboratorio\Service\ColetaItemService;
use ECidade\Saude\Laboratorio\Repository\ColetaItemRepository;
use ECidade\Saude\Laboratorio\Model\ColetaItem;

try {
    $get = db_utils::postMemory($_GET);

    if (empty($get->requisicao)) {
        throw new Exception('Requisição não informada.');
    }

    if (empty($get->sLista)) {
        throw new Exception('Exames não informados.');
    }

    $exames = explode(',', $get->sLista);

    $requisicaoLaboratorial = new RequisicaoLaboratorial($get->requisicao);

    $pdf = new ECidade\Pdf\Pdf('L', 'mm');

    $modelo3 = new Modelo3($requisicaoLaboratorial, $pdf);

    foreach ($exames as $codigoExame) {
        $coletaItemService = new ColetaItemService(new ColetaItemRepository(new cl_lab_coletaitem()));
        $itemColeta = $coletaItemService->buscar($codigoExame);
        $modelo3->adicionarExame($codigoExame, $itemColeta[0]['la32_d_data']);
    }

    $modelo3->imprimir();
} catch (Exception $erro) {
    db_redireciona("db_erros.php?fechar=true&db_erro=" . $erro->getMessage());
}
