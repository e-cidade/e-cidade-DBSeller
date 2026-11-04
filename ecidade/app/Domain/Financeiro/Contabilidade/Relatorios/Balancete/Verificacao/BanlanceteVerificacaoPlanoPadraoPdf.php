<?php

namespace App\Domain\Financeiro\Contabilidade\Relatorios\Balancete\Verificacao;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;

class BanlanceteVerificacaoPlanoPadraoPdf extends BanlanceteVerificacaoPdf
{
    /**
     * @var int exixo x onde começa imprimir os valores
     */
    protected $xValores = 163;

    public function setDados($dados)
    {
        foreach ($dados as $conta) {
            $this->dados[$conta->estrutural][] = $conta;
        }
        return $this;
    }

    protected function imprimeCabecalho()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 7);
        $this->cell($this->wEstrutural, $this->hLinha, 'Estrutural', 'B', 0, 'C', 1);

        if ($this->porReduzido) {
            $this->cell($this->wInsttuicao, $this->hLinha, 'Inst', 'B', 0, 'C', 1);
        }

        $this->cell($this->wDescricao, $this->hLinha, 'Descrição', 'B', 0, 'C', 1);
        if ($this->porReduzido) {
            $this->cell($this->wRecurso, $this->hLinha, 'Recurso', 'B', 0, 'C', 1);
        }
        $this->cell($this->wIsf, $this->hLinha, 'ISF', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo Anterior', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Débitos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Créditos', 'B', 0, 'C', 1);
        $this->cell($this->wValores, $this->hLinha, 'Saldo', 'B', 1, 'C', 1);
        $this->regular();
    }

    protected function calculaColunas()
    {
        $this->xValores = $this->getLeftMargin() + $this->wEstrutural + $this->wReduzido + $this->wInsttuicao;
        $this->xValores += $this->wDescricao;

        $this->wDescricao += $this->wReduzido;
        if (!$this->porReduzido) {
            $this->wDescricao += +$this->wInsttuicao + $this->wRecurso;
        }
    }

    protected function imprimirCorpo()
    {
        foreach ($this->dados as $contas) {
            $conta = $contas[0];
            $this->quebraPagina();

            // variáveis criadas para controle da quebra de linha da descrição da conta
            $totalContasEstrutural = count($contas);
            $numeroLinhasDescricao = $this->nbLines($this->wDescricao, $conta->nome);

            $yInicial = $this->getY();
            $conta->sintetica ? $this->bold() : $this->regular();

            $this->linha($this->wEstrutural, $conta->mascara, 'L');
            $this->linha($this->wInsttuicao, $conta->instituicao, 'C');
            $this->linhaDescricao($this->wDescricao, $conta->nome, 'L');

            $totalImpresso = 0;
            foreach ($contas as $dado) {
                $this->imprimirValoresPlanoPadrao($dado, $totalContasEstrutural, $totalImpresso);
                $this->ln();
                $totalImpresso++;
            }

            // Verifica se o nome da conta quebrou mais linhas que o total de valores impressos.
            // Se sim, adiciona uma quebra de linha proporcional para compensar
            $x = $numeroLinhasDescricao - $totalContasEstrutural;
            if ($x > 0) {
                $this->ln($x * 4);
            }
        }
    }

    protected function linhaDescricao($w, $valor, $align = 'L')
    {
        $xInicial = $this->getX();
        $yInicial = $this->getY();
        $this->multiCell($w, 4, $valor, 0, $align);
        $this->setXY($xInicial + $w, $yInicial);
    }

    protected function imprimirValoresPlanoPadrao($dado)
    {
        $this->quebraPagina();
        $this->setX($this->xValores);

        $isf = $dado->sintetica ? '' : $dado->indicador_superavit;

        $complemento = str_pad($dado->complemento, 4, '0', STR_PAD_LEFT);
        $recurso = $dado->sintetica ? '' : "{$dado->siconfi} - $dado->subrecurso - {$complemento}";


        $this->linha($this->wRecurso, $recurso, 'C');
        $this->linha($this->wIsf, $isf, 'C');
        $this->imprimeValores($dado);
        $this->calculaTotalizadores($dado);
    }
}
