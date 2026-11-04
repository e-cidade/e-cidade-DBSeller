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

use App\Domain\Financeiro\Empenho\Repositories\TipoServicoObraRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson               = JSON::create();
$oRetorno            = new stdClass();
$oParam              = $oJson->parse(str_replace("\\","", $_POST["json"]));
$oRetorno->erro      = false;

try {
    switch ($oParam->exec) {
        case 'getLabels':
            $labels = TipoServicoObraRepository::labels();
            $oRetorno->labels = $labels;
            break;

        case 'getByEmpenho':
            if ($oParam->numemp) {
                $tipoServicoObra = TipoServicoObraRepository::findByEmpenho($oParam->numemp);

                if ($tipoServicoObra) {
                    $tipoServicoObra = $tipoServicoObra->toArray();
                }

                $oRetorno->tipoServicoObra = $tipoServicoObra;
            }
            break;
    }

} catch (Exception $e) {
    $oRetorno->erro    = true;
    $oRetorno->message = $e->getMessage();
}

echo $oJson->stringify($oRetorno);
