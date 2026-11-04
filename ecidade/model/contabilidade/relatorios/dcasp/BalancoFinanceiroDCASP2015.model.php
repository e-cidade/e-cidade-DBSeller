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

use ECidade\Configuracao\RelatorioLegal\Enum\OrigemDadosEnum;

/**
 * Class BalancoFinanceiroDCASP2015
 */
class BalancoFinanceiroDCASP2015 extends RelatoriosLegaisBase
{

    /**
     * @type int
     */
    const CODIGO_RELATORIO = 152;

    const LINHA_INICIO_DISPENDIOS = 24;

    const LINHA_FINAL_DISPENDIOS = 46;

    const TIPO_ANALITICO = "A";

    const TIPO_SINTETICO = "S";

    /**
     *
     * @var PDFDocument
     */
    protected $oPdf;

    /**
     * Nome da instituição a ser exibida no relatório.
     *
     * @var string
     */
    protected $sDescricaoInstituicao;

    /**
     * Nome do período a ser exibido no relatório.
     *
     * @var string
     */
    protected $sDescricaoPeriodo;

    /**
     * Determina se deve exibir as informações do exercício anterior.
     *
     * @var boolean
     */
    protected $lExibirExercicioAnterior = true;

    /**
     * Linhas finais de cada seção. Utilizado somente para formatar a linha
     *
     * @var array
     */
    protected $aLinhasFinais = array(23, 46);

    /**
     * Linhas que podem ter Recursos, caso o relatório seja emitido
     * como analítico
     *
     * @var array<int,int>
     */
    protected $aLinhasComRecurso = array(4, 5, 6, 7, 8, 9, 27, 28, 29, 30, 31, 32, 33);

    /**
     * Tipo de impressão (Analítico ou Sintético)
     * Utilizar as constantes TIPO_ANALITICO e TIPO_SINTETICO
     *
     * @var string
     */
    protected $sTipo;

    /**
     * @var integer
     */
    protected $iAltura;

    /**
     * @var integer
     */
    protected $iLargura;

    /**
     * se deve imprimir o quadro auxiliar
     * @var boolean
     */
    protected $imprimirQuadroAuxiliar = false;

    protected $linhasQuadroAuxiliar = [2, 3, 4, 5, 6, 7, 8, 9];

    protected $linhasProcessadaAuxiliar = [];

    /**
     * @param int $iAnoUsu
     * @param int $iCodigoRelatorio
     * @param int $iCodigoPeriodo
     * @see RelatoriosLegaisBase
     */
    public function __construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo)
    {
        parent::__construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo);

        $this->oPdf = new PDFDocument();
        $this->iAltura = 4;
        $this->iLargura = $this->oPdf->getAvailWidth() - 10;
    }

    /**
     *
     * @param boolean $lExibirExercicioAnterior
     */
    public function setExibirExercicioAnterior($lExibirExercicioAnterior)
    {
        $this->lExibirExercicioAnterior = $lExibirExercicioAnterior;
    }

    /**
     * @param string $sTipo
     */
    public function setTipo($sTipo)
    {
        $this->sTipo = $sTipo;
    }

    public function getDados()
    {
        $this->aLinhasConsistencia = $this->getLinhasRelatorio();
        $this->processarTiposDeCalculo();

        $oDataInicialAnterior = clone $this->getDataInicial();
        $oDataInicialAnterior->modificarIntervalo('-1 year');
        $oDataFinalAnterior = clone $this->getDataFinal();
        $oDataFinalAnterior->modificarIntervalo('-1 year');

        if ($this->sTipo == static::TIPO_ANALITICO) {
            foreach ($this->aLinhasComRecurso as $iLinha) {
                foreach ($this->aLinhasConsistencia[$iLinha]->colunas as $oColuna) {
                    $campoRecurso = 'o15_descr';
                    if ($this->aLinhasConsistencia[$iLinha]->origem == OrigemDadosEnum::BALANCETE_DESPESA) {
                        $campoRecurso = 'descricao';
                    }
                    $oColuna->agrupar = (object)array(
                        'nome' => 'recursos',
                        'campo' => ($iLinha <= 9 ? 'o70_codigo' : 'o58_codigo'),
                        'descricao' => $campoRecurso
                    );
                }
            }
        }


        /**
         * Executa o Balancete da receita
         */
        if (!empty($this->aLinhasProcessarReceita)) {
            $where = "o70_instit in ({$this->getInstituicoes()})";

            $rsBalanceteReceita = $this->balanceteReceita(
                $where,
                $this->iAnoUsu,
                $this->getDataInicial(),
                $this->getDataFinal()
            );

            $this->limparEstruturaBalanceteReceita();

            if ($this->lExibirExercicioAnterior) {
                $rsBalanceteReceitaAnterior = $this->balanceteReceita(
                    $where,
                    $this->iAnoUsu - 1,
                    $oDataInicialAnterior,
                    $oDataFinalAnterior
                );

                $this->limparEstruturaBalanceteReceita();
            }


            foreach ($this->aLinhasProcessarReceita as $iLinha) {
                $oLinha = $this->aLinhasConsistencia[$iLinha];

                $aColunas = $this->getColunasPorLinha($oLinha, array(0));
                RelatoriosLegaisBase::calcularValorDaLinha(
                    $rsBalanceteReceita,
                    $oLinha,
                    $aColunas,
                    RelatoriosLegaisBase::TIPO_CALCULO_RECEITA
                );

                if ($this->lExibirExercicioAnterior) {
                    $aColunas = $this->getColunasPorLinha($oLinha, array(1));
                    $oLinha->vlrexanter = 0;

                    RelatoriosLegaisBase::calcularValorDaLinha(
                        $rsBalanceteReceitaAnterior,
                        $oLinha,
                        $aColunas,
                        RelatoriosLegaisBase::TIPO_CALCULO_RECEITA
                    );
                }
            }

            if ($this->imprimirQuadroAuxiliar) {
                $rsBalanceteReceitaAnterior = !empty($rsBalanceteReceitaAnterior) ? $rsBalanceteReceitaAnterior : null;
                $this->calculaQuadroAuxiliar($rsBalanceteReceita, $rsBalanceteReceitaAnterior);
            }
        }

        /**
         * Executa o Balancete da despesa
         */
        if (!empty($this->aLinhasProcessarDespesa)) {
            $sWhereDespesa = " o58_instit in({$this->getInstituicoes()})";
            $rsBalanceteDespesa = db_dotacaosaldo(
                8,
                2,
                2,
                true,
                $sWhereDespesa,
                $this->iAnoUsu,
                $this->getDataInicial()->getDate(),
                $this->getDataFinal()->getDate()
            );


            $this->limparEstruturaBalanceteDespesa();

            if ($this->lExibirExercicioAnterior) {
                $rsBalanceteDespesaAnterior = db_dotacaosaldo(
                    8,
                    2,
                    2,
                    true,
                    $sWhereDespesa,
                    $this->iAnoUsu - 1,
                    $oDataInicialAnterior->getDate(),
                    $oDataFinalAnterior->getDate()
                );

                $this->limparEstruturaBalanceteDespesa();
            }

            foreach ($this->aLinhasProcessarDespesa as $iLinha) {
                $oLinha = $this->aLinhasConsistencia[$iLinha];
                $aColunas = $this->getColunasPorLinha($oLinha, array(0));
                RelatoriosLegaisBase::calcularValorDaLinha(
                    $rsBalanceteDespesa,
                    $oLinha,
                    $aColunas,
                    RelatoriosLegaisBase::TIPO_CALCULO_DESPESA
                );
                if ($this->lExibirExercicioAnterior) {
                    $aColunas = $this->getColunasPorLinha($oLinha, array(1));
                    RelatoriosLegaisBase::calcularValorDaLinha(
                        $rsBalanceteDespesaAnterior,
                        $oLinha,
                        $aColunas,
                        RelatoriosLegaisBase::TIPO_CALCULO_DESPESA
                    );
                }
            }
        }

        /**
         * Executa o balancete de verificação
         */
        if (!empty($this->aLinhasProcessarVerificacao)) {
            $sWhereVerificacao = " c61_instit in({$this->getInstituicoes()})";
            $rsBalanceteVerificacao = db_planocontassaldo_matriz(
                $this->iAnoUsu,
                $this->getDataInicial()->getDate(),
                $this->getDataFinal()->getDate(),
                false,
                $sWhereVerificacao,
                '',
                'true',
                'false'
            );
            $this->limparEstruturaBalanceteVerificacao();

            if ($this->lExibirExercicioAnterior) {
                $rsBalanceteVerificacaoAnterior = db_planocontassaldo_matriz(
                    $this->iAnoUsu - 1,
                    $oDataInicialAnterior->getDate(),
                    $oDataFinalAnterior->getDate(),
                    false,
                    $sWhereVerificacao,
                    '',
                    'true',
                    'false'
                );

                $this->limparEstruturaBalanceteVerificacao();
            }

            foreach ($this->aLinhasProcessarVerificacao as $iLinha) {
                $oLinha = $this->aLinhasConsistencia[$iLinha];

                $aColunas = $this->getColunasPorLinha($oLinha, array(0));
                RelatoriosLegaisBase::calcularValorDaLinha(
                    $rsBalanceteVerificacao,
                    $oLinha,
                    $aColunas,
                    RelatoriosLegaisBase::TIPO_CALCULO_VERIFICACAO
                );

                if ($this->lExibirExercicioAnterior) {
                    $aColunas = $this->getColunasPorLinha($oLinha, array(1));
                    RelatoriosLegaisBase::calcularValorDaLinha(
                        $rsBalanceteVerificacaoAnterior,
                        $oLinha,
                        $aColunas,
                        RelatoriosLegaisBase::TIPO_CALCULO_VERIFICACAO
                    );
                }
            }
        }


        /**
         * Executa os restos a pagar
         */
        if (!empty($this->aLinhasProcessarRestosPagar)) {
            $oDaoRestosAPagar = new cl_empresto();
            $sWhereRestoPagar = " e60_instit in({$this->getInstituicoes()})";

            $sSqlRestosaPagar = $oDaoRestosAPagar->sql_rp_novo(
                $this->iAnoUsu,
                $sWhereRestoPagar,
                $this->getDataInicial()->getDate(),
                $this->getDataFinal()->getDate()
            );

            $rsRestosPagar = db_query($sSqlRestosaPagar);
            if ($this->lExibirExercicioAnterior) {
                $sSqlRestosaPagar = $oDaoRestosAPagar->sql_rp_novo(
                    $this->iAnoUsu - 1,
                    $sWhereRestoPagar,
                    $oDataInicialAnterior->getDate(),
                    $oDataFinalAnterior->getDate()
                );

                $rsRestosPagarAnterior = db_query($sSqlRestosaPagar);
            }


            foreach ($this->aLinhasProcessarRestosPagar as $iLinha) {
                $oLinha = $this->aLinhasConsistencia[$iLinha];

                $aColunas = $this->getColunasPorLinha($oLinha, array(0));
                RelatoriosLegaisBase::calcularValorDaLinha(
                    $rsRestosPagar,
                    $oLinha,
                    $aColunas,
                    RelatoriosLegaisBase::TIPO_CALCULO_RESTO
                );

                if ($this->lExibirExercicioAnterior) {
                    $aColunas = $this->getColunasPorLinha($oLinha, array(1));
                    RelatoriosLegaisBase::calcularValorDaLinha(
                        $rsRestosPagarAnterior,
                        $oLinha,
                        $aColunas,
                        RelatoriosLegaisBase::TIPO_CALCULO_RESTO
                    );
                }
            }
        }

        $this->processarValoresManuais();
        $this->processaTotalizadores($this->aLinhasConsistencia);

        /**
         * Remove os recursos não informados
         */
        if ($this->sTipo == static::TIPO_ANALITICO) {
            foreach ($this->aLinhasComRecurso as $iLinha) {
                if (isset($this->aLinhasConsistencia[$iLinha]->recursos[0])) {
                    unset($this->aLinhasConsistencia[$iLinha]->recursos[0]);
                }
            }
        }

        return $this->aLinhasConsistencia;
    }

    /**
     * Adiciona uma nova página, reinserindo o cabeçalho do relatório.
     *
     * @param string $sNomeSecao
     * @param boolean $lEscreveCabecalho
     */
    private function adicionarPagina($sNomeSecao = null, $lEscreveCabecalho = true)
    {
        $this->oPdf->clearHeaderDescription();
        $this->oPdf->addHeaderDescription($this->sDescricaoInstituicao);
        $this->oPdf->addHeaderDescription("BALANÇO FINANCEIRO");
        $this->oPdf->addHeaderDescription("EXERCÍCIO : {$this->iAnoUsu}");
        $this->oPdf->addHeaderDescription("PERÍODO : {$this->sDescricaoPeriodo}");
        $tipo = $this->sTipo == static::TIPO_ANALITICO ? "ANALÍTICO" : "SINTÉTICO";
        $this->oPdf->addHeaderDescription("TIPO : {$tipo}");
        $this->oPdf->AddPage();

        if ($lEscreveCabecalho === true) {
            $this->escreverCabecalho($sNomeSecao);
        }
    }

    /**
     * Escreve as assinaturas do relatório.
     *
     */
    private function escreveAssinatura()
    {
        if ($this->oPdf->getAvailHeight() < 45) {
            $this->adicionarPagina(null, false);
        }
        $this->oPdf->setBold(false);
        $oAssinatura = new cl_assinatura();
        $this->oPdf->ln(18);
        assinaturas($this->oPdf, $oAssinatura, 'BG', false, false);
    }

    /**
     * Popula os atributos que serão utilizados no cabeçalho.
     */
    private function preparaCabecalho()
    {
        $aListaInstituicoes = $this->getInstituicoes(true);

        $oPrefeitura = InstituicaoRepository::getInstituicaoPrefeitura();
        $sDescricao = "{$oPrefeitura->getDescricao()} - {$oPrefeitura->getUf()}";
        if (count($aListaInstituicoes) > 1) {
            $this->sDescricaoInstituicao = "INSTITUIÇÃO : {$sDescricao} - CONSOLIDAÇÃO";
        } else {
            $oInstituicao = current($aListaInstituicoes);
            $sDescricao = "{$oInstituicao->getDescricao()} - {$oPrefeitura->getUf()}";
            $this->sDescricaoInstituicao = "INSTITUIÇÃO : {$sDescricao}";
        }

        $this->sDescricaoPeriodo = $this->getPeriodo()->getDescricao();
    }

    /**
     * Escreve a nota explicativa.
     *
     */
    private function escreverNotaExplicativa()
    {
        $this->oPdf->Ln(2);
        $this->getNotaExplicativa($this->oPdf, $this->iCodigoPeriodo, $this->oPdf->getAvailWidth());
    }

    /**
     * Configura formatação do relatório.
     *
     */
    private function configurarPdf()
    {
        $this->oPdf->SetLeftMargin(10);
        $this->oPdf->Open();
        $this->oPdf->AliasNbPages();
        $this->oPdf->SetAutoPageBreak(true);
        $this->oPdf->SetFillcolor(235);
        $this->oPdf->SetFont('arial', '', 6);
    }

    /**
     * Escreve o cabeçalho da seção.
     *
     * @param string $sNomeSecao
     */
    private function escreverCabecalho($sNomeSecao = null)
    {
        if ($this->oPdf->getAvailHeight() < 18) {
            $this->adicionarPagina($sNomeSecao);
            return;
        }

        $this->oPdf->setBold(true);
        $this->oPdf->Cell($this->iLargura * 0.60, $this->iAltura, $sNomeSecao, 'TB', 0, 'C');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, "Exercício Atual", 'LTB', 0, 'C');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, "Exercício Anterior", 'LTB', 1, 'C');
        $this->oPdf->setBold(false);
    }

    /**
     * Escreve uma linha do relatório.
     *
     * @param stdClass $oLinha Linha a ser escrita.
     */
    private function escreverLinha(stdClass $oLinha)
    {
        $sExercicioAnterior = '-';
        $sExercicioAtual = db_formatar($oLinha->vlrexatual, 'f');
        $sBorda = '';
        $sDescricao = str_repeat(' ', $oLinha->nivel * 2) . $oLinha->descricao;

        /**
         * Se é linha totalizadora
         */
        if ($oLinha->totalizar) {
            $this->oPdf->setBold(true);
        }

        /**
         * Se deve exibir valor do exercício anterior
         */
        if ($this->lExibirExercicioAnterior) {
            $sExercicioAnterior = db_formatar($oLinha->vlrexanter, 'f');
        }

        if (in_array($oLinha->ordem, $this->aLinhasFinais)) {
            $sBorda = 'TB';
            $this->iAltura += 2;
        }

        $this->oPdf->Cell($this->iLargura * 0.60, $this->iAltura, $sDescricao, $sBorda, 0, 'L');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $sExercicioAtual, 'L' . $sBorda, 0, 'R');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $sExercicioAnterior, 'L' . $sBorda, 1, 'R');

        if (in_array($oLinha->ordem, $this->aLinhasFinais)) {
            $this->iAltura -= 2;
            if ($oLinha->ordem != static::LINHA_FINAL_DISPENDIOS) {
                $this->oPdf->Ln($this->iAltura);
            }
        }

        $this->oPdf->setBold(false);
    }

    /**
     * Escreve uma linha de Recurso
     *
     * @param stdClass $oLinha
     * @param stdClass $oRecurso
     */
    protected function escreverLinhaRecurso(stdClass $oLinha, stdClass $oRecurso)
    {
        $nValorRecursoAtual = property_exists($oRecurso, 'vlrexatual') ? $oRecurso->vlrexatual : 0;
        $sExercicioAnterior = '-';
        $sExercicioAtual = db_formatar($nValorRecursoAtual, 'f');
        $sBorda = '';
        $sDescricao = str_repeat(' ', ($oLinha->nivel * 2) + 2) . $oRecurso->nome;

        /**
         * Se deve exibir valor do exercício anterior
         */
        $nValorRecursoAnterior = 0;
        if ($this->lExibirExercicioAnterior) {
            $nValorRecursoAnterior = property_exists($oRecurso, 'vlrexanter') ? $oRecurso->vlrexanter : 0;
            $sExercicioAnterior = db_formatar($nValorRecursoAnterior, 'f');
        }

        if ($nValorRecursoAtual <= 0 && $nValorRecursoAnterior <= 0) {
            return;
        }


        // Tratando a descrições muito longas com '...'

        $larguraDescricao = $this->iLargura * 0.60;

        if (strlen($sDescricao) > $larguraDescricao) {
            $sDescricao = substr($sDescricao, 0, $larguraDescricao - 5) . '...';
        }

        $this->oPdf->Cell($this->iLargura * 0.60, $this->iAltura, $sDescricao, $sBorda, 0, 'L');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $sExercicioAtual, 'L' . $sBorda, 0, 'R');
        $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $sExercicioAnterior, 'L' . $sBorda, 1, 'R');
    }

    /**
     * Emite o relatório.
     *
     * @return void
     */
    public function emitir()
    {

        $aDados = $this->getDados();

        $sNomeSecao = "INGRESSOS";

        $this->preparaCabecalho();
        $this->configurarPdf();
        $this->adicionarPagina($sNomeSecao);

        foreach ($aDados as $oLinha) {
            if ($oLinha->ordem == static::LINHA_INICIO_DISPENDIOS) {
                $sNomeSecao = "DISPÊNDIOS";
                $this->escreverCabecalho($sNomeSecao);
            }

            if ($this->oPdf->getAvailHeight() < 18) {
                $this->adicionarPagina($sNomeSecao);
            }

            if ($oLinha->ordem == static::LINHA_FINAL_DISPENDIOS - 1 && $this->oPdf->getAvailHeight() < 58) {
                $this->adicionarPagina($sNomeSecao);
            }

            $this->escreverLinha($oLinha);


            if (isset($oLinha->recursos) && count($oLinha->recursos) > 0 &&
                in_array($oLinha->ordem, $this->aLinhasComRecurso)) {
                foreach ($oLinha->recursos as $oRecurso) {
                    if ($this->oPdf->getAvailHeight() < 18) {
                        $this->adicionarPagina($sNomeSecao);
                    }

                    $this->escreverLinhaRecurso($oLinha, $oRecurso);
                }
            }
        }

        $this->escreverNotaExplicativa();
        $this->imprimeQuadroAnexo();
        $this->escreveAssinatura();
        $this->oPdf->showPDF('BalancoFinanceiroDCASP_' . time());
    }

    /**
     * @return array
     */
    public function getLinhasObrigaRecurso()
    {
        $aRecursosObrigatorios = $this->aLinhasComRecurso;
        $aRecursosObrigatorios[] = 2;
        $aRecursosObrigatorios[] = 25;
        return $aRecursosObrigatorios;
    }

    /**
     * @param $where
     * @param $ano
     * @param $dataInicial
     * @param $dataFinal
     * @return string
     */
    protected function balanceteReceita($where, $ano, $dataInicial, $dataFinal)
    {
        if (EMENTARIO_RECEITA) {
            $rsBalanceteReceita = ReceitaSaldo(
                11,
                1,
                3,
                true,
                $where,
                $ano,
                $dataInicial->getDate(),
                $dataFinal->getDate()
            );
        } else {
            $rsBalanceteReceita = db_receitasaldo(
                11,
                1,
                3,
                true,
                $where,
                $ano,
                $dataInicial->getDate(),
                $dataFinal->getDate()
            );
        }

        return $rsBalanceteReceita;
    }

    public function setImprimirQuadroAuxiliar($imprimirQuadroAuxiliar)
    {
        $this->imprimirQuadroAuxiliar = $imprimirQuadroAuxiliar;
    }

    /**
     * @param resource $rsBalanceteReceita
     * @param resource|null $rsBalanceteReceitaAnterior
     */
    private function calculaQuadroAuxiliar($rsBalanceteReceita, $rsBalanceteReceitaAnterior = null)
    {
        foreach ($this->linhasQuadroAuxiliar as $ordem) {
            $linhaCalcular = $this->aLinhasConsistencia[$ordem];

            if ($ordem === 3) {
                $this->linhasProcessadaAuxiliar[$ordem] = $this->createObjetoQuadroAuxiliar($linhaCalcular->descricao);
                continue;
            }

            $this->linhasProcessadaAuxiliar[$ordem] = $this->calcularLinha(
                $linhaCalcular,
                $rsBalanceteReceita,
                $rsBalanceteReceitaAnterior
            );
        }

        $linhaTotalizadora = $this->linhasProcessadaAuxiliar[3];
        $linhasTotalizar = [4, 5, 6, 7, 8, 9];
        foreach ($linhasTotalizar as $ordem) {
            $linha = $this->linhasProcessadaAuxiliar[$ordem];
            $linhaTotalizadora->receitaOrcamentaria += $linha->receitaOrcamentaria;
            $linhaTotalizadora->deducaoReceitaOrcamentaria += $linha->deducaoReceitaOrcamentaria;
            $linhaTotalizadora->saldo += $linha->saldo;
            $linhaTotalizadora->receitaOrcamentariaAnterior += $linha->receitaOrcamentariaAnterior;
            $linhaTotalizadora->deducaoReceitaOrcamentariaAnterior += $linha->deducaoReceitaOrcamentariaAnterior;
            $linhaTotalizadora->saldoAnterior += $linha->saldoAnterior;
        }
    }

    /**
     * @param $descricao
     * @return object
     */
    public function createObjetoQuadroAuxiliar($descricao)
    {
        return (object)[
            "descricao" => $descricao,
            "receitaOrcamentaria" => 0,
            "deducaoReceitaOrcamentaria" => 0,
            "saldo" => 0,
            "receitaOrcamentariaAnterior" => 0,
            "deducaoReceitaOrcamentariaAnterior" => 0,
            "saldoAnterior" => 0,
        ];
    }

    private function calcularLinha($linhaCalcular, $rsBalanceteReceita, $rsBalanceteReceitaAnterior = null)
    {
        $dado = $this->createObjetoQuadroAuxiliar($linhaCalcular->descricao);
        $recursos = $linhaCalcular->parametros->orcamento->recurso->valor;

        $registros = pg_fetch_all($rsBalanceteReceita);

        foreach ($registros as $registro) {
            $idRecurso = $registro['o70_codigo'];
            $estrutural = $registro['o57_fonte'];
            if (in_array($idRecurso, $recursos) && substr($estrutural, 0, 1) == 4) {
                $dado->receitaOrcamentaria += $registro['saldo_arrecadado_acumulado'];
            }

            if (in_array($idRecurso, $recursos) && substr($estrutural, 0, 1) == 9) {
                $dado->deducaoReceitaOrcamentaria += $registro['saldo_arrecadado_acumulado'];
            }
        }

        if (!is_null($rsBalanceteReceitaAnterior)) {
            $registros = pg_fetch_all($rsBalanceteReceitaAnterior);
            foreach ($registros as $registro) {
                $idRecurso = $registro['o70_codigo'];
                $estrutural = $registro['o57_fonte'];
                if (in_array($idRecurso, $recursos) && substr($estrutural, 0, 1) == 4) {
                    $dado->receitaOrcamentariaAnterior += $registro['saldo_arrecadado_acumulado'];
                }

                if (in_array($idRecurso, $recursos) && substr($estrutural, 0, 1) == 9) {
                    $dado->deducaoReceitaOrcamentariaAnterior += $registro['saldo_arrecadado_acumulado'];
                }
            }
        }

        //valores das deduções vem negativos, portanto somo.
        $dado->saldo = $dado->receitaOrcamentaria + $dado->deducaoReceitaOrcamentaria;
        $dado->saldoAnterior = $dado->receitaOrcamentariaAnterior + $dado->deducaoReceitaOrcamentariaAnterior;

        return $dado;
    }

    protected function imprimeQuadroAnexo()
    {
        $this->oPdf->AddPage();

        $this->oPdf->ln(1);
        $this->oPdf->setBold(true);

        // CALCULO DA LARGURA DAS COLUNAS
        $larguraDescricao = $this->iLargura * 0.30;
        $larguraValores = $this->iLargura * 0.35;
        $larguraValor = $larguraValores / 3;

        // Inicio impressão do cabeçalho
        $x2 = $this->oPdf->GetX() + $this->iLargura;
        $this->oPdf->Line($this->oPdf->GetX(), $this->oPdf->GetY(), $x2, $this->oPdf->GetY());

        $this->oPdf->Cell($larguraDescricao, $this->iAltura, "", 0, 0, 'C');
        $this->oPdf->Cell($larguraValores, $this->iAltura, "Exercício Atual", 'LBR', 0, 'C');
        $this->oPdf->Cell($larguraValores, $this->iAltura, "Exercício Anterior", 'LB', 1, 'C');

        // Exercício Atual
        $this->oPdf->Cell($larguraDescricao, 8, "Especificação", 0, 0, 'C');
        $y = $this->oPdf->GetY();
        $x = $this->oPdf->GetX() + $larguraValor;
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Receita\nOrçamentaria\n(a)", 0, 'C');
        $this->oPdf->SetXY($x, $y);
        $x += $larguraValor;
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Dedução da\nReceita\nOrçamentaria (b)", 0, 'C');
        $this->oPdf->SetXY($x, $y);
        $x += $larguraValor;
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Saldo\n(c) = (a-b)", 0, 'C');

        $this->oPdf->SetXY($x, $y);
        $x += $larguraValor;
        // Exercício Anterior
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Receita\nOrçamentaria\n(d)", 0, 'C');
        $this->oPdf->SetXY($x, $y);
        $x += $larguraValor;
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Dedução da\nReceita\nOrçamentaria (e)", 0, 'C');
        $this->oPdf->SetXY($x, $y);
        $x += $larguraValor;
        $this->oPdf->MultiCell($larguraValor, $this->iAltura, "Saldo\n(f) = (d-e)", 0, 'C');

        $this->oPdf->setBold(false);

        $alturaFinal = $y + ($this->iAltura * 3);
        //desenha a borda botton final
        $this->oPdf->Line($this->oPdf->GetX(), $alturaFinal, $x2, $alturaFinal);
        $larguraColuna = $larguraDescricao + 10;

        // desenha as bordas das colunas
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);
        $larguraColuna = $larguraColuna + $larguraValor;
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);
        $larguraColuna = $larguraColuna + $larguraValor;
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);
        $larguraColuna = $larguraColuna + $larguraValor;
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);
        $larguraColuna = $larguraColuna + $larguraValor;
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);
        $larguraColuna = $larguraColuna + $larguraValor;
        $this->oPdf->Line($larguraColuna, $y, $larguraColuna, $alturaFinal);

        $this->oPdf->SetY($alturaFinal);
        // Final impressão do cabeçalho


        // Inicio impressão do valores
        $totalReceitaOrcamentaria = 0;
        $totalDeducaoReceitaOrcamentaria = 0;
        $totalSaldo = 0;
        $totalReceitaOrcamentariaAnterior = 0;
        $totalDeducaoReceitaOrcamentariaAnterior = 0;
        $totalSaldoAnterior = 0;

        $totalizar = [2, 3];
        foreach ($this->linhasProcessadaAuxiliar as $index => $linha) {
            if (in_array($index, $totalizar)) {
                $totalReceitaOrcamentaria += $linha->receitaOrcamentaria;
                $totalDeducaoReceitaOrcamentaria += $linha->deducaoReceitaOrcamentaria;
                $totalSaldo += $linha->saldo;
                $totalReceitaOrcamentariaAnterior += $linha->receitaOrcamentariaAnterior;
                $totalDeducaoReceitaOrcamentariaAnterior += $linha->deducaoReceitaOrcamentariaAnterior;
                $totalSaldoAnterior += $linha->saldoAnterior;
            }
            $receitaOrcamentaria = db_formatar($linha->receitaOrcamentaria, 'f');
            $deducaoReceitaOrcamentaria = db_formatar(abs($linha->deducaoReceitaOrcamentaria), 'f');
            $saldo = db_formatar($linha->saldo, 'f');
            $receitaOrcamentariaAnterior = db_formatar($linha->receitaOrcamentariaAnterior, 'f');
            $deducaoReceitaOrcamentariaAnterior = db_formatar(abs($linha->deducaoReceitaOrcamentariaAnterior), 'f');
            $saldoAnterior = db_formatar($linha->saldoAnterior, 'f');
            if (!$this->lExibirExercicioAnterior) {
                $receitaOrcamentariaAnterior = '-';
                $deducaoReceitaOrcamentariaAnterior = '-';
                $saldoAnterior = '-';
            }

            $this->oPdf->Cell($larguraDescricao, $this->iAltura, $linha->descricao, 0);
            $this->oPdf->Cell($larguraValor, $this->iAltura, $receitaOrcamentaria, 0, 0, 'R');
            $this->oPdf->Cell($larguraValor, $this->iAltura, $deducaoReceitaOrcamentaria, 0, 0, 'R');
            $this->oPdf->Cell($larguraValor, $this->iAltura, $saldo, 0, 0, 'R');
            $this->oPdf->Cell($larguraValor, $this->iAltura, $receitaOrcamentariaAnterior, 0, 0, 'R');
            $this->oPdf->Cell($larguraValor, $this->iAltura, $deducaoReceitaOrcamentariaAnterior, 0, 0, 'R');
            $this->oPdf->Cell($larguraValor, $this->iAltura, $saldoAnterior, 0, 1, 'R');
        }

        $totalReceitaOrcamentaria = db_formatar($totalReceitaOrcamentaria, 'f');
        $totalDeducaoReceitaOrcamentaria = db_formatar(abs($totalDeducaoReceitaOrcamentaria), 'f');
        $totalSaldo = db_formatar($totalSaldo, 'f');
        $totalReceitaOrcamentariaAnterior = db_formatar($totalReceitaOrcamentariaAnterior, 'f');
        $totalDeducaoReceitaOrcamentariaAnterior = db_formatar(abs($totalDeducaoReceitaOrcamentariaAnterior), 'f');
        $totalSaldoAnterior = db_formatar($totalSaldoAnterior, 'f');

        if (!$this->lExibirExercicioAnterior) {
            $totalReceitaOrcamentariaAnterior = '-';
            $totalDeducaoReceitaOrcamentariaAnterior = '-';
            $totalSaldoAnterior = '-';
        }

        $this->oPdf->Line($this->oPdf->GetX(), $this->oPdf->GetY(), $x2, $this->oPdf->GetY());
        $this->oPdf->SetFont('arial', 'B', 6);
        $this->oPdf->Cell($larguraDescricao, $this->iAltura, "TOTAL", 0);
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalReceitaOrcamentaria, 0, 0, 'R');
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalDeducaoReceitaOrcamentaria, 0, 0, 'R');
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalSaldo, 0, 0, 'R');
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalReceitaOrcamentariaAnterior, 0, 0, 'R');
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalDeducaoReceitaOrcamentariaAnterior, 0, 0, 'R');
        $this->oPdf->Cell($larguraValor, $this->iAltura, $totalSaldoAnterior, 0, 1, 'R');
        $this->oPdf->Line($this->oPdf->GetX(), $this->oPdf->GetY(), $x2, $this->oPdf->GetY());
    }
}
