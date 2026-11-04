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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("classes/db_pctipocompra_classe.php"));
require_once(modification("classes/db_emptipo_classe.php"));
require_once(modification("model/licitacao.model.php"));
require_once(modification("model/itemSolicitacao.model.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("model/CgmBase.model.php"));
require_once(modification("model/CgmFisico.model.php"));
require_once(modification("model/CgmJuridico.model.php"));
require_once(modification("classes/solicitacaocompras.model.php"));
require_once(modification("model/ProcessoCompras.model.php"));
require_once(modification("model/empenho/AutorizacaoEmpenho.model.php"));
use ECidade\Financeiro\Orcamento\Repository\RecursoRepository as RecursoRepository;
db_app::import("empenho.*");
$oDaoPcTipoCompra = new cl_pctipocompra();
$oDaoEmpTipo = new cl_emptipo();

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->message = "";
$oRetorno->erro = false;

switch ($oParam->exec) {
    case "getProcessoAdministrativo":
        $oRetorno->pc90_numeroprocesso = '';
        $oRetorno->numeroLicitacao = '';
        $oRetorno->anoLicitacao = '';
        $oRetorno->instituicaoLicitacao = '';
        // Caso venha da licitacao

        // Verifica se o processo foi preenchido apenas no cadastro da licitacao
        $oDaoLicLicita = db_utils::getDao('liclicita');
        $campos = "l20_procadmin, p58_numero || '/' || p58_ano as processo, l20_numero, l20_anousu, l20_instit";
        $sSqlLicitacao = $oDaoLicLicita->sql_query($oParam->iOrigem, $campos);
        $rsBuscaLicitacao = $oDaoLicLicita->sql_record($sSqlLicitacao);

        if ($oDaoLicLicita->numrows > 0) {
            $oProcesso = db_utils::fieldsMemory($rsBuscaLicitacao, 0);
            // verifica se possui processo informado no sistema
            if ($oProcesso->l20_procadmin) {
                $oRetorno->pc90_numeroprocesso = $oProcesso->l20_procadmin;
            }
            // verifica se possui processo cadastrado no sistema
            if ($oProcesso->processo) {
                $oRetorno->pc90_numeroprocesso = $oProcesso->processo;
            }
            $oRetorno->pc90_numeroprocesso = urlencode($oRetorno->pc90_numeroprocesso);
            $oRetorno->numeroLicitacao = $oProcesso->l20_numero;
            $oRetorno->anoLicitacao = $oProcesso->l20_anousu;
            $oRetorno->instituicaoLicitacao = $oProcesso->l20_instit;
        }

        break;

    case "pesquisaDadosAcordoOrigem":
        $oAcordo = new Acordo($oParam->acordo);
        $oDaoSolicitacao = new cl_solicita();
        $oDaoSolicitaVinculo = new cl_solicitavinculo();

        //Caso a origem do acordo for processo de compras(1)
        if ($oAcordo->getOrigem() == 1) {
            $processo = $oAcordo->getProcessosDeCompras();
            $codigoProcessoCompras = $processo[0]->getCodigo();
            $processoCompras = new ProcessoCompras($codigoProcessoCompras);
            $dadosAutorizacaoEmpenho = $processoCompras->getLicitacao();

            if ($dadosAutorizacaoEmpenho->getCodigo() != null) {
                $oStdDados = $dadosAutorizacaoEmpenho->getDados();
                $oRetorno->dados = [];
                $oStdLicitacao = new stdClass();
                $oStdLicitacao->numeroLicitacao      = $dadosAutorizacaoEmpenho->getEdital();
                $oStdLicitacao->anoLicitacao         = $dadosAutorizacaoEmpenho->getAno();
                $oStdLicitacao->iModalidadeLicitacao = $oStdDados->pc50_codcom;
                $oStdLicitacao->instituicaoLicitacao = $oStdDados->l20_instit;
                $oStdLicitacao->sModalidadeLicitacao = utf8_encode($oStdDados->pc50_codcom);
                $oStdLicitacao->sModalidadeLicitacao = utf8_encode($oStdDados->pc50_codcom);
                $oRetorno->dados[] = $oStdLicitacao;
            }
        } else {
            $aLicitacoesVinculadas = $oAcordo->getLicitacoes();
            if (count($aLicitacoesVinculadas) == 0) {
                $aProcessosDeComprasVinculados = $oAcordo->getProcessosDeCompras();
                foreach ($aProcessosDeComprasVinculados as $processoCompras) {
                    $aLicitacoesVinculadas[] = $processoCompras->getLicitacao();
                }
            }
            $oRetorno->dados = [];
            foreach ($aLicitacoesVinculadas as $oLicitacao) {

                $oStdDados     = $oLicitacao->getDados();
                $oStdLicitacao = new stdClass();
                $oStdLicitacao->numeroLicitacao      = $oStdDados->l20_numero;
                $oStdLicitacao->anoLicitacao         = $oStdDados->l20_anousu;
                $oStdLicitacao->instituicaoLicitacao = $oStdDados->l20_instit;
                $oStdLicitacao->iModalidadeLicitacao = $oStdDados->pc50_codcom;
                $oStdLicitacao->sModalidadeLicitacao = utf8_encode($oStdDados->pc50_codcom);
                $oRetorno->dados[] = $oStdLicitacao;
            }
        }

        break;

    case "getTipoLicitacao":

        $aWhere = array(
            "l03_instit = " . db_getsession("DB_instit"),
            "l03_codcom = {$oParam->iTipoCompra}",
            "l03_modalidadeativa = 't'"
        );

        $oDaoCfgLiclicita = new cl_cflicita();
        $sSqlTipoLicitacao = $oDaoCfgLiclicita->sql_query_file(null, "l03_tipo, l03_descr", 'l03_tipo', implode(' and ', $aWhere));
        $rsTipoLicitacao = $oDaoCfgLiclicita->sql_record($sSqlTipoLicitacao);
        $oRetorno->aTiposLicitacao = array();

        if ($oDaoCfgLiclicita->numrows > 0) {
            for ($iTipoLicitacao = 0; $iTipoLicitacao < $oDaoCfgLiclicita->numrows; $iTipoLicitacao++) {
                $oRetorno->aTiposLicitacao[] = db_utils::fieldsMemory($rsTipoLicitacao, $iTipoLicitacao);
            }
        }

        $oRetorno->obrigaLicitacao = false;
        if (!empty($oParam->iTipoCompra)) {
            $dao = new cl_pctipocompra();
            $rs = db_query($dao->sql_query($oParam->iTipoCompra, 'l44_obrigalicitacao', "pc50_descr"));

            $oRetorno->obrigaLicitacao = db_utils::fieldsMemory($rs, 0)->l44_obrigalicitacao === 't';
        }

        $oRetorno->complementos = [];
        foreach (\ECidade\Financeiro\Orcamento\Recurso\Complemento::getAll() as $complemento => $descricao) {
            $oRetorno->complementos[] = [
                "complemento" => $complemento,
                "descricao" => urlencode($descricao)
            ];
        }
        $oRetorno->listaMetaHistoricos = AutorizacaoEmpenho::getListaCompletaMetasHistoricos();

        break;
    /**
     * Busca os Tipos de Compra
     */
    case "getTipoCompraEmpenho":
        /**
         * Buscando resumo da solicitacao de compras
         */
        $oRetorno->sResumo = "";
        $oDaoSolicitacao = new cl_solicita();

        $sWhereResumo = "l20_codigo = {$oParam->iCodigo}";
        $oDaoBuscaDotacao = new licitacao($oParam->iCodigo);

        $aSolicitacaoComDotacaoAnoAnterior = $oDaoBuscaDotacao->getSolicitacoesDotacaoAnoAnterior();
        $oRetorno->solicitacaoComDotacaoAnoAnterior = $aSolicitacaoComDotacaoAnoAnterior;

        $sOrderResumo = "pc10_numero desc limit 1";
        $sSqlBuscaResumo = $oDaoSolicitacao->sql_query_estregistro(null, "pc10_resumo", $sOrderResumo, $sWhereResumo);
        $rsResumo = $oDaoSolicitacao->sql_record($sSqlBuscaResumo);

        if ($rsResumo && pg_num_rows($rsResumo) > 0) {
            $oResumo = db_utils::fieldsMemory($rsResumo, 0, false, false, false);
            $oRetorno->sResumo = utf8_encode($oResumo->pc10_resumo);
        }
        /*
         * Busca os Tipos de Compra
         */
        $sSqlPcTipoCompra = $oDaoPcTipoCompra->sql_query_file(
            null,
            'pc50_codcom, pc50_descr',
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

        $oDaoLicLicita = new cl_liclicita();
        $sSqlBuscaTipoCompra = $oDaoLicLicita->sql_query($oParam->iCodigo, "pc50_codcom");
        $rsBuscaTipoCompra = $oDaoLicLicita->sql_record($sSqlBuscaTipoCompra);
        if ($oDaoLicLicita->numrows > 0) {
            $iTipoCompra = db_utils::fieldsMemory($rsBuscaTipoCompra, 0)->pc50_codcom;
            $oRetorno->iTipoCompraInicial = $iTipoCompra;
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

    /**
     * Busca os Itens para uma Autorização
     */
    case "getItensParaAutorizacao":
        $oLicitacao = new licitacao($oParam->iCodigo);
        $aItensAutorizacao = $oLicitacao->getItensParaAutorizacao();
        $oRetorno->listaMetaHistoricos = AutorizacaoEmpenho::getListaCompletaMetasHistoricos();
        $oRetorno->aItens = array();
        $oRetorno->complementos = [];
        $dataLimite = date('Y-m-d', db_getsession('DB_datausu'));
        $ano = DB_getsession('DB_anousu');
        foreach ($aItensAutorizacao as $item) {
            $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($item->codigodotacao, $ano);
            $recurso = $dotacao->getDadosRecurso();
            $fonte = $recurso->getFonteRecurso($ano);

            $complementos = [];
            $complementosRec = RecursoRepository::getComplementosByGestao(
                $fonte->gestao,
                $recurso->getRecurso(),
                $ano,
                $dataLimite
            );
            foreach ($complementosRec as &$complemento) {
                $complemento->selecionado = $complemento->codigo == $recurso->getComplemento();
                $complementos[] = $complemento;
            }
            $oRetorno->complementos[$item->codigodotacao] = $complementos;
        }
        foreach ($aItensAutorizacao as $oStdItem) {
            $oStdItem->fornecedor = $oStdItem->fornecedor;
            $oRetorno->aItens[] = $oStdItem;
        }
        break;

    /**
     * Gera Autorização
     */
    case "gerarAutorizacoes":
        if (!isset($oParam->aAutorizacoes)) {
            $oRetorno->status = 2;
            $oRetorno->message = urlencode("Há muitos itens selecionados. É necessário selecionar menos itens para gerar a autorização de empenho.");
        } else {
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

                    foreach ($oAutorizacao->itens as $oItem) {
                        $oItem->observacao = db_stdClass::normalizeStringJsonEscapeString($oItem->observacao);
                    }
                }

                db_inicio_transacao();
                $oLicitacao = new licitacao($oParam->iCodigo);
                $oRetorno->autorizacoes = $oLicitacao->gerarAutorizacoes($oParam->aAutorizacoes);

                db_fim_transacao(false);

                $oRetorno->status = 1;
                $oRetorno->message = urlencode("Autorização efetuada com sucesso.");
            } catch (Exception $eErro) {
                $oRetorno->erro = true;
                $oRetorno->status = 2;
                $oRetorno->message = urlencode($eErro->getMessage());
                db_fim_transacao(true);
            }
        }
        break;
}
echo JSON::create()->stringify($oRetorno);
