<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

abstract class BalanceteReceitaPdf extends Pdf
{
    /**
     * Array com todas as contas do balancete
     * @var array
     */
    protected $dadosBalancete = [];

    /**
     * Controle do tamanho das colunas
     */
    protected $wReceita = 30;
    protected $wDescricao = 93;
    protected $wCP = 8;
    protected $wReduz = 12;
    protected $wRecurso = 12;
    protected $wComplemento = 10;
    protected $wValores = 21;
    protected $wPercentual = 11;
    protected $wTotal = 163;

    protected $fonte = 7;

    protected $totalValorInicial = 0;
    protected $totalPrevisaoAtualizada = 0;
    protected $totalPrevisaoAdicional = 0;
    protected $totalArrecadadoPeriodo = 0;
    protected $totalArrecadadoAcumulado = 0;
    protected $totalDiferenca = 0;

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
    }

    public function headers($titulo, $periodo, $instituicoes, $plano)
    {
        $this->addTitulo($titulo);
        $this->addTitulo('');
        $this->addTitulo("PERÍODO: {$periodo}");
        $this->addTitulo("INSTITUIÇÕES: {$instituicoes}");
        $this->addTitulo("PLANO: {$plano}");
    }

    public function setDadosBalancete($dadosBalancete)
    {
        $this->dadosBalancete = $dadosBalancete;
    }

    /**
     * retorna os paths dos arquivos pdfs
     * @return array
     */
    public function imprimir()
    {
        $this->imprimeCabecalho();
        $this->imprimirCorpo();

        $this->imprimirTotalizador();

        $filename = sprintf('tmp/balancete-receita-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    /**
     * @return void
     */
    public function imprimirCorpo()
    {
        foreach ($this->dadosBalancete as $dado) {
            $this->imprimirValores($dado);
            $this->ln();
        }
    }

    /**
     * @param $dado
     * @param $diferenca
     * @return void
     */
    protected function calculaTotalizadores($dado, $diferenca)
    {
        if (!$dado->sintetico) {
            $this->totalValorInicial += $dado->valor_inicial;
            $this->totalPrevisaoAtualizada += $dado->previsao_atualizada;
            $this->totalPrevisaoAdicional += $dado->previsao_adicional;
            $this->totalArrecadadoPeriodo += $dado->arrecadado_periodo;
            $this->totalArrecadadoAcumulado += $dado->arrecadado_acumulado;
            $this->totalDiferenca += $diferenca;
        }
    }

    protected function linha($w, $valor, $align = 'L', $fill = 0)
    {
        $this->cellAdapt($this->fonte, $w, 4, $valor, 0, 0, $align, $fill);
    }

    protected function calculaPercentual($previsaoAtualizada, $arrecadadoAcumulado)
    {
        $percentual = 0;
        if ($previsaoAtualizada > 0) {
            $percentual = ($arrecadadoAcumulado / ($previsaoAtualizada)) * 100;
        }

        return $percentual;
    }

    abstract protected function imprimirValores($dado);

    abstract protected function imprimirTotalizador();
}
