<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita;

class BalanceteReceitaEmentarioPadraoPdf extends BalanceteReceitaPdf
{
    protected $wReceita = 30;
    protected $wDescricao = 93;

    // segunda linha
    protected $wRecurso = 20;

    protected $wLinhaPintar = 279;

    public function setDadosBalancete($dadosBalancete)
    {
        foreach ($dadosBalancete as $conta) {
            $this->dadosBalancete[$conta->natureza][] = $conta;
        }
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($this->wReceita, 4, 'RECEITA', 1, 0, 'C');
        $this->Cell($this->wDescricao, 4, 'DESCRIÇÃO', 1, 0, 'C');
        $this->Cell($this->wCP, 4, 'CP', 1, 0, 'C');
        $this->Cell($this->wReduz, 4, 'REDUZ', 1, 0, 'C');
        $this->Cell($this->wRecurso, 4, 'REC.- COMPL.', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'PREVISTO', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'PREV.ADIC.', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'ARRECADADO', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'ARREC. ANO', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'DIFERENÇA', 1, 0, 'C');
        $this->Cell($this->wPercentual, 4, 'PERC.', 1, 1, 'C');
        $this->SetFont('Arial', '', 7);
    }

    /**
     * @return void
     */
    public function imprimirCorpo()
    {
        foreach ($this->dadosBalancete as $contas) {
            $conta = $contas[0];
            $this->quebraPagina();

            // variáveis criadas para controle da quebra de linha da descrição da receita
            $totalContasEstrutural = count($contas);
            $numeroLinhasDescricao = $this->nbLines($this->wDescricao, $conta->descricao);

            $yInicial = $this->getY();
            $hPintar = $this->calculaAlturaPintar($totalContasEstrutural, $numeroLinhasDescricao);

            if (!$conta->sintetico) {
                $this->pintaAnaliticas($yInicial, $hPintar);
            }

            $this->linha($this->wReceita, $conta->mascara, 'L');
            $this->linhaDescricao($this->wDescricao, $conta->descricao, 'L');

            $totalImpresso = 0;
            foreach ($contas as $dado) {
                $this->imprimirValoresEmentarioPadrao($dado, $totalContasEstrutural, $totalImpresso);
                $this->ln();
                $totalImpresso ++;
            }

            // Verifica se o nome da conta quebrou mais linhas que o total de valores impressos.
            // Se sim, adiciona uma quebra de linha proporcional para compensar
            $x = $numeroLinhasDescricao - $totalContasEstrutural;
            if ($x > 0) {
                $this->ln($x * 4);
            }
        }
    }

    protected function pintaAnaliticas($y, $h)
    {
        $this->rect($this->getX(), $y, $this->wLinhaPintar, $h, 'F');
    }

    protected function linhaDescricao($w, $valor, $align = 'L')
    {
        $xInicial = $this->getX();
        $yInicial = $this->getY();
        $this->multiCell($w, 4, $valor, 0, $align);
        $this->setXY($xInicial + $w, $yInicial);
    }

    protected function linha($w, $valor, $align = 'L', $fill = 0)
    {
        $this->cellAdapt($this->fonte, $w, 4, $valor, 0, 0, $align, $fill);
    }

    protected function imprimirValoresEmentarioPadrao($dado, $totalContas, $totalImpresso)
    {
        $this->quebraPaginaEmentarioPadrao($totalContas, $totalImpresso);
        $this->setX(133);
        $diferenca = $dado->previsao_atualizada - $dado->arrecadado_acumulado;
        $percentual = $this->calculaPercentual(
            $dado->previsao_atualizada,
            $dado->arrecadado_acumulado
        );

        $recurso = $dado->sintetico ? '' : "{$dado->gestao} - " . str_pad($dado->complemento, 4, '0', STR_PAD_LEFT);

        $this->linha($this->wCP, $dado->cp, 'C');
        $this->linha($this->wReduz, $dado->reduzido, 'C');
        $this->linha($this->wRecurso, $recurso, 'C');
        $this->linha($this->wValores, formataValorMonetario($dado->valor_inicial), 'R');
        $this->linha($this->wValores, formataValorMonetario($dado->previsao_adicional), 'R');
        $this->linha($this->wValores, formataValorMonetario($dado->arrecadado_periodo), 'R');
        $this->linha($this->wValores, formataValorMonetario($dado->arrecadado_acumulado), 'R');
        $this->linha($this->wValores, formataValorMonetario($diferenca), 'R');
        $this->linha($this->wPercentual, db_formatar($percentual, 'f'), 'R');

        $this->calculaTotalizadores($dado, $diferenca);
    }

    /**
     * @param integer $totalContasEstrutural quantidade de contas analiticas
     * @param integer $numeroLinhasDescricao quantas linhas a descrição vai ocupar
     * @return integer
     */
    public function calculaAlturaPintar($totalContasEstrutural, $numeroLinhasDescricao)
    {
        $alturaTotalContas = $totalContasEstrutural * 4;
        $alturaRestantePagina = $this->getAvailableHeight();

        $hPintar = $alturaTotalContas;
        if ($totalContasEstrutural === 1 && $numeroLinhasDescricao > 1) {
            $hPintar = $numeroLinhasDescricao * 4;
        }

        if ($alturaRestantePagina < ($alturaTotalContas + $this->hQuebraPagina)) {
            $hPintar = $alturaRestantePagina - 8;
            if ($hPintar < 4) {
                $hPintar = 4;
            }
        }

        return $hPintar;
    }

    protected function imprimirTotalizador()
    {
        $this->quebraPagina();
        $this->SetFont('Arial', 'B', 7);
        $this->linha($this->wTotal, 'Total', 'R');

        $this->linha($this->wValores, formataValorMonetario($this->totalValorInicial), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalPrevisaoAdicional), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalArrecadadoPeriodo), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalArrecadadoAcumulado), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalDiferenca), 'R');

        $percentual = $this->calculaPercentual(
            $this->totalPrevisaoAtualizada,
            $this->totalArrecadadoAcumulado
        );

        $this->linha($this->wPercentual, db_formatar($percentual, 'f'), 'R');
    }

    /**
     * @todo vau ter que fazer pintar o retangulo aqui
     * @todo acho que o calculo do $hPintar vai poder ser metodo
     */
    private function quebraPaginaEmentarioPadrao($totalContas, $totalImpresso)
    {
        if ($this->getAvailHeight() < $this->hQuebraPagina) {
            $this->imprimeCabecalho();
            $contasRestantes = $totalContas - $totalImpresso;
            $hPintar = $this->calculaAlturaPintar($contasRestantes, 1);
            $this->pintaAnaliticas(($this->yFimHeader() + 4), $hPintar);
        }
    }

    protected function imprimirValores($dado)
    {
        // TODO: Implement imprimirValores() method.
    }
}
