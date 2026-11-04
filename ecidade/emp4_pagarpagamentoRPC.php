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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification('model/agendaPagamento.model.php'));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/CgmBase.model.php"));
require_once(modification("model/CgmJuridico.model.php"));
require_once(modification("model/CgmFisico.model.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification('model/empenho/EmpenhoFinanceiro.model.php'));
require_once(modification('model/empenho/EmpenhoFinanceiroItem.model.php'));
require_once(modification('model/MaterialCompras.model.php'));
require_once(modification("classes/ordemPagamento.model.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaOrcamento.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlanoPCASP.model.php"));
require_once(modification("std/DBDate.php"));
db_app::import("exceptions.*");
db_app::import("configuracao.*");
db_app::import("caixa.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.planoconta.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("financeiro.*");
db_app::import("Dotacao");
db_app::import("contabilidade.contacorrente.*");
db_app::import("orcamento.*");

$oGet     = db_utils::postMemory($_GET);
$oJson    = new services_json();
$sStringToParse = str_replace("<aspa>", '\"', str_replace("\\", "", $_POST["json"]));
$oParam   = $oJson->decode($sStringToParse);
$oRetorno = new stdClass();

switch ($oParam->exec) {
    case "getNotas":
        $oAgenda = new agendaPagamento();
        $oAgenda->setUrlEncode(true);
        $sWhere  = " ((round(e53_valor,2)-round(e53_vlranu,2)-round(e53_vlrpag,2)) > 0 ";
        $sWhere .= " and (round(e60_vlremp,2)-round(e60_vlranu,2)-round(e60_vlrpag,2)) > 0) ";
        $sWhere .= " and e80_data  <= '".date("Y-m-d", db_getsession("DB_datausu"))."'";
        $sWhere .= " and e97_codforma = {$oParam->params[0]->iForma} and e81_cancelado is null";

        if ($oParam->params[0]->iForma == 2) {
            $sWhere .= " and e91_codcheque is not null";
            if ($oParam->params[0]->dtChequeIni != "" && $oParam->params[0]-> dtChequeFim== "") {
                $sWhere .= " and e86_data = '".implode("-", array_reverse(explode("/", $oParam->params[0]->dtChequeIni)))."'";
            } elseif ($oParam->params[0]->dtChequeIni != "" && $oParam->params[0]->dtChequeFim != "") {
                $dtChequeIni = implode("-", array_reverse(explode("/", $oParam->params[0]->dtChequeIni)));
                $dtChequeFim = implode("-", array_reverse(explode("/", $oParam->params[0]->dtChequeFim)));
                $sWhere .= " and e86_data between '{$dtChequeIni}' and '{$dtChequeFim}'";
            } elseif ($oParam->params[0]->dtChequeIni == "" && $oParam->params[0]->dtChequeFim != "") {
                $dtChequeFim  = implode("-", array_reverse(explode("/", $oParam->params[0]->dtChequeFim)));
                $sWhere    .= " and e86_data <= '{$dtChequeFim}'";
            }

            if ($oParam->params[0]->sNumeroCheque != "") {
                $sWhere    .= " and e91_cheque = '{$oParam->params[0]->sNumeroCheque}'";
            }
        }

        $sWhere .= " and k12_data is null";
        $sWhere .= " and e60_instit = ".db_getsession("DB_instit");
        if ($oParam->params[0]->iOrdemIni != '' && $oParam->params[0]->iOrdemFim == "") {
            $sWhere .= " and e50_codord = {$oParam->params[0]->iOrdemIni}";
        } elseif ($oParam->params[0]->iOrdemIni != '' && $oParam->params[0]->iOrdemFim != "") {
            $sWhere .= " and e50_codord between  {$oParam->params[0]->iOrdemIni} and {$oParam->params[0]->iOrdemFim}";
        }

        if ($oParam->params[0]->dtDataIni != "" && $oParam->params[0]->dtDataFim == "") {
            $sWhere .= " and e50_data = '".implode("-", array_reverse(explode("/", $oParam->params[0]->dtDataIni)))."'";
        } elseif ($oParam->params[0]->dtDataIni != "" && $oParam->params[0]->dtDataFim != "") {
            $dtDataIni = implode("-", array_reverse(explode("/", $oParam->params[0]->dtDataIni)));
            $dtDataFim = implode("-", array_reverse(explode("/", $oParam->params[0]->dtDataFim)));
            $sWhere .= " and e50_data between '{$dtDataIni}' and '{$dtDataFim}'";
        } elseif ($oParam->params[0]->dtDataIni == "" && $oParam->params[0]->dtDataFim != "") {
            $dtDataFim  = implode("-", array_reverse(explode("/", $oParam->params[0]->dtDataFim)));
            $sWhere    .= " and e50_data <= '{$dtDataFim}'";
        }

      //Filtro para Empenho
        if ($oParam->params[0]->iCodEmp != '' and $oParam->params[0]->iCodEmp2 == "") {
            if (strpos($oParam->params[0]->iCodEmp, "/")) {
                $aEmpenho = explode("/", $oParam->params[0]->iCodEmp);
                $sWhere .= " and e60_codemp = '{$aEmpenho[0]}' and e60_anousu={$aEmpenho[1]}";
            } else {
                $sWhere .= " and e60_codemp = '{$oParam->params[0]->iCodEmp}' and e60_anousu=".db_getsession("DB_anousu");
            }
        } elseif ($oParam->params[0]->iCodEmp != "" and $oParam->params[0]->iCodEmp2 != "") {
            $sWhere .= "  and (";
            if (strpos($oParam->params[0]->iCodEmp, "/")) {
                $aEmpenho = explode("/", $oParam->params[0]->iCodEmp);
                $sWhere .= " (cast(e60_codemp as integer) >= {$aEmpenho[0]} and e60_anousu={$aEmpenho[1]} )";
            } else {
                $sWhere .= " (cast(e60_codemp as integer) >= {$oParam->params[0]->iCodEmp} and e60_anousu=".db_getsession("DB_anousu").")";
            }
            if (strpos($oParam->params[0]->iCodEmp2, "/")) {
                $aEmpenho = explode("/", $oParam->params[0]->iCodEmp2);
                $sWhere .= " or  (cast(e60_codemp as integer) <= {$aEmpenho[0]} and e60_anousu={$aEmpenho[1]} )";
            } else {
                $sWhere .= "  or (cast(e60_codemp as integer) <= {$oParam->params[0]->iCodEmp2} and e60_anousu=".db_getsession("DB_anousu").")";
            }
            $sWhere .= ") ";
        }
      //echo "<br>".$sWhere;
      //filtro para filtrar por credor
        if ($oParam->params[0]->iNumCgm != '') {
            $sWhere .= " and (e60_numcgm = {$oParam->params[0]->iNumCgm})";
        }

      /*
       * Conta pagadora
       */
        if ($oParam->params[0]->iCtaPagadora != '0') {
            $sWhere .= " and e83_codtipo = {$oParam->params[0]->iCtaPagadora}";
        }

        if (!empty($oParam->params[0]->iRecurso)) {
            $sWhere .= " and o15_codigo = {$oParam->params[0]->iRecurso}";
        }

        if (!empty($oParam->params[0]->fonteRecurso)) {
            $ids = \ECidade\Financeiro\Orcamento\Repository\RecursoRepository::getIdsRecursoPorSubrecurso(
                $oParam->params[0]->fonteRecurso
            );

            $sWhere .= " and o15_codigo in (" . implode(', ', $ids) . ")";
        }

        if ($oParam->params[0]->sDtAut != '') {
            $oParam->params[0]->sDtAut = implode("-", array_reverse(explode("/", $oParam->params[0]->sDtAut)));
            $sWhere .= " and e42_dtpagamento = '{$oParam->params[0]->sDtAut}'";
        }

        if ($oParam->params[0]->iOPauxiliar != '') {
            $sWhere .= " and e42_sequencial = {$oParam->params[0]->iOPauxiliar}";
        }
        $sJoin  = " left join empagenotasordem on e81_codmov  = e43_empagemov     ";
        $sJoin .= " left join empageordem      on e43_ordempagamento = e42_sequencial ";
        $aOrdensAgenda = $oAgenda->getMovimentosAgenda($sWhere, $sJoin, false, false, ",e83_conta, e83_descr,e91_codcheque, e91_valor, o15_recurso, gestao");

        $oRetorno->status           = 2;
        $oRetorno->mensagem         = "";
        if (count($aOrdensAgenda) > 0) {
            $oRetorno->status           = 1;
            $oRetorno->mensagem         = 1;
            $oRetorno->aNotasLiquidacao = $aOrdensAgenda;
        }

        echo $oJson->encode($oRetorno);
        break;

    case "pagarMovimento":
        $instit = db_getsession("DB_instit");
        if (APROPRIACAO_RETENCAO) {
             \ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao::setInstituicaoProcessamento(db_getsession("DB_instit"));
        }
        $oRetorno = new stdClass();
        $oRetorno->status       = 1;
        $oRetorno->message      = "";
        $oRetorno->iItipoAutent = null;
        $oRetorno->sSlipsGeradoAutomatico = "";
        if (is_array($oParam->aMovimentos)) {
            try {
                $oRetorno->aAutenticacoes = array();
                $oRetorno->iItipoAutent   = 0;
                $oRetorno->aAutenticacoes = array();
                $aSlipAutomatico = array();
                db_inicio_transacao();
                foreach ($oParam->aMovimentos as $oMovimento) {
                    $oOrdemPagamento = new ordemPagamento($oMovimento->iNotaLiq);

                    /* [Inicio suspensaoOrcamentaria] */
                    $dadosOrdem = $oOrdemPagamento->getDadosOrdem();
                    $suspensaoOrcamentaria = new cl_suspensaoorcamentaria;
                    if ($suspensaoOrcamentaria->verificaSuspensaoPagamentoDotacao(
                        $dadosOrdem->e60_coddot,
                        $dadosOrdem->e60_anousu
                    )) {
                        $msg  = "Não foi possível realizar o pagamento do movimento {$oMovimento->iCodMov}.\n";
                        $msg .= "Ficha financeira suspensa para pagamentos.";
                        throw new Exception($msg);
                    }
                  /* [Fim suspensaoOrcamentaria] */


                    $oOrdemPagamento->setCheque($oMovimento->iCheque);
                    $oOrdemPagamento->setChequeAgenda($oMovimento->iCodCheque);
                    $oOrdemPagamento->setConta($oMovimento->iConta);
                    $oOrdemPagamento->setValorPago($oMovimento->nValorPagar);
                    $oOrdemPagamento->setMovimentoAgenda($oMovimento->iCodMov);
                    $oOrdemPagamento->setHistorico($oMovimento->sHistorico);
                    $oOrdemPagamento->pagarOrdem();

                  // gera slip das retencoes da OP que foram apropriadas
                    if (APROPRIACAO_RETENCAO) {
                            $oOrdemPagamento->gerarSlipDasRetencoesApropriadas($oMovimento);
                    }

                    $oRetorno->iItipoAutent     = $oOrdemPagamento->oAutentica->k11_tipautent;
                    $c70_codlan                 = $oOrdemPagamento->iCodLanc;
                    $oAutentica                 = new stdClass();
                    $oAutentica->iNota          = $oMovimento->iNotaLiq;
                    $oAutentica->sAutentica     = $oOrdemPagamento->getRetornoautenticacao();
                    $oRetorno->aAutenticacoes[] = $oAutentica;

                    if (count($oOrdemPagamento->getSlipsGeradosAutomaticamente(1)) > 0) {
                          $aSlipAutomatico[] = $sSlipsGeradoAutomatico = implode(", ", $oOrdemPagamento->getSlipsGeradosAutomaticamente(1));
                    }
                }
                if (APROPRIACAO_RETENCAO) {
                    \ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao::restaurarInstituicaoUsuario();
                }

                if (count($aSlipAutomatico) > 0) {
                    $sSlipsGeradoAutomatico = implode(", ", $aSlipAutomatico);
                    $oRetorno->sSlipsGeradoAutomatico =$sSlipsGeradoAutomatico;
                }

                db_fim_transacao(false);

                $oRetorno->message = urlencode("Pagamento(s) Efetuado(s) com sucesso!");
            } catch (Exception $eErro) {
                db_fim_transacao(true);
                if (APROPRIACAO_RETENCAO) {
                    \ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao::restaurarInstituicaoUsuario();
                }
                $oRetorno->status = 2;
                $oRetorno->message = urlencode($eErro->getMessage());
            }
        }

        echo $oJson->encode($oRetorno);
        break;

    case "estornarPagamento":
        $oRetorno = new stdClass();
        $oRetorno->status         = 1;
        $oRetorno->message        = "";
        $oRetorno->iTipoAutentica = 0;
        $oRetorno->sAutenticacao  = null;
        $oRetorno->sCodLanc       = null;
        try {
            db_inicio_transacao();

            $codigoMovimento = $oParam->iCodMov;
            if (empty($codigoMovimento)) {
                $sql = "
              select k12_codmov
                from corempagemovpagamento
                join empord ON empord.e82_codmov = corempagemovpagamento.k12_codmov
               where e82_codord = {$oParam->iNota}
           ";

                $rs = db_query($sql);

                if ($rs && pg_num_rows($rs) > 0) {
                    $codigoMovimento = db_utils::fieldsMemory($rs, 0)->k12_codmov;
                }
            }

            $oOrdemPagamento = new ordemPagamento($oParam->iNota);
            $oOrdemPagamento->setCheque($oParam->iCheque);
            $oOrdemPagamento->setChequeAgenda($oParam->iCodCheque);
            $oOrdemPagamento->setConta($oParam->iConta);
            $oOrdemPagamento->setValorPago($oParam->nValorEstornar);
            $oOrdemPagamento->setMovimentoAgenda($codigoMovimento);
            $oOrdemPagamento->setHistorico($oParam->sHistorico);


            if ($oParam->lEstornarPgto) {
                $iMovimento              = $oOrdemPagamento->getMovimentoAgenda();
                $iCaracteristicaPeculiar = null;

                if (!empty($iMovimento)) {
                    $oDaoEmpAgeConcarPeculiar = new cl_empageconcarpeculiar;
                    $sSqlConCarPeculiar = $oDaoEmpAgeConcarPeculiar->sql_query_file(null, "e79_concarpeculiar", null, "e79_empagemov = {$iMovimento}");
                    $rsConcarPeculiar = $oDaoEmpAgeConcarPeculiar->sql_record($sSqlConCarPeculiar);
                    if ($oDaoEmpAgeConcarPeculiar->numrows > 0) {
                        $iCaracteristicaPeculiar = db_utils::fieldsMemory($rsConcarPeculiar, 0)->e79_concarpeculiar;
                    }
                }
                $oOrdemPagamento->estornarOrdem($iCaracteristicaPeculiar);

                $oRetorno->iTipoAutentica = $oOrdemPagamento->getTipoAutenticacao();
                $oRetorno->sAutenticacao  = $oOrdemPagamento->getRetornoautenticacao();
                $oRetorno->sCodLanc       = $oOrdemPagamento->iCodLanc;
            }



            if (count($oParam->aRetencoes) > 0) {
                /**
                 * $anoPagamentoOrdem = $oOrdemPagamento->getDadosOrdem()->e50_anousu;
                 * esse ano não é o do pagamento, é o da criação da OP
                 * pode ter sido criado em 2022 e pago em 2023 como RP
                 * vamos buscar o ano do pagamento pelo lancamento contabil de pagamento.
                 */
                $anoPagamentoOrdem = $oOrdemPagamento->getAnoDoUltimoPagamento();
                $anoAtual = db_getsession("DB_anousu");
                $ordem = $oOrdemPagamento->getDadosOrdem()->e50_codord;

                if ($anoPagamentoOrdem <  $anoAtual) {
                    throw new Exception("Ordem {$ordem} Paga em {$anoPagamentoOrdem}. Não é permitido estorno no ano de {$anoAtual}.");
                }

                $oOrdemPagamento->setRetencoes($oParam->aRetencoes);
                $oOrdemPagamento->estornarRetencoes();
                if ($oRetorno->iTipoAutentica == 0) {
                    $oRetorno->iTipoAutentica = $oOrdemPagamento->getTipoAutenticacao();
                    $oRetorno->sAutenticacao  = urlEncode($oOrdemPagamento->getRetornoautenticacao());
                }
                if ($oRetorno->sCodLanc != "") {
                    $oRetorno->sCodLanc       += ",{$oOrdemPagamento->iCodLanc}";
                } else {
                    $oRetorno->sCodLanc       = "{$oOrdemPagamento->iCodLanc}";
                }
            }

          /*
           * Verificamos se o usuario solicitou a anulação do cheque
           */
            if ($oParam->iCodCheque != ""  && $oParam->lEstornaCheque) {
                $oAgendaPagamento  = new agendaPagamento();
                $oAgendaPagamento->cancelarCheque($oParam->iCodMov);
            }

          /**
           * Verifica se é uma devolução e pega os itens da nota vinculada a ordem de pagamento e aplica um desconto
           */
            $daoMovimento = new cl_empagemov();
            $buscaValor = $daoMovimento->sql_query_file($oParam->iCodMov);
            $resultBuscaValorOriginal = db_query($buscaValor);
            if (!$resultBuscaValorOriginal) {
                throw new Exception("Nao foi possível consultar o valor do movimento.");
            }
            $valorMovimento = db_utils::fieldsMemory($resultBuscaValorOriginal, 0)->e81_valor;
            $valorTotalEstorno = ((float)$valorMovimento === (float)$oParam->nValorEstornar);
            if ($oParam->lDevolucao) {
                $rsExecutaQuery = db_query(" select e69_codnota, empnotaitem.* from empnota              "
                . "        inner join empnotaitem on e72_codnota = e69_codnota  "
                . "        inner join pagordemnota on e71_codnota = e69_codnota "
                . "  where e71_codord = {$oParam->iNota}                        ");

                $iCodigoNota = db_utils::fieldsMemory($rsExecutaQuery, 0)->e69_codnota;

                $oNota = new stdClass;
                $oNota->e69_codnota = $iCodigoNota;
                $oNota->aItens = array();

                $nDescontoSaldo = $oParam->nValorEstornar;

                for ($i =0; $i< pg_num_rows($rsExecutaQuery); $i++) {
                      $oStdDadosNota = db_utils::fieldsMemory($rsExecutaQuery, $i);

                    if ($nDescontoSaldo != 0) {
                        if ($nDescontoSaldo >= $oStdDadosNota->e72_vlrliq) {
                              $oStdDadosNota->nTotalDesconto = $oStdDadosNota->e72_vlrliq;
                              $nDescontoSaldo               -= $oStdDadosNota->e72_vlrliq;
                        } else {
                            $oStdDadosNota->nTotalDesconto = $nDescontoSaldo;
                            $nDescontoSaldo                = 0;
                        }

                        $oNota->aItens[] = $oStdDadosNota;
                    }
                }
              /*
               * Efetua o desconto e anulacoes do empenho se tiver registros na empprestaitem
               * aumentamos os criterios para entrar no metodo ->desconto somente se a prestação estiver conferida
               *
               */
                $oDaoEmpPrestaItem = new cl_empprestaitem;
                $sWhere = "e46_numemp = {$oParam->iNumEmp} and e45_conferido is not null";
                $sSqlEmpPrestaItem = $oDaoEmpPrestaItem->sql_query_presta(null, "e45_conferido", null, $sWhere);

                $rsEmpPrestaItem   = $oDaoEmpPrestaItem->sql_record($sSqlEmpPrestaItem);
                if ($oDaoEmpPrestaItem->numrows > 0) {
                    $oOrdemPagamento->desconto($oNota, $oParam->nValorEstornar, $oParam->sHistorico);
                }
            }

            db_fim_transacao(false);
        } catch (Exception $eErro) {
            $oRetorno->message = urlencode($eErro->getMessage());
            $oRetorno->status  = 2;
            db_fim_transacao(true);
        }
        echo $oJson->encode($oRetorno);
        break;


    case "verificaSlipsAutomaticos":
        $oOrdemPagamento = new ordemPagamento($oParam->codord);
        $oRetorno->sSlipAutomatico = implode(", ", $oOrdemPagamento->getSlipsGeradosAutomaticamente(2));
        echo $oJson->encode($oRetorno);
        break;


    case "Autenticar":
        $oRetorno = new stdClass();
        $oRetorno->status         = 1;
        $oRetorno->message        = "";

        require_once modification("model/impressaoAutenticacao.php");
        try {
            $oImpressao = new impressaoAutenticacao($oParam->sString);
            $oModelo = $oImpressao->getModelo();
            $oModelo->imprimir();
        } catch (Exception $eErroAutenticacao) {
            $oRetorno->status  = 2;
            $oRetorno->message = urlEncode($eErroAutenticacao->getMessage());
        }

        $oRetorno->sAutenticacao = urlEncode($oParam->sString);
        echo $oJson->encode($oRetorno);

        break;
}
