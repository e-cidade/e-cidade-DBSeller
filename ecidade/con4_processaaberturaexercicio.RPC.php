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
use ECidade\Configuracao\Consistencia\Repository\Consistencia as ConsistenciaEncerramento;
use ECidade\Financeiro\Contabilidade\Encerramento\Exercicio\Encerramento;
use ECidade\Financeiro\Contabilidade\ExercicioContabil\Abertura;

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_libcontabilidade.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("classes/lancamentoContabil.model.php");

$oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->mensagem = '';
db_putsession("DB_desativar_account", true);
$rsDesabilitarAuditoria = db_query("SELECT fc_putsession('__disable_audit__', 'on');");

$oData = new DBDate($oParam->data);
$data = $oParam->data;

try {
    db_inicio_transacao();

    switch ($oParam->exec) {
        case "processarAbertura":
            $abertura = new Abertura(
                db_getsession("DB_anousu"),
                new DBDate($data),
                InstituicaoRepository::getInstituicaoSessao()
            );

            $documentosJaProcessados = $abertura->validaDocumentosProcessados($oParam->documentos);
            if (!empty($documentosJaProcessados)) {
                throw new Exception(
                    'O(s) documento(s) já foram processados. '. implode(', ', $documentosJaProcessados)
                );
            }

            $abertura->processar($oParam->documentos);
            $oRetorno->mensagem = "A abertura foi processada com sucesso.";
            $oRetorno->documentosEncerrados = $oParam->documentos;
            break;

        case "cancelarAbertura":
            $abertura = new Abertura(
                db_getsession("DB_anousu"),
                new DBDate($data),
                InstituicaoRepository::getInstituicaoSessao()
            );
            $abertura->cancelar($oParam->documentos);
            $oRetorno->mensagem = "A abertura foi cancelada com sucesso.";
            break;
        case 'validaSituacaoDocumentos':
            $where = " c149_anousu = ".  db_getsession("DB_anousu");
            $where .= " and  c149_instit = " .db_getsession("DB_instit");
            $dao = new cl_aberturaexerciciodocumento();
            $sql = $dao->sql_query_file(null, 'c149_documento', null, $where);

            $rs = db_query($sql);

            $oRetorno->documentosEncerrados = [];
            if ($rs && pg_num_rows($rs) > 0) {
                $oRetorno->documentosEncerrados = db_utils::makeCollectionFromRecord($rs, function ($dado) {
                   return $dado->c149_documento;
                });
            }

            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);

    $oRetorno->erro = true;
    $oRetorno->mensagem = urlencode($eErro->getMessage());
}
unset($_SESSION["DB_desativar_account"]);
$rsDesabilitarAuditoria = db_query("SELECT fc_putsession('__disable_audit__', 'off');");
echo JSON::create()->stringify($oRetorno);
