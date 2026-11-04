<?php

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_licitaparam_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

use App\Domain\Patrimonial\Licitacoes\Services\LicitaconService;

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$oDaoLicitaparam = new cl_licitaparam;
$oDaoDbconfig = new cl_db_config;
$db_opcao    = 22;
$db_botao    = false;
$sPosScripts = "";

if (isset($alterar)) {
    db_inicio_transacao();
    $result = $oDaoLicitaparam->sql_record($oDaoLicitaparam->sql_query(DB_getsession("DB_instit")));
    $error = false;

    if ($l12_validalicitacon > 1) {
        $rsDbconfig = $oDaoDbconfig->sql_record($oDaoDbconfig->sql_query_file(DB_getsession("DB_instit"), "cgc"));
        db_fieldsmemory($rsDbconfig, 0);
        try {
            $licitaconService = new LicitaconService();
            $orgao = $licitaconService->verificarOrgao($cgc);
            if (!$orgao) {
                db_msgbox("Não foi possível identificar o código do órgão para a instituição.");
                db_fim_transacao(true);
                $error = true;
            }
        } catch (Exception $exception) {
            db_msgbox("Variável LICITACON_URL não configurada ou incorreta, verifique.");
            db_fim_transacao(true);
            $error = true;
        }
    }

    if (!$error) {
        if ($result == false || $oDaoLicitaparam->numrows == 0) {
            $oDaoLicitaparam->incluir(DB_getsession("DB_instit"));
        } else {
            $oDaoLicitaparam->alterar(DB_getsession("DB_instit"));
        }
        db_fim_transacao();

        $sPosScripts .= 'alert("' . $oDaoLicitaparam->erro_msg . '");' . "\n";

        if ($oDaoLicitaparam->erro_status == "0") {
            $db_botao = true;
            $sPosScripts .= "document.form1.db_opcao.disabled = false;\n";

            if ($oDaoLicitaparam->erro_campo != "") {
                $sPosScripts .= "document.form1.{$oDaoLicitaparam->erro_campo}.classList.add('form-error');";
                $sPosScripts .= "document.form1.{$oDaoLicitaparam->erro_campo}.focus();";
            }
        } else {
            $sPosScripts .= "location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "';\n";
        }
    }
}

$db_opcao = 2;
$db_botao = true;
$result   = $oDaoLicitaparam->sql_record($oDaoLicitaparam->sql_query(DB_getsession("DB_instit")));

if ($result != false && $oDaoLicitaparam->numrows > 0) {
    db_fieldsmemory($result, 0);
}

if ($db_opcao == 22) {
    $sPosScripts .= "document.form1.pesquisar.click();\n";
}

$sPosScripts .=  'js_tabulacaoforms("form1", "l12_escolherprocesso", true, 1, "l12_escolherprocesso", true);';

include(modification("forms/db_frmlicitaparam.php"));
