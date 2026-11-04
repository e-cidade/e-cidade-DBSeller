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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("model/financeiro/ContaBancaria.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));
require_once(modification("model/contabilidade/planoconta/SistemaConta.model.php"));
require_once(modification("model/contabilidade/planoconta/SubSistemaConta.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlanoPCASP.model.php"));
require_once(modification("model/contabilidade/planoconta/ClassificacaoConta.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaCorrente.model.php"));

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->status = 1;
$oRetorno->message = '';


switch ($oParam->exec) {

    case "verificaConciliacaoAbertaPorReduzido":

        $iAnousu = db_getsession("DB_anousu");
        $oConta = new ContaPlanoPCASP($oParam->iCodCon, $iAnousu, $oParam->iReduzido, $oParam->iInstit);
        $lReduzidoPossuiConciliacao = $oConta->hasConciliacaoVinculada();

        /**
         * aqui vai ser liberado para usuario dbseller alterar a conta bancaria do reduzido, mesmo que tenha conciliacao
         * isso vai evitar demais acertos como o da tarefa M28803 , que mesmo com conciliação, foi realizado o update
         * na conplanocontabancaria
         */
        if (db_getsession("DB_login") == "dbseller" &&  db_getsession("DB_id_usuario") == 1 ) {
            $lReduzidoPossuiConciliacao = false;
        }

        $oRetorno->lPossuiConciliacaoVinculada = $lReduzidoPossuiConciliacao;
        break;


    case "salvarPlanoConta":

        /**
         * No momento em que é salvo devemos verificar o estrutural da
         * conta e localizar a qual conta corrente o estrutural pertence.
         */
        try {

            db_inicio_transacao();
            $iAno = null;
            if ($oParam->iCodigoConta != "") {
                $iAno = db_getsession("DB_anousu");
            }
            $oPlanoPCASP = new ContaPlanoPCASP       ($oParam->iCodigoConta, $iAno);
            $oPlanoPCASP->setAno(db_getsession("DB_anousu"));
            $oPlanoPCASP->setFuncao(db_stdClass::normalizeStringJsonEscapeString($oParam->sFuncao));
            $oPlanoPCASP->setFinalidade(db_stdClass::normalizeStringJsonEscapeString($oParam->sFuncionamento));
            $oPlanoPCASP->setContraPartida("0");
            $oPlanoPCASP->setDescricao(db_stdClass::normalizeStringJsonEscapeString($oParam->sTitulo));
            $oPlanoPCASP->setEstrutural($oParam->sEstrutural);
            $oPlanoPCASP->setIdentificadorFinanceiro($oParam->sIndicadorSuperavit);
            $oPlanoPCASP->setNaturezaSaldo($oParam->iNaturezaSaldo);
            $oPlanoPCASP->setSistemaContaCorrente($oParam->iContaCorrente);
            $oPlanoPCASP->setClassificacaoConta(new ClassificacaoConta($oParam->iClassificacao));
            $oPlanoPCASP->setSistemaConta(new SistemaConta($oParam->iDetalhamentoSistema));
            $oPlanoPCASP->setSubSistema(new SubSistemaConta($oParam->iSistemaConta));
            $oPlanoPCASP->setSaldoContinuo($oParam->bSaldoContinuo === 't');
            $oPlanoPCASP->salvar();

            $oRetorno->message = urlencode("Plano de contas salvo com sucesso.");
            $oRetorno->iAno = $oPlanoPCASP->getAno();
            $oRetorno->sDescricao = urlencode($oPlanoPCASP->getDescricao());
            $oRetorno->sEstrutural = $oParam->sEstrutural;
            $oRetorno->sFinalidade = urlencode($oPlanoPCASP->getFinalidade());
            $oRetorno->sFuncao = urlencode($oPlanoPCASP->getFuncao());
            $oRetorno->sIdentificadorFinanceiro = $oPlanoPCASP->getIdentificadorFinanceiro();
            $oRetorno->iNaturezaSaldo = $oPlanoPCASP->getNaturezaSaldo();
            $oRetorno->iClassificacao = $oParam->iClassificacao;
            $oRetorno->iDetalhamentoSistema = $oParam->iDetalhamentoSistema;
            $oRetorno->iSubSistemaConta = $oParam->iSistemaConta;
            $oRetorno->iContaCorrente = $oParam->iContaCorrente;
            $oRetorno->bSaldoContinuo = $oParam->bSaldoContinuo;
            $oRetorno->iCodigoConta = $oPlanoPCASP->getCodigoConta();

            db_fim_transacao(false);
        } catch (Exception $eErro) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($eErro->getMessage());
            db_fim_transacao(true);
        }
        break;

    case "salvarReduzido":

        try {

            db_inicio_transacao();

            /*
             * Valida se o parâmetro iCodigoReduzido está setado. Caso esteja, é realizado a ALTERAÇÃO dentro do model
             */
            $iReduzidoParametro = null;
            if (isset($oParam->iCodigoReduzido)) {
                $iReduzidoParametro = $oParam->iCodigoReduzido;
            }

            $oPlanoPCASP = new ContaPlanoPCASP(
                $oParam->iCodigoPlanoConta,
                db_getsession("DB_anousu"),
                $iReduzidoParametro,
                $oParam->iCodigoInstituicao
            );
            $sistemaConta = $oPlanoPCASP->getSistemaConta();
            if ($sistemaConta->getCodigoSistemaConta() === 6 && empty($oParam->iCodigoContaBancaria)) {

                $mensagem = "O campo Detalhamento do Sistema está selecionado como 6 - FINANCEIRO - BANCO, por este motivo é ";
                $mensagem .= "necessário informar uma conta bancária para esta conta.";
                throw new Exception($mensagem);
            }


            $oPlanoPCASP->setInstituicao($oParam->iCodigoInstituicao);
            $oPlanoPCASP->setRecurso($oParam->iCodigoRecurso);
            if (!empty($oParam->iCodigoContaBancaria)) {
                $oPlanoPCASP->setContaBancaria(new ContaBancaria($oParam->iCodigoContaBancaria));
            }

            $oPlanoPCASP->setInclusao(empty($oParam->iCodigoReduzido));
            $oPlanoPCASP->persistirReduzido();

            db_fim_transacao(false);
            $oRetorno->message = "Reduzidos salvos com sucesso!";
        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case "getReduzidos":

        /*
         * Busca as contas reduzidas de um plano de contas do PCASP
         */
        $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
        $oRetorno->aContasReduzidas = $oPlanoPCASP->getContasReduzidasAno();
        break;

    case "excluirReduzido":
        try {
            db_inicio_transacao();

            $oPlanoPCASP = new ContaPlanoPCASP(
                $oParam->iCodigoPlanoConta,
                db_getsession("DB_anousu"),
                $oParam->iCodigoReduzido,
                $oParam->iCodigoInstituicao
            );

            $oPlanoPCASP->removerReduzido($oParam->iCodigoReduzido);
            db_fim_transacao(false);
            $oRetorno->message = urlencode("Reduzido excluído com sucesso.");

        } catch (Exception $eErro) {
            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case "vinculaPlanoOrcamentario":

        try {

            db_inicio_transacao();
            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoPlanoPCASP, db_getsession("DB_anousu"));
            $oPlanoPCASP->vinculaPlanoContasOrcamento($oParam->iCodigoPlanoOrcamento);
            db_fim_transacao(false);
            $oRetorno->message = urlencode("Contas vinculadas com sucesso!");

        } catch (Exception $eErro) {
            db_fim_transacao(true);
            $oRetorno->message = urlencode($eErro->getMessage());
        }

        break;

    case "vinculaContaCorrente":

        try {

            db_inicio_transacao();

            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoPlanoPCASP, db_getsession("DB_anousu"));
            $oPlanoPCASP->vincularContaCorrente($oParam->iCodigoContaCorrente);

            db_fim_transacao(false);
            $oRetorno->message = urlencode("Conta Corrente vinculada com sucesso!");

        } catch (Exception $eErro) {
            db_fim_transacao(true);
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case 'excluirContaCorrente' :

        try {

            db_inicio_transacao();

            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoPlanoPCASP, db_getsession("DB_anousu"));
            $oPlanoPCASP->excluirContaCorrente($oParam->codigoContaCorrente);


            db_fim_transacao(false);
            $oRetorno->message = urlencode("Conta Corrente excluída com sucesso.");

        } catch (Exception $eErro) {
            db_fim_transacao(true);
            $oRetorno->message = urlencode($eErro->getMessage());
        }

        break;

    case "getVinculoPlanoOrcamento":

        $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
        $aContas = $oPlanoPCASP->getVinculoContaOrcamento();
        $oRetorno->aContasOrcamento = $aContas;
        break;

    case "excluiVinculoPlanoOrcamento":

        try {

            db_inicio_transacao();
            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
            $oPlanoPCASP->excluiVinculoContaOrcamento($oParam->iCodigoPlanoOrcamento);
            $oRetorno->message = urlencode("Vínculo excluído com sucesso.");
            db_fim_transacao(false);

        } catch (Exception $eErro) {
            db_fim_transacao(true);
            $oRetorno->message = urlencode($eErro->getMessage());
        }

        break;

    case "getPlanoContasPCASP":

        $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
        $oRetorno->dados = new stdClass();
        $oRetorno->dados->iCodigoConta = $oPlanoPCASP->getCodigoConta();
        $oRetorno->dados->c90_estrutcontabil = db_formatar($oPlanoPCASP->getEstrutural(), 'receita');
        $oRetorno->dados->sTitulo = urlencode($oPlanoPCASP->getDescricao());
        $oRetorno->dados->iNaturezaSaldo = $oPlanoPCASP->getNaturezaSaldo();
        $oRetorno->dados->sFuncionamento = urlencode($oPlanoPCASP->getFinalidade());
        $oRetorno->dados->sFuncao = urlencode($oPlanoPCASP->getFuncao());
        $oRetorno->dados->cbxSistema = $oPlanoPCASP->getSubSistema()->getCodigo();
        $oRetorno->dados->iDetalhamentoSistema = $oPlanoPCASP->getSistemaConta()->getCodigoSistemaConta();
        $oRetorno->dados->sDescricaoDetalhamentoSistema = urlencode($oPlanoPCASP->getSistemaConta()->getDescricao());
        $oRetorno->dados->iClassificacao = $oPlanoPCASP->getClassificacaoConta()->getCodigoClasse();
        $oRetorno->dados->sIndicadorSuperavit = $oPlanoPCASP->getIdentificadorFinanceiro();
        $oRetorno->dados->bSaldoContinuo = $oPlanoPCASP->isSaldoContinuo() ? 't' : 'f';
        $oContaCorrente = $oPlanoPCASP->getDadosSistemaContaCorrente();
        if (isset($oContaCorrente)) {

            $oRetorno->dados->iCodigoContaCorrente = $oContaCorrente->codigo;
            $oRetorno->dados->sDescricaoContaCorrente = urlencode($oContaCorrente->descricao);
        }

        $oRetorno->dados->iContaBancaria = "";
        if ($oPlanoPCASP->getContaBancaria() != null) {

            $oRetorno->dados->iContaBancaria = $oPlanoPCASP->getContaBancaria()->getSequencialContaBancaria();
            $oRetorno->dados->sDescricaoContaBancaria = urlencode($oPlanoPCASP->getContaBancaria()->getDadosConta());
        }
        $oRetorno->dados->iTipoConta = 0;
        if ($oPlanoPCASP->getContasReduzidas()) {
            $oRetorno->dados->iTipoConta = 1;
        }
        break;

    case 'removerConta':

        try {

            db_inicio_transacao();
            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
            $oPlanoPCASP->excluir();
            db_fim_transacao(false);

        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case "removerIndicadorSuperavit":

        try {

            db_inicio_transacao();

            $oPlanoPCASP = new ContaPlanoPCASP($oParam->iCodigoConta, db_getsession("DB_anousu"));
            $oPlanoPCASP->setIdentificadorFinanceiro('N');
            $oPlanoPCASP->salvar();
            db_fim_transacao(false);

        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case 'pesquisarContaPorEstrutural':

        try {
            $daoConplano = new cl_conplanoreduz;
            $where = array('c60_anousu = ' . db_getsession("DB_anousu"), "c61_instit = " . db_getsession("DB_instit"));
            if (empty($oParam->estrutural)) {
                throw new \Exception("Estrutural nao informado.");
            }
            $where[] = "c60_estrut = '{$oParam->estrutural}'";
            if (!empty($oParam->sistema)) {
                $where[] = 'c60_codsis = ' . $oParam->sistema;
            }


            $campos = "c61_codcon as codigo_conta, c61_reduz as codigo_reduzido, c60_estrut as estrutural, c60_descr as descricao_conta";
            $sqlConta = $daoConplano->sql_query_plano_reduzido($campos, implode(" and ", $where));
            $rsConta = db_query($sqlConta);
            if (pg_num_rows($rsConta) == 0) {
                throw new \Exception("Conta ({$oParam->estrutural}) não encontrada");
            }
            $conta = db_utils::fieldsMemory($rsConta, 0);
            $oRetorno->conta = $conta;

        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($eErro->getMessage());
        }

        break;

    case 'vincularContaBanco' :


        try {
            $daoBancoVinculoConta = new cl_bancovinculoconta();
            $vinculo = $oParam->vinculo;
            $vinculos = $daoBancoVinculoConta->getVinculosDoBanco($vinculo->banco);
            foreach ($vinculos as $vinculoBanco) {
                if ($vinculoBanco->tipo == $vinculo->tipo) {
                    throw new \Exception("O tipo informado já está vinculado para a conta contábil {$vinculoBanco->estrutural} - $vinculoBanco->descricao_conta");
                }
            }
            $daoBancoVinculoConta->db502_sequencial = null;
            $daoBancoVinculoConta->db502_db_bancos = $vinculo->banco;
            $daoBancoVinculoConta->db502_reduz = $vinculo->reduz;
            $daoBancoVinculoConta->db502_bancovinculocontatipo = $vinculo->tipo;
            $daoBancoVinculoConta->incluir();
            if ($daoBancoVinculoConta->erro_status == 0) {
                throw new \Exception('Não foi possível vincular a conta contábil ao Banco.');
            }
            $oRetorno->message = "Conta vinculada ao banco com sucesso.";
        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case 'getContaVinculadasBanco' :

        $daoBancoVinculoConta = new cl_bancovinculoconta();
        $vinculos = $daoBancoVinculoConta->getVinculosDoBanco($oParam->banco);
        foreach ($vinculos as $vinculo) {
            $vinculo->descricao_tipo = urlencode($vinculo->descricao_tipo);
            $vinculo->descricao_conta = urlencode($vinculo->descricao_conta);
        }
        $oRetorno->vinculos = $vinculos;
        break;

    case 'removerVinculoContaBanco':

        try {


            $daoBancoVinculoConta = new cl_bancovinculoconta();
            $vinculos = $daoBancoVinculoConta->excluir($oParam->vinculo);
            if ($daoBancoVinculoConta->erro_status == 0) {
                throw new \Exception('Não foi possível remover o vínculo da conta com o Banco.');
            }
            $oRetorno->message = urlencode("Vínculo removido com sucesso.");
        } catch (Exception $eErro) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    case "buscarSistema":
        try {

            $daoConsistemaconta = new cl_consistemaconta();
            $sql = $daoConsistemaconta->sql_query_file();
            $rs = db_query($sql);

            if (!$rs) {
                throw new DBException("Erro ao buscar os sub-sistemas de contas.");
            }

            $oRetorno->sistemas = db_utils::makeCollectionFromRecord($rs, function ($dadosSistema) {
                $sistema = new stdClass();
                $sistema->sequencial = $dadosSistema->c65_sequencial;
                $sistema->descricao = urlencode($dadosSistema->c65_descricao);
                $sistema->sigla = $dadosSistema->c65_sigla;
                return $sistema;
            });
        } catch (Exception $erro) {
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;


    case "getContaCorrente" :

        try {
            $iAno = db_getsession("DB_anousu");
            $campos = "distinct c122_sequencial as codigo, c122_descricao as descricao";
            $where = implode(' and ', array(
                "c122_tipo = 2",
                "c60_codcon = {$oParam->iCodigoConta}",
                "c60_anousu = {$iAno}"
            ));
            $daoContaCorrente = new cl_conplanosistema();
            $buscaVinculo = $daoContaCorrente->sql_query_vinculo_contas($campos, $where, "order by 1");
            $buscaVinculo = db_query($buscaVinculo);
            if (!$buscaVinculo) {
                throw new DBException("Ocorreu um erro ao consultar os conta correntes vinculados.");
            }

            $oRetorno->erro = false;
            $oRetorno->contasCorrentes = array();
            $oRetorno->contasCorrentes = db_utils::getCollectionByRecord($buscaVinculo, false, false, true);


        } catch (Exception $erro) {

            $oRetorno->erro = true;
            $oRetorno->message = urlencode($erro->getMessage());
        }


        break;

}
echo $oJson->encode($oRetorno);
