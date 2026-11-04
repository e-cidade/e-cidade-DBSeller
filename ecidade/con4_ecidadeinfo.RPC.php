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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");

$parametro = JSON::requestParameters();
$retorno = new stdClass();
$retorno->status = false;
$retorno->mensagem = '';

try {
    db_inicio_transacao();

    switch ($parametro->acao) {

        case "info":
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            $departamento = DBDepartamentoRepository::getDepartamentoSessao();
            $usuario = UsuarioSistemaRepository::getUsuarioSessao();

            $retorno->instituicao = $instituicao->toArray();
            $retorno->departamento = $departamento->toArray();
            $retorno->usuario = $usuario->toArray();
            $retorno->url = ECIDADE_REQUEST_PATH;
            break;
        case 'session':
            $retorno->session[] = sessionToObject('DB_login');
            $retorno->session[] = sessionToObject('DB_instit');
            $retorno->session[] = sessionToObject('DB_coddepto');
            $retorno->session[] = sessionToObject('DB_id_usuario');
            $retorno->session[] = sessionToObject('DB_Area');
            $retorno->session[] = sessionToObject('DB_modulo');
            $retorno->session[] = sessionToObject('DB_acessado');
            $retorno->session[] = sessionToObject('DB_itemmenu_acessado');
            $retorno->session[] = sessionToObject('DB_ano_pcasp', 2013);
            $retorno->session[] = sessionToObject('DB_datausu');
            $retorno->session[] = sessionToObject('DB_uol_hora');
            $retorno->session[] = sessionToObject('DB_anousu');
            $retorno->session[] = sessionToObject('data_usuario', date('Y-m-d', $_SESSION['DB_datausu']));
            $retorno->requestPath = ECIDADE_REQUEST_PATH;
            $retorno->requestApi = ECIDADE_REQUEST_PATH . 'v4/api';
            $retorno->UF = InstituicaoRepository::getInstituicaoPrefeitura()->getUf();
            break;
    }

    db_fim_transacao(false);

} catch (Exception $erro) {

    db_fim_transacao(true);

    $retorno->erro = true;
    $retorno->mensagem = $erro->getMessage();
}

echo JSON::create()->stringify($retorno);

function sessionToObject($index, $defaultValue = null)
{
    $value = array_key_exists($index, $_SESSION) ? $_SESSION[$index] : $defaultValue;

    return (object) [
        'name' => $index,
        'value' => $value
    ];
}
