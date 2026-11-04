<?php


namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita;

/**
 * Class BalanceteReceitaRetrato
 * @package App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita
 */
class BalanceteReceitaPaisagemPdf extends BalanceteReceitaPdf
{
    protected $wRecurso = 20;
    protected $apresentar;

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

    protected function imprimirValores($dado)
    {
        $this->quebraPagina();

        $diferenca = $dado->previsao_atualizada - $dado->arrecadado_acumulado;
        $percentual = $this->calculaPercentual(
            $dado->previsao_atualizada,
            $dado->arrecadado_acumulado
        );

        $fill = $dado->sintetico ? 0 : 1;
        if ($dado->sintetico) {
            $recurso = "";
        } else {
            switch ($this->apresentar) {
                case "3":
                    $recurso = "{$dado->gestao} - " .
                        str_pad($dado->complemento, 4, '0', STR_PAD_LEFT);
                    break;
                case "7":
                    $recurso = $dado->siconfi ? $dado->siconfi . " - " .
                        str_pad($dado->complemento, 4, '0', STR_PAD_LEFT) : '' ;
                    break;
                case "9":
                    $recurso = $dado->subrecurso ? $dado->subrecurso . " - " .
                        str_pad($dado->complemento, 4, '0', STR_PAD_LEFT) : '' ;
                    break;
            }
        }

        $this->linha($this->wReceita, $dado->mascara, 'L', $fill);
        $this->linha($this->wDescricao, $dado->descricao, 'L', $fill);
        $this->linha($this->wCP, $dado->cp, 'C', $fill);
        $this->linha($this->wReduz, $dado->reduzido, 'C', $fill);
        $this->linha($this->wRecurso, $recurso, 'C', $fill);
        $this->linha($this->wValores, formataValorMonetario($dado->valor_inicial), 'R', $fill);
        $this->linha($this->wValores, formataValorMonetario($dado->previsao_adicional), 'R', $fill);
        $this->linha($this->wValores, formataValorMonetario($dado->arrecadado_periodo), 'R', $fill);
        $this->linha($this->wValores, formataValorMonetario($dado->arrecadado_acumulado), 'R', $fill);
        $this->linha($this->wValores, formataValorMonetario($diferenca), 'R', $fill);
        $this->linha($this->wPercentual, db_formatar($percentual, 'f'), 'R', $fill);

        $this->calculaTotalizadores($dado, $diferenca);
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

    public function setDados($apresentar)
    {
        $this->apresentar = $apresentar;
    }
}
