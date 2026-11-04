<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\RecursosHumanos\ESocial\Model\Configuracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

$oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->mensagem = '';

try {
    switch ($oParam->exec) {
        case 'buscarAvaliacao':
            if (empty($oParam->matricula) && !$oParam->servidorSemRegistro) {
                throw new BusinessException("Matricula não informada.");
            }
            $configuracao = new Configuracao();
            $formularioId = $configuracao->getFormulario(Tipo::ADMISSAO_PRELIMINAR);

            $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo($formularioId);
            $avaliacaoEsocial = new AvaliacaoEsocialAdapter($avaliacao);
            if (!empty($oParam->preenchimento)) {
                $avaliacao->setAvaliacaoGrupo($oParam->preenchimento);
                $avaliacaoEsocial = new AvaliacaoEsocialAdapter($avaliacao);
                $avaliacaoEsocial->setCodigoGrupoResposta($oParam->preenchimento);
            } else if (!empty($oParam->matricula)){

                $sqlPreenchimento  = "select eso18_avaliacaogruporesposta from avaliacaogruporespostaadmissaopreliminar ";
                $sqlPreenchimento .= "where eso18_cgm = {$oParam->iCGM} and eso18_regist = {$oParam->matricula}";
                
                $resourcePreenchimento = db_query($sqlPreenchimento);
                $result = pg_fetch_row($resourcePreenchimento)[0];

                $avaliacao->setAvaliacaoGrupo($result);
                $avaliacaoEsocial = new AvaliacaoEsocialAdapter($avaliacao);
                $avaliacaoEsocial->setCodigoGrupoResposta($result);
            }
            
            $avaliacaoEsocial->setCgm(CgmFactory::getInstanceByCgm($oParam->iCGM));
            $avaliacaoEsocial->setAdmissaoPreliminar(true);

            $oRetorno->oFormulario = $avaliacaoEsocial->getObject();
            break;

        case 'salvarAvaliacao':
            if (empty($oParam->iCGM)) {
                throw new BusinessException("Cgm não informado.");
            }

            $oAvaliacao = AvaliacaoRepository::getAvaliacaoByCodigo($oParam->iCodigoAvaliacao);
            $oAvaliacao->setAvaliacaoGrupo();

            $iCodigoGrupoPerguntas = null;
            $aProcesso             = array();

            if (!empty($oParam->iCodigoGrupoPerguntas)) {
                $iCodigoGrupoPerguntas = $oParam->iCodigoGrupoPerguntas;
            }

            /**
             * Flag para criar um servidor fake, e permitir o processamento.
             */
            $servidorNaoCadastrado = false;

            /**
             * Adaptação para preencher a matricula
             * de acordo com o preenchimento de tela.
             */
            if (empty($oParam->matricula)) {
                $array = $oParam->aPerguntasRespostas->grupos[0]->perguntas;
                for ($i = 0; $i < sizeof($array); $i++) {
                    if ($array[$i]->codigo == 4000299 && (!empty($array[$i]->respostas))) {
                        $oParam->matricula = $array[$i]->respostas[0]->valor;
                        $servidorNaoCadastrado = true;
                    }
                }
            }
            
            $oServidor = new Servidor();
            
            if ($servidorNaoCadastrado) {
                $oServidor->setMatricula($oParam->matricula);
            } else {
                $oServidor = ServidorRepository::getInstanciaByCodigo($oParam->matricula);
            }

            $oAvaliacaoESocial = new AvaliacaoESocial();
            $oAvaliacaoESocial->setAvaliacao($oAvaliacao);
            $oAvaliacaoESocial->setServidor($oServidor);
            $oAvaliacaoESocial->setCgm(CgmFactory::getInstanceByCgm($oParam->iCGM));
            $oAvaliacaoESocial->setPerguntasRespostas($oParam->aPerguntasRespostas);
            $oAvaliacaoESocial->salvar($iCodigoGrupoPerguntas, "admissaoPreliminar", (array) $oParam);

            // Pega a Avaliacao salva
            $oAvaliacao  = $oAvaliacaoESocial->getAvaliacao();
            // Seta o preenchimento
            $oRetorno->preenchimento = $oAvaliacao->getAvaliacaoGrupo();
            $oRetorno->mensagem      = "Avaliação salva com sucesso.";
            break;
    }
} catch (Exception $e) {
    if (db_utils::inTransaction()) {
        db_fim_transacao(true);
    }

    $oRetorno->erro = true;
    $oRetorno->mensagem = $e->getMessage();
}

echo JSON::create()->stringify($oRetorno);
