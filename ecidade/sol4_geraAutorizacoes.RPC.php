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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conn.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_solicitem_classe.php"));
require_once(modification("classes/db_solicita_classe.php"));
require_once(modification("model/itemSolicitacao.model.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/licitacao.model.php"));
require_once(modification("classes/solicitacaocompras.model.php"));
require_once(modification("model/empenho/AutorizacaoEmpenho.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/ProcessoCompras.model.php"));
require_once(modification("classes/db_pcproc_classe.php"));

use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepository;
use ECidade\Patrimonial\Compras\AutorizacaoEmpenho\Services\AutorizacaoService;

$oJson = new services_json();


$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = "";
$oRetorno->complementos = array();
switch ($oParam->exec) {

    case "getTipoCompraEmpenho":

        $oDaoPcTipoCompra = new cl_pctipocompra();
        $oDaoSolicitacao = new cl_solicita();
        $oDaoEmpTipo = new cl_emptipo();
        $oDaoBuscaDotacao = new solicitacaoCompra($oParam->iCodigo);
        $oDaoSolicitaVinculo = new cl_solicitavinculo();

        /**
         * Buscando o tipo da solicitação
         */
        $oRetorno->sResumo = "";
        $sql = $oDaoSolicitacao->sql_query_consulta(null, "pc10_solicitacaotipo", "", "pc10_numero = {$oParam->iCodigo}");
        $rs = $oDaoSolicitacao->sql_record($sql);

        if ($rs && pg_num_rows($rs) > 0) {
            $tipoCompra = \db_utils::fieldsMemory($rs, 0)->pc10_solicitacaotipo;
        }

        $aSolicitacaoComDotacaoAnoAnterior = $oDaoBuscaDotacao->getSolicitacoesDotacaoAnoAnterior();
        $oRetorno->solicitacaoComDotacaoAnoAnterior = $aSolicitacaoComDotacaoAnoAnterior;

        $sWhereResumo = "pc10_numero = {$oParam->iCodigo}";
        $sOrderResumo = "pc10_numero desc limit 1";
        $sSqlBuscaResumo = $oDaoSolicitacao->sql_query_estregistro(null, "pc10_resumo", $sOrderResumo, $sWhereResumo);
        $rsResumo = $oDaoSolicitacao->sql_record($sSqlBuscaResumo);

        /*
         * Retorna o resumo da solicitação
         */
        if ($rsResumo && pg_num_rows($rsResumo) > 0) {
            $oResumo = db_utils::fieldsMemory($rsResumo, 0, false, false, false);
            $oRetorno->sResumo = utf8_encode($oResumo->pc10_resumo);
        }

        /*
         * Busca os Tipos de Compra
         */
        $sSqlPcTipoCompra = $oDaoPcTipoCompra->sql_query(null,
            'pc50_codcom, pc50_descr, l44_obrigalicitacao',
            null,
            'pc50_ativo is true'
        );
        $rsExecPcTipoCompra = $oDaoPcTipoCompra->sql_record($sSqlPcTipoCompra);
        $aPcTipoCompra = array();

        if ($oDaoPcTipoCompra->numrows > 0) {
            for ($iRow = 0; $iRow < $oDaoPcTipoCompra->numrows; $iRow++) {
                $oDadosTipoCompra = db_utils::fieldsMemory($rsExecPcTipoCompra, $iRow, false, false, true);
                $aPcTipoCompra[] = $oDadosTipoCompra;
            }
            $oRetorno->aPcTipoCompra = $aPcTipoCompra;
        }

        if ($oDaoSolicitacao->numrows > 0) {
            if ($tipoCompra != 5) {

                //verifica qual vai ser o tipo de compra inicial
                $sql = $oDaoSolicitacao->sql_query_tipocompra($oParam->iCodigo, "pc50_codcom");
                $rs = $oDaoSolicitacao->sql_record($sql);
                if ($oDaoSolicitacao->numrows > 0) {
                    $iTipoCompra = \db_utils::fieldsMemory($rs, 0)->pc50_codcom;
                } else {
                    $oDaoPcParam = new cl_pcparam();
                    $sql = $oDaoPcParam->sql_query(db_getsession("DB_instit"));
                    $rs = $oDaoPcParam->sql_record($sql);
                    $iTipoCompra = \db_utils::fieldsMemory($rs, 0)->pc50_codcom;
                }
                $oRetorno->iTipoCompraInicial = $iTipoCompra;

                //verifica se o tipo da licitação é obrigatorio
                $sql = $oDaoPcTipoCompra->sql_query(null,
                    'pc50_codcom, pc50_descr, l44_obrigalicitacao',
                    null,
                    "pc50_codcom = {$iTipoCompra}"
                );
                $rs = $oDaoPcTipoCompra->sql_record($sql);
                $liberaLicitacao = \db_utils::fieldsMemory($rs, 0)->l44_obrigalicitacao;
                if (isset($liberaLicitacao)) {
                    $oRetorno->l44_obrigalicitacao = $liberaLicitacao;
                }

                //caso for tipo 1 e houver licitação
                $oDaoSolicitacao = new cl_solicita();
                $sql = $oDaoSolicitacao->sql_query_licitacao_dotacao(
                    null,
                    'l20_codigo',
                    '',
                    "pc10_numero = {$oParam->iCodigo}"
                );
                $rs = $oDaoSolicitacao->sql_record($sql);
                $l20_codigo = \db_utils::fieldsMemory($rs, 0)->l20_codigo;
                if ($l20_codigo != '') {
                    $licitacao = new licitacao($l20_codigo);
                    $dadosLicitacao = $licitacao->getDados();
                    $oRetorno->iTipoCompraInicial = $dadosLicitacao->pc50_codcom;
                    $oRetorno->l44_obrigalicitacao = $dadosLicitacao->l44_obrigalicitacao;
                }
            } else {
                $sql = $oDaoSolicitacao->sql_query_consulta(null, 'pc12_tipo', '', "pc11_numero = {$oParam->iCodigo}");
                $rs = $oDaoSolicitacao->sql_record($sql);
                if ($oDaoSolicitacao->numrows > 0) {
                    $iTipoCompra = \db_utils::fieldsMemory($rs, 0)->pc12_tipo;
                    $oRetorno->iTipoCompraInicial = $iTipoCompra;
                }
            }
        }

        /*
         * Busca os Tipos de Empenho
         */

        $sSqlTipoEmpenho = $oDaoEmpTipo->sql_query_file();
        $rsExecTipoEmpenho = $oDaoEmpTipo->sql_record($sSqlTipoEmpenho);
        $aTipoEmpenho = array();

        if ($oDaoEmpTipo->numrows > 0) {
            for ($iRow = 0; $iRow < $oDaoEmpTipo->numrows; $iRow++) {
                $oDadosTipoEmpenho = db_utils::fieldsMemory($rsExecTipoEmpenho, $iRow, false, false, true);
                $aTipoEmpenho[] = $oDadosTipoEmpenho;
            }
            $oRetorno->aTipoEmpenho = $aTipoEmpenho;
        }

        if (count($aPcTipoCompra) > 0 && count($aTipoEmpenho) > 0) {
            $oRetorno->status = 0;
        }

        break;

    case "getProcessoAdministrativo":

        $oRetorno->pc90_numeroprocesso = '';
        $oRetorno->numeroLicitacao = '';
        $oRetorno->anoLicitacao = '';
        $oRetorno->instituicaoLicitacao = '';

        $clliclicitem = new cl_liclicitem();
        $oDaoLicLicita = new cl_liclicita();
        $oDaoSolicitaVinculo = new cl_solicitavinculo();
        $oDaoSolicitacao = new cl_solicita();

        $sql = $oDaoSolicitacao->sql_query_numero_solicita(
            null,
            "pc81_codproc",
            '',
            "pc10_numero = {$oParam->iOrigem}"
        );
        $rs = $oDaoSolicitacao->sql_record($sql);
        $pc81_codproc = \db_utils::fieldsMemory($rs, 0)->pc81_codproc;

        if ($oDaoSolicitacao->numrows > 0) {

            $processoCompras = new ProcessoCompras($pc81_codproc);
            $l20_codigo = $processoCompras->getLicitacao();
            $licitacao = new licitacao($l20_codigo->getCodigo());
            if ($licitacao->getCodigo() !== null) {
                $dadosLicitacao = $licitacao->getDados();
                $oRetorno->numeroLicitacao = $dadosLicitacao->l20_numero;
                $oRetorno->anoLicitacao = $dadosLicitacao->l20_anousu;
                $oRetorno->instituicaoLicitacao = $dadosLicitacao->l20_instit;
            }
        }

        break;

    case "getTipoLicitacao":

        $aWhere = array(
            "l03_instit = " . db_getsession("DB_instit"),
            "l03_codcom = {$oParam->iTipoCompra}",
            "l03_modalidadeativa = 't'"
        );

        $oDaoCfgLiclicita = new cl_cflicita;
        $sSqlTipoLicitacao = $oDaoCfgLiclicita->sql_query_file(null, "l03_tipo, l03_descr", '', implode(' and ', $aWhere));
        $rsTipoLicitacao = $oDaoCfgLiclicita->sql_record($sSqlTipoLicitacao);
        $oRetorno->aTiposLicitacao = array();

        if ($oDaoCfgLiclicita->numrows > 0) {
            for ($iTipoLicitacao = 0; $iTipoLicitacao < $oDaoCfgLiclicita->numrows; $iTipoLicitacao++) {
                $oRetorno->aTiposLicitacao[] = db_utils::fieldsMemory($rsTipoLicitacao, $iTipoLicitacao);
            }
        }

        $dao = new cl_pctipocompra();
        $sql = $dao->sql_query(null, "l44_obrigalicitacao", '', "pc50_codcom = {$oParam->iTipoCompra}");
        $rs = $dao->sql_record($sql);
        $oRetorno->obrigaLicitacao = \db_utils::fieldsMemory($rs, 0)->l44_obrigalicitacao;

        break;

    /**
     * Busca os itens de uma solicitação de compra para que seja feita a geração de empenho
     */
    case "getItensParaAutorizacao":
        try {
            $oSolicitacao = new solicitacaoCompra($oParam->iCodigo);
            $aItensSolicitacao = $oSolicitacao->getItensParaAutorizacao();
            $oRetorno->aItens = array();
            $oRetorno->complementos = [];
            $oRetorno->listaMetaHistoricos = AutorizacaoEmpenho::getListaCompletaMetasHistoricos();
            $dataLimite = date('Y-m-d', db_getsession('DB_datausu'));
            $anousu = DB_getsession('DB_anousu');
            foreach ($aItensSolicitacao as $item) {
                $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($item->codigodotacao, $anousu);
                $idRecurso = $dotacao->getRecurso();
                $recursoDotacao = $dotacao->getDadosRecurso();
                $fonte = $recursoDotacao->getFonteRecurso($anousu);

                $complementos = [];
                $dadosComplemento = RecursoRepository::getComplementosByGestao(
                    $fonte->gestao,
                    $recursoDotacao->getRecurso(),
                    $anousu,
                    $dataLimite
                );
                foreach ($dadosComplemento as &$complemento) {
                    $complemento->selecionado = $complemento->codigo == $recursoDotacao->getComplemento();
                    $complementos[] = $complemento;
                }
                $oRetorno->complementos[$item->codigodotacao] = $complementos;
            }

            foreach ($aItensSolicitacao as $oStdItem) {
                $oStdItem->fornecedor = urlencode($oStdItem->fornecedor);
                $oRetorno->aItens[] = $oStdItem;
            }

        } catch (Exception $eErro) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($eErro->getMessage());
        }
        break;

    /**
     * Gera autorização de empenho para os itens selecionados
     */
    case "gerarAutorizacoes":
        try {
            /**
             * corrigimos as strings antes de salvarmos os dados
             */

            foreach ($oParam->aAutorizacoes as $oAutorizacao) {
                $oAutorizacao->destino = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->destino);
                $oAutorizacao->complemento = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->complemento);
                $oAutorizacao->sContato = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->sContato);
                $oAutorizacao->sOutrasCondicoes = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->sOutrasCondicoes);
                $oAutorizacao->condicaopagamento = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->condicaopagamento);
                $oAutorizacao->prazoentrega = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->prazoentrega);
                $oAutorizacao->resumo = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->resumo);
                $oAutorizacao->sTipoLicitacao = db_stdClass::normalizeStringJsonEscapeString($oAutorizacao->sTipoLicitacao);
                foreach ($oAutorizacao->itens as $oItem) {
                    $oItem->observacao = db_stdClass::normalizeStringJsonEscapeString($oItem->observacao);
                }
            }
            db_inicio_transacao();
            $oSolicitacaoCompra = new solicitacaoCompra($oParam->iCodigo);
            $oRetorno->autorizacoes = $oSolicitacaoCompra->gerarAutorizacoes($oParam->aAutorizacoes);

            db_fim_transacao(false);

            $oRetorno->status = 1;
            $oRetorno->message = urlencode("Autorização efetuada com sucesso.");

        } catch (Exception $eErro) {
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($eErro->getMessage());
            db_fim_transacao(true);
        }

        break;

}

echo JSON::create()->stringify($oRetorno);
