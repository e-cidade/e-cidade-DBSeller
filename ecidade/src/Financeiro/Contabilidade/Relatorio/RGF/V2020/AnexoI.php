<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2020;

use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Linha;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2018\AnexoI as AnexoI2018;

/**
 * Class AnexoI
 *
 * @package ECidade\Financeiro\Contabilidade\Relatorio\RGF\V2020
 */
abstract class AnexoI extends AnexoI2018
{

    /**
     * @var array
     */
    protected $meses = [];

    /**
     * @var bool
     */
    protected $lDoExercicio = false;

    /**
     * @var integer
     */
    protected $linhaFinalQuadroDespesa;

    /**
     * @var integer
     */
    protected $linhaDespesaTotalComPessoal;

    /**
     * @var integer
     */
    protected $linhaDespesaCorrenteLiquida;

    /**
     * @var integer
     */
    protected $linhaDespesasNaoComputadas;

    /**
     * @var array
     */
    protected $totalizadoresQuadroDespesa;

    /**
     * @var array
     */
    protected $linhasTotalizadoras;

    /**
     * @var array
     */
    protected $linhasParaSubtrair = [];

    public function __construct($iAno, $oPeriodo, $aInstituicoes)
    {
        AnexoI2018::__construct($iAno, $oPeriodo, $aInstituicoes);
    }

    /**
     * @return Linha[]
     * @throws \Exception
     */
    public function getDadosProcessados()
    {
        $this->getDados();

        $oLinha = new Linha();
        $oLinha->informaMetodo("cabecalhoQuadroUmDetalhado");
        $this->aLinhasProcessadas[] = $oLinha;

        foreach ($this->aLinhasConsistencia as $oLinhaRelatorio) {
            if ($oLinhaRelatorio->ordem <= $this->linhaFinalQuadroDespesa) {
                $this->adicionaLinhaModeloDetalhado($oLinhaRelatorio);
            }

            if ($oLinhaRelatorio->ordem == $this->linhaDespesaCorrenteLiquida) {
                $oLinha = new Linha();
                $oLinha->informaMetodo("cabecalhoQuadroDois");
                $this->aLinhasProcessadas[] = $oLinha;
            }

            if ($oLinhaRelatorio->ordem >= $this->linhaDespesaCorrenteLiquida) {
                $iFill = 0;
                if ($oLinhaRelatorio->ordem == $this->linhaDespesaTotalComPessoal) {
                    $iFill = 1;
                }
                $this->adicionaLinhaModeloOficial(
                    $oLinhaRelatorio->descricao,
                    $oLinhaRelatorio->valor,
                    $oLinhaRelatorio->percentual,
                    array('TBR', 1, 'TBL'),
                    $iFill
                );
            }
        }
        $oLinha = new Linha();
        $oLinha->informaMetodo("notaExplicativaPdf");
        $this->aLinhasProcessadas[] = $oLinha;

        return $this->aLinhasProcessadas;
    }

    /**
     * Coloca os valores nas linhas do relatório legal na propriedade criada, chamada -> meses.
     */
    protected function agruparValoresNasLinhas()
    {
        for ($linha = 1; $linha <= $this->linhaFinalQuadroDespesa; $linha++) {
            $stdLinha = $this->aLinhasConsistencia[$linha];
            if (empty($stdLinha->meses)) {
                $stdLinha->meses = [];
            }

            $valoresPorMes = $this->aValoresDespesaPorLinhaMes[$linha];
            foreach ($valoresPorMes as $competencia => $valor) {
                $stdLinha->meses[] = (object)['competencia' => $competencia, 'valor' => $valor];
            }
        }
    }

    /**
     * totaliza as colunas por mes
     */
    protected function totalizarColunas()
    {
        foreach ($this->linhasTotalizadoras as $sintetica => $analitica) {
            foreach ($analitica as $linhaAnalitica) {
                foreach ($this->aValoresDespesaPorLinhaMes[$linhaAnalitica] as $competencia => $valor) {
                    if (in_array($linhaAnalitica, $this->linhasParaSubtrair)) {
                        $valor *= -1;
                    }
                    $this->aValoresDespesaPorLinhaMes[$sintetica][$competencia] += $valor;
                }
            }
        }

        foreach ($this->aValoresDespesaPorLinhaMes[$this->linhaFinalQuadroDespesa] as $competencia => $valor) {
            $linha_1  = $this->aValoresDespesaPorLinhaMes[1][$competencia];
            $linha_11 = $this->aValoresDespesaPorLinhaMes[$this->linhaDespesasNaoComputadas][$competencia];
            $this->aValoresDespesaPorLinhaMes[$this->linhaFinalQuadroDespesa][$competencia] = ($linha_1 - $linha_11);
        }

        $linha_1  = $this->aLinhasConsistencia[1];
        $linha_11 = $this->aLinhasConsistencia[$this->linhaDespesasNaoComputadas];
        $calculoLiquidado = ($linha_1->liquidado_ultimo_ano - $linha_11->liquidado_ultimo_ano);
        $calculoRestos    = ($linha_1->rp_nao_processado - $linha_11->rp_nao_processado);
        $this->aLinhasConsistencia[$this->linhaFinalQuadroDespesa]->liquidado_ultimo_ano = $calculoLiquidado;
        $this->aLinhasConsistencia[$this->linhaFinalQuadroDespesa]->rp_nao_processado = $calculoRestos;

        for ($row = 1; $row <= $this->linhaFinalQuadroDespesa; $row++) {
            $linha = $this->aLinhasConsistencia[$row];
            $linha->liquidado_ultimo_ano = 0;
            foreach ($this->aValoresDespesaPorLinhaMes[$row] as $competencia => $valor) {
                $linha->liquidado_ultimo_ano += $valor;
            }
        }
    }

    /**
     * Inicializa as propriedades para receber o valor das contas por mes.
     *
     * @throws \ParameterException
     */
    protected function inicializaValoresDespesaPorLinhaMes()
    {
        foreach ($this->getMesesAbrangente() as $iMes => $sCompetencia) {
            list($sMesAbreviado, $iAno) = explode('/', $sCompetencia);
            for ($linha = 1; $linha <= $this->linhaFinalQuadroDespesa; $linha++) {
                $this->aValoresDespesaPorLinhaMes[$linha]["{$iMes}/{$iAno}"] = 0;
            }
        }
    }

    /**
     * Adiciona os valores digitados manualmente
     * @throws \ParameterException
     */
    protected function calculaValorManual()
    {
        foreach ($this->aLinhasProcessarDespesa as $iLinha) {
            $aLinhasManuais = $this->aLinhasConsistencia[$iLinha]->oLinhaRelatorio->getValoresColunas(
                null,
                null,
                $this->getInstituicoes(),
                $this->iAnoUsu
            );
            foreach ($this->getMesesAbrangente() as $iMes => $sCompetencia) {
                list($sMesAbreviado, $iAno) = explode('/', $sCompetencia);
                foreach ($aLinhasManuais as $oLinhaManual) {
                    if ($oLinhaManual->colunas[0]->o117_valor == $sCompetencia) {
                        $this->aValoresDespesaPorLinhaMes[$iLinha]["{$iMes}/{$iAno}"] +=
                            $oLinhaManual->colunas[1]->o117_valor;
                    }
                }
            }
        }
    }

    public function getLinhas()
    {
        $this->processarDetalhamentoMensal();
        return $this->aLinhasConsistencia;
    }
}
