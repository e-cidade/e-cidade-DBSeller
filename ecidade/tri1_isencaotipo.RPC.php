<?php

/**
 * E-cidade Software Público para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

db_postmemory($_POST);

$oJson                   = new services_json();
$oParam                  = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno                = new stdClass();
$oRetorno->status        = 1;
$oRetorno->message       = '';

switch ($oParam->exec) {
    case 'buscacalculos':
        $oDaoCadCalc = new cl_cadcalc;
        try {
            $sSql = $oDaoCadCalc->sql_query(null, 'q85_codigo, q85_descr', 'q85_codigo');

            $execute = db_query($sSql);
            $oDados = [];

            for ($i = 0; $i < pg_num_rows($execute); $i++) {
                $result = db_utils::fieldsMemory($execute, $i);
                $result->q85_descr = utf8_encode($result->q85_descr);
                array_push($oDados, $result);
            }

            $oRetorno->dados = $oDados;
        } catch (Exception $oException) {
            $oRetorno->message = $oException->getMessage();
            $oRetorno->status  = 2;
        }
}

$oRetorno->message = urlencode($oRetorno->message);
echo $oJson->encode($oRetorno);
