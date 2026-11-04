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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("model/webservices/ControleAcessoAluno.model.php"));

$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = "";
$iCodigoEscola = db_getsession("DB_coddepto");

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
db_inicio_transacao();

$dao = new cl_escolagestorcenso();
try {
    switch ($oParam->exec) {

        case 'getGestorEscolaCenso':
            $sCampos = " case when cgmrh.z01_nome is not null";
            $sCampos .= "        then cgmrh.z01_nome";
            $sCampos .= "        else cgmcgm.z01_nome";
            $sCampos .= " end as z01_nome,";
            $sCampos .= " ed325_sequencial,";
            $sCampos .= " ed20_i_codigo,";
            $sCampos .= " case when cgmrh.z01_cgccpf is not null";
            $sCampos .= "        then cgmrh.z01_cgccpf";
            $sCampos .= "        else cgmcgm.z01_cgccpf";
            $sCampos .= " end as z01_cgccpf,";
            $sCampos .= " ed325_email, ed325_rechumano";

            $sSqlGestorEscola = $dao->sql_query_dados_gestor(
                null,
                $sCampos,
                "z01_nome",
                "ed325_escola = {$iCodigoEscola}"
            );

            $rsDadosGestorCenso = $dao->sql_record($sSqlGestorEscola);
            $oRetorno->gestores = array();
            if ($dao->numrows > 0) {
                $oRetorno->gestores = db_utils::getCollectionByRecord($rsDadosGestorCenso, false, false, true);
            }

            break;
        case 'salvarGestorEscola':
            if (empty($oParam->sequencial)) {
                $sql = $dao->sql_query_file(null, "count(*)", null, " ed325_escola = $iCodigoEscola");
                $rs = db_query($sql);

                if (!$rs) {
                    throw new Exception("Erro ao validar limite de gestores.");
                }
                if (db_utils::fieldsMemory($rs, 0)->count == 3) {
                    throw new Exception("Não é possível ter mais de 3 gestores.");
                }

                $sql = $dao->sql_query_file(null, "count(*)", null, " ed325_escola = $iCodigoEscola AND ed325_rechumano = " . $oParam->iRecHumano);
                $rs = db_query($sql);
                
                if (db_utils::fieldsMemory($rs, 0)->count > 0) {
                    throw new Exception("Gestor já cadastrado.");
                }
            }

            $dao->ed325_escola = $iCodigoEscola;
            $dao->ed325_rechumano = $oParam->iRecHumano;
            $dao->ed325_email = db_stdClass::normalizeStringJsonEscapeString($oParam->sEmail);

            if (empty($oParam->sequencial)) {
                $dao->incluir(null);
                $sInternalMessage = "Gestor cadastrado com sucesso.";
            } else {
                $dao->ed325_sequencial = $oParam->sequencial;
                $dao->alterar($oParam->sequencial);
                $sInternalMessage = "Gestor editado com sucesso.";
            }

            if ($dao->erro_status == 0) {
                throw new Exception("Erro ao salvar o gestor.");
            }

            $oRetorno->sequencial = $dao->ed325_sequencial;
            $oRetorno->sMessage = urlencode($sInternalMessage);
            break;
        case 'excluir':

            if (empty($oParam->codigo)) {
                throw new Exception("Informe o gestor a ser excluido.");
            }

            $dao->excluir($oParam->codigo);
            if ($dao->erro_status == 0) {
                throw new Exception("Erro ao excluir gestor.");
            }

            $oRetorno->sMessage = urlencode("Gestor excluido com sucesso.");
            break;
    }
    db_fim_transacao(false);
} catch (Exception $eErro) {

    db_fim_transacao(true);
    $oRetorno->status = 2;
    $oRetorno->sMessage = urlencode($eErro->getMessage());
}

echo $oJson->encode($oRetorno);
