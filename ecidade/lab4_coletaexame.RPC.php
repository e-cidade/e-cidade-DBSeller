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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Library\File\File;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Collection\TagsXML as TagsXMLCollection;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Converter\Pedido;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Enum\Parametros as ParametrosEnum;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\IntegraPedidos;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Parametros as ParametrosService;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\Pedido as PedidoService;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Service\TagXML;
use ECidade\Saude\Laboratorio\Model\Parametros as ParametrosLaboratorio;
use ECidade\Saude\Laboratorio\Repository\LaboratorioRepository;
use ECidade\Saude\Laboratorio\Service\LaboratorioService;

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMensagem = '';

define("MENSAGENS_COLETAEXAME_RPC", "saude.laboratorio.lab4_coletaexame_RPC.");

try {
    db_inicio_transacao();

    switch ($oParam->exec) {

        /**
         * ************************************************************
         * Salva os registros na tabelas lab_coletaitem e lab_requiitem
         * @param string $oParam ->dtEntrega
         *        string  $oParam->sHoraEntrega
         *        integer $oParam->iAvisaPaciente
         *        array   $oParam->aRequisicoesItem
         *        boolean $oParam->lFalta
         * ************************************************************
         */
        case 'salvar':
            db_inicio_transacao();

            $oDaoLabColetaItem = new cl_lab_coletaitem();
            $oDaoLabRequiItem = new cl_lab_requiitem();

            /**
             * Cria instancia de DBDate da data de entrega, caso estas tenha sido setada
             */
            $oDataEntrega = !empty($oParam->dtEntrega) ? new DBDate($oParam->dtEntrega) : "";
            $sDataEntrega = $oDataEntrega instanceof DBDate ? $oDataEntrega->getDate() : "";

            /**
             * Seta as propriedades da classe com os dados padrões a serem salvos
             */
            $oDaoLabColetaItem->la32_d_data = date('Y-m-d', db_getsession('DB_datausu'));
            $oDaoLabColetaItem->la32_c_hora = date('H:i');
            $oDaoLabColetaItem->la32_d_entrega = $sDataEntrega;
            $oDaoLabColetaItem->la32_c_horaentrega = $oParam->sHoraEntrega;
            $oDaoLabColetaItem->la32_i_avisapaciente = $oParam->iAvisaPaciente == 1 ? 1 : 2;
            $oDaoLabColetaItem->la32_i_usuario = db_getsession("DB_id_usuario");

            /**
             * Verifica se os exames estão autorizados
             */
            foreach ($oParam->aSituacaoRequisicao as $situacao) {
                if ($situacao != '20 - Autorizado' && $situacao != 'f - falta material') {
                    throw new DBException('Apenas exames com a situação "Autorizado" ou "falta de material" podem realizar este procedimento.');
                }
            }

            /**
             * Percorre os itens da requisição para salvá-los na tabela lab_coletaitem
             */
            foreach ($oParam->aItemRequisicao as $iRequisicaoItem) {
                if (!$oParam->lFalta) {
                    $oDaoLabColetaItem->la32_i_requiitem = $iRequisicaoItem;
                    $oDaoLabColetaItem->incluir(null);

                    if ($oDaoLabColetaItem->erro_status == "0") {
                        $oMensagem = new stdClass();
                        $oMensagem->sErro = $oDaoLabColetaItem->erro_msg;
                        throw new DBException(_M(MENSAGENS_COLETAEXAME_RPC . "erro_incluir_coletaitem", $oMensagem));
                    }
                }

                /**
                 * Caso os dados na tabela lab_coletaitem tenham sido salvos, salva os registros em lab_requiitem
                 */
                $oDaoLabRequiItem->la21_c_situacao = "30 - Coletado";
                if (isset($oParam->lFalta) && $oParam->lFalta) {
                    $oDaoLabRequiItem->la21_c_situacao = "f - falta material";
                }

                $oDaoLabRequiItem->la21_i_codigo = $iRequisicaoItem;
                $oDaoLabRequiItem->alterar($iRequisicaoItem);

                if ($oDaoLabRequiItem->erro_status == "0") {
                    $oMensagem = new stdClass();
                    $oMensagem->sErro = $oDaoLabRequiItem->erro_msg;
                    throw new DBException(_M(MENSAGENS_COLETAEXAME_RPC . "erro_incluir_requiitem", $oMensagem));
                }
            }

            $oRetorno->sMensagem = urlencode(_M(MENSAGENS_COLETAEXAME_RPC . "exames_salvos"));
            
            db_fim_transacao();

            break;

        case 'salvarDadosInfinity':
            db_inicio_transacao();

            $oDaoLabColetaItem = new cl_lab_coletaitem();
            $oDaoLabRequiItem = new cl_lab_requiitem();

            /**
             * Cria instancia de DBDate da data de entrega, caso estas tenha sido setada
             */
            $oDataEntrega = !empty($oParam->dtEntrega) ? new DBDate($oParam->dtEntrega) : "";
            $sDataEntrega = $oDataEntrega instanceof DBDate ? $oDataEntrega->getDate() : "";

            /**
             * Seta as propriedades da classe com os dados padrões a serem salvos
             */
            $oDaoLabColetaItem->la32_d_data = date('Y-m-d', db_getsession('DB_datausu'));
            $oDaoLabColetaItem->la32_c_hora = date('H:i');
            $oDaoLabColetaItem->la32_d_entrega = $sDataEntrega;
            $oDaoLabColetaItem->la32_c_horaentrega = $oParam->sHoraEntrega;
            $oDaoLabColetaItem->la32_i_avisapaciente = $oParam->iAvisaPaciente == 1 ? 1 : 2;
            $oDaoLabColetaItem->la32_i_usuario = db_getsession("DB_id_usuario");

            /**
             * Verifica se os exames estão autorizados
             */
            foreach ($oParam->aSituacaoRequisicao as $situacao) {
                if ($situacao != '20 - Autorizado' && $situacao != 'f - falta material') {
                    throw new DBException('Apenas exames com a situação "Autorizado" ou "falta de material" podem realizar este procedimento.');
                }
            }

            /**
             * Percorre os itens da requisição para salvá-los na tabela lab_coletaitem
             */
            foreach ($oParam->itensRequisicaoInfinity as $iRequisicaoItem) {
                if (!$oParam->lFalta) {
                    $oDaoLabColetaItem->la32_i_requiitem = $iRequisicaoItem;
                    $oDaoLabColetaItem->incluir(null);

                    if ($oDaoLabColetaItem->erro_status == "0") {
                        $oMensagem = new stdClass();
                        $oMensagem->sErro = $oDaoLabColetaItem->erro_msg;
                        throw new DBException(_M(MENSAGENS_COLETAEXAME_RPC . "erro_incluir_coletaitem", $oMensagem));
                    }
                }

                /**
                 * Caso os dados na tabela lab_coletaitem tenham sido salvos, salva os registros em lab_requiitem
                 */
                $oDaoLabRequiItem->la21_c_situacao = "30 - Coletado";
                if (isset($oParam->lFalta) && $oParam->lFalta) {
                    $oDaoLabRequiItem->la21_c_situacao = "f - falta material";
                }

                $oDaoLabRequiItem->la21_i_codigo = $iRequisicaoItem;
                $oDaoLabRequiItem->alterar($iRequisicaoItem);

                if ($oDaoLabRequiItem->erro_status == "0") {
                    $oMensagem = new stdClass();
                    $oMensagem->sErro = $oDaoLabRequiItem->erro_msg;
                    throw new DBException(_M(MENSAGENS_COLETAEXAME_RPC . "erro_incluir_requiitem", $oMensagem));
                }
            }

            $oRetorno->sMensagem = urlencode("Exames salvos com sucesso.");

            db_fim_transacao();

            break;
    }
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMensagem = urlencode(str_replace("\\n", "\n", $eErro->getMessage()));
}

echo $oJson->encode($oRetorno);
