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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_app.utils.php");

$oDaoCertLancImov = new cl_certlancimov();
$oDaoUsuarios     = new cl_db_usuarios();
$oDaoProtProcesso = new cl_protprocesso();

try {
    if (!$_POST["processoDoSistema"] || $_POST["processoDoSistema"] == '') {
        throw new Error('Processo do sistema inválido');
    }

    if (!$_POST["matricula"] || $_POST["matricula"] == '') {
        throw new Error('Matrícula inválida');
    }

    if ((!$_POST["z01_nomeprocesso"] || $_POST["z01_nomeprocesso"] == '')
        && (!$_POST["titularProcesso"] || $_POST["titularProcesso"] == '')
    ) {
        throw new Error('Titular do processo inválido');
    }

    if ((!$_POST["p58_codproc"] || $_POST["p58_codproc"] == '')
        && (!$_POST["numeroProcesso"] || $_POST["numeroProcesso"] == '')
    ) {
        throw new Error('Número processo inválido');
    }

    /**
     * Pode ser enviado para insercao na tabela o id e o nome tanto de um processo que ja existe
     * quanto de um processo que nao exista no banco de dados
     */
    $dataEmissao       = date("Y-m-d", db_getsession("DB_datausu"));
    $idUsuario         = db_getsession('DB_id_usuario');
    $z01_nomeprocesso  = isset($_POST["z01_nomeprocesso"])  ? $_POST["z01_nomeprocesso"]  : '';
    $p58_codproc       = isset($_POST["p58_codproc"])       ? $_POST["p58_codproc"]       : '';
    $processoDoSistema = isset($_POST["processoDoSistema"]) ? $_POST["processoDoSistema"] : '';
    $numeroProcesso    = isset($_POST["numeroProcesso"])    ? $_POST["numeroProcesso"]    : '';
    $titularProcesso   = isset($_POST["titularProcesso"])   ? $_POST["titularProcesso"]   : '';
    $observacao        = isset($_POST["observacao"])        ? $_POST["observacao"]        : '';
    $matricula         = isset($_POST["matricula"])         ? $_POST["matricula"]         : '';

    db_inicio_transacao();

    $nextValCertLancImov               = $oDaoCertLancImov->nextValTabela();
    $oDaoCertLancImov->j176_sequencial = $nextValCertLancImov;
    $oDaoCertLancImov->j176_usuario    = $idUsuario;
    $oDaoCertLancImov->j176_matricula  = $matricula;
    $oDaoCertLancImov->j176_emissao    = $dataEmissao;
    $oDaoCertLancImov->j176_observacao = addslashes($observacao);

    /**
     * Definicao da inclusao de acordo com o tipo de processo:
     * 1 - processo que ja existe no sistema
     * 2 - processo que nao existe no sistema
     */
    switch ($processoDoSistema) {
        case (1):
            $oDaoCertLancImov->j176_processo = $p58_codproc;
            $oDaoCertLancImov->j176_titular  = addslashes(strtoupper($z01_nomeprocesso));
            $oDaoCertLancImov->incluir($nextValCertLancImov);
            break;

        case (2):
            $oDaoCertLancImov->j176_processo = $numeroProcesso;
            $oDaoCertLancImov->j176_titular  = addslashes(strtoupper($titularProcesso));
            $oDaoCertLancImov->incluir($nextValCertLancImov);
            break;

        default:
            break;
    }

    if ($oDaoCertLancImov->erro_status && $oDaoCertLancImov->erro_status == '0') {
        echo "<script>alert('" . $oDaoCertLancImov->erro_msg . "')</script>";
        db_fim_transacao(true);
    } else {
        db_fim_transacao(false);
    }
} catch (Exception $error) {
    db_fim_transacao(true);

    echo "<script>alert('" . $error->getMessage() . "');</script>";
    echo "<script>location.href = 'cad3_conscertidoes001.php?abaSelecionada=cad3_conscertlanc001.php&matricula=" . $matricula . "';</script>";
}

echo "<script>location.href = 'cad3_conscertlanc003.php?matricula=" . $matricula . "';</script>";
