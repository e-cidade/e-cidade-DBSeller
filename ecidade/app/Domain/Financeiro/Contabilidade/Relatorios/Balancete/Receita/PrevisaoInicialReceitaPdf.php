<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Receita;

class PrevisaoInicialReceitaPdf extends BalanceteReceitaPdf
{
    protected $wDescricao = 168;
    protected $wRecurso = 20;
    protected $wCP = 7;
    protected $wTotal = 237;
    protected $apresentar;

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->Cell($this->wReceita, 4, 'RECEITA', 1, 0, 'C');
        $this->Cell($this->wDescricao, 4, 'DESCRIÇÃO', 1, 0, 'C');
        $this->Cell($this->wCP, 4, 'CP', 1, 0, 'C');
        $this->Cell($this->wReduz, 4, 'REDUZ', 1, 0, 'C');
        $this->Cell($this->wRecurso, 4, 'REC. - COMPL.', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'PREVISTO', 1, 0, 'C');
        $this->Cell($this->wValores, 4, 'PREV.ADIC.', 1, 1, 'C');
        $this->SetFont('Arial', '', 7);
    }

    protected function imprimirValores($dado)
    {
        $this->quebraPagina();
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

        if (!$dado->sintetico) {
            $this->totalValorInicial += $dado->valor_inicial;
            $this->totalPrevisaoAtualizada += $dado->previsao_atualizada;
            $this->totalPrevisaoAdicional += $dado->previsao_adicional;
        }
    }

    protected function imprimirTotalizador()
    {
        $this->quebraPagina();
        $this->SetFont('Arial', 'B', 7);
        $this->linha($this->wTotal, 'Total', 'R');

        $this->linha($this->wValores, formataValorMonetario($this->totalValorInicial), 'R');
        $this->linha($this->wValores, formataValorMonetario($this->totalPrevisaoAdicional), 'R');
    }

    public function setDados($apresentar)
    {
        $this->apresentar = $apresentar;
    }
}
