<?php
/**
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

/**
 * Class RelatorioEmpenhoClassificacaoCredores
 */
class RelatorioEmpenhoClassificacaoCredores
{
    /** @var integer */
    const SITUACAO_PAGOS = 1;

    /** @var integer */
    const SITUACAO_APAGAR = 2;

    /** @var integer */
    const ORDENAR_DATA_NOTA = 1;

    /** @var integer */
    const ORDENAR_DATA_VENCIMENTO = 2;

    const ORDENAR_DATA_LIQUIDACAO = 3;

    /**
     * @type PDFDocument
     */
    private $oPdf;

    /**
     * @type int
     */
    private $iExercicio;

    /**
     * @type DBDate
     */
    private $dtVencimentoInicial;

    /**
     * @type DBDate
     */
    private $dtVencimentoFinal;

    /**
     * @type int
     */
    private $sClassificacoes;

    /**
     * @type int
     */
    private $iSituacaoPagamento;

    /**
     * @type Instituicao
     */
    private $oInstituicao;

    /**
     * @var string
     */
    private $sFornecedores;

    /**
     * @var string
     */
    private $sRecursos;

    private $iOrdenacao = self::ORDENAR_DATA_NOTA;

    /**
     * @var DBDate
     */
    private $oDataNotaInicial;

    /**
     * @var DBDate
     */
    private $oDataNotaFinal;


    /**
     * @var DBDate
     */
    private $oDataLiquidacaoInicial;

    /**
     * @var DBDate
     */
    private $oDataLiquidacaoFinal;

    /**
     * @param $sFornecedores
     */
    public function setFornecedores($sFornecedores)
    {
        $this->sFornecedores = $sFornecedores;
    }

    /**
     * @param $sRecursos
     */
    public function setRecursos($sRecursos)
    {
        $this->sRecursos = $sRecursos;
    }

    /**
     * @param int $iExercicio
     */
    public function setExercicio($iExercicio)
    {
        $this->iExercicio = $iExercicio;
    }

    /**
     * @param DBDate $dtVencimentoInicial
     */
    public function setVencimentoInicial(DBDate $dtVencimentoInicial)
    {
        $this->dtVencimentoInicial = $dtVencimentoInicial;
    }


    /**
     * @param DBDate $dtVencimentoFinal
     */
    public function setVencimentoFinal(DBDate $dtVencimentoFinal)
    {
        $this->dtVencimentoFinal = $dtVencimentoFinal;
    }

    /**
     * @param string $sClassificacao
     * @throws ParameterException
     */
    public function setClassificacoes($sClassificacao)
    {
        $this->sClassificacoes = $sClassificacao;
    }

    /**
     * @param int $iSituacaoPagamento
     */
    public function setSituacaoPagamento($iSituacaoPagamento)
    {
        $this->iSituacaoPagamento = $iSituacaoPagamento;
    }

    /**
     * @param Instituicao $oInstituicao
     */
    public function setInstituicao(Instituicao $oInstituicao)
    {
        $this->oInstituicao = $oInstituicao;
    }

    /**
     * @param $iOrdenacao
     */
    public function setOrdenacao($iOrdenacao)
    {
        $this->iOrdenacao = (int)$iOrdenacao;
    }

    /**
     * @param DBDate|null $oDataInicial
     */
    public function setDataInicialNota(DBDate $oDataInicial = null)
    {
        $this->oDataNotaInicial = $oDataInicial;
    }

    /**
     * @param DBDate|null $oDataFinal
     */
    public function setDataFinalNota(DBDate $oDataFinal = null)
    {
        $this->oDataNotaFinal = $oDataFinal;
    }

    /**
     * @param DBDate $oDataLiquidacaoInicial
     */
    public function setDataLiquidacaoInicial($oDataLiquidacaoInicial)
    {
        $this->oDataLiquidacaoInicial = $oDataLiquidacaoInicial;
    }

    /**
     * @param DBDate $oDataLiquidacaoFinal
     */
    public function setDataLiquidacaoFinal($oDataLiquidacaoFinal)
    {
        $this->oDataLiquidacaoFinal = $oDataLiquidacaoFinal;
    }


    /**
     * @throws Exception
     */
    public function emitir()
    {
        $aInformacoes = $this->getDadosImprimir();

        $informacoesAgrupadasPorListaRecurso = array();
        /* agrupando as informações por lista de classificação e recurso */
        foreach ($aInformacoes as $stdLinha) {
            $indice = $stdLinha->codigo_classificacao;
            if (empty($informacoesAgrupadasPorListaRecurso[$indice])) {
                $informacoesAgrupadasPorListaRecurso[$indice] = new stdClass();
                $informacoesAgrupadasPorListaRecurso[$indice]->classificacao_codigo = $indice;
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos = array();
            }

            $codigoRecurso = $stdLinha->codigo_recurso;
            $hash = "$codigoRecurso#$stdLinha->complemento";
            if (empty($informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash])) {
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash] = new stdClass();
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash]->codigo = $codigoRecurso;
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash]->descricao = $stdLinha->descricao_recurso;
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash]->complemento = $stdLinha->complemento;
                $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash]->linhas = array();
            }

            $informacoesAgrupadasPorListaRecurso[$indice]->recursos[$hash]->linhas[] = $stdLinha;
        }

        $this->oPdf = new PDFDocument(PDFDocument::PRINT_LANDSCAPE);
        $this->oPdf->SetFillColor(220);
        $this->escreverCabecalhoRelatorio();
        $this->oPdf->SetFontSize(6);
        $this->oPdf->open();
        $this->oPdf->addPage();
        $lPreencher = false;
        $totalAPagarGeral = 0;
        $totalPagoGeral = 0;
        foreach ($informacoesAgrupadasPorListaRecurso as $codigoLista => $lista) {
            if ($this->quebrarPagina()) {
                $this->oPdf->addPage();
                $this->imprimeCabecalhoLista($codigoLista);
                $this->escreverCabecalho();
            }

            $this->imprimeCabecalhoLista($codigoLista);
            $totalListaPago = 0;
            $totalListaAPagar = 0;
            foreach ($lista->recursos as $codigoRecurso => $dadosRecurso) {
                $totalRecursosPago = 0;
                $totalRecursosAPagar = 0;
                $this->imprimeCabecalhoRecurso($dadosRecurso);
                $this->escreverCabecalho();
                foreach ($dadosRecurso->linhas as $oStdInformacao) {
                    if ($this->pagos() && empty($oStdInformacao->data_pagamento)) {
                        continue;
                    }

                    if ($this->aPagar() && !empty($oStdInformacao->data_pagamento)) {
                        continue;
                    }

                    if ($this->quebrarPagina()) {
                        $this->oPdf->addPage();
                        $this->imprimeCabecalhoRecurso($dadosRecurso);
                        $this->escreverCabecalho();
                    }

                    /*@todo verificar codigo daqui */

                    $sNumeroEmpenho = "{$oStdInformacao->numero_empenho}/{$oStdInformacao->ano}";

                    $sDataRecebimento = '-';
                    if (!empty($oStdInformacao->data_recebimento)) {
                        $oDataRecebimento = new DBDate($oStdInformacao->data_recebimento);
                        $sDataRecebimento = $oDataRecebimento->getDate(DBDate::DATA_PTBR);
                    }

                    $sDataVencimento = '-';
                    if (!empty($oStdInformacao->data_vencimento)) {
                        $oDataVencimento = new DBDate($oStdInformacao->data_vencimento);
                        $sDataVencimento = $oDataVencimento->getDate(DBDate::DATA_PTBR);
                    }

                    $sDataPagamento = '-';
                    if (!empty($oStdInformacao->data_pagamento)) {
                        $oDataPagamento = new DBDate($oStdInformacao->data_pagamento);
                        $sDataPagamento = $oDataPagamento->getDate(DBDate::DATA_PTBR);
                    }
                    $dataLiquidacao = '-';
                    if (!empty($oStdInformacao->data_liquidacao)) {
                        $oDataLiquidacao = new DBDate($oStdInformacao->data_liquidacao);
                        $dataLiquidacao = $oDataLiquidacao->getDate(DBDate::DATA_PTBR);
                    }

                    $this->oPdf->cell(20, 4, $sNumeroEmpenho, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
                    $this->oPdf->cell(20, 4, $oStdInformacao->desdobramento, 0, 0, PDFDocument::ALIGN_CENTER,
                        $lPreencher);
                    $this->oPdf->cell(20, 4, $oStdInformacao->codigo_ordem, 0, 0, PDFDocument::ALIGN_CENTER,
                        $lPreencher);
                    $this->oPdf->cell(25, 4, $oStdInformacao->numero_nota, 0, 0, PDFDocument::ALIGN_CENTER,
                        $lPreencher);
                    $this->oPdf->cell(10, 4, $oStdInformacao->codigo_recurso, 0, 0, PDFDocument::ALIGN_CENTER,
                        $lPreencher);
                    $this->oPdf->cell(20, 4, $sDataRecebimento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
                    $this->oPdf->cell(20, 4, $dataLiquidacao, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
                    $this->oPdf->cell(20, 4, $sDataVencimento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
                    $this->oPdf->cell(20, 4, $sDataPagamento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
                    $this->oPdf->cell(82, 4, substr($oStdInformacao->razao_social, 0, 95), 0, 0,
                        PDFDocument::ALIGN_LEFT, $lPreencher);
                    $this->oPdf->cell(20, 4, db_formatar($oStdInformacao->valor, 'f'), 0, 1, PDFDocument::ALIGN_RIGHT,
                        $lPreencher);

                    $iPaginaAntesJustificativa = $this->oPdf->getCurrentPage();
                    if (!empty($oStdInformacao->justificativa_pagamento)) {
                        $this->oPdf->setBold(true);
                        $this->oPdf->cell($this->oPdf->getAvailWidth(), 4,
                            "Justificativa da Suspensão da Ordem Cronológica de Pagamento do Empenho {$sNumeroEmpenho}:",
                            0, 1, PDFDocument::ALIGN_LEFT, $lPreencher);
                        $this->oPdf->setBold(false);
                        $this->oPdf->MultiCell($this->oPdf->getAvailWidth(), 4,
                            $oStdInformacao->justificativa_pagamento, 0, PDFDocument::ALIGN_JUSTIFY, $lPreencher);
                    }

                    $oListaClassificacao = ListaClassificacaoCredorRepository::getPorCodigo($oStdInformacao->codigo_classificacao);
                    if ($oListaClassificacao->dispensa() && !empty($oStdInformacao->justificativa_dispensa)) {
                        $this->oPdf->setBold(true);
                        $this->oPdf->cell($this->oPdf->getAvailWidth(), 4,
                            "Justificativa da Dispensa da Ordem Cronológica de Pagamento do Empenho {$sNumeroEmpenho}:",
                            0, 1, PDFDocument::ALIGN_LEFT, $lPreencher);
                        $this->oPdf->setBold(false);
                        $this->oPdf->MultiCell($this->oPdf->getAvailWidth(), 4, $oStdInformacao->justificativa_dispensa,
                            0, PDFDocument::ALIGN_JUSTIFY, $lPreencher);
                        $this->oPdf->ln(3);
                    }

                    $lPreencher = !$lPreencher;

                    /* totalizador por recurso */
                    $totalRecursosPago += !empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;
                    $totalRecursosAPagar += empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;

                    /* totalizador por lista */
                    $totalListaPago += !empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;
                    $totalListaAPagar += empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;

                    /* totalizador geral */
                    $totalPagoGeral += !empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;
                    $totalAPagarGeral += empty($oStdInformacao->data_pagamento) ? $oStdInformacao->valor : 0;
                }

                if ($this->exibirPagos()) {
                    $this->imprimirTotalizador("TOTAL RECURSO PAGO:", $totalRecursosPago,
                        $this->todos() || $this->pagos());
                }

                if ($this->exibirAPagar()) {
                    $this->imprimirTotalizador("TOTAL RECURSO À PAGAR:", $totalRecursosAPagar, $this->aPagar());
                }
            }

            if ($this->exibirPagos()) {
                $this->imprimirTotalizador("TOTAL DA LISTA PAGO:", $totalListaPago, $this->todos() || $this->pagos());
            }

            if ($this->exibirAPagar()) {
                $this->imprimirTotalizador("TOTAL DA LISTA À PAGAR:", $totalListaAPagar, $this->aPagar());
            }
        }

        if ($this->exibirPagos()) {
            $this->imprimirTotalizador("TOTAL GERAL PAGO:", $totalPagoGeral, $this->todos() || $this->pagos());
        }

        if ($this->exibirAPagar()) {
            $this->imprimirTotalizador("TOTAL GERAL À PAGAR:", $totalAPagarGeral, $this->aPagar());
        }

        $this->oPdf->showPDF();
    }

    private function quebrarPagina()
    {
        return $this->oPdf->getAvailHeight() < 10;
    }


    /**
     *
     * nao utilizar mais este metodo este é o código que imprimia o relatório antigamente, antes das quebras solicitadas
     * pelo Leandro Souza
     *
     * @throws BusinessException
     * @throws ParameterException
     * @deprecated
     */
    private function naoUtilizar()
    {

        foreach ($aInformacoes as $iIndice => $oStdInformacao) {

            if (!isset($aTotalizadoresPorLista[$oStdInformacao->codigo_classificacao])) {
                $oTotalizadorLista = new stdClass();
                $oTotalizadorLista->codigo_lista = $oStdInformacao->codigo_classificacao;
                $oTotalizadorLista->total_pago = 0;
                $oTotalizadorLista->total_pagar = 0;
                $aTotalizadoresPorLista[$oStdInformacao->codigo_classificacao] = $oTotalizadorLista;
            }

            if (empty($oStdInformacao->data_pagamento)) {
                $aTotalizadoresPorLista[$oStdInformacao->codigo_classificacao]->total_pagar += $oStdInformacao->valor;
            } else {
                $aTotalizadoresPorLista[$oStdInformacao->codigo_classificacao]->total_pago += $oStdInformacao->valor;
            }

            if ($this->pagos() && empty($oStdInformacao->data_pagamento)) {
                continue;
            }

            if ($this->aPagar() && !empty($oStdInformacao->data_pagamento)) {
                continue;
            }

            if ($this->quebrarPagina()) {
                $this->oPdf->addPage();
                $this->escreverCabecalho();
            }

            if ($iUltimaClassificaoImpressa != $oStdInformacao->codigo_classificacao) {
                if ($this->exibirPagos()) {
                    $this->imprimirTotalizador("TOTAL PAGO:",
                        $aTotalizadoresPorLista[$iUltimaClassificaoImpressa]->total_pago,
                        $this->todos() || $this->pagos());
                }
                if ($this->exibirAPagar()) {
                    $this->imprimirTotalizador("TOTAL A PAGAR:",
                        $aTotalizadoresPorLista[$iUltimaClassificaoImpressa]->total_pagar, $this->aPagar());
                }
                $this->imprimeCabecalhoLista($oStdInformacao->codigo_classificacao);
            }

            $sNumeroEmpenho = "{$oStdInformacao->numero_empenho}/{$oStdInformacao->ano}";

            $sDataRecebimento = '-';
            if (!empty($oStdInformacao->data_recebimento)) {
                $oDataRecebimento = new DBDate($oStdInformacao->data_recebimento);
                $sDataRecebimento = $oDataRecebimento->getDate(DBDate::DATA_PTBR);
            }

            $sDataVencimento = '-';
            if (!empty($oStdInformacao->data_vencimento)) {
                $oDataVencimento = new DBDate($oStdInformacao->data_vencimento);
                $sDataVencimento = $oDataVencimento->getDate(DBDate::DATA_PTBR);
            }

            $sDataPagamento = '-';
            if (!empty($oStdInformacao->data_pagamento)) {
                $oDataPagamento = new DBDate($oStdInformacao->data_pagamento);
                $sDataPagamento = $oDataPagamento->getDate(DBDate::DATA_PTBR);
            }

            $this->oPdf->cell(20, 4, $sNumeroEmpenho, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(20, 4, $oStdInformacao->elemento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $oStdInformacao->codigo_ordem, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $oStdInformacao->numero_nota, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(10, 4, $oStdInformacao->codigo_recurso, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $sDataRecebimento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $sDataRecebimento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $sDataVencimento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(25, 4, $sDataPagamento, 0, 0, PDFDocument::ALIGN_CENTER, $lPreencher);
            $this->oPdf->cell(82, 4, substr($oStdInformacao->razao_social, 0, 95), 0, 0, PDFDocument::ALIGN_LEFT,
                $lPreencher);
            $this->oPdf->cell(20, 4, db_formatar($oStdInformacao->valor, 'f'), 0, 1, PDFDocument::ALIGN_RIGHT,
                $lPreencher);

            $iPaginaAntesJustificativa = $this->oPdf->getCurrentPage();
            if (!empty($oStdInformacao->justificativa_pagamento)) {
                $this->oPdf->setBold(true);
                $this->oPdf->cell($this->oPdf->getAvailWidth(), 4,
                    "Justificativa da Suspensão da Ordem Cronológica de Pagamento do Empenho {$sNumeroEmpenho}:", 0, 1,
                    PDFDocument::ALIGN_LEFT, $lPreencher);
                $this->oPdf->setBold(false);
                $this->oPdf->MultiCell($this->oPdf->getAvailWidth(), 4, $oStdInformacao->justificativa_pagamento, 0,
                    PDFDocument::ALIGN_JUSTIFY, $lPreencher);
            }

            $oListaClassificacao = ListaClassificacaoCredorRepository::getPorCodigo($oStdInformacao->codigo_classificacao);
            if ($oListaClassificacao->dispensa() && !empty($oStdInformacao->justificativa_dispensa)) {
                $this->oPdf->setBold(true);
                $this->oPdf->cell($this->oPdf->getAvailWidth(), 4,
                    "Justificativa da Dispensa da Ordem Cronológica de Pagamento do Empenho {$sNumeroEmpenho}:", 0, 1,
                    PDFDocument::ALIGN_LEFT, $lPreencher);
                $this->oPdf->setBold(false);
                $this->oPdf->MultiCell($this->oPdf->getAvailWidth(), 4, $oStdInformacao->justificativa_dispensa, 0,
                    PDFDocument::ALIGN_JUSTIFY, $lPreencher);
                $this->oPdf->ln(3);
            }

            $lPreencher = !$lPreencher;
            $iUltimaClassificaoImpressa = $oStdInformacao->codigo_classificacao;

            $iPaginaDepoisJustificativa = $this->oPdf->getCurrentPage();
            if ($iPaginaAntesJustificativa != $iPaginaDepoisJustificativa) {
                $this->escreverCabecalho();
            }
        }

        if ($this->exibirPagos()) {
            $this->imprimirTotalizador("TOTAL PAGO:", $aTotalizadoresPorLista[$iUltimaClassificaoImpressa]->total_pago,
                $this->todos() || $this->pagos());
        }

        if ($this->exibirAPagar()) {
            $this->imprimirTotalizador("TOTAL A PAGAR:",
                $aTotalizadoresPorLista[$iUltimaClassificaoImpressa]->total_pagar, $this->aPagar());
        }
        $this->oPdf->Ln();

        $nValorTotalPago = 0;
        $nValorTotalPagar = 0;
        foreach ($aTotalizadoresPorLista as $oTotalizadorLista) {
            $nValorTotalPagar += $oTotalizadorLista->total_pagar;
            $nValorTotalPago += $oTotalizadorLista->total_pago;
        }

        if ($this->exibirPagos()) {
            $this->imprimirTotalizador("TOTAL GERAL PAGO:", $nValorTotalPago, $this->todos() || $this->pagos());
        }

        if ($this->exibirAPagar()) {
            $this->imprimirTotalizador("TOTAL GERAL A PAGAR:", $nValorTotalPagar, $this->aPagar());
        }
    }

    /**
     * @param $iCodigo
     */
    private function imprimeCabecalhoLista($iCodigo)
    {
        $oListaClassificacao = ListaClassificacaoCredorRepository::getPorCodigo($iCodigo);

        $this->oPdf->ln(5);
        $this->oPdf->setBold(true);
        $this->oPdf->SetFontSize(8);
        $sClassificacao = $oListaClassificacao->getDescricao();
        $this->oPdf->cell(
            $this->oPdf->getAvailWidth(),
            4,
            "Lista de Classificação de Credores: {$sClassificacao}",
            'B',
            1
        );
        $this->oPdf->setBold(false);
        $this->oPdf->SetFontSize(6);
    }

    private function imprimeCabecalhoRecurso($dadosRecurso)
    {
        $this->oPdf->setBold(true);
        $this->oPdf->SetFontSize(8);
        $titulo = "Recurso {$dadosRecurso->codigo} - {$dadosRecurso->descricao} - {$dadosRecurso->complemento}";
        $this->oPdf->cell($this->oPdf->getAvailWidth(), 4, $titulo, '1', 1, 'L', 1);
        $this->oPdf->setBold(false);
        $this->oPdf->SetFontSize(6);
    }


    /**
     * Escreve o cabeçalho
     */
    private function escreverCabecalho()
    {// 5+ 17+10
        $this->oPdf->setBold(true);
        $this->oPdf->cell(20, 4, 'Empenho', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Desdobramento', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Número O.P.', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(25, 4, 'Número N.F.', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(10, 4, 'Recurso', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Recebimento', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Liquidacao', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Vencimento', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Pagamento', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(82, 4, 'Razão Social', 1, 0, PDFDocument::ALIGN_CENTER, 1);
        $this->oPdf->cell(20, 4, 'Valor', 1, 1, PDFDocument::ALIGN_CENTER, 1);

        $this->oPdf->setBold(false);
    }

    /**
     * Escreve header do relatório
     */
    private function escreverCabecalhoRelatorio()
    {
        $this->oPdf->addHeaderDescription("Empenhos por Lista de Classificação de Credores");
        $this->oPdf->addHeaderDescription("");
        if (!empty($this->iExercicio)) {
            $this->oPdf->addHeaderDescription("Exercício: {$this->iExercicio}");
        }

        if (!empty($this->dtVencimentoInicial)) {
            $this->oPdf->addHeaderDescription("Data de Vencimento Inicial: {$this->dtVencimentoInicial->getDate(DBDate::DATA_PTBR)}");
        }

        if (!empty($this->dtVencimentoFinal)) {
            $this->oPdf->addHeaderDescription("Data de Vencimento Final: {$this->dtVencimentoFinal->getDate(DBDate::DATA_PTBR)}");
        }

        $sDescricaoSituacao = "Todos";
        if ($this->pagos()) {
            $sDescricaoSituacao = "Pagos";
        } elseif ($this->aPagar()) {
            $sDescricaoSituacao = "A Pagar";
        }
        $this->oPdf->addHeaderDescription("Situação: {$sDescricaoSituacao}");
    }

    /**
     * Busca principal com todos dados à serem inseridos
     * @throws Exception
     */
    private function getDadosImprimir()
    {
        $aCampos = array(
            "e60_numemp  as codigo_empenho",
            "e60_codemp  as numero_empenho",
            "e60_anousu  as ano",
            "e69_codnota as codigo_nota",
            "e69_numero  as numero_nota",
            "e50_codord  as codigo_ordem",
            "z01_nome    as razao_social",
            "e69_dtvencimento as data_vencimento",
            "e69_dtrecebe as data_recebimento",
            "gestao as codigo_recurso",
            "o200_descricao as complemento",
            "descricao as descricao_recurso",
            "e53_valor  as ordem_valor_total",
            "min(c70_data)  as data_liquidacao",
            "e53_vlranu as ordem_valor_anulado",
            'e09_sequencial',
            'cc30_codigo as codigo_classificacao',
            'corempagemov.k12_data as data_pagamento',
            'sum(CASE WHEN k12_valor IS NULL AND c53_tipo = 20 THEN c70_valor  WHEN k12_valor IS NULL AND c53_tipo = 21 THEN c70_valor * -1 else k12_valor END) AS valor',
            'trim(e09_justificativa) as justificativa_pagamento',
            'trim(cc31_justificativa) as justificativa_dispensa',
            'o56_elemento as desdobramento',
        );

        $aWhere = array(
            "e60_instit = {$this->oInstituicao->getCodigo()}",
            'e81_cancelado is null',
            "e53_vlranu <> e53_valor",
            "not exists(
              (select 1 from (
                   select cc36_dataretorno from empnotasuspensao where cc36_empnota = e69_codnota and cc36_dataretorno is null order by cc36_sequencial desc limit 1
                ) as x where cc36_dataretorno is null
              )
            )"
        );

        if ($this->exibirAPagar() && !$this->todos()) {
            $aWhere[] = "round(e53_valor, 2) <> round(e53_vlrpag +e53_vlranu, 2)";
        }

        if (!empty($this->iExercicio)) {
            $aWhere[] = "e60_anousu = {$this->iExercicio}";
        }

        if (!empty($this->dtVencimentoInicial)) {
            $aWhere[] = "e69_dtvencimento >= '{$this->dtVencimentoInicial->getDate()}'";
        }

        if (!empty($this->dtVencimentoFinal)) {
            $aWhere[] = "e69_dtvencimento <= '{$this->dtVencimentoFinal->getDate()}'";
        }

        if (!empty($this->sClassificacoes)) {
            $aWhere[] = "cc31_classificacaocredores in ({$this->sClassificacoes})";
        }

        if (!empty($this->sFornecedores)) {
            $aWhere[] = "z01_numcgm in ({$this->sFornecedores})";
        }

        if (!empty($this->sRecursos)) {
            $aWhere[] = "o15_codigo in ({$this->sRecursos})";
        }

        if (!empty($this->oDataNotaInicial)) {
            $aWhere[] = "e69_dtrecebe >= '{$this->oDataNotaInicial->getDate()}'";
        }

        if (!empty($this->oDataNotaFinal)) {
            $aWhere[] = "e69_dtrecebe <= '{$this->oDataNotaFinal->getDate()}'";
        }

        if (!empty($this->oDataLiquidacaoInicial)) {
            $aWhere[] = "  c70_data >='{$this->oDataLiquidacaoInicial->getDate()}'";
        }
        if (!empty($this->oDataLiquidacaoFinal)) {
            $aWhere[] = "  c70_data <='{$this->oDataLiquidacaoFinal->getDate()}'";
        }

        $ordenacao = 'e69_dtrecebe';
        switch ($this->iOrdenacao) {
            case self::ORDENAR_DATA_NOTA:
                $ordenacao = 'e69_dtrecebe';
                break;
            case self::ORDENAR_DATA_VENCIMENTO;
                $ordenacao = 'e69_dtvencimento';
                break;
            case self::ORDENAR_DATA_LIQUIDACAO;
                $ordenacao = '13';
                break;

        }
        $sWhere = implode(' and ', $aWhere);
        $sCampos = implode(', ', $aCampos);
        $sGroupOrder = " group by corempagemov.k12_data, e60_numemp, e60_anousu, e69_codnota, e69_numero, e50_codord, z01_nome, e53_valor, o56_elemento, descricao, ";
        $sGroupOrder .= " e53_vlranu, e53_vlrpag , cc31_classificacaocredores, e09_justificativa, cc31_justificativa, e09_sequencial, cc30_codigo, gestao, o200_descricao";
        $sGroupOrder .= " order by cc31_classificacaocredores, gestao, {$ordenacao} ";
        $oDaoEmpenho = new cl_empempenho();
        $sSqlBusca = $oDaoEmpenho->sql_query_empenho_classificacao_nota($sCampos, $sWhere . $sGroupOrder);

        $rsBuscaEmpenhos = $oDaoEmpenho->sql_record($sSqlBusca);

        if ($oDaoEmpenho->erro_status == "0") {
            throw new Exception("Nenhum registro encontrado para os filtros selecionados.");
        }

        return db_utils::getCollectionByRecord($rsBuscaEmpenhos);
    }

    /**
     * Informa se deve imprimir somente os movimentos pagos.
     * @return bool
     */
    private function pagos()
    {
        return $this->iSituacaoPagamento == self::SITUACAO_PAGOS;
    }

    /**
     * Informa se deve imprimir somente os movimentos a pagar.
     * @return bool
     */
    private function aPagar()
    {
        return $this->iSituacaoPagamento == self::SITUACAO_APAGAR;
    }

    /**
     * Informa se deve imprimir todos os movimentos.
     * @return bool
     */
    private function todos()
    {
        return $this->iSituacaoPagamento != self::SITUACAO_APAGAR && $this->iSituacaoPagamento != self::SITUACAO_PAGOS;
    }

    /**
     * @return bool
     */
    private function exibirPagos()
    {
        return $this->todos() || $this->pagos();
    }

    /**
     * @return bool
     */
    private function exibirAPagar()
    {
        return $this->todos() || $this->aPagar();
    }

    /**
     * Imprimir um totalizador.
     * @param string $sTitulo Título para o totalizador.
     * @param float $nValor Valor do totalizador.
     * @param boolean $lBorda Indica se deve adicionar borda superior.
     */
    private function imprimirTotalizador($sTitulo, $nValor, $lBorda)
    {
        $sBorda = $lBorda ? 'T' : 0;

        $this->oPdf->setBold(true);
        $this->oPdf->cell(217, 4, '', $sBorda);
        $this->oPdf->cell(30, 4, $sTitulo, $sBorda, 0, PDFDocument::ALIGN_LEFT, 0);
        $this->oPdf->cell(30, 4, db_formatar($nValor, 'f'), $sBorda, 1, PDFDocument::ALIGN_RIGHT, 0);

        $this->oPdf->setBold(false);
    }
}
