<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class BanlanceteVerificacaoSinteticoPdf extends BanlanceteVerificacaoPdf
{
    protected function calculaColunas()
    {
        $this->wDescricao += $this->wReduzido + $this->wInsttuicao + $this->wRecurso + $this->wIsf;
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->cell($this->wEstrutural, $this->hLinha, 'Estrutural', 'B', 0, 'C', 1);
        $this->cell($this->wDescricao, $this->hLinha, 'Descrição', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo Anterior', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Débitos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Créditos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo', 'B', 1, 'C', 1);
        $this->regular();
    }

    protected function imprimirCorpo()
    {
        foreach ($this->dados as $dado) {
            $this->calculaTotalizadores($dado);
            if (!$dado->sintetica) {
                continue;
            }
            $this->imprimeLinha($dado);
            $this->ln();
        }
    }

    protected function imprimeLinha($conta)
    {
        $this->quebraPagina();

        $this->linha($this->wEstrutural, $conta->mascara, 'L');
        $this->linha($this->wDescricao, $conta->nome, 'L');
        $this->imprimeValores($conta);
    }
}
