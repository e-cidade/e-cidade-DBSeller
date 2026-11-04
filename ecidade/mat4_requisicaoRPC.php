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

use App\Domain\Patrimonial\Material\Relatorios\IncosistenciasSaidaMaterialPDF;
use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/requisicaoMaterial.model.php"));
require_once(modification("classes/materialestoque.model.php"));
require_once(modification("classes/db_matparam_classe.php"));
require_once(modification("classes/db_db_almox_classe.php"));
require_once(modification("libs/JSON.php"));
require_once modification("libs/db_app.utils.php");

require_once modification("std/DBDate.php");

require_once(modification("model/contabilidade/contacorrente/ContaCorrenteFactory.model.php"));
require_once(modification("model/contabilidade/contacorrente/ContaCorrenteBase.model.php"));
require_once(modification("model/financeiro/ContaBancaria.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));
require_once(modification("model/contabilidade/planoconta/ClassificacaoConta.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaCorrente.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaOrcamento.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlanoPCASP.model.php"));

db_app::import("Acordo");
db_app::import("AcordoComissao");
db_app::import("CgmFactory");
db_app::import("financeiro.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("Dotacao");

db_app::import("contabilidade.contacorrente.*");

$cldb_dbalmox = new cl_db_almox;
$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
if ($oParam->exec == "getDados") {
    try {
        $oRequisicao = new requisicaoMaterial($oParam->params[0]->iCodReq);
        $oRequisicao->setEncode(true);
        if ($oRequisicao->getDados()) {
            $oRetorno = $oRequisicao->getInfo();
            $oRetorno->itens = $oRequisicao->getItens();
            $oRetorno->status = 1;
            $oRetorno->message = null;

            echo $oJson->encode($oRetorno);
        } else {
            echo $oJson->encode(array("status" => 2, "message" => urlencode("Não Foi possivel consultar itens.")));
        }
    } catch (Exception  $eExeption) {
        $sError = $eExeption->getMessage();
        echo $oJson->encode(array("status" => 2, "message" => urlencode($sError)));
    }
} elseif ($oParam->exec == "getLotes") {
    try {
        $oMaterialEstoque = new materialEstoque($oParam->params[0]->iCodMater);
        $oItens = $oMaterialEstoque->ratearLotes($oParam->params[0]->nValor, null, $oParam->params[0]->iCodEstoque);
        if (count($oItens) > 0) {
            $oRetorno->itens = $oItens;
            $oRetorno->status = 1;
            $oRetorno->message = null;

            echo $oJson->encode($oRetorno);
        } else {
            echo $oJson->encode(array("status" => 2, "message" => urlencode("Não Foi possivel consultar itens.")));
        }
    } catch (Exception $eException) {
        $sError = $eException->getMessage();
        echo $oJson->encode(array("status" => 2, "message" => urlencode($sError)));
    }
} elseif ($oParam->exec == "saveLote") {
    $oMaterialEstoque = new materialEstoque($oParam->params[0]->iCodMater);
    $oMaterialEstoque->saveLoteSession($oParam->params[0]->aItens);
    echo $oJson->encode(array("status" => 1, "message" => ""));
} elseif ($oParam->exec == "cancelarLote") {
    $oMaterialEstoque = new materialEstoque($oParam->params[0]->iCodMater);
    $oMaterialEstoque->cancelarLoteSession();
    echo $oJson->encode(array("status" => 1, "message" => ""));
} elseif ($oParam->exec == "atenderRequisicao") {
    try {
        db_inicio_transacao();

        $oRequisicao = new requisicaoMaterial($oParam->params[0]->iCodReq);

        $hasMaterialFarmacia = false;
        $dadosBnafar = null;
        foreach ($oParam->params[0]->aItens as $item) {
            if (!$hasMaterialFarmacia
                && FarmaciaHelper::utilizaIntegracaoBnafar()
                && FarmaciaHelper::isMaterialFarmacia($item->iCodMater)
            ) {
                $hasMaterialFarmacia = true;
                $dadosBnafar = (object)[
                    'tipoMovimentacao' => 7, // Transferência/Remanejamento
                    'cgm' => null,
                    'unidade' => $_SESSION['DB_coddepto']
                ];
            }
        }

        $oRequisicao->atenderRequisicao(
            $oParam->params[0]->iTipo,
            $oParam->params[0]->aItens,
            $oParam->params[0]->iCodEstoque,
            null,
            $dadosBnafar
        );

        db_fim_transacao(false);
        echo $oJson->encode(array("status" => 1, "message" => urlencode("Atendimento Efetuado com Sucesso")));
    } catch (Exception $eErro) {
        db_fim_transacao(true);
        echo $oJson->encode(array(
            "status" => 2,
            "message" => urlencode(str_replace("\\n", "\n", $eErro->getMessage()))
        ));
    }
} elseif ($oParam->exec == "saidaMaterial") {
    try {
        db_inicio_transacao();

        $dadosBnafar = null;
        if (FarmaciaHelper::utilizaIntegracaoBnafar()) {
            $dadosBnafar = (object)[
                'tipoMovimentacao' => $oParam->tipoMovimentacao,
                'cgm' => $oParam->cgmDestino,
                'unidade' => $oParam->unidadeDestino
            ];
        }

        foreach ($oParam->params[0]->itens as $oMaterial) {
            $oMaterialEstoque = new materialEstoque($oMaterial->iCodMater);
            MaterialEstoque::bloqueioMovimentacaoItem($oMaterial->iCodMater, db_getsession("DB_coddepto"));

            $exportaBnafar = FarmaciaHelper::validaMovimentacaoMaterial(
                $dadosBnafar,
                $oMaterial->iCodMater,
                true,
                $oMaterial->nQuantidade
            );

            if (isset($oMaterial->iCriterioCustoRateio)) {
                $oMaterialEstoque->setCriterioRateioCusto($oMaterial->iCriterioCustoRateio);
            }

            $observacao = sprintf(
                "Lançamento de saida manual do estoque. Material: %s - %s.",
                $oMaterial->iCodMater,
                $oMaterial->sObs
            );

            $dados = $exportaBnafar ? $dadosBnafar : null;

            $oMaterialEstoque->saidaMaterial($oMaterial->nQuantidade, $observacao, false, null, true, $dados);
            db_fim_transacao(false);
        }
        echo $oJson->encode(array("status" => 1, "message" => urlencode("Saída Efetuada com Sucesso")));
    } catch (Exception $eErro) {
        $oMaterialEstoque->cancelarLoteSession();
        db_fim_transacao(true);
        echo $oJson->encode(array("status" => 2, "message" => urlencode($eErro->getMessage())));
    }
} elseif ($oParam->exec == "saidaDeposito") {
    try {
        if (empty($oParam->depositos)) {
            throw new Exception('Informe ao menos um depósito.');
        }
        if (empty($oParam->observacao)) {
            throw new Exception('Informe a observação.');
        }

        $departamentos = [];
        foreach ($oParam->depositos as $codigoDeposito) {
            $deposito = \App\Domain\Patrimonial\Material\Models\Deposito::find($codigoDeposito);
            $departamentos[] = $deposito->departamento->getCodigo();
        }
        $codigosDepartamentos = implode(',', $departamentos);

        $daoMaterialEstoque = new cl_matestoque();
        $sql = $daoMaterialEstoque->sql_query_file(
            null,
            'm70_codmatmater,
             m70_quant,
             m70_coddepto',
            1,
            "m70_coddepto in ({$codigosDepartamentos}) and m70_quant > 0"
        );

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception('Erro ao buscar materiais do estoque');
        }
        $departamentoAtual = db_getsession("DB_coddepto");
        $erros = [];

        if (!pg_fetch_result($rs, 0)) {
            throw new Exception('Não existe quantidade em estoque a ser zerada, por favor, verifique.');
        }

        while ($dadosMaterial = pg_fetch_object($rs)) {
            db_inicio_transacao();
            db_putsession('DB_coddepto', $dadosMaterial->m70_coddepto);
            $oMaterialEstoque = new materialEstoque($dadosMaterial->m70_codmatmater);
            MaterialEstoque::bloqueioMovimentacaoItem($dadosMaterial->m70_codmatmater, $dadosMaterial->m70_coddepto);
            try {
                $oMaterialEstoque->saidaMaterial($dadosMaterial->m70_quant, $oParam->observacao);
            } catch (Exception $e) {
                db_fim_transacao(true);
                $erros[] = $dadosMaterial;
            }
            db_fim_transacao(false);
        }

        $relatorioInconsistencia = '';
        $incosistencias = false;
        if (!empty($erros)) {
            $incosistencias = true;
            $pdf = new IncosistenciasSaidaMaterialPDF($erros);
            $relatorioInconsistencia = $pdf->emitirPdf()['file'];
        }
        db_putsession('DB_coddepto', $departamentoAtual);
        echo $oJson->encode([
            "status" => 1,
            "message" => urlencode("Saída manual dos itens em estoque efetuada com sucesso!"),
            "inconsistencias" => $incosistencias,
            "relatorio" => $relatorioInconsistencia
        ]);
    } catch (Exception $eErro) {
        db_fim_transacao(true);
        echo $oJson->encode(array("status" => 2, "message" => urlencode($eErro->getMessage())));
    }
} elseif ($oParam->exec == "cancelarSaidaMaterial") {
    try {
        db_inicio_transacao();
        foreach ($oParam->params[0]->itens as $oMaterial) {
            $oMaterialEstoque = new materialEstoque($oMaterial->iCodMater);
            $oMaterialEstoque->cancelarSaidaMaterial(
                $oMaterial->nQuantidade,
                $oMaterial->iCodEstoqueIni,
                $oMaterial->sObs
            );
        }
        db_fim_transacao(false);
        echo $oJson->encode(array("status" => 1, "message" => urlencode("Cancelamento Efetuado com Sucesso")));
    } catch (Exception $eErro) {
        $oMaterialEstoque->cancelarLoteSession();
        db_fim_transacao(true);
        echo $oJson->encode(array("status" => 2, "message" => urlencode($eErro->getMessage())));
    }
} elseif ($oParam->exec == "getDadosPedidoRequisicao") { ///traz os dados do atendimento requisicao para atende-la
    try {
        $oSolicitacao = new requisicaoMaterial($oParam->params[0]->iCodReq);
        $oSolicitacao->setEncode(true);
        if ($oSolicitacao->getDadosPedidoRequisicao()) {
            $oRetorno = $oSolicitacao->getInfo();
            unset($oRetorno->senha);
            if ($oRetorno->m91_depto != "") {
                if ($oRetorno->m91_depto != "") {
                    $sql = $cldb_dbalmox->sql_record(
                        $cldb_dbalmox->sql_query("", "descrdepto as descr", "", "m91_depto= " . $oRetorno->m91_depto)
                    );
                    db_fieldsmemory($sql, 0);
                    $oRetorno->descr_depto = $descr;
                }
                $oRetorno->itens = $oSolicitacao->getItensPedidoRequisicao();
                $oRetorno->status = 1;
                $oRetorno->message = null;

                echo $oJson->encode($oRetorno);
            } else {
                echo $oJson->encode(array("status" => 2, "message" => urlencode("Não Foi possivel consultar itens.")));
            }
        }
    } catch (Exception  $oExeption) {
        $sError = $oExeption->getMessage();
        echo $oJson->encode(array("status" => 2, "message" => urlencode($sError)));
    }
} elseif ($oParam->exec == "anularRequisicao") {  ///função que faz a anulação dos itens da requisição
    db_inicio_transacao();
    try {
        foreach ($oParam->params[0]->aItens as $oMaterial) {
            $oMaterialEstoque = new materialEstoque($oMaterial->iCodMater);

            $oMaterialEstoque->anularRequisicao(
                $oMaterial->nQtde,
                db_stdClass::normalizeStringJsonEscapeString($oMaterial->sItemMotivo),
                $oMaterial->iCodMater,
                $oMaterial->iCodItemReq
            );
        }
        db_fim_transacao(false);
    } catch (Exception  $eErro) {
        $sqlerro = true;
        $erro_msg = str_replace("\n", "\\n", $eErro->getMessage());
        db_fim_transacao(true);
    }
    echo $oJson->encode(array("status" => 1, "message" => "Inclusão efetuada com Sucesso"));
}
