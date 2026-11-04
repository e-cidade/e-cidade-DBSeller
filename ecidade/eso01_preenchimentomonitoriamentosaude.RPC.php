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
            if (empty($oParam->iCGM)) {
                throw new BusinessException("Cgm não informado.");
            }
            $configuracao = new Configuracao();
            $formularioId = $configuracao->getFormulario(Tipo::MONITORAMENTO_SAUDE);

            $avaliacao = AvaliacaoRepository::getAvaliacaoByCodigo($formularioId);
            $avaliacaoEsocial = new AvaliacaoEsocialAdapter($avaliacao);
            if (!empty($oParam->preenchimento)) {
                $avaliacao->setAvaliacaoGrupo($oParam->preenchimento);
                $avaliacaoEsocial = new AvaliacaoEsocialAdapter($avaliacao);
                $avaliacaoEsocial->setCodigoGrupoResposta($oParam->preenchimento);
            }

            $avaliacaoEsocial->setCgm(CgmFactory::getInstanceByCgm($oParam->iCGM));
            $avaliacaoEsocial->setCat(true);
            $oRetorno->oFormulario = $avaliacaoEsocial->getObject();
            break;

        case 'salvarAvaliacao':
            if (empty($oParam->iCGM)) {
                throw new BusinessException("Cgm não informado.");
            }
            $oAvaliacao = AvaliacaoRepository::getAvaliacaoByCodigo($oParam->iCodigoAvaliacao);
            $oAvaliacao->setAvaliacaoGrupo();

            $iCodigoGrupoPerguntas = null;

            if (!empty($oParam->iCodigoGrupoPerguntas)) {
                $iCodigoGrupoPerguntas = $oParam->iCodigoGrupoPerguntas;
            }

            if (empty($oParam->cpf)) {
                throw new BusinessException("CPF não informado.");
            }
            if (empty($oParam->dataAtestado) || $oParam->dataAtestado == "  /  /    ") {
                throw new BusinessException("Data Atestado de Saúde Ocupacional não informada.\n\r Por favor informe ela no grupo 3 - Detalhamento das informações do Atestado de Saúde Ocupacional - ASO.");
            }

            $aParametros = ['cpf' => $oParam->cpf, 'dataAtestado' => $oParam->dataAtestado, 'matricula' => $oParam->matricula];

            if (!empty($oParam->iCodigoPreenchimento)) {
                $aParametros["iCodigoPreenchimento"] = $oParam->iCodigoPreenchimento;
            }

            $oAvaliacaoESocial = new AvaliacaoESocial();
            $oAvaliacaoESocial->setAvaliacao($oAvaliacao);
            $oAvaliacaoESocial->setCgm(CgmFactory::getInstanceByCgm($oParam->iCGM));
            $oAvaliacaoESocial->setPerguntasRespostas($oParam->aPerguntasRespostas);
            $oAvaliacaoESocial->salvar($iCodigoGrupoPerguntas, Tipo::MONITORIAMENTO_SAUDE, $aParametros);

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
