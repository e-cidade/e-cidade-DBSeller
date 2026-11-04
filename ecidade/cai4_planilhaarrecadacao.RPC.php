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
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBDate.php"));
require_once modification("libs/db_liborcamento.php");
require_once modification("model/contabilidade/planoconta/ContaPlano.model.php");
require_once modification("model/contabilidade/planoconta/ContaOrcamento.model.php");
require_once modification("model/contabilidade/planoconta/ClassificacaoConta.model.php");
require_once modification("model/contabilidade/planoconta/SistemaConta.model.php");
require_once modification("model/contabilidade/planoconta/SubSistemaConta.model.php");
require_once modification("model/CgmFactory.model.php");
require_once modification("model/configuracao/InstituicaoRepository.model.php");
require_once modification("model/caixa/PlanilhaArrecadacao.model.php");
require_once modification("model/caixa/ReceitaPlanilha.model.php");
require_once modification("model/caixa/AutenticacaoPlanilha.model.php");

db_app::import("exceptions.*");
db_app::import("configuracao.Instituicao");
db_app::import("configuracao.DBEstrutura");
db_app::import("CgmFactory");
db_app::import("contaTesouraria");
db_app::import("contabilidade.*");
db_app::import("orcamento.*");
db_app::import("configuracao.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("contabilidade.planoconta.*");
db_app::import("financeiro.*");
db_app::import("contabilidade.contacorrente.*");
db_app::import("exceptions.*");
 
$oJson              = new services_json();
$oParam             = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno           = new stdClass();
$oRetorno->dados    = array();
$oRetorno->status   = 1;

$oDataAtual = new DBDate(date("Y-m-d", db_getsession('DB_datausu')));

switch ($oParam->exec) {
    case "excluirPlanilha":
        $iPlanilha       = $oParam->iPlanilha;

        db_inicio_transacao();

        try {
            $oPlanilha = new PlanilhaArrecadacao($iPlanilha);
            $oPlanilha->excluir();
            db_fim_transacao(false);
            $oRetorno->message = urlencode("Planilha ({$iPlanilha}) excluída com sucesso");
        } catch (Exception $oExceptionErro) {
            db_fim_transacao(true);
            $oRetorno->status  = 2;
            $oRetorno->message = str_replace("\n", "\\n", urlencode($oExceptionErro->getMessage()));
        }


        break;

    case 'salvarPlanilha':
        db_inicio_transacao();
        try {
            $oPlanilhaArrecadacao = new PlanilhaArrecadacao();
            $dtArrecadacao = date('Y-m-d', db_getsession('DB_datausu'));
            $oPlanilhaArrecadacao->setDataCriacao($dtArrecadacao);
            $oPlanilhaArrecadacao->setInstituicao(InstituicaoRepository::getInstituicaoByCodigo(db_getsession('DB_instit')));
            $oPlanilhaArrecadacao->setProcessoAdministrativo(db_stdClass::normalizeStringJsonEscapeString($oParam->k144_numeroprocesso));

            foreach ($oParam->aReceitas as $oReceitas) {
                $recurso = new Recurso($oReceitas->iRecurso);
                $fonte = $recurso->getFonteRecurso(db_getsession('DB_anousu'));
                $complemento = empty($oReceitas->complemento) ? '0' : $oReceitas->complemento;
                if ($recurso->getComplemento() != $oReceitas->complemento) {
                    $recurso = RecursoRepository::getRecursoPorCodigoRecursoAndComplemento(
                        $fonte->gestao,
                        $recurso->getRecurso(),
                        $complemento,
                        $fonte->exercicio
                    );
                }
                $iNumeroCgm = buscaCgmOrigem($oReceitas);
                $iInscricao = empty($oReceitas->iInscricao) ? '' : $oReceitas->iInscricao;
                $iMatricula = empty($oReceitas->iMatricula) ? '' : $oReceitas->iMatricula;

                $contaTesouraria = new contaTesouraria($oReceitas->iContaTesouraria);
                if ($contaTesouraria->getDataLimite() != null) {
                  throw new Exception("Conta da tesouraria ({$oReceitas->iContaTesouraria}) não pode ter data de vencimento.");
                }
                
                $oReceitaPlanilha = new ReceitaPlanilha();
                $oReceitaPlanilha->setCaracteristicaPeculiar(new CaracteristicaPeculiar($oReceitas->iCaracteriscaPeculiar));
                $oReceitaPlanilha->setCGM(CgmFactory::getInstanceByCgm($iNumeroCgm));
                $oReceitaPlanilha->setContaTesouraria($contaTesouraria);
                $oReceitaPlanilha->setDataRecebimento(new DBDate($oReceitas->dtRecebimento));
                $oReceitaPlanilha->setInscricao($iInscricao);
                $oReceitaPlanilha->setMatricula($iMatricula);
                $oReceitaPlanilha->setObservacao(db_stdClass::normalizeStringJsonEscapeString($oReceitas->sObservacao));
                $oReceitaPlanilha->setOperacaoBancaria($oReceitas->sOperacaoBancaria);
                $oReceitaPlanilha->setOrigem($oReceitas->iOrigem);
                $oReceitaPlanilha->setRecurso($recurso);
                $oReceitaPlanilha->setTipoReceita($oReceitas->iReceita);
                $oReceitaPlanilha->setValor($oReceitas->nValor);
                $oReceitaPlanilha->setComplementoRecurso(empty($oReceitas->complemento) ? '0' : $oReceitas->complemento);
                $oPlanilhaArrecadacao->adicionarReceitaPlanilha($oReceitaPlanilha);
            }

            $oPlanilhaArrecadacao->salvar();
            db_fim_transacao(false);

            $sMensagemRetorno  = "Planilha {$oPlanilhaArrecadacao->getCodigo()} inclusa com sucesso.\n\n";
            $sMensagemRetorno .= "Deseja imprimir o documento gerado?";
            $oRetorno->message         = urlencode($sMensagemRetorno);
            $oRetorno->iCodigoPlanilha = $oPlanilhaArrecadacao->getCodigo();
        } catch (Exception $oExceptionErro) {
            db_fim_transacao(true);
            $oRetorno->status  = 2;
            $oRetorno->message = str_replace("\n", "\\n", urlencode($oExceptionErro->getMessage()));
        }
        break;

    case "verificaSlipsAutomaticos":
        $oPlanilhaArrecadacao = new PlanilhaArrecadacao($oParam->iPlanilha);
        $oRetorno->sSlipAutomatico = implode(", ", $oPlanilhaArrecadacao->getSlipsAutomaticosDasExtras(2));
        unset($oPlanilhaArrecadacao);
        break;




    case 'estornarPlanilha':
        db_inicio_transacao();
        try {
            $oPlanilhaArrecadacao = new PlanilhaArrecadacao($oParam->iPlanilha);
            $oDataAutenticacao = $oPlanilhaArrecadacao->getDataAutenticacao();
            if (empty($oDataAutenticacao)) {
                throw new Exception("A planilha [{$oParam->iPlanilha}] não encontra-se autenticada.");
            }

            if ($oDataAtual->getTimeStamp() < $oPlanilhaArrecadacao->getDataAutenticacao()->getTimeStamp()) {
                throw new Exception("Não é possível estornar uma planilha com data anterior a sua autenticação.");
            }

            $oPlanilhaArrecadacao->estornar();
            db_fim_transacao(false);
            $oRetorno->message = urlencode("Planilha {$oPlanilhaArrecadacao->getCodigo()} estornada com sucesso.");
        } catch (Exception $oExceptionErro) {
            db_fim_transacao(true);
            $oRetorno->status  = 2;
            $oRetorno->message = urlencode($oExceptionErro->getMessage());
        }

        break;

    case 'importarPlanilha':
    case 'getDadosPlanilhaArrecadacao':
        try {
            $oPlanilhaArrecadacao = new PlanilhaArrecadacao($oParam->iPlanilha);
            $receitasInativas = $oPlanilhaArrecadacao->validaReceitasInativas();

            if (!empty($receitasInativas)) {
                throw new Exception(sprintf(
                    'Essa planilha contém receitas inativas. Lista das receitas: %s',
                    implode(', ', $receitasInativas)
                ));
            }

            $aReceitasPlanilha = $oPlanilhaArrecadacao->getReceitasPlanilha();
            $oPlanilha = new stdClass();
            $oPlanilha->aReceitas = array();
            $oPlanilha->iPlanilha = $oPlanilhaArrecadacao->getCodigo();
            $oData = $oPlanilhaArrecadacao->getDataCriacao();
            $oPlanilha->dtDataCriacao = $oData->getDate(DBDate::DATA_PTBR);
            $oPlanilha->k144_numeroprocesso = $oPlanilhaArrecadacao->getProcessoAdministrativo();

            if (count($aReceitasPlanilha) > 0) {
                foreach ($aReceitasPlanilha as $oReceitaPlanilha) {
                    $oReceita = new stdClass();
                    $oReceita->iCodigo = $oReceitaPlanilha->getCodigo();
                    $oReceita->iReceita = $oReceitaPlanilha->getTipoReceita();
                    $oReceita->sDescricaoReceita = urlencode($oReceitaPlanilha->getDescricaoReceita());

                    $oReceita->iOrigem = $oReceitaPlanilha->getOrigem();
                    $oReceita->iCgm = $oReceitaPlanilha->getCGM()->getCodigo();
                    $oReceita->iInscricao = $oReceitaPlanilha->getInscricao();
                    $oReceita->iMatricula = $oReceitaPlanilha->getMatricula();
                    $oReceita->iCaracteriscaPeculiar = $oReceitaPlanilha->getCaracteristicaPeculiar()->getSequencial();

                    $oContaTesouraria = $oReceitaPlanilha->getContaTesouraria();
                    $oReceita->iContaTesouraria = $oContaTesouraria->getCodigoConta();
                    $oReceita->sDescricaoConta = urlencode($oContaTesouraria->getDescricao());

                    $oReceita->dtRecebimento = $oReceitaPlanilha->getDataRecebimento()->convertTo(DBDate::DATA_PTBR);
                    $oReceita->sObservacao = urlencode($oReceitaPlanilha->getObservacao());
                    $oReceita->sOperacaoBancaria = $oReceitaPlanilha->getOperacaoBancaria();
                    $oReceita->iRecurso = $oReceitaPlanilha->getRecurso()->getCodigo();
                    $oReceita->nValor = $oReceitaPlanilha->getValor();
                    $oReceita->complemento = $oReceitaPlanilha->getComplementoRecurso();

                    $oPlanilha->aReceitas[] = $oReceita;
                    unset($oReceita);
                }
            }

            $oRetorno->oPlanilha = $oPlanilha;
        } catch (Exception $e) {
            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($e->getMessage());
        }

        break;

    case 'autenticarPlanilha':
        db_inicio_transacao();
        try {
            $oPlanilhaArrecadacao = new PlanilhaArrecadacao($oParam->iPlanilha);
            if ($oDataAtual->getTimeStamp() < $oPlanilhaArrecadacao->getDataCriacao()->getTimeStamp()) {
                throw new Exception("Não é possível autenticar uma planilha com data anterior a sua criação.");
            }

            $oPlanilhaArrecadacao->getReceitasPlanilha();
            $oPlanilhaArrecadacao->validaContasExtas();
            $oPlanilhaArrecadacao->autenticar();

            $sNumeroProcesso = addslashes(db_stdClass::normalizeStringJsonEscapeString($oParam->k144_numeroprocesso));
            if (!empty($sNumeroProcesso)) {
                $oDaoPlacaixaProcesso = new cl_placaixaprocesso();

                $oDaoPlacaixaProcesso->k144_placaixa       = $oParam->iPlanilha;
                $oDaoPlacaixaProcesso->k144_numeroprocesso = $sNumeroProcesso;

                $where = "k144_placaixa = {$oParam->iPlanilha}";
                $sSqlProcesso = $oDaoPlacaixaProcesso->sql_query_file(null, "*", null, $where);
                $rsProcesso           = $oDaoPlacaixaProcesso->sql_record($sSqlProcesso);
                if ($oDaoPlacaixaProcesso->numrows > 0) {
                    $oDadosProcesso = db_utils::fieldsMemory($rsProcesso, 0);
                    $oDaoPlacaixaProcesso->k144_sequencial = $oDadosProcesso->k144_sequencial;
                    $oDaoPlacaixaProcesso->alterar($oDaoPlacaixaProcesso->k144_sequencial);
                } else {
                    $oDaoPlacaixaProcesso->incluir(null);
                }

                if ($oDaoPlacaixaProcesso->erro_status == 0) {
                    throw new Exception($oDaoPlacaixaProcesso->erro_msg);
                }
            }

            $oRetorno->sSlipAutomatico = implode(", ", $oPlanilhaArrecadacao->getSlipsAutomaticosDasExtras(1));
            db_fim_transacao(false);

            $sMensagemRetorno    = "Planilha {$oPlanilhaArrecadacao->getCodigo()} autenticada com sucesso.\n\n";
            $sMensagemRetorno   .= "Deseja imprimir o documento gerado?";
            $oRetorno->message   = urlencode($sMensagemRetorno);
            $oRetorno->iPlanilha = $oPlanilhaArrecadacao->getCodigo();
        } catch (Exception $oExceptionErro) {
            db_fim_transacao(true);
            $oRetorno->status  = 2;
            $oRetorno->message = str_replace("\n", "\\n", urlencode($oExceptionErro->getMessage()));
        }
        break;

    case 'alterarPlanilha':
        db_inicio_transacao();
        try {
            $oPlanilhaArrecadacao = new PlanilhaArrecadacao($oParam->iCodigoPlanilha);
            $oPlanilhaArrecadacao->excluirReceitas();

            $oPlanilhaArrecadacao->setProcessoAdministrativo(db_stdClass::normalizeStringJsonEscapeString($oParam->k144_numeroprocesso));

            foreach ($oParam->aReceitas as $oReceitas) {
                $iNumeroCgm       = buscaCgmOrigem($oReceitas);
                $iInscricao       = empty($oReceitas->iInscricao) ? '' : $oReceitas->iInscricao;
                $iMatricula       = empty($oReceitas->iMatricula) ? '' : $oReceitas->iMatricula;

                $oReceitaPlanilha = new ReceitaPlanilha(null);
                $contaTesouraria = new contaTesouraria($oReceitas->iContaTesouraria);
                if ($contaTesouraria->getDataLimite() != null) {
                  throw new Exception("Conta da tesouraria ({$oReceitas->iContaTesouraria}) não pode ter data de vencimento.");
                }
                $oReceitaPlanilha->setCaracteristicaPeculiar(new CaracteristicaPeculiar($oReceitas->iCaracteriscaPeculiar));
                $oReceitaPlanilha->setCGM(CgmFactory::getInstanceByCgm($iNumeroCgm));
                $oReceitaPlanilha->setContaTesouraria($contaTesouraria);
                $oReceitaPlanilha->setDataRecebimento(new DBDate($oReceitas->dtRecebimento));
                $oReceitaPlanilha->setInscricao($iInscricao);
                $oReceitaPlanilha->setMatricula($iMatricula);
                $oReceitaPlanilha->setObservacao(db_stdClass::normalizeStringJsonEscapeString($oReceitas->sObservacao));
                $oReceitaPlanilha->setOperacaoBancaria($oReceitas->sOperacaoBancaria);
                $oReceitaPlanilha->setOrigem($oReceitas->iOrigem);
                $oReceitaPlanilha->setRecurso(new Recurso($oReceitas->iRecurso));
                $oReceitaPlanilha->setTipoReceita($oReceitas->iReceita);
                $oReceitaPlanilha->setValor($oReceitas->nValor);
                $oReceitaPlanilha->setComplementoRecurso(empty($oReceitas->complemento) ? '0' : $oReceitas->complemento);

                $oPlanilhaArrecadacao->adicionarReceitaPlanilha($oReceitaPlanilha);
            }
            $oPlanilhaArrecadacao->salvar();

            db_fim_transacao(false);

            $sMensagemRetorno          = "Planilha {$oPlanilhaArrecadacao->getCodigo()} salva com sucesso.\n\n";
            $sMensagemRetorno         .= "Deseja imprimir o documento gerado?";
            $oRetorno->message         = urlencode($sMensagemRetorno);
            $oRetorno->iCodigoPlanilha = $oPlanilhaArrecadacao->getCodigo();
        } catch (Exception $oExceptionErro) {
            db_fim_transacao(true);
            $oRetorno->status  = 2;
            $oRetorno->message = str_replace("\n", "\\n", urlencode($oExceptionErro->getMessage()));
        }
        break;
}

/**
 * "Factory method" para retornar o cgm de acordo com a origem
 *
 * @param stdClass $oParam
 *
 * @return integer
 * @throws BusinessException
 */
function buscaCgmOrigem($oParam)
{

    $iNumeroCmg = "";
    switch ($oParam->iOrigem) {
        case 1:
            $iNumeroCmg = $oParam->iCgm;
            break;

        case 2:
            $iNumeroCmg = buscaCgmInscricao($oParam->iInscricao);
            break;

        case 3:
            $iNumeroCmg = buscaCgmMatricula($oParam->iMatricula);
            break;
        default:
            throw new BusinessException("Origem não identificada.");
    }

    return $iNumeroCmg;
}

/**
 * Busca o cgm da inscricao
 *
 * @param integer $iInscricao
 *
 * @throws BusinessException
 */
function buscaCgmInscricao($iInscricao)
{

    if (empty($iInscricao)) {
        $sMsgErro = "Número da inscricao vazio ou não informado.";
        throw new BusinessException($sMsgErro);
    }

    $oDaoIssBase = new cl_issbase;
    $sSqlInscricao = $oDaoIssBase->sql_query_file($iInscricao);
    $rsInscricao   = $oDaoIssBase->sql_record($sSqlInscricao);

    if ($rsInscricao && $oDaoIssBase->numrows == 1) {
        return db_utils::fieldsMemory($rsInscricao, 0)->q02_numcgm;
    }

    $sMsgErro  = "Erro ao buscar cgm da inscricao.\n";
    $sMsgErro .= "Inscrição: {$iInscricao}";
    $sMsgErro .= $oDaoIssBase->erro_msg;

    throw new BusinessException($sMsgErro);
}

/**
 * Busca o cgm da matricula
 *
 * @param integer $iMatricula
 *
 * @throws BusinessException
 */
function buscaCgmMatricula($iMatricula)
{

    if (empty($iMatricula)) {
        $sMsgErro = "Número da matricula vazio ou não informado.";
        throw new BusinessException($sMsgErro);
    }

    $oDaoIptuBase = new cl_iptubase;
    $sSqlMatricula  = $oDaoIptuBase->sql_query_file($iMatricula);
    $rsMatricula    = $oDaoIptuBase->sql_record($sSqlMatricula);

    if ($rsMatricula && $oDaoIptuBase->numrows == 1) {
        return db_utils::fieldsMemory($rsMatricula, 0)->j01_numcgm;
    }

    $sMsgErro  = "Erro ao buscar cgm da matricula.\n";
    $sMsgErro .= "Matricula: {$iMatricula}.\n";
    $sMsgErro .= $oDaoIptuBase->erro_msg;

    throw new BusinessException($sMsgErro);
}

echo $oJson->encode($oRetorno);
