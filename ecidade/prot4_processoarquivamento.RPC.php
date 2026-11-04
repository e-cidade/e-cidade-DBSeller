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

require_once(modification("libs/JSON.php"));
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

$clprocarquiv  = new cl_procarquiv;
$cldb_usuarios = new cl_db_usuarios;

$oJson             = new services_json();
$oParam            = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno          = new stdClass();
$oRetorno->erro    = false;
$oRetorno->message = '';

switch ($oParam->exec) {
    case 'getDadosArquivamento':
        $sCampos = "p67_id_usuario, p67_historico, p67_dtarq";
        $sSqlArquivamento = $clprocarquiv->sql_query($oParam->codigo_arquivamento, $sCampos);
        $rsArquivamento   = $clprocarquiv->sql_record($sSqlArquivamento);

        $oDadosArquivamento = db_utils::fieldsMemory($rsArquivamento, 0);

        $sSqlUsuario = $cldb_usuarios->sql_query($oDadosArquivamento->p67_id_usuario, "nome");
        $rsUsuario   = $cldb_usuarios->sql_record($sSqlUsuario);

        $oDadosUsuario = db_utils::fieldsMemory($rsUsuario, 0);

        if (!$rsArquivamento || $clprocarquiv->numrows == 0) {
            $oRetorno->erro = true;
            $message = "Não foi possível encontrar dados do Arquivamento.";
            $oRetorno->message = mb_convert_encoding($message, 'UTF-8', 'ISO-8859-1');
        } else {
            $data = new DBDate($oDadosArquivamento->p67_dtarq);
            $oRetorno->data      = $data->getDate(DBDate::DATA_PTBR);
            $oRetorno->usuario   = mb_convert_encoding($oDadosUsuario->nome, 'UTF-8', 'ISO-8859-1');
            $oRetorno->historico = mb_convert_encoding($oDadosArquivamento->p67_historico, 'UTF-8', 'ISO-8859-1');
        }

        break;
}

echo $oJson->encode($oRetorno);
