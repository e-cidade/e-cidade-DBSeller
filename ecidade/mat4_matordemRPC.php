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

use App\Domain\Financeiro\Empenho\Services\DocumentoService;
use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use App\Domain\Patrimonial\Protocolo\Services\EmpenhoDocumentoService;
use ECidade\Financeiro\Empenho\Mapper\TiposNotasParaiba;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/ordemCompra.model.php"));
require_once(modification("model/compras/ConfiguracaoDesdobramentoPatrimonio.model.php"));
require_once(modification("model/contabilidade/GrupoContaOrcamento.model.php"));
require_once(modification("libs/exceptions/BusinessException.php"));
require_once(modification("model/empenho/EmpenhoFinanceiroItem.model.php"));
require_once(modification("model/empenho/EmpenhoFinanceiro.model.php"));
require_once(modification("model/configuracao/DBDepartamento.model.php"));
require_once(modification("model/configuracao/Instituicao.model.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/MaterialCompras.model.php"));
require_once(modification("model/estoque/MaterialGrupo.model.php"));
require_once(modification("model/contabilidade/lancamento/LancamentoAuxiliarBase.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));

db_app::import('contabilidade.*');
db_app::import('contabilidade.lancamento.*');
db_app::import("financeiro.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("contabilidade.planoconta.*");
db_app::import("contabilidade.contacorrente.*");
db_app::import("configuracao.DBRegistry");

$post = db_utils::postmemory($_POST);
$json = new services_json();
$objJson = $json->decode(str_replace("\\", "", $_POST["json"]));
$oOrdemCompra = new ordemCompra($objJson->m51_codordem);
$method = $objJson->method;
$oOrdemCompra->setEncodeOn();
$iInstituicao = db_getsession('DB_instit');

if ($method == "getDados") {
    echo $oOrdemCompra->ordem2Json($objJson->e69_codnota);
} elseif ($method == "anularEntradaOrdem") {
    try {
        $oOrdemCompra->anularEntradaNota($objJson->e69_codnota);
        $status = 1;
        $mensagem = "Entrada de Ordem de Compra anulada com Sucesso.";
        $load = 1;
    } catch (BusinessException $oErro) {
        $status = 2;
        $mensagem = $oErro->getMessage();
        $load = 2;
    } catch (Exception $eErro) {
        $status = 2;
        $mensagem = $eErro->getMessage();
        $load = 2;
    }


    echo $json->encode(array("mensagem" => urlencode($mensagem), "status" => $status, "load" => $load));
} elseif ($method == "getOrdem") {
    $oOrdemCompra->setEncodeOn();
    $oOrdemCompra->getDados();
    if ($oOrdemCompra->getItensSaldo()) {
        echo $json->encode($oOrdemCompra->dadosOrdem);
    }
} elseif ($method == "anularOrdem") {
    $oOrdemCompra->anularOrdem($objJson->itensAnulados, db_stdclass::normalizeStringJsonEscapeString($objJson->sMotivo), $objJson->empanula);
    $mensagem = '';
    $status = 1;
    if ($oOrdemCompra->lSqlErro) {
        $mensagem = urlencode($oOrdemCompra->sErroMsg);
        $status = 2;
    }
    echo $json->encode(array("mensagem" => $mensagem, "status" => $status));
} elseif ($method == "getInfoEntrada") {
    try {
        $consultaParametro = "
          SELECT c01_modulo
            FROM parametrointegracaopatrimonial
           where c01_modulo = 2
             AND c01_instit = {$iInstituicao}
             AND c01_data IS NOT NULL";

        $rsConsultaParametro = db_query($consultaParametro);
        $result = pg_fetch_assoc($rsConsultaParametro);
        $mensagemErroGrupo = '';

        if ($result != false) {
            $sGrupoConta = "SELECT DISTINCT e60_numemp, e62_sequencial
                            FROM   empempenho
                                   INNER JOIN empelemento
                                           ON e64_numemp = e60_numemp
                                   INNER JOIN empempitem
                                           ON e62_numemp = e64_numemp
                                   INNER JOIN orcelemento
                                           ON o56_codele = e64_codele
                                              AND o56_anousu = e60_anousu
                                   INNER JOIN conplanoorcamentogrupo
                                           ON c21_codcon = o56_codele
                                              AND c21_anousu = o56_anousu
                                              AND c21_instit = e60_instit
                                   INNER JOIN matordemitem
                                           ON m52_numemp = e60_numemp
                            WHERE  m52_codordem = {$objJson->m51_codordem}";

            $rsGrupoConta = db_query($sGrupoConta);
            $mapItemGrupo = array();

            while ($row = pg_fetch_assoc($rsGrupoConta)) {
                $mapItemGrupo[$row['e62_sequencial']] = true;
            }

            $mensagemErroGrupo = '';

            if (pg_num_rows($rsGrupoConta) == 0) {
                $mensagemErroGrupo = "Você esta efetuando a entrada da nota de um empenho cuja despesa nãoo esta vinculada a nenhum grupo. É necessário que o desdobramento deste empenho esteja vinculado ao Grupo do Plano Orçamentário na Contabilidade, pois a Instituição possui Integração Patrimonial. <br/ >
                              Não foi configurado o grupo do plano orçamentário <br/>
                    Acesse para configurar: DB:FINANCEIRO > Contabilidade > Cadastros > Rotinas administrativas > Grupo do Plano Orçamentário (novo)";
            }
        }


        $oOrdemCompra->destroySession();
        $oOrdemCompra->getInfoEntrada();

        $oOrdemCompra->dadosOrdem->status = 1;
        $oOrdemCompra->dadosOrdem->itens = $oOrdemCompra->getDadosEntrada();


        $empenhoFinanceiro = $oOrdemCompra->getEmpenhoFinanceiro();
        $oListaClassificacaoCredor = $empenhoFinanceiro->getListaClassificacaoCredor();
        $oOrdemCompra->dadosOrdem->iClassificacao = '';
        $oOrdemCompra->dadosOrdem->sClassificacao = '';

        $cgm = $empenhoFinanceiro->getCgm();
        $cgnFisico = true;
        if ($cgm instanceof CgmJuridico) {
            $cgnFisico = false;
        }

        $desdobramento = $empenhoFinanceiro->getDesdobramento();
        $elemento = '';
        $subelemento = '';
        if ($desdobramento) {
            $elemento = substr($desdobramento->o56_elemento, 5, 2);
            $subelemento = substr($desdobramento->o56_elemento, 7, 2);
        }

        $outrosDados = null;
        $tiposNotas = [];
        $isEmpenhoFolha = $empenhoFinanceiro->isFolhaPagamento();
        if (isParaiba()) {
            $tipo = new TiposNotasParaiba();
            $tiposNotas = $tipo->getTiposNotasSegundoRegras($cgnFisico, $elemento, $subelemento);
            foreach ($tiposNotas as $key => $tipo) {
                $tiposNotas[$key]['label'] = urlencode($tipo['label']);
            }

            /**
             * Implementação provisória para Paraiba apenas até implementar o empenho automático da folha
             */
            // se o terceiro
            $isEmpenhoFolha = substr($desdobramento->o56_elemento, 2, 1) == 1;
        }

        $oOrdemCompra->dadosOrdem->dadosEmpenho = new stdClass();
        $oOrdemCompra->dadosOrdem->nomeInstituicao = $empenhoFinanceiro->getInstituicao()->getDescricao();
        $oOrdemCompra->dadosOrdem->dadosEmpenho->elemento = $elemento;
        $oOrdemCompra->dadosOrdem->dadosEmpenho->subelemento = $subelemento;
        $oOrdemCompra->dadosOrdem->dadosEmpenho->lPrestacaoConta = $empenhoFinanceiro->isPrestacaoContas();
        $oOrdemCompra->dadosOrdem->dadosEmpenho->isEmpenhoFolha = $isEmpenhoFolha;
        $oOrdemCompra->dadosOrdem->dadosEmpenho->tipoDeNotas = $tiposNotas;
        $oOrdemCompra->dadosOrdem->dadosEmpenho->grupo = null;
        $oOrdemCompra->dadosOrdem->processoEntradaNota = null;

        if (UTILIZA_INCORPORACAO_BEM) {
            $oOrdemCompra->dadosOrdem->dadosEmpenho->grupo = $empenhoFinanceiro->getContaOrcamento()->getGrupoConta()->getCodigo();
            $oOrdemCompra->dadosOrdem->processoEntradaNota = ordemCompra::getProcessoEntradaNota($oOrdemCompra->getOrdem());

            $daoProcessoEntrada = new cl_matordemprocessoentrada();
            $buscaProcessoEntrada = $daoProcessoEntrada->sql_query_empenho('m57_processoentrada', "e60_numemp = {$empenhoFinanceiro->getNumero()} limit 1");
            $buscaProcessoEntrada = db_query($buscaProcessoEntrada);
            if (!$buscaProcessoEntrada) {
                throw new Exception("Ocorreu um erro ao consultar o processo de entrada da ordem de compra.");
            }
            if (pg_num_rows($buscaProcessoEntrada) > 0) {
                $oOrdemCompra->dadosOrdem->processoEntradaNota = db_utils::fieldsMemory($buscaProcessoEntrada, 0)->m57_processoentrada;
            }
        }


        $sDataVencimento = '';
        if ($oListaClassificacaoCredor) {
            $oOrdemCompra->dadosOrdem->iClassificacao = $oListaClassificacaoCredor->getCodigo();
            $oOrdemCompra->dadosOrdem->sClassificacao = urlencode($oListaClassificacaoCredor->getDescricao());
        }
        if (!empty($oListaClassificacaoCredor)) {
            $sData = date("d/m/Y", db_getsession("DB_datausu"));
            $oDataVencimento = $oListaClassificacaoCredor->getDataVencimentoPorData(new DBDate($sData));
            $sDataVencimento = $oDataVencimento->getDate(DBDate::DATA_PTBR);
        }
        $oOrdemCompra->dadosOrdem->sDataVencimento = urlencode($sDataVencimento);
        if (isset($mapItemGrupo)) {
            $oOrdemCompra->dadosOrdem->mapItemGrupo = $mapItemGrupo;
        }
        $oOrdemCompra->dadosOrdem->result = $result;
        $oOrdemCompra->dadosOrdem->erroGrupo = urlencode($mensagemErroGrupo);

        $empenhoDocumentoService = new EmpenhoDocumentoService($empenhoFinanceiro);
        if (!is_null($empenhoDocumentoService->getDocumentoAndamento())) {
            $empEletronico = true;
        } else {
            $empEletronico = false;
        }
        $oOrdemCompra->dadosOrdem->eletronico = $empEletronico;

        echo $json->encode($oOrdemCompra->dadosOrdem);
    } catch (Exception $eErro) {
        echo $json->encode(array("mensagem" => urlencode($eErro->getMessage()), "status" => 2));
    }
} elseif ($method == "getInfoItem") {
    try {
        echo $json->encode($oOrdemCompra->getInfoItem($objJson->iCodLanc, $objJson->iIndice));
    } catch (Exception $oErro) {
        echo $json->encode(
            array(
                "mensagem" => urlencode($oErro->getMessage()),
                "status" => 2
            )
        );
    }
} elseif ($method == "saveMaterial") {
    try {
        $oOrdemCompra->saveMaterial($objJson->iCodLanc, $objJson->oMaterial);
        echo $json->encode(array(
            "mensagem" => "ok",
            "status" => 1,
            "lFraciona" => $objJson->oMaterial->fraciona,
            "iCodLanc" => $objJson->iCodLanc
        ));
    } catch (Exception $eErro) {
        echo $json->encode(
            array(
                "mensagem" => urlEncode($eErro->getMessage()),
                "status" => 2,
                "lfraciona" => false,
            )
        );
    }
} elseif ($method == "getDadosEntrada") {
    $aRetorno = array("aItens" => $oOrdemCompra->getDadosEntrada(), "marcar" => $objJson->marcar);
    echo $json->encode($aRetorno);
} elseif ($method == "cancelarFracionamento") {
    if ($oOrdemCompra->cancelarFracionamento($objJson->iCodLanc, $objJson->iIndice)) {
        echo $json->encode($oOrdemCompra->getDadosEntrada());
    }
} elseif ($method == "desmarcarItem") {
    $_SESSION["matordem{$objJson->m51_codordem}"][$objJson->iCodLanc][$objJson->iIndice]->checked = "";
} elseif ($method == "confirmarEntrada") {
    // aqui

    try {
        $sqlGrupoConta = "
      SELECT DISTINCT e60_numemp, e62_sequencial, c21_sequencial
      FROM   empempenho
            INNER JOIN empelemento
                    ON e64_numemp = e60_numemp
            INNER JOIN empempitem
                    ON e62_numemp = e64_numemp
            INNER JOIN orcelemento
                    ON o56_codele = e64_codele
                        AND o56_anousu = e60_anousu
            LEFT JOIN conplanoorcamentogrupo
                    ON c21_codcon = o56_codele
                        AND c21_anousu = o56_anousu
                        AND c21_instit = e60_instit
            INNER JOIN matordemitem
                    ON m52_numemp = e60_numemp
      WHERE  m52_codordem = {$objJson->m51_codordem}
    ";

        $mapItemGrupo = [];

        $rsItemGrupo = db_query($sqlGrupoConta);

        if ($result === false) {
            while ($row = pg_fetch_assoc($rsItemGrupo)) {
                if ($row['c21_sequencial'] == '') {
                    $codigoItem = $row['e62_sequencial'];
                    throw new Exception("Erro ao confirmar Entrada da Ordem de Compra.\n O item {$codigoItem} não possui grupo!");
                }
            }
        }

        db_inicio_transacao();

        /**
         * validação realizada pela retaguarda, para verificar se algum item esta sem vinculo com grupo e subgrupo.
         */
        $sComprasSemMaterial = '';
        foreach ($_SESSION["matordem{$objJson->m51_codordem}"] as $iCodLanc => $oItem) {
            foreach ($oItem as $iIndice => $oItemFilho) {
                FarmaciaHelper::validaItemEntradaOrdemCompra($oItemFilho, $objJson->sNumero, $objJson->dtDataNota);

                if ($oItemFilho->m63_codmatmater == ''
                    && $oItemFilho->pc01_servico != 't'
                    && $oItemFilho->checked !== ''
                    && $oItemFilho->m52_quant !== '0'
                ) {
                    $sComprasSemMaterial .= "\n" . $_SESSION["matordem{$objJson->m51_codordem}"][$iCodLanc][$iIndice]->pc01_codmater . ' - ' .
                        $_SESSION["matordem{$objJson->m51_codordem}"][$iCodLanc][$iIndice]->pc01_descrmater;
                }
            }
        }

        if (!empty($sComprasSemMaterial)) {
            throw new Exception(
                "Item sem ou mais de um vínculo com Material de Entrada:\n" . urldecode($sComprasSemMaterial) . "\n "
            );
        }

        $sObservacao = addslashes(db_stdClass::normalizeStringJsonEscapeString($objJson->sObs));
        $sNota = addslashes(db_stdClass::normalizeStringJsonEscapeString($objJson->sNumero));
        $sNumeroProcesso = addslashes(db_stdClass::normalizeStringJsonEscapeString($objJson->e04_numeroprocesso));
        $sLocalRecebimento = addslashes(db_stdClass::normalizeStringJsonEscapeString($objJson->sLocalRecebimento));

        $oOrdemDeCompra = new OrdemDeCompra($objJson->m51_codordem);
        $oEmpenhoFinanceiro = $oOrdemDeCompra->getEmpenhoFinanceiro();

        if ((round($oOrdemDeCompra->getValorLancado() + $objJson->nValorNota, 2)) > round($oOrdemDeCompra->getTotalOrdem(), 2)) {
            $message = "Já ocorreu lançamentos de entrada no valor R$ {$oOrdemDeCompra->getValorLancado()} \n";
            $message .= "Valor disponível a ser lançado: R$ " . ($oOrdemDeCompra->getTotalOrdem() - $oOrdemDeCompra->getValorLancado());
            throw new Exception($message);
        }

        $oDataVencimento = null;
        if (!empty($objJson->dtVencimento)) {
            $oDataVencimento = new DBDate($objJson->dtVencimento);
        }

        $oListaClassificacaoCredor = $oEmpenhoFinanceiro->getListaClassificacaoCredor();
        if ($oListaClassificacaoCredor) {
            $oListaClassificacaoCredor->validarParametros(
                $objJson->dtDataNota,
                $objJson->dtRecebeNota,
                $objJson->dtVencimento,
                $sLocalRecebimento
            );

            $oDataRecebimento = null;
            if (!empty($objJson->dtRecebeNota)) {
                $oDataRecebimento = new DBDate($objJson->dtRecebeNota);
            }
            $sMensagem = "data_vencimento_invalida_ordem_compra";
            $oListaClassificacaoCredor->validarDatas(
                new DBDate($objJson->dtDataNota),
                $oDataRecebimento,
                $oDataVencimento,
                $sMensagem
            );
        }

        if (empty($sNota)) {
            $sNota = "S/N";
        }

        if (empty($objJson->processoEntradaNota) && UTILIZA_INCORPORACAO_BEM) {
            throw new ParameterException("Campo Processo de Entrada da Nota é de preenchimento obrigatório.");
        }

        if (!empty($objJson->processoEntradaNota) && UTILIZA_INCORPORACAO_BEM) {
            $objJson->processoEntradaNota = (int)$objJson->processoEntradaNota;
            $processoEntradaNota = ordemCompra::getProcessoEntradaNota($objJson->m51_codordem);
            if (empty($processoEntradaNota)) {
                $daoProcessoEntrada = new cl_matordemprocessoentrada();
                $daoProcessoEntrada->m57_sequencial = null;
                $daoProcessoEntrada->m57_matordem = $objJson->m51_codordem;
                $daoProcessoEntrada->m57_processoentrada = $objJson->processoEntradaNota;
                $daoProcessoEntrada->incluir(null);
                if ($daoProcessoEntrada->erro_status === "0") {
                    throw new DBException("Ocorreu um erro ao salvar o processo de entrada da nota selecionado." . $daoProcessoEntrada->erro_banco);
                }
            } else {
                if ($objJson->processoEntradaNota !== $processoEntradaNota) {
                    throw new BusinessException("O tipo de processo da entrada da nota está diferente da entrada dada anteriormente.");
                }
            }
        }

        if (!empty($objJson->outrosDadosNota)) {
            $oOrdemCompra->setOutrosDadosNota($objJson->outrosDadosNota);
        }

        if (isRioDeJaneiro()) {
            $oSigfis = new \stdClass;

            if (isset($objJson->atestadores)) {
                $oSigfis->atestadores = $objJson->atestadores;
            }

            if (isset($objJson->tipodocliquidacao)) {
                $oSigfis->tipodocliquidacao = $objJson->tipodocliquidacao;
            }

            if (count(array_keys((array) $oSigfis))) {
                $oOrdemCompra->setDadosSigfis($oSigfis);
            }

            unset($oSigfis);
        }

        $oOrdemCompra->confirmaEntrada(
            $sNota,
            $objJson->dtDataNota,
            $objJson->dtRecebeNota,
            $objJson->nValorNota,
            $objJson->aItens,
            $objJson->oInfoNota,
            $sObservacao,
            $sNumeroProcesso,
            $oDataVencimento,
            $sLocalRecebimento,
            $objJson->processoEntradaNota
        );

        $codlan_c70 = (integer) $GLOBALS["codlan_c70"];
        $documentoService = new DocumentoService();
        if ($documentoService->isEletronico($objJson->m51_codordem)) {
            $documentoService->saveDocumentos($objJson->uploadNota, $objJson->m51_codordem, null, false, $codlan_c70);
        }

        /** [PAD/RS] Vinculo Numero de Serie */

        db_fim_transacao(false);
        echo $json->encode(array("mensagem" => "Entrada da ordem de compra efetuada com sucesso.", "status" => 1, "erro" => false));
    } catch (Exception $eError) {
        db_fim_transacao(true);
        echo $json->encode(array("mensagem" => urlencode($eError->getMessage()), "status" => 2, "erro" => true));
    }
} elseif ($method == "marcarItensSession") {
    if ($objJson->lMarcar) {
        $sChecked = "checked";
    } else {
        $sChecked = "";
    }
    foreach ($_SESSION["matordem{$objJson->m51_codordem}"] as $iCodLanc => $oItem) {
        foreach ($oItem as $iIndice => $oItemFilho) {
            echo $_SESSION["matordem{$objJson->m51_codordem}"][$iCodLanc][$iIndice]->checked . "\n";
            $_SESSION["matordem{$objJson->m51_codordem}"][$iCodLanc][$iIndice]->checked = $sChecked;
        }
    }
} elseif ($method == "verificaBensBaixado") {
    $aDocumentos = array(
        200 => 7,
        201 => 7,
        208 => 9,
        209 => 9,
        210 => 8,
        211 => 8
    );
    $status = 1;
    $aGrupos = array();
    $iCodigoNota = $objJson->iCodigoNota;
    $iInstituicao = db_getsession('DB_instit');
    $iGrupoEmpenho = 0;
    $oDaoConLanCamEmp = new cl_conlancamemp;

    $sCamposConLanCamEmp = " c71_coddoc, ";
    $sCamposConLanCamEmp .= " e60_numemp  ";
    $sWhereConLanCamEmp = "     conlancamnota.c66_codnota = {$iCodigoNota}                ";
    $sWhereConLanCamEmp .= " and conlancamdoc.c71_coddoc in (200, 201, 208, 209, 210, 211) ";
    $sSqlConLanCamEmp = $oDaoConLanCamEmp->sql_query_verificaBensBaixados(null, $sCamposConLanCamEmp, null, $sWhereConLanCamEmp);
    $rsConLanCamEmp = $oDaoConLanCamEmp->sql_record($sSqlConLanCamEmp);

    if ($oDaoConLanCamEmp->numrows > 0) {
        $iDocumento = db_utils::fieldsMemory($rsConLanCamEmp, 0)->c71_coddoc;
        $oDadosConLanCamEmp = db_utils::fieldsMemory($rsConLanCamEmp, 0);
        $oEmpenhoFinanceiro = new EmpenhoFinanceiro($oDadosConLanCamEmp->e60_numemp);
        $oContaOrcamento = new ContaOrcamento(
            $oEmpenhoFinanceiro->getDesdobramentoEmpenho(),
            $oEmpenhoFinanceiro->getAno(),
            null,
            $oEmpenhoFinanceiro->getInstituicao()
        );

        if ($oContaOrcamento->getGruposContas() !== false) {
            $aGrupos = $oContaOrcamento->getGruposContas();
            $iGrupoEmpenho = $aGrupos[0]->c20_sequencial;
        }

        $iGrupoDeveEstar = $aDocumentos[$iDocumento];
        if (($iGrupoDeveEstar != $iGrupoEmpenho) && ($iGrupoEmpenho != 9) && !UTILIZA_INCORPORACAO_BEM) {
            $oDaoConGrupo = new cl_congrupo;
            $sSQLConGrupo = $oDaoConGrupo->sql_query_file($iGrupoDeveEstar);
            $rsConGrupo = $oDaoConGrupo->sql_record($sSQLConGrupo);
            $oConGrupo = db_utils::fieldsMemory($rsConGrupo, 0);


            $sMensagemErro = "A operação não poderá ser realizada porque esta ordem de compra pertence ao empenho " . $oEmpenhoFinanceiro->getCodigo() . "/" . $oEmpenhoFinanceiro->getAno();
            $sMensagemErro .= " e sua conta de despesa não esta configurada no grupo de contas do plano orçamentário de origem, ";
            $sMensagemErro .= "o que compromete o fechamento contábil. Solicite ao responsável pela ";
            $sMensagemErro .= "Contabilidade a inclusão da conta " . $oContaOrcamento->getCodigoConta() . " - " . $oContaOrcamento->getDescricao();
            $sMensagemErro .= " no grupo de contas {$oConGrupo->c20_sequencial} - {$oConGrupo->c20_descr} ";

            $status = '3';
            echo $json->encode(array("status" => $status, "sMensagem" => urlencode($sMensagemErro)));
            return false;
        }
    }

    if ($oOrdemCompra->getBensAtivoNota($objJson->iCodigoNota) != false) {
        $status = 2;
    }

    if ($oOrdemCompra->houveDispensaTombamentoNoPatrimonio($objJson->iCodigoNota)) {
        $status = 4;
    }

    $oNotaLiquidacao = new NotaLiquidacao($objJson->iCodigoNota);
    if ($oNotaLiquidacao->getValorLiquidado() > 0) {
        $status = 5;
    }

    echo $json->encode(array("status" => $status, "iCodigoNota" => $iCodigoNota));
} elseif ($method == "verificaNota") {
    $status = 0;
    $sEmpenho = "";
    $sNota = addslashes(db_stdClass::normalizeStringJsonEscapeString($objJson->sNota));
    $iCgmFornecedor = $objJson->iCgmFornecedor;
    $oDaoEmpNota = new cl_empnota;

    $iInstituicao = db_getsession('DB_instit');
    $sWhereEmpNota = "e69_numero ilike '{$sNota}' and e60_instit = {$iInstituicao} and e60_numcgm = {$iCgmFornecedor}";
    $sSqlEmpNota = $oDaoEmpNota->sql_query(
        null,
        "distinct e60_codemp, e69_anousu",
        "e60_codemp, e69_anousu",
        $sWhereEmpNota
    );
    $rsEmpNota = $oDaoEmpNota->sql_record($sSqlEmpNota);

    if ($oDaoEmpNota->numrows > 0) {
        $status = 1;
        $aEmpenhos = array();
        foreach (db_utils::getCollectionByRecord($rsEmpNota) as $oEmpNota) {
            $aEmpenhos[] = "\n" . $oEmpNota->e60_codemp . '/' . $oEmpNota->e69_anousu;
        }
        $sEmpenho = implode(", ", $aEmpenhos);
    }
    echo $json->encode(array("status" => $status, "sEmpenho" => $sEmpenho));
} elseif ($method == 'anularEntradaOrdemEmpenhoMaterialPermanente') {
    try {
        db_inicio_transacao();

        $oDaoOrdemItemOC = new cl_matestoqueitemoc();
        $aWhere = array(
            "m74_codempnota = {$objJson->e69_codnota}",
            "m52_codordem = {$objJson->m51_codordem}"
        );
        $sSqlBuscaItens = $oDaoOrdemItemOC->sql_query_nota_ordem('matestoqueitemoc.*', implode(" and ", $aWhere));
        $rsBuscaItens = $oDaoOrdemItemOC->sql_record($sSqlBuscaItens);
        if ($oDaoOrdemItemOC->erro_status == "0") {
            throw new Exception("Ocorreu um erro na busca dos itens da ordem de compra.");
        }

        for ($iRow = 0; $iRow < $oDaoOrdemItemOC->numrows; $iRow++) {
            $oStdDadosItem = db_utils::fieldsMemory($rsBuscaItens, $iRow);
            $oStdDadosItem->m73_cancelado = $oStdDadosItem->m73_cancelado == "t";
            if ($oStdDadosItem->m73_cancelado) {
                throw new Exception("Ordem de compra já encontra-se cancelada.");
            }

            $oDaoBensDispensaTombamento = new cl_bensdispensatombamento();
            $sSqlBuscaTombamento = $oDaoBensDispensaTombamento->sql_query_file(null, "*", null, "e139_matestoqueitem = {$oStdDadosItem->m73_codmatestoqueitem}");
            $rsBuscaTombamento = db_query($sSqlBuscaTombamento);
            if (pg_num_rows($rsBuscaTombamento) > 0) {
                $sMensagem = "Alguns itens da nota encontram-se com dispensa de tombamento no patrimonio. É necessário estornar para que seja possível anular a entrada da ordem de compra.";
                throw new Exception($sMensagem);
            }

            $oDaoOrdemItemOCAlterar = new cl_matestoqueitemoc();
            $oDaoOrdemItemOCAlterar->m73_cancelado = 'true';
            $oDaoOrdemItemOCAlterar->m73_codmatordemitem = $oStdDadosItem->m73_codmatordemitem;
            $oDaoOrdemItemOCAlterar->m73_codmatestoqueitem = $oStdDadosItem->m73_codmatestoqueitem;
            $oDaoOrdemItemOCAlterar->alterar($oStdDadosItem->m73_codmatestoqueitem, $oStdDadosItem->m73_codmatordemitem);
            if ($oDaoOrdemItemOCAlterar->erro_status == "0") {
                throw new Exception("não foi possível alterar a situação da entrada da nota.");
            }

            if (UTILIZA_INCORPORACAO_BEM) {
                $oEstoqueItem = new MaterialEstoqueItem($oStdDadosItem->m73_codmatestoqueitem);

                $oMaterialEstoque = new materialEstoque($oEstoqueItem->getEstoque()->getMaterial()->getCodigo());
                $oMaterialEstoque->setCodDepto($oEstoqueItem->getEstoque()->getDepartamento()->getCodigo());
                $oMaterialEstoque->saidaMaterial(
                    $oEstoqueItem->getQuantidade(),
                    'Anulação da entrada da ordem de compra',
                    false,
                    new TipoMovimentacaoEstoque(TipoMovimentacaoEstoque::CODIGO_ANULACAO_ENTRADA_ORDEM_COMPRA)
                );
            }
        }

        $oDaoBensPendente = new cl_empnotaitembenspendente();
        $oDaoBensPendente->excluir(null, "e137_empnotaitem in (select e72_sequencial from empnotaitem where e72_codnota = {$objJson->e69_codnota})");
        if ($oDaoBensPendente->erro_status == "0") {
            throw new Exception("não foi possível excluir a pendência do bem no módulo patrimonial.");
        }

        $oDaoEmpNotaEle = new cl_empnotaele();
        $sSqlBuscaNotaEle = $oDaoEmpNotaEle->sql_query_file($objJson->e69_codnota, null, "*");
        $rsBuscaValorNota = $oDaoEmpNotaEle->sql_record($sSqlBuscaNotaEle);
        if (!$rsBuscaValorNota) {
            throw new Exception("não foi possível buscar os dados financeiros da nota de liquidação.");
        }
        $oStdNota = db_utils::fieldsMemory($rsBuscaValorNota, 0);

        if ($oStdNota->e70_vlrliq > 0) {
            throw new Exception("Nota de liquidação {$objJson->e69_codnota} com valor já liquidado. Procedimento abortado.");
        }

        $oDaoEmpNotaEle->e70_codnota = $oStdNota->e70_codnota;
        $oDaoEmpNotaEle->e70_codele = $oStdNota->e70_codele;
        $oDaoEmpNotaEle->e70_vlranu = $oStdNota->e70_valor;
        $oDaoEmpNotaEle->alterar($oStdNota->e70_codnota);
        if ($oDaoEmpNotaEle->erro_status == "0") {
            throw new Exception("não foi possível anular o valor da nota de liquidação gerada.");
        }

        if (UTILIZA_INCORPORACAO_BEM) {
            $oNotaLiquidacao = new NotaLiquidacao($objJson->e69_codnota);
            $empenhoFinanceiro = $oNotaLiquidacao->getEmpenho();
            $oStdDadosLancamento = new stdClass();
            $oStdDadosLancamento->iCodigoOrdemCompra = $objJson->m51_codordem;
            $oStdDadosLancamento->iCodigoDotacao = $empenhoFinanceiro->getDotacao()->getCodigo();
            $oStdDadosLancamento->iCodigoElemento = $empenhoFinanceiro->getContaOrcamento()->getCodigo();
            ;
            $oStdDadosLancamento->iCodigoNotaLiquidacao = $oNotaLiquidacao->getCodigoNota();
            $oStdDadosLancamento->iFavorecido = $empenhoFinanceiro->getCgm()->getCodigo();
            $oStdDadosLancamento->iNumeroEmpenho = $empenhoFinanceiro->getNumero();
            $oStdDadosLancamento->sObservacaoHistorico = "Estorno do lançamento em liquidação da ordem de compra {$objJson->m51_codordem}";
            $oStdDadosLancamento->iCodigoGrupo = 9;
            $oStdDadosLancamento->nValorTotal = round($oStdNota->e70_valor, 2);

            LancamentoEmpenhoEmLiquidacao::processar($oStdDadosLancamento, true);
            ordemCompra::excluirVinculoProcessoEntradaNota($empenhoFinanceiro->getNumero());
        }


        $mensagem = "Entrada da Ordem de Compra anulada com sucesso.";
        $status = 1;
        $load = 1;
        db_fim_transacao(false);
    } catch (Exception $e) {
        db_fim_transacao(true);
        $status = 2;
        $load = 2;
        $mensagem = $e->getMessage();
    }

    echo $json->encode(array("mensagem" => urlencode($mensagem), "status" => $status, "load" => $load));
} elseif ($method == 'getInfoMaterialEntrada') {
    $unidade = "";
    $quantunid = "";
    $status = 1;
    $mensagem = "";

    try {
        $daoMatmater = new cl_matmater();
        $sql = $daoMatmater->sql_query_file($objJson->matmater, "m60_codmatunid, m60_quantent");
        $rsSql = db_query($sql);
        $matmater = db_utils::getCollectionByRecord($rsSql);

        $unidade = $matmater[0]->m60_codmatunid;
        $quantunid = $matmater[0]->m60_quantent;

        if (empty($rsSql)) {
            throw new DBException("Erro ao buscar a unidade e configurada.");
        }
    } catch (DBException $erro) {
        $status = 2;
        $mensagem = $erro->getMessage();
    }

    echo $json->encode(array("mensagem" => urlencode($mensagem), "status" => $status, "unidade" => $unidade, "quantunid" => $quantunid));
}
